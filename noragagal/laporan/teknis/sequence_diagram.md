# Sequence Diagram - Sistem Tracking Status Dokumen Notaris

## 1. Sequence Diagram: Tracking Dokumen oleh Klien

### 1.1 Deskripsi

Sequence diagram ini menggambarkan interaksi antara Klien, Browser, System (Controller), Database, dan Security Components dalam proses tracking status dokumen.

```mermaid
sequenceDiagram
    actor Klien
    participant Browser
    participant Controller as Main\Controller
    participant DB as Database
    participant Security as Security Helpers
    participant History as RegistrasiHistory

    Klien->>Browser: Akses /index.php?gate=lacak
    Browser->>Controller: GET tracking()
    Controller->>Browser: Render tracking.php
    
    Klien->>Browser: Input Nomor Registrasi
    Browser->>Controller: POST tracking() searchRegistrasiByNomor()
    Controller->>Security: InputSanitizer::sanitizeGlobal()
    Security-->>Controller: Sanitized input
    
    Controller->>DB: findByNomorRegistrasi(nomor)
    DB-->>Controller: Registrasi data or null
    
    alt Registrasi tidak ditemukan
        Controller->>Browser: JSON: {success: false, message: "Tidak ditemukan"}
        Browser->>Klien: Tampilkan error
    else Registrasi ditemukan
        Controller->>Browser: JSON: {success: true, requires_verification: true}
        Browser->>Klien: Tampilkan form verifikasi 4 digit HP
    end
    
    Klien->>Browser: Input 4 digit HP
    Browser->>Controller: POST verify_tracking() verifyTracking()
    Controller->>Security: Rate limit check
    Security-->>Controller: Allowed or Rate limited
    
    alt Rate limited
        Controller->>Browser: JSON: {success: false, message: "Terlalu banyak request"}
        Browser->>Klien: Error message
    else Allowed
        Controller->>DB: findById(registrasi_id)
        DB-->>Controller: Registrasi + klien data
        
        Controller->>Controller: Extract last 4 digits of HP
        Controller->>Controller: Compare with input
        
        alt Kode tidak match
            Controller->>Security: logSecurityEvent('FAILED_VERIFICATION')
            Security-->>Controller: Logged
            Controller->>Browser: JSON: {success: false, message: "Kode salah"}
            Browser->>Klien: Error message
        else Kode match
            Controller->>Security: generateTrackingToken(id, verification_code)
            Security-->>Controller: Token string
            
            Controller->>DB: UPDATE registrasi SET tracking_token = ?
            DB-->>Controller: Success
            
            Controller->>Browser: JSON: {success: true, data: {token, registrasi_info}}
            Browser->>Klien: Redirect ke detail page dengan token
        end
    end
    
    Klien->>Browser: Akses detail dengan token
    Browser->>Controller: GET detail?token=xxx showRegistrasi()
    Controller->>Security: verifyTrackingToken(token)
    Security-->>Controller: Token data or false
    
    alt Token invalid/expired
        Controller->>Security: logSecurityEvent('INVALID_TOKEN_ACCESS')
        Controller->>Browser: 403 Forbidden
        Browser->>Klien: Error page
    else Token valid
        Controller->>DB: findById(registrasi_id)
        DB-->>Controller: Registrasi lengkap
        
        Controller->>Security: verify token matches DB
        Security-->>Controller: Match confirmed
        
        Controller->>History: getByRegistrasi(registrasi_id)
        History-->>Controller: History array
        
        Controller->>Browser: Render registrasi_detail.php
        Browser->>Klien: Tampilkan progress + history
    end
```

### 1.2 Detail Pesan

| Pesan | Deskripsi | Data |
|-------|-----------|------|
| `findByNomorRegistrasi()` | Cari registrasi by nomor | `SELECT ... WHERE nomor_registrasi = ?` |
| `generateTrackingToken()` | Buat secure token | Base64 + HMAC-SHA256 |
| `verifyTrackingToken()` | Validasi token | Check signature + expiry |
| `getByRegistrasi()` | Load history | `SELECT ... WHERE registrasi_id = ? ORDER BY created_at DESC` |

### 1.3 Security Measures

