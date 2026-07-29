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
require_once __DIR__ . "/../helpers/Request.php";
require_once __DIR__ . "/../middleware/SessionAuth.php";
require_once __DIR__ . "/../middleware/JwtAuth.php";
require_once __DIR__ . "/../middleware/AdminGuard.php";
require_once __DIR__ . "/../middleware/TaskOwnershipGuard.php";
require_once __DIR__ . "/../controllers/ProjectController.php";
require_once __DIR__ . "/../controllers/TaskController.php";
require_once __DIR__ . "/../controllers/UserController.php";
require_once __DIR__ . "/../controllers/RoleController.php";
require_once __DIR__ . "/../controllers/AuthController.php";

$method = Request::method();
$uri = Request::uri("/php-learning/public");
$input = Request::jsonBody();

// Each route declares its controller and any per-method guards, so adding a
// new resource or a new guarded action is a config entry here instead of a
// new elseif branch or another ad-hoc $xError ternary.
$routes = [
    "/projects" => [
        "controller" => "ProjectController",
        "guards" => [
            "DELETE" => ["AdminGuard"],
        ],
    ],
    "/tasks" => [
        "controller" => "TaskController",
        "guards" => [
            "PUT" => ["TaskOwnershipGuard"],
        ],
    ],
    "/users" => ["controller" => "UserController", "guards" => []],
    "/roles" => ["controller" => "RoleController", "guards" => []],
];

try {

    // Auth routes are handled separately from $routes: these are POST-only
    // actions on their own paths, not a GET/POST/PUT/DELETE resource, so they
    // don't go through handleRequest().
    if ($uri === "/signup" || $uri === "/login" || $uri === "/logout" || $uri === "/login/jwt") {
        if ($method !== "POST") {
            $result = Response::result(405, "Method Not Allowed");
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
            $result = Response::result(405, "Method Not Allowed");
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
        $route = $routes[$uri];

        // Every non-auth route requires an authenticated session. Checked once
        // here instead of inside each controller, so the check isn't duplicated
        // across ProjectController, TaskController, UserController, RoleController.
        $authError = SessionAuth::check();

        if ($authError) {
            $result = $authError;
        } else {
            $guardError = null;

            // Route- and method-specific guards, e.g. AdminGuard on
            // DELETE /projects, TaskOwnershipGuard on PUT /tasks. Every guard
            // is called the same way (check($input)) and returns null to
            // allow the request through, or an error result to stop it.
            foreach ($route["guards"][$method] ?? [] as $guardClass) {
                $guardError = $guardClass::check($input);

                if ($guardError) {
                    break;
                }
            }

            if ($guardError) {
                $result = $guardError;
            } else {
                $controllerClass = $route["controller"];
                $controller = new $controllerClass();
                $result = $controller->handleRequest($method, $input);
            }
        }
    } else {
        $result = Response::result(404, "Route Not Found");
    }

} catch (Exception $e) {

    // Log the real error server-side only; the client only ever sees a generic
    // message, since exception text can contain DB/connection details.
    error_log($e->getMessage());

    $result = Response::result(500, "Internal Server Error");
}

Response::json($result["body"], $result["status"]);
