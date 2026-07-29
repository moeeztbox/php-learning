<?php

require_once __DIR__ . "/../repositories/TaskRepository.php";
require_once __DIR__ . "/../repositories/RoleRepository.php";

// Ownership guard for updating tasks: Admin and Manager may update any task;
// any other role (e.g. Employee) may only update a task whose assigned_to
// matches the logged-in user. Mirrors AdminGuard's role-lookup pattern, but
// additionally checks the specific task's ownership.
class TaskOwnershipGuard
{
    private const PRIVILEGED_ROLES = ["Admin", "Manager"];

    public static function check($input)
    {
        $userId = $_SESSION["user_id"] ?? null;
        $roleId = $_SESSION["role_id"] ?? null;

        if (!$userId || !$roleId) {
            return self::forbidden();
        }

        $roleRepository = new RoleRepository();
        $role = $roleRepository->findRoleById($roleId);

        if ($role && in_array($role["name"], self::PRIVILEGED_ROLES, true)) {
            return null;
        }

        // A missing "id" is left to the controller's own field validation
        // (400 "id, ... are required"), not treated as an ownership failure.
        if (!isset($input["id"])) {
            return null;
        }

        $taskRepository = new TaskRepository();
        $task = $taskRepository->findTaskById($input["id"]);

        if (!$task || (int) $task["assigned_to"] !== (int) $userId) {
            return self::forbidden();
        }

        return null;
    }

    private static function forbidden()
    {
        return [
            "status" => 403,
            "body" => ["message" => "Forbidden: you can only update tasks assigned to you"]
        ];
    }
}
