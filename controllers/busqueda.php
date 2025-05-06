<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$searchTermFull = $_GET['term'] ?? null; 
$usuarios = []; 

if ($searchTermFull) {
    // Buscar usuarios basados en el término
    $sql = "SELECT UsuarioID, NombreUsuario, Correo, ImagenPerfil FROM Usuario
            WHERE NombreUsuario LIKE :searchTerm OR Correo LIKE :searchTerm";
    $usuarios = $db->query($sql, ['searchTerm' => '%' . $searchTermFull . '%'])->get();
}