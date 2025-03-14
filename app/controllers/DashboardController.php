<?php
// app/controllers/DashboardController.php
session_start();

// Verificamos que el usuario tenga rol de administrador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'administrador') {
    header("Location: index.php?controller=Auth&action=login");
    exit;
}
$active = 'dashboard';

// Incluir la conexión a la base de datos
require_once __DIR__ . '/../../config/database.php';

class DashboardController {
    
    public function index() {
        global $pdo;
        
        // Fecha actual y primer día del mes
        $hoy = date('Y-m-d');
        $primerDiaMes = date('Y-m-01');
        $mesActual = date('Y-m');

        // Configurar idioma para el día de la semana
        $diasSemana = [
            'Monday' => 'lunes',
            'Tuesday' => 'martes',
            'Wednesday' => 'miércoles',
            'Thursday' => 'jueves',
            'Friday' => 'viernes',
            'Saturday' => 'sábado',
            'Sunday' => 'domingo'
        ];
        $diaSemana = strtolower($diasSemana[date('l', strtotime($hoy))]);

        // 🔍 Consultar asistencias del día
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM asistencias WHERE fecha = :hoy");
        $stmt->execute(['hoy' => $hoy]);
        $asistenciasHoy = $stmt->fetchColumn() ?: 0;

        // 🔍 Consultar total de empleados
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'trabajador'");
        $totalEmpleados = $stmt->fetchColumn() ?: 0;

        // 🔍 Consultar total a pagar del mes con COALESCE para evitar NULL
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(pago_dia), 0) AS total_mes FROM asistencias WHERE fecha BETWEEN :primerDiaMes AND :hoy");
        $stmt->execute(['primerDiaMes' => $primerDiaMes, 'hoy' => $hoy]);
        $totalPagoMes = $stmt->fetchColumn();

     // 🔍 Obtener los trabajadores en ruta hoy, sin duplicados y excluyendo admins
$stmt = $pdo->prepare("
SELECT u.id, u.nombre_completo, u.nombre_usuario, u.foto_perfil
FROM usuarios u
WHERE FIND_IN_SET(:diaSemana, u.dias_trabajo)
AND u.rol NOT IN ('administrador', 'super_administrador')
GROUP BY u.id
");
$stmt->execute(['diaSemana' => $diaSemana]);

$trabajadoresEnRuta = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];


     // 🔍 Verificar si marcaron asistencia hoy (NO USAR `&$trabajador`)
foreach ($trabajadoresEnRuta as $index => $trabajador) { 
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM asistencias WHERE usuario_id = :usuario_id AND fecha = :hoy");
    $stmt->execute(['usuario_id' => $trabajador['id'], 'hoy' => $hoy]);
    $trabajadoresEnRuta[$index]['marcado'] = $stmt->fetchColumn() > 0 ? "✅ Marcó asistencia" : "❌ No ha marcado";
}

        // 🔍 Ranking de asistencia mensual
        $stmt = $pdo->prepare("
            SELECT u.id, u.nombre_completo, u.foto_perfil, COUNT(a.id) AS dias_trabajados
            FROM usuarios u
            LEFT JOIN asistencias a ON u.id = a.usuario_id AND a.fecha BETWEEN :primerDiaMes AND :hoy
            WHERE u.rol = 'trabajador'
            GROUP BY u.id
            ORDER BY dias_trabajados DESC
        ");
        $stmt->execute(['primerDiaMes' => $primerDiaMes, 'hoy' => $hoy]);
        $rankingAsistencias = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 🔥 Validar que la vista del dashboard exista antes de incluirla
        $viewPath = __DIR__ . '/../../app/views/dashboard.php';
        if (!file_exists($viewPath)) {
            die("Error: La vista del dashboard no existe en $viewPath");
        }

        include $viewPath;
    }
}
