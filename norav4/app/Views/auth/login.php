<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login' ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Force reset to override any external CSS */
        html, body {
            margin: 0; padding: 0; width: 100%; height: 100%;
            overflow: hidden;
        }
        .auth-container {
            width: 100vw; height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #0F1F28 0%, #1B3A4B 50%, #2D5A6B 100%);
            position: relative;
            z-index: 1;
        }
        .auth-container::before {
            content: ''; position: absolute; width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(156, 124, 56, 0.12) 0%, transparent 70%);
            top: -15%; right: -10%;
            animation: pulse 12s infinite alternate;
            z-index: -1;
        }
        .auth-container::after {
            content: ''; position: absolute; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            bottom: -10%; left: -5%;
            animation: pulse 10s infinite alternate-reverse;
            z-index: -1;
        }
        @keyframes pulse { from { opacity: 0.3; transform: scale(1); } to { opacity: 0.6; transform: scale(1.1); } }
        
        .auth-card {
            background: #fff; width: 100%; max-width: 420px; padding: 50px 40px; border-radius: 28px;
            box-shadow: 0 40px 120px rgba(0,0,0,0.5); animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(255,255,255,0.1);
            margin: 15px;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        
        .logo-box { 
            width: 70px; height: 70px; 
            background: linear-gradient(135deg, #1B3A4B, #2D5A6B); 
            border-radius: 20px; margin: 0 auto 25px; 
            display: flex; align-items: center; justify-content: center; 
            color: #9C7C38;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .auth-title { font-family: 'Cormorant Garamond', serif; font-size: 28px; text-align: center; color: #1B3A4B; margin-bottom: 8px; font-weight: 700; }
        .auth-subtitle { text-align: center; font-size: 14px; color: #7f8c8d; margin-bottom: 40px; letter-spacing: 1px; text-transform: uppercase; font-weight: 600; }
        
        .form-group { margin-bottom: 25px; position: relative; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #95a5a6; margin-bottom: 10px; letter-spacing: 1px; }
        .form-input { 
            width: 100%; padding: 15px 20px; border: 1.5px solid #edf2f7; border-radius: 14px; 
            font-size: 16px; font-weight: 600; transition: all 0.3s ease;
            background: #f8fafc;
            box-sizing: border-box;
        }
        .form-input:focus { outline: none; border-color: #9C7C38; background: #fff; box-shadow: 0 0 0 4px rgba(156, 124, 56, 0.1); }
        
        .password-toggle {
            position: absolute; right: 18px; top: 38px; cursor: pointer; color: #94a3b8;
            display: flex; align-items: center; height: 50px; z-index: 10;
        }
        .password-toggle:hover { color: #1B3A4B; }
        
        .btn-submit { 
            width: 100%; padding: 16px; 
            background: linear-gradient(135deg, #1B3A4B, #2D5A6B); 
            color: #fff; border: none; border-radius: 14px; 
            font-weight: 800; font-size: 16px; cursor: pointer; transition: all 0.3s ease; 
            margin-top: 15px;
            box-shadow: 0 12px 30px rgba(27, 58, 75, 0.3);
        }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(27, 58, 75, 0.4); }
        
        @media (max-width: 480px) {
            .auth-card { padding: 30px 20px; border-radius: 20px; margin: 10px; max-width: 340px; }
            .logo-box { width: 50px; height: 50px; margin-bottom: 15px; }
            .logo-box svg { width: 24px; height: 24px; }
            .auth-title { font-size: 20px; }
            .auth-subtitle { font-size: 11px; margin-bottom: 25px; }
            .form-group { margin-bottom: 18px; }
            .form-group label { font-size: 10px; margin-bottom: 6px; }
            .form-input { padding: 10px 14px; font-size: 14px; border-radius: 10px; }
            .password-toggle { height: 38px; top: 32px; right: 12px; }
            .btn-submit { padding: 12px; font-size: 14px; border-radius: 10px; }
        }
        
        .alert { padding: 14px 18px; border-radius: 12px; font-size: 14px; margin-bottom: 25px; font-weight: 600; text-align: center; }
        .alert-error { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
    </style>
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo-box">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </div>
            <h1 class="auth-title"><?= htmlspecialchars(APP_NAME) ?></h1>
            <p class="auth-subtitle">Dashboard Login</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/login" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required autofocus placeholder="Username anda">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••">
                    <div class="password-toggle" id="togglePassword">
                        <!-- eyeClosed (slashed) is default for hidden password if user says it was reversed -->
                        <svg id="eyeClosed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                        <svg id="eyeOpen" style="display:none;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Masuk ke Dashboard</button>
            </form>

            <div style="text-align: center; margin-top: 30px;">
                <a href="<?= APP_URL ?>/" style="font-size: 13px; color: #94a3b8; text-decoration: none; font-weight: 700; transition: color 0.3s;" onmouseover="this.style.color='#9C7C38'" onmouseout="this.style.color='#94a3b8'">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeOpen = document.querySelector('#eyeOpen');
        const eyeClosed = document.querySelector('#eyeClosed');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'password') {
                eyeClosed.style.display = 'block';
                eyeOpen.style.display = 'none';
            } else {
                eyeClosed.style.display = 'none';
                eyeOpen.style.display = 'block';
            }
        });
    </script>
</body>
</html>
