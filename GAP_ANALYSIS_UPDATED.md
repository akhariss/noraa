# 📊 Gap Analysis - Updated
## Sistem Registrasi Notaris Nora v2.0

**Last Updated:** April 5, 2026  
**Status:** Partial Fixes Applied - See Section 7 for Completion Summary

---

## 1. Pendahuluan

Dokumen ini mengidentifikasi area-area dalam kode yang memerlukan perbaikan, konsolidasi, atau peningkatan. Analisis ini didasarkan pada pemeriksaan langsung terhadap kode sumber.

---

## 2. Area Kode yang Memerlukan Perbaikan (Updated Status)

### 2.1 Spaghetti Code & Duplikasi

| No | Lokasi | Masalah | Status | Solusi |
|----|--------|---------|--------|--------|
| **G-01** | `modules/Dashboard/Controller.php:64-67` | Query stats dipanggil langsung di controller | ⏳ Not Started | Pindahkan ke service layer |
| **G-02** | `modules/Main/Controller.php:48-125` | `loadHomepageData()` langsung di controller | ⏳ Not Started | Pindahkan ke CMS service |
| **G-03** | `app/Domain/Entities/Registrasi.php:308-378` | War Room methods di dalam entity | ⏳ Not Started | Pindahkan ke service terpisah |
| **G-04** | `app/Domain/Entities/Registrasi.php:44-58` dan `findById()` | Duplikasi join query | ⏳ Not Started | Gunakan single method dengan parameter |
| **G-05** | `modules/Dashboard/Controller.php:386-395` | Login check (`isAuthenticated`) dilakukan manual di banyak tempat | ✅ FIXED | Gunakan middleware yang konsisten |

### 2.2 Logika Duplikat

| No | Lokasi | Masalah | Status | Solusi |
|----|--------|---------|--------|--------|
| **G-06** | `modules/Auth/Controller.php` & `helpers.php` | Duplikasi session check (`isAuthenticated`, `requireAuth`) | ✅ FIXED | Auth/Controller sekarang menggunakan helper functions |
| **G-07** | `modules/Auth/Controller.php` & `helpers.php` | Duplikasi CSRF generation dan verification | ✅ FIXED | Auth/Controller sekarang menggunakan helper functions |
| **G-08** | `WorkflowService.php:128-140` & `FinalisasiService.php:186-194` | Duplikasi milestone reset logic | ✅ FIXED | Extracted ke `Registrasi->resetMilestones()` |
| **G-09** | `Registrasi.php:176-266` & `Registrasi.php:325-378` | Duplikasi filter logic | ⏳ Not Started | Gunakan abstraction yang lebih baik |

### 2.3 Validasi yang Hilang

| No | Lokasi | Masalah | Status | Solusi |
|----|--------|---------|--------|--------|
| **G-10** | `modules/Dashboard/Controller.php:429-506` | Tidak ada cek apakah registrasi sedang locked | ✅ FIXED | Added `locked` column & validation di WorkflowService |
| **G-11** | `app/Services/WorkflowService.php` | Tidak ada validasi self-action | ⏳ Not Started | Tambahkan audit trail yang lebih detail |
| **G-12** | `modules/Dashboard/Controller.php:511-589` | Update klien tanpa validasi klien_id | ⏳ Not Started | Tambahkan validasi |
| **G-13** | `app/Domain/Entities/Klien.php:27-38` | getOrCreate tidak handle race condition | ⏳ Not Started | Gunakan INSERT IGNORE |
| **G-14** | `modules/Main/Controller.php:350-385` | Fallback verification tanpa rate limiting consistency | ⏳ Not Started | Samakan rate limiting |

### 2.4 Struktur yang Tidak Konsisten

| No | Lokasi | Masalah | Status | Solusi |
|----|--------|---------|--------|--------|
| **G-15** | `config/app.php:84-99` | Role definitions tidak konsisten | ⏳ Not Started | Hapus alias, gunakan single definition |
| **G-16** | `app/Services/WorkflowService.php` | Mixed responsibility | ⏳ Not Started | Pisahkan jadi class/function terpisah |
| **G-17** | `modules/Dashboard/Controller.php` | Very large controller (>900 lines) | ⏳ Not Started | Pecah jadi multiple controllers |
| **G-18** | `app/Domain/Entities/` | Tidak ada consistent return type | ⏳ Not Started | Standarisasi return types |
| **G-19** | `app/Core/Utils/helpers.php:429-451` | Rate limiter file-based | ⏳ Not Started | Gunakan database atau Redis |

