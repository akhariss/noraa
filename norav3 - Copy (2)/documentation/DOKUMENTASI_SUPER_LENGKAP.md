# DOKUMENTASI SUPER LENGKAP - APLIKASI NOTARIS & PPAT TRACKING SYSTEM

## INFORMASI UMUM APLIKASI

**Nama Aplikasi**: Notaris & PPAT Tracking System  
**Versi**: 1.0.0  
**Nama Kantor**: Notaris Sri Anah SH.M.Kn  
**Lokasi**: Cirebon, Jawa Barat  
**Kontak**: 085747898811  

---

## STRUKTUR APLIKASI - 5 LAYER UTAMA

### LAYER 1: DATABASE (Penyimpanan Data)
### LAYER 2: MODEL (Akses Data)
### LAYER 3: SERVICE (Logika Bisnis)
### LAYER 4: CONTROLLER (Pengatur Alur)
### LAYER 5: VIEW/PRESENTATION (Tampilan)

---

## LAYER 1: DATABASE - DETAIL TABEL

### 1. TABEL `users` - Data Pengguna Internal

**Fungsi**: Menyimpan semua staff yang bisa login ke sistem

**Kolom**:
- `id` - Nomor unik untuk setiap user (angka, otomatis bertambah)
- `username` - Nama untuk login (unik, tidak boleh sama)
- `password_hash` - Password yang sudah dienkripsi (tidak bisa dibaca)
- `role` - Jabatan user, hanya ada 2 pilihan: "notaris" atau "admin"
- `created_at` - Tanggal dan waktu user dibuat
- `updated_at` - Tanggal dan waktu user terakhir diupdate

**Data Contoh**:
- User ID 1: username "admin", role "admin"
- User ID 2: username "notaris", role "notaris"

**Aturan Bisnis**:
- Username harus unik (tidak boleh ada 2 user dengan username sama)
- Password minimal 6 karakter
- Hanya Notaris yang bisa membuat user baru
- Hanya Notaris yang bisa mengubah role user
- User tidak bisa menghapus diri sendiri

---

### 2. TABEL `klien` - Data Klien

**Fungsi**: Menyimpan informasi klien yang menggunakan jasa notaris

**Kolom**:
- `id` - Nomor unik untuk setiap klien
- `nama` - Nama lengkap klien (wajib diisi)
- `hp` - Nomor handphone klien (wajib diisi, untuk verifikasi tracking)
- `email` - Email klien (opsional, boleh kosong)
- `created_at` - Tanggal klien pertama kali didaftarkan
- `updated_at` - Tanggal data klien terakhir diupdate

**Aturan Bisnis**:
- Satu klien bisa memiliki banyak registrasi
- Sistem otomatis mencari klien yang sudah ada saat membuat registrasi baru (berdasarkan nama dan HP)
- Jika klien sudah ada, data digunakan kembali (tidak buat baru)
- 4 digit terakhir nomor HP digunakan untuk kode verifikasi tracking

---

### 3. TABEL `layanan` - Jenis Layanan Notaris

**Fungsi**: Menyimpan jenis-jenis layanan hukum yang ditawarkan

**Kolom**:
- `id` - Nomor unik untuk setiap layanan
- `nama_layanan` - Nama jenis layanan (contoh: "Jual Beli", "Hibah", "Waris")
- `deskripsi` - Penjelasan detail tentang layanan tersebut
- `created_at` - Tanggal layanan ditambahkan
- `updated_at` - Tanggal layanan terakhir diupdate

**Daftar Layanan yang Tersedia**:
1. **Jual Beli** - Akta Jual Beli properti (tanah, rumah, bangunan)
2. **Hibah** - Akta Hibah (pemberian harta kepada orang lain)
3. **Waris** - Akta Waris (pembagian harta warisan)
4. **Pembagian Hak Bersama** - Pembagian harta bersama (misal harta gono-gini)
5. **Roya** - Roya hipotik (penghapusan hak tanggungan)
6. **Lainnya** - Layanan notaris lainnya yang tidak termasuk di atas

**Aturan Bisnis**:
- Setiap registrasi harus memilih salah satu layanan
- Layanan tidak bisa dihapus jika sudah digunakan di registrasi yang ada

---

### 4. TABEL `registrasi` - Data Registrasi (INTI APLIKASI)

**Fungsi**: Menyimpan semua informasi tentang registrasi yang ditangani

**Kolom**:
- `id` - Nomor unik untuk setiap registrasi
- `klien_id` - ID klien yang memiliki registrasi (mengacu ke tabel klien)
- `layanan_id` - ID jenis layanan yang dipilih (mengacu ke tabel layanan)
- `nomor_registrasi` - Nomor registrasi unik, format: NP-YYYYMMDD-XXXX
  - NP = Notaris Registrasi
  - YYYYMMDD = Tanggal pembuatan (tahun, bulan, tanggal)
  - XXXX = Angka acak 4 digit
  - Contoh: NP-20260302-1925
- `status` - Status registrasi saat ini (14 kemungkinan status)
- `verification_code` - Kode verifikasi untuk tracking (4 digit terakhir HP)
- `tracking_token` - Token aman untuk akses tracking publik (enkripsi)
- `catatan_internal` - Catatan dari staff notaris tentang registrasi ini
- `created_at` - Tanggal registrasi dibuat
- `updated_at` - Tanggal registrasi terakhir diupdate
- `batal_flag` - Penanda apakah registrasi batal (0 = tidak, 1 = batal)

**14 STATUS REGISTRASI** (berurutan dari awal sampai selesai):

1. **draft** - "Draft / Pengumpulan Persyaratan"
   - Estimasi: 2 hari
   - Registrasi baru dibuat, sedang mengumpulkan dokumen

2. **pembayaran_admin** - "Pembayaran Administrasi"
   - Estimasi: 2 hari
   - Klien membayar biaya administrasi notaris

3. **validasi_sertifikat** - "Validasi Sertifikat"
   - Estimasi: 7 hari
   - Sertifikat divalidasi ke instansi terkait

4. **pencecekan_sertifikat** - "Pengecekan Sertifikat"
   - Estimasi: 7 hari
   - Pengecekan lanjutan sertifikat

5. **pembayaran_pajak** - "Pembayaran Pajak"
   - Estimasi: 1 hari
   - Klien membayar pajak terkait transaksi

6. **validasi_pajak** - "Validasi Pajak"
   - Estimasi: 5 hari
   - Validasi pembayaran pajak oleh authorities

7. **penomoran_akta** - "Penomoran Akta"
   - Estimasi: 1 hari
   - Akta diberi nomor resmi

8. **pendaftaran** - "Pendaftaran"
   - Estimasi: 5-7 hari
   - Pendaftaran ke instansi berwenang (misal BPN)

9. **pembayaran_pnbp** - "Pembayaran PNBP"
   - Estimasi: 1-2 hari
   - Pembayaran Penerimaan Negara Bukan Pajak

10. **pemeriksaan_bpn** - "Pemeriksaan BPN"
    - Estimasi: 7-10 hari
    - Pemeriksaan oleh Badan Pertanahan Nasional

11. **perbaikan** - "Perbaikan"
    - Estimasi: 3-7 hari
    - Perbaikan dokumen jika ada kekurangan

12. **selesai** - "Selesai"
    - Estimasi: 1 hari
    - Registrasi selesai, dokumen siap diambil

13. **ditutup** - "Ditutup"
    - Estimasi: 1 hari
    - Registrasi ditutup permanen (setelah selesai atau batal)

14. **batal** - "Batal"
    - Tanpa estimasi
    - Registrasi dibatalkan di tengah proses

**Aturan Bisnis Status**:
- Status normal hanya bisa MAJU, tidak bisa MUNDUR
- Pengecualian: Bisa mundur ke status "perbaikan" dari status manapun
- Status "batal" hanya bisa dipilih jika status saat ini masih di awal:
  - draft
  - pembayaran_admin
  - validasi_sertifikat
  - pencecekan_sertifikat
  - perbaikan
