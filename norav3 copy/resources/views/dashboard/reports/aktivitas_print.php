<?php
/**
 * Laporan Produktivitas Tim - FORMAL PRINT EDITION
 * Strictly follows PLAN_REPORT_AUDIT.md Bagian 3.
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Produktivitas - <?= date('d-m-Y') ?></title>
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
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #f0f0f0; font-size: 9pt; font-weight: bold; text-transform: uppercase; text-align: center; }
        
        .section-title { font-weight: bold; margin-bottom: 10px; text-transform: uppercase; font-size: 10pt; border-left: 4px solid #000; padding-left: 10px; }

        .signature-block { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig-box { width: 200px; text-align: center; }
        .sig-space { height: 70px; }
        
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="no-print" style="background: #333; padding: 10px; text-align: center; position: sticky; top: 0;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">🖨️ CETAK LAPORAN (PRINT)</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin-left: 10px;">❌ TUTUP</button>
    </div>

    <div class="kop-surat">
        <h1><?= OFFICE_NAME ?></h1>
        <p><?= OFFICE_ADDRESS ?> | Telp: <?= OFFICE_PHONE ?></p>
        <p>Email: <?= OFFICE_EMAIL ?></p>
    </div>

    <div class="report-header">
        <h2>Laporan Audit Produktivitas & Aktivitas Tim</h2>
        <p>Periode: <?= date('d M Y', strtotime($startDate)) ?> — <?= date('d M Y', strtotime($endDate)) ?></p>
    </div>

    <!-- 3.2 Pembuat Registrasi -->
    <div class="section-title">3.2 Rangking: Pembuat Registrasi Terbanyak</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Rank</th>
                <th>Nama User</th>
                <th>Jabatan / Role</th>
                <th style="text-align: center;">Jumlah Registrasi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rankings['creators'] as $idx => $r): ?>
            <tr>
                <td style="text-align: center;"><?= $idx + 1 ?></td>
                <td style="font-weight: bold;"><?= $r['name'] ?></td>
                <td><?= strtoupper($r['role']) ?></td>
                <td style="text-align: center; font-weight: bold;"><?= $r['total'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 3.3 Update Status -->
    <div class="section-title">3.3 Rangking: Update Status Terbanyak (Proses)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Rank</th>
                <th>Nama User</th>
                <th>Jabatan / Role</th>
                <th style="text-align: center;">Jumlah Aktivitas Update</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rankings['updaters'] as $idx => $r): ?>
            <tr>
                <td style="text-align: center;"><?= $idx + 1 ?></td>
                <td style="font-weight: bold;"><?= $r['name'] ?></td>
                <td><?= strtoupper($r['role']) ?></td>
                <td style="text-align: center; font-weight: bold;"><?= $r['total'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 3.5 Input Pembayaran -->
    <div class="section-title">3.4 Rangking: Input Pembayaran (Collection)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Rank</th>
                <th>Nama User</th>
                <th>Jabatan / Role</th>
                <th style="text-align: center;">Jml Transaksi</th>
                <th style="text-align: right;">Total Nominal Terkumpul</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rankings['collectors'] as $idx => $r): ?>
            <tr>
                <td style="text-align: center;"><?= $idx + 1 ?></td>
                <td style="font-weight: bold;"><?= $r['name'] ?></td>
                <td><?= strtoupper($r['role']) ?></td>
                <td style="text-align: center; font-weight: bold;"><?= $r['total_transaksi'] ?></td>
                <td style="text-align: right; font-weight: bold;">Rp <?= number_format($r['total_nominal'], 0, ',', '.') ?></td>
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
