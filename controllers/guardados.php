<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener el ID del usuario actual
$usuarioId = $_SESSION['user_id'] ?? null;

// Verificar si el usuario está autenticado (redirigir si no)
if (!$usuarioId) {
    header('Location: /inicioSesion');
    exit;
}

// Recuperar publicaciones guardadas por el usuario actual, ordenadas por la fecha en que fueron guardadas
$query = "
    SELECT 
        p.PublicacionID,
        p.ContenidoPublicacion,
        p.FechaPublicacion,
        u.NombreUsuario,
        u.ImagenPerfil,
        m.TipoMultimedia,
        g.FechaGuardado, -- Fecha en que se guardó
        (SELECT COUNT(*) FROM PublicacionLike WHERE PublicacionID = p.PublicacionID) AS Likes,
        (SELECT COUNT(*) FROM Guardado WHERE PublicacionID = p.PublicacionID) AS Guardados,
        -- *** NUEVO: Contar Respuestas (comentarios) ***
        (SELECT COUNT(*) FROM Publicacion WHERE PublicacionPadreID = p.PublicacionID) AS CommentsCount, 
        -- *** NUEVO: Contar Reposts (asumiendo tabla PublicacionRepost) ***
        (SELECT COUNT(*) FROM PublicacionRepost WHERE PublicacionID = p.PublicacionID) AS RepostsCount,
        EXISTS (
            SELECT 1 
            FROM PublicacionLike pl
            INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
            WHERE ul.UsuarioID = :usuarioId AND pl.PublicacionID = p.PublicacionID
        ) AS YaDioLike
        -- Ya sabemos que está guardado porque estamos en la tabla Guardado, 
        -- pero si necesitaras el estado de repost:
        -- , EXISTS (
        --     SELECT 1 
        --     FROM UsuarioRepost ur 
        --     JOIN Repost r ON ur.RepostID = r.RepostID 
        --     JOIN PublicacionRepost pr ON r.RepostID = pr.RepostID 
        --     WHERE ur.UsuarioID = :usuarioId AND pr.PublicacionID = p.PublicacionID
        -- ) AS YaHizoRepost
    FROM 
        Guardado g
    INNER JOIN 
        Publicacion p ON g.PublicacionID = p.PublicacionID
    LEFT JOIN 
        Usuario u ON p.UsuarioID = u.UsuarioID
    LEFT JOIN 
        Multimedia m ON p.PublicacionID = m.PublicacionID
    WHERE 
        g.UsuarioID = :usuarioId
        -- Asegurarse de que solo traemos publicaciones principales guardadas (opcional, depende de si guardas respuestas)
        -- AND p.PublicacionPadreID IS NULL 
    ORDER BY 
        g.FechaGuardado DESC; -- Ordenar por cuándo se guardó
";

$publicacionesGuardadas = $db->query($query, ['usuarioId' => $usuarioId])->get();

// Pasar las publicaciones guardadas a la vista
view("guardados.view.php", [
    'heading' => 'Guardados',
    'publicaciones' => $publicacionesGuardadas
]);