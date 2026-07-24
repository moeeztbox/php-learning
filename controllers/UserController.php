<?php

require_once __DIR__ . "/../repositories/UserRepository.php";

class UserController
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
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
        $users = $this->userRepository->getAllUsers();

        return [
            "status" => 200,
            "body" => $users
        ];
    }

    private function create($input)
    {
        if (
            !isset($input["name"]) ||
            !isset($input["email"]) ||
            !isset($input["password"]) ||
            !isset($input["role_id"])
        ) {
            return [
                "status" => 400,
                "body" => ["message" => "name, email, password and role_id are required"]
            ];
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

    private function update($input)
    {
        if (
            !isset($input["id"]) ||
            !isset($input["name"]) ||
            !isset($input["email"]) ||
            !isset($input["password"]) ||
            !isset($input["role_id"])
        ) {
            return [
                "status" => 400,
                "body" => ["message" => "id, name, email, password and role_id are required"]
            ];
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

    private function delete($input)
    {
        if (!isset($input["id"])) {
            return [
                "status" => 400,
                "body" => ["message" => "id is required"]
            ];
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
