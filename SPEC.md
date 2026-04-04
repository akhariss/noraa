# Nora 2.0 - CodeIgniter 4 Migration Specification

## Overview

**Project**: Notaris Sri Anah SH.M.Kn - Registration Tracking System  
**Current Framework**: Custom PHP (NOT CodeIgniter)  
**Target Framework**: CodeIgniter 4  
**Version**: 1.1.2

---

## 1. Architecture

### 1.1 Current Structure
```
nora2.0/
├── app/
│   ├── Adapters/         (Database, Logger)
│   ├── Core/             (Router, Autoloader, View, Utils)
│   ├── Domain/Entities/ (Models: Registrasi, Klien, User, etc.)
│   ├── Security/         (Auth, RBAC, CSRF, RateLimiter, InputSanitizer)
│   └── Services/         (WorkflowService, CMSEditorService, etc.)
├── config/               (app.php, routes.php)
├── modules/             (Main, Auth, Dashboard, CMS, Finalisasi)
├── public/               (index.php - front controller)
├── resources/views/     (dashboard, auth, public, company_profile)
└── storage/             (cache, logs, backups, migrations)
```

### 1.2 CI4 Target Structure
```
app/
├── Config/
├── Controllers/
├── Models/
├── Services/
├── Entities/
├── Filters/              (AuthFilter, CSRFFilter, RateLimitFilter)
├── Language/
├── Libraries/           (Custom: RBAC, WorkflowEngine)
└── Views/
writable/
├── cache/
├── logs/
└── sessions/
public/
└── index.php
```

---

## 2. Core Components Migration

### 2.1 Router (Custom → CI4 Routes)

**Current**: Query-param based routing (`?gate=xxx`)

**CI4 Implementation**:
```php
// app/Config/Routes.php
$routes->get('/', 'Main::home');
$routes->get('lacak', 'Main::tracking');
$routes->post('lacak', 'Main::tracking');
$routes->get('detail', 'Main::showRegistrasi');
$routes->post('verify_tracking', 'Main::verifyTracking');
$routes->get('health', 'Main::health');

$routes->get('login', 'Auth::showLogin');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('registrasi', 'Dashboard::registrasi');
    $routes->get('registrasi/create', 'Dashboard::createRegistrasi');
    $routes->post('registrasi', 'Dashboard::storeRegistrasi');
    $routes->get('registrasi/(:num)', 'Dashboard::showRegistrasi/$1');
    $routes->post('update_status', 'Dashboard::updateStatus');
    // ... etc
});
```

### 2.2 Database Adapter

**Current**: Custom singleton PDO wrapper (`app/Adapters/Database.php`)

**CI4**: Use `CodeIgniter\Database\BaseBuilder` + custom model base class

```php
// app/Libraries/Database.php (wrapper)
namespace App\Libraries;

use CodeIgniter\Database\ConnectionInterface;

class Database
{
    public static function select(string $sql, array $params = []): array
    {
        $db = \Config\Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->getResultArray();
    }
    
    public static function selectOne(string $sql, array $params = []): ?array
    {
        $db = \Config\Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->getRowArray() ?: null;
    }
    
    public static function execute(string $sql, array $params = []): int
    {
        $db = \Config\Database::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->affectedRows();
    }
}
```

### 2.3 Configuration

**Current**: `config/app.php` with define() constants

**CI4**: Use `app/Config/App.php` + environment variables

```php
// app/Config/App.php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public $appName = 'Notaris Sri Anah SH.M.Kn';
    public $appVersion = '1.1.2';
    public $baseURL = '';
    
    // Session
    public $sessionLifetime = 7200;
    public $sessionCookieName = 'nora_session';
    
    // Database
    public $DBHost = '127.0.0.1';
    public $DBName = 'norasblmupdate2';
    public $DBUser = 'root';
    public $DBPass = '';
    public $DBCharset = 'utf8mb4';
    
    // Status Labels (workflow)
    public $statusLabels = [
        'draft' => 'Draft / Pengumpulan Persyaratan',
        'pembayaran_admin' => 'Pembayaran Administrasi',
        // ... all statuses
    ];
}
```

---

## 3. Security Components

### 3.1 Authentication

**Current**: Custom session with fingerprinting (`app/Security/Auth.php`)

