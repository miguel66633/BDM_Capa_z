<?php

namespace App\Models;

use Core\Database;

class User
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Searches for users by name or email.
     *
     * @param string|null $searchTerm The term to search for. If null or empty, all users are returned (if SP is modified as suggested).
     * @return array An array of users matching the search term. Each user array will contain UsuarioID, NombreUsuario, Correo, ImagenPerfil.
     */
    public function searchUsers($searchTerm)
    {
        $result = $this->db->callProcedure(
            'sp_BuscarUsuariosLateral',
            [$searchTerm] // Pasar el término de búsqueda directamente
        );

        return $result; // callProcedure ya devuelve el resultado procesado (array de arrays asociativos)
    }
}