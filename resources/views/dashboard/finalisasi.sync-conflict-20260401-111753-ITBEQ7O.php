<?php
/**
 * Tutup Registrasi View
 * Manage penutupan registrasi dengan status Selesai, Batal, dan Ditutup
 */

$currentUser = getCurrentUser();
$pageTitle = 'Tutup Registrasi';
$activePage = 'finalisasi';

require VIEWS_PATH . '/templates/header.php';
?>

<style>
/* Nora 2.0 Command Center Layout - Finalisasi */
.nora-filter-toolbar {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Row 1: Primary Actions (Search) */
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

/* Row 2: Refinements (Filter Tabs) */
.refinement-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 10px;
    background: var(--cream);
    border-radius: 10px;
    border: 1px solid rgba(156, 124, 56, 0.08);
}

.filter-tab {
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    transition: all 0.3s ease;
    background: var(--white);
    color: var(--text-muted);
    border: 1px solid var(--border);
}

.filter-tab.active.all { background: var(--primary); color: var(--gold); border-color: var(--primary); }
.filter-tab.active.selesai { background: #28a745; color: white; border-color: #28a745; }
.filter-tab.active.batal { background: #dc3545; color: white; border-color: #dc3545; }
.filter-tab.active.ditutup { background: #6c757d; color: white; border-color: #6c757d; }

.filter-tab:hover:not(.active) {
    background: var(--border);
    color: var(--primary);
}

/* Stats Grid Styles */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.stat-card {
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 90px;
    border: 1px solid var(--gold); /* Unified Gold Border */
}

.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }

.stat-value { font-size: 26px; font-weight: 800; margin-bottom: 2px; line-height: 1; color: white !important; }
.stat-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; opacity: 0.85; color: white !important; }

.stat-primary { background: linear-gradient(135deg, #1565c0, #1976d2); } /* Tersedia */
.stat-success { background: linear-gradient(135deg, #2e7d32, #388e3c); } /* Diserahkan */
.stat-danger  { background: linear-gradient(135deg, #c62828, #d32f2f); } /* Batal */
.stat-gold    { background: linear-gradient(135deg, #455a64, #607d8b); } /* Ditutup */

/* Hero Styles */
.finalisasi-hero {
    background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 16px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.hero-badge {
    display: inline-block;
    background: rgba(212, 175, 55, 0.15);
    color: var(--gold-light);
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 8px;
    border: 1px solid rgba(212, 175, 55, 0.3);
}

.hero-subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
}

@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(4, 1fr); gap: 10px; }
    .stat-card { min-height: 80px; padding: 10px; }
    .stat-value { font-size: 22px; }
}

@media (max-width: 768px) {
    .stats-grid { 
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 6px;
        margin-bottom: 12px;
    }
    
    .stat-card {
        padding: 6px 2px;
        min-height: 65px;
        border-radius: 8px;
    }

    .stat-value { font-size: 22px; margin-bottom: 0; }
    .stat-label { font-size: 7.5px; letter-spacing: 0.1px; }

    .command-row { flex-direction: column; gap: 8px; }
    .command-search-input { height: 42px; font-size: 13px; padding-left: 36px; }
    .search-icon-inside { left: 10px; }
    
    .refinement-row { 
        display: flex;
        flex-wrap: nowrap;
        gap: 4px;
        padding: 6px;
        justify-content: space-between !important;
    }
    
    .filter-tab {
        padding: 6px 4px;
        font-size: 10px;
        flex: 1;
        text-align: center;
        white-space: nowrap;
        border-radius: 4px;
        letter-spacing: -0.2px;
    }

    .dashboard-card { padding: 8px; }
}

/* Fix for ultra-small screens */
@media (max-width: 360px) {
    .stat-value { font-size: 20px; }
    .stat-label { font-size: 7px; }
    .filter-tab { font-size: 9px; padding: 5px 2px; }
}
</style>

<div class="finalisasi-container">
    <!-- Premium Hero Header -->
    <div class="finalisasi-hero">
        <span class="hero-badge">Case Closure & Finalization</span>
        <p class="hero-subtitle">Kelola penutupan registrasi yang telah selesai atau batal untuk diarsipkan secara permanen</p>
    </div>

    <!-- Stats Dashboard -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-value"><?= $result['stats']['total'] ?></div>
            <div class="stat-label">Tersedia</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-value"><?= $result['stats']['diserahkan'] ?></div>
            <div class="stat-label">Diserahkan</div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-value"><?= $result['stats']['batal'] ?></div>
            <div class="stat-label">Batal</div>
        </div>
        <div class="stat-card stat-gold">
            <div class="stat-value"><?= $result['stats']['ditutup'] ?></div>
            <div class="stat-label">Ditutup</div>
        </div>
    </div>

    <!-- Command Center Toolbar -->
    <div class="dashboard-card" style="padding: 16px; margin-bottom: 16px;">
        <form method="GET" action="" class="nora-filter-toolbar">
            <input type="hidden" name="gate" value="finalisasi">
            <input type="hidden" name="filter" value="<?= htmlspecialchars($result['filter']) ?>">

            <div class="command-row">
                <div class="search-command-group">
                    <div class="search-icon-inside">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <input type="text" name="search" class="command-search-input" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari nomor registrasi, nama klien, atau nomor HP..." onkeypress="if(event.keyCode === 13) { this.form.submit(); }" onchange="this.form.submit()" onblur="if(this.value !== '<?= addslashes($search ?? '') ?>') { this.form.submit(); }">
                </div>
            </div>

            <div class="refinement-row">
                <a href="?gate=finalisasi&filter=all&search=<?= urlencode($search) ?>&page=1" class="filter-tab all <?= $result['filter'] === 'all' ? 'active' : '' ?>">📦 Semua</a>
                <a href="?gate=finalisasi&filter=selesai&search=<?= urlencode($search) ?>&page=1" class="filter-tab selesai <?= $result['filter'] === 'selesai' ? 'active' : '' ?>">✅ Diserahkan</a>
                <a href="?gate=finalisasi&filter=batal&search=<?= urlencode($search) ?>&page=1" class="filter-tab batal <?= $result['filter'] === 'batal' ? 'active' : '' ?>">❌ Batal</a>
                <a href="?gate=finalisasi&filter=ditutup&search=<?= urlencode($search) ?>&page=1" class="filter-tab ditutup <?= $result['filter'] === 'ditutup' ? 'active' : '' ?>">📂 Ditutup</a>
                
                <style>
@media (max-width: 768px) {
    .desktop-only { display: none; }
}
</style>

                <div style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
                    <span class="desktop-only" style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                        Page <?= $result['pagination']['current_page'] ?> of <?= $result['pagination']['total_pages'] ?>
                    </span>
                    <button type="button" onclick="window.location.href='?gate=finalisasi'" class="btn-filter" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; background: var(--white); border: 1px solid var(--border); cursor: pointer;" title="Reset Dashboard">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Table Card -->
    <div class="dashboard-card">
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
                <table class="data-table" id="registrasiTable" style="width: 100%; min-width: 900px; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: var(--cream);">
                            <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: var(--primary);">NOMOR REGISTRASI</th>
                            <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: var(--primary);">KLIEN</th>
                            <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: var(--primary);">LAYANAN</th>
                            <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: var(--primary);">STATUS</th>
                            <th style="padding: 14px 16px; text-align: center; font-weight: 700; color: var(--primary);">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($result['data'])): ?>
                        <tr>
                            <td colspan="5" style="padding: 60px; text-align: center; color: var(--text-muted);">
                                <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.3;">📂</div>
                                <p style="font-weight: 600;">Tidak ada record ditemukan dalam kategori ini</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($result['data'] as $p): ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 14px 16px; font-weight: 700; color: var(--primary);">
                                <?= htmlspecialchars($p['nomor_registrasi']) ?>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 600;"><?= htmlspecialchars($p['klien_nama']) ?></div>
                                <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($p['klien_hp']) ?></div>
                            </td>
                            <td style="padding: 14px 16px;"><?= htmlspecialchars($p['nama_layanan']) ?></td>
                            <td style="padding: 14px 16px;">
                                <span class="badge badge-<?= $p['status'] ?>" style="
                                    display: inline-block;
                                    padding: 6px 14px;
                                    border-radius: 8px;
                                    font-size: 11px;
                                    font-weight: 800;
                                    text-transform: uppercase;
                                    letter-spacing: 0.5px;
                                    background: <?= $p['behavior_role'] === 5 ? '#e8f5e9' : ($p['behavior_role'] === 7 ? '#ffebee' : '#f5f5f5') ?>;
                                    color: <?= $p['behavior_role'] === 5 ? '#2e7d32' : ($p['behavior_role'] === 7 ? '#c62828' : '#616161') ?>;
                                    border: 1px solid <?= $p['behavior_role'] === 5 ? '#c8e6c9' : ($p['behavior_role'] === 7 ? '#ffcdd2' : '#e0e0e0') ?>;
                                ">
                                    <?= ($p['behavior_role'] == 5) ? 'Diserahkan' : ($p['behavior_role'] == 7 ? 'Batal' : ($p['behavior_role'] == 6 ? 'Ditutup' : $p['status'])) ?>
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <a href="<?= APP_URL ?>/index.php?gate=registrasi_detail_finalisasi&id=<?= $p['id'] ?>" 
                                   class="btn-sm btn-primary"
                                   style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 12px; background: var(--primary); color: var(--gold); transition: all 0.3s;"
                                   onmouseover="this.style.background='var(--primary-light)'; this.style.transform='translateY(-1px)';"
                                   onmouseout="this.style.background='var(--primary)'; this.style.transform='translateY(0)';"
                                   >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8"></line></svg>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Container -->
            <div style="margin-top: 24px;">
                <?php
                $queryParams = http_build_query([
                    'gate' => 'finalisasi',
                    'filter' => $result['filter'],
                    'search' => $search
                ]);
                ?>
                <div style="display: flex; justify-content: center; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <?php if ($result['pagination']['has_prev']): ?>
                        <a href="?<?= $queryParams ?>&page=<?= $result['pagination']['current_page'] - 1 ?>" 
                           style="padding: 10px 18px; background: var(--primary); color: var(--gold); border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13px;">← Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $result['pagination']['total_pages']; $i++): ?>
                        <a href="?<?= $queryParams ?>&page=<?= $i ?>" 
                           style="padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13px; <?= $i === $result['pagination']['current_page'] ? 'background: var(--primary); color: var(--gold);' : 'background: var(--cream); color: var(--text); border: 1px solid var(--border);' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($result['pagination']['has_next']): ?>
                        <a href="?<?= $queryParams ?>&page=<?= $result['pagination']['current_page'] + 1 ?>" 
                           style="padding: 10px 18px; background: var(--primary); color: var(--gold); border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13px;">Next →</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tutup Registrasi Modal -->
<div id="tutupModal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
">
    <div style="
        background: var(--white);
        border-radius: 12px;
        padding: 32px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    ">
        <div style="
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        ">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="#6c757d" style="flex-shrink: 0;">
                <path d="M16 9v10H8V9h8m-1-5H9v3h6V4m1 0h-4c0 0-1 2-1 3H8v2h8V6c0-1-1-3-1-3z"></path>
            </svg>
            <h3 style="margin: 0; color: var(--primary); font-size: 18px;">Tutup Registrasi</h3>
        </div>

        <div style="
            background: #e9ecef;
            border-left: 4px solid #6c757d;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 24px;
        ">
            <p style="margin: 0; color: #495057; font-size: 14px; line-height: 1.6;">
                <strong>Info:</strong> Menutup registrasi akan mengubah status menjadi <strong>"Ditutup"</strong>. 
                Registrasi yang ditutup dapat dibuka kembali jika diperlukan.
            </p>
        </div>

        <div style="margin-bottom: 24px;">
            <p style="margin: 0 0 8px 0; color: var(--text-muted); font-size: 13px;">Registrasi:</p>
            <p id="tutupRegistrasiInfo" style="margin: 0; color: var(--text); font-weight: 600; font-size: 15px;"></p>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="
                display: block;
                font-weight: 600;
                margin-bottom: 8px;
                color: var(--text);
                font-size: 14px;
            ">Catatan Penutupan</label>
            <textarea id="tutupNotes" rows="4" placeholder="Tambahkan catatan (optional)" style="
                width: 100%;
                padding: 12px 16px;
                border: 1px solid var(--border);
                border-radius: 8px;
                font-size: 14px;
                font-family: inherit;
                resize: vertical;
            "></textarea>
        </div>

        <div style="
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        ">
            <button type="button" onclick="closeTutupModal()" style="
                background: var(--cream);
                color: var(--text);
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            ">Batal</button>
            <button type="button" onclick="confirmTutup()" style="
                background: #6c757d;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            ">📁 Tutup Registrasi</button>
        </div>
    </div>
</div>

<!-- Reopen Modal -->
<div id="reopenModal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
">
    <div style="
        background: var(--white);
        border-radius: 12px;
        padding: 32px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    ">
        <div style="
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        ">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="#17a2b8" style="flex-shrink: 0;">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            <h3 style="margin: 0; color: var(--primary); font-size: 18px;">Buka Kembali Registrasi</h3>
        </div>

        <div style="
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 24px;
        ">
            <p style="margin: 0; color: #1565c0; font-size: 14px; line-height: 1.6;">
                <strong>Info:</strong> Membuka kembali registrasi yang ditutup akan mengembalikan status ke proses sebelumnya.
            </p>
        </div>

        <div style="margin-bottom: 24px;">
            <p style="margin: 0 0 8px 0; color: var(--text-muted); font-size: 13px;">Registrasi:</p>
            <p id="reopenRegistrasiInfo" style="margin: 0; color: var(--text); font-weight: 600; font-size: 15px;"></p>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="
                display: block;
                font-weight: 600;
                margin-bottom: 8px;
                color: var(--text);
                font-size: 14px;
            ">Target Status</label>
            <select id="reopenTargetStatus" style="
                width: 100%;
                padding: 12px 16px;
                border: 1px solid var(--border);
                border-radius: 8px;
                font-size: 14px;
                font-family: inherit;
            ">
                <option value="back_to_process">Kembalikan ke Proses (Pemeriksaan BPN)</option>
                <option value="selesai">Biarkan sebagai Selesai/Batal</option>
            </select>
        </div>

        <div style="
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        ">
            <button type="button" onclick="closeReopenModal()" style="
                background: var(--cream);
                color: var(--text);
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            ">Batal</button>
            <button type="button" onclick="confirmReopen()" style="
                background: #17a2b8;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            ">🔄 Buka Kembali</button>
        </div>
    </div>
</div>

<script>
let currentRegistrasiId = null;

function openTutupModal(registrasiId, nomorRegistrasi, status) {
    currentRegistrasiId = registrasiId;
    const statusLabel = STATUS_LABELS[status] || status;
    document.getElementById('tutupRegistrasiInfo').textContent = `${nomorRegistrasi} (${statusLabel})`;
    document.getElementById('tutupModal').style.display = 'flex';
}

function closeTutupModal() {
    document.getElementById('tutupModal').style.display = 'none';
    document.getElementById('tutupNotes').value = '';
    currentRegistrasiId = null;
}

function confirmTutup() {
    const notes = document.getElementById('tutupNotes').value;
    const formData = new FormData();
    formData.append('registrasi_id', currentRegistrasiId);
    formData.append('target_status', 'ditutup');
    formData.append('notes', notes);
    formData.append('csrf_token', '<?= generateCSRFToken() ?>');

    fetch(`${APP_URL}/index.php?gate=tutup_registrasi`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan koneksi.');
        console.error(error);
    });
}

function openReopenModal(registrasiId, nomorRegistrasi) {
    currentRegistrasiId = registrasiId;
    document.getElementById('reopenRegistrasiInfo').textContent = nomorRegistrasi;
    document.getElementById('reopenModal').style.display = 'flex';
}

function closeReopenModal() {
    document.getElementById('reopenModal').style.display = 'none';
    currentRegistrasiId = null;
}

function confirmReopen() {
    const targetStatus = document.getElementById('reopenTargetStatus').value;
    const formData = new FormData();
    formData.append('registrasi_id', currentRegistrasiId);
    formData.append('target_status', targetStatus);
    formData.append('csrf_token', '<?= generateCSRFToken() ?>');

    fetch(`${APP_URL}/index.php?gate=reopen_case`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan koneksi.');
        console.error(error);
    });
}

// Close modals on outside click
document.getElementById('tutupModal').addEventListener('click', function(e) {
    if (e.target === this) closeTutupModal();
});

document.getElementById('reopenModal').addEventListener('click', function(e) {
    if (e.target === this) closeReopenModal();
});
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
