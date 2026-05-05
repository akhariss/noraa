# 📐 Application Modeling Documentation - Sistem NORA v2.1

## Kantor Notaris Sri Anah, S.H., M.Kn.

---

## 1. Class Diagram

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam classBackgroundColor #E3F2FD
skinparam classBorderColor #1565C0
skinparam ArrowColor #424242

title NORA v2.1 - Class Diagram

package "Models" {
  class User {
    +int id
    +string email
    +string password_hash
    +enum role
    +string nama_lengkap
    +bool is_active
    --
    +verifyPassword(password): bool
    +isActive(): bool
  }
  
  class Registrasi {
    +int id
    +string nomor_resi
    +string nama_klien
    +string hp_klien
    +int layanan_id
    +int current_step_id
    --
    +generateResi(): string
    +updateStatus(newStepId): void
    +isEditable(): bool
  }
  
  class WorkflowStep {
    +int id
    +string step_name
    +int step_order
    +enum behavior_role
    +bool is_cancellable
    +int sla_days
    --
    +getNextStep(): WorkflowStep
    +isCancellable(): bool
  }
  
  class Kendala {
    +int id
    +int registrasi_id
    +bool flag_active
    +string keterangan
    --
    +activate(): void
    +resolve(): void
  }
}

package "Services" {
  class AuthService {
    --
    +login(email, password): User
    +logout(): void
    +checkRateLimit(email): bool
  }
  
  class WorkflowService {
    --
    +updateStatus(registrasiId, newStepId): void
    +validateTransition(from, to): bool
  }
  
  class NotificationService {
    --
    +sendWhatsApp(registrasiId, templateKey): void
    +retryLogic(phone, message): bool
  }
  
  class FinalizationService {
    --
    +finalize(registrasiId, notarisId): void
    +reopen(registrasiId, catatan): void
    +autoCleanupRedFlags(registrasiId): void
  }
}

User "1" --> "*" Registrasi : "closes"
Registrasi "1" --> "*" Kendala : "issues"

AuthService --> User : "authenticates"
WorkflowService --> Registrasi : "updates"
WorkflowService --> WorkflowStep : "validates"
NotificationService --> Registrasi : "notifies"
FinalizationService --> Registrasi : "finalizes"
FinalizationService --> Kendala : "cleans"

@enduml
```

---

## 2. Sequence Diagrams

### 2.1 Sequence: Login User

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam sequenceMessageAlign center

title Sequence Diagram: Login User (UC-01)

actor User
participant "Browser" as Browser
participant "AuthController" as Controller
participant "AuthService" as Service
participant "UserRepository" as Repo
participant "Session" as Session

User -> Browser: Input username & password
Browser -> Controller: POST /login

activate Controller
Controller -> Controller: Sanitize & Validate Input

alt Input Kosong
    Controller --> Browser: Error "Field tidak boleh kosong"
    Browser --> User: Tampilkan Error
else Input Terisi
    Controller -> Service: authenticate(username, password)
    activate Service
    Service -> Repo: findByUsername(username)
    activate Repo
  
    alt User Tidak Ditemukan
        Repo --> Service: null
        Service --> Controller: AuthException
        Controller --> Browser: Error "Kredensial salah"
    else User Ditemukan
        Repo --> Service: user_object
      
        alt Akun Terkunci (Rate Limit)
            Service --> Controller: LockedException
            Controller --> Browser: Error "Coba lagi dalam 15 menit"
        else Akun Aktif
            Service -> Service: verifyPassword(password, user.password_hash)
          
            alt Password Salah
                Service -> Repo: incrementFailedAttempts(user_id)
                Service --> Controller: AuthException
                Controller --> Browser: Error "Kredensial salah"
            else Password Benar
                Service -> Repo: resetFailedAttempts(user_id)
                Service -> Session: create(user_id, role)
                activate Session
                Session --> Service: session_id
                deactivate Session
                Service --> Controller: success_login_data
            end
        end
    end
    deactivate Repo
    deactivate Service

    alt Login Berhasil
        Controller -> Controller: Check role redirect
        Controller --> Browser: Redirect (?gate=dashboard)
        Browser --> User: Tampilkan Dashboard
    end
end
deactivate Controller
@enduml
```

