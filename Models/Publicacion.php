<?php

class Publicacion
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Crea una nueva publicación.
     *
     * @param int $usuarioId ID del usuario que crea la publicación.
     * @param string $contenido Contenido textual de la publicación.
     * @param string|null $rutaTemporalImagen Ruta temporal del archivo de imagen/video subido.
     * @param int|null $publicacionPadreId ID de la publicación padre si es un comentario/respuesta.
     * @return array|false Devuelve un array con el resultado del SP o false en caso de error.
     */
    public function crearPublicacion($usuarioId, $contenido, $rutaTemporalImagen = null, $publicacionPadreId = null)
    {
        $contenidoMultimedia = null;
        if ($rutaTemporalImagen && file_exists($rutaTemporalImagen)) {
            $contenidoMultimedia = file_get_contents($rutaTemporalImagen);
        }

        // Llama al Stored Procedure sp_CrearNuevaPublicacion
        // El SP espera: p_UsuarioID, p_ContenidoPublicacion, p_TipoMultimedia (LONGBLOB), p_PublicacionPadreID
        $result = $this->db->callProcedure(
            'sp_CrearNuevaPublicacion',
            [$usuarioId, $contenido, $contenidoMultimedia, $publicacionPadreId]
        );

        // El SP sp_CrearNuevaPublicacion devuelve Success, NewPublicacionID, StatusMessage
        if ($result && isset($result[0]) && $result[0]['Success']) {
            return $result[0]; // Devuelve la respuesta completa del SP
        }
        
        // Loguear el error si el SP falló o no devolvió lo esperado
        error_log("Error al crear publicación. UsuarioID: {$usuarioId}, SP Result: " . print_r($result, true));
        return false;
    }

    /**
     * Obtiene todas las publicaciones principales.
     *
     * @return array Lista de publicaciones.
     */
    public function obtenerPublicaciones()
    {
        // Llama al Stored Procedure sp_ObtenerTodasPublicaciones
        $result = $this->db->callProcedure('sp_ObtenerTodasPublicaciones');
        return $result; // callProcedure ya devuelve el resultado procesado
    }
}