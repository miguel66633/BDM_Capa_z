<?php

session_start();
const BASE_PATH = __DIR__.'/../';

require BASE_PATH.'Core/functions.php';


spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require base_path("{$class}.php");
});

require base_path('bootstrap.php');

$router = new \Core\Router();
$routes = require base_path('routes.php');

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

// Verificar si es una solicitud a la API
if (strpos($uri, '/api') === 0) {
    require base_path('api.php');
    exit;
}

// Si no es API, manejar como ruta normal
$router->route($uri, $method);