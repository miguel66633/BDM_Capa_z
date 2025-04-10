<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_correo = '';
$error_contrasena = '';
$correo_valor = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['contrasena'] ?? '';

    $correo_valor = $correo;

    $url = '/api'; 

    $data = array(
        'action' => 'login', 
        'correo' => $correo,
        'contrasena' => $password
    );

    // Configurar cURL para la solicitud POST
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query($data)
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);

    // Verificar si cURL tuvo algún error
    if ($response === false) {
        echo "Error de cURL: " . curl_error($ch);
    }

    curl_close($ch);

    // Decodificar la respuesta de la API
    $response = json_decode($response, true);

    // Verificar el resultado del inicio de sesión
    if (isset($response['error'])) {
        if ($response['error'] === "Correo o contraseña incorrectos.") {
            $error_contrasena = $response['error'];
        } elseif ($response['error'] === "Correo y contraseña son obligatorios.") {
            $error_correo = $response['error'];
        }
    } elseif (isset($response['message'])) {
        // Guardar datos del usuario en la sesión
        $_SESSION['user_id'] = $response['user']['UsuarioID'];
        $_SESSION['user_name'] = $response['user']['NombreUsuario'];
        $_SESSION['user_email'] = $response['user']['Correo'];
        $_SESSION['user_role'] = $response['user']['TipoUsuario'];

        // Redirigir al usuario a la página principal
        header("Location: /inicio");
        exit();
    }
}

// Renderizar la vista con los errores y valores
view("inicioSesion.view.php", [
    'heading' => 'inicioSesion',
    'error_correo' => $error_correo,
    'error_contrasena' => $error_contrasena,
    'correo_valor' => $correo_valor
]);




