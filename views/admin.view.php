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
                    <h2 id="user-count">100</h2>
                    <br><br><br><br><br><br><br>
                    <h1>Publicaciones totales:</h1>
                    <h2 id="post-count">50</h2>
                </div>
            </section>
            
            <!-- Lista de reportes -->
            <section id="reports">
                <h2>Reportes</h2>
                <br><br>
                <div id="reports-content">
                    <ul>
                        <li><button onclick="mostrarReportesPopup()">Reporte de (Inserte Usuario)</button></li><br>
                        <li><button onclick="mostrarReportesPopup()">Reporte de (Inserte Usuario)</button></li><br>
                        <li><button onclick="mostrarReportesPopup()">Reporte de (Inserte Usuario)</button></li><br>
                        <li><button onclick="mostrarReportesPopup()">Reporte de (Inserte Usuario)</button></li>
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
            <h2>Reporte de (Inserte Usuario)</h2>
            <div class="stats-container-reporte">
                <div class="stat-item">
                    <h3>Publicaciones subidas</h3>
                    <p id="publicaciones-subidas">0</p>
                </div>
                <div class="stat-item">
                    <h3>Likes dados</h3>
                    <p id="likes-dados">0</p>
                </div>
                <div class="stat-item">
                    <h3>Comentarios hechos</h3>
                    <p id="comentarios-hechos">0</p>
                </div>
                <div class="stat-item">
                    <h3>Seguidores</h3>
                    <p id="seguidores-obtenidos">0</p>
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
