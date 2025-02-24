<?php
// app/controllers/TrabajadorController.php

session_start();
// Solo administradores pueden gestionar trabajadores
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    header("Location: index.php?controller=Auth&action=login");
    exit;
}

require_once __DIR__ . '/../../config/database.php';
$active = 'trabajadores';


class TrabajadorController {
    public function index() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE rol = 'trabajador' ORDER BY id DESC");
        $stmt->execute();
        $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $title = "Gestión de Trabajadores";
        ob_start();
        include __DIR__ . '/../views/trabajadores/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout.php';
    }
    
    public function create() {
        $title = "Agregar Trabajador";
        $worker = null; // no hay datos
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            include __DIR__ . '/../views/trabajadores/form.php';
        } else {
            ob_start();
            include __DIR__ . '/../views/trabajadores/form.php';
            $content = ob_get_clean();
            include __DIR__ . '/../views/layout.php';
        }
    }
    
    public function edit() {
        global $pdo;
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $worker = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$worker) {
            header("Location: index.php?controller=Trabajador&action=index");
            exit;
        }
        $title = "Editar Trabajador";
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            include __DIR__ . '/../views/trabajadores/form.php';
        } else {
            ob_start();
            include __DIR__ . '/../views/trabajadores/form.php';
            $content = ob_get_clean();
            include __DIR__ . '/../views/layout.php';
        }
    }
    public function store() {
        global $pdo;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_usuario = trim($_POST['nombre_usuario']);
            $nombre_completo = trim($_POST['nombre_completo']);
            $rol = $_POST['rol'];
            $tipo_pago = $_POST['tipo_pago'];
            $valor_dia = $_POST['valor_dia'];
            $dias_trabajo = isset($_POST['dias_trabajo']) ? implode(',', $_POST['dias_trabajo']) : '';
            // Cambiamos "contraseña" por "contrasena"
            $contrasena = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, nombre_completo, rol, tipo_pago, valor_dia, dias_trabajo, contraseña) 
                                   VALUES (:nombre_usuario, :nombre_completo, :rol, :tipo_pago, :valor_dia, :dias_trabajo, :contrasena)");
            
            if ($stmt->execute([
                'nombre_usuario' => $nombre_usuario,
                'nombre_completo' => $nombre_completo,
                'rol' => $rol,
                'tipo_pago' => $tipo_pago,
                'valor_dia' => $valor_dia,
                'dias_trabajo' => $dias_trabajo,
                'contrasena' => $contrasena
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
            $nombre_usuario = trim($_POST['nombre_usuario']);
            $nombre_completo = trim($_POST['nombre_completo']);
            $rol = $_POST['rol'];
            $tipo_pago = $_POST['tipo_pago'];
            $valor_dia = $_POST['valor_dia'];
            $dias_trabajo = isset($_POST['dias_trabajo']) ? implode(',', $_POST['dias_trabajo']) : '';
            $contraseña = $_POST['contraseña'];
            
            if (!empty($contraseña)) {
                $contrasena = password_hash($contraseña, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = :nombre_usuario, nombre_completo = :nombre_completo, 
                    rol = :rol, tipo_pago = :tipo_pago, valor_dia = :valor_dia, dias_trabajo = :dias_trabajo, contraseña = :contrasena 
                    WHERE id = :id");
                $result = $stmt->execute([
                    'nombre_usuario' => $nombre_usuario,
                    'nombre_completo' => $nombre_completo,
                    'rol' => $rol,
                    'tipo_pago' => $tipo_pago,
                    'valor_dia' => $valor_dia,
                    'dias_trabajo' => $dias_trabajo,
                    'contrasena' => $contrasena,
                    'id' => $id
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = :nombre_usuario, nombre_completo = :nombre_completo, 
                    rol = :rol, tipo_pago = :tipo_pago, valor_dia = :valor_dia, dias_trabajo = :dias_trabajo WHERE id = :id");
                $result = $stmt->execute([
                    'nombre_usuario' => $nombre_usuario,
                    'nombre_completo' => $nombre_completo,
                    'rol' => $rol,
                    'tipo_pago' => $tipo_pago,
                    'valor_dia' => $valor_dia,
                    'dias_trabajo' => $dias_trabajo,
                    'id' => $id
                ]);
            }
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
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            echo "success";
        } else {
            echo "error";
        }
    }
}
?>
