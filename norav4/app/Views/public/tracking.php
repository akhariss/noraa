<?php
/**
 * Public Tracking Partial - Nora V4
 * Mirroring V3 functionality and aesthetics
 */
$navPhone = cmsContent($homepageData, 'footer', 'phone', '6285747898811');
?>
<section class="section hero-tracking" id="tracking-page">
    <div class="container">
        <div class="tracking-content">
            <div class="tracking-header reveal">
                <a href="<?= APP_URL ?>/" class="back-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Kembali ke Beranda
                </a>
                <h1 class="section-title">Lacak Registrasi</h1>
                <p>Pantau status dokumen Anda secara real-time dengan aman.</p>
            </div>

            <div class="tracking-card reveal">
                <form id="trackingPageForm">
                    <div class="tracking-input-group">
                        <div class="input-with-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="nomor_registrasi" name="nomor_registrasi" placeholder="Contoh: NP-20260224-0001" required autocomplete="off">
                        </div>
                        <button type="submit" class="btn-track">Mulai Lacak</button>
                    </div>
                </form>

                <div id="verificationStep" class="verification-box" style="display: none;">
                    <div class="verification-header">
                        <h3>Verifikasi Kode Pengamanan</h3>
                        <p>Masukkan 4 digit terakhir nomor handphone klien.</p>
                    </div>
                    
                    <form id="verificationForm" class="verification-form-container">
                        <input type="hidden" id="registrasi_id" name="registrasi_id">
                        <div class="pin-input-wrap">
                            <input type="text" id="phone_code" name="phone_code" maxlength="4" placeholder="••••" required>
                        </div>
                        <button type="submit" class="btn-track">Verifikasi</button>
                    </form>
                </div>

                <div id="trackingResult"></div>
            </div>
        </div>
    </div>
</section>

<style>
/* Custom styles for Tracking Page */
.hero-tracking {
    padding: 180px 0 100px;
    background: var(--cream);
    min-height: 80vh;
}

.tracking-content {
    max-width: 800px;
    margin: 0 auto;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary);
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    transition: var(--transition-base);
}

.back-link:hover {
    color: var(--gold);
    transform: translateX(-5px);
}

.tracking-card {
    background: var(--white);
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.05);
    border: 1px solid var(--border);
}

.tracking-input-group {
    display: flex;
    gap: 15px;
}

.input-with-icon {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.input-with-icon svg {
    position: absolute;
    left: 20px;
    color: var(--text-muted);
}

.input-with-icon input {
    width: 100%;
    padding: 16px 20px 16px 55px;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    font-size: 16px;
    font-family: 'DM Sans', sans-serif;
    outline: none;
    transition: var(--transition-base);
}

.input-with-icon input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(27, 58, 75, 0.05);
}

.btn-track {
    padding: 16px 35px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: var(--transition-base);
}

.btn-track:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(27, 58, 75, 0.15);
}

/* Verification Styling */
.verification-box {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px dashed var(--border);
}

.verification-header h3 {
    font-size: 20px;
    color: var(--primary);
    margin-bottom: 5px;
}

.verification-header p {
    font-size: 14px;
    color: var(--text-light);
}

.verification-form-container {
    margin-top: 25px;
    display: flex;
    gap: 15px;
}

.pin-input-wrap {
    flex: 1;
}

.pin-input-wrap input {
    width: 100%;
    padding: 15px;
    border-radius: 12px;
    border: 1.5px solid var(--border);
    font-size: 24px;
    text-align: center;
    letter-spacing: 12px;
    font-weight: 700;
    outline: none;
}

