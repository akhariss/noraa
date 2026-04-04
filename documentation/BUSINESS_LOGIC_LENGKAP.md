# 📊 BUSINESS LOGIC - ALUR REGISTRASI LENGKAP
## Notaris & PPAT Tracking System

**Versi:** 2.0  
**Tanggal:** 2 Maret 2026  
**Status:** ✅ Final Implementation

---

## 🎯 OVERVIEW

Sistem ini mengelola lifecycle registrasi dari awal (create) sampai selesai (ditutup/batal) dengan 14 status yang mungkin, flag kendala, dan business rules yang ketat.

---

## 📋 DAFTAR STATUS LENGKAP (14 Status)

| No | Status Key | Label Display | Order | Estimasi | Bisa Batal? | Keterangan |
|----|-----------|---------------|-------|----------|-------------|------------|
| 1 | `draft` | Draft / Pengumpulan Persyaratan | 1 | 2 hari | ✅ YA | Initial status saat create |
| 2 | `pembayaran_admin` | Pembayaran Administrasi | 2 | 2 hari | ✅ YA | Klien bayar admin awal |
| 3 | `validasi_sertifikat` | Validasi Sertifikat | 3 | 7 hari | ✅ YA | Cek keaslian sertifikat |
| 4 | `pencecekan_sertifikat` | Pengecekan Sertifikat | 4 | 7 hari | ✅ YA | Verifikasi fisik sertifikat |
| 5 | `pembayaran_pajak` | Pembayaran Pajak | 5 | 1 hari | ❌ TIDAK | **BATAS PEMBATALAN** |
| 6 | `validasi_pajak` | Validasi Pajak | 6 | 5 hari | ❌ TIDAK | Cek bukti bayar pajak |
| 7 | `penomoran_akta` | Penomoran Akta | 7 | 1 hari | ❌ TIDAK | Assign nomor akta |
| 8 | `pendaftaran` | Pendaftaran | 8 | 5-7 hari | ❌ TIDAK | Daftar ke BPN |
| 9 | `pembayaran_pnbp` | Pembayaran PNBP | 9 | 1-2 hari | ❌ TIDAK | Biaya negara |
| 10 | `pemeriksaan_bpn` | Pemeriksaan BPN | 10 | 7-10 hari | ❌ TIDAK | BPN periksa berkas |
| 11 | `perbaikan` | Perbaikan | 11 | 3-7 hari | ✅ YA* | Koreksi dari BPN |
| 12 | `selesai` | Selesai | 12 | 1 hari | ❌ TIDAK | Akta selesai |
| 13 | `ditutup` | Ditutup | 13 | 1 hari | ❌ TIDAK | Final, read-only |
| 14 | `batal` | Batal | 14 | - | ❌ TIDAK | Registrasi dibatalkan |

**\*CATATAN:** Perbaikan bisa batal karena merupakan koreksi dari BPN, belum ada biaya baru yang keluar.

---

## 🔄 STATUS FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────────────┐
│                         STATUS FLOW DIAGRAM                          │
└─────────────────────────────────────────────────────────────────────┘

    START (Create Registrasi)
           │
           ▼
    ┌──────────────┐
    │    DRAFT     │ ─────┐
    │ (Default)    │      │
    └──────┬───────┘      │
           │              │ BATAL
           ▼              │ (Allowed ✅)
    ┌──────────────────┐  │
    │ Pembayaran Admin │──┤
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Validasi         │──┤
    │ Sertifikat       │  │
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Pengecekan       │──┤
    │ Sertifikat       │  │
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Pembayaran Pajak │──┼──❌ BATAL TIDAK BISA SETELAH INI
    └──────┬───────────┘  │  (Batas Pembatalan)
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Validasi Pajak   │  │
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Penomoran Akta   │  │
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Pendaftaran      │──┤
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Pembayaran PNBP  │  │
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Pemeriksaan BPN  │──┤
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │ Perbaikan        │──┤
    │ (Optional)       │  │ ✅ BATAL BISA LAGI!
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │     SELESAI      │  │
    └──────┬───────────┘  │
           │              │
           ▼              │
    ┌──────────────────┐  │
    │    DITUTUP       │──┘
    │ (Read-Only)      │
    └──────────────────┘
