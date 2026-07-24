<?php

require_once __DIR__ . "/../config/Database.php";

class RoleRepository
{
    private $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->connect();
    }

    public function createRole($name)
    {
       $sql = "INSERT INTO roles (name)
        VALUES (?)";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([$name]);
    }

    public function getAllRoles()
    {
        $sql = "SELECT * FROM roles";

        $statement = $this->connection->prepare($sql);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateRole($id, $name)
    {
         $sql = "UPDATE roles
                 SET name = ?
                 WHERE id = ?";

         $statement = $this->connection->prepare($sql);

        return $statement->execute([
        $name,
        $id
        ]);
    }
    public function deleteRole($id)
    {
         $sql = "DELETE FROM roles
            WHERE id = ?";

         $statement = $this->connection->prepare($sql);

    return $statement->execute([$id]);
    }
}