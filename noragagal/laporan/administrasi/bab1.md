# BAB 1 - PENDAHULUAN

## 1.1 Latar Belakang

### 1.1.1 Konteks Bisnis Notaris

Kantor Notaris merupakan lembaga hukum yang memiliki peran strategis dalam pelayanan pembuatan akta, pengurusan sertifikat tanah, dan legalisasi dokumen hukum. Pada Kantor Notaris Sri Anah, S.H., M.Kn., proses layanan dokumen melibatkan tahapan kompleks yang memerlukan koordinasi antara staff administrasi, notaris, dan instansi eksternal seperti BPN (Badan Pertanahan Nasional) serta kantor pajak.

Setiap dokumen yang diproses memiliki lifecycle panjang dengan multiple status yang harus dilalui, mulai dari pengumpulan persyaratan, pembayaran administrasi, validasi sertifikat, pembayaran pajak, penomoran akta, pendaftaran ke BPN, hingga penyerahan dokumen kepada klien.

### 1.1.2 Permasalahan yang Dihadapi

Berdasarkan analisis proses bisnis yang berjalan (AS-IS), ditemukan beberapa permasalahan fundamental:

**1. Transparansi Layanan yang Rendah**

Sistem pencatatan manual menggunakan buku register menyebabkan informasi status dokumen hanya tersedia secara terbatas. Klien tidak memiliki akses langsung untuk memantau progress dokumen mereka, sehingga bergantung sepenuhnya pada ketersediaan staff untuk mendapatkan informasi.

**2. Klien Tidak Mengetahui Status Dokumen Secara Real-time**

Proses inquiry status memerlukan klien untuk menghubungi kantor notaris melalui telepon atau pesan elektronik. Staff kemudian harus mencari catatan manual di buku register untuk memberikan informasi status. Proses ini tidak hanya memakan waktu tetapi juga berpotensi memberikan informasi yang tidak update.

**3. Inefisiensi Proses Komunikasi**

Pertanyaan berulang dari klien mengenai status dokumen membebani staff administrasi dengan tugas-tugas operasional yang sebenarnya dapat diotomatisasi. Waktu yang seharusnya digunakan untuk memproses dokumen justru habis untuk melayani inquiry status.

**4. Tidak Ada Standardisasi Workflow**

Pencatatan manual menyebabkan tidak adanya standardisasi dalam tracking tahapan proses. Setiap staff dapat memiliki cara pencatatan yang berbeda, menyulitkan monitoring dan evaluasi performa layanan.

### 1.1.3 Kebutuhan Solusi Teknologi

Berdasarkan permasalahan tersebut, diperlukan sebuah sistem informasi berbasis web yang dapat:

1. Menyediakan portal tracking status dokumen yang dapat diakses klien secara mandiri 24/7
2. Mengimplementasikan workflow terstruktur dengan status yang terdefinisi dengan jelas
3. Mencatat seluruh riwayat perubahan status secara otomatis untuk audit trail
4. Menyediakan dashboard internal untuk monitoring dan manajemen proses oleh staff dan notaris

### 1.1.4 Justifikasi Implementasi Sistem

Implementasi Sistem Informasi Tracking Status Dokumen berbasis web merupakan solusi strategis untuk:

- **Meningkatkan transparansi** - Klien dapat mengakses informasi status kapan saja tanpa harus menghubungi kantor
- **Meningkatkan efisiensi** - Staff dapat fokus pada pemrosesan dokumen daripada melayani inquiry berulang
- **Standardisasi proses** - Workflow dengan 14 status terdefinisi memastikan konsistensi proses
- **Audit trail lengkap** - Semua perubahan tercatat dalam sistem untuk keperluan monitoring dan evaluasi

---

## 1.2 Rumusan Masalah

Berdasarkan latar belakang yang telah diuraikan, rumusan masalah dalam pengembangan sistem ini adalah:

### 1.2.1 Masalah Utama

**"Bagaimana merancang dan mengimplementasikan sistem informasi tracking status dokumen berbasis web untuk meningkatkan transparansi layanan notaris dan efisiensi proses dokumen pada Kantor Notaris Sri Anah, S.H., M.Kn.?"**

### 1.2.2 Pertanyaan Penelitian

1. Bagaimana proses bisnis yang berjalan saat ini dalam penanganan dokumen di Kantor Notaris Sri Anah, S.H., M.Kn.?

2. Fitur tracking seperti apa yang diperlukan untuk memberikan transparansi status dokumen kepada klien?

3. Bagaimana merancang workflow internal yang memastikan standardisasi proses dari staff hingga approval notaris?

