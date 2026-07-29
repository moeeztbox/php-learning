<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../repositories/RoleRepository.php";
require_once __DIR__ . "/../helpers/Response.php";

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
            return Response::result(201, "Role created successfully");
        }

        return Response::result(500, "Failed to create role");
    }

    protected function update($input)
    {
        $error = $this->requireFields($input, ["id", "name"]);

        if ($error) {
            return $error;
        }

        $updated = $this->roleRepository->updateRole($input["id"], $input["name"]);

        if ($updated) {
            return Response::result(200, "Role updated successfully");
        }

        return Response::result(500, "Failed to update role");
    }

    protected function delete($input)
    {
        $error = $this->requireFields($input, ["id"]);

        if ($error) {
            return $error;
        }

        $deleted = $this->roleRepository->deleteRole($input["id"]);

        if ($deleted) {
            return Response::result(200, "Role deleted successfully");
        }

        return Response::result(500, "Failed to delete role");
    }
}
