<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Debes iniciar sesión para acceder a los chats.']);
    exit;
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener el ID del chat desde la solicitud
$chatId = $_POST['chat_id'] ?? null;

if (!$chatId) {
    echo json_encode(['error' => 'ID del chat no válido.']);
    exit;
}

$queryUser = "
    SELECT 
        u.UsuarioID,
        u.NombreUsuario,
        u.ImagenPerfil
    FROM 
        Chat c
    INNER JOIN 
        Usuario u ON (u.UsuarioID = c.DestinatarioID AND c.ChatID = :chatId)
        OR (u.UsuarioID = c.UsuarioID AND c.ChatID = :chatId)
    WHERE 
        u.UsuarioID != :currentUserId
    LIMIT 1;
";

$userInfo = $db->query($queryUser, [
    'chatId' => $chatId,
    'currentUserId' => $_SESSION['user_id']
])->find();

if (!$userInfo) {
    echo json_encode(['error' => 'No se encontró información del chat.']);
    exit;
}

// Convertir la imagen a base64 si existe
if (!empty($userInfo['ImagenPerfil'])) {
    $userInfo['ImagenPerfil'] = 'data:image/jpeg;base64,' . base64_encode($userInfo['ImagenPerfil']);
} else {
    $userInfo['ImagenPerfil'] = 'Resources/images/perfilPre.jpg'; // Imagen por defecto
}

// Consultar los mensajes del chat
$queryMessages = "
    SELECT 
        m.MensajeID,
        m.RemitenteID,
        m.ContenidoMensaje,
        m.FechaMensaje,
        u.NombreUsuario AS RemitenteNombre
    FROM 
        Mensaje m
    INNER JOIN 
        Usuario u ON m.RemitenteID = u.UsuarioID
    WHERE 
        m.ChatID = :chatId
    ORDER BY 
        m.FechaMensaje ASC;
";

$messages = $db->query($queryMessages, ['chatId' => $chatId])->get();

// Devolver la información del usuario y los mensajes como JSON
echo json_encode([
    'NombreUsuario' => $userInfo['NombreUsuario'],
    'ImagenPerfil' => $userInfo['ImagenPerfil'],
    'Mensajes' => $messages
]);
exit;