1. **Input Sanitization**: Semua input disanitasi sebelum diproses
2. **Rate Limiting**: 5 request/menit untuk tracking_verify
3. **Token Expiry**: 24 jam expiration
4. **HMAC Signature**: Token integrity protection
5. **No Phone Exposure**: HP tidak pernah ditampilkan lengkap

---

## 2. Sequence Diagram: Staff Input Registrasi Baru

### 2.1 Deskripsi

Sequence diagram untuk proses create registrasi baru oleh staff.

```mermaid
sequenceDiagram
    actor Staff
    participant Browser
    participant Controller as Dashboard\Controller
    participant Workflow as WorkflowService
    participant DB as Database
    participant Audit as AuditLog
    participant History as RegistrasiHistory
    participant Klien as Klien Entity

    Staff->>Browser: Akses /index.php?gate=registrasi_create
    Browser->>Controller: GET createRegistrasi()
    Controller->>DB: Query layanan list
    DB-->>Controller: Layanan array
    Controller->>Browser: Render registrasi_create.php
    
    Staff->>Browser: Submit form registrasi
    Browser->>Controller: POST registrasi_store() storeRegistrasi()
    Controller->>Controller: CSRF validation
    Controller->>Controller: InputSanitizer::sanitizeGlobal()
    
    Controller->>Controller: Validate status awal
    alt Status tidak valid (bukan 4 status pertama)
        Controller->>Browser: JSON: {success: false, message: "Status awal tidak valid"}
        Browser->>Staff: Error message
    else Status valid
        Controller->>Klien: getOrCreate(nama, hp, email)
        Klien->>DB: SELECT by hp
        DB-->>Klien: Klien existing or null
        
        alt Klien tidak ada
            Klien->>DB: INSERT klien
            DB-->>Klien: klien_id
        else Klien ada
            Klien-->>Controller: klien_id existing
        end
        
        Controller->>Controller: Generate nomor_registrasi
        Controller->>Controller: Generate verification_code
        
        Controller->>Workflow: createRegistrasi(data)
        Workflow->>DB: INSERT registrasi
        DB-->>Workflow: registrasi_id
        
        Workflow->>Audit: log('create', new_value)
        Audit->>DB: INSERT audit_log
        DB-->>Audit: Success
        
        Workflow->>History: create(initial_history)
        History->>DB: INSERT registrasi_history
        DB-->>History: Success
        
        Workflow-->>Controller: registrasi_id
        
        Controller->>Browser: JSON: {success: true, registrasi_id, nomor_registrasi}
        Browser->>Staff: Success + WhatsApp popup
    end
```

### 2.2 GetOrCreate Pattern

```php
// Klien Entity
public function getOrCreate($nama, $hp, $email = null) {
    $existing = $this->findByHp($hp);
    if ($existing) {
        return $existing['id'];
    }
    return $this->create(['nama' => $nama, 'hp' => $hp, 'email' => $email]);
}
```

### 2.3 Generated Data

| Data | Format | Contoh |
|------|--------|--------|
| nomor_registrasi | NP-YYYYMMDD-XXXX | NP-20260326-1234 |
| verification_code | Random string | a1b2c3d4e5f6 |
| tracking_token | Base64.HMAC | ey...xyz.abcd1234 |

---

## 3. Sequence Diagram: Update Status dengan Workflow Validation

### 3.1 Deskripsi

Sequence diagram detail untuk update status dengan validasi WorkflowService.

