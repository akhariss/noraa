<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-06: Klien Model
 */
class Klien
{
    public function findById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT id, nama, hp, created_at FROM klien WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function getAll(): array
    {
        return Database::select("SELECT id, nama, hp, created_at FROM klien ORDER BY nama ASC");
    }

    public function getOrCreate(array $data): int
    {
        try {
            return Database::insert(
                "INSERT INTO klien (nama, hp) VALUES (:nama, :hp)",
                ['nama' => $data['nama'], 'hp' => $data['hp']]
            );
        } catch (\PDOException $e) {
            Logger::error('Klien create failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['nama'])) {
            $fields[] = 'nama = :nama';
            $params['nama'] = $data['nama'];
        }
        if (isset($data['hp'])) {
            $fields[] = 'hp = :hp';
            $params['hp'] = $data['hp'];
        }

        if (empty($fields)) {
            return false;
        }

        try {
            Database::execute("UPDATE klien SET " . implode(', ', $fields) . " WHERE id = :id", $params);
            return true;
        } catch (\PDOException $e) {
            Logger::error('Klien update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
