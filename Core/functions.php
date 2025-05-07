<?php

use Core\Response;

function dd($var)
{
    header("HTTP/1.0 500");
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die();
}


function isUri($route)
{
    return $_SERVER['REQUEST_URI'] === $route ? 'bg-gray-900 text-white ' : 'text-gray-300 hover:bg-gray-700 hover:text-white ';
}

function abort($code = 404)
{
    http_response_code($code);

    require base_path("views/{$code}.php");

    die();
}

function authorize($condition, $status = Response::FORBIDDEN)
{
    if (! $condition) {
        abort($status);
    }
}


function base_path($path): string
{
    return BASE_PATH . $path;
}

function view($path, $attributes = [])
{
    extract($attributes);
    require base_path('views/') . $path;
}

/**
 * Formatea el tiempo transcurrido desde una fecha dada.
 *
 * @param string $fechaString La fecha de la publicación en formato Y-m-d H:i:s.
 * @return string El tiempo transcurrido formateado.
 */
function formatTiempoTranscurrido(string $fechaString): string {
    try {

        $databaseStorageTimeZone = new DateTimeZone('America/Mexico_City');

        $fechaPublicacion = new DateTimeImmutable($fechaString, $databaseStorageTimeZone);

        $ahora = new DateTimeImmutable();

        $intervalo = $ahora->diff($fechaPublicacion);

        if ($intervalo->days >= 1) {

            return $fechaPublicacion->format('d M.');
        }

        if ($intervalo->h >= 2 && $intervalo->h <= 23) {
            return "hace " . $intervalo->h . " horas";
        }

        if ($intervalo->h == 1) {
            return "hace 1 hora";
        }

        return "hace " . $intervalo->i . " minutos";

    } catch (Exception $e) {
        error_log("Error al formatear fecha: " . $e->getMessage() . " | Fecha recibida: " . $fechaString);
        return 'Fecha inválida';
    }
}

/**
 * Formatea el contenido BLOB de una imagen a un data URI base64 o devuelve una imagen por defecto.
 *
 * @param ?string $blobContent El contenido BLOB de la imagen.
 * @param string $defaultImagePath La ruta web a la imagen por defecto.
 * @return string El data URI de la imagen o la ruta a la imagen por defecto.
 */
function formatarImagen(?string $blobContent, string $defaultImagePath): string {
    if (!empty($blobContent)) {
        $base64 = base64_encode($blobContent);
        if ($base64 !== false) {
            // Intentar detectar el tipo MIME para ser más preciso
            // Requiere la extensión fileinfo habilitada en php.ini
            $finfo = finfo_open();
            $mimeType = finfo_buffer($finfo, $blobContent, FILEINFO_MIME_TYPE);
            finfo_close($finfo);

            // Asegurarse de que es un tipo de imagen conocido o usar un genérico
            if ($mimeType && strpos($mimeType, 'image/') === 0) {
                 return 'data:' . $mimeType . ';base64,' . $base64;
            }
            // Fallback si no se puede determinar el mime type o no es imagen, pero hay contenido
            return 'data:image/jpeg;base64,' . $base64; // Asumir jpeg como fallback
        }
    }
    return $defaultImagePath; // Ruta web absoluta o relativa desde la raíz
}

