<?php
/**
 * Public Registrasi Detail View - With Process Log
 */
// Get phone number from CMS footer section (LAW 0.2 - Single Source of Truth)
require_once CONFIG_PATH . '/database.php';
$phoneNumber = '6285747898811'; // Default fallback

try {
    $db = new Database();
    $stmt = $db->getConnection()->prepare(
        "SELECT content_value FROM cms_section_content 
         WHERE content_key = 'phone' AND section_id = 8 LIMIT 1"
    );
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result) {
        $phoneNumber = $result['content_value'];
    }
} catch (Exception $e) {
    // Use default fallback if query fails
}

$pageTitle = 'Detail Registrasi - ' . ($registrasi['nomor_registrasi'] ?? '-');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/tracking.css">
</head>
<body>
    <header style="background: white; border-bottom: 1px solid #e2e8f0; padding: 15px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
            <a href="<?= APP_URL ?>/index.php" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: #1B3A4B; font-weight: 700; font-size: 18px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; color: #9C7C38;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Notaris <span style="color: #9C7C38;">Sri Anah SH.M.Kn</span></span>
            </a>
            <nav style="display: flex; gap: 20px;">
                <a href="<?= APP_URL ?>/index.php" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">Beranda</a>
                <a href="<?= APP_URL ?>/index.php#masalah" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">FAQ</a>
                <a href="<?= APP_URL ?>/index.php?gate=lacak" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">Lacak</a>
                <a href="<?= APP_URL ?>/index.php#tentang" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">Tentang</a>
                <a href="https://wa.me/<?= str_replace([' ', '-'], '', $phoneNumber) ?>" target="_blank" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">Hubungi Kami</a>
            </nav>
        </div>
    </header>

    <div class="tracking-page">
        <div class="tracking-header">
            <a href="<?= APP_URL ?>/index.php?gate=lacak" class="back-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Lacak Registrasi
            </a>
            <h1>Detail Registrasi</h1>
        </div>

        <!-- Info Registrasi -->
        <div class="registrasi-detail-card">
            <div class="detail-header">
                <h1><?= htmlspecialchars($registrasi['nomor_registrasi'] ?? '-') ?></h1>
                <span class="badge badge-<?= $registrasi['status'] ?>">
                    <?= STATUS_LABELS[$registrasi['status']] ?>
                </span>
            </div>

            <div class="detail-info">
                <div class="info-row">
                    <span class="label">Klien:</span>
                    <span class="value"><?= htmlspecialchars($registrasi['klien_nama']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Layanan:</span>
                    <span class="value"><?= htmlspecialchars($registrasi['nama_layanan']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Dibuat:</span>
                    <span class="value"><?= date('d M Y', strtotime($registrasi['created_at'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Update Terakhir:</span>
                    <span class="value"><?= date('d M Y H:i', strtotime($registrasi['updated_at'])) ?></span>
                </div>
            </div>
        </div>

        <!-- Process Log (HISTORY REGISTRASI) -->
        <div class="process-log-card" style="margin-top: 24px;">
            <h2 style="margin-bottom: 20px;">📋 Catatan Proses</h2>
            
            <?php if (empty($history)): ?>
                <p style="color: var(--text-muted);">Belum ada riwayat perubahan.</p>
            <?php else: ?>
                <div class="process-log">
                    <?php foreach ($history as $h): ?>
                    <div class="log-entry" style="
                        padding: 16px;
                        background: var(--white);
                        border-left: 4px solid var(--gold);
                        border-radius: 8px;
                        margin-bottom: 16px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                    ">
                        <div class="log-header" style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            margin-bottom: 12px;
                            padding-bottom: 12px;
                            border-bottom: 1px solid var(--border);
                        ">
                            <span class="log-date" style="font-weight: 600; color: var(--primary);">
                                📅 <?= date('d M Y, H:i', strtotime($h['created_at'])) ?>
                            </span>
                            <span class="log-user" style="
                                background: var(--cream);
                                padding: 4px 12px;
                                border-radius: 4px;
                                font-size: 13px;
                                color: var(--text-light);
                            ">
                                👤 <?= htmlspecialchars($h['user_name'] ?? 'System') ?>
                            </span>
                        </div>
                        <div class="log-content">
                            <div style="margin-bottom: 8px;">
                                <strong>Status:</strong>
                                <?php
                                $oldStatus = $h['status_old'] ?? '-';
                                $newStatus = $h['status_new'] ?? '-';
                                if ($oldStatus !== $newStatus) {
                                    echo "<span style='color: var(--gold);'>$oldStatus</span> → <span style='color: var(--success);'>$newStatus</span>";
                                } else {
                                    echo '<span style="color: var(--text-muted);">No change</span>';
                                }
                                ?>
                            </div>
                            <?php if ($h['flag_kendala_active']): ?>
                            <div style="
                                background: #fff3cd;
                                padding: 8px 12px;
                                border-radius: 6px;
                                margin-bottom: 8px;
                                color: #856404;
                                font-weight: 600;
                            ">
                                🚩 Kendala: <?= htmlspecialchars($h['flag_kendala_tahap'] ?? 'Umum') ?>
                            </div>
                            <?php endif; ?>
                            <?php
                            $catatan = $h['catatan'] ?? '';
                            if ($catatan):
                            ?>
                            <div style="
                                background: var(--cream);
                                padding: 12px;
                                border-radius: 6px;
                                font-style: italic;
                                color: var(--text);
                            ">
                                💬 <?= nl2br(htmlspecialchars($catatan)) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Progress Timeline -->
        <div class="progress-card" style="margin-top: 24px;">
            <h2>Progress Status</h2>
            <div class="timeline">
                <?php foreach ($progress as $status => $item): ?>
                <div class="timeline-item <?= $item['completed'] ? 'completed' : '' ?> <?= $item['current'] ? 'current' : '' ?>">
                    <div class="timeline-marker">
                        <?php if ($item['completed']): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        <?php else: ?>
                            <span><?= $item['order'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="timeline-content">
                        <span class="timeline-label"><?= $item['label'] ?></span>
                        <span class="timeline-estimasi"><?= $item['estimasi'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="contact-cta" style="margin-top: 24px;">
            <p>Butuh bantuan? Hubungi kami via WhatsApp</p>
            <a href="https://wa.me/<?= str_replace([' ', '-'], '', $phoneNumber) ?>" class="btn-wa" target="_blank">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path>
                </svg>
                Hubungi via WhatsApp
            </a>
        </div>
    </div>

    <script>
        window.APP_URL = '<?= APP_URL ?>';
    </script>
    <footer style="background: #0a192f; color: #fff; padding: 40px 0 20px; font-size: 14px; text-align: center;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; color: rgba(255,255,255,0.5);">
                &copy; <?= date('Y') ?> Notaris Sri Anah SH.M.Kn. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
