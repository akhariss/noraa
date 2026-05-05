<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;

class CMSPage
{
    public function getByKey(string $pageKey): ?array
    {
        return Database::selectOne(
            "SELECT id, page_key, page_name, is_active, version, updated_by, updated_at
             FROM cms_pages WHERE page_key = :page_key LIMIT 1",
            ['page_key' => $pageKey]
        );
    }

    public function getById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT id, page_key, page_name, is_active, version, updated_by, updated_at
             FROM cms_pages WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function getActive(): array
    {
        return Database::select(
            "SELECT id, page_key, page_name, is_active, version, updated_by, updated_at
             FROM cms_pages WHERE is_active = 1 ORDER BY page_key ASC"
        );
    }

    public function updateVersion(int $id, int $updatedBy): bool
    {
        try {
            Database::execute(
                "UPDATE cms_pages SET version = version + 1, updated_by = :updated_by, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
                ['id' => $id, 'updated_by' => $updatedBy]
            );
            return true;
        } catch (\PDOException $e) {
            \App\Adapters\Logger::error('CMSPage updateVersion failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
