<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Error' ?> - <?= APP_NAME ?></title>

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
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>

            <div class="error-code"><?= $statusCode ?? '500' ?></div>
            <h1 class="error-title"><?= $title ?? 'Terjadi Kesalahan' ?></h1>
            <p class="error-message">
                <?= $message ?? 'Maaf, terjadi kesalahan pada server. Silakan coba lagi nanti.' ?>
            </p>

            <div class="error-actions">
                <?php if ($showBackButton ?? true): ?>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Kembali
                    </a>
                <?php endif; ?>

                <a href="<?= APP_URL ?>/index.php?gate=home" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Ke Homepage
                </a>
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
