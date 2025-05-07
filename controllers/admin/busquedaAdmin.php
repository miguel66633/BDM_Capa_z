<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

// Obtener el término de búsqueda (si existe)
$searchTerm = isset($_GET['term']) ? $_GET['term'] : null;

if ($searchTerm === '') {
    $searchTerm = null;
}
try {
    $usuarios = $db->callProcedure('sp_BuscarAdminUsuarios', [$searchTerm]);

} catch (\PDOException $e) {

    error_log("Error en busquedaAdmin.php al llamar sp_BuscarAdminUsuarios: " . $e->getMessage());
    $usuarios = []; 

}