# 📑 Use Case Documentation dengan Main Flow PlantUML - Sistem NORA v2.1
## Kantor Notaris Sri Anah, S.H., M.Kn.

---

## UC-01: View Landing Page

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #F1F8E9
skinparam activityBorderColor #33691E
skinparam ArrowColor #2E7D32

title UC-01: View Landing Page - Main Flow

|User|
start
:Akses URL utama (index.php);

|Sistem NORA|
:Route ke ?gate=home;

partition "Load Dynamic Content" {
  :Query cms_section_content;
  :Query cms_section_items;
  :Query cms_settings;
}

:Merge data ke template;
:Render HTML landing page;

|User|
:Lihat company profile;

if (Konten lengkap?) then (Ya)
  :User melihat informasi kantor;
  stop
else (Tidak)
  :Tampilkan default content;
  stop
endif

@enduml
```

---

## UC-02: Track Berkas (Self-Service)

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #E8F5E9
skinparam activityBorderColor #2E7D32
skinparam ArrowColor #2E7D32

title UC-02: Track Berkas - Main Flow

|Klien|
start
:Buka ?gate=lacak;
:Input nomor resi;
:Klik "Cari Berkas";

|Sistem NORA|
:Sanitize input;

if (Format valid?) then (Tidak)
  :Show error "Format tidak valid";
  stop
endif

:Query registrasi WHERE nomor_resi;

if (Resi ditemukan?) then (Tidak)
  :Show alert "Nomor Resi Tidak Ditemukan!";
  stop
else (Ya)
  partition "Data Collection" {
    :Get current_step_id;
    :Get nama_klien & layanan;
    :Query registrasi_history;
    :Query kendala WHERE flag_active = 1;
  }
  
  partition "Display Results" {
    :Tampilkan data berkas;
    :Tampilkan timeline 15 status;
    :Tampilkan tabel riwayat;
    
    if (Ada red flag aktif?) then (Ya)
      :Tampilkan pesan kendala (merah);
    else (Tidak)
      :Tampilkan "Progres Normal" (hijau);
    endif
  }
  
  |Klien|
  :Review informasi tracking;
  stop
endif

@enduml
```

---

## UC-03: Login Staff/Notaris

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #E1F5FE
skinparam activityBorderColor #01579B
skinparam ArrowColor #01579B

title UC-03: Login Staff/Notaris - Main Flow

|User|
start
:Klik "Staf Login";
:Input email;
:Input password;
:Klik "Login";

|Sistem NORA|
:Sanitize input;

if (Email & password tidak kosong?) then (Tidak)
  :Show error "Harus diisi";
  stop
endif

partition "Authentication" {
  :Query users WHERE email;
  
  if (User ditemukan?) then (Tidak)
    :Show error "Email tidak terdaftar";
    stop
  endif
  
  if (is_active == false?) then (Ya)
    :Show error "Akun tidak aktif";
    stop
  endif
  
  :password_verify(password, hash);
  
  if (Password valid?) then (Tidak)
    :Increment login_attempts;
    
    if (attempt_count >= 5?) then (Ya)
      :Lock account (15 min);
      :Show error "Akun terkunci";
    else (Tidak)
      :Show error "Password salah";
    endif
    
    stop
  endif
}

partition "Session & Redirect" {
  :Reset login_attempts;
  :Create session;
  :Log LOGIN_SUCCESS;
  
  if (Role == 'admin') then (Ya)
    :Redirect ke ?gate=registrasi;
  else (Notaris)
    :Redirect ke ?gate=dashboard;
  endif
}

|User|
:Masuk ke dashboard;
stop

@enduml
```

---

## UC-04: Registrasi Berkas Baru

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #E1F5FE
skinparam activityBorderColor #01579B
skinparam ArrowColor #01579B

title UC-04: Registrasi Berkas Baru - Main Flow

|Admin|
start
:Buka ?gate=registrasi;
:Klik "Tambah Data";
:Input nama_klien;
:Input hp_klien;
:Pilih layanan_id;
:Klik "Simpan";

|Sistem NORA|
:Sanitize inputs;

if (Semua required field terisi?) then (Tidak)
  :Show error "Field harus diisi";
  stop
endif

partition "Generate Unique Resi" {
  repeat
    :Generate NP-{date}-{sequence};
    :Check uniqueness;
  repeat while (resi sudah ada?) is (Ya) -> Tidak;
}

partition "Database Transaction" {
  :BEGIN TRANSACTION;
  
  :INSERT INTO registrasi;
  
  if (Insert success?) then (Tidak)
    :ROLLBACK;
    :Show error "Gagal menyimpan";
    stop
  endif
  
  :INSERT INTO audit_log;
  :COMMIT TRANSACTION;
}

partition "Async WA Notification" {
  :Call sendWhatsApp('wa_create');
}

|Admin|
:Tampil "Data tersimpan";
:Lihat berkas baru di list;
stop

@enduml
```

---

