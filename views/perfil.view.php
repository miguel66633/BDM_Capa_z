<?php require base_path('views/partials/head.z.php'); ?>
<div class="container">
  <?php require base_path('views/partials/nav.z.php'); ?>

  <?php
  $profileUserId = $usuario['UsuarioID'];
  $currentUserId = $_SESSION['user_id'] ?? null;
  ?>
  <input type="hidden" id="profileUserId" value="<?php echo $profileUserId; ?>">
  <input type="hidden" id="isOwner" value="<?php echo $isOwner ? 'true' : 'false'; ?>">


  <main id="contenido">
    <div class="scrollable-content">
      <div class="post-header">
        <button class="back-btn" onclick="window.history.back()">
          <img src="/resources/images/atras.svg" alt="Atrás"> 
        </button>
        <h2><?php echo htmlspecialchars($usuario['NombreUsuario']); ?></h2>
      </div>

      <div class="profile-banner">
        <!-- *** CAMBIO: Mostrar banner del usuario *** -->
        <img src="<?php echo isset($usuario['BannerPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($usuario['BannerPerfil']) : '/Resources/images/bannerPre.jpg'; ?>" alt="Banner de <?php echo htmlspecialchars($usuario['NombreUsuario']); ?>">
      </div>

      <div class="profile-info-perfil">
        <div class="profile-image">
          <img src="<?php echo isset($usuario['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($usuario['ImagenPerfil']) : '/Resources/images/perfilPre.jpg'; ?>" alt="Foto de Perfil de <?php echo htmlspecialchars($usuario['NombreUsuario']); ?>">
        </div>

        <div class="profile-details">
          <div class="name-and-edit">
            <div class="name-username">
              <h2><?php echo htmlspecialchars($usuario['NombreUsuario']); ?></h2>
            </div>
            <?php if ($isOwner): ?>
              <button class="edit-profile" id="openModalBtn">Editar perfil</button>
            <?php endif; ?>
          </div>
          <p class="bio">
            <?php echo !empty($usuario['Biografia']) ? htmlspecialchars($usuario['Biografia']) : 'Este usuario aún no tiene biografía.'; ?>
          </p>
        </div>
      </div>
      
      <div class="posts-section">
        <h3>Posts</h3>
      </div>

        <?php if (!empty($publicaciones)): ?>
            <?php foreach ($publicaciones as $publicacion): ?>
                <div class="publicacion" data-id="<?php echo $publicacion['PublicacionID']; ?>">
                    
                    <?php if ($publicacion['TipoEntrada'] === 'repost'): ?>
                        <div class="repost-indicator" style="font-size: 0.85em; color: #888; margin-bottom: 8px; padding-left: 50px;">
                            <img src="/Resources/images/repost.svg" style="width:14px; height:14px; vertical-align:middle; margin-right: 4px; filter: brightness(0.6);" alt="Repost icon">
                            <?php if ($publicacion['RepostadorID'] == $currentUserId && $isOwner): ?>
                                Reposteaste
                            <?php else: ?>
                                Reposteado por <a href="/perfil/<?php echo $publicacion['RepostadorID']; ?>" style="color: #888; text-decoration: none; font-weight: bold;"><?php echo htmlspecialchars($publicacion['RepostadorNombreUsuario']); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="publicacion-header">
                        <a href="/perfil/<?php echo $publicacion['AutorID']; ?>">
                            <img 
                                src="<?php echo isset($publicacion['AutorImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($publicacion['AutorImagenPerfil']) : '/Resources/images/perfilpre.jpg'; ?>" 
                                alt="Foto de perfil de <?php echo htmlspecialchars($publicacion['AutorNombreUsuario']); ?>"
                                class="publicacion-profile-pic"
                            >
                        </a>
                        <div class="publicacion-info">
                            <a href="/perfil/<?php echo $publicacion['AutorID']; ?>" class="publicacion-username-link" style="text-decoration: none; color: inherit;">
                                <span class="publicacion-username"><?php echo htmlspecialchars($publicacion['AutorNombreUsuario']); ?></span>
                            </a>
                            <span class="publicacion-user-handle">
                            @<?php echo htmlspecialchars(strtolower(str_replace(' ', '', $publicacion['AutorNombreUsuario']))); ?> •
                            <?php 
                                echo formatTiempoTranscurrido($publicacion['EffectiveDate']);
                            ?>
                        </span>
                        </div>
                    </div>

                    <div class="publicacion-contenido">
                        <p><?php echo nl2br(htmlspecialchars($publicacion['ContenidoPublicacion'])); ?></p>
                        <?php if (!empty($publicacion['TipoMultimedia'])): ?>
                            <div class="img">
                                <?php
                                    $multimediaContent = $publicacion['TipoMultimedia'];
                                    $base64Encoded = base64_encode($multimediaContent);
                                    $finfo = finfo_open();
                                    $mimeType = finfo_buffer($finfo, $multimediaContent, FILEINFO_MIME_TYPE);
                                    finfo_close($finfo);
                                ?>
                                <?php if (strpos($mimeType, 'video/') === 0): ?>
                                    <video controls class="publicacion-video" style="max-width: 100%; border-radius: 10px; margin-top:10px;">
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
                                <?php echo $publicacion['LikesCount'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="accion">
                             <button class="accion-btn repost-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                <img src="/Resources/images/<?php echo $publicacion['YaReposteo'] ? 'repostP.svg' : 'repost.svg'; ?>" class="accion-icon" alt="Repost">
                            </button>
                            <span class="accion-count" id="repost-count-<?php echo $publicacion['PublicacionID']; ?>">
                                <?php echo $publicacion['RepostsCount'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="accion">
                            <a href="/post/<?php echo $publicacion['PublicacionID']; ?>" class="accion-btn comentarios-btn">
                                <img src="/Resources/images/comments.svg" class="accion-icon" alt="Comentarios">
                            </a>
                            <span class="accion-count">
                                <?php echo $publicacion['CommentsCount'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="accion">
                            <button class="accion-btn saved-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                                <img
                                    src="/Resources/images/<?php echo $publicacion['YaGuardo'] ? 'guardados.svg' : 'saved.svg'; ?>"
                                    class="accion-icon"
                                    alt="Botón de guardado"
                                >
                            </button>
                            <span class="accion-count" id="save-count-<?php echo $publicacion['PublicacionID']; ?>">
                                <?php echo $publicacion['SavesCount'] ?? 0;
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #888; padding: 20px;">Este usuario aún no ha publicado nada ni ha hecho reposts.</p>
        <?php endif; ?>



      <?php if ($isOwner): ?>
      <div class="modalB" id="editProfileModal">
        <div class="modalB-content">
          <div class="modalB-header">
            <button class="close-modalB" id="closeModalBtn">x</button>
            <h2>Editar perfil</h2>
            <button class="save-modalB" id="saveModalBtn">Guardar</button>
          </div>

          <div class="profile-banner">
            <label for="bannerUpload">
              <img src="<?php echo isset($usuario['BannerPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($usuario['BannerPerfil']) : '/Resources/images/bannerPre.jpg'; ?>" alt="Banner">
            </label>
            <input type="file" id="bannerUpload" style="display: none;" accept="image/*" />
          </div>

          <div class="profile-info-perfil">
            <div class="profile-image">
              <label for="profileUpload">
                <img src="<?php echo isset($usuario['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($usuario['ImagenPerfil']) : '/Resources/images/perfilPre.jpg'; ?>" alt="Foto de Perfil">
              </label>
              <input type="file" id="profileUpload" style="display: none;" accept="image/*" />
            </div>

            <div class="modalB-body">
              <div class="edit-name">
                <label for="nameInput">Nombre</label>
                <input type="text" id="nameInput" value="<?php echo htmlspecialchars($usuario['NombreUsuario']); ?>">
              </div>
              <div class="edit-bio">
                <label for="bioInput">Biografía</label>
                <textarea id="bioInput"><?php echo htmlspecialchars($usuario['Biografia'] ?? ''); ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?> 

    </div> 
  </main>

  <?php if ($isOwner): ?>
    <script src="/js/perfil.js"></script>
  <?php endif; ?>
  <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>

</html>