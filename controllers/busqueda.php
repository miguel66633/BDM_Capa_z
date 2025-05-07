<?php
//es el buscar el usuario en la barra lateral de home
use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$searchTermFull = isset($_GET['term']) ? trim($_GET['term']) : null;

try {
    $usuarios = $db->callProcedure('sp_BuscarUsuariosLateral', [$searchTermFull]);
} catch (\PDOException $e) {
    error_log("Error en busqueda.php al llamar sp_BuscarUsuariosLateral: " . $e->getMessage());
    $usuarios = []; // En caso de error, asegurar que $usuarios sea un array vacío
}