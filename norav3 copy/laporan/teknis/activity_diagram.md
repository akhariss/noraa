# Activity Diagram - Sistem Tracking Status Dokumen Notaris

## 1. Activity Diagram: Tracking Dokumen oleh Klien

### 1.1 Deskripsi

Activity diagram ini menggambarkan alur klien dalam melakukan tracking status dokumen dari awal hingga berhasil melihat progress dan riwayat perubahan.

```mermaid
flowchart TD
    Start([Mulai]) --> A[Akses Halaman Tracking]
    A --> B[Input Nomor Registrasi]
    B --> C{Nomor Valid?}
    C -->|Tidak| D[Tampilkan Error Validasi]
    D --> B
    C -->|Ya| E[Cari di Database]
    E --> F{Ditemukan?}
    F -->|Tidak| G[Tampilkan Error: Tidak Ditemukan]
    G --> End([Selesai])
    F -->|Ya| H[Tampilkan Form Verifikasi]
    H --> I[Input 4 Digit Terakhir HP]
    I --> J{Kode Sesuai?}
    J -->|Tidak| K[Increment Failed Attempt]
    K --> L{Max Attempts?}
    L -->|Ya| M[Rate Limit IP]
    M --> End
    L -->|Tidak| I
    J -->|Ya| N[Generate Tracking Token]
    N --> O[Simpan Token ke Database]
    O --> P[Redirect ke Halaman Detail]
    P --> Q[Tampilkan Progress Bar]
    Q --> R{Tampilkan Riwayat?}
    R -->|Ya| S[Load Registrasi History]
    S --> T[Render Timeline]
    T --> End
    R -->|Tidak| End
```

### 1.2 Detail Aktivitas

| Aktivitas              | Deskripsi                            | Komponen                             |
| ---------------------- | ------------------------------------ | ------------------------------------ |
| Akses Halaman Tracking | Klien membuka `?gate=lacak`        | Main\Controller::tracking()          |
| Input Nomor Registrasi | Klien input nomor registrasi         | Form POST                            |
| Validasi Format        | Sistem validasi format input         | InputSanitizer                       |
| Cari di Database       | Query registrasi by nomor_registrasi | Registrasi::findByNomorRegistrasi()  |
| Form Verifikasi        | Sistem minta 4 digit HP              | tracking.php view                    |
| Verifikasi Kode        | Bandingkan input dengan data klien   | Main\Controller::verifyTracking()    |
| Generate Token         | Buat secure tracking token           | generateTrackingToken()              |
| Tampilkan Progress     | Render progress bar                  | registrasi_detail.php                |
| Load History           | Query registrasi_history             | RegistrasiHistory::getByRegistrasi() |

### 1.3 Business Rules

1. **Rate Limiting**: Maksimal 5 percobaan verifikasi per menit per IP
2. **Token Expiry**: Tracking token expired setelah 24 jam
3. **No Phone Exposure**: Nomor HP lengkap tidak pernah ditampilkan ke klien
4. **Secure Token**: Token menggunakan HMAC-SHA256 signature

---

## 2. Activity Diagram: Workflow Approval Notaris

### 2.1 Deskripsi

Activity diagram ini menggambarkan alur workflow internal dari staff input registrasi hingga approval oleh notaris.

```mermaid
flowchart TD
    Start([Mulai]) --> A[Staff Login]
    A --> B{Auth Success?}
    B -->|Tidak| C[Tampilkan Error]
    C --> End([Selesai])
    B -->|Ya| D[Akses Dashboard]
    D --> E[Klik Tambah Registrasi]
    E --> F[Input Data Klien]
    F --> G[Input Data Registrasi]
    G --> H{Status Awal Valid?}
    H -->|Tidak| I[Error: Hanya 4 Status Pertama]
    I --> G
    H -->|Ya| J[Submit Form]
    J --> K[Generate Nomor Registrasi]
    K --> L[Generate Verification Code]
    L --> M[Simpan Registrasi]
    M --> N[Log ke Audit Log]
    N --> O[Log ke Registrasi History]
    O --> P{Tampilkan WhatsApp Popup?}
    P -->|Ya| Q[Buka WhatsApp Web]
    P -->|Tidak| R[Redirect ke Daftar Registrasi]
    Q --> R
    R --> S[Staff Update Status Berkala]
    S --> T{Perlu Approval Notaris?}
    T -->|Tidak| U[Staff Update Status]
    U --> V[WorkflowService Validasi]
    V --> W{Valid?}
    W -->|Tidak| X[Error: Transisi Invalid]
    W -->|Ya| Y[Update Database]
    Y --> Z[Log History]
    Z --> AA{Status Final?}
    AA -->|Tidak| S
    AA -->|Ya| AB[Notaris Finalisasi]
    T -->|Ya| AC[Notaris Review Dokumen]
    AC --> AD{Approve?}
    AD -->|Tidak| AE[Set Status: Perbaikan]
    AD -->|Ya| AF[Update Status Lanjut]
    AE --> S
    AF --> Y
    AB --> AG[Status: Ditutup]
    AG --> AH[Read-Only State]
    AH --> End
```

