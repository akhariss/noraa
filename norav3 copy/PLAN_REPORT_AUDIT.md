# 📊 RENCANA FITUR LAPORAN AUDIT

> **Status:** Ide/Requirement — Implementasi setelah Registrasi & Finalisasi stabil
> **Last Updated:** 2026-04-12

---

## 🎯 GAMBARAN UMUM

Fitur laporan audit untuk Kantor Notaris Sri Anah SH.M.Kn yang memberikan **visibility penuh** terhadap operasional kantor dalam periode tertentu (bulanan/mingguan).

**Prinsip:**
- Periode → filter tanggal (`start_date` → `end_date`)
- Data driven → query dari database, bukan hardcode
- Storytelling → ringkasan naratif, bukan hanya angka
- Tabel → detail lengkap, bisa di-export (CSV/PDF nanti)

---

## 📋 BAGIAN 1: LAPORAN REGISTRASI PER PERIODE

### 1.1 Header Periode
```
Laporan Registrasi
Periode: 1 April 2026 — 30 April 2026
```

### 1.2 Ringkasan Cepat (Summary Cards)
| Metric | Nilai | Keterangan |
|---|---|---|
| Registrasi Baru | 15 | Masuk di periode ini |
| Registrasi Ditutup | 8 | Selesai/batal/ditutup di periode ini |
| Masih Aktif | 7 | Belum selesai per akhir periode |
| Total Tagihan | Rp 150.000.000 | Dari registrasi baru |
| Total Terbayar | Rp 120.000.000 | Dari semua transaksi periode ini |

### 1.3 Persebaran Layanan
Tabel atau chart:
```
Layanan          | Jumlah | Persentase
-----------------|--------|-----------
Jual Beli        | 5      | 33.3%
Hibah            | 4      | 26.7%
Waris            | 3      | 20.0%
Roya             | 2      | 13.3%
Lainnya          | 1      | 6.7%
```

### 1.4 Matrix Timeline Registrasi Aktif (PDF Layout)

**Format: Matrix — Baris = Registrasi, Kolom = Step**

**Legend (di atas tabel):**
```
Keterangan Step ID:
  1 = Draft
  2 = Pembayaran Admin
  3 = Validasi Sertifikat
  4 = Pengecekan Sertifikat
  5 = Pembayaran Pajak
  6 = Validasi Pajak
  7 = Penomoran Akta
  8 = Pendaftaran
  9 = Pembayaran PNBP
  10 = Pemeriksaan BPN
  11 = Perbaikan
  12 = Selesai
  13 = Diserahkan
  14 = Ditutup
  15 = Batal
```

**Tabel Matrix:**
| No | Nama Klien | Layanan | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | Status |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | Ahmad | Jual Beli | 1 | 3 | 2 | 5 | - | - | - | - | - | - | - | 🔴 Overdue (Pajak) |
| 2 | Budi | Waris | 2 | 1 | 4 | 3 | 1 | 2 | 1 | - | - | - | - | ✅ Normal |
| 3 | Citra | Hibah | 1 | 2 | - | - | - | - | - | - | - | - | - | ⏳ Baru |

**Aturan Isi Cell:**
- Angka = **jumlah hari** yang dihabiskan di step tersebut (dari `registrasi_history`: selisih tanggal masuk step → keluar step)
- `-` = belum pernah masuk step itu (masih di step awal) atau sudah selesai sebelum sampai step itu
- Cell yang sedang **diproses saat ini** (current_step_id) → **highlight kuning** + tampilkan angka hari sampai sekarang
- Cell yang **overdue** → **highlight merah** + angka hari

**Cara Hitung Durasi per Step:**
```sql
-- Dari registrasi_history: hitung hari di setiap step
SELECT 
    rh.registrasi_id,
    rh.status_new_id AS step_id,
    DATEDIFF(
        COALESCE(
            (SELECT MIN(rh2.created_at) 
             FROM registrasi_history rh2 
             WHERE rh2.registrasi_id = rh.registrasi_id 
               AND rh2.created_at > rh.created_at
               AND rh2.status_new_id != rh.status_new_id),
            NOW()
        ),
        rh.created_at
    ) AS hari_di_step
FROM registrasi_history rh
WHERE rh.created_at BETWEEN ? AND ?
```

