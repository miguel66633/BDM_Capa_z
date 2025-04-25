<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$error_correo = '';
$error_contrasena = '';
$correo_valor = '';

// Renderizar la vista con los errores y valores
view("inicioSesion.view.php", [
    'heading' => 'inicioSesion',
    'error_correo' => $error_correo,
    'error_contrasena' => $error_contrasena,
    'correo_valor' => $correo_valor
]);
