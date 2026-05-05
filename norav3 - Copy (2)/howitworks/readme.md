# HOW IT WORKS

## 1. Gambaran Umum Sistem

Sistem ini adalah **Notaris & PPAT Case Management System** yang dibangun dengan PHP native (custom framework) untuk Kantor Notaris Sri Anah SH.M.Kn di Cirebon. Sistem ini mengelola seluruh proses bisnis layanan notaris meliputi:

- **Penerimaan klien baru** (calon klien yang membutuhkan layanan notaris)
- **Pembuatan dan pencatatan perkara/registrasi** (akta tanah, hibah, waris, pendirian usaha, dll)
- **Workflow/status tracking** dengan tahap-tahap tertentu
- **Publikasi website company profile** dengan CMS terintegrasi
- **Tracking publik** - klien dapat melihat status perkara secara online

Sistem memiliki dua sisi akses:
1. **Public Site** - Website company profile + fitur lacak perkara
2. **Dashboard Admin** - Sistem manajemen internal untuk staff notaris

---

## 2. Struktur Nyata Project

Berdasarkan analisis kode yang ada, struktur folder project adalah:

```
nora2.0/
├── app/                          # Aplikasi inti
│   ├── Adapters/                 # Adaptor eksternal
│   │   ├── Database.php          # Singleton PDO - akses MySQL/MariaDB
│   │   └── Logger.php           # Logging ke file
│   ├── Core/                     # Core utilities
│   │   ├── Autoloader.php       # PSR-4 style autoloader
│   │   ├── Router.php           # Route dispatcher
│   │   ├── View.php            # View renderer
│   │   └── Utils/             # Helper functions
│   ├── Domain/Entities/         # Domain models (Active Record pattern)
│   │   ├── Registrasi.php       # Model perkara/registrasi
│   │   ├── Klien.php          # Model klien
│   │   ├── Layanan.php        # Model layanan (jenis akta)
│   │   ├── Transaksi.php     # Model transaksi (pembayaran)
│   │   ├── WorkflowStep.php  # Model tahap workflow
│   │   ├── NoteTemplate.php # Template catatan per tahap
│   │   ├── MessageTemplate.php # Template WA
│   │   └── ...entities lain
│   ├── Services/               # Business logic services
│   │   ├── WorkflowService.php # Orchestrator transisi status
│   │   ├── FinalisasiService.php
│   │   ├── TransaksiService.php
│   │   └── CMSEditorService.php
│   └── Security/               # Keamanan
│       ├── Auth.php           # Session management
│       ├── RBAC.php         # Role-based access
│       ├── InputSanitizer.php
│       ├── RateLimiter.php
│       └── CSRF.php
├── config/                     # Konfigurasi
│   ├── app.php                # Konstanta aplikasi
│   └── routes.php            # Route registry
├── database/                  # SQL migrations
├── modules/                   # Controllers
│   ├── Main/Controller.php  # Public pages (home, tracking)
│   ├── Dashboard/Controller.php # Admin dashboard
│   ├── Auth/Controller.php   # Login/logout
│   ├── CMS/Controller.php    # CMS editor (admin)
│   ├── Finalisasi/Controller.php # Finalisasi/tutup kasus
│   └── Media/Controller.php  # Image upload
├── resources/views/           # Views (TPS)
│   ├── company_profile/      # Public website pages
│   ├── public/              # Public tracking pages
│   ├── dashboard/           # Admin dashboard pages
│   ├── templates/           # Header/footer partials
│   └── errors/             # Error pages
├── public/                    # Entry point & assets
│   ├── index.php           # Front controller (single entry point)
│   ├── image.php          # Image handler
│   └── assets/            # CSS, JS, images
├── storage/                  # Logs & cache
├── documentation/             # Dokumentasi (administrasi)
└── laporan/                  # Laporan teknis
```

### Pengelompokan Berdasarkan Fungsi

| Kelompok | Komponen | Fungsi |
|----------|---------|--------|
| **Routing** | `config/routes.php`, `app/Core/Router.php` | Memetakan URL ke controller/action |
| **Controller** | `modules/*/Controller.php` | Handle request, panggil service |
| **Service** | `app/Services/*.php` | Business logic orchestration |
| **Entity/Model** | `app/Domain/Entities/*.php` | Data access (CRUD + query) |
| **Database** | `app/Adapters/Database.php` | PDO wrapper - eksekusi SQL |
| **View** | `resources/views/**/*.php` | Render HTML output |
| **Security** | `app/Security/*.php` | Auth, RBAC, sanitization |

