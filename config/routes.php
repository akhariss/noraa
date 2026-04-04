<?php
declare(strict_types=1);

/**
 * SK-01: Route Registry
 * All routes registered here using [Controller::class, 'action'] pattern.
 * Roles: ROLE_OWNER (Administrator), ROLE_TRUSTED (Administrator), ROLE_STAFF (Staff)
 */

use App\Core\Router;
use Modules\Main\Controller as PublicController;
use Modules\Auth\Controller as AuthController;
use Modules\Dashboard\Controller as DashboardController;
use Modules\Finalisasi\Controller as FinalisasiController;
use Modules\CMS\Controller as CMSEditorController;
use Modules\Media\Controller as ImageMediaController;

// ═══════════════════════════════════════════════════════════════
// PUBLIC ROUTES (no auth required)
// ═══════════════════════════════════════════════════════════════
Router::add('home',      'GET',  [PublicController::class, 'home'],        ['rateType' => 'homepage']);
Router::add('lacak',     'GET',  [PublicController::class, 'tracking'],    ['rateType' => 'tracking_search']);
Router::add('lacak',     'POST', [PublicController::class, 'tracking'],    ['rateType' => 'tracking_search']);
Router::add('detail',    'GET',  [PublicController::class, 'showRegistrasi'], ['rateType' => 'tracking_search']);
Router::add('verify_tracking', 'POST', [PublicController::class, 'verifyTracking'], ['rateType' => 'tracking_verify']);
Router::add('health',    'GET',  [PublicController::class, 'health'],      []);

// ═══════════════════════════════════════════════════════════════
// AUTH ROUTES
// ═══════════════════════════════════════════════════════════════
Router::add('login',  'GET',  [AuthController::class, 'showLoginPage'], []);
Router::add('login',  'POST', [AuthController::class, 'login'],         []);
Router::add('logout', 'GET',  [AuthController::class, 'logout'],        []);

// ═══════════════════════════════════════════════════════════════
// DASHBOARD ROUTES (auth required - all roles)
// ═══════════════════════════════════════════════════════════════
Router::add('dashboard',         'GET',  [DashboardController::class, 'index'],           ['auth' => true]);
Router::add('registrasi',       'GET',  [DashboardController::class, 'registrasi'],       ['auth' => true]);
Router::add('registrasi_create','GET',  [DashboardController::class, 'createRegistrasi'], ['auth' => true]);
Router::add('registrasi_store', 'POST', [DashboardController::class, 'storeRegistrasi'],  ['auth' => true]);
Router::add('registrasi_store', 'GET',  [DashboardController::class, 'createRegistrasi'], ['auth' => true]);
Router::add('registrasi_detail','GET',  [DashboardController::class, 'showRegistrasi'],   ['auth' => true]);
Router::add('registrasi_detail_finalisasi', 'GET', [DashboardController::class, 'showRegistrasiDetailFinalisasi'], ['auth' => true]);
Router::add('registrasi_history','GET', [DashboardController::class, 'showRegistrasiHistory'], ['auth' => true]);

// Status/Workflow Actions
Router::add('update_status',  'POST', [DashboardController::class, 'updateStatus'],  ['auth' => true]);
Router::add('update_klien',   'POST', [DashboardController::class, 'updateKlien'],   ['auth' => true]);
Router::add('toggle_kendala', 'POST', [DashboardController::class, 'toggleKendala'], ['auth' => true]);
Router::add('toggle_lock',    'POST', [DashboardController::class, 'toggleLock'],    ['auth' => true]);

// Finalisasi (Administrator only - Owner + Trusted)
Router::add('finalisasi',      'GET',  [FinalisasiController::class, 'index'],           ['auth' => true]);
Router::add('finalize_case',   'POST', [FinalisasiController::class, 'tutupRegistrasi'], ['auth' => true]);
Router::add('tutup_registrasi','POST', [FinalisasiController::class, 'tutupRegistrasi'], ['auth' => true]);
Router::add('reopen_case',     'POST', [FinalisasiController::class, 'reopen'],          ['auth' => true]);

// ═══════════════════════════════════════════════════════════════
// USERS (Administrator only - Owner + Trusted)
// ═══════════════════════════════════════════════════════════════
Router::add('users', 'GET',  [DashboardController::class, 'users'],       ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('users', 'POST', [DashboardController::class, 'handleUserPost'], ['auth' => true, 'role' => ROLE_OWNER]);

// ═══════════════════════════════════════════════════════════════
// CMS EDITOR (Administrator only - Owner + Trusted)
// ═══════════════════════════════════════════════════════════════
Router::add('cms_editor',       'GET',  [CMSEditorController::class, 'index'],             ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_edit_home',    'GET',  [CMSEditorController::class, 'editHome'],          ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_edit_messages','GET',  [CMSEditorController::class, 'editMessages'],      ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_edit_layanan', 'GET',  [CMSEditorController::class, 'editLayananPage'],   ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_update_content','POST',[CMSEditorController::class, 'updateContent'],     ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_update_item',  'POST', [CMSEditorController::class, 'updateItem'],        ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_upload_image', 'POST', [ImageMediaController::class, 'upload'],           ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_save_message_tpl','POST',[CMSEditorController::class, 'saveMessageTemplate'], ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_save_note_tpl','POST', [CMSEditorController::class, 'saveNoteTemplate'],  ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_add_layanan',  'POST', [CMSEditorController::class, 'addLayanan'],        ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_update_layanan','POST',[CMSEditorController::class, 'updateLayanan'],     ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_delete_layanan','POST',[CMSEditorController::class, 'deleteLayanan'],     ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_save_settings','POST', [CMSEditorController::class, 'saveAppSettings'],   ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_get_note_templates','GET', [CMSEditorController::class, 'getNoteTemplatesJson'], ['auth' => true]);
Router::add('cms_get_note_tpls','GET',  [CMSEditorController::class, 'getNoteTemplatesJson'], ['auth' => true]);
Router::add('cms_get_msg_tpl', 'GET',   [CMSEditorController::class, 'getMessageTemplate'], ['auth' => true]);

// Legacy CMS routes (redirects)
Router::add('cms',              'GET', [CMSEditorController::class, 'legacyCmsRedirect'],      ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_edit',         'GET', [CMSEditorController::class, 'editHome'],               ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_save',         'POST',[CMSEditorController::class, 'updateContent'],          ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_preview',      'GET', [CMSEditorController::class, 'editHome'],               ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('admin_cms',        'GET', [CMSEditorController::class, 'editHome'],               ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('admin_cms_edit',   'GET', [CMSEditorController::class, 'editHome'],               ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('admin_cms_edit_home','GET',[CMSEditorController::class, 'editHome'],              ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('admin_cms_save',   'POST',[CMSEditorController::class, 'updateContent'],          ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('admin_cms_preview','GET', [CMSEditorController::class, 'editHome'],               ['auth' => true, 'role' => ROLE_OWNER]);

// ═══════════════════════════════════════════════════════════════
// BACKUPS (Administrator only - Owner + Trusted)
// ═══════════════════════════════════════════════════════════════
Router::add('backups', 'GET',  [DashboardController::class, 'backups'],         ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('backups', 'POST', [DashboardController::class, 'handleBackupPost'],['auth' => true, 'role' => ROLE_OWNER]);

// ═══════════════════════════════════════════════════════════════
// AUDIT LOG (Administrator only - Owner + Trusted)
// ═══════════════════════════════════════════════════════════════
Router::add('audit', 'GET', [DashboardController::class, 'auditLogs'], ['auth' => true, 'role' => ROLE_OWNER]);
