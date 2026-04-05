# Sequence Diagram - Sistem Tracking Status Dokumen Kantor Notaris

## Deskripsi
Diagram sequence ini menggambarkan interaksi antar objek berdasarkan urutan waktu.

## 1. Sequence Diagram - Tracking Real-Time (UC01)

```mermaid
sequenceDiagram
    autonumber
    actor K as Klien
    participant UI as Web Interface
    participant Ctrl as TrackingController
    participant Svc as TrackingService
    participant DB as Database
    participant Notif as NotificationService

    K->>UI: 1. Akses /tracking
    UI->>K: 2. Tampilkan form tracking
    
    K->>UI: 3. Input tracking number (TRK-2026-001)
    UI->>Ctrl: 4. POST /api/tracking {number}
    
    Ctrl->>Ctrl: 5. validateFormat(number)
    
    alt Format valid
        Ctrl->>Svc: 6. getPerkaraByTrackingNumber(number)
        Svc->>DB: 7. SELECT * FROM perkara WHERE tracking_no=?
        DB-->>Svc: 8. Return perkara data
        
        alt Data ditemukan
            Svc->>DB: 9. SELECT * FROM status_log WHERE perkara_id=?
            DB-->>Svc: 10. Return status history
            
            Svc->>Svc: 11. formatPerkaraData()
            Svc->>Svc: 12. buildTimeline(history)
            Svc-->>Ctrl: 13. Return {perkara, timeline}
            
            Ctrl-->>UI: 14. JSON response
            UI->>K: 15. Render tracking result
            
            opt Notifikasi tersedia
                Ctrl->>Notif: 16. checkSubscription(klien_id)
                Notif-->>Ctrl: 17. Return subscription status
                UI->>K: 18. Show notification toggle
            end
        else Data tidak ditemukan
            Svc-->>Ctrl: Return null
            Ctrl-->>UI: Return error "Data tidak ditemukan"
            UI->>K: Show error message
        end
    else Format tidak valid
        Ctrl-->>UI: Return validation error
        UI->>K: Show validation error
    end
```

## 2. Sequence Diagram - Registrasi Perkara (UC02, UC06)

```mermaid
sequenceDiagram
    autonumber
    actor Staff
    participant UI as Web Interface
    participant Auth as AuthController
    participant Ctrl as RegistrasiController
    participant Svc as RegistrasiService
    participant WF as WorkflowEngine
    participant DB as Database
    participant Storage as File Storage
    participant Notif as NotificationService

    Staff->>UI: 1. Login (email, password)
    UI->>Auth: 2. POST /api/login
    Auth->>DB: 3. SELECT * FROM users WHERE email=?
    DB-->>Auth: 4. Return user
    Auth->>Auth: 5. verifyPassword()
    Auth-->>UI: 6. Return token + role
    UI->>Staff: 7. Dashboard Staff
    
    Staff->>UI: 8. Klik "Registrasi Baru"
    UI->>Staff: 9. Show form
    
    Staff->>UI: 10. Input data klien
    Staff->>UI: 11. Input jenis perkara
    Staff->>UI: 12. Upload dokumen
    UI->>Storage: 13. POST /api/upload
    Storage-->>UI: 14. Return file URLs
    
    Staff->>UI: 15. Submit form
    UI->>Ctrl: 16. POST /api/registrasi
    Ctrl->>Ctrl: 17. validateInput()
    
    Ctrl->>Svc: 18. createPerkara(data, files)
    Svc->>Svc: 19. generateTrackingNumber()
    Svc->>DB: 20. INSERT INTO perkara
    DB-->>Svc: 21. Return perkara_id
    
    Svc->>DB: 22. INSERT INTO status_log (status awal)
    
    Svc->>WF: 23. getWorkflowByJenis(jenis_perkara)
    
    alt Workflow ditemukan
        WF-->>Svc: 24. Return workflow template
        Svc->>Svc: 25. applyWorkflow(perkara_id, workflow)
        Svc->>DB: 26. UPDATE perkara SET workflow_id=?
    else Workflow tidak ditemukan
        Svc->>DB: Set status "Menunggu Notaris"
    end
    
    Svc->>Notif: 27. sendRegistrationEmail(klien.email)
    Notif-->>Svc: 28. Email sent
    
    Svc-->>Ctrl: 29. Return {success, tracking_number}
    Ctrl-->>UI: 30. JSON response
    UI->>Staff: 31. Show success + tracking number
```

## 3. Sequence Diagram - Update Status & Notifikasi (UC03, UC07)

