Mohon maaf atas ketidaknyamanannya. Sepertinya terdapat kendala teknis pada rendering diagram sebelumnya. Berikut adalah **Master App Flow (Alur Aplikasi) NORA v2.1** yang disusun ulang secara sangat detail, teknis, dan bersih menggunakan  **PlantUML** , sesuai dengan struktur database `norasblmupdate2.sql` dan logika bisnis yang kita diskusikan.

---

# 📑 Master App Flow - Sistem NORA v2.1

## 1. Titik Masuk Utama (Entry Point & Home)

User pertama kali mengakses `index.php?gate=home`. Halaman ini adalah **Company Profile Agrotech** yang dikelola melalui database.

**Cuplikan kode**

```plantuml
@startuml
skinparam ActivityBackgroundColor #F1F8E9
skinparam ActivityBorderColor #33691E
skinparam ArrowColor #2E7D32

title 1. Entry Point & Global Navigation Flow

start
:Akses URL Utama (index.php);
partition "Public Area (Company Profile)" #f9f9f9 {
    :Sistem Load Konten dari 'cms_section_content';
    note right: Teks & Gambar Agrotech dinamis dari DB
  
    if (Aksi User di Halaman Home?) then (Klik BUTTON [Lacak Berkas])
        :Redirect ke (?gate=lacak);
        detach
    else (Klik LINK [Login Staf])
        :Redirect ke (?gate=login);
        detach
    endif
}
@endum
```

---

## 2. Alur Lacak Berkas (Public Side)

Klien melakukan pemantauan mandiri tanpa login untuk melihat "Nasib Berkas" secara  *real-time* .

**Cuplikan kode**

```plantuml
@startuml
skinparam ActivityBackgroundColor #F1F8E9
skinparam ActivityBorderColor #33691E
skinparam ArrowColor #2E7D32

title 2. Alur Lacak Berkas (Public Side)

|Klien|
start
:Buka Halaman Lacak (?gate=lacak);
:Input Nomor Resi (NP-xxxx);
:Klik Tombol "Cari";

|Sistem NORA|
if (Resi Ditemukan di Database?) then (Ya)
    :Tarik Progres dari 'workflow_steps';
    :Ambil Riwayat dari 'registrasi_history';
    :Cek Status 'flag_active' di tabel 'kendala';
  
    |Klien|
    :Tampilkan Timeline 15 Status;
    :Tampilkan Riwayat Perubahan;
    if (Ada Red Flag Aktif?) then (Ya)
        :Tampilkan Pesan Kendala (Warna Merah);
    else (Tidak)
        :Tampilkan Status Normal;
    endif
else (Tidak)
    |Sistem NORA|
    :Tampilkan Error: "Resi Tidak Ditemukan";
endif
stop
@endum
```

---

## 3. Alur Login & Keamanan (Gatekeeper)

Proses autentikasi menggunakan `password_hash` dan pembagian peran ( *RBAC* ).

**Cuplikan kode**

```plantuml
@startuml
title 3. Authentication & Security Gate

|Admin / Staff|
start
:Buka Halaman Login (?gate=login);
repeat
    :Input Username & Password;
    :Sistem Query tabel 'users';
    :Validasi password_verify();
    if (Login Berhasil?) then (Ya)
        :Start Secure Session;
        :Set Role ('admin' / 'notaris');
        :Log Login ke 'audit_log';
        :Redirect ke Dashboard (?gate=dashboard);
        stop
    else (Tidak)
        :Tampilkan Alert "Login Gagal";
    endif
repeat while (Coba Lagi?) is (Ya)
@endum
```

---

## 4. Alur Manajemen Data: Registrasi (Add & Edit)

Admin memasukkan atau memperbaiki data klien sebelum menjalankan proses hukum.

**Cuplikan kode**

```plantuml
@startuml
title 4. Core Feature: Manajemen Registrasi (Add & Edit)

|Admin / Staff|
start
:Masuk Menu Registrasi (?gate=registrasi);

if (Aksi Admin?) then (Tambah Berkas Baru)
    :Klik "Tambah Data";
    :Input Nama Klien, HP, & Layanan;
    :Klik "Simpan";
    |#e3f2fd|Sistem NORA|
    :Insert data ke tabel 'klien' & 'registrasi';
    :Set 'current_step_id' = 1;
    :Generate Nomor Resi Otomatis;
    :Kirim WA via Template 'wa_create';
    note right: **Automation WA 1**
else (Edit Data Eksisting)
    |Admin / Staff|
    :Pilih Baris Data > Klik "Edit";
    :Ubah Detail Informasi;
    :Klik "Update Data";
    |Sistem NORA|
    :Update Row Database (WHERE id=x);
endif

|Admin / Staff|
:Lihat Notifikasi Sukses (HTMX Toast);
stop
@endum
```

---

## 5. Alur Mesin 15 Status & Otomasi WhatsApp

Proses inti menggerakkan berkas menggunakan logika  *Step-by-step* .

**Cuplikan kode**

```plantuml
@startuml
skinparam ActivityBackgroundColor #E3F2FD
skinparam ActivityBorderColor #1565C0

title 5. Workflow 15 Status & One-Click Automation

|Admin / Staff|
start
:Buka Detail Berkas di Dashboard;
:Klik Tombol "Update Progres";

|#fff9c4|Sistem NORA|
:Cek Status Saat Ini (n);
:Render Tombol Status (n+1) via HTMX;
note left: **Automation Step:**\nUrutan dikunci sesuai 'sort_order'

|Admin / Staff|
:Klik Tombol Status Baru;
:Isi Catatan Progres;
:Klik "Simpan & Kirim Notif";

|Sistem NORA|
:Update 'current_step_id' di tabel 'registrasi';
:Insert History ke 'registrasi_history';
:Ambil Template dari 'message_templates';
:Kirim Notifikasi via WhatsApp API;
note right: **One-Click Automation**

|Admin / Staff|
:Tampilan Dashboard Ter-update (Partial Load);
stop
@endum
```

