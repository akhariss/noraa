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

// Add more variables as needed...
