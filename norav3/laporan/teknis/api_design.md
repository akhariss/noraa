# API Design - Desain API Sistem Tracking Notaris

## 1. Overview API

Sistem menggunakan RESTful-inspired API dengan query-parameter routing (`?gate=xxx`). Semua response dalam format JSON untuk AJAX requests.

### 1.1 API Conventions

| Aspect | Convention |
|--------|------------|
| **Routing** | Query-parameter: `?gate=endpoint_name` |
| **HTTP Methods** | GET (read), POST (create/update/delete) |
| **Response Format** | JSON: `{success: boolean, message: string, data: object}` |
| **Authentication** | PHP Session-based |
| **Authorization** | RBAC (Role-Based Access Control) |
| **Error Handling** | HTTP status codes + JSON error messages |
| **Rate Limiting** | File-based throttling per endpoint |

---

## 2. Public API (No Authentication)

### 2.1 Tracking Endpoints

#### GET /index.php?gate=lacak

**Deskripsi:** Menampilkan halaman tracking search

**Response:** `text/html`

```http
GET /index.php?gate=lacak HTTP/1.1
Host: notaris.example.com

200 OK
Content-Type: text/html
```

---

#### POST /index.php?gate=lacak

**Deskripsi:** Search registrasi by nomor registrasi

**Request:**
```json
{
    "nomor_registrasi": "NP-20260326-1234"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Nomor registrasi ditemukan. Silakan verifikasi dengan 4 digit terakhir nomor HP.",
    "data": {
        "registrasi_id": 123,
        "nomor_registrasi": "NP-20260326-1234",
        "requires_verification": true
    }
}
```

**Response Error (Not Found):**
```json
{
    "success": false,
    "message": "Nomor registrasi tidak ditemukan"
}
```

**Response Error (Rate Limited):**
```json
{
    "success": false,
    "message": "Terlalu banyak permintaan. Silakan tunggu beberapa saat."
}
```

**HTTP Status:** `429 Too Many Requests`

---

#### POST /index.php?gate=verify_tracking

**Deskripsi:** Verifikasi 4 digit terakhir nomor HP

**Request:**
```json
{
    "registrasi_id": 123,
    "phone_code": "7890"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Verifikasi berhasil",
    "data": {
        "token": "eyJpZCI6MTIzLCJjb2RlIjoiYTFiMmMzZDQiLCJleHAiOjE3MTY5ODc2NTR9.abc123def456...",
        "id": 123,
        "nomor_registrasi": "NP-20260326-1234",
        "klien_nama": "John Doe",
        "layanan": "Akta Jual Beli",
        "status": "pembayaran_admin",
        "status_label": "Pembayaran Administrasi",
        "created_at": "26 Mar 2026",
        "updated_at": "26 Mar 2026 14:30"
    }
}
```

**Response Error (Wrong Code):**
```json
{
    "success": false,
    "message": "Kode verifikasi salah. 4 digit terakhir nomor HP tidak sesuai."
}
```

**Rate Limit:** 5 requests per minute per IP

---

#### GET /index.php?gate=detail&token={token}

**Deskripsi:** Tampilkan detail registrasi dengan tracking token

**Request:**
```
GET /index.php?gate=detail&token=eyJpZCI6MTIzLCJjb2RlIjoiYTFiMmMzZDQiLCJleHAiOjE3MTY5ODc2NTR9.abc123def456...
```

**Response:** `text/html` (rendered view)

**Token Validation:**
- Token must be valid (HMAC signature match)
- Token must not be expired (24 hours)
- Token must match database tracking_token

**Error (Invalid Token):**
```http
403 Forbidden
Content-Type: text/html

<h1>Akses Ditolak</h1>
<p>Token tidak valid atau sudah kadaluarsa (max 24 jam).</p>
```

---

### 2.2 Health Check

#### GET /index.php?gate=health

**Deskripsi:** Health check endpoint untuk monitoring

