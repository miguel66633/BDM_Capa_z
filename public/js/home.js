function toggleMenu() {
    let menu = document.getElementById("profile-menu");
    // menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function logout() {
//     window.location.href = "../html/inicioSesion.html";
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

  // Acción de "Postear" (por ahora, solo cierra el modal)
  function submitPost() {
    // Aquí podrías recoger el texto del textarea, la imagen, etc.
    // y hacer un envío a tu servidor o manejarlo como gustes.
    closeModal();
    setTimeout(mostrarConfirmPopup, 100); // Muestra el popup de confirmación después de un breve retraso

  }

  function mostrarConfirmPopup() {
    document.getElementById("popup").style.display = "flex";
  }
  
  function cerrarConfirmPopup() {
    document.getElementById("popup").style.display = "none";
  }




  
  // Resto de tus funciones existentes
  function toggleMenu() {
    let menu = document.getElementById("profile-menu");
    menu.classList.toggle("active");
  }
  
  function logout() {
    // window.location.href = "../html/inicioSesion.html";
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
document.querySelectorAll('.like-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const img = this.querySelector('.accion-icon');
      // Si la imagen actual es like.svg, se cambia a likeP.svg; de lo contrario, vuelve a like.svg
      if (img.getAttribute('src') === 'Resources/images/like.svg') {
        img.setAttribute('src', 'Resources/images/likeP.svg');
      } else {
        img.setAttribute('src', 'Resources/images/like.svg');
      }
    });
  });
  
  // Función para alternar la imagen del botón "saved"
  document.querySelectorAll('.saved-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const img = this.querySelector('.accion-icon');
      // Si la imagen actual es saved.svg, se cambia a guardados.svg; de lo contrario, vuelve a saved.svg
      if (img.getAttribute('src') === 'Resources/images/saved.svg') {
        img.setAttribute('src', 'Resources/images/guardados.svg');
      } else {
        img.setAttribute('src', 'Resources/images/saved.svg');
      }
    });
  });
  
  
  document.querySelectorAll('.repost-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      this.classList.toggle('active');
    });
  });
  



// Seleccionamos elementos
const carouselSlide = document.querySelector('.carousel-slide');
const slides = document.querySelectorAll('.slide'); 
const arrowLeft = document.querySelector('.carousel-arrow.left');
const arrowRight = document.querySelector('.carousel-arrow.right');

// Índice del slide visible
let currentIndex = 0;

// Función para mover el contenedor
function updateSlidePosition() {
  carouselSlide.style.transform = `translateX(-${currentIndex * 100}%)`;
}

// Flecha izquierda
arrowLeft.addEventListener('click', () => {
  // Si estamos en el primer slide, pasamos al último
  currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
  updateSlidePosition();
});

// Flecha derecha
arrowRight.addEventListener('click', () => {
  // Si estamos en el último slide, pasamos al primero
  currentIndex = (currentIndex + 1) % slides.length;
  updateSlidePosition();
});


document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".publicacion").forEach(publicacion => {
        publicacion.addEventListener("click", function(event) {
            // Verifica si el clic fue en el header, la imagen o el contenido del post
            if (event.target.closest(".publicacion-header") || 
                event.target.closest(".slide") || event.target.closest(".img")) {
                window.location.href = "post.html";
            }
        });
    });
});




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
  // Aquí podrías manejar la lógica de guardado, como enviar datos al servidor, etc.
  // ...

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

function mostrarReportesPopup() {
  document.getElementById("ReportesPopup").style.display = "flex";
}

function cerrarReportesPopup() {
  document.getElementById("ReportesPopup").style.display = "none";
}