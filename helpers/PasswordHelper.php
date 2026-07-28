<?php

// Single place responsible for password hashing/verification, so no
// controller calls password_hash()/password_verify() directly.
class PasswordHelper
{
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
