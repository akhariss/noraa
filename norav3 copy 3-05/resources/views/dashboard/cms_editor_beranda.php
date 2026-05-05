<?php
/**
 * CMS Editor - Homepage WYSIWYG
 * Duplicate homepage layout with contenteditable fields
 * READ & UPDATE only (no create/delete)
 */

// $currentUser and $auth are provided by the controller
// $currentUser = $auth->getCurrentUser();
$pageTitle = 'Editor - Halaman Beranda';
$activePage = 'app_settings';

// Load helper functions
$cmsHelper = function($data, $section, $key, $default = '') {
    return $data['sections'][$section]['content'][$key]['value'] ?? $default;
};

$cmsItems = function($data, $section, $type) {
    return array_filter($data['sections'][$section]['items'] ?? [], fn($item) => ($item['item_type'] ?? '') === $type);
};

$cmsExtra = function($item, $key, $default = '') {
    $extra = json_decode($item['extra_data'] ?? '{}', true);
    return $extra[$key] ?? $default;
};

require VIEWS_PATH . '/templates/header.php';
?>

<div class="cms-wysiwyg-wrapper">
    <!-- Toolbar (Fixed Top) -->
    <div class="cms-wysiwyg-toolbar">
        <div class="toolbar-left">
            <button onclick="window.location.href='<?= APP_URL ?>/index.php?gate=cms_editor'" class="btn-sm btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali
            </button>
        </div>
        <div class="toolbar-right">
            <button id="btn-undo" class="btn-sm btn-secondary" title="Undo (Ctrl+Z)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 7v6h6"></path>
                    <path d="M21 17a9 9 0 00-9-9 9 9 0 00-6 2.3L3 13"></path>
                </svg>
                Undo
            </button>
            <button id="btn-redo" class="btn-sm btn-secondary" title="Redo (Ctrl+Y)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 7v6h-6"></path>
                    <path d="M3 17a9 9 0 019-9 9 9 0 016 2.3l3-2.3"></path>
                </svg>
                Redo
            </button>
            <div class="toolbar-divider"></div>
            <button id="btn-cancel" class="btn-sm btn-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Batal
            </button>
            <button id="btn-save" class="btn-sm btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan
            </button>
        </div>
    </div>

    <!-- Message Alert -->
    <div id="editor-message" class="editor-message" style="display: none;"></div>

    <!-- WYSIWYG Content (Homepage Duplicate) -->
    <div class="cms-wysiwyg-content">
        <?php if (isset($pageData['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($pageData['error']) ?></div>
        <?php else: 
            $hero = [
                'badge' => $cmsHelper($pageData, 'hero', 'badge', 'Notaris Profesional'),
                'title' => $cmsHelper($pageData, 'hero', 'title', 'Notaris Online Terpercaya'),
                'subtitle' => $cmsHelper($pageData, 'hero', 'subtitle', 'Solusi Hukum Cepat'),
                'description' => $cmsHelper($pageData, 'hero', 'description', 'Deskripsi hero'),
                'wa_number' => $cmsHelper($pageData, 'hero', 'wa_number', '628574789811'),
            ];
        ?>
        <!-- HERO SECTION -->
        <section class="hero cms-hero-section">
            <div class="container">
                <div class="hero-inner">
                    <div class="hero-content">
                        <div class="hero-badge cms-editable" data-content-id="<?= $pageData['sections']['hero']['content']['badge']['id'] ?>">
                            <?= htmlspecialchars($hero['badge']) ?>
                        </div>
                        <h1 class="cms-editable" data-content-id="<?= $pageData['sections']['hero']['content']['title']['id'] ?>">
                            <?= htmlspecialchars($hero['title']) ?>
                        </h1>
                        <p class="hero-subtitle cms-editable" data-content-id="<?= $pageData['sections']['hero']['content']['subtitle']['id'] ?>">
                            <?= htmlspecialchars($hero['subtitle']) ?>
                        </p>
                        <p class="hero-desc cms-editable" data-content-id="<?= $pageData['sections']['hero']['content']['description']['id'] ?>">
                            <?= htmlspecialchars($hero['description']) ?>
                        </p>
                        <div class="hero-buttons">
                            <a href="https://wa.me/<?= str_replace([' ', '-'], '', $hero['wa_number']) ?>" class="btn-cta" target="_blank">
                                📱 Konsultasi via WhatsApp
                            </a>
                            <a href="#testimoni" class="btn-layanan">Lihat Testimoni</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MASALAH & LAYANAN SECTION -->
        <?php 
            $masalahData = [
                'title' => $cmsHelper($pageData, 'masalah', 'title', 'Masalah Apa yang Kami Selesaikan?'),
            ];
            $layananData = [
                'title' => $cmsHelper($pageData, 'layanan', 'title', 'Layanan Kami'),
            ];
            $masalahItems = $cmsItems($pageData, 'masalah', 'card');
            $layananItems = $cmsItems($pageData, 'layanan', 'card');
        ?>
        <section class="section masalah">
            <div class="container">
                <h2 class="section-title cms-editable" data-content-id="<?= $pageData['sections']['masalah']['content']['title']['id'] ?>">
                    <?= htmlspecialchars($masalahData['title']) ?>
                </h2>
                <div class="masalah-grid">
                    <?php foreach ($masalahItems as $item): ?>
                    <div class="masalah-card">
                        <h4 class="cms-editable" data-item-id="<?= $item['id'] ?>" data-field="title">
                            <?= htmlspecialchars($item['title'] ?? '') ?>
                        </h4>
                        <p class="cms-editable" data-item-id="<?= $item['id'] ?>" data-field="description">
                            <?= htmlspecialchars($item['description'] ?? '') ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section layanan">
            <div class="container">
                <h2 class="section-title cms-editable" data-content-id="<?= $pageData['sections']['layanan']['content']['title']['id'] ?>">
                    <?= htmlspecialchars($layananData['title']) ?>
                </h2>
                <div class="layanan-grid">
                    <?php foreach ($layananItems as $item): ?>
                    <div class="layanan-card">
                        <h4 class="cms-editable" data-item-id="<?= $item['id'] ?>" data-field="title">
                            <?= htmlspecialchars($item['title'] ?? '') ?>
                        </h4>
                        <p class="cms-editable" data-item-id="<?= $item['id'] ?>" data-field="description">
                            <?= htmlspecialchars($item['description'] ?? '') ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- TESTIMONI SECTION -->
        <?php 
            $testimoniData = [
                'title' => $cmsHelper($pageData, 'testimoni', 'title', 'Apa Kata Klien Kami?'),
            ];
            $testimoniItems = $cmsItems($pageData, 'testimoni', 'testimonial');
        ?>
        <section class="testimoni" id="testimoni">
            <div class="container">
                <h2 class="section-title cms-editable" data-content-id="<?= $pageData['sections']['testimoni']['content']['title']['id'] ?>">
                    <?= htmlspecialchars($testimoniData['title']) ?>
                </h2>
                <div class="testimoni-grid">
                    <?php foreach ($testimoniItems as $item): ?>
                    <div class="testimoni-card">
                        <p class="testimoni-text cms-editable" data-item-id="<?= $item['id'] ?>" data-field="description">
                            "<?= htmlspecialchars($item['description'] ?? '') ?>"
                        </p>
                        <div class="testimoni-author">
                            <div class="testimoni-name cms-editable" data-item-id="<?= $item['id'] ?>" data-field="title">
                                <?= htmlspecialchars($item['title'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ALUR KERJA SECTION -->
        <?php 
            $alurData = [
                'title' => $cmsHelper($pageData, 'alur', 'title', 'Cara Kerja Kami'),
            ];
            $alurItems = $cmsItems($pageData, 'alur', 'step');
        ?>
        <section class="section alur" id="alur">
            <div class="container">
                <h2 class="section-title cms-editable" data-content-id="<?= $pageData['sections']['alur']['content']['title']['id'] ?>">
                    <?= htmlspecialchars($alurData['title']) ?>
                </h2>
                <div class="alur-grid">
                    <?php foreach ($alurItems as $item): ?>
                    <div class="alur-item">
                        <h4 class="cms-editable" data-item-id="<?= $item['id'] ?>" data-field="title">
                            <?= htmlspecialchars($item['title'] ?? '') ?>
                        </h4>
                        <p class="cms-editable" data-item-id="<?= $item['id'] ?>" data-field="description">
                            <?= htmlspecialchars($item['description'] ?? '') ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- TENTANG SECTION -->
        <?php 
            $tentangData = [
                'title' => $cmsHelper($pageData, 'tentang', 'title', 'Tentang Kami'),
                'quote' => $cmsHelper($pageData, 'tentang', 'quote', '"Setiap klien adalah keluarga."'),
                'name' => $cmsHelper($pageData, 'tentang', 'name', 'Sri Anah SH.M.Kn'),
                'role' => $cmsHelper($pageData, 'tentang', 'role', 'Notaris & PPAT'),
                'experience' => $cmsHelper($pageData, 'tentang', 'experience', '15+'),
                'photo' => $cmsHelper($pageData, 'tentang', 'photo', ''),
            ];
            $tentangItems = $cmsItems($pageData, 'tentang', 'benefit');
        ?>
        <section class="section tentang" id="tentang">
            <div class="container">
                <div class="tentang-grid">
                    <div class="tentang-img">
                        <?php if (!empty($tentangData['photo'])): ?>
                            <!-- Photo Mode -->
                            <div class="tentang-photo">
                                <img id="tentang-photo-preview" src="<?= htmlspecialchars($tentangData['photo']) ?>" alt="<?= htmlspecialchars($tentangData['name']) ?>" style="max-width: 100%; border-radius: 8px;">
                                <div style="margin-top: 12px; text-align: center;">
                                    <button type="button" id="tentang-photo-upload-btn" class="btn-small" style="padding: 8px 12px; font-size: 12px;">
                                        Ganti Foto
                                    </button>
                                    <input type="file" id="tentang-photo-input" accept="image/jpeg,image/png,image/webp" style="display: none;">
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Icon Mode (default) -->
                            <div class="tentang-img-inner">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <button type="button" id="tentang-photo-upload-btn" class="btn-small" style="padding: 8px 12px; font-size: 12px; margin-top: 8px;">
                                    Upload Foto
                                </button>
                                <input type="file" id="tentang-photo-input" accept="image/jpeg,image/png,image/webp" style="display: none;">
                            </div>
                        <?php endif; ?>
                        
                        <div class="tentang-badge">
                            <strong class="cms-editable" data-content-id="<?= $pageData['sections']['tentang']['content']['experience']['id'] ?? 0 ?>">
                                <?= htmlspecialchars($tentangData['experience']) ?>
                            </strong>
                            <span>Tahun Pengalaman</span>
                        </div>
                    </div>
                    <div class="tentang-content">
                        <h2 class="cms-editable" data-content-id="<?= $pageData['sections']['tentang']['content']['title']['id'] ?? 0 ?>">
                            <?= htmlspecialchars($tentangData['title']) ?>
                        </h2>
                        <div class="tentang-narasi cms-editable" data-content-id="<?= $pageData['sections']['tentang']['content']['quote']['id'] ?? 0 ?>">
                            <?= htmlspecialchars($tentangData['quote']) ?>
                        </div>
                        <div class="tentang-name" style="display: none;">
                            <div class="cms-editable" data-content-id="<?= $pageData['sections']['tentang']['content']['name']['id'] ?? 0 ?>">
                                <?= htmlspecialchars($tentangData['name']) ?>
                            </div>
                            <div class="cms-editable" data-content-id="<?= $pageData['sections']['tentang']['content']['role']['id'] ?? 0 ?>">
                                <?= htmlspecialchars($tentangData['role']) ?>
                            </div>
                        </div>
                        <ul class="tentang-list">
                            <?php foreach ($tentangItems as $item): ?>
                            <li class="cms-editable" data-item-id="<?= $item['id'] ?>" data-field="title">
                                ✓ <?= htmlspecialchars($item['title'] ?? '') ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <?php 
            // Get phone from footer section (LAW 0.2 - Single Source of Truth)
            $footerPhone = $cmsHelper($pageData, 'footer', 'phone', '6285747898811');
            $ctaData = [
                'title' => $cmsHelper($pageData, 'cta', 'title', 'Siap Melayani Anda'),
                'subtitle' => $cmsHelper($pageData, 'cta', 'subtitle', 'Konsultasikan kebutuhan hukum Anda sekarang juga melalui WhatsApp'),
                'wa_number' => $footerPhone, // Dynamic from footer phone field
            ];
        ?>
        <section class="cta" id="kontak">
            <div class="container">
                <div class="cta-box">
                    <h2 class="cms-editable" data-content-id="<?= $pageData['sections']['cta']['content']['title']['id'] ?? 0 ?>">
                        <?= htmlspecialchars($ctaData['title']) ?>
                    </h2>
                    <p class="cms-editable" data-content-id="<?= $pageData['sections']['cta']['content']['subtitle']['id'] ?? 0 ?>">
                        <?= htmlspecialchars($ctaData['subtitle']) ?>
                    </p>
                    <a href="https://wa.me/<?= str_replace([' ', '-'], '', $ctaData['wa_number']) ?>" class="cta-btn" target="_blank">
                        📱 Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </section>

        <!-- FOOTER SECTION -->
        <?php 
            $footerData = [
                'brand' => $cmsHelper($pageData, 'footer', 'brand', 'Notaris Sri Anah SH.M.Kn'),
                'description' => $cmsHelper($pageData, 'footer', 'description', 'Pendamping hukum terpercaya.'),
                'address' => $cmsHelper($pageData, 'footer', 'address', 'Cirebon, Jawa Barat'),
                'phone' => $cmsHelper($pageData, 'footer', 'phone', '0857-4789-8811'),
                'email' => $cmsHelper($pageData, 'footer', 'email', 'info@example.com'),
                'work_days' => $cmsHelper($pageData, 'footer', 'work_days', 'Senin - Jumat'),
                'work_hours' => $cmsHelper($pageData, 'footer', 'work_hours', '09:00 - 16:00'),
                'work_days_sat' => $cmsHelper($pageData, 'footer', 'work_days_sat', 'Sabtu'),
                'work_hours_sat' => $cmsHelper($pageData, 'footer', 'work_hours_sat', '08:00 - 12:00'),
            ];
            $footerItems = $cmsItems($pageData, 'footer', 'quick_link');
        ?>
        <footer class="cms-footer-section">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-col">
                        <div class="footer-brand cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['brand']['id'] ?? 0 ?>">
                            <?= htmlspecialchars($footerData['brand']) ?>
                        </div>
                        <p class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['description']['id'] ?? 0 ?>">
                            <?= htmlspecialchars($footerData['description']) ?>
                        </p>
                    </div>
                    <div class="footer-col">
                        <h4>Layanan Cepat</h4>
                        <ul class="footer-links">
                            <?php foreach ($footerItems as $link): ?>
                            <li class="cms-editable" data-item-id="<?= $link['id'] ?>" data-field="title">
                                <?= htmlspecialchars($link['title'] ?? '') ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Hubungi Kami</h4>
                        <ul class="footer-contact">
                            <li class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['address']['id'] ?? 0 ?>">
                                <?= htmlspecialchars($footerData['address']) ?>
                            </li>
                            <li>
                                <span>WA: </span>
                                <span class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['phone']['id'] ?? 0 ?>">
                                    <?= htmlspecialchars($footerData['phone']) ?>
                                </span>
                            </li>
                            <li>
                                <span>Email: </span>
                                <span class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['email']['id'] ?? 0 ?>">
                                    <?= htmlspecialchars($footerData['email']) ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Jam Operasional</h4>
                        <p class="footer-hours-row">
                            <span class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['work_days']['id'] ?? 0 ?>">
                                <?= htmlspecialchars($footerData['work_days']) ?>
                            </span>
                            <span>: </span>
                            <span class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['work_hours']['id'] ?? 0 ?>">
                                <?= htmlspecialchars($footerData['work_hours']) ?>
                            </span>
                        </p>
                        <p class="footer-hours-row">
                            <span class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['work_days_sat']['id'] ?? 0 ?>">
                                <?= htmlspecialchars($footerData['work_days_sat']) ?>
                            </span>
                            <span>: </span>
                            <span class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['work_hours_sat']['id'] ?? 0 ?>">
                                <?= htmlspecialchars($footerData['work_hours_sat']) ?>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p class="cms-editable" data-content-id="<?= $pageData['sections']['footer']['content']['copyright_text']['id'] ?? 0 ?>">
                        <?= htmlspecialchars($footerData['copyright_text'] ?? '© 2024 Notaris Sri Anah SH.M.Kn. Hak Cipta Dilindungi.') ?>
                    </p>
                </div>
            </div>
        </footer>

        <?php endif; ?>
    </div>
</div>

<!-- Styles -->
<style>
.cms-wysiwyg-wrapper {
    display: flex;
    flex-direction: column;
    width: 100%;
    min-height: 100vh;
    background: #f5f5f5;
}

.cms-wysiwyg-toolbar {
    position: sticky;
    top: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: white;
    border-bottom: 2px solid #1B3A4B;
    z-index: 80;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    gap: 12px;
    flex-wrap: wrap;
}

.toolbar-left, .toolbar-right {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.toolbar-divider {
    width: 1px;
    height: 28px;
    background: #ddd;
    margin: 0 4px;
}

.cms-wysiwyg-content {
    flex: 1;
    margin-top: 0;
    padding: 0;
    width: 100%;
    box-sizing: border-box;
}

.cms-editable {
    cursor: text;
    padding: 4px 8px;
    border-radius: 3px;
    transition: all 0.2s;
    outline: 2px dashed transparent;
}

.cms-editable:hover {
    background: rgba(27, 58, 75, 0.1);
    outline: 2px dashed #1B3A4B;
}

.cms-editable:focus {
    background: rgba(27, 58, 75, 0.2);
    outline: 2px solid #1B3A4B;
    min-width: 20px;
    min-height: 1.2em;
    display: inline-block;
}

#editor-message {
    position: fixed;
    top: 135px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 4px;
    font-size: 14px;
    z-index: 200;
    animation: slideIn 0.3s ease-out;
    max-width: 400px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

#editor-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

#editor-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.hero, .section {
    position: relative;
}

.cms-hero-section {
    background: linear-gradient(145deg, #0F1F28 0%, #1B3A4B 50%, #2D5A6B 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    box-sizing: border-box;
}

.hero-inner {
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-content {
    max-width: 700px;
}

.hero-badge {
    display: inline-block;
    padding: 8px 16px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    font-size: 14px;
    margin-bottom: 20px;
}

.hero-content h1 {
    font-size: 48px;
    margin-bottom: 20px;
    font-weight: 700;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 24px;
    margin-bottom: 16px;
    opacity: 0.95;
}

.hero-desc {
    font-size: 16px;
    margin: 0 auto 30px;
    line-height: 1.6;
    max-width: 600px;
}

.hero-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-cta, .btn-layanan {
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.btn-cta {
    background: white;
    color: #667eea;
}

.btn-layanan {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-cta:hover, .btn-layanan:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.masalah, .layanan, .alur {
    padding: 60px 20px;
    background: white;
    margin: 20px 0;
}

.testimoni {
    padding: 60px 20px;
    background: #f9f9f9;
    margin: 20px 0;
}

.section-title {
    text-align: center;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 40px;
    color: #333;
}

.masalah-grid, .layanan-grid, .testimoni-grid, .alur-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

.masalah-card, .layanan-card, .alur-item {
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #eee;
    background: #fafafa;
}

.masalah-card h4, .layanan-card h4, .alur-item h4 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 12px;
    color: #333;
}

.masalah-card p, .layanan-card p, .alur-item p {
    font-size: 14px;
    line-height: 1.6;
    color: #666;
}

.testimoni-card {
    background: white;
    border-left: 4px solid #1B3A4B;
    padding: 20px;
    border-radius: 4px;
}

.testimoni-text {
    font-size: 15px;
    line-height: 1.8;
    margin-bottom: 16px;
    font-style: italic;
    color: #555;
}

.testimoni-author {
    text-align: right;
}

.testimoni-name {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

@media (max-width: 768px) {
    .cms-wysiwyg-toolbar {
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px 12px;
    }

    .toolbar-left, .toolbar-right {
        width: 100%;
    }

    .toolbar-right {
        justify-content: flex-end;
    }

    .hero-content h1 {
        font-size: 32px;
    }

    .hero-subtitle {
        font-size: 18px;
    }

    .section-title {
        font-size: 24px;
    }

    .hero-buttons {
        flex-direction: column;
    }

    .btn-cta, .btn-layanan {
        width: 100%;
        text-align: center;
    }

    .tentang-grid {
        grid-template-columns: 1fr;
    }

    .tentang-img, .tentang-content {
        width: 100%;
    }

    .footer-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Tentang Section */
.tentang {
    padding: 60px 20px;
    background: white;
    margin: 20px 0;
}

.tentang-grid {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 40px;
    align-items: start;
}

.tentang-img {
    text-align: center;
}

.tentang-img-inner {
    position: relative;
    color: #1B3A4B;
    margin-bottom: 30px;
}

.tentang-img-inner svg {
    width: 120px;
    height: 120px;
    margin-bottom: 15px;
}

.tentang-img-name {
    font-size: 22px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.tentang-img-title {
    font-size: 14px;
    color: #999;
    font-weight: 500;
}

.tentang-badge {
    background: linear-gradient(135deg, #1B3A4B 0%, #2D5A6B 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 6px;
    text-align: center;
    margin-top: 20px;
}

.tentang-badge strong {
    display: block;
    font-size: 28px;
    line-height: 1;
    margin-bottom: 5px;
}

.tentang-badge span {
    font-size: 12px;
    opacity: 0.9;
}

.tentang-content h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 25px;
    color: #333;
}

.tentang-narasi {
    font-size: 16px;
    font-style: italic;
    color: #666;
    line-height: 1.8;
    margin-bottom: 25px;
    padding: 15px 15px 15px 25px;
    border-left: 4px solid #1B3A4B;
    background: #f5f5f5;
}

.tentang-list {
    list-style: none;
    padding: 0;
}

.tentang-list li {
    padding: 10px 0;
    color: #666;
    line-height: 1.6;
    font-size: 15px;
    border-bottom: 1px solid #eee;
}

.tentang-list li:last-child {
    border-bottom: none;
}

/* CTA Section */
.cta {
    background: linear-gradient(135deg, #9C7C38 0%, #B8964F 100%);
    padding: 80px 20px;
    text-align: center;
    margin: 20px 0;
}

.cta-box h2 {
    font-size: 40px;
    font-weight: 700;
    color: white;
    margin-bottom: 20px;
}

.cta-box p {
    font-size: 18px;
    color: rgba(255, 255, 255, 0.95);
    margin-bottom: 35px;
    line-height: 1.6;
}

.cta-btn {
    display: inline-block;
    background: white;
    color: #9C7C38;
    padding: 15px 40px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

/* Footer Section */
.cms-footer-section {
    background: linear-gradient(135deg, #1B2A3B 0%, #0F1F2A 100%);
    color: white;
    padding: 60px 20px 20px;
    margin-top: 60px;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px;
    margin-bottom: 40px;
}

.footer-col h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #fff;
}

.footer-col p {
    font-size: 14px;
    line-height: 1.6;
    color: #ccc;
}

.footer-brand {
    font-size: 18px;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: #ccc;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #B8964F;
}

.footer-contact {
    list-style: none;
    padding: 0;
}

.footer-contact li {
    margin-bottom: 12px;
    font-size: 14px;
    color: #ccc;
    line-height: 1.6;
}

.footer-hours-row {
    margin-bottom: 10px !important;
    font-size: 14px;
    color: #ccc;
    line-height: 1.6;
}

.footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 13px;
    color: #aaa;
}
</style>

<script>
class WYSIWYGEditor {
    constructor() {
        this.history = [];
        this.historyIndex = -1;
        this.isSaving = false;
        this.init();
    }

    init() {
        this.setupContentEditable();
        this.setupEventListeners();
        this.captureInitialState();
    }

    setupContentEditable() {
        document.querySelectorAll('.cms-editable').forEach(el => {
            el.contentEditable = true;
            el.spellcheck = true;
            // Prevent HTML formatting - only plain text allowed (LAW 23.1)
            el.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = e.clipboardData.getData('text/plain');
                document.execCommand('insertText', false, text);
            });
            el.addEventListener('drop', (e) => {
                e.preventDefault();
            });
        });
    }

    setupEventListeners() {
        document.getElementById('btn-save')?.addEventListener('click', () => this.save());
        document.getElementById('btn-cancel')?.addEventListener('click', () => this.cancel());
        document.getElementById('btn-undo')?.addEventListener('click', () => this.undo());
        document.getElementById('btn-redo')?.addEventListener('click', () => this.redo());
        document.getElementById('btn-restore')?.addEventListener('click', () => this.restoreDefaults());

        // Capture state on changes
        document.querySelectorAll('.cms-editable').forEach(el => {
            el.addEventListener('blur', () => this.recordState());
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 'z') {
                    e.preventDefault();
                    this.undo();
                } else if (e.key === 'y' || (e.shiftKey && e.key === 'Z')) {
                    e.preventDefault();
                    this.redo();
                } else if (e.key === 's') {
                    e.preventDefault();
                    this.save();
                }
            }
        });
    }

    captureInitialState() {
        this.recordState();
    }

    recordState() {
        const state = this.getCurrentState();
        this.history = this.history.slice(0, this.historyIndex + 1);
        this.history.push(state);
        this.historyIndex++;
    }

    getCurrentState() {
        const state = {};
        document.querySelectorAll('.cms-editable').forEach(el => {
            const contentId = el.dataset.contentId;
            const itemId = el.dataset.itemId;
            const field = el.dataset.field;

            if (contentId) {
                state[`content_${contentId}`] = el.textContent;
            } else if (itemId && field) {
                state[`item_${itemId}_${field}`] = el.textContent;
            }
        });
        return state;
    }

    restoreState(state) {
        Object.entries(state).forEach(([key, value]) => {
            if (key.startsWith('content_')) {
                const contentId = key.replace('content_', '');
                const el = document.querySelector(`[data-content-id="${contentId}"]`);
                if (el) el.textContent = value;
            } else if (key.startsWith('item_')) {
                const match = key.match(/item_(\d+)_(\w+)/);
                if (match) {
                    const itemId = match[1];
                    const field = match[2];
                    const el = document.querySelector(`[data-item-id="${itemId}"][data-field="${field}"]`);
                    if (el) el.textContent = value;
                }
            }
        });
    }

    undo() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.restoreState(this.history[this.historyIndex]);
        }
    }

    redo() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.restoreState(this.history[this.historyIndex]);
        }
    }

    restoreDefaults() {
        if (confirm('Reset semua perubahan ke nilai default? Tindakan ini tidak bisa dibatalkan.')) {
            location.reload();
        }
    }

    async save() {
        if (this.isSaving) return; // Prevent concurrent saves (LAW 20.2)
        this.isSaving = true;
        
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        try {
            const changes = [];
            let hasChanges = false;

            // Collect all content changes
            document.querySelectorAll('[data-content-id]').forEach(el => {
                const contentId = el.dataset.contentId;
                const currentValue = el.textContent;
                const originalValue = this.getOriginalValue(contentId);
                
                if (currentValue !== originalValue) {
                    changes.push({
                        type: 'content',
                        contentId: contentId,
                        value: currentValue
                    });
                    hasChanges = true;
                }
            });

            // Collect all item changes
            document.querySelectorAll('[data-item-id]').forEach(el => {
                const itemId = el.dataset.itemId;
                const field = el.dataset.field;
                const currentValue = el.textContent;
                const originalValue = this.getOriginalItemValue(itemId, field);
                
                if (currentValue !== originalValue) {
                    changes.push({
                        type: 'item',
                        itemId: itemId,
                        field: field,
                        value: currentValue
                    });
                    hasChanges = true;
                }
            });

            if (!hasChanges) {
                this.showMessage('Tidak ada perubahan untuk disimpan', 'success');
                btn.disabled = false;
                btn.textContent = 'Simpan';
                return;
            }

            // Save with throttling (max 3 concurrent requests)
            const results = [];
            for (let i = 0; i < changes.length; i += 3) {
                const batch = changes.slice(i, i + 3);
                const batchResults = await Promise.all(
                    batch.map(change => {
                        if (change.type === 'content') {
                            return this.saveContent(change.contentId, change.value);
                        } else {
                            return this.saveItem(change.itemId, change.field, change.value);
                        }
                    })
                );
                results.push(...batchResults);
                
                // Delay between batches (avoid rate limiting)
                if (i + 3 < changes.length) {
                    await new Promise(resolve => setTimeout(resolve, 300));
                }
            }

            const allSuccess = results.every(r => r && r.success);
            this.showMessage(
                allSuccess ? '✓ Berhasil disimpan!' : '⚠ Ada error saat menyimpan',
                allSuccess ? 'success' : 'error'
            );

            if (allSuccess) {
                // Update original values after successful save
                this.updateOriginalValues();
                this.captureInitialState();
            }
        } catch (error) {
            this.showMessage('Error: ' + error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Simpan';
            this.isSaving = false;
        }
    }

    stripHtmlTags(text) {
        return text.replace(/<[^>]*>/g, '').trim();
    }

    getOriginalValue(contentId) {
        // Get from first history state (initial)
        if (this.history.length > 0) {
            return this.history[0][`content_${contentId}`] || '';
        }
        return '';
    }

    getOriginalItemValue(itemId, field) {
        if (this.history.length > 0) {
            return this.history[0][`item_${itemId}_${field}`] || '';
        }
        return '';
    }

    updateOriginalValues() {
        document.querySelectorAll('[data-content-id]').forEach(el => {
            el.dataset.originalValue = el.textContent;
        });
        document.querySelectorAll('[data-item-id]').forEach(el => {
            el.dataset.originalValue = el.textContent;
        });
    }

    async saveContent(contentId, value) {
        const cleanValue = this.stripHtmlTags(value);
        const response = await fetch(`${APP_URL}/index.php?gate=cms_update_content`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= generateCSRFToken() ?>'
            },
            body: `content_id=${contentId}&value=${encodeURIComponent(cleanValue)}&csrf_token=<?= generateCSRFToken() ?>`
        });

        // Check for session expiration (401/403)
        if (response.status === 401 || response.status === 403) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return null;
        }

        const jsonResponse = await response.json();

        // Also check JSON response for session expiration
        if (jsonResponse && jsonResponse.message &&
            jsonResponse.message.includes('Session expired')) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return null;
        }

        return jsonResponse;
    }

    async saveItem(itemId, field, value) {
        const cleanValue = this.stripHtmlTags(value);
        const response = await fetch(`${APP_URL}/index.php?gate=cms_update_item`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= generateCSRFToken() ?>'
            },
            body: `item_id=${itemId}&${field}=${encodeURIComponent(cleanValue)}&csrf_token=<?= generateCSRFToken() ?>`
        });

        // Check for session expiration (401/403)
        if (response.status === 401 || response.status === 403) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return null;
        }

        const jsonResponse = await response.json();

        // Also check JSON response for session expiration
        if (jsonResponse && jsonResponse.message &&
            jsonResponse.message.includes('Session expired')) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return null;
        }

        return jsonResponse;
    }

    showMessage(text, type) {
        const msgEl = document.getElementById('editor-message');
        msgEl.textContent = text;
        msgEl.className = `editor-message ${type}`;
        msgEl.style.display = 'block';
        setTimeout(() => msgEl.style.display = 'none', 4000);
    }

    cancel() {
        if (confirm('Keluar tanpa menyimpan? Perubahan Anda akan hilang.')) {
            window.location.href = '<?= APP_URL ?>/index.php?gate=cms_editor';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const editor = new WYSIWYGEditor();
    // Attach editor instance to wrapper for global access
    document.querySelector('.cms-wysiwyg-wrapper')._editor = editor;
    
    // Photo Upload Handler for Tentang Section
    // Get photo content ID from data attribute
    const photoContentId = document.querySelector('input#tentang-photo-input')?.closest('.tentang-img')?.querySelector('[data-content-id]')?.getAttribute('data-content-id');
    
    const photoUploadBtn = document.getElementById('tentang-photo-upload-btn');
    const photoInput = document.getElementById('tentang-photo-input');
    
    if (photoUploadBtn && photoInput) {
        photoUploadBtn.addEventListener('click', () => {
            photoInput.click();
        });
        
        photoInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            
            // Security: Check file size (LAW 25.1)
            if (file.size > 5 * 1024 * 1024) {
                const editor = document.querySelector('.cms-wysiwyg-wrapper')._editor;
                if (editor) editor.showMessage('Foto terlalu besar (max 5MB)', 'error');
                else alert('Foto terlalu besar (max 5MB)');
                return;
            }
            
            // Get photo content ID from any photo field or use hardcoded approach
            // Since photo field might not be in editable attributes, we'll find it from data structure
            const tentangSection = document.querySelector('.tentang-img');
            let contentId = tentangSection?.querySelector('[data-content-id]')?.getAttribute('data-content-id');
            
            // Fallback: search for photo field in visible elemen ts or use static id
            // For now, we'll extract from current page data structure
            const photoId = <?= $pageData['sections']['tentang']['content']['photo']['id'] ?? 'null' ?>;
            
            if (!photoId) {
                const editor = document.querySelector('.cms-wysiwyg-wrapper')._editor;
                if (editor) editor.showMessage('Gagal mendeteksi field foto', 'error');
                else alert('Gagal mendeteksi field foto');
                return;
            }
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('content_id', photoId);
            
            try {
                photoUploadBtn.disabled = true;
                photoUploadBtn.textContent = 'Uploading...';

                formData.append('csrf_token', '<?= generateCSRFToken() ?>');
                const response = await fetch(`${APP_URL}/index.php?gate=cms_upload_image`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= generateCSRFToken() ?>'
                    },
                    body: formData
                });

                // Check for session expiration (401/403)
                if (response.status === 401 || response.status === 403) {
                    window.location.href = APP_URL + '/index.php?gate=login&expired=1';
                    return;
                }

                const result = await response.json();

                // Also check JSON response for session expiration
                if (result && result.message &&
                    result.message.includes('Session expired')) {
                    window.location.href = APP_URL + '/index.php?gate=login&expired=1';
                    return;
                }

                if (result.success) {
                    const editor = document.querySelector('.cms-wysiwyg-wrapper')._editor;
                    if (editor) editor.showMessage('✓ Foto berhasil diunggah!', 'success');
                    else alert('Foto berhasil diunggah');
                    
                    // Update preview image
                    const preview = document.getElementById('tentang-photo-preview');
                    if (preview) {
                        preview.src = result.url;
                    } else {
                        // If first upload, reload to show new UI
                        location.reload();
                    }
                    
                    // Reset input
                    photoInput.value = '';
                } else {
                    const editor = document.querySelector('.cms-wysiwyg-wrapper')._editor;
                    if (editor) editor.showMessage('Error: ' + (result.message || 'Upload failed'), 'error');
                    else alert('Error: ' + (result.message || 'Upload failed'));
                }
            } catch (error) {
                console.error('Upload error:', error);
                const editor = document.querySelector('.cms-wysiwyg-wrapper')._editor;
                if (editor) editor.showMessage('Upload gagal: ' + error.message, 'error');
                else alert('Upload gagal: ' + error.message);
            } finally {
                photoUploadBtn.disabled = false;
                photoUploadBtn.textContent = photoUploadBtn.textContent.includes('Ganti') ? 'Ganti Foto' : 'Upload Foto';
            }
        });
    }
});
</script>
<?php require VIEWS_PATH . '/templates/footer.php'; ?>
