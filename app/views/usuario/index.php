<div class="container-fluid">
    <h1 class="mb-4">Bienvenido, <?= htmlspecialchars($_SESSION['usuario']['nombre_usuario']) ?></h1>

    <div class="card">
        <div class="card-body text-center">
            <h4>Marcado de Asistencia</h4>
            <p>
                <?= isset($asistencia_hoy['hora_ingreso']) ? "Entrada: " . $asistencia_hoy['hora_ingreso'] : "Aún no has marcado asistencia hoy." ?>
            </p>
            <p>
                <?= isset($asistencia_hoy['hora_salida']) ? "Salida: " . $asistencia_hoy['hora_salida'] : "" ?>
            </p>
            <button id="btnMarcarAsistencia" class="btn btn-primary" <?= isset($asistencia_hoy['hora_salida']) ? "disabled" : "" ?>>
                <?= isset($asistencia_hoy['hora_ingreso']) ? "Marcar Salida" : "Marcar Ingreso" ?>
            </button>
        </div>
    </div>

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
                        <td><?= date("d-m-Y", strtotime($asistencia['fecha'])) ?></td>
                        <td><?= htmlspecialchars($asistencia['hora_ingreso']) ?></td>
                        <td><?= htmlspecialchars($asistencia['hora_salida'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($asistencia['horas_trabajadas'] ?? '-') ?></td>
                        <td><?= "$" . number_format($asistencia['pago_dia'], 0, ",", ".") ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h3 class="mt-4">Total Acumulado</h3>
    <div class="alert alert-info">
        <h4>Total a cobrar: $<?= number_format($total_pago, 0, ",", ".") ?></h4>
    </div>
</div>

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
                }
            });
        });
    });
</script>
