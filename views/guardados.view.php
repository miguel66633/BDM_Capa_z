<?php require base_path('views/partials/head.z.php'); ?>
<div class="container">
    <?php require base_path('views/partials/nav.z.php'); ?>

    <main id="contenido">
        <div class="scrollable-content">
            <div class="main-header">
                <div class="header-content">
                    <h2>Guardados</h2>
                </div>
            </div>
            <?php if (empty($publicaciones)): ?>
                <p style="text-align: center; color: #888; padding: 20px;">No tienes publicaciones guardadas.</p>
            <?php else: ?>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <div class="publicacion">
                        <div class="publicacion-header">
                            <img src="<?php echo isset($publicacion['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($publicacion['ImagenPerfil']) : '/Resources/images/perfilpre.jpg'; ?>" alt="Foto de perfil" class="publicacion-profile-pic">
                            <div class="publicacion-info">
                                <span class="publicacion-username"><?php echo htmlspecialchars($publicacion['NombreUsuario']); ?></span>
                                <span class="publicacion-user-handle">
                                    @<?php echo htmlspecialchars(strtolower(str_replace(' ', '', $publicacion['NombreUsuario']))); ?> • 
                                    <?php 
                                        try {
                                            $fechaPub = new DateTime($publicacion['FechaPublicacion']);
                                            echo $fechaPub->format('d M.'); 
                                        } catch (Exception $e) {
                                            echo 'Fecha inválida';
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="publicacion-contenido">
                            <p><?php echo htmlspecialchars($publicacion['ContenidoPublicacion']); ?></p>
                            <?php if (!empty($publicacion['TipoMultimedia'])): ?>
                                <?php
                                    $multimediaContent = $publicacion['TipoMultimedia'];
                                    $base64Encoded = base64_encode($multimediaContent);
                                    // Es importante tener la extensión fileinfo habilitada en php.ini para mime_content_type
                                    // y que el string 'data://...' sea válido.
                                    $finfo = finfo_open();
                                    $mimeType = finfo_buffer($finfo, $multimediaContent, FILEINFO_MIME_TYPE);
                                    finfo_close($finfo);
                                ?>
                                <div class="img"> <!-- Puedes renombrar esta clase si es más genérica como "media-container" -->
                                    <?php if (strpos($mimeType, 'video/') === 0): ?>
                                        <video controls style="max-width: 100%; border-radius: 10px; margin-top:10px;">
                                            <source src="data:<?php echo htmlspecialchars($mimeType); ?>;base64,<?php echo $base64Encoded; ?>" type="<?php echo htmlspecialchars($mimeType); ?>">
                                            Tu navegador no soporta videos HTML5.
                                        </video>
                                    <?php elseif (strpos($mimeType, 'image/') === 0): ?>
                                        <img src="data:<?php echo htmlspecialchars($mimeType); ?>;base64,<?php echo $base64Encoded; ?>" alt="Multimedia de la publicación" class="publicacion-imagen">
                                    <?php else: ?>
                                        <p>Formato multimedia no soportado (Detectado: <?php echo htmlspecialchars($mimeType); ?>).</p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="publicacion-acciones">
                            <div class="accion">
                                <button class="accion-btn like-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                    <img 
                                        src="/Resources/images/<?php echo $publicacion['YaDioLike'] ? 'likeP.svg' : 'like.svg'; ?>" 
                                        class="accion-icon"
                                        alt="Botón de like"
                                    >
                                </button>
                                <span class="accion-count" id="like-count-<?php echo $publicacion['PublicacionID']; ?>">
                                    <?php echo $publicacion['Likes'] ?? 0; ?>
                                </span>
                            </div>
                            <!-- ***** NUEVO: Sección Repost ***** -->
                            <div class="accion">
                                <button class="accion-btn repost-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                    <img src="/Resources/images/<?php echo $publicacion['YaReposteo'] ? 'repostP.svg' : 'repost.svg'; ?>" class="accion-icon" alt="Repost">
                                </button>
                                <span class="accion-count" id="repost-count-<?php echo $publicacion['PublicacionID']; ?>">
                                    <?php echo $publicacion['RepostsCount'] ?? 0; ?> 
                                </span> 
                            </div>
                            <!-- ***** NUEVO: Sección Comentarios ***** -->
                            <div class="accion">
                                <a href="/post/<?php echo $publicacion['PublicacionID']; ?>" class="accion-btn comentarios-btn">
                                    <img src="/Resources/images/comments.svg" class="accion-icon" alt="Comentarios">
                                </a>
                                <span class="accion-count">
                                    <?php echo $publicacion['CommentsCount'] ?? 0; ?>
                                </span> 
                            </div>
                            <!-- ***** Sección Guardado (siempre activo aquí) ***** -->
                            <div class="accion">
                                <button class="accion-btn saved-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                    <!-- En la vista de guardados, el icono siempre debe ser el activo -->
                                    <img src="/Resources/images/guardados.svg" class="accion-icon" alt="Botón de guardado"> 
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