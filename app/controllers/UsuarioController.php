<?php
// app/controllers/UsuarioController.php

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?controller=Auth&action=login");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

class UsuarioController {
    public function index() {
        global $pdo;
        $usuario_id = $_SESSION['usuario']['id'];
        $rol = $_SESSION['usuario']['rol'];

        if ($rol === 'trabajador') {
            // Obtener las últimas asistencias del trabajador
            $stmt = $pdo->prepare("SELECT * FROM asistencias WHERE usuario_id = :usuario_id ORDER BY fecha DESC LIMIT 5");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcular total acumulado
            $stmt = $pdo->prepare("SELECT SUM(pago_dia) as total_pago FROM asistencias WHERE usuario_id = :usuario_id");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $total_pago = $stmt->fetch(PDO::FETCH_ASSOC)['total_pago'] ?? 0;

            // Verificar si ya marcó asistencia hoy
            $fecha_hoy = date("Y-m-d");
            $stmt = $pdo->prepare("SELECT * FROM asistencias WHERE usuario_id = :usuario_id AND fecha = :fecha");
            $stmt->execute(['usuario_id' => $usuario_id, 'fecha' => $fecha_hoy]);
            $asistencia_hoy = $stmt->fetch(PDO::FETCH_ASSOC);

            $title = "Dashboard del Trabajador";
            ob_start();
            include __DIR__ . '/../../app/views/usuario/index.php';
            $content = ob_get_clean();
            include __DIR__ . '/../../app/views/layout_usuario.php'; // Nuevo layout
        } else {
            header("Location: index.php?controller=Dashboard&action=index");
            exit;
        }
    }

    public function marcarAsistencia() {
        global $pdo;
        $usuario_id = $_SESSION['usuario']['id'];
        $fecha_hoy = date("Y-m-d");
        $hora_actual = date("H:i:s");

        // Verificar si el trabajador ya marcó asistencia hoy
        $stmt = $pdo->prepare("SELECT * FROM asistencias WHERE usuario_id = :usuario_id AND fecha = :fecha");
        $stmt->execute(['usuario_id' => $usuario_id, 'fecha' => $fecha_hoy]);
        $asistencia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$asistencia) {
            // Registrar la hora de ingreso
            $stmt = $pdo->prepare("INSERT INTO asistencias (usuario_id, fecha, hora_ingreso) VALUES (:usuario_id, :fecha, :hora_ingreso)");
            $stmt->execute([
                'usuario_id' => $usuario_id,
                'fecha' => $fecha_hoy,
                'hora_ingreso' => $hora_actual
            ]);
            echo json_encode(["status" => "success", "message" => "Ingreso registrado con éxito", "tipo" => "ingreso"]);
        } else if ($asistencia && empty($asistencia['hora_salida'])) {
            // Registrar la salida y calcular horas trabajadas
            $hora_ingreso = strtotime($asistencia['hora_ingreso']);
            $hora_salida = strtotime($hora_actual);
            $horas_trabajadas = round(($hora_salida - $hora_ingreso) / 3600, 2);

            // Obtener el valor del día del trabajador
            $stmt = $pdo->prepare("SELECT valor_dia FROM usuarios WHERE id = :usuario_id");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $valor_dia = $stmt->fetch(PDO::FETCH_ASSOC)['valor_dia'];

            // Calcular el pago basado en las horas trabajadas
            $pago_dia = $valor_dia;

            // Actualizar la asistencia con la salida y horas trabajadas
            $stmt = $pdo->prepare("UPDATE asistencias SET hora_salida = :hora_salida, horas_trabajadas = :horas_trabajadas, pago_dia = :pago_dia WHERE id = :id");
            $stmt->execute([
                'hora_salida' => $hora_actual,
                'horas_trabajadas' => $horas_trabajadas,
                'pago_dia' => $pago_dia,
                'id' => $asistencia['id']
            ]);

            echo json_encode(["status" => "success", "message" => "Salida registrada con éxito", "tipo" => "salida"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Ya has registrado tu asistencia hoy"]);
        }
        exit;
    }
}
