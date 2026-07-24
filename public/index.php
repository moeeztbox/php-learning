<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../helpers/Response.php";
require_once __DIR__ . "/../controllers/ProjectController.php";
require_once __DIR__ . "/../controllers/TaskController.php";
require_once __DIR__ . "/../controllers/UserController.php";
require_once __DIR__ . "/../controllers/RoleController.php";

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

try {

    switch ($uri) {

        case "/projects":
            $controller = new ProjectController();
            $result = $controller->handleRequest($method, $input);
            break;

        case "/tasks":
            $controller = new TaskController();
            $result = $controller->handleRequest($method, $input);
            break;

        case "/users":
            $controller = new UserController();
            $result = $controller->handleRequest($method, $input);
            break;

        case "/roles":
            $controller = new RoleController();
            $result = $controller->handleRequest($method, $input);
            break;

        default:
            $result = [
                "status" => 404,
                "body" => ["message" => "Route Not Found"]
            ];
    }

} catch (Exception $e) {

    $result = [
        "status" => 500,
        "body" => [
            "message" => "Internal Server Error",
            "error" => $e->getMessage()
        ]
    ];
}

Response::json($result["body"], $result["status"]);
