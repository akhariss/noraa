<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

class CMSSectionItem
{
    public function getBySectionId(int $sectionId): array
    {
        return Database::select(
            "SELECT id, section_id, item_type, title, description, extra_data, sort_order, is_active, updated_at
             FROM cms_section_items WHERE section_id = :section_id ORDER BY sort_order ASC",
            ['section_id' => $sectionId]
        );
    }

    public function getBySectionAndType(int $sectionId, string $type): array
    {
        return Database::select(
            "SELECT id, section_id, item_type, title, description, extra_data, sort_order, is_active, updated_at
             FROM cms_section_items WHERE section_id = :section_id AND item_type = :item_type AND is_active = 1
             ORDER BY sort_order ASC",
            ['section_id' => $sectionId, 'item_type' => $type]
        );
    }

    public function getById(int $id): ?array
    {
        return Database::selectOne(
            "SELECT id, section_id, item_type, title, description, extra_data, sort_order, is_active, updated_at
             FROM cms_section_items WHERE id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['title'])) {
            $fields[] = 'title = :title';
            $params['title'] = $data['title'];
        }
        if (isset($data['description'])) {
            $fields[] = 'description = :description';
            $params['description'] = $data['description'];
        }

        if (empty($fields)) {
            return false;
        }

        try {
            Database::execute("UPDATE cms_section_items SET " . implode(', ', $fields) . " WHERE id = :id", $params);
            return true;
        } catch (\PDOException $e) {
            Logger::error('CMSSectionItem update failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
