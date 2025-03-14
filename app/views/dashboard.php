<?php

$title = "Dashboard - Sistema de Asistencia";
ob_start();
?>
<h1 class="mb-4 text-center">Sistema de Asistencias La Metalera</h1>

<!-- ðŸš€ KPIs Principales -->
<div class="row">
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
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card custom-card text-white bg-warning">
      <div class="card-body">
        <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        <div>
          <h5 class="card-title">$<?= number_format($totalPagoMes, 0, ",", ".") ?></h5>
          <p class="card-text">Total a Pagar del Mes</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 🔥 Trabajadores en Ruta Hoy -->
<div class="row">
  <div class="col-12">
    <div class="card custom-card bg-light shadow-sm">
      <div class="card-header bg-primary text-white text-center">
        <h5 class="mb-0">Trabajadores en Ruta Hoy</h5>
      </div>
      <div class="card-body text-center">
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <?php if (!empty($trabajadoresEnRuta)): ?>
            <?php   
            
              foreach ($trabajadoresEnRuta as $trabajador): 
           
              ?>
              <div class="worker-container" data-bs-toggle="tooltip" data-bs-placement="top"
                   title="<?= htmlspecialchars($trabajador['nombre_completo']) . ' - ' . htmlspecialchars($trabajador['marcado']) ?>">
                  <div class="worker-circle">
                      <?php if (!empty($trabajador['foto_perfil'])): ?>
                          <img src="uploads/usuarios/<?= htmlspecialchars($trabajador['foto_perfil']) ?>" alt="Foto de <?= htmlspecialchars($trabajador['nombre_completo']) ?>">
                      <?php else: ?>
                          <?= strtoupper(substr(htmlspecialchars($trabajador['nombre_completo']), 0, 1)) ?>
                      <?php endif; ?>
                  </div>
                  <span class="worker-name"><?= htmlspecialchars($trabajador['nombre_completo']) ?></span>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-muted">No hay trabajadores asignados para hoy.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- ðŸ”¥ Ranking de Asistencia Mensual -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card custom-card bg-light shadow-sm">
      <div class="card-header bg-dark text-white text-center">
        <h5 class="mb-0">Ranking de Asistencia Mensual</h5>
      </div>
      <div class="card-body text-center">
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <?php foreach ($rankingAsistencias as $trabajador): ?>
            <div class="worker-container" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $trabajador['nombre_completo'] ?> - <?= $trabajador['dias_trabajados'] ?> dias">
                <div class="worker-circle">
                    <?php if (!empty($trabajador['foto_perfil'])): ?>
                        <img src="uploads/usuarios/<?= $trabajador['foto_perfil'] ?>" alt="Foto de <?= $trabajador['nombre_completo'] ?>">
                    <?php else: ?>
                        <?= strtoupper(substr($trabajador['nombre_completo'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <span class="worker-name"><?= $trabajador['nombre_completo'] ?></span>
                <span class="worker-days text-muted"><?= $trabajador['dias_trabajados'] ?> dias</span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ðŸ“Œ Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>

<!-- ðŸ“Œ Estilos Mejorados -->
<style>
/* ðŸ”¹ Contenedor de Trabajadores */
.worker-container {
    text-align: center;
    width: 90px;
}

/* ðŸ”¹ CÃ­rculos de Trabajadores */
.worker-circle {
    width: 70px;
    height: 70px;
    background-color: #007bff;
    color: #fff;
    font-size: 24px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 10px auto;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
    border: 3px solid #fff;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.worker-circle img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

/* ðŸ”¹ Hover en CÃ­rculos */
.worker-circle:hover {
    background-color: #0056b3;
}

/* ðŸ”¹ Nombre del Trabajador */
.worker-name {
    display: block;
    font-size: 12px;
    font-weight: bold;
    color: #333;
}

/* ðŸ”¹ DÃ­as Trabajados */
.worker-days {
    display: block;
    font-size: 12px;
    color: #6c757d;
}

/* ðŸ“± Responsive Mejorado */
@media (max-width: 768px) {
    .worker-circle {
        width: 60px;
        height: 60px;
        font-size: 18px;
    }

    .worker-name {
        font-size: 10px;
    }

    .worker-days {
        font-size: 10px;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
