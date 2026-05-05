# NORA V4 - STANDAR & ATURAN PENGEMBANGAN (RULES.md)

Dokumen ini adalah **Kitab Suci (Single Source of Truth)** untuk pengembangan Nora V4. Tujuannya agar sistem tetap rapi (Clean Code), tidak tumpang tindih (*spaghetti* seperti V3), dan sangat mudah dikelola (*maintainable*).

**Aturan Emas:** "Tulis sekali, gunakan di mana-mana" (DRY - *Don't Repeat Yourself*). Jika Anda harus mengubah satu warna di 10 file yang berbeda, berarti Anda melanggar aturan ini.

---

## 1. ATURAN FRONTEND (UI/UX, CSS, JS)

### A. Global Variabel (Sistem Token Desain)
**DILARANG KERAS** melakukan *hardcode* warna atau ukuran (seperti `#cda45e` atau `16px`) berulang-ulang di file CSS/PHP yang berbeda.
Semua warna utama, ukuran teks, dan bayangan (*shadow*) **WAJIB** merujuk pada Variabel CSS Global di file `assets/css/globals.css` atau blok `:root` utama.
```css
/* CONTOH BENAR */
:root {
    --gold: #cda45e;
    --primary: #1a1a1a;
    --bg-cream: #fbfbfb;
    --radius-modal: 24px;
}
.btn-primary { background: var(--gold); border-radius: var(--radius-modal); }
```
*Tujuan: Jika "Gold" ingin diubah jadi "Coklat Tua", cukup ganti di `:root` dan SELURUH aplikasi akan berubah otomatis.*

### B. Atomic Component (Komponen Reusable)
Elemen yang sering muncul seperti **Button, Card, Badge, Modal, dan Popup** tidak boleh ditulis ulang struktur HTML dan CSS-nya di tiap halaman.
1. **CSS Atom**: Buat class khusus seperti `.nora-card`, `.nora-badge`, `.modal-glass`.
2. **PHP Component**: Jika struktur HTML-nya panjang (seperti Modal Konfirmasi), pisahkan menjadi satu file di folder `app/Views/components/`.
   ```php
   // Cara panggil di halaman mana saja:
   require VIEWS_PATH . '/components/modal_konfirmasi.php';
   ```
*Tujuan: Jika desain modal diperbarui, otomatis berubah di seluruh halaman (Pendaftaran, Pelacakan, Dasbor).*

### C. Konsistensi Jarak & Padding
Gunakan kelipatan **4px** atau **8px** untuk margin dan padding (misal: 8px, 16px, 24px, 32px) agar skala proporsional.

---

## 2. ATURAN BACKEND (PHP & ARSITEKTUR MVC)

### A. Pemisahan Logika Secara Tegas (Separation of Concerns)
Jangan pernah mencampur urusan *Database*, *Business Logic*, dan *Tampilan* dalam satu fungsi.
*   **Controller**: HANYA untuk menerima *Request* (GET/POST), memanggil Model/Service, dan me-return View atau JSON. JANGAN taruh logika perhitungan atau query database di sini!
*   **Model**: HANYA berisi *Query* ke database (Select, Insert, Update, Delete). Tidak boleh ada logika bisnis.
*   **Service**: Semua perhitungan, manipulasi alur (seperti ganti status Workflow, kirim Notifikasi), **WAJIB** ditaruh di folder `app/Services/`.
*   **View**: Murni untuk menampilkan HTML. Dilarang melakukan *Query* database di dalam View!

### B. Aturan Penulisan Kode
1. **Class & Nama File**: `PascalCase` (contoh: `WorkflowService.php`).
2. **Fungsi & Variabel**: `camelCase` (contoh: `getTrackingProgress()`, `$klienBaru`).
3. **Helpers**: Jika ada fungsi kecil yang dipakai di mana-mana (seperti format Rupiah, format Tanggal, enkripsi), WAJIB dimasukkan ke `app/Core/helpers.php`.

---

## 3. ATURAN DATABASE & KEAMANAN

1. **Anti SQL-Injection**: DILARANG menyisipkan variabel langsung ke dalam *string* Query. **WAJIB** menggunakan fungsi binding `Database::select(query, [params])` bawaan sistem.
2. **Sanitasi Input Global**: Jangan mem-filter data secara manual satu per satu jika bentuknya standar. Biarkan `InputSanitizer::sanitizeGlobal()` membersihkan XSS secara otomatis di level sistem depan (`index.php`).

---

## 4. WORKFLOW PENGERJAAN FITUR BARU

Setiap kali Anda diminta membuat fitur baru, ikuti urutan ini:
1. **Analisis Komponen UI**: Apakah tombol/kartu ini sudah ada sebelumnya? Jika ada, gunakan class yang sudah ada. Jika belum, buat class baru di CSS global agar bisa dipakai halaman lain besok.
2. **Siapkan Database (Model)**: Buat fungsi *query*-nya.
3. **Tulis Logika (Service)**: Jika ada validasi bertingkat, taruh di Service.
4. **Sambungkan (Controller)**.
5. **Tampilkan (View)**: Rapikan HTML dengan memanggil komponen/variabel CSS yang sudah disepakati.

---
*Dokumen ini dibuat dan dijaga untuk memastikan Nora V4 tetap berskala Enterprise, Elegan, dan Anti-Spaghetti.*
