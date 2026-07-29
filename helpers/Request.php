<?php

// Small wrapper around the raw superglobals/input for the current request, so
// index.php's routing logic isn't mixed with the details of parsing it.
class Request
{
    public static function method()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    // Strips the app's base path and any trailing slash, so route matching in
    // index.php only ever compares against clean paths like "/projects".
    public static function uri($basePath)
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = rtrim($uri, "/");

        return $uri === "" ? "/" : $uri;
    }

    // Decodes the JSON request body, always returning an array (empty when
    // there's no body or it isn't valid JSON) so callers never need their own
    // is_array() guard.
    public static function jsonBody()
    {
        $decoded = json_decode(file_get_contents("php://input"), true);

        return is_array($decoded) ? $decoded : [];
    }
}