---

## 3. Arsitektur Sistem

**Tipe: Modular Monolith (Custom PHP Framework)**

Sistem ini **bukan Laravel/Framework umum**, melainkan custom PHP yang dibangun dengan prinsip:
- **Single Entry Point**: `public/index.php` sebagai satu-satunya pintu masuk
- **MVC Terinspirasi**: Pemisahan Controller (modules/), Model (Entities/), View (resources/)
- **Service Layer**: Business logic terpisah di `app/Services/`
- **Active Record**: Entity classes langsung melakukan CRUD ke database

### Bukti dari Kode

Dari `public/index.php` (baris 4-16):
```php
// Request Lifecycle:
// 1. Bootstrap  → BASE_PATH, Autoloader (PSR-4 lazy-load)
// 2. Config    → constants (DB, roles, status, security keys)
// 3. Helpers   → procedural functions (auth, format, SEO, security)
// 4. Session   → hardened (fingerprint, timeout, regeneration)
// 5. Headers   → CSP, anti-clickjacking, anti-sniff, no-cache
// 6. Sanitize  → $_GET, $_POST, $_COOKIE via InputSanitizer
// 7. Routes    → register all gate → [Controller, action] mappings
// 8. Dispatch  → rate-limit → auth → role → controller → view
```

Dari `config/routes.php`:
- Route registry menggunakan pattern `[Controller::class, 'action']`
- Menggunakan role-based access: `ROLE_OWNER` (Administrator), `ROLE_STAFF` (Staff)

---

## 4. Flow Sistem (REAL)

### 4.1 Skenario: Klien Baru Mendaftarkan Perkara

```
1. Staff akses Dashboard → /index.php?gate=dashboard
2. Login (cek username/password → session)
3. Klik "Registrasi Baru" → form registrasi.php
4. Isi: Nama klien, HP, layanan (jenis akta)
5. Submit → POST /index.php?gate=registrasi_store
   │
   ├──→ DashboardController::storeRegistrasi()
   │   ├── Cek data klien (ada/tidak)
   │   ├── Buat registrasi baru (nomor_registrasi auto)
   │   ├── Set current_step_id = 1 (Draft)
   │   ├── RegistrasiHistory::create() (audit trail)
   │   └── Return view registrasi_detail.php
```

### 4.2 Skenario: Update Status Perkara

```
1. Staff Klik "Update Status" di detail perkara
2. Pilih status baru (dropdown dari workflow_steps)
3. Submit → POST /index.php?gate=update_status
   │
   ├──→ DashboardController::updateStatus()
   │   ├── Validasi: tidak terkunci, tidak mundur ilegal
   │   ├── WorkflowService::updateStatus()
   │   │   ├── Database transaction BEGAN
   │   │   ├── Registrasi::updateStatus()
   │   │   ├── Kendala (jika ada flag kendala)
   │   │   ├── RegistrasiHistory::create()
   │   │   └── Database transaction COMMIT
   │   └── Return JSON {success, message}
```

### 4.3 Skenario: Klien Melacak Perkara (Public)

```
1. Buka /index.php?gate=lacak
2. Masukkan Nomor Registrasi → POST
   │
   ├──→ MainController::tracking()
   │   ├── Rate limit check
   │   ├── Registrasi::findByNomorRegistrasi()
   │   └── Return: {success, requires_verification: true}
3. Masukkan 4 digit terakhir HP → verifikasi
   │
   ├──→ MainController::verifyTracking()
   │   ├── Validasi: 4 digit terakhir cocok
   │   ├── Generate tracking_token
   │   └── Return: {success, token, data}
4. Buka /index.php?gate=detail&token=XXX
   │
   ├──→ MainController::showRegistrasi(token)
   │   ├── verifyTrackingToken()
   │   ├── Get registrasi + history
   │   └── Render view registrasi_detail.php
```