### 2.2 Detail Aktivitas

| Aktivitas                 | Deskripsi                | Komponen                                 |
| ------------------------- | ------------------------ | ---------------------------------------- |
| Staff Login               | Autentikasi staff        | Auth\Controller::login()                 |
| Input Data Klien          | Nama, HP, Email          | Form registrasi_create.php               |
| Input Data Registrasi     | Layanan, Status, Catatan | Form registrasi_create.php               |
| Validasi Status Awal      | Cek 4 status pertama     | allowedCreateStatuses array              |
| Generate Nomor Registrasi | NP-YYYYMMDD-XXXX         | Auto-increment + date                    |
| Simpan Registrasi         | INSERT ke registrasi     | Registrasi::create()                     |
| Update Status Berkala     | Staff update progress    | Dashboard\Controller::updateStatus()     |
| Validasi Transisi         | Workflow validation      | WorkflowService::updateStatus()          |
| Notaris Review            | Approval dokumen         | Implicit via update status               |
| Finalisasi                | Tutup kasus              | Finalisasi\Controller::tutupRegistrasi() |

### 2.3 Business Rules

1. **Status Awal Terbatas**: Hanya `draft`, `pembayaran_admin`, `validasi_sertifikat`, `pencecekan_sertifikat`
2. **Tidak Bisa Mundur**: Status tidak dapat mundur (kecuali dari `perbaikan`)
3. **Batas Pembatalan**: Tidak bisa batal setelah `pembayaran_pajak`
4. **Lock Mechanism**: Registrasi locked tidak dapat diupdate
5. **Final Status**: `selesai`, `ditutup`, `batal` adalah read-only

---

## 3. Activity Diagram: Update Status dengan Validasi

### 3.1 Deskripsi

Activity diagram detail untuk proses update status dengan validasi workflow.

```mermaid
flowchart TD
    Start([Mulai: Request Update Status]) --> A[User Submit Form]
    A --> B{User Authenticated?}
    B -->|Tidak| C[Redirect Login]
    B -->|Ya| D[CSRF Validation]
    D --> E{CSRF Valid?}
    E -->|Tidak| F[Reject Request]
    E -->|Ya| G[InputSanitizer::sanitizeGlobal]
    G --> H[WorkflowService::updateStatus]
    H --> I[Load Registrasi by ID]
    I --> J{Registrasi Exists?}
    J -->|Tidak| K[Return Error]
    J -->|Ya| L[Get Old Status]
    L --> M{Status Final?}
    M -->|Ya| N[Error: Cannot Update Final Status]
    M -->|Tidak| O{Registrasi Locked?}
    O -->|Ya| P[Error: Registrasi Locked]
    O -->|Tidak| Q[Get Current Order & New Order]
    Q --> R{New Status = Batal?}
    R -->|Ya| S{In CANCELLABLE_STATUSES?}
    S -->|Tidak| T[Error: Cannot Cancel After Tax]
    S -->|Ya| U[Proceed]
    R -->|Tidak| V{New Order < Current Order?}
    V -->|Ya| W{Old Status = Perbaikan?}
    W -->|Tidak| X[Error: Cannot Go Backward]
    W -->|Ya| Y[Allow Loop Back]
    V -->|Tidak| Z[Proceed]
    Y --> Z
    U --> Z
    Z --> AA[Update Database]
    AA --> AB{Flag Kendala Changed?}
    AB -->|Ya| AC[Update Kendala Table]
    AB -->|Tidak| AD[Skip]
    AC --> AE
    AD --> AE
    AE[Save Registrasi History] --> AF[Return Success]
    AF --> AG([Selesai])
    K --> AH([Error Exit])
    N --> AH
    P --> AH
    T --> AH
    X --> AH
```

