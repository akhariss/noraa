<?php
/**
 * Nora Message Management - NATURAL LUXE EDITION v18.0
 * WhatsApp templates in a stunning grid.
 */
$activePage = 'app_settings';
require VIEWS_PATH . '/templates/header.php';
?>

<style>
:root {
    --gold: #9C7C38;
    --gold-light: #B8964F;
    --navy: #1B3A4B;
    --bg-cream: #F7F4EF;
    --white: #FFFFFF;
}

.cms-pesan-luxe {
    background: #fdfdfd;
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
    color: var(--navy);
    padding: 30px;
}

/* Header Section */
.luxe-page-title {
    margin-bottom: 30px;
}
.luxe-page-title h1 { margin: 0; font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 700; color: var(--navy); }
.luxe-page-title p { margin: 5px 0 0; font-size: 14px; color: #888; }

/* Grid Layout */
.pesan-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
    gap: 25px;
}

.pesan-card-luxe {
    background: #fff;
    border: 1px solid #EEDFBB;
    border-radius: 20px;
    padding: 0;
    transition: 0.3s;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.pesan-card-luxe:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(27,58,75,0.08);
    border-color: var(--gold);
}

.pc-header {
    background: var(--bg-cream);
    padding: 20px 25px;
    border-bottom: 1px solid #EEDFBB;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pc-title h4 { margin: 0; font-size: 16px; font-weight: 850; color: var(--navy); }
.pc-key { font-size: 10px; font-weight: 900; color: var(--gold); text-transform: uppercase; letter-spacing: 1px; display: block; margin-top: 4px; }

.btn-pc-edit {
    background: var(--navy); color: #fff; border: none; padding: 8px 18px; border-radius: 50px;
    font-size: 11px; font-weight: 950; cursor: pointer; transition: 0.2s;
}
.btn-pc-edit:hover { transform: scale(1.05); box-shadow: 0 5px 12px rgba(27,58,75,0.2); }

.pc-body { padding: 25px; flex: 1; display: flex; flex-direction: column; }
.pc-desc { font-size: 13px; color: #777; margin: 0 0 20px 0; line-height: 1.5; }

/* Preview Bubble Styled */
.wa-bubble-luxe {
    background: #FDFBF7;
    border: 1.5px solid #F1E9D7;
    border-radius: 14px;
    padding: 18px;
    font-size: 13px;
    line-height: 1.6;
    color: #444;
    position: relative;
    border-left: 5px solid var(--gold);
}

.wa-bubble-label {
    font-size: 10px; font-weight: 950; color: var(--gold); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
}

/* Modal Styling */
.nora-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,31,40,0.85); display: none; align-items: center; justify-content: center; z-index: 20000; padding: 20px; }
.nora-modal-content { background: #fff; width: 100%; max-width: 800px; border-radius: 24px; overflow: hidden; box-shadow: 0 30px 70px rgba(0,0,0,0.4); display: flex; flex-direction: column; max-height: 90vh; }

.nora-modal-header { padding: 15px 25px; background: #fff; border-bottom: 1px solid #EEE; display: flex; justify-content: space-between; align-items: center; }
.nora-modal-header h3 { margin: 0; font-family: 'Cormorant Garamond', serif; font-size: 20px; color: var(--navy); }
.nora-modal-close { background: #fff; border: 2.5px solid var(--gold); color: var(--gold); width: 34px; height: 34px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; font-size: 16px; font-weight: 900; }

.nora-modal-body { padding: 20px 25px; overflow-y: auto; flex: 1; }
.luxe-f-group { margin-bottom: 15px; }
.luxe-f-group label { font-size: 9px; font-weight: 950; color: #AAA; text-transform: uppercase; margin-bottom: 6px; display: block; }
.luxe-f-input { width: 100%; padding: 12px; border: 2px solid #F8F8F8; border-radius: 12px; outline: none; transition: 0.3s; font-size: 14px; font-weight: 800; font-family: inherit; }
.luxe-f-textarea { min-height: 180px; resize: vertical; line-height: 1.5; font-weight: 600; }

.variable-box-luxe { background: var(--bg-cream); border-radius: 12px; padding: 15px; border: 1px dashed var(--gold); }
.variable-title { font-size: 10px; font-weight: 950; color: var(--gold); margin-bottom: 10px; display: block; }
.variable-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.v-chip { background: #fff; border: 1.2px solid #EEDFBB; color: var(--navy); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; font-family: monospace; cursor: pointer; transition: 0.2s; }

.nora-modal-footer { padding: 15px 25px; background: #fff; border-top: 1px solid #EEE; display: flex; justify-content: flex-end; gap: 10px; }
.btn-nora-submit { background: var(--navy); color: #fff; border: none; padding: 12px 40px; border-radius: 50px; font-size: 13px; font-weight: 950; cursor: pointer; transition: 0.2s; }
.btn-nora-submit:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(27,58,75,0.3); }
.btn-nora-secondary { background: #fff; border: 1.5px solid #DDD; color: #888; padding: 14px 30px; border-radius: 50px; font-weight: 900; font-size: 13px; cursor: pointer; }

/* Toast */
#toast { position: fixed; bottom: 30px; right: 30px; background: var(--navy); color: #fff; padding: 20px 40px; border-radius: 16px; font-weight: 950; box-shadow: 0 15px 40px rgba(0,0,0,0.2); border-left: 6px solid var(--gold); z-index: 100000; display: none; }
</style>

<div class="cms-pesan-luxe">
    <div class="luxe-page-title">
        <h1>Template Pesan WhatsApp</h1>
        <p>Atur format komunikasi otomatis agar tetap berkelas dan informatif.</p>
    </div>

    <div class="pesan-grid">
        <?php if (!empty($templates)): ?>
            <?php foreach ($templates as $template): ?>
                <div class="pesan-card-luxe" id="message-card-<?= $template['id'] ?>">
                    <div class="pc-header">
                        <div class="pc-title">
                            <h4><?= htmlspecialchars($template['template_name']) ?></h4>
                            <span class="pc-key"><?= htmlspecialchars($template['template_key']) ?></span>
                        </div>
                        <button onclick='editMessageTemplate(<?= $template['id'] ?>, <?= json_encode($template['template_name'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($template['template_body'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="btn-pc-edit">Edit</button>
                    </div>
                    <div class="pc-body">
                        <?php if (!empty($template['description'])): ?>
                            <p class="pc-desc"><?= htmlspecialchars($template['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="wa-bubble-luxe">
                            <span class="wa-bubble-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                PRATINJAU PESAN
                            </span>
                            <?= nl2br(htmlspecialchars($template['template_body'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align:center; padding:100px; color:#888;">
                <p>Belum ada template pesan yang didefinisikan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Luxe Modal Message -->
<div id="messageModal" class="nora-modal-overlay">
    <div class="nora-modal-content">
        <div class="nora-modal-header">
            <h3 id="messageModalTitle">Edit Template Pesan</h3>
            <button onclick="closeMessageModal()" class="nora-modal-close">&times;</button>
        </div>
        <form id="messageForm">
            <input type="hidden" name="template_id" id="messageTemplateId">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="nora-modal-body">
                <div class="luxe-f-group">
                    <label>Konten Pesan WhatsApp</label>
                    <textarea name="template_body" id="messageTemplateBody" class="luxe-f-input luxe-f-textarea" placeholder="Tulis format pesan di sini..."></textarea>
                </div>

                <div class="variable-box-luxe">
                    <span class="variable-title">VARIABEL YANG TERSEDIA (DAPAT DISALIN)</span>
                    <div class="variable-chips">
                        <span class="v-chip" onclick="copyVar('{nama_klien}')">{nama_klien}</span>
                        <span class="v-chip" onclick="copyVar('{nomor_registrasi}')">{nomor_registrasi}</span>
                        <span class="v-chip" onclick="copyVar('{status}')">{status}</span>
                        <span class="v-chip" onclick="copyVar('{nama_pengirim}')">{nama_pengirim}</span>
                        <span class="v-chip" onclick="copyVar('{phone}')">{phone}</span>
                        <span class="v-chip" onclick="copyVar('{alamat}')">{alamat}</span>
                        <span class="v-chip" onclick="copyVar('{tanggal}')">{tanggal}</span>
                        <span class="v-chip" onclick="copyVar('{link_tracking}')">{link_tracking}</span>
                    </div>
                </div>
            </div>

            <div class="nora-modal-footer">
                <button type="button" onclick="closeMessageModal()" class="btn-nora-secondary">Batal</button>
                <button type="submit" id="btnSubmitMessage" class="btn-nora-submit">Simpan Template</button>
            </div>
        </form>
    </div>
</div>

<div id="toast">Notif</div>

<script>
function editMessageTemplate(id, name, body) {
    document.getElementById('messageModalTitle').innerText = 'Edit Template: ' + name;
    document.getElementById('messageTemplateId').value = id;
    const bodyInput = document.getElementById('messageTemplateBody');
    bodyInput.value = body;
    document.getElementById('messageModal').style.display = 'flex';
    setTimeout(() => bodyInput.focus(), 100);
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
}

function copyVar(t) {
    const el = document.getElementById('messageTemplateBody');
    const start = el.selectionStart;
    const end = el.selectionEnd;
    const val = el.value;
    el.value = val.substring(0, start) + t + val.substring(end);
    el.focus();
    el.selectionStart = el.selectionEnd = start + t.length;
}

document.getElementById('messageForm').onsubmit = async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitMessage');
    const originalText = btn.innerText;
    
    try {
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        const fd = new FormData(e.target);
        const res = await fetch('<?= APP_URL ?>/index.php?gate=cms_save_message_tpl', {
            method: 'POST',
            body: fd
        });

        const data = await res.json();
        if (data.success) {
            showToast('Template pesan berhasil diperbarui.');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.message || 'Gagal menyimpan.');
            btn.disabled = false;
            btn.innerText = originalText;
        }
    } catch (err) {
        alert('Kesalahan koneksi.');
        btn.disabled = false;
        btn.innerText = originalText;
    }
};

function showToast(m) {
    const t = document.getElementById('toast');
    t.innerText = m;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3000);
}

window.onclick = (e) => {
    if (e.target.id === 'messageModal') closeMessageModal();
};
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
