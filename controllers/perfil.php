<?php

use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
$currentUserId = $_SESSION['user_id'] ?? null;
if (!$currentUserId) {
    header('Location: /inicioSesion');
    exit;
}

// Obtener el ID del perfil a visualizar desde la URL
$profileUserId = $params['id'] ?? null;
if (!$profileUserId) {
    abort(404);
}

// Determinar si el usuario actual está viendo su propio perfil
$isOwner = ($currentUserId == $profileUserId);

// --- Obtener Información del Perfil del Usuario Visualizado usando Stored Procedure ---
$usuario = null;
$estaSiguiendo = false; // Valor por defecto

try {
    // Ahora pasamos $currentUserId también
    $resultUsuario = $db->callProcedure('sp_GetUsuarioPerfilDetalles', [$profileUserId, $currentUserId]);
    if ($resultUsuario && isset($resultUsuario[0])) {
        $usuario = $resultUsuario[0];
        // El SP ahora devuelve 'EstaSiguiendo'
        $estaSiguiendo = (bool)($usuario['EstaSiguiendo'] ?? false);
    }
} catch (\PDOException $e) {
    error_log("Error en controllers/perfil.php al llamar sp_GetUsuarioPerfilDetalles: " . $e->getMessage());
}

if (!$usuario) {
    abort(404);
}
// --- Obtener Publicaciones del Usuario Visualizado (con conteos y estados del *VISITANTE*) ---
$publicaciones = []; // Inicializar
try {
    $publicaciones = $db->callProcedure('sp_GetPerfilFeed', [$profileUserId, $currentUserId]);
} catch (\PDOException $e) {
    error_log("Error en controllers/perfil.php al llamar sp_GetPerfilFeed: " . $e->getMessage());
}

// Pasar los datos a la vista
view("perfil.view.php", [
    'usuario' => $usuario, // Contiene SeguidosCount, SeguidoresCount
    'publicaciones' => $publicaciones,
    'isOwner' => $isOwner,
    'currentUserId' => $currentUserId,
    'estaSiguiendo' => $estaSiguiendo // Nueva variable para el estado del botón
]);