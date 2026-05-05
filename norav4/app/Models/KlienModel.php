<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class KlienModel
{
    public function findById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT id, nama, hp, email, created_at FROM klien WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function getAll(): array
    {
        return Database::select("SELECT id, nama, hp, email, created_at FROM klien ORDER BY nama ASC");
    }

    public function getOrCreate(array $data): int
    {
        // Try to find by HP first to avoid duplicates
        $existing = Database::selectOne("SELECT id FROM klien WHERE hp = :hp LIMIT 1", ['hp' => $data['hp']]);
        if ($existing) {
            return (int)$existing['id'];
        }

        return Database::insert(
            "INSERT INTO klien (nama, hp, email) VALUES (:nama, :hp, :email)",
            [
                'nama' => $data['nama'], 
                'hp' => $data['hp'],
                'email' => $data['email'] ?? null
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        $allowed = ['nama', 'hp', 'email'];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        return Database::execute("UPDATE klien SET " . implode(', ', $fields) . " WHERE id = :id", $params);
    }
}
