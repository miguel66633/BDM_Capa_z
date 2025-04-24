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

// Recuperar publicaciones guardadas por el usuario actual, ordenadas por la fecha en que fueron guardadas
$query = "
    SELECT 
        p.PublicacionID,
        p.ContenidoPublicacion,
        p.FechaPublicacion,
        u.NombreUsuario,
        u.ImagenPerfil,
        m.TipoMultimedia,
        (SELECT COUNT(*) FROM PublicacionLike WHERE PublicacionID = p.PublicacionID) AS Likes,
        (SELECT COUNT(*) FROM Guardado WHERE PublicacionID = p.PublicacionID) AS Guardados
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
        g.FechaGuardado DESC; -- Ordenar por la fecha en que fueron guardadas
";

$publicacionesGuardadas = $db->query($query, ['usuarioId' => $usuarioId])->get();

// Pasar las publicaciones guardadas a la vista
view("guardados.view.php", [
    'heading' => 'Guardados',
    'publicaciones' => $publicacionesGuardadas
]);