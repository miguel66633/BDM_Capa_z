<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Debes iniciar sesión para acceder a los mensajes.']);
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

// Consultar los mensajes del chat
$queryMessages = "
    SELECT 
        m.MensajeID,
        m.RemitenteID,
        m.ContenidoMensaje,
        m.FechaMensaje
    FROM 
        Mensaje m
    WHERE 
        m.ChatID = :chatId
    ORDER BY 
        m.FechaMensaje ASC;
";

$messages = $db->query($queryMessages, ['chatId' => $chatId])->get();

// Devolver los mensajes como JSON
echo json_encode(['Mensajes' => $messages]); // Simplificado, ya que el JS obtiene el UsuarioID del body.
exit;