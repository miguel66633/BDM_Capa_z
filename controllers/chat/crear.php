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

try {
    // Verificar si el chat ya existe
    $queryCheck = "
        SELECT * FROM Chat 
        WHERE UsuarioID = LEAST(:usuarioId, :destinatarioId) 
        AND DestinatarioID = GREATEST(:usuarioId, :destinatarioId)
    ";
    $chatExistente = $db->query($queryCheck, [
        'usuarioId' => $usuarioId,
        'destinatarioId' => $destinatarioId
    ])->find();

    if ($chatExistente) {
        echo json_encode(['error' => 'El chat ya existe.']);
        exit;
    }

    // Crear el nuevo chat
    $queryInsert = "
        INSERT INTO Chat (UsuarioID, DestinatarioID) 
        VALUES (LEAST(:usuarioId, :destinatarioId), GREATEST(:usuarioId, :destinatarioId))
    ";
    $db->query($queryInsert, [
        'usuarioId' => $usuarioId,
        'destinatarioId' => $destinatarioId
    ]);

    echo json_encode(['success' => 'Chat creado exitosamente.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al crear el chat: ' . $e->getMessage()]);
}