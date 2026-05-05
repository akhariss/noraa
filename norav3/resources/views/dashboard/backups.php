<?php
/**
 * Backup Management View (Notaris Only)
 */

$currentUser = getCurrentUser();
$pageTitle = 'Backup & Recovery';
$activePage = 'app_settings';
$pageScript = 'backups.js';

require VIEWS_PATH . '/templates/header.php';
?>

<div class="backup-actions">
    <button class="btn-primary" onclick="createBackup('database')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="17 8 12 3 7 8"></polyline>
            <line x1="12" y1="3" x2="12" y2="15"></line>
        </svg>
        Backup Database
    </button>
    <button class="btn-secondary" onclick="createBackup('site')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <folder></folder>
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
        </svg>
        Backup Full Site
    </button>
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3>Daftar Backup</h3>
    </div>
    <div class="card-body">
        <?php if (empty($backups)): ?>
            <p class="empty-state">Belum ada backup</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Size</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                    <tr>
                        <td><?= htmlspecialchars($backup['filename']) ?></td>
                        <td><?= round($backup['size'] / 1024, 2) ?> KB</td>
                        <td><?= date('d M Y H:i', $backup['created']) ?></td>
                        <td><span class="badge"><?= strtoupper($backup['type']) ?></span></td>
                        <td>
                            <a href="<?= APP_URL ?>/dashboard/backups/download?file=<?= urlencode($backup['filename']) ?>" class="btn-sm">Download</a>
                            <button class="btn-sm btn-danger" onclick="deleteBackup('<?= htmlspecialchars($backup['filename']) ?>')">Hapus</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div id="backupMessage" class="form-message" style="display: none;"></div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
