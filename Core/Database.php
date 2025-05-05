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






    public function callProcedure($procedureName, $params = [])
    {
        // Construir la consulta SQL dinámica con placeholders `?`
        $query = "CALL {$procedureName}(" . implode(', ', array_fill(0, count($params), '?')) . ")";

        // Preparar y ejecutar la consulta con parámetros
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute(array_values($params));

        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

}