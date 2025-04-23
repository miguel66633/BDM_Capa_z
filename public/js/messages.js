document.addEventListener("DOMContentLoaded", function () {
    const profileContainer = document.querySelector(".profile-container");
    const profileMenu = document.querySelector("#profile-menu");

    profileContainer.addEventListener("click", function (event) {
        event.stopPropagation(); // Evita que el evento se propague
        profileMenu.classList.toggle("active");
    });

    // Cierra el menú si se hace clic fuera de él
    document.addEventListener("click", function (event) {
        if (!profileContainer.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.classList.remove("active");
        }
    });
});


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

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const messageList = document.getElementById('message-list'); // Contenedor de los mensajes

    // Manejar la búsqueda al presionar Enter
    searchInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            const termino = searchInput.value.trim();

            if (termino === '') {
                alert('Por favor, ingresa un término de búsqueda.');
                return;
            }

            // Enviar la solicitud al servidor
            fetch('/buscar-usuario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `termino=${encodeURIComponent(termino)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else if (data.length > 0) {
                    // Mostrar los resultados de búsqueda
                    searchResults.innerHTML = data.map(usuario => `
                        <div class="search-result" data-usuario-id="${usuario.UsuarioID}">
                            <img src="${usuario.ImagenPerfil ? `data:image/jpeg;base64,${usuario.ImagenPerfil}` : 'Resources/images/perfilPre.jpg'}" class="mensaje-img" alt="${usuario.NombreUsuario}">
                            <span class="mensaje-nombre">${usuario.NombreUsuario}</span>
                        </div>
                    `).join('');
                } else {
                    searchResults.innerHTML = '<p>No se encontraron usuarios.</p>';
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // Manejar la selección de un usuario
    searchResults.addEventListener('click', (event) => {
        const result = event.target.closest('.search-result');
        if (result) {
            const usuarioId = result.getAttribute('data-usuario-id');
            const nombreUsuario = result.querySelector('.mensaje-nombre').textContent;

            // Agregar el usuario como un nuevo mensaje
            const nuevoMensaje = document.createElement('div');
            nuevoMensaje.classList.add('mensaje');
            nuevoMensaje.innerHTML = `
                <img src="../images/perfil.jpg" class="mensaje-img" alt="${nombreUsuario}">
                <div class="mensaje-info">
                    <div class="mensaje-header">
                        <span class="mensaje-nombre">${nombreUsuario}</span>
                        <span class="mensaje-fecha">• Ahora</span>
                    </div>
                    <div class="mensaje-texto">Nuevo chat iniciado</div>
                </div>
            `;
            messageList.appendChild(nuevoMensaje); // Agregar al final de la lista de mensajes
            searchResults.innerHTML = ''; // Limpiar los resultados de búsqueda
            searchInput.value = ''; // Limpiar el campo de búsqueda
        }
    });
});