**Catatan Khusus: Step Perbaikan (ID 11)**
- Step ini bisa **mundur** → registrasi kembali ke step sebelumnya
- Saat mundur: **tidak reset** durasi step sebelumnya, tapi **tambah entry baru** di `registrasi_history`
- Di matrix: kolom "Perbaikan" bisa muncul **lebih dari 1 kali** → tampilkan total (misal: `2+1` = 2 hari pertama + 1 hari setelah mundur)
- Atau tampilkan **jumlah total** semua hari di step perbaikan

---

### 1.5 Storytelling / Ringkasan Naratif
> **Contoh output:**
>
> "Periode April 2026, kantor menerima **15 registrasi baru** dengan layanan terbanyak adalah **Jual Beli (33%)**. Saat ini **7 registrasi masih aktif**, di mana **5 berjalan normal** sesuai SLA dan **2 mengalami keterlambatan** (overdue).
>
> Bottleneck utama ada di tahap **Pembayaran Pajak** (rata-rata 3 hari melebihi SLA) dan **Perbaikan** (2 registrasi harus diulang).
>
> Secara keseluruhan, **80% registrasi berjalan lancar** tanpa kendala."

### 1.6 Registrasi Batal/Ditutup
Tabel terpisah:
| No | No. Registrasi | Klien | Layanan | Status Akhir | Alasan | Tanggal Tutup |
|---|---|---|---|---|---|---|
| 1 | NP-20260402-002 | Dani | Roya | Batal | Klien membatalkan | 5 Apr 2026 |
| 2 | NP-20260410-009 | Eka | Waris | Ditutup | Selesai | 20 Apr 2026 |

---

## 💰 BAGIAN 2: LAPORAN KEUANGAN PER PERIODE

### 2.1 Ringkasan Keuangan
| Metric | Nilai |
|---|---|
| Total Masuk (Pembayaran) | Rp 120.000.000 |
| Jumlah Transaksi | 23 |
| Rata-rata per Transaksi | Rp 5.217.391 |

### 2.2 Detail Per Registrasi — Aktif & BELUM Lunas

**Filter:** Masih proses (aktif), belum lunas. Diurutkan dari sisa terbesar.

| No | No. Registrasi | Klien | Layanan | Total Tagihan | Sudah Bayar | Sisa | Keterangan |
|---|---|---|---|---|---|---|---|
| 1 | NP-20260401-001 | Ahmad | Jual Beli | Rp 15.000.000 | Rp 10.000.000 | Rp 5.000.000 | ⏳ Cicil |
| 2 | NP-20260405-003 | Budi | Waris | Rp 8.000.000 | Rp 0 | Rp 8.000.000 | ❌ Belum Bayar |

### 2.3 Detail Per Registrasi — Aktif & SUDAH Lunas

**Filter:** Masih proses (aktif), tapi sudah lunas (sisa = 0). Diurutkan dari tanggal lunas terbaru.

| No | No. Registrasi | Klien | Layanan | Total Tagihan | Total Bayar | Tanggal Lunas | Status |
|---|---|---|---|---|---|---|---|
| 1 | NP-20260408-007 | Citra | Hibah | Rp 12.000.000 | Rp 12.000.000 | 15 Apr 2026 | ✅ Lunas (Pembayaran Pajak) |
| 2 | NP-20260410-011 | Dian | Roya | Rp 5.000.000 | Rp 5.000.000 | 18 Apr 2026 | ✅ Lunas (Draft) |

### 2.4 Riwayat Pembayaran (Timeline)

Tabel semua transaksi pembayaran di periode ini:
| Tanggal | No. Registrasi | Klien | Nominal | Catatan | Oleh |
|---|---|---|---|---|---|
| 1 Apr 2026 | NP-20260401-001 | Ahmad | Rp 5.000.000 | DP | notaris |
| 5 Apr 2026 | NP-20260401-001 | Ahmad | Rp 5.000.000 | Pelunasan | notaris |
| 10 Apr 2026 | NP-20260405-003 | Budi | Rp 3.000.000 | Cicilan 1 | staff |

---

## 👥 BAGIAN 3: RANGKING AKTIVITAS USER (ADMIN & STAFF)

### 3.1 Konsep
Bukan timeline lengkap — tapi **rangking per kategori** siapa paling aktif di periode ini.
Format: **Tabel terpisah per jenis aktivitas**, diurutkan dari yang terbanyak.