- Setelah status "selesai", "batal", atau "ditutup", registrasi masuk finalisasi
- Status "ditutup" hanya bisa diakses oleh Notaris untuk menutup registrasi yang sudah selesai/batal

---

### 5. TABEL `kendala` - Flag Kendala/Masalah

**Fungsi**: Menandai registrasi yang mengalami hambatan/masalah di tahap tertentu

**Kolom**:
- `id` - Nomor unik untuk setiap kendala
- `registrasi_id` - ID registrasi yang mengalami kendala
- `tahap` - Nama tahap dimana kendala terjadi (contoh: "Validasi Sertifikat")
- `flag_active` - Status flag (1 = aktif/ada masalah, 0 = tidak aktif)
- `created_at` - Tanggal kendala dicatat
- `updated_at` - Tanggal kendala terakhir diupdate

**Aturan Bisnis**:
- Satu registrasi bisa memiliki kendala di tahap yang berbeda
- Flag kendala otomatis NONAKTIF saat status berubah jadi "selesai" atau "batal"
- Flag kendala ditampilkan dengan ikon 🚩 di daftar registrasi
- Staff bisa menandai/menghilangkan flag kendala manual

---

### 6. TABEL `registrasi_history` - Riwayat Perubahan Registrasi (BUSINESS LOG)

**Fungsi**: Mencatat SEMUA perubahan yang terjadi pada registrasi (log bisnis permanen)

**Kolom**:
- `id` - Nomor unik untuk setiap history entry
- `registrasi_id` - ID registrasi yang berubah
- `status_old` - Status sebelum perubahan
- `status_new` - Status setelah perubahan
- `catatan` - Catatan yang menyertai perubahan
- `flag_kendala_active` - Apakah ada flag kendala aktif saat perubahan (1/0)
- `flag_kendala_tahap` - Tahap kendala jika ada flag aktif
- `user_id` - ID staff yang melakukan perubahan
- `user_name` - Username staff yang melakukan perubahan
- `user_role` - Role staff (notaris/admin)
- `ip_address` - Alamat IP komputer saat perubahan
- `created_at` - Tanggal dan waktu perubahan

**Apa yang Dicatat**:
- Setiap perubahan status registrasi
- Setiap penandaan flag kendala
- Setiap perubahan catatan internal
- Pembuatan registrasi baru (status NULL → status awal)

**Aturan Bisnis**:
- Data TIDAK BISA dihapus atau diubah (permanent record)
- Digunakan untuk audit trail bisnis
- Ditampilkan di halaman Detail Registrasi sebagai "Riwayat Perubahan"
- Setiap entry menunjukkan SIAPA, KAPAN, dan APA yang berubah

---

### 7. TABEL `audit_log` - Log Audit Sistem

**Fungsi**: Mencatat aktivitas sistem (login, logout, create, update, delete user, backup, dll)

**Kolom**:
- `id` - Nomor unik untuk setiap log entry
- `registrasi_id` - ID registrasi terkait (jika ada, bisa kosong)
- `user_id` - ID staff yang melakukan aksi
- `role` - Role staff (notaris/admin)
- `action` - Jenis aksi: "login", "logout", "create", "update", "delete", "backup_create", "backup_delete"
- `old_value` - Data sebelum perubahan (format JSON, bisa kosong)
- `new_value` - Data setelah perubahan (format JSON, bisa kosong)
- `timestamp` - Tanggal dan waktu aksi

**Apa yang Dicatat**:
- Login user (dengan IP address)
- Logout user
- Create registrasi baru
- Create user baru
- Update user
- Delete user
- Create backup database
- Delete backup file

**Perbedaan dengan registrasi_history**:
- `audit_log` = Log AKSISISTEM (siapa login, siapa buat user, dll)
- `registrasi_history` = Log BISNIS REGISTRASI (perubahan status, flag kendala, dll)

---

### 8. TABEL `cms_content` - Konten Website (CMS)

**Fungsi**: Menyimpan konten halaman website company profile yang bisa diedit

**Kolom**:
- `id` - Nomor unik untuk setiap konten
- `page` - Nama halaman: "home", "layanan", "tentang", "kontak", "testimoni"
- `content` - Isi konten halaman (teks)
- `version` - Versi konten (otomatis bertambah setiap update)
- `updated_by` - ID user yang terakhir update
- `updated_at` - Tanggal konten terakhir diupdate

**Halaman yang Bisa Diedit**:
1. **home** - Konten halaman beranda
2. **layanan** - Konten halaman layanan
3. **tentang** - Konten halaman tentang kantor notaris
4. **kontak** - Konten halaman kontak
5. **testimoni** - Konten halaman testimoni klien

**Aturan Bisnis**:
- Hanya Notaris yang bisa edit konten CMS
- Setiap update menambah version number
- Menyimpan siapa yang update terakhir

---

### 9. TABEL `landing_sections` - Bagian Halaman Landing

**Fungsi**: Menyimpan konten bagian-bagian spesifik di halaman company profile

**Kolom**:
- `id` - Nomor unik untuk setiap section
- `section_key` - Kunci section (unik): "hero_badge", "hero_title", "hero_subtitle", "hero_desc", "masalah_title", "tentang_narasi", "footer_brand_desc"
- `content` - Isi konten section
- `updated_by` - ID user yang update
- `updated_at` - Tanggal section terakhir diupdate

**Section yang Tersedia**:
- `hero_badge` - Badge di banner utama ("Notaris & PPAT Cirebon")
- `hero_title` - Judul utama banner
- `hero_subtitle` - Subtitle banner
- `hero_desc` - Deskripsi di banner
- `masalah_title` - Judul section "Masalah"
- `tentang_narasi` - Narasi section Tentang
- `footer_brand_desc` - Deskripsi brand di footer

---

### 10. TABEL `cleanup_log` - Log Pembersihan Data

**Fungsi**: Mencatat aktivitas pembersihan/penghapusan data lama

**Kolom**:
- `id` - Nomor unik untuk setiap log
- `table_name` - Nama tabel yang dibersihkan
- `rows_affected` - Jumlah baris yang terpengaruh
- `cleanup_date` - Tanggal pembersihan
- `notes` - Catatan tentang pembersihan

---

## LAYER 2: MODEL - FUNGSI OPERASI DATA

### Model `User`
**Fungsi**: Operasi CRUD untuk tabel users
- `findById(id)` - Cari user berdasarkan ID
- `findByUsername(username)` - Cari user berdasarkan username
- `getAll()` - Ambil semua user
- `create(data)` - Buat user baru
- `update(id, data)` - Update user
- `delete(id)` - Hapus user

### Model `Klien`
**Fungsi**: Operasi CRUD untuk tabel klien
- `findById(id)` - Cari klien berdasarkan ID
- `getAll()` - Ambil semua klien
- `create(data)` - Buat klien baru
- `update(id, data)` - Update klien
- `getOrCreate(data)` - Cari klien berdasarkan nama+HP, jika tidak ada buat baru

### Model `Layanan`
**Fungsi**: Operasi untuk tabel layanan
- `findById(id)` - Cari layanan berdasarkan ID
- `getAll()` - Ambil semua layanan

### Model `Registrasi`
**Fungsi**: Operasi CRUD untuk tabel registrasi
- `findById(id)` - Cari registrasi berdasarkan ID
- `findByNomorRegistrasi(nomor)` - Cari registrasi berdasarkan nomor registrasi
- `getAll()` - Ambil semua registrasi
- `create(data)` - Buat registrasi baru
- `update(id, data)` - Update registrasi
- `updateStatus(id, status, catatan)` - Update status registrasi
- `setBatalFlag(id)` - Set flag batal untuk registrasi
- `canBeCancelled(id)` - Cek apakah registrasi masih bisa dibatalkan
- `getStatistik()` - Ambil statistik (total, aktif, selesai, batal)

### Model `Kendala`
**Fungsi**: Operasi untuk tabel kendala
- `findById(id)` - Cari kendala berdasarkan ID
- `getByRegistrasi(registrasiId)` - Ambil semua kendala untuk registrasi
- `getActiveByRegistrasi(registrasiId)` - Ambil kendala yang aktif saja
- `create(registrasiId, tahap)` - Buat kendala baru
- `toggleFlag(id)` - Aktifkan/nonaktifkan flag kendala
- `deactivateAll(registrasiId)` - Nonaktifkan semua kendala untuk registrasi

