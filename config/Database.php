<?php

class Database
{
    private $host = "localhost";
    private $dbName = "project_management";
    private $username = "root";
    private $password = "";

    private $connection;

    public function connect()
    {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbName}",
                $this->username,
                $this->password
            );

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connection;

        } catch (PDOException $e) {
            die("Connection Failed: " . $e->getMessage());
        }
    }
}