# Product Requirements Document (PRD)
# Sistem Informasi Tracking Status Dokumen Berbasis Web
# Kantor Notaris Sri Anah, S.H, M.Kn

---

**Dokumen PRD**
- **Versi**: 1.0
- **Tanggal**: April 2026
- **Status**: Draft
- **Author**: Development Team
- **Project**: WEB - Document Tracking System

---

## 1. Executive Summary

### 1.1 Latar Belakang
Kantor Notaris Sri Anah, S.H, M.Kn membutuhkan sistem informasi berbasis web untuk meningkatkan transparansi layanan notaris dalam pengelolaan dan tracking status dokumen. Saat ini, klien tidak memiliki visibilitas yang jelas terhadap progres dokumen mereka, yang menyebabkan ketidakpuasan dan meningkatkan beban kerja staff dalam menangani pertanyaan status dokumen secara manual.

### 1.2 Tujuan
Membangun sistem tracking dokumen real-time yang memungkinkan:
- Klien melacak status dokumen secara mandiri (self-service)
- Staff mengelola workflow perkara secara efisien
- Notaris memonitor dan mengapprove dokumen
- Meningkatkan transparansi dan kepuasan klien

### 1.3 Fitur Utama
1. **Self-Service Tracking Perkara Real-Time** (UC-01)
2. **Dashboard Manajemen Perkara & Workflow Automation** (UC-04, UC-06)
3. **CMS & Backup Management System** (UC-08, UC-09)

---

## 2. Problem Statement

### 2.1 Masalah Saat Ini
| No | Masalah | Dampak |
|----|---------|--------|
| 1 | Klien tidak dapat tracking status dokumen secara mandiri | Meningkatkan beban kerja staff |
| 2 | Tidak ada sistem tracking yang terpusat | Informasi tidak transparan |
| 3 | Update status dilakukan manual | Rentan human error |
| 4 | Tidak ada notifikasi otomatis | Klien tidak informed |
| 5 | Tidak ada backup sistematis | Risiko kehilangan data |
| 6 | CMS tidak terintegrasi | Manajemen konten website sulit |

### 2.2 Solusi yang Ditawarkan
| Masalah | Solusi |
|---------|--------|
| Tidak ada tracking mandiri | Self-service tracking dengan nomor tracking unik |
| Tidak ada sistem terpusat | Dashboard terpusat dengan real-time update |
| Update manual | Workflow automation dengan status transitions |
| Tidak ada notifikasi | Email/SMS notification system |
| Tidak ada backup | Automated backup system |
| CMS tidak terintegrasi | Built-in CMS module |

---

## 3. Stakeholder Analysis

### 3.1 Stakeholder
| Role | Nama/Peran | Kebutuhan |
|------|------------|-----------|
| **Product Owner** | Notaris Sri Anah, S.H, M.Kn | Transparansi, monitoring, laporan |
| **End User (Client)** | Klien/Pencari Perkara | Tracking mandiri, notifikasi, cetak status |
| **End User (Staff)** | Staff Administrasi | Registrasi mudah, update cepat, workflow |
| **Administrator** | IT Admin | Backup, maintenance, user management |
| **Developer** | Development Team | Dokumentasi jelas, requirements terdefinisi |

### 3.2 User Personas

#### Persona 1: Klien (Budi, 35 tahun, Pengusaha)
- **Goal**: Tracking dokumen tanpa harus telepon/kunjungi kantor
- **Pain Point**: Tidak tahu progress dokumen, harus telepon staff
- **Behavior**: Akses website dari mobile, butuh informasi cepat
- **Tech Savvy**: Menengah

#### Persona 2: Staff Administrasi (Siti, 28 tahun)
- **Goal**: Kelola registrasi dan update status dengan efisien
- **Pain Point**: Input data manual, sering ditanya klien tentang status
- **Behavior**: Gunakan desktop, multi-tasking
- **Tech Savvy**: Menengah-tinggi

#### Persona 3: Notaris (Sri Anah, S.H, M.Kn, 45 tahun)
- **Goal**: Monitor semua perkara, approve dokumen, lihat laporan
- **Pain Point**: Tidak ada overview real-time, approval manual
- **Behavior**: Gunakan tablet/laptop, butuh ringkasan cepat
- **Tech Savvy**: Menengah

