<?php

require_once __DIR__ . "/../config/Database.php";

class ProjectRepository
{
    private $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->connect();
    }

    // CREATE
    public function createProject($title, $description, $createdBy)
    {
        $sql = "INSERT INTO projects
                (title, description, created_by)
                VALUES
                (:title, :description, :created_by)";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "title" => $title,
            "description" => $description,
            "created_by" => $createdBy
        ]);
    }

    // READ
    public function getAllProjects()
    {
        $sql = "SELECT * FROM projects";

        $statement = $this->connection->prepare($sql);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function updateProject($id, $title, $description, $createdBy)
    {
        $sql = "UPDATE projects
                SET
                    title = :title,
                    description = :description,
                    created_by = :created_by
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id,
            "title" => $title,
            "description" => $description,
            "created_by" => $createdBy
        ]);
    }

    // DELETE
    public function deleteProject($id)
    {
        $sql = "DELETE FROM projects
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id
        ]);
    }
}
