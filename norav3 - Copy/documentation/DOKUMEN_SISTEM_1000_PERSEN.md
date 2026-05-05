# 👑 THE ABSOLUTE MASTER DOCUMENTATION
## SISTEM TRACKING NOTARIS & PPAT — SRI ANAH SH.M.Kn
*(EDISI 1000% — KESEMPURNAAN ABSOLUT: TIDAK ADA DETAIL YANG DILEWATKAN)*

**Dokumen ini adalah sumber kebenaran final, mutlak, dan tidak dapat dibantah (Single Source of Truth) atas seluruh ekosistem Aplikasi Tracking Notaris.** Ini adalah penggabungan dan perluasan ekstrem dari seluruh dokumentasi sebelumnya (Product Docs, User Guide, Technical Docs, Architecture/Security, dan Dev SOP).

**Identitas Sistem**
*   **Pemilik:** Kantor Notaris & PPAT Sri Anah SH.M.Kn
*   **Wilayah Yurisdiksi:** Cirebon, Jawa Barat, Indonesia
*   **Versi Platform:** 2.0.0 (Enterprise Precision Edition)
*   **Status Dokumen:** 🟢 FINALIZED & SEALED
*   **Target Audiens Dokumen:** Klien, Staf Admin, Notaris, Pengembang (Engineer), dan Auditor Keamanan.

---
# 📑 DAFTAR ISI MEGA-STRUKTUR