### 3.2 Decision Points

| Decision                   | Condition                    | True Branch       | False Branch   |
| -------------------------- | ---------------------------- | ----------------- | -------------- |
| User Authenticated?        | Session exists               | Proceed           | Redirect login |
| CSRF Valid?                | Token match                  | Proceed           | Reject         |
| Registrasi Exists?         | ID found                     | Proceed           | Error          |
| Status Final?              | In [selesai, ditutup, batal] | Error             | Proceed        |
| Registrasi Locked?         | is_locked = 1                | Error             | Proceed        |
| New Status = Batal?        | status == 'batal'            | Check cancellable | Check order    |
| In CANCELLABLE_STATUSES?   | status in array              | Proceed           | Error          |
| New Order < Current Order? | backward transition          | Check perbaikan   | Proceed        |
| Old Status = Perbaikan?    | status == 'perbaikan'        | Allow loop        | Error          |

---

## 4. Activity Diagram: CMS Management

### 4.1 Deskripsi

Activity diagram untuk manajemen konten CMS oleh notaris.

```mermaid
flowchart TD
    Start([Mulai]) --> A[Notaris Login]
    A --> B{Role = Notaris?}
    B -->|Tidak| C[403 Forbidden]
    B -->|Ya| D[Akses CMS Editor]
    D --> E{Pilih Section}
    E -->|Home| F[Edit Homepage Content]
    E -->|Layanan| G[Edit Layanan Section]
    E -->|Pesan| H[Edit Message Template]
    E -->|Catatan| I[Edit Note Template]
    E -->|Settings| J[App Settings]
  
    F --> K[Update cms_section_content]
    G --> K
    H --> L[Update message_templates]
    I --> M[Update note_templates]
    J --> N[Update config in database]
  
    K --> O[Set updated_by, updated_at]
    L --> O
    M --> O
    N --> O
  
    O --> P[Save Changes]
    P --> Q{Upload Image?}
    Q -->|Ya| R[Media Controller::upload]
    R --> S[Validate File Size & Type]
    S --> T{Valid?}
    T -->|Tidak| U[Error]
    T -->|Ya| V[Generate Secure Filename]
    V --> W[Move Uploaded File]
    W --> X[Return Image Path]
    X --> Y[Update Content with Path]
    Q -->|Tidak| Z
    U --> AA([Error Exit])
    Y --> Z
    Z --> AB([Selesai])
```

---

## 5. Activity Diagram: User Management

### 5.1 Deskripsi

Activity diagram untuk manajemen user (hanya notaris).

```mermaid
flowchart TD
    Start([Mulai]) --> A[Notaris Akses Users Menu]
    A --> B{Role = Notaris?}
    B -->|Tidak| C[403 Forbidden]
    B -->|Ya| D[Load User List]
    D --> E{Pilih Aksi}
  
    E -->|Create| F[Show Create Form]
    F --> G[Input Username, Password, Role]
    G --> H[Hash Password bcrypt]
    H --> I[INSERT users Table]
    I --> J[Audit Log: create user]
    J --> K[Refresh User List]
  
    E -->|Update| L[Show Edit Form]
    L --> M[Update Username/Role]
    M --> N[UPDATE users Table]
    N --> O[Audit Log: update user]
    O --> K
  
    E -->|Delete| P[Confirm Delete]
    P --> Q{Confirm?}
    Q -->|Tidak| K
    Q -->|Ya| R[DELETE users Table]
    R --> S[Audit Log: delete user]
    S --> K
  
    K --> T{Aksi Lain?}
    T -->|Ya| E
    T -->|Tidak| U([Selesai])
```

---

## 6. Activity Diagram: Finalisasi Kasus

### 6.1 Deskripsi

Activity diagram untuk proses finalisasi (tutup) kasus.

```mermaid
flowchart TD
    Start([Mulai]) --> A[Notaris Akses Finalisasi Menu]
    A --> B[Load Registrasi Status 'Selesai']
    B --> C{Pilih Registrasi}
    C --> D[Show Detail Finalisasi]
    D --> E[Input Catatan Finalisasi Optional]
    E --> F[Konfirmasi Tutup Kasus]
    F --> G{Confirm?}
    G -->|Tidak| H([Cancel])
    G -->|Ya| I[FinalisasiService::tutup]
    I --> J[Update Status → 'Ditutup']
    J --> K[Set finalized_at, finalized_by]
    K --> L[Deactivate All Kendala Flags]
    L --> M[Save Registrasi History]
    M --> N[Audit Log: finalize]
    N --> O[Set Read-Only State]
    O --> P([Selesai])
  
    H --> Q([Batal])
```