### 3.2 🏆 Pembuat Registrasi Terbanyak
| Rank | Nama User | Role | Jumlah Registrasi Dibuat | Persentase |
|---|---|---|---|---|
| 1 | notaris | Administrator | 8 | 53.3% |
| 2 | staff1 | Staff | 5 | 33.3% |
| 3 | staff2 | Staff | 2 | 13.3% |
| **Total** | | | **15** | **100%** |

**Query:**
```sql
SELECT u.name, u.role, COUNT(DISTINCT rh.registrasi_id) as total
FROM registrasi_history rh
JOIN users u ON u.id = rh.user_id
WHERE rh.status_old_id IS NULL  -- Entry pertama = registrasi baru
  AND rh.created_at BETWEEN ? AND ?
GROUP BY u.id, u.name, u.role
ORDER BY total DESC;
```

### 3.3 🔄 Update Status Terbanyak
| Rank | Nama User | Role | Jumlah Status Diubah | Persentase |
|---|---|---|---|---|
| 1 | staff1 | Staff | 23 | 46.0% |
| 2 | notaris | Administrator | 18 | 36.0% |
| 3 | staff2 | Staff | 9 | 18.0% |
| **Total** | | | **50** | **100%** |

**Query:**
```sql
SELECT u.name, u.role, COUNT(*) as total
FROM registrasi_history rh
JOIN users u ON u.id = rh.user_id
WHERE rh.status_old_id IS NOT NULL  -- Hanya update (bukan buat baru)
  AND rh.status_new_id IS NOT NULL
  AND rh.created_at BETWEEN ? AND ?
GROUP BY u.id, u.name, u.role
ORDER BY total DESC;
```

### 3.4 🔒 Tutup Registrasi Terbanyak
| Rank | Nama User | Role | Jumlah Registrasi Ditutup | Selesai | Batal |
|---|---|---|---|---|---|
| 1 | notaris | Administrator | 6 | 5 | 1 |
| 2 | staff1 | Staff | 2 | 2 | 0 |
| **Total** | | | **8** | **7** | **1** |

**Query:**
```sql
SELECT 
    u.name, u.role,
    COUNT(*) as total,
    SUM(CASE WHEN ws.behavior_role = 4 THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN ws.behavior_role = 7 THEN 1 ELSE 0 END) as batal
FROM registrasi_history rh
JOIN users u ON u.id = rh.user_id
JOIN workflow_steps ws ON ws.id = rh.status_new_id
WHERE ws.behavior_role IN (4, 5, 6, 7)  -- Selesai, Diserahkan, Ditutup, Batal
  AND rh.created_at BETWEEN ? AND ?
GROUP BY u.id, u.name, u.role
ORDER BY total DESC;
```

### 3.5 💰 Input Pembayaran Terbanyak
| Rank | Nama User | Role | Jumlah Transaksi | Total Nominal | Rata-rata per Transaksi |
|---|---|---|---|---|---|
| 1 | notaris | Administrator | 15 | Rp 85.000.000 | Rp 5.666.667 |
| 2 | staff1 | Staff | 8 | Rp 35.000.000 | Rp 4.375.000 |
| **Total** | | | **23** | **Rp 120.000.000** | **Rp 5.217.391** |

**Query:**
```sql
SELECT 
    u.name, u.role,
    COUNT(*) as total_transaksi,
    SUM(th.nominal_bayar) as total_nominal,
    AVG(th.nominal_bayar) as rata_rata
FROM transaksi_history th
JOIN users u ON u.id = th.created_by
WHERE th.created_at BETWEEN ? AND ?
GROUP BY u.id, u.name, u.role
ORDER BY total_transaksi DESC;
```

### 3.6 📊 Ringkasan Aktivitas Tim
> **Contoh output storytelling:**
>
> "Periode April 2026, **Admin (notaris)** paling aktif membuat registrasi baru (53.3%), sementara **Staff (staff1)** paling sering update status (46%). 
> 
> Admin juga menangani pembayaran terbanyak (65.2% dari total transaksi).
>
> **Kesimpulan:** Pembagian kerja cukup merata, tapi Admin lebih fokus di front-office (registrasi + pembayaran), Staff lebih di back-office (update status/proses)."

---

## 🗂️ STRUKTUR FILE YANG AKAN DIBUAT

