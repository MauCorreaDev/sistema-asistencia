<?php
$title = "Dashboard - Sistema de Asistencia";
ob_start();
?>
<h1 class="mb-4">Dashboard</h1>
<div class="row">
  <!-- Card: Asistencias del Día -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card custom-card text-white bg-success">
      <div class="card-body">
        <div class="icon"><i class="fas fa-calendar-check"></i></div>
        <div>
          <h5 class="card-title"><?= $asistenciasHoy ?></h5>
          <p class="card-text">Asistencias Hoy</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Card: Total de Empleados -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card custom-card text-white bg-info">
      <div class="card-body">
        <div class="icon"><i class="fas fa-users"></i></div>
        <div>
          <h5 class="card-title"><?= $totalEmpleados ?></h5>
          <p class="card-text">Total Empleados</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Agrega más cards según necesites -->
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