**CI4 Implementation**:
- Use `CodeIgniter\Session\Session`
- Create custom `AuthFilter`:
```php
// app/Filters/AuthFilter.php
namespace App\Filters;

use CodeIgniter\Filters\BaseFilter;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter extends BaseFilter
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login?expired=1');
        }
        
        // Session timeout check
        $lastActivity = $session->get('last_activity');
        if (time() - $lastActivity > SESSION_LIFETIME) {
            $session->destroy();
            return redirect()->to('/login?timeout=1');
        }
        
        $session->set('last_activity', time());
    }
}
```

### 3.2 RBAC (Role-Based Access Control)

**Current**: `app/Security/RBAC.php`

**CI4**: Create library with permission mapping:
```php
// app/Libraries/RBAC.php
namespace App\Libraries;

class RBAC
{
    private static array $permissions = [
        'administrator' => ['*'],
        'staff' => [
            'dashboard.view',
            'registrasi.view', 'registrasi.create', 'registrasi.edit',
            'status.update', 'klien.update', 'kendala.toggle'
        ],
        'publik' => [
            'home.view', 'tracking.view', 'tracking.verify', 'detail.view'
        ]
    ];
    
    public static function can(string $role, string $permission): bool
    {
        // ... implementation
    }
}
```

### 3.3 CSRF Protection

**CI4**: Use built-in CSRF filter:
```php
// app/Config/Filters.php
public $filters = [
    'csrf' => ['except' => ['api/*', 'health']],
    'auth' => ['except' => ['login', 'lacak', 'detail']]
];
```

### 3.4 Rate Limiting

**Current**: Custom `app/Security/RateLimiter.php`

**CI4**: Use built-in Throttle filter or create custom

---

## 4. Controllers Migration

### 4.1 Main Controller (Public)
| Current Route | CI4 Controller | Method |
|--------------|---------------|--------|
| `?gate=home` | `Main` | `home()` |
| `?gate=lacak` | `Main` | `tracking()` |
| `?gate=detail` | `Main` | `showRegistrasi()` |
| `?gate=health` | `Main` | `health()` |

### 4.2 Auth Controller
| Current Route | CI4 Controller | Method |
|--------------|---------------|--------|
| `?gate=login` | `Auth` | `showLogin()` |
| (POST) `?gate=login` | `Auth` | `login()` |
| `?gate=logout` | `Auth` | `logout()` |

### 4.3 Dashboard Controller
| Current Route | CI4 Controller | Method |
|--------------|---------------|--------|
| `?gate=dashboard` | `Dashboard` | `index()` |
| `?gate=registrasi` | `Dashboard` | `registrasi()` |
| `?gate=registrasi_create` | `Dashboard` | `createRegistrasi()` |
| `?gate=registrasi_store` | `Dashboard` | `storeRegistrasi()` |
| `?gate=registrasi_detail` | `Dashboard` | `showRegistrasi($id)` |
| `?gate=update_status` | `Dashboard` | `updateStatus()` |
| `?gate=toggle_kendala` | `Dashboard` | `toggleKendala()` |
| `?gate=toggle_lock` | `Dashboard` | `toggleLock()` |
| `?gate=users` | `Dashboard` | `users()` |
| `?gate=backups` | `Dashboard` | `backups()` |
| `?gate=audit` | `Dashboard` | `auditLogs()` |

### 4.4 CMS Controller
| Current Route | CI4 Controller | Method |
|--------------|---------------|--------|
| `?gate=cms_editor` | `CMS` | `index()` |
| `?gate=cms_edit_home` | `CMS` | `editHome()` |
| `?gate=cms_update_content` | `CMS` | `updateContent()` |
| `?gate=cms_add_layanan` | `CMS` | `addLayanan()` |
| `?gate=cms_get_note_templates` | `CMS` | `getNoteTemplatesJson()` |

### 4.5 Finalisasi Controller
| Current Route | CI4 Controller | Method |
|--------------|---------------|--------|
| `?gate=finalisasi` | `Finalisasi` | `index()` |
| `?gate=tutup_registrasi` | `Finalisasi` | `tutupRegistrasi()` |
| `?gate=reopen_case` | `Finalisasi` | `reopen()` |

---

## 5. Models (Entities) Migration

### 5.1 Base Model
```php
// app/Models/BaseModel.php
namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected function getConnection(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }
}
```

### 5.2 Entity Models

