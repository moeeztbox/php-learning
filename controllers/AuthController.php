<?php

require_once __DIR__ . "/../repositories/UserRepository.php";
require_once __DIR__ . "/../helpers/JwtHelper.php";
require_once __DIR__ . "/../helpers/PasswordHelper.php";

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

        $hashedPassword = PasswordHelper::hashPassword($password);

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
        $result = $this->authenticate($input);

        if (!$result["success"]) {
            return [
                "status" => $result["status"],
                "body" => $result["body"]
            ];
        }

        $user = $result["user"];

        // Regenerate the session ID on every successful login (keeping the same
        // session data) to prevent session fixation: an attacker who planted a
        // session ID on the victim before login can't reuse it as an authenticated
        // session afterward, since the ID changes at the moment of authentication.
        session_regenerate_id(true);

        // Store the minimum identity/authorization data needed by later requests,
        // so subsequent requests can identify and authorize the user via
        // $_SESSION without re-querying credentials on every call.
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role_id"] = $user["role_id"];

        return [
            "status" => 200,
            "body" => [
                "message" => "Login successful",
                "user" => $user
            ]
        ];
    }

    // Alternative to login(): reuses the exact same credential/lockout checks via
    // authenticate(), but issues a JWT instead of starting a session. No $_SESSION
    // write happens here at all.
    public function loginWithJwt($input)
    {
        $result = $this->authenticate($input);

        if (!$result["success"]) {
            return [
                "status" => $result["status"],
                "body" => $result["body"]
            ];
        }

        $user = $result["user"];

        // Only safe, non-sensitive claims go into the token payload.
        // JwtHelper::generateToken() adds iat/exp on top of these.
        $token = JwtHelper::generateToken([
            "user_id" => $user["id"],
            "email" => $user["email"],
            "role_id" => $user["role_id"]
        ]);

        return [
            "status" => 200,
            "body" => [
                "message" => "Login successful",
                "token" => $token
            ]
        ];
    }

    // Shared by login() and loginWithJwt(): validates input, looks up the user,
    // enforces the lockout window, and verifies the password - identical checks
    // regardless of which login method the caller ends up using.
    private function authenticate($input)
    {
        $email = isset($input["email"]) ? trim($input["email"]) : "";
        $password = isset($input["password"]) ? trim($input["password"]) : "";

        if ($email === "" || $password === "") {
            return [
                "success" => false,
                "status" => 400,
                "body" => ["message" => "email and password are required"]
            ];
        }

        $user = $this->userRepository->findUserByEmail($email);

        if (!$user) {
            return [
                "success" => false,
                "status" => 401,
                "body" => ["message" => "Invalid email or password"]
            ];
        }

        if ($this->isAccountLocked($user)) {
            return [
                "success" => false,
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

        if (!PasswordHelper::verifyPassword($password, $user["password"])) {
            $failedAttempts = $user["failed_attempts"] + 1;

            if ($failedAttempts >= self::MAX_FAILED_ATTEMPTS) {
                $lockedUntil = date("Y-m-d H:i:s", strtotime("+" . self::LOCKOUT_MINUTES . " minutes"));

                $this->userRepository->updateFailedAttemptsAndLock($user["id"], $failedAttempts, $lockedUntil);

                return [
                    "success" => false,
                    "status" => 403,
                    "body" => ["message" => "Account locked due to too many failed login attempts"]
                ];
            }

            $this->userRepository->incrementFailedAttempts($user["id"]);

            return [
                "success" => false,
                "status" => 401,
                "body" => ["message" => "Invalid email or password"]
            ];
        }

        // Successful login clears both the attempt counter and any lock in one write.
        $this->userRepository->updateFailedAttemptsAndLock($user["id"], 0, null);

        unset($user["password"]);

        return [
            "success" => true,
            "user" => $user
        ];
    }

    // Protected via JwtAuth in index.php: only reachable with a valid Bearer
    // token, whose decoded claims are passed in as $authenticatedUser.
    public function me($authenticatedUser)
    {
        return [
            "status" => 200,
            "body" => ["user" => $authenticatedUser]
        ];
    }

    public function logout()
    {
        // Clear all session variables first, then destroy the session data and
        // its cookie server-side, fully ending the authenticated session.
        session_unset();
        session_destroy();

        return [
            "status" => 200,
            "body" => ["message" => "Logout successful"]
        ];
    }

    private function isAccountLocked($user)
    {
        return !empty($user["locked_until"]) && strtotime($user["locked_until"]) > time();
    }
}
