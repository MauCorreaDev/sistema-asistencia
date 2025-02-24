<div class="container-fluid">
  <form id="asistenciaForm" action="index.php?controller=Asistencia&action=<?= isset($asistencia) && $asistencia ? 'update' : 'store' ?>&ajax=1" method="post">
    <?php if(isset($asistencia) && $asistencia): ?>
      <input type="hidden" name="id" value="<?= htmlspecialchars($asistencia['id']) ?>">
    <?php endif; ?>
    <div class="mb-3">
       <label for="usuario_id" class="form-label">Empleado</label>
       <select class="form-select" id="usuario_id" name="usuario_id" required>
         <option value="">Selecciona un empleado</option>
         <?php foreach($usuarios as $usuario): ?>
           <?php $selected = (isset($asistencia) && $asistencia['usuario_id'] == $usuario['id']) ? "selected" : ""; ?>
           <option value="<?= $usuario['id'] ?>" <?= $selected ?>><?= htmlspecialchars($usuario['nombre_completo']) ?></option>
         <?php endforeach; ?>
       </select>
    </div>
    <div class="mb-3">
       <label for="fecha" class="form-label">Fecha</label>
       <input type="date" class="form-control" id="fecha" name="fecha" value="<?= isset($asistencia) ? htmlspecialchars($asistencia['fecha']) : "" ?>" required>
    </div>
    <div class="mb-3">
       <label for="hora_ingreso" class="form-label">Hora de Ingreso</label>
       <input type="time" class="form-control" id="hora_ingreso" name="hora_ingreso" value="<?= isset($asistencia) ? htmlspecialchars($asistencia['hora_ingreso']) : "" ?>" required>
    </div>
    <div class="mb-3">
       <label for="hora_salida" class="form-label">Hora de Salida</label>
       <input type="time" class="form-control" id="hora_salida" name="hora_salida" value="<?= isset($asistencia) ? htmlspecialchars($asistencia['hora_salida']) : "" ?>" required>
    </div>
    <!-- Opcional: podríamos mostrar un campo para horas trabajadas, pero aquí se calcula automáticamente -->
    <button type="submit" class="btn btn-primary"><?= isset($asistencia) ? "Actualizar" : "Guardar" ?></button>
  </form>
</div>
