<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar el encabezado para devolver JSON ANTES de cualquier salida
header('Content-Type: application/json');

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

if (!$chatId) {
    echo json_encode(['error' => 'ID del chat no proporcionado.']);
    exit;
}
if (!$contenidoMensaje || trim($contenidoMensaje) === '') {
    echo json_encode(['error' => 'El contenido del mensaje no puede estar vacío.']);
    exit;
}
try {
    // Llamar al Stored Procedure
    $result = $db->callProcedure('sp_EnviarMensaje', [$remitenteId, $contenidoMensaje, $chatId]);

    if ($result && isset($result[0])) {
        $spResponse = $result[0];

        if ($spResponse['Success']) {
            echo json_encode([
                'success' => true, 
                'message' => $spResponse['StatusMessage'],
                'mensajeId' => $spResponse['MensajeID'] // Opcional: devolver el ID del nuevo mensaje
            ]);
        } else {
            echo json_encode(['error' => $spResponse['StatusMessage'] ?? 'Error al enviar el mensaje.']);
        }
    } else {
        // Esto no debería ocurrir si el SP siempre devuelve una fila.
        error_log("Error inesperado: sp_EnviarMensaje no devolvió un resultado válido para ChatID: {$chatId}, RemitenteID: {$remitenteId}");
        echo json_encode(['error' => 'Error inesperado del servidor al enviar el mensaje.']);
    }

} catch (\PDOException $e) { // Capturar específicamente PDOException
    error_log("Error en controllers/mensaje/enviar.php (PDOException): " . $e->getMessage());
    echo json_encode(['error' => 'Error de base de datos al enviar el mensaje.']);
} catch (Exception $e) { // Capturar otras excepciones generales
    error_log("Error en controllers/mensaje/enviar.php (Exception): " . $e->getMessage());
    echo json_encode(['error' => 'Error del servidor al enviar el mensaje.']);
}
exit;