**Response Success:**
```json
{
    "status": "healthy",
    "timestamp": 1711436400,
    "timestamp_iso": "2026-03-26T14:00:00+07:00",
    "database": "up",
    "version": "1.1.1",
    "php_version": "8.2.0"
}
```

**Response Error (Database Down):**
```json
{
    "status": "degraded",
    "timestamp": 1711436400,
    "timestamp_iso": "2026-03-26T14:00:00+07:00",
    "database": "down",
    "version": "1.1.1",
    "php_version": "8.2.0"
}
```

---

## 3. Authentication API

### 3.1 Login Endpoints

#### GET /index.php?gate=login

**Deskripsi:** Tampilkan halaman login

**Response:** `text/html`

---

#### POST /index.php?gate=login

**Deskripsi:** Authenticate user

**Request:**
```json
{
    "username": "admin",
    "password": "password123"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Login berhasil",
    "redirect": "/index.php?gate=dashboard"
}
```

**Response Error (Invalid Credentials):**
```json
{
    "success": false,
    "message": "Username atau password salah"
}
```

**Response Error (CSRF Invalid):**
```json
{
    "success": false,
    "message": "Token CSRF tidak valid"
}
```

**Security:**
- Password hashed dengan bcrypt (cost 12)
- Session fingerprinting (anti-hijacking)
- Rate limiting: 5 failed attempts per 5 minutes

---

#### GET /index.php?gate=logout

**Deskripsi:** Logout user

**Response:** Redirect to login page

**Side Effects:**
- Session destroyed
- Audit log: logout event

---

## 4. Dashboard API (Authentication Required)

### 4.1 Dashboard Overview

#### GET /index.php?gate=dashboard

**Deskripsi:** Dashboard home dengan statistik

**Response:** `text/html`

**Data Displayed:**
- Total registrasi
- Registrasi aktif
- Selesai bulan ini
- Batal bulan ini
- Registrasi dengan flag kendala
- Recent activity

---

### 4.2 Registrasi Endpoints

#### GET /index.php?gate=registrasi

**Deskripsi:** List registrasi dengan pagination dan filtering

**Query Parameters:**
```
?page=1&limit=20&search=NP-2026&status=draft&layanan=1
```

**Response:** `text/html`

---

#### GET /index.php?gate=registrasi_create

**Deskripsi:** Form create registrasi baru

**Response:** `text/html`

---

#### POST /index.php?gate=registrasi_store

**Deskripsi:** Store registrasi baru

**Request:**
```json
{
    "klien_nama": "John Doe",
    "klien_hp": "081234567890",
    "klien_email": "john@example.com",
    "layanan_id": 1,
    "status": "draft",
    "catatan": "Urgent"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Registrasi berhasil disimpan",
    "data": {
        "registrasi_id": 123,
        "nomor_registrasi": "NP-20260326-1234",
        "show_whatsapp_popup": true
    }
}
```

**Response Error (Invalid Status):**
```json
{
    "success": false,
    "message": "Status awal hanya boleh: Draft, Pembayaran Admin, Validasi Sertifikat, atau Pengecekan Sertifikat"
}
```

**Business Rules:**
- Status awal hanya boleh 4 status pertama
- Klien getOrCreate based on phone number
- Auto-generate nomor_registrasi (NP-YYYYMMDD-XXXX)
- Auto-generate verification_code

---

#### GET /index.php?gate=registrasi_detail&id={id}

**Deskripsi:** Detail registrasi dengan history

**Request:**
```
GET /index.php?gate=registrasi_detail&id=123
```

**Response:** `text/html`

**Data Included:**
- Registrasi data
- Klien data
- Layanan data
- Progress tracking
- History timeline
- Kendala flags

---

#### POST /index.php?gate=update_status

**Deskripsi:** Update status registrasi

**Request:**
```json
{
    "registrasi_id": 123,
    "status": "pembayaran_admin",
    "catatan": "Pembayaran telah dikonfirmasi",
    "flag_kendala": false
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Status berhasil diperbarui"
}
```

**Response Error (Invalid Transition):**
```json
{
    "success": false,
    "message": "Status tidak dapat mundur dari Validasi Sertifikat ke Draft"
}
```

