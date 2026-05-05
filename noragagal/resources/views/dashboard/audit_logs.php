<?php
/**
 * Audit Logs View (Notaris Only)
 */

$currentUser = getCurrentUser();
$pageTitle = 'Audit Logs';
$activePage = 'audit';

require VIEWS_PATH . '/templates/header.php';
?>

<div class="dashboard-card">
    <div class="card-header">
        <h3>Log Aktivitas Sistem</h3>
    </div>
    <div class="card-body">
        <?php if (empty($logs)): ?>
            <p class="empty-state">Belum ada log aktivitas</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Registrasi</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= date('d M Y H:i:s', strtotime($log['timestamp'])) ?></td>
                        <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
                        <td><span class="badge badge-<?= $log['role'] ?>"><?= ucfirst($log['role']) ?></span></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['nomor_registrasi'] ?? '-') ?></td>
                        <td class="log-details">
                            <?php if ($log['old_value']): ?>
                                <small>Old: <?= htmlspecialchars($log['old_value']) ?></small>
                            <?php endif; ?>
                            <?php if ($log['new_value']): ?>
                                <small>New: <?= htmlspecialchars($log['new_value']) ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3>Statistik Log per Action</h3>
    </div>
    <div class="card-body">
        <?php if (empty($logsByAction)): ?>
            <p class="empty-state">Belum ada data</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logsByAction as $stat): ?>
                    <tr>
                        <td><?= htmlspecialchars($stat['action']) ?></td>
                        <td><?= $stat['count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        ">
            <?php if ($page > 1): ?>
                <a href="?gate=audit&page=<?= $page - 1 ?>" 
                   style="
                        text-decoration: none;
                        padding: 8px 16px;
                        background: var(--primary);
                        color: var(--gold);
                        border-radius: 6px;
                        font-size: 13px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                   "
                   onmouseover="this.style.background='var(--primary-light)'"
                   onmouseout="this.style.background='var(--primary)'">
                    ← Prev
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?gate=audit&page=<?= $i ?>" 
                   style="
                        text-decoration: none;
                        padding: 8px 16px;
                        background: <?= $i === $page ? 'var(--primary)' : 'var(--cream)' ?>;
                        color: <?= $i === $page ? 'var(--gold)' : 'var(--text)' ?>;
                        border-radius: 6px;
                        font-size: 13px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                   "
                   onmouseover="this.style.background='<?= $i === $page ? 'var(--primary-light)' : 'var(--border)' ?>'"
                   onmouseout="this.style.background='<?= $i === $page ? 'var(--primary)' : 'var(--cream)' ?>'">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?gate=audit&page=<?= $page + 1 ?>" 
                   style="
                        text-decoration: none;
                        padding: 8px 16px;
                        background: var(--primary);
                        color: var(--gold);
                        border-radius: 6px;
                        font-size: 13px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                   "
                   onmouseover="this.style.background='var(--primary-light)'"
                   onmouseout="this.style.background='var(--primary)'">
                    Next →
                </a>
            <?php endif; ?>
            
            <span style="margin-left: 16px; font-size: 13px; color: var(--text-muted);">
                Page <?= (string)($page ?? 1) ?> of <?= (string)($totalPages ?? 1) ?> (<?= (string)($total ?? 0) ?> logs)
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