### 4.4 Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    PUBLIC FLOW                            │
├─────────────────────────────────────────────────────────────┤
│  Browser ──GET /lacak─────> MainController@tracking()      │
│       │                              │                    │
│  POST /lacak ──────────────────────▼                      │
│       │                    Registrasi::findByNomor()       │
│       │                              │                    │
│       │                    {requires_verification}          │
│       │                              │                    │
│  POST verify ───────────────> MainController@verify()      │
│       │                              │                    │
│       │                    Cek 4 digit HP                │
│       │                              │                    │
│       │                    Generate token              │
│       │                              │                    │
│  GET detail?token=XXX ────> MainController@show()       │
│                         │                                │
│                    Load + render                         │
│                    registrasi_detail.php                │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                   ADMIN FLOW                              │
├─────────────────────────────────────────────────────────────┤
│  Browser ──GET /dashboard ──> Auth check → Dashboard      │
│       │                              │                   │
│  POST /login ──────────────────▼                         │
│                          │                               │
│                     Auth::login()                        │
│                          │                               │
│                     Set session                         │
│                          │                               │
│  GET /registrasi ──────> DashboardController@registrasi()│
│                          │                               │
│                     Registrasi::getAll()                 │
│                          │                               │
│  POST /update_status ──> WorkflowService@updateStatus() │
│                          │                               │
│  ┌────────────────────��─��─▼────────────────────────┐   │
│  │  BEGIN TRANSACTION                                │   │
│  │  1. Validasi lock & workflow rules              │   │
│  │  2. Registrasi::updateStatus()               │   │
│  │  3. RegistrasiHistory::create()              │   │
│  │  4. COMMIT / ROLLBACK                        │   │
│  └────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. Interaksi Database

### 5.1 Tabel Utama dan Relasi

Dari `nora3_0 (3).sql`, berikut tables utama:

| Table | Primary Key | Digunakan Di | Deskripsi |
|------|------------|--------------|-----------|
| `klien` | id | Registrasi, Transaksi | Data klien (nama, HP, email) |
| `layanan` | id | Registrasi | Jenis layanan (Jual Beli, Waris, Hibah, dll) |
| `registrasi` | id | RegistrasiHistory, Transaksi, Kendala | Perkara/case |
| `workflow_steps` | id | Registrasi | Tahap workflow (Draft, Validasi, dll) |
| `registrasi_history` | id | - | Audit trail perubahan status |
| `transaksi` | registrasi_id | TransaksiHistory | Pembayaran |
| `transaksi_history` | transaksi_id | - | Riwayat pembayaran |
| `kendala` | registrasi_id | - | Masalah/hambatan perkara |
| `users` | id | AuditLog | User admin/staff |
| `audit_log` | id | - | Log aktivitas user |
| `message_templates` | template_key | - | Template WhatsApp |
| `note_templates` | workflow_step_id | - | Template catatan per tahap |
| `cms_pages` | id | CMS Page Sections | Halaman website |
| `cms_page_sections` | page_id | CMS Section Item | Section per halaman |
| `cms_section_content` | section_id | - | Konten text per section |
| `cms_section_items` | section_id | - | Items (buttons, cards, dll) |

### 5.2 Diagram Relasi (ERD Sederhana)

```
┌──────────┐       ┌─────────────┐       ┌────────────┐
│  klien   │◄──────│ registrasi │──────►│ layanan  │
└──────────┘       └─────────────┘       └────────────┘
                         │
          ┌──────────────┼──────────────┐
          ▼              ▼              ▼
    ┌───────────┐ ┌───────────┐ ┌───────────┐
    │ transaksi │ │registrasi_│ │ kendala  │
    │          │ │ history   │ │          │
    └───────────┘ └───────────┘ └───────────┘
         │
         ▼
┌───────────────┐
│transaksi_    │
│history      │
└─────────────┘

┌──────────┐       ┌────────────────┐
│ workflow │◄──────│ registrasi     │
│ _steps   │       │ (current_step) │
└──────────┘       └────────────────┘

┌──────────┐       ┌─────────────────┐
│  users   │◄──────│ audit_log       │
└──────────┘       └─────────────────┘

┌──────────┐       ┌──────────────┐
│  cms_    │◄──────│ cms_page_    │
│  pages   │       │ sections     │
└──────────┘       └──────────────┘
                         │
              ┌──────────┼──────────┐
              ▼          ▼          ▼
        ┌────────┐ ┌──────────┐ ┌──────────┐
        │content│ │  items   │ │template │
        └───────┘ └─────────┘ └──────────┘
```

