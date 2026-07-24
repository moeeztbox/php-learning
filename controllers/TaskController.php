<?php

require_once __DIR__ . "/../repositories/TaskRepository.php";

class TaskController
{
    private $taskRepository;

    public function __construct()
    {
        $this->taskRepository = new TaskRepository();
    }

    public function handleRequest($method, $input)
    {
        switch ($method) {

            case "GET":
                return $this->getAll();

            case "POST":
                return $this->create($input);

            case "PUT":
                return $this->update($input);

            case "DELETE":
                return $this->delete($input);

            default:
                return [
                    "status" => 405,
                    "body" => ["message" => "Method Not Allowed"]
                ];
        }
    }

    private function getAll()
    {
        $tasks = $this->taskRepository->getAllTasks();

        return [
            "status" => 200,
            "body" => $tasks
        ];
    }

    private function create($input)
    {
        if (
            !isset($input["project_id"]) ||
            !isset($input["assigned_to"]) ||
            !isset($input["title"]) ||
            !isset($input["description"]) ||
            !isset($input["status"]) ||
            !isset($input["deadline"])
        ) {
            return [
                "status" => 400,
                "body" => ["message" => "project_id, assigned_to, title, description, status and deadline are required"]
            ];
        }

        $created = $this->taskRepository->createTask(
            $input["project_id"],
            $input["assigned_to"],
            $input["title"],
            $input["description"],
            $input["status"],
            $input["deadline"]
        );

        if ($created) {
            return [
                "status" => 201,
                "body" => ["message" => "Task created successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to create task"]
        ];
    }

    private function update($input)
    {
        if (
            !isset($input["id"]) ||
            !isset($input["project_id"]) ||
            !isset($input["assigned_to"]) ||
            !isset($input["title"]) ||
            !isset($input["description"]) ||
            !isset($input["status"]) ||
            !isset($input["deadline"])
        ) {
            return [
                "status" => 400,
                "body" => ["message" => "id, project_id, assigned_to, title, description, status and deadline are required"]
            ];
        }

        $updated = $this->taskRepository->updateTask(
            $input["id"],
            $input["project_id"],
            $input["assigned_to"],
            $input["title"],
            $input["description"],
            $input["status"],
            $input["deadline"]
        );

        if ($updated) {
            return [
                "status" => 200,
                "body" => ["message" => "Task updated successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to update task"]
        ];
    }

    private function delete($input)
    {
        if (!isset($input["id"])) {
            return [
                "status" => 400,
                "body" => ["message" => "id is required"]
            ];
        }

        $deleted = $this->taskRepository->deleteTask($input["id"]);

        if ($deleted) {
            return [
                "status" => 200,
                "body" => ["message" => "Task deleted successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to delete task"]
        ];
    }
}
