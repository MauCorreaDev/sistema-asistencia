<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?controller=Auth&action=login");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

class UsuarioController {
    
    /**
     * Muestra el dashboard del trabajador con sus asistencias recientes y total del mes.
     */
    public function index() {
        global $pdo;
        $usuario_id = $_SESSION['usuario']['id'];
        $rol = $_SESSION['usuario']['rol'];

        if ($rol === 'trabajador') {
            // Obtener las últimas 5 asistencias del trabajador
            $stmt = $pdo->prepare("SELECT * FROM asistencias WHERE usuario_id = :usuario_id ORDER BY fecha DESC LIMIT 5");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 🔹 Obtener el total de pago del mes actual
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(pago_dia), 0) as total_pago 
                FROM asistencias 
                WHERE usuario_id = :usuario_id 
                AND YEAR(fecha) = YEAR(CURDATE()) 
                AND MONTH(fecha) = MONTH(CURDATE())
            ");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $total_pago = $stmt->fetch(PDO::FETCH_ASSOC)['total_pago'];

            // Verificar si el usuario ya marcó asistencia hoy
            $fecha_hoy = date("Y-m-d");
            $stmt = $pdo->prepare("SELECT * FROM asistencias WHERE usuario_id = :usuario_id AND fecha = :fecha");
            $stmt->execute(['usuario_id' => $usuario_id, 'fecha' => $fecha_hoy]);
            $asistencia_hoy = $stmt->fetch(PDO::FETCH_ASSOC);

            // Cargar la vista del dashboard
            $title = "Marcar Asistencia";
            ob_start();
            include __DIR__ . '/../../app/views/usuario/index.php';
            $content = ob_get_clean();
            include __DIR__ . '/../../app/views/layout_usuario.php';
        } else {
            header("Location: index.php?controller=Dashboard&action=index");
            exit;
        }
    }

    /**
     * Función para marcar asistencia (Ingreso/Salida).
     */
    public function marcarAsistencia() {
        global $pdo;

        // Verificar si el usuario tiene sesión iniciada
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(["status" => "error", "message" => "Sesión expirada. Vuelve a iniciar sesión."]);
            exit;
        }

        $usuario_id = $_SESSION['usuario']['id'];
        $fecha_hoy = date("Y-m-d");
        $hora_actual = date("H:i:s");

        // Verificar si el usuario ya marcó ingreso hoy
        $stmt = $pdo->prepare("SELECT * FROM asistencias WHERE usuario_id = :usuario_id AND fecha = :fecha");
        $stmt->execute(['usuario_id' => $usuario_id, 'fecha' => $fecha_hoy]);
        $asistencia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$asistencia) {
            // Marcar ingreso si no hay registro para hoy
            $stmt = $pdo->prepare("INSERT INTO asistencias (usuario_id, fecha, hora_ingreso) VALUES (:usuario_id, :fecha, :hora_ingreso)");
            $stmt->execute([
                'usuario_id' => $usuario_id,
                'fecha' => $fecha_hoy,
                'hora_ingreso' => $hora_actual
            ]);

            echo json_encode(["status" => "success", "message" => "Ingreso registrado a las $hora_actual"]);
        } elseif (!$asistencia['hora_salida']) {
            // Marcar salida si ya tiene ingreso registrado
            $hora_ingreso = strtotime($asistencia['hora_ingreso']);
            $hora_salida = strtotime($hora_actual);
            $horas_trabajadas = round(($hora_salida - $hora_ingreso) / 3600, 2);

            // 🔹 Obtener el valor del día del trabajador
            $stmt = $pdo->prepare("SELECT valor_dia FROM usuarios WHERE id = :usuario_id");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $valor_dia = $stmt->fetchColumn();

            // Asegurar que el valor del día sea válido
            if (!$valor_dia || $valor_dia <= 0) {
                $valor_dia = 10000; // Valor predeterminado
            }

            // 🔹 Pago del día siempre igual al valor del día del trabajador
            $pago_dia = $valor_dia;

            // 🔹 Actualizar la asistencia con la salida y el pago del día
            $stmt = $pdo->prepare("UPDATE asistencias 
                SET hora_salida = :hora_salida, 
                    horas_trabajadas = :horas_trabajadas, 
                    pago_dia = :pago_dia
                WHERE id = :asistencia_id");

            $stmt->execute([
                'hora_salida' => $hora_actual,
                'horas_trabajadas' => $horas_trabajadas,
                'pago_dia' => $pago_dia,
                'asistencia_id' => $asistencia['id']
            ]);

            echo json_encode(["status" => "success", "message" => "Salida registrada a las $hora_actual. Total trabajado: $horas_trabajadas horas."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Ya has marcado tu salida hoy."]);
        }
        exit;
    }
}
