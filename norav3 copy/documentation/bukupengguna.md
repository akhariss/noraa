# BUKU PENGGUNA — Sistem Tracking Notaris & PPAT Sri Anah SH.M.Kn

---

## DAFTAR ISI

1. [Gambaran Umum](#1-gambaran-umum)
2. [Peran Pengguna](#2-peran-pengguna)
3. [LAYER 1: Halaman Publik](#3-layer-1-halaman-publik)
4. [LAYER 2: Login](#4-layer-2-login)
5. [LAYER 3: Dashboard (Admin &amp; Notaris)](#5-layer-3-dashboard)
6. [LAYER 4: Khusus Notaris](#6-layer-4-khusus-notaris)
7. [Alur Workflow &amp; Business Logic](#7-alur-workflow--business-logic)
8. [Integrasi WhatsApp](#8-integrasi-whatsapp)
9. [Keamanan](#9-keamanan)
10. [Peta Navigasi](#10-peta-navigasi)

---

## 1. Gambaran Umum

Aplikasi ini adalah **Sistem Tracking Registrasi** untuk Kantor Notaris & PPAT Sri Anah SH.M.Kn, Cirebon. Terdiri dari 2 sisi utama:

- **Website publik** — profil kantor + tracking registrasi untuk klien.
- **Dashboard internal** — pengelolaan registrasi, user, CMS, backup oleh staf.

**URL Akses:** `http://localhost/newnota/`
**WhatsApp Kantor:** 085747898811
**Semua halaman** diakses via `index.php?gate=NAMA_HALAMAN`.

---

## 2. Peran Pengguna

### 2a. Publik (Tanpa Login)

- Melihat website profil (homepage).
- Melacak registrasi: input nomor registrasi → verifikasi 4 digit akhir HP → lihat detail.

### 2b. Admin (Login: `admin` / `admin123`)

- Akses: Dashboard, Daftar Registrasi, Tambah Registrasi, Detail Registrasi.
- Bisa: buat registrasi, update status, edit data klien, tandai kendala, kirim WA.
- **Tidak bisa:** kelola user, CMS, backup, audit log, tutup registrasi.

### 2c. Notaris / Super Admin (Login: `notaris` / `notaris123`)

- **Semua hak Admin** + tambahan:
- Kelola user (tambah/edit/hapus).
- Kelola konten website (CMS).
- Buat/kelola backup database & site.
- Lihat audit log (catatan aktivitas sistem).
- Tutup registrasi (finalisasi) & buka kembali registrasi yang ditutup.

---

## 3. LAYER 1: Halaman Publik

### 3a. Homepage (`gate=home`)

**Tampilan:** Halaman profil perusahaan satu halaman penuh (one-page) dengan scroll ke bawah.

#### ▸ HEADER (paling atas, tetap terlihat saat scroll)

- **Kiri:** Logo ikon dokumen + teks "Notaris Sri Anah SH.M.Kn".
- **Tengah:** Menu navigasi — 5 tautan: "Masalah", "Layanan", "Testimoni", "Lacak Registrasi" (ke `gate=lacak`), "Tentang".
- **Kanan:** Tombol hijau "Hubungi Kami" → buka WhatsApp `wa.me/6285747898811`.

#### ▸ SECTION HERO (Banner Utama)

**Sisi kiri:**

- Badge: "Notaris & PPAT Cirebon".
- Judul H1: "Pendamping Hukum Resmi untuk Properti, Usaha, dan Keluarga".
- Subjudul: "Aman, transparan, dan sesuai peraturan perundang-undangan."
- Deskripsi: "Melayani pembuatan akta, legalisasi, dan konsultasi hukum…"
- 2 tombol:
  - "Konsultasi via WhatsApp" (hijau, ikon WA) → buka `wa.me/6285747898811`.
  - "Lihat Testimoni" → scroll ke section testimoni.

**Sisi kanan — Kartu "Layanan Cepat":**

- Judul: ikon jam + "Layanan Cepat".
- 2 tautan cepat masing-masing berisi ikon + judul + sub-judul:
  1. Ikon search + "Lacak Registrasi" + "Cek status dokumen Anda" → link ke halaman tracking.
  2. Ikon WA + "Hubungi Kami" + "Respons cepat hari ini" → buka WhatsApp.

#### ▸ SECTION MASALAH

- Judul: "Apakah Anda Mengalami Hal Ini?"
- 4 kartu masalah, masing-masing berisi ikon + judul + deskripsi:
  1. Ikon peringatan + "Takut dokumen salah & berujung sengketa" + "Kesalahan kecil bisa berakibat besar di kemudian hari".
  2. Ikon tanya + "Bingung syarat & prosedur hukum" + "Banyak persyaratan yang tidak dipahami".
  3. Ikon jam + "Proses lama dan tidak transparan" + "Tidak tahu tahap apa yang sedang berjalan".
  4. Ikon chat + "Sulit menghubungi notaris saat butuh cepat" + "Respons lambat saat kondisi mendesak".
- Penutup: "Kantor Notaris Sri Anah SH.M.Kn hadir untuk memastikan setiap proses aman, jelas, dan sesuai hukum."

#### ▸ SECTION LAYANAN

- Judul: "Layanan Kami"
- 6 kartu layanan, masing-masing berisi ikon + judul + deskripsi + manfaat (ikon centang):
  1. Ikon rumah + **"Akta Properti"** + "Untuk jual beli rumah, tanah, dan pengalihan hak" + ✓ Aman secara hukum + ✓ Menghindari sengketa.
  2. Ikon dokumen + **"Pendirian Usaha"** + "PT, CV, Yayasan, dan perubahan anggaran dasar" + ✓ Legalitas lengkap + ✓ Siap operasional.
  3. Ikon file-text + **"Legalisasi"** + "Legalisasi dokumen dan pengesahan tanda tangan" + ✓ Diterima semua lembaga.
  4. Ikon bookmark + **"Akta Waris & Hibah"** + "Pembuatan akta wasiat, waris, dan hibah deed" + ✓ Aman untuk keluarga.
  5. Ikon map-pin + **"Layanan PPAT"** + "Pembuatan akta tanah dan perbuatan hukum lainnya" + ✓ Terdaftar resmi.
  6. Ikon tanya + **"Konsultasi Hukum"** + "Untuk kebutuhan hukum personal atau bisnis Anda" + Tombol "Hubungi Kami →" → WA.

#### ▸ SECTION TESTIMONI

- Judul: "Apa Kata Klien Kami?"
- 3 kartu testimoni, masing-masing: bintang ★★★★★ + kutipan + avatar inisial + nama + jenis layanan:
  1. Avatar "B" + **Swarta Sharia Property** (Klien Bisnis) — "Proses cepat dan transparan…"
  2. Avatar "S" + **Siti Rahayu** (Pendirian PT) — "Sangat profesional…"
  3. Avatar "A" + **Ahmad Fauzi** (Legalisasi Dokumen) — "Alhamdullillah semua berjalan Lancar…"
- CTA: "Ingin berkonsultasi juga?" + tombol "Chat WhatsApp" → WA.

#### ▸ SECTION ALUR KERJA

- Judul: "Cara Kerja Kami"
- 5 langkah berurutan, masing-masing berisi nomor bulat + judul + deskripsi:
  1. **Konsultasi** — Hubungi kami via WhatsApp.
  2. **Analisis** — Kami cek kelengkapan dokumen.
  3. **Proses** — Pembuatan akta dimulai.
  4. **Verifikasi** — Pengecekan akhir & validasi.
  5. **Selesai** — Dokumen siap diambil.

#### ▸ SECTION TENTANG

- Layout 2 kolom.
- **Kiri:** Ikon profil orang + nama "Sri Anah SH.M.Kn" + "Notaris & PPAT" + badge "15+ Tahun Pengalaman".
- **Kanan:** Judul "Tentang Kami" + kutipan miring: "Saya berkomitmen memberikan layanan notaris yang profesional…" + 4 poin keunggulan (ikon centang): Berizin resmi, Pengalaman 15+ tahun, Tim profesional, Layanan cepat & transparan.

#### ▸ SECTION TRACKING ONLINE (di dalam homepage)

- Judul: "Lacak Status Registrasi Anda"
- **Input:** Kotak teks placeholder "Masukkan nomor registrasi (NP-...)" + tombol "Lacak" (ikon search).
- **Business Logic saat submit (AJAX, tanpa reload halaman):**
  1. Sistem kirim nomor registrasi ke server (`POST gate=lacak`).
  2. Server cari di database tabel `registrasi` berdasarkan `nomor_registrasi`.
  3. **Jika tidak ditemukan:** tampilkan pesan "Nomor registrasi tidak ditemukan" di area hasil.
  4. **Jika ditemukan:** tampilkan kotak verifikasi keamanan (langkah 2).
- **Langkah 2 (Verifikasi):** Ikon gembok + judul "Verifikasi Keamanan" + penjelasan "Masukkan 4 digit terakhir nomor HP Anda…" + input 4 digit (maxlength=4, hanya angka) + tombol "Verifikasi".
- **Business Logic verifikasi (`POST gate=verify_tracking`):**
  1. Sistem ambil `klien_hp` dari registrasi yang ditemukan.
  2. Bandingkan 4 digit terakhir HP di database dengan input user.
  3. **Jika salah:** tampilkan error "Verifikasi gagal".
  4. **Jika benar:** sistem generate token tracking (disimpan ke kolom `tracking_token` di tabel `registrasi`) → tampilkan kartu hasil berisi: Nomor Registrasi, Badge Status, Nama Klien, Layanan, Tanggal Buat, Tanggal Update, dan tombol "Lihat Detail Lengkap" → mengarah ke `gate=detail&token=XXX`.
- **Rate Limiting:** Maksimal 5 pencarian per 60 detik. Maksimal 5 verifikasi per 60 detik.

#### ▸ SECTION CTA

- Judul: "Siap Melayani Anda" + "Konsultasikan kebutuhan hukum Anda sekarang juga melalui WhatsApp" + tombol besar "Hubungi via WhatsApp" → WA.

#### ▸ FOOTER

- 4 kolom:
  1. **Brand:** Logo + "Notaris Sri Anah" + deskripsi.
  2. **Layanan:** 4 tautan: Akta Properti, Pendirian Usaha, Legalisasi, Akta Waris.
  3. **Link Cepat:** 3 tautan: Tentang Kami, Testimoni, Login Dashboard.
  4. **Kontak:** Ikon lokasi + "Cirebon, Jawa Barat" + Ikon telepon + "085747898811".
- Baris terbawah: "© 2026 Kantor Notaris Sri Anah SH.M.Kn. All rights reserved."

---

### 3b. Halaman Lacak Registrasi (`gate=lacak`)

**Tampilan:** Halaman khusus tracking terpisah dari homepage.

- **Atas:** Tautan "← Kembali ke Homepage" + Judul H1 "Lacak Status Registrasi" + subjudul.
- **Form Pencarian:**
  - **Input teks** — placeholder: "Contoh: NP-20260224-0001", atribut `required`.
  - **Tombol** — ikon search + "Lacak Sekarang".
- **Kotak Verifikasi** (tersembunyi, muncul setelah nomor registrasi ditemukan):
  - Ikon gembok + H3 "Verifikasi Keamanan".
  - Teks: "Masukkan 4 digit terakhir nomor HP Anda untuk melihat detail registrasi:"
  - **Input hidden** `registrasi_id` (terisi otomatis dari hasil pencarian).
  - **Input teks** `phone_code` — maxlength="4", pattern="[0-9]{4}", placeholder="****", required.
  - Contoh: "Jika HP Anda 081234567**8901**, masukkan: **8901**".
  - **Tombol** "Verifikasi".
  - **Input hanya menerima angka** (karakter non-angka otomatis dihapus saat mengetik).
- **Area Hasil** (tersembunyi, muncul setelah verifikasi berhasil):
  - Kartu berisi: Nomor Registrasi + Badge Status (warna sesuai status) + 4 baris info (Klien, Layanan, Tanggal, Update) + tombol "Lihat Detail Lengkap".
- **Business Logic:** Sama seperti section tracking di homepage (lihat 3a).

---

### 3c. Halaman Detail Registrasi Publik (`gate=detail&token=XXX`)

**Tampilan:** Halaman detail lengkap registrasi untuk klien.

**Syarat akses:** Token harus valid di kolom `tracking_token` tabel `registrasi`. Token berlaku 24 jam. Jika token invalid/kadaluarsa → halaman "Akses Ditolak", diminta verifikasi ulang.

- **Atas:** Tautan "← Kembali ke Lacak Registrasi" + H1 "Detail Registrasi".
- **Kartu Info Registrasi:**
  - Header: Nomor Registrasi (H1) + Badge Status.
  - 4 baris info: Klien, Layanan, Dibuat (format: "dd MMM YYYY"), Update Terakhir (format: "dd MMM YYYY HH:mm").
- **Section Catatan Proses (📋):**
  - H2 "Catatan Proses"
  - Jika belum ada riwayat: "Belum ada riwayat perubahan."
  - Jika ada: Daftar kartu riwayat, masing-masing berisi:
    - **Header:** 📅 Tanggal + 👤 Nama User (badge di kanan).
    - **Isi:**
      - **Status:** Status lama → Status baru (warna gold → hijau). Atau "No change" jika tidak ada perubahan status.
      - **Kendala** (jika ada): Kotak kuning "🚩 Kendala: [nama tahap]".
      - **Catatan** (jika ada): Kotak krem dengan ikon 💬 + teks catatan (miring).
    - Setiap kartu memiliki garis kiri berwarna gold (border-left).
- **Section Progress Status:**
  - H2 "Progress Status"
  - **Timeline vertikal** menampilkan semua tahapan status dari awal sampai akhir:
    - Setiap item: Bulatan penanda (nomor urut atau ikon centang jika sudah selesai) + Label status + Estimasi waktu.
    - Item yang sudah dilalui: ditandai centang hijau (completed).
    - Item saat ini: ditandai khusus (current, lebih menonjol).
    - Item belum: ditandai nomor biasa (abu-abu).
- **Section CTA Kontak:**
  - "Butuh bantuan? Hubungi kami via WhatsApp" + Tombol hijau "Hubungi via WhatsApp" → `wa.me/6285747898811`.

---

## 4. LAYER 2: Login (`gate=login`)

**Tampilan:** Halaman penuh dengan kartu login di tengah layar.

- **Header kartu:** Ikon dokumen + H1 nama aplikasi + "Dashboard Notaris & PPAT".
- **Form login:**
  - **Input "Username"** — type=text, required, autofocus, autocomplete="username".
  - **Input "Password"** — type=password, required, autocomplete="current-password".
  - **Hidden input** `csrf_token` — diisi otomatis oleh server untuk keamanan.
  - **Tombol** "Login ke Dashboard".
- **Area pesan** (tersembunyi, muncul setelah submit):
  - Teks "Logging in..." saat proses.
  - Hijau + "Login berhasil" → redirect ke `gate=dashboard` setelah 1 detik.
  - Merah + "Username atau password salah" jika gagal.
  - Merah + "Terjadi kesalahan…" jika ada error jaringan.
- **Footer kartu:** Tautan "← Kembali ke Homepage" + Info demo: "admin/admin123 | notaris/notaris123".

**Business Logic:**

1. Form dikirim via AJAX (`POST gate=login`) dengan FormData (username, password, csrf_token).
2. Server verifikasi CSRF token → cek username di database → cek password (bcrypt) → cek rate limit.
3. **Rate Limit:** Maks 5 percobaan gagal per 5 menit. Jika terlampaui → respons "Terlalu banyak percobaan. Silakan tunggu."
4. **Jika berhasil:** Buat session baru, regenerate session ID (anti-fixation), simpan user_id + username + role di session, catat login di audit_log.
5. **Session berlaku 1 jam.** Setelah 1 jam tanpa aktivitas → otomatis logout.

---

## 5. LAYER 3: Dashboard (Admin & Notaris)

### Layout Dashboard (berlaku untuk semua halaman dashboard)

**Struktur 3 bagian:**

1. **Sidebar Kiri (Menu Navigasi):**

   - Header: Logo + "Notaris Sri Anah" + tombol tutup (mobile).
   - Menu untuk **semua role:** Dashboard, Registrasi.
   - Menu **khusus Notaris** (tidak muncul untuk Admin): Tutup Registrasi, User Management, CMS, Backup, Audit Log.
   - Footer sidebar: Tombol "Logout" → `gate=logout`.
   - Menu aktif ditandai dengan highlight khusus (class `active`).
2. **Topbar (Atas):**

   - Kiri: H1 judul halaman saat ini.
   - Kanan: Nama user yang login + badge role (Admin/Notaris).
3. **Area Konten (Tengah):** Berubah sesuai halaman.

**Mobile:** Sidebar tersembunyi. Ada tombol hamburger (3 garis) untuk membuka sidebar sebagai overlay.

---

### 5a. Dashboard Utama (`gate=dashboard`)

**Tampilan:**

#### 4 Kartu Statistik (baris atas):

| Kartu | Ikon    | Nilai         | Label             | Warna    |
| ----- | ------- | ------------- | ----------------- | -------- |
| 1     | Dokumen | Angka total   | "Total Registrasi"   | Biru tua |
| 2     | Jam     | Angka aktif   | "Sedang Diproses" | Biru     |
| 3     | Centang | Angka selesai | "Selesai"         | Hijau    |
| 4     | Silang  | Angka batal   | "Batal"           | Merah    |

**Business Logic statistik:** Query database: COUNT total, SUM status='selesai', SUM status='batal', SUM yang bukan selesai/batal = aktif.

#### Grid 2 Kolom:

**Kolom Kiri — Tabel "Registrasi Terbaru":**

- Header: H3 "Registrasi Terbaru" + tautan "Lihat Semua" → `gate=registrasi`.
- Jika kosong: "Belum ada registrasi".
- Jika ada: Tabel 5 kolom:
  - **Nomor Registrasi** — contoh: NP-20260303-0042.
  - **Klien** — nama klien.
  - **Layanan** — nama layanan.
  - **Status** — badge berwarna sesuai status (biru=draft, hijau=selesai, merah=batal, ungu=ditutup, orange=perbaikan).
  - **Tanggal** — format "dd MMM YYYY".
- Menampilkan 10 registrasi terakhir.

**Kolom Kanan — "Aktivitas Terakhir":**

- Header: H3 "Aktivitas Terakhir".
- Jika kosong: "Belum ada aktivitas".
- Jika ada: Daftar item, masing-masing berisi:
  - **Waktu** — format "dd MMM HH:mm".
  - **User** — username atau "System".
  - **Aksi** — jenis aksi (login, update_status, create_registrasi, dll).
  - **Nomor Registrasi** (jika terkait).
- Menampilkan 15 aktivitas terakhir dari tabel audit_log.

---

### 5b. Daftar Registrasi (`gate=registrasi`)

**Tampilan:**

#### Toolbar Atas (baris horizontal):

- **Kiri (4 filter + 1 tombol):**
  1. **Input pencarian** — placeholder "Cari...", lebar 200px. Mencari real-time di: nomor registrasi, klien, HP, layanan, status.
  2. **Dropdown Filter Layanan** — "Semua Layanan" + daftar semua layanan dari database.
  3. **Dropdown Filter Status** — "Semua Status" + 14 status: Draft, Pembayaran Admin, Validasi Sertifikat, Pengecekan Sertifikat, Pembayaran Pajak, Validasi Pajak, Penomoran Akta, Pendaftaran, Pembayaran PNBP, Pemeriksaan BPN, Perbaikan, Selesai, Ditutup, Batal.
  4. **Dropdown Filter Flag** — "Semua Flag" / "🚩 Aktif" / "- Tidak".
  5. **Tombol Refresh** — ikon panah putar, reload halaman.
- **Kanan:** Tombol "Tambah Registrasi" (ikon +) → `gate=registrasi_create`.

**Business Logic filter:** Semua filter berjalan secara real-time di browser (tanpa reload). Setiap baris tabel memiliki data attribute (data-status, data-nama, data-layanan, data-flag) yang dicocokkan oleh JavaScript.

#### Tabel Registrasi (7 kolom):

| Kolom         | Isi              | Detail                                                                        |
| ------------- | ---------------- | ----------------------------------------------------------------------------- |
| Nomor Registrasi | NP-XXXXXXXX-XXXX | Font tebal                                                                    |
| Klien         | Nama lengkap     |                                                                               |
| HP            | 08xxxxxxxxxx     |                                                                               |
| Layanan       | Nama layanan     |                                                                               |
| Status        | Badge berwarna   | Warna: biru=draft, hijau=selesai, merah=batal, ungu=ditutup, orange=perbaikan |
| Flag          | 🚩 atau -        | Kuning jika ada kendala aktif                                                 |
| Aksi          | Tombol "Detail"  | Link ke `gate=registrasi_detail&id=X`                                          |

- Jika belum ada registrasi: "Belum ada registrasi" (text center).

#### Paginasi (bawah tabel):

- Muncul jika total > 20 registrasi per halaman.
- Tombol: ← Prev, [1], [2], [3], ..., Next →.
- Info: "Page X of Y (Z items)".
- Saat filter aktif: info berubah jadi "Showing X of Y items (filtered)".

---

### 5c. Tambah Registrasi Baru (`gate=registrasi_create`)

**Tampilan:** Form dalam kartu putih.

#### Section 1: "Data Klien" (H3)

- **Input "Nama Klien"** — type=text, required, placeholder="Nama lengkap klien". Label: "Nama Klien *".
- **Input "Nomor HP"** — type=text, required, placeholder="08xxxxxxxxxx". Label: "Nomor HP *".

#### Section 2: "Data Registrasi" (H3)

- **Dropdown "Jenis Layanan"** — required. Label: "Jenis Layanan *". Opsi: "Pilih Layanan" (disabled) + semua layanan dari tabel `layanan` di database.
- **Dropdown "Status"** — required. Label: "Status *". 4 opsi:
  - "Draft / Pengumpulan Persyaratan" (default, selected).
  - "Pembayaran Administrasi".
  - "Validasi Sertifikat".
  - "Pengecekan Sertifikat".
  - **Business Logic:** Saat status berubah → fungsi `autoFillCatatan()` dipanggil → catatan terisi otomatis.
- **Textarea "Catatan"** — 5 baris, placeholder="Catatan...". Label: "Catatan". **Otomatis terisi** berdasarkan status yang dipilih:
  - Draft: "Registrasi Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan."
  - Pembayaran Admin: "Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan registrasi."
  - Validasi Sertifikat: "Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku."
  - Pengecekan Sertifikat: "Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi."

#### Tombol Aksi:

- **"Batal"** (link) — kembali ke `gate=registrasi`.
- **"Simpan Registrasi"** (submit) — kirim form.

#### Business Logic saat submit (AJAX):

1. Kirim data via `POST gate=registrasi_store` dengan FormData: csrf_token, klien_nama, klien_hp, layanan_id, status, catatan.
2. Server:
   a. Verifikasi CSRF token.
   b. Cek klien di tabel `klien` berdasarkan nomor HP → jika sudah ada, gunakan ID-nya; jika belum, buat baru (nama + HP).
   c. Generate **nomor registrasi** unik: format `NP-YYYYMMDD-XXXX` (contoh: NP-20260303-0042). XXXX = nomor urut hari itu.
   d. Simpan ke tabel `registrasi`: klien_id, layanan_id, nomor_registrasi, status, catatan_internal.
   e. Catat di tabel `registrasi_history` (riwayat bisnis): status_old=null, status_new=status, catatan.
   f. Catat di tabel `audit_log` (log sistem): action=create_registrasi.
   g. Kembalikan respons JSON: success, message, nomor_registrasi, klien_nama, klien_hp.
3. Jika **gagal**: tampilkan pesan error merah selama 5 detik.
4. Jika **berhasil**: tampilkan pesan hijau + muncul **Popup WhatsApp** (lihat bagian 8a).

---

### 5d. Detail Registrasi (`gate=registrasi_detail&id=X`)

**Tampilan:** Halaman paling kompleks. Terdiri dari beberapa kartu.

#### Tombol "← Kembali ke Daftar Registrasi" (atas)

#### Kartu 1: Informasi Registrasi

- Tombol "Edit" di pojok kanan atas → buka popup edit klien.
- Grid 3×2 (6 kotak info), masing-masing berisi label (huruf kecil, abu) + nilai (tebal):
  1. **Nomor Registrasi** — warna biru tua (primary).
  2. **Layanan** — nama layanan.
  3. **Klien** — nama klien.
  4. **HP** — nomor HP klien.
  5. **Dibuat** — format "dd MMM YYYY HH:mm".
  6. **Status** — badge berwarna + label status.

#### Kartu 2: Update Status (Form)

**Hidden inputs:** registrasi_id, csrf_token.

**Tampilan "Status Saat Ini":** Kotak dengan latar krem, teks tebal biru tua, menampilkan label status saat ini. Hanya tampilan, tidak bisa diedit.

**Dropdown "Status Baru":**

- **Jika status sudah final** (selesai/batal/ditutup):
  - Muncul kotak info hijau: ikon centang + "✓ Status Final - Tidak Dapat Diedit" + penjelasan:
    - Batal: "Registrasi ini telah dibatalkan dan tidak dapat dilanjutkan kembali."
    - Selesai: "Registrasi telah selesai dan siap untuk diambil/ditutup."
    - Ditutup: "Registrasi telah ditutup dan bersifat read-only."
  - Dropdown dinonaktifkan (disabled, abu-abu, cursor not-allowed).
- **Jika status belum final:**
  - Jika bisa dibatalkan: muncul info kuning: "ℹ️ Info Pembatalan: Registrasi dapat dibatalkan karena masih dalam tahap awal…"
  - Dropdown: opsi "-- Pilih Status Berikutnya --" + daftar status.
  - **Business Logic pemilihan status:**
    - Status saat ini tidak ditampilkan.
    - Status "ditutup" tidak pernah ditampilkan (hanya via halaman finalisasi).
    - Status yang lebih rendah dari saat ini tidak ditampilkan (tidak bisa mundur).
    - **Pengecualian "Perbaikan":** jika status saat ini = perbaikan, semua status di atas "pemeriksaan_bpn" dan "selesai" tidak ditampilkan (bisa mundur ke status manapun di bawahnya).
    - Status "Batal" hanya ditampilkan jika: status saat ini ada di daftar CANCELLABLE_STATUSES (draft, pembayaran_admin, validasi_sertifikat, pencecekan_sertifikat) ATAU status = perbaikan, DAN order < pembayaran_pajak. Ditampilkan dengan warna merah + "⚠️ Batal (Registrasi akan dihentikan)".
  - **Saat memilih status "Batal":** Fungsi `autoFillCatatan()` mendeteksi "batal" → TIDAK langsung auto-fill, melainkan muncul **Popup Konfirmasi Pembatalan**:
    - Ikon segitiga peringatan merah + H3 "Konfirmasi Pembatalan".
    - Kotak merah: "**Perhatian:** Registrasi yang dibatalkan tidak dapat dilanjutkan kembali."
    - Label teks catatan template batal.
    - 2 tombol: "Batal" (krem) → tutup popup, kembalikan dropdown ke kosong. "Ya, Batalkan" (merah) → auto-fill catatan batal, tutup popup.
  - **Saat memilih status lain:** Catatan langsung terisi otomatis sesuai template (lihat daftar 14 template di bawah).

**Textarea "Catatan":**

- 5 baris, berisi catatan internal saat ini (dari database `catatan_internal`).
- Saat status baru dipilih: isi catatan diganti template otomatis.
- Pengguna bisa mengedit teks template sebelum menyimpan.
- Border berubah gold sekilas (500ms) saat auto-fill untuk memberi feedback visual.

**14 Template Catatan Otomatis:**

| Status                | Template Catatan                                                                                             |
| --------------------- | ------------------------------------------------------------------------------------------------------------ |
| draft                 | Registrasi Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. |
| pembayaran_admin      | Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan registrasi.         |
| validasi_sertifikat   | Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.        |
| pencecekan_sertifikat | Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.               |
| pembayaran_pajak      | Proses pembayaran pajak yang berkaitan dengan registrasi sedang dilaksanakan sesuai ketentuan.                  |
| validasi_pajak        | Pembayaran pajak sedang dalam tahap pemeriksaan dan validasi oleh pihak terkait.                             |
| penomoran_akta        | Akta sedang dalam proses penomoran sebagai bagian dari penyelesaian dokumen.                                 |
| pendaftaran           | Registrasi sedang dalam proses pendaftaran resmi ke instansi yang berwenang.                                    |
| pembayaran_pnbp       | Pembayaran PNBP sedang diproses sebagai bagian dari tahapan lanjutan.                                        |
| pemeriksaan_bpn       | Berkas registrasi sedang dalam tahap pemeriksaan oleh instansi pertanahan.                                      |
| perbaikan             | Terdapat penyesuaian atau perbaikan administrasi yang sedang diselesaikan.                                   |
| selesai               | Seluruh tahapan utama telah diselesaikan. Registrasi Anda memasuki tahap akhir.                                 |
| ditutup               | Registrasi telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda kepada kantor kami.              |
| batal                 | Registrasi ini dinyatakan batal dan tidak dilanjutkan ke tahap berikutnya.                                      |

**Checkbox "Tandai ada kendala" / "✓ Kendala Aktif":**

- Latar kuning muda + border kuning.
- Jika belum ada kendala: label "Tandai ada kendala" + sub "Klik untuk menandai kendala". Checkbox unchecked.
- Jika sudah ada kendala: label "✓ Kendala Aktif" + sub "Perlu monitoring ekstra". Checkbox checked.
- **Jika status final:** Checkbox diganti kotak info abu-abu read-only (tidak bisa diubah).

**Tombol aksi:**

- "Batal" (krem) — kembali ke daftar registrasi.
- "Simpan Perubahan" (gradient biru tua, teks gold) — submit form.

**Business Logic saat submit update status (AJAX `POST gate=update_status`):**

1. Sistem cek apakah ada perubahan (status / flag / catatan vs data di database). Jika **tidak ada perubahan** → tampilkan error "Tidak ada perubahan yang dilakukan" selama 5 detik.
2. Tombol submit dinonaktifkan (disabled, opacity 0.6) untuk mencegah klik ganda.
3. Kirim data: registrasi_id, status, catatan, flag_kendala, csrf_token.
4. Server (WorkflowService):
   a. Validasi CSRF.
   b. Ambil data registrasi dari database.
   c. **Validasi status transition:** Cek urutan (STATUS_ORDER). Status baru harus urutan lebih tinggi kecuali: (1) dari Perbaikan boleh mundur, (2) flag_kendala saja boleh berubah tanpa ubah status.
   d. Jika status baru = "batal": set `batal_flag = TRUE`, deaktivasi semua kendala aktif di tabel `kendala`.
   e. Jika status baru = "selesai": deaktivasi semua kendala aktif.
   f. Update tabel `registrasi`: status + catatan_internal.
   g. Jika ada perubahan flag_kendala: toggle di tabel `kendala` (insert/update).
   h. Simpan ke tabel `registrasi_history`: user_id, registrasi_id, status_old, status_new, catatan, flag_kendala_active, flag_kendala_tahap.
   i. Catat di audit_log: action=update_status || toggle_flag.
5. Jika **berhasil**: pesan hijau. Jika status batal/selesai → auto-reload 0.5 detik. Status lain → auto-reload 5 detik.
6. Jika **gagal**: pesan merah, tombol submit diaktifkan kembali.

#### Kartu 3: Kirim Pembaruan WhatsApp

- Background gradient hijau (warna WA).
- H3 "Kirim Pembaruan Registrasi" + deskripsi "Kirim notifikasi status terbaru registrasi ini ke klien via WhatsApp".
- Tombol putih: ikon WA + "Kirim Status Terbaru via WhatsApp".
- **Business Logic:** Lihat bagian 8b.

#### Popup Edit Data Klien (tersembunyi, muncul saat klik tombol "Edit"):

- H3 "✏️ Edit Data Klien" + tombol × tutup.
- **Input "Nama Klien"** — type=text, required, terisi nama klien saat ini.
- **Input "Nomor HP"** — type=text, required, placeholder="08xxxxxxxxxx", terisi HP saat ini.
- Tombol: "Batal" + "Simpan Perubahan".
- **Business Logic (`POST gate=update_klien`):** Update tabel `klien` (nama + hp). Jika berhasil → tutup popup, reload halaman setelah 1.5 detik.

#### Kartu 4: Riwayat Perubahan (Business History)

- Header: H3 "Riwayat Perubahan (Business History)" + tautan "Lihat Semua Riwayat" → `gate=registrasi_history&id=X`.
- Tabel 5 kolom, menampilkan **7 entri terakhir** dari tabel `registrasi_history`:
  - **Timestamp** — format "dd MMM YYYY HH:mm".
  - **User** — username atau "System".
  - **Flag** — "🚩 ON" (kuning, tebal) + nama tahap kendala, atau "-" jika tidak ada.
  - **Status Change** — "status_lama → status_baru" atau "No change".
  - **Notes** — catatan (nl2br) atau "-".
- Jika kosong: "Belum ada riwayat perubahan".

#### Pesan Sukses/Error (fixed, pojok kanan bawah)

---

## 6. LAYER 4: Khusus Notaris

### 6a. Tutup Registrasi (`gate=finalisasi`)

**Tampilan:**

#### Header:

- H1 "Tutup Registrasi" + deskripsi.

#### 4 Kartu Statistik:

| Kartu   | Background        | Nilai | Label          |
| ------- | ----------------- | ----- | -------------- |
| Total   | Gradient biru tua | angka | Total Registrasi  |
| Selesai | Gradient hijau    | angka | Status Selesai |
| Batal   | Gradient merah    | angka | Status Batal   |
| Ditutup | Gradient abu      | angka | Status Ditutup |

#### Filter Bar:

- **Kiri:** Input pencarian "🔍 Cari registrasi…" — cari di nomor, klien, layanan, status (real-time).
- **Kanan:** 4 tombol filter tab: Semua / Selesai / Batal / Ditutup. Tab aktif berwarna sesuai (biru/hijau/merah/abu).
- **Info:** "Halaman X dari Y".

#### Tabel (5 kolom):

- Nomor Registrasi (tebal), Klien (nama + HP di bawah), Layanan, Status (badge), Aksi (tombol "📋 Detail").
- Tombol Detail → `gate=registrasi_detail_finalisasi&id=X`.
- Paginasi jika > 20 item.

### Detail Finalisasi (`gate=registrasi_detail_finalisasi&id=X`)

**Tampilan:**

- Tombol "← Kembali ke Daftar Registrasi".
- **Kartu Header:** Nomor Registrasi (H1) + badge status.
- **Kartu Identitas Registrasi:** Grid 2×3: Nomor Registrasi, Layanan, Klien, Nomor HP, Status, Dibuat. + Catatan Internal (jika ada).
- **Kartu Aksi Registrasi** (hanya muncul jika status BELUM ditutup):
  - Grid 2 kolom:
    - **"Proses Ulang"** (kotak kuning): Deskripsi "Kembalikan registrasi ke proses sebelumnya (Perbaikan)" + Tombol "🔄 Proses Ulang ke Perbaikan". → Popup konfirmasi → `POST gate=update_status` dengan status=perbaikan.
    - **"Tutup Registrasi"** (kotak abu): Deskripsi "Tutup registrasi ini secara permanen. Status akan berubah menjadi 'Ditutup'" + Tombol "📁 Tutup Registrasi Ini". → Konfirmasi browser → `POST gate=tutup_registrasi`.
  - Jika status sudah "ditutup": kartu aksi **tidak muncul**.
- **Kartu Riwayat:** Tabel 4 kolom (Timestamp, User, Status Change, Catatan) — semua riwayat.

### 6b–6e. User Management, CMS, Backup, Audit Log

**(Sudah dijelaskan detail di dokumen sebelumnya — lihat bagian 7a–7e versi sebelumnya)**

---

## 7. Alur Workflow & Business Logic

### Daftar 14 Status (urutan):

| #  | Kode                  | Label                           | Estimasi  |
| -- | --------------------- | ------------------------------- | --------- |
| 1  | draft                 | Draft / Pengumpulan Persyaratan | 2 hari    |
| 2  | pembayaran_admin      | Pembayaran Administrasi         | 2 hari    |
| 3  | validasi_sertifikat   | Validasi Sertifikat             | 7 hari    |
| 4  | pencecekan_sertifikat | Pengecekan Sertifikat           | 7 hari    |
| 5  | pembayaran_pajak      | Pembayaran Pajak                | 1 hari    |
| 6  | validasi_pajak        | Validasi Pajak                  | 5 hari    |
| 7  | penomoran_akta        | Penomoran Akta                  | 1 hari    |
| 8  | pendaftaran           | Pendaftaran                     | 5-7 hari  |
| 9  | pembayaran_pnbp       | Pembayaran PNBP                 | 1-2 hari  |
| 10 | pemeriksaan_bpn       | Pemeriksaan BPN                 | 7-10 hari |
| 11 | perbaikan             | Perbaikan                       | 3-7 hari  |
| 12 | selesai               | Selesai                         | 1 hari    |
| 13 | ditutup               | Ditutup                         | 1 hari    |
| 14 | batal                 | Batal                           | -         |

### Aturan Transisi Status:

1. **Maju saja:** Status hanya bisa maju (urutan lebih tinggi). Contoh: draft→pembayaran_admin ✓, pembayaran_admin→draft ✗.
2. **Pengecualian Perbaikan:** Dari status Perbaikan, bisa pindah ke status manapun (termasuk mundur).
3. **Pembatalan:** Hanya bisa dari status 1-4 (draft, pembayaran_admin, validasi_sertifikat, pencecekan_sertifikat) atau dari perbaikan, dan hanya jika order < pembayaran_pajak.
4. **Status Final:** Selesai, Batal, Ditutup → tidak bisa diubah dari halaman detail biasa.
5. **Tutup:** Hanya Notaris, dari halaman finalisasi, bisa mengubah selesai/batal → ditutup.
6. **Buka Kembali:** Hanya Notaris, dari halaman finalisasi, bisa mengubah ditutup → perbaikan atau kembali ke status sebelumnya.
7. **Auto-cleanup Kendala:** Saat status berubah ke batal atau selesai, semua kendala aktif di tabel `kendala` dideaktivasi otomatis.

### Dua Log Terpisah:

- **registrasi_history (Business Log):** Mencatat perubahan status, flag kendala, catatan, user. Ditampilkan ke klien di halaman publik.
- **audit_log (System Log):** Mencatat login/logout, CRUD user, backup, CMS update. Hanya Notaris yang bisa melihat.

---

## 8. Integrasi WhatsApp

### 8a. Popup WA Setelah Buat Registrasi

Setelah registrasi berhasil dibuat, muncul popup overlay:

- Ikon WA hijau + H3 "Kirim Notifikasi WhatsApp?"
- Kotak hijau muda: "✓ Registrasi Berhasil Dibuat" + Nama, Nomor Registrasi, Status.
- 2 tombol:
  - **"Lewati"** → tutup popup, redirect ke daftar registrasi setelah 300ms.
  - **"Kirim WhatsApp"** → buka tab baru `wa.me/62XXXXXXXXXX?text=PESAN`.

**Format pesan:**

```
Halo Bapak/Ibu [Nama],

Kami dari Kantor Notaris Notaris Sri Anah SH.M.Kn menginformasikan bahwa registrasi Anda telah terdaftar.

Detail Registrasi:
• Nomor Registrasi: [NP-XXXXXXXX-XXXX]
• Status: [Label Status]

Anda dapat memantau status dan progres registrasi secara mandiri melalui tautan tracking yang telah kami berikan.

Apabila terdapat pertanyaan lebih lanjut, silakan menghubungi kami melalui kontak resmi kantor.

Terima kasih atas kepercayaan Anda.

Hormat kami,
Kantor Notaris Notaris Sri Anah SH.M.Kn
```

**Konversi nomor HP:** Awalan "0" diganti "62" (contoh: 085747898811 → 6285747898811). Validasi: minimal 10 digit. Jika popup blocker aktif → alert peringatan.

### 8b. Kirim Update Status (Dari Detail Registrasi)

Tombol "Kirim Status Terbaru via WhatsApp" di halaman detail. Format pesan sama seperti 8a tapi dengan teks "menginformasikan status registrasi Anda saat ini" dan "Status Saat Ini: [Label]".

### 8c. Tombol WA di Homepage

Semua tombol hijau di homepage (header, hero, testimoni, CTA, footer) → `wa.me/6285747898811` tanpa pesan template.

---

## 9. Keamanan

| Fitur                | Detail                                                                                                 |
| -------------------- | ------------------------------------------------------------------------------------------------------ |
| CSRF Token           | Setiap form POST memiliki hidden input csrf_token. Server verifikasi sebelum proses.                   |
| Rate Limit Login     | Maks 5 gagal per 5 menit.                                                                              |
| Rate Limit Tracking  | Maks 5 search per 60 detik. Maks 5 verify per 60 detik.                                                |
| Session Lifetime     | 1 jam. Auto-logout jika tidak ada aktivitas.                                                           |
| Session Regeneration | Session ID diperbarui setiap 30 menit (anti-fixation).                                                 |
| Cookie Setting       | HttpOnly=true, SameSite=Strict, strict_mode=true.                                                      |
| Password Hash        | bcrypt, cost factor 12.                                                                                |
| Token Tracking       | Token random untuk akses publik detail registrasi. Berlaku 24 jam.                                        |
| Role Check           | Setiap halaman dashboard cek `isAuthenticated()`. Halaman notaris cek `requireRole(ROLE_NOTARIS)`. |
| Anti Double Submit   | Tombol submit dinonaktifkan setelah klik pertama.                                                      |
| Input Sanitization   | `htmlspecialchars()` di semua output. Prepared statements untuk semua query DB.                      |
| Cache Prevention     | Header meta no-cache di semua halaman dashboard.                                                       |

---

## 10. Peta Navigasi

```
PUBLIK
├── Homepage (gate=home)
│   ├── [Menu] → Lacak Registrasi (gate=lacak)
│   ├── [Menu] → Login (gate=login)
│   ├── [Tombol WA] → wa.me/6285747898811
│   └── [Section Tracking] → Input nomor → Verifikasi HP → Detail Publik
│
├── Lacak Registrasi (gate=lacak)
│   └── Input nomor → Verifikasi → Hasil → Detail Publik (gate=detail&token=X)
│
└── Detail Publik (gate=detail&token=X)
    └── [Catatan Proses + Timeline + Hubungi WA]

LOGIN (gate=login) → Dashboard (gate=dashboard)

DASHBOARD (Admin + Notaris)
├── Dashboard (gate=dashboard) → [Lihat Semua] → Daftar Registrasi
├── Registrasi (gate=registrasi) → [+ Tambah] → Form Buat (gate=registrasi_create)
│                           → [Detail] → Detail (gate=registrasi_detail&id=X)
│                                         ├── [Update Status + Catatan + Flag]
│                                         ├── [Edit Klien] → Popup
│                                         ├── [Kirim WA] → WhatsApp
│                                         └── [Riwayat] → History (gate=registrasi_history&id=X)
│
├── [NOTARIS] Tutup Registrasi (gate=finalisasi)
│   └── Detail Finalisasi (gate=registrasi_detail_finalisasi&id=X)
│       ├── [Proses Ulang] → Status Perbaikan
│       └── [Tutup] → Status Ditutup
│
├── [NOTARIS] User Management (gate=users)
│   ├── [Tambah] → Popup: username + password + role
│   ├── [Edit] → Popup: username + password baru + role
│   └── [Hapus] → Popup konfirmasi
│
├── [NOTARIS] CMS (gate=cms) → [Edit] → Popup: textarea konten
├── [NOTARIS] Backup (gate=backups) → [Buat DB/Site] + [Download/Hapus]
├── [NOTARIS] Audit Log (gate=audit) → [Tabel log + Statistik]
└── Logout (gate=logout) → Login
```
