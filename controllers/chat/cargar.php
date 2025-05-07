<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json'); 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Debes iniciar sesión para acceder a los chats.']);
    exit;
}
$db = App::resolve(Database::class);

$chatId = $_POST['chat_id'] ?? null;
$currentUserId = $_SESSION['user_id']; // Definir una vez

if (!$chatId) {
    echo json_encode(['error' => 'ID del chat no válido.']);
    exit;
}

try {
    // Obtener información del otro usuario en el chat
    $userInfoResult = $db->callProcedure('sp_GetChatParticipantInfo', [$chatId, $currentUserId]);
    
    if (!$userInfoResult || !isset($userInfoResult[0])) {
        echo json_encode(['error' => 'No se encontró información del chat o del participante.']);
        exit;
    }
    $userInfo = $userInfoResult[0];

    if (!empty($userInfo['ImagenPerfil'])) {
        $userInfo['ImagenPerfil'] = 'data:image/jpeg;base64,' . base64_encode($userInfo['ImagenPerfil']);
    } else {
        $userInfo['ImagenPerfil'] = '/Resources/images/perfilPre.jpg'; // Asegúrate que la ruta sea correcta desde la raíz web
    }

    // Obtener mensajes del chat
    $messages = $db->callProcedure('sp_GetChatMessages', [$chatId]);
    

    echo json_encode([
        'NombreUsuario' => $userInfo['NombreUsuario'],
        'ImagenPerfil' => $userInfo['ImagenPerfil'],
        'Mensajes' => $messages ?? [] // Devolver un array vacío si $messages es null o no está definido
    ]);

} catch (\PDOException $e) {
    error_log("Error en cargar.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error del servidor al cargar el chat.']);
}
exit;