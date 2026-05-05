# 📑 Dokumentasi Alur Aplikasi

# Sistem NORA v2.1 (Structured + Layered + PlantUML)

# 1. **Entry Point & Public Access Flow**

Fokus: titik masuk user ke sistem

```plantuml
@startuml
title 1. Entry Point & Public Access

start
:User akses index.php;

partition "Landing Page" {
  :Load CMS Content;
  
  if (Aksi User?) then (Lacak Berkas)
    :Redirect ke ?gate=lacak;
  else (Login)
    :Redirect ke ?gate=login;
  endif
}

stop
@enduml
```

# 2. **Public Feature Flow (Client Side)**

Fokus: fitur tanpa login

```plantuml
@startuml
title 2. Public Tracking Flow

|Klien|
start
:Buka halaman lacak;
:Input resi;
:Klik cari;

|Sistem|
if (Resi valid?) then (Ya)
  :Ambil registrasi;
  :Ambil history;
  :Cek kendala;

  |Klien|
  :Tampilkan timeline;

  if (Ada flag?) then (Ya)
    :Tampilkan Red Flag;
  else
    :Tampilkan normal;
  endif

else (Tidak)
  :Error: Resi tidak ditemukan;
endif

stop
@enduml
```

# 3. **Authentication & Authorization Flow**

Fokus: login + role

```plantuml
@startuml
title 3. Authentication Flow

start
:Input username & password;

if (Valid?) then (Ya)
  :Verifikasi password_hash;
  :Ambil role;

  if (Owner?) then (Ya)
    :Dashboard Owner;
  else
    :Dashboard Admin;
  endif

else (Tidak)
  :Tampilkan error;
endif

stop
@enduml
```

# 4. **Main Navigation Flow (Post-Login System)**

Fokus: struktur navigasi setelah login

```plantuml
@startuml
title 4. Main Navigation

start
:User masuk dashboard;

if (Role?) then (Owner)
  :Akses penuh;
else (Admin/Staff)
  :Akses operasional;
endif

:Menu utama;
-> Dashboard;
-> Registrasi;
-> Update Status;
-> CMS (Owner only);
-> Workflow (Owner only);
-> Finalisasi (Owner only);

stop
@enduml
```

# 5. **Core Feature Flow (Operational Engine)**

## 5.1 Manajemen Registrasi

```plantuml
@startuml
title 5.1 Registrasi

|Admin|
start
:Buka menu registrasi;

if (Tambah?) then (Ya)
  :Input data;
  :Klik simpan;

  |Sistem|
  :Insert registrasi;
  :Generate resi;
  :Kirim WA;

else (Edit)
  :Update data;
  |Sistem|
  :Update database;
endif

stop
@enduml
```

## 5.2 Update Status Berkas

```plantuml
@startuml
title 5.2 Update Status

|Admin|
start
:Pilih berkas;
:Klik update;

|Sistem|
:Ambil current_step;
:Filter next step;

|Admin|
:Pilih status;
:Klik simpan;

|Sistem|
:Insert history;
:Update registrasi;
:Update tracking;
:Kirim WA;

stop
@enduml
```

## 5.3 Workflow Engine (15 Status Logic)

```plantuml
@startuml
title 5.3 Workflow Engine

start
:Ambil current_step_id;

if (Step berikutnya ada?) then (Ya)
  :Tampilkan hanya step n+1;
else
  :Status final;
endif

:Update dashboard realtime;
stop
@enduml
```

# 6. **Management Flow (Owner Control)**

## 6.1 CMS Editor

```plantuml
@startuml
title 6.1 CMS Editor

|Owner|
start
:Pilih menu CMS;

if (Konten?) then (Ya)
  :Edit landing page;
elseif (Template)
  :Edit message template;
else
  :CRUD layanan;
endif

|Sistem|
:Simpan;
:Audit log;

stop
@enduml
```

## 6.2 Workflow Configuration

```plantuml
@startuml
title 6.2 Workflow Config

|Owner|
start
:Pilih status;

if (Ubah SLA?) then (Ya)
  :Update sla_days;
elseif (Behavior)
  :Update behavior_role;
else
  :Update urutan;
endif

|Sistem|
:Update workflow_steps;
:Audit log;

stop
@enduml
```

# 7. **Finalization Flow (Closing System)**

```plantuml
@startuml
title 7. Finalisasi

|Owner|
start
:Review berkas;

if (Perlu revisi?) then (Ya)
  :Kembali ke status 11;
else
  |Sistem|
  :Set status 14;
  :Nonaktifkan flag;
  :Lock data;
endif

stop
@enduml
```

# 8. **System-Wide Behavior (Global Logic)**

## 8.1 Error Handling

```plantuml
@startuml
title 8.1 Error Handling

start
if (Resi tidak ditemukan?) then (Ya)
  :Tampilkan error;
elseif (DB gagal?) then (Ya)
  :System error;
elseif (WA gagal?) then (Ya)
  :Retry / log;
endif

stop
@enduml
```

## 8.2 State Management

```plantuml
@startuml
title 8.2 State Management

start
:Draft;
:Progress;
:Selesai;
:Diserahkan;
:Ditutup;
:Batal;
stop
@enduml
```

## 8.3 Integration Flow

```plantuml
@startuml
title 8.3 Integration Flow

start
:Update status;
:Trigger WA Gateway;
:Update HTMX UI;
:Sync Database;
stop
@enduml
```

# 9. **Business Rules (Guard Logic)**

* Status ≥ 5 → tidak bisa batal
* Status harus berurutan (no skip)
* Hanya Owner bisa finalisasi
* Auto-cleanup saat status 14
* Semua aktivitas tercatat (`audit_log`, `registrasi_history`)
* WhatsApp otomatis tiap update
