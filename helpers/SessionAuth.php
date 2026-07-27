<?php

// Reusable session-auth check, called once per protected route from index.php
// instead of being duplicated inside every controller.
class SessionAuth
{
    // Returns a ready-made 401 response array if the request has no authenticated
    // session, or null when the caller is authenticated and dispatch should continue.
    public static function check()
    {
        if (!isset($_SESSION["user_id"])) {
            return [
                "status" => 401,
                "body" => ["message" => "Unauthorized"]
            ];
        }

        return null;
    }
}