---

## 3. Missing Features yang Perlu Ditambahkan (Updated Status)

| No | Fitur | Lokasi yang Seharusnya | Status | Keterangan |
|----|-------|------------------------|--------|-----------|
| **G-20** | Lock mechanism di database | `registrasi` table | ✅ FIXED | Column `locked` sudah ditambahkan + methods lock/unlock |
| **G-21** | Batal flag | `registrasi` table | ✅ FIXED | Column `batal_flag` sudah ditambahkan + methods get/set |
| **G-22** | Verifikasi email untuk klien | `klien` table | ⏳ Not Started | Tambah validasi email |
| **G-23** | Pagination di audit logs | `modules/Dashboard/Controller.php:879-903` | ⏳ Not Started | Implementasi optimal |
| **G-24** | Proper error handling di view | Semua view | ⏳ Not Started | Error pages comprehensive |
| **G-25** | API documentation | N/A | ⏳ Not Started | Tidak ada OpenAPI/Swagger |
| **G-26** | Unit tests | N/A | ⏳ Not Started | Tidak ada unit test |

---

## 4. Database Schema Changes

### Applied Changes (v2.1)

```sql
-- Added to registrasi table
ALTER TABLE registrasi ADD COLUMN locked tinyint(1) DEFAULT 0 COMMENT 'Lock mechanism to prevent concurrent edits';
ALTER TABLE registrasi ADD COLUMN batal_flag tinyint(1) DEFAULT 0 COMMENT 'Flag to indicate cancellation status';
```

### New Methods in Registrasi Entity
- `isLocked(int $id): bool` - Check if registrasi is locked
- `lock(int $id): bool` - Lock a registrasi
- `unlock(int $id): bool` - Unlock a registrasi
- `setBatalFlag(int $id, bool $isBatal): bool` - Set cancellation flag
- `getBatalFlag(int $id): bool` - Get cancellation flag status
- `resetMilestones(int $id): bool` - Reset closure milestones (G-08 consolidation)

---

## 5. Code Consolidation Summary

### G-06: Session Check Functions Consolidated
**Before:** Duplicate methods in `Auth/Controller` and `helpers.php`
**After:** `Auth/Controller` methods now delegate to helper functions
```php
// Auth/Controller.php
public function isAuthenticated(): bool {
    return isLoggedIn(); // Delegated to helper
}
public function requireAuth(): void {
    requireAuth(); // Delegated to helper
}
```

### G-07: CSRF Token Functions Consolidated
**Before:** Duplicate methods in `Auth/Controller` and `helpers.php`
**After:** `Auth/Controller` methods now delegate to helper functions
```php
// Auth/Controller.php
public function generateCSRFToken(): string {
    return generateCSRFToken(); // Delegated to helper
}
public function verifyCSRFToken(string $token): bool {
    return verifyCSRFToken($token); // Delegated to helper
}
```

### G-08: Milestone Reset Logic Consolidated
**Before:** Duplicate reset logic in `WorkflowService` and `FinalisasiService`
**After:** Extracted to `Registrasi->resetMilestones()`
```php
// Both services now use:
$this->registrasiModel->resetMilestones($registrasiId);
```

### G-10: Lock Validation Added
**Before:** No validation if registrasi is locked
**After:** `WorkflowService->updateStatus()` checks locked flag
```php
if (!empty($registrasi['locked']) && (int)$registrasi['locked'] === 1) {
    return ['success' => false, 'message' => 'Registrasi sedang dikunci...'];
}
```

---

## 6. Prioritas Perbaikan (Updated)

### ✅ Prioritas Tinggi - COMPLETED
| No | Item | Status |
|----|------|--------|
| P1 | G-10: Lock validation | ✅ COMPLETED |
| P2 | G-06: Duplicate session check | ✅ COMPLETED |
| P3 | G-08: Duplicate milestone logic | ✅ COMPLETED |
| P4 | G-20/G-21: Add locked & batal_flag | ✅ COMPLETED |

