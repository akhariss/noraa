# 📋 Product Requirements Document (PRD) - Sistem NORA v2.1

## Kantor Notaris Sri Anah, S.H., M.Kn.

---

## 📑 Document Information

| **Item**             | **Detail**                                          |
| -------------------------- | --------------------------------------------------------- |
| **Product Name**     | NORA (Notaris Online Registration & Tracking Application) |
| **Version**          | 2.1                                                       |
| **Document Version** | 1.0                                                       |
| **Date**             | April 2026                                                |
| **Author**           | Development Team                                          |
| **Stakeholders**     | Notaris Sri Anah, S.H., M.Kn. (Owner), Admin Staff, Klien |
| **Status**           | Final                                                     |

---

## 1. Executive Summary

### 1.1 Product Vision

Sistem NORA adalah platform digital untuk Kantor Notaris Sri Anah yang mengubah proses manual pencatatan berkas menjadi sistem terintegrasi dengan tracking real-time, automasi WhatsApp notification, dan workflow 15 status yang terstruktur.

### 1.2 Problem Statement

**Before (AS-IS):**

- Admin mencatat berkas di buku besar secara manual
- Klien harus menghubungi admin untuk cek status (interruptive)
- Tidak ada tracking sistematis
- Rawan human error dan kehilangan data

**After (TO-BE):**

- Semua data terdigitalisasi dalam satu dashboard
- Klien dapat tracking mandiri via web (self-service)
- 15 status workflow terstruktur dengan automasi
- WhatsApp notification otomatis setiap update

### 1.3 Business Objectives

| **Objective**   | **KPI**            | **Target**      |
| --------------------- | ------------------------ | --------------------- |
| Efisiensi Operasional | Waktu update status      | < 30 detik per berkas |
| Transparansi          | Klien cek status mandiri | 90% tracking via web  |
| Otomasi               | WhatsApp notification    | 100% otomatis         |
| Akurasi               | Error rate               | < 1%                  |
| Kepuasan Klien        | NPS Score                | > 70                  |

---

## 2. Product Overview

### 2.1 System Architecture Overview

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title NORA v2.1 - System Architecture

package "Client Layer" {
  [Web Browser] as Browser
  [Mobile Browser] as Mobile
}

package "Presentation Layer" {
  [Landing Page] as Landing
  [Tracking Page] as Tracking
  [Admin Dashboard] as Admin
  [Notaris Dashboard] as Notaris
}

package "Application Layer" {
  [Authentication Service] as Auth
  [Registration Service] as Regis
  [Workflow Engine] as Workflow
  [CMS Service] as CMS
  [Notification Service] as Notify
}

package "Data Layer" {
  database "MySQL Database" as DB {
    [registrasi] as Reg
    [workflow_steps] as WF
    [users] as Users
    [cms_content] as CMS
    [audit_log] as Log
  }
}

package "External Services" {
  [WhatsApp Gateway API] as WA
}

Browser --> Landing
Browser --> Tracking
Mobile --> Tracking
Admin --> Admin
Notaris --> Notaris

Landing --> CMS
Tracking --> Regis
Admin --> Auth
Admin --> Regis
Admin --> Workflow
Notaris --> Auth
Notaris --> CMS
Notaris --> Workflow

Auth --> Users
Regis --> Reg
Workflow --> WF
CMS --> CMS
Notify --> Reg

Regis --> Notify
Workflow --> Notify

Notify --> WA

