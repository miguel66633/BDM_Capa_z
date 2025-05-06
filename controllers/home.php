<?php

use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener el ID del usuario actual
$usuarioId = $_SESSION['user_id'] ?? null;

$query = "
    SELECT 
        p.PublicacionID,
        p.ContenidoPublicacion,
        p.FechaPublicacion,
        u.NombreUsuario,
        u.ImagenPerfil,
        m.TipoMultimedia,
        (SELECT COUNT(*) FROM PublicacionLike WHERE PublicacionID = p.PublicacionID) AS Likes,
        (SELECT COUNT(*) FROM Guardado WHERE PublicacionID = p.PublicacionID) AS Guardados,
        (SELECT COUNT(*) FROM Publicacion WHERE PublicacionPadreID = p.PublicacionID) AS CommentsCount, 
        -- *** ACTUALIZADO: Contar Reposts para cada publicación usando la estructura de 3 tablas ***
        (SELECT COUNT(DISTINCT pr.RepostID) 
         FROM PublicacionRepost pr 
         WHERE pr.PublicacionID = p.PublicacionID) AS RepostsCount,
        EXISTS (
            SELECT 1 
            FROM PublicacionLike pl
            INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
            WHERE ul.UsuarioID = :usuarioId AND pl.PublicacionID = p.PublicacionID
        ) AS YaDioLike,
        EXISTS (
            SELECT 1 
            FROM Guardado g 
            WHERE g.UsuarioID = :usuarioId AND g.PublicacionID = p.PublicacionID 
        ) AS YaGuardado,
        -- *** ACTUALIZADO: Verificar si el usuario actual ya reposteó esta publicación usando la estructura de 3 tablas ***
        EXISTS (
            SELECT 1 
            FROM Repost r
            JOIN UsuarioRepost ur ON r.RepostID = ur.RepostID
            JOIN PublicacionRepost pr ON r.RepostID = pr.RepostID
            WHERE ur.UsuarioID = :usuarioId AND pr.PublicacionID = p.PublicacionID
        ) AS YaReposteo
    FROM 
        Publicacion p
    LEFT JOIN 
        Usuario u ON p.UsuarioID = u.UsuarioID
    LEFT JOIN 
        Multimedia m ON p.PublicacionID = m.PublicacionID
    WHERE p.PublicacionPadreID IS NULL 
    ORDER BY 
        p.FechaPublicacion DESC
";

$publicaciones = $db->query($query, ['usuarioId' => $usuarioId])->get();

// Pasar las publicaciones a la vista
view("home.view.php", [
    'publicaciones' => $publicaciones
]);