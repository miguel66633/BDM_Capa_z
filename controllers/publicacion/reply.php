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

// Obtener los datos enviados desde el formulario
// El ID de la publicación padre viene de la URL (manejado por el router y puesto en $_GET['id'])
$publicacionPadreId = $_GET['id'] ?? null; 
$contenidoRespuesta = trim($_POST['contenido_comentario'] ?? '');
$usuarioId = $_SESSION['user_id'];
$archivoRespuesta = $_FILES['imagen_comentario'] ?? null; // Renombrado para claridad
$contenidoMultimedia = null; // Para almacenar el contenido binario del archivo

// --- Validación ---
$errors = [];

if (!$publicacionPadreId) {
    $errors['general'] = 'ID de publicación padre no válido.';
}

if (empty($contenidoRespuesta) && (!$archivoRespuesta || $archivoRespuesta['error'] === UPLOAD_ERR_NO_FILE)) {
    $errors['contenido'] = 'La respuesta no puede estar vacía si no se adjunta un archivo.';
} elseif (strlen($contenidoRespuesta) > 280) { // Ajusta según tu tabla Publicacion (ej. 280 caracteres)
    $errors['contenido'] = 'La respuesta no puede exceder los 280 caracteres.';
}

// Validar y procesar archivo (imagen o video) si se subió
if ($archivoRespuesta && $archivoRespuesta['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg']; // Tipos permitidos
    $max_file_size = 50 * 1024 * 1024; // 50 MB (ajusta según necesidad)

    $file_tmp_name = $archivoRespuesta['tmp_name'];
    $file_size = $archivoRespuesta['size'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_tmp_name);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        $errors['imagen'] = 'El tipo de archivo no es permitido. Sube imágenes (jpg, png, gif) o videos (mp4, webm, ogg). Detectado: ' . htmlspecialchars($mime_type);
    } elseif ($file_size > $max_file_size) {
        $errors['imagen'] = 'El archivo es demasiado grande (máx ' . ($max_file_size / 1024 / 1024) . 'MB).';
    } else {
        $contenidoMultimedia = file_get_contents($file_tmp_name);
    }
} elseif ($archivoRespuesta && $archivoRespuesta['error'] !== UPLOAD_ERR_NO_FILE) {
    $errors['imagen'] = 'Hubo un error al subir el archivo.';
}
// --- Fin Validación ---

// Si hay errores, guardar en sesión y redirigir de vuelta al post padre
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    if ($publicacionPadreId) {
        header('Location: /post/' . $publicacionPadreId);
    } else {
        header('Location: /inicio'); // Fallback
    }
    exit;
}

// --- Guardar la Respuesta como una Nueva Publicación ---
try {
    // 1. Insertar la publicación hija (el comentario)
    $queryInsertPublicacion = "
        INSERT INTO Publicacion (ContenidoPublicacion, UsuarioID, PublicacionPadreID, FechaPublicacion)
        VALUES (:contenido, :usuarioId, :padreId, NOW())
    ";
    $db->query($queryInsertPublicacion, [
        'contenido' => $contenidoRespuesta,
        'usuarioId' => $usuarioId,
        'padreId' => $publicacionPadreId
    ]);

    // Obtener el ID de la nueva publicación (la respuesta)
    $nuevaPublicacionId = $db->getConnection()->lastInsertId();

    // 2. Si hay multimedia, insertarla en Multimedia asociada a la nueva publicación
    if ($contenidoMultimedia && $nuevaPublicacionId) { // Usar $contenidoMultimedia
        $queryMultimedia = "
            INSERT INTO Multimedia (TipoMultimedia, PublicacionID) 
            VALUES (:tipoMultimedia, :publicacionId)
        ";
        $db->query($queryMultimedia, [
            'tipoMultimedia' => $contenidoMultimedia, // Contenido binario del archivo
            'publicacionId' => $nuevaPublicacionId
        ]);
    }

    // Opcional: Mensaje de éxito
    $_SESSION['success'] = 'Respuesta agregada con éxito.';

} catch (Exception $e) {
    // Manejo básico de errores
    error_log("Error al guardar respuesta: " . $e->getMessage());
    $_SESSION['errors'] = ['general' => 'No se pudo guardar la respuesta. Inténtalo de nuevo.'];
}

// Redirigir de vuelta a la página del post padre
if ($publicacionPadreId) {
    header('Location: /post/' . $publicacionPadreId);
} else {
    header('Location: /inicio'); // Fallback
}
exit;