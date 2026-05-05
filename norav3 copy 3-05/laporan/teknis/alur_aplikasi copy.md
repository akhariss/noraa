
Siap, ini adalah **Master App Flow (Alur Aplikasi)** paling detail, teknis, dan komprehensif untuk  **NORA v2.1** . Dokumen ini dirancang mengikuti struktur *Gate* Vanilla PHP kamu, memisahkan area publik (Agrotech Profile) dengan area privat (Dashboard), serta mengunci logika **15 Status** dan  **One-Click Automation** .

---

# 📑 Master App Flow - Sistem NORA v2.1

## 1. Entry Point: Landing Page (Default Gate)

User pertama kali mendarat di `index.php?gate=home`. Halaman ini menampilkan **Company Profile** dengan gaya Agrotech yang bersih.

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
    :Tampilkan Home (Agrotech Layout);
    note right: Section: Hero, Agrotech Services, About, Footer
  
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

## 2. Public Side: Self-Service Tracking (Via Button)

Alur ini menangani transparansi "Nasib Berkas" bagi klien tanpa perlu login.

```plantuml
@startuml
title 2. Alur Lacak Berkas (Klien Area)

|Klien|
start
:Halaman Lacak Berkas (?gate=lacak);
:Input Nomor Resi (Contoh: NP-202604-001);
:Klik Tombol "Cari";

|#e8f5e9|Sistem NORA|
if (Resi Valid & Ada di Database?) then (Ya)
    :Ambil Status Terkini (1 s/d 15);
    :Ambil Log Riwayat (Timestamp & Status);
    :Cek Status **🚩 Red Flag**;
  
    |Klien|
    :Tampilkan Progress Bar (Timeline 15 Tahap);
    :Tampilkan History Log (List Update);
    if (Red Flag Aktif?) then (Ya)
        #pink:Tampilkan Pesan Kendala (Warna Merah);
    else (Tidak)
        :Tampilkan Status Normal;
    endif
else (Tidak)
    |Sistem NORA|
    :Tampilkan Alert: "Nomor Resi Tidak Ditemukan!";
endif
stop
@endum
```

---

## 3. Authentication Gate (Via Link)

Alur masuk bagi Admin/Staff untuk mengelola area manajemen berkas.

**Cuplikan kode**

```plantuml
@startuml
title 3. Authentication & Security Flow

|Admin / Staff|
start
:Halaman Login (?gate=login);
repeat
    :Input Username & Password;
    :Proses Query User ke Database;
    :Validasi password_verify();
    if (Kredensial Valid?) then (Ya)
        :Start Secure Session;
        :Set Session Role (Admin/Notaris);
        :Redirect ke Dashboard (?gate=dashboard);
        stop
    else (Tidak)
        #ffcccc:Tampilkan Alert "Login Gagal!";
    endif
repeat while (User Mencoba Lagi?) is (Ya)
stop
@endum
```

---

## 4. Internal Side: Manajemen Registrasi (Add & Edit)

Alur saat Admin pertama kali memasukkan atau memperbaiki data berkas fisik ke sistem digital.

**Cuplikan kode**

```plantuml
@startuml
title 4. Core Feature: Manajemen Data (Add & Edit)

|Admin / Staff|
start
:Buka Menu Registrasi (?gate=registrasi);

if (Aksi yang Dipilih?) then (Input Berkas Baru)
    :Klik Tombol "Tambah Data";
    :Input: Nama Klien, No. HP, Pilih Layanan;
    :Klik "Simpan";
    |#e3f2fd|Sistem NORA|
    :Insert ke Tabel 'registrasi';
    :Set Status Awal = 1 (Persyaratan);
    :Generate Nomor Resi Otomatis;
    :Kirim WA Pendaftaran via API;
    note right: **Automation WA 1 (Pendaftaran)**
else (Koreksi Data Klien)
    |Admin / Staff|
    :Pilih Baris Berkas > Klik "Edit";
    :Ubah Detail (Nama/HP/Data Fisik);
    :Klik "Update";
    |Sistem NORA|
    :Update Row Database (WHERE id=x);
endif

|Admin / Staff|
:Tampilkan Feedback Success (HTMX Toast);
stop
@endum
```

---

## 5. Logic Core: Mesin 15 Status (Workflow Automation)

Inilah jantung sistem yang menggerakkan "Nasib Berkas" dengan logika **Automation Step** dan  **One-Click WA** .

**Cuplikan kode**

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
:Ambil Current_Status_ID (n);
:Render Tombol Status (n+1) via HTMX;
note left: **Automation Step:**\nSistem mengunci urutan status\nagar tidak melompat.

|Admin / Staff|
:Klik Tombol Status Baru;
:Input Catatan Progres;
:Klik "Simpan & Kirim Notif";

|Sistem NORA|
:Update Tabel 'registrasi' (status_id);
:Insert ke 'registrasi_history' (log audit);
:Ambil Template WA sesuai status_id;
:Kirim Notifikasi via WhatsApp Gateway;
note right: **One-Click Automation**

|Admin / Staff|
:Dashboard Refresh (Partial Load);
stop
@endum
```

---

## 6. Management Area: CMS & Finalisasi (Notaris Area)

Bagaimana Notaris mengelola konten agrotech dan menutup kasus secara permanen.

* **CMS Editor (`?gate=cms`):** Notaris mengubah teks, judul, dan gambar pada halaman Home (Landing Page) agar tetap relevan tanpa menyentuh kode PHP.
* **Finalisasi & Cleanup Flow:**
  1. Admin mengubah status ke  **13 (Diserahkan)** .
  2. Sistem memicu  **Auto-Cleanup** : Menghapus/menonaktifkan semua **Red Flag** secara otomatis.
  3. Status dikunci ke **14 (Kasus Ditutup)** **$\rightarrow$** Data menjadi  *Read-Only* .

---

## 7. Business Rules & Guard Logic (Decision Flow)

Aturan otomatis yang berjalan di balik layar untuk menjaga integritas data:

* **Safe Point Logic:**
  * Sistem mengecek variabel `status_id`.
  * Jika `status_id >= 5` (Sudah Bayar Pajak) **$\rightarrow$** Sistem menyembunyikan opsi/tombol  **"15. Batal"** .
* **Red Flag Logic:**
  * Jika Admin klik tombol kendala **$\rightarrow$** `is_kendala = 1`.
  * Halaman Lacak Klien otomatis berubah warna (Alert State).
* **Gate Security:**
  * Setiap file di folder `app/` diproteksi. Jika diakses tanpa session, otomatis mental ke `?gate=login`.

---

### Kesimpulan Alur Lengkap:

1. **Entry:** Mendarat di **Home** (Profile).
2. **Navigation:** Ke **Lacak** via Button, ke **Login** via Link.
3. **Process:** Login **$\rightarrow$** Dashboard **$\rightarrow$** **Add Berkas** **$\rightarrow$** **Update 15 Status** (Auto WA).
4. **Closing:** Serah terima **$\rightarrow$** **Auto-Cleanup Red Flag** **$\rightarrow$** Kasus Tutup.

Apakah alur super detail ini sudah mencakup seluruh skenario yang ingin kamu bangun di NORA v2.1?
