# Class Diagram - Sistem Tracking Status Dokumen Kantor Notaris

## Deskripsi
Diagram kelas ini menampilkan struktur class, atribut, method, dan relasi dalam sistem.

## Mermaid Diagram

```mermaid
classDiagram
    class User {
        <<Entity>>
        -id: int
        -email: string
        -password_hash: string
        -nama: string
        -role: RoleEnum
        -no_hp: string
        -is_active: boolean
        -created_at: datetime
        +login(email, password) AuthToken
        +logout() void
        +hasRole(role) boolean
    }

    class Klien {
        <<Entity>>
        -id: int
        -user_id: int
        -nama_lengkap: string
        -nik: string
        -alamat: text
        -email: string
        -no_hp: string
        -notification_pref: JSON
        +getPerkaraList() List~Perkara~
        +setNotificationPref(pref) void
    }

    class Perkara {
        <<Entity>>
        -id: int
        -tracking_number: string
        -jenis_perkara: JenisEnum
        -status: StatusEnum
        -klien_id: int
        -staff_id: int
        -notaris_id: int
        -tanggal_registrasi: date
        -tanggal_selesai: date
        -deskripsi: text
        -biaya: decimal
        -workflow_id: int
        +generateTrackingNumber() string
        +updateStatus(status, notes) void
        +getTimeline() List~StatusLog~
        +isFinal() boolean
    }

    class StatusLog {
        <<Entity>>
        -id: int
        -perkara_id: int
        -status: StatusEnum
        -notes: text
        -changed_by: int
        -changed_at: datetime
        +getStatusName() string
    }

    class Dokumen {
        <<Entity>>
        -id: int
        -perkara_id: int
        -nama_dokumen: string
        -file_url: string
        -file_size: int
        -is_verified: boolean
        -verified_by: int
        -uploaded_at: datetime
        +verify(verifier_id) void
    }

    class Workflow {
        <<Entity>>
        -id: int
        -nama: string
        -jenis_perkara: JenisEnum
        -steps: JSON
        -is_active: boolean
        +getSteps() List~WorkflowStep~
        +applyTo(perkara) void
    }

    class TrackingController {
        <<Controller>>
        -trackingService: TrackingService
        +submitTracking(number) Result
        +getTimeline(perkaraId) List~StatusLog~
    }

    class RegistrasiController {
        <<Controller>>
        -registrasiService: RegistrasiService
        +createPerkara(data, files) Result
    }

    class UpdateStatusController {
        <<Controller>>
        -updateService: UpdateStatusService
        +updateStatus(id, status, notes) Result
        +approveFinal(id) Result
    }

    class DashboardController {
        <<Controller>>
        -dashboardService: DashboardService
        +getDashboardData(role) DashboardData
        +generateReport(type, range) Report
    }

    class TrackingService {
        <<Service>>
        +getPerkaraByNumber(number) Perkara
        +getTimeline(perkaraId) List~StatusLog~
    }

    class RegistrasiService {
        <<Service>>
        +createPerkara(data) Perkara
        +generateTrackingNumber() string
        +applyWorkflow(perkara, workflow) void
    }

    class UpdateStatusService {
        <<Service>>
        +processUpdate(data) void
        +validateTransition(old, new) boolean
        +triggerNotification(perkara, status) void
    }

    class NotificationService {
        <<Service>>
        +sendEmail(to, subject, body) void
        +sendSMS(to, message) void
        +triggerStatusUpdate(klien, perkara, status) void
    }

    class PerkaraRepository {
        <<Repository>>
        +findById(id) Perkara
        +findByTrackingNumber(number) Perkara
        +save(perkara) void
    }

    class RoleEnum {
        <<enum>>
        CLIENT
        STAFF
        NOTARIS
        ADMIN
    }

    class StatusEnum {
        <<enum>>
        DRAFT
        MENUNGGU_VERIFIKASI
        DOKUMEN_LENGKAP
        PROSES_NOTARIS
        MENUNGGU_TANDA_TANGAN
        SELESAI
        DIBATALKAN
    }

    User "1" --> "1" Klien : has
    Klien "1" --> "0..*" Perkara : owns
    User "1" --> "0..*" Perkara : handles
    Perkara "1" --> "1..*" StatusLog : has
    Perkara "1" --> "0..*" Dokumen : contains
    Perkara "1" --> "1" Workflow : follows
    
    TrackingController --> TrackingService : uses
    RegistrasiController --> RegistrasiService : uses
    UpdateStatusController --> UpdateStatusService : uses
    DashboardController --> DashboardService : uses
    
    RegistrasiService --> NotificationService : uses
    UpdateStatusService --> NotificationService : uses
    
    TrackingService --> PerkaraRepository : uses
    RegistrasiService --> PerkaraRepository : uses
```

## Penjelasan Class

### Entity Classes (Model)

| Class | Tabel | Deskripsi |
|-------|-------|-----------|
| **User** | users | Data pengguna sistem (Staff, Notaris, Admin) |
| **Klien** | klien | Data klien/pencari perkara |
| **Perkara** | perkara | Data perkara/dokumen yang ditangani |
| **StatusLog** | status_log | History perubahan status |
| **Dokumen** | dokumen | File dokumen terkait perkara |
| **Workflow** | workflow | Template alur kerja otomatis |

### Controller Classes

| Class | Route | Use Case |
|-------|-------|----------|
| **TrackingController** | /api/tracking | UC01, UC05, UC07 |
| **RegistrasiController** | /api/registrasi | UC02, UC06 |
| **UpdateStatusController** | /api/perkara/:id/status | UC03, UC12 |
| **DashboardController** | /api/dashboard | UC04, UC10 |

### Service Classes

| Class | Fungsi |
|-------|--------|
| **TrackingService** | Logic tracking & timeline |
| **RegistrasiService** | Logic registrasi & workflow |
| **UpdateStatusService** | Logic update status & notifikasi |
| **NotificationService** | Kirim email/SMS notifikasi |

### Enum Classes

| Enum | Values |
|------|--------|
| **RoleEnum** | CLIENT, STAFF, NOTARIS, ADMIN |
| **StatusEnum** | DRAFT, MENUNGGU_VERIFIKASI, DOKUMEN_LENGKAP, PROSES_NOTARIS, MENUNGGU_TANDA_TANGAN, SELESAI, DIBATALKAN |