### 6.2 Reopen Case Flow

```mermaid
flowchart TD
    Start([Mulai: Reopen Case]) --> A[Notaris Pilih Case Ditutup]
    A --> B[Show Reopen Button]
    B --> C[Konfirmasi Reopen]
    C --> D{Confirm?}
    D -->|Tidak| E([Cancel])
    D -->|Ya| F[FinalisasiService::reopen]
    F --> G[Update Status → 'Selesai']
    G --> H[Clear finalized_at, finalized_by]
    H --> I[Save Registrasi History]
    I --> J[Audit Log: reopen]
    J --> K[Remove Read-Only State]
    K --> L([Selesai])
  
    E --> M([Batal])
```

---

## 7. Activity Diagram: Backup & Restore

### 7.1 Deskripsi

Activity diagram untuk backup database.

```mermaid
flowchart TD
    Start([Mulai]) --> A[Notaris Akses Backup Menu]
    A --> B{Role = Notaris?}
    B -->|Tidak| C[403 Forbidden]
    B -->|Ya| D[Show Backup Interface]
    D --> E{Pilih Aksi}
  
    E -->|Create Backup| F[BackupService::createBackup]
    F --> G[Execute mysqldump]
    G --> H[Generate SQL File]
    H --> I[Save to /storage/backups/]
    I --> J[Audit Log: backup create]
    J --> K[Refresh Backup List]
  
    E -->|Download| L[Download Backup File]
    L --> M[Stream File]
    M --> N[Log Download]
  
    E -->|Delete| O[Confirm Delete]
    O --> P{Confirm?}
    P -->|Tidak| K
    P -->|Ya| Q[Delete File]
    Q --> R[Audit Log: backup_delete]
    R --> K
  
    K --> S{Aksi Lain?}
    S -->|Ya| E
    S -->|Tidak| T([Selesai])
```

---

## 8. Swimlane Activity Diagram: End-to-End Process

### 8.1 Deskripsi

Swimlane diagram yang menunjukkan interaksi antara Klien, Staff, Notaris, dan Sistem dalam satu alur lengkap.

```mermaid
flowchart TD
    subgraph Klien
        K1[Akses Tracking Page]
        K2[Input Nomor Registrasi]
        K3[Input 4 Digit HP]
        K4[Lihat Progress]
        K5[Lihat History]
    end
  
    subgraph Staff
        S1[Login]
        S2[Create Registrasi]
        S3[Update Status Berkala]
        S4[Upload Dokumen]
    end
  
    subgraph Notaris
        N1[Login]
        N2[Review Dokumen]
        N3[Approval Update]
        N4[Finalisasi Kasus]
        N5[User Management]
    end
  
    subgraph Sistem
        SY1[Generate Nomor Registrasi]
        SY2[Validasi Transisi]
        SY3[Logging History]
        SY4[Generate Token]
        SY5[RBAC Check]
    end
  
    K1 --> K2
    K2 --> SY1
    SY1 --> S2
    S2 --> SY3
    SY3 --> S3
    S3 --> SY2
    SY2 --> SY3
    SY3 --> N2
    N2 --> N3
    N3 --> SY2
    SY2 --> SY3
    SY3 --> K3
    K3 --> SY4
    SY4 --> K4
    K4 --> K5
    SY3 --> N4
    N4 --> SY3
    N1 --> N5
    N5 --> SY5
```

---

## 9. Kesimpulan

Activity Diagram yang telah diuraikan mencakup:

1. **Tracking oleh Klien** - Alur lengkap dari input nomor registrasi hingga viewing progress
2. **Workflow Internal** - Staff input → notaris approval → finalisasi
3. **Update Status Validation** - Decision tree validasi transisi status
4. **CMS Management** - Content editing dan image upload
5. **User Management** - CRUD user dengan audit logging
6. **Finalisasi** - Tutup dan reopen kasus
7. **Backup** - Database backup management
8. **End-to-End Swimlane** - Interaksi semua aktor dalam satu flow

Semua diagram mengikuti business rules yang ketat untuk domain notaris, termasuk batasan pembatalan, validasi workflow, dan security measures.