```mermaid
sequenceDiagram
    actor Staff
    participant Browser
    participant Controller as Dashboard\Controller
    participant Workflow as WorkflowService
    participant Registrasi as Registrasi Entity
    participant Kendala as Kendala Entity
    participant History as RegistrasiHistory
    participant DB as Database
    participant Audit as AuditLog

    Staff->>Browser: Submit update status form
    Browser->>Controller: POST update_status()
    Controller->>Controller: CSRF + Auth check
    Controller->>Workflow: updateStatus(id, newStatus, userId, role, catatan, flagKendala)
    
    Workflow->>Registrasi: findById(registrasi_id)
    Registrasi->>DB: SELECT registrasi by id
    DB-->>Registrasi: Registrasi data
    Registrasi-->>Workflow: Registrasi array
    
    alt Registrasi tidak ditemukan
        Workflow-->>Controller: {success: false, message: "Tidak ditemukan"}
    else Registrasi ditemukan
        Workflow->>Workflow: Check final status (selesai/ditutup/batal)
        alt Status final
            Workflow-->>Controller: {success: false, message: "Status final"}
        else Not final
            Workflow->>Workflow: Check is_locked
            alt Locked
                Workflow-->>Controller: {success: false, message: "Locked"}
            else Not locked
                Workflow->>Workflow: Get current_order & new_order
                
                alt New status = Batal
                    Workflow->>Workflow: Check CANCELLABLE_STATUSES
                    alt Not cancellable (after pajak)
                        Workflow-->>Controller: {success: false, message: "Tidak bisa batal"}
                    else Cancellable
                        Workflow->>Registrasi: updateStatus()
                    end
                else Backward transition
                    Workflow->>Workflow: Check old_status = Perbaikan
                    alt Bukan perbaikan
                        Workflow-->>Controller: {success: false, message: "Tidak bisa mundur"}
                    else Perbaikan (allowed loop)
                        Workflow->>Registrasi: updateStatus()
                    end
                else Forward transition
                    Workflow->>Registrasi: updateStatus()
                end
                
                Registrasi->>DB: UPDATE registrasi SET status = ?
                DB-->>Registrasi: Success
                
                alt Flag kendala changed
                    Workflow->>Kendala: create() or toggleFlag()
                    Kendala->>DB: INSERT/UPDATE kendala
                    DB-->>Kendala: Success
                end
                
                Workflow->>History: create(history_data)
                History->>DB: INSERT registrasi_history
                DB-->>History: Success
                
                Workflow->>Audit: log('update', old_value, new_value)
                Audit->>DB: INSERT audit_log
                DB-->>Audit: Success
                
                Workflow-->>Controller: {success: true, message: "Status updated"}
            end
        end
    end
    
    Controller->>Browser: JSON response
    Browser->>Staff: Success/Error message
```

### 3.4 Validation Matrix

| Scenario | Old Status | New Status | Result | Reason |
|----------|------------|------------|--------|--------|
| Forward | draft | pembayaran_admin | ✅ OK | Normal progress |
| Backward | validasi_sertifikat | draft | ❌ Error | Cannot go backward |
| Cancel Early | draft | batal | ✅ OK | In CANCELLABLE_STATUSES |
| Cancel Late | pembayaran_pajak | batal | ❌ Error | After tax payment |
| Loop Back | perbaikan | pembayaran_pajak | ✅ OK | Perbaikan exception |
| Final Update | selesai | ditutup | ❌ Error | Final status read-only |

---

## 4. Sequence Diagram: Notaris Approval

### 4.1 Deskripsi

Sequence diagram untuk approval workflow oleh notaris.

```mermaid
sequenceDiagram
    actor Notaris
    participant Browser
    participant Controller as Dashboard\Controller
    participant Workflow as WorkflowService
    participant DB as Database
    participant History as RegistrasiHistory
    participant Audit as AuditLog

    Notaris->>Browser: Akses dashboard
    Browser->>Controller: GET dashboard()
    Controller->>DB: Query statistik + recent registrasi
    DB-->>Controller: Data array
    Controller->>Browser: Render index.php (dashboard)
    
    Notaris->>Browser: Klik detail registrasi
    Browser->>Controller: GET registrasi_detail
    Controller->>DB: findById with joins
    DB-->>Controller: Registrasi + klien + layanan
    Controller->>History: getByRegistrasi()
    History-->>Controller: History timeline
    Controller->>Browser: Render registrasi_detail.php
    
    Notaris->>Browser: Submit approval (update status)
    Browser->>Controller: POST update_status()
    
    rect rgb(200, 250, 200)
        note right of Controller: Approval Logic
        Controller->>Workflow: updateStatus()
        Workflow->>DB: Check transisi valid
        DB-->>Workflow: Validation result
        
        alt Transisi valid
            Workflow->>DB: UPDATE status
            Workflow->>History: create()
            Workflow->>Audit: log()
            Workflow-->>Controller: Success
        else Transisi invalid
            Workflow-->>Controller: Error
        end
    end
    
    Controller->>Browser: JSON response
    Browser->>Notaris: Success message
```

