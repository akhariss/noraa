---
---
trigger: always_on
---
# Zero Computing Protocol (ZCP) v2.1 — PHP Native AI Agent Rules

> Stack: PHP Native · No Framework · Zero Trust · Green Computing
> Rule Type: Strict / Deterministic / AI-Executable
> Violation = Build Rejected

---

## TAG REFERENCE

| Tag            | Arti                                                                     |
| -------------- | ------------------------------------------------------------------------ |
| `[BASE]`     | Wajib dari hari pertama — soal kebenaran & keamanan, tidak bisa ditunda |
| `[OPT]`      | Aktifkan hanya saat trigger terpenuhi — soal kecepatan & efisiensi      |
| `[DILARANG]` | Jangan pernah                                                            |
| `[FAIL]`     | Auto-fail jika dilanggar                                                 |
| `[ZT]`       | Zero Trust                                                               |
| `[GREEN]`    | Green Computing                                                          |

---

## RULE 0 — META

**0.1** Semua keputusan AI harus bisa dijawab YES/NO. Tidak boleh ada "mungkin", "nanti", "tergantung".
**0.2** Folder dibuat hanya jika ada file aktif. File dibuat hanya jika ada fungsi yang dipakai sekarang.
**0.3** Setiap fitur = satu folder domain. Logic tidak boleh tersebar di file random.

### BASE vs OPT — Resolusi Kontradiksi

> "Jangan optimasi dulu" vs "no N+1, pagination wajib" — ini beda kategori.

**BASE** (kebenaran/keamanan — wajib sekarang):

- No SQL injection, ownership check, input validation
- No N+1 query — ini **bug logika**, bukan optimasi
- Pagination — tanpa ini 10rb row bisa ke-fetch sekaligus, ini **cacat desain**
- Prepared statement — prevention, bukan performa

**OPT** (kecepatan — aktifkan saat trigger):

- Caching → DB latency > 200ms atau query sama > 5x/menit
- Gzip → response > 10KB
- Async/queue → proses > 500ms
- Index tambahan → query spesifik > 100ms setelah EXPLAIN

---

## RULE 1 — STRUKTUR DIREKTORI `[BASE]`

```
/public         → index.php, /css, /js, /img
/src
  /Domain       → Business logic murni (ZERO external dependency)
  /Application  → Use case, orchestration, DTO
  /Infrastructure → DB, storage, mail, external API
  /Interface    → Controller, middleware, router, response
/config         → database.php, app.php
/storage        → /logs, /uploads, /cache (LUAR public root)
/migrations     → 001_create_users.sql, 002_add_index.sql
/tests          → /Unit, /Integration
.env            → TIDAK di-commit
.env.example    → WAJIB di-commit, selalu sync
```

**Dependency arah:** Interface → Application → Domain. Infrastructure → Application.
`[FAIL]` Domain import Controller · Controller akses DB langsung · folder kosong · file orphan

---

## RULE 2 — NAMING CONVENTION `[BASE]`

| Tipe             | Format      | Contoh                       |
| ---------------- | ----------- | ---------------------------- |
| Class PHP        | PascalCase  | `UserController.php`       |
| Method/function  | camelCase   | `getUserById()`            |
| Variable         | camelCase   | `$userId`, `$orderTotal` |
| Konstanta        | UPPER_SNAKE | `MAX_UPLOAD_SIZE`          |
| File config/view | kebab-case  | `user-profile.php`         |
| DB column        | snake_case  | `created_at`, `user_id`  |
| CSS/JS file      | kebab-case  | `form-handler.js`          |

`[FAIL]` Singkatan random: `$u`, `$dt`, `$tmp` · method tanpa aksi: `get()`, `handle()`, `process()`

---

## RULE 3 — STRUKTUR KODE `[BASE]`

- File ≤ 300 baris. Jika lebih → split class.
- Function ≤ 40 baris. Exception: switch/match boleh 60 baris.
- Nesting maksimal 3 level → gunakan early return.
- 1 class = 1 tanggung jawab. 1 function = 1 tujuan.
- Semua function wajib type declaration: `function getUserById(int $id): ?array`
- File pure PHP: jangan tutup tag `?>`

`[FAIL]` 1 function yang validasi + simpan + kirim email sekaligus · tanpa type declaration

---

## RULE 4 — SECURITY / ZERO TRUST `[BASE]` `[ZT]`

**Pipeline input wajib:**

```
$_POST/$_GET/$_FILES → Sanitasi → Validasi → DTO → Business Logic
```

**Sanitasi:**

```php
$name = trim(htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'));
// Whitelist field — reject unknown
$input = array_intersect_key($_POST, array_flip(['name','email']));
```

**Query — prepared statement selalu:**

