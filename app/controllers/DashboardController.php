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

        // Configurar el idioma para el día de la semana
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
        $asistenciasHoy = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 🔍 Consultar total de empleados
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'trabajador'");
        $totalEmpleados = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 🔍 Consultar total a pagar del mes
        $stmt = $pdo->prepare("SELECT SUM(pago_dia) AS total_mes FROM asistencias WHERE fecha BETWEEN :primerDiaMes AND :hoy");
        $stmt->execute(['primerDiaMes' => $primerDiaMes, 'hoy' => $hoy]);
        $totalPagoMes = $stmt->fetch(PDO::FETCH_ASSOC)['total_mes'] ?? 0;

        // 🔍 Obtener los trabajadores que deben trabajar hoy con su foto de perfil
        $stmt = $pdo->prepare("
            SELECT id, nombre_completo, nombre_usuario, foto_perfil,
                   CONVERT(dias_trabajo USING utf8mb4) AS dias_trabajo 
            FROM usuarios 
            WHERE FIND_IN_SET(CONVERT(:diaSemana USING utf8mb4), CONVERT(dias_trabajo USING utf8mb4))
        ");
        $stmt->execute(['diaSemana' => $diaSemana]);
        $trabajadoresEnRuta = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔍 Verificar si marcaron asistencia hoy
        foreach ($trabajadoresEnRuta as &$trabajador) {
            $stmt = $pdo->prepare("SELECT COUNT(*) AS marcado FROM asistencias WHERE usuario_id = :usuario_id AND fecha = :hoy");
            $stmt->execute(['usuario_id' => $trabajador['id'], 'hoy' => $hoy]);
            $trabajador['marcado'] = $stmt->fetch(PDO::FETCH_ASSOC)['marcado'] > 0 ? "✅ Marcó asistencia" : "❌ No ha marcado";
        }

        // 🔍 Consulta: Ranking de asistencia mensual con fotos
        $stmt = $pdo->prepare("
            SELECT u.id, u.nombre_completo, u.foto_perfil, COUNT(a.id) AS dias_trabajados
            FROM usuarios u
            LEFT JOIN asistencias a ON u.id = a.usuario_id AND a.fecha LIKE :mes
            WHERE u.rol = 'trabajador'
            GROUP BY u.id
            ORDER BY dias_trabajados DESC
        ");
        $stmt->execute(['mes' => "$mesActual%"]);
        $rankingAsistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Incluir la vista con las variables
        include __DIR__ . '/../views/dashboard.php';
    }
}
