<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../repositories/RoleRepository.php";

// Refactor: handleRequest() dispatch and field validation now live in BaseController.
// This class only implements the four role-specific actions.
class RoleController extends BaseController
{
    private $roleRepository;

    public function __construct()
    {
        $this->roleRepository = new RoleRepository();
    }

    protected function getAll()
    {
        $roles = $this->roleRepository->getAllRoles();

        return [
            "status" => 200,
            "body" => $roles
        ];
    }

    protected function create($input)
    {
        $error = $this->requireFields($input, ["name"]);

        if ($error) {
            return $error;
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

    protected function update($input)
    {
        $error = $this->requireFields($input, ["id", "name"]);

        if ($error) {
            return $error;
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

    protected function delete($input)
    {
        $error = $this->requireFields($input, ["id"]);

        if ($error) {
            return $error;
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
