# 📘 DOKUMEN LENGKAP — NORA v2.0

> Sistem Manajemen Kantor Notaris Sri Anah SH.M.Kn
> Dibuat: 12 April 2026

---

# DAFTAR ISI

1. [Gambaran Aplikasi](#1-gambaran-aplikasi)
2. [Semua File dalam Proyek](#2-semua-file-dalam-proyek)
3. [Database — 18 Tabel](#3-database--18-tabel)
4. [Alur: Tambah Registrasi](#4-alur-tambah-registrasi)
5. [Alur: Daftar Registrasi](#5-alur-daftar-registrasi)
6. [Alur: Detail Registrasi](#6-alur-detail-registrasi)
7. [Alur: Finalisasi](#7-alur-finalisasi)
8. [Semua Bug yang Ditemukan](#8-semua-bug-yang-ditemukan)
9. [Rencana Fitur Laporan Audit](#9-rencana-fitur-laporan-audit)
10. [Yang Harus Dibereskan Dulu](#10-yang-harus-dibereskan-dulu)

---

# 1. Gambaran Aplikasi

**Apa ini?** Aplikasi manajemen kantor notaris untuk:
- Mencatat perkara/registrasi baru
- Melacak status perkara melalui 15 tahap workflow
- Mengelola pembayaran
- CMS untuk halaman publik
- Tracking publik (klien lacak status perkara)

**Teknologi:**
- PHP 8.2 + MariaDB 10.4
- Custom MVC framework (bukan Laravel/CodeIgniter)
- Front Controller pattern
- PSR-4 Autoloader

**Arsitektur (dari bawah ke atas):**
```
Database → Entity → Service → Controller → View
```

---

# 2. Semua File dalam Proyek

## 2.1 Entry Point

| File | Fungsi |
|---|---|
| `public/index.php` | Pintu masuk semua request |
| `public/image.php` | Serving gambar aman |

## 2.2 Core (`app/Core/`)

| File | Fungsi |
|---|---|
| `Autoloader.php` | Load class otomatis (PSR-4) |
| `Router.php` | Routing, cek auth, rate limit, dispatch |
| `View.php` | Render template aman (anti XSS) |
| `Utils/helpers.php` | Fungsi bantuan: CSRF, auth, format tanggal, logging |
| `Utils/security_helpers.php` | Enkripsi ID, gambar, token tracking |
| `Utils/security.php` | (Kosong/deprecated) |
| `Utils/seo_helpers.php` | Meta tag SEO untuk halaman publik |

## 2.3 Adapters (`app/Adapters/`)

| File | Fungsi |
|---|---|
| `Database.php` | Koneksi database (PDO singleton) |
| `Logger.php` | Catat log (app.log, error.log, security.log) |

## 2.4 Security (`app/Security/`)

| File | Fungsi |
|---|---|
| `Auth.php` | Session management, login, fingerprint |
| `CSRF.php` | Token CSRF |
| `InputSanitizer.php` | Bersihkan input user di bootstrap |
| `RateLimiter.php` | Batasi request per IP |
| `RBAC.php` | Kontrol akses berbasis role (tidak dipakai) |

## 2.5 Entity (`app/Domain/Entities/`) — 16 File

| File | Fungsi |
|---|---|
| `Registrasi.php` | CRUD perkara, filter, statistik, lock, batal |
| `RegistrasiHistory.php` | Catat setiap perubahan status |
| `Klien.php` | Data klien, get-or-create |
| `Layanan.php` | Jenis layanan (Jual Beli, Waris, dll) |
| `WorkflowStep.php` | 15 tahap workflow |
| `Kendala.php` | Flag hambatan per perkara |
| `Transaksi.php` | Header pembayaran (1 per registrasi) |
| `TransaksiHistory.php` | Riwayat pembayaran |
| `AuditLog.php` | Log audit sistem |
| `User.php` | Manajemen user |
| `CMSPage.php` | Halaman CMS |
| `CMSPageSection.php` | Section CMS |
| `CMSSectionContent.php` | Konten CMS |
| `CMSSectionItem.php` | Item CMS (tombol, testimoni, dll) |
| `MessageTemplate.php` | Template WhatsApp |
| `NoteTemplate.php` | Template catatan internal |

## 2.6 Services (`app/Services/`) — 6 File

| File | Fungsi |
|---|---|
| `WorkflowService.php` | Engine utama: update status, SLA, progress |
| `TransaksiService.php` | Logic pembayaran |
| `FinalisasiService.php` | Tutup/buka kembali perkara |
| `UserService.php` | CRUD user |
| `CMSEditorService.php` | Edit CMS |
| `BackupService.php` | Backup/restore database |

## 2.7 Controllers (`modules/`) — 6 File

| File | Fungsi |
|---|---|
| `Auth/Controller.php` | Login, logout |
| `Main/Controller.php` | Halaman publik (home, tracking) |
| `Dashboard/Controller.php` | Admin utama (1084 baris!) |
| `Finalisasi/Controller.php` | Finalisasi perkara |
| `CMS/Controller.php` | Editor CMS |
| `Media/Controller.php` | Upload/serving gambar |

## 2.8 Views (`resources/views/`) — 30 File

| Folder | File |
|---|---|
| `auth/` | `login.php` |
| `company_profile/` | `home.php` + 8 partials |
| `dashboard/` | 15 file (index, registrasi, users, dll) |
| `public/` | `tracking.php`, `registrasi_detail.php` |
| `errors/` | `403.php`, `error.php` |

## 2.9 Config & Database

| File | Fungsi |
|---|---|
| `config/app.php` | Konstanta: DB, role, 15 workflow steps |
| `config/routes.php` | 55 rute |
| `database/nora3_0 (1).sql` | Dump database lengkap |
| `database/migration_*.sql` | Script migrasi |

---

# 3. Database — 18 Tabel

## 3.1 Tabel Inti

### `users` — Pengguna Sistem
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| username | VARCHAR(100) | Unik |
| name | VARCHAR(100) | Nama tampil |
| password_hash | VARCHAR(255) | Terenkripsi |
| role | VARCHAR(50) | `administrator` atau `staff` |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Data:** 3 user (admin, notaris, tes)

---

### `klien` — Data Klien
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| nama | VARCHAR(255) | Nama klien |
| hp | VARCHAR(20) | Nomor HP |
| email | VARCHAR(255) | Opsional |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Data:** 40 record (banyak data uji coba)

---

### `layanan` — Jenis Layanan
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| nama_layanan | VARCHAR(255) | Nama layanan |
| deskripsi | TEXT | Opsional |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Data:** 7 layanan (Lainnya, Hibah, Waris, PHB, Roya, Jual Beli, nikah)

---

### `workflow_steps` — 15 Tahap Proses
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| step_key | VARCHAR(50) | Unik (draft, selesai, dll) |
| label | VARCHAR(100) | Nama tampil |
| sort_order | INT | Urutan |
| sla_days | INT | Target hari |
| behavior_role | INT | 0=Normal, 1=Start, 2=Iterasi, 3=Perbaikan, 4=Selesai, 5=Diserahkan, 6=Ditutup, 7=Batal |
| is_cancellable | TINYINT | Bisa dibatalkan? |

**15 Tahap:**

| ID | step_key | label | SLA | behavior_role |
|---|---|---|---|---|
| 1 | draft | Draft / Pengumpulan Persyaratan | 2 hari | 0 (Normal) |
| 2 | pembayaran_admin | Pembayaran Administrasi | 4 hari | 1 (Start) |
| 3 | validasi_sertifikat | Validasi Sertifikat | 7 hari | 1 (Start) |
| 4 | pencecekan_sertifikat | Pengecekan Sertifikat | 7 hari | 1 (Start) |
| 5 | pembayaran_pajak | Pembayaran Pajak | 1 hari | 2 (Iterasi) |
| 6 | validasi_pajak | Validasi Pajak | 5 hari | 2 (Iterasi) |
| 7 | penomoran_akta | Penomoran Akta | 1 hari | 2 (Iterasi) |
| 8 | pendaftaran | Pendaftaran | 7 hari | 2 (Iterasi) |
| 9 | pembayaran_pnbp | Pembayaran PNBP | 2 hari | 2 (Iterasi) |
| 10 | pemeriksaan_bpn | Pemeriksaan BPN | 10 hari | 2 (Iterasi) |
| 11 | perbaikan | Perbaikan | 5 hari | 3 (Perbaikan) |
| 12 | selesai | Selesai | 1 hari | 4 (Selesai) |
| 13 | diserahkan | Diserahkan | 3 hari | 5 (Diserahkan) |
| 14 | ditutup | Ditutup | 1 hari | 6 (Ditutup) |
| 15 | batal | Batal | 1 hari | 7 (Batal) |

---

### `registrasi` — Perkara Utama
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| klien_id | INT | FK → klien |
| layanan_id | INT | FK → layanan |
| nomor_registrasi | VARCHAR(50) | Unik (NP-YYYYMMDD-XXXX) |
| current_step_id | INT | FK → workflow_steps |
| step_started_at | DATETIME | Kapan step ini mulai |
| target_completion_at | DATETIME | Target selesai |
| selesai_batal_at | DATETIME | Tgl selesai/batal |
| diserahkan_at | DATETIME | Tgl diserahkan |
| ditutup_at | DATETIME | Tgl ditutup |
| keterangan | TEXT | Keterangan |
| verification_code | VARCHAR(10) | Kode verifikasi publik |
| tracking_token | VARCHAR(255) | Token akses publik |
| catatan_internal | TEXT | Catatan internal |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Data:** 8 record (5 aktif, 1 selesai, 1 diserahkan, 1 ditutup)

---

### `registrasi_history` — Riwayat Perubahan Status
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| registrasi_id | INT | FK → registrasi |
| status_old_id | INT | Status sebelumnya |
| status_new_id | INT | Status baru |
| action | VARCHAR(100) | Update/Finalisasi/Re-open |
| target_completion_at_old | DATETIME | Target lama |
| target_completion_at_new | DATETIME | Target baru |
| keterangan | TEXT | |
| catatan | TEXT | Catatan perubahan |
| flag_kendala_active | TINYINT | Ada kendala? |
| flag_kendala_tahap | VARCHAR(100) | Tahap kendala |
| user_id | INT | Yang mengubah |
| ip_address | VARCHAR(45) | IP address |
| created_at | TIMESTAMP | |

**Data:** 25 entry

---

### `transaksi` — Pembayaran Header (1 per registrasi)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| registrasi_id | INT | FK → registrasi (UNIK) |
| total_tagihan | DECIMAL(15,2) | Total yang harus dibayar |
| jumlah_bayar | DECIMAL(15,2) | Total yang sudah dibayar |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Data:** 5 record

---

### `transaksi_history` — Riwayat Pembayaran
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| transaksi_id | INT | FK → transaksi |
| nominal_bayar | DECIMAL(15,2) | Jumlah bayar (positif/negatif) |
| tanggal_bayar | DATE | Tanggal bayar |
| catatan | TEXT | |
| created_by | INT | Yang input |
| created_at | TIMESTAMP | |

**Data:** 6 entry

---

### `kendala` — Flag Hambatan
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| registrasi_id | INT | FK → registrasi |
| workflow_step_id | INT | FK → workflow_steps |
| flag_active | TINYINT | 1=aktif, 0=selesai |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Data:** 2 record (1 aktif, 1 selesai)

---

### `audit_log` — Log Audit Sistem
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| user_id | INT | FK → users |
| role | ENUM | notaris/admin |
| action | VARCHAR(50) | Jenis aksi |
| new_value | TEXT | Data baru (JSON) |
| timestamp | TIMESTAMP | |

**MASALAH:** Tidak punya kolom `old_value` dan `registrasi_id`. **Isi: 0 row** (tidak pernah ditulis).

---

### Tabel CMS (4 tabel)

| Tabel | Fungsi | Data |
|---|---|---|
| `cms_pages` | Halaman CMS | 1 (home) |
| `cms_page_sections` | Section per halaman | 8 section |
| `cms_section_content` | Teks/konten | 44 entry |
| `cms_section_items` | Item (tombol, testimoni) | 38 entry |

### Tabel Lainnya

| Tabel | Fungsi | Data |
|---|---|---|
| `message_templates` | Template WA | 2 |
| `note_templates` | Template catatan | 15 |
| `cleanup_log` | Log cleanup | 0 |
| `audit_log_backup_20260226` | Backup lama | 0 |

---

# 4. Alur: Tambah Registrasi

## 4.1 File yang Terlibat

```
View:        resources/views/dashboard/registrasi_create.php
Controller:  modules/Dashboard/Controller.php → storeRegistrasi()
Service:     app/Services/TransaksiService.php
Entity:      Klien, Layanan, WorkflowStep, Registrasi, RegistrasiHistory, AuditLog
Tabel:       klien, registrasi, registrasi_history, audit_log, transaksi
```

## 4.2 Langkah Demi Langkah

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. USER BUKA HALAMAN                                            │
│    URL: ?gate=registrasi_create                                 │
│    Controller: DashboardController::createRegistrasi()          │
│    Data: Semua klien, semua layanan, semua workflow step        │
│    View: registrasi_create.php                                  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. USER ISI FORM                                                 │
│    - Nama klien                                                 │
│    - HP klien                                                   │
│    - Layanan (dropdown)                                         │
│    - Status awal (dropdown → workflow step)                     │
│    - Target tanggal selesai (opsional)                          │
│    - Keterangan                                                 │
│    - Catatan internal                                           │
│    - Total tagihan (opsional, default 0)                        │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼ (POST ?gate=registrasi_store)
┌─────────────────────────────────────────────────────────────────┐
│ 3. CONTROLLER PROSES                                             │
│    a. Cari/buat klien → Klien::getOrCreate()                    │
│    b. Buat nomor registrasi → "NP-YYYYMMDD-XXXX"                │
│    c. Ambil workflow step → WorkflowStep::findById()            │
│    d. Hitung target → step_started_at + sla_days                │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. SIMPAN KE DATABASE                                            │
│    a. INSERT klien (jika baru)                                  │
│    b. INSERT registrasi                                         │
│    c. INSERT registrasi_history (status_old=NULL, status_new=X) │
│    d. INSERT audit_log (action=create)                          │
│    e. INSERT transaksi (jika total_tagihan > 0)                 │
│    f. UPDATE registrasi (tracking_token)                        │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 5. RESPON                                                       │
│    Return JSON: {success, message, registrasi_id, tracking_url} │
│    Frontend redirect ke halaman detail                          │
└─────────────────────────────────────────────────────────────────┘
```

---

# 5. Alur: Daftar Registrasi

## 5.1 File yang Terlibat

```
View:        resources/views/dashboard/registrasi.php
Controller:  modules/Dashboard/Controller.php → registrasi()
Entity:      Registrasi, Klien, Layanan, Kendala
Tabel:       registrasi, klien, layanan, workflow_steps, kendala
```

## 5.2 Langkah

```
1. User buka ?gate=registrasi
2. Controller ambil data dengan filter:
   - Search (nomor registrasi / nama klien)
   - Status (filter by workflow step)
   - Layanan (filter by layanan_id)
   - Flag kendala (ada/tidak)
   - Pagination (page, per_page)

3. Query:
   SELECT registrasi.* 
   JOIN klien ON klien.id = registrasi.klien_id
   JOIN layanan ON layanan.id = registrasi.layanan_id
   JOIN workflow_steps ON workflow_steps.id = registrasi.current_step_id
   LEFT JOIN kendala ON kendala.registrasi_id = registrasi.id
   WHERE ... filter ...
   ORDER BY created_at DESC
   LIMIT 20 OFFSET 0

4. Untuk tiap registrasi → cek kendala aktif

5. Render tabel dengan:
   - Nomor registrasi
   - Nama klien
   - Layanan
   - Status saat ini
   - Flag (batal, lock, overdue, kendala)
   - Tombol aksi (lihat, edit, hapus)
```

---

# 6. Alur: Detail Registrasi

## 6.1 File yang Terlibat

```
View:        resources/views/dashboard/registrasi_detail.php
Controller:  modules/Dashboard/Controller.php → showRegistrasi()
Service:     WorkflowService, TransaksiService
Entity:      Semua entity utama
```

## 6.2 Sections di Halaman Detail

| Section | Fungsi | Endpoint |
|---|---|---|
| Info Klien | Edit nama, HP, target, keterangan | POST ?gate=update_klien |
| Progress Bar | Visual 15 step workflow | - (render dari data) |
| Update Status | Ganti status perkara | POST ?gate=update_status |
| Kendala Toggle | Aktif/nonaktif flag kendala | POST ?gate=toggle_kendala |
| Lock/Unlock | Kunci registrasi | POST ?gate=toggle_lock |
| Pembayaran | Set tagihan, tambah bayar | POST ?gate=transaksi_store |
| Kirim WA | Auto-fill template WA | GET ?gate=cms_get_msg_tpl |
| History | Timeline perubahan | AJAX fetch |
| Finalisasi | Link ke finalisasi (jika step terminal) | ?gate=registrasi_detail_finalisasi |

## 6.3 Alur Update Status (Paling Kompleks)

```
1. User pilih step baru → isi catatan → submit

2. Controller: DashboardController::updateStatus()
   → Validasi CSRF
   → Ambil registrasi → Registrasi::findById()
   → Ambil workflow step → WorkflowStep::findById()

3. Service: WorkflowService::updateStatus()
   a. Cek registrasi valid
   b. Ambil status lama
   c. Cek transisi valid (behavior logic)
   d. Jika step = "perbaikan" → boleh mundur
   e. Update registrasi (current_step_id, step_started_at, target)
   f. Jika step terminal → set timestamp milestone
   g. Buat history entry
   h. Jika ada kendala aktif → nonaktifkan
   i. Buat audit log
   j. Return success/fail

4. Response JSON → frontend refresh
```

---

# 7. Alur: Finalisasi

## 7.1 File yang Terlibat

```
View:        resources/views/dashboard/finalisasi.php
             resources/views/dashboard/registrasi_detail_finalisasi.php
Controller:  modules/Finalisasi/Controller.php
Service:     FinalisasiService
Entity:      Registrasi, RegistrasiHistory, WorkflowStep
```

## 7.2 Langkah

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. ADMIN BUKA ?gate=finalisasi                                  │
│    → FinalisasiController::index()                              │
│    → FinalisasiService::getFinalisasiList()                     │
│    → Query: registrasi WHERE current_step_id IN (12,13,14,15)   │
│    → Render daftar yang sudah selesai/diserahkan/ditutup/batal  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. ADMIN KLIK "TUTUP"                                           │
│    → POST ?gate=tutup_registrasi                                │
│    → FinalisasiService::tutupRegistrasi()                       │
│    a. Cek current step = terminal                               │
│    b. UPDATE registrasi: current_step_id=14, ditutup_at=NOW()   │
│    c. INSERT registrasi_history (action="Finalisasi")           │
│    d. INSERT audit_log                                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. ADMIN KLIK "BUKA KEMBALI" (Reopen)                           │
│    → POST ?gate=reopen_case                                     │
│    → FinalisasiService::reopenCase()                            │
│    a. Cek current step = terminal                               │
│    b. Cari step sebelumnya (non-terminal)                       │
│    c. UPDATE registrasi: current_step_id=step_lama, ditutup=NULL│
│    d. INSERT registrasi_history (action="Re-open")              │
│    e. INSERT audit_log                                          │
└─────────────────────────────────────────────────────────────────┘
```

---

# 8. Semua Bug yang Ditemukan

## 8.1 Bug Kritis (🔴 Harus Segera Diperbaiki)

| No | Bug | Lokasi | Dampak |
|---|---|---|---|
| 1 | Method `toggleKendala()` tidak ada di WorkflowService | DashboardController line 660 | Error 500 saat toggle kendala |
| 2 | Method `lockRegistrasi()` tidak ada di WorkflowService | DashboardController | Error 500 saat lock |
| 3 | Method `unlockRegistrasi()` tidak ada di WorkflowService | DashboardController | Error 500 saat unlock |
| 4 | Registrasi create/update masih ada bug | StoreRegistrasi | Data tidak konsisten |
| 5 | Finalisasi tutup/reopen belum stabil | FinalisasiService | Error saat tutup/buka perkara |

## 8.2 Bug Medium (🟡 Perlu Diperbaiki)

| No | Bug | Lokasi | Dampak |
|---|---|---|---|
| 6 | `audit_log` tidak punya kolom `old_value` | Database | Tidak bisa track perubahan |
| 7 | `audit_log` tidak punya kolom `registrasi_id` | Database | Tidak bisa link ke perkara |
| 8 | `audit_log.role` enum tidak match `users.role` | Database vs users | Data role salah |
| 9 | `audit_log` tidak pernah ditulis (0 rows) | Database | Audit tidak jalan |
| 10 | Tidak ada FK di registrasi, kendala, registrasi_history | Database | Bisa ada data yatim |
| 11 | Nomor registrasi kosong (ID 4) | registrasi table | Pelanggaran bisnis |
| 12 | Overpayment di data lama | transaksi table | Angka tidak realistis |

## 8.3 Bug Minor (🟢 Bisa Ditunda)

| No | Bug | Lokasi | Dampak |
|---|---|---|---|
| 13 | Data dummy di klien (80% test data) | klien table | Mengganggu laporan |
| 14 | `verification_code` selalu NULL | registrasi table | Fitur tidak jalan |
| 15 | `selesai_batal_at` selalu NULL | registrasi table | Milestone tidak tercatat |
| 16 | `behavior_role` comment tidak match data | workflow_steps | Bingungkan developer |
| 17 | Dashboard Controller 1084 baris | Dashboard/Controller | Sulit maintain |

---

# 9. Rencana Fitur Laporan Audit

## 9.1 Gambaran Umum

Laporan audit memberikan **gambaran lengkap** operasional kantor dalam satu periode (bulanan/mingguan).

**Tujuan:**
- Monitoring kinerja
- Transparansi aktivitas
- Kontrol keuangan
- Evaluasi SLA

## 9.2 Struktur Laporan

```
BAGIAN 1: LAPORAN REGISTRASI
├─ Ringkasan Cepat (cards)
├─ Persebaran Layanan
├─ Matrix Timeline per Step
├─ Storytelling / Ringkasan Naratif
└─ Registrasi Batal / Ditutup

BAGIAN 2: LAPORAN KEUANGAN
├─ Ringkasan Keuangan
├─ Detail: Aktif & BELUM Lunas
├─ Detail: Aktif & SUDAH Lunas
└─ Riwayat Pembayaran (Timeline)

BAGIAN 3: RANGKING AKTIVITAS USER
├─ Pembuat Registrasi Terbanyak
├─ Update Status Terbanyak
├─ Tutup Registrasi Terbanyak
├─ Input Pembayaran Terbanyak
└─ Ringkasan Aktivitas Tim
```

## 9.3 Matrix Timeline (Fitur Unggulan)

**Format:** Baris = Registrasi, Kolom = Step ID, Isi = Hari di step tersebut

```
Legend: 1=Draft, 2=Pembayaran Admin, 3=Validasi Sertifikat, dst.

┌────┬────────┬─────────┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬──────────┐
│ No │ Klien  │ Layanan │ 1 │ 2 │ 3 │ 4 │ 5 │ 6 │ 7 │ 8 │ 9 │10 │11 │ Status   │
├────┼────────┼─────────┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼──────────┤
│ 1  │ Ahmad  │ Jual Beli│ 1 │ 3 │ 2 │ 5 │ - │ - │ - │ - │ - │ - │ - │ 🔴 Pajak│
│ 2  │ Budi   │ Waris   │ 2 │ 1 │ 4 │ 3 │ 1 │ 2 │ 1 │ - │ - │ - │ - │ ✅ Normal│
└────┴────────┴─────────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴──────────┘
```

**Aturan:**
- Angka = jumlah hari di step tersebut
- `-` = belum pernah di step itu
- Cell kuning = sedang diproses
- Cell merah = overdue

## 9.4 Feasibility

**BISA 100%** — Semua data yang dibutuhkan sudah ada di database.

| Komponen Laporan | Sumber Data | Status |
|---|---|---|
| Registrasi Baru/Ditutup/Aktif | `registrasi` | ✅ Ada |
| Matrix Timeline | `registrasi_history` | ✅ Ada |
| SLA | `registrasi` + `workflow_steps` | ✅ Ada |
| Kendala | `kendala` | ✅ Ada |
| Keuangan | `transaksi` + `transaksi_history` | ✅ Ada |
| Ranking User | `registrasi_history` + `transaksi_history` + `users` | ✅ Ada |

**Tidak perlu migration baru.** Hanya perlu query yang kreatif.

## 9.5 File yang Perlu Dibuat

```
app/Services/ReportService.php
modules/Dashboard/Controller.php  ← tambah method baru
resources/views/dashboard/reports/index.php
resources/views/dashboard/reports/registrasi.php
resources/views/dashboard/reports/keuangan.php
resources/views/dashboard/reports/aktivitas.php
```

**Estimasi:** 5-8 hari kerja.

---

# 10. Yang Harus Dibereskan Dulu

## Prioritas 1 — Bug Kritis (Sebelum Laporan)

| No | Yang Harus Diperbaiki | Estimasi |
|---|---|---|
| 1 | Fix `WorkflowService` — tambah method toggleKendala, lock, unlock | 2 jam |
| 2 | Fix Registrasi create/update — logic bug | 4 jam |
| 3 | Fix Finalisasi — tutup/reopen stabil | 4 jam |

## Prioritas 2 — Medium (Sebelum atau Bersamaan Laporan)

| No | Yang Harus Diperbaiki | Estimasi |
|---|---|---|
| 4 | Fix `audit_log` table — tambah kolom old_value, registrasi_id | 1 jam |
| 5 | Fix Dashboard war room — error analytics | 2 jam |
| 6 | Cleanup data dummy — hapus klien/test data tidak valid | 30 menit |

## Prioritas 3 — Low (Bisa Nanti)

| No | Yang Harus Diperbaiki | Estimasi |
|---|---|---|
| 7 | Tambah FK constraints | 1 jam |
| 8 | Fix verification_code, selesai_batal_at | 2 jam |
| 9 | Refactor Dashboard Controller (1084 baris) | 4 jam |

---

> **Setelah Prioritas 1 & 2 selesai → mulai implement Laporan Audit.**
> Detail teknis lengkap ada di `PLAN_REPORT_AUDIT.md`
> Versi presentasi ada di `REPORT_AUDIT_PRESENTASI.md`
