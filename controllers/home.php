<?php

use Core\App;
use Core\Database;
// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// $_SESSION['user'] = [
//     'UsuarioID' => $usuario['UsuarioID'],
//     'NombreUsuario' => $usuario['NombreUsuario'],
//     'TipoUsuario' => $usuario['TipoUsuario']
// ];



// Obtener el ID del usuario actual
$usuarioId = $_SESSION['user_id'] ?? null;

// Recuperar publicaciones, likes y guardados
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
        ) AS YaGuardado
    FROM 
        Publicacion p
    LEFT JOIN 
        Usuario u ON p.UsuarioID = u.UsuarioID
    LEFT JOIN 
        Multimedia m ON p.PublicacionID = m.PublicacionID
    ORDER BY 
        p.FechaPublicacion DESC;
";

$publicaciones = $db->query($query, ['usuarioId' => $usuarioId])->get();

// Pasar las publicaciones a la vista
view("home.view.php", [
    'heading' => 'Home',
    'publicaciones' => $publicaciones
]);