---

## 5. Sequence Diagram: Authentication & RBAC

### 5.1 Deskripsi

Sequence diagram untuk authentication dan Role-Based Access Control.

```mermaid
sequenceDiagram
    actor User
    participant Browser
    participant Controller as Auth\Controller
    participant Auth as Auth Entity
    participant RBAC as RBAC Security
    participant DB as Database
    participant Audit as AuditLog

    User->>Browser: Akses /index.php?gate=login
    Browser->>Controller: GET showLoginPage()
    Controller->>Browser: Render login.php
    
    User->>Browser: Submit credentials
    Browser->>Controller: POST login()
    Controller->>Controller: CSRF validation
    Controller->>Controller: InputSanitizer
    
    Controller->>Auth: attemptLogin(username, password)
    Auth->>DB: SELECT by username
    DB-->>Auth: User data (with password_hash)
    
    alt User not found
        Auth-->>Controller: false
        Controller->>Browser: Error: Invalid credentials
    else User found
        Auth->>Auth: password_verify(plain, hash)
        
        alt Password wrong
            Auth-->>Controller: false
            Controller->>Browser: Error: Invalid credentials
        else Password correct
            Auth->>Auth: Generate session fingerprint
            Auth->>Auth: Start secure session
            Auth->>DB: Set session variables
            
            Auth->>Audit: log('login', null, {ip, user_agent})
            Audit->>DB: INSERT audit_log
            DB-->>Audit: Success
            
            Auth-->>Controller: true
            
            Controller->>RBAC: checkRole(required_role)
            RBAC->>RBAC: Check session role
            
            alt Role match
                RBAC-->>Controller: Allowed
                Controller->>Browser: Redirect to dashboard
            else Role mismatch
                RBAC-->>Controller: Forbidden
                Controller->>Browser: 403 Forbidden
            end
        end
    end
```

### 5.2 Session Fingerprinting

```php
// Auth::startSecureSession()
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
$_SESSION['user_fingerprint'] = $fingerprint;

// On each request
if ($_SESSION['user_fingerprint'] !== $currentFingerprint) {
    // Session hijacking detected!
    session_destroy();
}
```

---

## 6. Sequence Diagram: CMS Content Management

### 6.1 Deskripsi

Sequence diagram untuk CMS editing oleh notaris.

```mermaid
sequenceDiagram
    actor Notaris
    participant Browser
    participant Controller as CMS\Controller
    participant Service as CMSEditorService
    participant Media as Media\Controller
    participant DB as Database

    Notaris->>Browser: Akses /index.php?gate=cms_editor
    Browser->>Controller: GET index()
    Controller->>Controller: RBAC::enforce('notaris')
    Controller->>Browser: Render cms_editor_grid.php
    
    Notaris->>Browser: Pilih edit homepage
    Browser->>Controller: GET cms_edit_home
    Controller->>DB: Query cms_pages + sections
    DB-->>Controller: CMS data
    Controller->>Browser: Render cms_editor_beranda.php
    
    Notaris->>Browser: Edit content + upload image
    Browser->>Media: POST cms_upload_image
    Media->>Media: Validate file (size, type)
    
    alt File invalid
        Media->>Browser: JSON: {success: false}
    else File valid
        Media->>Media: Generate secure filename
        Media->>Storage: Move uploaded file
        Media->>Browser: JSON: {success: true, path}
    end
    
    Browser->>Controller: POST cms_update_content
    Controller->>Service: updateContent(section_id, content)
    Service->>DB: UPDATE cms_section_content
    Service->>DB: UPDATE cms_section_items
    DB-->>Service: Success
    Service-->>Controller: Success
    Controller->>Browser: JSON: {success: true}
    Browser->>Notaris: Success message
```

---

## 7. Sequence Diagram: Finalisasi Case

### 7.1 Deskripsi

Sequence diagram untuk finalisasi (tutup) kasus.