### ⏳ Prioritas Sedang - PENDING
| No | Item | Alasan |
|----|------|--------|
| P5 | G-07: CSRF consolidation | ✅ COMPLETED |
| P6 | G-19: File-based rate limiter | Security - Pending |
| P7 | G-12, G-13: Validasi input | Security - Pending |
| P8 | G-15: Inconsistent role definitions | Maintainability - Pending |

### ⏳ Prioritas Rendah - PENDING
| No | Item | Alasan |
|----|------|--------|
| P9 | G-18: Inconsistent return types | Coding standards - Pending |
| P10 | G-17: Large controller refactor | Maintainability - Pending |
| P11 | G-25: API Documentation | Documentation - Pending |
| P12 | G-26: Unit Tests | Quality assurance - Pending |

---

## 7. Completion Summary

### ✅ Fixed (5 items)
- **G-06/G-07**: Consolidated duplicate session/CSRF functions in Auth/Controller
- **G-08**: Extracted milestone reset logic to `Registrasi->resetMilestones()`
- **G-10**: Added lock validation in WorkflowService before status updates
- **G-20**: Added `locked` column and lock/unlock methods to registrasi
- **G-21**: Added `batal_flag` column and flag getter/setter methods to registrasi

### ⏳ In Progress (0 items)

### ⏳ Not Started (21 items)
- G-01 through G-05: Spaghetti code refactoring
- G-09: Filter logic consolidation
- G-11 through G-19: Various validations and optimizations
- G-22 through G-26: Missing features and documentation

---

## 8. Rekomendasi Refactoring

### Phase 1: Code Quality (In Progress)
1. ✅ Session/Auth consolidation
2. ✅ CSRF consolidation  
3. ✅ Milestone reset consolidation
4. ⏳ Response type standardization (G-18)
5. ⏳ Filter logic consolidation (G-09)

### Phase 2: Validation & Security
1. ⏳ Lock mechanism (G-10) - COMPLETED
2. ⏳ Rate limiter improvement (G-19)
3. ⏳ Client validation (G-12)
4. ⏳ Email validation (G-22)
5. ⏳ Self-action validation (G-11)

### Phase 3: Architecture
1. ⏳ Controller refactoring (G-17)
2. ⏳ Service responsibility split (G-16)
3. ⏳ War room extraction (G-03)
4. ⏳ Query consolidation (G-04)

---

## 9. Implementation Notes

### Files Modified
1. `norasblmupdate2.sql` - Added locked & batal_flag columns
2. `app/Domain/Entities/Registrasi.php` - Added new methods + consolidated queries
3. `app/Services/WorkflowService.php` - Added lock validation
4. `app/Services/FinalisasiService.php` - Using resetMilestones()
5. `modules/Auth/Controller.php` - Consolidated to use helper functions

### Files to Monitor
- `modules/Dashboard/Controller.php` - Still has large method implementations (G-17)
- `app/Core/Utils/helpers.php` - File-based rate limiter (G-19)
- `app/Services/WorkflowService.php` - Mixed responsibilities (G-16)

---

## 10. Kesimpulan

Perbaikan tahap pertama (Phase 1) telah menyelesaikan 5 item kritis dari GAP analysis:
- ✅ Lock mechanism (G-20/G-21)
- ✅ Lock validation (G-10)
- ✅ Session consolidation (G-06)
- ✅ CSRF consolidation (G-07)
- ✅ Milestone consolidation (G-08)

Sistem sekarang memiliki:
- Single source of truth untuk authentication logic
- Proper lock mechanism untuk mencegah concurrent edits
- Consolidated milestone reset logic untuk maintainability

Perbaikan selanjutnya harus fokus pada:
1. Rate limiter improvement (G-19)
2. Controller refactoring (G-17)
3. Validation enhancements (G-12, G-13, G-14)
4. Architecture improvements (G-16, G-03)

---

**Versi**: 2.0  
**Tanggal Update**: April 5, 2026  
**Status**: Partial Implementation - Phase 1 Complete  
**Next Review**: After Phase 2 completion
