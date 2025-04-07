<nav class="sidebar">
    <div class="logo" onclick="window.location.href='../html/home.html'">Z</div>
    <button class="submit-btn" onclick="window.location.href='../html/home.html'">
        <img src="Resourses/images/inicio.svg" alt="Icono de Inicio" class="icono-btn">Inicio</button>
    <button class="submit-btn" onclick="window.location.href='../html/messages.html'">
        <img src="Resourses/images/mensajes.svg" alt="Icono de Inicio" class="icono-btn">Mensajes</button>
    <button class="submit-btn" onclick="window.location.href='../html/guardados.html'">
        <img src="Resourses/images/guardados.svg" alt="Icono de Inicio" class="icono-btn">Guardados</button>
    <button class="submit-btn" onclick="window.location.href='../html/perfil.html'">
        <img src="Resourses/images/perfil.svg" alt="Icono de Inicio" class="icono-btn">Perfil</button>

    <!-- Botón "Postear" arriba del perfil -->
    <button class="submit-btn postear-btn" onclick="openModal()">Postear</button>

    <!-- Contenedor del perfil en la parte inferior de la sidebar -->
    <div class="profile-container" onclick="toggleMenu()">
        <img src="Resourses/images/perfil.jpg" alt="Foto de perfil" class="profile-pic">
        <div class="profile-info">
            <p class="username">Miguel Reyes</p>
            <p class="user-handle">@migueriro</p>
        </div>
        
        <!-- Menú flotante dentro del mismo contenedor -->
        <div id="profile-menu" class="profile-menu">
            <button class="submit-btn" onclick="window.location.href='/Z'">Cerrar sesion</button>
            <button onclick="window.location.href='/admin'">Admin</button>
        </div>
    </div>
 </nav>