### Model `AuditLog`
**Fungsi**: Operasi untuk tabel audit_log
- `create(userId, role, action, registrasiId, oldValue, newValue)` - Catat log baru
- `getAll(limit)` - Ambil semua log
- `getRecent(limit)` - Ambil log terbaru
- `getByRegistrasi(registrasiId)` - Ambil log untuk registrasi tertentu
- `getCountByAction()` - Hitung log per jenis aksi

### Model `CMSContent`
**Fungsi**: Operasi untuk tabel cms_content
- `findByPage(page)` - Cari konten berdasarkan halaman
- `getAllPages()` - Ambil semua konten halaman
- `update(page, content, userId)` - Update konten

### Model `RegistrasiHistory`
**Fungsi**: Operasi untuk tabel registrasi_history
- `create(data)` - Catat history baru
- `getByRegistrasi(registrasiId)` - Ambil history untuk registrasi

---

## LAYER 3: SERVICE - LOGIKA BISNIS

### 1. WorkflowService
**Fungsi**: Mengelola alur kerja (workflow) status registrasi

**Method**:
- `updateStatus(registrasiId, newStatus, userId, role, catatan, flagKendala)` - Update status dengan validasi lengkap
- `toggleKendala(registrasiId, tahap, catatan, userId, role)` - Toggle flag kendala
- `getProgress(registrasiId)` - Get progress workflow untuk registrasi
- `getKendala(registrasiId)` - Get kendala untuk registrasi
- `getActiveKendala(registrasiId)` - Get active kendala
- `getHistory(registrasiId)` - Get business history untuk registrasi

**Logika Bisnis Validasi Status**:
1. Status tidak bisa mundur (kecuali ke "perbaikan")
2. Status "batal" hanya bisa sebelum tahap "pembayaran_pajak"
3. Jika status "perbaikan", bisa lanjut ke status manapun sampai "pemeriksaan_bpn"
4. Auto-delete flag kendala saat status jadi "selesai" atau "batal"
5. Setiap perubahan status WAJIB dicatat ke `registrasi_history`

---

### 2. CMSService
**Fungsi**: Mengelola konten website (CMS)

**Method**:
- `getAllPages()` - Ambil semua konten halaman
- `updateContent(page, content, userId, role)` - Update konten halaman

**Aturan Bisnis**:
- Hanya Notaris yang bisa update konten
- Setiap update menambah version number

---

### 3. BackupService
**Fungsi**: Backup dan restore database

**Method**:
- `createBackup(userId, role)` - Backup database (export SQL)
- `createSiteBackup(userId, role)` - Backup seluruh site (code + database)
- `listBackups()` - List semua file backup
- `downloadBackup(filename)` - Download file backup
- `deleteBackup(filename, userId, role)` - Delete file backup

**Lokasi Backup**: Folder `/backups/`
**Format File**: `backup_YYYY-MM-DD_HHMMSS.sql` atau `backup_YYYY-MM-DD_HHMMSS.zip`

---

### 4. UserService
**Fungsi**: Manajemen user

**Method**:
- `getAllUsers()` - Get semua user
- `createUser(userData, creatorId, creatorRole)` - Create user baru
- `updateUser(userId, userData, updaterId, updaterRole)` - Update user
- `deleteUser(userId, deleterId, deleterRole)` - Delete user

**Aturan Bisnis**:
- Hanya Notaris yang bisa manage user
- User tidak bisa menghapus diri sendiri
- Password minimal 6 karakter
- Password di-hash dengan bcrypt (cost 12)

---

### 5. FinalisasiService
**Fungsi**: Mengelola finalisasi registrasi (selesai/batal/ditutup)

**Method**:
- `getFinalisasiList(page, perPage, filter)` - Get list registrasi selesai/batal dengan pagination
- `tutupRegistrasi(registrasiId, userId, notes)` - Tutup registrasi (change status ke "ditutup")
- `reopenCase(registrasiId, userId, targetStatus)` - Buka kembali registrasi yang ditutup
- `getStatistik()` - Get statistik finalisasi

**Aturan Bisnis**:
- Hanya registrasi dengan status "selesai" atau "batal" yang bisa ditutup
- Hanya Notaris yang bisa akses finalisasi
- Tutup registrasi mencatat history
- Reopen registrasi mengembalikan status ke proses

---

## LAYER 4: CONTROLLER - PENGATUR ALUR

### 1. AuthController
**Fungsi**: Mengelola autentikasi (login/logout)

**Method**:
- `login()` - Proses login
- `logout()` - Proses logout
- `isAuthenticated()` - Cek apakah user sudah login
- `getCurrentUser()` - Get data user yang login
- `requireRole(role)` - Validasi role user
- `generateCSRFToken()` - Generate token CSRF untuk form
- `verifyCSRFToken(token)` - Verifikasi token CSRF

**Keamanan**:
- Password di-hash dengan bcrypt
- Session timeout 1 jam
- Session regeneration setiap 30 menit (anti session fixation)
- CSRF token untuk semua form

---

### 2. PublicController
**Fungsi**: Mengelola halaman publik (company profile + tracking)

**Method**:
- `home()` - Tampilkan halaman home (company profile)
- `tracking()` - Tampilkan halaman lacak registrasi
- `searchRegistrasiByNomor()` - Cari registrasi berdasarkan nomor registrasi
- `verifyTracking()` - Verifikasi 4 digit HP untuk tracking
- `showRegistrasi(token)` - Tampilkan detail registrasi untuk publik (dengan token valid)

**Keamanan Publik**:
- Rate limiting: max 5 request per menit
- Verifikasi 4 digit terakhir HP
- Token tracking dengan expiry 24 jam
- Token di-hash untuk keamanan

---

### 3. DashboardController
**Fungsi**: Mengelola semua halaman dashboard internal

**Method**:
- `index()` - Tampilkan dashboard utama
- `registrasi()` - Tampilkan daftar registrasi
- `createRegistrasi()` - Tampilkan form buat registrasi
- `storeRegistrasi()` - Simpan registrasi baru
- `showRegistrasi(id)` - Tampilkan detail registrasi
- `showRegistrasiHistory(id)` - Tampilkan history lengkap registrasi
- `updateStatus()` - Update status registrasi (AJAX)
- `updateKlien()` - Update data klien (AJAX)
- `toggleKendala()` - Toggle flag kendala (AJAX)
- `toggleLock()` - Lock/unlock registrasi
- `users()` - Tampilkan manajemen user
- `createUser()` - Create user (AJAX)
- `updateUser()` - Update user (AJAX)
- `deleteUser()` - Delete user (AJAX)
- `cms()` - Tampilkan manajemen CMS
- `updateCMS()` - Update konten CMS (AJAX)
- `backups()` - Tampilkan manajemen backup
- `createBackup()` - Create backup (AJAX)
- `deleteBackup()` - Delete backup (AJAX)
- `downloadBackup()` - Download backup
- `auditLogs()` - Tampilkan audit log

---

### 4. FinalisasiController
**Fungsi**: Mengelola proses finalisasi registrasi

**Method**:
- `index()` - Tampilkan daftar finalisasi
- `tutupRegistrasi()` - Tutup registrasi (AJAX)
- `reopen()` - Buka kembali registrasi (AJAX)

---

## LAYER 5: VIEW/PRESENTATION - DETAIL HALAMAN

### A. HALAMAN PUBLIK (Company Profile)

#### 1. Homepage (Company Profile)
**URL**: `index.php?gate=home`  
**Akses**: Semua orang (tanpa login)

**Struktur Halaman**:

**HEADER**:
- Logo kantor notaris (ikon dokumen + teks "Notaris Sri Anah SH.M.Kn")
- Menu navigasi: "Masalah", "Layanan", "Testimoni", "Lacak Registrasi"
- Tombol "Hubungi Kami" (link ke WhatsApp)

