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
            // Refactor: throw instead of die(). die() printed a plain-text string and
            // stopped execution immediately, bypassing index.php's try/catch and breaking
            // the "JSON responses only" contract. Throwing lets the existing try/catch in
            // index.php turn this into a proper JSON 500 response instead.
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
}