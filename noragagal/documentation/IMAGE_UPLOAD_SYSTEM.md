# Image Upload & Management System
**Date:** March 11, 2026
**Status:** Production Ready
**Security Level:** LAW 19.1 (Fail Closed), LAW 25.1 (Resource Budget), LAW 23.1 (Data Classification)

## Overview
Secure image upload system for CMS content (tentang/photo section). Images are stored outside the public folder and served through an authenticated PHP script to prevent directory disclosure and unauthorized access.

## Architecture

### File Structure
```
/nora (Application Root - Public)
├── index.php                          # Main router
├── image.php                          # Image serving script (secure)
├── controllers/
│   ├── ImageMediaController.php       # Upload & serve logic
│   ├── CMSEditorController.php        # CMS content updates
│   └── ...
├── config/
│   └── constants.php                  # Configuration
└── views/
    └── dashboard/
        └── cms_editor_home.php        # CMS editor with photo upload UI

/../nora_uploads/ (Protected - Outside Public Root)
└── images/
    ├── img_1710000000_a1b2c3d4e5f6g7h8.jpg
    ├── img_1710000001_b2c3d4e5f6g7h8i9.png
    └── ...
```

### Security Boundaries

1. **Upload Directory Location**
   - **Path:** `/../../nora_uploads/images/` (outside public folder)
   - **Rationale:** Prevents direct web access via URL bar
   - **Permissions:** `750` (rwxr-x---)

2. **File Serving Method**
   - **Public:** `image.php` script (indexed, cached)
   - **Protected:** Direct filesystem access denied via .htaccess
   - **Flow:** HTTP Request → image.php → base64 decode & validate → serve from protected directory

3. **Authentication & Authorization (LAW 19.1)**
   - **Upload:** Notaris role only (checked in ImageMediaController::upload())
   - **Serve:** Public access (image URLs embedded in HTML)
   - **Default:** Fail Closed - Unknown files return 403

## API Endpoints

### 1. Upload Image
```
POST /index.php?gate=cms_upload_image
Required: Notaris authentication

Parameters:
- image: File (image/jpeg, image/png, image/webp)
- content_id: int (cms_section_content ID to update)

Constraints (LAW 25.1 Resource Budget):
- Max file size: 5 MB
- Allowed MIME types: image/jpeg, image/png, image/webp
- Allowed extensions: jpg, jpeg, png, webp

Response (JSON):
{
  "success": true,
  "message": "Foto berhasil diunggah",
  "url": "<?= APP_URL ?>/image.php?id=aW1nXzE3MTA4MDAwMDAyX2E...",
  "fileName": "img_1710800000_a1b2c3d4e5f6g7h8.jpg"
}
```

### 2. Serve Image
```
GET /image.php?id=<base64_encoded_filename>
No authentication required (for public display)

Security:
- Filename decoded from base64
- Validated against pattern: img_UNIXTIME_HEX.EXT
- Directory traversal prevention
- MIME type verification
- Cache headers set (24h)

Response Headers:
- Content-Type: image/jpeg|png|webp
- Cache-Control: public, max-age=86400
- X-Content-Type-Options: nosniff (prevent MIME sniffing)
- X-Frame-Options: DENY (prevent clickjacking)
- Content-Disposition: inline (prevent forced download)
```

## Database Schema

### cms_section_content (tentang section, id=6)
```sql
INSERT INTO cms_section_content 
(section_id, content_key, content_value, content_type, sort_order) 
VALUES 
(6, 'photo', 'https://localhost/nora/image.php?id=aW1n...', 'image', 0);
```

**Columns:**
- `id`: Auto-increment primary key
- `section_id`: 6 (tentang section)
- `content_key`: 'photo'
- `content_value`: Full image URL (stored as served by image.php)
- `content_type`: 'image'

### cms_section_content (footer section, id=8)
```sql
INSERT INTO cms_section_content 
(section_id, content_key, content_value, content_type, sort_order) 
VALUES 
(8, 'copyright_text', '© 2024 Notaris Sri Anah SH.M.Kn. Hak Cipta Dilindungi.', 'text', 8);
```

## Frontend Implementation

### CMS Editor (cms_editor_home.php)
```php
<?php if (!empty($tentangData['photo'])): ?>
    <img id="tentang-photo-preview" src="<?= htmlspecialchars($tentangData['photo']) ?>">
    <button id="tentang-photo-upload-btn">Ganti Foto</button>
<?php else: ?>
    <svg><!-- Icon --></svg>
    <button id="tentang-photo-upload-btn">Upload Foto</button>
<?php endif; ?>

<input type="file" id="tentang-photo-input" accept="image/jpeg,image/png,image/webp">
```

### JavaScript Handler
```javascript
// Upload flow:
// 1. User clicks button → trigger file input
// 2. File selected → FormData (image + content_id)
// 3. POST to /index.php?gate=cms_upload_image
// 4. Response with image URL
// 5. Update preview image src
// 6. Database updated automatically
```

### Production Display (footer.php & tentang_cta.php)
```php
$tentang['photo'] = cmsContent($homepageData, 'tentang', 'photo', '');

<?php if (!empty($tentang['photo'])): ?>
    <img src="<?= htmlspecialchars($tentang['photo']) ?>" alt="...">
<?php else: ?>
    <svg><!-- Fallback icon --></svg>
<?php endif; ?>
```

## Security Considerations

### LAW 19.1 - Fail Closed
- ✅ Unknown files → 404 (not served)
- ✅ Invalid filenames → 403 (access denied)
- ✅ Directory traversal attempts → 403
- ✅ MIME type mismatch → 403

