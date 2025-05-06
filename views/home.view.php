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
                <div class="publicacion" data-id="<?php echo $publicacion['PublicacionID']; ?>">
                    <!-- Encabezado de la publicación -->
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

                    <!-- Contenido de la publicación -->
                    <div class="publicacion-contenido">
                        <p><?php echo htmlspecialchars($publicacion['ContenidoPublicacion']); ?></p>


                        <!-- Mostrar imagen si existe -->
                        <?php if (!empty($publicacion['TipoMultimedia'])): ?>
                        <?php
                            // Decodificar una pequeña porción para determinar el tipo MIME
                            // Esto no es lo más eficiente. Idealmente, el tipo MIME se guardaría en la BD.
                            $multimediaContent = $publicacion['TipoMultimedia'];
                            $base64Encoded = base64_encode($multimediaContent);
                            $mimeType = mime_content_type('data://application/octet-stream;base64,' . $base64Encoded);
                        ?>
                        <div class="img"> <!-- Puedes renombrar esta clase si es más genérica como "media-container" -->
                            <?php if (strpos($mimeType, 'video/') === 0): ?>
                                <video controls style="max-width: 100%; border-radius: 10px; margin-top:10px;">
                                    <source src="data:<?php echo htmlspecialchars($mimeType); ?>;base64,<?php echo $base64Encoded; ?>" type="<?php echo htmlspecialchars($mimeType); ?>">
                                    Tu navegador no soporta videos HTML5.
                                </video>
                            <?php elseif (strpos($mimeType, 'image/') === 0): ?>
                                <img src="data:<?php echo htmlspecialchars($mimeType); ?>;base64,<?php echo $base64Encoded; ?>" alt="Imagen de la publicación" class="publicacion-imagen">
                            <?php else: ?>
                                <p>Formato multimedia no soportado.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                    <!-- Acciones de la publicación -->
                    <div class="publicacion-acciones">
                    <div class="accion">
                            <button class="accion-btn like-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                <img 
                                    src="Resources/images/<?php echo $publicacion['YaDioLike'] ? 'likeP.svg' : 'like.svg'; ?>" 
                                    class="accion-icon"
                                    alt="Botón de like"
                                >
                            </button>
                            <span class="accion-count" id="like-count-<?php echo $publicacion['PublicacionID']; ?>">
                                <?php echo $publicacion['Likes'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="accion">
                            <button class="accion-btn repost-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                <img src="Resources/images/<?php echo $publicacion['YaReposteo'] ? 'repostP.svg' : 'repost.svg'; ?>" class="accion-icon" alt="Repost">
                            </button>
                            <span class="accion-count" id="repost-count-<?php echo $publicacion['PublicacionID']; ?>">
                                <?php echo $publicacion['RepostsCount'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="accion">
                            <a href="/post/<?php echo $publicacion['PublicacionID']; ?>" class="accion-btn comentarios-btn">
                                <img src="Resources/images/comments.svg" class="accion-icon" alt="Comentarios">
                            </a>
                            <span class="accion-count">
                                <?php echo $publicacion['CommentsCount'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="accion">
                            <button class="accion-btn saved-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                <img 
                                    src="Resources/images/<?php echo $publicacion['YaGuardado'] ? 'guardados.svg' : 'saved.svg'; ?>" 
                                    class="accion-icon"
                                    alt="Botón de guardado"
                                >
                            </button>
                            <span class="accion-count" id="save-count-<?php echo $publicacion['PublicacionID']; ?>">
                                <?php echo $publicacion['Guardados'] ?? 0; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>
<!-- <script src="js/home.js" defer></script> -->
</body>
</html>