<?php

require_once __DIR__ . "/../repositories/ProjectRepository.php";

class ProjectController
{
    private $projectRepository;

    public function __construct()
    {
        $this->projectRepository = new ProjectRepository();
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
        $projects = $this->projectRepository->getAllProjects();

        return [
            "status" => 200,
            "body" => $projects
        ];
    }

    private function create($input)
    {
        if (
            !isset($input["title"]) ||
            !isset($input["description"]) ||
            !isset($input["created_by"])
        ) {
            return [
                "status" => 400,
                "body" => ["message" => "title, description and created_by are required"]
            ];
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

    private function update($input)
    {
        if (
            !isset($input["id"]) ||
            !isset($input["title"]) ||
            !isset($input["description"]) ||
            !isset($input["created_by"])
        ) {
            return [
                "status" => 400,
                "body" => ["message" => "id, title, description and created_by are required"]
            ];
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

    private function delete($input)
    {
        if (!isset($input["id"])) {
            return [
                "status" => 400,
                "body" => ["message" => "id is required"]
            ];
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