### 2.2 Sequence: Registrasi Berkas Baru

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title Sequence Diagram: Registrasi Berkas Baru (UC-03)

actor Admin
participant "Browser" as Browser
participant "RegistrationController" as Controller
participant "RegistrationService" as Service
participant "Database" as DB
participant "NotificationService" as Notify

Admin -> Browser: Klik "Tambah Data"
Browser -> Controller: GET /registrasi/create
activate Controller
Controller --> Browser: Form View
deactivate Controller

Admin -> Browser: Isi Data & Klik "Simpan"
Browser -> Controller: POST /registrasi

activate Controller
Controller -> Controller: Validate inputs

alt Validasi Gagal
    Controller --> Browser: Error Validasi
else Validasi Sukses
    Controller -> Service: processRegistration(data)
    activate Service
  
    Service -> Service: generateUniqueResi()
  
    Service -> DB: BEGIN TRANSACTION
    activate DB
    Service -> DB: INSERT klien
    Service -> DB: INSERT registrasi (status=1)
  
    alt DB Error
        DB --> Service: Exception
        Service -> DB: ROLLBACK
        Service --> Controller: SaveException
    else Sukses
        Service -> DB: COMMIT
        deactivate DB
      
        Service -> Notify: sendWhatsApp(resi, 'wa_create')
        activate Notify
        Notify -> Notify: Format template & Send API
        Notify --> Service: status_queued
        deactivate Notify
      
        Service --> Controller: registrasi_success
    end
    deactivate Service
  
    Controller --> Browser: Success Alert (HTMX)
    Browser --> Admin: Update Table & Show Resi
end
deactivate Controller
@enduml
```

### 2.3 Sequence: Update Status Berkas

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title Sequence Diagram: Update Status Berkas (UC-06)

actor Admin
participant "Dashboard" as UI
participant "WorkflowController" as Controller
participant "WorkflowService" as Service
participant "Database" as DB
participant "NotificationService" as Notify

Admin -> UI: Klik "Update Progres"
UI -> Controller: GET /registrasi/{id}/next-steps
activate Controller

Controller -> Service: getAvailableSteps(current_id)
activate Service
Service -> DB: Query workflow_steps
Service -> Service: Filter next status (n+1)
Service --> Controller: steps_data
deactivate Service

Controller --> UI: Render Button Group (HTMX)
deactivate Controller

Admin -> UI: Pilih Status & Klik "Simpan"
UI -> Controller: POST /registrasi/{id}/update

activate Controller
Controller -> Service: updateStatus(id, new_step)
activate Service

Service -> DB: BEGIN TRANSACTION
Service -> DB: UPDATE registrasi.current_step_id
Service -> DB: INSERT registrasi_history
Service -> DB: COMMIT

Service -> Notify: triggerAutoWA(id, new_step)
activate Notify
Notify --> Service: async_triggered
deactivate Notify

Service --> Controller: update_success
deactivate Service

Controller --> UI: Partial Page Update (HTMX)
UI --> Admin: Dashboard Terupdate
deactivate Controller
@enduml
```

### 2.4 Sequence: Track Berkas

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title Sequence Diagram: Track Berkas (UC-02)

actor Klien
participant "Lacak Page" as Web
participant "TrackingController" as Controller
participant "Database" as DB

Klien -> Web: Masukkan Nomor Resi & Cari
Web -> Controller: POST /lacak/search

activate Controller
Controller -> DB: findByResi(nomor_resi)
activate DB
DB --> Controller: data_registrasi

alt Resi Tidak Ada
    Controller --> Web: Pesan "Data Tidak Ditemukan"
else Resi Ditemukan
    Controller -> DB: getHistory(registrasi_id)
    Controller -> DB: checkActiveKendala(registrasi_id)
    DB --> Controller: history_&_kendala_data
    deactivate DB
  
    Controller --> Web: Render Timeline & Riwayat
    Web --> Klien: Tampilkan Progres (Warna Merah jika ada kendala)
