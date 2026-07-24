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

    if (isset($routes[$uri])) {
        $controllerClass = $routes[$uri];
        $controller = new $controllerClass();
        $result = $controller->handleRequest($method, $input);
    } else {
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