**HERO SECTION**:
- Badge: "Notaris & PPAT Cirebon"
- Judul: "Pendamping Hukum Resmi untuk Properti, Usaha, dan Keluarga"
- Subtitle: "Aman, transparan, dan sesuai peraturan perundang-undangan."
- Deskripsi: "Melayani pembuatan akta, legalisasi, dan konsultasi hukum dengan professionalism dan penuh kehati-hatian."
- 2 Tombol:
  - "Konsultasi via WhatsApp" (link ke wa.me/6285747898811)
  - "Lihat Testimoni" (scroll ke section testimoni)
- Card "Layanan Cepat":
  - Link "Lacak Registrasi" (ke halaman tracking)
  - Link "Hubungi Kami" (ke WhatsApp)

**SECTION MASALAH** (ID: masalah):
- Judul: "Apakah Anda Mengalami Hal Ini?"
- 4 Kartu Masalah:
  1. Ikon tanda seru - "Takut dokumen salah & berujung sengketa"
     - Deskripsi: "Kesalahan kecil bisa berakibat besar di kemudian hari"
  2. Ikon tanda tanya - "Bingung syarat & prosedur hukum"
     - Deskripsi: "Banyak persyaratan yang tidak dipahami"
  3. Ikon jam - "Proses lama dan tidak transparan"
     - Deskripsi: "Tidak tahu tahap apa yang sedang berjalan"
  4. Ikon chat - "Sulit menghubungi notaris saat butuh cepat"
     - Deskripsi: "Respons lambat saat kondisi mendesak"
- Closing text: "Kantor Notaris Sri Anah SH.M.Kn hadir untuk memastikan setiap proses aman, jelas, dan sesuai hukum."

**SECTION LAYANAN** (ID: layanan):
- Judul: "Layanan Kami"
- Grid kartu layanan (minimal 6 kartu):
  1. **Akta Properti** - Untuk jual beli rumah, tanah, dan pengalihan hak
  2. **Akta Hibah** - Untuk pemberian harta kepada orang lain
  3. **Akta Waris** - Untuk pembagian harta warisan
  4. **Pembagian Hak Bersama** - Untuk harta gono-gini
  5. **Roya Hipotik** - Penghapusan hak tanggungan
  6. **Layanan Lainnya** - Layanan notaris lainnya
- Setiap kartu menampilkan:
  - Ikon sesuai layanan
  - Nama layanan
  - Deskripsi singkat
  - Benefit/manfaat

**SECTION TESTIMONI** (ID: testimoni):
- Judul: "Apa Kata Klien Kami"
- Daftar testimoni dari klien (jika ada)

**SECTION TENTANG** (ID: tentang):
- Judul: "Tentang Kantor Notaris"
- Narasi: "Saya berkomitmen memberikan layanan notaris yang profesional, transparan, dan sesuai dengan peraturan perundang-undangan yang berlaku."
- Informasi kantor

**FOOTER**:
- Brand: "Notaris Sri Anah SH.M.Kn"
- Deskripsi: "Kantor Notaris & PPAT Cirebon. Melayani dengan profesional, jujur, dan bertanggung jawab."
- Informasi kontak
- Link navigasi

---

#### 2. Halaman Lacak Registrasi
**URL**: `index.php?gate=lacak`  
**Akses**: Semua orang (tanpa login)

**Struktur Halaman**:

**HEADER**:
- Tombol "Kembali ke Homepage" (dengan ikon panah)
- Judul: "Lacak Status Registrasi"
- Subtitle: "Masukkan nomor registrasi Anda untuk melihat status"

**STEP 1 - PENCARIAN**:
- Form dengan input:
  - Field "Nomor Registrasi" (placeholder: "Contoh: NP-20260224-0001")
  - Tombol "Lacak Sekarang" (dengan ikon kaca pembesar)
- Submit form → Sistem cek nomor registrasi di database

**STEP 2 - VERIFIKASI** (muncul jika nomor registrasi ditemukan):
- Box "Verifikasi Keamanan" (dengan ikon gembok)
- Text: "Masukkan 4 digit terakhir nomor HP Anda untuk melihat detail registrasi:"
- Form dengan input:
  - Field "Kode Verifikasi" (max 4 karakter, hanya angka)
  - Hidden field "registrasi_id"
  - Tombol "Verifikasi"
- Hint text: "Contoh: Jika HP Anda 081234567**8901**, masukkan: **8901**"
- Submit form → Sistem verifikasi 4 digit terakhir HP

**RESULT** (muncul jika verifikasi berhasil):
- Card hasil dengan informasi:
  - Nomor registrasi (header)
  - Badge status (warna sesuai status)
  - Info:
    - Klien: [nama klien]
    - Layanan: [nama layanan]
    - Tanggal: [tanggal dibuat]
    - Update: [tanggal update terakhir]
  - Tombol "Lihat Detail Lengkap" (link ke halaman detail dengan token)

**ERROR HANDLING**:
- Jika nomor registrasi tidak ditemukan: "Nomor registrasi tidak ditemukan"
- Jika kode verifikasi salah: "Kode verifikasi salah. 4 digit terakhir nomor HP tidak sesuai."
- Jika terlalu banyak request: "Terlalu banyak percobaan. Silakan tunggu beberapa saat."

---

#### 3. Halaman Detail Registrasi (Publik)
**URL**: `index.php?gate=detail&token=[TOKEN]`  
**Akses**: Hanya dengan token valid (expiry 24 jam)

**Struktur Halaman**:

**HEADER**:
- Tombol "Kembali ke Lacak Registrasi" (dengan ikon panah)
- Judul: "Detail Registrasi"

**CARD INFO REGISTRASI**:
- Header:
  - Nomor registrasi (besar)
  - Badge status (warna sesuai status)
- Info grid:
  - Klien: [nama klien]
  - Layanan: [nama layanan]
  - Dibuat: [tanggal dibuat, format: "02 Mar 2026"]
  - Update Terakhir: [tanggal update, format: "02 Mar 2026 14:30"]

**CARD CATATAN PROSES** (Process Log):
- Judul: "📋 Catatan Proses"
- List riwayat perubahan (dari tabel `registrasi_history`):
  - Setiap entry menampilkan:
    - Header:
      - Tanggal & waktu: "📅 02 Mar 2026, 14:30"
      - User: "👤 [nama staff]"
    - Konten:
      - Status: "[status lama] → [status baru]" (jika berubah)
      - Jika ada flag kendala: "🚩 Kendala: [tahap]" (background kuning)
      - Catatan: "[catatan]" (background cream, italic)
- Jika belum ada history: "Belum ada riwayat perubahan."

**CARD PROGRESS STATUS**:
- Judul: "Progress Status"
- Timeline vertical dengan 14 step (sesuai jumlah status):
  - Setiap step menampilkan:
    - Marker (lingkaran):
      - Jika sudah dilewati: ikon centang (warna hijau)
      - Jika status saat ini: lingkaran biru
      - Jika belum: angka urutan (warna abu)
    - Label: nama status (contoh: "Draft / Pengumpulan Persyaratan")
    - Estimasi: waktu estimasi (contoh: "2 hari")
  - Step yang sudah dilewati: warna hijau
  - Step saat ini: warna biru
  - Step belum: warna abu

**CONTACT CTA**:
- Text: "Butuh bantuan? Hubungi kami via WhatsApp"
- Tombol "Hubungi via WhatsApp" (dengan ikon WA, link ke wa.me/6285747898811)

**KEAMANAN**:
- Token diverifikasi di server
- Token expiry 24 jam dari pembuatan
- Jika token tidak valid: "Akses Ditolak - Token tidak valid atau sudah kadaluarsa"
- Redirect ke halaman lacak registrasi jika tidak ada token

---

### B. HALAMAN AUTENTIKASI

#### 4. Halaman Login
**URL**: `index.php?gate=login`  
**Akses**: Semua orang (tapi hanya staff yang punya akun bisa login)

**Struktur Halaman**:

**HEADER**:
- Logo (ikon dokumen)
- Judul: "Notaris Sri Anah SH.M.Kn"
- Subtitle: "Dashboard Notaris & PPAT"

