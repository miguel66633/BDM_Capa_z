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

document.addEventListener("DOMContentLoaded", function () {
    // Función para validar el correo electrónico y la contraseña
    function validarFormulario() {
        const email = document.getElementById("email-registro").value;
        const contrasena = document.getElementById("password-registro").value;

        const tieneArroba = email.includes("@");
        const longitudValida = contrasena.length >= 4 && contrasena.length <= 12;
        const tieneMayuscula = /[A-Z]/.test(contrasena);
        const tieneMinuscula = /[a-z]/.test(contrasena);
        const tieneNumero = /[0-9]/.test(contrasena);

        document.getElementById("alerta-email").style.display = tieneArroba ? "none" : "block";
        document.getElementById("alerta-longitud").style.display = longitudValida ? "none" : "block";
        document.getElementById("alerta-mayuscula").style.display = tieneMayuscula && tieneMinuscula ? "none" : "block";
        document.getElementById("alerta-numero").style.display = tieneNumero ? "none" : "block";

        return tieneArroba && longitudValida && tieneMayuscula && tieneMinuscula && tieneNumero;
    }

    // Evento para el botón de registro
    document.querySelector(".submit-btn").addEventListener("click", function (event) {
        if (!validarFormulario()) {
            event.preventDefault(); // Evita que el formulario se envíe si no es válido
        } else {
            window.location.href = '../html/home.html'; // Redirigir si el formulario es válido
        }
    });
});
