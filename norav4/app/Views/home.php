<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'norav4'); ?></title>
    <link rel="stylesheet" href="<?php echo ASSET_URL; ?>/css/responsive.css">
    <link rel="stylesheet" href="<?php echo ASSET_URL; ?>/css/auth.css">
    <link rel="stylesheet" href="<?php echo ASSET_URL; ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo ASSET_URL; ?>/css/buttons.css">
</head>
<body>
    <header>
        <nav>
            <h1><?php echo APP_NAME; ?></h1>
            <a href="<?php echo APP_URL; ?>/lacak">Lacak Registrasi</a>
            <a href="<?php echo APP_URL; ?>/office">Office Login</a>
        </nav>
    </header>

    <main class="hero">
        <div class="container">
            <h1>Layanan Notaris & PPAT Terbaik</h1>
            <p>norav4 - Re-engineered for Production Excellence</p>
            <p>Secure • Modular • Fast • Responsive</p>
            <div class="cta">
                <a href="<?php echo APP_URL; ?>/lacak" class="btn-primary">Lacak Perkara Anda</a>
                <a href="<?php echo APP_URL; ?>/office" class="btn-secondary">Masuk Office</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?></p>
    </footer>

    <script src="<?php echo ASSET_URL; ?>/js/main.js"></script>
</body>
</html>