**FORM LOGIN**:
- Hidden field: csrf_token
- Field "Username" (required, autofocus)
- Field "Password" (required, type password)
- Tombol "Login ke Dashboard"
- Link "Kembali ke Homepage"
- Hint text: "Demo: admin/admin123 | notaris/notaris123"

**PROSES LOGIN**:
1. User input username & password
2. Submit form → POST ke `index.php?gate=login`
3. Sistem cek username di database
4. Sistem verify password (bcrypt verify)
5. Jika berhasil:
   - Create session
   - Redirect ke dashboard
6. Jika gagal:
   - Tampilkan pesan error: "Username atau password salah"

**KEAMANAN**:
- CSRF token untuk mencegah CSRF attack
- Password di-hash dengan bcrypt
- Session timeout 1 jam
- Rate limiting untuk mencegah brute force

---

### C. HALAMAN DASHBOARD (INTERNAL)

#### 5. Dashboard Utama
**URL**: `index.php?gate=dashboard`  
**Akses**: Staff internal (setelah login)

**Struktur Halaman**:

**STATISTIK CARD** (4 kartu horizontal):
1. **Total Registrasi**:
   - Ikon: dokumen (total)
   - Angka: [jumlah semua registrasi]
   - Label: "Total Registrasi"

2. **Sedang Diproses**:
   - Ikon: jam (aktif)
   - Angka: [jumlah registrasi selain selesai/batal/ditutup]
   - Label: "Sedang Diproses"

3. **Selesai**:
   - Ikon: centang (selesai)
   - Angka: [jumlah registrasi status "selesai"]
   - Label: "Selesai"

4. **Batal**:
   - Ikon: X (batal)
   - Angka: [jumlah registrasi status "batal"]
   - Label: "Batal"

**TABEL REGISTRASI TERBARU**:
- Header: "Registrasi Terbaru" + Link "Lihat Semua" (ke halaman daftar registrasi)
- Tabel dengan kolom:
  - Nomor Registrasi
  - Klien
  - Layanan
  - Status (badge warna)
  - Tanggal (format: "02 Mar 2026")
- Menampilkan 10 registrasi terbaru
- Jika belum ada registrasi: "Belum ada registrasi"

**TABEL AKTIVITAS TERAKHIR**:
- Header: "Aktivitas Terakhir"
- List vertikal dengan item:
  - Waktu: "02 Mar 14:30"
  - User: "[username]"
  - Aksi: "[action]"
  - Nomor Registrasi: "[nomor_registrasi]" (jika terkait)
- Menampilkan 15 aktivitas terbaru dari `audit_log`
- Jika belum ada aktivitas: "Belum ada aktivitas"

**QUICK ACCESS** (menu cepat):
- Card dengan grid menu:
  - "Daftar Registrasi" (ikon dokumen)
  - "Buat Registrasi Baru" (ikon plus)
  - "Finalisasi" (ikon centang)
  - "Users" (ikon user, hanya Notaris)
  - "CMS" (ikon edit, hanya Notaris)
  - "Backups" (ikon download, hanya Notaris)
  - "Audit Log" (ikon list, hanya Notaris)

---

#### 6. Halaman Daftar Registrasi
**URL**: `index.php?gate=registrasi`  
**Akses**: Staff internal (Admin dan Notaris)

**Struktur Halaman**:

**HEADER**:
- Judul: "Daftar Registrasi"
- Tombol "Tambah Registrasi" (link ke halaman buat registrasi)

**SEARCH & FILTER**:
- Search box: "Cari registrasi..." (cari berdasarkan nomor registrasi, nama klien, layanan)
- Filter dropdown (jika ada): "Semua Status", "Draft", "Proses", "Selesai", "Batal"
- Refresh button
- Info: "Halaman X dari Y" (pagination)

**TABEL REGISTRASI**:
- Kolom:
  1. **Nomor Registrasi**: NP-YYYYMMDD-XXXX (link ke detail)
  2. **Klien**: [nama klien] + [nomor HP] (small text)
  3. **Layanan**: [nama layanan]
  4. **Status**: Badge warna (sesuai status)
  5. **Tanggal**: [tanggal dibuat, format: "02 Mar 2026"]
  6. **Aksi**: Tombol "Detail" (link ke detail registrasi)

**INDIKATOR FLAG KENDALA**:
- Jika registrasi memiliki flag kendala aktif:
  - Tampilkan ikon 🚩 di sebelah nomor registrasi
  - Badge status dengan border merah

**PAGINATION**:
- Jika lebih dari 20 registrasi: pagination
- Tombol: "← Prev", angka halaman, "Next →"
- Info: "Page X of Y (Z registrasi)"

**EMPTY STATE**:
- Jika belum ada registrasi: "Belum ada registrasi" + Tombol "Tambah Registrasi"

---

#### 7. Halaman Buat Registrasi Baru
**URL**: `index.php?gate=registrasi_create`  
**Akses**: Staff internal (Admin dan Notaris)

**Struktur Halaman**:

**HEADER**:
- Judul: "Tambah Registrasi Baru"
- Tombol "Batal" (kembali ke daftar registrasi)

**FORM**:

**SECTION 1: DATA KLIEN**
- Field "Nama Klien" (required, text, placeholder: "Nama lengkap klien")
- Field "Nomor HP" (required, text, placeholder: "08xxxxxxxxxx")

**SECTION 2: DATA REGISTRASI**
- Dropdown "Jenis Layanan" (required):
  - Option: "Pilih Layanan" (default)
  - Option: "Jual Beli"
  - Option: "Hibah"
  - Option: "Waris"
  - Option: "Pembagian Hak Bersama"
  - Option: "Roya"
  - Option: "Lainnya"

- Dropdown "Status" (required, default: "draft"):
  - Option: "Draft / Pengumpulan Persyaratan"
  - Option: "Pembayaran Administrasi"
  - Option: "Validasi Sertifikat"
  - Option: "Pengecekan Sertifikat"
  - (hanya 4 status awal, status lainnya tidak bisa dipilih saat create)

- Textarea "Catatan" (opsional, 5 baris, placeholder: "Catatan...")
  - Auto-fill saat pilih status:
    - draft → "Registrasi Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan."
    - pembayaran_admin → "Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan registrasi."
    - validasi_sertifikat → "Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku."
    - pencecekan_sertifikat → "Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi."

**TOMBOL AKSI**:
- Tombol "Batal" (secondary, link ke daftar registrasi)
- Tombol "Simpan Registrasi" (primary, submit form)

**PROSES SUBMIT**:
1. Validasi form (nama, HP, layanan wajib diisi)
2. AJAX POST ke `index.php?gate=registrasi_store`
3. Sistem:
   - Cari atau buat klien baru (berdasarkan nama+HP)
   - Generate nomor registrasi: NP-YYYYMMDD-XXXX
   - Generate verification_code: 4 digit terakhir HP
   - Generate tracking_token (enkripsi)
   - Simpan registrasi ke database
   - Catat ke `registrasi_history` (entry pertama)
   - Catat ke `audit_log` (action: create)
4. Response:
   - Jika berhasil: Tampilkan popup WhatsApp
   - Jika gagal: Tampilkan pesan error

**POPUP WHATSAPP** (muncul setelah berhasil simpan):
- Header: Ikon WA + "Kirim Notifikasi WhatsApp?"
- Box hijau: "✓ Registrasi Berhasil Dibuat"
- Info:
  - Nama: [nama klien]
  - Nomor Registrasi: [nomor_registrasi]
  - Status: [status_label]
- 2 Tombol:
  - "Lewati" (close popup, redirect ke daftar registrasi)
  - "Kirim WhatsApp" (buka WhatsApp Web dengan pesan template)

**TEMPLATE PESAN WHATSAPP**:
```
Halo Bapak/Ibu [nama klien],

Kami dari Kantor Notaris Sri Anah SH.M.Kn menginformasikan bahwa registrasi Anda telah terdaftar.

Detail Registrasi:
• Nomor Registrasi: [nomor_registrasi]
• Status: [status_label]

Anda dapat memantau status dan progres registrasi secara mandiri melalui tautan tracking yang telah kami berikan.

Apabila terdapat pertanyaan lebih lanjut, silakan menghubungi kami melalui kontak resmi kantor.

Terima kasih atas kepercayaan Anda.

Hormat kami,
Kantor Notaris Sri Anah SH.M.Kn
```