@enduml
```

### 2.2 User Roles & Permissions

| **Role**            | **Access Level** | **Key Permissions**                      | **Restrictions**                 |
| ------------------------- | ---------------------- | ---------------------------------------------- | -------------------------------------- |
| **Klien (Public)**  | Read-only              | Track berkas via resi                          | No login, no edit                      |
| **Admin**           | Write                  | CRUD registrasi, update status, manage kendala | Cannot finalize cases, cannot edit CMS |
| **Notaris (Owner)** | Full                   | All admin + CMS, workflow config, finalisasi   | None                                   |

### 2.3 Core Features

| **Feature**       | **Priority** | **UC Reference** | **Business Value**      |
| ----------------------- | ------------------ | ---------------------- | ----------------------------- |
| Self-Service Tracking   | P0 (Critical)      | UC-02                  | Reduce admin interruption 90% |
| 15 Status Workflow      | P0 (Critical)      | UC-06                  | Standardize legal procedure   |
| WhatsApp Automation     | P0 (Critical)      | UC-12                  | Instant client notification   |
| Registration Management | P0 (Critical)      | UC-04, UC-05           | Digital record keeping        |
| CMS Editor              | P1 (High)          | UC-07                  | Dynamic content management    |
| Workflow Configuration  | P1 (High)          | UC-08                  | Flexible process adaptation   |
| Case Finalization       | P1 (High)          | UC-09                  | Audit & compliance            |
| Red Flag Management     | P1 (High)          | UC-10                  | Issue tracking                |
| Performance Dashboard   | P2 (Medium)        | UC-11                  | Business intelligence         |

---

## 3. Functional Requirements

### 3.1 Public Features

#### FR-01: Landing Page

- **ID:** FR-01
- **Priority:** P0
- **Description:** Halaman utama menampilkan company profile dinamis
- **Acceptance Criteria:**
  - [ ] Konten loaded dari database (cms_section_content)
  - [ ] Responsive design (desktop & mobile)
  - [ ] Load time < 2 seconds
  - [ ] Navigation ke Tracking & Login
- **Test Case:** AD-01

#### FR-02: Self-Service Tracking

- **ID:** FR-02
- **Priority:** P0
- **Description:** Klien track berkas via nomor resi tanpa login
- **Acceptance Criteria:**
  - [ ] Input nomor resi valid (format NP-xxxx)
  - [ ] Timeline 15 status dengan highlight current
  - [ ] History tabel chronological
  - [ ] Red flag display jika ada kendala
  - [ ] Response time < 1 second
- **Test Case:** AD-02

### 3.2 Admin Features

#### FR-03: Authentication

- **ID:** FR-03
- **Priority:** P0
- **Description:** Login system dengan role-based access
- **Acceptance Criteria:**
  - [ ] Email & password validation
  - [ ] BCRYPT password hashing
  - [ ] Max 5 failed attempts → lock 15 min
  - [ ] Session timeout 8 hours
  - [ ] Role-based redirect (Admin/Notaris)
- **Test Case:** AD-03

#### FR-04: Registration Management

- **ID:** FR-04
- **Priority:** P0
- **Description:** CRUD data registrasi berkas
- **Acceptance Criteria:**
  - [ ] Auto-generate unique resi (NP-xxxx)
  - [ ] Form validation (required fields, HP format)
  - [ ] Edit blocked after status 14
  - [ ] Audit trail for all changes
  - [ ] WA notification on create
- **Test Case:** AD-04, AD-05

#### FR-05: Status Update (15 Workflow)

- **ID:** FR-05
- **Priority:** P0
- **Description:** Update progres berkas melalui 15 status terstruktur
- **Acceptance Criteria:**
  - [ ] Sequential progression only (n → n+1)
  - [ ] Safe point: Batal button disabled after status 5
  - [ ] Auto-load note templates
  - [ ] One-click automation (DB update + WA + timeline)
  - [ ] Partial page refresh (HTMX)
  - [ ] Real-time tracking update
- **Test Case:** AD-06

#### FR-06: Red Flag Management

- **ID:** FR-06
- **Priority:** P1
- **Description:** Tandai dan selesaikan kendala berkas
- **Acceptance Criteria:**
  - [ ] Multiple flags per berkas allowed
  - [ ] Keterangan required
  - [ ] Blocked after status 14
  - [ ] Visual indicator (red highlight)
  - [ ] Auto-cleanup on finalization
- **Test Case:** AD-10

### 3.3 Notaris Features

#### FR-07: CMS Content Management

- **ID:** FR-07
- **Priority:** P1
- **Description:** Edit landing page content & templates
- **Acceptance Criteria:**
  - [ ] Edit beranda (hero, about, services)
  - [ ] Upload images (max 2MB, JPG/PNG/WEBP)
  - [ ] XSS prevention on HTML content
  - [ ] Template placeholder validation
  - [ ] CRUD layanan with usage check
- **Test Case:** AD-07

#### FR-08: Workflow Configuration

- **ID:** FR-08
- **Priority:** P1
- **Description:** Configure 15 status logic & SLA
- **Acceptance Criteria:**
  - [ ] Edit label, order, SLA days
  - [ ] Set behavior_role (Normal/Start/Iteration/Success/Fail)
  - [ ] Toggle is_cancellable flag
  - [ ] Validation: unique Start/Success roles
  - [ ] Validation: no duplicate sort_order
- **Test Case:** AD-08

#### FR-09: Case Finalization

- **ID:** FR-09
- **Priority:** P1
- **Description:** Review & permanently close cases
- **Acceptance Criteria:**
  - [ ] Filter only status 13 or 15
  - [ ] Full history review
  - [ ] Re-open option (back to status 11)
  - [ ] Irreversible confirmation dialog
  - [ ] Auto-cleanup all red flags
  - [ ] Set status 14 & lock data
- **Test Case:** AD-09

#### FR-10: Performance Dashboard

- **ID:** FR-10
- **Priority:** P2
- **Description:** Monitor operational metrics & SLA
- **Acceptance Criteria:**
  - [ ] Total berkas per status
  - [ ] Average processing time
  - [ ] Longest files per stage
  - [ ] SLA compliance percentage
  - [ ] Visual charts & tables
- **Test Case:** AD-11

### 3.4 System Features

#### FR-11: WhatsApp Automation

- **ID:** FR-11
- **Priority:** P0
- **Description:** Auto-send WA notifications
- **Acceptance Criteria:**
  - [ ] Template-based messages
  - [ ] Variable replacement ([Nama_Klien], etc.)
  - [ ] Async non-blocking process
  - [ ] Retry logic (max 3x, 30s interval)
  - [ ] Log all attempts (wa_logs)
  - [ ] Queue failed messages for manual
- **Test Case:** AD-12

#### FR-12: Audit Trail

- **ID:** FR-12
- **Priority:** P0
- **Description:** Log all system activities
- **Acceptance Criteria:**
  - [ ] Log all CRUD operations
  - [ ] Record old & new values
  - [ ] Timestamp & user_id
  - [ ] IP address logging
  - [ ] Immutable audit records
- **Test Case:** All ADs

---

## 4. Non-Functional Requirements

### 4.1 Performance Requirements

| **Metric**    | **Requirement** | **Measurement** |
| ------------------- | --------------------- | --------------------- |
| Page Load Time      | < 2 seconds           | Lighthouse            |
| API Response Time   | < 500ms               | Server logs           |
| Concurrent Users    | 50 users              | Load testing          |
| Database Query Time | < 100ms               | EXPLAIN analysis      |
| WA Send Time        | < 3 seconds           | wa_logs               |

### 4.2 Security Requirements

| **Requirement** | **Implementation** | **Verification** |
| --------------------- | ------------------------ | ---------------------- |
| Password Security     | BCRYPT hashing (cost 12) | Code review            |
| SQL Injection         | Prepared statements      | Penetration test       |
| XSS Prevention        | HTML sanitization        | OWASP ZAP scan         |
| CSRF Protection       | Token validation         | Security audit         |
| Session Security      | HTTPS, timeout 8h        | Configuration review   |
| Rate Limiting         | 5 attempts/15min         | Load testing           |
| Data Encryption       | TLS 1.3 for transit      | SSL Labs test          |

### 4.3 Availability Requirements

| **Metric**   | **Target**   | **Monitoring** |
| ------------------ | ------------------ | -------------------- |
| Uptime             | 99.5% monthly      | Uptime monitoring    |
| Backup Frequency   | Daily              | Backup logs          |
| Recovery Time      | < 4 hours          | DR plan              |
| Maintenance Window | Sunday 02:00-04:00 | Schedule             |

### 4.4 Compatibility Requirements

| **Platform** | **Version** | **Support Level** |
| ------------------ | ----------------- | ----------------------- |
| Chrome             | 90+               | Full                    |
| Firefox            | 88+               | Full                    |
| Safari             | 14+               | Full                    |
| Mobile Chrome      | 90+               | Full                    |
| Mobile Safari      | 14+               | Full                    |

---

## 5. Data Model

### 5.1 Entity Relationship Diagram

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam entityBackgroundColor #E3F2FD
skinparam entityBorderColor #1565C0

title NORA v2.1 - Entity Relationship Diagram

entity "users" as users {
  * id : INT (PK)
  --
  * email : VARCHAR(100) (UNIQUE)
  * password_hash : VARCHAR(255)
  * role : ENUM('admin','notaris')
  * nama_lengkap : VARCHAR(100)
  * is_active : BOOLEAN
  created_at : TIMESTAMP
  updated_at : TIMESTAMP
}

entity "layanan" as layanan {
  * id : INT (PK)
  --
  * nama_layanan : VARCHAR(100)
  deskripsi : TEXT
  biaya : DECIMAL(15,2)
  is_active : BOOLEAN
  created_at : TIMESTAMP
}

entity "registrasi" as registrasi {
  * id : INT (PK)
  --
  * nomor_resi : VARCHAR(50) (UNIQUE)
  * nama_klien : VARCHAR(100)
  * hp_klien : VARCHAR(20)
  * layanan_id : INT (FK)
  * current_step_id : INT
  status : VARCHAR(20)
  created_at : TIMESTAMP
  updated_at : TIMESTAMP
  closed_at : TIMESTAMP
  closed_by : INT (FK)
}

entity "workflow_steps" as workflow_steps {
  * id : INT (PK)
  --
  * step_name : VARCHAR(100)
  * step_order : INT (UNIQUE)
  * behavior_role : ENUM
  * is_cancellable : BOOLEAN
  sla_days : INT
  color_code : VARCHAR(7)
}

entity "registrasi_history" as registrasi_history {
  * id : INT (PK)
  --
  * registrasi_id : INT (FK)
  from_step_id : INT
  to_step_id : INT
  catatan : TEXT
  timestamp : TIMESTAMP
  user_id : INT (FK)
}

entity "kendala" as kendala {
  * id : INT (PK)
  --
  * registrasi_id : INT (FK)
  * flag_active : BOOLEAN
  keterangan : TEXT
  created_at : TIMESTAMP
  resolved_at : TIMESTAMP
}

entity "message_templates" as message_templates {
  * id : INT (PK)
  --
  * template_key : VARCHAR(50) (UNIQUE)
  * template_content : TEXT
  variables : JSON
}

entity "note_templates" as note_templates {
  * id : INT (PK)
  --
  * step_id : INT (FK)
  * template_content : TEXT
}

entity "wa_logs" as wa_logs {
  * id : INT (PK)
  --
  * registrasi_id : INT (FK)
  * template_key : VARCHAR(50)
  status : ENUM('sent','failed')
  sent_at : TIMESTAMP
  error_message : TEXT
}

entity "audit_log" as audit_log {
  * id : INT (PK)
  --
  * user_id : INT (FK)
  * action : VARCHAR(100)
  old_value : JSON
  new_value : JSON
  ip_address : VARCHAR(45)
  timestamp : TIMESTAMP
}

entity "cms_section_content" as cms_section_content {
  * id : INT (PK)
  --
  * section_key : VARCHAR(50) (UNIQUE)
  content_text : TEXT
  content_html : TEXT
  updated_at : TIMESTAMP
}

entity "cms_section_items" as cms_section_items {
  * id : INT (PK)
  --
  item_name : VARCHAR(100)
  item_description : TEXT
  item_image : VARCHAR(255)
  sort_order : INT
}

entity "cms_settings" as cms_settings {
  * id : INT (PK)
  --
  * setting_key : VARCHAR(50) (UNIQUE)
  setting_value : TEXT
}

entity "login_attempts" as login_attempts {
  * id : INT (PK)
  --
  * email : VARCHAR(100)
  ip_address : VARCHAR(45)
  attempt_count : INT
  locked_until : TIMESTAMP
}

users ||--o{ registrasi : "closed_by (Notaris)"
users ||--o{ registrasi_history : "user_id"
users ||--o{ audit_log : "user_id"

layanan ||--o{ registrasi : "layanan_id"

workflow_steps ||--o{ registrasi : "current_step_id"
workflow_steps ||--o{ registrasi_history : "from_step_id, to_step_id"
workflow_steps ||--o{ note_templates : "step_id"

registrasi ||--o{ registrasi_history : "registrasi_id"
registrasi ||--o{ kendala : "registrasi_id"
registrasi ||--o{ wa_logs : "registrasi_id"

@enduml
```

