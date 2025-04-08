function mostrarGooglePopup() {
    document.getElementById("googlePopup").style.display = "flex";
}

function cerrarGooglePopup() {
    document.getElementById("googlePopup").style.display = "none";
    document.getElementById("alerta-email").style.display = "none";
    document.getElementById("alerta-longitud").style.display = "none" ;
    document.getElementById("alerta-mayuscula").style.display = "none";
    document.getElementById("alerta-numero").style.display = "none";
}

function mostrarPopup() {
    document.getElementById("popup").style.display = "flex";
}

function cerrarPopup() {
    document.getElementById("popup").style.display = "none";
}

function registrate(){
    document.getElementById("popup").style.display = "none";
    document.getElementById("googlePopup").style.display = "flex";
}

// document.addEventListener("DOMContentLoaded", function () {
//     // Función para validar el correo electrónico y la contraseña
//     function validarFormulario() {
//         const email = document.getElementById("email-registro").value;
//         const contrasena = document.getElementById("password-registro").value;

//         const tieneArroba = email.includes("@");
//         const longitudValida = contrasena.length >= 4 && contrasena.length <= 12;
//         const tieneMayuscula = /[A-Z]/.test(contrasena);
//         const tieneMinuscula = /[a-z]/.test(contrasena);
//         const tieneNumero = /[0-9]/.test(contrasena);

//         document.getElementById("alerta-email").style.display = tieneArroba ? "none" : "block";
//         document.getElementById("alerta-longitud").style.display = longitudValida ? "none" : "block";
//         document.getElementById("alerta-mayuscula").style.display = tieneMayuscula && tieneMinuscula ? "none" : "block";
//         document.getElementById("alerta-numero").style.display = tieneNumero ? "none" : "block";

//         return tieneArroba && longitudValida && tieneMayuscula && tieneMinuscula && tieneNumero;
//     }

//     // Evento para el botón de registro
//     document.querySelector(".submit-btn").addEventListener("click", function (event) {
//         if (!validarFormulario()) {
//             event.preventDefault(); // Evita que el formulario se envíe si no es válido
//         } else {
//             window.location.href = '../html/home.html'; // Redirigir si el formulario es válido
//         }
//     });
// });



async function handleRegister(event) {
    event.preventDefault();
    
    try {

        const response = await fetch('/api?action=register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                email: document.getElementById('email-registro').value,
                password: document.getElementById('password-registro').value 
            })
        });

        // Debug mejorado
        console.log('URL:', response.url);
        console.log('Status:', response.status);
        console.log('Headers:', Object.fromEntries(response.headers));

        const textResponse = await response.text();
        console.log('Response text:', textResponse);

        try {
            const data = JSON.parse(textResponse);
            if (data.success) {
                window.location.href = '/inicio';
            } else {
                alert(data.error || 'Error al registrar usuario');
            }
        } catch (jsonError) {
            console.error('Error parsing JSON:', jsonError, 'Raw text:', textResponse);
            throw new Error('Error procesando respuesta del servidor');
        }
    } catch (error) {
        console.error('Error detallado:', {
            message: error.message,
            type: error.name,
        });
        alert(`Error al conectar con el servidor: ${error.message}`);
    }
}

// También actualiza la función de login
async function handleLogin(event) {
    event.preventDefault();
    
    const formData = {
        email: document.getElementById('email-login').value,
        password: document.getElementById('password-login').value
    };

    try {
        const response = await fetch('/api?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.success) {
            window.location.href = '/BDM_Capa_Z/inicio';
        } else {
            alert(data.error || 'Error en el inicio de sesión');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al conectar con el servidor');
    }
}