```
app/
├── Services/
│   └── ReportService.php              ← Core query logic
│
modules/
└── Dashboard/
    └── Controller.php                 ← Tambah 4-5 method baru
    │
resources/views/dashboard/
├── reports/
│   ├── index.php                      ← Landing page (pilih jenis laporan)
│   ├── registrasi.php                 ← Laporan registrasi + timeline + storytelling
│   ├── keuangan.php                   ← Laporan keuangan + detail + riwayat
│   └── history.php                    ← Timeline lengkap (gabungan semua)
│
database/
└── migration_004_report_indexes.sql   ← Index untuk performa query
```

---

## 🔧 PRASYARAT (Yang Harus Dibereskan Dulu)

### ✅ Sudah Selesai:
- [x] Validasi anti-triliun di transaksi
- [x] Validasi anti-overpayment
- [x] Fix type casting (int vs string)
- [x] Logger::app → Logger::info

### ⏳ Belum Selesai (Blocking):
- [ ] **Registrasi** — Logic create/update masih ada bug
- [ ] **Finalisasi** — Tutup/reopen case belum stabil
- [ ] **Dashboard** — War room analytics masih ada error
- [ ] **Audit Log** — Table schema belum lengkap (missing old_value, registrasi_id)

### 🔜 Setelah Prasyarat Selesai:
1. Buat migration fix `audit_log` table
2. Buat `ReportService.php`
3. Buat controller methods
4. Buat views
5. Test dengan data dummy
6. Tambah export CSV/PDF (optional)

---

## 📊 QUERY PENTING (Draft)

### Query: Registrasi Aktif dengan Timeline
```sql
SELECT 
    r.id,
    r.nomor_registrasi,
    k.nama AS klien_nama,
    l.nama_layanan,
    ws.label AS status_label,
    r.step_started_at,
    r.target_completion_at,
    ws.sla_days,
    DATEDIFF(r.target_completion_at, NOW()) AS sisa_hari,
    CASE 
        WHEN r.target_completion_at IS NULL THEN 'belum'
        WHEN r.target_completion_at < NOW() THEN 'overdue'
        ELSE 'normal'
    END AS sla_status,
    (SELECT COUNT(*) FROM kendala kd WHERE kd.registrasi_id = r.id AND kd.flag_active = 1) AS kendala_aktif
FROM registrasi r
LEFT JOIN klien k ON k.id = r.klien_id
LEFT JOIN layanan l ON l.id = r.layanan_id
LEFT JOIN workflow_steps ws ON ws.id = r.current_step_id
WHERE r.current_step_id NOT IN (
    SELECT id FROM workflow_steps WHERE behavior_role IN (4, 5, 6, 7)
) -- Exclude: selesai, diserahkan, ditutup, batal
ORDER BY r.created_at DESC;
```

### Query: Storytelling Data
```sql
-- Total per layanan
SELECT l.nama_layanan, COUNT(*) as jumlah
FROM registrasi r
JOIN layanan l ON l.id = r.layanan_id
WHERE r.created_at BETWEEN ? AND ?
GROUP BY l.nama_layanan
ORDER BY jumlah DESC;

-- Rata-rata delay per step
SELECT ws.label, AVG(DATEDIFF(r.updated_at, r.target_completion_at)) as avg_delay
FROM registrasi r
JOIN workflow_steps ws ON ws.id = r.current_step_id
WHERE r.target_completion_at < r.updated_at
  AND r.created_at BETWEEN ? AND ?
GROUP BY ws.label;

-- Jumlah perbaikan
SELECT COUNT(*) as total_perbaikan
FROM kendala
WHERE created_at BETWEEN ? AND ?;
```

---

## 🔍 FEASIBILITY ANALYSIS — Bisa Gak Dengan DB Sekarang?

### ✅ **BISA 100%** — Semua data yang dibutuhkan SUDAH ADA

