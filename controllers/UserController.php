<?php

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../repositories/UserRepository.php";

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

        $created = $this->userRepository->createUser(
            $input["name"],
            $input["email"],
            $input["password"],
            $input["role_id"]
        );

        if ($created) {
            return [
                "status" => 201,
                "body" => ["message" => "User created successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to create user"]
        ];
    }

    protected function update($input)
    {
        $error = $this->requireFields($input, ["id", "name", "email", "password", "role_id"]);

        if ($error) {
            return $error;
        }

        $updated = $this->userRepository->updateUser(
            $input["id"],
            $input["name"],
            $input["email"],
            $input["password"],
            $input["role_id"]
        );

        if ($updated) {
            return [
                "status" => 200,
                "body" => ["message" => "User updated successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to update user"]
        ];
    }

    protected function delete($input)
    {
        $error = $this->requireFields($input, ["id"]);

        if ($error) {
            return $error;
        }

        $deleted = $this->userRepository->deleteUser($input["id"]);

        if ($deleted) {
            return [
                "status" => 200,
                "body" => ["message" => "User deleted successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to delete user"]
        ];
    }
}
