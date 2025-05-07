
<?php require base_path('views/partials/head.z.php'); ?>
<link rel="stylesheet" href="css/messages.css">
<div class="container">
    <?php require base_path('views/partials/nav.z.php'); ?>

        <main id="contenido">
          <div class="scrollable-content">
              <div class="main-header">
                  <div class="header-content">
                      <h2>Mensajes</h2>
                  </div>
              </div>
              <div class="search-bar">
                  <img src="/Resources/images/buscar.svg" class="search-icon"> <!-- Corregir ruta -->
                  <input type="text" placeholder="Busca a alguien" class="search-input" id="search-input">
              </div>
              <div id="search-results" class="search-results"></div>

              <div id="chat-list-container"> 
                <?php if (!empty($chats)): ?>
                    <?php foreach ($chats as $chat): ?>
                        <div class="mensaje" 
                             data-chat-id="<?php echo $chat['ChatID']; ?>" 
                             data-imagen-perfil="<?php echo $chat['ImagenPerfil']; ?>" 
                             data-nombre-usuario="<?php echo htmlspecialchars($chat['NombreUsuario']); ?>"
                             data-destinatario-id="<?php echo $chat['PersonaID']; // Añadir ID del otro usuario ?>"> 
                            <img src="<?php echo $chat['ImagenPerfil']; ?>" class="mensaje-img" alt="<?php echo htmlspecialchars($chat['NombreUsuario']); ?>">
                            <div class="mensaje-info">
                                <div class="mensaje-header">
                                    <span class="mensaje-nombre"><?php echo htmlspecialchars($chat['NombreUsuario']); ?></span>
                                    <span class="mensaje-fecha">• <?php echo !empty($chat['HoraUltimoMensaje']) ? date('d M. H:i', strtotime($chat['HoraUltimoMensaje'])) : date('d M. H:i', strtotime($chat['FechaCreacion'])); ?></span>
                                </div>
                                <div class="mensaje-texto">
                                    <?php echo !empty($chat['UltimoMensaje']) ? htmlspecialchars($chat['UltimoMensaje']) : 'Comienza a conversar'; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p id="no-chats-message" style="text-align: center; color: #888; padding: 20px;">No tienes chats activos.</p>
                <?php endif; ?>
              </div> 
          </div>
        </main>

        <aside id="lateral">
          <div class="scrollable-contenta">
          <div class="chat-header">
              <img src="Resources/images/perfilPre.jpg" class="chat-header-img" alt="Imagen del usuario">
              <span class="chat-header-name">Selecciona un chat</span>
          </div>

            <div class="chat-messages" id="chat-messages">
          </div>
              
          </div>
            <div class="chat-input-bar">
              <input type="hidden" id="chat-id" value="">

              <button class="send-button" id="ubicacion-button">
                  <img src="/resources/images/localizacion.svg" alt="localizacion">
              </button>

              <input type="text" class="chat-input" id="chat-input" placeholder="Escribe tu mensaje...">
              <button class="send-button" id="send-button">
                  <img src="/resources/images/enviar.svg" alt="Enviar">
              </button>
            </div>
        </aside>
        <script defer src="../js/messages.js"></script>
    </div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>

</html>