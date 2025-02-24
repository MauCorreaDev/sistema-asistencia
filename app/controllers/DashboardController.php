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
        
        // Ejemplo de consulta: contar las asistencias del día actual
        $hoy = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM asistencias WHERE fecha = :hoy");
        $stmt->execute(['hoy' => $hoy]);
        $asistenciasHoy = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Otra consulta de ejemplo: contar el total de empleados
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'trabajador'");
        $totalEmpleados = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Puedes agregar más consultas para obtener otros KPIs
        
        // Incluir la vista y pasar las variables necesarias
        include __DIR__ . '/../views/dashboard.php';
    }
}
?>
