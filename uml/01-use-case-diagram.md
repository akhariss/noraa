# Use Case Diagram - Sistem Tracking Status Dokumen Kantor Notaris

## Deskripsi
Diagram ini menggambarkan interaksi antara aktor dengan sistem tracking dokumen berbasis web di Kantor Notaris Sri Anah, S.H, M.Kn.

## Mermaid Diagram

```mermaid
usecaseDiagram
    actor "Klien/Pencari Perkara" as Client
    actor "Staff Administrasi" as Staff
    actor "Notaris Sri Anah" as Notaris
    actor "Admin Sistem" as Admin

    package "Sistem Tracking Dokumen Notaris" {
        usecase "UC01\nTracking Real-Time" as UC01
        usecase "UC02\nRegistrasi Perkara" as UC02
        usecase "UC03\nUpdate Status" as UC03
        usecase "UC04\nDashboard" as UC04
        usecase "UC05\nCetak Status" as UC05
        usecase "UC06\nWorkflow Auto" as UC06
        usecase "UC07\nNotifikasi" as UC07
        usecase "UC08\nBackup Data" as UC08
        usecase "UC09\nCMS" as UC09
        usecase "UC10\nLaporan" as UC10
        usecase "UC11\nArsip Digital" as UC11
        usecase "UC12\nVerifikasi" as UC12
    }

    Client --> UC01
    Client --> UC05
    Client --> UC07

    Staff --> UC02
    Staff --> UC03
    Staff --> UC04
    Staff --> UC11
    Staff --> UC12

    Notaris --> UC04
    Notaris --> UC06
    Notaris --> UC10
    Notaris --> UC12

    Admin --> UC08
    Admin --> UC09
    Admin --> UC04
    Admin --> UC03

    UC02 ..> UC03 : <<include>>
    UC06 ..> UC03 : <<include>>
    UC07 ..> UC01 : <<extend>>
    UC10 ..> UC04 : <<extend>>
```

## Penjelasan Aktor & Use Case

| Aktor | Use Case | Deskripsi |
|-------|----------|-----------|
| **Klien** | UC01 - Tracking Real-Time | Lacak status dokumen dengan nomor tracking |
| **Klien** | UC05 - Cetak Status | Cetak bukti status dokumen (PDF) |
| **Klien** | UC07 - Notifikasi | Aktifkan email/SMS notifikasi status |
| **Staff** | UC02 - Registrasi | Daftarkan perkara baru ke sistem |
| **Staff** | UC03 - Update Status | Update progress status dokumen |
| **Staff** | UC04 - Dashboard | Lihat daftar perkara & statistik |
| **Staff** | UC11 - Arsip Digital | Kelola arsip dokumen digital |
| **Staff** | UC12 - Verifikasi | Verifikasi kelengkapan dokumen awal |
| **Notaris** | UC04 - Dashboard | Monitor semua perkara |
| **Notaris** | UC06 - Workflow Auto | Buat template workflow otomatis |
| **Notaris** | UC10 - Laporan | Lihat statistik & generate laporan |
| **Notaris** | UC12 - Verifikasi | Verifikasi final dokumen |
| **Admin** | UC08 - Backup | Backup & restore database |
| **Admin** | UC09 - CMS | Kelola konten website |
| **Admin** | UC04 - Dashboard | Monitoring sistem |
| **Admin** | UC03 - Update Status | Update status darurat |

## Relasi Use Case

| Relasi | Tipe | Penjelasan |
|--------|------|------------|
| UC02 → UC03 | <<include>> | Registrasi selalu include update status awal |
| UC06 → UC03 | <<include>> | Workflow automation include update status |
| UC07 → UC01 | <<extend>> | Notifikasi adalah optional extension dari tracking |
| UC10 → UC04 | <<extend>> | Laporan adalah optional extension dari dashboard |
