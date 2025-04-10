<?php

require_once __DIR__ . '/bootstrap.php';

use Core\App;

$database = App::resolve(Core\Database::class); // Resuelve la instancia desde el contenedor
$conn = $database->getConnection();

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $correo = $_POST['correo'] ?? '';
    $password = $_POST['contrasena'] ?? '';

    if ($action === 'register') {
        $nombre = $_POST['nombre_completo'] ?? '';

        if (!empty($nombre) && !empty($correo) && !empty($password)) {
            // Verificar si el correo ya está registrado
            $query = "SELECT UsuarioID FROM Usuario WHERE Correo = :correo";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response['error'] = "El correo ya está registrado. Por favor, usa otro.";
            } else {
                // Hashear la contraseña
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insertar usuario
                $query = "INSERT INTO Usuario (NombreUsuario, Correo, PasswordUsu, Biografia, TipoUsuario)
                          VALUES (:nombre, :correo, :password, '', 0)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':password', $hashedPassword);

                if ($stmt->execute()) {
                    $response['message'] = "Usuario registrado con éxito.";
                } else {
                    $response['error'] = "Error al registrar el usuario: " . $stmt->errorInfo()[2];
                }
            }
        } else {
            $response['error'] = "Todos los campos son obligatorios.";
        }

    } elseif ($action === 'login') {
        if (!empty($correo) && !empty($password)) {
            $query = "SELECT * FROM Usuario WHERE Correo = :correo";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['PasswordUsu'])) {
                $response['message'] = "Inicio de sesión exitoso.";
                $response['user'] = [
                    'UsuarioID' => $usuario['UsuarioID'],
                    'NombreUsuario' => $usuario['NombreUsuario'],
                    'Correo' => $usuario['Correo'],
                    'Biografia' => $usuario['Biografia'],
                    'TipoUsuario' => $usuario['TipoUsuario']
                ];
            } else {
                $response['error'] = "Correo o contraseña incorrectos.";
            }
        } else {
            $response['error'] = "Correo y contraseña son obligatorios.";
        }
    } else {
        $response['error'] = "Acción inválida.";
    }
} else {
    $response['error'] = "Método no permitido.";
}

header('Content-Type: application/json');
echo json_encode($response);
