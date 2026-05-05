<?php
/**
 * Laporan Aktivitas - High Clarity Edition
 * Focus: Staff Performance Transparency & Contribution Audit.
 */

$currentUser = getCurrentUser();
$pageTitle = 'Audit Kinerja';
$activePage = 'laporan';

require VIEWS_PATH . '/templates/header.php';
?>

<style>
/* ═══ High-Clarity Performance Theme ═══ */
.audit-body { padding: 25px; background: #fdfcfb; min-height: 100vh; }

.leader-grid-lx {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;
}
.leader-card-lx {
    background: #fff; border-radius: 15px; border: 1.5px solid var(--border); overflow: hidden;
}
.leader-card-lx h3 { 
    margin: 0; padding: 15px 20px; font-size: 11px; font-weight: 900; color: var(--primary); 
    text-transform: uppercase; background: #fdfcfb; border-bottom: 1.5px solid var(--border);
}

.rank-row-lx { 
    display: flex; align-items: center; gap: 15px; padding: 12px 20px; 
    border-bottom: 1px solid #f8f9fa; transition: 0.2s;
}
.rank-row-lx:last-child { border-bottom: none; }
.rank-row-lx:hover { background: #fdfaf3; }

.rank-badge {
    width: 24px; height: 24px; border-radius: 50%; background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 950; color: #94a3b8;
}
.rank-1 .rank-badge { background: var(--gold); color: var(--primary); box-shadow: 0 4px 10px rgba(212,175,55,0.2); }

.user-info { flex-grow: 1; }
.user-info .name { font-size: 13px; font-weight: 800; color: var(--primary); display: block; }
.user-info .role { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }

.score-box { text-align: right; }
.score-box .val { font-size: 15px; font-weight: 950; color: var(--primary); display: block; }
.score-box .lbl { font-size: 8px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }

.btn-lx-pro { background: var(--primary); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 800; font-size: 12px; cursor: pointer; }

@media print { .no-print { display: none !important; } .audit-body { padding: 0; } }
</style>

<div class="audit-body">
    <!-- Header Controls -->
    <div class="no-print" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px 25px; border-radius: 12px; border: 1.5px solid var(--border);">
        <div style="font-size: 18px; font-weight: 900; color: var(--primary); letter-spacing: -0.5px;">Audit Produktivitas Tim</div>
        <form method="GET" style="display: flex; gap: 12px; align-items: center;">
            <input type="hidden" name="gate" value="laporan_aktivitas">
            <input type="date" name="start" value="<?= $startDate ?>" style="height: 38px; padding: 0 12px; border-radius: 8px; border: 1px solid #ddd; font-weight: 700;">
            <input type="date" name="end" value="<?= $endDate ?>" style="height: 38px; padding: 0 12px; border-radius: 8px; border: 1px solid #ddd; font-weight: 700;">
            <button type="submit" class="btn-lx-pro">Update</button>
            <a href="javascript:void(0)" 
               onclick="downloadFormalPDF(this)"
               class="btn-lx-pro" 
               style="background:#fff; color:var(--primary); border:1.5px solid var(--primary); text-decoration:none; display:flex; align-items:center; margin-left:10px;">
               📥 Download PDF Formal
            </a>
        </form>
    </div>

<!-- Hidden Formal Template for PDF Generation -->
<div id="formal-print-template" style="display:none;">
    <div style="font-family: 'Times New Roman', serif; padding: 20px; color: #000; background: #fff;">
        <div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 22px; text-transform: uppercase;"><?= OFFICE_NAME ?></h1>
            <p style="margin: 2px 0; font-size: 12px;"><?= OFFICE_ADDRESS ?> | Telp: <?= OFFICE_PHONE ?></p>
        </div>
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 18px; text-decoration: underline; text-transform: uppercase;">Laporan Produktivitas Tim</h2>
            <p style="margin: 5px 0; font-size: 14px; font-weight: bold;">Periode: <?= date('d M Y', strtotime($startDate)) ?> — <?= date('d M Y', strtotime($endDate)) ?></p>
        </div>
        
        <h3 style="font-size: 12px; text-transform: uppercase; border-left: 4px solid #000; padding-left: 10px;">Ringkasan Aktivitas Staff</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 5px; background: #f0f0f0;">Nama User</th>
                    <th style="border: 1px solid #000; padding: 5px; background: #f0f0f0;">Role</th>
                    <th style="border: 1px solid #000; padding: 5px; background: #f0f0f0; text-align: center;">Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankings['updaters'] as $r): ?>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; font-weight: bold;"><?= $r['name'] ?></td>
                    <td style="border: 1px solid #000; padding: 5px;"><?= strtoupper($r['role']) ?></td>
                    <td style="border: 1px solid #000; padding: 5px; text-align: center;"><?= $r['total'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 50px; text-align: right;">
            <p>Disetujui Oleh,</p>
            <div style="height: 60px;"></div>
            <p><strong><?= OFFICE_NAME ?></strong></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script>
function downloadFormalPDF(btn) {
    const originalText = btn.innerHTML;
    btn.innerHTML = '⌛ Memproses PDF...';
    btn.style.opacity = '0.5';
    
    const element = document.getElementById('formal-print-template');
    element.style.display = 'block';

    const opt = {
        margin: [15, 15, 15, 15],
        filename: 'Produktivitas_Staff_<?= date('d-m-Y') ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, letterRendering: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        element.style.display = 'none';
        btn.innerHTML = originalText;
        btn.style.opacity = '1';
    });
}
</script>

    <div id="report-content">
        <div class="leader-grid-lx">
            <!-- Creators -->
            <div class="leader-card-lx">
                <h3>🏆 Berkas Baru (Pendaftaran)</h3>
                <?php foreach ($rankings['creators'] as $idx => $r): ?>
                <div class="rank-row-lx <?= $idx == 0 ? 'rank-1' : '' ?>">
                    <div class="rank-badge"><?= $idx + 1 ?></div>
                    <div class="user-info">
                        <span class="name"><?= htmlspecialchars($r['name']) ?></span>
                        <span class="role"><?= $r['role'] ?></span>
                    </div>
                    <div class="score-box">
                        <span class="val"><?= $r['total'] ?></span>
                        <span class="lbl">Berkas</span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($rankings['creators'])): ?>
                <div style="padding:40px; text-align:center; color:#94a3b8; font-size:11px; font-weight:800;">BELUM ADA DATA</div>
                <?php endif; ?>
            </div>

            <!-- Updaters -->
            <div class="leader-card-lx">
                <h3>🔄 Update Status (Operasional)</h3>
                <?php foreach ($rankings['updaters'] as $idx => $r): ?>
                <div class="rank-row-lx <?= $idx == 0 ? 'rank-1' : '' ?>">
                    <div class="rank-badge"><?= $idx + 1 ?></div>
                    <div class="user-info">
                        <span class="name"><?= htmlspecialchars($r['name']) ?></span>
                        <span class="role"><?= $r['role'] ?></span>
                    </div>
                    <div class="score-box">
                        <span class="val"><?= $r['total'] ?></span>
                        <span class="lbl">Proses</span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($rankings['updaters'])): ?>
                <div style="padding:40px; text-align:center; color:#94a3b8; font-size:11px; font-weight:800;">BELUM ADA DATA</div>
                <?php endif; ?>
            </div>

            <!-- Collectors -->
            <div class="leader-card-lx">
                <h3>💰 Penerimaan Kas (Kasir)</h3>
                <?php foreach ($rankings['collectors'] as $idx => $r): ?>
                <div class="rank-row-lx <?= $idx == 0 ? 'rank-1' : '' ?>">
                    <div class="rank-badge"><?= $idx + 1 ?></div>
                    <div class="user-info">
                        <span class="name"><?= htmlspecialchars($r['name']) ?></span>
                        <span class="role"><?= $r['role'] ?></span>
                    </div>
                    <div class="score-box">
                        <span class="val" style="color:#10b981;">Rp <?= number_format($r['total_nominal'], 0, ',', '.') ?></span>
                        <span class="lbl"><?= $r['total_transaksi'] ?> Transaksi</span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($rankings['collectors'])): ?>
                <div style="padding:40px; text-align:center; color:#94a3b8; font-size:11px; font-weight:800;">BELUM ADA DATA</div>
                <?php endif; ?>
            </div>
        </div>

        <div style="background: #fff; padding: 25px; border-radius: 15px; border: 1.5px solid var(--border); border-left: 5px solid var(--gold);">
            <h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 900; color: var(--primary);">💡 Business Intelligence Note</h4>
            <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.6; font-style: italic;">
                "Data di atas mencerminkan kontribusi nyata staff terhadap operasional kantor. Staff dengan ranking tertinggi menunjukkan dedikasi yang tinggi dalam menjaga alur kerja tetap berjalan sesuai target."
            </p>
        </div>
    </div>
</div>

<style>
<?php require VIEWS_PATH . '/templates/footer.php'; ?>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