---

## 4. Functional Requirements

### 4.1 Fitur 1: Self-Service Tracking Perkara Real-Time (UC-01, UC-05, UC-07)

#### 4.1.1 Deskripsi
Klien dapat melacak status dokumen secara real-time menggunakan nomor tracking unik tanpa perlu login.

#### 4.1.2 User Stories
| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-01 | Sebagai klien, saya ingin tracking dokumen dengan nomor tracking | High | Input nomor tracking → tampil detail status |
| US-02 | Sebagai klien, saya ingin melihat timeline progress dokumen | High | Tampil timeline visual dengan tanggal |
| US-03 | Sebagai klien, saya ingin melihat riwayat perubahan status | Medium | Tampil list status change history |
| US-04 | Sebagai klien, saya ingin mencetak bukti status | Medium | Generate PDF dengan detail perkara |
| US-05 | Sebagai klien, saya ingin aktifkan notifikasi email | High | Toggle notifikasi → terima email saat status berubah |
| US-06 | Sebagai klien, saya ingin melihat estimasi waktu selesai | Low | Tampil estimated completion date |

#### 4.1.3 Functional Specifications

**Input:**
- Nomor tracking (format: TRK-YYYY-NNN)
- Validasi: Required, format valid, regex: `^TRK-\d{4}-\d{3}$`

**Proses:**
1. User akses halaman `/tracking`
2. Input nomor tracking pada form
3. Sistem validasi format dengan regex
4. Query database: `SELECT * FROM perkara WHERE tracking_no = ?`
5. Jika ditemukan, query status_log: `SELECT * FROM status_log WHERE perkara_id = ? ORDER BY created_at DESC`
6. Format response: perkara data + timeline + riwayat status
7. Render tampilan dengan timeline visual

**Output:**
- Detail perkara: tracking number, jenis perkara, status saat ini, tanggal registrasi
- Timeline visual progress (step-by-step)
- Riwayat status change (table)
- Estimasi waktu selesai (dari workflow step)
- Tombol cetak PDF (generate PDF dengan library FPDF/dompdf)
- Toggle notifikasi email/SMS (subscribe/unsubscribe)

**Business Rules:**
- Nomor tracking harus unik (format: TRK-YYYY-NNN, auto-increment per tahun)
- Status update real-time (max delay 1 menit dari update staff)
- PDF generation dengan watermark "KANTOR NOTARIS SRI ANAH"
- Notifikasi hanya untuk klien yang subscribe (check `notification_pref` JSON di tabel klien)
- Tracking tanpa login (public access)

