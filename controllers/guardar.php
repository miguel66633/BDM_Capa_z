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
    echo json_encode(['error' => 'Debes iniciar sesión para guardar publicaciones.']);
    exit;
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener los datos enviados
$publicacionId = $_POST['publicacion_id'] ?? null;
$usuarioId = $_SESSION['user_id'];

if (!$publicacionId) {
    echo json_encode(['error' => 'ID de publicación no válido.']);
    exit;
}

try {
    $result = $db->callProcedure('sp_ToggleGuardado', [$usuarioId, $publicacionId]);

    if ($result && isset($result[0])) {
        $spResponse = $result[0];
        // Mapear la respuesta del SP a la estructura que el frontend espera
        echo json_encode([
            'success' => (bool)$spResponse['Success'],      // Éxito general de la operación
            'message' => $spResponse['StatusMessage'],
            'guardado' => (bool)$spResponse['YaGuardo'],    // El nuevo estado de si está guardado o no
            'savesCount' => (int)$spResponse['SavesCount']  // El nuevo conteo de guardados
        ]);
    } else {
        error_log("Error inesperado: sp_ToggleGuardado no devolvió un resultado válido para UsuarioID: {$usuarioId}, PublicacionID: {$publicacionId}");
        echo json_encode(['success' => false, 'message' => 'Error inesperado del servidor al procesar el guardado.']);
    }

} catch (\PDOException $e) { // Capturar excepciones PDO específicamente
    error_log("Error en controllers/guardar.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error crítico al procesar el guardado. Intente de nuevo.']);
}
exit;