<?php
// filepath: c:\xampp\htdocs\BDM_Capa_Z\controllers\repost\toggle.php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$db = App::resolve(Database::class);
$usuarioId = $_SESSION['user_id'];
$publicacionId = $_POST['publicacion_id'] ?? null;

if (!$publicacionId) {
    echo json_encode(['success' => false, 'message' => 'ID de publicación no proporcionado.']);
    exit;
}

try {
    // Llamar al Stored Procedure
    $result = $db->callProcedure('sp_ToggleRepost', [$usuarioId, $publicacionId]);

    if ($result && isset($result[0])) {
        $spResponse = $result[0];

        if ($spResponse['Success']) {
            echo json_encode([
                'success' => true,
                'yaReposteo' => (bool)$spResponse['YaReposteo'], // Asegurar que sea booleano
                'repostsCount' => (int)$spResponse['RepostsCount'], // Asegurar que sea entero
                'message' => $spResponse['StatusMessage']
            ]);
        } else {
            // El SP manejó el error y devolvió Success = false
            echo json_encode([
                'success' => false, 
                'message' => $spResponse['StatusMessage'] ?? 'Error al procesar el repost desde SP.'
            ]);
        }
    } else {
        // Esto no debería ocurrir si el SP siempre devuelve una fila.
        error_log("Error inesperado: sp_ToggleRepost no devolvió un resultado válido para UsuarioID: {$usuarioId}, PublicacionID: {$publicacionId}");
        echo json_encode(['success' => false, 'message' => 'Error inesperado del servidor al procesar el repost.']);
    }

} catch (Exception $e) {
    // Capturar excepciones de la llamada a callProcedure o errores no manejados por el SP
    error_log("Error en toggle repost (PHP): " . $e->getMessage() . " para UsuarioID: " . $usuarioId . " y PublicacionID: " . $publicacionId);
    echo json_encode(['success' => false, 'message' => 'Error crítico al procesar el repost. Intente de nuevo.']);
}
exit;