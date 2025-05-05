<?php require base_path('views/partials/head.z.php'); ?>
<div class="container">
  <?php require base_path('views/partials/nav.z.php'); ?>

  <?php
  // ID del usuario del perfil que se está viendo (viene del controlador)
  $profileUserId = $usuario['UsuarioID'];
  // ID del usuario logueado (de la sesión)
  $currentUserId = $_SESSION['user_id'] ?? null;
  ?>
  <!-- Campo oculto con el ID del perfil visualizado (útil para JS si es necesario) -->
  <input type="hidden" id="profileUserId" value="<?php echo $profileUserId; ?>">
  <!-- Campo oculto para saber si es el dueño (útil para JS si es necesario) -->
  <input type="hidden" id="isOwner" value="<?php echo $isOwner ? 'true' : 'false'; ?>">


  <main id="contenido">
    <div class="scrollable-content">
      <!-- Encabezado con botón de regreso y nombre de usuario -->
      <div class="post-header">
        <button class="back-btn" onclick="window.history.back()"> <!-- Usar history.back() es más flexible -->
          <img src="/resources/images/atras.svg" alt="Atrás"> <!-- Ruta desde la raíz -->
        </button>
        <!-- *** CAMBIO: Mostrar nombre del usuario del perfil *** -->
        <h2><?php echo htmlspecialchars($usuario['NombreUsuario']); ?></h2>
      </div>

      <!-- Banner -->
      <div class="profile-banner">
        <!-- *** CAMBIO: Mostrar banner del usuario *** -->
        <img src="<?php echo isset($usuario['BannerPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($usuario['BannerPerfil']) : '/Resources/images/bannerPre.jpg'; ?>" alt="Banner de <?php echo htmlspecialchars($usuario['NombreUsuario']); ?>">
      </div>

      <!-- Sección de información del perfil -->
      <div class="profile-info-perfil">
        <!-- Imagen de perfil -->
        <div class="profile-image">
          <!-- *** CAMBIO: Mostrar imagen de perfil del usuario *** -->
          <img src="<?php echo isset($usuario['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($usuario['ImagenPerfil']) : '/Resources/images/perfilPre.jpg'; ?>" alt="Foto de Perfil de <?php echo htmlspecialchars($usuario['NombreUsuario']); ?>">
        </div>

        <!-- Nombre, usuario y botón de editar -->
        <div class="profile-details">
          <div class="name-and-edit">
            <div class="name-username">
              <!-- *** CAMBIO: Mostrar nombre del usuario *** -->
              <h2><?php echo htmlspecialchars($usuario['NombreUsuario']); ?></h2>
              <!-- Podrías añadir el @handle si lo tienes -->
            </div>
            <!-- *** CAMBIO: Mostrar botón solo si es el dueño *** -->
            <?php if ($isOwner): ?>
              <button class="edit-profile" id="openModalBtn">Editar perfil</button>
            <?php endif; ?>
          </div>

          <!-- Biografía -->
          <p class="bio">
            <!-- *** CAMBIO: Mostrar biografía del usuario *** -->
            <?php echo !empty($usuario['Biografia']) ? htmlspecialchars($usuario['Biografia']) : 'Este usuario aún no tiene biografía.'; ?>
          </p>

          <!-- Seguidores y seguidos (Añadir lógica si la implementas) -->
          <!-- <div class="follows"> ... </div> -->
        </div>
      </div>

      <!-- Sección de Posts -->
      <div class="posts-section">
        <h3>Posts</h3>
      </div>

        <!-- Bucle para mostrar las publicaciones del usuario -->
        <?php if (!empty($publicaciones)): ?>
            <?php foreach ($publicaciones as $publicacion): ?>
                <div class="publicacion" data-id="<?php echo $publicacion['PublicacionID']; ?>"> <!-- Añadir data-id -->
                    <div class="publicacion-header">
                        <img
                            src="<?php echo isset($publicacion['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($publicacion['ImagenPerfil']) : '/Resources/images/perfilPre.jpg'; ?>"
                            alt="Foto de perfil de <?php echo htmlspecialchars($publicacion['NombreUsuario']); ?>"
                            class="publicacion-profile-pic"
                        >
                        <div class="publicacion-info">
                            <!-- *** CAMBIO: Enlazar nombre de usuario al perfil *** -->
                            <a href="/perfil/<?php echo $publicacion['UsuarioID']; ?>" class="publicacion-username-link" style="text-decoration: none; color: inherit;">
                                <span class="publicacion-username"><?php echo htmlspecialchars($publicacion['NombreUsuario']); ?></span>
                            </a>
                            <span class="publicacion-user-handle">
                                @<?php echo htmlspecialchars(strtolower(str_replace(' ', '', $publicacion['NombreUsuario']))); ?> •
                                <?php
                                    try {
                                        $fecha = new DateTime($publicacion['FechaPublicacion']);
                                        echo $fecha->format('d M.');
                                    } catch (Exception $e) {
                                        echo 'Fecha inválida';
                                    }
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- Contenido de la publicación -->
                    <div class="publicacion-contenido">
                        <p><?php echo htmlspecialchars($publicacion['ContenidoPublicacion']); ?></p>

                        <?php if (!empty($publicacion['TipoMultimedia'])): ?>
                            <div class="img">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($publicacion['TipoMultimedia']); ?>" alt="Imagen de la publicación" class="publicacion-imagen">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Acciones de la publicación (Like, Comentario, Guardado) -->
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
                        <!-- Repost (si lo implementas) -->
                        <!-- <div class="accion"> ... </div> -->
                        <div class="accion">
                            <!-- Enlace al post individual -->
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
                                <?php echo $publicacion['SavesCount'] ?? 0; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #888; padding: 20px;">Este usuario aún no ha publicado nada.</p>
        <?php endif; ?>


      <!-- *** CAMBIO: Renderizar MODAL solo si es el dueño *** -->
      <?php if ($isOwner): ?>
      <!-- MODAL (oculto por defecto) -->
      <div class="modalB" id="editProfileModal">
        <div class="modalB-content">
          <!-- Cabecera del modal -->
          <div class="modalB-header">
            <button class="close-modalB" id="closeModalBtn">x</button>
            <h2>Editar perfil</h2>
            <button class="save-modalB" id="saveModalBtn">Guardar</button>
          </div>

          <!-- Banner editable -->
          <div class="profile-banner">
            <label for="bannerUpload">
              <!-- Mostrar banner actual -->
              <img src="<?php echo isset($usuario['BannerPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($usuario['BannerPerfil']) : '/Resources/images/bannerPre.jpg'; ?>" alt="Banner">
            </label>
            <input type="file" id="bannerUpload" style="display: none;" accept="image/*" />
          </div>

          <!-- Info editable -->
          <div class="profile-info-perfil">
            <!-- Imagen de perfil editable -->
            <div class="profile-image">
              <label for="profileUpload">
                <!-- Mostrar imagen actual -->
                <img src="<?php echo isset($usuario['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($usuario['ImagenPerfil']) : '/Resources/images/perfilPre.jpg'; ?>" alt="Foto de Perfil">
              </label>
              <input type="file" id="profileUpload" style="display: none;" accept="image/*" />
            </div>

            <!-- Campos editables -->
            <div class="modalB-body">
              <div class="edit-name">
                <label for="nameInput">Nombre</label>
                <!-- Mostrar nombre actual -->
                <input type="text" id="nameInput" value="<?php echo htmlspecialchars($usuario['NombreUsuario']); ?>">
              </div>
              <div class="edit-bio">
                <label for="bioInput">Biografía</label>
                <!-- Mostrar bio actual -->
                <textarea id="bioInput"><?php echo htmlspecialchars($usuario['Biografia'] ?? ''); ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?> <!-- Fin del if ($isOwner) para el modal -->

    </div> <!-- Fin scrollable-content -->
  </main>
  <!-- *** CAMBIO: Incluir JS solo si es el dueño (ya que maneja el modal de edición) *** -->
  <?php if ($isOwner): ?>
    <script src="/js/perfil.js"></script> <!-- Asegúrate que la ruta sea correcta -->
  <?php endif; ?>
  <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>

</html>