### 5.3 Query Utama (Dari Entity Classes)

**Registrasi.php** - Mencari dan manipulasi perkara:
```php
// Cari by ID dengan join klien/layanan/workflow
findById(int $id): ?array

// Cari by nomor registrasi
findByNomorRegistrasi(string $nomor): ?array

// Cari by HP klien
findByKlienPhone(string $hp): array

// Get all dengan filter
getWithFilters(search, status, layanan, limit, offset, order): array

// Update status + milestone
updateStatus(id, status, stepId, keterang, catatan, targetDate, milestones): bool

// Lock/unlock untuk edit concurent
lock(int $id): bool
unlock(int $id): bool
```

**WorkflowStep.php** - Tahap workflow (dari SQL):
```sql
-- Workflow steps lengkap (15 tahap):
1  draft              : Draft / Pengumpulan Persyaratan
3  validasi_sertifikat: Validasi Sertifikat
4  pencecekan_sertifikat: Pengecekan Sertifikat
5  pembayaran_pajak  : Pembayaran Pajak
6  validasi_pajak    : Validasi Pajak
7  penomoran_akta    : Penomoran Akta
8  pendaftaran       : Pendaftaran
10 pemeriksaan_bpn  : Pemeriksaan BPN
11 perbaikan         : Perbaikan (bisa mundur)
12 selesai          : Selesai
13 diserahkan       : Diserahkaan
14 ditutup         : Ditutup
15 batal            : Batal
16 review           : Review Penyerahan
```

---

## 6. Cara Kerja Backend

### 6.1 Request Handling (Front Controller)

Dari `public/index.php`:

```php
//Step 1-3: Bootstrap, Config, Helpers
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/app/Core/Utils/helpers.php';

//Step 4-6: Security
App\Security\Auth::startSecureSession();
App\Security\InputSanitizer::sanitizeGlobal();

//Step 7: Load routes
require_once BASE_PATH . '/config/routes.php';

//Step 8: Dispatch
App\Core\Router::dispatch();
```

### 6.2 Route Dispatch

Dari `config/routes.php` - Contoh route:
```php
// Public routes
Router::add('home',      'GET',  [PublicController::class, 'home']);
Router::add('lacak',     'GET',  [PublicController::class, 'tracking']);
Router::add('detail',    'GET',  [PublicController::class, 'showRegistrasi']);

// Auth routes
Router::add('login',  'GET',  [AuthController::class, 'showLoginPage']);
Router::add('login',  'POST', [AuthController::class, 'login']);

// Dashboard (auth required)
Router::add('registrasi', 'GET', [DashboardController::class, 'registrasi'], ['auth' => true]);
Router::add('update_status', 'POST', [DashboardController::class, 'updateStatus'], ['auth' => true]);

// Admin only routes
Router::add('users', 'GET', [DashboardController::class, 'users'], ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('cms_editor', 'GET', [CMSEditorController::class, 'index'], ['auth' => true, 'role' => ROLE_OWNER]);
```

### 6.3 Controller Logic

**MainController** (modules/Main/Controller.php):
- `home()` - Load CMS data, render company profile
- `tracking()` - Search by nomor registrasi
- `verifyTracking()` - Verifikasi 4 digit HP, generate token
- `showRegistrasi(token)` - Tampilkan detail dengan token validation

**DashboardController** (modules/Dashboard/Controller.php):
- `index()` - Dashboard utama dengan statistik
- `registrasi()` - List semua registrasi dengan filter
- `createRegistrasi()` - Form registrasi baru
- `storeRegistrasi()` - Simpan registrasi baru
- `showRegistrasi()` - Detail registrasi
- `updateStatus()` - Update workflow status
- `transaksiStore()` - Simpan pembayaran

### 6.4 Service Layer

**WorkflowService** (app/Services/WorkflowService.php):
```php
updateStatus(
    int $registrasiId,
    string $newStatusKey,
    int $userId,
    string $role,
    ?string $catatan,
    ?bool $flagKendala,
    ?string $keterangan
): array
```

Logic penting:
1. Cek lock status (G-10) - mencegah edit concurrent
2. Validasi tidak mundur illegal (kecuali ke tahap Perbaikan)
3. Validasi cancellable (batal hanya jika diijinkan)
4. Transaction: BEGIN → UPDATE → HISTORY → COMMIT

