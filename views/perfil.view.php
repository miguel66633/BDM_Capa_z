<?php require base_path('views/partials/head.z.php'); ?>
<div class="container">
  <?php require base_path('views/partials/nav.z.php'); ?>

  <?php
  $userId = $_SESSION['user_id'];
  ?>
  <!-- Aquí insertamos el userId desde PHP en un campo oculto -->
  <input type="hidden" id="userId" value="<?php echo $userId; ?>">

  <main id="contenido">
    <div class="scrollable-content">
      <!-- Encabezado con botón de regreso y nombre de usuario (NO mover) -->
      <div class="post-header">
        <button class="back-btn" onclick="window.location.href='/inicio'">
          <img src="resources/images/atras.svg" alt="Atrás">
        </button>
        <h2>Miguel Reyes</h2>
      </div>

      <!-- Banner -->
      <div class="profile-banner">
        <img src="Resources/images/bannerPre.jpg" alt="Banner">
      </div>

      <!-- Sección de información del perfil -->
      <div class="profile-info-perfil">
        <!-- Imagen de perfil -->
        <div class="profile-image">
          <img src="Resources/images/perfilPre.jpg" alt="Resources/images/perfilPre.jpg">
        </div>

        <!-- Nombre, usuario y botón de editar -->
        <div class="profile-details">
          <div class="name-and-edit">
            <div class="name-username">
              <h2></h2>
              <!-- <p>@migueiro</p> -->
            </div>
            <button class="edit-profile" id="openModalBtn">Editar perfil</button>
          </div>

          <!-- Biografía -->
          <p class="bio">
           
          </p>

          <!-- Seguidores y seguidos -->
          <!-- <div class="follows">
            <span><strong>561</strong> Siguiendo</span>
            <span><strong>519</strong> Seguidores</span>
          </div> -->
        </div>
      </div>

      <!-- Sección de Posts -->
      <div class="posts-section">
        <h3>Posts</h3>
      </div>
      
        <!-- ***** CAMBIO: Bucle para mostrar las publicaciones del usuario ***** -->
        <?php if (!empty($publicaciones)): ?>
            <?php foreach ($publicaciones as $publicacion): ?>
                <div class="publicacion">
                    <div class="publicacion-header">
                        <!-- Imagen de perfil del post (puede ser la misma del perfil) -->
                        <img 
                            src="<?php echo isset($publicacion['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($publicacion['ImagenPerfil']) : '/Resources/images/perfilPre.jpg'; ?>" 
                            alt="Foto de perfil de <?php echo htmlspecialchars($publicacion['NombreUsuario']); ?>" 
                            class="publicacion-profile-pic"
                        >
                        <div class="publicacion-info">
                            <span class="publicacion-username"><?php echo htmlspecialchars($publicacion['NombreUsuario']); ?></span>
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

                    <!-- Contenido de la publicación (texto e imagen opcional) -->
                    <div class="publicacion-contenido">
                        <p><?php echo htmlspecialchars($publicacion['ContenidoPublicacion']); ?></p>
                        
                        <?php if (!empty($publicacion['TipoMultimedia'])): ?>
                            <div class="img">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($publicacion['TipoMultimedia']); ?>" alt="Imagen de la publicación" class="publicacion-imagen">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Acciones de la publicación (DINÁMICAS) -->
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
                                <img src="/Resources/images/repost.svg" class="accion-icon" alt="Repost">
                            </button>
                            <span class="accion-count">
                                <?php // echo $publicacion['RepostsCount'] ?? 0; ?> 0
                            </span> 
                        </div>
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


      <!-- MODAL (oculto por defecto) -->
      <div class="modalB" id="editProfileModal">
        <div class="modalB-content">
          <!-- Cabecera del modal -->
          <div class="modalB-header">
            <!-- Botón para cerrar con la X -->
            <button class="close-modalB" id="closeModalBtn">x</button>
            <!-- Título de la cabecera -->
            <h2>Editar perfil</h2>
            <!-- Botón de guardar -->
            <button class="save-modalB" id="saveModalBtn">Guardar</button>
          </div>

          <div class="profile-banner">
            <label for="bannerUpload">
              <img src="Resources/images/bannerPre.jpg" alt="Banner">
            </label>
            <!-- Input de tipo file (oculto) -->
            <input type="file" id="bannerUpload" style="display: none;" accept="image/*" />
          </div>

          <!-- Sección de información del perfil -->
          <div class="profile-info-perfil">
            <!-- Imagen de perfil -->
            <div class="profile-image">
              <label for="profileUpload">
                <img src="Resources/images/perfilPre.jpg" alt="Foto de Perfil">
              </label>
              <!-- Input de tipo file (oculto) -->
              <input type="file" id="profileUpload" style="display: none;" accept="image/*" />
            </div>


            <!-- Nombre, usuario y botón de editar -->
            <div class="modalB-body">
              <!-- Recuadro para editar el nombre -->
              <div class="edit-name">
                <label for="nameInput">Nombre</label>
                <!-- Ajusta el value con el nombre actual -->
                <input type="text" id="nameInput" value="Miguel Reyes">
              </div>

              <!-- Recuadro para editar la biografía -->
              <div class="edit-bio">
                <label for="bioInput">Biografía</label>
                <!-- Ajusta el contenido del textarea con la biografía actual -->
                <textarea id="bioInput">Aquí puedes colocar la biografía de la persona, una breve descripción o presentación.</textarea>
              </div>
            </div>
          </div>


        </div>
      </div>
  </main>
  <script src="js/perfil.js"></script>
  <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>

</html>