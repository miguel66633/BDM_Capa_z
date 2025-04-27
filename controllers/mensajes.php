<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /inicioSesion');
    exit;
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener el ID del usuario actual
$usuarioId = $_SESSION['user_id'];

// Consultar los chats del usuario con el último mensaje
$query = "
        SELECT 
        c.ChatID,
        -- Selecciona el ID de la OTRA persona en el chat
        CASE 
            WHEN c.UsuarioID = :usuarioId THEN u2.UsuarioID
            ELSE u1.UsuarioID
        END AS PersonaID,
        -- Selecciona el Nombre de la OTRA persona en el chat
        CASE 
            WHEN c.UsuarioID = :usuarioId THEN u2.NombreUsuario
            ELSE u1.NombreUsuario
        END AS NombreUsuario,
        -- Selecciona la Imagen de la OTRA persona en el chat
        CASE 
            WHEN c.UsuarioID = :usuarioId THEN u2.ImagenPerfil
            ELSE u1.ImagenPerfil
        END AS ImagenPerfil,
        c.FechaCreacion,
        (SELECT m.ContenidoMensaje 
         FROM Mensaje m 
         WHERE m.ChatID = c.ChatID 
         ORDER BY m.FechaMensaje DESC 
         LIMIT 1) AS UltimoMensaje,
        (SELECT m.FechaMensaje 
         FROM Mensaje m 
         WHERE m.ChatID = c.ChatID 
         ORDER BY m.FechaMensaje DESC 
         LIMIT 1) AS HoraUltimoMensaje -- Este es el campo clave para ordenar
    FROM 
        Chat c
    INNER JOIN 
        Usuario u1 ON c.UsuarioID = u1.UsuarioID
    INNER JOIN 
        Usuario u2 ON c.DestinatarioID = u2.UsuarioID
    WHERE 
        c.UsuarioID = :usuarioId OR c.DestinatarioID = :usuarioId -- Encuentra chats donde el usuario actual participa
    ORDER BY 
        HoraUltimoMensaje DESC, c.FechaCreacion DESC;
";

$chats = $db->query($query, ['usuarioId' => $usuarioId])->get();

// Convertir las imágenes a base64
foreach ($chats as &$chat) {
    if (!empty($chat['ImagenPerfil'])) {
        $chat['ImagenPerfil'] = 'data:image/jpeg;base64,' . base64_encode($chat['ImagenPerfil']);
    } else {
        $chat['ImagenPerfil'] = '/Resources/images/perfilPre.jpg'; // Imagen por defecto
    }
}

// Pasar los chats a la vista
view("mensajes.view.php", [
    'chats' => $chats
]);