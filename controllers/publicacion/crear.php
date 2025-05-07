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
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
    $max_size = 50 * 1024 * 1024; // 50MB

    $validacion = Core\Validator::validarYProcesarArchivo($_FILES['imagen'], $allowed_types, $max_size, true);

    if (!empty($validacion['errores'])) {
        // Tomar el primer error para simplificar, o acumularlos
        $errors['imagen'] = $validacion['errores'][0]; 
    } else {
        $contenidoArchivo = $validacion['contenido'];
    }
}


if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: /inicio');
    exit;
}

try {
    // Llamar al Stored Procedure
    // El cuarto parámetro es p_PublicacionPadreID, que es null para posts principales
    $result = $db->callProcedure('sp_CrearPublicacion', [
        $usuarioId,
        $contenido,
        $contenidoArchivo, // Puede ser null
        null              // p_PublicacionPadreID
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