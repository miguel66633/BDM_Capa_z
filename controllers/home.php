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
        -- *** NUEVO: Contar Respuestas (comentarios) para cada publicación ***
        (SELECT COUNT(*) FROM Publicacion WHERE PublicacionPadreID = p.PublicacionID) AS CommentsCount, 
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
    -- *** AÑADIDO: Filtrar para mostrar solo publicaciones principales (sin padre) ***
    WHERE p.PublicacionPadreID IS NULL 
    ORDER BY 
        p.FechaPublicacion DESC;
";

$publicaciones = $db->query($query, ['usuarioId' => $usuarioId])->get();

// --- NUEVO: Lógica para buscar usuarios para la lista lateral principal ---
$searchTermFull = $_GET['term'] ?? null;
$usuariosLaterales = []; // Inicializar como array vacío

if ($searchTermFull) {
    // Si hay un término de búsqueda en la URL, buscar usuarios
    $sql = "SELECT UsuarioID, NombreUsuario, Correo, ImagenPerfil FROM Usuario
            WHERE NombreUsuario LIKE :searchTerm OR Correo LIKE :searchTerm";
    $usuariosLaterales = $db->query($sql, ['searchTerm' => '%' . $searchTermFull . '%'])->get();
}
// Si no hay término, $usuariosLaterales permanecerá vacío (o puedes cargar usuarios por defecto si quieres)


// Pasar AMBAS listas de datos a la vista
view("home.view.php", [
    'heading' => 'Inicio',
    'publicaciones' => $publicaciones, // Datos del feed
    'usuarios' => $usuariosLaterales // Datos para la lista lateral principal
]);