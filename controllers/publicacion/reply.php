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
$contenidoRespuesta = trim($_POST['contenido_comentario'] ?? ''); // Contenido del comentario
$usuarioId = $_SESSION['user_id']; // ID del usuario que comenta
$imagenRespuesta = $_FILES['imagen_comentario'] ?? null; // Imagen opcional
$contenidoImagen = null;

// --- Validación ---
$errors = [];

if (!$publicacionPadreId) {
    $errors['general'] = 'ID de publicación padre no válido.';
}

if (empty($contenidoRespuesta)) {
    $errors['contenido'] = 'La respuesta no puede estar vacía.';
} elseif (strlen($contenidoRespuesta) > 100) { // Ajusta según tu tabla Publicacion
    $errors['contenido'] = 'La respuesta no puede exceder los 100 caracteres.';
}

// Validar y procesar imagen si se subió
if ($imagenRespuesta && $imagenRespuesta['error'] === UPLOAD_ERR_OK) {
    $check = getimagesize($imagenRespuesta['tmp_name']);
    if ($check !== false) {
        $contenidoImagen = file_get_contents($imagenRespuesta['tmp_name']);
    } else {
        $errors['imagen'] = 'El archivo subido no es una imagen válida.';
    }
} elseif ($imagenRespuesta && $imagenRespuesta['error'] !== UPLOAD_ERR_NO_FILE) {
    $errors['imagen'] = 'Hubo un error al subir la imagen.';
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

    // 2. Si hay imagen, insertarla en Multimedia asociada a la nueva publicación
    if ($contenidoImagen && $nuevaPublicacionId) {
        $queryMultimedia = "
            INSERT INTO Multimedia (TipoMultimedia, PublicacionID) 
            VALUES (:tipoMultimedia, :publicacionId)
        ";
        $db->query($queryMultimedia, [
            'tipoMultimedia' => $contenidoImagen,
            'publicacionId' => $nuevaPublicacionId
        ]);
    }

    // Opcional: Mensaje de éxito
    $_SESSION['success'] = 'Respuesta agregada con éxito.';

} catch (Exception $e) {
    // Manejo básico de errores
    error_log("Error al guardar respuesta: " . $e->getMessage()); // Loggear el error real
    $_SESSION['errors'] = ['general' => 'No se pudo guardar la respuesta. Inténtalo de nuevo.'];
}

// Redirigir de vuelta a la página del post padre
header('Location: /post/' . $publicacionPadreId);
exit;

?>