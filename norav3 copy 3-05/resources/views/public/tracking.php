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
    
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/company-profile.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/tracking.css?v=<?= time() ?>">
    <style>
        .hero-tracking {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 20px 40px;
            background: var(--cream);
            position: relative;
            overflow: hidden;
        }
        /* Geometric accent */
        .hero-tracking::after {
            content: '';
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(156, 124, 56, 0.08) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            z-index: 1;
        }
        .tracking-content {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        .tracking-header h1 {
            font-size: 42px;
            margin-bottom: 5px;
            color: var(--primary-dark);
            letter-spacing: -1px;
        }
        .tracking-header p {
            font-size: 16px;
            color: var(--text-light);
            margin-bottom: 25px; /* Reduced from 30px */
        }
        .tracking-input-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .tracking-input-group input {
            flex: 1;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            padding: 16px 20px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .tracking-input-group input:focus {
            border-color: var(--primary);
        }
        .tracking-input-group input {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--text);
            padding: 16px 20px;
            font-size: 18px;
            outline: none;
            min-width: 0; /* allows input to shrink in flex container */
        }
        .tracking-input-group input::placeholder {
            color: rgba(0,0,0,0.3);
            font-size: 16px;
        }
        .btn-track {
            background: var(--gold);
            color: var(--primary-dark);
            border: none;
            padding: 15px 35px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .btn-track:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
        }
        
        .verification-box {
            margin-top: 35px;
            text-align: left;
            animation: fadeIn 0.4s ease-out;
        }
        #verificationStep h3 {
            color: var(--primary-dark) !important;
        }
        #verificationStep p {
            color: var(--text-light) !important;
        }
        #verificationStep input {
            color: var(--primary-dark) !important;
            border: 1px solid var(--border) !important;
            background: var(--white) !important;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .result-card {
            margin-top: 40px;
            background: var(--white);
            color: var(--text);
            border-radius: 24px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            text-align: left;
            animation: fadeIn 0.5s ease-out;
        }
        
        .result-card h3 {
            color: var(--primary);
        }

        .verification-form-container {
            margin-top: 25px; 
            display: flex; 
            gap: 12px;
        }

        @media (max-width: 600px) {
            .hero-tracking { padding: 60px 20px 30px; }
            .tracking-header h1 { font-size: 28px; margin-bottom: 5px; }
            .tracking-header p { font-size: 14px; margin-bottom: 25px; }
            .tracking-input-group { flex-direction: column; gap: 10px; }
            .tracking-input-group input { width: 100%; text-align: center; }
            .btn-track { width: 100%; }
            
            .verification-box { margin-top: 25px; }
            .verification-form-container { flex-direction: column; gap: 8px; }
            #phone_code { font-size: 18px !important; letter-spacing: 5px !important; padding: 14px !important; }
        }
    </style>
