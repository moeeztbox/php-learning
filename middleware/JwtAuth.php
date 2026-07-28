<?php

require_once __DIR__ . "/../helpers/JwtHelper.php";

// JWT counterpart to SessionAuth: guards routes that authenticate via a Bearer
// token instead of a PHP session. Unlike SessionAuth, the authenticated user's
// identity isn't sitting in a superglobal, so check() hands back the decoded
// claims on success instead of just null.
class JwtAuth
{
    public static function check()
    {
        $authHeader = self::getAuthorizationHeader();

        if (!$authHeader || stripos($authHeader, "Bearer ") !== 0) {
            return [
                "success" => false,
                "status" => 401,
                "body" => ["message" => "Unauthorized"]
            ];
        }

        $token = trim(substr($authHeader, 7));

        $payload = JwtHelper::verifyToken($token);

        if (!$payload) {
            return [
                "success" => false,
                "status" => 401,
                "body" => ["message" => "Invalid or expired token"]
            ];
        }

        return [
            "success" => true,
            "user" => $payload
        ];
    }

    private static function getAuthorizationHeader()
    {
        if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
            return $_SERVER["HTTP_AUTHORIZATION"];
        }

        // Some Apache/PHP setups don't populate HTTP_AUTHORIZATION in
        // $_SERVER even when the header was sent, so fall back to reading
        // it directly from the request headers.
        if (function_exists("getallheaders")) {
            foreach (getallheaders() as $name => $value) {
                if (strtolower($name) === "authorization") {
                    return $value;
                }
            }
        }

        return null;
    }
}
