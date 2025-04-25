<?php

namespace Core\Middleware;

class Admin
{
    public function handle()
    {
        // Iniciar la sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar si el usuario ha iniciado sesión y es administrador
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 2) {
            // Redirigir a la pantalla de inicio si no es administrador
            header('Location: /inicio');
            exit();
        }

        // Si es administrador, permitir el acceso (no se hace nada)
    }
}