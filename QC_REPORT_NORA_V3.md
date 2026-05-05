# QC Report — Nora V3 Application
**Auditor:** AI QC Agent  
**Scope:** Full codebase scan with focus on `registrasi_print.php` + connected files  
**Reference Documents:** `dosabesar.md`, `skills.md`, `todonya.md`  
**Date:** 2026-05-03

---

> [!IMPORTANT]
> **Mode: QC ONLY.** This report identifies problems and instructs the refactor agent. No code has been modified.

---

## Executive Summary

The codebase is **partially modernized** — it has a recognizable MVC skeleton (Router, Services, Security, modules), but several critical patterns from the old codebase survive inside the view layer. The largest risks are **raw output without escaping (XSS)**, **inline business logic in views**, **hardcoded magic numbers inside views instead of the Service layer**, **duplicated SLA/overdue logic**, and **a footer template included twice**. Security infrastructure (Auth, CSRF, RateLimiter) is well-built but **not consistently applied at the view/output level**.

| Severity | Count |
|---|---|
| 🔴 CRITICAL | 4 |
| 🟠 HIGH | 7 |
| 🟡 MEDIUM | 6 |
| 🟢 LOW | 4 |

---

## 🔴 CRITICAL Findings

### C-01 · Raw XSS Output Everywhere in Views
**Files:** `registrasi_print.php` (lines 321–554), `registrasi.php` (lines 167–384)  
**Dosa:** #3 (Not Secure), #1 (Spaghetti Code)

All user-sourced data from the database is echoed directly with `<?= $row['klien'] ?>`, `<?= $row['nomor'] ?>`, `<?= $cmsBranding['name'] ?>`, etc. — **zero output escaping**. The `e()` helper in `helpers.php` (line 20) exists for this exact purpose but is **never called** in either report view.

**Instruction for Refactor Agent:**
- Replace every `<?= $var ?>` echo of database-sourced data with `<?= e($var) ?>`.
- Specifically target: `$cmsBranding['name']`, `$cmsBranding['address']`, `$cmsBranding['phone']`, `$row['klien']`, `$row['nomor']`, `$row['layanan']`, `$d['nama_layanan']`, `$s['label']`.
- `number_format()` output is safe (numeric), `date()` output is safe — these do NOT need wrapping.

---

### C-02 · Hardcoded Magic Numbers for Behavior Roles (SLA Filter)
**Files:** `registrasi_print.php` (lines 33, 37), `registrasi.php` (lines 20, 24, 286, 358)  
**Dosa:** #2 (Hardcoded Values), #4 (Duplication)

The array `[3, 4, 5, 6, 7, 8]` for `$excludedRoles` is **defined independently in both view files** and also partially duplicated inside `ReportService.php` (lines 230, 303). This magic number set is the core SLA exclusion rule but has **no single source of truth**.

**Instruction for Refactor Agent:**
- In `ReportService.php`, define a class constant: `private const SLA_EXCLUDED_BEHAVIOR_ROLES = [3, 4, 5, 6, 7, 8];`
- Move the overdue counting logic (`$totalOverdue` loop) entirely into `ReportService.php` as a new method `getOverdueCount(array $matrix): int`.
- Pass the computed `$totalOverdue` and `$overdueRate` to views via the controller's data array — views must not compute business metrics.
- Remove the `$excludedRoles` definition from both view files.

---

### C-03 · Business Logic Executed Inside View Files
**Files:** `registrasi_print.php` (lines 1–77), `registrasi.php` (lines 13–37)  
**Dosa:** #1 (Spaghetti), #5 (No MVC separation)

Both view files contain a large PHP block at the top that:
1. Opens a database connection directly (`$conn = \App\Adapters\Database::getInstance()`).
2. Executes raw queries with hardcoded CMS IDs (lines 11, 15, 19 of `registrasi_print.php`).
3. Recomputes metrics (`$totalMasuk`, `$selesaiCount`, `$totalOverdue`, `$completionRate`, `$piutang`, `$allDistribution`, `$allStageCounts`) that should come from the controller/service.

**Instruction for Refactor Agent:**
- The DB queries for CMS branding (IDs 13, 20, 21) must be extracted to `ReportService.php` as `getCmsBranding(): array`. The controller passes this to the view via `$cmsBranding`.
- All metric calculations (`$totalMasuk`, `$completionRate`, `$piutang`, `$allDistribution`, `$allStageCounts`) must be computed in `ReportService.php` and injected into the view. Views must only render, not calculate.
- The `$allDistribution` merge loop (lines 51–64 of `registrasi_print.php`) belongs in `ReportService::getServiceDistribution()`.
- The `$allStageCounts` loop (lines 67–76) belongs in `ReportService::getMatrixTimeline()` as an additional key in the returned array.

