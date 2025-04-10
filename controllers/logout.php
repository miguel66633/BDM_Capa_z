<?php
session_start(); // Asegúrate de iniciar la sesión antes de destruirla
session_destroy(); // Destruir la sesión actual

// Redirigir a la página /Z
header("Location: /Z");
exit();