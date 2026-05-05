# Use Case Documentation - Sistem NORA v2.1

---

## UC-01: View Landing Page

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #F1F8E9
skinparam ActivityBorderColor #33691E
skinparam ArrowColor #2E7D32

title UC-01: Entry Point & Global Navigation Flow

start
:Akses URL Utama (index.php);

partition "Public Area (Company Profile)" {
    :Load Konten dari cms_section_content & cms_section_items;
    note right: Data Hero, Layanan, & Profil dinamis dari DB

    if (User Memilih Navigasi?) then (Klik BUTTON [Lacak Berkas])
        :Redirect ke (?gate=lacak);
        stop
    elseif (Klik LINK [Staf Login]) then (Klik LINK [Staf Login])
        :Redirect ke (?gate=login);
        stop
    else (Browse Konten)
        :User melihat company profile;
        stop
    endif
}

@enduml
```

---

## UC-02: Track Berkas (Self-Service)

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #E8F5E9
skinparam ActivityBorderColor #2E7D32
skinparam ArrowColor #2E7D32

title UC-02: Alur Lacak Berkas (Self-Service Tracking)

|Klien|
start
:Buka Halaman Lacak (?gate=lacak);
:Input Nomor Resi (NP-xxxx);
:Klik Tombol "Cari Berkas";

|Sistem NORA|
if (Resi Valid & Ada di Database?) then (Ya)
    :Tarik Data current_step_id dari tabel registrasi;
    :Ambil Log Riwayat dari registrasi_history;
    :Cek Status flag_active di tabel kendala;

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

@enduml
```

---

## UC-03: Login Staff/Notaris

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #E1F5FE
skinparam ActivityBorderColor #01579B
skinparam ArrowColor #01579B

title UC-03: Authentication & Security Flow

|User|
start
:Input Username;
:Input Password;
:Klik Tombol "Login";

|Sistem NORA|
if (Username Valid?) then (Ya)
    :Verifikasi password_hash;
    if (Password Valid?) then (Ya)
        :Buat Session dengan Role;
        if (Role == Admin) then (Ya)
            :Redirect ke Dashboard Registrasi;
        else (Notaris)
            :Redirect ke Dashboard Performance;
        endif
    else (Tidak)
        :Tampilkan Error "Password Salah";
        stop
    endif
else (Tidak)
    :Tampilkan Error "Username Tidak Ditemukan";
    stop
endif
stop

@enduml
```

---

## UC-04: Registrasi Berkas Baru

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #E1F5FE
skinparam ActivityBorderColor #01579B
skinparam ArrowColor #01579B

title UC-04: Manajemen Registrasi (Add Berkas Baru)

|Admin|
start
:Buka Menu Registrasi (?gate=registrasi);

if (Aksi yang Dipilih?) then (Tambah Berkas Baru)
    :Klik "Tambah Data";
    :Input: Nama, HP, & Pilih Layanan;
    :Klik "Simpan";
    |Sistem NORA|
    :Insert data ke tabel registrasi;
    :Set current_step_id = 1 (Draft);
    :Generate Nomor Resi Otomatis;
    :Kirim WA via Template wa_create;
    note right: Automation WA 1
else (Koreksi Data Klien)
    |Admin|
    :Pilih Berkas > Klik "Edit";
    :Ubah Detail Data;
    :Klik "Update";
    |Sistem NORA|
    :Update Database (Tabel registrasi);
endif

|Admin|
:Tampilkan Feedback Success;
stop

@enduml
```

---

## UC-05: Edit Data Registrasi

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #E1F5FE
skinparam ActivityBorderColor #01579B
skinparam ArrowColor #01579B

title UC-05: Manajemen Registrasi (Edit Berkas)

|Admin|
start
:Pilih Berkas;
:Klik "Edit";
:Ubah Detail Data;
:Klik "Update";

|Sistem NORA|
:Validasi Input;
:Update Database (Tabel registrasi);
:Log ke audit_log;

|Admin|
:Tampilkan Feedback Success;
stop

@enduml
```

---

## UC-06: Update Status Berkas (15 Status)

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #E3F2FD
skinparam ActivityBorderColor #1565C0
skinparam ArrowColor #1565C0

title UC-06: Workflow 15 Status & One-Click Automation

|Admin|
start
:Pilih Berkas di Dashboard;
:Klik Tombol "Update Progres";

|Sistem NORA|
:Ambil current_step_id;
:Render Tombol Progres via HTMX;
note left: Sistem mengunci urutan status\nagar tidak melompat.

|Admin|
:Klik Tombol Status Baru;
:Load note_templates Otomatis;
:Klik "Simpan & Kirim Notif";

|Sistem NORA|
:Update registrasi (current_step_id);
:Insert ke registrasi_history;
:Kirim WA via Template wa_update;
note right: One-Click Automation

|Admin|
:Dashboard Refresh (Partial Load);
stop

@enduml
```

---

## UC-07: Manage CMS Content

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #F3E5F5
skinparam ActivityBorderColor #7B1FA2
skinparam ArrowColor #7B1FA2

title UC-07: Alur Manajemen Terpusat CMS Editor

|Notaris|
start
:Buka Menu CMS Editor (?gate=cms_editor);

if (Pilih Modul?) then (Edit Beranda)
    :Ubah Teks/Gambar Landing Page;
    :Update tabel cms_section_content;
