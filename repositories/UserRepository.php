<?php

require_once __DIR__ . "/BaseRepository.php";

// Refactor: the PDO-connection constructor moved to BaseRepository; this class now
// only holds user-specific SQL.
class UserRepository extends BaseRepository
{
    // CREATE
    public function createUser($name, $email, $password, $roleId)
    {
        $sql = "INSERT INTO users
                (name, email, password, role_id)
                VALUES
                (:name, :email, :password, :role_id)";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "role_id" => $roleId
        ]);
    }

    // READ
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";

        $statement = $this->connection->prepare($sql);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            "email" => $email
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE
    public function updateUser($id, $name, $email, $password, $roleId)
    {
        $sql = "UPDATE users
                SET
                    name = :name,
                    email = :email,
                    password = :password,
                    role_id = :role_id
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id,
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "role_id" => $roleId
        ]);
    }

    // DELETE
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id
        ]);
    }

    // LOGIN LOCKOUT
    public function incrementFailedAttempts($id)
    {
        $sql = "UPDATE users
                SET failed_attempts = failed_attempts + 1
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id
        ]);
    }

    public function resetFailedAttempts($id)
    {
        $sql = "UPDATE users
                SET failed_attempts = 0
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id
        ]);
    }

    public function lockUser($id, $lockedUntil)
    {
        $sql = "UPDATE users
                SET locked_until = :locked_until
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id,
            "locked_until" => $lockedUntil
        ]);
    }

    public function updateFailedAttemptsAndLock($id, $failedAttempts, $lockedUntil)
    {
        $sql = "UPDATE users
                SET
                    failed_attempts = :failed_attempts,
                    locked_until = :locked_until
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            "id" => $id,
            "failed_attempts" => $failedAttempts,
            "locked_until" => $lockedUntil
        ]);
    }
}