```

---

## 1️⃣ CREATE REGISTRASI (Tambah Registrasi Baru)

### 📍 Lokasi
```
Dashboard → Registrasi → Tambah Registrasi Baru
```

### 📝 Form Input
```
┌─────────────────────────────────────────┐
│  Tambah Registrasi Baru                    │
├─────────────────────────────────────────┤
│  Data Klien:                            │
│  • Nama Klien: [____________]           │
│  • No. HP: [____________]               │
│                                         │
│  Data Registrasi:                          │
│  • Jenis Layanan: [Dropdown ▼]          │
│  • Status: [Dropdown ▼]                 │
│  • Catatan: [____________]              │
│                                         │
│  [Batal] [Simpan Registrasi]               │
└─────────────────────────────────────────┘
```

### ✅ Status yang Boleh Dipilih Saat Create

**Hanya 4 status pertama:**
1. Draft (default)
2. Pembayaran Administrasi
3. Validasi Sertifikat
4. Pengecekan Sertifikat

### ❓ MENGAPA Hanya 4 Status Pertama?

**Alasan Bisnis:**
- Status setelah `pembayaran_pajak` **TIDAK BISA dibatalkan**
- Create registrasi dengan status setelah `pembayaran_pajak` dianggap tidak valid
- Sistem memaksa user memilih status awal yang **aman** (bisa masih dibatalkan)

**Contoh Valid:**
```
✅ Create → Draft
✅ Create → Pembayaran Admin
✅ Create → Validasi Sertifikat
✅ Create → Pengecekan Sertifikat
```

**Contoh Invalid:**
```
❌ Create → Pembayaran Pajak (sudah tidak bisa batal)
❌ Create → Selesai (tidak logis)
```

### 🔧 Code Implementation

**File: `controllers/DashboardController.php`**
```php
// Get status from POST, default to draft
$status = $_POST['status'] ?? STATUS_DRAFT;

// Validate status (only allow first 4 statuses for create)
$allowedCreateStatuses = [
    STATUS_DRAFT,
    STATUS_PEMBAYARAN_ADMIN,
    STATUS_VALIDASI_SERTIFIKAT,
    STATUS_PENCECEKAN_SERTIFIKAT
];