**LINK TRACKING**:
- Format: `http://localhost/newnota/index.php?gate=detail&token=[TOKEN]`
- Token expiry 24 jam
- Klien bisa akses detail registrasi dengan link ini tanpa verifikasi

---

#### 8. Halaman Detail Registrasi (Internal)
**URL**: `index.php?gate=registrasi_detail&id=[ID]`  
**Akses**: Staff internal (Admin dan Notaris)

**Struktur Halaman**:

**BACK BUTTON**:
- Tombol "Kembali ke Daftar Registrasi" (dengan ikon panah kiri)

**CARD 1: INFO REGISTRASI**
- Header: "Informasi Registrasi"
- Grid 3 kolom:
  1. Nomor Registrasi: NP-YYYYMMDD-XXXX (warna primary, bold)
  2. Layanan: [nama layanan]
  3. Klien: [nama klien]
  4. HP: [nomor HP]
  5. Dibuat: [tanggal + waktu, format: "02 Mar 2026 14:30"]
  6. Status: Badge warna (sesuai status)
- Tombol "Edit" (pojok kanan atas, untuk edit data klien)

**CARD 2: UPDATE STATUS**
- Judul: "Update Status"
- Form dengan field:
  - "Status Saat Ini": Read-only (badge warna)
  - "Status Baru": Dropdown (hanya status yang valid sesuai workflow)
    - Jika status saat ini "draft": bisa pilih "pembayaran_admin", "batal"
    - Jika status saat ini "pembayaran_admin": bisa pilih "validasi_sertifikat", "batal"
    - Dan seterusnya sesuai workflow
    - Jika status "selesai", "batal", "ditutup": dropdown disabled + info "Status Final - Tidak Dapat Diedit"
  - "Catatan": Textarea (5 baris)
    - Auto-fill sesuai status yang dipilih
    - Bisa edit manual
  - "Flag Kendala": Checkbox
    - Label: "Tandai ada kendala" atau "✓ Kendala Aktif" (jika sudah ada)
    - Hint: "Klik untuk menandai kendala" atau "Perlu monitoring ekstra"
    - Hanya muncul jika status bukan "selesai", "batal", "ditutup"
- Tombol "Simpan Perubahan" (primary, dengan ikon centang)

**PROSES UPDATE STATUS**:
1. Pilih status baru dari dropdown
2. Catatan auto-fill (bisa edit)
3. Centang flag kendala jika ada masalah
4. Submit → AJAX POST ke `index.php?gate=update_status`
5. Sistem validasi:
   - Status tidak bisa mundur (kecuali ke "perbaikan")
   - Status "batal" hanya bisa sebelum "pembayaran_pajak"
6. Jika valid:
   - Update database
   - Catat ke `registrasi_history`
   - Auto-delete flag kendala jika status jadi "selesai" atau "batal"
7. Response: "Status berhasil diperbarui"

**CARD 3: KIRIM PEMBARUAN REGISTRASI** (WhatsApp)
- Background hijau gradient
- Judul: "📱 Kirim Pembaruan Registrasi"
- Text: "Kirim notifikasi status terbaru registrasi ini ke klien via WhatsApp"
- Tombol besar: "Kirim Status Terbaru via WhatsApp" (dengan ikon WA)

**TEMPLATE PESAN WHATSAPP** (update status):
```
Halo Bapak/Ibu [nama klien],

Berikut update status registrasi Anda:

Nomor Registrasi: NP-YYYYMMDD-XXXX
Status Sebelumnya: [status_old]
Status Baru: [status_new]

Catatan: [catatan]

Anda dapat memantau detail registrasi melalui link tracking yang telah diberikan.

Terima kasih.

Hormat kami,
Kantor Notaris Sri Anah SH.M.Kn
```

**CARD 4: PROGRESS WORKFLOW**
- Judul: "Progress Workflow"
- Timeline vertical dengan 14 step (sama seperti halaman publik)
- Setiap step menampilkan:
  - Marker (centang jika sudah, angka jika belum)
  - Label status
  - Estimasi waktu
  - Deskripsi singkat

**CARD 5: RIWAYAT PERUBAHAN** (Business History)
- Header: "Riwayat Perubahan (Business History)" + Link "Lihat Semua Riwayat"
- Tabel dengan kolom:
  1. Timestamp: "02 Mar 2026 14:30"
  2. User: "[username]"
  3. Flag: "🚩 ON" (jika ada kendala) atau "-"
  4. Status Change: "[old] → [new]" atau "No change"
  5. Notes: "[catatan]"
- Menampilkan 7 entry terbaru
- Jika belum ada: "Belum ada riwayat perubahan"

**MODAL EDIT KLIEN** (muncul saat klik "Edit"):
- Popup modal dengan form:
  - Field "Nama Klien" (required, text)
  - Field "Nomor HP" (required, text)
  - Tombol "Batal"
  - Tombol "Simpan"
- Submit → AJAX POST ke `index.php?gate=update_klien`
- Update tabel `klien` berdasarkan klien_id dari registrasi

---

#### 9. Halaman Riwayat Lengkap Registrasi
**URL**: `index.php?gate=registrasi_history&id=[ID]`  
**Akses**: Staff internal (Admin dan Notaris)

**Struktur Halaman**:

**BACK BUTTON**:
- Tombol "Kembali ke Detail Registrasi"

**CARD INFO REGISTRASI**:
- Grid 3 kolom:
  1. Nomor Registrasi: NP-YYYYMMDD-XXXX
  2. Klien: [nama klien]
  3. Total Riwayat: [jumlah] entri

**TABEL RIWAYAT LENGKAP**:
- Judul: "📜 Riwayat Lengkap Semua Perubahan"
- Tabel dengan kolom:
  1. #: Nomor urut (dari yang terbaru ke terlama)
  2. Timestamp: "02 Mar 2026 14:30"
  3. User: "[username]"
  4. Action: "[action]" (jika ada)
  5. Flag: "🚩 ON" atau "-"
  6. Status Change: "[old] → [new]" atau "No change"
  7. Notes: "[catatan]"
- Menampilkan SEMUA history (tanpa limit)
- Urutan: terbaru di atas

---

#### 10. Halaman Finalisasi
**URL**: `index.php?gate=finalisasi`  
**Akses**: Hanya Notaris

**Struktur Halaman**:

**HEADER**:
- Judul: "Tutup Registrasi"
- Subtitle: "Kelola penutupan registrasi dengan status Selesai, Batal, dan Ditutup"

**STATISTIK CARD** (4 kartu horizontal):
1. **Total Registrasi**:
   - Background: gradient primary
   - Angka: [jumlah semua registrasi selesai+batal+ditutup]
   - Label: "Total Registrasi"

2. **Status Selesai**:
   - Background: gradient hijau
   - Angka: [jumlah registrasi status "selesai"]
   - Label: "Status Selesai"

3. **Status Batal**:
   - Background: gradient merah
   - Angka: [jumlah registrasi status "batal"]
   - Label: "Status Batal"

4. **Status Ditutup**:
   - Background: gradient abu
   - Angka: [jumlah registrasi status "ditutup"]
   - Label: "Status Ditutup"

**FILTER & SEARCH**:
- Search box: "🔍 Cari registrasi..."
- Filter tabs:
  - "Semua" (active: background primary)
  - "Selesai" (active: background hijau)
  - "Batal" (active: background merah)
  - "Ditutup" (active: background abu)
- Info: "Halaman X dari Y"

**TABEL REGISTRASI**:
- Kolom:
  1. Nomor Registrasi: NP-YYYYMMDD-XXXX
  2. Klien: [nama klien] + [nomor HP] (small text)
  3. Layanan: [nama layanan]
  4. Status: Badge warna (hijau=selesai, merah=batal, abu=ditutup)
  5. Aksi: Tombol "📋 Detail" (link ke detail finalisasi)

