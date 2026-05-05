<?php
/**
 * Tracking Error View
 */
require_once VIEWS_PATH . '/company_profile/cms_helpers.php';
$brandName = cmsContent($homepageData ?? [], 'footer', 'brand', 'Notaris Sri Anah SH.M.Kn');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - <?= htmlspecialchars($brandName) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/company-profile.css">
    <style>
        body { background: var(--cream); display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'DM Sans', sans-serif; }
        .error-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); text-align: center; max-width: 450px; border-top: 5px solid #e53e3e; }
        h1 { color: #e53e3e; margin-bottom: 15px; font-family: 'Cormorant Garamond', serif; }
        p { color: var(--text-light); margin-bottom: 25px; }
        .btn-back { display: inline-block; background: var(--primary); color: var(--gold); padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="error-card">
        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#e53e3e" stroke-width="2" style="margin-bottom: 20px;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <h1>Akses Ditolak</h1>
        <p>Token tracking Anda tidak valid atau sudah kadaluarsa. Silakan lakukan verifikasi ulang melalui halaman Lacak Registrasi.</p>
        <a href="<?= APP_URL ?>/index.php?gate=lacak" class="btn-back">Kembali ke Lacak Registrasi</a>
    </div>
</body>
</html>
