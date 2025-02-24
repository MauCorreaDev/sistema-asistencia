<div class="container-fluid">
  <h1 class="mb-4">Gestión de Asistencias</h1>
  <!-- Botón para agregar asistencia que abrirá un modal -->
  <button class="btn btn-primary mb-3" id="btnAddAsistencia">
    <i class="fas fa-plus"></i> Agregar Asistencia
  </button>
  
  <!-- Contenedor responsive para la tabla -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="asistenciasTable">
      <thead>
         <tr>
            <th>ID</th>
            <th>Empleado</th>
            <th>Fecha</th>
            <th>Hora Ingreso</th>
            <th>Hora Salida</th>
            <th>Horas Trabajadas</th>
            <th>Pago Día</th>
            <th>Acciones</th>
         </tr>
      </thead>
      <tbody>
         <?php foreach($asistencias as $asistencia): ?>
         <tr id="asistenciaRow<?= $asistencia['id'] ?>">
            <td><?= htmlspecialchars($asistencia['id']) ?></td>
            <td><?= htmlspecialchars($asistencia['nombre_completo']) ?></td>
            <td><?= date("d-m-y", strtotime($asistencia['fecha'])) ?></td>
            <td><?= htmlspecialchars($asistencia['hora_ingreso']) ?></td>
            <td><?= htmlspecialchars($asistencia['hora_salida']) ?></td>
            <td><?= htmlspecialchars($asistencia['horas_trabajadas']) ?></td>
            <td><?= "$" . number_format($asistencia['pago_dia'], 0, ",", ".") ?></td>
            <td>
               <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $asistencia['id'] ?>">
                 <i class="fas fa-edit"></i> Editar
               </button>
               <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $asistencia['id'] ?>">
                 <i class="fas fa-trash-alt"></i> Eliminar
               </button>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal para Agregar/Editar Asistencia -->
<div class="modal fade" id="asistenciaModal" tabindex="-1" aria-labelledby="asistenciaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
       <div class="modal-header">
          <h5 class="modal-title" id="asistenciaModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
       </div>
       <div class="modal-body">
          <!-- Se cargará el formulario vía AJAX -->
       </div>
    </div>
  </div>
</div>

<!-- jQuery (para AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    // Abrir modal para agregar asistencia
    $("#btnAddAsistencia").click(function(){
       $("#asistenciaModalLabel").text("Agregar Asistencia");
       $.ajax({
          url: "index.php?controller=Asistencia&action=create&ajax=1",
          type: "GET",
          success: function(response){
             $("#asistenciaModal .modal-body").html(response);
             $("#asistenciaModal").modal("show");
          }
       });
    });
    
    // Abrir modal para editar asistencia
    $(".btn-edit").click(function(){
       var asistenciaId = $(this).data("id");
       $("#asistenciaModalLabel").text("Editar Asistencia");
       $.ajax({
          url: "index.php?controller=Asistencia&action=edit&id=" + asistenciaId + "&ajax=1",
          type: "GET",
          success: function(response){
             $("#asistenciaModal .modal-body").html(response);
             $("#asistenciaModal").modal("show");
          }
       });
    });
    
    // Eliminar asistencia vía AJAX
    $(".btn-delete").click(function(){
       if(confirm("¿Estás seguro de eliminar esta asistencia?")){
          var asistenciaId = $(this).data("id");
          $.ajax({
             url: "index.php?controller=Asistencia&action=delete&id=" + asistenciaId,
             type: "GET",
             success: function(){
                $("#asistenciaRow" + asistenciaId).remove();
             }
          });
       }
    });
    
    // Envío del formulario vía AJAX
    $(document).on("submit", "#asistenciaForm", function(e){
       e.preventDefault();
       var form = $(this);
       $.ajax({
          url: form.attr("action"),
          type: "POST",
          data: form.serialize(),
          success: function(response){
             console.log("Respuesta:", response);
             if(response.trim() === "success"){
                $("#asistenciaModal").modal("hide");
                location.reload();
             } else {
                alert("Error al guardar: " + response);
             }
          },
          error: function(){
             alert("Error en la petición AJAX.");
          }
       });
    });
});
</script>
