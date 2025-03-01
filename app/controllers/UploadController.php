<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode(["status" => "error", "message" => "Sesión expirada. Vuelve a iniciar sesión"]);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;

class UploadController {
    public function subirImagen() {
        global $pdo;
        $usuario_id = $_SESSION['usuario']['id'];

        $directorio = __DIR__ . "/../../public/uploads/usuarios/";

        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        if (!isset($_FILES['fotoPerfil']) || $_FILES['fotoPerfil']['error'] != UPLOAD_ERR_OK) {
            echo json_encode(["status" => "error", "message" => "Error al subir la imagen"]);
            exit;
        }

        $fotoPerfil = $_FILES['fotoPerfil'];
        $ext = pathinfo($fotoPerfil['name'], PATHINFO_EXTENSION);
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array(strtolower($ext), $extensionesPermitidas)) {
            echo json_encode(["status" => "error", "message" => "Formato no permitido. Usa JPG, JPEG, PNG o GIF"]);
            exit;
        }

        $nombreFoto = "perfil_" . $usuario_id . "." . $ext;
        $rutaCompleta = $directorio . $nombreFoto;

        try {
            $image = Image::make($fotoPerfil['tmp_name'])->fit(300, 300)->save($rutaCompleta, 80);

            $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = :foto_perfil WHERE id = :usuario_id");
            $stmt->execute(['foto_perfil' => $nombreFoto, 'usuario_id' => $usuario_id]);

            $_SESSION['usuario']['foto_perfil'] = $nombreFoto;

            echo json_encode(["status" => "success", "message" => "Imagen subida con éxito", "foto" => $nombreFoto]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error al procesar la imagen: " . $e->getMessage()]);
        }
        exit;
    }
}