### 5.2 Database Schema Summary

| **Table**    | **Records (Est.)** | **Growth Rate** | **Retention** |
| ------------------ | ------------------------ | --------------------- | ------------------- |
| users              | 10                       | Static                | Permanent           |
| layanan            | 20                       | Low                   | Permanent           |
| registrasi         | 500/year                 | 500/year              | Permanent           |
| workflow_steps     | 15                       | Static                | Permanent           |
| registrasi_history | 7500/year                | 15x registrasi        | Permanent           |
| kendala            | 100/year                 | Variable              | Permanent           |
| message_templates  | 15                       | Low                   | Permanent           |
| wa_logs            | 8000/year                | 16x registrasi        | 2 years             |
| audit_log          | 10000/year               | High                  | 5 years             |
| cms_* tables       | 50                       | Low                   | Permanent           |
| login_attempts     | 100/month                | Medium                | 30 days             |

---

## 6. Workflow Diagram

### 6.1 15 Status Workflow

```plantuml
@startuml
skinparam backgroundColor #FAFAFA

title NORA v2.1 - 15 Status Workflow

[*] --> Persyaratan

Persyaratan --> Administrasi
Administrasi --> ValidasiSertifikat
ValidasiSertifikat --> PengecekanSertifikat
PengecekanSertifikat --> PembayaranPajak

PembayaranPajak --> ValidasiPajak
ValidasiPajak --> PenomoranAkta
PenomoranAkta --> Pendaftaran
Pendaftaran --> PembayaranPNBP
PembayaranPNBP --> PemeriksaanPertanahan
PemeriksaanPertanahan --> Perbaikan

Perbaikan --> Selesai
Perbaikan --> Perbaikan
Selesai --> Diserahkan
Diserahkan --> KasusDitutup

[*] --> Batal

note right of PembayaranPajak
  **SAFE POINT**
  Setelah status ini,
  tombol "Batal" disabled
  karena pajak sudah dibayar
end note

note right of Perbaikan
  **ITERATION**
  Dapat kembali ke tahap
  sebelumnya jika perlu revisi
end note

note right of KasusDitutup
  **FINAL STATE**
  Data locked (read-only)
  Auto-cleanup red flags
end note

@enduml
```

