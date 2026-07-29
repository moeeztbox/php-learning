<?php

require_once __DIR__ . "/../repositories/RoleRepository.php";

// Role-based guard: SessionAuth only answers "is someone logged in?" - this
// answers "does the logged-in user have the Admin role?". Applied only to the
// specific action(s) that require Admin (currently: DELETE /projects), not to
// every session-protected route.
class AdminGuard
{
    private const ADMIN_ROLE_NAME = "Admin";

    public static function check()
    {
        $roleId = $_SESSION["role_id"] ?? null;

        if (!$roleId) {
            return [
                "status" => 403,
                "body" => ["message" => "Forbidden: Admin role required"]
            ];
        }

        $roleRepository = new RoleRepository();
        $role = $roleRepository->findRoleById($roleId);

        if (!$role || strcasecmp($role["name"], self::ADMIN_ROLE_NAME) !== 0) {
            return [
                "status" => 403,
                "body" => ["message" => "Forbidden: Admin role required"]
            ];
        }

        return null;
    }
}