---

### C-04 · Hardcoded CMS Row IDs in View (No Abstraction)
**File:** `registrasi_print.php` (lines 11, 15, 19)  
**Dosa:** #2 (Hardcoded), #3 (Not Secure)

```php
$conn->query("SELECT content_value FROM cms_section_content WHERE id = 13")
$conn->query("SELECT content_value FROM cms_section_content WHERE id = 20")
$conn->query("SELECT content_value FROM cms_section_content WHERE id = 21")
```

Integer IDs `13`, `20`, `21` are **magic numbers** with no context. If the CMS table is re-seeded or rows are re-ordered, the wrong data silently replaces the correct one. These are also **raw queries** (no prepared statements), though the IDs are literals so SQL injection is not possible here — the architectural problem is the brittleness.

**Instruction for Refactor Agent:**
- Add a `content_key` or `section_slug` column approach (or use a named lookup via CMS constants) to `getCmsBranding()` in `ReportService.php`.
- Define constants in `config/variables.php`: `CMS_ID_BRAND_NAME = 13`, `CMS_ID_BRAND_ADDRESS = 20`, `CMS_ID_BRAND_PHONE = 21`.
- Use those constants in the service query until a slug-based lookup is implemented.
- Remove all direct DB calls from the view entirely.

---

## 🟠 HIGH Findings

### H-01 · Double Footer Include
**File:** `registrasi.php` (lines 383–385)

```php
<?php require VIEWS_PATH . '/templates/footer.php'; ?>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
```

The footer template is included **twice**. This will render duplicate closing `</body></html>` tags, duplicate scripts, and potentially break any JavaScript that attaches to `DOMContentLoaded`.

**Instruction for Refactor Agent:** Delete line 385 (the second `require` of `footer.php`).

---

### H-02 · ReportService Does Not Log Exception in `getServiceDistribution()`
**File:** `ReportService.php` (lines 141–143)

```php
} catch (Exception $e) {
    return [];
}
```

The catch block silently swallows exceptions with **no logging**. Compare to `getSummary()` (line 112) which correctly calls `Logger::error()`. A silent failure here means the service distribution table will appear empty with no diagnostic trace.

**Instruction for Refactor Agent:** Add `Logger::error('ReportService::getServiceDistribution failed', ['error' => $e->getMessage()]);` inside the catch block, consistent with the pattern in `getSummary()`.

---

### H-03 · N+1 Query Problem in `getMatrixTimeline()`
**File:** `ReportService.php` (lines 258–267)

For every registration fetched (the outer loop at line 254), an individual `SELECT` query is issued to `registrasi_history` for that registration's ID. With 50 active registrations, this is **51 queries**. With 200, it's **201 queries**.

**Instruction for Refactor Agent:**
- Fetch all history rows for the full period in **one query** grouped by `registrasi_id`, using an `IN (...)` clause or a subquery scoped to the same date range.
- Store results in a keyed array `$historyByReg[$registrasi_id][]` before the loop.
- Replace the inner `$hStmt->execute([$regId])` with `$history = $historyByReg[$regId] ?? []`.

---

### H-04 · `cookie_secure` Hardcoded to `0` (Insecure HTTPS Cookies)
**File:** `app/Security/Auth.php` (line 30)

```php
ini_set('session.cookie_secure', '0'); // Set to 1 if HTTPS
```

This is a **comment-flagged known debt** that has not been resolved. In production, session cookies will be sent over HTTP, exposing sessions to interception.

**Instruction for Refactor Agent:**
- Replace with: `ini_set('session.cookie_secure', APP_ENV === 'production' ? '1' : '0');`
- Or better: read from `$_SERVER['HTTPS']` for dynamic detection: `ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');`

---

### H-05 · `DB_PASS` Is Empty String — Credentials in Config File
**File:** `config/app.php` (lines 42–46)

```php
define('DB_HOST',  '127.0.0.1');
define('DB_USER',  'root');
define('DB_PASS',  '');
```

Database credentials are hardcoded in a PHP file tracked by version control. An empty `root` password is a critical insecurity for any non-local environment.

