<?php
/**
 * Laporan Audit Registrasi - FORMAL PRINT EDITION
 * Strictly following PLAN_REPORT_AUDIT.md in a professional document format.
 */

// Calculate advanced metrics
$totalMasuk = count($matrix['berjalan']) + count($matrix['selesai']);
$selesaiCount = count($matrix['selesai']);
$totalOverdue = 0;
foreach (array_merge($matrix['berjalan'], $matrix['selesai']) as $row) {
    foreach ($matrix['steps'] as $s) {
        if (isset($row['durations'][$s['id']]) && $row['durations'][$s['id']] > (int)$s['sla_days']) {
            $totalOverdue++;
            break; 
        }
    }
}
$completionRate = $totalMasuk > 0 ? round(($selesaiCount / $totalMasuk) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Audit - <?= date('d-m-Y') ?></title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { 
            font-family: 'Times New Roman', serif; 
            line-height: 1.4; 
            color: #000; 
            background: #fff; 
            margin: 0 auto; 
            padding: 0; 
            font-size: 10pt; 
            width: 1080px; /* Precise A4 Landscape width for 96dpi */
        }
        
        /* Letterhead / Kop Surat */
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
        .kop-surat p { margin: 2px 0; font-size: 10pt; }

        /* Report Header */
        .report-header { text-align: center; margin-bottom: 30px; }
        .report-header h2 { margin: 0; font-size: 14pt; text-decoration: underline; text-transform: uppercase; }
        .report-header p { margin: 5px 0; font-size: 11pt; font-weight: bold; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-size: 10pt; font-weight: bold; text-transform: uppercase; text-align: center; }
        
        /* Matrix Specific */
        .matrix-table { font-size: 9pt; }
        .matrix-table th { font-size: 8pt; padding: 4px 2px; }
        .matrix-table td { text-align: center; }
        .matrix-table .klien-col { text-align: left; font-weight: bold; }
        
        /* Summary Boxes */
        .summary-row { display: flex; gap: 20px; margin-bottom: 20px; }
        .summary-box { flex: 1; border: 1px solid #000; padding: 10px; text-align: center; }
        .summary-box .label { font-size: 9pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #000; margin-bottom: 5px; padding-bottom: 5px; }
        .summary-box .value { font-size: 14pt; font-weight: bold; }

        /* Narrative */
        .narrative { border: 1px solid #000; padding: 15px; margin-bottom: 25px; text-align: justify; background: #fafafa; font-style: italic; }

        /* Status Colors for Print */
        .overdue { font-weight: bold; text-decoration: underline; }
        
        /* Signatures */
        .signature-block { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig-box { width: 200px; text-align: center; }
        .sig-space { height: 70px; }
        
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #333; padding: 10px; text-align: center; position: sticky; top: 0;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">🖨️ CETAK LAPORAN (PRINT)</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin-left: 10px;">❌ TUTUP</button>
    </div>

    <!-- 1.1 Letterhead -->
    <div class="kop-surat">
        <h1><?= OFFICE_NAME ?></h1>
        <p><?= OFFICE_ADDRESS ?> | Telp: <?= OFFICE_PHONE ?></p>
        <p>Email: <?= OFFICE_EMAIL ?></p>
    </div>

    <div class="report-header">
        <h2>Laporan Audit Operasional & Registrasi</h2>
        <p>Periode: <?= date('d M Y', strtotime($startDate)) ?> — <?= date('d M Y', strtotime($endDate)) ?></p>
    </div>

    <!-- 1.2 Ringkasan Cepat -->
    <div class="summary-row">
        <div class="summary-box">
            <div class="label">Registrasi Baru</div>
            <div class="value"><?= $summary['registrasi_baru'] ?></div>
        </div>
        <div class="summary-box">
            <div class="label">Registrasi Ditutup</div>
            <div class="value"><?= $summary['registrasi_ditutup'] ?></div>
        </div>
        <div class="summary-box">
            <div class="label">Masih Aktif</div>
            <div class="value"><?= $summary['masih_aktif'] ?></div>
        </div>
        <div class="summary-box">
            <div class="label">Total Terbayar</div>
            <div class="value">Rp <?= number_format($summary['total_terbayar'], 0, ',', '.') ?></div>
        </div>
    </div>

    <div style="display: flex; gap: 30px;">
        <!-- 1.3 Persebaran Layanan -->
        <div style="flex: 1;">
            <p style="font-weight: bold; margin-bottom: 5px;">1.3 Persebaran Layanan</p>
            <table>
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Jumlah</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distribution as $d): ?>
                    <tr>
                        <td><?= $d['nama_layanan'] ?></td>
                        <td style="text-align: center;"><?= $d['jumlah'] ?></td>
                        <td style="text-align: right;"><?= $d['persentase'] ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 1.5 Storytelling -->
        <div style="flex: 2;">
            <p style="font-weight: bold; margin-bottom: 5px;">1.5 Ringkasan Naratif</p>
            <div class="narrative">
                "Berdasarkan data operasional periode <?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?>, 
                kantor Notaris Sri Anah SH.M.Kn telah menerima sebanyak <?= $summary['registrasi_baru'] ?> registrasi baru. 
                Tingkat penyelesaian berkas (Closing Rate) tercatat sebesar <?= $completionRate ?>%. 
                Saat ini terdapat <?= $totalOverdue ?> berkas yang teridentifikasi mengalami keterlambatan pengerjaan (Overdue).
                Total realisasi pembayaran yang masuk pada periode ini adalah sebesar Rp <?= number_format($summary['total_terbayar'], 0, ',', '.') ?>."
            </div>
        </div>
    </div>

    <div style="page-break-before: always;"></div>

    <!-- 1.4 Matrix Timeline -->
    <p style="font-weight: bold; margin-bottom: 10px;">1.4 Matrix Timeline Registrasi Aktif (Hari per Tahapan)</p>
    <table class="matrix-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="text-align: left;">Nama Klien / Layanan</th>
                <?php foreach ($matrix['steps'] as $s): ?>
                <th><?= $s['id'] ?></th>
                <?php endforeach; ?>
                <th>Status SLA</th>
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
                <td><?= $idx + 1 ?></td>
                <td class="klien-col">
                    <?= $row['klien'] ?><br>
                    <span style="font-size: 8pt; font-weight: normal; color: #555;"><?= $row['layanan'] ?></span>
                </td>
                <?php foreach ($matrix['steps'] as $s): 
                    $days = $row['durations'][$s['id']] ?? null;
                    $isOver = ($days !== null && $days > (int)$s['sla_days']);
                ?>
                <td class="<?= $isOver ? 'overdue' : '' ?>">
                    <?= $days !== null ? $days : '-' ?>
                </td>
                <?php endforeach; ?>
                <td>
                    <?= $isRowOver ? 'OVERDUE' : 'NORMAL' ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p style="font-size: 8pt; margin-top: -10px;">
        * Angka menunjukkan jumlah hari yang dihabiskan di setiap tahapan.
        <strong>Legend Step ID:</strong>
        <?php foreach ($matrix['steps'] as $s): ?>
        <?= $s['id'] ?>:<?= $s['label'] ?>; 
        <?php endforeach; ?>
    </p>

    <!-- 1.6 Batal/Ditutup -->
    <p style="font-weight: bold; margin: 30px 0 10px 0;">1.6 Registrasi Selesai / Batal (Periode Ini)</p>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Klien</th>
                <th>Layanan</th>
                <th>Status Akhir</th>
                <th>Total Durasi</th>
                <th>Tgl Selesai</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matrix['selesai'] as $idx => $row): ?>
            <tr>
                <td style="text-align: center;"><?= $idx + 1 ?></td>
                <td><?= $row['klien'] ?></td>
                <td><?= $row['layanan'] ?></td>
                <td><?= strtoupper($row['status_label'] ?? 'SELESAI') ?></td>
                <td style="text-align: center;"><?= $row['total_days'] ?> Hari</td>
                <td style="text-align: center;"><?= $row['target'] ? date('d/m/Y', strtotime($row['target'])) : '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="signature-block">
        <div class="sig-box" style="margin-left: auto;">
            <p>Disetujui Oleh,</p>
            <div class="sig-space"></div>
            <p><strong><?= OFFICE_NAME ?></strong></p>
        </div>
    </div>

    <script>
        // Auto print hint
        console.log('Formal Audit Report Loaded. Ready to print.');
    </script>
</body>
</html>
