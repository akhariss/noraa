# Activity Diagram - Sistem Tracking Status Dokumen Kantor Notaris

## Deskripsi
Diagram aktivitas ini menggambarkan alur kerja sistem tracking dokumen.

## 1. Activity Diagram - Tracking Real-Time (UC01)

```mermaid
flowchart TD
    Start([Mulai]) --> A[Klien akses website]
    A --> B[Klik menu Tracking]
    B --> C[Input nomor tracking]
    C --> D{Validasi format?}
    
    D -->|Invalid| E[Error: Format salah]
    E --> C
    
    D -->|Valid| F[Query: SELECT perkara]
    F --> G{Data ditemukan?}
    
    G -->|Tidak| H[Error: Data tidak ada]
    H --> C
    
    G -->|Ya| I[Get status terkini]
    I --> J[Get timeline progress]
    J --> K[Get riwayat status]
    K --> L[Tampilkan hasil]
    
    L --> M{User action?}
    M -->|Cetak PDF| N[Generate PDF]
    M -->|Aktifkan notifikasi| O[Subscribe notification]
    M -->|Kembali| P[Halaman utama]
    
    N --> Q[Download PDF]
    Q --> P
    O --> R[Set email/SMS preference]
    R --> P
    P --> End([Selesai])
```

## 2. Activity Diagram - Registrasi & Workflow (UC02, UC06)

```mermaid
flowchart TD
    Start([Mulai]) --> A[Staff login]
    A --> B{Auth valid?}
    
    B -->|No| C[Error: Login gagal]
    C --> A
    
    B -->|Yes| D[Dashboard Staff]
    D --> E[Klik Registrasi Baru]
    E --> F[Form: Data klien]
    F --> G[Form: Jenis perkara]
    G --> H[Upload dokumen]
    H --> I{Dokumen lengkap?}
    
    I -->|Tidak| J[Warning: Dokumen kurang]
    J --> G
    
    I -->|Ya| K[Generate nomor tracking]
    K --> L[Simpan ke DB: perkara]
    L --> M[Simpan ke DB: status_log]
    M --> N{Workflow tersedia?}
    
    N -->|Ya| O[Apply workflow template]
    N -->|Tidak| P[Set: Menunggu notaris]
    
    O --> Q[Set status awal dari workflow]
    P --> Q
    
    Q --> R[Kirim notifikasi ke klien]
    R --> S[Refresh dashboard]
    S --> End([Selesai])
```

## 3. Activity Diagram - Update Status & Notifikasi (UC03, UC07)

```mermaid
flowchart TD
    Start([Mulai]) --> A[Staff/Notaris login]
    A --> B[Pilih perkara]
    B --> C[Lihat detail perkara]
    C --> D[Klik Update Status]
    
    D --> E[Pilih status baru]
    E --> F[Input catatan progress]
    F --> G{Upload dokumen?}
    
    G -->|Ya| H[Upload file]
    G -->|Tidak| I[Skip upload]
    
    H --> I
    I --> J{Status final?}
    
    J -->|Ya| K[Request notaris approval]
    J -->|Tidak| L[Update langsung]
    
    K --> M{Approved?}
    M -->|No| N[Revisi required]
    N --> E
    
    M -->|Yes| O[Set status: FINAL]
    L --> O
    
    O --> P[Simpan ke DB: status_log]
    P --> Q[Log audit trail]
    Q --> R{Notifikasi aktif?}
    
    R -->|Ya| S[Send email ke klien]
    R -->|Tidak| T[Skip notifikasi]
    
    S --> T
    T --> U[Update dashboard]
    U --> End([Selesai])
```

## Penjelasan Activity

| Activity | Use Case | Aktor | Output |
|----------|----------|-------|--------|
| Tracking Real-Time | UC01, UC05, UC07 | Klien | Status, timeline, PDF |
| Registrasi & Workflow | UC02, UC06 | Staff | Nomor tracking, notifikasi |
| Update Status & Notifikasi | UC03, UC07 | Staff/Notaris | Status updated, email sent |

## Swimlane Activities

| Swimlane | Activities |
|----------|------------|
| **Klien** | Input tracking, cetak PDF, subscribe notifikasi |
| **Staff** | Login, registrasi, update status, upload dokumen |
| **Notaris** | Approval final, verifikasi, workflow setup |
| **Sistem** | Validasi, generate tracking, query DB, send notification |
