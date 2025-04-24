
<?php require base_path('views/partials/head.z.php'); ?>
<link rel="stylesheet" href="css/messages.css">
<div class="container">
    <?php require base_path('views/partials/nav.z.php'); ?>

        <!-- Sección central donde se carga el contenido -->
        <main id="contenido">
          <div class="scrollable-content">
              <div class="main-header">
                  <div class="header-content">
                      <h2>Mensajes</h2>
                  </div>
              </div>

              <div class="search-bar">
                  <img src="resources/images/buscar.svg" class="search-icon">
                  <input type="text" placeholder="Busca a alguien" class="search-input" id="search-input">
              </div>

              <div id="search-results" class="search-results"></div>

              <?php if (!empty($chats)): ?>
                  <?php foreach ($chats as $chat): ?>
                    <div class="mensaje" data-chat-id="<?php echo $chat['ChatID']; ?>">
                        <img src="<?php echo $chat['ImagenPerfil']; ?>" class="mensaje-img" alt="<?php echo htmlspecialchars($chat['NombreUsuario']); ?>">
                        <div class="mensaje-info">
                            <div class="mensaje-header">
                                <span class="mensaje-nombre"><?php echo htmlspecialchars($chat['NombreUsuario']); ?></span>
                                <span class="mensaje-handle">@<?php echo htmlspecialchars(strtolower($chat['NombreUsuario'])); ?></span>
                                <span class="mensaje-fecha">• <?php echo date('d M. Y', strtotime($chat['FechaCreacion'])); ?></span>
                            </div>
                            <div class="mensaje-texto">Nuevo chat iniciado</div>
                        </div>
                    </div>
                  <?php endforeach; ?>
              <?php else: ?>
                  <p>No tienes chats activos.</p>
              <?php endif; ?>
          </div>
        </main>



        <!-- Sección derecha (puede cambiar con cada sección) -->
        <aside id="lateral">
          <div class="scrollable-contenta">
          <div class="chat-header">
              <img src="Resources/images/perfilPre.jpg" class="chat-header-img" alt="Imagen del usuario">
              <span class="chat-header-name">Selecciona un chat</span>
          </div>

            <div class="chat-messages" id="chat-messages">
              <!-- Los mensajes se cargarán dinámicamente aquí -->
          </div>
              
          </div>
            <!-- Barra de escritura (parte inferior) -->
            <div class="chat-input-bar">
              <input type="hidden" id="chat-id" value="">
              <!-- Botón para subir imagen -->
              <!-- <label for="imageUpload" class="upload-button">
                <img src="resources/images/img.svg" alt="Cargar imagen">
              </label>
              <input id="imageUpload" type="file" accept="image/*" style="display: none;"> -->

              <!-- Input de texto -->
              <input type="text" class="chat-input" id="chat-input" placeholder="Escribe tu mensaje...">
              <!-- Botón de enviar -->
              <button class="send-button" id="send-button">
                  <img src="resources/images/enviar.svg" alt="Enviar">
              </button>
            </div>
        </aside>
        <script defer src="../js/messages.js"></script>
    </div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>

</html>