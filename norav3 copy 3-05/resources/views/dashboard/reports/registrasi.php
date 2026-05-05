<?php
/**
 * Laporan Audit Registrasi - STRICT PLAN EDITION (Hybrid)
 * Strictly follows PLAN_REPORT_AUDIT.md structure with Natural Luxe UI.
 */

$currentUser = getCurrentUser();
$pageTitle = 'Audit Registrasi';
$activePage = 'laporan';

require VIEWS_PATH . '/templates/header.php';
?>

<style>
/* ═══ Natural Luxe Audit Theme - SHARP EDITION ═══ */
.audit-body { 
    padding: 20px 15px; 
    background: var(--cream); 
    min-height: 100vh; 
    font-family: var(--font-primary);
}

/* Section Cards */
.audit-card-lx {
    background: #fff; 
    border-radius: 12px; 
    border: 1px solid var(--border);
    box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
    margin-bottom: 20px; 
    overflow: hidden;
}

.card-head-lx { 
    padding: 15px 25px; 
    border-bottom: 1px solid var(--border); 
    background: #fcfcfc;
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
}

.card-head-lx h3 { 
    margin: 0; 
    font-size: 13px; 
    font-weight: 800; 
    color: var(--primary); 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
}

.card-body-lx { padding: 25px; }

/* 1.2 Summary Grid */
.summary-grid-plan { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 30px; }
.sum-box-plan { 
    background: #fff; padding: 20px; border-radius: 10px; border: 1px solid var(--border); text-align: center;
    transition: 0.2s;
}
.sum-box-plan .lbl { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px; display: block; }
.sum-box-plan .val { font-size: 26px; font-weight: 800; color: var(--primary); }

/* 1.5 Storytelling */
.story-block-lx { 
    background: #fafafa; 
    border-left: 5px solid var(--gold); 
    padding: 20px 25px; 
    font-size: 15px; line-height: 1.6; color: var(--text); font-style: italic;
    border-radius: 0 8px 8px 0;
}

/* Matrix Legend */
.legend-box-lx { 
    display: flex; flex-wrap: wrap; gap: 8px; padding: 15px; background: #f9f9f9; 
    border-radius: 8px; margin-bottom: 15px; border: 1px solid var(--border);
}
.legend-item-lx { 
    font-size: 11px; font-weight: 700; color: var(--text-light); background: #fff; 
    padding: 4px 10px; border-radius: 4px; border: 1px solid var(--border);
}

