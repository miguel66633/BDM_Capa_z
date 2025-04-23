<?php ?>
<div class="modal" id="postModal">
    <div class="modal-content">
      <div class="modal-header">
        <span class="close" onclick="closeModal()">&times;</span>
      </div>
      <div class="modal-body">

        <div class="profile-container">
            <img src="<?php echo isset($_SESSION['user_img']) && !empty($_SESSION['user_img']) ? 'data:image/jpeg;base64,' . $_SESSION['user_img'] : 'Resources/images/perfilpre.jpg'; ?>" alt="Foto de perfil" class="profile-pic">
            <div class="profile-info">
                <p class="username"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            </div>
        </div>

        <!-- Formulario para cargar texto e imagen -->
        <form action="/crear-publicacion" method="POST" enctype="multipart/form-data">

            <textarea name="contenido" class="post-textarea" placeholder="¿Qué quieres compartir?"></textarea>
            
            <div class="modal-footer">
                <!-- Etiqueta para cargar imagen -->
                <label for="postImage" class="image-upload-label">
                    <img src="Resources/images/img.svg" alt="Cargar imagen" />
                </label>
                <!-- Input file oculto -->
                <input type="file" id="postImage" name="imagen" accept="image/*,video/*" style="display: none;" />
                
                <!-- Botón para enviar el formulario -->
                <button type="submit" class="submit-btn">Postear</button>
            </div>
        </form>
      </div>
    </div>
  </div>

    <!-- Ventana emergente: Confirmar carga de post -->
  <div id="popup" class="popup-container">
    <div class="popup">
        <span class="close-ConfirmPost" onclick="cerrarConfirmPopup()">&times;</span>
        <h2>Tu post se ha subido con exito</h2>
    </div>
  </div>