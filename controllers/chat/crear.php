<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar el encabezado para devolver JSON
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Debes iniciar sesión para iniciar un chat.']);
    exit;
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener los datos enviados
$usuarioId = $_SESSION['user_id'];
$destinatarioId = $_POST['destinatario_id'] ?? null;

if (!$destinatarioId) {
    echo json_encode(['error' => 'ID del destinatario no válido.']);
    exit;
}

// ***** NUEVO: Verificar si el usuario intenta chatear consigo mismo *****
if ($usuarioId == $destinatarioId) {
    echo json_encode(['error' => 'No puedes iniciar un chat contigo mismo.']);
    exit;
}

try {
    // Verificar si el chat ya existe (usando LEAST y GREATEST para evitar duplicados)
    $queryCheck = "
        SELECT ChatID FROM Chat 
        WHERE UsuarioID = LEAST(:usuarioId, :destinatarioId) 
        AND DestinatarioID = GREATEST(:usuarioId, :destinatarioId)
    ";
    $chatExistente = $db->query($queryCheck, [
        'usuarioId' => $usuarioId,
        'destinatarioId' => $destinatarioId
    ])->find();

    if ($chatExistente) {
        // Si ya existe, simplemente devolver éxito (o el ID del chat existente si lo necesitas)
        echo json_encode(['success' => 'El chat ya existe.', 'chatId' => $chatExistente['ChatID']]); 
        exit;
    }

    // Crear el nuevo chat (usando LEAST y GREATEST)
    $queryInsert = "
        INSERT INTO Chat (UsuarioID, DestinatarioID) 
        VALUES (LEAST(:usuarioId, :destinatarioId), GREATEST(:usuarioId, :destinatarioId))
    ";
    $db->query($queryInsert, [
        'usuarioId' => $usuarioId,
        'destinatarioId' => $destinatarioId
    ]);

    // Obtener el ID del chat recién creado
    $nuevoChatId = $db->getConnection()->lastInsertId();

    echo json_encode(['success' => 'Chat creado exitosamente.', 'chatId' => $nuevoChatId]);

} catch (Exception $e) {
    // Loggear el error real para depuración
    error_log("Error al crear chat: " . $e->getMessage()); 
    echo json_encode(['error' => 'Error al procesar la solicitud del chat.']);
}