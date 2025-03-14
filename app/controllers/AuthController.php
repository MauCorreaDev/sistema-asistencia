<?php
// app/controllers/AuthController.php

// Incluir la conexión a la base de datos
require_once __DIR__ . '/../../config/database.php';

class AuthController {
    public function login() {
        session_start();
        // Verificar si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener y sanitizar datos
            $nombre_usuario = filter_input(INPUT_POST, 'nombre_usuario');
            $contraseña = filter_input(INPUT_POST, 'contraseña');
            
            // Consultar la base de datos para obtener el usuario
            global $pdo;
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = :nombre_usuario LIMIT 1");
            $stmt->execute(['nombre_usuario' => $nombre_usuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                // Comprobar la contraseña usando password_verify()
                if (password_verify($contraseña, $usuario['contraseña'])) {
                    // Autenticación exitosa: guardar datos en la sesión
                    $_SESSION['usuario'] = [
                        'id' => $usuario['id'],
                        'nombre_usuario' => $usuario['nombre_usuario'],
                        'nombre_completo' => $usuario['nombre_completo'], // Agregado
                        'rol' => $usuario['rol']
                    ];
                    
                    // Redireccionar según el rol
                    if ($usuario['rol'] == 'administrador') {
                        header("Location: index.php?controller=Dashboard&action=index");
                        exit;
                    } else {
                        header("Location: index.php?controller=Usuario&action=index");
                        exit;
                    }
                } else {
                    // Contraseña incorrecta
                    $error = "Credenciales inválidas.";
                }
            } else {
                // Usuario no encontrado
                $error = "Credenciales inválidas.";
            }
        }
        
        // Incluir la vista de login y pasar el mensaje de error (si existe)
        include __DIR__ . '/../views/login.php';
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
