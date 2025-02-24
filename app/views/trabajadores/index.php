<!-- app/views/trabajadores/index.php -->
<div class="container-fluid">
  <h1 class="mb-4">Gestión de Trabajadores</h1>
  <!-- Botón para agregar trabajador que abrirá un modal -->
  <button class="btn btn-primary mb-3" id="btnAddWorker">
    <i class="fas fa-plus"></i> Agregar Trabajador
  </button>

  <!-- Contenedor responsive para la tabla -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="workersTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre de Usuario</th>
          <th>Nombre Completo</th>
          <th>Tipo de Pago</th>
          <th>Valor Día</th>
          <th>Días de Trabajo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($workers as $worker): ?>
        <tr id="workerRow<?= $worker['id'] ?>">
          <td><?= htmlspecialchars($worker['id']) ?></td>
          <td><?= htmlspecialchars($worker['nombre_usuario']) ?></td>
          <td><?= htmlspecialchars($worker['nombre_completo']) ?></td>
          <td><?= htmlspecialchars($worker['tipo_pago']) ?></td>
          <td><?= "$" . number_format($worker['valor_dia'], 0, ",", ".") ?></td>
          <td><?= htmlspecialchars($worker['dias_trabajo']) ?></td>
          <td>
            <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $worker['id'] ?>">
              <i class="fas fa-edit"></i> Editar
            </button>
            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $worker['id'] ?>">
              <i class="fas fa-trash-alt"></i> Eliminar
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal para Agregar/Editar Trabajador -->
<div class="modal fade" id="workerModal" tabindex="-1" aria-labelledby="workerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="workerModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Aquí se cargará el formulario vía AJAX -->
      </div>
    </div>
  </div>
</div>

<!-- jQuery (necesario para AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
    // Abrir modal para agregar trabajador
    $("#btnAddWorker").click(function(){
        $("#workerModalLabel").text("Agregar Trabajador");
        $.ajax({
            url: "index.php?controller=Trabajador&action=create&ajax=1",
            type: "GET",
            success: function(response){
                $("#workerModal .modal-body").html(response);
                $("#workerModal").modal("show");
            }
        });
    });

    // Abrir modal para editar trabajador
    $(".btn-edit").click(function(){
        var workerId = $(this).data("id");
        $("#workerModalLabel").text("Editar Trabajador");
        $.ajax({
            url: "index.php?controller=Trabajador&action=edit&id=" + workerId + "&ajax=1",
            type: "GET",
            success: function(response){
                $("#workerModal .modal-body").html(response);
                $("#workerModal").modal("show");
            }
        });
    });

    // Eliminar trabajador vía AJAX
    $(".btn-delete").click(function(){
        if(confirm("¿Estás seguro de eliminar este trabajador?")){
            var workerId = $(this).data("id");
            $.ajax({
                url: "index.php?controller=Trabajador&action=delete&id=" + workerId,
                type: "GET",
                success: function(){
                    $("#workerRow" + workerId).remove();
                }
            });
        }
    });

    // Interceptar el envío del formulario (dentro del modal) para usar AJAX
    $(document).on("submit", "#userForm", function(e){
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function(response){
                $("#workerModal").modal("hide");
                // Para simplificar se recarga la página, pero podrías actualizar la tabla vía AJAX
                location.reload();
            }
        });
    });
});
</script>
