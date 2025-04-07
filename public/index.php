<?php

session_start();
const BASE_PATH = __DIR__.'/../';

require BASE_PATH.'Core/functions.php';

// Manejar solicitudes de recursos estáticos
if (isset($_GET['file'])) {
    require BASE_PATH . 'static.php';
    exit;
}

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require base_path("{$class}.php");
});

require base_path('bootstrap.php');
require base_path('api.php');

$router = new \Core\Router();
$routes = require base_path('routes.php');

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);