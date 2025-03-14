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

    /**
     * Muestra el perfil del usuario logueado.
     */
    public function perfil() {
        global $pdo;
        $usuario_id = $_SESSION['usuario']['id'];

        // Obtener datos del usuario desde la base de datos
        $stmt = $pdo->prepare("SELECT id, nombre_completo, nombre_usuario, correo, celular, tipo_pago, valor_dia, foto_perfil FROM usuarios WHERE id = :usuario_id");
        $stmt->execute(['usuario_id' => $usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Cargar la vista del perfil
        $title = "Mi Perfil";
        ob_start();
        include __DIR__ . '/../../app/views/usuario/perfil.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../app/views/layout_usuario.php';
    }

    /**
     * Actualiza la información del perfil del usuario, incluyendo foto de perfil.
     */
 public function actualizarPerfil() {
        global $pdo;

        // Verificar sesión activa
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['error'] = "Sesión expirada. Vuelve a iniciar sesión.";
            header("Location: index.php?controller=Usuario&action=perfil");
            exit;
        }

        // Capturar datos del formulario
        $usuario_id = $_SESSION['usuario']['id'];
        $correo = $_POST['correo'] ?? '';
        $celular = $_POST['celular'] ?? '';
        $fotoPerfil = $_FILES['fotoPerfil'] ?? null;

        // 📂 Directorio donde se guardarán las imágenes de perfil
        $directorio = __DIR__ . "/../../uploads/usuarios/";

        // Crear directorio si no existe
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // 🔹 Manejo de la imagen de perfil (si se ha subido una nueva)
        if ($fotoPerfil && $fotoPerfil['size'] > 0) {
            // Validar formato de imagen permitido
            $ext = strtolower(pathinfo($fotoPerfil['name'], PATHINFO_EXTENSION));
            $formatosPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (!in_array($ext, $formatosPermitidos)) {
                $_SESSION['error'] = "Formato de imagen no permitido. Usa JPG, JPEG, PNG o GIF.";
                header("Location: index.php?controller=Usuario&action=perfil");
                exit;
            }

            if ($fotoPerfil['size'] > $maxSize) {
                $_SESSION['error'] = "La imagen supera los 2MB permitidos.";
                header("Location: index.php?controller=Usuario&action=perfil");
                exit;
            }

            // 📌 Eliminar imagen anterior si existe
            $stmt = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = :usuario_id");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $fotoAntigua = $stmt->fetchColumn();

            if (!empty($fotoAntigua) && file_exists($directorio . $fotoAntigua)) {
                unlink($directorio . $fotoAntigua);
            }

            // 📌 Guardar la nueva imagen
            $nombreFoto = "perfil_" . $usuario_id . "." . $ext;
            $rutaDestino = $directorio . $nombreFoto;

            if (!move_uploaded_file($fotoPerfil['tmp_name'], $rutaDestino)) {
                $_SESSION['error'] = "Error al subir la imagen. Intenta nuevamente.";
                header("Location: index.php?controller=Usuario&action=perfil");
                exit;
            }
        } else {
            // Mantener la imagen actual si no se subió una nueva
            $stmt = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = :usuario_id");
            $stmt->execute(['usuario_id' => $usuario_id]);
            $nombreFoto = $stmt->fetchColumn();
        }

        // 🔹 Actualizar datos en la base de datos
        $stmt = $pdo->prepare("UPDATE usuarios SET correo = :correo, celular = :celular, foto_perfil = :foto_perfil WHERE id = :usuario_id");
        $stmt->execute([
            'correo' => $correo,
            'celular' => $celular,
            'foto_perfil' => $nombreFoto,
            'usuario_id' => $usuario_id
        ]);

        // 🔹 Actualizar sesión con la nueva imagen de perfil
        $_SESSION['usuario']['foto_perfil'] = $nombreFoto;
        $_SESSION['success'] = "Perfil actualizado con éxito.";

        // Redirigir al perfil
        header("Location: index.php?controller=Usuario&action=perfil");
        exit;
    }
}