### 6.2 Business Rules Summary

| **Rule ID** | **Rule**                   | **Enforcement** | **UC Reference** |
| ----------------- | -------------------------------- | --------------------- | ---------------------- |
| BR-01             | Sequential status progression    | System validation     | UC-06                  |
| BR-02             | Safe point at status 5           | Disable Batal button  | UC-06                  |
| BR-03             | Unique resi number               | Database constraint   | UC-04                  |
| BR-04             | Edit blocked after status 14     | System check          | UC-05                  |
| BR-05             | Only Notaris can finalize        | Role check            | UC-09                  |
| BR-06             | Auto-cleanup red flags           | Transaction trigger   | UC-09                  |
| BR-07             | WA notification on create/update | Async process         | UC-12                  |
| BR-08             | Audit all changes                | Mandatory logging     | All UC                 |
| BR-09             | Password hash BCRYPT             | Application layer     | UC-03                  |
| BR-10             | Session timeout 8 hours          | Session management    | UC-03                  |

---

## 7. Integration Requirements

### 7.1 WhatsApp Gateway Integration

| **Aspect** | **Specification**  |
| ---------------- | ------------------------ |
| Provider         | Fonnte / Wablas / Other  |
| API Type         | REST API (HTTPS)         |
| Authentication   | API Key (Bearer token)   |
| Rate Limit       | 100 messages/hour        |
| Retry Policy     | 3 attempts, 30s interval |
| Timeout          | 10 seconds per request   |
| Message Format   | JSON payload             |
| Error Handling   | Log & queue for manual   |