### 6.5 Data Access (Entity/Active Record)

**Registrasi Entity** (app/Domain/Entities/Registrasi.php):
- Setiap method melakukan query langsung ke database via `Database::select/execute`
- Mendukung backward compatibility dengan column checking
- Method untuk lock/unlock, milestone reset

---

## 7. Masalah & Kekurangan

### 7.1 Dari Kode (Tidak dari Teori)

**M1: Inconsistent Naming di Database**
- Tabel `kendala` tidak punya foreign key constraint ke `registrasi` meskipun kolom ada
- Tabel `note_templates` menggunakan `workflow_step_id` unik, tapi ada gap di ID (1,3,4,5,6,7,8,10-15)

**M2: Column Migration Berpotensi Error**
- Registrasi entity punya column checking dengan cache (`$columnCache`)
- Kolom `locked` dan `batal_flag` dicek ada/tidak sebelum digunakan
- Tapi tidak ada error handling clean saat kolom belum ada di production

**M3: Rate Limiting Sederhana**
- Rate limiter berdasarkan IP + action type
- Tidak ada sliding window, hanya hit count
- Tidak ada distributed rate limiting

**M4: Password Hash Campuran**
- Users table punya hash berbeda (`$2y$12$...` vs `$argon2id$...`)
- Tidak ada strategi upgrade hash

**M5: CMS Belum Full**
- CMS Editor hanya untuk admin (ROLE_OWNER)
- CMS data loading di MainController langsung query, tidak ada service
- Image handling via separate MediaController

**M6: No API/ReST**
- Semua request via server-side rendering (PHP)
- Tidak ada JSON API untuk mobile app
- Tidak ada webhook untuk integrasi

**M7: Error Handling**
- Catch umum tapi tidak ada error recovery yang sistematis
- Log ke file tapi tidak ada centralized logging (Sentry, dll)

### 7.2 Scaling Issues

| Aspek | Kondisi Saat Ini | Potensi Masalah |
|------|-----------------|----------------|
| **DB Connection** | Singleton PDO | Bottleneck pada high traffic |
| **Query** | N+1 queries di beberapa view |-Performance turun |
| **Session** | File-based (default PHP) | Tidak cocok untuk multi-server |
| **Static Assets** | Tidak ada CDN | Load lambat |
| **Caching** | Rate limit cache saja | View cache tidak ada |

---

## 8. Rekomendasi Perbaikan

### 8.1 Struktur & Maintainability

**R1: pisahkan Konfigurasi dari Kode**
```
config/
├── app.php           # Tetap: paths, ENV
├── database.php      # BARU: DB config terpisah
├── security.php      # BARU: keys, JWT secrets
└── workflows.php    # BARU: step definitions
```

**R2: Gunakan Dependency Injection**
```php
// Sekarang (tidak testable):
class WorkflowService {
    private Registrasi $registrasiModel;
    public function __construct() {
        $this->registrasiModel = new Registrasi();
    }
}

// Rekomendasi:
class WorkflowService {
    private RegistrasiRepository $repo;
    public function __construct(RegistrasiRepository $repo) {
        $this->repo = $repo;
    }
}
```

**R3: Buat Repository Interfaces**
```php
interface RegistrasiRepository {
    public function findById(int $id): ?Registrasi;
    public function findByNomor(string $nomor): ?Registrasi;
    public function save(Registrasi $entity): bool;
    // ...
}
```

### 8.2 Performance

**R4: Query Optimization**
```php
// Sebelum: N+1 problem di view
foreach ($registrasi as $r) {
    $klien = (new Klien())->findById($r['klien_id']); // N+1!
}

// Sesudah: Eager load
$registrasi = Registrasi::with(['klien', 'layanan'])->get();
```

**R5: Add DB Index yang Kurang**
```sql
-- Missing indexes:
ALTER TABLE registrasi ADD INDEX idx_current_step (current_step_id);
ALTER TABLE registrasi ADD INDEX idx_target_completion (target_completion_at);
ALTER TABLE registrasi_history ADD INDEX idx_created (created_at);
```

