<?php
// filepath: c:\xampp\htdocs\BDM_Capa_Z\api.php
include 'Models\Database.php';

$database = new Database();
$conn = $database->getConnection();

$response = [];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['idUsuario'])) {
            $idUsuario = $_GET['idUsuario'];
            $query = "SELECT * FROM usuarios WHERE idUsuario = :idUsuario";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            $response = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $query = "SELECT * FROM usuarios";
            $stmt = $conn->query($query);
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        break;

    case 'POST':
        if (isset($_GET['action']) && $_GET['action'] === 'register') {
            if (!empty($_POST['fullName']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['role']) && !empty($_POST['gender']) && !empty($_POST['birthdate'])) {
                $fullName = $_POST['fullName'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $gender = $_POST['gender'];
                $birthdate = $_POST['birthdate'];
                $role = ($_POST['role'] === 'instructor') ? 2 : 3;

                $photo = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $targetDir = "uploads/";
                    $photo = $targetDir . basename($_FILES['photo']['name']);

                    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo)) {
                        $response['error'] = "Error al subir la foto.";
                    }
                }

                // Verificar si el correo ya existe
                $query = "SELECT idUsuario FROM usuarios WHERE email = :email";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $response['error'] = "El correo ya está registrado. Por favor, usa otro.";
                } else {
                    $query = "INSERT INTO usuarios (fullName, gender, birthdate, photo, email, password, role) 
                              VALUES (:fullName, :gender, :birthdate, :photo, :email, :password, :role)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':fullName', $fullName);
                    $stmt->bindParam(':gender', $gender);
                    $stmt->bindParam(':birthdate', $birthdate);
                    $stmt->bindParam(':photo', $photo);
                    $stmt->bindParam(':email', $email);
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->bindParam(':role', $role);

                    if ($stmt->execute()) {
                        $response['message'] = "Usuario creado con éxito";
                        $response['user_id'] = $conn->lastInsertId();
                    } else {
                        $response['error'] = "Error al crear el usuario: " . $stmt->errorInfo()[2];
                    }
                }
            } else {
                $response['error'] = "Faltan datos. Por favor, completa todos los campos.";
            }
        } elseif (isset($_GET['action']) && $_GET['action'] === 'login') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $query = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $response['error'] = "El correo no está registrado.";
            } elseif ($user && $user['activo'] == 0) {
                $response['error'] = "La cuenta está desactivada. Contacta al administrador.";
            } elseif ($user && !password_verify($password, $user['password'])) {
                $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE idUsuario = :idUsuario");
                $stmt->bindParam(':idUsuario', $user['idUsuario']);
                $stmt->execute();

                if ($user['intentos_fallidos'] + 1 >= 3) {
                    $stmt = $conn->prepare("UPDATE usuarios SET activo = 0 WHERE idUsuario = :idUsuario");
                    $stmt->bindParam(':idUsuario', $user['idUsuario']);
                    $stmt->execute();
                    $response['error'] = "La cuenta ha sido desactivada después de múltiples intentos fallidos.";
                } else {
                    $response['error'] = "Contraseña incorrecta.";
                }
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0 WHERE idUsuario = :idUsuario");
                $stmt->bindParam(':idUsuario', $user['idUsuario']);
                $stmt->execute();

                $response['message'] = "Inicio de sesión exitoso";
                $response['user'] = $user;
            }
        } elseif (isset($_GET['action']) && $_GET['action'] === 'modificar') {
            if (!empty($_POST['idUsuario'])) {
                $idUsuario = $_POST['idUsuario'];

                $fieldsToUpdate = [];
                $params = [];

                if (!empty($_POST['fullName'])) {
                    $fieldsToUpdate[] = "fullName = :fullName";
                    $params[':fullName'] = $_POST['fullName'];
                }

                if (!empty($_POST['email'])) {
                    $fieldsToUpdate[] = "email = :email";
                    $params[':email'] = $_POST['email'];
                }

                if (!empty($_POST['password'])) {
                    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $fieldsToUpdate[] = "password = :password";
                    $params[':password'] = $hashedPassword;
                }

                if (!empty($_POST['birthdate'])) {
                    $birthdate = date('Y-m-d', strtotime($_POST['birthdate'])); // Asegura el formato correcto
                    $fieldsToUpdate[] = "birthdate = :birthdate";
                    $params[':birthdate'] = $birthdate;
                }

                if (!empty($_POST['role'])) {
                    $role = intval($_POST['role']);
                    $fieldsToUpdate[] = "role = :role";
                    $params[':role'] = $role;
                }

                if (!empty($_POST['gender'])) {
                    $gender = $_POST['gender'];
                    $fieldsToUpdate[] = "gender = :gender";
                    $params[':gender'] = $gender;
                }

                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $targetDir = "uploads/";
                    $photo = $targetDir . basename($_FILES['photo']['name']);
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo)) {
                        $fieldsToUpdate[] = "photo = :photo";
                        $params[':photo'] = $photo;
                    } else {
                        $response['error'] = "Error al subir la foto.";
                    }
                }

                // Verificar si hay al menos un campo para actualizar
                if (count($fieldsToUpdate) > 0) {
                    // Construimos la consulta dinámica
                    $query = "UPDATE usuarios SET " . implode(", ", $fieldsToUpdate) . " WHERE idUsuario = :idUsuario";
                    $stmt = $conn->prepare($query);

                    // Añadir el idUsuario al array de parámetros
                    $params[':idUsuario'] = $idUsuario;

                    if ($stmt->execute($params)) {
                        // Si la actualización es exitosa, actualizar la sesión
                        session_start();

                        if (!empty($_POST['fullName'])) {
                            $_SESSION['user_name'] = $_POST['fullName'];
                        }

                        if (!empty($_POST['role'])) {
                            $_SESSION['user_role'] = $role;
                        }

                        if (isset($photo)) {
                            $_SESSION['user_img'] = $photo; // Actualizar la imagen en la sesión
                        }

                        $response['message'] = "Usuario modificado con éxito";
                        $response['user_name'] = $_SESSION['user_name'];
                        $response['user_role'] = $_SESSION['user_role'];
                        $response['user_img'] = $_SESSION['user_img'];
                    } else {
                        $response['error'] = "Error al modificar el usuario: " . implode(" ", $stmt->errorInfo());
                    }
                } else {
                    $response['error'] = "No hay campos para modificar.";
                }
            } else {
                $response['error'] = "El ID de usuario es obligatorio.";
            }
        }

        break;
    case 'DELETE':
        //HAY QUE CAMBIAR PARA QUE EN VEZ DE QUE SE BORRE SOLO SE DESACTIVE
        $idUsuario = $_GET['idUsuario'];
        $query = "DELETE FROM usuarios WHERE idUsuario = :idUsuario";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response['message'] = "Usuario eliminado con éxito";
        } else {
            $response['error'] = "Error al eliminar el usuario";
        }
        break;

    default:
        $response['error'] = "Método no permitido";
}

header('Content-Type: application/json');
echo json_encode($response);