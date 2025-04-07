<?php

// include 'Models\Database.php';

// $database = new Database();
// $conn = $database->getConnection();

// $response = [];

// switch ($_SERVER['REQUEST_METHOD']) {
//     case 'GET':
//         if (isset($_GET['idUsuario'])) {
//             $idUsuario = $_GET['idUsuario'];
//             $query = "SELECT * FROM usuarios WHERE idUsuario = :idUsuario";
//             $stmt = $conn->prepare($query);
//             $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
//             $stmt->execute();
//             $response = $stmt->fetch(PDO::FETCH_ASSOC);
//         } else {
//             $query = "SELECT * FROM usuarios";
//             $stmt = $conn->query($query);
//             $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
//         }
//         break;

//     case 'POST':
//         if (isset($_POST['accion']) && $_POST['accion'] === 'registro') {

//             if (!empty($_POST['full_name']) && !empty($_POST['correo']) && !empty($_POST['contrasena']) && !empty($_POST['role']) && !empty($_POST['gender']) && !empty($_POST['birthdate'])) {
//                 $nombre = $_POST['full_name'];
//                 $correo = $_POST['correo'];
//                 $password = $_POST['contrasena'];
//                 $genero = $_POST['gender'];
//                 $fecha_nacimiento = $_POST['birthdate'];
//                 $id_rol = ($_POST['role'] === 'instructor') ? 2 : 3;


//                 $foto_avatar = null;
//                 if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

//                     $targetDir = "uploads/";
//                     $foto_avatar = $targetDir . basename($_FILES['photo']['name']);


//                     if (!move_uploaded_file($_FILES['photo']['tmp_name'], $foto_avatar)) {
//                         $response['error'] = "Error al subir la foto.";
//                     }
//                 }

//                 // Verificar correo ya exigitste
//                 $query = "SELECT idUsuario FROM usuarios WHERE correo = :correo";
//                 $stmt = $conn->prepare($query);
//                 $stmt->bindParam(':correo', $correo);
//                 $stmt->execute();

//                 if ($stmt->rowCount() > 0) {
//                     $response['error'] = "El correo ya está registrado. Por favor, usa otro.";
//                 } else {
//                     $query = "INSERT INTO usuarios (nombre, genero, fecha_nacimiento, foto_avatar, correo, contrasena, id_rol) 
//                               VALUES (:nombre, :genero, :fecha_nacimiento, :foto_avatar, :correo, :contrasena, :id_rol)";
//                     $stmt = $conn->prepare($query);
//                     $stmt->bindParam(':nombre', $nombre);
//                     $stmt->bindParam(':genero', $genero);
//                     $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
//                     $stmt->bindParam(':foto_avatar', $foto_avatar);
//                     $stmt->bindParam(':correo', $correo);
//                     $stmt->bindParam(':contrasena', $password);
//                     $stmt->bindParam(':id_rol', $id_rol);

//                     if ($stmt->execute()) {
//                         $response['message'] = "Usuario creado con éxito";
//                         $response['user_id'] = $conn->lastInsertId();
//                     } else {
//                         $response['error'] = "Error al crear el usuario: " . $stmt->errorInfo()[2];
//                     }
//                 }
//             } else {
//                 $response['error'] = "Faltan datos. Por favor, completa todos los campos.";
//             }
//         } elseif (isset($_POST['accion']) && $_POST['accion'] === 'inicio_sesion') {
//             $correo = $_POST['correo'];
//             $password = $_POST['contrasena'];

//             $query = "SELECT * FROM usuarios WHERE correo = :correo";
//             $stmt = $conn->prepare($query);
//             $stmt->bindParam(':correo', $correo);
//             $stmt->execute();
//             $user = $stmt->fetch(PDO::FETCH_ASSOC);

//             if (!$user) {
//                 $response['error'] = "El correo no está registrado.";
//             } elseif ($user && $user['activo'] == 0) {
//                 $response['error'] = "La cuenta está desactivada. Contacta al administrador.";
//             } elseif ($user && $password !== $user['contrasena']) {
//                 $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE idUsuario = :idUsuario");
//                 $stmt->bindParam(':idUsuario', $user['idUsuario']);
//                 $stmt->execute();

//                 if ($user['intentos_fallidos'] + 1 >= 3) {
//                     $stmt = $conn->prepare("UPDATE usuarios SET activo = 0 WHERE idUsuario = :idUsuario");
//                     $stmt->bindParam(':idUsuario', $user['idUsuario']);
//                     $stmt->execute();
//                     $response['error'] = "La cuenta ha sido desactivada después de múltiples intentos fallidos.";
//                 } else {
//                     $response['error'] = "Contraseña incorrecta.";
//                 }
//             } else {
//                 $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0 WHERE idUsuario = :idUsuario");
//                 $stmt->bindParam(':idUsuario', $user['idUsuario']);
//                 $stmt->execute();

