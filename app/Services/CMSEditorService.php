<?php
declare(strict_types=1);

namespace App\Services;

use App\Domain\Entities\CMSPage;
use App\Domain\Entities\CMSPageSection;
use App\Domain\Entities\CMSSectionContent;
use App\Domain\Entities\CMSSectionItem;
use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-14: CMSEditorService
 */
class CMSEditorService
{
    private CMSPage $pageModel;
    private CMSPageSection $sectionModel;
    private CMSSectionContent $contentModel;
    private CMSSectionItem $itemModel;
    private const MAX_ITEMS = 2000;

    public function __construct()
    {
        $this->pageModel = new CMSPage();
        $this->sectionModel = new CMSPageSection();
        $this->contentModel = new CMSSectionContent();
        $this->itemModel = new CMSSectionItem();
    }

    public function getPageForEditing(string $pageKey): array
    {
        $page = $this->pageModel->getByKey($pageKey);
        if (!$page) {
            return ['error' => 'Page not found', 'status' => 404];
        }

        $sections = $this->sectionModel->getByPageId($page['id']);
        $result = ['page' => $page, 'sections' => []];

        foreach ($sections as $section) {
            $contentRows = $this->contentModel->getBySectionId($section['id']);
            $itemRows = $this->itemModel->getBySectionId($section['id']);

            if (count($itemRows) > self::MAX_ITEMS) {
                Logger::info("Section {$section['id']} items exceeds MAX_ITEMS");
            }

            $content = [];
            foreach ($contentRows as $row) {
                $content[$row['content_key']] = [
                    'id'    => $row['id'],
                    'value' => $row['content_value'],
                    'type'  => $row['content_type'],
                ];
            }

            $result['sections'][$section['section_key']] = [
                'id'            => $section['id'],
                'section_key'   => $section['section_key'],
                'section_name'  => $section['section_name'],
                'section_order' => $section['section_order'],
                'content'       => $content,
                'items'         => $itemRows,
            ];
        }

        return $result;
    }

    public function getContentById(int $contentId): ?array
    {
        return $this->contentModel->getContentById($contentId);
    }

    public function updateContent(int $contentId, string $newValue): array
    {
        try {
            $updated = $this->contentModel->update($contentId, $newValue);
            return $updated
                ? ['success' => true, 'message' => 'Content updated']
                : ['success' => false, 'message' => 'Update failed'];
        } catch (\Exception $e) {
            Logger::error('updateContent failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Update error'];
        }
    }

    public function updateItem(int $itemId, array $data): array
    {
        try {
            $item = $this->itemModel->getById($itemId);
            if (!$item) {
                return ['success' => false, 'message' => 'Item not found'];
            }

            $allowed = ['title', 'description'];
            $filtered = array_intersect_key($data, array_flip($allowed));

            if (empty($filtered)) {
                return ['success' => false, 'message' => 'No valid fields'];
            }

            $updated = $this->itemModel->update($itemId, $filtered);
            return $updated
                ? ['success' => true, 'message' => 'Item updated']
                : ['success' => false, 'message' => 'Update failed'];
        } catch (\Exception $e) {
            Logger::error('updateItem failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Update error'];
        }
    }

    public function getAppSettings(): array
    {
        $settings = [];

        $profileRows = $this->contentModel->getBySectionId(6);
        foreach ($profileRows as $row) {
            $settings['profile'][$row['content_key']] = [
                'id' => $row['id'], 'value' => $row['content_value'], 'type' => $row['content_type'],
            ];
        }

        $contactRows = $this->contentModel->getBySectionId(8);
        foreach ($contactRows as $row) {
            $settings['contact'][$row['content_key']] = [
                'id' => $row['id'], 'value' => $row['content_value'], 'type' => $row['content_type'],
            ];
        }

        $badgeRows = $this->contentModel->getBySectionId(1);
        foreach ($badgeRows as $row) {
            if ($row['content_key'] === 'badge') {
                $settings['badge'] = [
                    'id' => $row['id'], 'value' => $row['content_value'], 'type' => $row['content_type'],
                ];
            }
        }

        return $settings;
    }

    public function updateAppSettings(array $updates): array
    {
        try {
            Database::beginTransaction();
            foreach ($updates as $id => $value) {
                $cid = (int)$id;
                if ($cid <= 0) {
                    continue;
                }
                $this->contentModel->update($cid, trim((string)$value));
            }
            Database::commit();
            return ['success' => true, 'message' => 'Settings berhasil disimpan'];
        } catch (\Exception $e) {
            Database::rollback();
            Logger::error('updateAppSettings failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal menyimpan settings'];
        }
    }

    public function restoreDefaults(string $pageKey, string $sectionKey): array
    {
        return ['success' => true, 'message' => 'Restore feature coming soon'];
    }
}
