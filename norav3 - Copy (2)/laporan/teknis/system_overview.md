# System Overview - Sistem Tracking Status Dokumen Notaris

## 1. Ringkasan Eksekutif

Sistem Informasi Tracking Status Dokumen adalah aplikasi berbasis web yang dikembangkan untuk Kantor Notaris Sri Anah, S.H., M.Kn. dengan tujuan meningkatkan transparansi layanan dan efisiensi proses dokumen.

### 1.1 Fokus Utama Sistem

```mermaid
flowchart LR
    subgraph "Tiga Pilar Utama"
        A[Transparansi<br/>Tracking Real-time]
        B[Workflow<br/>Internal Terstruktur]
        C[CMS &<br/>Manajemen Data]
    end
    
    A --> D[Kepuasan Klien]
    B --> E[Efisiensi Staff]
    C --> F[Branding Digital]
```

---

## 2. Transparansi - Tracking Real-time

### 2.1 Deskripsi

Sistem menyediakan portal publik yang memungkinkan klien melacak status dokumen mereka secara mandiri, 24/7, tanpa perlu menghubungi kantor notaris.

### 2.2 Fitur Utama

| Fitur | Deskripsi | Benefit |
|-------|-----------|---------|
| **Search by Nomor Registrasi** | Klien input nomor registrasi untuk mencari dokumen | Akses cepat tanpa login |
| **Verifikasi 4 Digit HP** | Autentikasi menggunakan 4 digit terakhir nomor HP | Security tanpa password |
| **Progress Bar Visual** | Tampilan 14 status dengan progress bar horizontal | Mudah dipahami |
| **Estimasi Waktu** | Setiap status menampilkan estimasi penyelesaian | Kepastian waktu |
| **Process Log** | Timeline riwayat perubahan status | Transparansi penuh |

### 2.3 Security Measures

```mermaid
flowchart TD
    A[Request Tracking] --> B[Rate Limiting Check]
    B --> C{Within Limit?}
    C -->|No| D[429 Too Many Requests]
    C -->|Yes| E[Search Database]
    E --> F{Found?}
    F -->|No| G[Generic Error]
    F -->|Yes| H[Require Verification]
    H --> I[Verify 4 Digit HP]
    I --> J{Match?}
    J -->|No| K[Log Failed Attempt]
    J -->|Yes| L[Generate Token]
    L --> M[Token with 24h Expiry]
    M --> N[HMAC-SHA256 Signature]
    N --> O[Grant Access]
```

**Keamanan Tracking:**
- **No Phone Exposure**: Nomor HP lengkap tidak pernah ditampilkan
- **Rate Limiting**: 5 percobaan verifikasi per menit
- **Token Expiry**: Tracking token expired setelah 24 jam
- **HMAC Signature**: Token integrity protection
- **Failed Attempt Logging**: Deteksi brute force

### 2.4 User Experience

**Before Implementation:**
```
Klien → Telepon Kantor → Staff Cari Manual → Info Status (mungkin outdated)
Time: 5-10 menit per inquiry
Availability: Jam kerja saja
```

**After Implementation:**
```
Klien → Buka Website → Input Nomor → Verifikasi → Lihat Status Real-time
Time: < 1 menit
Availability: 24/7
```

---

## 3. Workflow - Internal Terstruktur

### 3.1 Deskripsi

Sistem mengimplementasikan workflow engine dengan 14 status terdefinisi yang mencerminkan proses notaris sebenarnya, dengan validasi transisi yang ketat.

### 3.2 Status Workflow (14 Status)

```mermaid
flowchart LR
    subgraph "Cancellable Zone"
        S1[draft] --> S2[pembayaran_admin]
        S2 --> S3[validasi_sertifikat]
        S3 --> S4[pencecekan_sertifikat]
    end
    
    S4 --> S5[pembayaran_pajak]
    
    subgraph "Non-Cancellable Zone"
        S5 --> S6[validasi_pajak]
        S6 --> S7[penomoran_akta]
        S7 --> S8[pendaftaran]
        S8 --> S9[pembayaran_pnbp]
        S9 --> S10[pemeriksaan_bpn]
        S10 --> S11[perbaikan]
    end
    
    S11 --> S12[selesai]
    S12 --> S13[diserahkan]
    S13 --> S14[ditutup]
    
    S11 -.->|Batal| S15[batal]
    S1 -.->|Batal| S15
    S2 -.->|Batal| S15
    S3 -.->|Batal| S15
    S4 -.->|Batal| S15
    
    style S5 fill:#ff9999
    style S15 fill:#ff6666
```

### 3.3 Business Rules Enforcement

