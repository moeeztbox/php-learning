<?php

require_once __DIR__ . "/../repositories/UserRepository.php";

// Standalone controller: signup/login don't map to the GET/POST/PUT/DELETE-per-resource
// shape BaseController dispatches (both are POST, on two different routes), so this
// controller is not extended from BaseController and defines its own entry points.
class AuthController
{
    private $userRepository;

    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 10;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function signup($input)
    {
        $name = isset($input["name"]) ? trim($input["name"]) : "";
        $email = isset($input["email"]) ? trim($input["email"]) : "";
        $password = isset($input["password"]) ? trim($input["password"]) : "";

        if ($name === "" || $email === "" || $password === "") {
            return [
                "status" => 400,
                "body" => ["message" => "name, email and password are required"]
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                "status" => 400,
                "body" => ["message" => "A valid email address is required"]
            ];
        }

        if (strlen($password) < 8) {
            return [
                "status" => 400,
                "body" => ["message" => "Password must be at least 8 characters long"]
            ];
        }

        $existingUser = $this->userRepository->findUserByEmail($email);

        if ($existingUser) {
            return [
                "status" => 400,
                "body" => ["message" => "Email is already registered"]
            ];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // role_id is not part of the public signup contract; pass through if given,
        // otherwise leave it null (no default-role assumption is made here).
        $created = $this->userRepository->createUser(
            $name,
            $email,
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
        $email = isset($input["email"]) ? trim($input["email"]) : "";
        $password = isset($input["password"]) ? trim($input["password"]) : "";

        if ($email === "" || $password === "") {
            return [
                "status" => 400,
                "body" => ["message" => "email and password are required"]
            ];
        }

        $user = $this->userRepository->findUserByEmail($email);

        if (!$user) {
            return [
                "status" => 401,
                "body" => ["message" => "Invalid email or password"]
            ];
        }

        if ($this->isAccountLocked($user)) {
            return [
                "status" => 403,
                "body" => ["message" => "Account is locked. Please try again later."]
            ];
        }

        // A previous lock has expired: clear it before judging this attempt, so
        // failed_attempts carried over from the last lockout doesn't immediately
        // re-lock the account on the very next try.
        if (!empty($user["locked_until"])) {
            $this->userRepository->updateFailedAttemptsAndLock($user["id"], 0, null);
            $user["failed_attempts"] = 0;
            $user["locked_until"] = null;
        }

        if (!password_verify($password, $user["password"])) {
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

        // Successful login clears both the attempt counter and any lock in one write.
        $this->userRepository->updateFailedAttemptsAndLock($user["id"], 0, null);

        unset($user["password"]);

        return [
            "status" => 200,
            "body" => [
                "message" => "Login successful",
                "user" => $user
            ]
        ];
    }

    private function isAccountLocked($user)
    {
        return !empty($user["locked_until"]) && strtotime($user["locked_until"]) > time();
    }
}