**R6: Add Query Cache**
```php
// Menggunakan cache untuk data yang jarang berubah:
class WorkflowStepCache {
    private static ?array $cache = null;
    public static function getAll(): array {
        if (self::$cache === null) {
            self::$cache = (new WorkflowStep())->getAll();
        }
        return self::$cache;
    }
}
```

### 8.3 Security

**R7: Pisahkan Read/Write DB User**
```sql
-- User read-only untuk public tracking:
GRANT SELECT ON nora3.0.* TO 'nora_read'@'localhost';
GRANT SELECT, INSERT, UPDATE ON nora3.0.* TO 'nora_write'@'localhost';
```

**R8: Upgrade Password Hashing**
```php
// Migrasi ke argon2id dengan cost factor yang sama:
if (password_needs_rehash($hash, PASSWORD_ARGON2ID)) {
    // Simpan hash baru
}
```

**R9: Add Rate Limiting yang Lebih Baik**
- Gunakan Redis/Memcached untuk distributed rate limiting
- Implementasi sliding window algorithm

### 8.4 Scalability

**R10: Tambahkan JSON API**

```php
// routes.php
Router::add('api/v1/registrasi', 'GET', [ApiController::class, 'list'], ['auth' => true]);
Router::add('api/v1/registrasi/{id}', 'GET', [ApiController::class, 'show'], ['auth' => true]);

// response:
{
    "data": {
        "id": 1,
        "nomor_registrasi": "NR-2026-001",
        "status": "selesai"
    },
    "meta": {
        "timestamp": "2026-04-19T18:30:00Z"
    }
}
```

**R11: pisahkan Public/Admin App**
- Admin: Dashboard SPA (React/Vue)
- Public: Static + API untuk tracking

**R12: Use Message Queue untuk Background Jobs**
```php
// Kirim WhatsApp tidak blocking:
MessageQueue::publish('wa.notification', [
    'template' => 'wa_update',
    'data' => [...],
]);
```

---

## 9. Ringkasan Singkat

### Cara Kerja Sistem dalam 10 Poin

1. **Single Entry Point**: Semua request masuk melalui `public/index.php` yang melakukan bootstrap, load helpers, session, sanitization, lalu dispatch ke router.

2. **Route Registry**: Mapping URL ke Controller@Action didefinisikan di `config/routes.php` dengan support role-based access (`ROLE_OWNER`, `ROLE_STAFF`).

3. **Controller handling**: MainController (public), DashboardController (admin) menangani request dan memanggil service/entity sesuai kebutuhan.

4. **Service Layer**: WorkflowService meng orchestrasi perubahan status dengan transaksi database, validasi lock, audit trail di registrasi_history.

5. **Entity/Model**: Kelas di `app/Domain/Entities/` adalah Active Record yang langsung akses database via PDO wrapper (Database adapter).

6. **Workflow 15 Tahap**: Case bergerak dari Draft → Validasi → Pajak → Penomoran → Pendaftaran → BPN → Selesai/Diserahkan/Ditutup/Batal.

7. **Tracking Publik**: Klien lacak dengan nomor registrasi + verifikasi 4 digit terakhir HP; dapat lihat progress tanpa login.

8. **CMS Terintegrasi**: Konten website (hero, layanan, testimoni) disimpan di database (`cms_*` tables) dan di-render di homepage.

9. **Transaksi/Pembayaran**: Setiap registrasi bisa punya transaksi pembayaran yang tracking di tabel `transaksi` dan `transaksi_history`.

10. **Audit Trail**: Semua perubahan status tercatat di `registrasi_history` dengan flag kendala, user pengubah, IP address; login aktivitas di `audit_log`.

---

## Appendix: File Penting untuk Referensi

| File |行| Fungsi |
|-----|--|--------|
| `config/routes.php` | 1-108 | Route registry |
| `public/index.php` | 1-80 | Front controller |
| `app/Adapters/Database.php` | 1-187 | PDO adapter |
| `app/Domain/Entities/Registrasi.php` | 1-568 | Model registrasi |
| `app/Services/WorkflowService.php` | 1-225 | Business logic |
| `modules/Main/Controller.php` | 1-451 | Public controller |
| `modules/Dashboard/Controller.php` | - | Admin controller |
| `nora3_0 (3).sql` | 1-850 | Database schema |

---

*Generated: 2026-04-19*
*Sistem: NORA v1.1.2 - Notaris Sri Anah Case Management*