if (!in_array($status, $allowedCreateStatuses)) {
    echo json_encode([
        'success' => false,
        'message' => 'Status awal hanya boleh: Draft, Pembayaran Admin, Validasi Sertifikat, atau Pengecekan Sertifikat'
    ]);
    return;
}
```

### 🎁 Fitur Tambahan Setelah Create

**WhatsApp Popup:**
- Setelah create berhasil → popup WhatsApp muncul
- Data: Nama, Nomor Registrasi, Status
- Options: "Kirim WhatsApp" atau "Lewati"
- Jika "Kirim WhatsApp" → WA Web buka di tab baru
- Jika "Lewati" → redirect ke daftar registrasi

---

## 2️⃣ UPDATE STATUS (Progress Registrasi)

### 📍 Lokasi
```
Dashboard → Registrasi → Detail Registrasi → Update Status
```

### 📝 Yang Bisa Diupdate

1. **Status Baru** (dropdown)
2. **Catatan** (textarea)
3. **Flag Kendala** (checkbox)

### ✅ VALID (Boleh Update)

#### **Status Maju (Forward)**
```
Draft → Pembayaran Admin ✅
Pembayaran Admin → Validasi Sertifikat ✅
Validasi Sertifikat → Pengecekan Sertifikat ✅
... dst (maju terus)
```

#### **Status Batal (hanya sebelum pajak + perbaikan)**
```
Draft → Batal ✅
Pembayaran Admin → Batal ✅
Validasi Sertifikat → Batal ✅
Pengecekan Sertifikat → Batal ✅
Perbaikan → Batal ✅ (khusus!)
```

#### **Status Mundur (hanya dari Perbaikan)**
```
Perbaikan → Pembayaran Pajak ✅
Perbaikan → Validasi Pajak ✅
Perbaikan → Penomoran Akta ✅
Perbaikan → Pendaftaran ✅
Perbaikan → Pembayaran PNBP ✅
Perbaikan → Pemeriksaan BPN ✅
```

**MENGAPA Perbaikan Bisa Mundur?**
- Karena Perbaikan adalah **koreksi dari BPN**
- BPN menemukan kesalahan → berkas dikembalikan untuk diperbaiki
- Setelah diperbaiki → status kembali ke tahap sebelumnya untuk diverifikasi ulang
- Ini **bukan kemunduran normal**, tapi **loop koreksi**

#### **Hanya Catatan/Flag (Status Tetap)**
```
Status tetap, update catatan ✅
Status tetap, toggle flag kendala ✅
```

### ❌ INVALID (Tidak Boleh Update)

#### **Status Mundur (Kecuali dari Perbaikan)**
```
Validasi Sertifikat → Draft ❌
Pembayaran Pajak → Pengecekan Sertifikat ❌
Selesai → Pemeriksaan BPN ❌
```

**MENGAPA Tidak Bisa Mundur?**
- Setiap status mewakili **progress fisik** yang sudah dilakukan
- Tidak mungkin "un-do" progress fisik
- Contoh: Sudah bayar pajak → tidak bisa "un-pay"

#### **Batal Setelah Batas**
```
Pembayaran Pajak → Batal ❌
Validasi Pajak → Batal ❌
Penomoran Akta → Batal ❌
Pendaftaran → Batal ❌
... dst
```

**MENGAPA Ada Batas Pembatalan?**
- Setelah `pembayaran_pajak`, sudah ada **biaya yang dibayarkan ke negara**
- Pajak yang sudah bayar **tidak bisa refund**
- Ada **konsekuensi hukum** (akta sudah terdaftar)

#### **Update Jika Locked**
```
Registrasi Locked → Update status ❌
Registrasi Locked → Toggle flag ❌
Registrasi Locked → Update catatan ❌
```

#### **Update Jika Read-Only (Final Status)**
```
Status Selesai → Update ❌
Status Batal → Update ❌
Status Ditutup → Update ❌
```

---

## 3️⃣ PEMBATALAN (Batal)

### 📍 Kapan Bisa Batal?

| Status | Bisa Batal? | Alasan |
|--------|-------------|--------|
| Draft | ✅ YA | Belum ada biaya |
| Pembayaran Admin | ✅ YA | Baru admin internal |
| Validasi Sertifikat | ✅ YA | Belum ke BPN |
| Pengecekan Sertifikat | ✅ YA | Belum ke BPN |
| **Pembayaran Pajak** | ❌ **TIDAK** | **Sudah bayar ke negara** |
| Validasi Pajak | ❌ TIDAK | Sudah validasi |
| Penomoran Akta | ❌ TIDAK | Sudah terdaftar |
| Pendaftaran | ❌ TIDAK | Sudah di BPN |
| Pembayaran PNBP | ❌ TIDAK | Sudah ada PNBP |
| Pemeriksaan BPN | ❌ TIDAK | Berkas di BPN |
| **Perbaikan** | ✅ **YA** | **Koreksi BPN, belum biaya baru** |
| Selesai | ❌ TIDAK | Sudah selesai |
| Ditutup | ❌ TIDAK | Sudah final |
| Batal | ❌ TIDAK | Sudah batal |

### 🔧 Apa yang Terjadi Saat Batal?

1. **Status → `batal`**
2. **`batal_flag` → `TRUE`**
3. **Semua flag kendala dihapus (auto-delete)**
4. **Tidak bisa edit lagi (read-only)**
5. **Tidak bisa di-undo (harus create registrasi baru)**

### 📝 Code Implementation

**File: `models/Registrasi.php`**
```php
/**
 * Check if status can be cancelled
 */