4. Bagaimana mengimplementasikan sistem yang aman untuk melindungi data dokumen hukum yang bersifat sensitif?

5. Bagaimana mengukur peningkatan transparansi dan efisiensi setelah implementasi sistem?

---

## 1.3 Batasan Masalah

Untuk memastikan fokus pengembangan dan kejelasan scope, batasan masalah dalam penelitian ini adalah:

### 1.3.1 Batasan Fungsional

**Sistem Mencakup:**

1. **Tracking Status Dokumen untuk Klien**
   - Pencarian dokumen berdasarkan nomor registrasi
   - Verifikasi menggunakan 4 digit terakhir nomor telepon
   - Tampilan progress tracking dengan 14 status
   - Riwayat perubahan status (business history)

2. **Workflow Internal**
   - Manajemen registrasi dokumen (CRUD)
   - Update status dengan validasi transisi
   - Approval workflow oleh notaris
   - Flag kendala untuk monitoring hambatan
   - Lock mechanism untuk dokumen sensitif

3. **Content Management System (CMS)**
   - Manajemen konten homepage company profile
   - Manajemen informasi layanan
   - Template pesan WhatsApp untuk notifikasi
   - Template catatan internal

4. **Manajemen Pengguna**
   - Role-Based Access Control (RBAC)
   - User management (notaris only)
   - Audit log aktivitas sistem

**Sistem Tidak Mencakup:**

1. **Tanda Tangan Digital** - Sistem tidak menyediakan fitur e-signature untuk dokumen hukum
2. **Payment Gateway** - Pembayaran biaya layanan tetap dilakukan secara manual/offline
3. **Integrasi BPN** - Tidak ada integrasi langsung dengan sistem BPN
4. **Mobile Application** - Sistem berbasis web responsive, bukan aplikasi mobile native
5. **Notifikasi Otomatis** - Notifikasi WhatsApp dilakukan manual menggunakan template, tidak ada auto-send

### 1.3.2 Batasan Teknis

1. **Platform**: Web-based application dengan akses melalui browser
2. **Backend**: PHP native dengan arsitektur MVC (Model-View-Controller)
3. **Database**: MySQL/MariaDB
4. **Frontend**: HTML5, CSS3, JavaScript vanilla (tanpa framework)
5. **Server**: Apache dengan mod_rewrite untuk URL handling
6. **Session Management**: PHP session dengan security enhancement (fingerprinting, CSRF protection)

### 1.3.3 Batasan Pengguna

| Role | Deskripsi | Akses |
|------|-----------|-------|
| **Notaris** | Pemilik/pimpinan kantor notaris | Full access: dashboard, users, CMS, finalisasi, audit log |
| **Admin/Staff** | Staff administrasi | Dashboard: registrasi, update status, klien update |
| **Klien** | Pengguna layanan | Public tracking tanpa login |
| **Publik** | Pengunjung website | Homepage company profile |

### 1.3.4 Batasan Domain

Sistem ini dikembangkan khusus untuk domain **Kantor Notaris Sri Anah, S.H., M.Kn.** dengan business rules spesifik:

- 14 status workflow yang mencerminkan proses notaris sebenarnya
- Batasan pembatalan dokumen setelah tahap pembayaran pajak
- Validasi transisi status untuk mencegah kemunduran yang tidak logis
- Terminologi dan proses yang sesuai dengan praktik notaris di Indonesia

---

## 1.4 Tujuan Penelitian

### 1.4.1 Tujuan Umum

Merancang dan mengimplementasikan Sistem Informasi Tracking Status Dokumen berbasis web untuk meningkatkan transparansi layanan notaris dan efisiensi proses dokumen pada Kantor Notaris Sri Anah, S.H., M.Kn.

### 1.4.2 Tujuan Khusus

1. **Menganalisis proses bisnis** yang berjalan untuk memahami workflow penanganan dokumen di kantor notaris

2. **Merancang sistem tracking** yang memungkinkan klien memantau status dokumen secara mandiri dan real-time

3. **Mengimplementasikan workflow engine** dengan 14 status terdefinisi dan validasi transisi yang ketat

4. **Mengembangkan CMS terintegrasi** untuk manajemen konten company profile dan template komunikasi

5. **Mengimplementasikan security measures** untuk melindungi data dokumen hukum yang sensitif

6. **Mengevaluasi sistem** berdasarkan fungsionalitas, keamanan, dan kesesuaian dengan kebutuhan bisnis

---

## 1.5 Manfaat Penelitian

### 1.5.1 Manfaat Teoritis

