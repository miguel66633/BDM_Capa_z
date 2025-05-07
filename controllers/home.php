<?php

use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener el ID del usuario actual
$usuarioId = $_SESSION['user_id'] ?? null;

if (!$usuarioId) {
    // Esto no debería ocurrir debido al middleware 'auth' en la ruta /inicio
   error_log("Acceso a home.php sin user_id en sesión, a pesar del middleware 'auth'.");
   // Podrías redirigir o manejar como un error, aunque el middleware debería prevenirlo.
   header('Location: /inicioSesion');
   exit;
}

$publicaciones = []; 

try {
    // Llamar al Stored Procedure para obtener las publicaciones para el home
    $publicaciones = $db->callProcedure('sp_GetPublicacionesHome', [$usuarioId]);
} catch (\PDOException $e) {
    error_log("Error en controllers/home.php al llamar sp_GetPublicacionesHome: " . $e->getMessage());
    // $publicaciones ya está inicializado como array vacío
}

// Pasar las publicaciones a la vista
view("home.view.php", [
    'publicaciones' => $publicaciones
]);