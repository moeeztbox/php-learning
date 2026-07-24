<?php

require_once __DIR__ . "/../config/Database.php";

class TaskRepository
{
    private $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->connect();
    }

    // CREATE
    public function createTask(
        $projectId,
        $assignedTo,
        $title,
        $description,
        $status,
        $deadline
    )
    {
        $sql = "INSERT INTO tasks
                (
                    project_id,
                    assigned_to,
                    title,
                    description,
                    status,
                    deadline
                )
                VALUES
                (
                    :project_id,
                    :assigned_to,
                    :title,
                    :description,
                    :status,
                    :deadline
                )";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "project_id" => $projectId,
            "assigned_to" => $assignedTo,
            "title" => $title,
            "description" => $description,
            "status" => $status,
            "deadline" => $deadline
        ]);
    }

    // READ
    public function getAllTasks()
    {
        $sql = "SELECT * FROM tasks";

        $statement = $this->connection->prepare($sql);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function updateTask(
        $id,
        $projectId,
        $assignedTo,
        $title,
        $description,
        $status,
        $deadline
    )
    {
        $sql = "UPDATE tasks
                SET
                    project_id = :project_id,
                    assigned_to = :assigned_to,
                    title = :title,
                    description = :description,
                    status = :status,
                    deadline = :deadline
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id,
            "project_id" => $projectId,
            "assigned_to" => $assignedTo,
            "title" => $title,
            "description" => $description,
            "status" => $status,
            "deadline" => $deadline
        ]);
    }

    // DELETE
    public function deleteTask($id)
    {
        $sql = "DELETE FROM tasks
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id
        ]);
    }
}
