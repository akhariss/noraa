# Fitur Aplikasi Nora 2.0

## Gambaran Umum

Aplikasi Nora 2.0 adalah sistem manajemen registrasi dan pengajuan layanan notaris yang dilengkapi dengan fitur pelacakan publik dan dashboard analitik.

---

## 3 Fitur Utama

### 1. Manajemen Registrasi & Workflow
Sistem inti untuk mengelola pengajuan layanan notaris dari awal hingga selesai.

**Fitur:**
- Pembuatan registrasi baru dengan data klien dan layanan
- Workflow status: Draft → Proses → Penyerahan → Selesai/Batal → Ditutup
- Timeline progress dengan milestone dan estimasi SLA
- Target completion dengan deadline otomatis
- Flag kendala/masalah pada setiap tahap
- Lock/unlock registrasi untuk proteksi data
- History tracking setiap perubahan status

**File terkait:** `modules/Dashboard/Controller.php:262-419`

---

### 2. Pelacakan Publikasi (Public Tracking)
Publik dapat melacak status pengajuan mereka secara real-time.

**Fitur:**
- Pencarian dengan nomor registrasi
- Verifikasi dengan 4 digit terakhir nomor HP
- Tampilan timeline progress publik
- Log proses (history) yang transparan
- Token keamanan (expire 24 jam)

**File terkait:** `modules/Main/Controller.php:130-424`

---

### 3. Dashboard War Room & Analytics
Monitoring performa dan workload secara real-time untuk tim internal.

**Fitur:**
- Statistik kasus: aktif, overdue, pending approval
- Carousel tugas penting
- Filter multi-dimensi (status, layanan, periode, pembayaran)
- Chart analytics periodik (harian/mingguan/bulanan)
- Quick access ke registrasi terbaru

**File terkait:** `modules/Dashboard/Controller.php:57-86`

---

## Fitur Pendukung

### Manajemen User
- Login/logout dengan CSRF protection & rate limiting
- Role-based access (Owner/Administrator & Staff)
- CRUD user management
- Audit log setiap aktivitas

**File:** `modules/Auth/Controller.php`, `modules/Dashboard/Controller.php:775-890`

---

### Manajemen Klien & Layanan
- Auto-create/get klien berdasarkan nama & HP
- CRUD layanan notaris
- Pengaturan template catatan per status workflow
- Template pesan WhatsApp customizable

**File:** `modules/CMS/Controller.php:362-503`

---

### Manajemen Pembayaran
- Inisialisasi tagihan saat registrasi
- Pembayaran awal (DP)
- Pembayaran cicilan
- Validasi: tagihan tidak boleh < sudah dibayar
- Summary pembayaran real-time

**File:** `modules/Dashboard/Controller.php:1066-1143`

---

### CMS Editor (Content Management)
- Edit halaman beranda/perusahaan
- Kelola layanan notaris
- Edit template pesan & catatan
- Pengaturan aplikasi (kontak, nama, dll)

**File:** `modules/CMS/Controller.php:32-644`

---

### Finalisasi & Penutupan
- Daftar registrasi selesai/batal/ditutup
- Tutup registrasi (Administrator only)
- Reopen registrasi yang sudah ditutup
- Filter dan pagination finalisasi

**File:** `modules/Finalisasi/Controller.php`

---

### Upload Media
- Upload gambar untuk konten CMS
- Max 5MB, format: JPG, PNG, WebP
- Encrypt filename untuk keamanan
- Auto-replace gambar lama

**File:** `modules/Media/Controller.php`

---

### Backup & Audit
- Backup database
- Backup seluruh site
- Download backup
- Audit log semua aktivitas

**File:** `modules/Dashboard/Controller.php:932-1030`

---

## Alur Kerja Utama

```
Registrasi Baru
     │
     ▼
┌─────────┐
│  Draft  │ ← Klien & layanan ditentukan
└────┬────┘
     │ Update status
     ▼
┌─────────┐
│ Proses  │ ← Tim notaris mengerjakan
└────┬────┘
     │ Flag kendala jika ada masalah
     ▼
┌────────────┐
│ Diserahkan │ ← Berkas siap diserahkan
└─────┬──────┘
      │ Penyerapan ke klien
      ▼
┌─────────┐
│ Selesai │ ──── Atau ──── ┌────────┐
└────┬────┘                │ Batal  │
     │                      └────────┘
     ▼
┌─────────┐
│ Ditutup │ ← Administrator menutup
└─────────┘
```

---

## Catatan Teknis

- **Framework:** Custom PHP MVC
- **Modular:** SK-14 Architecture
- **Security:** CSRF, Rate Limiting, Role-based Access
- **Database:** MySQL dengan Entity-Service pattern
