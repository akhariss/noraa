<?php
/**
 * Dashboard War Room v5.0 - AA Command Center
 * Features: Luxe UI, NEW pulsating indicators, Tactical SWD Analytics
 */

$currentUser = getCurrentUser();
$pageTitle = 'Pusat Komando';
$activePage = 'laporan';
$role = $currentUser['role'] ?? ROLE_STAFF;

require VIEWS_PATH . '/templates/header.php';
?>

<!-- Premium UI Assets -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
    .war-room-container { padding: 0; animation: fadeIn 0.4s ease-out; }
    
    /* ═══ Header Section ═══ */
    .hero-banner {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 25px;
        text-align: center;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(156, 124, 56, 0.2);
    }
    .hero-banner h1 { font-family: 'Cormorant Garamond', serif; color: #fff; font-size: 28px; margin: 0 0 10px 0; letter-spacing: 1px; }
    .hero-banner p { color: rgba(255,255,255,0.8); font-size: 14px; margin: 0; }

    /* ═══ Pilar Stats ═══ */
    .pilar-group { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px; }
    .pilar-card { 
        background: white; 
        border: 1px solid var(--border); 
        border-radius: 12px; 
        padding: 24px; 
        text-align: center; 
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .pilar-card:hover { transform: translateY(-5px); border-color: var(--gold); box-shadow: 0 8px 24px rgba(156, 124, 56, 0.1); }
    .pilar-card .val { display: block; font-size: 36px; font-weight: 800; color: var(--primary); line-height: 1; }
    .pilar-card .lbl { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-top: 10px; letter-spacing: 1.5px; }

    /* ═══ NEW Indicator ═══ */
    @keyframes blink-new { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
    .new-badge { 
        background: var(--gold); 
        color: #fff; 
        font-size: 9px; 
        font-weight: 900; 
        padding: 1px 5px; 
        border-radius: 3px; 
        margin-left: 5px; 
        animation: blink-new 1.5s infinite;
        vertical-align: middle;
    }

    /* ═══ Sector Containers ═══ */
    .vault-container { background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .vault-header { background: #fcfbf8; padding: 18px 25px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .vault-header h3 { font-size: 13px; font-weight: 800; color: var(--primary); margin: 0; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 10px; }
    
    .data-table th { background: #f9f7f0; color: var(--text-muted); font-size: 11px; text-transform: uppercase; font-weight: 800; padding: 12px 20px; }
    .data-table td { padding: 15px 20px; border-bottom: 1px solid #f5f5f5; font-size: 13px; }

    /* ═══ Analytics ═══ */
    .analytics-grid { display: grid; grid-template-columns: 3fr 2fr; gap: 20px; margin-bottom: 30px; }
    .node-swd { background: #1B3A4B; padding: 30px; border-radius: 15px; color: #fff; position: relative; border-left: 6px solid var(--gold); }
    .node-chart { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid var(--border); }
    
    @media (max-width: 992px) { .analytics-grid { grid-template-columns: 1fr; } }
</style>

<div class="war-room-container">

    <!-- HERO BANNER -->
    <div class="hero-banner">
        <h1>Welcome Back, AA</h1>
        <p>Laporan taktis dan pengawasan dokumen operasional secara real-time di Localhost</p>
    </div>

    <!-- STATS -->
    <div class="pilar-group">
        <div class="pilar-card"><span class="val"><?= (int)$warRoomStats['total'] ?></span><span class="lbl">Total File</span></div>
        <div class="pilar-card"><span class="val" style="color:var(--gold);"><?= (int)$warRoomStats['aktif'] ?></span><span class="lbl">Proses Aktif</span></div>
        <div class="pilar-card"><span class="val" style="color:#2e7d32;"><?= (int)$warRoomStats['pending'] ?></span><span class="lbl">Review Boss</span></div>
        <div class="pilar-card"><span class="val" style="color:#b71c1c;"><?= (int)$warRoomStats['terkendala'] ?></span><span class="lbl">Kendala</span></div>
    </div>

    <!-- MAIN OPERATIONS -->
    <div class="ops-sector">
        <!-- Prioritas Deadline -->
        <div class="vault-container">
            <div class="vault-header">
                <h3><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> Prioritas Deadline</h3>
                <a href="<?= APP_URL ?>/index.php?gate=registrasi&order=terlambat" style="font-size:11px; font-weight:700; color:var(--primary); text-decoration:none;">LIHAT SEMUA →</a>
            </div>
            <div class="table-responsive">
                <table class="data-table" style="width:100%;">
                    <thead><tr><th>No Reg</th><th>Klien</th><th class="hide-mobile">Status</th><th>Estimasi</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php if(empty($overdueTasks)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:40px; color:#999; font-style:italic;">Semua tuntas. Operasional aman.</td></tr>
                        <?php else: ?>
                            <?php foreach(array_slice($overdueTasks, 0, 5) as $row): ?>
                            <?php $isNew = (strtotime($row['created_at']) > (time() - 86400)); ?>
                            <tr>
                                <td style="font-weight:700;">
                                    <?= $row['nomor_registrasi'] ?>
                                    <?php if($isNew): ?><span class="new-badge">NEW</span><?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['klien_nama']) ?></td>
                                <td class="hide-mobile">
                                    <?php 
                                    $bRole = (int)($row['behavior_role'] ?? 1);
                                    $sS = \App\Domain\Entities\Registrasi::getStatusStyle($bRole); 
                                    ?>
                                    <span class="badge" style="background:<?= $sS['bg'] ?>; color:<?= $sS['color'] ?>; border-color:<?= $sS['border'] ?>;"><?= htmlspecialchars($row['status_label']) ?></span>
                                </td>
                                <td style="font-weight:800; color:<?= $row['diff_raw'] >= 0 ? '#b71c1c' : '#2e7d32' ?>;">
                                    <?= $row['diff_raw'] >= 0 ? 'H+' . $row['diff_raw'] : 'H' . $row['diff_raw'] ?>
                                </td>
                                <td><a href="<?= APP_URL ?>/index.php?gate=registrasi_detail&id=<?= $row['id'] ?>" class="btn-sm btn-primary" style="padding:4px 10px; font-size:11px; text-decoration:none;">Buka</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Menunggu Review -->
        <?php if ($role === ROLE_OWNER): ?>
        <div class="vault-container">
            <div class="vault-header">
                <h3><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Menunggu Review (AA)</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table" style="width:100%;">
                    <thead><tr><th>No Reg</th><th>Klien</th><th class="hide-mobile">Layanan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php if(empty($pendingApproval)): ?>
                            <tr><td colspan="4" style="text-align:center; padding:30px; color:#999;">Tidak ada berkas menunggu review.</td></tr>
                        <?php else: ?>
                            <?php foreach(array_slice($pendingApproval, 0, 5) as $row): ?>
                            <?php $isNew = (strtotime($row['created_at']) > (time() - 86400)); ?>
                            <tr>
                                <td style="font-weight:700;">
                                    <?= $row['nomor_registrasi'] ?>
                                    <?php if($isNew): ?><span class="new-badge">NEW</span><?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['klien_nama']) ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($row['nama_layanan']) ?></td>
                                <td><a href="<?= APP_URL ?>/index.php?gate=registrasi_detail&id=<?= $row['id'] ?>" class="btn-sm btn-primary" style="padding:4px 10px; font-size:11px; text-decoration:none;">Tinjau</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- ANALYTICS -->
    <div class="analytics-grid">
        <div class="node-swd">
            <h3 style="font-size:14px; color:#fff; margin-bottom:15px; font-family:'Cormorant Garamond', serif; font-weight:700;">NARASI TAKTIS AA</h3>
            <div id="swdText" style="font-size:14px; line-height:1.8; opacity:0.9;">Menganalisa kinerja operasional...</div>
        </div>
        <div class="node-chart">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <h3 style="font-size:12px; font-weight:800; margin:0; color:var(--primary);">KOMPOSISI LAYANAN</h3>
                <input type="month" id="chDate" style="font-size:10px; padding:4px;" value="<?= date('Y-m') ?>" onchange="updateCh()">
            </div>
            <div style="height:200px;"><canvas id="aaChart"></canvas></div>
        </div>
    </div>

</div>

<script>
    let myChart = null;
    function updateCh() {
        const d = document.getElementById('chDate').value;
        const swd = document.getElementById('swdText');
        fetch(`<?= APP_URL ?>/index.php?gate=dashboard&ajax=chart&date=${d}`).then(r => r.json()).then(data => {
            if(myChart) myChart.destroy();
            const colors = ['#1B3A4B','#9C7C38','#2E7D32','#B71C1C','#455A64'];
            myChart = new Chart(document.getElementById('aaChart').getContext('2d'), {
                type: 'doughnut',
                data: { labels: data.map(n => n.label), datasets: [{ data: data.map(n => n.value), backgroundColor: colors, borderWidth: 0 }] },
                options: { cutout: '70%', plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10, weight: '700' } } } }, responsive:true, maintainAspectRatio:false }
            });
            if(data.length > 0) {
                swd.innerHTML = `<p>Performa bulan ini didominasi oleh layanan <strong>${data[0].label}</strong>. Fokus tim harus tertuju pada penyelesaian backlog di sektor ini guna menjaga kepuasan klien AA.</p>`;
            } else swd.innerHTML = 'Belum ada data tercatat untuk periode ini.';
        });
    }
    document.addEventListener('DOMContentLoaded', updateCh);
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
