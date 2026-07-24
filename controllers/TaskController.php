<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../repositories/TaskRepository.php";

// Refactor: handleRequest() dispatch and field validation now live in BaseController.
// This class only implements the four task-specific actions.
class TaskController extends BaseController
{
    private $taskRepository;

    public function __construct()
    {
        $this->taskRepository = new TaskRepository();
    }

    protected function getAll()
    {
        $tasks = $this->taskRepository->getAllTasks();

        return [
            "status" => 200,
            "body" => $tasks
        ];
    }

    protected function create($input)
    {
        $error = $this->requireFields($input, [
            "project_id", "assigned_to", "title", "description", "status", "deadline"
        ]);

        if ($error) {
            return $error;
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

    protected function update($input)
    {
        $error = $this->requireFields($input, [
            "id", "project_id", "assigned_to", "title", "description", "status", "deadline"
        ]);

        if ($error) {
            return $error;
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

    protected function delete($input)
    {
        $error = $this->requireFields($input, ["id"]);

        if ($error) {
            return $error;
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
