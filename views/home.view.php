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

            <!-- Mostrar publicaciones -->
            <?php foreach ($publicaciones as $publicacion): ?>
                <div class="publicacion">
                    <!-- Encabezado de la publicación -->
                    <div class="publicacion-header">
                        <img src="<?php echo isset($publicacion['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($publicacion['ImagenPerfil']) : 'Resources/images/perfilpre.jpg'; ?>" alt="Foto de perfil" class="publicacion-profile-pic">
                        <div class="publicacion-info">
                            <span class="publicacion-username"><?php echo htmlspecialchars($publicacion['NombreUsuario']); ?></span>
                            <span class="publicacion-user-handle">@<?php echo htmlspecialchars($publicacion['NombreUsuario']); ?> • <?php echo htmlspecialchars($publicacion['FechaPublicacion']); ?></span>
                        </div>
                    </div>

                    <!-- Contenido de la publicación -->
                    <div class="publicacion-contenido">
                        <p><?php echo htmlspecialchars($publicacion['ContenidoPublicacion']); ?></p>

                        <!-- Mostrar imagen si existe -->
                        <?php if (!empty($publicacion['TipoMultimedia'])): ?>
                            <div class="img">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($publicacion['TipoMultimedia']); ?>" alt="Imagen de la publicación" class="publicacion-imagen">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Acciones de la publicación -->
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
            <?php endforeach; ?>
        </div>
    </main>

    <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>
</html>