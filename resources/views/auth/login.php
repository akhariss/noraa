<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard - <?= APP_NAME ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                </div>
                <h1><?= APP_NAME ?></h1>
                <p>Dashboard Notaris & PPAT</p>
            </div>

            <form id="loginForm" class="auth-form" method="POST" action="<?= APP_URL ?>/index.php?gate=login">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <?php 
                // Show session timeout message
                $timeout = $_GET['timeout'] ?? $_GET['expired'] ?? null;
                if ($timeout):
                ?>
                <div class="session-timeout-message" style="background-color: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px; border-radius: 4px; margin-bottom: 16px; font-size: 14px;">
                    <strong>⚠️ Sesi Anda Telah Berakhir</strong><br>
                    Sesi Anda telah kadaluarsa karena terlalu lama tidak digunakan. Silakan login kembali.
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-login">Login ke Dashboard</button>
            </form>

            <div id="loginMessage" class="auth-message" style="display: none;"></div>

            <div class="auth-footer">
                <p><a href="<?= APP_URL ?>/index.php?gate=home">← Kembali ke Homepage</a></p>
                <p style="margin-top: 12px; font-size: 13px; color: var(--text-muted);">
                    Demo: staff/staff123 | admin/admin123
                </p>
            </div>
        </div>
    </div>

    <script>
    // Define APP_URL for JavaScript
    window.APP_URL = '<?= APP_URL ?>';
    </script>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const messageDiv = document.getElementById('loginMessage');

        messageDiv.style.display = 'block';
        messageDiv.className = 'auth-message';
        messageDiv.textContent = 'Logging in...';

        fetch('<?= APP_URL ?>/index.php?gate=login', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.className = 'auth-message ' + (data.success ? 'success' : 'error');
            messageDiv.textContent = data.message;

            if (data.success) {
                setTimeout(() => {
                    window.location.href = '<?= APP_URL ?>/index.php?gate=dashboard';
                }, 1000);
            }
        })
        .catch(error => {
            messageDiv.className = 'auth-message error';
            messageDiv.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
        });
    });
    </script>
</body>
</html>
