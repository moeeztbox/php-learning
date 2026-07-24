<?php

require_once __DIR__ . "/../config/Database.php";

class UserRepository
{
    private $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->connect();
    }

    // CREATE
    public function createUser($name, $email, $password, $roleId)
    {
        $sql = "INSERT INTO users
                (name, email, password, role_id)
                VALUES
                (:name, :email, :password, :role_id)";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "role_id" => $roleId
        ]);
    }

    // READ
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";

        $statement = $this->connection->prepare($sql);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function updateUser($id, $name, $email, $password, $roleId)
    {
        $sql = "UPDATE users
                SET
                    name = :name,
                    email = :email,
                    password = :password,
                    role_id = :role_id
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id,
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "role_id" => $roleId
        ]);
    }

    // DELETE
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id
        ]);
    }
}