<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class LayananModel
{
    public function findById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT id, nama_layanan, deskripsi, created_at, updated_at FROM layanan WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function getAll(): array
    {
        return Database::select("SELECT id, nama_layanan, deskripsi, created_at, updated_at FROM layanan ORDER BY id ASC");
    }

    public function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO layanan (nama_layanan, deskripsi) VALUES (:nama, :deskripsi)",
            [
                'nama' => $data['nama_layanan'],
                'deskripsi' => $data['deskripsi'] ?? null
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['nama_layanan'])) {
            $fields[] = "nama_layanan = :nama";
            $params['nama'] = $data['nama_layanan'];
        }
        if (isset($data['deskripsi'])) {
            $fields[] = "deskripsi = :deskripsi";
            $params['deskripsi'] = $data['deskripsi'];
        }

        if (empty($fields)) return false;

        return Database::execute("UPDATE layanan SET " . implode(', ', $fields) . " WHERE id = :id", $params);
    }

    public function delete(int $id): bool
    {
        return Database::execute("DELETE FROM layanan WHERE id = :id", ['id' => $id]);
    }
}
