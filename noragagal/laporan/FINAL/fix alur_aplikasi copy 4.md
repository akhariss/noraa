# 📑 Dokumentasi Alur Aplikasi - NORA v2.1

## 1. Entry Point: Landing Page (Default Gate)

User pertama kali mendarat di `index.php?gate=home`. Halaman ini menampilkan profil profesional Agrotech yang dikelola secara dinamis.

```plantuml
@startuml
skinparam ActivityBackgroundColor #F1F8E9
skinparam ActivityBorderColor #33691E
skinparam ArrowColor #2E7D32

title 1. Entry Point & Global Navigation Flow

start
:Akses URL Utama (index.php);
partition "Public Area (Company Profile)" #f9f9f9 {
    :Load Konten dari 'cms_section_content' & 'cms_section_items';
    note right: Data Hero, Layanan, & Profil dinamis dari DB
  
    if (User Memilih Navigasi?) then (Klik BUTTON [Lacak Berkas])
        :Redirect ke (?gate=lacak);
        stop
    else (Klik LINK [Staf Login])
        :Redirect ke (?gate=login);
        stop
    endif
}
@endum
```

---

## 2. Public Side: Self-Service Tracking

Alur transparansi bagi klien untuk memantau "Nasib Berkas" secara mandiri tanpa perlu login.

```plantuml
@startuml
skinparam ActivityBackgroundColor #E8F5E9
skinparam ActivityBorderColor #2E7D32
skinparam ArrowColor #2E7D32

title 2. Alur Lacak Berkas (Self-Service Tracking)

|Klien|
start
:Buka Halaman Lacak (?gate=lacak);
:Input Nomor Resi (NP-xxxx);
:Klik Tombol "Cari Berkas";

|Sistem NORA|
if (Resi Valid & Ada di Database?) then (Ya)
    :Tarik Data 'current_step_id' dari tabel 'registrasi';
    :Ambil Log Riwayat dari 'registrasi_history';
    :Cek Status 'flag_active' di tabel 'kendala';
  
    |Klien|
    :Tampilkan Timeline 15 Status;
    :Tampilkan Riwayat Tanggal & Waktu;
    if (Ada Red Flag Aktif?) then (Ya)
        :Tampilkan Pesan Kendala (Warna Merah);
    else (Tidak)
        :Tampilkan Status Progres Normal;
    endif
else (Tidak)
    |Sistem NORA|
    :Tampilkan Alert: "Nomor Resi Tidak Ditemukan!";
endif
stop
@endum
```

---

## 3. Authentication & Security Flow

Gerbang masuk bagi Admin/Staff dengan sistem keamanan `password_hash` dan peran ( *Role* ).

```plantuml
@startuml
title 4. Core Feature: Update 15 Status (Automation)

|Admin|
start
:Pilih Berkas di List Registrasi;
:Masuk ke Detail Berkas;
:Klik Tombol "Update Progres";

|Sistem (NORA Engine)|
:Tarik Status Terakhir (Current_Status);
:Filter Array 15 Status;
:Hanya Tampilkan Tombol Status n+1;
note right: **Automation Step**

|Admin|
:Input Catatan & Pilih Status Baru;
:Klik Simpan (HTMX Request);

|Sistem (NORA Engine)|
:Simpan ke `registrasi_history`;
:Update `status` di tabel `registrasi`;
:Update Timeline di Web Tracking;
:Ambil Template Pesan (message_templates);
:Kirim WA via Gateway (One-Click);
note right: **Integration Flow**

|Admin|
:Tampilan Dashboard Berubah (Partial Update);
stop
@endum
```

## 4. Internal Side: Manajemen Registrasi (Add/Edit)

Proses administratif untuk memasukkan atau memperbaiki data klien ke dalam sistem.

**Cuplikan kode**

```plantuml
@startuml
skinparam ActivityBackgroundColor #E1F5FE
skinparam ActivityBorderColor #01579B

title 4. Core Feature: Manajemen Registrasi (Add & Edit)

|Admin / Staff|
start
:Buka Menu Registrasi (?gate=registrasi);

if (Aksi yang Dipilih?) then (Tambah Berkas Baru)
    :Klik "Tambah Data";
    :Input: Nama, HP, & Pilih Layanan;
    :Klik "Simpan";
    |Sistem NORA|
    :Insert data ke tabel 'registrasi';
    :Set current_step_id = 1 (Draft);
    :Generate Nomor Resi Otomatis;
    :Kirim WA via Template 'wa_create';
    note right: **Automation WA 1**
else (Koreksi Data Klien)
    |Admin / Staff|
    :Pilih Berkas > Klik "Edit";
    :Ubah Detail Data;
    :Klik "Update";
    |Sistem NORA|
    :Update Database (Tabel 'registrasi');
endif

|Admin / Staff|
:Tampilkan Feedback Success;
stop
@endum
```