**Instruction for Refactor Agent:**
- Introduce a `.env` file at the project root with `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
- Add `.env` to `.gitignore`.
- Load via a simple `parse_ini_file()` or a `vlucas/phpdotenv`-style loader at the top of `config/app.php`, falling back to the existing constants if `.env` is absent (for backward compat).
- Add `.env.example` template with placeholder values for onboarding.

---

### H-06 · `SECURITY_KEY_*` Constants Hardcoded in Config (Secret Exposure Risk)
**File:** `config/app.php` (lines 104–107)

256-bit security keys are hardcoded in a PHP file. If this file is ever committed to a public repo or the server exposes PHP source, all tokens derived from these keys are compromised.

**Instruction for Refactor Agent:** Migrate `SECURITY_KEY_ID`, `SECURITY_KEY_IMG`, `SECURITY_KEY_TRACKING`, `SECURITY_KEY_SHORT` to the `.env` file as part of the H-05 fix.

---

### H-07 · `APP_NAME` and Production URL Hardcoded
**File:** `config/app.php` (lines 21, 63)

```php
define('APP_NAME', 'Notaris Sri Anah SH.M.Kn');
define('APP_URL', 'https://notaris.example.com');  // CHANGE FOR PRODUCTION
```

Business name and production URL are hardcoded with a comment saying "change for production" — this is a deployment risk if the config is copy-pasted without modification.

**Instruction for Refactor Agent:** Move `APP_NAME` and the production `APP_URL` to the `.env` file as part of the H-05 fix.

---

## 🟡 MEDIUM Findings

### M-01 · Inline CSS `style=""` Used Extensively Instead of CSS Classes
**Files:** `registrasi.php` (lines 143–384), `registrasi_print.php` (lines 349–554)

Hundreds of `style="..."` attributes are scattered throughout table cells, divs, and spans. This makes theming impossible without a global search-and-replace and violates the DRY principle.

**Instruction for Refactor Agent:** For `registrasi_print.php`, consolidate repeated inline styles (e.g., `font-size: 6.5pt; font-weight: normal`) into the `<style>` block as named classes. Minimum: extract table cell font-size patterns, financial table row styles, and section title variations.

---

### M-02 · `shortLabels` Array in `ReportService` Is a Hardcoded Fallback That Will Drift
**File:** `ReportService.php` (lines 27–40)

```php
private array $shortLabels = [
    'DRAFT' => '📁 Draft',
    ...
];
```

This static array will diverge from the database as workflow steps are added/renamed via the CMS. The `label` column in `workflow_steps` table already has this data.

**Instruction for Refactor Agent:** Remove `$shortLabels`. In `getMatrixTimeline()`, use the database `label` directly (`$s['label']`) or add a `short_label` column to `workflow_steps` managed via the CMS workflow editor.

---

### M-03 · `$isRowOver` Computed But Never Used in `registrasi.php`
**File:** `registrasi.php` (lines 267–272)

```php
$isRowOver = false;
foreach ($matrix['steps_aktif'] as $s) {
    if (isset($row['durations'][$s['id']]) && $row['durations'][$s['id']] > (int)$s['sla_days']) {
        $isRowOver = true; break;
    }
}
```

`$isRowOver` is computed per row but **never referenced** in the row's HTML output — the `<tr>` tag does not use it for highlighting.

**Instruction for Refactor Agent:** Either (a) add `style="<?= $isRowOver ? 'background: #fff1f1;' : '' ?>"` to the `<tr>` to highlight overdue rows, or (b) remove the dead code entirely. Choose (a) as it improves the audit UX.

---

### M-04 · `helpers.php` Mixes `function` Namespace with OOP Delegation
**File:** `app/Core/Utils/helpers.php` (entire file)

The file has procedural functions like `getClientIP()`, `logError()`, and `logAudit()` that duplicate logic already inside `App\Adapters\Logger` and `App\Security\Auth`. The `checkRateLimit()` function is even marked `@deprecated` (line 109).

**Instruction for Refactor Agent:**
- Remove deprecated `checkRateLimit()` function entirely.
- Audit all remaining helpers: keep only those that are thin delegator wrappers (`isLoggedIn()`, `getCurrentUser()`) or pure utility functions (`e()`, `formatDateID()`, `slugify()`).
- Remove `logError()` and `logAudit()` if equivalent calls exist in `Logger::error()` and the audit log system — do not leave duplicates.

---

### M-05 · `getMatrixTimeline()` Has No Error Logging on Catch
**File:** `ReportService.php` (lines 316–323)

```php
} catch (Exception $e) {
    return ['steps_aktif' => [], ...];
}
```

Silent failure — same problem as H-02, specific to the most critical report function.

**Instruction for Refactor Agent:** Add `Logger::error('ReportService::getMatrixTimeline failed', ['error' => $e->getMessage()]);` inside the catch.

---

### M-06 · `registrasi.php` Missing CSRF Protection on Date Filter Form
**File:** `registrasi.php` (lines 149–154)

The filter `<form method="GET">` has no CSRF token. While GET requests are lower risk, consistent CSRF protection is required per `skills.md` "Secure by Default."

**Instruction for Refactor Agent:** Add a CSRF token to the filter form, and validate it in the controller's `laporanRegistrasi()` method. Use the existing `CSRF.php` class.

---

## 🟢 LOW Findings

### L-01 · `APP_ENV` Hardcoded to `'development'` — No Deploy Guard
**File:** `config/app.php` (line 20)

```php
define('APP_ENV', 'development');
```

Will be resolved by the H-05 `.env` migration, but flag explicitly as it currently means debug modes may be active in production.

---

### L-02 · SQL Database Name Has a Space: `'nora3.0'`
**File:** `config/app.php` (line 43)

```php
define('DB_NAME', 'nora3.0');
```

Database names with `.` can cause issues in some MySQL configurations. The name `nora3_0` is the SQL-safe convention.

**Instruction for Refactor Agent:** Rename the database to `nora3_0` (or `nora_v3`) and update this constant + the `.env` file. Provide a migration note in `CHANGELOG.md`.

---

### L-03 · `footer.php` Double-Include Is a Side Effect of Copy-Paste in View Files
**File:** `registrasi.php` (line 385)

Already flagged as H-01. Additionally noted as a low-severity process issue: this suggests views are copy-pasted without review.

**Instruction for Refactor Agent:** Establish a view composition checklist — each view should have exactly one `header.php` include and one `footer.php` include. Consider a `View::render()` wrapper that enforces this.

---

### L-04 · `composer.json` Is Minimal — No Autoload Standard Defined
**File:** `composer.json` (59 bytes)

The file is present but minimal. Without a proper `autoload` PSR-4 definition, the custom `Autoloader.php` is doing manual work that Composer should handle.

**Instruction for Refactor Agent:** Update `composer.json` to include:
```json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "modules/"
    }
  }
}
```
Then run `composer dump-autoload` and remove the custom `Autoloader.php` if Composer covers all namespaces.

---

## ✅ Things Done Right (Do Not Regress)

These patterns are **correct** and must be preserved by the refactor agent:

| What | Where |
|---|---|
| Prepared statements in all `ReportService` queries | `ReportService.php` lines 59, 64, 75, 86, 96 |
| `try/catch` wrapping all DB operations | All `ReportService` methods |
| Session fingerprinting & tamper detection | `Auth.php` lines 64–79, 241–244 |
| Session regeneration against fixation | `Auth.php` lines 84–100 |
| `hash_equals()` for timing-safe comparison | `Auth.php` lines 66, 192 |
| Route-level auth + role enforcement | `routes.php` with `['auth' => true, 'role' => ROLE_OWNER]` |
| Workflow steps from DB (no hardcoded status enum) | `helpers.php` lines 412–458 |
| Static caching of DB lookups | `helpers.php` `static $cache = null` pattern |
| CSRF class exists and is structured | `app/Security/CSRF.php` |
| Logger adapter centralized | `app/Adapters/Logger.php` |
| `declare(strict_types=1)` on all service files | Multiple files |

---

## Priority Order for Refactor Agent

```
1. C-01 → Escape all view output with e()
2. C-03 → Move DB logic out of views into ReportService
3. C-04 → Replace magic CMS IDs with constants / service method
4. C-02 → Centralize SLA exclusion logic in ReportService
5. H-01 → Delete duplicate footer.php include
6. H-03 → Fix N+1 query in getMatrixTimeline
7. H-02 / M-05 → Add missing Logger calls in catch blocks
8. H-04 → Fix cookie_secure for production
9. H-05 / H-06 / H-07 → Introduce .env for secrets
10. M-01 → Consolidate inline styles into CSS classes
11. M-02 → Remove shortLabels hardcoded array
12. M-03 → Fix $isRowOver dead code
13. M-04 → Clean up helpers.php duplicates
14. M-06 → Add CSRF to filter form
15. L-01–L-04 → Low priority cleanup
```

---

*QC Report generated by AI QC Agent. No code was modified during this analysis.*
