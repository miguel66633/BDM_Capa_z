<?php
$file = __DIR__ . '/../views/' . $_GET['file'];

if (file_exists($file)) {
    $mime = mime_content_type($file);

    // Forzar el tipo MIME a text/css si es un archivo CSS
    if (pathinfo($file, PATHINFO_EXTENSION) === 'css') {
        $mime = 'text/css';
    }

    header("Content-Type: $mime");
    readfile($file);
    exit;
} else {
    http_response_code(404);
    echo "Archivo no encontrado.";
    exit;
}