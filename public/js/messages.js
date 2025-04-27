function toggleMenu() {
    let menu = document.getElementById("profile-menu");
    // menu.style.display = menu.style.display === "block" ? "none" : "block";
}

document.addEventListener("DOMContentLoaded", function () {
    const profileContainer = document.querySelector(".profile-container");
    const profileMenu = document.querySelector("#profile-menu"); // Asegúrate que el ID es correcto

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

//Manejo de la Búsqueda de Usuarios y Creación/Selección de Chats
document.addEventListener('DOMContentLoaded', () => {
    const chatListContainer = document.getElementById('chat-list-container'); 
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const messageListContainer = document.querySelector('main#contenido .scrollable-content'); 
    const chatHeaderImg = document.querySelector('.chat-header-img');
    const chatHeaderName = document.querySelector('.chat-header-name');
    const chatMessages = document.getElementById('chat-messages');
    const chatIdInput = document.getElementById('chat-id');
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-button');
    const locationButton = document.getElementById('ubicacion-button'); 

    const usuarioIdActual = parseInt(document.body.dataset.userId || 0, 10);

    function enviarMensaje(contenidoMensaje) {
        const chatId = chatIdInput.value;

        if (!chatId) {
            alert('Por favor, selecciona un chat primero.');
            return;
        }

        if (!contenidoMensaje || contenidoMensaje.trim() === '') {
            // No mostrar alerta si el input de texto está vacío,
            // pero sí si se intenta enviar ubicación vacía (aunque no debería pasar)
            if (chatInput.value.trim() === '' && contenidoMensaje === chatInput.value) {
                 alert('El mensaje no puede estar vacío.');
            } else if (!contenidoMensaje || contenidoMensaje.trim() === '') {
                 alert('El contenido del mensaje es inválido.');
            }
            return;
        }

        fetch('/mensaje/enviar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `chat_id=${encodeURIComponent(chatId)}&contenido=${encodeURIComponent(contenidoMensaje)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                // Limpiar el input de texto SOLO si el mensaje enviado era del input
                // (Esto evita borrar el input si se envió ubicación mientras se escribía)
                // Comprobación simple: si el contenido enviado es igual al del input actual
                if (contenidoMensaje === chatInput.value) {
                    chatInput.value = ''; 
                }
                cargarMensajes(chatId); 
                actualizarListaChats(chatId, contenidoMensaje);
            }
        })
        .catch(error => console.error('Error al enviar mensaje:', error));
    }

    // --- Función para cargar mensajes ---
    function cargarMensajes(chatId) {
        fetch('/mensaje/cargar', { 
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
                chatMessages.innerHTML = '<p class="error-message">Error al cargar mensajes.</p>';
            } else {
                 if (data.NombreUsuario && chatHeaderName) chatHeaderName.textContent = data.NombreUsuario;
                 if (data.ImagenPerfil && chatHeaderImg) chatHeaderImg.src = data.ImagenPerfil;

                chatMessages.innerHTML = data.Mensajes.map(mensaje => {
                    const hora = new Date(mensaje.FechaMensaje).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const esMiMensaje = mensaje.RemitenteID === usuarioIdActual;
                    
                    const rawContent = mensaje.ContenidoMensaje;
                    
                    const escapedContentDiv = document.createElement('div');
                    escapedContentDiv.innerText = rawContent;
                    const escapedContent = escapedContentDiv.innerHTML;

                    const urlRegex = /(https?:\/\/[^\s]+)/g; // Regex para encontrar URLs
                    const contentWithLinks = escapedContent.replace(urlRegex, (url) => {

                        return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
                    });


                    return `
                        <div class="message ${esMiMensaje ? 'my-message' : 'other-message'}">

                            <p>${contentWithLinks}</p> 
                            <span class="message-time">${hora}</span>
                        </div>
                    `;
                }).join('');
                chatMessages.scrollTop = chatMessages.scrollHeight; 
            }
        })
        .catch(error => {
            console.error('Error al cargar mensajes:', error);
            chatMessages.innerHTML = '<p class="error-message">Error al cargar mensajes.</p>';
        });
    }

    function actualizarListaChats(chatId, ultimoMensaje) {
        const chatElement = chatListContainer.querySelector(`.mensaje[data-chat-id="${chatId}"]`); 
        if (chatElement) {
            const textoMensaje = chatElement.querySelector('.mensaje-texto');
            const fechaMensaje = chatElement.querySelector('.mensaje-fecha');
            if (textoMensaje) {
                const divTemp = document.createElement('div');
                divTemp.innerText = ultimoMensaje;
                const preview = divTemp.innerHTML.length > 30 ? divTemp.innerHTML.substring(0, 27) + '...' : divTemp.innerHTML;
                textoMensaje.innerHTML = preview; 
            }
            if (fechaMensaje) {
                fechaMensaje.textContent = '• Ahora'; // O formato de hora actual
            }
            if (chatListContainer.firstChild !== chatElement) { 
                chatListContainer.prepend(chatElement);
           }
        }
    }

    searchInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            const termino = searchInput.value.trim();
            searchResults.style.display = 'block'; 

            if (termino === '') {
                searchResults.innerHTML = ''; 
                searchResults.style.display = 'none';
                return;
            }

            fetch('/buscar-usuario', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `termino=${encodeURIComponent(termino)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    searchResults.innerHTML = `<p class="error-message">${data.error}</p>`;
                } else if (data.length > 0) {
                    searchResults.innerHTML = data.map(usuario => `
                        <div class="search-result" data-usuario-id="${usuario.UsuarioID}">
                            <img src="${usuario.ImagenPerfil ? `data:image/jpeg;base64,${usuario.ImagenPerfil}` : '/Resources/images/perfilPre.jpg'}" class="mensaje-img" alt="${usuario.NombreUsuario}">
                            <span class="mensaje-nombre">${usuario.NombreUsuario}</span>
                        </div>
                    `).join('');

                    searchResults.querySelectorAll('.search-result').forEach(result => {
                        result.addEventListener('click', () => {
                            const destinatarioId = result.getAttribute('data-usuario-id');
                            const nombreUsuario = result.querySelector('.mensaje-nombre').textContent;
                            const imagenPerfilSrc = result.querySelector('.mensaje-img').src; // Obtener src de la imagen

                            searchResults.innerHTML = '';
                            searchResults.style.display = 'none';
                            searchInput.value = '';

                            let chatExistenteElement = messageListContainer.querySelector(`.mensaje[data-destinatario-id="${destinatarioId}"]`);

                            if (chatExistenteElement) {
                                chatExistenteElement.click();
                            } else {
                                fetch('/crear-chat', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `destinatario_id=${encodeURIComponent(destinatarioId)}`
                                })
                                .then(response => response.json())
                                .then(chatData => {
                                    if (chatData.error) {
                                        alert(chatData.error);
                                    } else {
                                        const nuevoChatDiv = document.createElement('div');
                                        nuevoChatDiv.classList.add('mensaje');
                                        nuevoChatDiv.setAttribute('data-chat-id', chatData.chatId);
                                        nuevoChatDiv.setAttribute('data-destinatario-id', destinatarioId);
                                        nuevoChatDiv.setAttribute('data-imagen-perfil', imagenPerfilSrc);
                                        nuevoChatDiv.setAttribute('data-nombre-usuario', nombreUsuario);

                                        nuevoChatDiv.innerHTML = `
                                            <img src="${imagenPerfilSrc}" class="mensaje-img" alt="${nombreUsuario}">
                                            <div class="mensaje-info">
                                                <div class="mensaje-header">
                                                    <span class="mensaje-nombre">${nombreUsuario}</span>
                                                    <span class="mensaje-fecha">• Ahora</span>
                                                </div>
                                                <div class="mensaje-texto">Comienza a conversar</div>
                                            </div>
                                        `;
                                        messageListContainer.insertBefore(nuevoChatDiv, messageListContainer.children[3]);
                                        addChatSelectionListener(nuevoChatDiv);


                                        nuevoChatDiv.click();
                                    }
                                })
                                .catch(error => console.error('Error al crear chat:', error));
                            }
                        });
                    });
                } else {
                    searchResults.innerHTML = '<p>No se encontraron usuarios.</p>';
                }
            })
            .catch(error => {
                console.error('Error al buscar:', error);
                searchResults.innerHTML = '<p>Error al buscar usuarios.</p>';
            });
        } else {
              searchResults.style.display = 'none';
        }
    });

     document.addEventListener('click', (event) => {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.style.display = 'none';
        }
     });

    function addChatSelectionListener(chatElement) {
        chatElement.addEventListener('click', () => {
            const chatId = chatElement.getAttribute('data-chat-id');
            const chatName = chatElement.getAttribute('data-nombre-usuario');
            const chatImg = chatElement.getAttribute('data-imagen-perfil');

            if (chatHeaderImg) chatHeaderImg.src = chatImg || '/Resources/images/perfilPre.jpg';
            if (chatHeaderName) chatHeaderName.textContent = chatName || 'Chat';

            document.querySelectorAll('.mensaje').forEach(m => m.classList.remove('seleccionado'));
            chatElement.classList.add('seleccionado');

            chatIdInput.value = chatId;

            cargarMensajes(chatId);
        });
    }

    document.querySelectorAll('.mensaje[data-chat-id]').forEach(addChatSelectionListener);

    if (sendButton) {
        sendButton.addEventListener('click', () => {
            enviarMensaje(chatInput.value);
        });
    }

    if (chatInput) {
        chatInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                enviarMensaje(chatInput.value);
            }
        });
    }

    if (locationButton) {
        locationButton.addEventListener('click', () => {
            const chatId = chatIdInput.value;
            if (!chatId) {
                alert('Por favor, selecciona un chat primero.');
                return;
            }

            if (!navigator.geolocation) {
                alert('La geolocalización no es soportada por tu navegador.');
                return;
            }

            locationButton.disabled = true;
            locationButton.style.opacity = '0.5';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    const googleMapsUrl = `https://www.google.com/maps?q=${latitude},${longitude}`;
                    const mensajeUbicacion = `Mi ubicación: ${googleMapsUrl}`;

                    enviarMensaje(mensajeUbicacion);

                    locationButton.disabled = false;
                    locationButton.style.opacity = '1';
                },
                (error) => {
                    console.error("Error al obtener la ubicación: ", error);
                    alert(`No se pudo obtener la ubicación: ${error.message}`);
                    locationButton.disabled = false;
                    locationButton.style.opacity = '1';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }

     const primerChat = document.querySelector('.mensaje[data-chat-id]');
     if (primerChat) {
         primerChat.click();
     }

});
