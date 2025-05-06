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

$pdo = $db->getConnection(); // Obtener la conexión PDO para transacciones

try {
    $pdo->beginTransaction();

    // Verificar si ya existe un repost del usuario para esta publicación
    $queryCheck = "
        SELECT r.RepostID 
        FROM Repost r
        JOIN UsuarioRepost ur ON r.RepostID = ur.RepostID
        JOIN PublicacionRepost pr ON r.RepostID = pr.RepostID
        WHERE ur.UsuarioID = :usuarioId AND pr.PublicacionID = :publicacionId
    ";
    $existingRepost = $db->query($queryCheck, [
        'usuarioId' => $usuarioId,
        'publicacionId' => $publicacionId
    ])->find();

    if ($existingRepost) {
        // Ya existe, entonces quitar repost (eliminar)
        $repostId = $existingRepost['RepostID'];

        // Eliminar de las tablas de unión primero
        $db->query("DELETE FROM UsuarioRepost WHERE UsuarioID = :usuarioId AND RepostID = :repostId", [
            'usuarioId' => $usuarioId,
            'repostId' => $repostId
        ]);
        $db->query("DELETE FROM PublicacionRepost WHERE PublicacionID = :publicacionId AND RepostID = :repostId", [
            'publicacionId' => $publicacionId,
            'repostId' => $repostId
        ]);
        // Finalmente, eliminar de la tabla Repost.
        // Esto es seguro si el RepostID es único para esta acción de repost.
        // Si un RepostID pudiera ser compartido (no parece ser el caso con tu SP), se necesitaría más lógica.
        $db->query("DELETE FROM Repost WHERE RepostID = :repostId", ['repostId' => $repostId]);
        
        $yaReposteo = false;
} else {
        // No existe, entonces agregar repost (insertar)
        // 1. Insertar en Repost
        // *** CAMBIO: Usar NOW() para guardar fecha y hora ***
        $db->query("INSERT INTO Repost (FechaRepost) VALUES (NOW())");
        $repostId = $pdo->lastInsertId(); // Obtener el RepostID recién creado

        // 2. Insertar en UsuarioRepost
        $db->query("INSERT INTO UsuarioRepost (UsuarioID, RepostID) VALUES (:usuarioId, :repostId)", [
            'usuarioId' => $usuarioId,
            'repostId' => $repostId
        ]);

        // 3. Insertar en PublicacionRepost
        $db->query("INSERT INTO PublicacionRepost (PublicacionID, RepostID) VALUES (:publicacionId, :repostId)", [
            'publicacionId' => $publicacionId,
            'repostId' => $repostId
        ]);
        $yaReposteo = true;
    }

    $pdo->commit();

    // Obtener el nuevo conteo de reposts para esta publicación
    $queryCount = "
        SELECT COUNT(DISTINCT pr.RepostID) AS RepostsCount
        FROM PublicacionRepost pr
        WHERE pr.PublicacionID = :publicacionId
    ";
    $newCounts = $db->query($queryCount, ['publicacionId' => $publicacionId])->find();
    $repostsCount = $newCounts['RepostsCount'] ?? 0;

    echo json_encode([
        'success' => true,
        'yaReposteo' => $yaReposteo,
        'repostsCount' => $repostsCount
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error en toggle repost: " . $e->getMessage() . " para UsuarioID: " . $usuarioId . " y PublicacionID: " . $publicacionId);
    echo json_encode(['success' => false, 'message' => 'Error al procesar el repost. Intente de nuevo.']);
}
exit;
?>