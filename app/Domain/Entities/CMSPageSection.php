<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;

class CMSPageSection
{
    public function getByPageId(int $pageId): array
    {
        return Database::select(
            "SELECT id, page_id, section_key, section_name, section_order, is_active, updated_at
             FROM cms_page_sections WHERE page_id = :page_id ORDER BY section_order ASC",
            ['page_id' => $pageId]
        );
    }

    public function getById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT id, page_id, section_key, section_name, section_order, is_active, updated_at
             FROM cms_page_sections WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function getByPageIdAndKey(int $pageId, string $sectionKey): ?array
    {
        return Database::selectOne(
            "SELECT id, page_id, section_key, section_name, section_order, is_active, updated_at
             FROM cms_page_sections WHERE page_id = :page_id AND section_key = :section_key LIMIT 1",
            ['page_id' => $pageId, 'section_key' => $sectionKey]
        );
    }
}
