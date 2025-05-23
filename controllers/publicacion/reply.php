<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /inicioSesion'); 
    exit;
}

$db = App::resolve(Database::class);

// Obtener los datos enviados
$publicacionPadreId = $_GET['id'] ?? null; 
$contenidoRespuesta = trim($_POST['contenido_comentario'] ?? '');
$usuarioId = $_SESSION['user_id'];
$archivoRespuesta = $_FILES['imagen_comentario'] ?? null; 
$contenidoMultimedia = null; 
$errors = []; 

// --- VALIDACIONES ---
if (!$publicacionPadreId) {
    $errors['general'] = 'ID de publicación padre no válido.';
}

if (empty($contenidoRespuesta) && (!$archivoRespuesta || $archivoRespuesta['error'] === UPLOAD_ERR_NO_FILE)) {
    $errors['contenido'] = 'La respuesta no puede estar vacía si no se adjunta un archivo.';
} elseif (mb_strlen($contenidoRespuesta) > 100) {
    $errors['contenido'] = 'La respuesta no puede exceder los 100 caracteres.';
}

if ($archivoRespuesta && $archivoRespuesta['error'] !== UPLOAD_ERR_NO_FILE) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
    $max_file_size = 50 * 1024 * 1024; // 50 MB

    $validacion = Core\Validator::validarYProcesarArchivo($archivoRespuesta, $allowed_types, $max_file_size, true);
    
    if (!empty($validacion['errores'])) {

        if (isset($errors['imagen'])) {
            if (is_array($errors['imagen'])) {
                $errors['imagen'] = array_merge($errors['imagen'], $validacion['errores']);
            } else {
                $errors['imagen'] = [$errors['imagen']]; // Convertir a array si era string
                $errors['imagen'] = array_merge($errors['imagen'], $validacion['errores']);
            }
        } else {
            $errors['imagen'] = $validacion['errores'];
        }
    } else {
        $contenidoMultimedia = $validacion['contenido'];
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    if ($publicacionPadreId) {
        header('Location: /post/' . $publicacionPadreId);
    } else {
        header('Location: /inicio'); // Fallback
    }
    exit;
}

try {
    $result = $db->callProcedure('sp_CrearPublicacion', [
        $usuarioId,
        $contenidoRespuesta,
        $contenidoMultimedia, // Puede ser null
        $publicacionPadreId
    ]);

    if ($result && isset($result[0])) {
        $spResponse = $result[0];
        if ($spResponse['Success']) {
            $_SESSION['success'] = $spResponse['StatusMessage']; // Usar el mensaje del SP
        } else {
            $_SESSION['errors'] = ['general' => $spResponse['StatusMessage'] ?? 'No se pudo guardar la respuesta.'];
        }
    } else {
        $_SESSION['errors'] = ['general' => 'Respuesta inesperada del servidor al guardar la respuesta.'];
        error_log("Error inesperado: sp_CrearPublicacion (para respuesta) no devolvió un resultado válido. PadreID: {$publicacionPadreId}, UsuarioID: {$usuarioId}");
    }

} catch (Exception $e) {
    error_log("Error al llamar sp_CrearPublicacion para respuesta: " . $e->getMessage());
    $_SESSION['errors'] = ['general' => 'Ocurrió un error técnico al guardar la respuesta.'];
}

if ($publicacionPadreId) {
    header('Location: /post/' . $publicacionPadreId);
} else {
    header('Location: /inicio'); // Fallback
}
exit;