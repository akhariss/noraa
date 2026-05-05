<div class="detail-page">
    <div class="container">
        <div class="hero-detail">
            <a href="<?= APP_URL ?>/lacak" class="back-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Lacak
            </a>
            <h1><?= htmlspecialchars($registrasi['nomor_registrasi']) ?></h1>
            <div class="status-row">
                <?php $pStyle = \App\Models\RegistrasiModel::getStatusStyle((int)($registrasi['behavior_role'] ?? 0)); ?>
                <span class="status-badge-v4" style="background: <?= $pStyle['bg'] ?>; color: <?= $pStyle['color'] ?>;">
                    <?= htmlspecialchars($registrasi['status_label'] ?? $registrasi['status']) ?>
                </span>
                <span class="last-update">Diperbarui: <?= date('d M Y, H:i', strtotime($registrasi['updated_at'])) ?></span>
                
                <?php if (!empty($registrasi['kendala_flag'])): ?>
                    <span class="indicator error">🚩 BERMASALAH</span>
                <?php else: ?>
                    <span class="indicator success">✓ LANCAR</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="glass-section info-summary">
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Nama Klien</span>
                    <span class="value"><?= htmlspecialchars($registrasi['klien_nama'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Jenis Layanan</span>
                    <span class="value"><?= htmlspecialchars($registrasi['nama_layanan'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Tanggal Daftar</span>
                    <span class="value"><?= date('d M Y', strtotime($registrasi['created_at'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Estimasi Selesai</span>
                    <span class="value highlight"><?= !empty($registrasi['target_completion_at']) ? date('d M Y', strtotime($registrasi['target_completion_at'])) : 'Menunggu Update' ?></span>
                </div>
            </div>
        </div>

        <div class="main-detail-grid">
            <div class="glass-section timeline-panel">
                <h2 class="section-subtitle">Progres Dokumen</h2>
                <div class="v-timeline">
                    <?php foreach ($progress as $key => $step): ?>
                    <div class="v-timeline-item <?= $step['completed'] ? 'completed' : '' ?> <?= $step['current'] ? 'current' : '' ?>">
                        <div class="v-marker">
                            <?php if ($step['completed'] && !$step['current']): ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php else: ?>
                                <span><?= $step['order'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="v-content">
                            <span class="v-label"><?= htmlspecialchars($step['label']) ?></span>
                            <?php if (!empty($step['current'])): ?><span class="v-status">Sedang Berjalan</span><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="glass-section history-panel">
                <h2 class="section-subtitle">Riwayat Proses</h2>
                <div class="history-list">
                    <?php foreach ($history as $h): ?>
                    <div class="history-card">
                        <div class="h-meta">
                            <span class="h-date"><?= date('d M Y, H:i', strtotime($h['created_at'])) ?></span>
                            <span class="h-status"><?= htmlspecialchars($h['status_new_label'] ?? 'Pembaruan Sistem') ?></span>
                        </div>
                        <?php if (!empty($h['catatan'])): ?>
                            <div class="h-note"><?= nl2br(htmlspecialchars($h['catatan'])) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($history)): ?>
                        <div class="empty-state">Belum ada riwayat tercatat untuk dokumen ini.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-page {
    background: var(--cream);
    padding: 180px 0 100px;
    min-height: 100vh;
}
.hero-detail {
    margin-bottom: 40px;
}
.hero-detail h1 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 48px;
    color: var(--primary);
    margin: 15px 0;
}
.status-row {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}
.status-badge-v4 {
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.last-update {
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 500;
}
.indicator {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1px;
}
.indicator.success { color: #38a169; }
.indicator.error { color: #e53e3e; }

.glass-section {
    background: white;
    border-radius: 24px;
    padding: 35px;
    border: 1px solid var(--border);
    box-shadow: 0 15px 45px rgba(0,0,0,0.03);
    margin-bottom: 30px;
}
.section-subtitle {
    font-family: 'Cormorant Garamond', serif;
    font-size: 26px;
    color: var(--gold);
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 30px;
}
.info-item .label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-muted);
    margin-bottom: 8px;
}
.info-item .value {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
}
.value.highlight { color: var(--primary); }

.main-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 30px;
}

/* Vertical Timeline */
.v-timeline {
    position: relative;
    padding-left: 45px;
}
.v-timeline::before {
    content: '';
    position: absolute;
    left: 17px;
    top: 5px;
    bottom: 5px;
    width: 2px;
    background: var(--border);
}
.v-timeline-item {
    position: relative;
    margin-bottom: 30px;
}
.v-marker {
    position: absolute;
    left: -45px;
    width: 36px;
    height: 36px;
    background: white;
    border: 2px solid var(--border);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    font-size: 12px;
    font-weight: 800;
    color: var(--text-muted);
    transition: all 0.3s ease;
}
.v-timeline-item.completed .v-marker {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}
.v-timeline-item.current .v-marker {
    background: var(--gold);
    border-color: var(--gold);
    color: white;
    box-shadow: 0 0 0 5px rgba(156, 124, 56, 0.2);
}
.v-content {
    display: flex;
    flex-direction: column;
}
.v-label {
    font-weight: 700;
    color: var(--text-muted);
    font-size: 15px;
}
.v-timeline-item.completed .v-label, .v-timeline-item.current .v-label {
    color: var(--text);
}

/* History List */
.history-card {
    padding: 20px;
    border-radius: 16px;
    background: #fcfcfc;
    border: 1px solid var(--border);
    border-left: 4px solid var(--gold);
    margin-bottom: 15px;
}
.h-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}
.h-date {
    font-size: 12px;
    font-weight: 700;
    color: var(--gold);
}
.h-status {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 600;
}
.h-note {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-light);
    font-style: italic;
}

@media (max-width: 992px) {
    .main-detail-grid { grid-template-columns: 1fr; }
    .hero-detail h1 { font-size: 32px; }
}
</style>
