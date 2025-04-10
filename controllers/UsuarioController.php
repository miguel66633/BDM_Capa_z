<?php

namespace Controllers;

use Core\Database;
use Models\UsuarioModel;

class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->usuarioModel = new UsuarioModel($db);
    }
}