public function canBeCancelled(int $id): bool {
    $registrasi = $this->findById($id);
    if (!$registrasi) return false;

    $cancellableStatuses = [
        'draft',
        'pembayaran_admin',
        'validasi_sertifikat',
        'pencecekan_sertifikat',
        'perbaikan'  // Added: Perbaikan can also be cancelled
    ];

    return in_array($registrasi['status'], $cancellableStatuses);
}

/**
 * Set batal flag only (status already changed)
 */
public function setBatalFlag(int $id): bool {
    $query = "UPDATE {$this->table} SET batal_flag = TRUE WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute(['id' => $id]);
}
```

---

## 4️⃣ FLAG KENDALA

### 📍 Apa Itu Flag Kendala?

- Penanda bahwa registrasi ini memiliki **kendala/masalah**
- Visual: 🚩 (bendera merah) di list registrasi
- Bisa ON/OFF via checkbox

### 📍 Kapan Bisa Toggle Flag?

| Status | Bisa Toggle? |
|--------|--------------|
| Draft - Perbaikan | ✅ YA |
| Selesai | ❌ TIDAK |
| Batal | ❌ TIDAK |
| Ditutup | ❌ TIDAK |

### 🔧 Apa yang Terjadi Saat Toggle Flag?

**Flag ON (checked):**
```
- Catat ke history: "Kendala aktif di tahap [status]"
- Visual: 🚩 ON di list
- Perlu monitoring ekstra
```

**Flag OFF (unchecked):**
```
- Catat ke history: "Kendala dinonaktifkan"
- Visual: - (tidak ada icon)
- Status kembali normal
```

### 🗑️ Auto-Delete Flag

**Kapan flag dihapus otomatis?**
- Saat status jadi **Selesai** → flag dihapus
- Saat status jadi **Batal** → flag dihapus

**MENGAPA Auto-Delete?**
- Selesai: Registrasi sudah selesai → tidak ada masalah lagi
- Batal: Registrasi sudah batal → tidak relevan lagi

### 📝 Code Implementation

**File: `services/WorkflowService.php`**
```php
// Auto-delete any active kendala flags for BATAL or SELESAI
if ($newStatus === STATUS_BATAL || $newStatus === STATUS_SELESAI) {
    // Use deactivateAll for reliability
    $this->kendalaModel->deactivateAll($registrasiId);
    error_log("FLAGS: Auto-deleted for registrasi_id=$registrasiId (status=$newStatus)");
}
```

---

## 5️⃣ LOCK REGISTRASI

### 📍 Apa Itu Lock?

- Membekukan registrasi agar **tidak bisa diedit sama sekali**
- Biasanya untuk audit atau freeze sementara

### 🔒 Effect Lock

**Registrasi Unlocked:**
```
- Update status ✅
- Update catatan ✅
- Toggle flag ✅
- Edit klien ✅
```

**Registrasi Locked:**
```
- Update status ❌
- Update catatan ❌
- Toggle flag ❌
- Edit klien ❌
```

### 👤 Siapa yang Bisa Lock?

- **Hanya Notaris** (role = 'notaris')
- Admin tidak bisa

---

## 6️⃣ READ-ONLY STATUS (Final)

### 📍 Status yang Bersifat Final

1. **Selesai** - Registrasi sudah selesai
2. **Batal** - Registrasi sudah dibatalkan
3. **Ditutup** - Registrasi sudah ditutup (final final)

### 🔒 UI Behavior

```
┌─────────────────────────────────────────────┐
│ ✓ Status Final - Tidak Dapat Diedit         │
│                                             │
│ Registrasi telah selesai dan siap untuk        │
│ diambil/ditutup.                            │
│                                             │
│ [Status: Selesai ▼] (DISABLED, gray)        │
└─────────────────────────────────────────────┘
```

### 📝 Code Implementation

**File: `views/dashboard/registrasi_detail.php`**
```php
<?php
$isReadOnlyStatus = in_array($registrasi['status'], ['batal', 'selesai', 'ditutup']);
if ($isReadOnlyStatus):
?>
<div style="background: #e8f5e9; border: 2px solid #4caf50;">
    <strong>✓ Status Final - Tidak Dapat Diedit</strong><br>
    <?php if ($registrasi['status'] === 'batal'): ?>
        Registrasi ini telah dibatalkan dan tidak dapat dilanjutkan kembali.
    <?php elseif ($registrasi['status'] === 'selesai'): ?>
        Registrasi telah selesai dan siap untuk diambil/ditutup.
    <?php else: ?>
        Registrasi telah ditutup dan bersifat read-only.
    <?php endif; ?>
