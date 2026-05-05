<?php
/**
 * Laporan Selection Hub - Natural Luxe Hub Edition
 * Sync with App Settings UI for visual consistency.
 */
$pageTitle = 'Pusat Analitik';
$activePage = 'laporan';
require VIEWS_PATH . '/templates/header.php';
?>

<div class="cms-grid-wrapper">
    <!-- Hero Section -->
    <div class="cms-hero">
        <div class="container">
            <div class="cms-hero-content">
                <span class="cms-badge">Pusat Analitik & Audit</span>
                <p class="cms-hero-subtitle">Pantau performa operasional, kesehatan keuangan, dan produktivitas tim secara real-time</p>
            </div>
        </div>
    </div>

    <!-- Grid Cards -->
    <div class="container">
        <div class="cms-grid">
            <!-- Laporan Registrasi & Matrix -->
            <a href="?gate=laporan_registrasi" class="cms-card">
                <div class="card-icon" style="background: linear-gradient(135deg, #4f46e5, #3730a3);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <h3>Laporan Audit Proses</h3>
                <p>Monitoring antrian berkas, cek durasi pengerjaan per tahap (Matrix), dan deteksi hambatan (Bottleneck).</p>
                <div class="card-tags">
                    <span>Audit</span>
                    <span>Matrix</span>
                    <span>SLA Monitoring</span>
                </div>
            </a>

            <!-- Laporan Keuangan -->
            <a href="?gate=laporan_keuangan" class="cms-card">
                <div class="card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h3>Laporan Keuangan</h3>
                <p>Rekapitulasi tagihan baru, total uang masuk, piutang pengerjaan berjalan, dan audit transaksi harian.</p>
                <div class="card-tags">
                    <span>Finance</span>
                    <span>Payments</span>
                    <span>Receivables</span>
                </div>
            </a>

            <!-- Laporan Aktivitas -->
            <a href="?gate=laporan_aktivitas" class="cms-card">
                <div class="card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    </svg>
                </div>
                <h3>Produktivitas Tim</h3>
                <p>Ranking kinerja staff berdasarkan jumlah berkas yang dibuat, diupdate, dan pembayaran yang ditangani.</p>
                <div class="card-tags">
                    <span>Performance</span>
                    <span>User Rank</span>
                    <span>Staff Kpi</span>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.cms-grid-wrapper { min-height: 100vh; background: var(--cream); padding-bottom: 60px; }
.cms-hero { background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%); padding: 40px 0; text-align: center; }
.cms-badge { display: inline-block; background: rgba(156, 124, 56, 0.15); color: var(--gold-light); padding: 8px 16px; border-radius: 4px; font-size: 11px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; border: 1px solid rgba(156, 124, 56, 0.3); }
.cms-hero-subtitle { font-size: 15px; color: rgba(255, 255, 255, 0.85); margin: 0; font-weight: 500; }
.cms-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-top: 30px; }
.cms-card { background: linear-gradient(135deg, var(--cream), var(--white) 100%); padding: 30px; border-radius: 18px; text-decoration: none; border: 2px solid rgba(156, 124, 56, 0.2); transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); display: flex; flex-direction: column; height: 100%; }
.cms-card:hover { transform: translateY(-12px); box-shadow: 0 25px 60px rgba(27, 58, 75, 0.15); border-color: rgba(156, 124, 56, 0.4); }
.card-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 6px 16px rgba(0,0,0,0.12); }
.card-icon svg { width: 28px; height: 28px; color: white; }
.cms-card h3 { font-size: 20px; color: var(--primary); margin-bottom: 12px; font-weight: 700; font-family: 'Cormorant Garamond', serif; }
.cms-card p { font-size: 14px; color: var(--text-light); margin-bottom: 20px; line-height: 1.6; flex-grow: 1; }
.card-tags { display: flex; flex-wrap: wrap; gap: 8px; }
.card-tags span { background: var(--white); color: var(--primary); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid rgba(156, 124, 56, 0.2); }
</style>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
