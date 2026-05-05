# 🚀 NORA V3 ARCHITECTURE & MODERNIZATION PLAN
**Document Status:** Final Audit & Master Plan
**Target:** Industry Standard, Zero Trust, Green Computing, High Modularity

---

## 📊 1. EXECUTIVE SUMMARY & ANALYSIS
Aplikasi Nora saat ini (V2.0) memiliki fondasi arsitektur custom MVC yang baik (menggunakan `App\Core\Router`, namespace PSR-4, dan PDO Adapter). Namun, karena banyak *code* di-_generate_ secara parsial oleh AI, muncul beberapa "Tech Debt" (Hutang Teknis) yang signifikan:

1. **Spaghetti Code & Pelanggaran DRY (Don't Repeat Yourself):** Logika JavaScript (`fetch` AJAX, Modal logic) dan CSS di-copy-paste ke setiap halaman (`users.php`, `registrasi.php`, `finalisasi.php`).
2. **Inkonsistensi UI/UX:** Pembuatan Badge/Label, Modal, dan Form berbeda-beda di tiap halaman. Contoh: Ada `atomic_modal.php` tapi `users.php` masih membuat modal konfirmasi sendiri (hardcoded).
3. **Mega-Controller:** Controller masih bercampur aduk memegang logika bisnis, manipulasi array, dan rendering HTML.
4. **Security (Belum Sepenuhnya Zero Trust):** Token CSRF sudah ada, tapi implementasi dan pengecekannya belum terpusat secara konsisten di semua *endpoint* POST.

---

## 🛠️ 2. PHASE 1: MODULARITY & REFACTORING (CLEAN CODE)
Fokus utama: Menghilangkan spaghetti code dan mengadopsi pola **Service Repository Pattern** secara penuh.

### 2.1. Standardisasi JavaScript (Core.js)
Semua fungsi AJAX, SweetAlert/Modal, dan Form Handling harus dipusatkan di satu file `public/assets/js/core.js`.
* **Action:** Hapus blok `<script>` panjang di bawah setiap halaman `resources/views/dashboard/*.php`.
* **Solusi:** Buat class/fungsi global seperti `NoraApp.submitForm(formId, endpoint)` dan `NoraApp.confirmAction(message, callback)`.

### 2.2. Implementasi Atomic Design (UI Components)
Berhenti menulis HTML panjang untuk elemen berulang.
* **Badges:** Buat helper `renderBadge($text, $colorType)` alih-alih hardcode `<span class="badge" style="...">`.
* **Modals:** Gunakan `atomic_modal.php` secara absolut untuk **semua** popup. Hapus modal-modal *custom* di `users.php` dan `registrasi.php`.
* **Data Tables:** Buat komponen `parts/table_header.php` untuk konsistensi struktur tabel.

### 2.3. Controller Diet (Thin Controller, Fat Service)
Controller hanya boleh menerima request, memanggil *Service*, dan merender *View* atau *JSON*.
* **Action:** Pindahkan logika query yang kompleks dari Controller ke *Service Layer* (`app/Services/`).

---

## 🔒 3. PHASE 2: ZERO TRUST SECURITY ARCHITECTURE
Zero Trust berarti "Jangan percaya siapapun, verifikasi semuanya."

### 3.1. Centralized Form Request Validation
* **Masalah:** Saat ini Controller mengecek input satu per satu dengan `isset()` dan `trim()`.
* **Solusi:** Buat `InputValidator` class. Semua request POST harus melewati skema validasi ketat sebelum menyentuh Controller. Jika gagal, *reject* di level Router/Middleware.

### 3.2. Strict CSRF Middleware
* **Solusi:** Otomatisasi pengecekan CSRF. Jika request method adalah `POST`/`PUT`/`DELETE`, sistem otomatis menolak (403) jika `csrf_token` di header/body tidak valid. Tidak perlu lagi dipanggil manual di tiap Controller.

### 3.3. RBAC (Role-Based Access Control) Matrix
* **Solusi:** Di `routes.php`, peran `auth` dan `role` sudah ada. Tingkatkan dengan mengecek kapabilitas spesifik (contoh: `can_delete_user`, `can_edit_payment`) menggunakan *Policy Classes*, bukan sekadar mengecek tipe `role == ROLE_OWNER`.

---

## 🍃 4. PHASE 3: GREEN COMPUTING & PERFORMANCE
"Green Computing" dalam konteks web app berarti meminimalkan beban komputasi server dan bandwidth jaringan.

### 4.1. N+1 Query Elimination
* **Masalah:** Terlalu banyak query berulang di dalam *loop* (misal mencari klien untuk tiap registrasi).
* **Solusi:** Gunakan teknik *Eager Loading* atau JOIN yang lebih optimal (saat ini sudah sebagian menggunakan JOIN, tapi perlu disisir di `getStatistik` dan `getCountWithFilters`).

### 4.2. UI/DOM Optimization
* **Solusi:** Kurangi *nesting* tag HTML yang terlalu dalam (Div Soup). Hapus style inline (`style="..."`) yang ada di ribuan elemen tabel dan pindahkan ke `company-profile.css` atau `dashboard.css`. Ini akan memangkas ukuran payload HTML hingga 40%.

### 4.3. Caching & Asset Minification
* **Solusi:** Implementasi Redis/File Cache untuk query data statis seperti data "Layanan". Minifikasi semua CSS dan JS menggunakan alat *build* sederhana agar respons lebih cepat dan hemat daya.

---

## 🗺️ 5. ROADMAP EKSEKUSI (STEP-BY-STEP)

| Step | Area Pekerjaan | Detail Task | Estimasi |
|:---:|:---|:---|:---:|
| **1** | **UI Components** | Ekstrak semua inline style (tabel, badge, button) ke `dashboard.css`. Hapus style hardcode di `users.php` & `registrasi.php`. | Tahap 1 |
| **2** | **JS Centralization**| Buat `assets/js/app.js`. Pindahkan fungsi `handleFormSubmit`, `confirmDelete`, `openModal` menjadi fungsi global. Hapus skrip *inline*. | Tahap 1 |
| **3** | **Security Layer** | Terapkan Global CSRF checker di `Router::dispatch()` untuk semua method POST. | Tahap 2 |
| **4** | **Database/Query** | Audit model `Registrasi.php`, ganti fungsi-fungsi repetitif menjadi *Query Builder* yang modular. | Tahap 2 |
| **5** | **Zero Trust Auth**| Implementasi validasi kapabilitas user ketat, dan log semua *Security Events* mencurigakan di satu tempat. | Tahap 3 |

---
**Kesimpulan:**
Kita tidak perlu menulis ulang dari awal (Rewrite). Kita hanya perlu melakukan **Refactoring Masif** untuk membersihkan hutang teknis AI, mengekstrak kode yang berulang menjadi komponen, dan memusatkan logika keamanan ke dalam satu pintu masuk (Middleware/Router).
