<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

// Obtener el término de búsqueda (si existe)
$searchTerm = isset($_GET['term']) ? $_GET['term'] : null;

// Crear la consulta SQL para obtener usuarios, incluyendo la imagen en BLOB
$sql = "SELECT UsuarioID, NombreUsuario, Correo, ImagenPerfil FROM Usuario";

if ($searchTerm) {
    // Si hay un término de búsqueda, filtrar por NombreUsuario o Correo
    $sql .= " WHERE NombreUsuario LIKE :searchTerm OR Correo LIKE :searchTerm";
    $params = ['searchTerm' => '%' . $searchTerm . '%'];
} else {
    // Si no hay término de búsqueda, traer todos los usuarios
    $params = [];
}


// Ejecutar la consulta
$usuarios = $db->query($sql, $params)->get();
