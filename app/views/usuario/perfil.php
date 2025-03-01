<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="text-center mb-4">Mi Perfil</h2>

            <!-- 🟢 Alertas de éxito y error -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg p-4">
                <div class="row g-4 align-items-center">
                    <!-- 📸 Columna de Foto de Perfil -->
                    <div class="col-md-4 text-center">
                        <label for="fotoPerfil" class="profile-pic-label">
                            <?php if (!empty($usuario['foto_perfil'])): ?>
                                <img id="previewFoto" class="profile-pic"
                                    src="uploads/usuarios/<?= $usuario['foto_perfil'] ?>" 
                                    alt="Foto de Perfil">
                            <?php else: ?>
                                <i class="fas fa-user-circle profile-icon"></i> 
                            <?php endif; ?>
                        </label>
                    </div>

                    <!-- 📄 Columna de Datos del Usuario -->
                    <div class="col-md-8">
                        <h4 class="fw-bold text-primary"><?= htmlspecialchars($usuario['nombre_completo']) ?></h4>

                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="table-label"><i class="fas fa-envelope"></i> Correo:</th>
                                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                </tr>
                                <tr>
                                    <th class="table-label"><i class="fas fa-phone"></i> Celular:</th>
                                    <td><?= htmlspecialchars($usuario['celular']) ?></td>
                                </tr>
                                <tr>
                                    <th class="table-label"><i class="fas fa-money-bill-wave"></i> Tipo de Pago:</th>
                                    <td><?= ucfirst($usuario['tipo_pago']) ?></td>
                                </tr>
                                <tr>
                                    <th class="table-label"><i class="fas fa-dollar-sign"></i> Valor del Día:</th>
                                    <td>$<?= number_format($usuario['valor_dia'], 0, ",", ".") ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- 📌 Formulario para Editar Perfil -->
                <form method="POST" action="index.php?controller=Usuario&action=actualizarPerfil" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="celular" class="form-label">Número Celular</label>
                            <input type="text" class="form-control" name="celular" value="<?= htmlspecialchars($usuario['celular']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="fotoPerfil" class="form-label">Actualizar Foto de Perfil</label>
                        <input type="file" class="form-control" name="fotoPerfil" accept="image/*" onchange="mostrarVistaPrevia(event)">
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 🎨 Estilos Mejorados -->
<style>
/* 🔹 Contenedor de la Foto de Perfil */
.profile-pic-label {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* 🔹 Imagen de Perfil */
.profile-pic {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #007bff;
    transition: transform 0.3s ease-in-out;
}

/* 🔹 Ícono de usuario si no hay foto */
.profile-icon {
    font-size: 120px;
    color: #6c757d;
}

/* 🔹 Hover en la imagen */
.profile-pic:hover {
    transform: scale(1.05);
}

/* 🔹 Asegurar que los textos no se desborden en móviles */
.table-responsive {
    overflow-x: auto;
}

/* 🔹 Etiquetas dentro de la tabla */
.table-label {
    white-space: nowrap;
    font-weight: 600;
    color: #6c757d;
}

/* 🔹 Responsividad */
@media (max-width: 576px) {
    .profile-pic {
        width: 120px;
        height: 120px;
    }
    .profile-icon {
        font-size: 100px;
    }
    .table-label {
        font-size: 14px;
    }
}
</style>

<!-- 📌 Script para Vista Previa de Imagen -->
<script>
function mostrarVistaPrevia(event) {
    var input = event.target;
    var reader = new FileReader();

    reader.onload = function() {
        var preview = document.getElementById('previewFoto');
        if (preview) {
            preview.src = reader.result;
        } else {
            // Si no hay imagen previa, reemplazamos el ícono con la imagen cargada
            let container = document.querySelector(".profile-pic-label");
            container.innerHTML = `<img id="previewFoto" class="profile-pic" src="${reader.result}" alt="Foto de Perfil">`;
        }
    };

    reader.readAsDataURL(input.files[0]);
}
</script>
