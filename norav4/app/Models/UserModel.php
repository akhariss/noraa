<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class UserModel
{
    public function findById(int $id): ?array
    {
        return Database::selectOne("SELECT id, username, name, role, created_at FROM users WHERE id = :id", ['id' => $id]);
    }

    public function findByUsername(string $username): ?array
    {
        return Database::selectOne("SELECT * FROM users WHERE username = :username", ['username' => $username]);
    }

    public function getAll(): array
    {
        return Database::select("SELECT id, username, name, role, created_at FROM users ORDER BY created_at DESC");
    }

    public function create(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return Database::insert(
            "INSERT INTO users (username, name, password_hash, role) VALUES (:username, :name, :hash, :role)",
            [
                'username' => $data['username'],
                'name'     => $data['name'],
                'hash'     => $hash,
                'role'     => $data['role']
            ]
        );
    }
}
