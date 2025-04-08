<div class="modal" id="postModal">
    <div class="modal-content">
      <div class="modal-header">
        <!-- <h2>¿Qué está pasando?</h2> -->
        <span class="close" onclick="closeModal()">&times;</span>
      </div>
      <div class="modal-body">
        <img src="Resources/images/perfil.jpg" alt="Foto de perfil" class="modal-profile-pic">
        <textarea class="post-textarea" placeholder="¿Qué quieres compartir?"></textarea>
        
        <div class="modal-footer">
          <!-- Etiqueta para cargar imagen -->
          <label for="postImage" class="image-upload-label">
            <img src="Resources/images/img.svg" alt="" />
          </label>
          <!-- Input file oculto -->
          <input type="file" id="postImage" accept="image/*,video/*" style="display: none;"/>
        
          <button class="submit-btn" onclick="submitPost()">Postear</button>
        </div>
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