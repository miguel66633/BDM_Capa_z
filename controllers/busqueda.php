<?php

use Core\App;
use Core\Database;

// Descomenta estas líneas SOLO si necesitas depurar errores PHP en la respuesta AJAX
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$db = App::resolve(Database::class);

$isAjaxSearch = isset($_GET['ajax_search']) && $_GET['ajax_search'] == '1';
$searchTerm = $_GET['term'] ?? '';

if ($isAjaxSearch) {
    // --- Lógica para AJAX (Vista Previa) ---
    header('Content-Type: application/json');

    if (empty($searchTerm)) {
        echo json_encode([]);
        exit;
    }

    try {
        $query = "SELECT UsuarioID, NombreUsuario, ImagenPerfil
                  FROM Usuario
                  WHERE NombreUsuario LIKE :term
                  LIMIT 10";

        $users = $db->query($query, ['term' => '%' . $searchTerm . '%'])->get();

        foreach ($users as &$user) {
            if (!empty($user['ImagenPerfil'])) {
                $base64Image = base64_encode($user['ImagenPerfil']);
                if ($base64Image === false) {
                    $user['ImagenPerfilBase64'] = '/Resources/images/perfilPre.jpg';
                } else {
                    $user['ImagenPerfilBase64'] = 'data:image/jpeg;base64,' . $base64Image;
                }
            } else {
                $user['ImagenPerfilBase64'] = '/Resources/images/perfilPre.jpg';
            }
            unset($user['ImagenPerfil']);
        }

        $jsonResponse = json_encode($users, JSON_UNESCAPED_UNICODE);
        if (json_last_error() !== JSON_ERROR_NONE) {
             error_log("AJAX Search: JSON Encode Error - " . json_last_error_msg());
             echo json_encode(['error' => 'Server error preparing data.']);
        } else {
             echo $jsonResponse;
        }

    } catch (\Throwable $e) {
        error_log("AJAX Search Exception/Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Error during search.']);
    }
    exit; // Terminar ejecución para AJAX

} else {
    // --- Lógica para Carga Normal de Página ---
    $searchTermFull = isset($_GET['term']) ? $_GET['term'] : null;
    $usuarios = []; // Definir $usuarios para que exista en la vista

    if ($searchTermFull) {
        $sql = "SELECT UsuarioID, NombreUsuario, Correo, ImagenPerfil FROM Usuario
                WHERE NombreUsuario LIKE :searchTerm OR Correo LIKE :searchTerm";
        $usuarios = $db->query($sql, ['searchTerm' => '%' . $searchTermFull . '%'])->get();
    }
    // La variable $usuarios será usada por lateral.php
}