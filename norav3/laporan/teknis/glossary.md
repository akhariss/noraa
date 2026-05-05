# Glossary - Glosarium Istilah Sistem Tracking Notaris

## A

### Admin
Staff administrasi kantor notaris yang memiliki akses ke dashboard untuk mengelola registrasi, update status, dan data klien. Role: `admin`.

### Akta
Dokumen hukum yang dibuat oleh notaris, seperti Akta Jual Beli, Akta Hibah, Surat Kuasa, dll.

### API (Application Programming Interface)
Antarmuka pemrograman yang memungkinkan komunikasi antara komponen sistem. Dalam sistem ini, API menggunakan query-parameter routing (`?gate=xxx`).

### Audit Log
Catatan lengkap semua aksi penting dalam sistem, termasuk login, logout, create, update, delete. Disimpan dalam tabel `audit_log`.

### Authentication
Proses verifikasi identitas pengguna melalui username dan password.

### Authorization
Proses penentuan hak akses pengguna berdasarkan role (RBAC).

## B

### BPN (Badan Pertanahan Nasional)
Instansi pemerintah yang menangani pendaftaran tanah dan sertifikat.

### Bcrypt
Algoritma hashing password yang digunakan dalam sistem ini dengan cost factor 12.

### Backup
Salinan database yang disimpan untuk keperluan recovery. Disimpan dalam `/storage/backups/`.

### Business Rules
Aturan bisnis yang mengatur workflow sistem, seperti status transition validation, cancellation limit, dll.

## C

### CMS (Content Management System)
Sistem manajemen konten untuk mengelola homepage, template pesan, dan pengaturan aplikasi.

### CSRF (Cross-Site Request Forgery)
Serangan keamanan dimana attacker memaksa user melakukan aksi tanpa sepengetahuan mereka. Dicegah dengan CSRF token.

### Controller
Komponen MVC yang menangani request user dan memanggil service/entity yang sesuai. Terletak di `/modules/`.

### CANCELLABLE_STATUSES
Array konstanta yang berisi status yang masih bisa dibatalkan: `['draft', 'pembayaran_admin', 'validasi_sertifikat', 'pencecekan_sertifikat', 'perbaikan']`.

## D

### Dashboard
Halaman utama untuk staff/notaris setelah login, menampilkan statistik dan menu navigasi.

### Database
MySQL/MariaDB database yang menyimpan semua data sistem.

### Domain-Driven Design
Pendekatan desain software yang berfokus pada domain business. Diimplementasikan dengan Entities di `/app/Domain/Entities/`.

## E

### Entity
Model data yang merepresentasikan tabel database dan berisi business logic. Contoh: `Registrasi`, `Klien`, `User`.

### Entity Relationship Diagram (ERD)
Diagram yang menunjukkan relasi antar tabel database.

## F

### Finalisasi
Proses penutupan kasus yang sudah selesai. Status berubah dari `selesai` ke `ditutup`.

### Flag Kendala
Penanda bahwa registrasi sedang mengalami kendala/hambatan. Disimpan dalam tabel `kendala`.

### Front Controller
Pola desain dimana semua request melalui satu entry point (`public/index.php`).

## G

### Green Computing
Praktik komputasi ramah lingkungan dengan mengurangi konsumsi energi. Diimplementasikan dengan caching, query optimization, rate limiting.

## H

### History (Registrasi History)
Catatan immutable semua perubahan status registrasi. Disimpan dalam tabel `registrasi_history`.

### HMAC-SHA256
Algoritma untuk membuat signature token tracking.

### HTTP Headers
Header keamanan yang dikirim dalam response, seperti X-Frame-Options, X-XSS-Protection, dll.

## I

### Input Sanitization
Proses pembersihan input user untuk mencegah XSS dan serangan lainnya. Dilakukan oleh `InputSanitizer` class.

## K

### Kendala
Entitas yang merepresentasikan hambatan dalam proses registrasi. Tabel: `kendala`.

### Klien
Pengguna layanan notaris. Data disimpan dalam tabel `klien`.

## L

### Layanan
Jenis layanan notaris yang tersedia. Data disimpan dalam tabel `layanan`.

### Lock Mechanism
Mekanisme untuk mengunci registrasi agar tidak bisa diupdate. Field: `is_locked`.

## M

### MVC (Model-View-Controller)
Pola arsitektur aplikasi dengan pemisahan Model (data), View (tampilan), Controller (logika).

### Module
Komponen fitur dalam sistem. Terletak di `/modules/`. Contoh: Main, Auth, Dashboard, CMS, Finalisasi, Media.

## N

### Notaris
Pemilik/pimpinan kantor notaris. Role: `notaris`. Memiliki akses penuh ke semua fitur sistem.

### Nomor Registrasi
Nomor unik untuk setiap registrasi. Format: `NP-YYYYMMDD-XXXX`. Contoh: `NP-20260326-1234`.

## P

### PNBP (Penerimaan Negara Bukan Pajak)
Biaya negara yang dibayarkan untuk proses pertanahan.

### Prepared Statements
Teknik untuk mencegah SQL injection dengan memisahkan SQL logic dari data.

### Progress Tracking
Visualisasi progress status registrasi dalam bentuk progress bar.

