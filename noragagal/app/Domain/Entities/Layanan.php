<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-06: Layanan Model
 */
class Layanan
{
    public function findById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT id, nama_layanan, created_at, updated_at FROM layanan WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function getAll(): array
    {
        return Database::select("SELECT id, nama_layanan, created_at, updated_at FROM layanan ORDER BY id ASC");
    }

    public function create(string $name): ?int
    {
        try {
            return Database::insert(
                "INSERT INTO layanan (nama_layanan) VALUES (:nama_layanan)",
                ['nama_layanan' => $name]
            );
        } catch (\PDOException $e) {
            Logger::error('Layanan create failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function update(int $id, string $name): bool
    {
        try {
            Database::execute(
                "UPDATE layanan SET nama_layanan = :nama_layanan WHERE id = :id",
                ['nama_layanan' => $name, 'id' => $id]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('Layanan update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            Database::execute("DELETE FROM layanan WHERE id = :id", ['id' => $id]);
            return true;
        } catch (\PDOException $e) {
            Logger::error('Layanan delete failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function getReferencesCount(int $id): int
    {
        $result = Database::selectOne(
            "SELECT COUNT(*) as cnt FROM registrasi WHERE layanan_id = :id",
            ['id' => $id]
        );
        return (int)($result['cnt'] ?? 0);
    }

    public function reassignRegistrasi(int $fromId, int $toId): bool
    {
        try {
            Database::execute(
                "UPDATE registrasi SET layanan_id = :to_id WHERE layanan_id = :from_id",
                ['to_id' => $toId, 'from_id' => $fromId]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('Layanan reassign failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