```mermaid
sequenceDiagram
    actor Notaris
    participant Browser
    participant Controller as Finalisasi\Controller
    participant Service as FinalisasiService
    participant DB as Database
    participant History as RegistrasiHistory
    participant Kendala as Kendala Entity
    participant Audit as AuditLog

    Notaris->>Browser: Akses /index.php?gate=finalisasi
    Browser->>Controller: GET index()
    Controller->>DB: SELECT WHERE status = 'selesai'
    DB-->>Controller: Registrasi list
    Controller->>Browser: Render finalisasi.php
    
    Notaris->>Browser: Pilih kasus untuk ditutup
    Browser->>Controller: POST tutup_registrasi
    Controller->>Service: tutupRegistrasi(id, notes)
    
    Service->>DB: Check status = 'selesai'
    DB-->>Service: Confirmed
    
    Service->>DB: UPDATE status → 'ditutup'
    Service->>DB: SET finalized_at, finalized_by
    DB-->>Service: Success
    
    Service->>Kendala: deactivateAll(registrasi_id)
    Kendala->>DB: UPDATE kendala SET flag_active = 0
    DB-->>Kendala: Success
    
    Service->>History: create(final_history)
    History->>DB: INSERT registrasi_history
    DB-->>History: Success
    
    Service->>Audit: log('finalize', old, new)
    Audit->>DB: INSERT audit_log
    DB-->>Audit: Success
    
    Service-->>Controller: Success
    Controller->>Browser: Redirect to finalisasi
    Browser->>Notaris: Success message
```

---

## 8. Sequence Diagram: Backup Management

### 8.1 Deskripsi

Sequence diagram untuk backup database.

```mermaid
sequenceDiagram
    actor Notaris
    participant Browser
    participant Controller as Dashboard\Controller
    participant Service as BackupService
    participant FileSystem as Storage/Backups
    participant DB as Database
    participant Audit as AuditLog

    Notaris->>Browser: Akses /index.php?gate=backups
    Browser->>Controller: GET backups()
    Controller->>Controller: RBAC::enforce('notaris')
    Controller->>FileSystem: Scan backup files
    FileSystem-->>Controller: File list
    Controller->>Browser: Render backups.php
    
    Notaris->>Browser: Klik Create Backup
    Browser->>Controller: POST handleBackupPost
    Controller->>Service: createBackup()
    
    Service->>Service: Generate filename (backup_YYYY-MM-DD_HHMMSS.sql)
    Service->>DB: mysqldump command
    DB-->>Service: SQL dump
    Service->>FileSystem: Write SQL file
    FileSystem-->>Service: Success
    
    Service->>Audit: log('backup_create', filename)
    Audit->>DB: INSERT audit_log
    DB-->>Audit: Success
    
    Service-->>Controller: Success
    Controller->>Browser: Refresh file list
    Browser->>Notaris: Success + download link
    
    opt Delete Backup
        Notaris->>Browser: Klik Delete
        Browser->>Controller: POST delete backup
        Controller->>Service: deleteBackup(filename)
        Service->>FileSystem: Delete file
        FileSystem-->>Service: Success
        Service->>Audit: log('backup_delete', filename)
        Audit->>DB: INSERT audit_log
        Service-->>Controller: Success
        Controller->>Browser: Refresh list
    end
```

---

## 9. Kesimpulan

Sequence Diagram yang telah diuraikan mencakup 9 skenario utama:

1. **Tracking Dokumen** - Full flow dari search hingga viewing
2. **Input Registrasi** - Create dengan getOrCreate pattern
3. **Update Status** - Workflow validation detail
4. **Notaris Approval** - Internal workflow
5. **Authentication** - Login + RBAC + session fingerprinting
6. **CMS Management** - Content editing + image upload
7. **Finalisasi** - Tutup kasus dengan auto-deactivate kendala
8. **Backup** - Database backup management

Setiap sequence diagram menunjukkan:
- **Lifeline** yang jelas (aktor, controller, service, database)
- **Pesan** synchronous/asynchronous
- **Alt/Opt** fragments untuk conditional logic
- **Security measures** di setiap critical point

Diagram ini menjadi referensi implementasi dan dokumentasi teknis sistem.