</div>

<select id="status" name="status" disabled>
    <option value="<?= $registrasi['status'] ?>">
        <?= STATUS_LABELS[$registrasi['status']] ?>
    </option>
</select>
<?php endif; ?>
```

---

## 7️⃣ HISTORY & AUDIT

### 📍 Setiap Perubahan Dicatat

**Business History (registrasi_history):**
```
- Siapa yang update (user_name, role)
- Kapan (timestamp)
- Dari status apa ke status apa
- Catatan perubahan
- Flag kendala ON/OFF
- IP address
```

### 📝 Contoh Log

```
[2026-03-02 11:30:45] User: admin (notaris)
Status: draft → pembayaran_admin
Catatan: "Klien sudah bayar admin"
Flag: OFF
IP: 192.168.1.100
```

### 🔧 Code Implementation

**File: `services/WorkflowService.php`**
```php
// Save to REGISTRASI_HISTORY (BUSINESS LOG - PERMANENT!)
if ($statusChanged || $flagChanged || $catatanChanged) {
    $historyId = $this->registrasiHistoryModel->create([
        'registrasi_id' => $registrasiId,
        'status_old' => $oldStatus,
        'status_new' => $newStatus,
        'catatan' => $catatan,
        'flag_kendala_active' => $flagKendala,
        'user_id' => $userId,
        'user_name' => $userName,
        'user_role' => $role,
        'ip_address' => $_SERVER['REMOTE_ADDR']
    ]);
}
```

---

## 8️⃣ CONTOH SKENARIO LENGKAP

### 📖 Skenario 1: Normal Flow (Tanpa Kendala)

```
1. Create → Draft
   - User create registrasi baru
   - Status: draft (default)
   - WhatsApp popup muncul

2. Update → Pembayaran Admin
   - Klien bayar admin awal
   - Status: draft → pembayaran_admin
   - Catatan: "Klien sudah bayar admin"

3. Update → Validasi Sertifikat
   - Sertifikat mulai divalidasi
   - Status: pembayaran_admin → validasi_sertifikat

4. Update → Pengecekan Sertifikat
   - Verifikasi fisik sertifikat
   - Status: validasi_sertifikat → pencecekan_sertifikat

5. Update → Pembayaran Pajak
   - Klien bayar pajak
   - Status: pencecekan_sertifikat → pembayaran_pajak
   - ⚠️ BATAS PEMBATALAN (setelah ini tidak bisa batal)

6. Update → Validasi Pajak
   - Pajak divalidasi
   - Status: pembayaran_pajak → validasi_pajak

7. Update → Penomoran Akta
   - Akta dinomori
   - Status: validasi_pajak → penomoran_akta

8. Update → Pendaftaran
   - Daftar ke BPN
   - Status: penomoran_akta → pendaftaran

9. Update → Pembayaran PNBP
   - Bayar PNBP
   - Status: pendaftaran → pembayaran_pnbp

10. Update → Pemeriksaan BPN
    - BPN periksa berkas
    - Status: pembayaran_pnbp → pemeriksaan_bpn

