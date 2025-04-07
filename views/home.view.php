<?php require base_path('views/partials/head.z.php'); ?>
<div class="container">
    <?php require base_path('views/partials/nav.z.php'); ?>

    <main id="contenido">

            <div class="scrollable-content">
                <div class="main-header">
                  <div class="header-content">
                    <h2>Inicio</h2>
                </div>
              </div>

                <div class="publicacion">
                    <!-- Encabezado de la publicación: foto y nombre de usuario -->
                    <div class="publicacion-header">
                    <img src="Resources/images/perfil.jpg" alt="Foto de perfil" class="publicacion-profile-pic">
                    <div class="publicacion-info">
                        <span class="publicacion-username">Miguel Reyes</span>
                        <span class="publicacion-user-handle">@migueriro • 12 feb.</span>
                    </div>
                </div>
                <!-- Contenido de la publicación (texto e imagen opcional) -->
                <div class="publicacion-contenido">
                    <p>Yippie yippieee</p>

                    <div class="img">
                      <img src="Resources/images/ejemplo1.png" alt="Imagen de la publicación" class="publicacion-imagen">
                    </div>

                    <!-- <img src="../images/ejemplo1.png" alt="Imagen de la publicación" class="publicacion-imagen"> -->
                </div>
                
                <div class="publicacion-acciones">
                    <div class="accion">
                      <button class="accion-btn like-btn">
                        <img src="Resources/images/like.svg" class="accion-icon">
                      </button>
                      <span class="accion-count">123</span>
                    </div>
                    <div class="accion">
                        <button class="accion-btn repost-btn">
                          <img src="Resources/images/repost.svg" class="accion-icon" alt="Repost">
                        </button>
                        <span class="accion-count">45</span>
                      </div>
                      <div class="accion">
                        <button class="accion-btn" onclick="window.location.href='/post'">
                            <img src="Resources/images/comments.svg" class="accion-icon">
                        </button>
                        <span class="accion-count">67</span>
                    </div>
                    <div class="accion">
                      <button class="accion-btn saved-btn">
                        <img src="Resources/images/saved.svg" class="accion-icon">
                      </button>
                      <span class="accion-count">89</span>
                    </div>
                  </div>
                </div>
        </main>

    <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>
</html>