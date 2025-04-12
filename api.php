<?php

require_once __DIR__ . '/bootstrap.php';

use Core\App;

$database = App::resolve(Core\Database::class); // Resuelve la instancia desde el contenedor
$conn = $database->getConnection();

$response = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['UsuarioID'])) {
        $idUsuario = $_GET['UsuarioID'];

        $query = "SELECT * FROM Usuario WHERE UsuarioID = :idUsuario";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $response = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($response) {
            $response['ImagenPerfil'] = base64_encode($response['ImagenPerfil']);
            $response['BannerPerfil'] = base64_encode($response['BannerPerfil']);
        } else {
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
    } else {
        echo json_encode(['error' => 'Falta el parámetro UsuarioID']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $imagenPerfilBase64 = null;

                if (!empty($usuario['ImagenPerfil'])) {
                    $imagenPerfilBase64 = base64_encode($usuario['ImagenPerfil']);
                }

                // Iniciar sesión y almacenar datos del usuario
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['user_id'] = $usuario['UsuarioID'];
                $_SESSION['user_name'] = $usuario['NombreUsuario'];
                $_SESSION['user_role'] = $usuario['TipoUsuario'];
                $_SESSION['user_img'] = base64_encode($usuario['ImagenPerfil']);


                $response['message'] = "Inicio de sesión exitoso.";
                $response['user'] = [
                    'UsuarioID' => $usuario['UsuarioID'],
                    'NombreUsuario' => $usuario['NombreUsuario'],
                    'Correo' => $usuario['Correo'],
                    'Biografia' => $usuario['Biografia'],
                    'TipoUsuario' => $usuario['TipoUsuario'],
                    'ImagenPerfil' => $imagenPerfilBase64
                ];
            } else {
                $response['error'] = "Correo o contraseña incorrectos.";
            }
        } else {
            $response['error'] = "Correo y contraseña son obligatorios.";
        }
    } elseif ($action === 'modificar') {
        // Verificar que el usuario esté logueado
        if (isset($_SESSION['user_id'])) {
            $usuarioID = $_SESSION['user_id'];
            $nombreUsuario = $_POST['nombre_usuario'] ?? '';
            $biografia = $_POST['biografia'] ?? '';
            $imagenPerfil = $_FILES['imagen_perfil']['tmp_name'] ?? null;
            $bannerPerfil = $_FILES['banner_perfil']['tmp_name'] ?? null;

            // Validar que al menos uno de los campos esté presente
            if (!empty($nombreUsuario) || !empty($biografia) || !empty($imagenPerfil) || !empty($bannerPerfil)) {
                // Crear la consulta de actualización
                $query = "UPDATE Usuario SET NombreUsuario = :nombreUsuario, Biografia = :biografia";

                // Si hay imágenes, agregamos los campos de actualización para las imágenes
                if ($imagenPerfil) {
                    $imagenPerfilData = file_get_contents($imagenPerfil);
                    $query .= ", ImagenPerfil = :imagenPerfil";
                }
                if ($bannerPerfil) {
                    $bannerPerfilData = file_get_contents($bannerPerfil);
                    $query .= ", BannerPerfil = :bannerPerfil";
                }

                $query .= " WHERE UsuarioID = :usuarioID";

                // Preparar y ejecutar la consulta
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':nombreUsuario', $nombreUsuario);
                $stmt->bindParam(':biografia', $biografia);
                if (isset($imagenPerfilData)) {
                    $stmt->bindParam(':imagenPerfil', $imagenPerfilData, PDO::PARAM_LOB);
                }
                if (isset($bannerPerfilData)) {
                    $stmt->bindParam(':bannerPerfil', $bannerPerfilData, PDO::PARAM_LOB);
                }
                $stmt->bindParam(':usuarioID', $usuarioID);

                if ($stmt->execute()) {
                    $_SESSION['user_name'] = $nombreUsuario;  // Actualiza el nombre
                    if ($imagenPerfil) {
                        $_SESSION['user_img'] = base64_encode($imagenPerfilData);
                    }
                    $response['message'] = "Perfil actualizado con éxito.";
                } else {
                    $response['error'] = "Error al actualizar el perfil: " . $stmt->errorInfo()[2];
                }
            } else {
                $response['error'] = "Al menos uno de los campos (Nombre, Biografía, Imagen o Banner) debe ser modificado.";
            }
        } else {
            $response['error'] = "No estás autenticado.";
        }
    } else {
        $response['error'] = "Acción inválida.";
    }
} else {
    $response['error'] = "Método no permitido.";
}

header('Content-Type: application/json');
echo json_encode($response);
