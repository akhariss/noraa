<?php
/**
 * Dashboard War Room v4.21 - UI CONSISTENCY FIX
 * UI: Synced fonts with Registrasi, Standard CSS, Precise stats.
 */

$currentUser = getCurrentUser();
$pageTitle = 'Pusat Komando';
$activePage = 'dashboard';
$role = $currentUser['role'] ?? ROLE_STAFF;

require VIEWS_PATH . '/templates/header.php';
?>

<!-- Premium UI Assets -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
    .war-room-container { padding: 0; animation: fadeIn 0.3s ease-in; font-size: 13px !important; }
    
    /* PILAR STATS */
    .pilar-group { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 25px; }
    .pilar-card { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; text-align: center; box-shadow: var(--shadow-sm); }
    .pilar-card .val { display: block; font-size: 32px; font-weight: 700; color: var(--primary); line-height: 1; }
    .pilar-card .lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-top: 8px; letter-spacing: 1px; }

    /* VAULT CONTAINERS */
    .ops-sector { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 25px; }
    .vault-container { background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm); }
    .vault-header { background: #fafafa; padding: 15px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .vault-header h3 { font-size: 14px; font-weight: 700; color: var(--primary); margin: 0; text-transform: uppercase; }
    .vault-nav { display: flex; gap: 10px; align-items: center; }
    .node-info { font-size: 10px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; }
    .nav-btn { width: 30px; height: 30px; border-radius: 6px; border: 1px solid var(--border); background: white; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .nav-btn:hover { background: var(--primary); color: white; }
    
    .carousel-row { display: none; }
    .carousel-row.active { display: table-row; }

    /* ANALYTICS */
    .analytics-cluster { display: grid; grid-template-columns: 3fr 2fr; gap: 20px; margin-bottom: 25px; }
    .node-swd { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 25px; border-left: 6px solid var(--gold); }
    .node-chart { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 25px; }
    .panel-ctrl { display: flex; gap: 12px; align-items: center; margin-bottom: 15px; }
    .input-nora { font-size: 11px; padding: 6px 10px; border: 1px solid var(--border); border-radius: 5px; font-weight: 700; color: var(--primary); }

    .pro-empty { padding: 40px; text-align: center; color: var(--text-muted); font-size: 13px; font-weight: 600; font-style: italic; }

    /* MOBILE Breakpoint (Based on screenshot) */
    @media (max-width: 768px) {
        .pilar-group { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .pilar-card .val { font-size: 24px; }
        .analytics-cluster { grid-template-columns: 1fr; }
        .hide-mobile { display: none; }
        .data-table td { padding: 12px 10px !important; }
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="war-room-container">

    <!-- PILAR STATS -->
    <div class="pilar-group">
        <div class="pilar-card"><span class="val"><?= (int)$warRoomStats['total'] ?></span><span class="lbl">Total</span></div>
        <div class="pilar-card"><span class="val"><?= (int)$warRoomStats['aktif'] ?></span><span class="lbl">Aktif</span></div>
        <div class="pilar-card"><span class="val" style="color:var(--gold);"><?= (int)$warRoomStats['pending'] ?></span><span class="lbl">Pending</span></div>
        <div class="pilar-card"><span class="val" style="color:#2e7d32;"><?= (int)$warRoomStats['ditutup'] ?></span><span class="lbl">Selesai</span></div>
        <div class="pilar-card"><span class="val" style="color:#b71c1c;"><?= (int)$warRoomStats['terkendala'] ?></span><span class="lbl">Kendala</span></div>
    </div>

    <!-- OPS SECTOR -->
    <div class="ops-sector">
        <?php if ($role === ROLE_STAFF): ?>
        <div class="vault-container" id="overdueVault">
            <div class="vault-header">
                <h3>🗓️ Prioritas Deadline</h3>
                <div class="vault-nav">
                    <span class="node-info" id="overdueCount">Node 1-5 dari <?= count($overdueTasks) ?></span>
                    <button class="nav-btn" onclick="rotateVault('overdueVault', -1)">←</button>
                    <button class="nav-btn" onclick="rotateVault('overdueVault', 1)">→</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table" style="margin:0; width:100%;">
                    <thead><tr><th>REG. NO</th><th>KLIEN</th><th class="hide-mobile">STATUS</th><th>ESTIMASI</th><th>AKSI</th></tr></thead>
                    <tbody>
                        <?php if(empty($overdueTasks)): ?>
                            <tr><td colspan="5" class="pro-empty">Semua tuntas. Sektor aman.</td></tr>
                        <?php else: ?>
                            <?php foreach($overdueTasks as $i => $row): ?>
                            <tr class="carousel-row <?= $i<5?'active':'' ?>" data-idx="<?= $i ?>">
                                <td style="color:var(--primary); font-size:12px;"><?= $row['nomor_registrasi'] ?></td>
                                <td><?= htmlspecialchars($row['klien_nama']) ?></td>
                                <td class="hide-mobile"><span class="badge badge-primary"><?= $row['status_label'] ?></span></td>
                                <td style="font-weight:700; color:<?= $row['diff_raw'] >= 0 ? '#b71c1c' : '#2e7d32' ?>;">
                                    <?= $row['diff_raw'] >= 0 ? 'H+' . $row['diff_raw'] : 'H' . $row['diff_raw'] ?>
                                </td>
                                <td><a href="<?= APP_URL ?>/index.php?gate=registrasi_detail&id=<?= $row['id'] ?>" class="btn-sm btn-primary" style="text-decoration:none; font-size:11px; padding:4px 10px;">Buka</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($role === ROLE_OWNER): ?>
        <div class="vault-container" id="approvalVault">
            <div class="vault-header">
                <h3>🛂 Menunggu Persetujuan</h3>
                <div class="vault-nav">
                    <span class="node-info" id="approvalCount">Node 1-5 dari <?= count($pendingApproval) ?></span>
                    <button class="nav-btn" onclick="rotateVault('approvalVault', -1)">←</button>
                    <button class="nav-btn" onclick="rotateVault('approvalVault', 1)">→</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table" style="margin:0; width:100%;">
                    <thead><tr><th>REG. NO</th><th>KLIEN</th><th class="hide-mobile">LAYANAN</th><th>AKSI</th></tr></thead>
                    <tbody>
                        <?php if(empty($pendingApproval)): ?>
                            <tr><td colspan="4" class="pro-empty">Semua tuntas.</td></tr>
                        <?php else: ?>
                            <?php foreach($pendingApproval as $i => $row): ?>
                            <tr class="carousel-row <?= $i<5?'active':'' ?>" data-idx="<?= $i ?>">
                                <td style="color:var(--primary); font-size:12px;"><?= $row['nomor_registrasi'] ?></td>
                                <td><?= htmlspecialchars($row['klien_nama']) ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($row['nama_layanan']) ?></td>
                                <td><a href="<?= APP_URL ?>/index.php?gate=registrasi_detail&id=<?= $row['id'] ?>" class="btn-sm btn-primary" style="text-decoration:none; font-size:11px; padding:4px 10px;">Tinjau</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <div class="vault-container" id="importantVault">
            <div class="vault-header">
                <h3>🚨 Bendera Penting</h3>
                <div class="vault-nav">
                    <span class="node-info" id="importantCount">Node 1-7 dari <?= count($importantTasks) ?></span>
                    <button class="nav-btn" onclick="rotateVault('importantVault', -1)">←</button>
                    <button class="nav-btn" onclick="rotateVault('importantVault', 1)">→</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table" style="margin:0; width:100%;">
                    <thead><tr><th>REG. NO</th><th>KLIEN</th><th class="hide-mobile">STATUS</th><th>AKSI</th></tr></thead>
                    <tbody>
                        <?php if(empty($importantTasks)): ?>
                            <tr><td colspan="4" class="pro-empty">Tidak ada bendera.</td></tr>
                        <?php else: ?>
                            <?php foreach($importantTasks as $i => $row): ?>
                            <tr class="carousel-row <?= $i<7?'active':'' ?>" data-idx="<?= $i ?>">
                                <td style="color:var(--primary); font-size:12px;"><?= $row['nomor_registrasi'] ?></td>
                                <td><?= htmlspecialchars($row['klien_nama']) ?></td>
                                <td class="hide-mobile"><span class="badge badge-primary" style="opacity:0.8;"><?= $row['status_label'] ?></span></td>
                                <td style="text-align:right;"><a href="<?= APP_URL ?>/index.php?gate=registrasi_detail&id=<?= $row['id'] ?>" class="btn-sm btn-primary" style="text-decoration:none; font-size:11px; padding:4px 10px;">Lihat</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DATA ANALYTICS -->
    <?php if ($role === ROLE_OWNER): ?>
    <div class="analytics-cluster">
        <div class="node-swd">
            <h3 style="font-size:13px; font-weight:700; color:var(--gold); margin-bottom:20px;">NARASI TAKTIS (SWD)</h3>
            <div id="narrativeEngine" style="color:var(--text); line-height:1.8;"><p>Memuat narasi operasional...</p></div>
        </div>
        <div class="node-chart">
            <div class="panel-ctrl">
                <h3 style="font-size:12px; font-weight:700; color:var(--primary); margin:0; flex:1;">GRAFIK</h3>
                <input type="month" id="timeAxis" class="input-nora" value="<?= date('Y-m') ?>" onchange="runAjax()">
            </div>
            <div style="height:200px;"><canvas id="mainWarChart"></canvas></div>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
    function rotateVault(id, dir) {
        let vault = document.getElementById(id); let rows = vault.querySelectorAll('.carousel-row');
        let stride = (id === 'importantVault' ? 7 : 5); if (rows.length <= stride) return;
        let first = 0; for(let i=0; i<rows.length; i++) { if(rows[i].classList.contains('active')) { first = i; break; } }
        rows.forEach(r => r.classList.remove('active')); let next = (first + (dir * stride) + rows.length) % rows.length;
        for(let i=0; i<stride; i++) { let idx = (next + i) % rows.length; if(rows[idx]) rows[idx].classList.add('active'); }
        let info = document.getElementById(id.replace('Vault', 'Count')); if(info) info.innerText = `Node ${next+1}-${Math.min(next+stride, rows.length)} dari ${rows.length}`;
    }
    setInterval(() => rotateVault('overdueVault', 1), 20000); setInterval(() => rotateVault('approvalVault', 1), 20000); setInterval(() => rotateVault('importantVault', 1), 20000);
    let pieInst = null;
    function runAjax() {
        const t = document.getElementById('timeAxis').value;
        const swd = document.getElementById('narrativeEngine'); swd.innerHTML = '<p style="opacity:0.5;">Analisa taktis berproses...</p>';
        fetch(`<?= APP_URL ?>/index.php?gate=dashboard&ajax=chart&period=monthly&date=${t}`).then(res => res.json()).then(data => {
            const labels = data.map(n => n.label); const values = data.map(n => n.value);
            if(pieInst) pieInst.destroy();
            pieInst = new Chart(document.getElementById('mainWarChart').getContext('2d'), {
                type: 'pie', data: { labels: labels, datasets: [{ data: values, backgroundColor: ['#1B3A4B','#9C7C38','#2E7D32','#B71C1C','#455A64'], borderWidth: 1 }] },
                options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: '600' } } } }, responsive: true, maintainAspectRatio: false }
            });
            if(data.length > 0) {
                const total = values.reduce((a, b) => a + b, 0);
                swd.innerHTML = `<p>Layanan dominan adalah <strong>${data[0].label}</strong> (${Math.round(data[0].value/total*100)}%). Total volume transaksi tercatat sebanyak <strong>${total}</strong> berkas.</p>`;
            } else swd.innerHTML = '<p class="pro-empty">Tidak ada data.</p>';
        });
    }
    document.addEventListener('DOMContentLoaded', runAjax);
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
