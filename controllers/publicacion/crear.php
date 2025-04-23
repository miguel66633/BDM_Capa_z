<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Debes iniciar sesión para publicar.';
    header('Location: /inicio');
    exit;
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener los datos enviados desde el formulario
$usuarioId = $_SESSION['user_id'];
$contenido = $_POST['contenido'] ?? '';
$imagen = $_FILES['imagen']['tmp_name'] ?? null;
$nombreImagen = $_FILES['imagen']['name'] ?? null;

// Validar los datos
$errors = [];

if (empty($contenido)) {
    $errors['contenido'] = 'El contenido no puede estar vacío.';
}

if ($imagen && !getimagesize($imagen)) {
    $errors['imagen'] = 'El archivo subido no es una imagen válida.';
}

// Si hay errores, redirigir con mensajes de error
if (!empty($errors)) {
    $_SESSION['error'] = $errors;
    header('Location: /inicio');
    exit;
}

// Insertar la publicación en la base de datos
$query = "INSERT INTO Publicacion (ContenidoPublicacion, UsuarioID, FechaPublicacion) VALUES (:contenido, :usuarioId, NOW())";
$resultado = $db->query($query, [
    'contenido' => $contenido,
    'usuarioId' => $usuarioId
]);

// Si se subió una imagen, guardarla en el servidor y en la base de datos como BLOB
if ($resultado && $imagen) {
    $publicacionId = $db->getConnection()->lastInsertId();

    // Leer el contenido binario de la imagen
    $contenidoImagen = file_get_contents($imagen);

    // Guardar la imagen en la base de datos como BLOB
    $queryMultimedia = "INSERT INTO Multimedia (TipoMultimedia, PublicacionID) VALUES (:tipoMultimedia, :publicacionId)";
    $db->query($queryMultimedia, [
        'tipoMultimedia' => $contenidoImagen,
        'publicacionId' => $publicacionId
    ]);
}

// Redirigir con mensaje de éxito
$_SESSION['success'] = 'Tu publicación se ha creado con éxito.';
header('Location: /inicio');
exit;