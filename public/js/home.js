function toggleMenu() {
    let menu = document.getElementById("profile-menu");
    // menu.style.display = menu.style.display === "block" ? "none" : "block";
}

document.addEventListener("DOMContentLoaded", function () {
    const profileContainer = document.querySelector(".profile-container");
    const profileMenu = document.querySelector(".profile-menu");

    profileContainer.addEventListener("click", function (event) {
        event.stopPropagation(); // Evita que se cierre inmediatamente al hacer clic
        profileMenu.classList.toggle("active");
    });

    // Cierra el menú si se hace clic fuera de él
    document.addEventListener("click", function (event) {
        if (!profileContainer.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.classList.remove("active");
        }
    });
});

// Abre la ventana modal
function openModal() {
    document.getElementById('postModal').style.display = 'block';
}
  
// Cierra la ventana modal
function closeModal() {
  document.getElementById('postModal').style.display = 'none';
}

function mostrarConfirmPopup() {
  document.getElementById("popup").style.display = "flex";
}

function cerrarConfirmPopup() {
  document.getElementById("popup").style.display = "none";
}

function toggleMenu() {
  let menu = document.getElementById("profile-menu");
  menu.classList.toggle("active");
}

function logout() {

}

  document.addEventListener("DOMContentLoaded", function () {
    const profileContainer = document.querySelector(".profile-container");
    const profileMenu = document.querySelector(".profile-menu");
  
    profileContainer.addEventListener("click", function (event) {
      event.stopPropagation(); // Evita que se cierre inmediatamente al hacer clic
      profileMenu.classList.toggle("active");
    });
  
    // Cierra el menú si se hace clic fuera de él
    document.addEventListener("click", function (event) {
      if (!profileContainer.contains(event.target) && !profileMenu.contains(event.target)) {
        profileMenu.classList.remove("active");
      }
    });
  });
  

  // Función para alternar la imagen del botón "like"
  document.addEventListener('DOMContentLoaded', () => {
    const likeButtons = document.querySelectorAll('.like-btn');

    likeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const publicacionId = button.getAttribute('data-publicacion-id');
            const likeCountElement = document.getElementById(`like-count-${publicacionId}`);
            const img = button.querySelector('.accion-icon');

            fetch('/like', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `publicacion_id=${publicacionId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    const currentLikes = parseInt(likeCountElement.textContent, 10);
                    if (data.liked) {
                        likeCountElement.textContent = currentLikes + 1;
                        img.setAttribute('src', '/Resources/images/likeP.svg'); // Cambiar a imagen de "like activo"
                    } else {
                        likeCountElement.textContent = currentLikes - 1;
                        img.setAttribute('src', '/Resources/images/like.svg'); // Cambiar a imagen de "like inactivo"
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});

  // Función para alternar la imagen del botón "saved"
  document.addEventListener('DOMContentLoaded', () => {
    const saveButtons = document.querySelectorAll('.saved-btn');

    saveButtons.forEach(button => {
        button.addEventListener('click', () => {
            const publicacionId = button.getAttribute('data-publicacion-id');
            const saveCountElement = document.getElementById(`save-count-${publicacionId}`);
            const img = button.querySelector('.accion-icon');

            fetch('/guardar', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `publicacion_id=${publicacionId}`
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) { // Comprobar el éxito general de la operación
                  // Actualizar el contador directamente con el valor del backend
                  if (saveCountElement) {
                      saveCountElement.textContent = data.savesCount;
                  }
                  
                  // Actualizar el ícono del botón
                  if (data.guardado) { // 'guardado' viene de 'YaGuardo' en el SP
                      img.setAttribute('src', '/Resources/images/guardados.svg'); // Icono de "guardado activo"
                  } else {
                      img.setAttribute('src', '/Resources/images/saved.svg');   // Icono de "guardado inactivo"
                  }
                  // console.log(data.message); // Opcional: para depuración o feedback
              } else {
                  // Mostrar el mensaje de error del backend
                  alert(data.message || 'Ocurrió un error al procesar la acción.');
              }
          })
          .catch(error => {
              console.error('Error en la petición de guardado:', error);
              alert('Error de conexión al intentar guardar/dejar de guardar.');
          });
        });
    });
});
  
  
// document.querySelectorAll('.repost-btn').forEach(function(btn) {
//   btn.addEventListener('click', function() {
//     this.classList.toggle('active');
//   });
// });

document.addEventListener('DOMContentLoaded', function () {
  // ... (código existente para likes, guardados, etc.)

  document.querySelectorAll('.repost-btn').forEach(button => {
      button.addEventListener('click', function () {
        // this.classList.toggle('active');
          const publicacionId = this.dataset.publicacionId;
          const icon = this.querySelector('.accion-icon');
          const countSpan = document.getElementById(`repost-count-${publicacionId}`);

          fetch('/repost/toggle', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                  'X-Requested-With': 'XMLHttpRequest' 
              },
              body: `publicacion_id=${publicacionId}`
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  countSpan.textContent = data.repostsCount;
                  if (data.yaReposteo) {
                      icon.src = '/Resources/images/repostP.svg'; // Cambia al ícono activo
                  } else {
                      icon.src = '/Resources/images/repost.svg'; // Cambia al ícono inactivo
                  }
              } else {
                  console.error('Error al repostear:', data.message);
              }
          })
          .catch(error => console.error('Error en la petición de repost:', error));
      });
  });
});



// Seleccionamos elementos
const carouselSlide = document.querySelector('.carousel-slide');
const slides = document.querySelectorAll('.slide'); 
// const arrowLeft = document.querySelector('.carousel-arrow.left');
// const arrowRight = document.querySelector('.carousel-arrow.right');

// Índice del slide visible
let currentIndex = 0;

// Función para mover el contenedor
function updateSlidePosition() {
  carouselSlide.style.transform = `translateX(-${currentIndex * 100}%)`;
}

// Flecha izquierda
// arrowLeft.addEventListener('click', () => {
//   // Si estamos en el primer slide, pasamos al último
//   currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
//   updateSlidePosition();
// });

// // Flecha derecha
// arrowRight.addEventListener('click', () => {
//   // Si estamos en el último slide, pasamos al primero
//   currentIndex = (currentIndex + 1) % slides.length;
//   updateSlidePosition();
// });


// document.addEventListener("DOMContentLoaded", function() {
//     document.querySelectorAll(".publicacion").forEach(publicacion => {
//         publicacion.addEventListener("click", function(event) {
//             // Verifica si el clic fue en el header, la imagen o el contenido del post
//             if (event.target.closest(".publicacion-header") || 
//                 event.target.closest(".slide") || event.target.closest(".img")) {
//                 window.location.href = "post.html";
//             }
//         });
//     });
// });




// Elementos del DOM
const openModalBtn = document.getElementById('openModalBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const saveModalBtn = document.getElementById('saveModalBtn');
const editProfileModal = document.getElementById('editProfileModal');

// Función para abrir el modal
openModalBtn.addEventListener('click', () => {
  editProfileModal.style.display = 'flex';
});

// Función para cerrar el modal al hacer clic en la X
closeModalBtn.addEventListener('click', () => {
  editProfileModal.style.display = 'none';
});

// Función para cerrar el modal al hacer clic en "Guardar"
saveModalBtn.addEventListener('click', () => {

  // Cerrar el modal
  editProfileModal.style.display = 'none';
});

// Cerrar el modal al hacer clic en cualquier área oscura fuera de .modal-content
window.addEventListener('click', (event) => {
  if (event.target === editProfileModal) {
    editProfileModal.style.display = 'none';
  }
});




//Admin

document.addEventListener("DOMContentLoaded", function () {
  // Simulación de datos (reemplazar con datos reales)
  const userCount = 100; // Ejemplo
  const postCount = 50; // Ejemplo
  const reports = [
      "Reporte 1: Detalle del reporte...",
      "Reporte 2: Detalle del reporte...",
      // Más reportes
  ];

  // Actualizar estadísticas
  document.getElementById("user-count").textContent = userCount;
  document.getElementById("post-count").textContent = postCount;

  // Actualizar lista de reportes
  const reportsContent = document.getElementById("reports-content").querySelector("ul");
  reportsContent.innerHTML = "";
  reports.forEach(report => {
      const li = document.createElement("li");
      li.textContent = report;
      reportsContent.appendChild(li);
  });
});



function mostrarReportesPopup(usuarioId) {
  // Mostrar el popup de reportes
  document.getElementById("ReportesPopup").style.display = "flex";

  // Hacer una solicitud AJAX para obtener el reporte del usuario seleccionado
  fetch(`/admin?UsuarioID=${usuarioId}&ajax=1`)
    .then(response => response.json())
    .then(data => {
        console.log("JSON recibido:", data); // Ver la estructura

        // Acceder a los valores dentro del array
        document.querySelector("#ReportesPopup h2").textContent = `Reporte de ${data[0].NombreUsuario}`;
        document.getElementById("publicaciones-subidas").textContent = data[0].TotalPublicaciones;
        document.getElementById("likes-dados").textContent = data[0].TotalLikes;
        document.getElementById("comentarios-hechos").textContent = data[0].TotalComentarios;
        document.getElementById("guardados-hechos").textContent = data[0].TotalGuardados;
    })
    .catch(error => console.error("Error al obtener reportes:", error));

}



// function mostrarReportesPopup(usuarioId) {
//   // Redirigir al usuario con el ID seleccionado
//   window.location.href = `/admin?UsuarioID=${usuarioId}`;


//   // document.getElementById("ReportesPopup").style.display = "flex";
// }



function cerrarReportesPopup() {
  document.getElementById("ReportesPopup").style.display = "none";
}


document.addEventListener('DOMContentLoaded', () => {
  // Seleccionar todas las publicaciones
  const publicaciones = document.querySelectorAll('.publicacion');

  publicaciones.forEach(publicacion => {
      const postId = publicacion.getAttribute('data-id'); // Obtener el ID de la publicación

      // Redirigir al hacer clic en el botón de comentarios
      const comentariosBtn = publicacion.querySelector('.comentarios-btn');
      if (comentariosBtn) {
          comentariosBtn.addEventListener('click', (event) => {
              event.stopPropagation(); // Evitar que el clic active el evento del contenedor
              if (postId) {
                  window.location.href = `/post/${postId}`; // Redirigir a la página del post
              }
          });
      }
  });
});