end
deactivate Controller
@enduml
```

### 2.5 Sequence: Finalisasi

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title Sequence Diagram: Finalisasi (UC-09)

actor Notaris
participant "Finalisasi Page" as UI
participant "FinalizationController" as Controller
participant "FinalizationService" as Service
participant "Database" as DB

Notaris -> UI: Review Berkas Status 13/15
UI -> Controller: GET /finalize/{id}
activate Controller
Controller -> DB: fetchAllHistory(id)
DB --> Controller: data_lengkap
Controller --> UI: Show Review View
deactivate Controller

Notaris -> UI: Klik "Konfirmasi Tutup Kasus"
UI -> Controller: POST /finalize/{id}

activate Controller
Controller -> Service: executeClosing(id, user_id)
activate Service

Service -> DB: BEGIN TRANSACTION
Service -> DB: UPDATE kendala SET flag_active=0 (Cleanup)
Service -> DB: UPDATE registrasi SET status=14 (Closed)
Service -> DB: INSERT audit_log (Closing Action)
Service -> DB: COMMIT

Service --> Controller: success
deactivate Service

Controller --> UI: Locked State (Read-Only)
UI --> Notaris: Berkas Berhasil Ditutup Permanen
deactivate Controller
@enduml
```

### 2.6 Sequence: WhatsApp Notification

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title Sequence Diagram: WA Notification Service

participant "Service Caller" as Caller
participant "NotificationService" as Notify
participant "Database" as DB
participant "WA Gateway API" as API

Caller -> Notify: send(registrasi_id, type)
activate Notify

Notify -> DB: Get Template & Klien Data
DB --> Notify: template_&_phone

Notify -> Notify: Compile Message (Replace Tags)

loop Max 3 Retries
    Notify -> API: POST /send-message
    activate API
    API --> Notify: HTTP Status (200/500)
    deactivate API
  
    alt Status OK (200)
        Notify -> DB: Log Status 'Sent'
        Notify --> Caller: success
    else Error (500)
        Notify -> Notify: Wait & Retry
    end
end

alt All Retries Failed
    Notify -> DB: Log Status 'Failed'
    Notify --> Caller: error_logged
end
deactivate Notify
@enduml
```

---

## 3. State Machine Diagram

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam stateBackgroundColor #E3F2FD
skinparam stateBorderColor #1565C0

title NORA v2.1 - State Machine Diagram (Registrasi)

[*] --> Draft

state Draft {
  [*] --> Persyaratan
  Persyaratan --> Administrasi
  Administrasi --> ValidasiSertifikat
  ValidasiSertifikat --> PengecekanSertifikat
}

state Proses {
  [*] --> PembayaranPajak
  PembayaranPajak --> ValidasiPajak
  ValidasiPajak --> PenomoranAkta
  PenomoranAkta --> Pendaftaran
  Pendaftaran --> PembayaranPNBP
  PembayaranPNBP --> PemeriksaanPertanahan
  PemeriksaanPertanahan --> Perbaikan
}

state Iterasi {
  Perbaikan --> Perbaikan: Revisi
  Perbaikan --> Selesai: Approved
}

state Selesai {
  [*] --> Diserahkan
  Diserahkan --> KasusDitutup
}

Draft --> Proses : Status 4 to 5
Proses --> Iterasi : Status 10 to 11
Iterasi --> Selesai : Status 11 to 12
Selesai --> [*] : Status 13 to 14

[*] --> Batal : Status 1-4 only

note right of Draft
  Status 1-4:
  - Tombol Batal aktif
  - Dapat dibatalkan
end note

note left of Proses
  SAFE POINT
  Setelah status 5:
  - Tombol Batal disabled
  - Tidak dapat dibatalkan
end note

note right of Iterasi
  ITERATION
  Dapat kembali ke tahap
  sebelumnya jika perlu revisi
end note

note right of KasusDitutup
  FINAL STATE
  - Data read-only
  - Auto-cleanup red flags
  - Tidak dapat diubah
end note

@enduml
```

---

## 4. Component Diagram

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam componentBackgroundColor #E3F2FD
skinparam componentBorderColor #1565C0

title NORA v2.1 - Component Diagram

package "Frontend Layer" {
  component "Landing Page" as Landing
  component "Tracking Page" as Tracking
  component "Admin Dashboard" as AdminDash
  component "Notaris Dashboard" as NotarisDash
}

package "Application Layer" {
  component "Router" as Router
  component "Auth Middleware" as AuthMW
  component "Controllers" as Controllers
  component "Services" as Services
}

package "Data Access Layer" {
  component "Repositories" as Repos
  component "Database Connection" as DBConn
}

