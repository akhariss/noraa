<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '403 - Forbidden' ?> - <?= APP_NAME ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/error.css">
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>

            <div class="error-code"><?= $statusCode ?? '403' ?></div>
            <h1 class="error-title"><?= $title ?? 'Akses Ditolak' ?></h1>
            <p class="error-message">
                <?= $message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda memerlukan akses.' ?>
            </p>

            <div class="error-actions">
                <?php if ($showBackButton ?? true): ?>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Kembali ke Halaman Sebelumnya
                    </a>
                <?php endif; ?>

                <?php if ($isLoggedIn ?? false): ?>
                    <a href="<?= APP_URL ?>/index.php?gate=dashboard" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Ke Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/index.php?gate=login" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        Login ke Dashboard
                    </a>
                <?php endif; ?>
            </div>

            <div class="error-footer">
                <p>
                    <a href="<?= APP_URL ?>/index.php?gate=home">← Kembali ke Homepage</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
