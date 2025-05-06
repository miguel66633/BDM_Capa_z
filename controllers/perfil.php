<?php

use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
$currentUserId = $_SESSION['user_id'] ?? null; // Definir ANTES de usarlo
if (!$currentUserId) {
    header('Location: /inicioSesion');
    exit;
}

// Obtener el ID del perfil a visualizar desde la URL
// Asegúrate que tu router define $params y la clave 'id'
$profileUserId = $params['id'] ?? null;

// Determinar si el usuario actual está viendo su propio perfil
$isOwner = ($currentUserId == $profileUserId); // Ahora $currentUserId y $profileUserId están definidos

// --- Obtener Información del Perfil del Usuario Visualizado ---
$queryUsuario = "SELECT UsuarioID, NombreUsuario, Biografia, ImagenPerfil, BannerPerfil FROM Usuario WHERE UsuarioID = :profileUserId";
$usuario = $db->query($queryUsuario, ['profileUserId' => $profileUserId])->find();

if (!$usuario) {
    // Si el usuario con ese ID no existe, abortar.
    // Aquí también podría ocurrir el problema si el ID no existe en la BD.
    abort(404);
}

// --- Obtener Publicaciones del Usuario Visualizado (con conteos y estados del *VISITANTE*) ---
$queryFeed = "
SELECT * FROM (
    -- Publicaciones originales del dueño del perfil
    SELECT
        p.PublicacionID,
        p.ContenidoPublicacion,
        p.FechaPublicacion AS EffectiveDate, -- Fecha de la publicación original
        u_autor.NombreUsuario AS AutorNombreUsuario,
        u_autor.ImagenPerfil AS AutorImagenPerfil,
        u_autor.UsuarioID AS AutorID,
        m.TipoMultimedia,
        -- (conteos y EXISTS para likes, guardados, comentarios, reposts)
        (SELECT COUNT(DISTINCT pl_count.LikeID) FROM PublicacionLike pl_count WHERE pl_count.PublicacionID = p.PublicacionID) AS LikesCount,
        (SELECT COUNT(*) FROM Guardado g_count WHERE g_count.PublicacionID = p.PublicacionID) AS SavesCount,
        (SELECT COUNT(*) FROM Publicacion comm_count WHERE comm_count.PublicacionPadreID = p.PublicacionID) AS CommentsCount,
        (SELECT COUNT(DISTINCT pr_count.RepostID) FROM PublicacionRepost pr_count WHERE pr_count.PublicacionID = p.PublicacionID) AS RepostsCount,
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
        EXISTS (
            SELECT 1
            FROM Repost r_check
            JOIN UsuarioRepost ur_check ON r_check.RepostID = ur_check.RepostID
            JOIN PublicacionRepost pr_check ON r_check.RepostID = pr_check.RepostID
            WHERE ur_check.UsuarioID = :currentUserId AND pr_check.PublicacionID = p.PublicacionID
        ) AS YaReposteo,
        'original' AS TipoEntrada,
        NULL AS RepostadorNombreUsuario,
        NULL AS RepostadorID,
        NULL AS FechaRepostOriginal 
    FROM Publicacion p
    JOIN Usuario u_autor ON p.UsuarioID = u_autor.UsuarioID
    LEFT JOIN Multimedia m ON p.PublicacionID = m.PublicacionID
    WHERE p.UsuarioID = :profileOwnerId AND p.PublicacionPadreID IS NULL

    UNION ALL

    -- Publicaciones reposteadas por el dueño del perfil
    SELECT
        p_original.PublicacionID,
        p_original.ContenidoPublicacion,
        r.FechaRepost AS EffectiveDate, -- *** ESTA LÍNEA ES CLAVE: Debe ser r.FechaRepost ***
        u_autor_original.NombreUsuario AS AutorNombreUsuario, 
        u_autor_original.ImagenPerfil AS AutorImagenPerfil,   
        u_autor_original.UsuarioID AS AutorID,                
        m_original.TipoMultimedia,
        -- (conteos y EXISTS para likes, guardados, comentarios, reposts para p_original)
        (SELECT COUNT(DISTINCT pl_count.LikeID) FROM PublicacionLike pl_count WHERE pl_count.PublicacionID = p_original.PublicacionID) AS LikesCount,
        (SELECT COUNT(*) FROM Guardado g_count WHERE g_count.PublicacionID = p_original.PublicacionID) AS SavesCount,
        (SELECT COUNT(*) FROM Publicacion comm_count WHERE comm_count.PublicacionPadreID = p_original.PublicacionID) AS CommentsCount,
        (SELECT COUNT(DISTINCT pr_count.RepostID) FROM PublicacionRepost pr_count WHERE pr_count.PublicacionID = p_original.PublicacionID) AS RepostsCount,
        EXISTS (
            SELECT 1
            FROM PublicacionLike pl
            INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
            WHERE ul.UsuarioID = :currentUserId AND pl.PublicacionID = p_original.PublicacionID
        ) AS YaDioLike,
        EXISTS (
            SELECT 1
            FROM Guardado g
            WHERE g.UsuarioID = :currentUserId AND g.PublicacionID = p_original.PublicacionID
        ) AS YaGuardo,
        EXISTS (
            SELECT 1
            FROM Repost r_check
            JOIN UsuarioRepost ur_check ON r_check.RepostID = ur_check.RepostID
            JOIN PublicacionRepost pr_check ON r_check.RepostID = pr_check.RepostID
            WHERE ur_check.UsuarioID = :currentUserId AND pr_check.PublicacionID = p_original.PublicacionID
        ) AS YaReposteo,
        'repost' AS TipoEntrada,
        u_repostador.NombreUsuario AS RepostadorNombreUsuario, 
        u_repostador.UsuarioID AS RepostadorID,
        r.FechaRepost AS FechaRepostOriginal 
    FROM Repost r
    JOIN UsuarioRepost ur ON r.RepostID = ur.RepostID AND ur.UsuarioID = :profileOwnerId 
    JOIN PublicacionRepost pr ON r.RepostID = pr.RepostID
    JOIN Publicacion p_original ON pr.PublicacionID = p_original.PublicacionID 
    JOIN Usuario u_autor_original ON p_original.UsuarioID = u_autor_original.UsuarioID 
    JOIN Usuario u_repostador ON ur.UsuarioID = u_repostador.UsuarioID 
    LEFT JOIN Multimedia m_original ON p_original.PublicacionID = m_original.PublicacionID
) AS ProfileFeed
ORDER BY EffectiveDate DESC
";

$publicacionesPerfil = $db->query($queryFeed, [
    'profileOwnerId' => $profileUserId,
    'currentUserId' => $currentUserId
])->get();


// Pasar los datos a la vista (SOLO si no se abortó antes)
view("perfil.view.php", [
    'usuario' => $usuario,
    'publicaciones' => $publicacionesPerfil, // Usamos $publicacionesPerfil que contiene el feed combinado
    'isOwner' => $isOwner,
    'currentUserId' => $currentUserId, // Para la lógica de "Reposteaste"
    'isOwner' => $isOwner // Asegurado que está definido
]);