**API Request Format:**

```json
POST https://api.whatsapp-gateway.com/send
Headers:
  Authorization: Bearer {API_KEY}
  Content-Type: application/json

Body:
{
  "phone": "6281234567890",
  "message": "Template content with variables replaced",
  "countryCode": "62"
}
```

### 7.2 Template Variables

| **Variable**   | **Source Table** | **Source Field** | **Example**     |
| -------------------- | ---------------------- | ---------------------- | --------------------- |
| `[Nama_Klien]`     | registrasi             | nama_klien             | Budi Santoso          |
| `[Nama_Layanan]`   | layanan                | nama_layanan           | Akta Jual Beli        |
| `[Nomor_Resi]`     | registrasi             | nomor_resi             | NP-20240101-001       |
| `[Status_Terbaru]` | workflow_steps         | step_name              | Pengecekan Sertifikat |

---

## 8. User Interface Specifications

### 8.1 Screen Flow Diagram

```plantuml
@startuml
skinparam backgroundColor #FAFAFA
skinparam rectangleBackgroundColor #E3F2FD
skinparam rectangleBorderColor #1565C0
skinparam ArrowColor #2E7D32
skinparam roundCorner 10

title NORA v2.1 - Screen Flow Diagram (Navigation Path)

' Definisi Kotak Area
rectangle "Public Area (Landing Profile)" as Public #f9f9f9 {
  rectangle "Landing Page\n(?gate=home)" as Landing
  rectangle "Tracking Page\n(?gate=lacak)" as Tracking
  rectangle "Login Page\n(?gate=login)" as Login
}

rectangle "Admin Operasional" as Admin #e3f2fd {
  rectangle "Dashboard Registrasi\n(?gate=registrasi)" as AdminDash
  rectangle "Form Tambah/Edit\n(Data Klien)" as RegForm
  rectangle "Detail & Riwayat Berkas" as Detail
  rectangle "Update Status Form\n(HTMX Interface)" as StatusForm
}

rectangle "Notaris (Owner) Control" as Notaris #fff9c4 {
  rectangle "Performance Dashboard\n(?gate=dashboard)" as NotarisDash
  rectangle "CMS Editor\n(Konten & Template)" as CMS
  rectangle "Workflow Config\n(SLA & Behavior)" as CMSWF
  rectangle "Finalisasi\n(?gate=tutup_registrasi)" as Final
}

' Alur Navigasi
Landing -down-> Tracking : Klik "Lacak Berkas"
Landing -down-> Login : Klik "Staf Login"
Tracking -up-> Landing : Kembali

Login -right-> AdminDash : Role = Admin
Login -right-> NotarisDash : Role = Notaris

AdminDash -down-> RegForm : Tambah/Edit
RegForm -up-> AdminDash : Simpan
AdminDash -right-> Detail : Pilih Berkas
Detail -down-> StatusForm : Update Progres
StatusForm -up-> Detail : Simpan (HTMX)

NotarisDash -down-> CMS : Manage Content
NotarisDash -down-> CMSWF : Config Workflow
NotarisDash -down-> Final : Finalisasi
Final -up-> NotarisDash : Selesai

AdminDash -up-> Landing : Logout
NotarisDash -up-> Landing : Logout

@enduml
```

