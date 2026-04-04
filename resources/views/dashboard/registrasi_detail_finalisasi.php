<?php
/**
 * Detail Registrasi View (For Finalisasi Page)
 * Michelin Elite Refactor v5.53 - Mirror of registrasi_detail.php
 */

use App\Domain\Entities\WorkflowStep;

// SK-07: Fetch Real Note Templates (Ditingkatkan di v5.88)
$dbRaw = \App\Adapters\Database::select(
    "SELECT workflow_step_id, template_body FROM note_templates WHERE workflow_step_id IN (11, 14)"
);
$realTpls = []; 
foreach($dbRaw as $r) {
    $realTpls[(int)$r['workflow_step_id']] = addslashes(trim($r['template_body']));
}
$tplPerbaikan = !empty($realTpls[11]) ? $realTpls[11] : 'Terdapat perbaikan administrasi.';
$tplDitutup   = !empty($realTpls[14]) ? $realTpls[14] : 'Perkara telah resmi ditutup.';

$currentUser = getCurrentUser();
$pageTitle = 'Detail Finalisasi - ' . ($registrasi['nomor_registrasi'] ?? '-');
$activePage = 'finalisasi';
$pageScript = 'registrasi-detail.js?v=' . time();

require VIEWS_PATH . '/templates/header.php';

// Define UI dynamic variables
$role = (int)($registrasi['behavior_role'] ?? 0);
$bg = '#e3f2fd'; $color = '#1976d2'; $border_badge = '#90caf9';
if ($role === 2) { $bg = '#fff3e0'; $color = '#f57c00'; $border_badge = '#ffcc80'; } // Perbaikan
if ($role === 5) { $bg = '#e8f5e9'; $color = '#2e7d32'; $border_badge = '#a5d6a7'; } // Diserahkan
if ($role === 7) { $bg = '#ffebee'; $color = '#c62828'; $border_badge = '#ef9a9a'; } // Batal
if ($role === 6) { $bg = '#f5f5f5'; $color = '#616161'; $border_badge = '#e0e0e0'; } // Ditutup

$targetDateRaw = $registrasi['target_completion_at'] ?? '';
$isOverdue = (!empty($targetDateRaw) && strtotime($targetDateRaw) > 0 && new DateTime() > new DateTime($targetDateRaw));

// Read-only for Finalisasi focus
$isReadOnlyStatus = true; 
?>

<script>
    const REG_DATA = {
        id: <?= (int)($registrasi['id'] ?? 0) ?>,
        klien_nama: <?= json_encode($registrasi['klien_nama'] ?? '-') ?>,
        nomor_registrasi: <?= json_encode($registrasi['nomor_registrasi'] ?? '-') ?>,
        klien_hp: <?= json_encode($registrasi['klien_hp'] ?? '') ?>,
        sender: <?= json_encode($currentUser['username'] ?? 'Admin') ?>,
        kantor: <?= json_encode(APP_NAME) ?>,
        tanggal: <?= json_encode(date('l, d F Y')) ?>
    };

    const DB_TEMPLATES = {
        review: "<?= $tplPerbaikan ?>",
        tutup: "<?= $tplDitutup ?>"
    };
    // Elite Logic Sync v5.91: Detect starting state

</script>