1. **Kontribusi Akademis**
   - Referensi implementasi sistem tracking dokumen untuk domain notaris
   - Studi kasus penerapan workflow management system di UMKM hukum
   - Dokumentasi business rules sistem notaris Indonesia

2. **Pengembangan Keilmuan**
   - Penerapan prinsip Domain-Driven Design dalam konteks bisnis hukum
   - Implementasi Role-Based Access Control untuk sistem multi-role
   - Pattern validasi workflow untuk proses bertahap

### 1.5.2 Manfaat Praktis

**Bagi Kantor Notaris Sri Anah, S.H., M.Kn.:**

1. **Peningkatan Transparansi**
   - Klien dapat mengakses status dokumen 24/7 tanpa harus menghubungi kantor
   - Progress tracking visual dengan estimasi waktu per tahap

2. **Efisiensi Operasional**
   - Pengurangan beban staff untuk inquiry status berulang
   - Standardisasi workflow mengurangi kesalahan proses

3. **Monitoring yang Lebih Baik**
   - Dashboard real-time untuk monitoring semua dokumen aktif
   - Audit trail lengkap untuk evaluasi performa

4. **Citra Profesional**
   - Sistem modern meningkatkan kepercayaan klien
   - Company profile online dengan CMS yang mudah dikelola

**Bagi Klien:**

1. **Kemudahan Akses Informasi**
   - Tracking mandiri kapan saja dan di mana saja
   - Transparansi progress proses dokumen

2. **Kepastian Status**
   - Informasi status yang akurat dan update
   - Riwayat perubahan status yang tercatat

**Bagi Staff dan Notaris:**

1. **Workflow Terstruktur**
   - Panduan jelas tahapan yang harus dilalui
   - Validasi otomatis mencegah kesalahan transisi

2. **Monitoring Efisien**
   - Dashboard overview semua dokumen aktif
   - Filter dan search untuk dokumen spesifik

---

## 1.6 Sistematika Penulisan

Dokumentasi sistem ini disusun dengan sistematika sebagai berikut:

**Bagian I - Laporan Administrasi**
- Bab 1: Pendahuluan (latar belakang, rumusan masalah, batasan, tujuan, manfaat)
- Gambaran Proses Bisnis (proses AS-IS dan TO-BE)

**Bagian II - Laporan Teknis**
- Use Case Diagram
- Activity Diagram
- Sequence Diagram
- Alur Aplikasi
- System Overview
- Folder Blueprint
- Architecture
- Request Lifecycle
- Database Schema
- Business Rules
- API Design
- Security Analysis
- Attacker vs Defense
- Authentication
- Performance
- Green Computing
- Atomic Design
- Module Interaction
- Deployment
- Security Checklist
- Glossary

**Bagian III - Analisis Kritis**
- System Evaluation
- Security Risk Assessment
- Critical Findings
- Improvement Roadmap

---

## 1.7 Definisi Operasional

| Istilah | Definisi |
|---------|----------|
| **Tracking Status** | Kemampuan klien untuk memantau progress dokumen melalui 14 tahapan status yang terdefinisi |
| **Workflow** | Alur proses dokumen dari status awal (draft) hingga status akhir (selesai/ditutup/batal) |
| **Transparansi Layanan** | Ketersediaan informasi status dokumen yang dapat diakses klien secara mandiri dan real-time |
| **Efisiensi Proses** | Pengurangan waktu dan effort yang diperlukan untuk proses inquiry dan update status |
| **Role-Based Access Control** | Mekanisme keamanan yang membatasi akses pengguna berdasarkan role (notaris, admin, publik) |
| **Audit Trail** | Catatan lengkap semua perubahan status dan aksi pengguna dalam sistem |

---

## 1.8 Kesimpulan Bab 1

Bab ini telah menguraikan latar belakang permasalahan transparansi dan efisiensi pada Kantor Notaris Sri Anah, S.H., M.Kn., serta merumuskan kebutuhan akan Sistem Informasi Tracking Status Dokumen berbasis web. 

Rumusan masalah difokuskan pada bagaimana merancang sistem yang dapat meningkatkan transparansi layanan notaris melalui tracking real-time dan workflow terstruktur. Batasan masalah mencakup aspek fungsional, teknis, dan domain notaris spesifik.

Tujuan penelitian diarahkan pada implementasi sistem yang memberikan manfaat praktis bagi kantor notaris (efisiensi, monitoring), klien (transparansi akses), dan staff (workflow terstruktur).

Bab-bab selanjutnya akan mendeskripsikan detail teknis implementasi sistem dan analisis kritis terhadap hasil pengembangan.