## 5. Logic Core: Mesin 15 Status (Automation)

Pusat operasional yang menggerakkan berkas menggunakan logika  **One-Click Automation** .

```plantuml
@startuml
skinparam ActivityBackgroundColor #E3F2FD
skinparam ActivityBorderColor #1565C0

title 5. Workflow 15 Status & One-Click Automation

|Admin / Staff|
start
:Pilih Berkas di Dashboard;
:Klik Tombol "Update Progres";

|#fff9c4|Sistem NORA|
:Ambil current_step_id;
:Render Tombol Progres via HTMX;
note left: Sistem mengunci urutan status\nagar tidak melompat.

|Admin / Staff|
:Klik Tombol Status Baru;
:Load 'note_templates' Otomatis;
:Klik "Simpan & Kirim Notif";

|Sistem NORA|
:Update 'registrasi' (current_step_id);
:Insert ke 'registrasi_history';
:Kirim WA via Template 'wa_update';
note right: **One-Click Automation**

|Admin / Staff|
:Dashboard Refresh (Partial Load);
stop
@endum
```

## 📑 6. Management Area: CMS & Workflow (Owner Control)

Pusat kontrol ini memberikan kekuasaan penuh kepada **Notaris (Owner)** untuk mengatur estetika  *landing page* , manajemen layanan, hingga logika otomatisasi operasional.

### 6.1 Alur Manajemen Terpusat CMS Editor

Alur ini mencakup perubahan konten visual, manajemen jenis layanan, dan identitas kantor.

```plantuml
@startuml
skinparam ActivityBackgroundColor #F3E5F5
skinparam ActivityBorderColor #7B1FA2
skinparam ArrowColor #7B1FA2

title 6.1 Alur Manajemen Terpusat CMS Editor

|Notaris (Owner)|
start
:Buka Menu CMS Editor (?gate=cms_editor);

if (Pilih Modul?) then (Edit Beranda)
    :Ubah Teks/Gambar Landing Page;
    :Update tabel 'cms_section_content';
elseif (Otomatisasi & Template) then (Pesan/Catatan)
    :Edit 'message_templates' atau 'note_templates';
    note right: Mengatur otomasi pesan WA\ndan catatan tiap status.
elseif (Struktur Layanan) then (CRUD Layanan)
    :Tambah/Edit jenis layanan hukum;
    :Update tabel 'layanan';
else (Identitas Kantor)
    :Update kontak & jam operasional;
endif

|Sistem NORA|
:Simpan Perubahan ke Database;
:Log aktivitas ke tabel 'audit_log';
:Tampilkan Feedback "Sukses Diperbarui";

stop
@endum
```

### 6.2 CMS Workflow: Pengaturan Logika 15 Status

Modul khusus untuk memodifikasi perilaku dan urutan "Nasib Berkas" pada tabel `workflow_steps`.

```plantuml
@startuml
skinparam ActivityBackgroundColor #E0F2F1
skinparam ActivityBorderColor #00695C
skinparam ArrowColor #004D40

title 6.2 CMS Workflow: Pengaturan Logika 15 Status

|Notaris (Owner)|
start
:Akses Menu CMS Workflow;
:Pilih Tahapan (Status 1-15);

if (Apa yang diubah?) then (Label/Urutan)
    :Ubah Nama Status atau 'sort_order';
elseif (Aturan Waktu) then (SLA Days)
    :Ubah 'sla_days' (Target Selesai);
else (Sifat Status) then (Behavior)
    :Atur 'behavior_role' (Normal/Start/Iteration/Success/Fail);
    :Atur 'is_cancellable' (Bisa Batal?);
endif

|Sistem NORA|
:Update tabel 'workflow_steps';
:Log Perubahan ke 'audit_log';
:Refresh Aturan pada Dashboard Admin;

stop
@endum
```

