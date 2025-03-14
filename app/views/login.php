<!-- app/views/login.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Sistema de Asistencia</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts (Roboto) -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500&display=swap" rel="stylesheet">
  <!-- Estilos personalizados -->
  <link href="css/style.css" rel="stylesheet">
  <style>
    /* public/css/style.css */
body {
    background: #f5f7fa;
    font-family: 'Roboto', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

.login-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    width: 100%;
    max-width: 400px;
}

.card-header {
    background-color: #2E7D32; /* Verde oscuro */
    padding: 20px;
    text-align: center;
    position: relative;
}

.card-header h2 {
    color: #fff;
    margin: 0;
    font-weight: 500;
}

.card-body {
    padding: 30px;
}

.form-control {
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: none;
}

.form-control:focus {
    border-color: #2E7D32;
    box-shadow: 0 0 5px rgba(46,125,50,0.5);
}

.btn-primary {
    background-color: #2E7D32;
    border-color: #2E7D32;
    border-radius: 4px;
    font-weight: bold;
}

.btn-primary:hover {
    background-color: #26702c;
    border-color: #26702c;
}

.custom-checkbox {
    display: flex;
    align-items: center;
}

.custom-checkbox input {
    margin-right: 5px;
}

.logo {
    max-width: 100px;
    display: block;
    margin: 0 auto 15px auto;
}

  </style>
</head>
<body>
  <div class="login-card">
    <div class="card-header">
      <img src="img/logometalera.png" alt="Logo Empresa" class="logo">
      <h2>Iniciar Sesión</h2>
    </div>
    <div class="card-body">
      <?php if(isset($error)) { ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php } ?>
      <form action="index.php?controller=Auth&action=login" method="post">
        <div class="mb-3">
          <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
          <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
        </div>
        <div class="mb-3">
          <label for="contraseña" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="contraseña" name="contraseña" required>
        </div>
        <div class="mb-3 custom-checkbox">
          <input type="checkbox" id="recordarme" name="recordarme">
          <label for="recordarme">Recordarme</label>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Ingresar</button>
        </div>
        <div class="mt-3 text-center">
          <a href="#">¿Olvidaste tu contraseña?</a>
        </div>
      </form>
    </div>
  </div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