@media (max-width: 768px) {
    .hero-tracking {
        padding: 100px 0 50px;
    }
    .tracking-card {
        padding: 25px 20px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .tracking-input-group, .verification-form-container {
        flex-direction: column;
        gap: 12px;
    }
    .input-with-icon input {
        padding: 12px 15px 12px 45px;
        font-size: 14px;
    }
    .input-with-icon svg {
        left: 15px;
        width: 18px;
        height: 18px;
    }
    .btn-track {
        width: 100%;
        padding: 12px;
        font-size: 14px;
    }
    .section-title {
        font-size: 22px;
        margin-bottom: 5px;
    }
    .tracking-header p {
        font-size: 12px;
    }
    .pin-input-wrap input {
        font-size: 18px;
        padding: 10px;
        letter-spacing: 8px;
    }
    .verification-box {
        margin-top: 25px;
        padding-top: 20px;
    }
    .verification-header h3 {
        font-size: 18px;
    }
}
</style>

<script>
// Tracking Logic - Nora V4
document.addEventListener('DOMContentLoaded', function() {
    const trackingForm = document.getElementById('trackingPageForm');
    if (trackingForm) {
        trackingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const originalText = btn.innerHTML;
            const resultDiv = document.getElementById('trackingResult');
            const verificationStep = document.getElementById('verificationStep');
            
            btn.disabled = true;
            btn.innerHTML = 'Mengecek...';
            resultDiv.innerHTML = '';
            verificationStep.style.display = 'none';
            
            // Use query gate pattern for XAMPP stability
            const trackingUrl = APP_URL + '/index.php?gate=lacak';
            
            fetch(trackingUrl, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    if (data.success) {
                        document.getElementById('registrasi_id').value = data.data.registrasi_id;
                        verificationStep.style.display = 'block';
                        verificationStep.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        resultDiv.innerHTML = `<div class="alert-box-v4 error">${data.message}</div>`;
                    }
                } catch (err) {
                    throw new Error("Server Error (HTML returned instead of JSON).");
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                resultDiv.innerHTML = `<div class="alert-box-v4 error">${error.message}</div>`;
            });
        });
    }

    const verificationForm = document.getElementById('verificationForm');
    if (verificationForm) {
        verificationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const resultDiv = document.getElementById('trackingResult');
            btn.disabled = true;
            btn.innerHTML = 'Verifikasi...';

            const verifyUrl = APP_URL + '/index.php?gate=verify_tracking';

            fetch(verifyUrl, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    btn.disabled = false;
                    btn.innerHTML = 'Verifikasi';
                    if (data.success) {
                        renderResult(data.data);
                        document.getElementById('verificationStep').style.display = 'none';
                    } else {
                        resultDiv.innerHTML = `<div class="alert-box-v4 error">${data.message}</div>`;
                    }
                } catch (err) {
                    throw new Error("Gagal verifikasi. Respon server tidak valid. <br><small>Isi: " + text.substring(0, 100).replace(/</g, '&lt;') + "...</small>");
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = 'Verifikasi';
                resultDiv.innerHTML = `<div class="alert-box-v4 error">${error.message}</div>`;
            });
        });
    }

    function renderResult(item) {
        const resultDiv = document.getElementById('trackingResult');
        const detailLink = `${APP_URL}/index.php?gate=detail&token=${encodeURIComponent(item.token)}`;
        
        const lunasBadge = item.is_lunas 
            ? `<span class="badge lunas">LUNAS</span>`
            : `<span class="badge belum-lunas">BELUM LUNAS</span>`;

        resultDiv.innerHTML = `
            <div class="result-card-v4">
                <div class="result-top">
                    <div class="reg-info">
                        <span class="label">Nomor Registrasi</span>
                        <div class="val-big">${item.nomor_registrasi}</div>
                    </div>
                    <div class="status-info">
                        <span class="status-badge" style="background: ${item.status_style.bg}; color: ${item.status_style.color};">
                            ${item.status_label}
                        </span>
                        <div class="lunas-wrap">${lunasBadge}</div>
                    </div>
                </div>

                <div class="result-grid">
                    <div class="grid-item">
                        <span class="label">Klien</span>
                        <div class="val">${item.klien_nama}</div>
                    </div>
                    <div class="grid-item">
                        <span class="label">Layanan</span>
                        <div class="val">${item.layanan}</div>
                    </div>
                    <div class="grid-item">
                        <span class="label">Estimasi Selesai</span>
                        <div class="val highlight">${item.estimasi_selesai}</div>
                    </div>
                </div>

                ${item.keterangan ? `
                <div class="keterangan-box">
                    <span class="label">Keterangan Berkas</span>
                    <div class="val-text">${item.keterangan}</div>
                </div>
                ` : ''}

                ${item.latest_log ? `
                <div class="latest-log-box">
                    <div class="h-meta">
                        <span class="label">PEMBARUAN TERAKHIR</span>
                        <span class="indicator ${item.kendala_flag ? 'error' : 'success'}">
                            ${item.kendala_flag ? '⚠️ BERMASALAH' : '✓ LANCAR'}
                        </span>
                    </div>
                    <div class="h-date">${item.latest_log.date} WIB</div>
                    <div class="h-note">"${item.latest_log.note || 'Berjalan lancar'}"</div>
                </div>
                ` : ''}

                <a href="${detailLink}" class="btn-track full-btn" style="margin-top: 25px;">Lihat Riwayat & Timeline &rarr;</a>
            </div>
        `;
    }
});
</script>

