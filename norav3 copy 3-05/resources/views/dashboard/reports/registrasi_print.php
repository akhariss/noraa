<?php
/**
 * Laporan Audit Registrasi - FORMAL PRINT EDITION
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Audit - <?= date('d-m-Y') ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.1;
            color: #1a1a1a;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }

        .page-break {
            page-break-before: always;
        }

        .kop-surat {
            text-align: center;
            border-bottom: 2pt solid #000;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }

        .kop-surat h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-surat p {
            margin: 0;
            font-size: 8pt;
            color: #444;
        }

        .report-header {
            display: table;
            width: 100%;
            border-bottom: 0.5pt solid #eee;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .report-header h2 {
            display: table-cell;
            text-align: left;
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            vertical-align: bottom;
        }

        .report-header .period {
            display: table-cell;
            text-align: right;
            font-size: 9pt;
            color: #444;
            vertical-align: bottom;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 0.5pt solid #333;
            padding: 4px 6px;
        }

        th {
            background: #f2f2f2;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .matrix-table th,
        .matrix-table td {
            font-size: 7pt;
            text-align: center;
        }

        .matrix-table .klien-col {
            text-align: left;
            font-weight: bold;
        }

        .summary-row td {
            border: 1pt solid #000;
            padding: 6px;
            text-align: center;
        }

        .summary-box-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            display: block;
            margin-bottom: 1px;
        }

        .summary-box-value {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
        }

        .section-title {
            font-weight: bold;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
            border-left: 3pt solid #000;
            padding-left: 7px;
            font-size: 8.5pt;
        }

        .narrative {
            border: 1pt solid #000;
            padding: 10px;
            text-align: justify;
            background: #fff;
            font-style: italic;
            font-size: 9pt;
            line-height: 1.4;
        }

        .overdue {
            font-weight: bold;
            text-decoration: underline;
            color: #d00;
        }

        .workflow-legend-container {
            margin-bottom: 8px;
            padding: 6px;
            background: #f9f9f9;
            border: 0.5pt solid #ddd;
        }

        .workflow-table {
            border: none;
            margin-bottom: 0;
        }

        .workflow-table td {
            border: none;
            padding: 2px 4px;
            font-size: 7pt;
            text-align: left;
        }

        .workflow-num {
            display: inline-block;
            background: #000;
            color: #fff;
            width: 11pt;
            height: 11pt;
            text-align: center;
            line-height: 11pt;
            font-weight: bold;
            border-radius: 2px;
            margin-right: 4px;
            font-size: 6.5pt;
        }

        .signature-block {
            margin-top: 30px;
            text-align: right;
        }

        .sig-box {
            width: 280px;
            display: inline-block;
            text-align: center;
        }

        .sig-space {
            height: 60px;
        }

        /* Page Numbering */
        .footer {
            position: fixed;
            bottom: -5mm;
            left: 0;
            right: 0;
            height: 5mm;
            text-align: center;
            font-size: 7pt;
            color: #999;
        }

        .footer .page-number:after {
            content: "Halaman " counter(page);
        }

        /* Distribution & Matrix cell overrides for better A4 fit */
        .small-txt { font-size: 6.5pt; font-weight: normal; }
        .bold-txt { font-weight: bold; }
        .center-txt { text-align: center; }
        .right-txt { text-align: right; }

        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <div class="footer">
        <span class="page-number"></span>
    </div>

    <?php if (!isset($_GET['format']) || $_GET['format'] !== 'download'): ?>
        <div class="no-print"
            style="background: #333; padding: 6px; text-align: center; position: sticky; top: 0; z-index: 9999;">
            <button onclick="window.print()" style="padding: 6px 14px; font-weight: bold; cursor: pointer;">🖨️ CETAK
                LAPORAN</button>
            <button onclick="window.close()" style="padding: 6px 14px; margin-left: 10px;">❌ TUTUP</button>
        </div>
    <?php endif; ?>

    <!-- PAGE 1 -->
    <div id="page-1">
        <div class="kop-surat">
            <h1>KANTOR NOTARIS <?= strtoupper(e($cmsBranding['name'])) ?></h1>
            <p><?= e($cmsBranding['address']) ?> | WhatsApp / Phone: <?= e($cmsBranding['phone']) ?></p>
        </div>

        <div class="report-header">
            <h2>RINGKASAN AUDIT OPERASIONAL & REGISTRASI</h2>
            <div class="period">Periode: <strong><?= date('d/m/Y', strtotime($startDate)) ?></strong> s/d
                <strong><?= date('d/m/Y', strtotime($endDate)) ?></strong>
            </div>
        </div>

        <p class="section-title">1.1 RINGKASAN INDIKATOR UTAMA</p>
        <table class="summary-row">
            <tr>
                <td><span class="summary-box-label">Registrasi Baru</span><span
                        class="summary-box-value"><?= (int)$summary['registrasi_baru'] ?></span></td>
                <td><span class="summary-box-label">Registrasi Tutup</span><span
                        class="summary-box-value"><?= (int)$summary['registrasi_ditutup'] ?></span></td>
                <td><span class="summary-box-label">Masih Aktif</span><span
                        class="summary-box-value"><?= count($matrix['berjalan']) ?></span></td>
                <td><span class="summary-box-label">Total Tagihan</span><span class="summary-box-value">Rp
                        <?= number_format($summary['total_tagihan'], 0, ',', '.') ?></span></td>
                <td><span class="summary-box-label">Realisasi Bayar</span><span class="summary-box-value">Rp
                        <?= number_format($summary['total_terbayar'], 0, ',', '.') ?></span></td>
            </tr>
        </table>

        <p class="section-title">1.2 DISTRIBUSI LAYANAN</p>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th class="small-txt" style="text-align: left;">Jenis Layanan</th>
                    <th class="small-txt" style="width: 100px;">Jumlah</th>
                    <th class="small-txt" style="width: 100px;">%</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allDistribution as $d): ?>
                    <tr>
                        <td class="small-txt"><?= e($d['nama_layanan']) ?></td>
                        <td class="small-txt center-txt"><?= (int)$d['jumlah'] ?></td>
                        <td class="small-txt right-txt"><?= number_format((float)$d['persentase'], 1) ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="section-title">1.3 ANTRIAN BEBAN KERJA PER TAHAPAN</p>
        <table style="width: 100%; table-layout: fixed;">
            <thead>
                <tr><?php foreach ($allStageCounts as $s): ?>
                        <th class="small-txt"><?= e($s['label']) ?></th><?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr><?php foreach ($allStageCounts as $s): ?>
                        <td
                            class="center-txt"
                            style="font-size: 10pt; font-weight: bold; <?= $s['count'] > 0 ? 'background: #fff9c4;' : '' ?>">
                            <?= (int)$s['count'] ?>
                        </td><?php endforeach; ?>
                </tr>
            </tbody>
        </table>

        <p class="section-title">1.4 RINGKASAN KEUANGAN & REKONSILIASI</p>
        <table style="width: 100%; border: 1.5pt solid #000;">
            <tbody>
                <tr style="border-bottom: 1pt solid #000;">
                    <td class="small-txt" style="background: #eee; width: 40%; padding: 6px;">TOTAL
                        POTENSI TAGIHAN</td>
                    <td class="right-txt" style="font-size: 10pt; font-weight: bold; padding: 6px;">Rp
                        <?= number_format($summary['total_tagihan'], 0, ',', '.') ?>
                    </td>
                </tr>
                <tr style="border-bottom: 1pt solid #000;">
                    <td class="small-txt" style="background: #eee; padding: 6px;">TOTAL REALISASI
                        TERBAYAR</td>
                    <td class="right-txt" style="font-size: 10pt; font-weight: bold; color: green; padding: 6px;">Rp
                        <?= number_format($summary['total_terbayar'], 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <td class="small-txt" style="background: #eee; padding: 6px;">TOTAL SISA
                        PIUTANG BERJALAN</td>
                    <td class="right-txt" style="font-size: 10pt; font-weight: bold; color: #d00; padding: 6px;">Rp
                        <?= number_format($piutang, 0, ',', '.') ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="display: table; width: 100%; margin-top: 15px; border-spacing: 0;">
            <div style="display: table-cell; width: 68%; vertical-align: top; padding-right: 15px;">
                <p class="section-title" style="margin-top: 0;">1.5 NARASI ANALISIS AUDIT (STORYTELLING)</p>
                <div class="narrative">
                    "Periode ini, kantor menerima <strong><?= (int)$summary['registrasi_baru'] ?> registrasi baru</strong>.
                    Tingkat penyelesaian berada di angka <strong><?= number_format($completionRate, 1) ?>%</strong>.
                    Terdapat <strong><?= (int)$totalOverdue ?> berkas (<?= number_format($overdueRate, 1) ?>%)</strong> overdue SLA.
                    Realisasi pembayaran mencapai <strong>Rp
                        <?= number_format($summary['total_terbayar'], 0, ',', '.') ?></strong>."
                </div>
            </div>
            <div style="display: table-cell; width: 32%; vertical-align: top;">
                <p class="section-title" style="margin-top: 0;">INFORMASI DOKUMEN</p>
                <div style="border: 0.5pt solid #ddd; padding: 8px; background: #fafafa; font-size: 8pt;">
                    <p style="margin: 0;">ID Laporan: AUD-<?= date('YmdHis') ?></p>
                    <p style="margin: 0;">Waktu Cetak: <?= date('d/m/y H:i') ?></p>
                    <p style="margin: 0;">Total Record: <?= (int)$totalMasuk ?> Berkas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGE 2 -->
    <div class="page-break"></div>
    <div style="display: table; width: 100%; border-bottom: 1.5pt solid #000; margin-bottom: 6px; padding-bottom: 4px;">
        <div style="display: table-cell; vertical-align: bottom;">
            <h3 style="margin: 0; font-size: 10pt; text-transform: uppercase;">2. REGISTRASI AKTIF</h3>
        </div>
        <div style="display: table-cell; text-align: right; font-size: 9pt; font-weight: bold;">TOTAL:
            <?= count($matrix['berjalan']) ?> BERKAS
        </div>
    </div>

    <div class="workflow-legend-container">
        <table class="workflow-table">
            <?php $steps = $matrix['steps_aktif'];
            $cols = ceil(count($steps) / 2);
            for ($i = 0; $i < 2; $i++): ?>
                <tr>
                    <?php for ($j = 0; $j < $cols; $j++):
                        $idx = ($i * $cols) + $j;
                        if (isset($steps[$idx])): ?>
                            <td><span class="workflow-num"><?= $idx + 1 ?></span> <?= e($steps[$idx]['label']) ?></td>
                        <?php else: ?>
                            <td></td><?php endif; endfor; ?>
                </tr>
            <?php endfor; ?>
        </table>
    </div>

    <table class="matrix-table">
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 100px;">REGISTRASI</th>
                <th style="text-align: left;">NAMA KLIEN</th>
                <th style="width: 90px;">LAYANAN</th><?php foreach ($matrix['steps_aktif'] as $idx => $s): ?>
                    <th style="width: 26px;"><?= $idx + 1 ?></th><?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matrix['berjalan'] as $idx => $row): ?>
                <tr>
                    <td><?= $idx + 1 ?></td>
                    <td style="font-weight: bold;"><?= e($row['nomor']) ?></td>
                    <td class="klien-col"><?= e($row['klien']) ?></td>
                    <td><?= e($row['layanan']) ?></td>
                    <?php foreach ($matrix['steps_aktif'] as $s):
                        $days = $row['durations'][$s['id']] ?? null;
                        // Use Service Constant for SLA check
                        $excludedRoles = \App\Services\ReportService::SLA_EXCLUDED_BEHAVIOR_ROLES;
                        $isOver = ($days !== null && $days > (int) $s['sla_days'] && !in_array((int)$s['behavior_role'], $excludedRoles)); ?>
                        <td class="<?= $isOver ? 'overdue' : '' ?>"><?= $days !== null ? $days : '-' ?></td><?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- PAGE 3 -->
    <div class="page-break"></div>
    <div style="display: table; width: 100%; border-bottom: 1.5pt solid #333; margin-bottom: 6px; padding-bottom: 4px;">
        <div style="display: table-cell; vertical-align: bottom;">
            <h3 style="margin: 0; font-size: 10pt; text-transform: uppercase;">3. REGISTRASI SELESAI</h3>
        </div>
        <div style="display: table-cell; text-align: right; font-size: 9pt; font-weight: bold;">TOTAL:
            <?= count($matrix['selesai']) ?> BERKAS
        </div>
    </div>

    <div class="workflow-legend-container">
        <table class="workflow-table">
            <?php $steps = $matrix['steps_selesai'];
            $cols = ceil(count($steps) / 2);
            for ($i = 0; $i < 2; $i++): ?>
                <tr>
                    <?php for ($j = 0; $j < $cols; $j++):
                        $idx = ($i * $cols) + $j;
                        if (isset($steps[$idx])): ?>
                            <td><span class="workflow-num"><?= $idx + 1 ?></span> <?= e($steps[$idx]['label']) ?></td>
                        <?php else: ?>
                            <td></td><?php endif; endfor; ?>
                </tr>
            <?php endfor; ?>
        </table>
    </div>

    <table class="matrix-table" style="font-size: 6.5pt;">
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 90px;">REGISTRASI</th>
                <th style="text-align: left;">NAMA KLIEN</th>
                <th style="width: 80px;">LAYANAN</th>
                <th style="width: 45px;">DIBUAT</th>
                <th style="width: 45px;">SELESAI</th><?php foreach ($matrix['steps_selesai'] as $idx => $s): ?>
                    <th style="width: 22px;"><?= $idx + 1 ?></th><?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matrix['selesai'] as $idx => $row): ?>
                <tr>
                    <td><?= $idx + 1 ?></td>
                    <td style="font-weight: bold;"><?= e($row['nomor']) ?></td>
                    <td class="klien-col"><?= e($row['klien']) ?></td>
                    <td><?= e($row['layanan']) ?></td>
                    <td class="small-txt"><?= date('d/m/y', strtotime($row['created_at'])) ?></td>
                    <td class="small-txt">
                        <?= $row['selesai_at'] ? date('d/m/y', strtotime($row['selesai_at'])) : '-' ?>
                    </td>
                    <?php foreach ($matrix['steps_selesai'] as $s):
                        $days = $row['durations'][$s['id']] ?? null;
                        $excludedRoles = \App\Services\ReportService::SLA_EXCLUDED_BEHAVIOR_ROLES;
                        $isOver = ($days !== null && $days > (int) $s['sla_days'] && !in_array((int)$s['behavior_role'], $excludedRoles)); ?>
                        <td class="<?= $isOver ? 'overdue' : '' ?>"><?= $days !== null ? $days : '-' ?></td><?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="signature-block">
        <div class="sig-box">
            <p style="margin-bottom: 2px;">Cirebon, .................................... 20<?= date('y') ?></p>
            <p style="margin-bottom: 5px; font-weight: bold; font-size: 8pt;">Penanggung Jawab,</p>
            <div class="sig-space"></div>
            <p style="margin-bottom: 2px;"><strong><?= e($cmsBranding['name']) ?></strong></p>
            <p style="margin-top: 0; font-size: 8pt; border-top: 0.5pt solid #000; display: inline-block; padding-top: 2px; min-width: 150px;">Notaris & PPAT</p>
        </div>
    </div>
</body>

</html>