### 8.2 Responsive Design Requirements

| **Breakpoint** | **Width** | **Layout**                 |
| -------------------- | --------------- | -------------------------------- |
| Mobile               | < 768px         | Single column, hamburger menu    |
| Tablet               | 768px - 1024px  | Two columns, collapsible sidebar |
| Desktop              | > 1024px        | Full layout, fixed sidebar       |

---

## 9. Testing Strategy

### 9.1 Test Coverage Requirements

| **Test Type** | **Coverage Target** | **Method** |
| ------------------- | ------------------------- | ---------------- |
| Unit Tests          | 80% code coverage         | PHPUnit          |
| Integration Tests   | All API endpoints         | Postman/Newman   |
| E2E Tests           | Critical user journeys    | Cypress          |
| Performance Tests   | < 2s page load            | Lighthouse CI    |
| Security Tests      | OWASP Top 10              | OWASP ZAP        |

### 9.2 Test Environment

| **Environment** | **Purpose**  | **URL**      |
| --------------------- | ------------------ | ------------------ |
| Development           | Active development | localhost          |
| Staging               | UAT & testing      | staging.nora.local |
| Production            | Live system        | nora.notaris.com   |

---

## 10. Deployment & Release

### 10.1 Deployment Checklist

- [ ] Database migration executed
- [ ] Environment variables configured
- [ ] WhatsApp API key set
- [ ] SSL certificate installed
- [ ] Backup created
- [ ] Smoke tests passed
- [ ] Monitoring configured
- [ ] Rollback plan ready

### 10.2 Release Notes Template

```
Version: 2.1.0
Date: April 2026

New Features:
- Self-service tracking
- 15 status workflow
- WhatsApp automation

Bug Fixes:
- [List fixes]

Known Issues:
- [List issues]

Migration Notes:
- [Database changes]
```

---

## 11. Glossary

| **Term**       | **Definition**                    |
| -------------------- | --------------------------------------- |
| **Resi**       | Nomor registrasi unik (format: NP-xxxx) |
| **Red Flag**   | Tanda kendala pada berkas               |
| **Safe Point** | Batas pembatalan (status 5)             |
| **SLA**        | Service Level Agreement (target hari)   |
| **HTMX**       | Technology untuk partial page refresh   |
| **BCRYPT**     | Password hashing algorithm              |

---

## 12. Approval

| **Role** | **Name**        | **Signature** | **Date** |
| -------------- | --------------------- | ------------------- | -------------- |
| Product Owner  | Sri Anah, S.H., M.Kn. |                     |                |
| Lead Developer |                       |                     |                |
| QA Lead        |                       |                     |                |

---

*Document End - PRD NORA v2.1*
