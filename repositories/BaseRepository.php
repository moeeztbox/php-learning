<?php

require_once __DIR__ . "/../config/Database.php";

// Refactor: every repository (User, Role, Project, Task) was repeating the exact same
// constructor to open a Database connection. That logic now lives once here, and each
// repository just extends this class instead of duplicating it (DRY + Reusability).
abstract class BaseRepository
{
    protected $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->connect();
    }
}
