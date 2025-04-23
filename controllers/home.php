<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Recuperar publicaciones y multimedia
$query = "
    SELECT 
        p.PublicacionID,
        p.ContenidoPublicacion,
        p.FechaPublicacion,
        u.NombreUsuario,
        u.ImagenPerfil,
        m.TipoMultimedia
    FROM 
        Publicacion p
    LEFT JOIN 
        Usuario u ON p.UsuarioID = u.UsuarioID
    LEFT JOIN 
        Multimedia m ON p.PublicacionID = m.PublicacionID
    ORDER BY 
        p.FechaPublicacion DESC;
";

$publicaciones = $db->query($query)->get(); // Cambiado fetchAll() por get()

// Pasar las publicaciones a la vista
view("home.view.php", [
    'heading' => 'Home',
    'publicaciones' => $publicaciones
]);
