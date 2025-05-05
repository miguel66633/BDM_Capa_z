<?php

use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
$currentUserId = $_SESSION['user_id'] ?? null; // Definir ANTES de usarlo
if (!$currentUserId) {
    header('Location: /inicioSesion');
    exit;
}

// Obtener el ID del perfil a visualizar desde la URL
// Asegúrate que tu router define $params y la clave 'id'
$profileUserId = $params['id'] ?? null;

// Determinar si el usuario actual está viendo su propio perfil
$isOwner = ($currentUserId == $profileUserId); // Ahora $currentUserId y $profileUserId están definidos

// --- Obtener Información del Perfil del Usuario Visualizado ---
$queryUsuario = "SELECT UsuarioID, NombreUsuario, Biografia, ImagenPerfil, BannerPerfil FROM Usuario WHERE UsuarioID = :profileUserId";
$usuario = $db->query($queryUsuario, ['profileUserId' => $profileUserId])->find();

if (!$usuario) {
    // Si el usuario con ese ID no existe, abortar.
    // Aquí también podría ocurrir el problema si el ID no existe en la BD.
    abort(404);
}

// --- Obtener Publicaciones del Usuario Visualizado (con conteos y estados del *VISITANTE*) ---
$queryPublicacionesUsuario = "
    SELECT
        p.PublicacionID,
        p.ContenidoPublicacion,
        p.FechaPublicacion,
        u.NombreUsuario,
        u.ImagenPerfil,
        u.UsuarioID, -- *** AÑADIR ESTA LÍNEA ***
        m.TipoMultimedia,
        (SELECT COUNT(*) FROM PublicacionLike WHERE PublicacionID = p.PublicacionID) AS LikesCount,
        (SELECT COUNT(*) FROM Guardado WHERE PublicacionID = p.PublicacionID) AS SavesCount,
        (SELECT COUNT(*) FROM Publicacion WHERE PublicacionPadreID = p.PublicacionID) AS CommentsCount,
        EXISTS (
            SELECT 1
            FROM PublicacionLike pl
            INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
            WHERE ul.UsuarioID = :currentUserId AND pl.PublicacionID = p.PublicacionID
        ) AS YaDioLike,
        EXISTS (
            SELECT 1
            FROM Guardado g
            WHERE g.UsuarioID = :currentUserId AND g.PublicacionID = p.PublicacionID
        ) AS YaGuardo
    FROM
        Publicacion p
    INNER JOIN
        Usuario u ON p.UsuarioID = u.UsuarioID
    LEFT JOIN
        Multimedia m ON p.PublicacionID = m.PublicacionID
    WHERE
        p.UsuarioID = :profileUserId
        AND p.PublicacionPadreID IS NULL
    ORDER BY
        p.FechaPublicacion DESC;
";

$publicacionesUsuario = $db->query($queryPublicacionesUsuario, [
    'profileUserId' => $profileUserId,
    'currentUserId' => $currentUserId
])->get();


// Pasar los datos a la vista (SOLO si no se abortó antes)
view("perfil.view.php", [
    'heading' => 'Perfil de ' . htmlspecialchars($usuario['NombreUsuario']),
    'usuario' => $usuario, // Asegurado que existe
    'publicaciones' => $publicacionesUsuario,
    'isOwner' => $isOwner // Asegurado que está definido
]);