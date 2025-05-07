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
    echo json_encode(['error' => 'Debes iniciar sesión para dar like.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$db = App::resolve(Database::class);
$publicacionId = $_POST['publicacion_id'] ?? null;
$usuarioId = $_SESSION['user_id'];

if (!$publicacionId) {
    echo json_encode(['success' => false, 'message' => 'ID de publicación no proporcionado.']);
    exit;
}

try {
    // Llamar al procedimiento almacenado
    $result = $db->callProcedure('sp_ToggleLikeAndGetCounts', [$usuarioId, $publicacionId]);
    
    // El SP devuelve una fila con YaDioLike y LikesCount
    if ($result && isset($result[0])) {
        $data = $result[0];
        echo json_encode([
            'success' => true,
            'liked' => (bool)$data['YaDioLike'], // El SP devuelve 0 o 1 para BOOLEAN
            'likesCount' => (int)$data['LikesCount']
        ]);
    } else {
        // Esto podría ocurrir si el SP no devuelve filas o hay un error no capturado antes
        echo json_encode(['success' => false, 'message' => 'Error al procesar la acción de like.']);
    }

} catch (Exception $e) {
    error_log("Error en toggle like: " . $e->getMessage() . " para UsuarioID: " . $usuarioId . " y PublicacionID: " . $publicacionId);
    echo json_encode(['success' => false, 'message' => 'Error del servidor al procesar el like.']);
}
exit;