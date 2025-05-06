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
        (SELECT COUNT(DISTINCT pl.LikeID) FROM PublicacionLike pl WHERE pl.PublicacionID = p.PublicacionID) AS Likes, -- Corregido para contar likes de la publicación
        (SELECT COUNT(*) FROM Guardado WHERE PublicacionID = p.PublicacionID) AS Guardados, -- Conteo total de guardados de esta publicación
        (SELECT COUNT(*) FROM Publicacion WHERE PublicacionPadreID = p.PublicacionID) AS CommentsCount, 
        -- *** NUEVO: Contar Reposts para la publicación guardada ***
        (SELECT COUNT(DISTINCT pr.RepostID) 
         FROM PublicacionRepost pr 
         WHERE pr.PublicacionID = p.PublicacionID) AS RepostsCount,
        EXISTS (
            SELECT 1 
            FROM PublicacionLike pl
            INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
            WHERE ul.UsuarioID = :usuarioId AND pl.PublicacionID = p.PublicacionID
        ) AS YaDioLike,
        -- Ya sabemos que está guardado porque estamos en la tabla Guardado,
        -- pero mantenemos la columna YaGuardo para consistencia con otras vistas si es necesario.
        1 AS YaGuardo, -- Siempre será true en esta vista
        -- *** NUEVO: Verificar si el usuario actual ya reposteó la publicación guardada ***
        EXISTS (
            SELECT 1 
            FROM Repost r
            JOIN UsuarioRepost ur ON r.RepostID = ur.RepostID
            JOIN PublicacionRepost pr ON r.RepostID = pr.RepostID
            WHERE ur.UsuarioID = :usuarioId AND pr.PublicacionID = p.PublicacionID
        ) AS YaReposteo
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
    ORDER BY 
        g.FechaGuardado DESC; 
";

$publicacionesGuardadas = $db->query($query, ['usuarioId' => $usuarioId])->get();

// Pasar las publicaciones guardadas a la vista
view("guardados.view.php", [
    'heading' => 'Guardados',
    'publicaciones' => $publicacionesGuardadas
]);