<style>
.alert-box-v4 {
    margin-top: 30px;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    font-weight: 500;
}
.alert-box-v4.error {
    background: #fff5f5;
    border: 1px solid #feb2b2;
    color: #c53030;
}
.result-card-v4 {
    margin-top: 35px;
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 15px 45px rgba(0,0,0,0.03);
}
.result-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 30px;
    border-bottom: 3px solid var(--gold);
    background: #fdfdfd;
}
.result-card-v4 .label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    margin-bottom: 5px;
}
.val-big {
    font-size: 16px;
    font-weight: 700;
    color: var(--primary);
}
.status-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid rgba(0,0,0,0.05);
}
.badge {
    padding: 3px 8px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 700;
    border: 1px solid transparent;
}
.badge.lunas { background: #e8f5e9; color: #2e7d32; border-color: #c8e6c9; }
.badge.belum-lunas { background: #fff3e0; color: #e65100; border-color: #ffe0b2; }
.result-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
    padding: 30px;
    background: white;
}
.keterangan-box {
    padding: 0 30px 25px;
}
.keterangan-box .val-text {
    font-size: 14px;
    color: var(--text);
    line-height: 1.6;
    margin-top: 4px;
}
.latest-log-box {
    background: #fafafa;
    padding: 25px 30px;
    border-top: 1px solid #eee;
}
.latest-log-box .h-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.latest-log-box .h-date {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 6px;
}
.latest-log-box .h-note {
    font-size: 13.5px;
    font-style: italic;
    color: var(--text-light);
}
.grid-item .val {
    font-weight: 700;
    color: var(--text);
    font-size: 13px;
}
.grid-item .val.highlight { color: var(--primary); }
.full-btn {
    display: block;
    text-align: center;
    text-decoration: none;
    font-size: 14px;
    padding: 12px;
}
@media (max-width: 480px) {
    .hero-tracking { padding: 90px 0 20px; }
    .hero-tracking h1 { font-size: 24px; }
    .hero-tracking p { font-size: 13px; margin-top: 5px; }
    
    .result-card-v4 { padding: 0; border-radius: 12px; }
    .result-top { padding: 12px 15px; border-bottom-width: 2px; }
    .val-big { font-size: 14px; }
    
    .result-grid { 
        grid-template-columns: 1fr; 
        gap: 6px; 
        padding: 10px 12px; 
        margin-bottom: 0;
        background: #fff;
    }
    .grid-item .val { font-size: 10.5px; }
    .info-item .label, .label { font-size: 7.5px; margin-bottom: 1px; }

    .keterangan-box { 
        padding: 0 12px 10px;
    }
    .keterangan-box .val-text { font-size: 10.5px; line-height: 1.3; }

    .latest-log-box { 
        padding: 10px 12px; 
        margin: 0;
        border-top: 1px solid #eee;
        border-left: none;
        background: #fdfdfd;
    }
    .latest-log-box .h-date { font-size: 10.5px; margin-bottom: 3px; }
    .latest-log-box .h-note { font-size: 10px; line-height: 1.3; }
    
    .full-btn { 
        font-size: 11.5px; 
        padding: 10px; 
        margin-top: 10px !important; 
        border-radius: 10px;
    }
    .indicator { font-size: 9px; }
    .status-badge { padding: 3px 8px; font-size: 9px; }
    .badge { padding: 2px 5px; font-size: 8px; }
}
</style>
