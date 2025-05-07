<?php

use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /inicioSesion');
    exit;
}

$usuarioId = $_SESSION['user_id'];

$postId = $_GET['id'] ?? null;

if (!$postId) {
    header('Location: /inicio');
    exit();
}

$publicacion = null;
$respuestas = [];
$errors = $_SESSION['errors'] ?? [];
$successMessage = $_SESSION['success'] ?? null;
unset($_SESSION['errors']);
unset($_SESSION['success']); 


try {
    $resultPublicacion = $db->callProcedure('sp_GetPublicacionDetalles', [$postId, $usuarioId]);
    if ($resultPublicacion && isset($resultPublicacion[0])) {
        $publicacion = $resultPublicacion[0];
    }
} catch (\PDOException $e) {
    error_log("Error en controllers/post.php al llamar sp_GetPublicacionDetalles: " . $e->getMessage());
}


if (!$publicacion) {
    header('Location: /inicio');
    exit();
}

try {
    $respuestas = $db->callProcedure('sp_GetPublicacionRespuestas', [$postId, $usuarioId]);
} catch (\PDOException $e) {
    error_log("Error en controllers/post.php al llamar sp_GetPublicacionRespuestas: " . $e->getMessage());
}

view("post.view.php", [
    'publicacion' => $publicacion,
    'respuestas' => $respuestas,
    'errors' => $errors,
    'successMessage' => $successMessage
]);