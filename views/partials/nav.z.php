<nav class="sidebar">
    <div class="logo" onclick="window.location.href='/inicio'">Z</div>
    <button class="submit-btn" onclick="window.location.href='/inicio'">
        <img src="/Resources/images/inicio.svg" alt="Icono de Inicio" class="icono-btn">Inicio</button>
    <button class="submit-btn" onclick="window.location.href='/mensajes'">
        <img src="/Resources/images/mensajes.svg" alt="Icono de Inicio" class="icono-btn">Mensajes</button>
    <button class="submit-btn" onclick="window.location.href='/guardados'">
        <img src="/Resources/images/guardados.svg" alt="Icono de Inicio" class="icono-btn">Guardados</button>
        <?php
        // Asegúrate de que la sesión está iniciada y el user_id existe
        $profileLink = '/inicioSesion'; // Link por defecto si no está logueado
        if (isset($_SESSION['user_id'])) {
            $profileLink = '/perfil/' . $_SESSION['user_id'];
        }
    ?>
    <button class="submit-btn" onclick="window.location.href='<?php echo $profileLink; ?>'">
        <img src="/Resources/images/perfil.svg" alt="Icono de Perfil" class="icono-btn">Perfil</button>
        
    <!-- Botón "Postear" arriba del perfil -->
    <button class="submit-btn postear-btn" onclick="openModal()">Postear</button>

    <!-- Contenedor del perfil en la parte inferior de la sidebar -->
    <div class="profile-container" onclick="toggleMenu()">
    <img src="<?php echo isset($_SESSION['user_img']) && !empty($_SESSION['user_img']) ? 'data:image/jpeg;base64,' . $_SESSION['user_img'] : '/Resources/images/perfilpre.jpg'; ?>" alt="Foto de perfil" class="profile-pic">

        <div class="profile-info">
            <p class="username"><?php echo $_SESSION['user_name']; ?></p>           
        </div>
        
        <!-- Menú flotante dentro del mismo contenedor -->
        <div id="profile-menu" class="profile-menu">
            <button class="submit-btn" onclick="window.location.href='/logout'">Cerrar sesion</button>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 2): ?>
                <button onclick="window.location.href='/admin'">Admin</button>
            <?php endif; ?>
        </div>
    </div>
 </nav>