<div class="container-fluid">
    <h1 class="mb-4 text-center">Bienvenido, <?= htmlspecialchars($_SESSION['usuario']['nombre_completo']) ?></h1>

    <!-- Alertas dinámicas -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Card de marcaje de asistencia -->
    <div class="card shadow-lg">
        <div class="card-body text-center">
            <h4>Marcado de Asistencia</h4>
            <p>
                <?= isset($asistencia_hoy['hora_ingreso']) ? "<strong>Entrada:</strong> " . htmlspecialchars($asistencia_hoy['hora_ingreso']) : "<span class='text-muted'>Aún no has marcado asistencia hoy.</span>" ?>
            </p>
            <p>
                <?= isset($asistencia_hoy['hora_salida']) ? "<strong>Salida:</strong> " . htmlspecialchars($asistencia_hoy['hora_salida']) : "<span class='text-muted'>Aún no has registrado salida.</span>" ?>
            </p>
            <button id="btnMarcarAsistencia" class="btn btn-primary mt-2" <?= isset($asistencia_hoy['hora_salida']) ? "disabled" : "" ?>>
                <?= isset($asistencia_hoy['hora_ingreso']) ? "Marcar Salida" : "Marcar Ingreso" ?>
            </button>
        </div>
    </div>

    <!-- Historial de asistencias -->
    <h3 class="mt-4">Historial de Asistencias Recientes</h3>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora Ingreso</th>
                    <th>Hora Salida</th>
                    <th>Horas Trabajadas</th>
                    <th>Pago Día</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asistencias as $asistencia) : ?>
                    <tr>
                        <td><?= htmlspecialchars(date("d-m-Y", strtotime($asistencia['fecha']))) ?></td>
                        <td><?= htmlspecialchars($asistencia['hora_ingreso'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($asistencia['hora_salida'] ?? '-', ENT_QUOTES, 'UTF-8', false) ?></td>
                        <td><?= htmlspecialchars($asistencia['horas_trabajadas'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= "$" . htmlspecialchars(number_format($asistencia['pago_dia'], 0, ",", "."), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Total del mes -->
    <h3 class="mt-4">Total Acumulado del Mes</h3>
    <div class="alert alert-info">
        <h4>Total a cobrar: $<?= number_format($total_pago, 0, ",", ".") ?></h4>
    </div>
</div>

<!-- Script para el marcaje de asistencia -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $("#btnMarcarAsistencia").click(function () {
            $.ajax({
                url: "index.php?controller=Usuario&action=marcarAsistencia",
                type: "POST",
                success: function (response) {
                    let data = JSON.parse(response);
                    alert(data.message);
                    location.reload();
                },
                error: function () {
                    alert("Error al procesar la solicitud.");
                }
            });
        });
    });
</script>
