# Component Diagram - Sistem Tracking Status Dokumen Kantor Notaris

## Deskripsi
Diagram komponen ini menggambarkan arsitektur software dan hubungan antar komponen.

## Mermaid Diagram

```mermaid
graph TB
    subgraph Client["Client Tier"]
        Browser[Web Browser]
        HTML[HTML/CSS/JS]
    end

    subgraph Server["Web Server Tier"]
        Apache[Apache/Nginx]
        SSL[SSL/TLS]
    end

    subgraph App["Application Tier - PHP/Laravel"]
        subgraph Controllers["Controllers"]
            TrackingCtrl[TrackingController]
            RegistrasiCtrl[RegistrasiController]
            UpdateCtrl[UpdateStatusController]
            DashboardCtrl[DashboardController]
            AuthCtrl[AuthController]
        end

        subgraph Services["Services"]
            TrackingSvc[TrackingService]
            RegistrasiSvc[RegistrasiService]
            UpdateSvc[UpdateStatusService]
            NotifSvc[NotificationService]
            WorkflowSvc[WorkflowEngine]
            AuthSvc[AuthService]
        end

        subgraph API["API Layer"]
            REST[REST API Endpoints]
            Middleware[API Middleware]
        end
    end

    subgraph Data["Data Tier"]
        MySQL[(MySQL Database)]
        Redis[(Redis Cache)]
    end

    subgraph Storage["Storage Tier"]
        Local[Local Storage]
        Cloud[Cloud Storage S3]
    end

    subgraph External["External Services"]
        Email[Email Service SMTP]
        SMS[SMS Gateway API]
    end

    subgraph Modules["Additional Modules"]
        CMS[CMS Module]
        Backup[Backup Module]
    end

    Browser --> Apache
    Apache --> TrackingCtrl
    Apache --> RegistrasiCtrl
    Apache --> UpdateCtrl
    Apache --> DashboardCtrl
    Apache --> AuthCtrl

    TrackingCtrl --> TrackingSvc
    RegistrasiCtrl --> RegistrasiSvc
    UpdateCtrl --> UpdateSvc
    DashboardCtrl --> DashboardSvc
    AuthCtrl --> AuthSvc

    TrackingSvc --> MySQL
    RegistrasiSvc --> MySQL
    UpdateSvc --> MySQL
    
    TrackingSvc --> Redis
    DashboardCtrl --> Redis

    UpdateSvc --> NotifSvc
    RegistrasiSvc --> NotifSvc
    
    NotifSvc --> Email
    NotifSvc --> SMS

    RegistrasiSvc --> WorkflowSvc
    
    MySQL --> Local
    MySQL --> Cloud

    CMS --> MySQL
    Backup --> MySQL
    Backup --> Local
    Backup --> Cloud
```

## Penjelasan Komponen

### Client Tier

| Komponen | Teknologi | Fungsi |
|----------|-----------|--------|
| **Web Browser** | Chrome, Firefox, Safari, Edge | Client access |
| **HTML/CSS/JS** | Bootstrap 5, jQuery | Frontend UI |

### Web Server Tier

| Komponen | Teknologi | Fungsi |
|----------|-----------|--------|
| **Apache/Nginx** | Apache 2.4 / Nginx 1.20 | HTTP server |
| **SSL/TLS** | Let's Encrypt | HTTPS encryption |

### Application Tier

#### Controllers

| Controller | Use Case | Route |
|------------|----------|-------|
| **TrackingController** | UC01, UC05, UC07 | /api/tracking |
| **RegistrasiController** | UC02, UC06 | /api/registrasi |
| **UpdateStatusController** | UC03, UC12 | /api/perkara/:id/status |
| **DashboardController** | UC04, UC10 | /api/dashboard |
| **AuthController** | Login/Logout | /api/auth |

#### Services

| Service | Fungsi |
|---------|--------|
| **TrackingService** | Query tracking, format timeline |
| **RegistrasiService** | Create perkara, generate tracking number |
| **UpdateStatusService** | Update status, validate transition |
| **NotificationService** | Send email/SMS notification |
| **WorkflowEngine** | Apply workflow template |
| **AuthService** | Authentication & authorization |

### Data Tier

| Komponen | Teknologi | Fungsi |
|----------|-----------|--------|
| **MySQL** | MySQL 8.0 | Primary database |
| **Redis** | Redis 6.x | Cache & session storage |

### Storage Tier

| Komponen | Fungsi |
|----------|--------|
| **Local Storage** | Server file storage |
| **Cloud Storage S3** | Backup & scalable storage |

### External Services

| Service | Provider | Fungsi |
|---------|----------|--------|
| **Email Service** | SMTP/PHPMailer | Send email notification |
| **SMS Gateway** | Twilio/Local API | Send SMS notification |

### Additional Modules

| Module | Use Case | Fungsi |
|--------|----------|--------|
| **CMS Module** | UC09 | Content management |
| **Backup Module** | UC08 | Automated backup |

## Arsitektur Layer

```
┌─────────────────────────────────────────┐
│         Presentation Layer               │
│    (Web Browser, HTML/CSS/JS)            │
├─────────────────────────────────────────┤
│         Web Server Layer                 │
│    (Apache/Nginx, SSL)                   │
├─────────────────────────────────────────┤
│         Application Layer                │
│    (Controllers, Services, API)          │
├─────────────────────────────────────────┤
│         Data Access Layer                │
│    (Repositories, MySQL, Redis)          │
├─────────────────────────────────────────┤
│         Infrastructure Layer             │
│    (Storage, Email, SMS, Backup)         │
└─────────────────────────────────────────┘
```

## Teknologi Stack

| Layer | Teknologi |
|-------|-----------|
| **Frontend** | HTML5, CSS3, JavaScript, Bootstrap 5 |
| **Backend** | PHP 8.x, Laravel Framework |
| **Database** | MySQL 8.0 |
| **Cache** | Redis 6.x |
| **Web Server** | Apache 2.4 / Nginx |
| **Email** | PHPMailer / SMTP |
| **SMS** | Twilio / Local SMS API |
| **Storage** | Local / AWS S3 |