```php
// BENAR
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ? AND user_id = ?");
// FAIL — concatenation
$q = "SELECT * FROM users WHERE id = " . $id;
```

**Ownership check wajib di setiap query:**

```php
WHERE id = :id AND user_id = :session_user_id
```

**Auth:**

```php
password_hash($pass, PASSWORD_ARGON2ID, ['memory_cost' => 65536]);
setcookie('s', $token, ['httponly'=>true,'secure'=>true,'samesite'=>'Strict']);
```

**HTTP Headers wajib:**

```php
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none';");
```

**Error response:** 400/401/403/500 generik. `[FAIL]` expose stack trace, query, atau path server.

**Rate limit** `[OPT]` → aktifkan jika endpoint publik: auth 5 req/min, API 100 req/min.
**CSRF** `[BASE]` → wajib untuk semua form mutasi (POST/PUT/DELETE).

---

## RULE 5 — PERFORMANCE / GREEN COMPUTING

**No SELECT *** `[BASE]` `[FAIL]` `[GREEN]`

```php
// BENAR: SELECT id, name FROM users WHERE id = ?
// FAIL:  SELECT * FROM users
```

**No N+1** `[BASE]` `[FAIL]` `[GREEN]`

```php
// FAIL: query di dalam loop
// BENAR: JOIN atau batch fetch
```

**Pagination** `[BASE]` `[FAIL]` `[GREEN]`

```php
$limit = min((int)($_GET['limit'] ?? 10), 100);
$stmt = $pdo->prepare("SELECT id, name FROM users LIMIT ? OFFSET ?");
```

**Memory — generator untuk data besar** `[BASE]` `[GREEN]`

```php
function getRows(PDO $pdo): Generator {
    $stmt = $pdo->query("SELECT id, name FROM users");
    while ($row = $stmt->fetch()) yield $row;
}
```

**DB Singleton** `[BASE]` `[GREEN]` — 1 koneksi per request, jangan buat koneksi di dalam loop.

**Timeout wajib** `[BASE]`: DB 3s · External API 5s · `curl_setopt($ch, CURLOPT_TIMEOUT, 5)`

**Caching** `[OPT]` — aktif jika DB latency > 200ms. TTL wajib ada. `[FAIL]` cache tanpa TTL.
**Gzip** `[OPT]` — aktif jika response > 10KB.

---

## RULE 6 — FRONTEND `[BASE]`

- Wajib `<meta name="viewport" content="width=device-width, initial-scale=1.0">`
- Mobile-first CSS, gunakan flex/grid
- Class CSS harus reusable, tidak spesifik berlebihan
- `[FAIL]` inline style di HTML (kecuali nilai benar-benar dinamis dari PHP)
- `[FAIL]` CSS class yang tidak dipakai di HTML manapun
- Gambar wajib compress, gunakan `loading="lazy"` untuk gambar di bawah fold
- `[FAIL]` asset > 500KB tanpa kompresi
- Script pakai `defer`, letakkan sebelum `</body>`
- `[FAIL]` load library besar untuk task yang bisa dilakukan vanilla JS

---

## RULE 7 — DATABASE `[BASE]`

- Migration: `{nomor}_{deskripsi}.sql` — berurutan, tidak boleh edit yang sudah jalan di prod
- Index wajib untuk kolom di WHERE, JOIN, ORDER BY pada tabel > 1000 row
- Multi-entity mutation wajib dibungkus transaction
- `[FAIL]` transaction melintasi external API call
- External ID (di URL/API): UUID v4 / ULID. Internal FK: auto increment boleh.

---

## RULE 8 — FILE UPLOAD `[BASE]` `[ZT]`

```php
// Validasi magic bytes — bukan ekstensi
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($_FILES['file']['tmp_name']);
if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) { /* reject */ }

// Simpan di luar public root, rename ke UUID
$dir = dirname(__DIR__) . '/storage/uploads/';
$name = bin2hex(random_bytes(16)) . '.jpg';
```

`[FAIL]` simpan di `/public/uploads/` · pakai nama asli dari user · validasi hanya ekstensi

---

## RULE 9 — LOGGING `[BASE]`

```php
error_log(json_encode([
    'ts' => date('c'), 'level' => 'error',
    'event' => 'login_failed', 'user_id' => $id,
    'ip' => $_SERVER['REMOTE_ADDR']
]) . PHP_EOL, 3, STORAGE_PATH.'/logs/app.log');
```

`[FAIL]` log password, token, NIK, atau nomor rekening · `debug` log di production
Retensi: app/error log 30 hari · audit log 1 tahun · temp upload 24 jam lalu hapus

---

## RULE 10 — DEPLOYMENT REALITY `[BASE]`

**Rollback wajib:** tag git setiap release. Rollback < 5 menit. `[FAIL]` deploy tanpa rollback plan.

**Migration — Expand → Migrate → Contract:**

