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
    echo json_encode(['error' => 'ID del destinatario no proporcionado.']);
    exit;
}

// ***** NUEVO: Verificar si el usuario intenta chatear consigo mismo *****
if ($usuarioId == $destinatarioId) {
    echo json_encode(['error' => 'No puedes iniciar un chat contigo mismo.']);
    exit;
}

try {
    // Llamar al Stored Procedure
    $result = $db->callProcedure('sp_GetOrCreateChat', [$usuarioId, $destinatarioId]);

    if ($result && isset($result[0])) {
        $spResponse = $result[0]; // El SP devuelve una sola fila

        if ($spResponse['Success']) {
            // Si el SP indica éxito (ya sea porque creó o porque ya existía)
            echo json_encode([
                'success' => $spResponse['StatusMessage'], 
                'chatId' => $spResponse['ChatID']
            ]);
        } else {
            // Si el SP indica un fallo (ej. no se pudo crear, o el caso de chatear consigo mismo si se quitara la validación PHP)
            echo json_encode(['error' => $spResponse['StatusMessage'] ?? 'Error al procesar la solicitud del chat.']);
        }
    } else {
        // Esto no debería ocurrir si el SP siempre devuelve una fila.
        error_log("Error inesperado: sp_GetOrCreateChat no devolvió un resultado válido para UsuarioID: {$usuarioId}, DestinatarioID: {$destinatarioId}");
        echo json_encode(['error' => 'Error inesperado del servidor al procesar la solicitud del chat.']);
    }

} catch (Exception $e) {
    // Loggear el error real para depuración
    error_log("Error al llamar sp_GetOrCreateChat: " . $e->getMessage()); 
    echo json_encode(['error' => 'Error del servidor al procesar la solicitud del chat.']);
}