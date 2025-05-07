<?php include base_path('controllers/admin/busquedaAdmin.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="icon" href="Resources/images/logo-Z.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <!-- Barra lateral izquierda -->
        
        <?php require base_path('views/partials/nav.z.php'); ?>

        <!-- Contenedor principal para el contenido de administración -->
        <main id="admin-content">
            <!-- Estadísticas -->
            <section id="stats">
                <h2>Estadísticas</h2>
                <br><br><br><br><br><br><br><br>
                <div id="stats-content">
                    <h1>Usuarios registrados:</h1>
                    <h2 id="user-count"><?= $estadisticas['UsuariosRegistrados']; ?></h2>
                    <br><br><br><br><br><br><br>
                    <h1>Publicaciones totales:</h1>
                    <h2 id="post-count"><?= $estadisticas['PublicacionesGenerales']; ?></h2>
                </div>
            </section>
            
            <!-- Lista de reportes -->
            <section id="reports">
                <h2>Reportes</h2>
                <br><br>
                <div id="reports-content">
                    <ul>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <li>
                                    <!-- Mostrar imagen de usuario -->
                                    <?php if ($usuario['ImagenPerfil']): ?>
                                        <img src="data:image/jpeg;base64,<?= base64_encode($usuario['ImagenPerfil']); ?>" alt="<?= htmlspecialchars($usuario['NombreUsuario']); ?>" class="user-img">
                                    <?php else: ?>
                                        <img src="/Resources/images/perfilPre.jpg" alt="Imagen por defecto" class="user-img">
                                    <?php endif; ?>

                                    <button onclick="mostrarReportesPopup(<?= $usuario['UsuarioID']; ?>)">
                                        <?= htmlspecialchars($usuario['NombreUsuario']); ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No se encontraron usuarios.</li>
                        <?php endif; ?>
                        <!-- Más reportes -->
                    </ul>
                </div>
            </section>
        </main>
    </div>
    <!--Ventana de reportes-->
    <div id="ReportesPopup" class="popup-container">
        <div class="popup">
            <span class="close" onclick="cerrarReportesPopup()">&times;</span>
            <h2></h2>
            <div class="stats-container-reporte">
                <div class="stat-item">
                    <h3>Publicaciones subidas</h3>
                    <p id="publicaciones-subidas"></p>
                </div>
                <div class="stat-item">
                    <h3>Likes dados</h3>
                    <p id="likes-dados"></p>
                </div>
                <div class="stat-item">
                    <h3>Comentarios hechos</h3>
                    <p id="comentarios-hechos"></p>
                </div>
                <div class="stat-item">
                    <h3>Guardados</h3>
                    <p id="guardados-hechos"></p>
                </div>
            </div>

            <!-- <div class="button-container">
                <button class="submit-btn-Report">Salir</button>
            </div> -->
            
        </div>
    </div>
    <script src="../js/home.js"></script>
</body>
</html>
