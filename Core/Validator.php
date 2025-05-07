<?php

namespace Core;

class Validator
{
    public static function string($value, $min = 1, $max = INF)
    {
        $value = trim($value);


        return strlen($value) >= $min && strlen($value) <= $max;
    }


    public static function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

/**
 * Valida un archivo subido y opcionalmente lee su contenido.
 *
 * @param array $fileInput El array de $_FILES para el archivo.
 * @param array $allowedMimeTypes Array de tipos MIME permitidos.
 * @param int $maxFileSize Tamaño máximo del archivo en bytes.
 * @param bool $leerContenido Si es true, intenta leer el contenido del archivo.
 * @return array Un array con ['errores' => [], 'contenido' => null|string, 'mime_type' => null|string].
 */
public static function validarYProcesarArchivo(array $fileInput, array $allowedMimeTypes, int $maxFileSize, bool $leerContenido = false): array
{
    $resultado = ['errores' => [], 'contenido' => null, 'mime_type' => null];

    if (empty($fileInput) || !isset($fileInput['error']) || !isset($fileInput['tmp_name'])) {
        // Esto podría no ser un error si el archivo es opcional.
        // La lógica que llama debe decidir si la ausencia de archivo es un error.
        // $resultado['errores'][] = 'No se proporcionó información del archivo.';
        return $resultado;
    }
    
    if ($fileInput['error'] === UPLOAD_ERR_NO_FILE) {
        // No hay archivo subido, no es un error per se si es opcional.
        return $resultado;
    }

    if ($fileInput['error'] !== UPLOAD_ERR_OK) {
        $resultado['errores'][] = 'Error al subir el archivo. Código: ' . $fileInput['error'];
        return $resultado; // Error temprano, no continuar.
    }

    $tempPath = $fileInput['tmp_name'];
    if (!is_uploaded_file($tempPath)) {
        $resultado['errores'][] = 'El archivo no es un archivo subido válido.';
        return $resultado;
    }

    // Validar tipo MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tempPath);
    finfo_close($finfo);
    $resultado['mime_type'] = $mimeType;

    if (!in_array($mimeType, $allowedMimeTypes)) {
        $resultado['errores'][] = 'El tipo de archivo no es permitido. Permitidos: ' . implode(', ', $allowedMimeTypes) . '. Detectado: ' . htmlspecialchars($mimeType);
    }

    // Validar tamaño
    if ($fileInput['size'] > $maxFileSize) {
        $resultado['errores'][] = 'El archivo es demasiado grande (máx ' . round($maxFileSize / 1024 / 1024, 2) . 'MB). Tamaño detectado: ' . round($fileInput['size'] / 1024 / 1024, 2) . 'MB.';
    }

    if (empty($resultado['errores']) && $leerContenido) {
        $contenidoArchivo = file_get_contents($tempPath);
        if ($contenidoArchivo === false) {
            $resultado['errores'][] = 'No se pudo leer el archivo multimedia.';
        } else {
            $resultado['contenido'] = $contenidoArchivo;
        }
    }

    return $resultado;
}



}