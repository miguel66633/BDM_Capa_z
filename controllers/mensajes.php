<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /inicioSesion');
    exit;
}

$db = App::resolve(Database::class);
$usuarioId = $_SESSION['user_id'];
$chats = [];

try {
    // Llamar al Stored Procedure para obtener los chats del usuario
    $chats = $db->callProcedure('sp_GetUsuarioChatsConDetalles', [$usuarioId]);
} catch (\PDOException $e) {
    error_log("Error en controllers/mensajes.php al llamar sp_GetUsuarioChatsConDetalles: " . $e->getMessage());
    // $chats ya está inicializado como array vacío
}

// Procesar la imagen de perfil (este paso se mantiene en PHP)
foreach ($chats as &$chat) {
    if (!empty($chat['ImagenPerfil'])) {
        // Usar la función formatarImagen que creamos anteriormente
        $chat['ImagenPerfil'] = formatarImagen($chat['ImagenPerfil'], '/Resources/images/perfilPre.jpg');
    } else {
        $chat['ImagenPerfil'] = '/Resources/images/perfilPre.jpg';
    }
}
unset($chat); // Romper la referencia del último elemento

view("mensajes.view.php", [
    'chats' => $chats
]);