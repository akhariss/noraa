<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\RegistrasiModel;
use App\Models\KlienModel;
use App\Models\LayananModel;
use App\Services\WorkflowService;
use App\Services\RegistrasiService;
use App\Core\Database;

/**
 * DashboardController - Slim, modular, and professional
 */
class DashboardController
{
    private RegistrasiModel $registrasi;
    private KlienModel $klien;
    private LayananModel $layanan;
    private WorkflowService $workflow;
    private RegistrasiService $registrasiService;

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $contentView = VIEWS_PATH . '/' . $view . '.php';
        require VIEWS_PATH . '/layouts/main.php';
    }

    public function __construct()
    {
        // Auth Check for all methods in this controller
        if (!Auth::check()) {
            redirect('/login');
        }

        $this->registrasi = new RegistrasiModel();
        $this->klien = new KlienModel();
        $this->layanan = new LayananModel();
        $this->workflow = new WorkflowService();
        $this->registrasiService = new RegistrasiService();
    }

    /**
     * Dashboard Overview (War Room)
     */
    public function index(): void
    {
        // Follow V3 architecture: no separate War Room dashboard, directly go to data registrasi
        redirect('/registrasi');
    }

    /**
     * List Registrasi
     */
    public function registrasi(): void
    {
        $filters = [
            'search'  => $_GET['search'] ?? '',
            'status'  => $_GET['status'] ?? '',
            'layanan' => $_GET['layanan'] ?? '',
            'flag'    => $_GET['flag'] ?? '',
            'tab'     => $_GET['tab'] ?? 'semua',
            'order'   => $_GET['order'] ?? 'terbaru',
            'bayar'   => $_GET['bayar'] ?? '',
            'periode' => $_GET['periode'] ?? ''
        ];

        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        if (isset($_GET['ajax_export']) && $_GET['ajax_export'] === '1') {
            $allExportItems = $this->registrasi->getPaginated($filters, 10000, 0); // Get virtually all matched data
            
            // Clean output buffer to ensure pure JSON
            if (ob_get_length()) ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $allExportItems,
                'total' => count($allExportItems)
            ]);
            exit;
        }

        $items = $this->registrasi->getPaginated($filters, $limit, $offset);
        
        // V4 Strict Requirement: Accurate Total Pagination
        $total = $this->registrasi->countPaginated($filters);
        $totalPages = ceil($total / $limit);

        // Required by V3 view
        $layanan = $this->layanan->getAll();
        $allSteps = \App\Core\Database::select("SELECT id, step_key, label FROM workflow_steps ORDER BY sort_order ASC");

        $this->render('dashboard/registrasi', [
            'title' => 'Data Registrasi - ' . APP_NAME,
            'activeTab' => 'registrasi',
            'registrasiWithFlags' => $items,
            'layanan' => $layanan,
            'allSteps' => $allSteps,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $filters['search'],
            'filterStatus' => $filters['status'],
            'filterLayanan' => $filters['layanan'],
            'filterFlag' => $filters['flag'],
            'filterOrder' => $filters['order'],
            'filterTab' => $filters['tab'],
            'filterBayar' => $filters['bayar'],
            'filterPeriode' => $filters['periode']
        ]);
    }

    /**
     * Create Page
     */
    public function create(): void
    {
        $kliens = $this->klien->getAll();
        $layanans = $this->layanan->getAll();
        
        // Get initial workflow step
        $defaultStep = Database::selectOne("SELECT * FROM workflow_steps WHERE behavior_role = 0 LIMIT 1");
        
        // Calculate SLA Estimasi
        $totalSlaDays = (int)Database::selectOne("SELECT SUM(sla_days) as total FROM workflow_steps")['total'];
        
        $this->render('dashboard/create', [
            'title' => 'Tambah Registrasi Baru',
            'activeTab' => 'registrasi/create',
            'kliens' => $kliens,
            'layanans' => $layanans,
            'defaultStep' => $defaultStep,
            'totalSlaDays' => $totalSlaDays
        ]);
    }

    /**
     * Store Data
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/registrasi');

        $user = Auth::user();
        
        // Clean currency inputs
        $_POST['total_tagihan'] = (float)str_replace(['.', ','], ['', '.'], $_POST['total_tagihan'] ?? '0');
        $_POST['pembayaran'] = (float)str_replace(['.', ','], ['', '.'], $_POST['pembayaran'] ?? '0');

        $res = $this->registrasiService->createRegistrasi($_POST, $user);
        
        if (ob_get_length()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    /**
     * Update Status via WorkflowService
     */
    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/registrasi');

        $user = Auth::user();
        $res = $this->workflow->updateStatus(
            (int)$_POST['id'],
            $_POST['status'],
            (int)$user['id'],
            $user['role'],
            $_POST['catatan'] ?? null,
            isset($_POST['flag_kendala'])
        );

        jsonResponse($res);
    }
}
