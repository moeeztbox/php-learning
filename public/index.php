<?php

date_default_timezone_set('Asia/Karachi');

session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "httponly" => true,
    "samesite" => "Lax",
    // TODO: set to true in production, where the app is served over HTTPS.
    "secure" => false
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../helpers/Response.php";
require_once __DIR__ . "/../helpers/SessionAuth.php";
require_once __DIR__ . "/../helpers/JwtAuth.php";
require_once __DIR__ . "/../controllers/ProjectController.php";
require_once __DIR__ . "/../controllers/TaskController.php";
require_once __DIR__ . "/../controllers/UserController.php";
require_once __DIR__ . "/../controllers/RoleController.php";
require_once __DIR__ . "/../controllers/AuthController.php";

$method = $_SERVER["REQUEST_METHOD"];

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$basePath = "/php-learning/public";

if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

$uri = rtrim($uri, "/");

if ($uri === "") {
    $uri = "/";
}

$rawInput = file_get_contents("php://input");
$input = json_decode($rawInput, true);

if (!is_array($input)) {
    $input = [];
}

// Refactor: route map replaces the old switch statement. Each URI maps to the
// controller class responsible for it, so adding a new resource is a one-line
// addition here instead of a new switch case in this file.
$routes = [
    "/projects" => "ProjectController",
    "/tasks" => "TaskController",
    "/users" => "UserController",
    "/roles" => "RoleController",
];

try {

    // Auth routes are handled separately from $routes: both are POST-only actions
    // on their own paths, not a GET/POST/PUT/DELETE resource, so they don't go
    // through handleRequest().
    if ($uri === "/signup" || $uri === "/login" || $uri === "/logout" || $uri === "/login/jwt") {
        if ($method !== "POST") {
            $result = [
                "status" => 405,
                "body" => ["message" => "Method Not Allowed"]
            ];
        } else {
            $authController = new AuthController();

            if ($uri === "/signup") {
                $result = $authController->signup($input);
            } elseif ($uri === "/login") {
                $result = $authController->login($input);
            } elseif ($uri === "/login/jwt") {
                $result = $authController->loginWithJwt($input);
            } else {
                $result = $authController->logout();
            }
        }
    } elseif ($uri === "/me") {
        // Protected via JWT rather than the session guard: requires a valid
        // Bearer token in the Authorization header instead of $_SESSION.
        if ($method !== "GET") {
            $result = [
                "status" => 405,
                "body" => ["message" => "Method Not Allowed"]
            ];
        } else {
            $jwtResult = JwtAuth::check();

            if (!$jwtResult["success"]) {
                $result = [
                    "status" => $jwtResult["status"],
                    "body" => $jwtResult["body"]
                ];
            } else {
                $authController = new AuthController();
                $result = $authController->me($jwtResult["user"]);
            }
        }
    } elseif (isset($routes[$uri])) {
        // Every non-auth route requires an authenticated session. Checked once
        // here instead of inside each controller, so the check isn't duplicated
        // across ProjectController, TaskController, UserController, RoleController.
        $authError = SessionAuth::check();

        if ($authError) {
            $result = $authError;
        } else {
            $controllerClass = $routes[$uri];
            $controller = new $controllerClass();
            $result = $controller->handleRequest($method, $input);
        }
    } else {
        $result = [
            "status" => 404,
            "body" => ["message" => "Route Not Found"]
        ];
    }

} catch (Exception $e) {

    // Log the real error server-side only; the client only ever sees a generic
    // message, since exception text can contain DB/connection details.
    error_log($e->getMessage());

    $result = [
        "status" => 500,
        "body" => ["message" => "Internal Server Error"]
    ];
}

Response::json($result["body"], $result["status"]);
