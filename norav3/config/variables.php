<?php
/**
 * Global Application Variables & Constants
 * Single source of truth for business information.
 */

// ═══════════════════════════════════════════════════════════════
// 1. OFFICE PROFILE
// ═══════════════════════════════════════════════════════════════
define('OFFICE_NAME',     'Sri Anah SH.M.Kn');
define('OFFICE_ADDRESS',  'Jl. Sultan Ageng Tirtayasa No. 123, Kedawung, Cirebon, Jawa Barat');
define('OFFICE_PHONE',    '0877-4877-8882');
define('OFFICE_EMAIL',    'notaris.srianah@gmail.com');

// ═══════════════════════════════════════════════════════════════
// 2. WHATSAPP CONFIG
// ═══════════════════════════════════════════════════════════════
define('WA_MSG_FALLBACK', 'Halo {nama_klien}, registrasi {nomor_registrasi} telah terdaftar. Terima kasih.');
define('WA_LINK_TEMPLATE', 'https://wa.me/{phone}?text={message}');

// ═══════════════════════════════════════════════════════════════
// 3. UI THEME VARIABLES (PHP Side)
// ═══════════════════════════════════════════════════════════════
define('THEME_PRIMARY', '#0A1F44'); // Navy
define('THEME_GOLD',    '#9C7C38'); // Gold
define('THEME_GOLD_LIGHT', '#D4AF37');

// ═══════════════════════════════════════════════════════════════
// 4. WORKFLOW FALLBACKS
// ═══════════════════════════════════════════════════════════════
define('WF_STEP_DRAFT', 'registrasi');
define('WF_STEP_FINAL', 'ditutup');

// ═══════════════════════════════════════════════════════════════
// 5. CMS SECTION IDS (for branding extraction)
// ═══════════════════════════════════════════════════════════════
// These IDs correspond to records in cms_section_content table (hardcoded per current schema)
// IDs: 13 = name (section 6), 20 = address (section 8), 21 = phone (section 8)
define('CMS_ID_BRAND_NAME',    13);
define('CMS_ID_BRAND_ADDRESS', 20);
define('CMS_ID_BRAND_PHONE',   21);

// ═══════════════════════════════════════════════════════════════
// 6. ROLE EXCLUSIONS (for SLA overdue tracking)
// ═══════════════════════════════════════════════════════════════
// Roles 3-8 are operational staff with extended/ no SLA. Excluded from overdue count.
define('EXCLUDED_ROLES', [3, 4, 5, 6, 7, 8]);

// Add more variables as needed...
