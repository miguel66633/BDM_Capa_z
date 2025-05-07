<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['errors'] = ['auth' => 'Debes iniciar sesión para publicar.'];
    header('Location: /inicio');
    exit;
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener los datos enviados desde el formulario
$usuarioId = $_SESSION['user_id'];
$contenido = $_POST['contenido'] ?? '';
$imagenTempPath = $_FILES['imagen']['tmp_name'] ?? null; // Ruta temporal del archivo subido

$errors = [];

// Validaciones (se mantienen en PHP)
if (empty($contenido) && (empty($imagenTempPath) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE)) {
    $errors['contenido'] = 'El contenido no puede estar vacío si no se sube un archivo.';
}
if (mb_strlen($contenido) > 100) { // 100 es el límite de Publicacion.ContenidoPublicacion
    $errors['contenido'] = 'El contenido no puede exceder los 100 caracteres.';
}
$contenidoArchivo = null;
if (!empty($imagenTempPath) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $imagenTempPath);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        $errors['imagen'] = 'El tipo de archivo no es permitido. Sube imágenes (jpg, png, gif) o videos (mp4, webm, ogg). Detectado: ' . htmlspecialchars($mime_type);
    }

    if ($_FILES['imagen']['size'] > 50000000) { // 50MB
        $errors['imagen'] = 'El archivo es demasiado grande (máx 50MB).';
    }

    if (empty($errors['imagen'])) { // Solo leer si no hay errores previos de imagen
        $contenidoArchivo = file_get_contents($imagenTempPath);
        if ($contenidoArchivo === false) {
            $errors['imagen'] = 'No se pudo leer el archivo multimedia.';
        }
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: /inicio');
    exit;
}

try {
    // Llamar al Stored Procedure
    // El tercer parámetro ($contenidoArchivo) puede ser null si no se subió imagen/video
    $result = $db->callProcedure('sp_CrearPublicacion', [
        $usuarioId,
        $contenido,
        $contenidoArchivo
    ]);

    if ($result && isset($result[0])) {
        $spResponse = $result[0];
        if ($spResponse['Success']) {
            $_SESSION['success_message'] = $spResponse['StatusMessage'];
        } else {
            $_SESSION['errors'] = ['general' => $spResponse['StatusMessage'] ?? 'No se pudo crear la publicación.'];
        }
    } else {
        $_SESSION['errors'] = ['general' => 'Respuesta inesperada del servidor al crear la publicación.'];
        error_log("Error inesperado: sp_CrearPublicacion no devolvió un resultado válido para UsuarioID: {$usuarioId}");
    }

} catch (Exception $e) {
    error_log("Error al llamar sp_CrearPublicacion: " . $e->getMessage());
    $_SESSION['errors'] = ['general' => 'Ocurrió un error técnico al crear la publicación.'];
}

header('Location: /inicio');
exit;