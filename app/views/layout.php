<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? $title : 'Sistema de Asistencia' ?></title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
  <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- jQuery (necesario para DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column p-3">
      <!-- Botón de cerrar para móvil -->
      <button class="close-btn d-md-none" id="menu-close"><i class="fas fa-times"></i></button>
      <div class="mb-4 text-center">
        <img src="img\logometalera.png" alt="Logo Empresa" class="logo img-fluid">
        <h4>Sistema de Asistencia (Prueba)</h4>
      </div>
      <div class="search-box">
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
        <input type="text" id="searchWorker" class="form-control" placeholder="Buscar trabajador...">
    </div>
</div>

      <ul class="nav nav-pills flex-column">
  <li class="nav-item">
    <a href="index.php?controller=Dashboard&action=index" class="nav-link <?= (isset($active) && $active === 'dashboard') ? 'active' : '' ?>">
      <i class="fas fa-tachometer-alt me-2"></i><span class="link-text">Dashboard</span>
    </a>
  </li>
  <li class="nav-item">
    <a href="index.php?controller=Trabajador&action=index" class="nav-link <?= (isset($active) && $active === 'trabajadores') ? 'active' : '' ?>">
      <i class="fas fa-users me-2"></i><span class="link-text">Trabajadores</span>
    </a>
  </li>
  <li class="nav-item">
    <a href="index.php?controller=Asistencia&action=index" class="nav-link <?= (isset($active) && $active === 'asistencias') ? 'active' : '' ?>">
      <i class="fas fa-clipboard-check me-2"></i><span class="link-text">Asistencias</span>
    </a>
  </li>
  <li class="nav-item">
    <a href="index.php?controller=Reportes&action=index" class="nav-link <?= (isset($active) && $active === 'reportes') ? 'active' : '' ?>">
      <i class="fas fa-chart-line me-2"></i><span class="link-text">Reportes</span>
    </a>
  </li>
</ul>
      <hr>
      <div class="footer-link">
        <a href="index.php?controller=Auth&action=logout" class="nav-link">
          <i class="fas fa-sign-out-alt me-2"></i><span class="link-text">Cerrar Sesión</span>
        </a>
      </div>
    </nav>
    <!-- Main Content -->
    <div class="content">
      <!-- Navbar para móvil -->
      <nav class="navbar navbar-light bg-light d-md-none mb-3">
        <div class="container-fluid">
          <button class="toggle-mobile btn btn-outline-secondary" id="menu-toggle">
            <i class="fas fa-bars"></i>
          </button>
        </div>
      </nav>
      <!-- Botón de colapso para escritorio -->
      <div class="d-none d-md-block mb-3">
        <button class="toggle-desktop btn btn-outline-secondary" id="sidebar-toggle">
          <i class="fas fa-angle-double-left"></i>
        </button>
      </div>
      <!-- Área de contenido específica -->
      <div class="container-fluid">
        <?= $content ?>
      </div>
      <!-- Footer -->
      <footer class="bg-dark text-white text-center py-3 mt-4">
        <div class="container">
          <p class="mb-0">&copy; <?= date("Y") ?> La Metalera - Todos los derechos reservados</p>
        </div>
      </footer>
    </div>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Variables de elementos
    const menuToggle = document.getElementById('menu-toggle');
    const menuClose = document.getElementById('menu-close');
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');

    // Toggle para el sidebar en móviles
    if(menuToggle) {
      menuToggle.addEventListener('click', () => {
        sidebar.classList.add('active');
      });
    }
    if(menuClose) {
      menuClose.addEventListener('click', () => {
        sidebar.classList.remove('active');
      });
    }
    // Cerrar el sidebar al hacer clic fuera (solo en móvil)
    document.addEventListener('click', (e) => {
      if(window.innerWidth < 768 && sidebar.classList.contains('active')) {
        if(!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
          sidebar.classList.remove('active');
        }
      }
    });
    // Toggle para colapsar/expandir el sidebar en escritorio
    if(sidebarToggle) {
      sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        if (sidebar.classList.contains('collapsed')) {
          sidebarToggle.innerHTML = '<i class="fas fa-angle-double-right"></i>';
        } else {
          sidebarToggle.innerHTML = '<i class="fas fa-angle-double-left"></i>';
        }
      });
    }
  </script>
  <!-- jQuery UI (para autocompletar) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(document).ready(function(){
    $("#searchWorker").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "index.php?controller=Trabajador&action=buscar",
                dataType: "json",
                data: { term: request.term },
                success: function(data) {
                    response($.map(data, function(item) {
                        return {
                            label: item.nombre_completo,
                            value: item.nombre_completo,
                            id: item.id
                        };
                    }));
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            window.location.href = "index.php?controller=Reportes&action=index&empleado=" + ui.item.id;
        },
        open: function(event, ui) {
            var autocomplete = $(".ui-autocomplete");
            var inputOffset = $("#searchWorker").offset();
            
            // 🔥 Ajustamos la posición del autocompletado
            autocomplete.css({
                position: "fixed", // 🔥 Fijamos la posición en la pantalla
                top: inputOffset.top + $("#searchWorker").outerHeight() - $(window).scrollTop() + "px",
                left: inputOffset.left + "px",
                width: $("#searchWorker").outerWidth() + "px",
                zIndex: 99999 // 🔥 Aseguramos que esté sobre todo
            });
        }
    });
});

</script>

</body>
</html>
