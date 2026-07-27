<?php

require_once __DIR__ . "/../repositories/UserRepository.php";

// Standalone controller: signup/login don't map to the GET/POST/PUT/DELETE-per-resource
// shape BaseController dispatches (both are POST, on two different routes), so this
// controller is not extended from BaseController and defines its own entry points.
class AuthController
{
    private $userRepository;

    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function signup($input)
    {
        if (
            !isset($input["name"]) ||
            !isset($input["email"]) ||
            !isset($input["password"])
        ) {
            return [
                "status" => 400,
                "body" => ["message" => "name, email and password are required"]
            ];
        }

        $existingUser = $this->userRepository->findUserByEmail($input["email"]);

        if ($existingUser) {
            return [
                "status" => 400,
                "body" => ["message" => "Email is already registered"]
            ];
        }

        $hashedPassword = password_hash($input["password"], PASSWORD_DEFAULT);

        // role_id is not part of the public signup contract; pass through if given,
        // otherwise leave it null (no default-role assumption is made here).
        $created = $this->userRepository->createUser(
            $input["name"],
            $input["email"],
            $hashedPassword,
            $input["role_id"] ?? null
        );

        if ($created) {
            return [
                "status" => 201,
                "body" => ["message" => "User registered successfully"]
            ];
        }

        return [
            "status" => 500,
            "body" => ["message" => "Failed to register user"]
        ];
    }

    public function login($input)
    {
        if (!isset($input["email"]) || !isset($input["password"])) {
            return [
                "status" => 400,
                "body" => ["message" => "email and password are required"]
            ];
        }

        $user = $this->userRepository->findUserByEmail($input["email"]);

        if (!$user) {
            return [
                "status" => 401,
                "body" => ["message" => "Invalid email or password"]
            ];
        }

        if (!empty($user["locked_until"]) && strtotime($user["locked_until"]) > time()) {
            return [
                "status" => 403,
                "body" => ["message" => "Account is locked. Please try again later."]
            ];
        }

        if (!password_verify($input["password"], $user["password"])) {
            $failedAttempts = $user["failed_attempts"] + 1;

            if ($failedAttempts >= self::MAX_FAILED_ATTEMPTS) {
                $lockedUntil = date("Y-m-d H:i:s", strtotime("+" . self::LOCKOUT_MINUTES . " minutes"));

                $this->userRepository->updateFailedAttemptsAndLock($user["id"], $failedAttempts, $lockedUntil);

                return [
                    "status" => 403,
                    "body" => ["message" => "Account locked due to too many failed login attempts"]
                ];
            }

            $this->userRepository->incrementFailedAttempts($user["id"]);

            return [
                "status" => 401,
                "body" => ["message" => "Invalid email or password"]
            ];
        }

        $this->userRepository->resetFailedAttempts($user["id"]);

        unset($user["password"]);

        return [
            "status" => 200,
            "body" => [
                "message" => "Login successful",
                "user" => $user
            ]
        ];
    }
}
