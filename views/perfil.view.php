<?php require base_path('views/partials/head.z.php'); ?>
<div class="container">
    <?php require base_path('views/partials/nav.z.php'); ?>

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
                    <h2>Miguel Reyes</h2>
                    <!-- <p>@migueiro</p> -->
                  </div>
                  <button class="edit-profile" id="openModalBtn">Editar perfil</button>
                </div>
        
                <!-- Biografía -->
                <p class="bio">
                  Aquí puedes colocar la biografía de la persona, una breve descripción o presentación.
                </p>
        
                <!-- Seguidores y seguidos -->
                <div class="follows">
                  <span><strong>561</strong> Siguiendo</span>
                  <span><strong>519</strong> Seguidores</span>
                </div>
              </div>
            </div>
        
            <!-- Sección de Posts -->
            <div class="posts-section">
              <h3>Posts</h3>
              <!-- Aquí irían las publicaciones del usuario -->
            </div>
            <div class="publicacion">
              <!-- Encabezado de la publicación: foto y nombre de usuario -->
              <div class="publicacion-header">
              <img src="../images/perfil.jpg" alt="Foto de perfil" class="publicacion-profile-pic">
              <div class="publicacion-info">
                  <span class="publicacion-username">Miguel Reyes</span>
                  <span class="publicacion-user-handle">@migueriro • 12 feb.</span>
              </div>
          </div>
          <!-- Contenido de la publicación (texto e imagen opcional) -->
          <div class="publicacion-contenido">
              <p>Yippie yippieee</p>

              <div class="img">
                <img src="../images/ejemplo1.png"/>
              </div>

              <!-- <img src="../images/ejemplo1.png" alt="Imagen de la publicación" class="publicacion-imagen"> -->
          </div>
          
          <div class="publicacion-acciones">
              <div class="accion">
                <button class="accion-btn like-btn">
                  <img src="../images/like.svg" class="accion-icon">
                </button>
                <span class="accion-count">123</span>
              </div>
              <div class="accion">
                  <button class="accion-btn repost-btn">
                    <img src="../images/repost.svg" class="accion-icon" alt="Repost">
                  </button>
                  <span class="accion-count">45</span>
                </div>
                <div class="accion">
                  <button class="accion-btn" onclick="window.location.href='../html/post.html'">
                      <img src="../images/comments.svg" class="accion-icon">
                  </button>
                  <span class="accion-count">67</span>
              </div>
              <div class="accion">
                <button class="accion-btn saved-btn">
                  <img src="../images/saved.svg" class="accion-icon">
                </button>
                <span class="accion-count">89</span>
              </div>
            </div>
          </div>


              <!-- Publicación 2 -->
              <div class="publicacion">
                <div class="publicacion-header">
                    <img src="../images/perfil2.png" alt="Foto de perfil" class="publicacion-profile-pic">
                    <div class="publicacion-info">
                        <span class="publicacion-username">El furro</span>
                        <span class="publicacion-user-handle">@YakaraVt  • 10 feb.</span></span>
                    </div>
                </div>
                <div class="publicacion-contenido">
                    <p>Miren a mi nuevo hijo</p>
                    
                    <div class="carousel-container">
                        <div class="carousel-slide">
                          <!-- Cada slide es un contenedor individual para la imagen -->
                          <div class="slide">
                            <img src="../images/ejemplo2.jpg" alt="Imagen 1" />
                          </div>
                          <div class="slide">
                            <img src="../images/ejemplo1.jpg" alt="Imagen 2" />
                          </div>
                          <div class="slide">
                            <img src="../images/ejemplolargo.jpeg" alt="Imagen 3" />
                          </div>
                        </div>
                        
                        <!-- Flechas de navegación -->
                        <button class="carousel-arrow left">&lt;</button>
                        <button class="carousel-arrow right">&gt;</button>
                      </div>
                <div class="publicacion-acciones">
                    <div class="accion">
                      <button class="accion-btn like-btn">
                        <img src="../images/like.svg" class="accion-icon">
                      </button>
                      <span class="accion-count">52</span>
                    </div>
                    <div class="accion">
                        <button class="accion-btn repost-btn">
                          <img src="../images/repost.svg" class="accion-icon" alt="Repost">
                        </button>
                        <span class="accion-count">1</span>
                      </div>
                    <div class="accion">
                      <button class="accion-btn">
                        <img src="../images/comments.svg" class="accion-icon">
                      </button>
                      <span class="accion-count">6</span>
                    </div>
                    <div class="accion">
                      <button class="accion-btn saved-btn">
                        <img src="../images/saved.svg" class="accion-icon">
                      </button>
                      <span class="accion-count">3</span>
                    </div>
                  </div>
                </div>


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
                      <img src="Resources/images/perfil.jpg" alt="Foto de Perfil">
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
        </main>

    <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>
</html>