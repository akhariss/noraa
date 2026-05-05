<?php
/**
 * Public Tracking View - SECURE (Premium Hero Style)
 */
require_once VIEWS_PATH . '/company_profile/cms_helpers.php';
require_once APP_PATH . '/Core/Utils/helpers.php';

$brandName = cmsContent($homepageData ?? [], 'footer', 'brand', 'Notaris Sri Anah SH.M.Kn');
$footerPhone = cmsContent($homepageData ?? [], 'footer', 'phone', '6285747898811');

$pageTitle = 'Lacak Registrasi - ' . $brandName;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/variables.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/company-profile.css?v=<?= time() ?>">
    <!-- Style managed modularly via company-profile.css -->
    <!-- Style managed in tracking.css -->
    <script>const APP_URL = '<?= APP_URL ?>';</script>
</head>
<body>
    <?php require VIEWS_PATH . '/company_profile/partials/navbar.php'; ?>

    <main class="hero-tracking">
        <div class="tracking-content">
            <div class="tracking-header">
                <a href="<?= APP_URL ?>/" class="back-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:2px;">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Kembali
                </a>
                <h1>Lacak Registrasi</h1>
                <p>Pantau status dokumen Anda secara real-time dengan aman.</p>
            </div>

            <form id="trackingPageForm">
                <div class="tracking-input-group">
                    <input type="text" id="nomor_registrasi" name="nomor_registrasi" placeholder="Contoh: NP-20260224-0001" required autocomplete="off">
                    <button type="submit" class="btn-track">Mulai Lacak</button>
                </div>
            </form>

            <div id="verificationStep" class="verification-box" style="display: none; margin-top: 35px; text-align: left; animation: fadeIn 0.4s ease-out;">
                <div style="margin-bottom: 15px;">
                    <h3 style="margin: 0 0 5px; font-size: 18px; font-family: 'DM Sans', sans-serif; font-weight: 600; color: var(--primary-dark);">Verifikasi Kode Pengamanan</h3>
                    <p style="margin: 0; font-size: 14px; color: var(--text-light); line-height: 1.4;">Masukkan 4 digit terakhir nomor handphone klien.</p>
                </div>
                
                <form id="verificationForm" style="margin-top: 25px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <input type="hidden" id="verify_nomor_registrasi" name="registrasi_id">
                    <input type="text" id="phone_code" name="phone_code" maxlength="4" placeholder="••••" required 
                           style="flex: 1; padding: 15px; border-radius: 8px; font-size: 20px; text-align: center; letter-spacing: 8px; outline: none; transition: border-color 0.3s ease; border: 1px solid var(--border); color: var(--primary-dark); background: var(--white); min-width: 200px;">
                    <button type="submit" class="btn-track" style="padding: 15px 30px;">Verifikasi</button>
                </form>
            </div>

            <div id="trackingResult" class="tracking-result">
                <!-- Result Card injected here -->
            </div>
        </div>
    </main>

    <?php require VIEWS_PATH . '/company_profile/partials/footer.php'; ?>

    <script>
    document.getElementById('trackingPageForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        const originalText = btn.innerHTML;
        const resultDiv = document.getElementById('trackingResult');
        const verificationStep = document.getElementById('verificationStep');
        
        btn.disabled = true;
        btn.innerHTML = 'Mengecek...';
        resultDiv.innerHTML = '';
        verificationStep.style.display = 'none';
        
        try {
            const formData = new FormData(this);
            const response = await fetch(`${APP_URL}/lacak`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await response.json();
            
            btn.disabled = false;
            btn.innerHTML = originalText;
            
            if (data.success && data.data) {
                document.getElementById('verify_nomor_registrasi').value = data.data.registrasi_id;
                verificationStep.style.display = 'block';
                document.getElementById('phone_code').focus();
            } else {
                resultDiv.innerHTML = `<div style="margin-top: 30px; padding: 20px; background: rgba(255,0,0,0.1); border-radius: 12px; color: #ff8e8e;">${data.message || 'Dokumen tidak ditemukan.'}</div>`;
            }
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = originalText;
            resultDiv.innerHTML = `<div style="margin-top: 30px; padding: 20px; background: rgba(255,0,0,0.1); border-radius: 12px; color: #ff8e8e;">Gagal terhubung ke peladen. Silakan muat ulang halaman.</div>`;
            console.error(error);
        }
    });

    document.getElementById('verificationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        const resultDiv = document.getElementById('trackingResult');
        btn.disabled = true;
        btn.innerHTML = '...';

        try {
            const formData = new FormData(this);
            const response = await fetch(`${APP_URL}/verify_tracking`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await response.json();
            
            btn.disabled = false;
            btn.innerHTML = 'Verifikasi';
            
            if (data.success && data.data) {
                const item = data.data;
                const detailLink = `${APP_URL}/index.php?gate=lacak&action=detail&nomor_registrasi=${item.nomor_registrasi}&token=${encodeURIComponent(data.token)}`;
                
                const keteranganDisplay = (item.keterangan && item.keterangan !== '-' && item.keterangan !== '') 
                                        ? item.keterangan 
                                        : '-';
                                        
                const catatanDisplay = (item.catatan_internal && item.catatan_internal !== '-' && item.catatan_internal !== '') 
                                        ? item.catatan_internal 
                                        : 'Proses sedang berjalan';
                                        
                // Lunas Badge Logic
                const lunasBadge = item.pembayaran_status === 'LUNAS' 
                    ? `<span style="background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; letter-spacing: 0.5px;">LUNAS</span>`
                    : `<span style="background: #fff3e0; color: #e65100; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; letter-spacing: 0.5px;">BELUM LUNAS</span>`;

                const statusLabel = item.status_label || item.status || 'Proses';
                const statusBg = '#e3f2fd'; // fallback
                const statusColor = '#1976d2';

                resultDiv.innerHTML = `
                    <div style="margin-top: 25px; background: var(--white); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; animation: fadeIn 0.4s ease-out; text-align: left; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        
                        <!-- Header (Perfect Symmetry) -->
                        <div style="padding: 16px 20px; background: #fafafa; display: flex; justify-content: space-between; align-items: center;">
                            <!-- Kiri: Nomor & Badge -->
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 700; color: var(--text); letter-spacing: 0.5px; white-space: nowrap;">
                                    ${item.nomor_registrasi}
                                </div>
                                <div>
                                    <span style="background: var(--primary); color: var(--gold); padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase;">
                                        ${statusLabel}
                                    </span>
                                </div>
                            </div>
                            <!-- Kanan: Estimasi & Tanggal -->
                            <div style="display: flex; flex-direction: column; gap: 8px; text-align: right;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Estimasi Selesai</div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--text);">${item.estimasi_selesai || '-'}</div>
                            </div>
                        </div>
                        
                        <!-- Gold Line (Full Width) -->
                        <div style="height: 1px; background: var(--gold); opacity: 0.3;"></div>
                        
                        <!-- Body -->
                        <div style="padding: 16px 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <div style="font-size: 15px; font-weight: 700; color: var(--text);">${item.klien_nama || '-'}</div>
                                ${lunasBadge}
                            </div>
                            
                            <div style="margin-bottom: 12px; display: flex; flex-direction: column; gap: 4px;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Layanan</div>
                                <div style="font-size: 13px; font-weight: 700; color: var(--text);">${item.nama_layanan || '-'}</div>
                            </div>
                            
                            <div style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 4px;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Keterangan Tambahan</div>
                                <div style="font-size: 13px; color: var(--text-light); line-height: 1.5;">${keteranganDisplay}</div>
                            </div>
                            
                            <!-- Update Block -->
                            <div style="background: var(--cream); border-radius: 8px; padding: 14px; margin-bottom: 20px; border-left: 3px solid var(--gold);">
                                <div style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px;">Pembaruan Terakhir</div>
                                <div style="font-size: 13px; font-weight: 700; color: var(--primary); margin-bottom: 8px;">${item.updated_at || '-'}</div>
                                <div style="font-size: 13px; color: var(--text); line-height: 1.5; font-style: italic;">"${catatanDisplay}"</div>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="${detailLink}" style="display: flex; justify-content: center; align-items: center; width: 100%; color: var(--primary); font-weight: 700; font-size: 12px; border: 1px solid var(--primary); padding: 12px; border-radius: 8px; text-decoration: none; background: transparent; transition: all 0.2s;">
                                Lihat Riwayat Lengkap &rarr;
                            </a>
                        </div>
                    </div>
                `;
                document.getElementById('verificationStep').style.display = 'none';
            } else {
                resultDiv.innerHTML = `<div style="margin-top: 20px; padding: 20px; background: rgba(255,0,0,0.1); border-radius: 12px; color: #ff8e8e;">${data.message || 'Verifikasi gagal.'}</div>`;
            }
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = 'Verifikasi';
            resultDiv.innerHTML = `<div style="margin-top: 20px; padding: 20px; background: rgba(255,0,0,0.1); border-radius: 12px; color: #ff8e8e;">Gagal terhubung ke peladen.</div>`;
            console.error(error);
        }
    });
    </script>
</body>
</html>