| Rule | Implementation | Rationale |
|------|----------------|-----------|
| **Tidak Bisa Mundur** | WorkflowService validates order | Progress fisik tidak bisa di-undo |
| **Batas Pembatalan** | CANCELLABLE_STATUSES array | Setelah pajak, ada konsekuensi hukum |
| **Perbaikan Loop** | Special case for perbaikan | Koreksi BPN boleh mundur |
| **Lock Mechanism** | is_locked flag | Sensitif cases perlu proteksi |
| **Final Status Read-Only** | Check selesai/ditutup/batal | Prevent invalid updates |

### 3.4 WorkflowService Architecture

```mermaid
flowchart TD
    A[Update Status Request] --> B[WorkflowService::updateStatus]
    B --> C[Load Registrasi]
    C --> D{Exists?}
    D -->|No| E[Error Not Found]
    D -->|Yes| F{Final Status?}
    F -->|Yes| G[Error Read-Only]
    F -->|No| H{Locked?}
    H -->|Yes| I[Error Locked]
    H -->|No| J[Validate Transisi]
    J --> K{Valid?}
    K -->|No| L[Error Invalid Transition]
    K -->|Yes| M[Update Database]
    M --> N[Handle Kendala Flag]
    N --> O[Save History]
    O --> P[Log Audit]
    P --> Q[Success]
```

---

## 4. CMS & Manajemen Data

### 4.1 Deskripsi

Content Management System terintegrasi untuk mengelola konten homepage, template pesan, dan pengaturan aplikasi.

### 4.2 CMS Components

```mermaid
flowchart TD
    subgraph "CMS Modules"
        A[CMS Editor]
        B[Media Manager]
        C[Template Manager]
        D[Settings]
    end
    
    A --> E[cms_pages]
    A --> F[cms_page_sections]
    A --> G[cms_section_content]
    A --> H[cms_section_items]
    
    B --> I[Image Upload]
    I --> J[Secure Filename]
    J --> K[/storage/images/]
    
    C --> L[message_templates]
    C --> M[note_templates]
    
    D --> N[App Settings]
```

### 4.3 Manageable Content

| Content Type | Editable By | Storage |
|--------------|-------------|---------|
| Homepage Hero | Notaris | cms_section_content |
| Layanan List | Notaris | cms_section_items + layanan table |
| Testimoni | Notaris | cms_section_items |
| WhatsApp Templates | Notaris | message_templates |
| Note Templates | Notaris | note_templates |
| App Settings | Notaris | Database/Config |

### 4.4 Image Upload Security

```php
// Security measures for image upload:
1. Max size: 5MB
2. Allowed types: jpg, jpeg, png, pdf
3. Secure filename: img_<random_hex>.ext
4. Storage: /public/assets/images/ (outside web root for originals)
5. Serving: Via image.php dengan token validation
```

---

## 5. Arsitektur Sistem

### 5.1 High-Level Architecture

```mermaid
flowchart TD
    subgraph "Client Layer"
        A[Klien Browser]
        B[Staff Browser]
        C[Notaris Browser]
    end
    
    subgraph "Web Server (Apache)"
        D[.htaccess Rewrite]
        E[public/index.php]
    end
    
    subgraph "Application Layer"
        F[Router]
        G[Controllers]
        H[Services]
        I[Entities]
    end
    
    subgraph "Security Layer"
        J[Auth]
        K[RBAC]
        L[CSRF]
        M[InputSanitizer]
    end
    
    subgraph "Data Layer"
        N[(MySQL Database)]
    end
    
    A --> D
    B --> D
    C --> D
    
    D --> E
    E --> F
    F --> G
    G --> H
    H --> I
    I --> N
    
    E --> J
    E --> K
    E --> L
    E --> M
```

### 5.2 Design Patterns

| Pattern | Implementation | Location |
|---------|----------------|----------|
| **Front Controller** | All requests through public/index.php | public/index.php |
| **Singleton** | Database connection | App\Adapters\Database |
| **Repository** | Entity classes for data access | App\Domain\Entities\* |
| **Service Layer** | Business logic | App\Services\* |
| **Domain-Driven Design** | Entities with business logic | App\Domain\Entities |
| **RBAC** | Permission mapping | App\Security\RBAC |
| **Query-Parameter Routing** | ?gate=xxx routing | config/routes.php |

---

## 6. Security Architecture

### 6.1 Seven-Layer Security

```mermaid
flowchart LR
    subgraph "Security Layers"
        L1[1. Input Sanitization]
        L2[2. CSRF Protection]
        L3[3. Session Hijacking Prevention]
        L4[4. Rate Limiting]
        L5[5. RBAC]
        L6[6. SQL Injection Prevention]
        L7[7. XSS Prevention]
    end
    
    L1 --> L2 --> L3 --> L4 --> L5 --> L6 --> L7
```

