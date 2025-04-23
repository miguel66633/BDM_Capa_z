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
    // Verificar si el usuario ya dio "like" a la publicación
    $queryCheck = "SELECT * FROM PublicacionLike WHERE PublicacionID = :publicacionId AND LikeID IN (
        SELECT LikeID FROM UsuarioLike WHERE UsuarioID = :usuarioId
    )";
    $likes = $db->query($queryCheck, [
        'publicacionId' => $publicacionId,
        'usuarioId' => $usuarioId
    ])->get();

    $likeExistente = $likes[0] ?? null; // Obtener el primer resultado si existe

    if ($likeExistente) {
        // Eliminar el "like"
        $queryDelete = "DELETE FROM TablaLike WHERE LikeID = :likeId";
        $db->query($queryDelete, [
            'likeId' => $likeExistente['LikeID']
        ]);
        echo json_encode(['success' => 'Like eliminado.', 'liked' => false]);
    } else {
        // Agregar un nuevo "like"
        $queryInsertLike = "INSERT INTO TablaLike (FechaLike) VALUES (NOW())";
        $db->query($queryInsertLike);

        $likeId = $db->getConnection()->lastInsertId();

        $queryInsertUsuarioLike = "INSERT INTO UsuarioLike (UsuarioID, LikeID) VALUES (:usuarioId, :likeId)";
        $db->query($queryInsertUsuarioLike, [
            'usuarioId' => $usuarioId,
            'likeId' => $likeId
        ]);

        $queryInsertPublicacionLike = "INSERT INTO PublicacionLike (PublicacionID, LikeID) VALUES (:publicacionId, :likeId)";
        $db->query($queryInsertPublicacionLike, [
            'publicacionId' => $publicacionId,
            'likeId' => $likeId
        ]);

        echo json_encode(['success' => 'Like agregado.', 'liked' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Ocurrió un error al procesar el like.']);
}
exit;