<div class="registrasi-detail">
    <!-- Back Button -->
    <div style="margin-bottom: 5px;">
        <a href="<?= APP_URL ?>/index.php?gate=finalisasi" class="btn-back" style="
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        " onmouseover="this.style.background='var(--cream)';this.style.color='var(--primary)'" onmouseout="this.style.background='';this.style.color='var(--text-light)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Daftar Finalisasi
        </a>
    </div>

    <!-- Info Card (Mirrored from registrasi_detail.php) -->
    <style>
        .nora-detail-card {
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            margin-bottom: 20px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .nora-detail-header {
            background: #fdfcfb;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
        }
        .nora-detail-title {
            color: var(--primary); font-size: 16px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;
        }
        .nora-detail-body { padding: 16px 20px; }
        .nora-detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); column-gap: 20px; row-gap: 16px; }
        .nora-data-group { display: flex; flex-direction: column; gap: 4px; }
        .nora-data-label {
            font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted); display: flex; align-items: center; gap: 4px;
        }
        .nora-data-value { font-size: 13px; font-weight: 600; color: var(--text); word-break: break-word; }
        .nora-data-box { background: #fffcf5; border: 1px solid #f2e9d8; border-radius: 8px; padding: 12px 16px; margin-top: 20px; border-left: 3px solid var(--gold); }
        .nora-data-box p { margin: 0; color: var(--text); font-size: 12px; line-height: 1.5; font-weight: 500; }
    </style>

    <div class="nora-detail-card">
        <div class="nora-detail-header">
            <h2 class="nora-detail-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--gold);">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <?= htmlspecialchars($registrasi['nomor_registrasi'] ?? '-') ?>
            </h2>
            <span class="badge" style="background: <?= $bg ?>; color: <?= $color ?>; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; border: 1px solid <?= $border_badge ?>;">
                <?= ($role === 5) ? 'Diserahkan' : (($role === 7) ? 'Batal' : (($role === 6) ? 'Ditutup' : ($registrasi['status_label'] ?? $registrasi['status']))) ?>
            </span>
        </div>

        <div class="nora-detail-body">
            <div class="nora-detail-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(135px, 1fr)); gap: 15px;">
                <div class="nora-data-group">
                    <div class="nora-data-label">👤 Nama Klien</div>
                    <div style="font-size: 13px; font-weight: 800; color: #444;"><?= htmlspecialchars($registrasi['klien_nama'] ?? '-') ?></div>
                </div>
                <div class="nora-data-group">
                    <div class="nora-data-label">📞 Nomor Kontak</div>
                    <div style="font-size: 13px; font-weight: 800; color: #444;"><?= htmlspecialchars($registrasi['klien_hp'] ?? '-') ?></div>
                </div>
                <div class="nora-data-group">
                    <div class="nora-data-label">💼 Jenis Layanan</div>
                    <div style="font-size: 13px; font-weight: 800; color: #444;"><?= htmlspecialchars($registrasi['layanan_nama'] ?? '-') ?></div>
                </div>
                <div class="nora-data-group">
                    <div class="nora-data-label">📅 Dibuat</div>
                    <div style="font-size: 13px; font-weight: 800; color: #444;"><?= date('d F Y', strtotime($registrasi['created_at'])) ?></div>
                </div>

                <!-- Absolute Unified Grid v6.05 -->
                <div class="nora-data-group">
                    <div class="nora-data-label" style="color: #2e7d32;">📊 Tgl Penyelesaian</div>
                    <div style="font-size: 13px; font-weight: 800; color: #1b5e20;">
                        <?= !empty($registrasi['selesai_batal_at']) ? date('d F Y H:i', strtotime($registrasi['selesai_batal_at'])) : '-' ?>
                    </div>
                </div>

                <div class="nora-data-group">
                    <div class="nora-data-label" style="color: #1976d2;">📦 Tgl Penyerahan</div>
                    <div style="font-size: 13px; font-weight: 800; color: #0d47a1;">
                        <?= !empty($registrasi['diserahkan_at']) ? date('d F Y H:i', strtotime($registrasi['diserahkan_at'])) : '-' ?>
                    </div>
                </div>

                <?php if ($role === 6): ?>
                <div class="nora-data-group">
                    <div class="nora-data-label" style="color: #616161;">🗄️ Arsip Ditutup</div>
                    <div style="font-size: 13px; font-weight: 800; color: #424242;">
                        <?= !empty($registrasi['ditutup_at']) ? date('d F Y H:i', strtotime($registrasi['ditutup_at'])) : '-' ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin-top: 15px;">
                <div class="nora-data-box" style="margin-top:0;">
                    <div class="nora-data-label" style="margin-bottom: 4px;">Keterangan Perkara</div>
                    <p style="font-size: 13px; color: #333; line-height: 1.6; margin: 0;"><?= !empty($registrasi['keterangan']) ? nl2br(htmlspecialchars($registrasi['keterangan'])) : 'Tidak ada keterangan opsional.' ?></p>
                </div>

                <!-- Elite Internal Notes v5.74 -->
                <div class="nora-data-box" style="background: #fff8e1; border-left: 4px solid #ffca28; margin-top: 0;">
                    <div class="nora-data-label" style="color: #795548; font-weight: 800; display: flex; align-items: center; gap: 6px;">
                        📋 CATATAN TERAKHIR
                    </div>
                    <p style="font-size: 13px; color: #5d4037; font-weight: 600; line-height: 1.6; margin: 8px 0 0 0;">
                        <?= !empty($registrasi['catatan_internal']) ? nl2br(htmlspecialchars($registrasi['catatan_internal'])) : 'Tidak ada catatan internal.' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Finalisasi Dynamic Hub v5.97 -->
    <div id="finalCard" class="detail-card" style="background: var(--white); border-radius: 12px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-bottom: 24px; border: 2px solid <?= $role === 6 ? '#2196f3' : 'var(--primary)' ?>; transition: all 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 id="hubTitle" style="margin: 0; color: <?= $role === 6 ? '#2196f3' : 'var(--primary)' ?>; font-size: 15px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <?= $role === 6 ? '🔄 PENINJAUAN ULANG KASUS' : '📁 PENUTUPAN ARSIP DIGITAL' ?>
            </h3>
            
            <div style="background: #f8f9fa; padding: 5px; border-radius: 50px; display: flex; gap: 5px; border: 1px solid #eee;">
                <?php if ($role !== 6): ?>
                <button id="tab-tutup" onclick="switchAction('tutup')" style="padding: 6px 15px; border-radius: 50px; border: none; cursor: pointer; font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 6px; transition: all 0.3s ease;">
                    📁 Tutup Arsip
                </button>
                <?php endif; ?>
                <button id="tab-review" onclick="switchAction('review')" style="padding: 6px 15px; border-radius: 50px; border: none; cursor: pointer; font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 6px; transition: all 0.3s ease;">
                    🔄 Tinjau Ulang
                </button>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label id="notesLabel" style="display: block; font-size: 10px; font-weight: 800; color: #999; margin-bottom: 6px; text-transform: uppercase;">
                📋 CATATAN AKTIVITAS
            </label>
            <textarea id="hubNotes" rows="3" style="width: 100%; border: 1px solid var(--border); border-radius: 10px; padding: 12px; font-size: 13px; color: #444; background: #fafafa; border-left: 4px solid <?= $role === 6 ? '#2196f3' : 'var(--primary)' ?>; outline: none; transition: all 0.3s;"><?= $role === 6 ? $tplPerbaikan : $tplDitutup ?></textarea>
        </div>

        <button id="finalSubmitBtn" type="button" onclick="handleFinalSubmit()" style="width: 100%; padding: 12px; background: <?= $role === 6 ? '#2196f3' : 'var(--primary)' ?>; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 15px rgba(33, 150, 243, 0.25); transition: transform 0.2s; active { transform: scale(0.98); }">
            <span id="btnIcon"><?= $role === 6 ? '🔄' : '📁' ?></span> <span id="btnText">Simpan Perubahan</span>
        </button>
    </div>

    <!-- WhatsApp Card (Mirroring Main Detail Style) v5.80 -->
    <div class="detail-card" style="background: var(--white); border-radius: 12px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-bottom: 24px; border: 1px solid var(--border);">
        <h3 style="margin: 0 0 15px 0; color: #25d366; font-size: 15px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
            💬 Notifikasi WhatsApp
        </h3>
        <p style="margin: 0 0 20px 0; color: #666; font-size: 12px; line-height: 1.6;">Kirimkan laporan progres terakhir kepada klien melalui pesan WhatsApp resmi kantor.</p>
        <button type="button" onclick="sendWhatsAppNotificationFinal()" style="width: 100%; padding: 12px; background: #25d366; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.2);">
            <span>📲</span> Kirim Progres via WhatsApp
        </button>
    </div>

    <!-- History View (Locked with standard style) -->
    <div class="detail-card" style="background: var(--white); border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid var(--border);">
        <h3 style="margin: 0 0 16px 0; color: var(--primary); font-size: 15px; font-weight: 800; display: flex; align-items: center; gap: 8px;">📜 Riwayat Aktivitas</h3>
        <div style="overflow-x: auto;">
            <table class="data-table" style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #fdfdfd;">
                        <th style="padding: 12px; border-bottom: 2px solid #f0f0f0; text-align: left; color: #999; font-weight: 800;">WAKTU</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f0f0f0; text-align: left; color: #999; font-weight: 800;">USER</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f0f0f0; text-align: left; color: #999; font-weight: 800;">STATUS</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f0f0f0; text-align: left; color: #999; font-weight: 800;">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                    <tr><td colspan="4" style="padding: 30px; text-align: center; color: #ccc;">Belum ada sejarah aktivitas.</td></tr>
                    <?php else: foreach ($history as $h): ?>
                    <tr style="border-bottom: 1px solid #f9f9f9;">
                        <td style="padding: 12px; color: #888;"><?= date('d/m/y H:i', strtotime($h['created_at'])) ?></td>
                        <td style="padding: 12px; font-weight: 800; color: #444;"><?= htmlspecialchars($h['user_name'] ?? 'System') ?></td>
                        <td style="padding: 12px;"><span style="color: var(--primary); font-weight: 800;"><?= htmlspecialchars($h['status_new_label'] ?? $h['status_new']) ?></span></td>
                        <td style="padding: 12px; color: #666;"><?= nl2br(htmlspecialchars($h['catatan'] ?? '')) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<!-- Toast Success Pojok Bawah v6.11 -->
<div id="actionMessage" style="position: fixed; bottom: 20px; right: 20px; padding: 12px 20px; border-radius: 8px; font-weight: 800; z-index: 9999; display: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15); font-size: 13px; animation: slideUp 0.3s ease;"></div>

<style>
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.success-toast { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
.error-toast { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
</style>

<script>
    var currentAction = <?= $role === 6 ? "'review'" : "'tutup'" ?>;

    function switchAction(type) {
        currentAction = type;
        const tabTutup = document.getElementById('tab-tutup');
        const tabReview = document.getElementById('tab-review');
        const notesLabel = document.getElementById('notesLabel');
        const hubNotes = document.getElementById('hubNotes');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');

        if (type === 'tutup' && tabTutup) {
            tabTutup.style.background = 'white';
            tabTutup.style.color = 'var(--primary)';
            tabTutup.style.border = '2px solid var(--primary)';
            tabTutup.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';
            
            if (tabReview) {
                tabReview.style.background = 'transparent';
                tabReview.style.color = '#888';
                tabReview.style.border = '2px solid transparent';
                tabReview.style.boxShadow = 'none';
            }
            
            notesLabel.innerText = "📋 CATATAN PENUTUPAN ARSIP";
            hubNotes.value = DB_TEMPLATES.tutup;
            btnText.innerText = "Simpan Penutupan";
            btnIcon.innerText = "📁";
        } else if (type === 'review' && tabReview) {
            tabReview.style.background = 'white';
            tabReview.style.color = 'var(--primary)';
            tabReview.style.border = '2px solid var(--primary)';
            tabReview.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';

            if (tabTutup) {
                tabTutup.style.background = 'transparent';
                tabTutup.style.color = '#888';
                tabTutup.style.border = '2px solid transparent';
                tabTutup.style.boxShadow = 'none';
            }

            notesLabel.innerText = "📋 CATATAN PENINJAUAN KEMBALI";
            hubNotes.value = DB_TEMPLATES.review;
            btnText.innerText = "Simpan Peninjauan";
            btnIcon.innerText = "🔄";
        }
    }

    function showToast(msg, isSuccess = true) {
        const toast = document.getElementById('actionMessage');
        if (!toast) return;
        toast.innerText = (isSuccess ? "✓ " : "✕ ") + msg;
        toast.className = isSuccess ? "success-toast" : "error-toast";
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }

    function handleFinalSubmit() {
        const btn = document.getElementById('finalSubmitBtn');
        const notes = document.getElementById('hubNotes').value;
        const gate = (currentAction === 'tutup') ? 'tutup_registrasi' : 'reopen_case';

        if (!btn) return;
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = "...";

        const formData = new FormData();
        formData.append('registrasi_id', REG_DATA.id);
        formData.append('notes', notes);
        formData.append('csrf_token', '<?= $_SESSION['_csrf_token'] ?? '' ?>');

        // Logic Sync v6.15: If reopen, send target_status
        if (currentAction === 'review') {
            formData.append('target_status', 'back_to_process');
        }

        fetch(`<?= APP_URL ?>/index.php?gate=${gate}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || "✓ Berhasil Diperbarui!");
                setTimeout(() => {
                    if (currentAction === 'review') {
                        window.location.href = '<?= APP_URL ?>/index.php?gate=finalisasi';
                    } else {
                        location.reload();
                    }
                }, 1000);
            } else {
                showToast(data.message || "✕ Gagal: " + (data.message || "Eror Server"), false);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(err => {
            showToast("✕ Koneksi bermasalah", false);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function sendWhatsAppNotificationFinal() {
        const label = (currentAction === 'tutup') ? 'Selesai & Diarsipkan' : 'Dalam Peninjauan Kembali';
        const message = `Halo Bapak/Ibu ${REG_DATA.klien_nama},\n\nMemberitahukan bahwa proses berkas dengan nomor ${REG_DATA.nomor_registrasi} saat ini telah : *${label}*.\n\nTerima kasih.`;
        
        let hp = REG_DATA.klien_hp.replace(/[^0-9]/g, '');
        if (hp.startsWith('0')) hp = '62' + hp.substring(1);
        
        const waUrl = `https://wa.me/${hp}?text=${encodeURIComponent(message)}`;
        window.open(waUrl, '_blank');
        location.reload();
    }

    document.addEventListener('DOMContentLoaded', () => {
        switchAction(currentAction);
    });
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
