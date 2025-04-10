//LLENAR EL PERFIL CON LOS DATOS DEL USUARIO
document.addEventListener("DOMContentLoaded", function () {
  const userId = document.getElementById("userId").value;

  fetch(`/api?UsuarioID=${userId}`)
    .then((response) => response.json())
    .then((data) => {
      console.log("Datos del usuario:", data);

      // Rellenamos los datos en el HTML

      document.querySelector(
        ".profile-details .name-and-edit .name-username h2"
      ).textContent = data.NombreUsuario;

      // Si existe una biografía, rellenamos ese campo
      const bio =
        data.Biografia ||
        "Aquí puedes colocar una breve descripción o presentación.";
      document.querySelector(
        ".profile-info-perfil .profile-details .bio"
      ).textContent = bio;

      if (data.ImagenPerfil) {
        document.querySelector(
          ".profile-info-perfil .profile-image img"
        ).src = `data:image/jpeg;base64,${data.ImagenPerfil}`;
      }

      // Si tienes la imagen de banner, actualizamos la URL en formato base64
      if (data.BannerPerfil) {
        document.querySelector(
          ".profile-banner img"
        ).src = `data:image/jpeg;base64,${data.BannerPerfil}`;
      }

      // Rellenar el modal de edición
      document.getElementById("nameInput").value = data.NombreUsuario;
      document.getElementById("bioInput").value = bio;
      if (data.ImagenPerfil) {
        document.querySelector(
          ".modalB .profile-image img"
        ).src = `data:image/jpeg;base64,${data.ImagenPerfil}`;
      }
      if (data.BannerPerfil) {
        document.querySelector(
          ".modalB .profile-banner img"
        ).src = `data:image/jpeg;base64,${data.BannerPerfil}`;
      }
    })
    .catch((error) => {
      console.error("Error al obtener el perfil:", error);
    });
});

document
  .getElementById("saveModalBtn")
  .addEventListener("click", function (event) {
    event.preventDefault(); // Prevenir la acción predeterminada del botón (recarga de página)

    // Obtener los valores de los campos
    const nombreUsuario = document.getElementById("nameInput").value;
    const biografia = document.getElementById("bioInput").value;

    // Obtener las imágenes (si es que el usuario las ha seleccionado)
    const imagenPerfil = document.getElementById("profileUpload").files[0];
    const bannerPerfil = document.getElementById("bannerUpload").files[0];

    // Crear un FormData para enviar los datos, incluidas las imágenes
    const formData = new FormData();
    formData.append("action", "modificar"); // Acción que se ejecutará en la API
    formData.append("nombre_usuario", nombreUsuario);
    formData.append("biografia", biografia);

    // Agregar las imágenes si existen
    if (imagenPerfil) {
      formData.append("imagen_perfil", imagenPerfil);
    }
    if (bannerPerfil) {
      formData.append("banner_perfil", bannerPerfil);
    }

    // Enviar la solicitud AJAX
    fetch("/api", {
      // Reemplaza con la URL de tu API
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.message) {
          alert("Perfil actualizado con éxito");
          document.getElementById("editProfileModal").style.display = "none"; // Cerrar modal
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

document
  .getElementById("profileUpload")
  .addEventListener("change", function (event) {
    const file = event.target.files[0]; // Obtener el archivo seleccionado
    const reader = new FileReader();

    reader.onload = function (e) {
      // Cambiar la fuente de la imagen del perfil a la imagen seleccionada
      document.querySelector(".modalB .profile-image img").src =
        e.target.result;
    };

    if (file) {
      reader.readAsDataURL(file); // Leer la imagen como base64
    }
  });

// Mostrar la imagen seleccionada de banner
document
  .getElementById("bannerUpload")
  .addEventListener("change", function (event) {
    const file = event.target.files[0]; // Obtener el archivo seleccionado
    const reader = new FileReader();

    reader.onload = function (e) {
      // Cambiar la fuente de la imagen del banner a la imagen seleccionada
      document.querySelector(".modalB .profile-banner img").src =
        e.target.result;
    };

    if (file) {
      reader.readAsDataURL(file); // Leer la imagen como base64
    }
  });