### 6.2 Security Implementation

| Layer | Implementation | File |
|-------|----------------|------|
| Input Sanitization | InputSanitizer::sanitizeGlobal() | app/Security/InputSanitizer.php |
| CSRF Protection | CSRF::token(), CSRF::validate() | app/Security/CSRF.php |
| Session Security | Auth::startSecureSession() | app/Security/Auth.php |
| Rate Limiting | RateLimiter::check() | app/Security/RateLimiter.php |
| RBAC | RBAC::enforce() | app/Security/RBAC.php |
| SQL Injection | Prepared statements only | app/Adapters/Database.php |
| XSS Prevention | htmlspecialchars() in View | app/Core/View.php |

---

## 7. Database Schema Overview

### 7.1 Core Tables

```mermaid
flowchart TD
    subgraph "User Management"
        U1[users]
        U2[audit_log]
    end
    
    subgraph "Registration Core"
        R1[registrasi]
        R2[klien]
        R3[layanan]
        R4[registrasi_history]
    end
    
    subgraph "Workflow"
        W1[kendala]
    end
    
    subgraph "CMS"
        C1[cms_pages]
        C2[cms_page_sections]
        C3[cms_section_content]
        C4[cms_section_items]
        C5[message_templates]
        C6[note_templates]
    end
    
    R1 --> R2
    R1 --> R3
    R1 --> R4
    R1 --> W1
    R1 --> U2
```

### 7.2 Key Relationships

| Table | Foreign Keys | Purpose |
|-------|--------------|---------|
| registrasi | klien_id, layanan_id | Main registration record |
| registrasi_history | registrasi_id, user_id | Immutable history ledger |
| audit_log | user_id, registrasi_id | Security audit trail |
| kendala | registrasi_id | Obstacle flags |
| cms_page_sections | page_id | CMS structure |
| cms_section_content | section_id | CMS content values |
| cms_section_items | section_id | CMS items (buttons, cards) |

---

## 8. Performance Considerations

### 8.1 Caching Strategy

| Component | Cache TTL | Storage |
|-----------|-----------|---------|
| Homepage CMS | 1 hour | Memory/Database |
| Tracking Search | 5 minutes | Session |
| User Session | 2 hours | PHP Session |
| Rate Limit Data | 1 minute | File (storage/cache/ratelimit/) |

### 8.2 Database Optimization

```sql
-- Indexed columns for performance
CREATE INDEX idx_registrasi_nomor ON registrasi(nomor_registrasi);
CREATE INDEX idx_registrasi_status ON registrasi(status);
CREATE INDEX idx_registrasi_token ON registrasi(tracking_token);
CREATE INDEX idx_history_registrasi ON registrasi_history(registrasi_id);
CREATE INDEX idx_audit_user ON audit_log(user_id);
```

---

## 9. Deployment Architecture

### 9.1 Server Requirements

| Component | Requirement |
|-----------|-------------|
| Web Server | Apache 2.4+ with mod_rewrite |
| PHP | 7.4+ (tested on 8.x) |
| Database | MySQL 5.7+ / MariaDB 10.4+ |
| SSL | Recommended for production |

### 9.2 Directory Permissions

```
/storage/       - 775 (writable by web server)
/storage/logs/  - 775
/storage/cache/ - 775
/public/        - 755
/app/           - 755
/config/        - 644 (database credentials)
```

---

## 10. Monitoring & Audit

### 10.1 Audit Log Coverage

| Action | Logged To | Data Captured |
|--------|-----------|---------------|
| User Login/Logout | audit_log | IP, timestamp, user |
| Create Registrasi | audit_log + registrasi_history | Full data |
| Update Status | audit_log + registrasi_history | Old/new status |
| User CRUD | audit_log | Username, role changes |
| Backup Delete | audit_log | Filename |
| Failed Verification | security.log | IP, attempted code |

### 10.2 Dashboard Metrics

**Dashboard menampilkan:**
- Total registrasi
- Registrasi aktif
- Selesai bulan ini
- Batal bulan ini
- Registrasi dengan flag kendala
- Recent activity

---

## 11. Kesimpulan

Sistem Tracking Status Dokumen Notaris adalah solusi komprehensif yang mencakup:

1. **Transparansi** - Portal tracking 24/7 untuk klien
2. **Workflow** - 14 status dengan validasi business rules ketat
3. **CMS** - Manajemen konten fleksibel untuk notaris
4. **Security** - 7-layer security architecture
5. **Audit** - Complete audit trail untuk semua aksi penting

Sistem ini production-ready dengan fokus pada domain notaris Indonesia, mengikuti praktik terbaik pengembangan web modern dan security standards.
