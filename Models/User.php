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

    public function searchUsers($searchTerm)
    {
        // Crear la consulta SQL base
        $sql = "SELECT * FROM Usuario";
        $params = [];

        // Si hay un término de búsqueda, añadir un WHERE
        if ($searchTerm) {
            $sql .= " WHERE NombreUsuario LIKE :searchTerm OR Correo LIKE :searchTerm";
            $params = ['searchTerm' => '%' . $searchTerm . '%'];
        }

        // Debug: Verifica la consulta SQL y los parámetros
        echo "Consulta SQL: $sql<br>";  // Muestra la consulta SQL
        var_dump($params);  // Muestra los parámetros de la consulta

        // Ejecutar la consulta
        $this->db->query($sql, $params);

        // Obtener los resultados
        return $this->db->get();
    }
}
