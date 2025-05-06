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
$tipoImagen = $_FILES['imagen']['type'] ?? null; // Obtener el tipo MIME reportado por el navegador

// Validar los datos
$errors = [];

if (empty($contenido) && !$imagen) { // Permitir post solo con imagen/video o solo texto
    $errors['contenido'] = 'El contenido no puede estar vacío si no se sube un archivo.';
}

if ($imagen) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $imagen);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        $errors['imagen'] = 'El tipo de archivo no es permitido. Sube imágenes (jpg, png, gif) o videos (mp4, webm, ogg). Detectado: ' . $mime_type;
    }

    if ($_FILES['imagen']['size'] > 50000000) { $errors['imagen'] = 'El archivo es demasiado grande (máx 50MB).'; }
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

    // Leer el contenido binario del archivo
    $contenidoArchivo = file_get_contents($imagen); // Esto funcionará para imágenes y videos

    // Guardar el archivo en la base de datos como BLOB
    // La columna TipoMultimedia almacenará el contenido binario
    $queryMultimedia = "INSERT INTO Multimedia (TipoMultimedia, PublicacionID) VALUES (:tipoMultimedia, :publicacionId)";
    $db->query($queryMultimedia, [
        'tipoMultimedia' => $contenidoArchivo,
        'publicacionId' => $publicacionId
    ]);
}

// Redirigir con mensaje de éxito
$_SESSION['success'] = 'Tu publicación se ha creado con éxito.';
header('Location: /inicio');
exit;