<?php

namespace Toniette\QueryBuilder;

use Exception;
use PDO;

class QueryBuilder
{

    private PDO $pdo;
    private string $query;

    public function __construct(
        private string $dbname,
        private string $username = "root",
        private string $password = "",
        private string $driver = "mysql",
        private string $host = "localhost",
        private int $port = 3306,
        private array $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    )
    {
        try {
            $this->pdo = new PDO(
                $this->driver .
                ":host=" . $this->host .
                ";dbname=" . $this->dbname .
                ";port=" . $this->port,
                $this->username,
                $this->password,
                $this->options
            );
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function select(array $columns = ["*"])
    {
        $this->query = "SELECT ";
        foreach ($columns as $key => $value) {
            $this->query .= ($key != array_key_last($columns)) ? "{$value}, " : "`{$value}`";
        }
    }

    public function from(string $entity)
    {
        $this->query .= " FROM {$entity}";
    }

    public function where(string $column, string $operator, mixed $value)
    {
        if (!str_contains($this->query, "WHERE")) {
            $this->query .= " WHERE ";
        }
        $this->query .= "`{$column}` {$operator} `{$value}`";
    }

    public function or()
    {
        $this->query .= " OR ";
    }

    public function and()
    {
        $this->query .= " AND ";
    }

    public function update(string $entity, array $arguments)
    {
        $this->query = "UPDATE {$entity} SET ";
        foreach ($arguments as $key => $value) {
            $this->query .= "{$key} = {$value}";
            if($key != array_key_last($arguments)) {
                $this->query .= ", ";
            }
        }
    }

    public function truncate(string $entity)
    {
        $this->query = "TRUNCATE TABLE {$entity}";
    }
}