---


## 📑 6. Alur CMS & Finalisasi (Notaris Area)

Bagaimana Notaris mengelola konten profesional Agrotech dan menutup riwayat berkas secara permanen di database `norasblmupdate2`.

### 6.1 CMS Editor Flow (Agrotech Profile)

Alur bagi Notaris untuk mengubah tampilan Landing Page tanpa menyentuh kode.

**Cuplikan kode**

```plantuml
@startuml
skinparam ActivityBackgroundColor #FFF9C4
skinparam ActivityBorderColor #FBC02D
skinparam ArrowColor #F57F17

title 6.1 Alur CMS Editor (Company Profile)

|Notaris (Owner)|
start
:Akses Menu CMS (?gate=cms);
:Pilih Halaman 'home' & Section;
note right
  Pilihan Section:
  Hero, Layanan, atau Testimoni
end note

|Sistem NORA|
:Load Data dari 'cms_section_content';
:Load Item dari 'cms_section_items';

|Notaris (Owner)|
:Ubah Teks atau Unggah Gambar Agrotech;
:Klik "Publish Perubahan";

|Sistem NORA|
:Update Tabel 'cms_section_content';
:Update 'extra_data' pada 'cms_section_items';
:Catat Aksi ke tabel 'audit_log';

|Notaris (Owner)|
:Verifikasi Visual di Halaman Depan;
stop
@endum
```

### 6.2 Finalisasi & Auto-Cleanup Flow

Proses otomatisasi saat berkas telah diserahkan kepada klien agar database tetap bersih.

**Cuplikan kode**

```plantuml
@startuml
skinparam ActivityBackgroundColor #E1F5FE
skinparam ActivityBorderColor #01579B

title 6.2 Alur Finalisasi & Auto-Cleanup

|Admin / Staff|
start
:Pilih Berkas di Dashboard;
:Update Status ke **13 (Diserahkan)**;

|Sistem NORA|
:Update 'diserahkan_at' di tabel 'registrasi';
:Insert history 'Diserahkan' ke 'registrasi_history';

partition "Auto-Cleanup Engine" #back:f1f8e9 {
    :Jalankan UPDATE kendala SET flag_active = 0;
    note right: Mematikan semua **🚩 Red Flag** otomatis
    :Set Status ke **14 (Kasus Ditutup)**;
}

:Kunci Data (State: Read-Only);
:Catat Aksi 'finalize' ke 'audit_log';

|Admin / Staff|
:Lihat Status Final di Dashboard;
stop
@endum
```

---

## 📑 7. Aturan Bisnis & Logika Pengaman (Safe Point)

Sistem NORA v2.1 memiliki logika *Guard* otomatis untuk menjaga integritas proses hukum di kantor Notaris.

**Cuplikan kode**

```plantuml
@startuml
skinparam ActivityBackgroundColor #FFF9C4
skinparam ActivityBorderColor #FBC02D

title 7. Logika Pengaman & Aturan Bisnis (Safe Point)

start
:Admin Pilih Berkas di Dashboard;

partition "Logic Guard: Safe Point" #back:ffcccc {
    if (Cek status_id >= 5 (Pembayaran Pajak)?) then (Ya)
        :Sembunyikan Tombol **"15. Batal"**;
        note left: Mencegah kerugian finansial\nkarena dana sudah di kas negara
    else (Tidak)
        :Tampilkan Opsi Pembatalan;
    endif
}

partition "Logic Guard: Red Flag System" #back:ffebee {
    if (Ada Kendala di Lapangan?) then (Ya)
        :Admin Aktifkan 'flag_active' = 1;
        :Dashboard & Halaman Lacak Berubah **MERAH**;
    else (Tidak)
        :Tampilan Status Normal (Hijau);
    endif
}

partition "Logic Guard: Audit Trail" #back:e3f2fd {
    :Setiap Perubahan Data;
    :Simpan 'old_value' & 'new_value' ke 'audit_log';
}

stop
@endum
```

---

### Penjelasan Detail Teknis:

* **Penyelesaian Sintaks Warna** : Saya telah memperbaiki sintaks warna menggunakan format terbaru (seperti `#back:f1f8e9`) untuk menghindari eror *deprecated* yang kamu alami sebelumnya.
* **CMS Management** : Notaris dapat mengelola 8 section utama (Hero, Layanan, Alur, dsb) melalui tabel `cms_page_sections` dan `cms_section_content` secara dinamis.
* **Safe Point (Status 5)** : Berdasarkan aturan bisnis, setelah memasuki tahap  **5. Pembayaran Pajak** , sistem mengunci fitur pembatalan untuk melindungi transaksi yang sudah melibatkan setoran negara.
* **Auto-Cleanup** : Fungsi ini sangat krusial di sistem kamu untuk memastikan saat berkas sudah di tahap  **14 (Kasus Ditutup)** , tidak ada lagi bendera kendala (Red Flag) yang menggantung di dashboard.
* **Audit Trail** : Setiap langkah dari status 1 sampai 14 terekam permanen di tabel `audit_log` dan `registrasi_history` untuk keperluan transparansi hukum kantor Notaris.