## R

### RBAC (Role-Based Access Control)
Sistem authorization berdasarkan role pengguna. Diimplementasikan dalam class `App\Security\RBAC`.

### Rate Limiting
Mekanisme untuk membatasi jumlah request dalam waktu tertentu. Diimplementasikan dalam class `App\Security\RateLimiter`.

### Registrasi
Entitas utama sistem yang merepresentasikan dokumen yang sedang diproses. Tabel: `registrasi`.

### Registrasi History
Tabel yang menyimpan riwayat perubahan status registrasi. Immutable (tidak bisa diubah).

### Role
Peran pengguna dalam sistem: `notaris`, `admin`, `publik`.

### Router
Komponen yang mengarahkan request ke controller yang sesuai. Class: `App\Core\Router`.

## S

### Security Headers
HTTP headers untuk keamanan: X-Frame-Options, X-Content-Type-Options, X-XSS-Protection, dll.

### Service
Layer business logic yang mengorchestrasi entities. Terletak di `/app/Services/`. Contoh: `WorkflowService`, `UserService`.

### Session
Mekanisme untuk menjaga state login user. Menggunakan fingerprinting untuk mencegah hijacking.

### Session Fingerprinting
Teknik keamanan dengan membuat hash dari User Agent + IP address untuk mendeteksi session hijacking.

### SQL Injection
Serangan keamanan dengan menyisipkan SQL code malicious melalui input. Dicegah dengan prepared statements.

### STATUS_ORDER
Array konstanta yang mendefinisikan urutan 14 status workflow.

### Status Workflow
14 status yang dilalui registrasi: draft → pembayaran_admin → validasi_sertifikat → pencecekan_sertifikat → pembayaran_pajak → validasi_pajak → penomoran_akta → pendaftaran → pembayaran_pnbp → pemeriksaan_bpn → perbaikan → selesai → diserahkan → ditutup.

## T

### Token Tracking
Token aman untuk akses tracking publik. Menggunakan HMAC-SHA256 signature dengan expiry 24 jam.

### Tracking
Fitur untuk klien mengecek status dokumen secara online.

## U

### Use Case Diagram
Diagram yang menunjukkan interaksi antara aktor dan use case dalam sistem.

### User
Pengguna sistem yang terautentikasi. Data disimpan dalam tabel `users`.

## V

### Verification Code
Kode acak yang digunakan untuk generate tracking token. Field: `verification_code`.

### View
Komponen MVC yang menangani tampilan. Terletak di `/resources/views/`.

## W

### Workflow
Alur proses registrasi dari status awal hingga final.

### WorkflowService
Service yang menangani business logic transisi status. Class: `App\Services\WorkflowService`.

## X

### XSS (Cross-Site Scripting)
Serangan keamanan dengan menyisipkan JavaScript malicious. Dicegah dengan `htmlspecialchars()` dan input sanitization.

---

## Indeks

| Istilah | Kategori | Halaman Referensi |
|---------|----------|-------------------|
| Admin | User Role | authentication.md |
| Akta | Domain | business_rules.md |
| API | Technical | api_design.md |
| Audit Log | Security | security_analysis.md |
| Authentication | Security | authentication.md |
| Authorization | Security | authentication.md |
| BPN | Domain | business_rules.md |
| Bcrypt | Security | authentication.md |
| Backup | Operations | deployment.md |
| Business Rules | Business | business_rules.md |
| CMS | Feature | api_design.md |
| CSRF | Security | security_analysis.md |
| Controller | Architecture | folder_blueprint.md |
| Dashboard | Feature | system_overview.md |
| Database | Technical | database_schema.md |
| Entity | Architecture | architecture.md |
| Finalisasi | Feature | api_design.md |
| Flag Kendala | Feature | business_rules.md |
| Green Computing | Best Practice | green_computing.md |
| HMAC-SHA256 | Security | security_analysis.md |
| Kendala | Entity | database_schema.md |
| Klien | Entity | database_schema.md |
| Layanan | Entity | database_schema.md |
| Lock Mechanism | Feature | business_rules.md |
| MVC | Architecture | architecture.md |
| Module | Architecture | module_interaction.md |
| Notaris | User Role | authentication.md |
| Nomor Registrasi | Domain | business_rules.md |
| PNBP | Domain | business_rules.md |
| RBAC | Security | authentication.md |
| Rate Limiting | Security | performance.md |
| Registrasi | Entity | database_schema.md |
| Service | Architecture | architecture.md |
| Session | Security | authentication.md |
| SQL Injection | Security | attacker_vs_defense.md |
| Status Workflow | Domain | business_rules.md |
| Token Tracking | Security | security_analysis.md |
| Tracking | Feature | system_overview.md |
| User | Entity | database_schema.md |
| View | Architecture | folder_blueprint.md |
| Workflow | Business | business_rules.md |
| XSS | Security | attacker_vs_defense.md |

---

## Kesimpulan

Glosarium ini mencakup 50+ istilah teknis dan domain yang digunakan dalam Sistem Tracking Status Dokumen Notaris. Istilah-istilah ini digunakan secara konsisten di seluruh dokumentasi untuk memastikan pemahaman yang sama antara developer, stakeholder, dan pengguna sistem.
