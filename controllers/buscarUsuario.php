<?php

use Core\App;
use Core\Database;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar el encabezado para devolver JSON
header('Content-Type: application/json');

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

// Obtener el término de búsqueda
$termino = $_POST['termino'] ?? '';

if (empty($termino)) {
    echo json_encode(['error' => 'El término de búsqueda está vacío.']);
    exit;
}

// Buscar usuarios cuyo nombre coincida con el término
$query = "
    SELECT 
        UsuarioID,
        NombreUsuario,
        ImagenPerfil
    FROM 
        Usuario
    WHERE 
        NombreUsuario LIKE :termino
    LIMIT 10;
";

$resultados = $db->query($query, ['termino' => '%' . $termino . '%'])->get();

// Convertir las imágenes a base64
foreach ($resultados as &$usuario) {
    if (!empty($usuario['ImagenPerfil'])) {
        $usuario['ImagenPerfil'] = base64_encode($usuario['ImagenPerfil']);
    } else {
        $usuario['ImagenPerfil'] = null; // Imagen por defecto si no hay imagen
    }
}

// Devolver los resultados como JSON
echo json_encode($resultados);
exit;