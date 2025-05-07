<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para seguir a otros usuarios.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$db = App::resolve(Database::class);
$currentUserSeguidorID = $_SESSION['user_id'];
$profileUserSeguidoID = $_POST['profile_user_id'] ?? null;

if (!$profileUserSeguidoID) {
    echo json_encode(['success' => false, 'message' => 'ID del perfil no proporcionado.']);
    exit;
}

if ($currentUserSeguidorID == $profileUserSeguidoID) {
    echo json_encode(['success' => false, 'message' => 'No puedes seguirte a ti mismo.']);
    exit;
}

try {
    $result = $db->callProcedure('sp_ToggleSeguimiento', [$currentUserSeguidorID, $profileUserSeguidoID]);

    if ($result && isset($result[0])) {
        $response = $result[0];
        echo json_encode([
            'success' => $response['Success'],
            'message' => $response['StatusMessage'],
            'estaSiguiendo' => (bool)$response['EstaSiguiendo'],
            'nuevosSeguidoresCountDelPerfil' => (int)$response['NuevosSeguidoresCountDelPerfil']
        ]);
    } else {
        error_log("Error inesperado: sp_ToggleSeguimiento no devolvió un resultado válido.");
        echo json_encode(['success' => false, 'message' => 'Error inesperado del servidor.']);
    }
} catch (\PDOException $e) {
    error_log("Error en controllers/seguimiento/toggle.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud de seguimiento.']);
}
exit;