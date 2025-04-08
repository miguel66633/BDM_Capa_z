<?php
ob_start();
session_start();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

use Core\App;
use Core\Database;
use Core\Validator;

ob_clean();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$response = ['success' => false];

try {
    $db = App::resolve(Database::class);
    
    try {
        $db->query('SELECT 1')->find();
    } catch (PDOException $e) {
        throw new Exception('Error de conexión a la base de datos: ' . $e->getMessage());
    }

    $data = null;
    if ($method === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON inválido: ' . json_last_error_msg());
        }
    }

    switch ($action) {
        case 'register':
            if ($method === 'POST') {
                try {
                    // Log de datos recibidos
                    file_put_contents('register_debug.log', 
                        date('Y-m-d H:i:s') . " - Datos recibidos: " . json_encode($data) . "\n", 
                        FILE_APPEND
                    );
        
                    // Validaciones
                    $errors = [];
                    if (!Validator::email($data['email'])) {
                        $errors['email'] = 'Email inválido';
                    }
                    if (!Validator::string($data['password'], 7, 255)) {
                        $errors['password'] = 'Contraseña inválida';
                    }
        
                    if (!empty($errors)) {
                        throw new Exception('Errores de validación: ' . json_encode($errors));
                    }
        
                    // // Verificar si la tabla existe
                    // $tables = $db->query("SHOW TABLES LIKE 'users'")->get();
                    // if (empty($tables)) {
                    //     // Crear la tabla si no existe
                    //     $db->query("
                    //         CREATE TABLE users (
                    //             id INT AUTO_INCREMENT PRIMARY KEY,
                    //             email VARCHAR(255) UNIQUE NOT NULL,
                    //             password VARCHAR(255) NOT NULL,
                    //             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    //         )
                    //     ");
                    // }
        
                    // Insertar usuario
                    $result = $db->query('INSERT INTO users(email, password) VALUES(:email, :password)', [
                        'email' => $data['email'],
                        'password' => password_hash($data['password'], PASSWORD_DEFAULT)
                    ]);
        
                    // Verificar inserción
                    $newUser = $db->query('SELECT * FROM users WHERE email = :email', [
                        'email' => $data['email']
                    ])->find();
        
                    if ($newUser) {
                        file_put_contents('register_debug.log', 
                            date('Y-m-d H:i:s') . " - Usuario creado con ID: " . $newUser['id'] . "\n", 
                            FILE_APPEND
                        );
        
                        $_SESSION['user'] = [
                            'id' => $newUser['id'],
                            'email' => $newUser['email']
                        ];
        
                        $response = [
                            'success' => true,
                            'message' => 'Usuario registrado exitosamente',
                            'user_id' => $newUser['id']
                        ];
                    } else {
                        throw new Exception('Error al crear usuario - no se encontró después de la inserción');
                    }
        
                } catch (Exception $e) {
                    file_put_contents('register_debug.log', 
                        date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", 
                        FILE_APPEND
                    );
                    throw $e;
                }
            }
            break;

        case 'login':
            if ($method === 'POST') {
                $user = $db->query('SELECT * FROM users WHERE email = :email', [
                    'email' => $data['email']
                ])->find();

                if (!$user) {
                    throw new Exception('Usuario no encontrado', 401);
                }

                if (!password_verify($data['password'], $user['password'])) {
                    throw new Exception('Contraseña incorrecta', 401);
                }

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email']
                ];

                $response = [
                    'success' => true,
                    'message' => 'Login exitoso'
                ];
            }
            break;

        case 'logout':
            session_destroy();
            $response = [
                'success' => true,
                'message' => 'Sesión cerrada'
            ];
            break;
        case 'test':
            $response = [
                'success' => true,
                'message' => 'API funcionando correctamente',
                'time' => date('Y-m-d H:i:s')
            ];
            break;

        default:
            throw new Exception('Endpoint no encontrado', 404);
    }

    echo json_encode($response);

} catch (Exception $e) {

    $code = $e->getCode() ?: 500;
    http_response_code($code);
    
    if (!isset($response['errors'])) {
        $response['error'] = $e->getMessage();
    }
    
    echo json_encode($response);
} finally {

    exit();
}