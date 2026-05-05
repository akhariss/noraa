<?php
declare(strict_types=1);

namespace Modules\CMS;

use Modules\Auth\Controller as AuthController;

/**
 * SK-14: CMSEditorController
 * Slim controller for CMS editor
 */

use Exception;
use App\Domain\Entities\CMSPage;
use App\Domain\Entities\MessageTemplate;
use App\Domain\Entities\NoteTemplate;
use App\Domain\Entities\Layanan;
use App\Services\CMSEditorService;

class Controller
{
    private CMSEditorService $cmsService;

    public function __construct()
    {
        $this->cmsService = new CMSEditorService();
    }

    /**
     * Show CMS editor main page (grid menu)
     */
    public function index(): void
    {
        requireRole(ROLE_OWNER);

        $currentUser = getCurrentUser();
        $auth = new AuthController();
        $pageTitle = 'CMS Editor';
        $activePage = 'cms';

        // Check if requesting a specific page (page as primary, tab as fallback for legacy redirects)
        $page = $_GET['page'] ?? $_GET['tab'] ?? null;

        if ($page) {
            // Route to specific page
            $this->renderPage($page);
            return;
        }

        // Show grid menu
        $cmsStats = $this->getStats();
        
        // DEBUG: Log that we're about to load view
        error_log("CMS Editor: Loading grid view. currentUser=" . print_r($currentUser, true));
        
        require VIEWS_PATH . '/dashboard/cms_editor_grid.php';
    }

    /**
     * Get stats for grid page
     */
    private function getStats(): array
    {
        $modelLayanan = new Layanan();
        $modelMessage = new MessageTemplate();
        $modelNote = new NoteTemplate();
        
        return [
            'layanan' => $modelLayanan->getAll(),
            'templates' => $modelMessage->getAll(),
            'catatan' => $modelNote->getAll()
        ];
    }

    /**
     * Render specific page
     */
    private function renderPage(string $page): void
    {
        $currentUser = getCurrentUser();
        $auth = new AuthController();
        
        switch ($page) {
            case 'beranda':
                $pageData = $this->loadTabData('beranda');
                require VIEWS_PATH . '/dashboard/cms_editor_beranda.php';
                break;
                
            case 'layanan':
                $layanan = $this->loadTabData('layanan')['layanan'] ?? [];
                require VIEWS_PATH . '/dashboard/cms_editor_layanan.php';
                break;
                
            case 'pesan':
                $templates = $this->loadTabData('pesan')['templates'] ?? [];
                require VIEWS_PATH . '/dashboard/cms_editor_pesan.php';
                break;
                
            case 'catatan':
                $templates = $this->loadTabData('catatan')['templates'] ?? [];
                $statusLabels = $this->loadTabData('catatan')['statusLabels'] ?? getStatusLabels();
                require VIEWS_PATH . '/dashboard/cms_editor_catatan.php';
                break;
                
            case 'settings':
                $pageData = $this->loadTabData('settings');
                require VIEWS_PATH . '/dashboard/cms_editor_settings.php';
                break;
                
            case 'workflow':
                $wfModel = new \App\Domain\Entities\WorkflowStep();
                $workflowSteps = $wfModel->getAll();
                $behaviorMap = $wfModel->getBehaviorMap();
                require VIEWS_PATH . '/dashboard/cms_editor_workflow.php';
                break;
                
            default:
                // Redirect to grid if page not found
                header('Location: ' . APP_URL . '/index.php?gate=cms_editor');
                exit;
        }
    }

    /**
     * Load data for active tab
     */
    private function loadTabData(string $tab): array
    {
        switch ($tab) {
            case 'beranda':
                return $this->cmsService
                    ->getPageForEditing('home');

            case 'layanan':
                $model = new Layanan();
                return [
                    'layanan' => $model->getAll()
                ];

            case 'pesan':
                $model = new MessageTemplate();
                return [
                    'templates' => $model->getAll()
                ];

            case 'catatan':
                $model = new NoteTemplate();
                return [
                    'templates' => $model->getAll(),
                    'statusLabels' => getStatusLabels()
                ];

            case 'settings':
                return $this->cmsService
                    ->getAppSettings();

            default:
                return [];
        }
    }

