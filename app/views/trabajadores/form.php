<?php if(isset($worker) && $worker): ?>
  <input type="hidden" name="id" value="<?= htmlspecialchars($worker['id']) ?>">
<?php endif; ?>

<div class="container-fluid">
<form id="userForm" action="index.php?controller=Trabajador&action=<?= isset($worker) && $worker ? 'update' : 'store' ?>&ajax=1" method="post">
    <?php if(isset($worker) && $worker): ?>
      <input type="hidden" name="id" value="<?= htmlspecialchars($worker['id']) ?>">
    <?php endif; ?>
    <div class="mb-3">
      <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
      <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?= isset($worker) ? htmlspecialchars($worker['nombre_usuario']) : "" ?>" required>
    </div>
    <div class="mb-3">
      <label for="nombre_completo" class="form-label">Nombre Completo</label>
      <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" value="<?= isset($worker) ? htmlspecialchars($worker['nombre_completo']) : "" ?>" required>
    </div>
    <div class="mb-3">
      <label for="rol" class="form-label">Rol</label>
      <select class="form-select" id="rol" name="rol" required>
        <option value="administrador" <?= (isset($worker) && $worker['rol'] == 'administrador') ? "selected" : "" ?>>Administrador</option>
        <option value="trabajador" <?= (isset($worker)) ? ($worker['rol'] == 'trabajador' ? "selected" : "") : "selected" ?>>Trabajador</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="tipo_pago" class="form-label">Tipo de Pago</label>
      <select class="form-select" id="tipo_pago" name="tipo_pago" required>
        <option value="diario" <?= (isset($worker) && $worker['tipo_pago'] == 'diario') ? "selected" : "selected" ?>>Diario</option>
        <option value="por_hora" <?= (isset($worker) && $worker['tipo_pago'] == 'por_hora') ? "selected" : "" ?>>Por Hora</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="valor_dia" class="form-label">Valor del Día</label>
      <input type="number" step="0.01" class="form-control" id="valor_dia" name="valor_dia" value="<?= isset($worker) ? htmlspecialchars($worker['valor_dia']) : "" ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Días de Trabajo</label>
      <?php 
        $diasTrabajados = isset($worker) ? explode(',', $worker['dias_trabajo']) : [];
      ?>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="dias_trabajo[]" value="lunes" id="lunes" <?= in_array('lunes', $diasTrabajados) ? 'checked' : '' ?>>
        <label class="form-check-label" for="lunes">Lunes</label>
      </div>
      <!-- Repite para martes, miércoles, etc. -->
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="dias_trabajo[]" value="martes" id="martes" <?= in_array('martes', $diasTrabajados) ? 'checked' : '' ?>>
        <label class="form-check-label" for="martes">Martes</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="dias_trabajo[]" value="miercoles" id="miercoles" <?= in_array('miercoles', $diasTrabajados) ? 'checked' : '' ?>>
        <label class="form-check-label" for="miercoles">Miércoles</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="dias_trabajo[]" value="jueves" id="jueves" <?= in_array('jueves', $diasTrabajados) ? 'checked' : '' ?>>
        <label class="form-check-label" for="jueves">Jueves</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="dias_trabajo[]" value="viernes" id="viernes" <?= in_array('viernes', $diasTrabajados) ? 'checked' : '' ?>>
        <label class="form-check-label" for="viernes">Viernes</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="dias_trabajo[]" value="sabado" id="sabado" <?= in_array('sabado', $diasTrabajados) ? 'checked' : '' ?>>
        <label class="form-check-label" for="sabado">Sábado</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="dias_trabajo[]" value="domingo" id="domingo" <?= in_array('domingo', $diasTrabajados) ? 'checked' : '' ?>>
        <label class="form-check-label" for="domingo">Domingo</label>
      </div>
    </div>
    <div class="mb-3">
      <label for="contraseña" class="form-label"><?= isset($worker) ? "Nueva Contraseña (dejar en blanco para no cambiar)" : "Contraseña" ?></label>
      <input type="password" class="form-control" id="contraseña" name="contraseña" <?= isset($worker) ? "" : "required" ?>>
    </div>
    <button type="submit" class="btn btn-primary"><?= isset($worker) ? "Actualizar" : "Guardar" ?></button>
  </form>
</div>
