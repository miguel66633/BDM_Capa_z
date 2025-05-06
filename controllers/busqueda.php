<?php

use Core\App;
use Core\Database;

// Ya no necesitamos depurar AJAX aquí
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$db = App::resolve(Database::class);

// --- Lógica ÚNICA: Carga Normal de Página ---
$searchTermFull = $_GET['term'] ?? null; // Obtener término de la URL
$usuarios = []; // Definir $usuarios para que exista en la vista

if ($searchTermFull) {
    // Buscar usuarios basados en el término
    $sql = "SELECT UsuarioID, NombreUsuario, Correo, ImagenPerfil FROM Usuario
            WHERE NombreUsuario LIKE :searchTerm OR Correo LIKE :searchTerm";
    // Ejecutar la consulta
    $usuarios = $db->query($sql, ['searchTerm' => '%' . $searchTermFull . '%'])->get();
    // Nota: Ya NO convertimos la imagen a base64 aquí, lo hará la vista si es necesario.
}