```mermaid
sequenceDiagram
    autonumber
    actor N as Notaris/Staff
    participant UI as Web Interface
    participant Ctrl as UpdateStatusController
    participant Svc as UpdateStatusService
    participant DB as Database
    participant Notif as NotificationService
    participant Log as AuditLog

    N->>UI: 1. Pilih perkara
    UI->>DB: 2. GET /api/perkara/:id
    DB-->>UI: 3. Return perkara detail
    UI->>N: 4. Show detail + form update
    
    N->>UI: 5. Pilih status baru
    N->>UI: 6. Input catatan
    N->>UI: 7. Upload dokumen (optional)
    Staff->>UI: 8. Submit update
    
    UI->>Ctrl: 9. PUT /api/perkara/:id/status
    Ctrl->>Ctrl: 10. validateTransition(oldStatus, newStatus)
    
    alt Status transition valid
        Ctrl->>Svc: 11. updateStatus(perkara_id, new_status, notes)
        
        alt Status = FINAL
            Svc->>UI: 12. Request notaris approval
            UI->>N: 13. Show approval dialog
            N->>UI: 14. Approve/Reject
            
            alt Approved
                Svc->>DB: 15. UPDATE perkara SET status='FINAL'
            else Rejected
                Svc-->>Ctrl: Return "Revisi required"
                Ctrl-->>UI: Return to form
            end
        else Status intermediate
            Svc->>DB: 15. UPDATE perkara SET status=?
        end
        
        Svc->>DB: 16. INSERT INTO status_log
        Svc->>Log: 17. logAudit(user_id, action, metadata)
        
        Svc->>Notif: 18. triggerNotification(perkara_id)
        Notif->>DB: 19. SELECT notification_pref FROM klien
        DB-->>Notif: 20. Return preference
        
        alt Email notification aktif
            Notif->>Notif: 21. composeEmail(template, data)
            Notif->>Notif: 22. sendEmail(klien.email)
        end
        
        alt SMS notification aktif
            Notif->>Notif: 23. sendSMS(klien.no_hp)
        end
        
        Notif-->>Svc: 24. Notification sent
        Svc-->>Ctrl: 25. Return success
        Ctrl-->>UI: 26. JSON response
        UI->>N: 27. Show success message
        UI->>UI: 28. Refresh dashboard
    else Status transition tidak valid
        Ctrl-->>UI: Return error "Invalid transition"
        UI->>N: Show error
    end
```

## 4. Sequence Diagram - Dashboard & Laporan (UC04, UC10)

```mermaid
sequenceDiagram
    autonumber
    actor User as Notaris/Staff
    participant UI as Web Interface
    participant Ctrl as DashboardController
    participant Svc as DashboardService
    participant Cache as Redis Cache
    participant DB as Database

    User->>UI: 1. Akses /dashboard
    UI->>Ctrl: 2. GET /api/dashboard
    
    Ctrl->>Cache: 3. GET cache:dashboard:{user_id}
    
    alt Cache hit
        Cache-->>Ctrl: 4. Return cached data
        Ctrl-->>UI: 5. Return dashboard data
        UI->>User: 6. Render dashboard
    else Cache miss
        Ctrl->>Svc: 4. getDashboardData(user_id, role)
        
        Svc->>DB: 5. SELECT COUNT(*) by status
        DB-->>Svc: 6. Return counts
        
        Svc->>DB: 7. SELECT recent perkara
        DB-->>Svc: 8. Return recent cases
        
        Svc->>DB: 9. SELECT today activity log
        DB-->>Svc: 10. Return activities
        
        Svc->>Svc: 11. aggregateStatistics()
        Svc-->>Ctrl: 12. Return dashboard data
        
        Ctrl->>Cache: 13. SET cache (TTL=300s)
        Ctrl-->>UI: 14. Return dashboard data
        UI->>User: 15. Render dashboard
        
        opt Generate laporan (UC10)
            User->>UI: 16. Klik "Generate Report"
            UI->>Ctrl: 17. GET /api/report?type={type}&date={range}
            Ctrl->>Svc: 18. generateReport(type, date_range)
            
            Svc->>DB: 19. SELECT filtered data
            DB-->>Svc: 20. Return report data
            
            Svc->>Svc: 21. formatReport(PDF/Excel)
            Svc-->>Ctrl: 22. Return report file
            Ctrl-->>UI: 23. Return file
            UI->>User: 24. Download report
        end
    end
```

## Penjelasan Sequence

| Sequence | Use Case | Aktor | Deskripsi |
|----------|----------|-------|-----------|
| 1 | UC01, UC07 | Klien | Tracking real-time dengan notifikasi |
| 2 | UC02, UC06 | Staff | Registrasi perkara dengan workflow |
| 3 | UC03, UC07 | Staff/Notaris | Update status dengan notifikasi |
| 4 | UC04, UC10 | Notaris/Staff | Dashboard dengan laporan |

## Komponen yang Terlibat

| Komponen | Peran |
|----------|-------|
| **Web Interface** | Frontend UI (HTML/CSS/JS) |
| **Controller** | Handle HTTP request/response |
| **Service** | Business logic implementation |
| **Database** | MySQL - penyimpanan data |
| **Cache** | Redis - caching untuk performa |
| **NotificationService** | Email/SMS notification |
| **AuditLog** | Logging aktivitas sistem |
| **File Storage** | Penyimpanan dokumen |
