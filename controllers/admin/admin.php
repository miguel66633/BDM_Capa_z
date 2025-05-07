<?php

use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);


// Manejo de solicitudes AJAX para obtener el reporte sin recargar la página
if (isset($_GET['UsuarioID']) && isset($_GET['ajax'])) {
    $usuarioId = $_GET['UsuarioID'];

    // Llamar al procedimiento almacenado para obtener el reporte del usuario
    $reporte = $db->callProcedure("sp_EventosAdmin", ['REPORTE', $usuarioId]);

    // Formatear la respuesta en JSON
    header('Content-Type: application/json');
    echo json_encode($reporte);
    exit; // Evitar que se cargue la vista completa
}

// Obtener el ID del usuario para mostrar su reporte en la vista
$usuarioId = $_GET['UsuarioID'] ?? $_SESSION['user_id'];

// Llamar al procedimiento almacenado para obtener el reporte en la vista
$reporte = $db->callProcedure("sp_EventosAdmin", ['REPORTE', $usuarioId]);

// Obtener la lista de usuarios para mostrarlos en la interfaz
$usuarios = $db->query("SELECT UsuarioID, NombreUsuario, ImagenPerfil FROM Usuario")->get();

$estadisticas = $db->query("SELECT * FROM Estadisticas")->find();

// Pasar los datos a la vista para que se muestren en la UI
view("admin.view.php", [
    'heading' => 'Administración de Usuarios',
    'reporte' => $reporte, // Reporte del usuario seleccionado
    'usuarios' => $usuarios, // Lista de usuarios disponibles
    'estadisticas' => $estadisticas
]);