11. Update → Selesai
    - Berkas selesai
    - Status: pemeriksaan_bpn → selesai
    - Flag kendala auto-delete

12. Update → Ditutup
    - Registrasi ditutup
    - Status: selesai → ditutup
    - Read-only permanent
```

### 📖 Skenario 2: Ada Kendala di Tengah

```
1. Create → Draft
2. Update → Pembayaran Admin
3. Update → Validasi Sertifikat
4. Toggle Flag ON 🚩
   - Ada masalah di sertifikat
   - Flag: OFF → ON
   - Catatan: "Sertifikat bermasalah"

5. Update → Pengecekan Sertifikat
   - Status tetap maju meski flag ON
   - Flag masih ON

6. Toggle Flag OFF ✅
   - Masalah selesai
   - Flag: ON → OFF

7. Update → Pembayaran Pajak
8. ... lanjut normal sampai Selesai
```

### 📖 Skenario 3: Batal di Awal

```
1. Create → Draft
2. User klik Batal
   - Pilih status: Batal
   - Konfirmasi di popup merah
   - Klik "Simpan Perubahan"

3. Sistem:
   - Status: draft → batal
   - batal_flag: TRUE
   - Redirect otomatis (500ms)
   - Read-only

4. Result:
   - Registrasi tidak bisa diedit lagi
   - HARUS CREATE REGISTRASI BARU jika mau lanjut
```

### 📖 Skenario 4: Batal di Perbaikan

```
1. Create → Draft
2. ... progress ...
3. Update → Pemeriksaan BPN
4. Update → Perbaikan
   - BPN menemukan kesalahan
   - Status: pemeriksaan_bpn → perbaikan

5. User klik Batal
   - Pilih status: Batal
   - Konfirmasi di popup merah
   - Klik "Simpan Perubahan"

6. Sistem:
   - Status: perbaikan → batal
   - batal_flag: TRUE
   - Flag kendala auto-delete
   - Redirect otomatis (500ms)

7. Result:
   - Registrasi batal
   - Tidak bisa diedit lagi
```

**MENGAPA Perbaikan Bisa Batal?**
- Karena Perbaikan adalah **koreksi**, bukan progress baru
- Belum ada biaya baru yang keluar
- Masih dalam tahap "perbaikan kesalahan"

### 📖 Skenario 5: Lock Registrasi

```
1. Create → Draft
2. Update → Pembayaran Admin
3. Lock Registrasi (oleh Notaris)
   - Registrasi dibekukan
   - Tidak bisa edit

4. Try Update → ERROR
   - "Registrasi terkunci, tidak dapat diubah"

5. Unlock Registrasi (oleh Notaris)
   - Registrasi dibuka kembali

6. Update → Validasi Sertifikat
   - BERHASIL
```

### 📖 Skenario 6: Perbaikan Loop (Mundur)

```
1. ... progress sampai Pemeriksaan BPN
2. Update → Perbaikan
   - BPN menemukan kesalahan
   - Status: pemeriksaan_bpn → perbaikan

3. Update → Penomoran Akta (MUNDUR!)
   - Koreksi kesalahan di penomoran
   - Status: perbaikan → penomoran_akta
   - ✅ VALID (khusus untuk perbaikan)

4. Update → Pendaftaran (MAJU LAGI)
   - Setelah dikoreksi, daftar lagi
   - Status: penomoran_akta → pendaftaran

