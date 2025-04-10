<?php

class UsuarioModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerUsuarioPorCorreo($correo) {
        try {
            $query = "SELECT * FROM Usuario WHERE Correo = :correo";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }

    public function crearUsuario($datos) {
        try {
            $query = "CALL sp_AdministrarUsuario(
                'INSERT', 
                NULL,
                :nombreUsuario,
                :correo,
                :passwordUsu,
                :biografia,
                :imagenPerfil,
                :bannerPerfil,
                :tipoUsuario
            )";

            $stmt = $this->conn->prepare($query);
            
            // Bind de los parámetros necesarios según tu BD
            $stmt->bindParam(':nombreUsuario', $datos['nombreUsuario']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':passwordUsu', $datos['passwordUsu']);
            $stmt->bindParam(':biografia', $datos['biografia']);
            $stmt->bindParam(':imagenPerfil', $datos['imagenPerfil']);
            $stmt->bindParam(':bannerPerfil', $datos['bannerPerfil']);
            $stmt->bindParam(':tipoUsuario', $datos['tipoUsuario']);

            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al crear usuario: " . $e->getMessage());
        }
    }

    public function actualizarIntentosFallidos($usuarioId, $intentos) {
        try {
            $query = "UPDATE Usuario SET intentos_fallidos = :intentos 
                     WHERE UsuarioID = :usuarioId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':intentos', $intentos, PDO::PARAM_INT);
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar intentos: " . $e->getMessage());
        }
    }
}