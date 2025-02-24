<?php
// public/index.php

// Un enrutador muy básico basado en query strings
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'Auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Construir el nombre del controlador (asumimos que el controlador existe en app/controllers)
$controllerName = $controller . 'Controller';
$controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $obj = new $controllerName;
    if (method_exists($obj, $action)) {
        $obj->{$action}();
    } else {
        echo "La acción '$action' no existe en el controlador '$controllerName'.";
    }
} else {
    echo "El controlador '$controllerName' no existe.";
}
?>