```sql
-- FAIL — langsung rename (break production seketika)
ALTER TABLE users RENAME COLUMN username TO display_name;

-- BENAR
-- Phase 1: tambah kolom baru dulu
ALTER TABLE users ADD COLUMN display_name VARCHAR(50);
-- Phase 2: isi data, update kode, deploy, tunggu stabil
-- Phase 3: hapus kolom lama
ALTER TABLE users DROP COLUMN username;
```

`[FAIL]` migration tanpa `down()` function · drop/rename kolom langsung · NOT NULL tanpa DEFAULT di tabel berisi data

**Config sync:**
`[FAIL]` `.env` di-commit · key baru tidak ada di `.env.example` · hardcoded credential di kode

**Pre-deploy checklist:**

- [ ] Semua test lulus
- [ ] Tidak ada `var_dump`/`print_r`/`die` tersisa
- [ ] `.env.example` sudah sync
- [ ] Migration ditest di lokal/staging dan punya `down()`
- [ ] Git tag sudah dibuat

**Post-deploy (5 menit pertama):** cek `/health`, login flow, error log, response time.
Jika gagal → **rollback segera**, jangan coba fix di production.

---

## RULE 11 — TESTING `[BASE]`

- Gunakan **Testprite** · Unit di `/tests/Unit` · Integration di `/tests/Integration`
- Wajib cover: auth flow (100%), domain logic (≥70%), validasi input, error handling
- `[FAIL]` push ke main tanpa semua test lulus · skip test karena deadline

---

## RULE 12 — SELF-CLEANING `[BASE]`

Wajib sebelum setiap commit — hapus:

- [ ] Function tidak pernah dipanggil
- [ ] Variable tidak dipakai
- [ ] `require`/`include` tidak dipakai
- [ ] CSS class tidak ada di HTML
- [ ] `var_dump`, `print_r`, `die` sisa debug
- [ ] Comment-out code → hapus, jangan dikomen
- [ ] Folder kosong setelah cleanup

`[FAIL]` dead code dikomen · duplicate logic di 2+ tempat → extract ke function/class

---

## RULE 13 — DEFINITION OF DONE

Fitur SELESAI jika semua ini terpenuhi:

- [ ] Input divalidasi & disanitasi `[BASE]`
- [ ] Ownership check ada di semua query `[BASE]`
- [ ] Tidak ada N+1 / SELECT * / list tanpa pagination `[BASE]`
- [ ] Error tidak bocor ke client
- [ ] Security headers terpasang
- [ ] Tidak ada dead code / unused import / folder kosong
- [ ] CSS responsif, tidak ada class orphan, asset terkompresi
- [ ] Semua test lulus
- [ ] `.env.example` sync, migration punya `down()`, git tag siap

---

## RULE 14 — EXECUTION FLOW (AI WAJIB IKUTI)

```
1. ANALYZE  → Pahami requirement · tentukan stage · ada perubahan DB?
2. BUILD    → Buat hanya yang perlu · ikuti naming & struktur · BASE rules dari awal
3. VALIDATE → Cek BASE rules · cek query correctness · cek apakah OPT trigger terpenuhi
4. CLEANUP  → Hapus dead code · unused import · CSS orphan · folder kosong
5. TEST     → Jalankan Testprite · pastikan lulus
6. DEPLOY   → .env.example sync? · migration punya down()? · git tag siap? · rollback plan?
7. DONE     → Verifikasi Rule 13 · jika gagal → kembali ke step relevan
```

---

## ANTI-PATTERN AUTO-FAIL

| Pattern                            | Kategori   |
| ---------------------------------- | ---------- |
| SELECT *                           | BASE/GREEN |
| N+1 query                          | BASE/GREEN |
| SQL string concatenation           | BASE/ZT    |
| Tidak ada ownership check          | BASE/ZT    |
| `$_POST` langsung tanpa sanitasi | ZT         |
| Stack trace di response client     | ZT         |
| File upload tanpa magic bytes      | ZT         |
| Hardcoded credential               | ZT         |
| `.env` di-commit ke repo         | ZT         |
| Inline style di HTML               | GREEN      |
| CSS orphan                         | GREEN      |
| List tanpa pagination              | BASE       |
| Folder kosong                      | STRUCT     |
| Comment-out dead code              | CLEAN      |
| Duplicate logic di 2+ tempat       | CLEAN      |
| Deploy tanpa rollback plan         | DEPLOY     |
| Migration tanpa `down()`         | DEPLOY     |
| Drop/rename kolom langsung         | DEPLOY     |

---

```
BASE rules tidak pernah ditunda.
OPT rules tidak pernah dipaksakan.
DEPLOY rules tidak pernah diskip.

Zero Trust + Zero Waste = Zero Regret.
```

---
