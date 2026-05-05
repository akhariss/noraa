<?php
/**
 * Arsip View - Behavior 5,6
 * Tabs: Semua, Diserahkan, Batal 
 * Search + filter (urutan, layanan, periode) - no status/atensi/payment
 * Finalisasi-style table + Export
 */

$currentUser = getCurrentUser();
$pageTitle = 'Arsip Registrasi';
$activePage = 'arsip';

require VIEWS_PATH . '/templates/header.php';

$search = $_GET['search'] ?? '';
$order = $_GET['order'] ?? 'baru';
$arsipTab = $_GET['arsip_tab'] ?? 'semua';
$filterLayanan = $_GET['layanan'] ?? '';
$filterPeriode = $_GET['periode'] ?? '';

if (!in_array($arsipTab, ['semua', 'diserahkan', 'batal'])) $arsipTab = 'semua';
$sortOrder = ($order === 'lama') ? 'lama' : 'baru';

$layananLabel = 'Semua';
if (!empty($filterLayanan)) {
    foreach ($layanan as $l) { if ($l['id'] == $filterLayanan) { $layananLabel = $l['nama_layanan']; break; } }
}
?>

<style>
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; }

.hero-authority {
    background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
    padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.hero-badge-auth {
    display: inline-block;
    background: rgba(212, 175, 55, 0.15); color: var(--gold-light);
    padding: 6px 14px; border-radius: 8px; font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; margin-bottom: 8px;
    border: 1px solid rgba(212, 175, 55, 0.3);
}
.hero-p-auth { font-size: 14px; color: rgba(255, 255, 255, 0.85); margin: 0; }

.action-vault-v4 { background: #fff; border-radius: 15px; border: 1px solid var(--border); padding: 18px; margin-bottom: 20px; box-shadow: 0 2px 15px rgba(0,0,0,0.02); }
.tabs-organic { display: flex; gap: 8px; margin-bottom: 18px; }
.tab-v4 { text-decoration: none; padding: 8px 18px; border-radius: 10px; font-size: 11px; font-weight: 800; color: var(--text-muted); background: #f8f8f8; border: 1px solid #eee; transition: 0.2s; cursor: pointer; }
.tab-v4.active { background: var(--primary); color: #fff; border-color: var(--primary-dark); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

.search-field-v4 { position: relative; display: flex; gap: 10px; margin-bottom: 18px; }
.input-search-v4 { flex: 1; height: 50px; padding: 0 45px; border-radius: 12px; border: 1px solid var(--border); font-size: 15px; background: #fafafa; }
.btn-search-v4 { height: 50px; padding: 0 25px; background: var(--primary); color: var(--cream); border-radius: 12px; border: none; font-weight: 800; cursor: pointer; font-size: 12px; }

.filter-full-row { display: grid; grid-template-columns: repeat(3, 1fr) 50px; gap: 10px; background: var(--cream); padding: 15px; border-radius: 12px; align-items: end; border: 1px solid rgba(156, 124, 56, 0.08); }
.f-label-v4 { font-size: 9px; font-weight: 800; color: #99abb4; text-transform: uppercase; margin-bottom: 5px; display: block; letter-spacing: 0.5px; }
.f-select-v4 { width: 100%; height: 40px; padding: 0 10px; border-radius: 8px; border: 1px solid var(--border); font-size: 12.5px; font-weight: 700; color: var(--primary); background: #fff; cursor: pointer; }
.btn-reset-v4 { width: 40px; height: 40px; border-radius: 8px; border: 1px solid #dcd8ce; background: #fff; color: var(--primary); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; }
.btn-reset-v4:hover { transform: rotate(90deg); color: white; background: var(--primary); }

/* Calendar popup */
.cal-lx-v4 { display: none; position: absolute; top: calc(100% + 5px); right: 0; background: #fff; border-radius: 15px; border: 1px solid #eee; box-shadow: 0 10px 40px rgba(0,0,0,0.12); width: 250px; z-index: 1000; padding: 15px; }
.cal-lx-v4.open { display: block; animation: lxDown 0.2s ease-out; }
.lx-btn-tab { display: flex; gap: 4px; background: #f4f5f7; padding: 4px; border-radius: 8px; margin-bottom: 12px; }
.btn-m-lx { flex: 1; border: none; padding: 6px; font-size: 10px; font-weight: 950; border-radius: 6px; cursor: pointer; color: #99abb4; background: transparent; transition: 0.2s; }
.btn-m-lx.active { background: #fff; color: var(--primary); box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
@keyframes lxDown { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

.table-luxe { background: #fff; border-radius: 15px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.02); }
.table-head-v4 { padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f3f5; }
.lx-header { background: var(--cream, #F7F4EF) !important; border-bottom: 2px solid var(--gold); }
.lx-header th { 
    padding: 15px 20px; text-align: left; font-size: 11px; font-weight: 950; 
    color: var(--text, #1B3A4B) !important; text-transform: uppercase; letter-spacing: 0.5px;
    font-family: 'DM Sans', sans-serif; line-height: 1.6; position: relative;
}
.lx-sort-btn { background: transparent; border: none; color: inherit; font: inherit; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 0; width: 100%; }
.sort-icons { display: flex; flex-direction: column; line-height: 0.5; font-size: 8px; opacity: 0.5; }

.lx-row td { padding: 14px 20px; border-bottom: 1px solid #f1f1f1; color: var(--primary); font-size: 13.5px; }
.lx-row:nth-child(even) { background: #fcfbf8; }
.lx-row:hover { background: #fdfaf3 !important; }

.badge-lx { padding: 4px 12px; border-radius: 50px; font-size: 9px; font-weight: 900; text-transform: uppercase; border: 1px solid; display: inline-block; }

.btn-auth-detail { 
    display: inline-flex; align-items: center; gap: 8px; 
    background: var(--cream, #F7F4EF) !important; color: var(--primary) !important; 
    padding: 8px 18px; border-radius: 50px; font-size: 11.5px; font-weight: 800; 
    text-decoration: none; border: 1.5px solid var(--primary); transition: 0.2s; white-space: nowrap;
}
.btn-auth-detail:hover { background: var(--primary) !important; color: var(--cream, #F7F4EF) !important; }
.btn-auth-detail i { width: 14px; height: 14px; border: 1.5px solid var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8px; font-style: normal; }
.btn-auth-detail:hover i { border-color: var(--cream, #F7F4EF); }

.btn-export-v4 { 
    height: 40px; padding: 0 20px; background: var(--primary); color: var(--cream); 
    border-radius: 8px; border: 1.5px solid var(--primary-dark); font-weight: 800; 
    cursor: pointer; font-size: 11px; display: flex; align-items: center; gap: 10px; transition: 0.2s;
}
.btn-export-v4:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(27,58,75,0.2); }
</style>

<!-- Hero Section -->
<div class="hero-authority">
    <span class="hero-badge-auth">Arsip & Riwayat</span>
    <p class="hero-p-auth">Data registrasi yang telah diserahkan atau ditutup</p>
</div>

<!-- Command Center -->
<div class="action-vault-v4">
    <form method="GET" action="" id="arsipForm">
        <input type="hidden" name="gate" value="arsip">
        <input type="hidden" name="order" id="orderInput" value="<?= htmlspecialchars($order) ?>">
        <input type="hidden" name="arsip_tab" id="arsipTabH" value="<?= htmlspecialchars($arsipTab) ?>">

        <!-- Tabs Section -->
        <div class="tabs-organic">
            <a href="javascript:void(0)" onclick="setArsipTab('semua')" class="tab-v4 <?= $arsipTab === 'semua' ? 'active' : '' ?>">SEMUA</a>
            <a href="javascript:void(0)" onclick="setArsipTab('diserahkan')" class="tab-v4 <?= $arsipTab === 'diserahkan' ? 'active' : '' ?>">DISERAHKAN</a>
            <a href="javascript:void(0)" onclick="setArsipTab('batal')" class="tab-v4 <?= $arsipTab === 'batal' ? 'active' : '' ?>">BATAL</a>
        </div>

        <!-- Search Section -->
        <div class="search-field-v4">
            <label for="arsipSearch" class="sr-only">Cari Nama Klien atau Nomor Registrasi</label>
            <div style="position:absolute; left:18px; top:15px; color:#99abb4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <input type="text" name="search" id="arsipSearch" class="input-search-v4" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama klien, nomor registrasi..." oninput="autoSearch()">
            <button type="submit" class="btn-search-v4">CARI DATA</button>
        </div>

        <!-- Filter Row: Urutan, Layanan, Periode only (no status/atensi/payment) -->
        <div class="filter-full-row">
            <div class="f-item">
                <label for="orderSel" class="f-label-v4">Urutkan</label>
                <select name="order" id="orderSel" onchange="this.form.submit()" class="f-select-v4">
                    <option value="baru" <?= ($order ?? '') === 'baru' ? 'selected' : '' ?>>🕒 Terbaru</option>
                    <option value="lama" <?= ($order ?? '') === 'lama' ? 'selected' : '' ?>>🕒 Terlama</option>
                </select>
            </div>
            <div class="f-item">
                <label for="layananSel" class="f-label-v4">Layanan</label>
                <select name="layanan" id="layananSel" onchange="this.form.submit()" class="f-select-v4">
                    <option value="">Semua Layanan</option>
                    <?php foreach ($layanan as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= ($filterLayanan ?? '') == $l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['nama_layanan']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="f-item" style="position:relative;">
                <label for="btnWaktu" class="f-label-v4">Periode</label>
                <button type="button" id="btnWaktu" class="f-select-v4" style="display:flex; align-items:center; width:100%; text-align:left;" onclick="document.getElementById('lxCalArsip').classList.toggle('open')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:8px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line></svg>
                    <span><?= !empty($filterPeriode) ? htmlspecialchars($filterPeriode) : 'Semua..' ?></span>
                </button>
                <div class="cal-lx-v4" id="lxCalArsip">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <span style="font-size:9px; font-weight:900; color:#99abb4; text-transform:uppercase;">Pilih Rentang</span>
                        <span style="cursor:pointer; font-size:18px; font-weight:800;" onclick="document.getElementById('lxCalArsip').classList.remove('open')">&times;</span>
                    </div>
                    <div class="lx-btn-tab">
                        <button type="button" onclick="mMod('day', this)" class="btn-m-lx">HARI</button>
                        <button type="button" onclick="mMod('month', this)" class="btn-m-lx active">BULAN</button>
                        <button type="button" onclick="mMod('year', this)" class="btn-m-lx">TAHUN</button>
                    </div>
                    <label for="inCalArsip" class="sr-only">Input Kalender</label>
                    <input type="month" id="inCalArsip" value="<?= $filterPeriode ?>" style="width:100%; height:36px; border:1px solid #eee; border-radius:8px; padding:0 10px; margin-bottom:12px; font-size:13px; font-weight:800;" onchange="applyPeriode(this.value)">
                    <button type="button" onclick="clearPeriode()" style="width:100%; border:none; background:#f5f5f5; padding:10px; border-radius:8px; font-size:10px; font-weight:900; color:var(--primary); cursor:pointer;">RESET FILTER WAKTU</button>
                </div>
                <input type="hidden" name="periode" id="pf_arsip" value="<?= htmlspecialchars($filterPeriode ?? '') ?>">
            </div>
            <div class="f-item">
                <button type="button" onclick="window.location.href='?gate=arsip'" class="btn-reset-v4" title="Reset Semua Filter">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Table Container -->
<div class="table-luxe">
    <div class="table-head-v4">
        <h4 style="font-size:12px; font-weight:900; color:var(--primary); margin:0; text-transform:uppercase;">ARSIP REGISTRASI (<?= $result['pagination']['total'] ?? 0 ?>)</h4>
        <div style="display:flex; gap:12px;">
            <button type="button" onclick="arsipExport()" class="btn-export-v4">EXPORT EXCEL</button>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse;" id="arsipTable">
        <thead class="lx-header">
            <tr>
                <th style="width: 200px;">
                    <button type="button" class="lx-sort-btn" onclick="toggleSort()">
                        NO REGISTRASI
                        <div class="sort-icons <?= $order === 'baru' ? 'active-down' : 'active-up' ?>">
                            <span class="up">▲</span>
                            <span class="down">▼</span>
                        </div>
                    </button>
                </th>
                <th>KLIEN</th>
                <th>LAYANAN</th>
                <th>STATUS</th>
                <th style="text-align: center;">TERAKHIR EDIT</th>
                <th style="width: 80px; text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($result['data'])): ?>
                <tr><td colspan="6" style="padding: 60px; text-align: center; color: #ccc; font-weight: 600;">Data arsip tidak ditemukan.</td></tr>
            <?php else: ?>
                <?php foreach ($result['data'] as $p): ?>
                <tr class="lx-row">
                    <td style="font-weight: 800; font-family: monospace; letter-spacing: -0.5px;"><?= htmlspecialchars($p['nomor_registrasi']) ?></td>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);"><?= htmlspecialchars($p['klien_nama']) ?></div>
                        <div style="font-size: 11px; color: #99abb4;"><?= htmlspecialchars($p['klien_hp']) ?></div>
                    </td>
                    <td style="font-size: 12px; font-weight: 600;"><?= htmlspecialchars($p['nama_layanan']) ?></td>
                    <td>
                        <?php $s = \App\Domain\Entities\Registrasi::getStatusStyle((int)$p['behavior_role']); ?>
                        <span class="badge-lx" style="background:<?= $s['bg'] ?>; color:<?= $s['color'] ?>; border-color:<?= $s['border'] ?>;">
                            <?= htmlspecialchars($p['status_label']) ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <div style="font-weight: 800;"><?= date('d F Y', strtotime($p['updated_at'])) ?></div>
                        <div style="font-size: 10px; color: #99abb4; opacity: 0.8;"><?= date('H:i', strtotime($p['updated_at'])) ?></div>
                    </td>
                    <td style="text-align: center;">
                        <a href="<?= APP_URL ?>/index.php?gate=registrasi_detail_finalisasi&id=<?= $p['id'] ?>&from=arsip" class="btn-auth-detail"><i>o</i> Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($result['pagination']['total_pages'] > 1): ?>
<div style="display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 30px; padding-bottom: 20px; font-family: 'DM Sans', sans-serif;">
    <div style="display: flex; gap: 6px; align-items: center;">
        <?php 
        $baseQuery = http_build_query(['gate' => 'arsip', 'order' => $order, 'search' => $search, 'arsip_tab' => $arsipTab, 'layanan' => $filterLayanan, 'periode' => $filterPeriode]);
        $p = $result['pagination'];
        $curr = $p['current_page']; $totalP = $p['total_pages'];
        $startPage = max(1, $curr - 2); $endPage = min($totalP, $curr + 2);
        ?>
        <?php if ($p['has_prev']): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $curr - 1 ?>" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700;">&laquo; Prev</a>
        <?php endif; ?>
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $i ?>" style="padding: 8px 14px; background: <?= $i == $curr ? 'var(--primary)' : '#fff' ?>; border: 1px solid <?= $i == $curr ? 'var(--primary)' : '#ddd' ?>; border-radius: 8px; text-decoration: none; color: <?= $i == $curr ? 'var(--gold)' : 'var(--primary)' ?>; font-size: 13px; font-weight: 800;"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($p['has_next']): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $curr + 1 ?>" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700;">Next &raquo;</a>
        <?php endif; ?>
    </div>
    <div style="font-size: 11px; font-weight: 700; color: #99abb4; text-transform: uppercase; letter-spacing: 0.5px;">
        Menampilkan Halaman <?= $curr ?> dari <?= $totalP ?> (Total <?= $p['total'] ?> Data)
    </div>
</div>
<?php endif; ?>

<script>
let sTimer;
function autoSearch() { clearTimeout(sTimer); sTimer = setTimeout(() => { document.getElementById('arsipForm').submit(); }, 1200); }
function setArsipTab(v) { document.getElementById('arsipTabH').value = v; document.getElementById('arsipForm').submit(); }
function toggleSort() {
    const cur = document.getElementById('orderInput').value;
    document.getElementById('orderInput').value = (cur === 'baru') ? 'lama' : 'baru';
    document.getElementById('arsipForm').submit();
}
function applyPeriode(v) { if(v) { document.getElementById('pf_arsip').value=v; document.getElementById('arsipForm').submit(); } }
function clearPeriode() { document.getElementById('pf_arsip').value=''; document.getElementById('arsipForm').submit(); }
function mMod(m, btn) { 
    const el = document.getElementById('inCalArsip');
    el.type = (m === 'day' ? 'date' : (m === 'year' ? 'number' : 'month'));
    el.placeholder = (m === 'year' ? 'Ex: 2026' : '');
    document.querySelectorAll('.btn-m-lx').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
window.onclick = function(e) { if (!e.target.closest('.f-item') && !e.target.closest('.cal-lx-v4')) { document.getElementById('lxCalArsip').classList.remove('open'); } }

function arsipExport() {
    const table = document.getElementById('arsipTable'); if (!table) return;
    let csv = "\uFEFF";
    let meta = ['ARSIP REGISTRASI','Download: '+new Date().toLocaleString(),'Total: <?= $result['pagination']['total'] ?? 0 ?>'];
    meta.forEach(l => { csv += '"' + l + '"\r\n'; });
    table.querySelectorAll('tr').forEach(tr => {
        let r = [];
        tr.querySelectorAll('th, td').forEach((c, idx) => {
            if (idx === 5) return; // skip action column
            let txt = c.innerText.trim().replace(/\n/g, ' ');
            r.push('"' + txt.replace(/"/g, '""') + '"');
        });
        csv += r.join(';') + "\r\n";
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'Arsip_Registrasi_' + new Date().toISOString().split('T')[0] + '.csv';
    link.click();
}
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
