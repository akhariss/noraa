<?php
/**
 * Laporan Audit Keuangan - FORMAL PRINT EDITION
 * Strictly follows PLAN_REPORT_AUDIT.md Bagian 2.
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - <?= date('d-m-Y') ?></title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', serif; line-height: 1.4; color: #000; background: #fff; margin: 0; padding: 0; font-size: 11pt; }
        
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
        .kop-surat p { margin: 2px 0; font-size: 10pt; }

        .report-header { text-align: center; margin-bottom: 30px; }
        .report-header h2 { margin: 0; font-size: 14pt; text-decoration: underline; text-transform: uppercase; }
        .report-header p { margin: 5px 0; font-size: 11pt; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-size: 9pt; font-weight: bold; text-transform: uppercase; text-align: center; }
        
        .summary-row { display: flex; gap: 20px; margin-bottom: 25px; }
        .summary-box { flex: 1; border: 1px solid #000; padding: 10px; text-align: center; }
        .summary-box .label { font-size: 9pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #000; margin-bottom: 5px; padding-bottom: 5px; }
        .summary-box .value { font-size: 14pt; font-weight: bold; }

        .section-title { font-weight: bold; margin-bottom: 10px; text-transform: uppercase; font-size: 10pt; border-left: 4px solid #000; padding-left: 10px; }

        .signature-block { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig-box { width: 200px; text-align: center; }
        .sig-space { height: 70px; }
        
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <?php if (!isset($_GET['format']) || $_GET['format'] !== 'download'): ?>
    <div class="no-print" style="background: #333; padding: 10px; text-align: center; position: sticky; top: 0;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">🖨️ CETAK LAPORAN (PRINT)</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin-left: 10px;">❌ TUTUP</button>
    </div>
    <?php endif; ?>

    <div class="kop-surat">
        <h1><?= OFFICE_NAME ?></h1>
        <p><?= OFFICE_ADDRESS ?> | Telp: <?= OFFICE_PHONE ?></p>
        <p>Email: <?= OFFICE_EMAIL ?></p>
    </div>

    <div class="report-header">
        <h2>Laporan Audit Keuangan (Transaksi)</h2>
        <p>Periode: <?= date('d M Y', strtotime($startDate)) ?> — <?= date('d M Y', strtotime($endDate)) ?></p>
    </div>

    <!-- 2.1 Ringkasan Keuangan -->
    <div class="summary-row">
        <div class="summary-box">
            <div class="label">Total Masuk (Pembayaran)</div>
            <div class="value">Rp <?= number_format($summary['total_terbayar'], 0, ',', '.') ?></div>
        </div>
        <div class="summary-box">
            <div class="label">Jumlah Transaksi</div>
            <div class="value"><?= count($history) ?></div>
        </div>
        <div class="summary-box">
            <div class="label">Rata-rata Pembayaran</div>
            <div class="value">Rp <?= count($history) > 0 ? number_format($summary['total_terbayar'] / count($history), 0, ',', '.') : 0 ?></div>
        </div>
    </div>

    <!-- 2.2 Detail Per Registrasi — Aktif & BELUM Lunas -->
    <div class="section-title">2.2 Registrasi Aktif — Belum Lunas (Piutang)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>No. Registrasi / Klien</th>
                <th>Layanan</th>
                <th>Total Tagihan</th>
                <th>Sudah Bayar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($unpaidList as $idx => $row): ?>
            <tr>
                <td style="text-align: center;"><?= $idx + 1 ?></td>
                <td>
                    <strong><?= $row['nomor_registrasi'] ?></strong><br>
                    <?= $row['klien_nama'] ?>
                </td>
                <td><?= $row['nama_layanan'] ?></td>
                <td style="text-align: right;">Rp <?= number_format($row['total_tagihan'], 0, ',', '.') ?></td>
                <td style="text-align: right;">Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                <td style="text-align: right; font-weight: bold;">Rp <?= number_format($row['sisa'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($unpaidList)): ?>
            <tr><td colspan="6" style="text-align: center;">Tidak ada piutang aktif.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- 2.3 Detail Per Registrasi — Aktif & SUDAH Lunas -->
    <div class="section-title">2.3 Registrasi Aktif — Sudah Lunas</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>No. Registrasi / Klien</th>
                <th>Layanan</th>
                <th>Total Tagihan</th>
                <th>Status Tahapan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paidList as $idx => $row): ?>
            <tr>
                <td style="text-align: center;"><?= $idx + 1 ?></td>
                <td>
                    <strong><?= $row['nomor_registrasi'] ?></strong><br>
                    <?= $row['klien_nama'] ?>
                </td>
                <td><?= $row['nama_layanan'] ?></td>
                <td style="text-align: right;">Rp <?= number_format($row['total_tagihan'], 0, ',', '.') ?></td>
                <td style="text-align: center;"><?= strtoupper($row['status_label']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($paidList)): ?>
            <tr><td colspan="5" style="text-align: center;">Tidak ada registrasi aktif yang sudah lunas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="page-break-before: always;"></div>

    <!-- 2.4 Riwayat Pembayaran (Timeline) -->
    <div class="section-title">2.4 Riwayat Pembayaran (Timeline Transaksi)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Tanggal</th>
                <th>No. Registrasi / Klien</th>
                <th style="width: 120px;">Nominal</th>
                <th>Catatan / Perihal</th>
                <th style="width: 80px;">Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $row): ?>
            <tr>
                <td style="text-align: center;"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <strong><?= $row['nomor_registrasi'] ?></strong><br>
                    <?= $row['klien_nama'] ?>
                </td>
                <td style="text-align: right; font-weight: bold;">Rp <?= number_format($row['nominal_bayar'], 0, ',', '.') ?></td>
                <td><?= $row['catatan'] ?: '-' ?></td>
                <td style="text-align: center;"><?= $row['oleh'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="signature-block">
        <div class="sig-box" style="margin-left: auto;">
            <p>Disetujui Oleh,</p>
            <div class="sig-space"></div>
            <p><strong><?= OFFICE_NAME ?></strong></p>
        </div>
    </div>

</body>
</html>