//                 $response['message'] = "Inicio de sesión exitoso";
//                 $response['user'] = $user;
//             }
//         } elseif (isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
//             if (!empty($_POST['idUsuario'])) {
//                 $idUsuario = $_POST['idUsuario'];

//                 $fieldsToUpdate = [];
//                 $params = [];

//                 if (!empty($_POST['full_name'])) {
//                     $fieldsToUpdate[] = "nombre = :nombre";
//                     $params[':nombre'] = $_POST['full_name'];
//                 }

//                 if (!empty($_POST['correo'])) {
//                     $fieldsToUpdate[] = "correo = :correo";
//                     $params[':correo'] = $_POST['correo'];
//                 }

//                 if (!empty($_POST['contrasena'])) {
//                     $fieldsToUpdate[] = "contrasena = :contrasena";
//                     $params[':contrasena'] = $_POST['contrasena'];
//                 }

//                 if (!empty($_POST['fecha_nacimiento'])) {
//                     $fecha_nacimiento = date('Y-m-d', strtotime($_POST['fecha_nacimiento'])); // Asegura el formato correcto
//                     $fieldsToUpdate[] = "fecha_nacimiento = :fecha_nacimiento";
//                     $params[':fecha_nacimiento'] = $fecha_nacimiento;
//                 }

//                 if (!empty($_POST['role'])) {
//                     $id_rol = intval($_POST['role']);
//                     $fieldsToUpdate[] = "id_rol = :id_rol";
//                     $params[':id_rol'] = $id_rol;
//                 }

//                 if (!empty($_POST['genero'])) {
//                     $genero = $_POST['genero'];
//                     $fieldsToUpdate[] = "genero = :genero";
//                     $params[':genero'] = $genero;
//                 }

//                 if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
//                     $targetDir = "uploads/";
//                     $foto_avatar = $targetDir . basename($_FILES['photo']['name']);
//                     if (move_uploaded_file($_FILES['photo']['tmp_name'], $foto_avatar)) {
//                         $fieldsToUpdate[] = "foto_avatar = :foto_avatar";
//                         $params[':foto_avatar'] = $foto_avatar;
//                     } else {
//                         $response['error'] = "Error al subir la foto.";
//                     }
//                 }

//                 // Verificar si hay al menos un campo para actualizar
//                 if (count($fieldsToUpdate) > 0) {
//                     // Construimos la consulta dinámica
//                     $query = "UPDATE usuarios SET " . implode(", ", $fieldsToUpdate) . " WHERE idUsuario = :idUsuario";
//                     $stmt = $conn->prepare($query);

//                     // Añadir el idUsuario al array de parámetros
//                     $params[':idUsuario'] = $idUsuario;

//                     if ($stmt->execute($params)) {
//                         // Si la actualización es exitosa, actualizar la sesión
//                         session_start();

//                         if (!empty($_POST['full_name'])) {
//                             $_SESSION['user_name'] = $_POST['full_name'];
//                         }

//                         if (!empty($_POST['role'])) {
//                             $_SESSION['user_role'] = $id_rol;
//                         }

//                         if (isset($foto_avatar)) {
//                             $_SESSION['user_img'] = $foto_avatar; // Actualizar la imagen en la sesión
//                         }

//                         $response['message'] = "Usuario modificado con éxito";
//                         $response['user_name'] = $_SESSION['user_name'];
//                         $response['user_role'] = $_SESSION['user_role'];
//                         $response['user_img'] = $_SESSION['user_img'];
//                     } else {
//                         $response['error'] = "Error al modificar el usuario: " . implode(" ", $stmt->errorInfo());
//                     }
//                 } else {
//                     $response['error'] = "No hay campos para modificar.";
//                 }
//             } else {
//                 $response['error'] = "El ID de usuario es obligatorio.";
//             }
//         }

//         break;
//     case 'DELETE':
//         //HAY QUE CAMBIAR PARA QUE EN VEZ DE QUE SE BORRE SOLO SE DESACTIVE
//         $idUsuario = $_GET['idUsuario'];
//         $query = "DELETE FROM usuarios WHERE idUsuario = :idUsuario";
//         $stmt = $conn->prepare($query);
//         $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

//         if ($stmt->execute()) {
//             $response['message'] = "Usuario eliminado con éxito";
//         } else {
//             $response['error'] = "Error al eliminar el usuario";
//         }
//         break;

//     default:
//         $response['error'] = "Método no permitido";
// }

// header('Content-Type: application/json');
// echo json_encode($response);