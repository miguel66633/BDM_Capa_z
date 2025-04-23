<?php

require base_path('Models/Publicacion.php');

class PublicacionController
{
    private $db;
    private $publicacionModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->publicacionModel = new Publicacion($db);
    }

    public function crear()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['user_id'];
        $contenido = $_POST['contenido'] ?? '';
        $imagen = $_FILES['imagen']['tmp_name'] ?? null;

        if (empty($contenido)) {
            $_SESSION['error'] = 'El contenido no puede estar vacío.';
            header('Location: /inicio');
            exit;
        }

        $resultado = $this->publicacionModel->crearPublicacion($usuarioId, $contenido, $imagen);

        if ($resultado) {
            $_SESSION['success'] = 'Publicación creada con éxito.';
        } else {
            $_SESSION['error'] = 'Hubo un problema al crear la publicación.';
        }

        header('Location: /inicio');
    }

    public function index()
    {
        $publicaciones = $this->publicacionModel->obtenerPublicaciones();
        require base_path('views/home.view.php');
    }
}