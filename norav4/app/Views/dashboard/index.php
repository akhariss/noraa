<?php
/**
 * Dashboard View - Nora V4
 * War Room v5.0 - AA Command Center
 */
?>
<style>
    .hero-banner {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        padding: 40px;
        border-radius: 15px;
        margin-bottom: 25px;
        text-align: center;
        border: 1px solid rgba(156, 124, 56, 0.2);
        color: #fff;
    }
    .hero-banner h2 { font-family: 'Cormorant Garamond', serif; font-size: 32px; margin-bottom: 10px; }
    
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
    .pilar-card:hover { transform: translateY(-5px); border-color: var(--gold); }
    .pilar-card .val { display: block; font-size: 36px; font-weight: 800; color: var(--primary); }
    .pilar-card .lbl { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-top: 10px; letter-spacing: 1.5px; }

    .vault-container { background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 25px; }
    .vault-header { background: #fcfbf8; padding: 18px 25px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .vault-header h3 { font-size: 13px; font-weight: 800; color: var(--primary); margin: 0; text-transform: uppercase; }
    
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #f9f7f0; text-align: left; font-size: 11px; text-transform: uppercase; font-weight: 800; padding: 12px 20px; color: var(--text-muted); }
    .data-table td { padding: 15px 20px; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
</style>

<div class="war-room">
    <div class="hero-banner">
        <h2>Selamat Datang, Admin</h2>
        <p>Pusat kendali operasional Notaris Sri Anah SH.M.Kn - Nora V4 Professional</p>
    </div>

    <div class="pilar-group">
        <div class="pilar-card"><span class="val"><?= $stats['total'] ?></span><span class="lbl">Total File</span></div>
        <div class="pilar-card"><span class="val" style="color:var(--gold);"><?= $stats['aktif'] ?></span><span class="lbl">Proses Aktif</span></div>
        <div class="pilar-card"><span class="val" style="color:#2e7d32;"><?= $stats['selesai'] ?></span><span class="lbl">Review Boss</span></div>
        <div class="pilar-card"><span class="val" style="color:#b71c1c;"><?= $stats['kendala_aktif'] ?></span><span class="lbl">Kendala</span></div>
    </div>

    <div class="vault-container">
        <div class="vault-header">
            <h3>Monitoring Real-time</h3>
            <a href="<?= APP_URL ?>/registrasi" class="btn-text">Lihat Semua →</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No Registrasi</th>
                        <th>Klien</th>
                        <th>Layanan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items ?? [] as $row): ?>
                    <tr>
                        <td style="font-weight: 700;"><?= $row['nomor_registrasi'] ?></td>
                        <td><?= htmlspecialchars($row['klien_nama']) ?></td>
                        <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                        <td>
                            <span class="badge" style="background: #e3f2fd; color: #1976d2; padding: 4px 10px; border-radius: 4px; font-size: 11px;">
                                <?= $row['status_label'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/registrasi/detail/<?= $row['id'] ?>" class="btn-sm btn-outline">Buka</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #999;">Belum ada data registrasi aktif.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
