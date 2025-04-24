<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Debes iniciar sesión para enviar mensajes.']);
    exit;
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener los datos enviados
$chatId = $_POST['chat_id'] ?? null;
$contenidoMensaje = $_POST['contenido'] ?? null;
$remitenteId = $_SESSION['user_id'];

if (!$chatId || !$contenidoMensaje) {
    echo json_encode(['error' => 'Datos incompletos.']);
    exit;
}

// Insertar el mensaje en la base de datos
$queryInsert = "
    INSERT INTO Mensaje (ChatID, RemitenteID, ContenidoMensaje, FechaMensaje)
    VALUES (:chatId, :remitenteId, :contenidoMensaje, NOW());
";

$db->query($queryInsert, [
    'chatId' => $chatId,
    'remitenteId' => $remitenteId,
    'contenidoMensaje' => $contenidoMensaje
]);

echo json_encode(['success' => true]);
exit;