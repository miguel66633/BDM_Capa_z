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

// --- Obtener Información del Perfil del Usuario ---
$queryUsuario = "SELECT NombreUsuario, Biografia, ImagenPerfil, BannerPerfil FROM Usuario WHERE UsuarioID = :usuarioId";
$usuario = $db->query($queryUsuario, ['usuarioId' => $usuarioId])->find();

if (!$usuario) {
    // Manejar caso de usuario no encontrado (aunque no debería pasar si está logueado)
    abort(404); 
}

// --- Obtener Publicaciones del Usuario (con conteos y estados) ---
$queryPublicacionesUsuario = "
    SELECT 
        p.PublicacionID,
        p.ContenidoPublicacion,
        p.FechaPublicacion,
        u.NombreUsuario, -- Aunque es el mismo usuario, lo incluimos por consistencia
        u.ImagenPerfil,  -- Imagen de perfil del usuario (para cada post)
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
    INNER JOIN -- Usamos INNER JOIN porque sabemos que el usuario existe
        Usuario u ON p.UsuarioID = u.UsuarioID
    LEFT JOIN 
        Multimedia m ON p.PublicacionID = m.PublicacionID
    WHERE 
        p.UsuarioID = :usuarioId -- Filtrar por el ID del usuario del perfil
        AND p.PublicacionPadreID IS NULL -- Mostrar solo publicaciones principales
    ORDER BY 
        p.FechaPublicacion DESC;
";

$publicacionesUsuario = $db->query($queryPublicacionesUsuario, [
    'usuarioId' => $usuarioId,
    'currentUserId' => $usuarioId // En el perfil propio, currentUserId es el mismo que usuarioId
])->get();


// Pasar los datos a la vista
view("perfil.view.php", [
    'heading' => 'Perfil',
    'usuario' => $usuario, // Datos del perfil (nombre, bio, imágenes)
    'publicaciones' => $publicacionesUsuario // Publicaciones del usuario
]);