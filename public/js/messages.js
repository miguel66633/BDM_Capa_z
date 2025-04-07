document.querySelectorAll('.mensaje').forEach(mensaje => {
    mensaje.addEventListener('click', function() {
        document.querySelectorAll('.mensaje').forEach(m => m.classList.remove('seleccionado'));
        this.classList.add('seleccionado');
        document.querySelector('.chat-header-name').textContent = this.querySelector('.mensaje-nombre').textContent;
    });
});

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

function logout() {
    window.location.href = "../html/inicioSesion.html";
  }
