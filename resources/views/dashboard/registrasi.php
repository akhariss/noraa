<?php
/**
 * Registrasi List View - Simple
 */

$currentUser = getCurrentUser();
$pageTitle = 'Daftar Registrasi';
$activePage = 'registrasi';

require VIEWS_PATH . '/templates/header.php';
?>

<style>
/* Badge styles untuk status */
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

/* Badge khusus untuk Batal - MERAH MENCOLOK */
.badge-batal {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ef9a9a;
    font-weight: 700;
}

/* Badge untuk Selesai */
.badge-selesai {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

/* Badge untuk Ditutup */
.badge-ditutup {
    background: #f3e5f5;
    color: #7b1fa2;
    border: 1px solid #ce93d8;
}

/* Badge untuk Draft */
.badge-draft {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #90caf9;
}

/* Badge untuk Perbaikan */
.badge-perbaikan {
    background: #fff3e0;
    color: #f57c00;
    border: 1px solid #ffcc80;
}

/* Nora 2.0 Command Center Layout */
.nora-filter-toolbar {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Row 1: Primary Actions (Search & Add) */
.command-row {
    display: flex;
    gap: 12px;
    width: 100%;
}

.search-command-group {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon-inside {
    position: absolute;
    left: 14px;
    color: var(--text-muted);
    pointer-events: none;
}

.command-search-input {
    width: 100%;
    height: 48px;
    padding: 0 16px 0 44px;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 15px;
    background: var(--white);
    transition: border-color 0.3s, box-shadow 0.3s;
}

.command-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(27, 58, 75, 0.05);
    outline: none;
}

.btn-add-primary {
    height: 48px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: var(--gold);
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 24px;
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(27, 58, 75, 0.15);
    transition: all 0.3s ease;
}

.btn-add-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(27, 58, 75, 0.25);
}

/* Row 2: Refinements (Filter Pills) */
.refinement-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 12px;
    background: var(--cream);
    border-radius: 10px;
    border: 1px solid rgba(156, 124, 56, 0.08);
}

.filter-pill {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
    min-width: 140px;
}

.pill-label {
    font-size: 10px;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-left: 2px;
}

.pill-select {
    height: 38px;
    padding: 0 10px;
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 13px;
    background: var(--white);
    color: var(--primary);
    cursor: pointer;
}

.refinement-actions {
    display: flex;
    align-items: flex-end;
    gap: 8px;
}

