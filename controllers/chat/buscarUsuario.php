<?php
//es el buscar el usuario de mensajes para un nuevo chat
use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar el encabezado para devolver JSON
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Debes iniciar sesión para buscar usuarios.']);
    exit;
}

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener el término de búsqueda y el ID del usuario actual
$termino = $_POST['termino'] ?? '';
$usuarioIdActual = $_SESSION['user_id']; // ID del usuario logueado

if (empty($termino)) {
    echo json_encode(['error' => 'El término de búsqueda está vacío.']);
    exit;
}

try {
    // Llamar al Stored Procedure
    $resultados = $db->callProcedure('sp_BuscarUsuariosParaChat', [$termino, $usuarioIdActual]);

    if ($resultados) { // Verificar si $resultados no es false o null
        foreach ($resultados as &$usuario) {
            if (!empty($usuario['ImagenPerfil'])) {
                $usuario['ImagenPerfil'] = base64_encode($usuario['ImagenPerfil']);
            } else {
                $usuario['ImagenPerfil'] = null; 
            }
        }
    } else {
        $resultados = [];
    }


} catch (\PDOException $e) {
    error_log("Error en buscarUsuario.php al llamar sp_BuscarUsuariosParaChat: " . $e->getMessage());
    $resultados = [];
     echo json_encode(['error' => 'Error del servidor al buscar usuarios.']);
     exit;
}

echo json_encode($resultados);
exit;