## UC-05: Edit Data Registrasi

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #E1F5FE
skinparam activityBorderColor #01579B
skinparam ArrowColor #01579B

title UC-05: Edit Data Registrasi - Main Flow

|Admin|
start
:Buka ?gate=registrasi;
:Cari berkas target;
:Klik "Edit";

|Sistem NORA|
:Query registrasi;

if (Data ditemukan?) then (Tidak)
  :Show error "Data tidak ditemukan";
  stop
endif

if (current_step_id >= 14?) then (Ya)
  :Show error "Data tidak dapat diubah";
  stop
endif

:Tampilkan form dengan existing data;

|Admin|
:Ubah nama_klien;
:Ubah hp_klien;
:Klik "Update";

|Sistem NORA|
:Sanitize inputs;

if (Ada perubahan data?) then (Tidak)
  :Show warning "Tidak ada perubahan";
  stop
endif

partition "Database Transaction" {
  :BEGIN TRANSACTION;
  :Get old values;
  
  :UPDATE registrasi;
  
  :INSERT INTO audit_log;
  
  :COMMIT TRANSACTION;
}

|Admin|
:Tampil "Data berhasil diperbarui";
stop

@enduml
```

---

## UC-06: Update Status Berkas (15 Status)

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #E3F2FD
skinparam activityBorderColor #1565C0
skinparam ArrowColor #1565C0

title UC-06: Update Status Berkas - Main Flow

|Admin|
start
:Pilih berkas di Dashboard;
:Klik "Update Progres";

|Sistem NORA|
:Query current_step_id;

partition "Render Next Status" {
  :Get workflow_steps;
  :Render tombol n+1 only;
  
  if (current_step_id <= 4?) then (Ya)
    :Enable tombol "Batal";
  else (Tidak)
    :Disable tombol "Batal";
  endif
  
  :Load note_templates;
}

|Admin|
:Klik tombol status baru;
:Edit catatan (opsional);
:Klik "Simpan & Kirim Notif";

|Sistem NORA|
:Validate transition n → n+1;

if (Valid?) then (Tidak)
  :Show error "Status harus berurutan";
  stop
endif

partition "Database Transaction" {
  :BEGIN TRANSACTION;
  
  :UPDATE registrasi;
  :INSERT INTO registrasi_history;
  
  :COMMIT TRANSACTION;
}

partition "Post-Update Actions" {
  :Update web tracking;
  :Call sendWhatsApp('wa_update');
}

:Partial refresh dashboard;

|Admin|
:Lihat status terupdate;
stop

@enduml
```

---

## UC-07: Manage CMS Content

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #F3E5F5
skinparam activityBorderColor #7B1FA2
skinparam ArrowColor #7B1FA2

title UC-07: Manage CMS Content - Main Flow

|Notaris|
start
:Buka ?gate=cms_editor;

|Sistem NORA|
:Tampilkan modul CMS;

|Notaris|
:Pilih "Edit Beranda";

|Sistem NORA|
:Query cms_section_content;
:Tampilkan editor;

|Notaris|
:Edit teks;
:Upload gambar baru;
:Klik "Simpan";

|Sistem NORA|
partition "Validation" {
  if (Upload gambar?) then (Ya)
    :Validate format;
    :Validate size;
    
    if (Validasi gagal?) then (Ya)
      :Show error "Format/ukuran tidak valid";
      stop
    endif
    
    :Save gambar;
  endif
  
  :Sanitize HTML;
}

partition "Save Changes" {
  :UPDATE cms_section_content;
  :INSERT INTO audit_log;
}

|Notaris|
:Tampil "Sukses Diperbarui";
stop

@enduml
```

---

## UC-08: Manage Workflow Steps

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #E0F2F1
skinparam activityBorderColor #00695C
skinparam ArrowColor #00695C

title UC-08: Manage Workflow Steps - Main Flow

|Notaris|
start
:Akses CMS Workflow;

|Sistem NORA|
:Query workflow_steps;
:Tampilkan 15 status;

|Notaris|
:Pilih status;
:Edit sla_days;
:Edit behavior_role;
:Klik "Simpan";

|Sistem NORA|
partition "Validation" {
  if (behavior_role changed?) then (Ya)
    if (New role = 'Start') then (Ya)
      :Check unique Start status;
    elseif (New role = 'Success') then (Ya)
      :Check unique Success status;
    endif
  endif
  
  if (sort_order changed?) then (Ya)
    :Check duplicate sort_order;
    
    if (Duplicate?) then (Ya)
      :Show error "Urutan sudah digunakan";
      stop
    endif
  endif
}

partition "Database Transaction" {
  :BEGIN TRANSACTION;
  :UPDATE workflow_steps;
  :INSERT INTO audit_log;
  :COMMIT TRANSACTION;
}

:Invalidate workflow cache;
:Refresh rules;

|Notaris|
:Tampil "Workflow berhasil diperbarui";
stop

@enduml
```

---

