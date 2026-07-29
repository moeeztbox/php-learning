<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../repositories/TaskRepository.php";
require_once __DIR__ . "/../helpers/Response.php";

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
            return Response::result(201, "Task created successfully");
        }

        return Response::result(500, "Failed to create task");
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
            return Response::result(200, "Task updated successfully");
        }

        return Response::result(500, "Failed to update task");
    }

    protected function delete($input)
    {
        $error = $this->requireFields($input, ["id"]);

        if ($error) {
            return $error;
        }

        $deleted = $this->taskRepository->deleteTask($input["id"]);

        if ($deleted) {
            return Response::result(200, "Task deleted successfully");
        }

        return Response::result(500, "Failed to delete task");
    }
}
