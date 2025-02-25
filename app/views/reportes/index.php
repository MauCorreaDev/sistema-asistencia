    <div class="container-fluid">
    <h1 class="mb-4">Reportes de Asistencias</h1>
    
    <!-- Formulario de filtros -->
    <form method="GET" action="index.php" class="mb-4">
        <input type="hidden" name="controller" value="Reportes">
        <input type="hidden" name="action" value="index">
        <div class="row">
        <div class="col-md-3">
            <label for="prefiltro" class="form-label">Pre Filtro</label>
            <select class="form-select" id="prefiltro" name="prefiltro">
            <option value="ninguno" <?= (isset($_GET['prefiltro']) && $_GET['prefiltro'] == 'ninguno') ? 'selected' : '' ?>>Sin Prefiltro</option>
            <option value="este_mes" <?= (isset($_GET['prefiltro']) && $_GET['prefiltro'] == 'este_mes') ? 'selected' : '' ?>>Este Mes</option>
            <option value="esta_semana" <?= (isset($_GET['prefiltro']) && $_GET['prefiltro'] == 'esta_semana') ? 'selected' : '' ?>>Esta Semana</option>
            <option value="mes_anterior" <?= (isset($_GET['prefiltro']) && $_GET['prefiltro'] == 'mes_anterior') ? 'selected' : '' ?>>Mes Anterior</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="empleado" class="form-label">Empleado</label>
            <select class="form-select" id="empleado" name="empleado">
            <option value="">Todos</option>
            <?php foreach($trabajadores as $trabajador): ?>
                <option value="<?= $trabajador['id'] ?>" <?= (isset($_GET['empleado']) && $_GET['empleado'] == $trabajador['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($trabajador['nombre_completo']) ?>
                </option>
            <?php endforeach; ?>
            </select>
        </div>
        <!-- Opcional: puedes dejar estas fechas editables, se sobrescribirán si se elige un prefiltro -->
        <div class="col-md-3">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '' ?>">
        </div>
        <div class="col-md-3">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '' ?>">
        </div>
        <div class="col-md-3 mt-3">
            <label class="form-label">Días de la Semana</label>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Monday" id="dia1" <?= (isset($_GET['dias_semana']) && in_array("Monday", $_GET['dias_semana'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="dia1">Lunes</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Tuesday" id="dia2" <?= (isset($_GET['dias_semana']) && in_array("Tuesday", $_GET['dias_semana'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="dia2">Martes</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Wednesday" id="dia3" <?= (isset($_GET['dias_semana']) && in_array("Wednesday", $_GET['dias_semana'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="dia3">Miércoles</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Thursday" id="dia4" <?= (isset($_GET['dias_semana']) && in_array("Thursday", $_GET['dias_semana'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="dia4">Jueves</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Friday" id="dia5" <?= (isset($_GET['dias_semana']) && in_array("Friday", $_GET['dias_semana'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="dia5">Viernes</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Saturday" id="dia6" <?= (isset($_GET['dias_semana']) && in_array("Saturday", $_GET['dias_semana'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="dia6">Sábado</label>
            </div>
        </div>
        </div>
        <button type="submit" class="btn btn-secondary mt-3">Filtrar</button>
        <a href="index.php?controller=Reportes&action=index" class="btn btn-outline-secondary mt-3">Limpiar Filtros</a>
        <!-- Botón para exportar a PDF (placeholder) -->
        <a id="exportarPDF" class="btn btn-success mt-3">
  Exportar a PDF
</a>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("exportarPDF").addEventListener("click", function(event) {
        event.preventDefault(); // Evita la recarga de la página
        
        let form = document.querySelector("form");
        let params = new URLSearchParams(new FormData(form));

        // Eliminar parámetros innecesarios de la URL
        params.delete("controller");
        params.delete("action");

        // Construir la URL correcta para exportar el PDF
        let url = "index.php?controller=Reportes&action=exportar&" + params.toString();

        // Redirigir a la URL para descargar el PDF
        window.location.href = url;
    });
});
</script>



    </form>
    
    <!-- Tabla de Reportes -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Hora Ingreso</th>
                <th>Hora Salida</th>
                <th>Horas Trabajadas</th>
                <th>Pago Día</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($asistencias as $asistencia): ?>
            <tr>
                <td><?= htmlspecialchars($asistencia['id']) ?></td>
                <td><?= htmlspecialchars($asistencia['nombre_completo']) ?></td>
                <td><?= date("d-m-y", strtotime($asistencia['fecha'])) ?></td>
                <td><?= htmlspecialchars($asistencia['hora_ingreso']) ?></td>
                <td><?= htmlspecialchars($asistencia['hora_salida']) ?></td>
                <td><?= htmlspecialchars($asistencia['horas_trabajadas']) ?></td>
                <td><?= "$" . number_format($asistencia['pago_dia'], 0, ",", ".") ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>
    </div>