| Current Entity | CI4 Model | Table |
|---------------|-----------|-------|
| `Registrasi` | `RegistrasiModel` | `registrasi` |
| `Klien` | `KlienModel` | `klien` |
| `User` | `UserModel` | `users` |
| `Layanan` | `LayananModel` | `layanan` |
| `WorkflowStep` | `WorkflowStepModel` | `workflow_steps` |
| `Kendala` | `KendalaModel` | `kendala` |
| `RegistrasiHistory` | `RegistrasiHistoryModel` | `registrasi_history` |
| `AuditLog` | `AuditLogModel` | `audit_logs` |
| `CMSPage` | `CMSPageModel` | `cms_pages` |
| `MessageTemplate` | `MessageTemplateModel` | `message_templates` |
| `NoteTemplate` | `NoteTemplateModel` | `note_templates` |

---

## 6. Services Migration

### 6.1 WorkflowService
```php
// app/Services/WorkflowService.php
namespace App\Services;

use App\Models\RegistrasiModel;
use App\Models\KendalaModel;
use App\Models\WorkflowStepModel;

class WorkflowService
{
    public function updateStatus(
        int $registrasiId,
        string $newStatusKey,
        int $userId,
        string $role,
        ?string $catatan = null,
        ?bool $flagKendala = null,
        ?string $keterangan = null
    ): array {
        // Core logic preserved
    }
    
    public function getProgress(int $registrasiId): array
    {
        // Core logic preserved
    }
}
```

### 6.2 Other Services
- `CMSEditorService` → Port to CI4 model queries
- `BackupService` → Use CI4 file operations
- `FinalisasiService` → Port business logic
- `UserService` → Port user management logic

---

## 7. Database Schema (Preserve)

```sql
-- Core tables (already exist)
-- registrasi, klien, users, layanan, workflow_steps, 
-- kendala, registrasi_history, audit_logs,
-- cms_pages, cms_page_sections, cms_section_content, cms_section_items,
-- message_templates, note_templates, app_settings
```

### 7.1 Key Foreign Key Relationships
```
registrasi.klien_id -> klien.id
registrasi.layanan_id -> layanan.id
registrasi.current_step_id -> workflow_steps.id
kendala.registrasi_id -> registrasi.id
registrasi_history.registrasi_id -> registrasi.id
audit_logs.user_id -> users.id
```

---

## 8. View Templates

### 8.1 Layout Structure
- Header: `resources/views/templates/header.php`
- Footer: `resources/views/templates/footer.php`

### 8.2 View Folders (CI4)
```
app/Views/
├── dashboard/
│   ├── index.php
│   ├── registrasi.php
│   ├── registrasi_detail.php
│   ├── registrasi_create.php
│   ├── users.php
│   ├── backups.php
│   ├── audit_logs.php
│   ├── cms_editor_grid.php
│   ├── cms_editor_beranda.php
│   ├── cms_editor_layanan.php
│   └── finalisasi.php
├── auth/
│   └── login.php
├── public/
│   ├── tracking.php
│   └── registrasi_detail.php
├── company_profile/
│   └── home.php
├── errors/
│   ├── 403.php
│   └── error.php
└── templates/
    ├── header.php
    └── footer.php
```

---

## 9. Key Features to Preserve

### 9.1 Workflow System
- Dynamic step definition from `workflow_steps` table
- `behavior_role` for terminal states
- SLA days per step
- Backward movement restrictions (repair mode only)

### 9.2 Tracking System
- Secure token-based verification
- 4-digit phone verification
- Process log/history

### 9.3 Security Features
- Session fingerprinting
- CSRF protection
- Rate limiting per endpoint
- Audit logging
- Input sanitization

### 9.4 CMS Features
- Dynamic page content
- Section-based layout
- Layanan management
- Message/note templates per workflow step

---

## 10. Migration Checklist

- [ ] Set up CI4 project
- [ ] Configure database connection
- [ ] Create Config/App.php with constants
- [ ] Create Config/Routes.php with all routes
- [ ] Create AuthFilter
- [ ] Create RBAC library
- [ ] Create BaseModel
- [ ] Create all entity models
- [ ] Create service classes
- [ ] Create controllers
- [ ] Copy/migrate views
- [ ] Set up session config
- [ ] Configure CSRF filter
- [ ] Create .env file
- [ ] Test authentication flow
- [ ] Test workflow operations
- [ ] Test public tracking
- [ ] Test CMS functionality
