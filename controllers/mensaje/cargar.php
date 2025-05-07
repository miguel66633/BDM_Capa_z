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

header('Content-Type: application/json');
// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener el ID del chat desde la solicitud
$chatId = $_POST['chat_id'] ?? null;

if (!$chatId) {
    echo json_encode(['error' => 'ID del chat no válido.']);
    exit;
}

try {
    $messages = $db->callProcedure('sp_GetChatMessages', [$chatId]);
    echo json_encode(['Mensajes' => $messages ?? []]); // Asegurar que siempre sea un array

} catch (\PDOException $e) {
    error_log("Error en controllers/mensaje/cargar.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error del servidor al cargar los mensajes.']);
}
exit;