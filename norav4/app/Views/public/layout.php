<?php
require_once __DIR__ . '/cms_helpers.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?></title>
    
    <!-- SEO & Meta Tags (Mirror V3) -->
    <meta name="description" content="Notaris & PPAT Sri Anah, SH.M.Kn - Pendamping Hukum Resmi untuk Properti, Usaha, dan Keluarga di Cirebon.">
    <meta name="keywords" content="Notaris Cirebon, PPAT Cirebon, Akta Properti, Pendirian Usaha, Sri Anah">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Assets -->
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/company-profile.css?v=<?= time() ?>">
</head>
<body>
    <?php require VIEWS_PATH . '/public/partials/navbar.php'; ?>

    <main>
        <?php 
        // Ensure $homepageData is available for the included content
        require $content; 
        ?>
    </main>

    <?php require VIEWS_PATH . '/public/partials/footer.php'; ?>

    <script>
        const APP_URL = "<?= APP_URL ?>";
    </script>
    <script src="<?= ASSET_URL ?>/js/company-profile.js?v=<?= time() ?>"></script>
</body>
</html>
