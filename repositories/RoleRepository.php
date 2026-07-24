<?php

require_once __DIR__ . "/BaseRepository.php";

// Refactor: the PDO-connection constructor moved to BaseRepository; this class now
// only holds role-specific SQL.
class RoleRepository extends BaseRepository
{
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