/* Matrix Table */
.lx-matrix-table { width: 100%; border-collapse: collapse; }
.lx-matrix-table th { 
    font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--primary); 
    padding: 12px 5px; background: #fcfcfc; 
    border-bottom: 2px solid var(--border); text-align: center;
}
.lx-matrix-table td { padding: 12px 10px; border-bottom: 1px solid #eee; font-size: 12px; vertical-align: middle; }
.lx-matrix-table tr:hover td { background: #fdfdfd; }

.day-tag { 
    display: inline-block; padding: 3px 6px; border-radius: 4px; font-weight: 800; font-size: 11px; 
    min-width: 28px; text-align: center;
}
.day-overdue { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.day-active { background: #fefce8; color: #854d0e; border: 1px solid #fef08a; }
.day-done { background: #f1f5f9; color: #475569; border: 1px solid var(--border); }

.btn-lx-action { 
    background: var(--primary); color: #fff; border: none; height: 38px; padding: 0 20px; 
    border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s;
}
.btn-lx-action:hover { background: var(--primary-dark); }

.lx-btn-download {
    background: #fff; color: var(--primary); border: 1px solid var(--primary);
    padding: 0 15px; border-radius: 6px; height: 38px; display: flex; align-items: center; 
    gap: 8px; font-size: 12px; font-weight: 700; text-decoration: none; transition: 0.2s;
}
.lx-btn-download:hover { background: var(--primary); color: #fff; }

/* PDF Optimizations */
.is-generating-pdf .audit-body { padding: 0 !important; background: #fff !important; width: 1150px !important; }
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
                <input type="date" name="start" value="<?= e($startDate) ?>" style="height:38px; padding:0 12px; border-radius:8px; border:1.5px solid var(--border); font-weight:700;">
                <input type="date" name="end" value="<?= e($endDate) ?>" style="height:38px; padding:0 12px; border-radius:8px; border:1.5px solid var(--border); font-weight:700;">
                <button type="submit" class="btn-lx-action">Filter</button>
            </form>
            <a href="index.php?gate=laporan_registrasi&start=<?= urlencode($startDate) ?>&end=<?= urlencode($endDate) ?>&format=download" class="lx-btn-download">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download Laporan PDF
            </a>
        </div>
    </div>

    <div id="report-content">
        <!-- 1.2 Summary Cards -->
        <div class="summary-grid-plan">
            <div class="sum-box-plan">
                <span class="lbl">Registrasi Baru</span>
                <span class="val"><?= (int)$summary['registrasi_baru'] ?></span>
            </div>
            <div class="sum-box-plan" style="border-bottom: 3px solid #10b981;">
                <span class="lbl">Registrasi Ditutup</span>
                <span class="val" style="color:#10b981;"><?= (int)$summary['registrasi_ditutup'] ?></span>
            </div>
            <div class="sum-box-plan">
                <span class="lbl">Masih Aktif</span>
                <span class="val"><?= (int)($summary['masih_aktif'] ?? 0) ?></span>
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

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 0px;">
            <!-- 1.3 Persebaran Layanan -->
            <div class="audit-card-lx" style="margin-bottom: 0;">
                <div class="card-head-lx"><h3>📊 Persebaran Layanan</h3></div>
                <div class="card-body-lx">
                    <table class="dist-table-lx" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="text-align:left; font-size:10px;">JENIS LAYANAN</th>
                                <th style="text-align:center; font-size:10px;">JUMLAH</th>
                                <th style="text-align:right; font-size:10px;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allDistribution as $d): ?>
                            <tr>
                                <td style="font-weight:800; color:var(--primary);"><?= e($d['nama_layanan']) ?></td>
                                <td style="text-align:center; font-weight:900;"><?= (int)$d['jumlah'] ?></td>
                                <td style="text-align:right; color:#94a3b8; font-weight:700;"><?= number_format((float)$d['persentase'], 1) ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 1.5 Storytelling -->
            <div class="audit-card-lx" style="margin-bottom: 0;">
                <div class="card-head-lx"><h3>📝 Ringkasan Naratif (Storytelling)</h3></div>
                <div class="card-body-lx">
                    <div class="story-block-lx">
                        "Periode ini, kantor menerima <strong><?= (int)$summary['registrasi_baru'] ?> registrasi baru</strong>. 
                        Tingkat penyelesaian (Closing Rate) berada di angka <strong><?= number_format($completionRate, 1) ?>%</strong>. 
                        Saat ini terdapat <strong><?= (int)$totalOverdue ?> berkas (<?= number_format($overdueRate, 1) ?>%)</strong> yang mengalami keterlambatan SLA (Overdue).
                        
                        Bottleneck utama terdeteksi pada tahap pengerjaan yang melebihi batas waktu yang ditentukan. 
                        Secara finansial, realisasi pembayaran mencapai <strong>Rp <?= number_format($summary['total_terbayar'], 0, ',', '.') ?></strong> 
                        dari potensi tagihan baru sebesar Rp <?= number_format($summary['total_tagihan'], 0, ',', '.') ?>."
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.4 Matrix Timeline Registrasi Aktif -->
        <div class="audit-card-lx" style="margin-top: 20px;">
            <div class="card-head-lx">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <h3>📁 Registrasi Aktif</h3>
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 2px 10px; border-radius: 20px;">
                        <?= date('d/m/y', strtotime($startDate)) ?> — <?= date('d/m/y', strtotime($endDate)) ?>
                    </span>
                </div>
                <span style="font-size:10px; font-weight:900; color:#94a3b8;"><?= count($matrix['berjalan']) ?> BERKAS BERJALAN</span>
            </div>
            <div class="card-body-lx" style="padding: 15px 25px;">
                <!-- 2-Line Styled Legend -->
                <div style="display: grid; grid-template-columns: repeat(<?= ceil(count($matrix['steps_aktif'])/2) ?>, 1fr); gap: 8px 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
                    <?php foreach ($matrix['steps_aktif'] as $idx => $s): ?>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 10px;">
                        <span style="background: var(--gold); color: #fff; width: 18px; height: 18px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight: 900; flex-shrink: 0; border: 1px solid var(--gold-dark);"><?= $idx + 1 ?></span>
                        <span style="font-weight: 800; color: var(--primary); white-space: nowrap;"><?= e($s['label']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="overflow-x: auto;">
                    <table class="lx-matrix-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">NO</th>
                                <th style="width: 160px; text-align: left; padding-left: 15px;">NO. REGISTRASI</th>
                                <th style="min-width: 140px; text-align: left; padding-left: 15px;">KLIEN</th>
                                <th style="width: 110px; text-align: left; padding-left: 15px;">LAYANAN</th>
                                <?php foreach ($matrix['steps_aktif'] as $idx => $s): ?>
                                <th style="text-align: center; width: 42px;"><?= $idx + 1 ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matrix['berjalan'] as $idx => $row): 
                                $isRowOver = false;
                                foreach ($matrix['steps_aktif'] as $s) {
                                    if (isset($row['durations'][$s['id']]) && $row['durations'][$s['id']] > (int)$s['sla_days']) {
                                        $isRowOver = true; break;
                                    }
                                }
                            ?>
                            <tr>
                                <td style="text-align:center; font-weight:900; color:#cbd5e1;"><?= $idx + 1 ?></td>
                                <td style="padding-left: 15px; font-weight: 800; font-size: 11px;"><?= e($row['nomor']) ?></td>
                                <td style="padding-left: 15px;">
                                    <div style="font-weight:950; color:var(--primary); font-size:12px;"><?= e($row['klien']) ?></div>
                                </td>
                                <td style="padding-left: 15px;">
                                    <div style="font-size:9px; color:#94a3b8; font-weight:800; text-transform:uppercase;"><?= e($row['layanan']) ?></div>
                                </td>
                                <?php foreach ($matrix['steps_aktif'] as $s): 
                                    $days = $row['durations'][$s['id']] ?? null;
                                    $isCurrent = ($row['current_step'] == $s['id']);
                                    // Use Service Constant for SLA check
                                    $excludedRoles = \App\Services\ReportService::SLA_EXCLUDED_BEHAVIOR_ROLES;
                                    $isOver = ($days !== null && $days > (int)$s['sla_days'] && !in_array((int)$s['behavior_role'], $excludedRoles));
                                ?>
                                <td style="text-align: center; <?= $isCurrent ? 'background: rgba(212,175,55,0.02);' : '' ?>">
                                    <?php if ($days !== null): ?>
                                        <span class="day-tag <?= $isOver ? 'day-overdue' : ($isCurrent ? 'day-active' : 'day-done') ?>">
                                            <?= (float)$days ?>d<?= $isOver ? '!' : '' ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #f1f1f1;">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 1.6 Registrasi Selesai / Batal -->
        <div class="audit-card-lx">
            <div class="card-head-lx" style="background:#f8fafc; border-bottom:1px solid var(--border);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <h3>🏁 Registrasi Selesai</h3>
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; background: #fff; border: 1px solid var(--border); padding: 2px 10px; border-radius: 20px;">
                        <?= date('d/m/y', strtotime($startDate)) ?> — <?= date('d/m/y', strtotime($endDate)) ?>
                    </span>
                </div>
                <span style="font-size:10px; font-weight:900; color:var(--primary);"><?= count($matrix['selesai']) ?> BERKAS SELESAI</span>
            </div>
            <div class="card-body-lx" style="padding: 15px 25px;">
                <!-- 2-Line Styled Legend -->
                <div style="display: grid; grid-template-columns: repeat(<?= ceil(count($matrix['steps_selesai'])/2) ?>, 1fr); gap: 8px 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
                    <?php foreach ($matrix['steps_selesai'] as $idx => $s): ?>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 10px;">
                        <span style="background: #1e293b; color: #fff; width: 18px; height: 18px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight: 900; flex-shrink: 0;"><?= $idx + 1 ?></span>
                        <span style="font-weight: 800; color: var(--primary); white-space: nowrap;"><?= e($s['label']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="overflow-x: auto;">
                    <table class="lx-matrix-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">NO</th>
                                <th style="width: 160px; text-align: left; padding-left: 15px;">NO. REGISTRASI</th>
                                <th style="min-width: 140px; text-align: left; padding-left: 15px;">KLIEN</th>
                                <th style="width: 110px; text-align: left; padding-left: 15px;">LAYANAN</th>
                                <th style="width: 75px; text-align: center;">DIBUAT</th>
                                <th style="width: 75px; text-align: center;">SELESAI</th>
                                <?php foreach ($matrix['steps_selesai'] as $idx => $s): ?>
                                <th style="text-align: center; width: 42px;"><?= $idx + 1 ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matrix['selesai'] as $idx => $row): ?>
                            <tr>
                                <td style="text-align:center; font-weight:900; color:#cbd5e1;"><?= $idx + 1 ?></td>
                                <td style="padding-left: 15px; font-weight: 800; font-size: 11px;"><?= e($row['nomor']) ?></td>
                                <td style="padding-left: 15px;">
                                    <div style="font-weight:950; color:var(--primary); font-size:12px;"><?= e($row['klien']) ?></div>
                                </td>
                                <td style="padding-left: 15px;">
                                    <div style="font-size:9px; color:#94a3b8; font-weight:800; text-transform:uppercase;"><?= e($row['layanan']) ?></div>
                                </td>
                                <td style="text-align: center; font-size: 10px; font-weight: 700;"><?= date('d/m/y', strtotime($row['created_at'])) ?></td>
                                <td style="text-align: center; font-size: 10px; font-weight: 700; color: #10b981;"><?= $row['selesai_at'] ? date('d/m/y', strtotime($row['selesai_at'])) : '-' ?></td>
                                <?php foreach ($matrix['steps_selesai'] as $s): 
                                    $days = $row['durations'][$s['id']] ?? null;
                                    $excludedRoles = \App\Services\ReportService::SLA_EXCLUDED_BEHAVIOR_ROLES;
                                    $isOver = ($days !== null && $days > (int)$s['sla_days'] && !in_array((int)$s['behavior_role'], $excludedRoles));
                                ?>
                                <td style="text-align: center;">
                                    <?php if ($days !== null): ?>
                                        <span class="day-tag <?= $isOver ? 'day-overdue' : 'day-done' ?>"><?= (float)$days ?>d</span>
                                    <?php else: ?>
                                        <span style="color: #f1f1f1;">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($matrix['selesai'])): ?>
                            <tr>
                                <td colspan="<?= count($matrix['steps_selesai']) + 6 ?>" style="text-align:center; padding:20px; color:#94a3b8; font-weight:800; font-size:12px;">Tidak ada data selesai pada periode ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
