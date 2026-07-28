<?php

// JWT configuration, kept alongside Database.php as the project's other piece
// of connection/secret config. JwtHelper reads these constants instead of
// owning the secret itself.
//
// Named JwtConfig rather than Jwt: PHP class-name resolution is
// case-insensitive, so a bare "Jwt" here would collide with the
// "use Firebase\JWT\JWT;" import in JwtHelper.php and silently resolve to
// the wrong class.
class JwtConfig
{
    const ALGORITHM = "HS256";

    // TODO: move to an environment variable / secure secret store before
    // production use.
    const SECRET_KEY = "4d9f8a7c2b1e6f5a9d3c8b7e1f4a6d9c2e5b8f1a7d3c6e9f5b2a8d4c1e7f9a";

    const DEFAULT_EXPIRY_SECONDS = 3600;
}
