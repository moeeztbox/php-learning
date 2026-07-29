<?php

require_once __DIR__ . "/BaseRepository.php";

// Refactor: the PDO-connection constructor moved to BaseRepository; this class now
// only holds role-specific SQL.
class RoleRepository extends BaseRepository
{
    // CREATE
    public function createRole($name)
    {
        $sql = "INSERT INTO roles (name) VALUES (:name)";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "name" => $name
        ]);
    }

    // READ
    public function getAllRoles()
    {
        $sql = "SELECT * FROM roles";

        $statement = $this->connection->prepare($sql);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findRoleById($id)
    {
        $sql = "SELECT id, name FROM roles WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            "id" => $id
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function updateRole($id, $name)
    {
        $sql = "UPDATE roles SET name = :name WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id,
            "name" => $name
        ]);
    }

    // DELETE
    public function deleteRole($id)
    {
        $sql = "DELETE FROM roles WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id
        ]);
    }
}