<?php

class Response
{
    public static function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }

    // Builds the ["status" => ..., "body" => ["message" => ...]] shape that
    // controllers, guards, and index.php all return before Response::json()
    // eventually emits it - one place instead of repeating the array literal.
    // Named result() rather than error(): it's used for plain single-message
    // responses regardless of status code (e.g. 201 success, not just 4xx/5xx).
    public static function result($statusCode, $message)
    {
        return [
            "status" => $statusCode,
            "body" => ["message" => $message]
        ];
    }
}
