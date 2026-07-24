<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../repositories/ProjectRepository.php";

// Refactor: handleRequest() dispatch and field validation now live in BaseController.
// This class only implements the four project-specific actions.
class ProjectController extends BaseController
{
    private $projectRepository;

    public function __construct()
    {
        $this->projectRepository = new ProjectRepository();
    }

    protected function getAll()
    {
        $projects = $this->projectRepository->getAllProjects();

        return [
            "status" => 200,
            "body" => $projects
        ];
    }

    protected function create($input)
    {
        $error = $this->requireFields($input, ["title", "description", "created_by"]);

        if ($error) {
            return $error;
        }

        $created = $this->projectRepository->createProject(
            $input["title"],
            $input["description"],
            $input["created_by"]
        );

        if ($created) {
            return [
                "status" => 201,
                "body" => ["message" => "Project created successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to create project"]
        ];
    }

    protected function update($input)
    {
        $error = $this->requireFields($input, ["id", "title", "description", "created_by"]);

        if ($error) {
            return $error;
        }

        $updated = $this->projectRepository->updateProject(
            $input["id"],
            $input["title"],
            $input["description"],
            $input["created_by"]
        );

        if ($updated) {
            return [
                "status" => 200,
                "body" => ["message" => "Project updated successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to update project"]
        ];
    }

    protected function delete($input)
    {
        $error = $this->requireFields($input, ["id"]);

        if ($error) {
            return $error;
        }

        $deleted = $this->projectRepository->deleteProject($input["id"]);

        if ($deleted) {
            return [
                "status" => 200,
                "body" => ["message" => "Project deleted successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to delete project"]
        ];
    }
}