**PAGINATION**:
- Sama seperti halaman daftar registrasi

**EMPTY STATE**:
- Jika belum ada registrasi: "Belum ada registrasi yang perlu finalisasi"

---

#### 11. Halaman Detail Registrasi Finalisasi
**URL**: `index.php?gate=registrasi_detail_finalisasi&id=[ID]`  
**Akses**: Hanya Notaris

**Struktur Halaman**:

**BACK BUTTON**:
- Tombol "Kembali ke Finalisasi"

**CARD INFO REGISTRASI** (sama seperti detail registrasi biasa)

**CARD AKSI FINALISASI**:
- Jika status "selesai" atau "batal":
  - Tombol "Tutup Registrasi" (primary)
  - Form popup dengan field:
    - "Catatan Penutupan" (opsional, text)
    - Tombol "Batal"
    - Tombol "Tutup" (confirm)
  - Submit → AJAX POST ke `index.php?gate=tutup_registrasi`
  - Update status jadi "ditutup"
  - Catat ke `registrasi_history`

- Jika status "ditutup":
  - Tombol "Buka Kembali Registrasi" (warning)
  - Form popup dengan pilihan:
    - Radio button: "Kembali ke Proses" (status: pemeriksaan_bpn)
    - Radio button: "Tetap [Selesai/Batal]" (hapus finalisasi saja)
    - Tombol "Batal"
    - Tombol "Buka Kembali" (confirm)
  - Submit → AJAX POST ke `index.php?gate=reopen_case`
  - Update status sesuai pilihan
  - Catat ke `registrasi_history`

**CARD RIWAYAT** (sama seperti detail registrasi biasa)

---

#### 12. Halaman Manajemen User
**URL**: `index.php?gate=users`  
**Akses**: Hanya Notaris

**Struktur Halaman**:

**HEADER**:
- Judul: "User Management"
- Tombol "Tambah User" (dengan ikon plus)

**SEARCH & FILTER**:
- Search box: "Cari user..."
- Filter dropdown: "Semua Role", "👑 Super Admin", "👤 Admin"
- Refresh button

**TABEL USER**:
- Kolom:
  1. Username: "[username]" + Badge "👑 Super Admin" (jika notaris)
  2. Role: Badge "👑 Super Admin" (gold) atau "👤 Admin" (cyan)
  3. Dibuat: [tanggal dibuat, format: "02 Mar 2026"]
  4. Terakhir Update: [tanggal update, format: "02 Mar 2026"]
  5. Aksi:
     - Tombol "Edit" (primary, dengan ikon edit)
     - Tombol "Hapus" (danger, dengan ikon trash)
     - Jika user saat ini: text "User saat ini" (tidak bisa edit/hapus)

**MODAL TAMBAH USER**:
- Judul: "👤 Tambah User Baru"
- Form dengan field:
  - "Username" (required, text)
  - "Password" (required, password, min 6 karakter)
  - "Role" (required, dropdown):
    - "👤 Admin"
    - "👑 Notaris (Super Admin)"
- Tombol "Batal"
- Tombol "Simpan"
- Submit → AJAX POST ke `index.php?gate=users` (action: create)
- Catat ke `audit_log` (action: create)

**MODAL EDIT USER**:
- Judul: "✏️ Edit User"
- Form dengan field:
  - "Username" (required, text, value: username saat ini)
  - "Password" (opsional, password, min 6 karakter)
  - "Role" (required, dropdown, value: role saat ini)
- Tombol "Batal"
- Tombol "Simpan"
- Submit → AJAX POST ke `index.php?gate=users` (action: update)
- Catat ke `audit_log` (action: update)

**HAPUS USER**:
- Konfirmasi: "Apakah Anda yakin ingin menghapus user [username]?"
- Submit → AJAX POST ke `index.php?gate=users` (action: delete)
- Catat ke `audit_log` (action: delete)

**ATURAN BISNIS**:
- User tidak bisa menghapus diri sendiri
- Hanya Notaris yang bisa manage user
- Password minimal 6 karakter
- Username harus unik

---

#### 13. Halaman Manajemen CMS
**URL**: `index.php?gate=cms`  
**Akses**: Hanya Notaris

**Struktur Halaman**:

**GRID CMS CARD** (5 kartu):
- Setiap kartu menampilkan:
  - Header: Nama halaman (Home, Layanan, Tentang, Kontak, Testimoni) + Badge version (v1)
  - Preview konten: 150 karakter pertama dari konten
  - Footer:
    - "Updated: [tanggal + waktu]"
    - "by [username]"
  - Tombol "Edit"

**MODAL EDIT CMS**:
- Judul: "Edit CMS Content"
- Form dengan field:
  - Hidden field: "page" (nama halaman)
  - Textarea "Content" (10 baris, full width)
- Tombol "Batal"
- Tombol "Simpan"
- Submit → AJAX POST ke `index.php?gate=cms` (action: update)
- Update tabel `cms_content`
- Catat ke `audit_log` (action: update)

---

#### 14. Halaman Manajemen Backup
**URL**: `index.php?gate=backups`  
**Akses**: Hanya Notaris

**Struktur Halaman**:

**AKSI BACKUP**:
- Tombol "Backup Database" (primary, dengan ikon download)
  - Create backup SQL database
  - Submit → AJAX POST ke `index.php?gate=backups` (action: create, type: database)
- Tombol "Backup Full Site" (secondary, dengan ikon folder)
  - Create backup ZIP (code + database)
  - Submit → AJAX POST ke `index.php?gate=backups` (action: create, type: site)

**TABEL DAFTAR BACKUP**:
- Kolom:
  1. Filename: "backup_2026-03-02_143025.sql"
  2. Size: "[size] KB"
  3. Tanggal: "02 Mar 2026 14:30"
  4. Tipe: Badge "DATABASE" atau "SITE"
  5. Aksi:
     - Tombol "Download" (link ke `?gate=backups&file=[filename]`)
     - Tombol "Hapus" (danger)

**HAPUS BACKUP**:
- Konfirmasi: "Apakah Anda yakin ingin menghapus backup [filename]?"
- Submit → AJAX POST ke `index.php?gate=backups` (action: delete)
- Catat ke `audit_log` (action: backup_delete)

**LOKASI FILE**:
- Folder: `/backups/`
- Format: `backup_YYYY-MM-DD_HHMMSS.sql` atau `.zip`

---

#### 15. Halaman Audit Log
**URL**: `index.php?gate=audit`  
**Akses**: Hanya Notaris

**Struktur Halaman**:

**CARD LOG AKTIVITAS SISTEM**:
- Judul: "Log Aktivitas Sistem"
- Tabel dengan kolom:
  1. Timestamp: "02 Mar 2026 14:30:25"
  2. User: "[username]" atau "System"
  3. Role: Badge "Notaris" atau "Admin"
  4. Action: "[action]" (login, logout, create, update, delete, backup_create, backup_delete)
  5. Registrasi: "[nomor_registrasi]" (jika terkait)
  6. Details:
     - "Old: [JSON old_value]" (jika ada)
     - "New: [JSON new_value]" (jika ada)
- Pagination: 20 log per halaman

**CARD STATISTIK LOG PER ACTION**:
- Judul: "Statistik Log per Action"
- Tabel dengan kolom:
  1. Action: "[action]"
  2. Jumlah: [count]
- Menampilkan jumlah log per jenis action

**PAGINATION**:
- Sama seperti halaman lainnya

---

## ATURAN BISNIS LENGKAP

### 1. Workflow Status Registrasi

**STATUS FLOW NORMAL**:
```
draft → pembayaran_admin → validasi_sertifikat → pencecekan_sertifikat → 
pembayaran_pajak → validasi_pajak → penomoran_akta → pendaftaran → 
pembayaran_pnbp → pemeriksaan_bpn → selesai
```

**STATUS KHUSUS**:
- **Perbaikan**: Bisa dari status manapun, bisa lanjut ke status manapun sampai pemeriksaan_bpn
- **Batal**: Hanya bisa dari draft, pembayaran_admin, validasi_sertifikat, pencecekan_sertifikat, perbaikan
- **Ditutup**: Hanya bisa dari selesai atau batal (oleh Notaris via finalisasi)