**Response Error (Cannot Cancel):**
```json
{
    "success": false,
    "message": "Status sudah melewati tahap pembatalan"
}
```

**Response Error (Locked):**
```json
{
    "success": false,
    "message": "Registrasi sedang dikunci"
}
```

**Validation Rules:**
- No backward transition (except perbaikan)
- No cancellation after pembayaran_pajak
- No update if locked
- No update if final status

---

#### POST /index.php?gate=update_klien

**Deskripsi:** Update data klien

**Request:**
```json
{
    "klien_id": 456,
    "nama": "John Doe Updated",
    "hp": "081234567899",
    "email": "john.updated@example.com"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Data klien berhasil diperbarui"
}
```

---

#### POST /index.php?gate=toggle_kendala

**Deskripsi:** Toggle flag kendala

**Request:**
```json
{
    "registrasi_id": 123,
    "tahap": "Validasi Sertifikat",
    "catatan": "Menunggu dokumen tambahan dari klien"
}
```

**Response Success (Create):**
```json
{
    "success": true,
    "message": "Kendala ditambahkan - Perlu monitoring ekstra",
    "action": "create",
    "kendala_id": 789
}
```

**Response Success (Deactivate):**
```json
{
    "success": true,
    "message": "Kendala dinonaktifkan - Status kembali normal",
    "action": "deactivate",
    "kendala_id": 789
}
```

---

#### POST /index.php?gate=toggle_lock

**Deskripsi:** Lock/unlock registrasi

**Request:**
```json
{
    "registrasi_id": 123,
    "action": "lock" // or "unlock"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Registrasi berhasil dikunci"
}
```

**Authorization:** Notaris only

---

### 4.3 User Management (Notaris Only)

#### GET /index.php?gate=users

**Deskripsi:** User management page

**Response:** `text/html`

**Authorization:** Notaris only

---

#### POST /index.php?gate=users

**Deskripsi:** Handle user CRUD (create, update, delete)

**Request (Create):**
```json
{
    "action": "create",
    "username": "newuser",
    "password": "password123",
    "role": "admin"
}
```

**Request (Update):**
```json
{
    "action": "update",
    "user_id": 3,
    "username": "updateduser",
    "role": "notaris"
}
```

**Request (Delete):**
```json
{
    "action": "delete",
    "user_id": 3
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "User berhasil dibuat/diperbarui/dihapus"
}
```

**Security:**
- Password hashed dengan bcrypt
- Audit log untuk semua user actions
- Role change requires notaris authorization

---

### 4.4 Finalisasi Endpoints

#### GET /index.php?gate=finalisasi

**Deskripsi:** List registrasi yang siap finalisasi (status: selesai)

**Response:** `text/html`

---

#### POST /index.php?gate=tutup_registrasi

**Deskripsi:** Tutup registrasi (status → ditutup)

**Request:**
```json
{
    "registrasi_id": 123,
    "notes": "Dokumen telah diserahkan ke klien"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Kasus berhasil ditutup"
}
```

**Response Error (Invalid Status):**
```json
{
    "success": false,
    "message": "Status harus selesai untuk finalisasi"
}
```

**Authorization:** Notaris only

---

#### POST /index.php?gate=reopen_case

**Deskripsi:** Reopen kasus yang sudah ditutup

**Request:**
```json
{
    "registrasi_id": 123
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Kasus berhasil dibuka kembali"
}
```

**Authorization:** Notaris only

---

### 4.5 Backup Management (Notaris Only)

#### GET /index.php?gate=backups

**Deskripsi:** Backup management page

**Response:** `text/html`

---

#### POST /index.php?gate=backups

**Deskripsi:** Handle backup actions (create, download, delete)

**Request (Create):**
```json
{
    "action": "create"
}
```

**Request (Delete):**
```json
{
    "action": "delete",
    "filename": "backup_2026-03-26_140000.sql"
}
```

