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
    // Verificar si el usuario ya guardó la publicación
    $queryCheck = "SELECT * FROM Guardado WHERE PublicacionID = :publicacionId AND UsuarioID = :usuarioId";
    $guardadoExistente = $db->query($queryCheck, [
        'publicacionId' => $publicacionId,
        'usuarioId' => $usuarioId
    ])->get();

    if (!empty($guardadoExistente)) {
        // Eliminar el "guardado"
        $queryDelete = "DELETE FROM Guardado WHERE PublicacionID = :publicacionId AND UsuarioID = :usuarioId";
        $db->query($queryDelete, [
            'publicacionId' => $publicacionId,
            'usuarioId' => $usuarioId
        ]);
        echo json_encode(['success' => 'Guardado eliminado.', 'guardado' => false]);
    } else {
        // Agregar un nuevo "guardado"
        $queryInsert = "INSERT INTO Guardado (EstadoGuardado, UsuarioID, PublicacionID, FechaGuardado) VALUES (1, :usuarioId, :publicacionId, NOW())";
        $db->query($queryInsert, [
            'usuarioId' => $usuarioId,
            'publicacionId' => $publicacionId
        ]);
        echo json_encode(['success' => 'Guardado agregado.', 'guardado' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error al procesar el guardado.']);
}
exit;