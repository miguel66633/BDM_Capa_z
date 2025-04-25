<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión en Z</title>
    <link rel="stylesheet" href="css/inicioSesion.css">
    <link rel="icon" href="Resources/images/logo-Z.ico" type="image/x-icon">

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
            <form id="registerForm" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="full-name">Nombre de usuario:</label>
                <input type="text" id="full-name" name="nombre_completo">
                <div class="error-message" id="error-fullname"></div>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="correo">
                <div class="error-message" id="error-email"></div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="contrasena">
                <div class="error-message" id="error-password"></div>
            </div>

                <button type="submit" class="submit-btn">Registrarse</button>
            </form>
        </div>
    </div>

    <!-- Ventana emergente de inicio de sesión -->
    <div id="popup" class="popup-container">
    <div class="popup">
        <span class="close" onclick="cerrarPopup()">&times;</span>
        <h2>Inicia sesión</h2>
        <form id="loginForm" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="email-login">Correo electrónico:</label>
                <input type="email" id="email-login" name="correo">
                <div class="error-message" id="error-login-email"></div>
            </div>

            <div class="form-group">
                <label for="password-login">Contraseña:</label>
                <input type="password" id="password-login" name="contrasena">
                <div class="error-message" id="error-login-password"></div>
            </div>

            <button type="submit" class="submit-btn">Iniciar sesión</button>
        </form>
    </div>
</div>

    <script src="js/inicioSesion.js"></script>

</body>
</html>