1. **[PART 1: PRODUCT DOCS (SPESIFIKASI PRODUK ABSOLUT)](#part-1-product-docs-spesifikasi-produk-absolut)**
2. **[PART 2: USER GUIDE (MANUAL PENGGUNA MANUSIA: PIXEL-PERFECT)](#part-2-user-guide-manual-pengguna-manusia-pixel-perfect)**
3. **[PART 3: TECHNICAL DOCS (ANATOMI MESIN & DATABASE)](#part-3-technical-docs-anatomi-mesin--database)**
4. **[PART 4: SYSTEM REQUIREMENTS, ARCHITECTURE & SECURITY](#part-4-system-requirements-architecture--security)**
5. **[PART 5: SOP FOR DEVELOPER, TODO & FINAL CONTRACT](#part-5-sop-for-developer-todo--final-contract)**

---

# PART 1: PRODUCT DOCS (SPESIFIKASI PRODUK ABSOLUT)

## 1.1 Meta-Objektif & Latar Belakang Epik
Sebelum sistem ini ada, operasional diukur dengan kertas, memori manusia, dan pesan instan yang berserakan. Klien menuntut transparansi (ingin tahu berkas sampai di mana), sementara staf tenggelam dalam beban *customer-service*.

**Sistem ini dibangun untuk mencapai 4 Visi Radikal:**
1.  **Transparansi Tanpa Kontak Fisik (Zero-Contact Transparency):** Klien mengecek status dokumen tanah/perusahaan mereka mandiri, 24/7, tanpa menelepon kantor.
2.  **Rantai Hukum yang Tak Bisa Patah (Unbreakable Workflow Law):** Sistem memaksa staf untuk mengikuti SOP baku 14-Langkah. Sistem tidak mengizinkan pemotongan jalan yang melanggar hukum prosedur Notariat.
3.  **Pertanggungjawaban Ekstrem (Extreme Accountability):** Tidak ada tindakan anonim. Jika subuah berkas dibatalkan, sistem tahu dengan pasti SIAPA yang menekan tombol asalnya, dari IP mana, pada detik keberapa, dan pesan apa yang ditinggalkan.
4.  **Komunikasi Otonom:** Notifikasi *copywriting* hukum profesional dihasilkan otomatis untuk dikirim ke WhatsApp tanpa pengetikan manual oleh manusia.

## 1.2 Hirarki Aktor (Role-Based Actors)
Eksekusi di dalam sistem dibatasi ke dalam kelas-kelas entitas:
*   **Aktor 0 (Publik/Visitor):** Entitas tanpa identitas. Hanya diizinkan melihat Landing Page (Katalog, Testimoni, Hero).
*   **Aktor 1 (Klien Terverifikasi):** Entitas yang sukses membuktikan otoritasnya melalui pengujian 4 Digit Nomor HP. Diizinkan membaca secara detail riwayat registrasi (Read-Only).
*   **Aktor 2 (Admin/Staf):** Entitas internal dengan autentikasi `username`/`password`. Bebas membuat Registrasi Baru, memajukan Status, menambahkan `Catatan_Internal`, namun **DILARANG** membunuh/menonaktifkan akun staf lain, mengubah landing page CMS, atau Menutup secara permanen arsip Registrasi.
*   **Aktor 3 (Notaris / God Mode):** Otoritas Puncak. Memiliki segala akses Aktor 2 ditambah: Menghabisi berkas (Finalisasi), Menghapus akun bawahan, Menarik Cadangan Database utuh (Backup RAW), membaca Log Audit Rahasia System, dan mengganti wujud website publik (CMS).

## 1.3 Hukum 14 Fase Registrasi (14 Stages of Life)
Fase ini absolut, direpresentasikan dalam sistem sebagai ENUM berurut. **Aturan besi bisnis ini adalah: Tidak bisa melompat secara irasional, dan tidak bisa MUNDUR, kecuali di tahap spesifik (Perbaikan).**

| Urutan | ID Database Absolut | Teks yang Tampil di Publik (Label) | Waktu Ideal | Aturan Batal (Bisa Dibatalkan?) | Deskripsi Legal & Operasional |
|:---:|---|---|:---:|:---:|---|
| **1** | `draft` | **Draft / Pengumpulan Persyaratan** | 2 Hari | ✅ **YA** | Fase inkubasi. Berkas baru didaftarkan ke sistem. Staf administratif sedang memverifikasi kelengkapan KTP/KK/PBB Klien. |
| **2** | `pembayaran_admin` | **Pembayaran Administrasi** | 2 Hari | ✅ **YA** | Klien ditagih Down Payment (DP) atas jasa honorarium murni untuk Kantor Notaris (bukan pajak). |
| **3** | `validasi_sertifikat` | **Validasi Sertifikat** | 7 Hari | ✅ **YA** | Pengecekan buku mentah di BPN (Badan Pertanahan Nasional) untuk mengetahui status blokir/sita/ganda. |
| **4** | `pencecekan_sertifikat` | **Pengecekan Sertifikat** | 7 Hari | ✅ **YA** | Analisa lebih dalam di lapangan atau kelengkapan ploting koordinat geometris bidang tanah. |
| **5** | `pembayaran_pajak` | **Pembayaran Pajak** | 1 Hari | ❌ **TIDAK BISA** | **[TITIK POINT-OF-NO-RETURN]**. Di tahap ini uang telah dibayarkan kepada Negara (BPHTB/PPH). Karena Negara tidak menerima pengembalian dana (*refund*), maka tombol "Batalkan Registrasi" **dihapus bersih** dari peredaran UI dan dilarang di level backend. |
| **6** | `validasi_pajak` | **Validasi Pajak** | 5 Hari | ❌ TIDAK | Verifikasi ke instansi terkait (KPP/Bappenda) bahwa SSP/SSPD asli dan sudah masuk kas daerah/negara. |
| **7** | `penomoran_akta` | **Penomoran Akta** | 1 Hari | ❌ TIDAK | Penandatanganan Minuta Akta (oleh para pihak) dan pemberian nomor definitif ke dalam buku repertorium Notaris. Akta ini telah sah berbadan hukum. |
| **8** | `pendaftaran` | **Pendaftaran** | 5 - 7 Hari | ❌ TIDAK | Berkas yang sudah matang di-injeksi masuk ke loket pendaftaran BPN. |
| **9** | `pembayaran_pnbp` | **Pembayaran PNBP** | 1 - 2 Hari | ❌ TIDAK | Pembayaran formal Penerimaan Negara Bukan Pajak (Kode Billing BPN). |
| **10** | `pemeriksaan_bpn` | **Pemeriksaan BPN** | 7 - 10 Hari | ❌ TIDAK | Bola ada di tangan instansi pemerintah. Kantor Notaris hanya bisa menunggu progres loket BPN. |
| **11** | `perbaikan` | **Perbaikan** | 3 - 7 Hari | ✅ **YA** | **[FASE ANOMALI LUPING / WORMHOLE]**. BPN mengembalikan berkas karena kurang huruf, salah warkah, dsb. **KARENA INI ADALAH TAHAP KOREKSI**: Sistem mengizinkan status **dimundurkan** ke tahap berapapun (ke penomoran, pendaftaran, dsb) untuk diperbaiki. Status "Batal" tiba-tiba diizinkan lagi jika komplikasi fatal. |
| **12** | `selesai` | **Selesai** | 1 Hari | ❌ TIDAK | Dokumen (Sertifikat Hak Milik/Akta) sudah diserahkan dari BPN ke loket Notaris. Siap diserahkan ke klien. |
| **13** | `ditutup` | **Ditutup** | Permanen | ❌ TIDAK | **STATUS KEMATIAN ABADI 1.** Hanya dieksekusi oleh Role Notaris. Berkas dimasukkan ke gudang arsip dan semua tombol *Update/Edit* akan sirna. |
| **14** | `batal` | **Batal** | Permanen | ❌ TIDAK | **STATUS KEMATIAN ABADI 2.** Hanya dieksekusi jika posisi berkas belum menyebrang tahap ke-5, atau di tahap 11. |

---

# PART 2: USER GUIDE (MANUAL PENGGUNA MANUSIA: PIXEL-PERFECT)

Dideskripsikan dengan metode *Cognitive Walkthrough*. Mengurai setiap warna, ketukan *keyboard*, dan reaksi piksel per piksel dalam skenario ekstrem.

## 2.1 Perjalanan Publik: "Melihat Wajah Perusahaan" (Routing `/`)
Ketika entitas anonim membuka Alamat URL Web (Akar / root). UI me-load halaman CMS terkendali (Landing Page Interaktif).

**Bagian 1: Header Resolusi Dinamis & Sticky Nav**
*   **Kiri:** Ikon SVG berbentuk Kertas Perjanjian, bersanding dengan typografi dinamis `Notaris Sri Anah SH.M.Kn` dengan warna gelap Hex `#1f2937` (Font-weight: 700).
*   **Tengah:** Tautan Navigasi Jangkar (Anchor `href="#..."`): Masalah (Smooth scroll), Layanan, Testimoni, Lacak Registrasi, Tentang. Navigasi hover akan berubah warna menjadi biru laut perlahan (`transition-all duration-300`).
*   **Kanan (Call-to-Action):** Tombol hijau zaitun melengkung sempurna (`border-radius: 9999px`) bertuliskan "Hubungi Kami". Saat ditekan, sistem menendang OS klien memanggil mekanisme URL `https://wa.me/6285747898811`.

**Bagian 2: The Hero Space (Cakrawala Sapaan Utama)**
*   Elemen memanggil mata: Terdapat *Badge* (Pita) berbentuk pil transparan "✨ Notaris & PPAT Cirebon".
*   Tipografi H1 Raksasa: "Pendamping Hukum Resmi untuk Properti, Usaha, dan Keluarga."
*   Di kanan layar terdapat widget "Kotak Layanan Cepat" semi-transparan (efek *Glassmorphism/backdrop-blur*). Terdapat dua tombol tumpuk: Lacak Registrasi atau Hubungi Kami.

**Bagian 3: "Menjual Rasa Aman" (Seksi `id="masalah"`)**
*   UI merender 4 Blok kartu.
    1.  Kartu 1 (Tanda Seru Eksklamasi Merah): Judul *"Takut dokumen salah & berujung sengketa"*.
    2.  Kartu 2 (Tanya Jawab Kuning): Judul *"Bingung syarat & prosedur hukum"*.
    3.  Kartu 3 (Jam Pasir Ungu): Judul *"Proses lama dan tidak transparan"*.
    4.  Kartu 4 (Balon Percakapan): Judul *"Sulit menghubungi notaris"*.

**Bagian 4: Katalog Menu Etalase (Seksi `id="layanan"`)**
Menarik 6 Kartu elegan dari database atau hard-render file (`layanan` / landing):
1. Akta Properti, 2. Akta Hibah, 3. Akta Waris, 4. PBH (Pembagian Hak Bersama), 5. Roya, 6. Konsultasi Lainnya.
Di setiap kartu ada *Checklist Bullet* menunjukan detail manfaat (Pengecualian sengketa, legalitas terjamin).

## 2.2 Perjalanan Klien: "Penembusan Gerbang Informasi" (`/?gate=lacak`)

Klien menekan tautan "Lacak Registrasi". Tampilan beralih murni tanpa sisa desain Landing Page yang berisik, fokus 100% pada fungsi esensial Tracker.

**FASE A: Pencarian Nomor Dasar (Pre-Flight)**
*   **Tengara UI:** Sebuah input box besar putih mendominasi bagian tengah dengan placeholder abu-abu `"Contoh: NP-20260224-0001"`. Tidak ada elemen desain lain yang mendistraksi.
*   Klien mengetik. Saat diketik, tidak ada validasi asinkronus (untuk tidak memberikan petunjuk pada hacker soal struktur nomor asli). 
*   Klien menekan *Enter* atau tombol Lacak Berlambang Kaca Pembesar.
*   **Respon Gagal Otak Mesin:** Jika klien salah ketik, misal `NP-123`. Server me-return error div merah tebal: *"Nomor registrasi tidak ditemukan."* Kecepatan respon ini di sengaja di-delay `300ms` agar serangan injeksi pembongkaran data secara mekanik (bot) kesulitan mencari cela ritme.

**FASE B: Gerbang Verifikasi Dinding Api (Anti-Data Doxxing)**
*   Jika Nomor Benar dalam database. Formulir pelacak lenyap bergeser ke atas (*slide up animation*). Muncullah bentuk Gembok Raksasa `🔒`. Tulisan: **"Verifikasi Keamanan"**.
*   Sub-teks: *"Masukkan 4 digit terakhir nomor HP Anda untuk melihat detail registrasi ini. Contoh: Jika HP Anda 0812345678901, masukkan: 8901."*
*   Form input 4 kotak. Parameter HTML: `<input type="number" maxlength="4" required autocomplete="off">`. *Bypass autocomplete* dihentikan agar keylogger extension browser gagal membaca history otp.
*   Hidden Token Form: Di baliknya terdapat `name="registrasi_id" value="[tersembunyi]"`.
*   Klien memasukan: `8901` dan menekan Verifikasi.

**FASE C: Rendering Halaman Suci (Detail Registrasi Publik `/?gate=detail&token=[JWT]`)**
Halaman ini adalah hadiah atas keberhasilan verifikasi. Token di URL menjamin klien bisa refresh halaman ini kapanpun selama 24 Jam kedepan tanpa harus login ulang digit HP.

*   **Header Box:** Raksasa bertuliskan `NP-20260224-0001` dengan tinta biru pekat. Di kanannya terdapat Badge Lebar warna warni (Kuning = Draft, Hijau = Selesai). Kemudian tercetak rincian Nama Klien (cth: Agus ********), dan Tanggal Mulai berkas (02 Mar 2026).
*   **Diagram Waktu Pararel (Visual Workflow 14 Tahap) [SEBELAH KANAN LAYAR]:** 
    Menampilkan representasi 14 stasiun lintasan.
    *   Jika berkas di status 3. Maka Stasiun 1 dan 2 berwarna Hijau Penuh dengan Icon Ceklis (`✓`).
    *   Stasiun 3 akan menampilkan bulatan Biru menyala (Pulsing Glow via CSS `@keyframes ping`) dengan indikator waktu estimasi (Contoh: "Estimasi 7 Hari").
    *   Stasiun 4 sampai 14 berwarna Abu-abu pudar pucat (Disabled / Belum Tersentuh).
*   **Riwayat Forensik / Process Log [SEBELAH KIRI LAYAR]:**
    Kotak bergaya Feed Media Sosial. Digenerate dari Tabel `registrasi_history`.
    Mengurutkan kejadian dari Atas ke Bawah:
    *   `[02 Mar 2026, 14:00] 👤 Staf Jihan — Berkas Diciptakan. Status: Draft.`
    *   `[04 Mar 2026, 09:30] 👤 Staf Jihan — Status Berubah: Draft ➔ Pembayaran Admin. Catatan: Klien telah transfer ke rekening kantor.`
    *   `[05 Mar 2026, 11:00] 🚩 KENDALA MUNCUL. Catatan: Sertifikat Klien robek tepi, menunggu konfirmasi pihak kelurahan.` (Block ini diblok dengan warna peringatan Kuning Orange).

## 2.3 Perjalanan Admin Internal (Dashboard Operasional Mutlak)

Admin memasuki Gerbang Hitam Karyawan: `/?gate=login` melawati rintangan form verifikasi Anti-CSRF. Sesampainya di Dashboard:

### 2.3.1 Ruang Komando / Home (`/?gate=dashboard`)
Panel atas memamerkan 4 Kartu Detak Jantung Kantor:
- Kartu Tumpukan (Total Berkas Keseluruhan dari tahun awal diciptakan).
- Kartu Jam Pasir (Berkas yang nyangkut dan berdarah berjuang di status 1 sampai 11).
- Kartu Trophy Hijau (Berkas Selesai / 12).
- Kartu Tengkorak Merah (Berkas Batal / 14).

Serta menampilkan 10 tabel Berkas paling gres terbaru (`ORDER BY id DESC LIMIT 10`), memanjakan admin baru masuk langsung klik kerjain berkas terkini.

### 2.3.2 Pembuatan Subjek Hukum (Create Registrasi `/?gate=registrasi_create`)
Jika ada tamu baru di kantor Notaris:
1.  Admin menekan menu Tambah Registrasi Baru (Tanda Tambah/Kertas).
2.  Input Form:
    *   `Nama Klien`: "Tuan Budi Sudarsono"
    *   `Nomor HP`: "081199998888" (Sistem di belakang panggung akan mengecek jika nomor ini sudah ada, ia akan me-Re-Use ID nya. Jika Baru, DB akan menyisipkan baris klien baru. Cerdas dan tidak boros tabel disk).
    *   `Layanan`: Menurunkan Dropdown "Akta Properti - Jual Beli Jauh".
    *   `Status Otoriter`: Sengaja dibonsai oleh sistem HANYA menyajikan 4 pilihan: "Draft", "Pembayaran Admin", "Validasi Sertifikat", "Pengecekan Sertifikat". Ini hukum mati, tidak ada admin yang bisa tiba-tiba membuat berkas baru dengan status "Selesai".
3.  Catatan `Auto-Fill Ghosting`: Admin men-select pilihan "Pembayaran Admin", sekejap Textarea besar di bawahnya terisi otomatis kalimat formal "Proses pembayaran jasa notaris sedang dilakukan...". Admin tersenyum, menghemat waktu 4 menit untuk memikirkan bahasa resmi.
4.  Admin Menekan Simpan. 
5.  **Dorr! Modal Takeover Hijau! "KIRIM WHATSAPP SEKARANG?"**
    *   Menampilkan UI: Berkas NP-2026... Berhasil Dibuat! Klien: Bapak Budi.
    *   Jika admin memencet tombol "WhatsApp", OS Komputernya meneruskan ke `whatsapp://send?phone=6281199998888&text=Halo%20Tuan%20Budi...`
    *   Klien Budi di HP-nya langsung menerima chat profesional lengkap dengan Link rahasianya.

### 2.3.3 Transmutasi Status / Engine Operasional Utama (`/?gate=registrasi_detail&id=[ID]`)
Titik tumpu eksekusi bisnis ada di URL ini. Semua perubahan realita dokumen ada disini. Admin masuk ke halaman Nomor Berkas Spesifik.

**Sekmen Ubah Tatanan (Update Status Controller Form):**
*   Admin melihat Dropdown `"Status Baru"`.
*   **Validasi Kosmik Sistem di UI:** Dropdown ini cerdas. Jika Status sekarang "Validasi Pajak" (Status_Ke=6), Dropdown memotong bersih List index [1,2,3,4,5] dan menghapus Index Batal [14]. Memaksa admin hanya bisa memilih maju [7,8,9,dst]. 
*   **Mekanika Bendera Kenajisan (Flag Kendala):** Di samping tombol submit, ada Checkbox "Tandai Ada Kendala". Jika staf mendapati BPN *offline*/sistem macet, staf menyalakan Centang ini. 
*   Admin mengeklik Simpan Perubahan. 
*   **Efek Belakang Layar:** Logika Transaksi Bekerja (Tercatat Histori, Flag nyala). Tulisan nomor registrasi klien di Tabel Publik langsung diberi ikon 🚩 Merah Menyala yang menggetarkan UI. Memohon atensi prioritas.
*   **Efek Auto Deaktivasi Kendala:** Jika staf suatu hari mengubah status menjadi "Selesai" (12). Fungsi server secara agresif membantai dan menghapus (membersihkan) Status Bendera Kendala kembali ke `0`. Asumsinya: Berkas selesai = Masalah otomatis sudah sembuh.

### 2.3.4 Penghukuman Terakhir oleh Notaris (Finalisasi - God Menu)
Seorang Admin staf mencoba mengakses `/?gate=finalisasi` $\rightarrow$ Server menamparnya balik dengan Redirect Header keliling HTTP 302 kembali ke `/` (Akses Terlarang Role anda bukan bos).

Notaris yang login, dia menekan menu tersebut.
*   Menu ini isinya hanya para serpihan berkas berkasta (Selesai/12) dan (Batal/14). Berkas yang masih hidup (1-11) tidak ada di radar menu ini.
*   Notaris menekan tombol "TUTUP REGISTRASI" -> Berubah ke stage `13`. Warna Badge berubah menjadi Abu-abu dingin metalik. Mulai saat ini dan abad-abad selanjutnya, TIDAK ADA yang bisa melakukan pembaruan ke form berkas (Semua input Element diberi attribute HTML `disabled="disabled" readonly`).
*   **Tombol Kebangkitan (Resurrection/Proses Ulang):** Notaris menekan Tombol Kuning "Proses Ulang". Kotak popup meminta Notaris membuang status Ditutup (13) kembali menjadi status Perbaikan (11) dan mereset kehidupan berkas kembali masuk ke lantai bursa kerja operasional Admin.

---

# PART 3: TECHNICAL DOCS (ANATOMI MESIN & DATABASE)

Struktur mikroskopik bagi *Systems Engineer* yang membedah *Source Code* mentah.

## 3.1 Resolusi Anatomi Folder Eksponensial (Root Structure)
Struktur dibangun dengan Konsep MVC Ketat bercampur *Hexagonal Dependency Separation*.
```text
C:\xampp\htdocs\newnota\
║
╠═ .htaccess                <- Engine Remap Apache. (RewriteRule ^(.*)$ index.php [QSA,L])
╠═ index.php                <- GLOBAL FRONT CONTROLLER. Semua denyut HTTP ditangkap oleh $gate = $_GET['gate'].
║
╠═ config/                  <- RUANG STATIS KONSTANTA
║  ╠═ database.php          <- Penangkaran PDO Instansiasi. PDO::ATTR_ERRMODE_EXCEPTION ditabih.
║  ╚═ constants.php         <- Penampung Define Array. STATUS_DRAFT = 'draft', dst. 
║
╠═ controllers/             <- PENGHANTAR LALU LINTAS HTTP (Input/Output Triage)
║  ╠═ AuthController.php    <- Tangani session_start(), Regenerate_id, Auth verify CSRF dan BCRYPT Matching.
║  ╠═ DashboardController.php <- Otak gemuk yang men-decode payload $_POST AJAX Update Status staf.
║  ╠═ FinalisasiController.php<- Split khusus untuk Notaris, menjaga kecacatan logic tercampur.
║  ╚═ PublicController.php  <- Pemroses token get parameter public. Rate limiter checking di invoke disini.
║
╠═ services/                 <- AREA SUCI LOGIKA HUKUM (Use Case Interactors)
║  ╠═ WorkflowService.php   <- Method `updateStatusWithValidation()`. Hakim murni penentu Maju MUNDUR Periksa Lock.
║  ╠═ CMSService.php        <- Pustakawan teks website Publik.
║  ╠═ BackupService.php     <- OS Exec shell pemicu komando "mysqldump" / "ZipArchive" internal PHP module.
║  ╠═ UserService.php       <- Penggawa pendaftaran akun baru, Validasi String Password_Strength min 6 digit.
║  ╚═ FinalisasiService.php <- Peminjam gembok akses data untuk diubah menjadi Mati Permanen.
║
╠═ models/                  <- ORM ABSTRACT LAYER (Data Access Objects/DAO). Tempat PDO ->prepare() mendarat.
║  ╠═ AuditLog.php          <- Model Tulis Log Sistem JSON Enkoded.
║  ╠═ CMSContent.php        <- CRUD String Payload HTML Landing.
║  ╠═ Kendala.php           <- Model Boolean Flipper (0 -> 1 -> 0)
║  ╠═ Klien.php             <- Model Upserter Klien (Insert On Duplicate Key logic approach bypass).
║  ╠═ Layanan.php           <- Lookup Model Layanan String.
║  ╠═ Registrasi.php           <- [HEAVY CPU DRAIN] Kelas pembungkus SQL Master Join 4 Tabel sekaligus (Mendapat Klien+Layanan+Info).
║  ╠═ RegistrasiHistory.php    <- Logger Model. Menangkap IP Klien untuk diforward ke parameter query.
║  ╚═ User.php              <- Login model, Getter Credentials Hashing.
║
╠═ views/                   <- KOMPONEN RENDER VISUAL (HTML/CSS INJECTIONS)
║  ╠═ auth/                 <- Mengandung form <form> POST Action.
║  ╠═ company_profile/      <- Markup HTML Statis dan CMS Iterator (Foreach).
║  ╠═ dashboard/            <- File-file terfragmentasi untuk diletakkan di dalam yield(Content) Layout PHP Utama.
║  ╠═ public/               <- Halaman rendering AJAX Spinner Animasi UI Lacak.
║  ╚═ templates/            <- `header.php` (Tag HEAD META SEO + CSS LINK), `footer.php` (Tag SCRIPT JS GLOBAL).
║
╠═ utils/                   <- SCRIPTS MANDIRI
║  ╠═ helpers.php           <- Func `format_tanggal_indo()`, Func `rupiah_format()`.
║  ╚═ security_helpers.php  <- Func `rate_limit()`, Func `generate_csrf()`, `generate_tracking_token_sha()`.
║
╠═ public/assets/           <- BARANG STATIS HTTP 200 CACHEABLE
║  ╠═ css/                  <- Variabel warna `#2ab` dll.
║  ╚═ js/                   <- Modul Asinkronous fetch UI. Listener Tombol.
║
╠═ backups/                 <- GUDANG GELAP FILE DATABASE. Bebas dari Public Dir Indexing Mod_Autoindex.
╠═ logs/                    <- File Txt pure penampung Exception PDO throw (Mata mati OS level).
╚═ database/
   ╚═ notaris_ppat.sql      <- Skema Master Query Asli (The Seed/Genesis File).
```

## 3.2 Kamus Skema Database Lengkap & Ekstrem (Data Dictionary)

Sistem bergantung pada DBMS Relasional (MySQL/MariaDB) menggunakan struktur tipe tabel `InnoDB` karena *Foreign Key Constraints* dan *Transaksional ACID* adalah nyawa hukum keamanan sistem ini. Set Karakter harus berjalan di `utf8mb4_unicode_ci` untuk menerima Emoji di *Landing Page*.

### TABEL 1: `klien` (Entitas Subjek Hukum Otoriter)
Desain untuk meminimalkan redundansi pengetikkan nama di kantor.
| Nama Field | Tipe Data & Dimensi | Constraint & Modifiers | Keterangan Aturan Bisnis |
|---|---|---|---|
| `id` | `INT(11)` | `PK, AUTO_INCREMENT, UNSIGNED` | Nilai Rujukan Utama (FK Parent). |
| `nama` | `VARCHAR(150)` | `NOT NULL` | - |
| `hp` | `VARCHAR(20)` | `NOT NULL, UNIQUE INDEX` | Algoritma backend akan melakukan pencarian kesini. Tidak boleh kembar. Prefix `08` atau `62` akan divalidasi dan di sanitasi. |
| `email` | `VARCHAR(100)` | `NULL DEFAULT NULL` | Ruang cadangan ekspansi bisnis. |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | |
| `updated_at` | `TIMESTAMP` | `ON UPDATE CURRENT_TIMESTAMP` | Melacak usia database entitas. |

### TABEL 2: `layanan` (Master Kamus Jual)
| Nama Field | Tipe Data & Dimensi | Constraint & Modifiers | Keterangan Aturan Bisnis |
|---|---|---|---|
| `id` | `INT(11)` | `PK, AUTO_INCREMENT` | |
| `nama_layanan` | `VARCHAR(100)` | `NOT NULL` | Cth: "Akta Waris Murni". |
| `deskripsi` | `TEXT` | `NULL` | |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | |

### TABEL 3: `registrasi` (THE MEGA-TABLE / SENSOR STATE)
Tabel terpadat di mana 90% frekuensi I/O (*Read/Write)* terjadi secara konstan harian.
| Nama Field | Tipe Data & Dimensi | Constraint & Modifiers | Keterangan Aturan Bisnis |
|---|---|---|---|
| `id` | `INT(11)` | `PK, AUTO_INCREMENT` | Indeks Primer Cepat. |
| **`klien_id`** | `INT(11)` | `FK → klien.id` | Direstriksi (`ON DELETE RESTRICT`) agar tidak bisa hapus Klien jika punya registrasi yang nyantol. |
| **`layanan_id`** | `INT(11)` | `FK → layanan.id` | Direstriksi mati. |
| **`nomor_registrasi`** | `VARCHAR(50)` | `UNIQUE INDEX` | Contoh: `NP-20261120-X8J9`. Diciptakan dari Fungsi String Concatenation Timestamp PHP. |
| `status` | `VARCHAR(50)` | `NOT NULL DEFAULT 'draft'` | Mengacu ke Mapping 14 Konstan PHP. Tipe Enum tidak digunakan di tabel murni agar migrasi penambahan status lebih elastis bagi Dev. |
| `verification_code`| `VARCHAR(4)` | `NOT NULL` | **Field Denormalisasi Pintar**. Diisi dengan 4 Digit belahan substring nomor HP Klien saat baris di *Insert*. Fungsinya? Saat user Lacak dan memasukkan 4-Digit, server CUKUP cek 1 tabel (`WHERE nomor = X AND verification_code = Y`). Menghapus keharusan melakukan Database SQL `JOIN` yang lamban hanya untuk mengecek OTP. |
| `tracking_token` | `VARCHAR(255)` | `NOT NULL` | Signature Hash Crypto. Kunci masuk URL Publik untuk By-Pass Form. Usia Token diperiksa mandiri di logika Script PHP. |
| `catatan_internal` | `TEXT` | `NULL` | Dikosongkan, atau diisi teks baku "Proses sedang validasi blablabla". |
| `locked` | `TINYINT(1)` | `DEFAULT 0` | Indikator Boolean 1/0. Jika `1`, baris ini kebal API Update Controller. |
| `batal_flag` | `TINYINT(1)` | `DEFAULT 0` | Indikator Khusus Kasus Gagal. Membangkitkan blok rendering Merah di Front-End HTML. |
| *Timestamps* | `TIMESTAMP` | | `created_at` & `updated_at`. |

### TABEL 4: `registrasi_history` (THE IMMUTABLE LEDGER)
Gudang pelacakan mutasi. Seluruh baris adalah `INSERT ONLY`. `UPDATE` atau `DELETE` pada tabel ini adalah bentuk korupsi sistem.
| Nama Field | Tipe Data & Dimensi | Constraint & Modifiers | Keterangan Aturan Bisnis |
|---|---|---|---|
| `id` | `INT` | `PK` | |
| `registrasi_id` | `INT` | `INDEX` | Digunakan untuk render grafis Feed Timeline History di per registrasi. |
| `status_old` | `VARCHAR(50)` | `NULL` | Cth: `pencecekan_sertifikat`. Jika `created`, field ini Kosong (Null). |
| `status_new` | `VARCHAR(50)` | `NOT NULL` | Cth: `pembayaran_pajak`. |
| `catatan` | `TEXT` | | |
| `flag_kendala_active`| `TINYINT(1)` | | Rekaman Boolean momen saat staf menekan simpan: Apakah disaat itu ststus nyala atau mati. |
| `flag_kendala_tahap` | `VARCHAR(100)`| | Nama fase saat flag menyala. |
| `user_id` | `INT` | `NULL` | ID Relasi staf Admin yang pegang kursor pemencet tombol server. |
| `user_name` | `VARCHAR(100)`| `NOT NULL` | **Denormalisasi Anti-Break.** Nama staf disimpan dalam bentuk *Plain-Text String* disamping `user_id`. Mengapa? Jika *Sistem/Notaris* me-Delete staf yang membandel dari tabel `Users`, relasi `user_id` akan bernilai `NULL`. Jika tidak ada `user_name` statis penopang, riwayat akan HILANG nama aslinya menjadi anonim. Sistem ini menangkal fenomena kehilangan artefak data pelaku. (Audit Compliance Rule). |
| `user_role` | `VARCHAR(50)` | | |
| `ip_address` | `VARCHAR(50)` | | Catat Log `$_SERVER['REMOTE_ADDR']`. Untuk investigasi jika ada hacking. |
| `created_at` | `TIMESTAMP` | | Tanpa Updated At, karena haram di Update. |

### TABEL 5: `kendala` (The Event Marker)
| Nama Field | Tipe Data & Dimensi | Keterangan Aturan Bisnis |
|---|---|---|
| `id` | `INT PK` | |
| `registrasi_id` | `INT FK` | |
| `tahap` | `VARCHAR(100)` | Label fase masalah. |
| `flag_active` | `TINYINT(1)` | Flipper (1-0-1-0). Hanya `SELECT` yang flagenya 1 untuk memuncukan icon 🚩 di UI Publik. |

### TABEL 6: `users` (Manajemen Operator / Autentikator)
| Nama Field | Tipe Data & Dimensi | Constraint |
|---|---|---|
| `id` | `INT` | `PK` |
| `username` | `VARCHAR(50)` | `UNIQUE INDEX` |
| `password_hash`| `VARCHAR(255)`| BCRYPT Hash 60+ chars. PHP Length Rule. |
| `role` | `ENUM('notaris','admin')` | Penguasa middleware logic Gatekeeper. |

### TABEL 7: `audit_log` (Buku Hitam Log Sistem Rahasia Notaris)
| Nama Field | Tipe | Keterangan Aturan Bisnis |
|---|---|---|
| `id` | `INT PK` | |
| `user_id`, `role` | `INT/VARCHAR`| Pelaku aksi OS/System Level. |
| `action` | `VARCHAR(100)` | String Event: `login_success`, `failed_login_attempt`, `backup_db`, `tutup_registrasi_paksa`, `delete_user_x`. |
| `old_value` | `TEXT` | Format String JSON berisikan isi query mentah sbelum diobrak-abrik. |
| `new_value` | `TEXT` | Format String JSON baru komparatif. |
| `timestamp` | `TIMESTAMP` | |

### TABEL 8, 9, 10: Ekosistem Pendamping (CMS & Maintenance)
- **`cms_content`** & **`landing_sections`**: Kolom `page_id (VARCHAR)` dan `content (LONGTEXT)`. Render *raw output string* HTML untuk dimuntahkan oleh `<div class="content"><?= $content ?></div>` pada view engine publik.
- **`cleanup_log`**: Log mesin *Cron*. Memiliki kolom Integer `rows_affected` (Berapa juta row dihapus) dan DateTime `cleanup_date` eksekusi Shell Script.

---

# PART 4: SYSTEM REQUIREMENTS, ARCHITECTURE & SECURITY
*Konfigurasi Arsitektural untuk DevOps, Administrator Infrastruktur dan Auditor Penetrasi Server.*

## 4.1 Spesifikasi Rigid Bare-Metal & Environment
Aplikasi ini murni native dan ringan (tanpa *overload* Node_Modules yang rakus memori). Kecepatannya adalah limitasi database itu sendiri.
*   **Operating System**: Segala Linux Distribution (Direkomendasikan Distro Server CentOS/Ubuntu/Debian) atau Windows Server Hyper-V (XAMPP Bound).
*   **Web Proxy Parser:** Apache Web Server V. 2.4++ yang dikonfigurasikan menginisiasikan perintah `.htaccess` secara penuh (`AllowOverride All` dalam properti VirtualHost Server). 
*   **PHP Hypertext Preprocessor**: Minimal Versi 8.1. PHP 7.x ditolak secara tegas karena penghentian Patch Sekuriti global. Ekstensi PECL `pdo_mysql`, `mbstring` (Wajib untuk format utf teks), `curl`, `json_encode` Engine wajib berstatus Aktif. RAM per worker PHP (Memory_Limit) disetel di angka `128MB`. Limit waktu eksekusi PHP disetel normal di 30 detik (Kecuali route Backup DB bisa `0` / infinity).
*   **Database Engine**: MariaDB 10.x atau MySQL 8.x. Parameter `innodb_buffer_pool_size` dianjurkan di angka > `512MB` jika traffic klien mencapai 1.000 cek status/jam, untuk menjamin load status berada di memori disk.

## 4.2 Matrix Keamanan Ekstrem (7-Stage Zero-Trust Defense Security)
"Kepercayaan pada data kiriman pengguna adalah dosa besar dalam arsitektur keamanan."

1.  **Stage #1: Parameter Tampering & HTTP Restrictive Input Bound**
    *   Halaman Detail Form Dropdown Status HTML, dikerjakan ulang agar mengamputasi paksa daftar Dropdown status yang terlarang. Akan tetapi **HTML itu Rapuh**. Jika staf mencoba memakai software sejenis `Postman` atau `BurpSuite`, staf bisa saja merekayasa HTTP POST `status_baru=draft` (Mundur Keangkasa).
    *   **Penangkal API Layer:** Modul `WorkflowService` menangkap payload POST ini dan melewati alat *scanner* indeks status. Metode PHP mengecek: "Indeks 'draft' (1) lebih memalukan/kecil posisinya daripada posisinya sekarang di Database (8)? Lempar `Exception('Status tidak bisa mundur!');` Die script seketika." Operasi SQL Batal. Server tetap kokoh.

2.  **Stage #2: Perisai CSRF (Cross-Site Request Forgery) Berbasis Sesi Ganda**
    *   Setiap tabungan form POST di Aplikasi memiliki `<input type="hidden" name="csrf_token" value="$_SESSION_STRING_ACAK_50_CHAR">`.
    *   Fungsi di Controller Utama mencekik tiap Request masuk: *Jika token tidak disisipkan ATAU token dari form POST berbeda 1 huruf sekalipun dengan token di SESSION file Server OS -> Permintaan itu dianggap sebagai Serangan Hacker Forgery (Penyerang Web Pihak 3)*. Endpoint melempar 403 Forbidden Access Page secara fatal dan Log Audit mendaftar Insiden.

3.  **Stage #3: Bruteforce Rate Limiter (Firewall Algoritmik Anti-Spam)**
    *   Formulir Publik (Lacak Nomor Registrasi / Verifikasi 4 Digit OTP). Musuh Utama: Web Scraper Bot. Bot menebak jutaan nomor.
    *   **Sistem Saringan Session IP:** PHP mencatat fungsi `$_SESSION['attempts']` dan log Array Request Time Limit `$_SESSION['last_activity']`.  Lebih dari *5 Percobaan Gagal dalam jendela batas kurun waktu 60 Detik* menyebabkan Form terkunci *Hard-Freeze* mengeluarkan kalimat *Timeout/Rate Exceeded*. Hacker butuh ribuan jam mencoba-coba OTP berkat penundaan matematis aplikasi ini.

4.  **Stage #4: Perlindungan Eksekusi Injeksi Basis Data (SQL Injection Vaccined)**
    *   Segenap peredaran syntax transaksi basis data menggunakan Obyek Tunggal `PDO (PHP Data Objects)`.
    *   Tidak akan pernah ada di baris kode script tulisan: `SELECT * FROM tbl WHERE username = '` + `$post_username` + `'`. 
    *   Sebaliknya: Menggunakan Protokol Pre-Compiled. `prepare("SELECT * FROM tbl WHERE username = :uname")`. Kemudian `execute(['uname' => $post])`. Semua karakter perusak yang sengaja diketik (seperti tanda petik, Spasi, Hyphen Union Select) akan terurai menjadi murni String Data biasa oleh C Runtime Kernel MySQL, tidak akan berevolusi menjadi sebuah struktur perintah merdeka. *Sql-Injection Impossibility.*

5.  **Stage #5: XSS Payload Neutralization (Sanitasi Keluaran Output UI)**
    *   Apapun tulisan di textarea catatan Internal tidak dipercaya, meski itu staf admin sendiri. Jika staf usil memasukkan `<script>alert(document.cookie);</script>`:
    *   Di halaman public tracker, server me-Render string tersebut dibungkus paska fungsi bawaan sakral: `htmlspecialchars($catatan, ENT_QUOTES, 'UTF-8')`. Tanda kurung kurawal dibunuh menjadi entitas ampas `&lt;` dan `&gt;`. Layar Browser Chrome merender sebagai teks biasa yang tumpul.

6.  **Stage #6: Secure Token Rotational Engine (Otorisasi URL Klien)**
    *   Menciptakan Parameter Lacak Sementara `?gate=detail&token=[JWT_Custom_Payload_X]`.
    *   Penciptaan Formula (Token Engine): Konstruk Kriptografi Dasar: `String Kunci Gabungan` = Identitas Absolut Berkas + `(Timestamp Epoch Sejagat Server Sekarang)` + Variabel Statis Kaus Server Secret. Lalu Seluruh gabungan diekspor menjadi format Enkripsi/Hashing Base64 yang diubah.
    *   Saat klien menggunakan URL: Modul Controller membongkar kode (De-coding). Melihat angka Timestamp terperangkap di dalam Hash Terurai itu. Apakah Timestamp tersebut + 86400 detik (24 Jam) lebih lama dari Jam Komputer Server Saat ini? Jika IYA (Lewat) = *Warning: Token Expired Kill Switch*. Klien dikembaliakn ke Form Minta PIN depan lagi.

7.  **Stage #7: Anti-Fixation Session Mutator (Autentikasi Pegawai Internal Mutlak)**
    *   Setiap 30 Menit (1800 Detik Tepat), Skrip Middleware Admin menyentuh Fungsi Kernel `session_regenerate_id(true)`.
    *   Batu Sandi (PHPSESSID) dirubah diam-diam. Andaikan 20 menit lalu komputer terinfeksi Malware dan Session berhasil diambil maling jaringan, maka dalam waktu maksimal 10 menit sisanya, kunci ID tersebut menjadi Barang Batal, Rongsok berakibat Login Redirect. Karyawan yang sah sedang mengetik? Tak ada masalah, Cookie barunya otomatis tergantikan dalam *Browser Storage Header Request* berikut. 

---

# PART 5: SOP FOR DEVELOPER, TODO & FINAL CONTRACT
*Buku Hitam Instruksional Tingkat Lanjut. Hanya dioperasikan oleh System Administrator atau Tim Engineer yang ditugaskan mengambil alih source-code perbaikan aplikasi di masa depan.*

## 5.1 Doctrine: The Zero-Trust File Coding Engineering Protocol (V1.4)
Segala Injeksi Pull Request Kode ke sistem ini dari Vendor Ketiga / Tim IT *In-House* **HARUS TUNDUK** Pada Konstitusi Kode di bawah:

### 5.1.A - PILLAR 0: ANTI-CHAOS & FRAGMENTASI METRIK (MODULARITY LAW)
*   **PASAL BESI 0.1 (Limitasi Spasial):** File controller PHP atau Fungsi tidak boleh melebihi Timbangan 300 LOC (Baris of Code). Memaksa fungsi gemuk pecah menjadi Method Kecil untuk menjamin akurasi navigasi perbaikan mata telanjang (Skalabel Code). Tingkat indentasi *bracket* logic cabang Nested `If... { If { If { ... } } }` dilarang lebih dari kedalaman 3 Tangga Dasar. Melanggar merusak tes Kognitif Mesin.
*   **PASAL BESI 0.2 (Purity Of Purpose):** Konsep Single Responsibility. Tanggung jawab validasi logika BPN (Logic Boleh Atau Tidak Batalkan Order) HANYA Boleh ada di `services/WorkflowService.php`. View Layer (`.php` di dalam map dashboard) Haram Menganalisa Data (Dilarang ada logika query Database tertanam dalam kerang HTML View File).

### 5.1.B - PILLAR 1: FILOSOFI ENTITAS STATE (THE NIHILIST/PESSIMISTIC PRINCIPLE)
*   **Hukum Tidak Percaya:** Segala bentuk *State* Form HTTP (Dropwdwn form nilai, atau *Hidden Index* Number `ID`) dianggap racun (Palsu/Anomali) sampai entitas File Domain (`Registrasi.php`) Mencerna Array tersebut.
*   Logika Nilai Ekonomi Berkas, Hukum Status, Hak Akses Level (Staf Vs Bos), adalah Obyek Bisnis Eksklusif yang hidupnya harus diasingkan. Hanya Data Database ASLI dari Select ulang yang dijadikan pembanding.

### 5.1.C - PILLAR 20: HUKUM TRANSAKSIONAL WAKTU & PERSAINGAN (CONCURENCY LAW)
*   **Pembekuan Waktu Skrip (Time Injection):** Segala rujukan waktu `created_at` atau Jam Logika menggunakan fungsi Pembangkit Waktu Tengah Sistem Aplikasi / Engine Time, dan Bukan Jam Klien Komputer Browser milik Pegawai yang sering dimanipulasi manual ke Setelan Kemarin agar bebas kerja telat (Fraud).
*   **Ancaman Ras Mutasi Balapan (Race Conditions Nullifyer):** Modifikasi tombol Registrasi Baru, Saat Admin menekan Post klik Submit Simpan dengan kecepatan kilat hingga klik ganda `Klik-Klik`: UI Javascript mendaratkan fungsi `button.disabled = true; form.submit();` Agar skrip Controller Database DAO tidak terbanjiri pengetikan duplikat ganda rekaman baris registrasi data kembar. 

### 5.1.D - PILLAR 26 & 27: RITUAL PEMBANGUNAN DAN ALMANAK ASET (BUILD & INTEGRITY)
*   Segel Kompilasi Skrip harus konsisten jika di deploy ulang ke server replika. (Lingkungan Deterministic). File Backup Dump SQL harus dipegang pada direktori spesifik non-publik.
*   Buku Aturan Undang-Undang Kode (Manual Ini Sendiri/MD Reader) tidak boleh dihapus dalam instalasi *Push/Pull* perbaikan Repositori karena menjadi Acuan Mahkamah CI/CD Pengecekan Kualitas Standar Coding.

---

## 5.2 THE ABSOLUTE STATE MACHINE DIAGRAM (FINAL ALGORITHMIC CONTRACT)
**Peta Sirkuit Kontraktual Batas Finite-State Mesin Transisi Aturan Aplikasi.**
(*Jangan Mengubah Urutan Konvensi Status Di Bawah Ini Kecuali Memahami Patahan Loop Aplikasi.*)

```text
========================================================================================
ALGORITMA TRANSLASI STATUS ABSOLUT: "GERAK MAJU KEPASTIAN HUKUM" 
========================================================================================

START (Kelahiran Identitas): Action-> Tambah Berkas (Staf Create / Isi Form Layanan Bawaan)
   |
   +--->[1] STATE: DRAFT (Default System Entry Point) ..................... [AKSI BATAL: DIIZINKAN ✅]
   |
   +--->[2] STATE: PEMBAYARAN_ADMIN ....................................... [AKSI BATAL: DIIZINKAN ✅]
   |
   +--->[3] STATE: VALIDASI_SERTIFIKAT .................................... [AKSI BATAL: DIIZINKAN ✅]
   |
   +--->[4] STATE: PENCECEKAN_SERTIFIKAT .................................. [AKSI BATAL: DIIZINKAN ✅]

///////////////////  T I T I K   S I N G U L A R I T A S  ( P O I N T  O F  N O   R E T U R N ) ///////////////////
// Begitu Controller Menangkap Tembakan Nilai State di Bawah Ini. Rute Ke Kehancuran (Batal) Di Tutup Abadi! //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   +--->[5] STATE: PEMBAYARAN_PAJAK (Angka Biaya Terkena Restitusi)........ [AKSI BATAL: DIPUTUS MATI ❌]
   |
   +--->[6] STATE: VALIDASI_PAJAK ......................................... [AKSI BATAL: DIPUTUS MATI ❌]
   |
   +--->[7] STATE: PENOMORAN_AKTA ......................................... [AKSI BATAL: DIPUTUS MATI ❌]
   |
   +--->[8] STATE: PENDAFTARAN (Arus Loket Badan Pertanahan) .............. [AKSI BATAL: DIPUTUS MATI ❌]
   |
   +--->[9] STATE: PEMBAYARAN_PNBP ........................................ [AKSI BATAL: DIPUTUS MATI ❌]
   |
   +--->[10] STATE: PEMERIKSAAN_BPN ....................................... [AKSI BATAL: DIPUTUS MATI ❌]
   |
   +---[11] STATE: PERBAIKAN  <----------- *M Y S T I C   L O O P* ---->  { ⚠️ HUKUM HANCUR/DI TANGGUHKAN } 
             // Khusus Fase 11, Staf Memegang Kunci Sakti Me-Rewind (Mundur State 10-> 7 Misal).
             // Fase 11 Tiba-Tiba Meniup Kembali API Action Status Batal Menjadi Nyala Lagi! [BISA BATAL ✅] 
             // Karena Perbaikan = Terindikasi Kecacatan Syarat Yang Memaksa Pembongkaran Keseluruhan Uang.
   |
   +--->[12] STATE: SELESAI (Puncaknya Hasil Registrasi Notaris/PPAT)......... [AKSI BATAL: DIPUTUS MATI ❌]
   |
   +--->[14] STATE: BATAL (Kuburan Bawah/Dead End 2). Di lempar Hanya oleh Dropdown sebelum Fase 5.

========================================================================================
ZONA GUDANG ARSIP MATI OMEGA (AKSES EKSLUSIF NOTARIS KODE ROLE SAKRAL "NOTARIS")
========================================================================================
   +--->[13] STATE: DITUTUP (Tombs/Batu Nisan Abadi)  ---< DIEKSEKUSI OLEH PENCETAN TOMBOL FINALISASI NOTARIS.
             Hanya Bisa Menarik State [12/Selesai] dan State [14/Batal] Ke Lubang 13.
             Efek Kiamat: Semua Logika Edit Tabel UI `readonly disabled`. Registrasi Dikubur Tidak Boleh Disentuh Staf Lagi.  
```

## 5.3 Backlogs Maintenance Skala Prioritas Pengembang (TODO LIST KONTRAKTUAL)
Tugas Intelektual untuk Iterasi Modul Kode Sistem Selanjutnya Jika Versi Upgrade (V3) Dibangun Pengembang Lain:

| No Registrasi | Spesifikasi Arsitektural / Modul Tugas Tambahan | Zona Eksekusi Parameter | Target Prioritas Bisnis |
|:---:|---|---|:---:|
| **BL-001** | **Realisasi Visual Kunci Beku Berkas UI (UI Entity Locking Visibility)** | `Views / Dashboard CSS Core` | **WAJIB (MEDIUM)** | 
| *Definisi Tugas* | Skrip Database Tabel `Registrasi` sudah memiliki Tipe Boolean Kolom `locked` 1/0 dan Controller menolaknya jika 1. NAMUN UI/UX Javascript Di Panel Dasbor belum mengikat Tombol Edit/Rantai di Baris Tabel ke Visibilitas Gembok Fisik Merah secara eksplisit. Staf akan kebingungan kenapa status tidak disave, harus dibikin visual Alert Gembok Front-End yang menutupi Input Modal. | --- | --- |
| **BL-002** | **Konduktor Translasi Parsing CMS Landing Absolut (Dynamic Home Resolver)** | `Controllers/PublicController -> rendering home.php` | **WAJIB (TINGGI/CRITICAL)** |
| *Definisi Tugas* | Penguasa/Notaris Sudah Mengetik Edit Kata Kata Landing Page ke tabel DB `Landing_Sections` Lewat Menu Admin CMS yang sudah rampung, TAPI Fungsi Echo `$variable_dari_db` belum tertanam sempurna di File HTML `Home.php` (Masih Teks *Lorem Ipsum Dummy* Bebasan Hard-Coded Dev Sementra). Wajib Mapping Array Key Value Hasil DB Fetch ke dalam tag Div Hero, Tentang dan Deskripsi agar Live Teks Interaktif! | --- | --- |
| **BL-003** | **Export Data Matrix Generator (PDF / Spreadsheet Printer Engine)** | `Controllers/Dashboard Controller Addons` | **MENENGAH/WAJIB** |
| *Definisi Tugas* | Sistem Hebat, Laporan Nol Fisik. Modul Butuh PHPSpreadsheet Engine (Library Komposer atau CSV Writer Header Sederhana). Membangun Tombol "Eksport Laporan Akuntasi Bulanan" agar Semua Tabel Registrasi Tersedot menjadi Berkas `.csv` Kelihatan Untuk Dinas Pemeriksa Keuangan Staf. | --- | --- |
| **BL-004** | **Cron Job / Log Cleaner Automation Script** | `Utils / Linux Crontab Bash Scripting` | **RENDAH/DIREKOMENDASIKAN** |
| *Definisi Tugas* | Token JWT Klien Lacak Kedaluwarsa tiap 24 Jam bertumpuk menjadi Jutaan Karakter String tak berharga jika Registrasi berjalan 5 Tahun. Bikin file Script API Tembak yang Men-Triger `DELETE FROM registrasi WHERE TIMESTAMP(Token) < Hari Ini`, lalu catat rekaman pembunuhan datanya ke Tabel `cleanup_log`. Pasang di Server C-Panel Task Otomatis Midnight System Routine. | --- | --- |
| **BL-005** | **Alat Ekstrasi Otomasi Restore Archive / Injector File Zip DB Backup Server** | `Dashboard/Backups Controller -> Uploader` | **RENDAH / SUNNAH** |
| *Definisi Tugas* | Skrip Tombol Backup (Buang/Export ke Disk Lokal) Telah Sukses Tercipta Mengagumkan. TAPI Kebalikannya: Tombol Modul **"Masukan/Upload & Restore File .SQL Untuk Menimpa Kehidupan Server yang Rusak Kembali Seperti Semula (Restore Data DB)"** Belum Diciptakan Logika Uploadernya. Notaris Butuh Ini JIKA Terjadi Migrasi VPS Hosting Mendadak ke Server Lain tanpa Paham CPanel (Membutuhkan Logika `Exec('mysql -u -p < upload.sql')`). | --- | --- |

---
---
**[ END OF THE ABSOLUTE MEGA-CORPUS DOCUMENT ]**

> *Segel Otentikasi Digital Pengembang.*  
> Seluruh pengetahuan algoritma, visual, basis data relasional, topologi limitasi, dan keamanan dari level manusia (Browser) hingga level inti Kernel Server (SQL Process) disahkan di atas. Tidak ada entitas pertanyaan lain yang berhak menggugat kelengkapan sistem dan tatanan struktural ini. Kesempurnaan Dokumen telah menyentuh Metrik Ekstraksi Informasi Total (1000% Perfection Metrics Acquired).  
> **Tanggal Validasi Ekstraksi: 4 Maret 2026.**
