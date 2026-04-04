<?php
/**
 * Public Tracking View - SECURE
 * Search by nomor registrasi + 4-digit phone verification
 */
$pageTitle = 'Lacak Registrasi - ' . APP_NAME;
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
    
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/tracking.css">
</head>
<body>
    <header style="background: white; border-bottom: 1px solid #e2e8f0; padding: 15px 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
            <a href="<?= APP_URL ?>/index.php" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: #1B3A4B; font-weight: 700; font-size: 18px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; color: #9C7C38;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Notaris <span style="color: #9C7C38;">Sri Anah SH.M.Kn</span></span>
            </a>
            <nav style="display: flex; gap: 20px;">
                <a href="<?= APP_URL ?>/index.php" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">Beranda</a>
                <a href="<?= APP_URL ?>/index.php#masalah" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">FAQ</a>
                <a href="<?= APP_URL ?>/index.php?gate=lacak" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">Lacak</a>
                <a href="<?= APP_URL ?>/index.php#tentang" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">Tentang</a>
                <a href="https://wa.me/6285747898811" target="_blank" style="text-decoration: none; color: #1a1a1a; font-weight: 500; font-size: 13px;">Hubungi Kami</a>
            </nav>
        </div>
    </header>

    <div class="tracking-page">
        <div class="tracking-header">
            <a href="<?= APP_URL ?>/index.php?gate=home" class="back-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Homepage
            </a>
            <h1>Lacak Status Registrasi</h1>
            <p>Masukkan nomor registrasi Anda untuk melihat status</p>
        </div>

        <!-- Step 1: Search by nomor registrasi -->
        <div class="tracking-search">
            <form id="trackingForm" class="tracking-form">
                <div class="tracking-input-group">
                    <input type="text" id="nomor_registrasi" name="nomor_registrasi" placeholder="Contoh: NP-20260224-0001" required>
                    <button type="submit" class="btn-track">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        Lacak Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 2: Verification (hidden initially) -->
        <div id="verificationStep" class="verification-box" style="display: none;">
            <div class="verification-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <h3>Verifikasi Keamanan</h3>
            </div>
            <p>Masukkan 4 digit terakhir nomor HP Anda untuk melihat detail registrasi:</p>
            <form id="verificationForm" class="verification-form">
                <input type="hidden" id="registrasi_id" name="registrasi_id">
                <div class="verification-input-group">
                    <input type="text" id="phone_code" name="phone_code" maxlength="4" placeholder="****" pattern="[0-9]{4}" required>
                    <button type="submit" class="btn-verify">Verifikasi</button>
                </div>
                <small style="color: var(--text-muted);">Contoh: Jika HP Anda 081234567<strong>8901</strong>, masukkan: <strong>8901</strong></small>
            </form>
        </div>

        <!-- Result (hidden initially) -->
        <div id="trackingResult" class="tracking-result"></div>
    </div>

    <script>
    const APP_URL = '<?= APP_URL ?>';

    // Step 1: Search by nomor registrasi
    document.getElementById('trackingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const resultDiv = document.getElementById('trackingResult');
        const verificationStep = document.getElementById('verificationStep');
        
        resultDiv.innerHTML = '<div class="loading">Memeriksa nomor registrasi...</div>';
        verificationStep.style.display = 'none';
        
        fetch(APP_URL + '/index.php?gate=lacak', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.innerHTML = '';
            
            if (data.success && data.data) {
                // Show verification step
                document.getElementById('registrasi_id').value = data.data.registrasi_id;
                verificationStep.style.display = 'block';
                resultDiv.innerHTML = '';
            } else {
                resultDiv.innerHTML = '<div class="result-empty">' + (data.message || 'Nomor registrasi tidak ditemukan') + '</div>';
            }
        })
        .catch(error => {
            resultDiv.innerHTML = '<div class="result-error">Terjadi kesalahan. Silakan coba lagi.</div>';
        });
    });

    // Step 2: Verify 4-digit code
    document.getElementById('verificationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const resultDiv = document.getElementById('trackingResult');

        resultDiv.innerHTML = '<div class="loading">Memverifikasi...</div>';

        fetch(APP_URL + '/index.php?gate=verify_tracking', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Verification response:', data); // Debug log
            
            if (data.success) {
                // Show full details
                const item = data.data;
                // Use token for secure link instead of raw ID
                const detailLink = `${APP_URL}/index.php?gate=detail&token=${encodeURIComponent(item.token)}`;
                
                resultDiv.innerHTML = `
                    <div class="result-cards">
                        <div class="result-card">
                            <div class="result-header">
                                <span class="result-nomor">${item.nomor_registrasi}</span>
                                <span class="badge badge-${item.batal_flag ? 'batal' : item.status}">${item.status_label}</span>
                            </div>
                            <div class="result-info">
                                <div class="info-row">
                                    <span class="label">Klien:</span>
                                    <span class="value">${item.klien_nama}</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Layanan:</span>
                                    <span class="value">${item.layanan}</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Tanggal:</span>
                                    <span class="value">${item.created_at}</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Update:</span>
                                    <span class="value">${item.updated_at}</span>
                                </div>
                            </div>
                            <a href="${detailLink}" class="btn-detail">Lihat Detail Lengkap</a>
                        </div>
                    </div>
                `;
                document.getElementById('verificationStep').style.display = 'none';
            } else {
                resultDiv.innerHTML = '<div class="result-error">' + (data.message || 'Verifikasi gagal') + '</div>';
            }
        })
        .catch(error => {
            console.error('Verification error:', error);
            resultDiv.innerHTML = '<div class="result-error">Terjadi kesalahan. Silakan coba lagi.<br><small>' + error.message + '</small></div>';
        });
    });

    // Only allow numbers in phone code
    document.getElementById('phone_code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    </script>
    <footer style="background: #0a192f; color: #fff; padding: 40px 0 20px; font-size: 14px; text-align: center;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; color: rgba(255,255,255,0.5);">
                &copy; <?= date('Y') ?> Notaris Sri Anah SH.M.Kn. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