**VALIDASI STATUS**:
- Status tidak bisa mundur (kecuali ke perbaikan)
- Status batal hanya sebelum pembayaran_pajak
- Status selesai/batal/ditutup adalah final (tidak bisa edit status, hanya bisa tutup atau reopen)

### 2. Flag Kendala

**ATURAN**:
- Flag kendala menandai ada masalah di tahap tertentu
- Satu registrasi bisa punya multiple kendala di tahap berbeda
- Flag auto-delete saat status jadi selesai atau batal
- Flag bisa toggle (ON/OFF) manual oleh staff
- Flag ditampilkan dengan ikon 🚩 di daftar registrasi

**WORKFLOW**:
1. Staff centang checkbox "Flag Kendala"
2. Pilih tahap kendala (otomatis sesuai status saat ini)
3. Isi deskripsi kendala (opsional)
4. Submit → Catat ke `kendala` dan `registrasi_history`

### 3. Tracking Publik

**VERIFIKASI**:
1. User input nomor registrasi
2. Sistem cek nomor registrasi di database
3. Jika ditemukan: minta verifikasi 4 digit terakhir HP
4. User input 4 digit HP
5. Sistem verifikasi dengan data klien
6. Jika berhasil: generate tracking_token
7. Redirect ke halaman detail dengan token

**TOKEN**:
- Format: base64(json(id, code, time)).hash
- Expiry: 24 jam dari pembuatan
- Validasi: cek token di database (tracking_token column)
- Jika tidak valid: "Akses Ditolak - Token tidak valid atau sudah kadaluarsa"

### 4. WhatsApp Integration

**TRIGGER KIRIM WA**:
1. Saat create registrasi baru (popup setelah simpan)
2. Saat update status (tombol di detail registrasi)

**TEMPLATE PESAN**:
- Create: "Registrasi Anda telah terdaftar..." + nomor registrasi + status + link tracking
- Update: "Update status registrasi Anda..." + status lama → status baru + catatan

**CARA KERJA**:
1. Generate pesan template
2. Encode URL (encodeURIComponent)
3. Buka WhatsApp Web: `https://wa.me/6285747898811?text=[MESSAGE]`
4. Staff kirim manual (tidak otomatis)

### 5. Role-Based Access Control

**NOTARIS (Super Admin)**:
- ✅ Semua akses (full access)
- Dashboard, Daftar Registrasi, Buat Registrasi, Detail Registrasi
- Finalisasi (tutup/buka registrasi)
- Users (create, edit, delete user)
- CMS (edit konten website)
- Backups (create, download, delete)
- Audit Log (view semua log)

**ADMIN (Staff)**:
- ✅ Dashboard, Daftar Registrasi, Buat Registrasi, Detail Registrasi
- ❌ Finalisasi
- ❌ Users
- ❌ CMS
- ❌ Backups
- ❌ Audit Log

**PUBLIK (Klien)**:
- ✅ Company Profile (home, layanan, tentang, kontak, testimoni)
- ✅ Lacak Registrasi (dengan verifikasi)
- ✅ Detail Registrasi (dengan token valid)
- ❌ Dashboard
- ❌ Login (tidak punya akun)

### 6. Keamanan

**AUTENTIKASI**:
- Password di-hash dengan bcrypt (cost 12)
- Session timeout 1 jam
- Session regeneration setiap 30 menit (anti session fixation)
- CSRF token untuk semua form

**OTORISASI**:
- Role check di setiap halaman sensitif
- Redirect otomatis jika tidak punya akses
- Log security event jika ada unauthorized access

**DATA PUBLIK**:
- Rate limiting: max 5 request per menit
- Verifikasi 4 digit HP untuk tracking
- Token tracking dengan expiry 24 jam
- Token di-hash untuk keamanan

**AUDIT TRAIL**:
- Semua aksi tercatat di `audit_log` (sistem) dan `registrasi_history` (bisnis)
- IP address tercatat
- Data lama dan baru tersimpan (JSON)
- Data tidak bisa dihapus (permanent record)

---

## STRUKTUR FOLDER LENGKAP

```
newnota/
├── config/
│   ├── constants.php       # Konstanta (role, status, path, dll)
│   └── database.php        # Koneksi database PDO
├── controllers/
│   ├── AuthController.php      # Login, logout, CSRF, session
│   ├── DashboardController.php # Semua halaman dashboard
│   ├── PublicController.php    # Halaman publik (company profile, tracking)
│   └── FinalisasiController.php # Finalisasi registrasi
├── models/
│   ├── User.php            # CRUD users
│   ├── Klien.php           # CRUD klien
│   ├── Layanan.php         # CRUD layanan
│   ├── Registrasi.php         # CRUD registrasi
│   ├── Kendala.php         # CRUD kendala
│   ├── AuditLog.php        # CRUD audit_log
│   ├── CMSContent.php      # CRUD cms_content
│   └── RegistrasiHistory.php  # CRUD registrasi_history
├── services/
│   ├── WorkflowService.php     # Logika workflow status
│   ├── CMSService.php          # Logika CMS
│   ├── BackupService.php       # Logika backup
│   ├── UserService.php         # Logika user management
│   └── FinalisasiService.php   # Logika finalisasi
├── views/
│   ├── auth/
│   │   └── login.php       # Halaman login
│   ├── company_profile/
│   │   └── home.php        # Homepage company profile
│   ├── dashboard/
│   │   ├── index.php           # Dashboard utama
│   │   ├── registrasi.php         # Daftar registrasi
│   │   ├── registrasi_create.php  # Buat registrasi
│   │   ├── registrasi_detail.php  # Detail registrasi
│   │   ├── registrasi_history.php # Riwayat lengkap
│   │   ├── finalisasi.php      # Finalisasi
│   │   ├── registrasi_detail_finalisasi.php # Detail finalisasi
│   │   ├── users.php           # Manajemen user
│   │   ├── cms.php             # Manajemen CMS
│   │   ├── backups.php         # Manajemen backup
│   │   └── audit_logs.php      # Audit log
│   ├── public/
│   │   ├── tracking.php        # Lacak registrasi
│   │   └── registrasi_detail.php  # Detail registrasi publik
│   └── templates/
│       ├── header.php      # Header HTML (CSS, nav)
│       └── footer.php      # Footer HTML (JS, copyright)
├── utils/
│   ├── helpers.php         # Fungsi helper umum
│   └── security_helpers.php # Fungsi keamanan (CSRF, rate limit, token)
├── database/
│   └── notaris_ppat (4).sql # Database SQL dump
├── backups/                # Folder backup database
├── logs/                   # Folder log aplikasi
├── public/
│   └── assets/
│       ├── css/            # File CSS
│       └── js/             # File JavaScript
├── index.php               # Entry point utama (routing)
└── .htaccess               # Konfigurasi Apache
```

---

## KESIMPULAN

Aplikasi **Notaris & PPAT Tracking System** adalah sistem manajemen registrasi notaris yang lengkap dengan fitur:

1. **5 Layer Arsitektur**: Database → Model → Service → Controller → View
2. **10 Tabel Database**: users, klien, layanan, registrasi, kendala, registrasi_history, audit_log, cms_content, landing_sections, cleanup_log
3. **5 Service**: WorkflowService, CMSService, BackupService, UserService, FinalisasiService
4. **4 Controller**: AuthController, PublicController, DashboardController, FinalisasiController
5. **15 Halaman**: 3 publik, 1 auth, 11 dashboard
6. **3 Role**: Notaris (full access), Admin (limited), Publik (tracking only)
7. **14 Status Workflow**: Dari draft sampai selesai/batal/ditutup
8. **Fitur Utama**: Tracking registrasi, Flag kendala, Finalisasi, Backup, CMS, WhatsApp notification
9. **Keamanan**: Authentication, Authorization, CSRF, Rate Limiting, Audit Trail, Token tracking

Semua fitur didokumentasikan secara detail tanpa kode, hanya penjelasan fungsionalitas, input, output, dan alur kerja.
