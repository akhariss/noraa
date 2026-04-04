<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

class CMSSectionContent
{
    public function getBySectionId(int $sectionId): array
    {
        return Database::select(
            "SELECT id, section_id, content_key, content_value, content_type, sort_order
             FROM cms_section_content WHERE section_id = :section_id ORDER BY sort_order ASC",
            ['section_id' => $sectionId]
        );
    }

    public function getByKey(int $sectionId, string $key): ?array
    {
        return Database::selectOne(
            "SELECT id, section_id, content_key, content_value, content_type, sort_order
             FROM cms_section_content WHERE section_id = :section_id AND content_key = :content_key LIMIT 1",
            ['section_id' => $sectionId, 'content_key' => $key]
        );
    }

    public function getContentById(int $contentId): ?array
    {
        return Database::selectOne(
            "SELECT id, section_id, content_key, content_value, content_type, sort_order
             FROM cms_section_content WHERE id = :id LIMIT 1",
            ['id' => $contentId]
        );
    }

    public function update(int $id, string $value): bool
    {
        try {
            Database::execute(
                "UPDATE cms_section_content SET content_value = :content_value WHERE id = :id",
                ['id' => $id, 'content_value' => $value]
            );
            return true;
        } catch (\PDOException $e) {
            Logger::error('CMSSectionContent update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
