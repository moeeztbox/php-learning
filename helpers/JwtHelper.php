<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/JwtConfig.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// JWT service: wraps firebase/php-jwt so callers never touch the library
// directly. Configuration (secret, algorithm, expiry) lives in config/Jwt.php,
// not here — this class only implements encode/decode behavior.
class JwtHelper
{
    public static function generateToken(array $payload, $expiresInSeconds = null)
    {
        $expiresInSeconds = $expiresInSeconds ?? JwtConfig::DEFAULT_EXPIRY_SECONDS;
        $issuedAt = time();

        $payload["iat"] = $issuedAt;
        $payload["exp"] = $issuedAt + $expiresInSeconds;

        return JWT::encode($payload, JwtConfig::SECRET_KEY, JwtConfig::ALGORITHM);
    }

    // Returns the decoded payload as an array, or null if the token is missing,
    // expired, or has an invalid signature.
    public static function verifyToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(JwtConfig::SECRET_KEY, JwtConfig::ALGORITHM));

            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
