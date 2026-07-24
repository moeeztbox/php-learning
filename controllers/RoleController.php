<?php

require_once __DIR__ . "/../repositories/RoleRepository.php";

class RoleController
{
    private $roleRepository;

    public function __construct()
    {
        $this->roleRepository = new RoleRepository();
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
        $roles = $this->roleRepository->getAllRoles();

        return [
            "status" => 200,
            "body" => $roles
        ];
    }

    private function create($input)
    {
        if (!isset($input["name"])) {
            return [
                "status" => 400,
                "body" => ["message" => "name is required"]
            ];
        }

        $created = $this->roleRepository->createRole($input["name"]);

        if ($created) {
            return [
                "status" => 201,
                "body" => ["message" => "Role created successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to create role"]
        ];
    }

    private function update($input)
    {
        if (!isset($input["id"]) || !isset($input["name"])) {
            return [
                "status" => 400,
                "body" => ["message" => "id and name are required"]
            ];
        }

        $updated = $this->roleRepository->updateRole($input["id"], $input["name"]);

        if ($updated) {
            return [
                "status" => 200,
                "body" => ["message" => "Role updated successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to update role"]
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

        $deleted = $this->roleRepository->deleteRole($input["id"]);

        if ($deleted) {
            return [
                "status" => 200,
                "body" => ["message" => "Role deleted successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to delete role"]
        ];
    }
}
