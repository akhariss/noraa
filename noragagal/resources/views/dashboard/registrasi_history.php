<?php
/**
 * Registrasi History View - Full History
 */

$currentUser = getCurrentUser();
$pageTitle = 'Riwayat Lengkap - ' . ($registrasi['nomor_registrasi'] ?? '-');
$activePage = 'registrasi';

require VIEWS_PATH . '/templates/header.php';
?>

<div class="registrasi-detail">
    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="<?= APP_URL ?>/index.php?gate=registrasi_detail&id=<?= $registrasi['id'] ?>" class="btn-back" style="
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        " onmouseover="this.style.background='var(--cream)';this.style.color='var(--primary)'" onmouseout="this.style.background='';this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Detail Registrasi
        </a>
    </div>

    <!-- Info Card -->
    <div class="info-card" style="
        background: var(--white);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 24px;
    ">
        <div class="info-grid" style="
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        ">
            <div class="info-item">
                <span class="info-label" style="
                    display: block;
                    font-size: 12px;
                    color: var(--text-muted);
                    text-transform: uppercase;
                    margin-bottom: 4px;
                ">Nomor Registrasi</span>
                <span class="info-value" style="
                    font-weight: 600;
                    color: var(--primary);
                    font-size: 15px;
                "><?= htmlspecialchars($registrasi['nomor_registrasi'] ?? '-') ?></span>
            </div>
            <div class="info-item">
                <span class="info-label" style="
                    display: block;
                    font-size: 12px;
                    color: var(--text-muted);
                    text-transform: uppercase;
                    margin-bottom: 4px;
                ">Klien</span>
                <span class="info-value" style="
                    font-weight: 600;
                    color: var(--text);
                    font-size: 15px;
                "><?= htmlspecialchars($registrasi['klien_nama']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label" style="
                    display: block;
                    font-size: 12px;
                    color: var(--text-muted);
                    text-transform: uppercase;
                    margin-bottom: 4px;
                ">Total Riwayat</span>
                <span class="info-value" style="
                    font-weight: 600;
                    color: var(--primary);
                    font-size: 15px;
                "><?= count($history) ?> entri</span>
            </div>
        </div>
    </div>

    <!-- Full History Table -->
    <div class="detail-card" style="
        background: var(--white);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    ">
        <h3 style="margin: 0 0 20px 0; color: var(--primary); font-size: 18px;">📜 Riwayat Lengkap Semua Perubahan</h3>
        <div style="overflow-x: auto;">
            <table class="data-table" style="
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
            "                <thead>
                    <tr style="background: var(--cream);">
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: var(--text-light); border-bottom: 2px solid var(--border);">#</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: var(--text-light); border-bottom: 2px solid var(--border);">Waktu</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: var(--text-light); border-bottom: 2px solid var(--border);">Admin</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: var(--text-light); border-bottom: 2px solid var(--border);">Flag</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: var(--text-light); border-bottom: 2px solid var(--border);">Perubahan Status</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; color: var(--text-light); border-bottom: 2px solid var(--border);">Catatan / Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="6" style="padding: 30px; text-align: center; color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.6;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                Belum ada riwayat perubahan
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php 
                    $counter = count($history);
                    foreach ($history as $h): 
                    ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px 16px; color: var(--text-muted); font-weight: 600;"><?= $counter-- ?></td>
                        <td style="padding: 14px 16px; color: var(--text); font-size: 12px; white-space: nowrap;"><?= date('d M Y H:i', strtotime($h['created_at'])) ?></td>
                        <td style="padding: 14px 16px; color: var(--text); white-space: nowrap;"><?= htmlspecialchars($h['user_name'] ?? 'System') ?></td>
                        <td style="padding: 14px 16px; font-size: 12px; white-space: nowrap;">
                            <?php if ($h['flag_kendala_active']): ?>
                                <span style="color: #ffc107; font-weight: 600;">🚩 ON</span>
                            <?php else: ?>
                                <span style="color: var(--text-muted);">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 14px 16px; font-size: 12px; color: var(--text-light); white-space: nowrap;">
                            <?php
                            $labels = getStatusLabels();
                            $oldStatus = $h['status_old'] ?? '';
                            $newStatus = $h['status_new'] ?? '';

                            $oldLabel = $labels[$oldStatus] ?? $oldStatus;
                            $newLabel = $labels[$newStatus] ?? $newStatus;
 
                            if ($oldStatus && $oldStatus !== $newStatus) {
                                echo "<span style='color: var(--text-muted); text-decoration: line-through; font-size: 10px;'>$oldLabel</span><br>";
                                echo "<span style='color: var(--primary); font-weight: 600;'>$newLabel</span>";
                            } else {
                                echo "<span style='color: var(--text); font-weight: 600;'>$newLabel</span>";
                            }
                            ?>
                        </td>
                        <td style="padding: 14px 16px; font-size: 12px; color: var(--text-light); line-height: 1.5;">
                            <?php
                            $catatan = $h['catatan'] ?? '';
                            echo $catatan ? nl2br(htmlspecialchars($catatan)) : '<span style="color: var(--text-muted);">-</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>tbody>
            </table>
        </div>
    </div>
</div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
