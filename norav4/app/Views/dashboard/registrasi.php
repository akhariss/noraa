<?php
/**
 * Registrasi List View - Nora V4
 * Command Center v5.0 (Smart Calendar Restored)
 * Restoration: Putting back Day/Month/Year tabs in Calendar Filter.
 */
$displayTotal = $total ?? count($registrasiWithFlags ?? []);
?>


<!-- Hero Section -->
<div class="hero-authority">
    <span class="hero-badge-auth">Layanan Klien & Administrasi Akta</span>
    <p class="hero-p-auth">Monitor data pendaftaran dari pendaftaran hingga operasional selesai secara real-time</p>
</div>

<!-- Command Center -->
<div class="action-vault-v4">
    <form method="GET" action="" id="v4Form">
        <input type="hidden" name="gate" value="registrasi">
        <input type="hidden" name="tab" id="tabH4" value="<?= htmlspecialchars($filterTab ?? 'semua') ?>">

        <!-- Tabs Section -->
        <div class="tabs-organic">
            <a href="javascript:void(0)" onclick="setTab4('semua')" class="tab-v4 <?= ($filterTab ?? 'semua') === 'semua' ? 'active' : '' ?>">SEMUA</a>
            <a href="javascript:void(0)" onclick="setTab4('aktif')" class="tab-v4 <?= ($filterTab ?? '') === 'aktif' ? 'active' : '' ?>">AKTIF (PROSES)</a>
            <a href="javascript:void(0)" onclick="setTab4('review')" class="tab-v4 <?= ($filterTab ?? '') === 'review' ? 'active' : '' ?>">REVIEW</a>
            <a href="javascript:void(0)" onclick="setTab4('penyerahan')" class="tab-v4 <?= ($filterTab ?? '') === 'penyerahan' ? 'active' : '' ?>">PENYERAHAN</a>
            <a href="javascript:void(0)" onclick="setTab4('arsip')" class="tab-v4 <?= ($filterTab ?? '') === 'arsip' ? 'active' : '' ?>">ARSIP</a>
        </div>

        <!-- Search Section -->
        <div class="search-field-v4">
            <label for="lxSearch4" class="sr-only">Cari Nama Klien atau Nomor Registrasi</label>
            <div style="position:absolute; left:18px; top:15px; color:#99abb4; pointer-events:none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <input type="text" name="search" id="lxSearch4" class="input-search-v4" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari nama klien, nomor registrasi, atau whatsapp..." oninput="autoV4()">
            <button type="submit" class="btn-search-v4">CARI DATA</button>
        </div>

        <!-- Filter Row Section -->
        <div class="filter-full-row">
            <div class="f-item">
                <label for="orderSel" class="f-label-v4">Urutkan</label>
                <select name="order" id="orderSel" onchange="this.form.submit()" class="f-select-v4">
                    <option value="terbaru" <?= ($filterOrder ?? '') === 'terbaru' ? 'selected' : '' ?>>🕒 Terbaru di Edit</option>
                    <option value="terlama" <?= ($filterOrder ?? '') === 'terlama' ? 'selected' : '' ?>>🕒 Terlama di Edit</option>
                    <option value="baru_dibuat" <?= ($filterOrder ?? '') === 'baru_dibuat' ? 'selected' : '' ?>>➕ Terbaru Dibuat</option>
                    <option value="lama_dibuat" <?= ($filterOrder ?? '') === 'lama_dibuat' ? 'selected' : '' ?>>➕ Terlama Dibuat</option>
                    <option value="nama_asc" <?= ($filterOrder ?? '') === 'nama_asc' ? 'selected' : '' ?>>🔠 Nama A-Z</option>
                    <option value="nama_desc" <?= ($filterOrder ?? '') === 'nama_desc' ? 'selected' : '' ?>>🔠 Nama Z-A</option>
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
            <div class="f-item">
                <label for="statusSel" class="f-label-v4">Status</label>
                <select name="status" id="statusSel" onchange="this.form.submit()" class="f-select-v4">
                    <option value="">Semua Status</option>
                    <?php foreach ($allSteps as $s): ?>
                    <option value="<?= $s['step_key'] ?>" <?= ($filterStatus ?? '') === $s['step_key'] ? 'selected' : '' ?>><?= htmlspecialchars($s['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="f-item">
                <label for="bayarSel" class="f-label-v4">Payment</label>
                <select name="bayar" id="bayarSel" onchange="this.form.submit()" class="f-select-v4">
                    <option value="">Semua</option>
                    <option value="lunas" <?= ($filterBayar ?? '') === 'lunas' ? 'selected' : '' ?>>Lunas</option>
                    <option value="belum" <?= ($filterBayar ?? '') === 'belum' ? 'selected' : '' ?>>Belum</option>
                </select>
            </div>
            <div class="f-item">
                <label for="flagSel" class="f-label-v4">Atensi</label>
                <select name="flag" id="flagSel" onchange="this.form.submit()" class="f-select-v4">
                    <option value="">Semua</option>
                    <option value="1" <?= ($filterFlag ?? '') === '1' ? 'selected' : '' ?>>Bermasalah 🚩</option>
                </select>
            </div>
            <div class="f-item" style="position:relative;">
                <label for="btnWaktu" class="f-label-v4">Periode</label>
                <button type="button" id="btnWaktu" class="f-select-v4" style="display:flex; align-items:center; width:100%; text-align:left;" onclick="document.getElementById('lxCal4').classList.toggle('open')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:8px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line></svg>
                    <span><?= !empty($filterPeriode) ? htmlspecialchars($filterPeriode) : 'Semua..' ?></span>
                </button>
                <div class="cal-lx-v4" id="lxCal4">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <span style="font-size:9px; font-weight:900; color:#99abb4; text-transform:uppercase;">Pilih Rentang</span>
                        <span style="cursor:pointer; font-size:18px; font-weight:800;" onclick="document.getElementById('lxCal4').classList.remove('open')">&times;</span>
                    </div>
                    <!-- Restoration of Mode Selection Tabs -->
                    <div class="lx-btn-tab">
                        <button type="button" onclick="mMod('day', this)" class="btn-m-lx">HARI</button>
                        <button type="button" onclick="mMod('month', this)" class="btn-m-lx active">BULAN</button>
                        <button type="button" onclick="mMod('year', this)" class="btn-m-lx">TAHUN</button>
                    </div>
                    <label for="inCal4" class="sr-only">Input Kalender</label>
                    <input type="month" id="inCal4" value="<?= $filterPeriode ?>" style="width:100%; height:36px; border:1px solid #eee; border-radius:8px; padding:0 10px; margin-bottom:12px; font-size:13px; font-weight:800;" onchange="applyV4(this.value)">
                    <button type="button" onclick="clearV4()" style="width:100%; border:none; background:#f5f5f5; padding:10px; border-radius:8px; font-size:10px; font-weight:900; color:var(--primary); cursor:pointer;">RESET FILTER WAKTU</button>
                </div>
                <input type="hidden" name="periode" id="pf_v4" value="<?= htmlspecialchars($filterPeriode ?? '') ?>">
            </div>
            <div class="f-item">
                <button type="button" onclick="window.location.href='?gate=registrasi'" class="btn-reset-v4" title="Reset Semua Filter">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Table Container -->
<div class="table-vault-v4">
    <div class="table-head-v4">
        <h4 style="font-size:12px; font-weight:900; color:var(--primary); margin:0; text-transform:uppercase;">DAFTAR REGISTRASI (<?= $displayTotal ?>)</h4>
        <div style="display:flex; gap:12px;">
            <button type="button" onclick="v4Export()" class="btn-export-v4">EXPORT EXCEL</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="lx-auth-table" id="v4Table">
            <thead>
                <tr>
                    <th style="width:170px;">No Registrasi</th>
                    <th>Nama Klien</th>
                    <th class="hide-mobile">Layanan</th>
                    <th>Status Pekerjaan</th>
                    <th style="text-align:center;">Payment</th>
                    <th style="text-align:center;">Flag</th>
                    <th style="text-align:center;">Update</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registrasiWithFlags)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:60px; color:#bbb; font-style:italic;">Data tidak ditemukan.</td></tr>
                <?php else: ?>
                    <?php foreach ($registrasiWithFlags as $p): ?>
                    <?php $lP = ((float)($p['total_tagihan'] ?? 0) > 0 && (float)($p['jumlah_bayar'] ?? 0) >= (float)($p['total_tagihan'] ?? 0)); ?>
                    <tr>
                        <td style="font-weight:800; color:var(--primary);"><?= htmlspecialchars($p['nomor_registrasi']) ?></td>
                        <td><div style="font-weight:700; color:var(--primary);"><?= htmlspecialchars($p['klien_nama']) ?></div></td>
                        <td class="hide-mobile"><?= htmlspecialchars($p['nama_layanan']) ?></td>
                        <td>
                            <?php $style = getStatusStyle((int)$p['behavior_role']); ?>
                            <span class="badge-auth-lx" style="background:<?= $style['bg'] ?>; color:<?= $style['color'] ?>; border-color:<?= $style['border'] ?>;">
                                <?= htmlspecialchars($p['status_label']) ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <?php if($p['total_tagihan'] > 0): ?>
                                <span class="badge-auth-lx <?= $lP ? 'pay-pill-lunas' : 'pay-pill-belum' ?>"><?= $lP ? 'LUNAS' : 'BELUM' ?></span>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td style="text-align:center;"><?= !empty($p['has_flag']) ? '🚩' : '-' ?></td>
                        <td style="text-align:center;">
                            <div style="font-weight:900; font-size:12.5px;"><?= date('d/m/y', strtotime($p['updated_at'])) ?></div>
                            <div style="font-size:10.5px; color:#99abb4;"><?= date('H:i', strtotime($p['updated_at'])) ?></div>
                        </td>
                        <td style="text-align:center;">
                            <a href="?gate=registrasi_detail&id=<?= $p['id'] ?>" class="btn-auth-detail"><i>o</i> Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination UI v5.0 (Performance Ready) -->
<?php if ($totalPages > 1): ?>
<div style="display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 30px; padding-bottom: 20px; font-family: 'DM Sans', sans-serif;">
    <div style="display: flex; gap: 6px; align-items: center;">
        <?php 
        // Build base query string with all active filters
        $baseQuery = http_build_query([
            'gate'    => 'registrasi',
            'search'  => $search,
            'status'  => $filterStatus,
            'layanan' => $filterLayanan,
            'flag'    => $filterFlag,
            'order'   => $filterOrder,
            'tab'     => $filterTab,
            'bayar'   => $filterBayar,
            'periode' => $filterPeriode
        ]);
        
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        ?>

        <?php if ($page > 1): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $page - 1 ?>" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700; transition: all 0.2s;">&laquo; Prev</a>
        <?php endif; ?>

        <?php if ($startPage > 1): ?>
            <a href="?<?= $baseQuery ?>&page=1" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700;">1</a>
            <?php if ($startPage > 2): ?><span style="padding: 8px; color: #999;">...</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $i ?>" style="padding: 8px 14px; background: <?= $i == $page ? 'var(--primary)' : '#fff' ?>; border: 1px solid <?= $i == $page ? 'var(--primary)' : '#ddd' ?>; border-radius: 8px; text-decoration: none; color: <?= $i == $page ? 'var(--gold)' : 'var(--primary)' ?>; font-size: 13px; font-weight: 800;"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?><span style="padding: 8px; color: #999;">...</span><?php endif; ?>
            <a href="?<?= $baseQuery ?>&page=<?= $totalPages ?>" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700;"><?= $totalPages ?></a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $page + 1 ?>" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700; transition: all 0.2s;">Next &raquo;</a>
        <?php endif; ?>
    </div>
    <div style="font-size: 11px; font-weight: 700; color: #99abb4; text-transform: uppercase; letter-spacing: 0.5px;">
        Menampilkan Halaman <?= $page ?> dari <?= $totalPages ?> (Total <?= $total ?> Data)
    </div>
</div>
<?php endif; ?>

<script>
let v4Timer;
function autoV4() {
    clearTimeout(v4Timer);
    v4Timer = setTimeout(() => { document.getElementById('v4Form').submit(); }, 1200);
}
window.onclick = function(e) { if (!e.target.closest('.f-item') && !e.target.closest('.cal-lx-v4')) { document.getElementById('lxCal4').classList.remove('open'); } }
function setTab4(v) { document.getElementById('tabH4').value = v; document.getElementById('v4Form').submit(); }
function applyV4(v) { if(v) { document.getElementById('pf_v4').value=v; document.getElementById('v4Form').submit(); } }
function clearV4() { document.getElementById('pf_v4').value=''; document.getElementById('v4Form').submit(); }

// Restoration of mMod logic
function mMod(m, btn) { 
    const el = document.getElementById('inCal4');
    el.type = (m === 'day' ? 'date' : (m === 'year' ? 'number' : 'month'));
    el.placeholder = (m === 'year' ? 'Ex: 2026' : '');
    document.querySelectorAll('.btn-m-lx').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

async function v4Export() {
    const btn = document.querySelector('.btn-export-v4');
    const originalText = btn.innerHTML;
    btn.innerHTML = 'GENERATING...';
    btn.disabled = true;

    try {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('ajax_export', '1');
        
        const response = await fetch(window.location.pathname + '?' + urlParams.toString());
        const result = await response.json();
        const data = result.data || [];

        let csv = "\uFEFF";
        let meta = ['DAFTAR REGISTRASI SRI ANAH','Download: '+new Date().toLocaleString(),'Total: ' + result.total];
        meta.forEach(l => { csv += '"' + l + '"\r\n'; });
        
        // Headers
        csv += '"No Registrasi";"Nama Klien";"Layanan";"Status Pekerjaan";"Payment";"Flag";"Terakhir Update"\r\n';
        
        data.forEach(p => {
            let row = [
                p.nomor_registrasi,
                p.klien_nama,
                p.nama_layanan,
                p.status_label,
                (p.total_tagihan > 0 ? (p.jumlah_bayar >= p.total_tagihan ? 'LUNAS' : 'BELUM') : '-'),
                p.has_flag,
                p.updated_at
            ];
            csv += row.map(v => '"' + (v || '').toString().replace(/"/g, '""') + '"').join(';') + "\r\n";
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'Registrasi_AA_Full_' + new Date().toISOString().split('T')[0] + '.csv';
        link.click();
    } catch (err) {
        console.error('Export failed:', err);
        alert('Gagal mengekspor data.');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}
</script>

