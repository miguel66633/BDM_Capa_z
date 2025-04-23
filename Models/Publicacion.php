<?php

class Publicacion
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function crearPublicacion($usuarioId, $contenido, $imagen = null)
    {
        $query = "INSERT INTO Publicacion (ContenidoPublicacion, UsuarioID, FechaPublicacion) VALUES (:contenido, :usuarioId, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':usuarioId', $usuarioId);

        if ($stmt->execute()) {
            if ($imagen) {
                $publicacionId = $this->db->lastInsertId();
                $rutaImagen = "uploads/publicaciones/{$publicacionId}.jpg";
                move_uploaded_file($imagen, $rutaImagen);
            }
            return true;
        }

        return false;
    }

    public function obtenerPublicaciones()
    {
        $query = "SELECT p.*, u.NombreUsuario, u.ImagenPerfil 
                  FROM Publicacion p 
                  JOIN Usuario u ON p.UsuarioID = u.UsuarioID 
                  ORDER BY p.FechaPublicacion DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}