<?php require base_path('views/partials/head.z.php'); ?>
<div class="container">
    <?php require base_path('views/partials/nav.z.php'); ?>

    <main id="contenido">
        <div class="scrollable-content">
            <h2>Publicaciones Guardadas</h2>
            <?php if (empty($publicaciones)): ?>
                <p>No tienes publicaciones guardadas.</p>
            <?php else: ?>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <div class="publicacion">
                        <div class="publicacion-header">
                            <img src="<?php echo isset($publicacion['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($publicacion['ImagenPerfil']) : 'Resources/images/perfilpre.jpg'; ?>" alt="Foto de perfil" class="publicacion-profile-pic">
                            <div class="publicacion-info">
                                <span class="publicacion-username"><?php echo htmlspecialchars($publicacion['NombreUsuario']); ?></span>
                                <span class="publicacion-user-handle">
                                    @<?php echo htmlspecialchars($publicacion['NombreUsuario']); ?> • 
                                    <?php 
                                        $fechaHora = new DateTime($publicacion['FechaPublicacion']);
                                        echo $fechaHora->format('d/m/Y H:i');
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="publicacion-contenido">
                            <p><?php echo htmlspecialchars($publicacion['ContenidoPublicacion']); ?></p>
                            <?php if (!empty($publicacion['TipoMultimedia'])): ?>
                                <div class="img">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($publicacion['TipoMultimedia']); ?>" alt="Imagen de la publicación" class="publicacion-imagen">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="publicacion-acciones">
                            <div class="accion">
                                <button class="accion-btn like-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                    <img src="Resources/images/like.svg" class="accion-icon">
                                </button>
                                <span class="accion-count" id="like-count-<?php echo $publicacion['PublicacionID']; ?>">
                                    <?php echo $publicacion['Likes'] ?? 0; ?>
                                </span>
                            </div>
                            <div class="accion">
                                <button class="accion-btn saved-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                    <img src="Resources/images/guardados.svg" class="accion-icon">
                                </button>
                                <span class="accion-count" id="save-count-<?php echo $publicacion['PublicacionID']; ?>">
                                    <?php echo $publicacion['Guardados'] ?? 0; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>
</html>