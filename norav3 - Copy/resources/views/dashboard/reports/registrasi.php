<?php
/**
 * Laporan Audit Registrasi - STRICT PLAN EDITION (Hybrid)
 * Strictly follows PLAN_REPORT_AUDIT.md structure with Natural Luxe UI.
 */

$currentUser = getCurrentUser();
$pageTitle = 'Audit Registrasi';
$activePage = 'laporan';

require VIEWS_PATH . '/templates/header.php';

// Calculate advanced metrics for Storytelling (1.5)
$totalMasuk = count($matrix['berjalan']) + count($matrix['selesai']);
$selesaiCount = count($matrix['selesai']);
$totalOverdue = 0;
$overdueList = [];

foreach (array_merge($matrix['berjalan'], $matrix['selesai']) as $row) {
    $rowOverdue = false;
    foreach ($matrix['steps'] as $s) {
        if (isset($row['durations'][$s['id']]) && $row['durations'][$s['id']] > (int)$s['sla_days']) {
            $totalOverdue++;
            $rowOverdue = true;
            break; 
        }
    }
    if ($rowOverdue) $overdueList[] = $row;
}

$completionRate = $totalMasuk > 0 ? round(($selesaiCount / $totalMasuk) * 100, 1) : 0;
$overdueRate = $totalMasuk > 0 ? round(($totalOverdue / $totalMasuk) * 100, 1) : 0;
?>

<style>
/* ═══ Natural Luxe Audit Theme ═══ */
.audit-body { padding: 30px; background: var(--cream); min-height: 100vh; font-family: 'DM Sans', sans-serif; }

