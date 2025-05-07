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
    require base_path('views/') . $path; // Esta es la línea 45 de tu error
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