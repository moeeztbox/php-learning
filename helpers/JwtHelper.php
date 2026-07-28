<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Wraps firebase/php-jwt so callers never touch the library directly.
// Not wired into any route yet — this only prepares token generation/verification
// for a future JWT-based login flow, alongside (not replacing) session auth.
class JwtHelper
{
    private const ALGORITHM = "HS256";

    // TODO: move this to an environment variable / secure config before this
    // class is actually used, instead of a hardcoded secret.
    private const SECRET_KEY = "change-this-to-a-long-random-secret";

    public static function generateToken(array $payload, $expiresInSeconds = 3600)
    {
        $issuedAt = time();

        $payload["iat"] = $issuedAt;
        $payload["exp"] = $issuedAt + $expiresInSeconds;

        return JWT::encode($payload, self::SECRET_KEY, self::ALGORITHM);
    }

    // Returns the decoded payload as an array, or null if the token is missing,
    // expired, or has an invalid signature.
    public static function verifyToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::SECRET_KEY, self::ALGORITHM));

            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
