# State Machine Diagram - Sistem Tracking Status Dokumen Kantor Notaris

## Deskripsi
Diagram state machine ini menggambarkan transisi status pada objek **Perkara**.

## 1. State Machine Diagram - Status Perkara

```mermaid
stateDiagram-v2
    [*] --> DRAFT: Staff buat perkara baru
    
    DRAFT --> MENUNGGU_VERIFIKASI: Staff submit untuk verifikasi
    note right of MENUNGGU_VERIFIKASI
        Dokumen awal telah diupload
        Menunggu verifikasi staff
    end note
    
    MENUNGGU_VERIFIKASI --> DOKUMEN_TIDAK_LENGKAP: Staff reject dokumen
    note right of DOKUMEN_TIDAK_LENGKAP
        Ada dokumen yang kurang/invalid
        Perlu revisi dari klien
    end note
    
    DOKUMEN_TIDAK_LENGKAP --> MENUNGGU_VERIFIKASI: Klien upload revisi
    
    MENUNGGU_VERIFIKASI --> DOKUMEN_LENGKAP: Staff approve dokumen
    note right of DOKUMEN_LENGKAP
        Semua dokumen lengkap dan valid
        Siap untuk proses notaris
    end note
    
    DOKUMEN_LENGKAP --> PROSES_NOTARIS: Notaris mulai proses
    note right of PROSES_NOTARIS
        Notaris sedang mengerjakan
        dokumen perkara
    end note
    
    PROSES_NOTARIS --> REVISI_INTERNAL: Notaris temukan masalah
    note right of REVISI_INTERNAL
        Perlu perbaikan internal
        oleh staff/notaris
    end note
    
    REVISI_INTERNAL --> PROSES_NOTARIS: Revisi selesai
    
    PROSES_NOTARIS --> MENUNGGU_TANDA_TANGAN: Proses selesai
    note right of MENUNGGU_TANDA_TANGAN
        Dokumen siap ditandatangani
        oleh notaris dan pihak terkait
    end note
    
    MENUNGGU_TANDA_TANGAN --> SELESAI: Semua TTD lengkap
    note right of SELESAI
        Perkara selesai
        Dokumen final tersedia
    end note
    
    SELESAI --> [*]: Perkara archived
    
    MENUNGGU_VERIFIKASI --> DIBATALKAN: Klien batalkan
    DOKUMEN_TIDAK_LENGKAP --> DIBATALKAN: Tidak ada revisi
    PROSES_NOTARIS --> DIBATALKAN: Request pembatalan
    MENUNGGU_TANDA_TANGAN --> DIBATALKAN: Request pembatalan
    
    DIBATALKAN --> [*]: Perkara closed
```

## 2. State Machine Diagram - Notifikasi

```mermaid
stateDiagram-v2
    [*] --> NOTIF_OFF: Default state
    
    NOTIF_OFF --> SUBSCRIBED: User aktifkan notifikasi
    note right of SUBSCRIBED
        User subscribe email/SMS
        untuk status updates
    end note
    
    SUBSCRIBED --> PENDING_SEND: Status berubah
    note right of PENDING_SEND
        Trigger notification
        masuk queue
    end note
    
    PENDING_SEND --> SENDING: Process send
    
    SENDING --> SENT: Email/SMS terkirim
    note right of SENT
        Notification delivered
        successfully
    end note
    
    SENDING --> FAILED: Send failed
    note right of FAILED
        Error saat pengiriman
    end note
    
    FAILED --> RETRY: Auto retry (max 3x)
    RETRY --> SENDING: Retry attempt
    
    RETRY --> PERMANENTLY_FAILED: Max retry
    note right of PERMANENTLY_FAILED
        Gagal setelah 3x percobaan
        Log error untuk review
    end note
    
    SENT --> SUBSCRIBED: Ready untuk notif berikutnya
    PERMANENTLY_FAILED --> SUBSCRIBED: Kembali ke subscribed
    
    SUBSCRIBED --> NOTIF_OFF: User nonaktifkan
    SENT --> NOTIF_OFF: User nonaktifkan
```

## Penjelasan State Perkara

| State | Deskripsi | Trigger Masuk | Trigger Keluar |
|-------|-----------|---------------|----------------|
| **DRAFT** | Perkara baru dibuat | Staff buat perkara | Staff submit |
| **MENUNGGU_VERIFIKASI** | Menunggu verifikasi dokumen | Staff submit | Staff approve/reject |
| **DOKUMEN_TIDAK_LENGKAP** | Dokumen kurang/invalid | Staff reject | Klien upload revisi |
| **DOKUMEN_LENGKAP** | Dokumen lengkap & valid | Staff approve | Notaris mulai proses |
| **PROSES_NOTARIS** | Notaris mengerjakan | Notaris start | Proses selesai/revisi |
| **REVISI_INTERNAL** | Perlu perbaikan internal | Notaris temukan masalah | Revisi selesai |
| **MENUNGGU_TANDA_TANGAN** | Siap TTD | Proses notaris selesai | TTD lengkap |
| **SELESAI** | Perkara selesai | Semua TTD lengkap | Archive |
| **DIBATALKAN** | Perkara dibatalkan | Request cancel | Close |

## Penjelasan State Notifikasi

| State | Deskripsi |
|-------|-----------|
| **NOTIF_OFF** | Notifikasi dinonaktifkan |
| **SUBSCRIBED** | User berlangganan notifikasi |
| **PENDING_SEND** | Notifikasi dalam queue |
| **SENDING** | Proses pengiriman |
| **SENT** | Berhasil terkirim |
| **FAILED** | Gagal terkirim |
| **RETRY** | Retry pengiriman (max 3x) |
| **PERMANENTLY_FAILED** | Gagal permanen setelah max retry |

## Guard Conditions

| Transisi | Guard Condition |
|----------|-----------------|
| MENUNGGU_VERIFIKASI → DOKUMEN_LENGKAP | [semua dokumen valid] |
| MENUNGGU_VERIFIKASI → DOKUMEN_TIDAK_LENGKAP | [ada dokumen tidak valid] |
| PROSES_NOTARIS → MENUNGGU_TANDA_TANGAN | [proses selesai] |
| MENUNGGU_TANDA_TANGAN → SELESAI | [semua tanda tangan lengkap] |
| SENDING → FAILED | [send_error] |
| FAILED → RETRY | [retry_count < 3] |

## Entry/Exit Actions

| State | Entry Action | Exit Action |
|-------|--------------|-------------|
| MENUNGGU_VERIFIKASI | addToVerificationQueue() | removeFromQueue() |
| SELESAI | sendCompletionNotification() | archivePerkara() |
| DIBATALKAN | sendCancellationNotification() | closePerkara() |
| SENT | logNotificationSent() | - |
