document.addEventListener("DOMContentLoaded", function () {
    const profileContainer = document.querySelector(".profile-container");
    const profileMenu = document.querySelector("#profile-menu");

    if (profileContainer && profileMenu) {
        profileContainer.addEventListener("click", function (event) {
            event.stopPropagation();
            profileMenu.classList.toggle("active");
        });

        document.addEventListener("click", function (event) {
            if (!profileContainer.contains(event.target) && !profileMenu.contains(event.target)) {
                profileMenu.classList.remove("active");
            }
        });
    }
});

    // seleccion de un chat en la lista
document.querySelectorAll('.mensaje').forEach(mensaje => {
    mensaje.addEventListener('click', function() {
        document.querySelectorAll('.mensaje').forEach(m => m.classList.remove('seleccionado'));
        this.classList.add('seleccionado');
        document.querySelector('.chat-header-name').textContent = this.querySelector('.mensaje-nombre').textContent;
    });
});


//Manejo de la Búsqueda de Usuarios
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

                    // Agregar el evento de clic solo si hay resultados
                    searchResults.querySelectorAll('.search-result').forEach(result => {
                        result.addEventListener('click', () => {
                            const usuarioId = result.getAttribute('data-usuario-id');
                            const nombreUsuario = result.querySelector('.mensaje-nombre').textContent;

                            // Enviar la solicitud al servidor para crear el chat
                            fetch('/crear-chat', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `destinatario_id=${encodeURIComponent(usuarioId)}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    alert(data.error);
                                } else {
                                    // Agregar el usuario como un nuevo mensaje en la lista de chats
                                    const nuevoMensaje = document.createElement('div');
                                    nuevoMensaje.classList.add('mensaje');
                                    nuevoMensaje.innerHTML = `
                                        <img src="../images/perfil.jpg" class="mensaje-img" alt="${nombreUsuario}">
                                        <div class="mensaje-info">
                                            <div class="mensaje-header">
                                                <span class="mensaje-nombre">${nombreUsuario}</span>
                                                <span class="mensaje-handle">@${nombreUsuario.toLowerCase()}</span>
                                                <span class="mensaje-fecha">• Ahora</span>
                                            </div>
                                            <div class="mensaje-texto">Nuevo chat iniciado</div>
                                        </div>
                                    `;
                                    messageList.appendChild(nuevoMensaje); // Agregar al final de la lista de mensajes
                                    searchResults.innerHTML = ''; // Limpiar los resultados de búsqueda
                                    searchInput.value = ''; // Limpiar el campo de búsqueda
                                }
                            })
                            .catch(error => console.error('Error:', error));
                        });
                    });
                } else {
                    searchResults.innerHTML = '<p>No se encontraron usuarios.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                searchResults.innerHTML = '<p>Ocurrió un error al buscar usuarios.</p>';
            });
        }
    });
});

//Cargar Información del Chat Seleccionado
document.addEventListener('DOMContentLoaded', () => {
    const messageList = document.getElementById('message-list'); // Contenedor de los mensajes
    const chatHeaderName = document.querySelector('.chat-header-name'); // Nombre del usuario en la parte derecha
    const chatHeaderImg = document.querySelector('.chat-header-img'); // Imagen del usuario en la parte derecha

    // Manejar el clic en un chat
    messageList.addEventListener('click', (event) => {
        const chatElement = event.target.closest('.mensaje');
        if (chatElement) {
            const chatId = chatElement.getAttribute('data-chat-id');
    
            fetch('/cargar-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `chat_id=${encodeURIComponent(chatId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    console.log('Imagen de perfil:', data.ImagenPerfil); // Depuración
                    chatHeaderName.textContent = data.NombreUsuario;
                    chatHeaderImg.src = data.ImagenPerfil;
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});