**Notification Feature (UC07):**
- Klien dapat mengaktifkan/menonaktifkan notifikasi dari halaman tracking result
- Notification preference disimpan di `klien.notification_pref` JSON: `{"email": true, "sms": false}`
- Trigger notifikasi saat status berubah (dikelola oleh `NotificationService`)
- Channel notifikasi: Email (SMTP/PHPMailer), SMS (Twilio/Local API)
- Template email untuk setiap status change (lihat State Machine Diagram #2)

#### 4.1.4 Flow Diagram
```
[Klien] → [Input Tracking Number] → [Validate Format]
    → [Query Database] → [Format Data] → [Display Result]
    → [Optional: Print PDF / Subscribe Notification]
```

#### 4.1.5 Related UML Diagrams
- Use Case: UC-01, UC-05, UC-07
- Activity: 02-activity-diagram.md (Flow Tracking Real-Time)
- Sequence: 03-sequence-diagram.md (Tracking Dokumen)
- Class: 04-class-diagram.md (TrackingController, TrackingService)
- State Machine: 05-state-machine-diagram.md (Perkara Status Lifecycle)

---

### 4.2 Fitur 2: Dashboard Manajemen Perkara & Workflow Automation (UC-02, UC-03, UC-04, UC-06, UC-10, UC-11, UC-12)

#### 4.2.1 Deskripsi
Dashboard terpusat untuk staff dan notaris dalam mengelola perkara, workflow automation, dan monitoring.

#### 4.2.2 User Stories

**Sub-fitur 2.1: Registrasi Perkara (UC-02)**

| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-07 | Sebagai staff, saya ingin registrasi perkara baru | High | Form registrasi → generate nomor tracking |
| US-08 | Sebagai staff, saya ingin upload dokumen awal | High | Upload multiple files, validasi tipe/size |
| US-09 | Sebagai staff, saya ingin assign workflow otomatis | Medium | Pilih jenis perkara → apply workflow template |
| US-10 | Sebagai sistem, saya ingin generate nomor tracking unik | High | Format: TRK-YYYY-NNN, auto-increment |

**Sub-fitur 2.2: Update Status (UC-03)**

| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-11 | Sebagai staff/notaris, saya ingin update status perkara | High | Pilih status → input catatan → save |
| US-12 | Sebagai staff, saya ingin upload dokumen progress | Medium | Upload dokumen terkait update |
| US-13 | Sebagai sistem, saya ingin trigger notifikasi otomatis | High | Status berubah → kirim email ke klien |

**Sub-fitur 2.3: Dashboard (UC-04, UC-10)**

| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-14 | Sebagai notaris/staff, saya ingin lihat statistik perkara | High | Tampil total, by status, grafik |
| US-15 | Sebagai notaris, saya ingin lihat perkara terbaru | Medium | List 10 perkara dengan update terbaru |
| US-16 | Sebagai notaris, saya ingin generate laporan | Medium | Filter by date → export PDF/Excel |
| US-17 | Sebagai notaris, saya ingin filter by status/jenis | Low | Filter dropdown → refresh data |

**Sub-fitur 2.4: Workflow Automation (UC-06)**

| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-18 | Sebagai notaris, saya ingin buat template workflow | High | Define steps, status, estimasi waktu |
| US-19 | Sebagai sistem, saya ingin auto-apply workflow | Medium | Registrasi → apply workflow berdasarkan jenis |
| US-20 | Sebagai staff, saya ingin lihat progress workflow | Medium | Tampil step-by-step progress |

**Sub-fitur 2.5: Verifikasi Dokumen (UC-12)**

| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-21 | Sebagai notaris, saya ingin verifikasi kelengkapan dokumen | High | Review dokumen → approve/reject |
| US-22 | Sebagai staff, saya ingin lihat status verifikasi | Medium | Tampil verified/unverified status |

**Sub-fitur 2.6: Arsip Digital (UC-11)**

| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-23 | Sebagai staff, saya ingin kelola arsip dokumen | Medium | List, search, download dokumen |
| US-24 | Sebagai staff, saya ingin categorize dokumen | Low | Assign kategori/tag dokumen |

#### 4.2.3 Functional Specifications

**Dashboard Components:**

1. **Statistics Widget**
   - Total perkara (all time): `SELECT COUNT(*) FROM perkara`
   - Perkara aktif by status: `SELECT status, COUNT(*) FROM perkara GROUP BY status`
   - Perkara selesai bulan ini: `SELECT COUNT(*) FROM perkara WHERE MONTH(tanggal_selesai) = MONTH(CURRENT_DATE())`
   - Perkara butuh attention (status: MENUNGGU_VERIFIKASI, REVISI_INTERNAL)

2. **Recent Cases Table**
   - Query: `SELECT * FROM perkara ORDER BY updated_at DESC LIMIT 10`
   - Columns: Tracking No, Klien, Jenis, Status, Last Update, Action
   - Sortable, searchable, paginated (10 items per page)

3. **Activity Log**
   - Query: `SELECT * FROM status_log WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC`
   - Timeline aktivitas hari ini
   - Filter by user, type

4. **Charts**
   - Pie chart: Distribution by status (`SELECT status, COUNT(*) GROUP BY status`)
   - Bar chart: Cases per month (`SELECT MONTH(created_at), COUNT(*) GROUP BY MONTH`)
   - Line chart: Completion trend (`SELECT DATE(tanggal_selesai), COUNT(*) GROUP BY DATE`)

**Workflow Engine:**

```json
{
  "workflow_template": {
    "name": "Akta Pendirian PT",
    "jenis_perkara": "AKTA_PENDIRIAN_PT",
    "steps": [
      {
        "step_number": 1,
        "step_name": "Verifikasi Dokumen",
        "status": "MENUNGGU_VERIFIKASI",
        "estimated_days": 2
      },
      {
        "step_number": 2,
        "step_name": "Proses Drafting",
        "status": "PROSES_NOTARIS",
        "estimated_days": 5
      },
      {
        "step_number": 3,
        "step_name": "Review & Revisi",
        "status": "PROSES_NOTARIS",
        "estimated_days": 3
      },
      {
        "step_number": 4,
        "step_name": "Tanda Tangan",
        "status": "MENUNGGU_TANDA_TANGAN",
        "estimated_days": 2
      },
      {
        "step_number": 5,
        "step_name": "Selesai",
        "status": "SELESAI",
        "estimated_days": 0
      }
    ]
  }
}
```

**Notification Service (UC07):**

```php
// NotificationService.php
class NotificationService {
    public function triggerStatusUpdate($perkara, $newStatus) {
        $klien = $perkara->getKlien();
        $pref = json_decode($klien->notification_pref, true);
        
        if ($pref['email']) {
            $this->sendEmail($klien->email, $this->composeEmail($perkara, $newStatus));
        }
        
        if ($pref['sms']) {
            $this->sendSMS($klien->no_hp, $this->composeSMS($perkara, $newStatus));
        }
        
        $this->logNotification($perkara->id, $newStatus);
    }
}
```

**Notification Email Template:**
```
Subject: [Tracking] Status Dokumen Anda Berubah - {tracking_number}

Kepada Yth. {klien.nama},

Status dokumen Anda dengan nomor tracking {tracking_number} telah berubah:

Jenis Perkara: {jenis_perkara}
Status Lama: {old_status}
Status Baru: {new_status}
Tanggal Update: {updated_at}
Catatan: {notes}

Silakan cek progress dokumen Anda di: {tracking_url}

Hormat kami,
Kantor Notaris Sri Anah, S.H, M.Kn
```

#### 4.2.4 Flow Diagram
```
[Staff Login] → [Dashboard] → [Pilih Registrasi]
    → [Input Data Klien & Perkara] → [Upload Dokumen]
    → [Apply Workflow] → [Generate Tracking Number]
    → [Send Notification] → [Save to Database]
```

#### 4.2.5 Related UML Diagrams
- Use Case: UC-02, UC-03, UC-04, UC-06, UC-10, UC-11, UC-12
- Activity: 02-activity-diagram.md (Flow Registrasi & Update Status)
- Sequence: 03-sequence-diagram.md (Registrasi & Update Status)
- Class: 04-class-diagram.md (RegistrasiController, WorkflowEngine)
- State Machine: 05-state-machine-diagram.md (All state transitions)

---

### 4.3 Fitur 3: CMS & Backup Management System (UC-08, UC-09)

#### 4.3.1 Deskripsi
Content Management System untuk mengelola konten website dan automated backup system untuk keamanan data.

#### 4.3.2 User Stories

**Sub-fitur 3.1: CMS (UC-09)**

| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-25 | Sebagai admin, saya ingin edit halaman profil | Medium | WYSIWYG editor → save content |
| US-26 | Sebagai admin, saya ingin kelola layanan | Medium | CRUD layanan yang ditawarkan |
| US-27 | Sebagai admin, saya ingin upload media/gambar | Medium | Upload images, manage media library |
| US-28 | Sebagai admin, saya ingin kelola FAQ | Low | CRUD FAQ entries |
| US-29 | Sebagai admin, saya ingin preview perubahan | Low | Preview before publish |

**Sub-fitur 3.2: Backup (UC-08)**

| ID | User Story | Priority | Acceptance Criteria |
|----|------------|----------|---------------------|
| US-30 | Sebagai admin, saya ingin backup database manual | High | Click backup → download SQL file |
| US-31 | Sebagai admin, saya ingin schedule backup otomatis | High | Set schedule → auto backup |
| US-32 | Sebagai admin, saya ingin backup file dokumen | Medium | Backup storage files |
| US-33 | Sebagai admin, saya ingin restore dari backup | High | Select backup file → restore |
| US-34 | Sebagai admin, saya ingin lihat riwayat backup | Low | List backup history |

#### 4.3.3 Functional Specifications

**CMS Features:**
- WYSIWYG editor (TinyMCE/CKEditor)
- Page management (create, edit, delete pages)
- Media library (upload, organize images/files)
- Menu management
- SEO settings (meta tags, slug)
- Version control (draft/published)

**Backup Features:**
- Manual backup trigger
- Scheduled backup (daily, weekly, monthly)
- Backup database (MySQL dump)
- Backup files (documents, images)
- Compress backup files (ZIP)
- Store in local/cloud storage
- Backup retention policy (keep last N backups)
- Restore functionality
- Backup verification (integrity check)

**Backup Schedule Configuration:**
```json
{
  "backup_schedule": {
    "database": {
      "frequency": "daily",
      "time": "02:00",
      "retention_days": 30,
      "compression": true,
      "storage": "local_and_cloud"
    },
    "files": {
      "frequency": "weekly",
      "day": "Sunday",
      "time": "03:00",
      "retention_days": 90,
      "compression": true,
      "storage": "local_and_cloud"
    }
  }
}
```

#### 4.3.4 Flow Diagram
```
[Admin Login] → [CMS Dashboard] → [Select Page/Media]
    → [Edit Content] → [Preview] → [Publish]
    
[Admin Login] → [Backup Dashboard] → [Manual Backup]
    → [Select Type (DB/Files)] → [Execute Backup]
    → [Store Backup] → [Log History]
```

#### 4.3.5 Related UML Diagrams
- Use Case: UC-08, UC-09
- Component: 06-component-diagram.md (CMS Module, Backup Module)

---

## 5. Non-Functional Requirements

### 5.1 Performance Requirements

| Requirement | Target | Measurement |
|-------------|--------|-------------|
| Page Load Time | < 3 seconds | Time to First Byte (TTFB) |
| Tracking Query Response | < 2 seconds | API response time |
| Dashboard Load | < 4 seconds | Full page load |
| Concurrent Users | 50 users | Simultaneous active sessions |
| Database Query | < 500ms | Average query time |
| PDF Generation | < 5 seconds | Generate and download |

### 5.2 Security Requirements

| Requirement | Implementation |
|-------------|----------------|
| Authentication | Password hashing (bcrypt), session management |
| Authorization | Role-based access control (RBAC) |
| Data Encryption | HTTPS/TLS, encrypted passwords |
| Input Validation | Server-side validation, sanitization |
| SQL Injection Prevention | Prepared statements, ORM |
| XSS Prevention | Output encoding, CSP headers |
| CSRF Protection | CSRF tokens on forms |
| File Upload Security | File type validation, size limits, malware scan |
| Audit Trail | Log all CRUD operations |
| Session Timeout | 30 minutes idle timeout |
| Password Policy | Min 8 chars, uppercase, lowercase, number, special char |

### 5.3 Usability Requirements

| Requirement | Target |
|-------------|--------|
| Learning Curve | < 30 minutes untuk staff |
| Mobile Responsive | Yes (Bootstrap 5) |
| Browser Support | Chrome, Firefox, Safari, Edge (latest 2 versions) |
| Accessibility | WCAG 2.1 Level AA |
| Language | Bahasa Indonesia |
| Help Documentation | Built-in help, tooltips |

### 5.4 Reliability Requirements

| Requirement | Target |
|-------------|--------|
| Uptime | 99.5% |
| Backup Frequency | Daily (database), Weekly (files) |
| Backup Retention | 30 days (database), 90 days (files) |
| Data Recovery Time | < 4 hours |
| Error Handling | User-friendly error messages |
| Logging | Comprehensive audit log |

### 5.5 Scalability Requirements

| Requirement | Target |
|-------------|--------|
| Max Users | 500 registered users |
| Max Perkara | 10,000 records |
| Max Dokumen | 50,000 files |
| Storage | Expandable (local/cloud) |
| Database | Optimized queries, indexing, caching |

---

## 6. System Architecture

### 6.1 Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Frontend** | HTML5, CSS3, JavaScript, Bootstrap 5 | Latest |
| **Backend** | PHP (Laravel Framework) | 8.x / 10.x |
| **Database** | MySQL | 8.0 |
| **Cache** | Redis | 6.x |
| **Web Server** | Apache / Nginx | 2.4 / 1.20 |
| **Email** | PHPMailer / SMTP | Latest |
| **SMS** | Twilio / Local Gateway API | Latest |
| **Storage** | Local / AWS S3 | Latest |

### 6.2 Database Schema (High-Level)

```sql
-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    role ENUM('CLIENT', 'STAFF', 'NOTARIS', 'ADMIN'),
    no_hp VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Klien Table
CREATE TABLE klien (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT FOREIGN KEY REFERENCES users(id),
    nama_lengkap VARCHAR(255),
    nik VARCHAR(16) UNIQUE,
    alamat TEXT,
    email VARCHAR(255),
    no_hp VARCHAR(20),
    notification_pref JSON,
    created_at TIMESTAMP
);

-- Perkara Table
CREATE TABLE perkara (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tracking_number VARCHAR(50) UNIQUE NOT NULL,
    jenis_perkara ENUM(...),
    status ENUM(...),
    klien_id INT FOREIGN KEY REFERENCES klien(id),
    staff_id INT FOREIGN KEY REFERENCES users(id),
    notaris_id INT FOREIGN KEY REFERENCES users(id),
    tanggal_registrasi DATE,
    tanggal_selesai DATE,
    deskripsi TEXT,
    biaya DECIMAL(15,2),
    workflow_id INT FOREIGN KEY REFERENCES workflow(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Status Log Table
CREATE TABLE status_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    perkara_id INT FOREIGN KEY REFERENCES perkara(id),
    status ENUM(...),
    notes TEXT,
    changed_by INT FOREIGN KEY REFERENCES users(id),
    changed_at TIMESTAMP
);

-- Dokumen Table
CREATE TABLE dokumen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    perkara_id INT FOREIGN KEY REFERENCES perkara(id),
    nama_dokumen VARCHAR(255),
    jenis_dokumen ENUM(...),
    file_url VARCHAR(500),
    file_size INT,
    uploaded_by INT FOREIGN KEY REFERENCES users(id),
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by INT FOREIGN KEY REFERENCES users(id),
    uploaded_at TIMESTAMP
);

-- Workflow Table
CREATE TABLE workflow (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255),
    jenis_perkara ENUM(...),
    steps JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT FOREIGN KEY REFERENCES users(id),
    created_at TIMESTAMP
);

-- Audit Log Table
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    perkara_id INT,
    action VARCHAR(100),
    user_id INT FOREIGN KEY REFERENCES users(id),
    metadata JSON,
    created_at TIMESTAMP
);
```

---

## 7. User Interface Requirements

### 7.1 Screen List

| Screen ID | Screen Name | User Role | Description |
|-----------|-------------|-----------|-------------|
| SCR-01 | Landing Page | All | Homepage dengan info notaris |
| SCR-02 | Tracking Page | Client | Input nomor tracking, lihat status |
| SCR-03 | Tracking Result | Client | Detail status, timeline, history |
| SCR-04 | Login Page | Staff, Notaris, Admin | Authentication |
| SCR-05 | Dashboard (Staff) | Staff | Registrasi, update, arsip |
| SCR-06 | Dashboard (Notaris) | Notaris | Monitoring, approval, laporan |
| SCR-07 | Dashboard (Admin) | Admin | User management, CMS, backup |
| SCR-08 | Registrasi Page | Staff | Form registrasi perkara baru |
| SCR-09 | Update Status Page | Staff, Notaris | Update status, upload dokumen |
| SCR-10 | Detail Perkara | Staff, Notaris | Lihat detail lengkap perkara |
| SCR-11 | Workflow Management | Notaris | Create/edit workflow templates |
| SCR-12 | Laporan Page | Notaris | Generate dan download laporan |
| SCR-13 | CMS Editor | Admin | Edit website content |
| SCR-14 | Backup Management | Admin | Manual/scheduled backup |
| SCR-15 | User Management | Admin | CRUD users, assign roles |

### 7.2 Wireframe References
- Lihat folder `/html-mockups` untuk prototype HTML

---

## 8. Integration Requirements

### 8.1 External Integrations

| Integration | Purpose | Method |
|-------------|---------|--------|
| Email Service | Notifikasi status | SMTP (PHPMailer) |
| SMS Gateway | Notifikasi SMS | REST API |
| Cloud Storage | Backup & document storage | AWS S3 API |
| WhatsApp API (Optional) | Notifikasi WhatsApp | REST API |

### 8.2 Data Migration

| Data Source | Target | Method |
|-------------|--------|--------|
| Existing records (Excel/Paper) | Database | Manual entry via registrasi |
| Old documents | File storage | Upload via arsip digital |

---

## 9. Testing Requirements

### 9.1 Test Types

| Test Type | Scope | Tools |
|-----------|-------|-------|
| Unit Testing | Controllers, Services, Models | PHPUnit |
| Integration Testing | API endpoints, database | PHPUnit, Postman |
| Functional Testing | User workflows | Selenium, Cypress |
| Performance Testing | Load, stress | JMeter, Apache Bench |
| Security Testing | Vulnerability scan | OWASP ZAP |
| UAT | User acceptance | Manual testing |

### 9.2 Test Coverage Target
- Unit tests: > 80% code coverage
- Integration tests: All API endpoints
- Functional tests: All user stories

---

## 10. Deployment Requirements

### 10.1 Environment

| Environment | Purpose | URL |
|-------------|---------|-----|
| Development | Development & testing | http://localhost:8000 |
| Staging | UAT & final testing | http://staging.notaris-app.local |
| Production | Live system | https://notaris-sri-anah.com |

### 10.2 Deployment Strategy
- Version control: Git (GitHub/GitLab)
- CI/CD: GitHub Actions / GitLab CI
- Deployment: Manual approval for production
- Rollback: Git revert + database backup restore

### 10.3 Server Requirements

| Component | Specification |
|-----------|---------------|
| **CPU** | 2 cores minimum |
| **RAM** | 4 GB minimum |
| **Storage** | 50 GB SSD |
| **OS** | Ubuntu 20.04 LTS / Windows Server |
| **Web Server** | Apache 2.4 / Nginx |
| **PHP** | 8.0+ with extensions (openssl, pdo, mbstring, etc.) |
| **Database** | MySQL 8.0 |
| **Cache** | Redis 6.x |

---

## 11. Project Timeline

### 11.1 Milestones

| Phase | Duration | Deliverables |
|-------|----------|--------------|
| **Phase 1: Planning & Design** | Week 1-2 | PRD, UML diagrams, UI mockups |
| **Phase 2: Development - Core** | Week 3-6 | Authentication, tracking, dashboard |
| **Phase 3: Development - Features** | Week 7-9 | Workflow, notification, CMS |
| **Phase 4: Testing** | Week 10 | Unit tests, integration tests, UAT |
| **Phase 5: Deployment** | Week 11 | Production deployment, training |
| **Phase 6: Maintenance** | Ongoing | Bug fixes, enhancements |

### 11.2 Sprint Breakdown

**Sprint 1 (Week 1-2):**
- Setup project structure
- Database design
- Authentication module
- Tracking page (UC-01)

**Sprint 2 (Week 3-4):**
- Registrasi module (UC-02)
- Update status module (UC-03)
- Dashboard basic (UC-04)

**Sprint 3 (Week 5-6):**
- Workflow automation (UC-06)
- Notification service (UC-07)
- Verifikasi dokumen (UC-12)

**Sprint 4 (Week 7-8):**
- Laporan module (UC-10)
- Arsip digital (UC-11)
- CMS module (UC-09)

**Sprint 5 (Week 9-10):**
- Backup module (UC-08)
- Testing & bug fixes
- UAT

**Sprint 6 (Week 11):**
- Production deployment
- User training
- Documentation

---

## 12. Risk Management

### 12.1 Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Data loss | Low | High | Regular backup, RAID storage |
| Security breach | Medium | High | Security testing, HTTPS, RBAC |
| User adoption low | Medium | Medium | Training, user-friendly UI |
| Scope creep | Medium | Medium | Strict PRD, change request process |
| Timeline delay | Medium | Medium | Agile methodology, buffer time |
| Server downtime | Low | High | Monitoring, backup server |
| Integration failure | Low | Medium | Fallback mechanism, error handling |

---

## 13. Success Metrics

### 13.1 KPIs

| Metric | Target | Measurement |
|--------|--------|-------------|
| User Adoption Rate | > 80% | Registered clients / Total clients |
| Tracking Usage | > 70% | Tracking queries / Total cases |
| Customer Satisfaction | > 4.0/5.0 | Survey score |
| Staff Efficiency | +30% | Cases handled per day |
| Error Rate | < 2% | Errors / Total operations |
| System Uptime | > 99.5% | Monitoring tool |
| Page Load Time | < 3 seconds | Performance monitoring |

---

## 14. Glossary

| Term | Definition |
|------|------------|
| **Perkara** | Kasus/dokumen yang ditangani notaris |
| **Tracking Number** | Nomor unik untuk lacak status (TRK-YYYY-NNN) |
| **Workflow** | Template alur kerja otomatis |
| **Status Log** | History perubahan status |
| **RBAC** | Role-Based Access Control |
| **CMS** | Content Management System |
| **UAT** | User Acceptance Testing |
| **WYSIWYG** | What You See Is What You Get |

---

## 15. References

### 15.1 Related Documents
- **UML Diagrams (HTML)**: `/html-mockups/01-uml-diagrams.html` - Render all Mermaid diagrams
- **Use Case Diagram**: `/uml/01-use-case-diagram.md` - UC01-UC12 dengan aktor & relasi
- **Activity Diagram**: `/uml/02-activity-diagram.md` - Flow tracking, registrasi, update status
- **Sequence Diagram**: `/uml/03-sequence-diagram.md` - Interaksi objek tracking & registrasi
- **Class Diagram**: `/uml/04-class-diagram.md` - Entity, Controller, Service, Repository
- **State Machine Diagram**: `/uml/05-state-machine-diagram.md` - Status perkara lifecycle & notifikasi
- **Component Diagram**: `/uml/06-component-diagram.md` - Arsitektur sistem & tech stack
- **Database Schema**: `database/schema.sql`
- **API Documentation**: `docs/api.md`

### 15.2 Cross-Reference UML - PRD

| PRD Section | UML Diagram | Use Case |
|-------------|-------------|----------|
| Fitur 1: Self-Service Tracking | Activity Diagram #1, Sequence Diagram #1 | UC01, UC05, UC07 |
| Fitur 2.1: Registrasi Perkara | Activity Diagram #2, Sequence Diagram #2 | UC02, UC06 |
| Fitur 2.2: Update Status | Activity Diagram #3, Sequence Diagram #3 | UC03, UC12 |
| Fitur 2.3: Dashboard | Sequence Diagram #4 | UC04, UC10 |
| Fitur 2.4: Workflow Automation | Activity Diagram #2, Class Diagram | UC06 |
| Fitur 2.5: Verifikasi Dokumen | State Machine Diagram | UC12 |
| Fitur 3.1: CMS | Component Diagram | UC09 |
| Fitur 3.2: Backup | Component Diagram | UC08 |
| Status Perkara | State Machine Diagram #1 | All UC |
| Notifikasi | State Machine Diagram #2, Sequence Diagram | UC07 |
| Arsitektur Sistem | Component Diagram, Class Diagram | All UC |

### 15.3 Standards
- ISO/IEC 25010: Software Quality Requirements
- OWASP Top 10: Security Guidelines
- WCAG 2.1: Accessibility Guidelines
- UML 2.5: Unified Modeling Language Standard

---

## 16. Approval

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Product Owner | Notaris Sri Anah, S.H, M.Kn | | |
| Project Manager | | | |
| Lead Developer | | | |
| QA Lead | | | |

---

**Dokumen PRD - Versi 1.0**
**Terakhir diupdate: April 2026**