5. ... lanjut sampai Selesai
```

**MENGAPA Perbaikan Bisa Mundur?**
- Ini **bukan kemunduran normal**
- Ini **loop koreksi**
- BPN menemukan kesalahan → berkas dikembalikan
- Setelah dikoreksi → status kembali ke tahap yang salah
- Lalu maju lagi setelah diperbaiki

---

## 9️⃣ VALIDASI LENGKAP

### ✅ CREATE REGISTRASI

**VALID:**
```
Status: draft ✅
Status: pembayaran_admin ✅
Status: validasi_sertifikat ✅
Status: pencecekan_sertifikat ✅
```

**INVALID:**
```
Status: pembayaran_pajak ❌ (sudah tidak bisa batal)
Status: selesai ❌ (tidak logis)
```

### ✅ UPDATE STATUS

**VALID:**
```
Status maju (forward) ✅
Status batal (jika sebelum pajak atau perbaikan) ✅
Status mundur (hanya dari perbaikan) ✅
Hanya catatan/flag (status tetap) ✅
```

**INVALID:**
```
Status mundur (kecuali dari perbaikan) ❌
Batal setelah pajak ❌
Update jika locked ❌
Update jika status read-only (selesai/batal/ditutup) ❌
Tidak ada perubahan sama sekali ❌
```

---

## 🔟 QUICK REFERENCE

### 📍 Create Registrasi
```
Location: Dashboard → Registrasi → Tambah Registrasi Baru
Default Status: Draft
Allowed Status: Draft, Pembayaran Admin, Validasi Sertifikat, Pengecekan Sertifikat
After Create: WhatsApp popup muncul
```

### 📍 Update Status
```
Location: Dashboard → Registrasi → Detail → Update Status
Rules:
  ✅ Status bisa maju
  ❌ Status tidak bisa mundur (kecuali dari Perbaikan)
  ❌ Batal hanya sebelum Pembayaran Pajak + Perbaikan
  ❌ Tidak bisa update jika locked
  ❌ Tidak bisa update jika selesai/batal/ditutup
```

### 📍 View Progress
```
Location: Dashboard → Registrasi → Detail → Timeline
Display: All 14 statuses with completion status
```

### 📍 Flag Kendala
```
Location: Dashboard → Registrasi → Detail → Flag Kendala
Rules:
  ✅ Bisa toggle jika status draft - perbaikan
  ❌ Tidak bisa toggle jika selesai/batal/ditutup
  🗑️ Auto-delete saat status jadi selesai/batal
```

---

## 📊 DATABASE SCHEMA

### Tabel: `registrasi`
```sql
CREATE TABLE registrasi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    klien_id INT,
    layanan_id INT,
    nomor_registrasi VARCHAR(50),
    status ENUM('draft', 'pembayaran_admin', ..., 'batal'),
    catatan_internal TEXT,
    locked TINYINT(1) DEFAULT 0,
    batal_flag TINYINT(1) DEFAULT 0,
    tracking_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Tabel: `kendala`
```sql
CREATE TABLE kendala (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registrasi_id INT,
    tahap VARCHAR(100),
    flag_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Tabel: `registrasi_history`
```sql
CREATE TABLE registrasi_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registrasi_id INT,
    status_old VARCHAR(50),
    status_new VARCHAR(50),
    catatan TEXT,
    flag_kendala_active TINYINT(1),
    flag_kendala_tahap VARCHAR(100),
    user_id INT,
    user_name VARCHAR(100),
    user_role ENUM('notaris', 'admin', 'publik'),
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🔐 KEAMANAN & AUDIT

### Authorization

- ✅ Hanya **Notaris** yang bisa membatalkan registrasi
- ✅ Admin tidak bisa batal (hanya notaris)
- ✅ Session validation required

### Audit Trail

- ✅ Semua perubahan dicatat di `registrasi_history`
- ✅ IP address disimpan
- ✅ User name dan role disimpan
- ✅ Timestamp otomatis

---

## 📞 SUPPORT

Jika ada pertanyaan tentang business logic:

1. Cek file `config/constants.php` untuk daftar status
2. Cek file `services/WorkflowService.php` untuk validasi
3. Cek file `models/Registrasi.php` untuk database operations
4. Cek file ini untuk business logic lengkap

---

**Dibuat oleh:** Development Team  
**Tanggal:** 2 Maret 2026  
**Last Updated:** 2 Maret 2026  
**Version:** 2.0 (Final)
