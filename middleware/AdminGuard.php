<?php

require_once __DIR__ . "/../repositories/RoleRepository.php";
require_once __DIR__ . "/../helpers/Response.php";

// Role-based guard: SessionAuth only answers "is someone logged in?" - this
// answers "does the logged-in user have the Admin role?". Applied only to the
// specific action(s) that require Admin (currently: DELETE /projects), not to
// every session-protected route.
class AdminGuard
{
    private const ADMIN_ROLE_NAME = "Admin";

    // $input is unused here but kept in the signature so index.php can call
    // every route guard the same way: $guardClass::check($input).
    public static function check($input = null)
    {
        $roleId = $_SESSION["role_id"] ?? null;

        if (!$roleId) {
            return Response::result(403, "Forbidden: Admin role required");
        }

        $roleRepository = new RoleRepository();
        $role = $roleRepository->findRoleById($roleId);

        if (!$role || strcasecmp($role["name"], self::ADMIN_ROLE_NAME) !== 0) {
            return Response::result(403, "Forbidden: Admin role required");
        }

        return null;
    }
}
