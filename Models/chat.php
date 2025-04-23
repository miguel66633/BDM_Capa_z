<?php

class Chat
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function obtenerChatsPorUsuario($usuarioId)
    {
        $query = "
            SELECT 
                c.ChatID,
                u.UsuarioID AS DestinatarioID,
                u.NombreUsuario,
                u.ImagenPerfil,
                c.FechaCreacion
            FROM 
                Chat c
            INNER JOIN 
                Usuario u ON (u.UsuarioID = c.DestinatarioID AND c.UsuarioID = :usuarioId)
                OR (u.UsuarioID = c.UsuarioID AND c.DestinatarioID = :usuarioId)
            ORDER BY c.FechaCreacion DESC;
        ";

        return $this->db->query($query, ['usuarioId' => $usuarioId])->get();
    }
}