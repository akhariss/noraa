<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;

/**
 * SK-06: User Model
 * No SELECT *, prepared statements only.
 */
class User
{
    /**
     * Find user by username (includes password_hash for auth).
     */
    public function findByUsername(string $username): ?array
    {
        return Database::selectOne(
            "SELECT id, username, name, password_hash, role, created_at, updated_at
             FROM users WHERE username = :username LIMIT 1",
            ['username' => $username]
        );
    }

    /**
     * Find user by ID (no password_hash).
     */
    public function findById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT id, username, name, role, created_at, updated_at
             FROM users WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    /**
     * Verify password.
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Create new user.
     */
    public function create(array $data): bool
    {
        try {
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            Database::insert(
                "INSERT INTO users (username, name, password_hash, role)
                 VALUES (:username, :name, :password_hash, :role)",
                [
                    'username'      => $data['username'],
                    'name'          => $data['name'] ?? $data['username'],
                    'password_hash' => $passwordHash,
                    'role'          => $data['role'],
                ]
            );
            return true;
        } catch (\PDOException $e) {
            \App\Adapters\Logger::error('User create failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Update user.
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['username'])) {
            $fields[] = 'username = :username';
            $params['username'] = $data['username'];
        }
        if (isset($data['name'])) {
            $fields[] = 'name = :name';
            $params['name'] = $data['name'];
        }
        if (isset($data['password'])) {
            $fields[] = 'password_hash = :password_hash';
            $params['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        if (isset($data['role'])) {
            $fields[] = 'role = :role';
            $params['role'] = $data['role'];
        }

        if (empty($fields)) {
            return false;
        }

        try {
            Database::execute(
                "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id",
                $params
            );
            return true;
        } catch (\PDOException $e) {
            \App\Adapters\Logger::error('User update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Delete user.
     */
    public function delete(int $id): bool
    {
        try {
            Database::execute("DELETE FROM users WHERE id = :id", ['id' => $id]);
            return true;
        } catch (\PDOException $e) {
            \App\Adapters\Logger::error('User delete failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get all users.
     */
    public function getAll(): array
    {
        return Database::select(
            "SELECT id, username, name, role, created_at, updated_at
             FROM users ORDER BY created_at DESC"
        );
    }

    /**
     * Reset password.
     */
    public function resetPassword(int $id, string $newPassword): bool
    {
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            Database::execute(
                "UPDATE users SET password_hash = :password_hash WHERE id = :id",
                ['password_hash' => $passwordHash, 'id' => $id]
            );
            return true;
        } catch (\PDOException $e) {
            \App\Adapters\Logger::error('Password reset failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
