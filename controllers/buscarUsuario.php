<?php

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

// Buscar usuarios cuyo nombre coincida con el término, excluyendo al usuario actual
$query = "
    SELECT 
        UsuarioID,
        NombreUsuario,
        ImagenPerfil
    FROM 
        Usuario
    WHERE 
        NombreUsuario LIKE :termino
        -- ***** NUEVO: Excluir al usuario actual de los resultados *****
        AND UsuarioID != :usuarioIdActual 
    LIMIT 10;
";

$resultados = $db->query($query, [
    'termino' => '%' . $termino . '%',
    'usuarioIdActual' => $usuarioIdActual 
])->get();

// Convertir las imágenes a base64
foreach ($resultados as &$usuario) {
    if (!empty($usuario['ImagenPerfil'])) {
        $usuario['ImagenPerfil'] = base64_encode($usuario['ImagenPerfil']);
    } else {
        $usuario['ImagenPerfil'] = null; // O la ruta a tu imagen por defecto si prefieres
    }
}

// Devolver los resultados como JSON
echo json_encode($resultados);
exit;