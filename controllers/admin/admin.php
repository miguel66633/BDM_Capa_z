<?php
use Core\App;
use Core\Database;

// Resolver la conexión a la base de datos
$db = App::resolve(Database::class);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario es administrador (asumiendo que tienes un middleware o verificación similar)
// Ejemplo: if (!($_SESSION['user_role'] ?? null === 2)) { header('Location: /inicio'); exit; }


// Manejo de solicitudes AJAX para obtener el reporte sin recargar la página
if (isset($_GET['UsuarioID']) && isset($_GET['ajax'])) {
    $usuarioIdAjax = $_GET['UsuarioID']; // Renombrar para evitar colisión

    // Llamar al procedimiento almacenado para obtener el reporte del usuario
    $reporteAjax = $db->callProcedure("sp_EventosAdmin", ['REPORTE', $usuarioIdAjax]);

    // Formatear la respuesta en JSON
    header('Content-Type: application/json');
    echo json_encode($reporteAjax);
    exit; // Evitar que se cargue la vista completa
}

$usuarioIdParaReporte = $_GET['UsuarioID'] ?? ($_SESSION['user_id'] ?? null);
$reporte = []; // Inicializar

if ($usuarioIdParaReporte) {
    // Llamar al procedimiento almacenado para obtener el reporte en la vista
    $reporte = $db->callProcedure("sp_EventosAdmin", ['REPORTE', $usuarioIdParaReporte]);
}


// Obtener la lista de usuarios para mostrarlos en la interfaz usando el SP
$usuarios = [];
try {
    // Llamar a sp_BuscarAdminUsuarios con NULL para obtener todos los usuarios
    $usuarios = $db->callProcedure('sp_BuscarAdminUsuarios', [null]);
} catch (\PDOException $e) {
    error_log("Error en controllers/admin/admin.php al llamar sp_BuscarAdminUsuarios: " . $e->getMessage());
    // $usuarios ya está inicializado como array vacío
}

// Obtener estadísticas usando funciones
$estadisticas = null;
try {
    $estadisticas = $db->getEstadisticas();
} catch (\PDOException $e) {
    error_log("Error en controllers/admin/admin.php al obtener estadísticas con funciones: " . $e->getMessage());
    // Fallback en caso de error
    $estadisticas = ['UsuariosRegistrados' => 0, 'PublicacionesGenerales' => 0];
}


// Pasar los datos a la vista para que se muestren en la UI
view("admin.view.php", [
    'heading' => 'Administración', // Título más genérico para la página de admin
    'reporte' => $reporte, // Reporte del usuario seleccionado (puede estar vacío si no hay $usuarioIdParaReporte)
    'usuarios' => $usuarios, // Lista de usuarios disponibles
    'estadisticas' => $estadisticas // Estadísticas del sitio
]);