/* Section Cards */
.audit-card-lx {
    background: #fff; border-radius: 16px; border: 1.5px solid var(--border);
    box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 30px; overflow: hidden;
}
.card-head-lx { padding: 20px 25px; border-bottom: 1px solid var(--border); background: #fdfcfb; display: flex; justify-content: space-between; align-items: center; }
.card-head-lx h3 { margin: 0; font-size: 13px; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; }
.card-body-lx { padding: 25px; }

/* 1.2 Summary Grid */
.summary-grid-plan { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 30px; }
.sum-box-plan { 
    background: #fff; padding: 20px; border-radius: 12px; border: 1.5px solid var(--border); text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.01);
}
.sum-box-plan .lbl { font-size: 9px; font-weight: 900; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; display: block; }
.sum-box-plan .val { font-size: 22px; font-weight: 950; color: var(--primary); }

/* 1.3 Service Dist Table */
.dist-table-lx { width: 100%; border-collapse: collapse; }
.dist-table-lx th { text-align: left; font-size: 10px; font-weight: 900; color: #94a3b8; padding: 12px; border-bottom: 1.5px solid var(--border); }
.dist-table-lx td { padding: 12px; font-size: 13px; border-bottom: 1px solid #f8f9fa; }

/* 1.5 Storytelling */
.story-block-lx { 
    background: #fdfcfb; border-left: 5px solid var(--gold); padding: 20px 25px; 
    font-size: 14px; line-height: 1.8; color: #475569; font-style: italic;
}

/* 1.4 Matrix Legend */
.legend-box-lx { 
    display: flex; flex-wrap: wrap; gap: 10px; padding: 15px; background: #f8fafc; 
    border-radius: 10px; margin-bottom: 15px; border: 1px solid #e2e8f0;
}
.legend-item-lx { font-size: 10px; font-weight: 800; color: #64748b; background: #fff; padding: 4px 10px; border-radius: 5px; border: 1px solid #e2e8f0; }

/* 1.4 Matrix Table */
.lx-matrix-table { width: 100%; border-collapse: collapse; }
.lx-matrix-table th { 
    font-size: 8px; font-weight: 900; text-transform: uppercase; color: var(--primary); 
    padding: 10px 2px; background: #fdfcfb; border-bottom: 1.5px solid var(--border); text-align: center;
}
.lx-matrix-table td { padding: 10px 5px; border-bottom: 1px solid #f8f9fa; font-size: 11px; vertical-align: middle; }
.day-tag { display: inline-block; padding: 2px 5px; border-radius: 4px; font-weight: 900; font-size: 10px; min-width: 24px; text-align: center; }
.day-overdue { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
.day-active { background: #fefce8; color: #854d0e; border: 1px solid #fef08a; }
.day-done { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

.btn-lx-action { background: var(--primary); color: #fff; border: none; height: 38px; padding: 0 20px; border-radius: 8px; font-size: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; }
.btn-lx-action:hover { opacity: 0.9; transform: translateY(-1px); }

/* PDF Optimizations */
.is-generating-pdf .audit-body { padding: 0 !important; background: #fff !important; width: 1150px !important; }
.is-generating-pdf .summary-grid-plan { gap: 10px !important; }
.is-generating-pdf .sum-box-plan { padding: 10px !important; }
.is-generating-pdf .sum-box-plan .val { font-size: 18px !important; }
.is-generating-pdf .lx-matrix-table th { font-size: 7px !important; }
.is-generating-pdf .lx-matrix-table td { font-size: 9.5px !important; }
</style>

<div class="audit-body">
    <!-- 1.1 Header Periode -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;" class="no-print">
        <div>
            <h1 style="margin:0; font-size:24px; font-weight:950; color:var(--primary); letter-spacing:-1px;">Laporan Registrasi & Audit</h1>
            <p style="margin:5px 0 0 0; font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Periode: <?= date('d M Y', strtotime($startDate)) ?> — <?= date('d M Y', strtotime($endDate)) ?></p>
        </div>
        <div style="display:flex; gap:10px;">
            <form method="GET" style="display:flex; gap:8px;">
                <input type="hidden" name="gate" value="laporan_registrasi">
                <input type="date" name="start" value="<?= $startDate ?>" style="height:38px; padding:0 12px; border-radius:8px; border:1.5px solid var(--border); font-weight:700;">
                <input type="date" name="end" value="<?= $endDate ?>" style="height:38px; padding:0 12px; border-radius:8px; border:1.5px solid var(--border); font-weight:700;">
                <button type="submit" class="btn-lx-action">Filter</button>
            </form>
            <a href="index.php?gate=laporan_registrasi&start=<?= $startDate ?>&end=<?= $endDate ?>&format=print" 
               target="_blank" 
               class="btn-lx-action" 
               style="background:#fff; color:var(--primary); border:2px solid var(--primary); display:flex; align-items:center; text-decoration:none;">
               📥 Cetak Laporan Formal (PDF)
            </a>
        </div>
    </div>

    <div id="report-content">
        <!-- 1.2 Summary Cards -->
        <div class="summary-grid-plan">
            <div class="sum-box-plan">
                <span class="lbl">Registrasi Baru</span>
                <span class="val"><?= $summary['registrasi_baru'] ?></span>
            </div>
            <div class="sum-box-plan" style="border-bottom: 3px solid #10b981;">
                <span class="lbl">Registrasi Ditutup</span>
                <span class="val" style="color:#10b981;"><?= $summary['registrasi_ditutup'] ?></span>
            </div>
            <div class="sum-box-plan">
                <span class="lbl">Masih Aktif</span>
                <span class="val"><?= $summary['masih_aktif'] ?? 0 ?></span>
            </div>
            <div class="sum-box-plan">
                <span class="lbl">Total Tagihan</span>
                <span class="val" style="font-size:16px;">Rp <?= number_format($summary['total_tagihan'], 0, ',', '.') ?></span>
            </div>
            <div class="sum-box-plan" style="border-bottom: 3px solid var(--gold);">
                <span class="lbl">Total Terbayar</span>
                <span class="val" style="font-size:16px; color:var(--gold);">Rp <?= number_format($summary['total_terbayar'], 0, ',', '.') ?></span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 30px;">
            <!-- 1.3 Persebaran Layanan -->
            <div class="audit-card-lx">
                <div class="card-head-lx"><h3>📊 Persebaran Layanan</h3></div>
                <div class="card-body-lx">
                    <table class="dist-table-lx">
                        <thead>
                            <tr>
                                <th>JENIS LAYANAN</th>
                                <th style="text-align:center;">JUMLAH</th>
                                <th style="text-align:right;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($distribution as $d): ?>
                            <tr>
                                <td style="font-weight:800; color:var(--primary);"><?= $d['nama_layanan'] ?></td>
                                <td style="text-align:center; font-weight:900;"><?= $d['jumlah'] ?></td>
                                <td style="text-align:right; color:#94a3b8; font-weight:700;"><?= $d['persentase'] ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 1.5 Storytelling -->
            <div class="audit-card-lx">
                <div class="card-head-lx"><h3>📝 Ringkasan Naratif (Storytelling)</h3></div>
                <div class="card-body-lx">
                    <div class="story-block-lx">
                        "Periode ini, kantor menerima <strong><?= $summary['registrasi_baru'] ?> registrasi baru</strong>. 
                        Tingkat penyelesaian (Closing Rate) berada di angka <strong><?= $completionRate ?>%</strong>. 
                        Saat ini terdapat <strong><?= $totalOverdue ?> berkas (<?= $overdueRate ?>%)</strong> yang mengalami keterlambatan SLA (Overdue).
                        
                        Bottleneck utama terdeteksi pada tahap pengerjaan yang melebihi batas waktu yang ditentukan. 
                        Secara finansial, realisasi pembayaran mencapai <strong>Rp <?= number_format($summary['total_terbayar'], 0, ',', '.') ?></strong> 
                        dari potensi tagihan baru sebesar Rp <?= number_format($summary['total_tagihan'], 0, ',', '.') ?>."
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.4 Matrix Timeline Registrasi Aktif -->
        <div class="audit-card-lx">
            <div class="card-head-lx">
                <h3>📁 1.4 Matrix Timeline Registrasi Aktif</h3>
                <span style="font-size:10px; font-weight:900; color:#94a3b8;"><?= count($matrix['berjalan']) ?> BERKAS BERJALAN</span>
            </div>
            <div class="card-body-lx" style="padding: 15px 25px;">
                <!-- Legend -->
                <div class="legend-box-lx">
                    <?php foreach ($matrix['steps'] as $s): ?>
                    <div class="legend-item-lx"><?= $s['id'] ?>: <?= $s['label'] ?></div>
                    <?php endforeach; ?>
                </div>

                <div style="overflow-x: auto;">
                    <table class="lx-matrix-table">
                        <thead>
                            <tr>
                                <th style="width: 30px;">NO</th>
                                <th style="min-width: 150px; text-align: left; padding-left: 15px;">KLIEN & LAYANAN</th>
                                <?php foreach ($matrix['steps'] as $s): ?>
                                <th><?= $s['id'] ?></th>
                                <?php endforeach; ?>
                                <th style="width: 100px; background: #fdfcfb; border-left: 2px solid #eee;">STATUS SLA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matrix['berjalan'] as $idx => $row): 
                                $isRowOver = false;
                                foreach ($matrix['steps'] as $s) {
                                    if (isset($row['durations'][$s['id']]) && $row['durations'][$s['id']] > (int)$s['sla_days']) {
                                        $isRowOver = true; break;
                                    }
                                }
                            ?>
                            <tr>
                                <td style="text-align:center; font-weight:900; color:#cbd5e1;"><?= $idx + 1 ?></td>
                                <td style="padding-left: 15px;">
                                    <div style="font-weight:950; color:var(--primary); font-size:12px;"><?= $row['klien'] ?></div>
                                    <div style="font-size:9px; color:#94a3b8; font-weight:800; text-transform:uppercase;"><?= $row['layanan'] ?></div>
                                </td>
                                <?php foreach ($matrix['steps'] as $s): 
                                    $days = $row['durations'][$s['id']] ?? null;
                                    $isCurrent = ($row['current_step'] == $s['id']);
                                    $isOver = ($days !== null && $days > (int)$s['sla_days']);
                                ?>
                                <td style="text-align: center; <?= $isCurrent ? 'background: rgba(212,175,55,0.02);' : '' ?>">
                                    <?php if ($days !== null): ?>
                                        <span class="day-tag <?= $isOver ? 'day-overdue' : ($isCurrent ? 'day-active' : 'day-done') ?>">
                                            <?= $days ?>d<?= $isOver ? '!' : '' ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #f1f1f1;">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                                <td style="text-align: center; border-left: 2px solid #eee; background: #fdfcfb;">
                                    <?php if ($isRowOver): ?>
                                        <span style="color:#ef4444; font-size:9px; font-weight:950;">🔴 OVERDUE</span>
                                    <?php else: ?>
                                        <span style="color:#10b981; font-size:9px; font-weight:950;">✅ NORMAL</span>
                                    <?php if (count($row['durations']) < 2) echo '<br><small style="color:#94a3b8; font-size:8px;">⏳ BARU</small>'; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 1.6 Registrasi Selesai/Batal -->
        <div class="audit-card-lx">
            <div class="card-head-lx">
                <h3>📁 1.6 Registrasi Batal / Selesai (Arsip)</h3>
                <span style="font-size:10px; font-weight:900; color:#10b981;"><?= count($matrix['selesai']) ?> BERKAS TERMINAL</span>
            </div>
            <div class="card-body-lx">
                <table class="dist-table-lx">
                    <thead>
                        <tr>
                            <th style="width:40px; text-align:center;">NO</th>
                            <th>KLIEN & LAYANAN</th>
                            <th>STATUS AKHIR</th>
                            <th style="text-align:center;">TOTAL DURASI</th>
                            <th style="text-align:right;">TANGGAL TUTUP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matrix['selesai'] as $idx => $row): ?>
                        <tr>
                            <td style="text-align:center; font-weight:900; color:#cbd5e1;"><?= $idx + 1 ?></td>
                            <td>
                                <div style="font-weight:900; color:var(--primary);"><?= $row['klien'] ?></div>
                                <div style="font-size:10px; color:#94a3b8; font-weight:800;"><?= $row['layanan'] ?></div>
                            </td>
                            <td>
                                <span class="badge-lx" style="font-size:10px; font-weight:900; padding:4px 10px; border-radius:6px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                                    <?= strtoupper($row['status_label'] ?? 'SELESAI') ?>
                                </span>
                            </td>
                            <td style="text-align:center; font-weight:950; color:var(--primary);"><?= $row['total_days'] ?> HARI</td>
                            <td style="text-align:right; font-size:11px; font-weight:800; color:#94a3b8;"><?= $row['target'] ? date('d M Y', strtotime($row['target'])) : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