elseif (Otomatisasi & Template) then (Pesan/Catatan)
    :Edit message_templates atau note_templates;
    note right: Mengatur otomasi pesan WA\ndan catatan tiap status.
elseif (Struktur Layanan) then (CRUD Layanan)
    :Tambah/Edit jenis layanan hukum;
    :Update tabel layanan;
else (Identitas Kantor)
    :Update kontak & jam operasional;
endif

|Sistem NORA|
:Simpan Perubahan ke Database;
:Log aktivitas ke tabel audit_log;
:Tampilkan Feedback "Sukses Diperbarui";

stop

@enduml
```

---

## UC-08: Manage Workflow Steps

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #E0F2F1
skinparam ActivityBorderColor #00695C
skinparam ArrowColor #004D40

title UC-08: CMS Workflow - Pengaturan Logika 15 Status

|Notaris|
start
:Akses Menu CMS Workflow;
:Pilih Tahapan (Status 1-15);

if (Apa yang diubah?) then (Label/Urutan)
    :Ubah Nama Status atau sort_order;
elseif (Aturan Waktu) then (SLA Days)
    :Ubah sla_days (Target Selesai);
else (Sifat Status) then (Behavior)
    :Atur behavior_role (Normal/Start/Iteration/Success/Fail);
    :Atur is_cancellable (Bisa Batal?);
endif

|Sistem NORA|
:Update tabel workflow_steps;
:Log Perubahan ke audit_log;
:Refresh Aturan pada Dashboard Admin;

stop

@enduml
```

---

## UC-09: Finalisasi & Tutup Kasus

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #FFF9C4
skinparam ActivityBorderColor #FBC02D
skinparam ArrowColor #F57F17

title UC-09: Alur Finalisasi (Owner Review & Closing)

|Notaris|
start
:Akses Menu Tutup Registrasi (?gate=tutup_registrasi);

|Sistem NORA|
:Filter Berkas (Hanya Status 13/15);
note left: Hanya berkas Diserahkan\natau Batal yang muncul.

|Notaris|
:Review Riwayat & Catatan Internal;

if (Hasil Review?) then (Perlu Perbaikan)
    :Klik "Review Ulang / Re-open";
    :Kembalikan ke Status 11 (Perbaikan);
    :Input Catatan Revisi untuk Admin;
    |Sistem NORA|
    :Kirim WA Notifikasi ke Admin;
else (Sudah Sempurna)
    :Klik "Konfirmasi Tutup Kasus";
    |Sistem NORA|
    partition "Auto-Cleanup Process" {
        :UPDATE kendala SET flag_active = 0;
        note right: Red Flag dimatikan otomatis
        :Set Status = 14 (Kasus Ditutup);
        :Kunci Data (State: Read-Only);
    }
endif

|Notaris|
:Lihat Log Finalisasi di Dashboard;
stop

@enduml
```

---

## UC-10: Manage Red Flag (Kendala)

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #FFEBEE
skinparam ActivityBorderColor #C62828
skinparam ArrowColor #C62828

title UC-10: Manage Red Flag (Kendala)

|Admin|
start
:Pilih Berkas;
:Klik "Tambah Kendala";
:Input Keterangan Kendala;
:Klik "Simpan";

|Sistem NORA|
:Insert ke tabel kendala;
:Set flag_active = 1;
:Log ke audit_log;

|Admin|
:Red Flag Tampil di Dashboard;
stop

@enduml
```

---

## UC-11: View Dashboard Performance

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #F3E5F5
skinparam ActivityBorderColor #7B1FA2
skinparam ArrowColor #7B1FA2

title UC-11: Dashboard Performance - Notaris

|Notaris|
start
:Login sebagai Notaris;
:Akses Dashboard Performance;

|Sistem NORA|
:Query Data Berkas Aktif;
:Hitung SLA Compliance;
:Tampilkan Statistik Performa;

|Notaris|
:Review Dashboard;
stop

@enduml
```

---

## UC-12: Auto-Kirim WhatsApp Notification

### 4. Main Flow (Alur Utama)

```plantuml
@startuml
skinparam ActivityBackgroundColor #FFF3E0
skinparam ActivityBorderColor #E65100
skinparam ArrowColor #E65100

title UC-12: Auto-Kirim WhatsApp Notification

start

:Trigger dari UC-04 atau UC-06;

partition "Get Template & Data" {
  if (Source == UC-04?) then (Ya)
    :template_key = wa_create;
  else (UC-06)
    :template_key = wa_update;
  endif
  
  :Query message_templates;
  :Query registrasi;
  :Query layanan;
}

partition "Template Processing" {
  :Replace [Nama_Klien];
  :Replace [Nama_Layanan];
  :Replace [Nomor_Resi];
}

partition "WA Gateway Integration" {
  :retry_count = 0;
  
  while (retry_count < 3) is (Ya)
    :Call WA API;
    
    if (API success?) then (Ya)
      :status = sent;
      :retry_count = 3;
    else (Tidak)
      :retry_count = retry_count + 1;
      
      if (retry_count >= 3?) then (Ya)
        :status = failed;
      else (Tidak)
        :Wait 30 seconds;
      endif
    endif
  endwhile (Tidak)
}

:Insert into wa_logs;
:Return to caller;
stop

@enduml
```

---

*Use Case Documentation berdasarkan dokumentasi asli Sistem NORA v2.1*