package "External Services" {
  component "WhatsApp Gateway API" as WA
}

package "Infrastructure" {
  database "MySQL Database" as DB
  component "Session Storage" as Session
}

Landing --> Router
Tracking --> Router
AdminDash --> Router
NotarisDash --> Router

Router --> AuthMW
AuthMW --> Controllers

Controllers --> Services
Services --> Repos
Repos --> DBConn

Services --> WA

DBConn --> DB
AuthMW --> Session

note right of WA
  Third-party API
  (Fonnte/Wablas)
end note

note bottom of DB
  Tables:
  - registrasi
  - workflow_steps
  - users
  - audit_log
end note

@enduml
```

---

## 5. Deployment Diagram

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam nodeBackgroundColor #E8F5E9
skinparam nodeBorderColor #2E7D32

title NORA v2.1 - Deployment Diagram

node "Client Devices" {
  node "Desktop Browser" {
    component "Chrome/Firefox/Safari" as DesktopBrowser
  }
  
  node "Mobile Browser" {
    component "Mobile Chrome/Safari" as MobileBrowser
  }
}

node "Web Server (Apache/Nginx)" {
  node "Application Server (PHP)" {
    component "NORA Application v2.1" as App
  }
  
  node "Session Storage" {
    component "Session Files/Redis" as Session
  }
}

node "Database Server (MySQL)" {
  database "NORA Database" {
    component "registrasi" as Reg
    component "workflow_steps" as WF
    component "users" as Users
    component "audit_log" as Log
  }
  
  database "Backups" {
    component "Daily Backup" as Backup
  }
}

node "External Services" {
  cloud "WhatsApp Gateway" {
    component "Fonnte/Wablas API" as WA
  }
}

DesktopBrowser --> App : HTTPS
MobileBrowser --> App : HTTPS

App --> Session : Read/Write
App --> Reg : CRUD Operations
App --> WF : Query Workflow
App --> Users : Authentication
App --> Log : Audit Trail

App --> WA : REST API (HTTPS)
WA --> App : Response JSON

Reg --> Backup : Daily Backup

note right of App
  PHP 8.x
  HTMX for AJAX
  Bootstrap CSS
end note

note bottom of Reg
  MySQL 8.0
  InnoDB Engine
  Transaction Support
end note

@enduml
```

---

## 6. Screen Flow Diagram

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam rectangleBackgroundColor #E3F2FD
skinparam rectangleBorderColor #1565C0

title NORA v2.1 - Screen Flow Diagram

rectangle "Public Area" {
  rectangle "Landing Page\n(?gate=home)" as Landing
  rectangle "Tracking Page\n(?gate=lacak)" as Tracking
  rectangle "Login Page\n(?gate=login)" as Login
}

rectangle "Admin Area" {
  rectangle "Dashboard Registrasi\n(?gate=registrasi)" as AdminDash
  rectangle "Form Registrasi\n(Add/Edit)" as RegForm
  rectangle "Detail Berkas" as Detail
  rectangle "Update Status Form" as StatusForm
}

rectangle "Notaris Area" {
  rectangle "Dashboard Performance\n(?gate=dashboard)" as NotarisDash
  rectangle "CMS Editor\n(?gate=cms_editor)" as CMS
  rectangle "CMS Workflow\n(?gate=cms_workflow)" as CMSWF
  rectangle "Finalisasi\n(?gate=tutup_registrasi)" as Final
}

start --> Landing

Landing --> Tracking : Klik "Lacak Berkas"
Landing --> Login : Klik "Staf Login"

Tracking --> stop : User selesai

Login --> AdminDash : Role = Admin
Login --> NotarisDash : Role = Notaris

AdminDash --> RegForm : Tambah Data
AdminDash --> Detail : Pilih Berkas
RegForm --> AdminDash : Simpan
Detail --> StatusForm : Update Progres
StatusForm --> Detail : Simpan

NotarisDash --> CMS : Manage Content
NotarisDash --> CMSWF : Configure Workflow
NotarisDash --> Final : Finalisasi Kasus
CMS --> NotarisDash : Simpan
CMSWF --> NotarisDash : Simpan
Final --> NotarisDash : Tutup Kasus

