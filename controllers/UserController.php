<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../repositories/UserRepository.php";
require_once __DIR__ . "/../helpers/PasswordHelper.php";
require_once __DIR__ . "/../helpers/Response.php";

// Refactor: handleRequest() dispatch and field validation now live in BaseController.
// This class only implements the four user-specific actions.
class UserController extends BaseController
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    protected function getAll()
    {
        $users = $this->userRepository->getAllUsers();

        return [
            "status" => 200,
            "body" => $users
        ];
    }

    protected function create($input)
    {
        $error = $this->requireFields($input, ["name", "email", "password", "role_id"]);

        if ($error) {
            return $error;
        }

        $hashedPassword = PasswordHelper::hashPassword($input["password"]);

        $created = $this->userRepository->createUser(
            $input["name"],
            $input["email"],
            $hashedPassword,
            $input["role_id"]
        );

        if ($created) {
            return Response::result(201, "User created successfully");
        }

        return Response::result(500, "Failed to create user");
    }

    protected function update($input)
    {
        $error = $this->requireFields($input, ["id", "name", "email", "password", "role_id"]);

        if ($error) {
            return $error;
        }

        $hashedPassword = PasswordHelper::hashPassword($input["password"]);

        $updated = $this->userRepository->updateUser(
            $input["id"],
            $input["name"],
            $input["email"],
            $hashedPassword,
            $input["role_id"]
        );

        if ($updated) {
            return Response::result(200, "User updated successfully");
        }

        return Response::result(500, "Failed to update user");
    }

    protected function delete($input)
    {
        $error = $this->requireFields($input, ["id"]);

        if ($error) {
            return $error;
        }

        $deleted = $this->userRepository->deleteUser($input["id"]);

        if ($deleted) {
            return Response::result(200, "User deleted successfully");
        }

        return Response::result(500, "Failed to delete user");
    }
}
