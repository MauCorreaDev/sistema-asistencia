    <?php
    // app/controllers/ReportesController.php

    session_start();
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
        header("Location: index.php?controller=Auth&action=login");
        exit;
    }

    require_once __DIR__ . '/../../config/database.php';

    class ReportesController {
        public function index() {
            global $pdo;
            $stmt = $pdo->query("SELECT id, nombre_completo FROM usuarios WHERE rol = 'trabajador' ORDER BY nombre_completo ASC");
            $trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $filtro_empleado = isset($_GET['empleado']) ? $_GET['empleado'] : '';
            $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
            $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
            $dias_semana = isset($_GET['dias_semana']) ? $_GET['dias_semana'] : [];
            $prefiltro = isset($_GET['prefiltro']) ? $_GET['prefiltro'] : 'ninguno';

            if ($prefiltro !== 'ninguno') {
                if ($prefiltro == 'este_mes') {
                    $fecha_inicio = date("Y-m-01");
                    $fecha_fin = date("Y-m-t");
                } elseif ($prefiltro == 'esta_semana') {
                    $fecha_inicio = date("Y-m-d", strtotime("monday this week"));
                    $fecha_fin = date("Y-m-d", strtotime("saturday this week"));
                } elseif ($prefiltro == 'mes_anterior') {
                    $fecha_inicio = date("Y-m-01", strtotime("first day of last month"));
                    $fecha_fin = date("Y-m-t", strtotime("last day of last month"));
                }
            }
            
            $where = [];
            $params = [];
            if (!empty($filtro_empleado)) {
                $where[] = "a.usuario_id = :empleado";
                $params['empleado'] = $filtro_empleado;
            }
            if (!empty($fecha_inicio)) {
                $where[] = "a.fecha >= :fecha_inicio";
                $params['fecha_inicio'] = $fecha_inicio;
            }
            if (!empty($fecha_fin)) {
                $where[] = "a.fecha <= :fecha_fin";
                $params['fecha_fin'] = $fecha_fin;
            }
            if (!empty($dias_semana)) {
                $placeholders = [];
                foreach ($dias_semana as $index => $dia) {
                    $placeholder = ":dia_$index";
                    $placeholders[] = $placeholder;
                    $params["dia_$index"] = $dia;
                }
                $where[] = "DAYNAME(a.fecha) IN (" . implode(',', $placeholders) . ")";
            }
            
            $sql = "SELECT a.*, u.nombre_completo 
                    FROM asistencias a 
                    INNER JOIN usuarios u ON a.usuario_id = u.id";
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY a.fecha DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $title = "Reportes de Asistencias";
            $active = 'reportes';
            ob_start();
            include __DIR__ . '/../views/reportes/index.php';
            $content = ob_get_clean();
            include __DIR__ . '/../views/layout.php';
        }

        public function exportar() {
            global $pdo;
        
            require_once __DIR__ . '/../libs/dompdf/autoload.inc.php';
        
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->setPaper('A4', 'portrait'); // Formato vertical
        
            // Parámetros del GET
            $filtro_empleado = isset($_GET['empleado']) ? $_GET['empleado'] : '';
            $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
            $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
        
            // Obtener datos del trabajador si se ha filtrado
            $nombre_trabajador = "Todos los Trabajadores";
            if (!empty($filtro_empleado)) {
                $stmt = $pdo->prepare("SELECT nombre_completo FROM usuarios WHERE id = :empleado");
                $stmt->execute(['empleado' => $filtro_empleado]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($usuario) {
                    $nombre_trabajador = $usuario['nombre_completo'];
                }
            }
        
            // Construcción de la consulta SQL con los filtros
            $where = [];
            $params = [];
            if (!empty($filtro_empleado)) {
                $where[] = "a.usuario_id = :empleado";
                $params['empleado'] = $filtro_empleado;
            }
            if (!empty($fecha_inicio)) {
                $where[] = "a.fecha >= :fecha_inicio";
                $params['fecha_inicio'] = $fecha_inicio;
            }
            if (!empty($fecha_fin)) {
                $where[] = "a.fecha <= :fecha_fin";
                $params['fecha_fin'] = $fecha_fin;
            }
        
            $sql = "SELECT a.*, u.nombre_completo 
                    FROM asistencias a 
                    INNER JOIN usuarios u ON a.usuario_id = u.id";
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            $sql .= " ORDER BY a.fecha DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            if (empty($asistencias)) {
                die("No hay registros para generar el PDF.");
            }
        
            // **Cálculo del total de días trabajados y el monto total**
            $total_dias = count($asistencias);
            $total_pago = array_sum(array_column($asistencias, 'pago_dia'));
        
            // **Estilos CSS para el PDF**
            $css = '
                <style>
                    body { font-family: Arial, sans-serif; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .header img { max-width: 100px; }
                    .title { font-size: 20px; font-weight: bold; color: #2d572c; }
                    .sub-title { font-size: 16px; font-weight: bold; color: #444; margin-top: 5px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                    table, th, td { border: 1px solid black; }
                    th { background-color: #2d572c; color: white; padding: 8px; text-align: left; }
                    td { padding: 6px; }
                    .summary { margin-top: 20px; padding: 10px; background-color: #f2f2f2; border-radius: 5px; }
                    .summary p { font-size: 14px; font-weight: bold; margin: 5px 0; }
                </style>
            ';
        
            // **HTML para el PDF**
            $html = '<html><head>' . $css . '</head><body>';
        
            // **Encabezado con logo y título**
            $html .= '<div class="header">
                        <img "..\public\img\logometalera.png">
                        <p class="title">La Metalera - Reporte de Asistencias</p>
                        <p class="sub-title">Empleado: ' . $nombre_trabajador . '</p>
                        <p class="sub-title">Período: ' . ($fecha_inicio ?: 'Inicio') . ' - ' . ($fecha_fin ?: 'Actualidad') . '</p>
                      </div>';
        
            // **Tabla de asistencias**
            $html .= '<table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Hora Ingreso</th>
                                <th>Hora Salida</th>
                                <th>Horas Trabajadas</th>
                                <th>Pago Día</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($asistencias as $asistencia) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($asistencia['id']) . '</td>
                            <td>' . date("d-m-Y", strtotime($asistencia['fecha'])) . '</td>
                            <td>' . htmlspecialchars($asistencia['hora_ingreso']) . '</td>
                            <td>' . htmlspecialchars($asistencia['hora_salida']) . '</td>
                            <td>' . htmlspecialchars($asistencia['horas_trabajadas']) . '</td>
                            <td>$' . number_format($asistencia['pago_dia'], 0, ",", ".") . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
        
            // **Resumen total**
            $html .= '<div class="summary">
                        <p>Total de días trabajados: ' . $total_dias . '</p>
                        <p>Total a cobrar: $' . number_format($total_pago, 0, ",", ".") . '</p>
                      </div>';
        
            $html .= '</body></html>';
        
            // **Cargar HTML en Dompdf**
            $dompdf->loadHtml($html);
            $dompdf->render();
        
            // **Descargar el PDF**
            $fecha_actual = date("Y-m-d");
            $nombreArchivo = str_replace(" ", "_", $nombre_trabajador) . "_{$fecha_actual}.pdf";
            
            header("Content-Type: application/pdf");
            header("Content-Disposition: attachment; filename={$nombreArchivo}");
            echo $dompdf->output();
            exit;
        }
    }        