.btn-pill-action {
    height: 38px;
    padding: 0 16px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-pill-reset {
    background: var(--white);
    border: 1px solid var(--border);
    color: var(--text-muted);
    width: 38px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-pill-search {
    background: var(--primary);
    color: var(--white);
    min-width: 80px;
}

/* Hero Section Styles */
.registrasi-hero {
    background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 12px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.registrasi-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    width: 100%;
}

.registrasi-badge {
    display: inline-block;
    background: rgba(156, 124, 56, 0.2);
    color: var(--gold-light);
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 8px;
    border: 1px solid rgba(156, 124, 56, 0.3);
}

.registrasi-hero-subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
    font-weight: 400;
}

.registrasi-action-card {
    background: var(--white);
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 10px;
    border: 1px solid rgba(156, 124, 56, 0.1);
}

/* Responsive Overrides */
@media (max-width: 1024px) {
    .filter-pill { min-width: calc(50% - 5px); }
    .refinement-actions { width: 100%; margin-top: 4px; }
    .btn-pill-search { flex: 1; }
}

@media (max-width: 768px) {
    .command-row { flex-direction: column; }
    .btn-add-primary { width: 100%; justify-content: center; height: 40px; font-size: 14px; }
    .registrasi-action-card { padding: 12px; }
    .pill-select { height: 32px; font-size: 12px; padding: 0 8px; }
    .command-search-input { height: 40px; font-size: 13px; }
    .btn-pill-action { height: 32px; font-size: 12px; }
    .refinement-row { gap: 8px; padding: 10px; }
    .filter-pill { min-width: calc(50% - 4px); }
}

@media (min-width: 769px) {
    .data-table { min-width: 900px; }
}
</style>

<!-- Premium Hero Header -->
<div class="registrasi-hero">
    <div class="registrasi-hero-content">
        <span class="registrasi-badge">Layanan Klien</span>
        <p class="registrasi-hero-subtitle">Pantau dan kelola proses layanan klien dari awal hingga selesai</p>
    </div>
</div>

<!-- Command Center Toolbar -->
<div class="registrasi-action-card">
    <form method="GET" action="" class="nora-filter-toolbar">
        <input type="hidden" name="gate" value="registrasi">

        <!-- Tier 1: Primary Commands -->
        <div class="command-row">
            <div class="search-command-group">
                <div class="search-icon-inside">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <input type="text" name="search" class="command-search-input" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari nomor registrasi, nama klien, atau nomor HP..." onkeypress="if(event.keyCode === 13) { this.form.submit(); }" onchange="this.form.submit()" onblur="if(this.value !== '<?= addslashes($search ?? '') ?>') { this.form.submit(); }">
            </div>
            
            <a href="<?= APP_URL ?>/index.php?gate=registrasi_create" class="btn-add-primary" title="Tambah Registrasi Baru">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Tambah</span>
            </a>
        </div>

        <!-- Tier 2: Refinements -->
        <div class="refinement-row">
            <!-- Filter: Urutkan -->
            <div class="filter-pill">
                <label class="pill-label">Urutkan</label>
                <select name="order" onchange="this.form.submit()" class="pill-select">
                    <option value="terbaru" <?= ($filterOrder ?? '') === 'terbaru' ? 'selected' : '' ?>>📅 Terbaru</option>
                    <option value="terlama" <?= ($filterOrder ?? '') === 'terlama' ? 'selected' : '' ?>>⏳ Terlama</option>
                    <option value="terlambat" <?= ($filterOrder ?? '') === 'terlambat' ? 'selected' : '' ?>>⏰ Terlambat</option>
                    <option value="nama_asc" <?= ($filterOrder ?? '') === 'nama_asc' ? 'selected' : '' ?>>👤 A-Z</option>
                    <option value="nama_desc" <?= ($filterOrder ?? '') === 'nama_desc' ? 'selected' : '' ?>>👤 Z-A</option>
                </select>
            </div>

            <!-- Filter: Layanan -->
            <div class="filter-pill">
                <label class="pill-label">Layanan</label>
                <select name="layanan" onchange="this.form.submit()" class="pill-select">
                    <option value="">Semua Layanan</option>
                    <?php foreach ($layanan as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= ($filterLayanan ?? '') == $l['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($l['nama_layanan']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter: Status -->
            <div class="filter-pill">
                <label class="pill-label">Status Progres</label>
                <select name="status" onchange="this.form.submit()" class="pill-select">
                    <option value="">Semua Status</option>
                    <?php foreach ($allSteps as $s): ?>
                    <option value="<?= $s['step_key'] ?>" <?= ($filterStatus ?? '') === $s['step_key'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['label']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter: Flag -->
            <div class="filter-pill">
                <label class="pill-label">Atensi</label>
                <select name="flag" onchange="this.form.submit()" class="pill-select">
                    <option value="">Semua Records</option>
                    <option value="1" <?= ($filterFlag ?? '') === '1' ? 'selected' : '' ?>>🚩 Kendala Aktif</option>
                    <option value="0" <?= ($filterFlag ?? '') === '0' ? 'selected' : '' ?>>✅ Normal</option>
                </select>
            </div>

            <!-- Meta Actions -->
            <div class="refinement-actions">
                <button type="button" onclick="window.location.href='?gate=registrasi'" title="Reset Filter" class="btn-pill-action btn-pill-reset">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                </button>
                <button type="submit" class="btn-pill-action btn-pill-search">Cari</button>
            </div>
        </div>
    </form>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <?php if (empty($registrasiWithFlags)): ?>
            <p style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada registrasi</p>
        <?php else: ?>
            <div class="table-responsive" style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
                <table class="data-table" id="registrasiTable" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: var(--cream);">
                            <th style="padding: 12px 14px; text-align: left; font-weight: 600;">Reg. No</th>
                            <th style="padding: 12px 14px; text-align: left; font-weight: 600;" class="hide-mobile">Klien</th>
                            <th style="padding: 12px 14px; text-align: left; font-weight: 600;" class="hide-mobile">Layanan</th>
                            <th style="padding: 12px 14px; text-align: left; font-weight: 600;">Status</th>
                            <th style="padding: 12px 10px; text-align: center; font-weight: 600;">Flag</th>
                            <th style="padding: 12px 14px; text-align: center; font-weight: 600;" class="hide-mobile">Dibuat</th>
                            <th style="padding: 12px 14px; text-align: center; font-weight: 600;">Estimasi</th>
                            <th style="padding: 12px 14px; text-align: center; font-weight: 600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrasiWithFlags as $p): ?>
                        <tr data-status="<?= $p['status'] ?>" data-nama="<?= strtolower($p['klien_nama']) ?>" data-layanan="<?= $p['layanan_id'] ?>" data-flag="<?= $p['has_flag'] ? '1' : '0' ?>" style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 12px 14px; font-weight: 600;" data-label="Reg. No">
                                <?= htmlspecialchars($p['nomor_registrasi'] ?? '-') ?>
                                <div class="mobile-details" style="display: none; margin-top: 8px; font-size: 12px; color: var(--text-muted);">
                                    <div><strong>Klien:</strong> <?= htmlspecialchars($p['klien_nama']) ?></div>
                                    <div><strong>Layanan:</strong> <?= htmlspecialchars($p['nama_layanan']) ?></div>
                                    <div><strong>Dibuat:</strong> <?= date('d M Y', strtotime($p['created_at'])) ?></div>
                                    <div><strong>Estimasi:</strong> <?= date('d M Y', strtotime($p['target_completion_at'])) ?></div>
                                </div>
                            </td>
                            <td style="padding: 12px 14px;" data-label="Klien" class="hide-mobile"><?= htmlspecialchars($p['klien_nama']) ?></td>
                            <td style="padding: 12px 14px;" data-label="Layanan" class="hide-mobile"><?= htmlspecialchars($p['nama_layanan']) ?></td>
                            <td style="padding: 12px 14px;" data-label="Status">
                                <?php
                                $role = (int)($p['behavior_role'] ?? 0);
                                $bg = '#e3f2fd'; $color = '#1976d2'; $border = '#90caf9';
                                if ($role === 2) { $bg = '#fff3e0'; $color = '#f57c00'; $border = '#ffcc80'; }
                                if ($role === 3) { $bg = '#e8f5e9'; $color = '#2e7d32'; $border = '#a5d6a7'; }
                                if ($role === 4) { $bg = '#f3e5f5'; $color = '#7b1fa2'; $border = '#ce93d8'; }
                                if ($role === 5) { $bg = '#ffebee'; $color = '#c62828'; $border = '#ef9a9a'; }
                                ?>
                                <span class="badge" style="background: <?= $bg ?>; color: <?= $color ?>; border: 1px solid <?= $border ?>;">
                                    <?= htmlspecialchars($p['status_label'] ?? $p['status']) ?>
                                </span>
                            </td>
                            <td style="padding: 12px 10px; text-align: center;" data-label="Flag">
                                <?= !empty($p['has_flag']) ? '<span style="color: #ffc107; font-size: 16px;" title="Ada Kendala Aktif">🚩</span>' : '<span style="color: var(--primary); font-weight: bold; font-size: 14px;">-</span>' ?>
                            </td>
                            <td style="padding: 12px 14px; text-align: center;" data-label="Dibuat" class="hide-mobile">
                                <small style="display: block; font-size: 12px; color: var(--text);"><?= date('d/m/y', strtotime($p['created_at'])) ?></small>
                            </td>
                            <td style="padding: 12px 14px; text-align: center;" data-label="Estimasi">
                                <?php 
                                    $tgt = $p['target_completion_at'] ?? '';
                                    $isOverdue = isset($p['diff_raw']) && (int)$p['diff_raw'] >= 0;
                                    $color = $isOverdue ? '#b71c1c' : 'var(--text)';
                                    $weight = $isOverdue ? '700' : '600';

                                    if (!empty($tgt) && strtotime($tgt) > 0): 
                                ?>
                                    <small style="display: block; font-size: 12px; color: <?= $color ?>; font-weight: <?= $weight ?>;"><?= date('d/m/y', strtotime($tgt)) ?></small>
                                <?php else: ?>
                                    <small style="display: block; font-size: 12px; color: var(--text-muted);">N/A</small>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px 14px; text-align: center;" data-label="Aksi">
                                <a href="<?= APP_URL ?>/index.php?gate=registrasi_detail&id=<?= $p['id'] ?>" class="btn-sm btn-primary" style="display: inline-flex; align-items: center; gap: 6px; height: 32px; padding: 6px 12px; font-size: 12px; line-height: 1; box-sizing: border-box; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span style="white-space: nowrap;">Detail</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
            flex-wrap: wrap;
        ">
            <?php
            // Build query string with all filters preserved
            $queryParams = http_build_query([
                'gate' => 'registrasi',
                'search' => $search,
                'status' => $filterStatus,
                'layanan' => $filterLayanan,
                'flag' => $filterFlag
            ]);
            ?>
            
            <?php if ($page > 1): ?>
                <a href="?<?= $queryParams ?>&page=<?= $page - 1 ?>"
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
                <a href="?<?= $queryParams ?>&page=<?= $i ?>"
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
                <a href="?<?= $queryParams ?>&page=<?= $page + 1 ?>"
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

            <span style="margin-left: 16px; font-size: 13px; color: var(--text-muted);" class="pagination-info">
                Page <?= (string)($page ?? 1) ?> of <?= (string)($totalPages ?? 1) ?> (<?= (string)($total ?? 0) ?> items)
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- NO JavaScript filter - Server-side filtering only (guidesop.md compliant) -->

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
