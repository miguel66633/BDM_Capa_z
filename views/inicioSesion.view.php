<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión en Z</title>
    <link rel="stylesheet" href="/static.php?file=css/inicioSesion.css">
    <script src="/static.php?file=js/inicioSesion.js"></script>
</head>
<body>
    <div class="main-container">
        <!-- Columna izquierda (Logotipo) -->
        <div class="left-column">
            <span class="logo">Z</span>
        </div>

        <!-- Columna derecha (Texto y botones) -->
        <div class="right-column">
            <h1>Hasta el final</h1>
            <p>Únete hoy</p>
            <button onclick="mostrarGooglePopup()">Registrarse</button>
            <p>¿Ya tienes una cuenta?</p>
            <button onclick="mostrarPopup()">Iniciar sesión</button>
        </div>
    </div>
    

    <!-- Ventana emergente de registro -->
    <div id="googlePopup" class="popup-container">
        <div class="popup">
            <span class="close" onclick="cerrarGooglePopup()">&times;</span>
            <h2>Regístrate</h2>
            <div class="input-container">
                <label>Correo electrónico</label>
                <input type="text" id="email-registro">
                <e class="alerta" id="alerta-email">El correo electrónico debe contener un "@"</e>
            </div>

            <div class="input-container password-container">
                <label>Contraseña</label>
                <div class="password-container">
                    <input type="password" id="password-registro">
                    <span class="toggle-Goggle-password" onclick="togglePassword()">👁️</span>
                </div>
                <e class="alerta" id="alerta-longitud">La contraseña debe tener de 4 a 12 caracteres</e>
                <e class="alerta" id="alerta-mayuscula">La contraseña debe tener mayusculas y minusculas</e>
                <e class="alerta" id="alerta-numero">La contraseña debe tener numeros</e>
            </div>

            <button class="submit-btn">Registrarse</button>
        </div>
    </div>


    <!-- Ventana emergente de inicio de sesión -->
    <div id="popup" class="popup-container">
        <div class="popup">
            <span class="close" onclick="cerrarPopup()">&times;</span>
            <h2>Introduce Usuario y Contraseña</h2>
    
            <div class="input-container">
                <label>Correo electrónico</label>
                <input type="text" value="usuario@example.com">
                <e> Correo electronico no existe </e>
            </div>
    
            <div class="input-container password-container">
                <label>Contraseña</label>
                <input type="password" id="password">
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
                <e> Tu contraseña esta incorrecta </e>
            </div>
            <button class="submit-btn" onclick="window.location.href='../html/home.html'">Iniciar sesión</button>
            <p>¿No tienes una cuenta? <a href="#" onclick="registrate()">Regístrate</a></p>
        </div>
    </div>

</body>
</html>