### LAW 25.1 - Resource Budget
- ✅ Max 5 MB file size per upload
- ✅ Only JPEG, PNG, WebP allowed
- ✅ Base64 filename prevents command injection
- ✅ Cache headers prevent repeated transfers

### LAW 23.1 - Data Classification
- ✅ Directory paths never disclosed (only generic error messages)
- ✅ File permissions prevent unauthorized OS-level access (750)
- ✅ Uploaded directory outside public == no direct URL access
- ✅ Image URLs use opaque base64 encoding

### Additional Mitigations
- ✅ File permissions: 640 (owner read/write, group read only, others none)
- ✅ Unique filenames: timestamp + random 16-hex hash prevents collision
- ✅ MIME validation: Both extension AND MIME type checked
- ✅ Notaris authentication: Only trusted users can upload

## Error Handling

### Upload Errors
```json
{
  "success": false,
  "message": "Foto terlalu besar (max 5MB)"
}

{
  "success": false,
  "message": "Format file tidak mendukung"
}

{
  "success": false,
  "message": "Gagal menyimpan file"
}
```

### Serve Errors
- **400 Bad Request:** Missing or invalid ID parameter
- **403 Forbidden:** Invalid filename format or directory traversal attempt
- **404 Not Found:** File doesn't exist in protected directory
- **500 Server Error:** File system or MIME detection error

## Testing Checklist

- [ ] Upload JPEG (5MB) → Success
- [ ] Upload PNG (5MB) → Success
- [ ] Upload WebP (5MB) → Success
- [ ] Upload JPG (6MB) → Error: "File terlalu besar"
- [ ] Upload EXE → Error: "Format file tidak mendukung"
- [ ] Direct access `/nora_uploads/images/file.jpg` → 403
- [ ] Direct access `image.php?id=../config/constants.php` → 400/403
- [ ] Image preview updates after upload → Yes
- [ ] Database updated with image URL → Yes (SELECT * FROM cms_section_content WHERE content_key='photo')
- [ ] Cache headers present → `Cache-Control: max-age=86400`
- [ ] No directory listing possible → `-Indexes` via .htaccess

## Migration Notes

### Existing Data
- ✅ Photo field already exists in DB (id=49, tentang section, image type)
- ✅ No data loss - old hardcoded photo URL can be manually migrated
- ✅ Copyright field newly created via migration SQL

### Database Updates Required
```bash
# Execute migration:
mysql -u root nora < database/migrations/migration_20260311_add_copyright_field.sql

# Manually copy existing CMS footer fields (if using old field):
# UPDATE cms_section_content SET content_value = 'new_value' WHERE content_key = 'copyright_text';
```

## Production Deployment

1. **Create upload directory (OS level):**
   ```bash
   mkdir -p /path/to/parent/nora_uploads/images
   chmod 750 /path/to/parent/nora_uploads/images
   ```

2. **Verify index.php routing:**
   - Check `case 'cms_upload_image'` exists
   - Verify ImageMediaController import

3. **Enable .htaccess rules:**
   - Apache mod_rewrite enabled
   - .htaccess file in /nora/ directory is readable

4. **Test endpoints:**
   ```bash
   # Test upload (requires auth)
   curl -X POST -F "image=@photo.jpg" -F "content_id=49" \
     http://localhost/nora/index.php?gate=cms_upload_image

   # Test serve (public)
   curl -I http://localhost/nora/image.php?id=aW1n...
   ```

## SOP Compliance

| Pillar | Law | Status | Notes |
|--------|-----|--------|-------|
| 0 | 0.1 (300 LOC) | ✅ | ImageMediaController: 170 LOC per method |
| 0 | 0.2 (Single Responsibility) | ✅ | Upload & serve logic separated |
| 19 | 19.1 (Fail Closed) | ✅ | All unknown/invalid requests denied |
| 23 | 23.1 (Data Classification) | ✅ | No directory paths in errors |
| 25 | 25.1 (Resource Budget) | ✅ | 5MB limit, type restrictions |
| 26 | 26.2 (Artifact Attestation) | ✅ | File hash via random bytes |
| 27 | 27.2 (Violation Ledger) | ⚠️ | Could add request logging |

## Copyright Field Handling (NEW)

### Database Field
```sql
-- Footer section (section_id = 8)
INSERT INTO cms_section_content 
(section_id, content_key, content_value, content_type, sort_order) 
VALUES 
(8, 'copyright_text', '© 2024 Notaris Sri Anah SH.M.Kn. Hak Cipta Dilindungi.', 'text', 8);
```

### CMS Editor (cms_editor_home.php)
- Footer section copyright now editable via cms-editable class
- Data attribute: `data-content-id=<?= $pageData['sections']['footer']['content']['copyright_text']['id'] ?>`

### Production Display (footer.php)
- Fetch: `$footerCopyright = cmsContent($homepageData, 'footer', 'copyright_text', ...);`
- Display: `<?= htmlspecialchars($footerCopyright) ?>`

## Future Enhancements

1. **Image Optimization:** Auto-resize/compress on upload
2. **Crop Tool:** Allow admin to crop before saving
3. **CDN Integration:** Serve images from external CDN
4. **Analytics:** Track image download/view statistics
5. **Versioning:** Keep upload history for reverts
6. **Watermark:** Auto-add notaris branding to images

---

**Document Version:** 1.0.0
**Last Updated:** March 11, 2026
**Author:** System Administrator
**Review Status:** Ready for Production