**Response Success (Create):**
```json
{
    "success": true,
    "message": "Backup berhasil dibuat",
    "filename": "backup_2026-03-26_140000.sql"
}
```

**Response Success (Delete):**
```json
{
    "success": true,
    "message": "Backup berhasil dihapus"
}
```

**Authorization:** Notaris only

---

### 4.6 Audit Log (Notaris Only)

#### GET /index.php?gate=audit

**Deskripsi:** Audit log viewer

**Query Parameters:**
```
?date_from=2026-03-01&date_to=2026-03-31&user_id=2&action=create
```

**Response:** `text/html`

**Authorization:** Notaris only

---

## 5. CMS API (Notaris Only)

### 5.1 CMS Editor Endpoints

#### GET /index.php?gate=cms_editor

**Deskripsi:** CMS grid menu

**Response:** `text/html`

---

#### GET /index.php?gate=cms_edit_home

**Deskripsi:** Edit homepage content

**Response:** `text/html`

---

#### POST /index.php?gate=cms_update_content

**Deskripsi:** Update CMS content

**Request:**
```json
{
    "section_id": 1,
    "content_key": "hero_title",
    "content_value": "Selamat Datang di Kantor Notaris Sri Anah",
    "content_type": "text"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Konten berhasil diperbarui"
}
```

---

#### POST /index.php?gate=cms_update_item

**Deskripsi:** Update CMS item (button, card, etc.)

**Request:**
```json
{
    "item_id": 456,
    "title": "Layanan Notaris",
    "description": "Kami menyediakan berbagai layanan notaris",
    "extra_data": {"link": "/layanan"}
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Item berhasil diperbarui"
}
```

---

#### POST /index.php?gate=cms_upload_image

**Deskripsi:** Upload image untuk CMS

**Request:** `multipart/form-data`

```
POST /index.php?gate=cms_upload_image
Content-Type: multipart/form-data

image: [file]
```

**Response Success:**
```json
{
    "success": true,
    "path": "/assets/images/img_a1b2c3d4e5f6g7h8.jpg"
}
```

**Response Error (File Too Large):**
```json
{
    "success": false,
    "message": "Ukuran file melebihi batas (max 5MB)"
}
```

**Response Error (Invalid Type):**
```json
{
    "success": false,
    "message": "Tipe file tidak diizinkan (jpg, jpeg, png, pdf only)"
}
```

**Upload Constraints:**
- Max size: 5MB
- Allowed extensions: jpg, jpeg, png, pdf
- Secure filename: img_<random_hex>.ext

---

#### POST /index.php?gate=cms_save_message_tpl

**Deskripsi:** Save WhatsApp message template

**Request:**
```json
{
    "template_key": "registrasi_baru",
    "template_name": "Notifikasi Registrasi Baru",
    "template_body": "Halo {nama_klien}, registrasi Anda telah terdaftar."
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Template berhasil disimpan"
}
```

---

#### POST /index.php?gate=cms_save_note_tpl

**Deskripsi:** Save internal note template

**Request:**
```json
{
    "status_key": "draft",
    "template_body": "Perkara Anda telah terdaftar..."
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Template berhasil disimpan"
}
```

---

#### POST /index.php?gate=cms_add_layanan

**Deskripsi:** Add new layanan

**Request:**
```json
{
    "nama_layanan": "Akta Cerai",
    "deskripsi": "Pembuatan akta cerai"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Layanan berhasil ditambahkan"
}
```

---

#### POST /index.php?gate=cms_update_layanan

**Deskripsi:** Update existing layanan

**Request:**
```json
{
    "layanan_id": 1,
    "nama_layanan": "Akta Jual Beli Updated",
    "deskripsi": "Deskripsi updated"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Layanan berhasil diperbarui"
}
```

---

#### POST /index.php?gate=cms_delete_layanan

**Deskripsi:** Delete layanan

**Request:**
```json
{
    "layanan_id": 1
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Layanan berhasil dihapus"
}
```

---

#### POST /index.php?gate=cms_save_settings

**Deskripsi:** Save app settings

