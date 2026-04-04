# Nora 2.0 - CodeIgniter 4 Migration Prompt

## Task

Migrate the Nora 2.0 application from custom PHP framework to **CodeIgniter 4**.

## Source Code Reference

See `SPEC.md` in this directory for complete technical specification.

## Project Context

- **Project Name**: Notaris Sri Anah SH.M.Kn - Registration Tracking System
- **Current Version**: 1.1.2
- **Database**: MySQL (norasblmupdate2)
- **Current Framework**: Custom PHP (not CodeIgniter)

## Current Architecture

```
nora2.0/
├── app/
│   ├── Adapters/Database.php      # Singleton PDO wrapper
│   ├── Adapters/Logger.php
│   ├── Core/Router.php            # Query-param routing (?gate=xxx)
│   ├── Core/Autoloader.php
│   ├── Core/View.php
│   ├── Core/Utils/
│   ├── Domain/Entities/           # Models (Registrasi, Klien, User, etc.)
│   ├── Security/Auth.php          # Session & auth
│   ├── Security/RBAC.php          # Role-based access
│   ├── Security/CSRF.php
│   ├── Security/RateLimiter.php
│   ├── Security/InputSanitizer.php
│   └── Services/                  # WorkflowService, CMSEditorService, etc.
├── config/app.php                 # All constants
├── config/routes.php              # Route registry
├── modules/                        # Controllers (Main, Auth, Dashboard, CMS, Finalisasi)
├── public/index.php                # Front controller
├── resources/views/                # Templates
└── storage/
```

## Target Architecture (CI4)

```
app/
├── Config/               (App.php, Database.php, Routes.php, Filters.php)
├── Controllers/           (Main, Auth, Dashboard, CMS, Finalisasi)
├── Models/               (RegistrasiModel, KlienModel, UserModel, etc.)
├── Services/             (WorkflowService, CMSEditorService, etc.)
├── Libraries/            (RBAC, custom Database wrapper)
├── Filters/              (AuthFilter, RateLimitFilter)
├── Entities/             (optional, for Entity casting)
└── Views/                (dashboard, auth, public, templates)
writable/
├── cache/
├── logs/
└── sessions/
public/
└── index.php
```

## Key Requirements

### 1. Database
- Use CI4 Database with `BaseBuilder`
- Keep existing tables: `registrasi`, `klien`, `users`, `layanan`, `workflow_steps`, `kendala`, `registrasi_history`, `audit_logs`, `cms_pages`, `cms_page_sections`, `cms_section_content`, `cms_section_items`, `message_templates`, `note_templates`, `app_settings`

### 2. Configuration
Port all constants from `config/app.php`:
- Status labels (workflow steps)
- Roles: `administrator` (owner), `staff`, `publik`
- Session lifetime (7200 seconds)
- Rate limits
- CMS pages config

### 3. Routing
Convert query-param routes (`?gate=xxx`) to CI4 routes:
- `/` → Main::home
- `/lacak` → Main::tracking
- `/detail` → Main::showRegistrasi
- `/login`, `/logout` → Auth
- `/dashboard/*` → Dashboard (protected)
- `/cms_editor/*` → CMS (admin only)
- `/finalisasi/*` → Finalisasi (admin only)

### 4. Authentication
- Create `AuthFilter` for protected routes
- Port session management with timeout (2 hours)
- Port fingerprinting for session security

### 5. RBAC
Port permission mapping:
- `administrator`: full access
- `staff`: operational access (create/edit registrasi, update status)
- `publik`: tracking only

### 6. Workflow System
Preserve all business logic:
- Dynamic steps from database
- `behavior_role` (1=start, 2=process, 3=success/repair, 4=archive, 5=handover, 6=closed, 7=cancelled)
- SLA days per step
- Backward movement restrictions
- Milestone timestamps (`selesai_batal_at`, `diserahkan_at`)

### 7. Tracking System
- Token-based verification
- 4-digit phone verification
- Process history log

### 8. CMS
- Dynamic page sections
- Layanan management
- Message/note templates per workflow step

## Security Requirements

- CSRF protection (use CI4 built-in)
- Input sanitization
- Rate limiting per endpoint
- Audit logging for all operations
- Secure session configuration

## Implementation Steps

1. **Setup CI4 project**
   ```bash
   composer create-project codeigniter4/appstarter nora-ci4
   ```

2. **Configure database** in `app/Config/Database.php`

3. **Create Config/App.php** - port all constants

4. **Create Config/Routes.php** - map all routes

5. **Create AuthFilter** for authentication

6. **Create Models** for each entity

7. **Create Services** with business logic

8. **Create Controllers** - port from modules/

9. **Create Views** - copy from resources/views

10. **Test** all functionality

## Important Notes

- Keep all SQL queries as-is (they work correctly)
- Preserve exact business logic in WorkflowService
- Maintain the workflow step progression rules
- Keep the tracking verification system intact
- Preserve CMS dynamic content system

## Database Connection (from config/app.php)

```php
DB_HOST = '127.0.0.1'
DB_NAME = 'norasblmupdate2'
DB_USER = 'root'
DB_PASS = ''
DB_CHARSET = 'utf8mb4'
```

## Files to Read First

- `SPEC.md` - complete technical specification
- `config/app.php` - all constants
- `config/routes.php` - all routes
- `app/Security/Auth.php` - session handling
- `app/Services/WorkflowService.php` - core business logic
- `modules/Dashboard/Controller.php` - main controller

## Output

Deliver a complete, working CodeIgniter 4 application that:
1. Authenticates users
2. Manages registrasi workflow
3. Provides public tracking
4. Supports CMS editing
5. Logs all operations

All features must work exactly as the current system.
