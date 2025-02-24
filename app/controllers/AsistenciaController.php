<?php
// app/controllers/AsistenciaController.php

session_start();
// Solo administradores pueden gestionar asistencias
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    header("Location: index.php?controller=Auth&action=login");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

class AsistenciaController {

    public function index() {
        global $pdo;
        // Consulta con join para obtener el nombre del empleado
        $stmt = $pdo->query("SELECT a.*, u.nombre_completo FROM asistencias a INNER JOIN usuarios u ON a.usuario_id = u.id ORDER BY a.fecha DESC");
        $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $title = "Gestión de Asistencias";
        $active = 'asistencias';
        ob_start();
        include __DIR__ . '/../views/asistencias/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout.php';
    }
    
    public function create() {
        global $pdo;
        $title = "Agregar Asistencia";
        $asistencia = null;
        // Obtener lista de trabajadores para el select
        $stmt = $pdo->query("SELECT id, nombre_completo FROM usuarios WHERE rol = 'trabajador' ORDER BY nombre_completo ASC");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            include __DIR__ . '/../views/asistencias/form.php';
        } else {
            ob_start();
            include __DIR__ . '/../views/asistencias/form.php';
            $content = ob_get_clean();
            include __DIR__ . '/../views/layout.php';
        }
    }
    
    public function edit() {
        global $pdo;
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM asistencias WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $asistencia = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$asistencia) {
            header("Location: index.php?controller=Asistencia&action=index");
            exit;
        }
        // Obtener la lista de trabajadores para el select
        $stmt = $pdo->query("SELECT id, nombre_completo FROM usuarios WHERE rol = 'trabajador' ORDER BY nombre_completo ASC");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $title = "Editar Asistencia";
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            include __DIR__ . '/../views/asistencias/form.php';
        } else {
            ob_start();
            include __DIR__ . '/../views/asistencias/form.php';
            $content = ob_get_clean();
            include __DIR__ . '/../views/layout.php';
        }
    }
    
    public function store() {
        global $pdo;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_POST['usuario_id'];
            $fecha = $_POST['fecha'];
            $hora_ingreso = $_POST['hora_ingreso'];
            $hora_salida = $_POST['hora_salida'];
            
            // Calcular las horas trabajadas
            $horas_trabajadas = 0;
            if (!empty($hora_ingreso) && !empty($hora_salida)) {
                $datetime1 = new DateTime($fecha . ' ' . $hora_ingreso);
                $datetime2 = new DateTime($fecha . ' ' . $hora_salida);
                $interval = $datetime1->diff($datetime2);
                $horas_trabajadas = $interval->h + ($interval->i / 60);
            }
            
            // Obtener datos del empleado para calcular el pago_dia
            $query = $pdo->prepare("SELECT tipo_pago, valor_dia FROM usuarios WHERE id = :id");
            $query->execute(['id' => $usuario_id]);
            $userData = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                if ($userData['tipo_pago'] == 'diario') {
                    $pago_dia = $userData['valor_dia'];
                } elseif ($userData['tipo_pago'] == 'por_hora') {
                    $pago_dia = $horas_trabajadas * $userData['valor_dia'];
                } else {
                    $pago_dia = 0;
                }
            } else {
                $pago_dia = 0;
            }
            
            $stmt = $pdo->prepare("INSERT INTO asistencias (usuario_id, fecha, hora_ingreso, hora_salida, horas_trabajadas, pago_dia)
                                   VALUES (:usuario_id, :fecha, :hora_ingreso, :hora_salida, :horas_trabajadas, :pago_dia)");
            
            if ($stmt->execute([
                'usuario_id' => $usuario_id,
                'fecha' => $fecha,
                'hora_ingreso' => $hora_ingreso,
                'hora_salida' => $hora_salida,
                'horas_trabajadas' => $horas_trabajadas,
                'pago_dia' => $pago_dia
            ])) {
                echo "success";
            } else {
                $errorInfo = $stmt->errorInfo();
                echo "error: " . $errorInfo[2];
            }
        }
    }
    
    public function update() {
        global $pdo;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $usuario_id = $_POST['usuario_id'];
            $fecha = $_POST['fecha'];
            $hora_ingreso = $_POST['hora_ingreso'];
            $hora_salida = $_POST['hora_salida'];
            
            $horas_trabajadas = 0;
            if (!empty($hora_ingreso) && !empty($hora_salida)) {
                $datetime1 = new DateTime($fecha . ' ' . $hora_ingreso);
                $datetime2 = new DateTime($fecha . ' ' . $hora_salida);
                $interval = $datetime1->diff($datetime2);
                $horas_trabajadas = $interval->h + ($interval->i / 60);
            }
            
            // Obtener datos del empleado
            $query = $pdo->prepare("SELECT tipo_pago, valor_dia FROM usuarios WHERE id = :id");
            $query->execute(['id' => $usuario_id]);
            $userData = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                if ($userData['tipo_pago'] == 'diario') {
                    $pago_dia = $userData['valor_dia'];
                } elseif ($userData['tipo_pago'] == 'por_hora') {
                    $pago_dia = $horas_trabajadas * $userData['valor_dia'];
                } else {
                    $pago_dia = 0;
                }
            } else {
                $pago_dia = 0;
            }
            
            $stmt = $pdo->prepare("UPDATE asistencias SET usuario_id = :usuario_id, fecha = :fecha, hora_ingreso = :hora_ingreso, hora_salida = :hora_salida, horas_trabajadas = :horas_trabajadas, pago_dia = :pago_dia WHERE id = :id");
            $result = $stmt->execute([
                'usuario_id' => $usuario_id,
                'fecha' => $fecha,
                'hora_ingreso' => $hora_ingreso,
                'hora_salida' => $hora_salida,
                'horas_trabajadas' => $horas_trabajadas,
                'pago_dia' => $pago_dia,
                'id' => $id
            ]);
            if ($result) {
                echo "success";
            } else {
                echo "error";
            }
        }
    }
    
    
    public function delete() {
        global $pdo;
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM asistencias WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            echo "success";
        } else {
            echo "error";
        }
    }
}
?>
