<div class="container-fluid">
    <h1 class="mb-4">Reportes de Asistencias</h1>

    <!-- Formulario de filtros -->
    <form method="GET" action="index.php" class="mb-4">
        <input type="hidden" name="controller" value="Reportes">
        <input type="hidden" name="action" value="index">
        <div class="row">

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
                    <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Monday" id="dia1">
                    <label class="form-check-label" for="dia1">Lunes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Tuesday" id="dia2">
                    <label class="form-check-label" for="dia2">Martes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Wednesday" id="dia3">
                    <label class="form-check-label" for="dia3">Miércoles</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias_semana[]" value="Thursday" id="dia4">
                    <label class="form-check-label" for="dia4">Jueves</label>
                </div>
            </div>

        </div>

        <button type="submit" class="btn btn-secondary mt-3">Filtrar</button>
        <a href="index.php?controller=Reportes&action=index" class="btn btn-outline-secondary mt-3">Limpiar Filtros</a>
    </form>

    <!-- Tabla de Reportes -->
    <div class="table-responsive">
        <table id="tablaReportes" class="table table-bordered table-striped">
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
                    <td><?= date("d-m-Y", strtotime($asistencia['fecha'])) ?></td>
                    <td><?= htmlspecialchars($asistencia['hora_ingreso']) ?></td>
                    <td><?= htmlspecialchars($asistencia['hora_salida'] ?? '') ?></td>
                    <td><?= htmlspecialchars($asistencia['horas_trabajadas'] ?? '') ?></td>
                    <td><?= "$" . number_format($asistencia['pago_dia'], 0, ",", ".") ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('#tablaReportes')) {
        $('#tablaReportes').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: 'Exportar a PDF',
                    title: 'Reporte de Asistencias',
                    orientation: 'landscape', // Mejor en horizontal para más espacio
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(doc) {
                        // 🔹 CABECERA: Información de la empresa
                        doc.content.unshift({
                            text: 'LA METALERA - SISTEMA DE ASISTENCIA',
                            fontSize: 16,
                            alignment: 'center',
                            bold: true,
                            margin: [0, 0, 0, 10]
                        });

                        // 🔹 Agregar detalles de los filtros
                        let filtros = [];
                        filtros.push('Empleado: ' + ($('#empleado').val() ? $('#empleado option:selected').text() : 'Todos'));
                        filtros.push('Fecha Inicio: ' + ($('#fecha_inicio').val() ? $('#fecha_inicio').val() : 'No especificada'));
                        filtros.push('Fecha Fin: ' + ($('#fecha_fin').val() ? $('#fecha_fin').val() : 'No especificada'));

                        let dias = [];
                        $('input[name="dias_semana[]"]:checked').each(function() {
                            dias.push($(this).next().text());
                        });
                        filtros.push('Días de la Semana: ' + (dias.length > 0 ? dias.join(', ') : 'Todos'));

                        doc.content.unshift({
                            text: filtros.join('\n'),
                            fontSize: 10,
                            margin: [0, 0, 0, 10]
                        });

                        // 🔹 Buscar la tabla dentro del contenido del PDF y asignar anchos
                        let tableIndex = doc.content.findIndex(el => el.table);
                        if (tableIndex !== -1) {
                            doc.content[tableIndex].table.widths = ['10%', '20%', '15%', '15%', '15%', '15%', '10%'];
                        }

                        // 🔹 Mejor diseño en encabezados
                        doc.styles.tableHeader = {
                            fillColor: '#007bff',
                            color: 'white',
                            alignment: 'center',
                            bold: true,
                            fontSize: 11
                        };

                        doc.defaultStyle.fontSize = 9;
                        doc.styles.tableHeader.fontSize = 10;
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel',
                    title: 'Reporte de Asistencias'
                },
                {
                    extend: 'csvHtml5',
                    text: 'Exportar a CSV',
                    title: 'Reporte de Asistencias'
                },
                {
                    extend: 'print',
                    text: 'Imprimir',
                    title: 'Reporte de Asistencias'
                }
            ],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron registros",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros en total)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "pageLength": 10,  
            "order": [[2, "desc"]]
        });
    }
});
</script>