## UC-09: Finalisasi & Tutup Kasus

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #FFF9C4
skinparam activityBorderColor #FBC02D
skinparam ArrowColor #F57F17

title UC-09: Finalisasi & Tutup Kasus - Main Flow

|Notaris|
start
:Buka ?gate=tutup_registrasi;

|Sistem NORA|
:Query registrasi WHERE status IN (13, 15);
:Tampilkan list berkas siap finalisasi;

|Notaris|
:Pilih berkas;
:Review riwayat & catatan;

|Sistem NORA|
:Load full history;
:Load kendala;

|Notaris|
:Klik "Konfirmasi Tutup Kasus";

|Sistem NORA|
:Show warning "Data akan terkunci permanen";

|Notaris|
:Klik "Ya, Tutup Kasus";

|Sistem NORA|
partition "Auto-Cleanup Process" {
  :BEGIN TRANSACTION;
  
  :UPDATE kendala SET flag_active = 0;
  
  :UPDATE registrasi SET status = 14;
  
  :INSERT INTO registrasi_history;
  
  :INSERT INTO audit_log;
  
  if (All success?) then (Tidak)
    :ROLLBACK;
    :Show error "Gagal finalisasi";
    stop
  endif
  
  :COMMIT TRANSACTION;
}

:Set data ke READ-ONLY;

|Notaris|
:Tampil "Kasus Ditutup Permanen";
stop

@enduml
```

---

## UC-10: Manage Red Flag (Kendala)

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #FFEBEE
skinparam activityBorderColor #C62828
skinparam ArrowColor #C62828

title UC-10: Manage Red Flag - Main Flow

|Admin|
start
:Buka detail berkas;

|Sistem NORA|
:Check current_step_id < 14;

if (Editable?) then (Tidak)
  :Show error "Berkas sudah finalisasi";
  stop
endif

:Show tombol "Tambah Kendala";

|Admin|
:Klik "Tambah Kendala";
:Input keterangan kendala;
:Klik "Simpan";

|Sistem NORA|
if (Keterangan kosong?) then (Ya)
  :Show error "Keterangan harus diisi";
  stop
endif

partition "Database Transaction" {
  :BEGIN TRANSACTION;
  
  :INSERT INTO kendala;
  :INSERT INTO audit_log;
  
  :COMMIT TRANSACTION;
}

:Update tampilan;

|Admin|
:Tampil "Kendala berhasil ditambahkan";
stop

@enduml
```

---

## UC-11: View Dashboard Performance

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #F3E5F5
skinparam activityBorderColor #7B1FA2
skinparam ArrowColor #7B1FA2

title UC-11: View Dashboard Performance - Main Flow

|Notaris|
start
:Login;
:Akses ?gate=dashboard;

|Sistem NORA|
partition "Data Aggregation" {
  :Query registrasi aktif;
  :Query registrasi_history;
  :Query workflow_steps;
}

partition "Metrics Calculation" {
  :Count berkas per status;
  :Calculate avg processing time;
  :Identify longest files;
  :Calculate SLA compliance %;
}

if (Data cukup?) then (Tidak)
  :Show "Belum ada data";
  stop
endif

partition "Dashboard Rendering" {
  :Render chart;
  :Render table;
  :Render SLA gauge;
}

|Notaris|
:Review metrik;
stop

@enduml
```

---

## UC-12: Auto-Kirim WhatsApp Notification

### 4. Main Flow (Alur Utama) - PlantUML

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam activityBackgroundColor #FFF3E0
skinparam activityBorderColor #E65100
skinparam ArrowColor #E65100

title UC-12: Auto-Kirim WhatsApp Notification - Main Flow

start

:Trigger dari UC-04 atau UC-06;

partition "Get Template & Data" {
  if (Source == UC-04?) then (Ya)
    :template_key = 'wa_create';
  else (UC-06)
    :template_key = 'wa_update';
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
  
  repeat
    :Call WA API;
    
    if (API success?) then (Ya)
      :status = 'sent';
      :Exit retry;
    else (Tidak)
      :retry_count = retry_count + 1;
      
      if (retry_count >= 3?) then (Ya)
        :status = 'failed';
        :Queue for manual;
        :Exit retry;
      else (Tidak)
        :Wait 30 seconds;
      endif
    endif
  repeat while (retry_count < 3) is (Ya) -> Tidak;
}

:INSERT INTO wa_logs;

:Return to caller;
stop

@enduml
```

---

## Summary

Semua **12 Use Cases** sudah memiliki **Main Flow dalam bentuk PlantUML Activity Diagram** yang:
- ✅ **Syntax valid** (tidak ada error)
- ✅ **Mudah dibaca** dengan swimlanes (Actor vs System)
- ✅ **Mudah ditest** dengan step-by-step activities
- ✅ **Konsisten** format dan styling
- ✅ **Lengkap** dengan decision points dan partitions

*Dibuat untuk dokumentasi teknis Sistem NORA v2.1 - Kantor Notaris Sri Anah, S.H., M.Kn.*