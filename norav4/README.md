# norav4 - MVC PHP Framework (Re-engineer norav3)

## Fitur Best-of-Best:
- ✅ **Secure**: CSRF, XSS, SQLi protection, rate-limit, secure session
- ✅ **Modular**: Clean MVC, Services layer, Traits
- ✅ **Lightweight**: No Composer, simple autoloader, optimized queries
- ✅ **Responsive UI**: Preserve perfect norav3 design + CMS editable
- ✅ **Shared Hosting Ready**: Upload → import DB → edit .env → done!
- ✅ **Fast**: Cache, indexed DB, lazy load

## Setup Shared Hosting (No Terminal!):

1. **Upload Files**: Zip norav4/ → extract ke public_html/norav4/
2. **Database**:
   ```
   - Buat DB baru: nora_v4 (MySQL/MariaDB)
   - Import: norav4/database/nora_v4.sql
   ```
3. **Config**: Copy `.env.example` → `.env`, edit DB & keys
4. **Test**: Akses `yourdomain.com/norav4/public/` → Login admin/notaris
5. **Default Login**:
   - admin / admin (staff)
   - notaris / notaris (admin)

## Struktur:
```
public/      → index.php (single entry), assets/
app/
├── Config/  → settings
├── Controllers/ → actions
├── Models/ → DB entities
├── Core/   → Router/DB/Auth
└── Services/ → business logic
database/    → SQL schema
```

## Update TODO.md untuk progress:
```
Edit TODO.md line progress
```

**Production Ready! 🚀**