    /**
     * Show CMS editor view for homepage (legacy)
     */
    public function editHome(): void
    {
        requireRole(ROLE_OWNER);
        // Redirect to tabbed editor
        header('Location: ' . APP_URL
            . '/index.php?gate=cms_editor&tab=beranda');
        exit;
    }

    /**
     * Show CMS editor view for messages (legacy)
     */
    public function editMessages(): void
    {
        requireRole(ROLE_OWNER);
        header('Location: ' . APP_URL
            . '/index.php?gate=cms_editor&tab=pesan');
        exit;
    }

    /**
     * API: Update content value (LAW 2.3: 5s timeout)
     */
    public function updateContent(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            return;
        }

        $user = getCurrentUser();
        if (!$user || $user['role'] !== ROLE_OWNER) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $contentId = (int)($_POST['content_id'] ?? 0);
        $value = trim($_POST['value'] ?? '');

        if ($contentId <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid content ID'
            ]);
            return;
        }

        $result = $this->cmsService
            ->updateContent($contentId, $value);
        echo json_encode($result);
    }

    /**
     * API: Update item (LAW 2.3: 5s timeout)
     */
    public function updateItem(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            return;
        }

        $user = getCurrentUser();
        if (!$user || $user['role'] !== ROLE_OWNER) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $itemId = (int)($_POST['item_id'] ?? 0);

        if ($itemId <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid item ID'
            ]);
            return;
        }

        $data = [];
        $allowedFields = ['title', 'description'];
        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = trim($_POST[$field]);
            }
        }

        if (empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'No fields to update'
            ]);
            return;
        }

        $result = $this->cmsService
            ->updateItem($itemId, $data);
        echo json_encode($result);
    }

    /**
     * API: Save message template
     */
    public function saveMessageTemplate(): void
    {
        header('Content-Type: application/json');

        $user = getCurrentUser();
        if (!$user || $user['role'] !== ROLE_OWNER) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $id = (int)($_POST['template_id'] ?? 0);
        $body = trim($_POST['template_body'] ?? '');

        if ($id <= 0 || empty($body)) {
            echo json_encode([
                'success' => false,
                'message' => 'Data tidak valid'
            ]);
            return;
        }

        $model = new MessageTemplate();
        $ok = $model->updateBody(
            $id, $body, (int)$user['user_id']
        );

        echo json_encode([
            'success' => $ok,
            'message' => $ok
            ? 'Template pesan berhasil disimpan'
            : 'Gagal menyimpan template'
        ]);
    }

    /**
     * API: Save note template
     */
    public function saveNoteTemplate(): void
    {
        header('Content-Type: application/json');

        $user = getCurrentUser();
        if (!$user || $user['role'] !== ROLE_OWNER) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Session expired.']);
            return;
        }

        $statusKey = trim($_POST['status_key'] ?? '');
        $body = trim($_POST['template_body'] ?? '');

        if (empty($statusKey)) {
            echo json_encode([
                'success' => false,
                'message' => 'Status Key tidak ditemukan.'
            ]);
            return;
        }

        $model = new NoteTemplate();
        
        // Elite Upsert Logic: Handle creation if ID doesn't exist
        $existing = $model->getByStatusKey($statusKey);
        
        if ($existing) {
            $ok = $model->updateBody(
                (int)$existing['id'], $body, (int)$user['user_id']
            );
        } else {
            $ok = $model->create([
                'status_key' => $statusKey,
                'template_body' => $body,
                'created_by' => (int)$user['user_id']
            ]);
        }

        echo json_encode([
            'success' => $ok !== false,
            'message' => $ok !== false
            ? 'Template catatan berhasil disimpan ✨'
            : 'Gagal menyimpan template ke database.'
        ]);
    }

    /**
     * API: Add new layanan
     */
    public function addLayanan(): void
    {
        header('Content-Type: application/json');

        $user = getCurrentUser();
        if (!$user || $user['role'] !== ROLE_OWNER) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $name = trim($_POST['nama_layanan'] ?? '');
        if (empty($name) || mb_strlen($name) > 100) {
            echo json_encode([
                'success' => false,
                'message' => 'Nama layanan tidak valid (maks 100 karakter)'
            ]);
            return;
        }

        $model = new Layanan();
        $newId = $model->create($name);

        echo json_encode([
            'success' => $newId !== false,
            'message' => $newId !== false
            ? 'Layanan berhasil ditambahkan'
            : 'Gagal menambahkan layanan',
            'id' => $newId
        ]);
    }

    /**
     * API: Update layanan
     */
    public function updateLayanan(): void
    {
        header('Content-Type: application/json');

        $user = getCurrentUser();
        if (!$user || $user['role'] !== ROLE_OWNER) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['nama_layanan'] ?? '');

        if ($id <= 1 || empty($name) || mb_strlen($name) > 100) {
            echo json_encode([
                'success' => false,
                'message' => $id === 1 ? 'Layanan utama tidak boleh diubah.' : 'Data tidak valid'
            ]);
            return;
        }

        $model = new Layanan();
        $ok = $model->update($id, $name);

        echo json_encode([
            'success' => $ok,
            'message' => $ok
            ? 'Layanan berhasil diperbarui'
            : 'Gagal memperbarui layanan'
        ]);
    }

    /**
     * API: Delete layanan
     */
    public function deleteLayanan(): void
    {
        header('Content-Type: application/json');

        $user = getCurrentUser();
        if (!$user || $user['role'] !== ROLE_OWNER) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        // LAW 4.2: Prevent deleting ID 1 (protected)
        if ($id <= 1) {
            echo json_encode([
                'success' => false,
                'message' => $id === 1 ? 'Layanan utama tidak boleh dihapus.' : 'ID tidak valid'
            ]);
            return;
        }

        $model = new Layanan();
        
        // Check references count
        $refCount = $model->getReferencesCount($id);
        
        if ($refCount > 0) {
            // Reassign to ID 1 instead of blocking
            $reassigned = $model->reassignRegistrasi($id, 1);
            
            if (!$reassigned) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal mengalihkan registrasi ke layanan utama.'
                ]);
                return;
            }
            
            // Now delete the layanan
            $ok = $model->delete($id);
            
            echo json_encode([
                'success' => $ok,
                'message' => $ok
                ? "Layanan dihapus. {$refCount} registrasi dialihkan ke layanan utama."
                : 'Gagal menghapus layanan.',
                'reassigned_count' => $refCount
            ]);
            return;
        }

        // No references, just delete
        $ok = $model->delete($id);

        echo json_encode([
            'success' => $ok,
            'message' => $ok
            ? 'Layanan berhasil dihapus'
            : 'Gagal menghapus layanan'
        ]);
    }

    /**
     * API: Save app settings
     */
    public function saveAppSettings(): void
    {
        header('Content-Type: application/json');

        $user = getCurrentUser();
        if (!$user || $user['role'] !== ROLE_OWNER) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $updates = $_POST['settings'] ?? [];
        if (empty($updates) || !is_array($updates)) {
            echo json_encode([
                'success' => false,
                'message' => 'No settings to update'
            ]);
            return;
        }

        $result = $this->cmsService
            ->updateAppSettings($updates);
        echo json_encode($result);
    }

    /**
     * API: Get all note templates as JSON
     * Used by perkara_detail and perkara_create
     */
    public function getNoteTemplatesJson(): void
    {
        header('Content-Type: application/json');

        $user = getCurrentUser();
        if (!$user) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $model = new NoteTemplate();
        $templates = $model->getAll();
        
        $workflowModel = new \App\Domain\Entities\WorkflowStep();
        $steps = $workflowModel->getAll();
        
        // Elite Sync v5.31: Map with behavior info
        $map = [];
        $behaviorMap = [];
        foreach ($templates as $row) {
            $map[$row['status_key']] = $row['template_body'];
            
            // Get behavior for this step
            foreach ($steps as $s) {
                if ($s['step_key'] === $row['status_key']) {
                    if ((int)$s['behavior_role'] === 5) {
                        $behaviorMap['handover'] = $row['template_body'];
                    }
                    break;
                }
            }
        }
        
        $sla = [];
        foreach ($steps as $s) {
            $sla[$s['step_key']] = (int)$s['sla_days'];
        }

        echo json_encode([
            'success'   => true,
            'templates' => $map,
            'handover_tpl' => $behaviorMap['handover'] ?? null,
            'sla'       => $sla
        ]);
    }

    /**
     * API: Get message template by key
     * Used by perkara_detail and perkara_create
     */
    public function getMessageTemplate(): void
    {
        header('Content-Type: application/json');

        $user = getCurrentUser();
        if (!$user) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Silakan login kembali.'
            ]);
            return;
        }

        $key = trim($_GET['key'] ?? '');
        if (empty($key)) {
            echo json_encode([
                'success' => false,
                'message' => 'Key required'
            ]);
            return;
        }

        $model = new MessageTemplate();
        $tpl = $model->getByKey($key);
        echo json_encode([
            'success' => $tpl !== null,
            'template' => $tpl
        ]);
    }
    /**
     * Show CMS editor layanan page (called by Router)
     */
    public function editLayananPage(): void
    {
        requireRole(ROLE_OWNER);
        $user = getCurrentUser();
        $pageTitle = 'Editor - Layanan';
        $activePage = 'cms_layanan';
        require VIEWS_PATH . '/dashboard/cms_editor_layanan.php';
    }

    /**
     * Legacy CMS redirect.
     */
    public function legacyCmsRedirect(): void
    {
        header('Location: ' . APP_URL . '/index.php?gate=cms_edit_home');
        exit;
    }

    // ═══════════════════════════════════════════════════════════════
    // WORKFLOW STEPS CMS ACTIONS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Save (Create or Update) a workflow step
     */
    public function saveWorkflowStep(): void
    {
        header('Content-Type: application/json');
        requireRole(ROLE_OWNER);

        if (!\verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Session expired. Silakan login kembali.']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $stepKey = trim($_POST['step_key'] ?? '');
        $label = trim($_POST['label'] ?? '');
        $slaDays = (int)($_POST['sla_days'] ?? 0);
        $behaviorRole = (int)($_POST['behavior_role'] ?? 1);
        $isCancellable = (int)($_POST['is_cancellable'] ?? 0);

        if (empty($stepKey) || empty($label)) {
            echo json_encode(['success' => false, 'message' => 'Step Key dan Label wajib diisi.']);
            return;
        }

        // Sanitize step_key: lowercase, no spaces
        $stepKey = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $stepKey));

        $wfModel = new \App\Domain\Entities\WorkflowStep();

        // Check step_key uniqueness
        if ($wfModel->keyExists($stepKey, $id)) {
            echo json_encode(['success' => false, 'message' => "Step Key '$stepKey' sudah digunakan."]);
            return;
        }

        // Behavior role 'Smart Transfer' Logic
        $uniqueBehaviors = [0, 3, 4, 5, 6, 7, 8];
        if (in_array($behaviorRole, $uniqueBehaviors, true)) {
            // Find any other step using this unique role and reset it to 'Normal Progress' (1)
            \App\Adapters\Database::execute(
                "UPDATE workflow_steps SET behavior_role = 1 WHERE behavior_role = ? AND id != ?",
                [$behaviorRole, $id]
            );
        }

        $data = [
            'step_key'       => $stepKey,
            'label'          => $label,
            'sla_days'       => $slaDays,
            'behavior_role'  => $behaviorRole,
            'is_cancellable' => $isCancellable,
        ];

        try {
            if ($id > 0) {
                // Update
                $wfModel->update($id, $data);
                echo json_encode(['success' => true, 'message' => "Step '$label' berhasil diperbarui."]);
            } else {
                // Create - auto sort_order
                $data['sort_order'] = $wfModel->getMaxSortOrder() + 1;
                $newId = $wfModel->create($data);
                echo json_encode(['success' => true, 'message' => "Step '$label' berhasil ditambahkan.", 'id' => $newId]);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a workflow step
     */
    public function deleteWorkflowStep(): void
    {
        header('Content-Type: application/json');
        requireRole(ROLE_OWNER);

        if (!\verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Session expired.']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
            return;
        }

        $wfModel = new \App\Domain\Entities\WorkflowStep();

        // 🛡️ ANTI-ORPHAN LOGIC (Data Yatim Prevention)
        // 1. Find the evacuation destination (Behavior 3: Perbaikan)
        $perbaikanStep = $wfModel->findByBehavior(3);
        if (!$perbaikanStep) {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: Tahapan "Perbaikan" (Behavior 3) tidak ditemukan sebagai jalur evakuasi data.']);
            return;
        }
        $fallbackId = (int)$perbaikanStep['id'];

        // Prevent deleting the evacuation point itself
        if ($id === $fallbackId) {
            echo json_encode(['success' => false, 'message' => 'Tahapan "Perbaikan" adalah jalur sistem utama dan tidak boleh dihapus.']);
            return;
        }

        try {
            // 2. Perform Mass Evacuation (Current Status & History)
            // Move active registrations
            \App\Adapters\Database::execute(
                "UPDATE registrasi SET current_step_id = :fallback WHERE current_step_id = :old",
                ['fallback' => $fallbackId, 'old' => $id]
            );
            
            // Move history entries (Corrected Column Names: status_old_id & status_new_id)
            \App\Adapters\Database::execute(
                "UPDATE registrasi_history SET status_old_id = :fallback WHERE status_old_id = :old",
                ['fallback' => $fallbackId, 'old' => $id]
            );
            \App\Adapters\Database::execute(
                "UPDATE registrasi_history SET status_new_id = :fallback WHERE status_new_id = :old",
                ['fallback' => $fallbackId, 'old' => $id]
            );

            // 3. Finally delete the now-empty step
            $wfModel->delete($id);
            
            $destLabel = $perbaikanStep['label'] ?? 'Perbaikan';
            echo json_encode([
                'success' => true, 
                'message' => "Tahapan berhasil dihapus secara permanen. Semua data aktif dan riwayat telah dialihkan ke tahap \"{$destLabel}\"."
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    /**
     * Reorder workflow steps (drag-and-drop)
     */
    public function reorderWorkflowSteps(): void
    {
        header('Content-Type: application/json');
        requireRole(ROLE_OWNER);

        if (!\verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Session expired.']);
            return;
        }

        $rawOrder = $_POST['order'] ?? '';
        // CRITICAL FIX: Decode HTML entities if server auto-escapes POST (e.g. &quot; -> ")
        $rawOrder = htmlspecialchars_decode($rawOrder);
        $orderData = json_decode($rawOrder, true);
        
        // Debug logging
        file_put_contents(LOGS_PATH . '/order_debug.log', 
            "[" . date('Y-m-d H:i:s') . "] POST: " . json_encode($_POST) . "\n" .
            "[" . date('Y-m-d H:i:s') . "] Raw: $rawOrder | Decoded: " . json_encode($orderData) . "\n", 
            FILE_APPEND
        );

        if (empty($orderData) || !is_array($orderData)) {
            echo json_encode(['success' => false, 'message' => 'Data urutan tidak valid. (Debug: Check logs)']);
            return;
        }

        $wfModel = new \App\Domain\Entities\WorkflowStep();
        
        // Build [id => sort_order] map
        $orderedIds = [];
        foreach ($orderData as $index => $id) {
            $orderedIds[(int)$id] = $index + 1;
        }

        try {
            $success = $wfModel->reorder($orderedIds);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Urutan berhasil diperbarui.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui urutan di database.']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
