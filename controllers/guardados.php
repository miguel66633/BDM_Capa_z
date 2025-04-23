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
        EXISTS (
            SELECT 1 
            FROM PublicacionLike pl
            INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
            WHERE ul.UsuarioID = :usuarioId AND pl.PublicacionID = p.PublicacionID
        ) AS YaDioLike
    FROM 
        Publicacion p
    LEFT JOIN 
        Usuario u ON p.UsuarioID = u.UsuarioID
    LEFT JOIN 
        Multimedia m ON p.PublicacionID = m.PublicacionID
    ORDER BY 
        p.FechaPublicacion DESC;
";

$publicacionesGuardadas = $db->query($query, ['usuarioId' => $usuarioId])->get();

// Pasar las publicaciones guardadas a la vista
view("guardados.view.php", [
    'heading' => 'Guardados',
    'publicaciones' => $publicacionesGuardadas
]);