
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

                <div class="mensaje">
                    <img src="../images/perfil.jpg" class="mensaje-img">
                    <div class="mensaje-info">
                        <div class="mensaje-header">
                            <span class="mensaje-nombre">NachoBIT</span>
                            <span class="mensaje-handle">@TheNachoBIT</span>
                            <span class="mensaje-fecha">• 16 dic. 2021</span>
                        </div>
                        <div class="mensaje-texto">Epico xd</div>
                    </div>
                </div>

                <div class="mensaje">
                    <img src="../images/perfil.jpg" class="mensaje-img">
                    <div class="mensaje-info">
                        <div class="mensaje-header">
                            <span class="mensaje-nombre">NachoBIT</span>
                            <span class="mensaje-handle">@TheNachoBIT</span>
                            <span class="mensaje-fecha">• 16 dic. 2021</span>
                        </div>
                        <div class="mensaje-texto">Epico xd</div>
                    </div>
                </div>

        </div>
        </main>

        <!-- Sección derecha (puede cambiar con cada sección) -->
        <aside id="lateral">
          <div class="scrollable-contenta">
            <div class="chat-header">
              <img src="../images/perfil2.png" class="chat-header-img">
              <span class="chat-header-name">YakaravVT</span>
            </div>

              <div class="message other-message">
                <p>Hola, ¿cómo estás?</p>
              </div>
              <!-- Mensaje propio (alineado a la derecha) -->
              <div class="message my-message">
                <p>Todo bien, ¿y tú?</p>
              </div>
              <!-- Más mensajes de ejemplo -->
              <div class="message other-message">
                <p>Bien, gracias. ¿Qué haces?</p>
              </div>
              <div class="message my-message">
                <p>Nada, probando este chat :)</p>
              </div>

              <div class="message other-message">
                <p>Hola, ¿cómo estás?</p>
              </div>
              <!-- Mensaje propio (alineado a la derecha) -->
              <div class="message my-message">
                <p>Todo bien, ¿y tú?</p>
              </div>
              <!-- Más mensajes de ejemplo -->
              <div class="message other-message">
                <p>Bien, gracias. ¿Qué haces?</p>
              </div>
              <div class="message my-message">
                <p>Nada, probando este chat :)</p>
              </div>
              
              <div class="message other-message">
                <p>Hola, ¿cómo estás?</p>
              </div>
              <!-- Mensaje propio (alineado a la derecha) -->
              <div class="message my-message">
                <p>Todo bien, ¿y tú?</p>
              </div>
              <!-- Más mensajes de ejemplo -->
              <div class="message other-message">
                <p>Bien, gracias. ¿Qué haces?</p>
              </div>
              <div class="message my-message">
                <p>Nada, probando este chat :)</p>
              </div>

            
          </div>
            <!-- Barra de escritura (parte inferior) -->
            <div class="chat-input-bar">
              <!-- Botón para subir imagen -->
              <!-- <label for="imageUpload" class="upload-button">
                <img src="resources/images/img.svg" alt="Cargar imagen">
              </label>
              <input id="imageUpload" type="file" accept="image/*" style="display: none;"> -->

              <!-- Input de texto -->
              <input type="text" class="chat-input" placeholder="Escribe tu mensaje...">

              <!-- Botón de enviar -->
              <button class="send-button">
                <img src="resources/images/enviar.svg" alt="Enviar">
              </button>
            </div>
        </aside>
        <script defer src="../js/messages.js"></script>
    </div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>

</html>