| Komponen | Tabel Sumber | Status |
|---|---|---|
| **Registrasi Baru/Ditutup/Aktif** | `registrasi` + `klien` + `layanan` + `workflow_steps` | ✅ Lengkap |
| **Matrix Timeline per Step** | `registrasi_history` (pakai `status_old_id`, `status_new_id`, `created_at`) | ✅ Lengkap |
| **SLA Calculation** | `registrasi.step_started_at` + `registrasi.target_completion_at` + `workflow_steps.sla_days` | ✅ Lengkap |
| **Kendala/Perbaikan** | `kendala` (flag_active) + `registrasi_history` | ✅ Lengkap |
| **Keuangan** | `transaksi` + `transaksi_history` + `users` | ✅ Lengkap |
| **Rangking: Buat Registrasi** | `registrasi_history` WHERE `status_old_id IS NULL` + `users` | ✅ Lengkap |
| **Rangking: Update Status** | `registrasi_history` WHERE `status_old_id IS NOT NULL` + `users` | ✅ Lengkap |
| **Rangking: Tutup Registrasi** | `registrasi_history` WHERE `status_new_id` = step selesai/batal + `workflow_steps.behavior_role` | ✅ Lengkap |
| **Rangking: Pembayaran** | `transaksi_history` + `users` (via `created_by`) | ✅ Lengkap |

### ⚠️ **Tantangan Teknis:**

1. **Matrix Timeline** — Query agak kompleks (conditional aggregation), tapi **BISA**
2. **Step Perbaikan yang Mundur** — Perlu logic khusus: hitung total hari di step perbaikan dari multiple entries
3. **Print-friendly PDF** — CSS `@media print` harus rapi, matrix table bisa lebar

### 📋 **Tidak Perlu Migration Baru!**

Semua kolom yang dibutuhkan **sudah ada** di DB sekarang. Tidak perlu ALTER TABLE atau ADD COLUMN.

---

## 📊 QUERY DRAFT: Matrix Timeline (Yang Paling Kompleks)

```sql
-- Query utama: Matrix hari per step per registrasi
SELECT 
    r.id,
    k.nama AS klien_nama,
    l.nama_layanan,
    
    -- Hitung hari di setiap step (conditional aggregation)
    MAX(CASE WHEN ws.id = 1 THEN DATEDIFF(...) END) AS step_1,
    MAX(CASE WHEN ws.id = 2 THEN DATEDIFF(...) END) AS step_2,
    MAX(CASE WHEN ws.id = 3 THEN DATEDIFF(...) END) AS step_3,
    -- ... dst sampai step 11
    
    -- Status SLA
    CASE 
        WHEN r.target_completion_at < NOW() THEN 'overdue'
        WHEN r.target_completion_at IS NOT NULL THEN 'normal'
        ELSE 'belum'
    END AS sla_status
    
FROM registrasi r
LEFT JOIN klien k ON k.id = r.klien_id
LEFT JOIN layanan l ON l.id = r.layanan_id
LEFT JOIN registrasi_history rh ON rh.registrasi_id = r.id
LEFT JOIN workflow_steps ws ON ws.id = rh.status_new_id
WHERE r.created_at BETWEEN ? AND ?
  AND r.current_step_id NOT IN (14, 15) -- Exclude Ditutup & Batal
GROUP BY r.id, k.nama, l.nama_layanan
ORDER BY r.created_at DESC;
```

---

## 🎨 UI/UX NOTES (PDF Focus)

- **Print-friendly** → CSS `@media print` untuk layout A4 landscape
- **Matrix table** → Font kecil (10-11px), kolom step narrow (30-40px)
- **Color coding** → Gunakan background color (kuning/merah) bukan emoji untuk print
- **Legend** → Di atas matrix, list semua step ID dengan label
- **Page break** → Kalau matrix terlalu panjang, otomatis page break

---

## 📝 CATATAN TAMBAHAN

1. **Step Perbaikan** adalah satu-satunya step yang bisa **mundur**. Saat mundur:
   - Entry baru di `registrasi_history` dengan `status_old_id` = step yang ditinggalkan, `status_new_id` = step sebelumnya
   - Di matrix: kolom "Perbaikan" akan muncul beberapa kali → **sum total** semua hari

2. **SLA Calculation**:
   - Normal: `target_completion_at > NOW()`
   - Overdue: `target_completion_at < NOW()` dan status belum selesai
   - Belum: `target_completion_at IS NULL` atau step belum mulai

3. **Periode Filter**:
   - Registrasi baru: `created_at BETWEEN start AND end`
   - Registrasi aktif: `created_at <= end AND current_step belum terminal`
   - Pembayaran: `transaksi_history.created_at BETWEEN start AND end`

---

> **Next Step:** Setelah Registrasi & Finalisasi stabil, mulai implement dari `ReportService.php` → Controller → Views.
