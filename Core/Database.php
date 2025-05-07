<?php

namespace Core;

use PDO;

class Database
{
    public $connection;
    public $statement;

    public function __construct($config, $username = 'root', $password = '')
    {
        $dsn = 'mysql:' . http_build_query($config, '', ';');

        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($query, $params = [])
    {
        $this->statement = $this->connection->prepare($query);

        $this->statement->execute($params);

        return $this;
    }

    public function get()
    {
        return $this->statement->fetchAll();
    }

    public function find()
    {
        return $this->statement->fetch();
    }

    public function findOrFail()
    {
        $result = $this->find();

        if (!$result) {
            abort();
        }

        return $result;
    }




    /**
     * Ejecuta un procedimiento almacenado y devuelve sus resultados.
     *
     * @param string $procedureName El nombre del procedimiento almacenado.
     * @param array $params Un array de parámetros para el procedimiento almacenado.
     * @return array|bool El resultado del procedimiento (array de filas) o true/false para operaciones sin result set.
     */

    public function callProcedure(string $procedureName, array $params = [])
    {
        try {
            $paramPlaceholders = implode(',', array_fill(0, count($params), '?'));
            $sql = "CALL {$procedureName}({$paramPlaceholders})";
            
            $this->statement = $this->connection->prepare($sql);
            $this->statement->execute(array_values($params));

            // Si el SP devuelve un conjunto de resultados (con SELECT)
            if ($this->statement->columnCount() > 0) {
                $results = $this->statement->fetchAll(PDO::FETCH_ASSOC);
                $this->statement->closeCursor();
                return $results;
            }
            
            $this->statement->closeCursor(); // Importante cerrar el cursor
            return true; // Para SPs que no devuelven conjuntos de resultados explícitos

        } catch (\PDOException $e) { // Añadir la barra invertida \
            // Considera un manejo de errores más robusto o logging
            error_log("Error en callProcedure {$procedureName}: " . $e->getMessage());
            // Podrías relanzar la excepción o devolver false/null
            throw $e; // O return false; dependiendo de cómo quieras manejarlo
        }
    }

}