</head>
<body>
    <?php require VIEWS_PATH . '/company_profile/partials/navbar.php'; ?>

    <main class="hero-tracking">
        <div class="tracking-content">
            <div class="tracking-header">
                <a href="<?= APP_URL ?>/index.php" class="back-link" style="color: var(--primary); font-weight: 600; margin-bottom: 5px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">
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

            <div id="verificationStep" class="verification-box" style="display: none;">
                <div style="margin-bottom: 15px;">
                    <h3 style="margin: 0 0 5px; font-size: 18px; font-family: 'DM Sans', sans-serif; font-weight: 600; color: var(--primary-dark);">Verifikasi Kode Pengamanan</h3>
                    <p style="margin: 0; font-size: 14px; color: var(--text-light); line-height: 1.4;">Masukkan 4 digit terakhir nomor handphone klien.</p>
                </div>
                
                <form id="verificationForm" class="verification-form-container">
                    <input type="hidden" id="registrasi_id" name="registrasi_id">
                    <input type="text" id="phone_code" name="phone_code" maxlength="4" placeholder="••••" required 
                           style="flex: 1; padding: 15px; border-radius: 8px; font-size: 20px; text-align: center; letter-spacing: 8px; outline: none; transition: border-color 0.3s ease;">
                    <button type="submit" class="btn-track" style="padding: 15px 30px;">Verifikasi</button>
                </form>
            </div>

            <div id="trackingResult"></div>
        </div>
    </main>

    <?php require VIEWS_PATH . '/company_profile/partials/footer.php'; ?>

    <script>
    document.getElementById('trackingPageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        const originalText = btn.innerHTML;
        const resultDiv = document.getElementById('trackingResult');
        const verificationStep = document.getElementById('verificationStep');
        
        btn.disabled = true;
        btn.innerHTML = 'Mengecek...';
        resultDiv.innerHTML = '';
        verificationStep.style.display = 'none';
        
        fetch(APP_URL + '/index.php?gate=lacak', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if (data.success) {
                document.getElementById('registrasi_id').value = data.data.registrasi_id;
                verificationStep.style.display = 'block';
            } else {
                resultDiv.innerHTML = `<div style="margin-top: 30px; padding: 20px; background: rgba(255,0,0,0.1); border-radius: 12px; color: #ff8e8e;">${data.message}</div>`;
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            resultDiv.innerHTML = `<div style="margin-top: 30px; padding: 20px; background: rgba(255,0,0,0.1); border-radius: 12px; color: #ff8e8e;">Gagal terhubung ke peladen. Silakan muat ulang halaman.</div>`;
            console.error(error);
        });
    });

    document.getElementById('verificationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        const resultDiv = document.getElementById('trackingResult');
        btn.disabled = true;
        btn.innerHTML = '...';

        fetch(APP_URL + '/index.php?gate=verify_tracking', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Cek';
            if (data.success) {
                const item = data.data;
                const detailLink = `${APP_URL}/index.php?gate=detail&token=${encodeURIComponent(item.token)}`;
                
                const keteranganDisplay = (item.keterangan && item.keterangan !== '-' && item.keterangan !== '') 
                                        ? item.keterangan 
                                        : '-';
                                        
                const catatanDisplay = (item.catatan_status && item.catatan_status !== '-' && item.catatan_status !== '') 
                                        ? item.catatan_status 
                                        : 'Proses sedang berjalan';
                                        
                // Lunas Badge Logic
                const lunasBadge = item.is_lunas 
                    ? `<span style="background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; letter-spacing: 0.5px;">LUNAS</span>`
                    : `<span style="background: #fff3e0; color: #e65100; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; letter-spacing: 0.5px;">BELUM LUNAS</span>`;

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
                                    <span style="background: ${item.status_style.bg}; color: ${item.status_style.color}; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase;">
                                        ${item.status_label}
                                    </span>
                                </div>
                            </div>
                            <!-- Kanan: Estimasi & Tanggal -->
                            <div style="display: flex; flex-direction: column; gap: 8px; text-align: right;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Estimasi Selesai</div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--text);">${item.estimasi_selesai}</div>
                            </div>
                        </div>
                        
                        <!-- Gold Line (Full Width) -->
                        <div style="height: 1px; background: var(--gold); opacity: 0.3;"></div>
                        
                        <!-- Body -->
                        <div style="padding: 16px 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <div style="font-size: 15px; font-weight: 700; color: var(--text);">${item.klien_nama}</div>
                                ${lunasBadge}
                            </div>
                            
                            <div style="margin-bottom: 12px; display: flex; flex-direction: column; gap: 4px;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Layanan</div>
                                <div style="font-size: 13px; font-weight: 700; color: var(--text);">${item.layanan}</div>
                            </div>
                            
                            <div style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 4px;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Keterangan Tambahan</div>
                                <div style="font-size: 13px; color: var(--text-light); line-height: 1.5;">${keteranganDisplay}</div>
                            </div>
                            
                            <!-- Update Block -->
                            <div style="background: var(--cream); border-radius: 8px; padding: 14px; margin-bottom: 20px; border-left: 3px solid var(--gold);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                                    <div style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Pembaruan Terakhir</div>
                                    
                                    ${item.kendala_flag 
                                        ? `<span style="background: #fff5f5; color: #e53e3e; border: 1px solid #feb2b2; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 800; display: inline-flex; align-items: center; gap: 3px;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                            BERMASALAH</span>`
                                        : `<span style="background: #f0fff4; color: #38a169; border: 1px solid #9ae6b4; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 800; display: inline-flex; align-items: center; gap: 3px;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            LANCAR</span>`
                                    }
                                </div>
                                <div style="font-size: 13px; font-weight: 700; color: var(--primary); margin-bottom: 8px;">${item.updated_at} WIB</div>
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
                resultDiv.innerHTML = `<div style="margin-top: 20px; color: #ff8e8e;">${data.message}</div>`;
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = 'Cek';
            resultDiv.innerHTML = `<div style="margin-top: 20px; color: #ff8e8e;">Gagal mengirim data. Silakan muat ulang halaman.</div>`;
            console.error(error);
        });
    });
    </script>
</body>
</html>
