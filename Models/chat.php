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
        // Llama al Stored Procedure
        $result = $this->db->callProcedure('sp_ObtenerChatsPorUsuario', [$usuarioId]);
        return $result;
    }
}