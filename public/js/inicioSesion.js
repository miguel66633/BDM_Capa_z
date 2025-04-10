function mostrarGooglePopup() {
    document.getElementById("googlePopup").style.display = "flex";
}

function cerrarGooglePopup() {
    document.getElementById("googlePopup").style.display = "none";
    // Limpiar alertas al cerrar
    limpiarAlertas();
}

function mostrarPopup() {
    document.getElementById("popup").style.display = "flex";
}

function cerrarPopup() {
    document.getElementById("popup").style.display = "none";
    // Limpiar errores al cerrar
    document.getElementById("error-login-email").style.display = "none";
    document.getElementById("error-login-password").style.display = "none";
}

function limpiarAlertas() {
    document.getElementById("alerta-email").style.display = "none";
    document.getElementById("alerta-longitud").style.display = "none";
    document.getElementById("alerta-mayuscula").style.display = "none";
    document.getElementById("alerta-numero").style.display = "none";
}

function togglePassword() {
    const passwordInput = document.activeElement.parentElement.querySelector('input[type="password"]');
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
}


document.querySelector('#registerForm').addEventListener('submit', function (event) {
    event.preventDefault();

    // Obtener los valores de los campos
    const fullName = document.getElementById('full-name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();

    // Limpiar errores anteriores
    document.getElementById('error-fullname').innerHTML = '';
    document.getElementById('error-email').innerHTML = '';
    document.getElementById('error-password').innerHTML = '';

    let errores = [];

    // Validación nombre
    if (fullName === '') {
        errores.push({ id: 'error-fullname', mensaje: 'El nombre de usuario no puede estar vacío.' });
    } else if (!/^[a-zA-Z\s]+$/.test(fullName)) {
        errores.push({ id: 'error-fullname', mensaje: 'El nombre de usuario solo debe contener letras.' });
    }

    // Validación correo
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        errores.push({ id: 'error-email', mensaje: 'El correo electrónico no puede estar vacío.' });
    } else if (!emailRegex.test(email)) {
        errores.push({ id: 'error-email', mensaje: 'Por favor, introduce un correo electrónico válido.' });
    }

    // Validación contraseña
    if (password === '') {
        errores.push({ id: 'error-password', mensaje: 'La contraseña no puede estar vacía.' });
    } else {
        if (password.length < 8) {
            errores.push({ id: 'error-password', mensaje: 'La contraseña debe tener al menos 8 caracteres.' });
        }
        if (!/[A-Z]/.test(password)) {
            errores.push({ id: 'error-password', mensaje: 'La contraseña debe contener al menos una letra mayúscula.' });
        }
        if (!/[0-9]/.test(password)) {
            errores.push({ id: 'error-password', mensaje: 'La contraseña debe contener al menos un número.' });
        }
        if (!/[!@#$%^&*]/.test(password)) {
            errores.push({ id: 'error-password', mensaje: 'La contraseña debe contener al menos un carácter especial (!@#$%^&*).' });
        }
    }

    // Mostrar errores si hay
    if (errores.length > 0) {
        errores.forEach(error => {
            document.getElementById(error.id).innerHTML = `<p>${error.mensaje}</p>`;
        });
        return;
    }

    // Enviar datos si no hay errores
    const formData = new FormData(this);
    formData.append('action', 'register'); // Agregar acción de registro

    fetch('api/', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Aquí puedes mostrar mensajes de éxito o redirigir
        if (data.error) {
            alert(data.error);
        } else if (data.message) {
            alert(data.message);
            window.location.href = '/inicio';
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        alert('Error al enviar los datos. Intenta nuevamente.');
    });
});


document.querySelector('#loginForm').addEventListener('submit', function (event) {
    event.preventDefault();

    // Obtener los valores de los campos
    const email = document.getElementById('email-login').value.trim();
    const password = document.getElementById('password-login').value.trim();

    // Limpiar errores anteriores
    document.getElementById('error-login-email').innerHTML = '';
    document.getElementById('error-login-password').innerHTML = '';

    let errores = [];

    // Validación correo
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        errores.push({ id: 'error-login-email', mensaje: 'El correo electrónico no puede estar vacío.' });
    } else if (!emailRegex.test(email)) {
        errores.push({ id: 'error-login-email', mensaje: 'Por favor, introduce un correo electrónico válido.' });
    }

    // Validación contraseña
    if (password === '') {
        errores.push({ id: 'error-login-password', mensaje: 'La contraseña no puede estar vacía.' });
    }

    // Mostrar errores si hay
    if (errores.length > 0) {
        errores.forEach(error => {
            document.getElementById(error.id).innerHTML = `<p>${error.mensaje}</p>`;
        });
        return;
    }

    // Enviar datos si no hay errores
    const formData = new FormData(this);
    formData.append('action', 'login'); // Agregar acción de inicio de sesión

    fetch('/api', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Mostrar mensajes de éxito o error
        if (data.error) {
            // Asignar el mensaje de error al elemento correspondiente
            document.getElementById('error-login-email').innerHTML = `<p>${data.error}</p>`;
        } else if (data.message) {
            // alert(data.message);
            window.location.href = '/inicio'; // Redirigir al inicio
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        document.getElementById('error-login-email').innerHTML = `<p>Error al iniciar sesión. Intenta nuevamente.</p>`;
    });
});