**Request:**
```json
{
    "settings_key": "app_name",
    "settings_value": "Notaris Sri Anah SH.M.Kn"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Pengaturan berhasil disimpan"
}
```

---

#### GET /index.php?gate=cms_get_note_templates

**Deskripsi:** Get note templates as JSON

**Response:**
```json
{
    "success": true,
    "templates": [
        {
            "status_key": "draft",
            "template_body": "Perkara Anda telah terdaftar..."
        },
        {
            "status_key": "selesai",
            "template_body": "Dokumen telah selesai..."
        }
    ]
}
```

---

## 6. Error Handling

### 6.1 Error Response Format

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": "Specific error message"
    }
}
```

### 6.2 HTTP Status Codes

| Code | Usage |
|------|-------|
| 200 | Success |
| 302 | Redirect (login, logout) |
| 400 | Bad Request (validation error) |
| 403 | Forbidden (RBAC fail, CSRF fail, invalid token) |
| 404 | Not Found (route not found, registrasi not found) |
| 429 | Too Many Requests (rate limit exceeded) |
| 500 | Internal Server Error (exception caught) |

### 6.3 Error Examples

**Validation Error:**
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "nomor_registrasi": "Nomor registrasi wajib diisi",
        "status": "Status tidak valid"
    }
}
```

**CSRF Error:**
```json
{
    "success": false,
    "message": "Token CSRF tidak valid"
}
```

**RBAC Error:**
```json
{
    "success": false,
    "message": "Forbidden"
}
```

**Rate Limit Error:**
```json
{
    "success": false,
    "message": "Terlalu banyak permintaan. Silakan tunggu beberapa saat."
}
```

---

## 7. Rate Limiting

### 7.1 Rate Limit Configuration

| Endpoint | Limit | Window |
|----------|-------|--------|
| tracking_search | 5 requests | 1 minute |
| tracking_verify | 5 requests | 1 minute |
| login | 5 failed attempts | 5 minutes |
| homepage | 100 requests | 1 minute |
| Other endpoints | 10 requests | 1 minute |

### 7.2 Rate Limit Response

```json
{
    "success": false,
    "message": "Terlalu banyak permintaan. Silakan tunggu beberapa saat."
}
```

**HTTP Status:** `429 Too Many Requests`

---

## 8. Security

### 8.1 Authentication

- Session-based authentication
- Session fingerprinting (anti-hijacking)
- Session lifetime: 2 hours

### 8.2 Authorization

- RBAC (Role-Based Access Control)
- Permission mapping per endpoint
- Wildcard access for notaris role

### 8.3 CSRF Protection

- Token required for all POST requests
- Token validation in controller
- Regenerate token per session

### 8.4 Input Sanitization

- Global input sanitization
- htmlspecialchars() for XSS prevention
- Type casting for numeric inputs

---

## 9. API Summary

### 9.1 Endpoint Count

| Category | Endpoints |
|----------|-----------|
| Public (Tracking) | 4 |
| Authentication | 3 |
| Dashboard | 10+ |
| Registrasi | 8 |
| User Management | 4 |
| Finalisasi | 3 |
| Backup | 2 |
| Audit Log | 1 |
| CMS | 12+ |
| **Total** | **47+** |

### 9.2 Method Distribution

| Method | Count | Purpose |
|--------|-------|---------|
| GET | 25+ | Read operations, page display |
| POST | 22+ | Create, update, delete operations |

---

## 10. Kesimpulan

API design mengikuti prinsip:

1. **RESTful-inspired** - Resource-based endpoints dengan JSON responses
2. **Query-parameter Routing** - Simple routing dengan ?gate=xxx
3. **Consistent Response Format** - {success, message, data} structure
4. **Security First** - Authentication, authorization, CSRF, rate limiting
5. **Error Handling** - Proper HTTP status codes dan error messages
6. **Business Rules** - Validation enforcement dalam controller/service
7. **Documentation** - Clear endpoint documentation

API ini mendukung semua functional requirements sistem tracking dengan security dan scalability yang baik.
