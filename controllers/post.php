<?php

use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /inicioSesion');
    exit;
}

// Obtener el ID del usuario actual
$usuarioId = $_SESSION['user_id'];

// *** NUEVO: Obtener el ID de la publicación desde la URL (extraído por el Router) ***
$postId = $_GET['id'] ?? null; 

// *** NUEVO: Descomentar y verificar si el postId existe ***
if (!$postId) {
    // Redirigir si no hay ID en la URL
    header('Location: /inicio'); 
    exit();
}

$queryPublicacion = "
    SELECT 
        p.PublicacionID,
        p.ContenidoPublicacion,
        p.FechaPublicacion,
        u.NombreUsuario,
        u.ImagenPerfil,
        u.UsuarioID AS AutorID, -- Añadido para consistencia si se necesita
        m.TipoMultimedia,
        (SELECT COUNT(DISTINCT pl.LikeID) FROM PublicacionLike pl WHERE pl.PublicacionID = p.PublicacionID) AS LikesCount,
        (SELECT COUNT(*) FROM Guardado g WHERE g.PublicacionID = p.PublicacionID) AS SavesCount,
        (SELECT COUNT(*) FROM Publicacion comm WHERE comm.PublicacionPadreID = p.PublicacionID) AS CommentsCount,
        -- *** NUEVO: Contar Reposts para la publicación principal ***
        (SELECT COUNT(DISTINCT pr.RepostID) 
         FROM PublicacionRepost pr 
         WHERE pr.PublicacionID = p.PublicacionID) AS RepostsCount,
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
        ) AS YaGuardo,
        -- *** NUEVO: Verificar si el usuario actual ya reposteó la publicación principal ***
        EXISTS (
            SELECT 1 
            FROM Repost r
            JOIN UsuarioRepost ur ON r.RepostID = ur.RepostID
            JOIN PublicacionRepost pr ON r.RepostID = pr.RepostID
            WHERE ur.UsuarioID = :currentUserId AND pr.PublicacionID = p.PublicacionID
        ) AS YaReposteo
    FROM 
        Publicacion p
    LEFT JOIN 
        Usuario u ON p.UsuarioID = u.UsuarioID
    LEFT JOIN 
        Multimedia m ON p.PublicacionID = m.PublicacionID
    WHERE 
        p.PublicacionID = :postId
";
// Añadir currentUserId al array de parámetros para la publicación principal
$publicacion = $db->query($queryPublicacion, [
    'postId' => $postId,
    'currentUserId' => $usuarioId
])->find(); 

if (!$publicacion) {
    header('Location: /inicio'); 
    exit();
}

// --- NUEVO: Obtener las RESPUESTAS (Publicaciones Hijas) con sus conteos ---
$queryRespuestas = "
    SELECT 
        p_hija.PublicacionID,      
        p_hija.ContenidoPublicacion, 
        p_hija.FechaPublicacion,   
        u.UsuarioID AS RespondedorID,
        u.NombreUsuario,
        u.ImagenPerfil,
        m.TipoMultimedia AS ImagenRespuesta,
        (SELECT COUNT(DISTINCT pl.LikeID) FROM PublicacionLike pl WHERE pl.PublicacionID = p_hija.PublicacionID) AS LikesCount,
        (SELECT COUNT(*) FROM Guardado g WHERE g.PublicacionID = p_hija.PublicacionID) AS SavesCount,
        (SELECT COUNT(*) FROM Publicacion WHERE PublicacionPadreID = p_hija.PublicacionID) AS RepliesToReplyCount, -- Comentarios de esta respuesta
        -- *** NUEVO: Contar Reposts para cada respuesta ***
        (SELECT COUNT(DISTINCT pr.RepostID) 
         FROM PublicacionRepost pr 
         WHERE pr.PublicacionID = p_hija.PublicacionID) AS RepostsCount,
        EXISTS (
            SELECT 1 
            FROM PublicacionLike pl
            INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
            WHERE ul.UsuarioID = :currentUserId AND pl.PublicacionID = p_hija.PublicacionID
        ) AS YaDioLikeRespuesta,
        EXISTS (
            SELECT 1 
            FROM Guardado g
            WHERE g.UsuarioID = :currentUserId AND g.PublicacionID = p_hija.PublicacionID
        ) AS YaGuardoRespuesta,
        -- *** NUEVO: Verificar si el usuario actual ya reposteó esta respuesta ***
        EXISTS (
            SELECT 1 
            FROM Repost r
            JOIN UsuarioRepost ur ON r.RepostID = ur.RepostID
            JOIN PublicacionRepost pr ON r.RepostID = pr.RepostID
            WHERE ur.UsuarioID = :currentUserId AND pr.PublicacionID = p_hija.PublicacionID
        ) AS YaReposteoRespuesta -- Nombre diferente para evitar colisión si se usara en el mismo nivel
    FROM 
        Publicacion p_hija         
    INNER JOIN 
        Usuario u ON p_hija.UsuarioID = u.UsuarioID
    LEFT JOIN 
        Multimedia m ON p_hija.PublicacionID = m.PublicacionID 
    WHERE 
        p_hija.PublicacionPadreID = :postId 
    ORDER BY 
        p_hija.FechaPublicacion ASC 
";
$respuestas = $db->query($queryRespuestas, [
    'postId' => $postId,
    'currentUserId' => $usuarioId 
])->get(); 

$errors = $_SESSION['errors'] ?? [];
$successMessage = $_SESSION['success'] ?? null;
unset($_SESSION['errors']); // Limpiar después de leer
unset($_SESSION['success']); // Limpiar después de leer
// *** FIN DE LÍNEAS A AÑADIR ***

view("post.view.php", [
    'publicacion' => $publicacion,
    'respuestas' => $respuestas, 
    'errors' => $errors,           // Ahora $errors está definida
    'successMessage' => $successMessage // Ahora $successMessage está definida
]);