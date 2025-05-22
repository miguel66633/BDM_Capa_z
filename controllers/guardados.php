<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener el ID del usuario actual
$usuarioId = $_SESSION['user_id'] ?? null;

// Verificar si el usuario está autenticado (redirigir si no)
if (!$usuarioId) {
    header('Location: /inicioSesion');
    exit;
}

$publicacionesGuardadas = []; 

try {
    // Llamar al Stored Procedure para obtener las publicaciones guardadas
    $publicacionesGuardadas = $db->callProcedure('sp_GetPublicacionesGuardadasUsuario', [$usuarioId]);
} catch (\PDOException $e) {
    error_log("Error en controllers/guardados.php al llamar sp_GetPublicacionesGuardadasUsuario: " . $e->getMessage());
    // $publicacionesGuardadas ya está inicializado como array vacío
}


// Pasar las publicaciones guardadas a la vista
view("guardados.view.php", [
    'heading' => 'Guardados',
    'publicaciones' => $publicacionesGuardadas
]);