document.addEventListener("DOMContentLoaded", function () {
  // --- INICIO DEL CÓDIGO DEL PRIMER BLOQUE DOMContentLoaded ---
  const userIdElement = document.getElementById("userId"); // Es buena práctica verificar si existe
  if (userIdElement) {
      const userId = userIdElement.value;

      fetch(`/api?UsuarioID=${userId}`) // Asegúrate que esta ruta /api esté configurada para devolver los datos del perfil
          .then((response) => response.json())
          .then((data) => {
              console.log("Datos del usuario:", data);

              // Rellenamos los datos en el HTML
              const nombreUsuarioH2 = document.querySelector(".profile-details .name-and-edit .name-username h2");
              if (nombreUsuarioH2) {
                  nombreUsuarioH2.textContent = data.NombreUsuario;
              }

              const bioP = document.querySelector(".profile-info-perfil .profile-details .bio");
              if (bioP) {
                  bioP.textContent = data.Biografia || "Aquí puedes colocar una breve descripción o presentación.";
              }
              
              const perfilImage = document.querySelector(".profile-info-perfil .profile-image img");
              if (perfilImage && data.ImagenPerfil) {
                  perfilImage.src = `data:image/jpeg;base64,${data.ImagenPerfil}`;
              }

              const bannerImage = document.querySelector(".profile-banner img");
              if (bannerImage && data.BannerPerfil) {
                  bannerImage.src = `data:image/jpeg;base64,${data.BannerPerfil}`;
              }

              // Rellenar el modal de edición
              const nameInput = document.getElementById("nameInput");
              if (nameInput) {
                  nameInput.value = data.NombreUsuario;
              }

              const bioInput = document.getElementById("bioInput");
              if (bioInput) {
                  bioInput.value = data.Biografia || "Aquí puedes colocar una breve descripción o presentación.";
              }
              
              const modalPerfilImage = document.querySelector(".modalB .profile-image img");
              if (modalPerfilImage && data.ImagenPerfil) {
                  modalPerfilImage.src = `data:image/jpeg;base64,${data.ImagenPerfil}`;
              }

              const modalBannerImage = document.querySelector(".modalB .profile-banner img");
              if (modalBannerImage && data.BannerPerfil) {
                  modalBannerImage.src = `data:image/jpeg;base64,${data.BannerPerfil}`;
              }
          })
          .catch((error) => {
              console.error("Error al obtener el perfil:", error);
          });
  }
  // --- FIN DEL CÓDIGO DEL PRIMER BLOQUE DOMContentLoaded ---

  const saveModalButton = document.getElementById("saveModalBtn");
  if (saveModalButton) {
      saveModalButton.addEventListener("click", function (event) {
          event.preventDefault(); 

          const nombreUsuario = document.getElementById("nameInput").value;
          const biografia = document.getElementById("bioInput").value;
          const imagenPerfilFile = document.getElementById("profileUpload").files[0];
          const bannerPerfilFile = document.getElementById("bannerUpload").files[0];

          const formData = new FormData();
          formData.append("action", "modificar"); 
          formData.append("nombre_usuario", nombreUsuario);
          formData.append("biografia", biografia);

          if (imagenPerfilFile) {
              formData.append("imagen_perfil", imagenPerfilFile);
          }
          if (bannerPerfilFile) {
              formData.append("banner_perfil", bannerPerfilFile);
          }

          fetch("/api", { 
              method: "POST",
              body: formData,
          })
          .then((response) => response.json())
          .then((data) => {
              if (data.message) {
                  alert("Perfil actualizado con éxito");
                  const editProfileModal = document.getElementById("editProfileModal");
                  if (editProfileModal) {
                      editProfileModal.style.display = "none"; 
                  }
                  location.reload();
              } else if (data.error) {
                  alert("Error: " + data.error);
              }
          })
          .catch((error) => {
              console.error("Error en la solicitud:", error);
              alert("Hubo un problema al actualizar el perfil.");
          });
      });
  }

  const profileUploadInput = document.getElementById("profileUpload");
  if (profileUploadInput) {
      profileUploadInput.addEventListener("change", function (event) {
          const file = event.target.files[0]; 
          const reader = new FileReader();
          reader.onload = function (e) {
              const modalProfileImg = document.querySelector(".modalB .profile-image img");
              if (modalProfileImg) {
                  modalProfileImg.src = e.target.result;
              }
          };
          if (file) {
              reader.readAsDataURL(file); 
          }
      });
  }

  const bannerUploadInput = document.getElementById("bannerUpload");
  if (bannerUploadInput) {
      bannerUploadInput.addEventListener("change", function (event) {
          const file = event.target.files[0]; 
          const reader = new FileReader();
          reader.onload = function (e) {
              const modalBannerImg = document.querySelector(".modalB .profile-banner img");
              if (modalBannerImg) {
                  modalBannerImg.src = e.target.result;
              }
          };
          if (file) {
              reader.readAsDataURL(file); 
          }
      });
  }

  // --- INICIO DEL CÓDIGO DEL SEGUNDO BLOQUE DOMContentLoaded (LÓGICA DEL BOTÓN SEGUIR) ---
  const followToggleButton = document.querySelector('.follow-toggle-btn');
  if (followToggleButton) {
      followToggleButton.addEventListener('click', function() {
          const profileId = this.dataset.profileId;
          // let estaSiguiendo = this.dataset.estaSiguiendo === 'true'; // No es necesario aquí, el backend lo determinará

          const formData = new FormData();
          formData.append('profile_user_id', profileId);

          fetch('/seguimiento/toggle', {
            method: 'POST',
            body: formData
          })
          .then(response => {
              if (!response.ok) {
                  throw new Error(`HTTP error! status: ${response.status}`);
              }
              return response.json();
          })
          .then(data => {
              if (data.success) {
                  this.textContent = data.estaSiguiendo ? 'Dejar de seguir' : 'Seguir';
                  this.dataset.estaSiguiendo = data.estaSiguiendo ? 'true' : 'false';
                  
                  const seguidoresCountElement = document.getElementById(`seguidores-count-${profileId}`);
                  if (seguidoresCountElement) {
                      seguidoresCountElement.textContent = data.nuevosSeguidoresCountDelPerfil;
                  }
                  // Opcionalmente, mostrar un mensaje de éxito breve
                  // console.log(data.message); 
              } else {
                  alert(data.message || 'Ocurrió un error al procesar la solicitud.');
              }
          })
          .catch(error => {
              console.error('Error en la solicitud de seguimiento:', error);
              alert('Error de conexión al intentar seguir/dejar de seguir. Revisa la consola para más detalles.');
          });
      });
  }
  // --- FIN DEL CÓDIGO DEL SEGUNDO BLOQUE DOMContentLoaded ---
});