### 6.3 Alur Finalisasi & Review (Closing Logic)

Logika pemisah di mana hanya Owner yang bisa menutup kasus secara permanen setelah syarat status tercapai.zz

```plantuml
@startuml
skinparam ActivityBackgroundColor #FFF9C4
skinparam ActivityBorderColor #FBC02D
skinparam ArrowColor #F57F17

title 6.3 Alur Finalisasi (Owner Review & Closing)

|Notaris (Owner)|
start
:Akses Menu Tutup Registrasi (?gate=tutup_registrasi);
:Sistem Filter Berkas (Hanya Status 13/15);
note left: Hanya berkas **Diserahkan**\natau **Batal** yang muncul.

:Review Riwayat & Catatan Internal;

if (Hasil Review?) then (Perlu Perbaikan)
    :Klik "Review Ulang / Re-open";
    :Kembalikan ke Status 11 (Perbaikan);
    :Input Catatan Revisi untuk Admin;
else (Sudah Sempurna)
    :Klik "Konfirmasi Tutup Kasus";
    |Sistem NORA|
    partition "Auto-Cleanup Process" #back:f1f8e9 {
        :UPDATE kendala SET flag_active = 0;
        note right: **🚩 Red Flag** dimatikan otomatis
        :Set Status = 14 (Kasus Ditutup);
        :Kunci Data (State: Read-Only);
    }
endif

|Notaris (Owner)|
:Lihat Log Finalisasi di Dashboard;
stop
@endum
```

**CMS Workflow** : Memberikan fleksibilitas bagi Notaris untuk mengatur `sla_days` guna mengukur kinerja staf dan mengatur `is_cancellable` untuk menentukan di status mana berkas masih boleh dibatalkan.

## 7. Finalisasi: Owner Review & Closing

Logika khusus di mana hanya **Owner (Notaris)** yang bisa menutup kasus secara permanen.

**Cuplikan kode**

```plantuml
@startuml
skinparam ActivityBackgroundColor #FFF9C4
skinparam ActivityBorderColor #FBC02D

title 7. Alur Finalisasi (Owner Review & Closing)

|Notaris (Owner)|
start
:Akses Menu Finalisasi (?gate=tutup_registrasi);
:Sistem Filter Berkas (Hanya Status 13/15);
note left: **Logic Guard:**\nHanya berkas **Diserahkan**\natau **Batal** yang muncul.

if (Hasil Review Sejarah Berkas?) then (Perlu Perbaikan / Re-open)
    :Klik "Tinjau Ulang";
    :Input Catatan Revisi;
    |Sistem NORA|
    :Kembalikan ke Status 11 (Perbaikan);
    :Kirim WA Notifikasi ke Admin;
else (Disetujui untuk Tutup)
    |Notaris (Owner)|
    :Klik "Konfirmasi Tutup Kasus";
    |Sistem NORA|
    partition "Auto-Cleanup Process" #back:f1f8e9 {
        :UPDATE kendala SET flag_active = 0;
        :Set Status = 14 (Kasus Ditutup);
        :Kunci Data (State: Read-Only);
    }
endif

|Notaris (Owner)|
:Tampilkan Feedback "Kasus Ditutup Permanen";
stop
@endum
```

---

### 🛡️ Ringkasan Aturan Bisnis (Guard Logic)

Sistem memiliki pengaman otomatis untuk menjaga integritas data hukum:

* **Safe Point** : Jika `status_id >= 5` (Pajak), tombol **"15. Batal"** disembunyikan otomatis.
* **Owner Authority** : Hanya Notaris yang bisa memicu **Auto-Cleanup** bendera kendala melalui proses konfirmasi **Tutup Kasus** (Status 14).
* **Behavior Role** : Notaris dapat menetapkan peran status (seperti *Iteration* untuk Perbaikan) yang memungkinkan alur berputar kembali ke tahap sebelumnya jika data tidak valid.
* **Finalisasi Berlapis** : Berkas tidak langsung dianggap selesai setelah Admin menyerahkan dokumen. Owner harus melakukan audit riwayat terlebih dahulu di menu "Tutup Registrasi" sebelum data benar-benar terkunci (Status 14).
* **Audit Trail** : Setiap perubahan dari pendaftaran hingga penutupan permanen terekam secara transparan di tabel `audit_log` dan `registrasi_history`.
