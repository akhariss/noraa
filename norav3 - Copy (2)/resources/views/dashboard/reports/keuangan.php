<?php
/**
 * Laporan Keuangan - High Clarity Edition
 * Focus: Detailed Arus Kas, Receivable Audit, and Professional Transparency.
 */

$currentUser = getCurrentUser();
$pageTitle = 'Audit Keuangan';
$activePage = 'laporan';

require VIEWS_PATH . '/templates/header.php';

// Calculate advanced finance metrics
$totalPiutang = 0;
foreach($unpaidList as $u) $totalPiutang += $u['sisa'];

$totalOmzetPotential = $summary['total_tagihan'] ?? 0;
$totalRealisasi = $summary['total_terbayar'] ?? 0;
$collectionRate = $totalOmzetPotential > 0 ? round(($totalRealisasi / $totalOmzetPotential) * 100, 1) : 0;
?>

<style>
/* ═══ High-Clarity Finance Theme ═══ */
.audit-body { padding: 25px; background: #fdfcfb; min-height: 100vh; }

.audit-summary-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;
}
.summary-box {
    background: #fff; padding: 20px; border-radius: 12px; border: 1.5px solid var(--border);
    box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column;
}
.summary-box .lbl { font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
.summary-box .val { font-size: 18px; font-weight: 950; color: var(--primary); }
.summary-box.danger .val { color: #ef4444; }
.summary-box.success .val { color: #10b981; }

.audit-table-card { background: #fff; border-radius: 15px; border: 1.5px solid var(--border); overflow: hidden; margin-bottom: 30px; }
.table-title { padding: 15px 25px; background: #fdfcfb; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.table-title h3 { margin: 0; font-size: 14px; font-weight: 900; color: var(--primary); text-transform: uppercase; }

.lx-audit-table { width: 100%; border-collapse: collapse; }
.lx-audit-table th { padding: 12px 20px; background: #fdfcfb; text-align: left; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; border-bottom: 1.5px solid var(--border); }
.lx-audit-table td { padding: 15px 20px; border-bottom: 1px solid #f8f9fa; vertical-align: middle; }
.lx-audit-table tr:hover td { background: #fdfaf3; }

.badge-fin { padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 900; border: 1px solid; }
.badge-unpaid { background: #fff1f2; color: #e11d48; border-color: #fecaca; }
.badge-paid { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }

.btn-lx-pro { background: var(--primary); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 800; font-size: 12px; cursor: pointer; }

@media print { .no-print { display: none !important; } .audit-body { padding: 0; } }
</style>

<div class="audit-body">
    <!-- Financial Overview -->
    <div class="audit-summary-grid no-print">
        <div class="summary-box success">
            <span class="lbl">Realisasi (Uang Masuk)</span>
            <span class="val">Rp <?= number_format($totalRealisasi, 0, ',', '.') ?></span>
        </div>
        <div class="summary-box">
            <span class="lbl">Potensi Omzet Baru</span>
            <span class="val">Rp <?= number_format($totalOmzetPotential, 0, ',', '.') ?></span>
        </div>
        <div class="summary-box danger">
            <span class="lbl">Total Piutang Berjalan</span>
            <span class="val">Rp <?= number_format($totalPiutang, 0, ',', '.') ?></span>
        </div>
        <div class="summary-box">
            <span class="lbl">Collection Rate</span>
            <span class="val"><?= $collectionRate ?>% <small style="font-size: 10px; color: #94a3b8;">Terbayar</small></span>
        </div>
    </div>

    <!-- Filters -->
    <div class="no-print" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px 25px; border-radius: 12px; border: 1.5px solid var(--border);">
        <form method="GET" style="display: flex; gap: 15px; align-items: center;">
            <input type="hidden" name="gate" value="laporan_keuangan">
            <input type="date" name="start" value="<?= $startDate ?>" style="height: 38px; padding: 0 12px; border-radius: 8px; border: 1px solid #ddd; font-weight: 700;">
            <input type="date" name="end" value="<?= $endDate ?>" style="height: 38px; padding: 0 12px; border-radius: 8px; border: 1px solid #ddd; font-weight: 700;">
            <button type="submit" class="btn-lx-pro">Update Data</button>
        </form>
    <a href="index.php?gate=laporan_keuangan&start=<?= $startDate ?>&end=<?= $endDate ?>&format=download" 
       class="btn-lx-pro" 
       style="background:#fff; color:var(--primary); border:1.5px solid var(--primary); text-decoration:none; display:flex; align-items:center;">
       📥 Download PDF Formal (.doc)
    </a>
</div>

    <div id="report-content">
        <!-- Piutang Table -->
        <div class="audit-table-card">
            <div class="table-title">
                <h3>Detil Piutang Berjalan (Belum Lunas)</h3>
                <span style="font-size: 10px; font-weight: 800; color: #ef4444;"><?= count($unpaidList) ?> BERKAS PIUTANG</span>
            </div>
            <table class="lx-audit-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">NO</th>
                        <th style="min-width: 250px;">KLIEN & JENIS LAYANAN</th>
                        <th>TOTAL TAGIHAN</th>
                        <th style="color: #ef4444;">SISA PIUTANG</th>
                        <th>STATUS BAYAR</th>
                        <th>TAHAP BERKAS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unpaidList as $idx => $r): ?>
                    <tr>
                        <td style="text-align: center; font-weight: 900; color: #cbd5e1;"><?= $idx + 1 ?></td>
                        <td>
                            <div style="font-weight: 900; color: var(--primary); font-size: 14px;"><?= $r['klien_nama'] ?></div>
                            <div style="font-size: 10px; color: #94a3b8; font-weight: 800;"><?= $r['nomor_registrasi'] ?></div>
                        </td>
                        <td style="font-weight: 700;">Rp <?= number_format($r['total_tagihan'], 0, ',', '.') ?></td>
                        <td style="font-weight: 950; color: #ef4444;">Rp <?= number_format($r['sisa'], 0, ',', '.') ?></td>
                        <td><span class="badge-fin badge-unpaid">PIUTANG</span></td>
                        <td style="font-size: 11px; font-weight: 700; color: #64748b;"><?= $r['status_label'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Cash Timeline -->
        <div class="audit-table-card">
            <div class="table-title">
                <h3>Riwayat Penerimaan Kas (Audit Pembayaran)</h3>
                <span style="font-size: 10px; font-weight: 800; color: #10b981;"><?= count($history) ?> TRANSAKSI</span>
            </div>
            <table class="lx-audit-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">NO</th>
                        <th>WAKTU PEMBAYARAN</th>
                        <th style="min-width: 200px;">KLIEN</th>
                        <th style="text-align: right;">NOMINAL DITERIMA</th>
                        <th>OLEH STAFF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $idx => $h): ?>
                    <tr>
                        <td style="text-align: center; font-weight: 900; color: #cbd5e1;"><?= $idx + 1 ?></td>
                        <td style="font-size: 11px; font-weight: 700; color: #64748b;"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                        <td>
                            <div style="font-weight: 800; color: var(--primary);"><?= $h['klien_nama'] ?></div>
                            <div style="font-size: 10px; color: #94a3b8; font-weight: 800;"><?= $h['nomor_registrasi'] ?></div>
                        </td>
                        <td style="text-align: right; font-weight: 950; color: #10b981; font-size: 15px;">Rp <?= number_format($h['nominal_bayar'], 0, ',', '.') ?></td>
                        <td style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;"><?= htmlspecialchars($h['oleh']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* PDF Optimizations */
.is-generating-pdf .audit-body { padding: 0 !important; background: #fff !important; width: 800px !important; }
.is-generating-pdf .audit-table-card { border: 1px solid #eee !important; box-shadow: none !important; margin-bottom: 20px !important; }
.is-generating-pdf .lx-audit-table th { font-size: 8px !important; }
.is-generating-pdf .lx-audit-table td { font-size: 11px !important; padding: 10px !important; }
</style>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
