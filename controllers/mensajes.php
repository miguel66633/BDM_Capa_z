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

// Consultar los chats del usuario
$query = "
    SELECT 
        c.ChatID,
        u.UsuarioID AS DestinatarioID,
        u.NombreUsuario,
        u.ImagenPerfil,
        c.FechaCreacion
    FROM 
        Chat c
    INNER JOIN 
        Usuario u ON (u.UsuarioID = c.DestinatarioID AND c.UsuarioID = :usuarioId)
        OR (u.UsuarioID = c.UsuarioID AND c.DestinatarioID = :usuarioId)
    ORDER BY c.FechaCreacion DESC;
";

$chats = $db->query($query, ['usuarioId' => $usuarioId])->get();

// Convertir las imágenes a base64
foreach ($chats as &$chat) {
    if (!empty($chat['ImagenPerfil'])) {
        $chat['ImagenPerfil'] = 'data:image/jpeg;base64,' . base64_encode($chat['ImagenPerfil']);
    } else {
        $chat['ImagenPerfil'] = 'Resources/images/perfilPre.jpg'; // Imagen por defecto
    }
}

// Pasar los chats a la vista
view("mensajes.view.php", [
    'chats' => $chats
]);