@enduml
```

---

## 7. Authentication Flow

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam sequenceMessageAlign center

title Security: Authentication & Session Management Flow

actor User
participant "Browser" as Browser
participant "SSL/TLS Layer" as HTTPS
participant "Auth Middleware" as Middleware
participant "Session Manager" as Session
participant "MySQL Database" as DB

User -> Browser: Input Username & Password
Browser -> HTTPS: POST /login (Encrypted)
note right of HTTPS: **TLS 1.3** Active

HTTPS -> Middleware: Decrypt & Forward
activate Middleware

Middleware -> Middleware: Validate Rate Limit
note left: Anti-Brute Force

Middleware -> DB: Query User by Username
activate DB
DB --> Middleware: user_record
deactivate DB

Middleware -> Middleware: Verify BCRYPT Hash

alt Authentication Success
    Middleware -> Session: Create Session
    activate Session
    Session --> Middleware: session_id
    deactivate Session
  
    Middleware -> HTTPS: Set Secure Cookies
    note right of HTTPS
      **Flags:**
      - HttpOnly (Anti-XSS)
      - Secure (HTTPS Only)
      - SameSite=Strict
    end note
  
    HTTPS --> Browser: 302 Redirect (?gate=dashboard)
    Browser --> User: Berhasil Masuk Dashboard
else Authentication Failed
    Middleware -> DB: Log Failed Attempt
    activate DB
    DB --> Middleware: logged
    deactivate DB
  
    Middleware -> HTTPS: 401 Unauthorized
    HTTPS --> Browser: Tampilkan Pesan Error
    Browser --> User: Silakan Coba Lagi
end

deactivate Middleware

legend right
  **Standard NORA v2.1:**
  - Password: Bcrypt Hash
  - Session Timeout: 8 Hours
  - Audit Trail: Mandatory
end legend

@enduml
```

---

## 8. Caching Strategy

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title Performance: Caching Strategy

package "Browser Cache" {
  component "Static Assets" as StaticCache
  component "Session Storage" as SessionCache
}

package "Server Cache" {
  component "CMS Content Cache" as CMSCache
  component "Workflow Rules Cache" as WFCache
  component "Tracking Cache" as TrackingCache
}

package "Database" {
  component "Indexed Tables" as Indexes
  component "Query Cache" as QueryCache
}

User --> StaticCache : CSS, JS, Images
User --> SessionCache : Session data

StaticCache --> Browser : 304 Not Modified (if cached)

CMSCache --> Database : Invalidate on CMS update
WFCache --> Database : Invalidate on workflow change
TrackingCache --> Database : Invalidate on status update

Indexes --> Database : Faster queries
QueryCache --> Database : Reuse query plans

note right of CMSCache
  TTL: Until CMS update
  Scope: Global
end note

note right of TrackingCache
  TTL: 5 minutes
  Scope: Per resi
end note

note bottom of Indexes
  Indexed columns:
  - registrasi.nomor_resi
  - registrasi.current_step_id
  - audit_log.user_id
end note

@enduml
```

---

## 9. Error Handling Strategy

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title Error Handling Strategy

start

:Error Occurs;

if (Error Type?) then (Validation Error)
  :Log to audit_log;
  :Return 400 Bad Request;
  :Show user-friendly message;
  stop
elseif (Authentication Error)
  :Log to audit_log;
  :Return 401 Unauthorized;
  :Show "Please login";
  stop
elseif (Authorization Error)
  :Log to audit_log;
  :Return 403 Forbidden;
  :Show "Access denied";
  stop
elseif (Not Found Error)
  :Log to audit_log;
  :Return 404 Not Found;
  :Show "Page not found";
  stop
elseif (Database Error)
  :Log to audit_log (ERROR);
  :Rollback transaction;
  :Return 500 Internal Server Error;
  :Show maintenance message;
  stop
elseif (External API Error)
  :Log to wa_logs (failed);
  :Retry (max 3x);
  :Queue for manual if all fail;
  :Continue main flow;
  stop
else (Unexpected Error)
  :Log to audit_log (CRITICAL);
  :Rollback if transaction;
  :Return 500;
  :Notify admin;
  stop
endif

@enduml
```

---

*Dibuat untuk dokumentasi teknis Sistem NORA v2.1 - Kantor Notaris Sri Anah, S.H., M.Kn.*
