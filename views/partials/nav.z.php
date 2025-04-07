<nav class="sidebar">
    <div class="logo" onclick="window.location.href='/inicio'">Z</div>
    <button class="submit-btn" onclick="window.location.href='/inicio'">
        <img src="Resources/images/inicio.svg" alt="Icono de Inicio" class="icono-btn">Inicio</button>
    <button class="submit-btn" onclick="window.location.href='/mensajes'">
        <img src="Resources/images/mensajes.svg" alt="Icono de Inicio" class="icono-btn">Mensajes</button>
    <button class="submit-btn" onclick="window.location.href='/guardados'">
        <img src="Resources/images/guardados.svg" alt="Icono de Inicio" class="icono-btn">Guardados</button>
    <button class="submit-btn" onclick="window.location.href='/perfil'">
        <img src="Resources/images/perfil.svg" alt="Icono de Inicio" class="icono-btn">Perfil</button>

    <!-- Botón "Postear" arriba del perfil -->
    <button class="submit-btn postear-btn" onclick="openModal()">Postear</button>

    <!-- Contenedor del perfil en la parte inferior de la sidebar -->
    <div class="profile-container" onclick="toggleMenu()">
        <img src="Resources/images/perfil.jpg" alt="Foto de perfil" class="profile-pic">
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