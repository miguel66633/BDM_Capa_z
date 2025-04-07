<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin / Z</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="container">
        <!-- Barra lateral izquierda -->
        <nav class="sidebar">
            <div class="logo" onclick="window.location.href='../html/home.html'">Z</div>
            <button class="submit-btn" onclick="window.location.href='../html/home.html'">
                <img src="../images/inicio.svg" alt="Icono de Inicio" class="icono-btn">Inicio</button>
            <button class="submit-btn" onclick="window.location.href='../html/messages.html'">
                <img src="../images/mensajes.svg" alt="Icono de Inicio" class="icono-btn">Mensajes</button>
            <button class="submit-btn" onclick="window.location.href='../html/guardados.html'">
                <img src="../images/guardados.svg" alt="Icono de Inicio" class="icono-btn">Guardados</button>
            <button class="submit-btn" onclick="window.location.href='../html/perfil.html'">
                <img src="../images/perfil.svg" alt="Icono de Inicio" class="icono-btn">Perfil</button>

            <!-- Botón "Postear" arriba del perfil -->
            <button class="submit-btn postear-btn" onclick="openModal()">Postear</button>

            <!-- Contenedor del perfil en la parte inferior de la sidebar -->
            <div class="profile-container" onclick="toggleMenu()">
                <img src="../images/perfil.jpg" alt="Foto de perfil" class="profile-pic">
                <div class="profile-info">
                  <p class="username">Miguel Reyes</p>
                  <p class="user-handle">@migueriro</p>
                </div>
              
                <!-- Menú flotante dentro del mismo contenedor -->
                <div id="profile-menu" class="profile-menu">
                  <button onclick="logout()">Cerrar sesión</button>
                  <button onclick="window.location.href='../html/admin.html'">Admin</button>
                </div>
              </div>
        </nav>

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
