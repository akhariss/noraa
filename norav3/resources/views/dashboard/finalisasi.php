<?php
/**
 * Finalisasi View - Command Center Luxe v5.2
 * Simplified filtering: SEMUA, REVIEW, BATAL.
 * Sort buttons in table headers.
 */

$currentUser = getCurrentUser();
$pageTitle = 'Penutupan Registrasi';
$activePage = 'finalisasi';

require VIEWS_PATH . '/templates/header.php';

// Prepare variables from controller
$filter = $result['filter'] ?? 'all';
$search = $_GET['search'] ?? '';
$order = $_GET['order'] ?? 'baru';
$stats = $result['stats'] ?? ['total' => 0, 'review' => 0, 'batal' => 0];

// Handle Sort Toggles
$sortOrder = ($order === 'lama') ? 'lama' : 'baru';
$nextSort = ($sortOrder === 'baru') ? 'lama' : 'baru';
?>

<style>
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; }

/* ═══ Hero Authority Header ═══ */
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

/* ═══ Smart Command Hub ═══ */
.action-vault-v5 { background: #fff; border-radius: 14px; border: 1px solid var(--border); padding: 15px; margin-bottom: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }

.f-row-v5 { display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap; }
.tabs-fluid { display: flex; gap: 8px; }
.tab-v5 { 
    text-decoration: none; padding: 8px 18px; border-radius: 10px; font-size: 11px; font-weight: 800; 
    color: var(--text-muted, #888); background: #f8f8f8; border: 1px solid #eee; transition: 0.2s; cursor: pointer;
}
.tab-v5.active { background: var(--primary); color: #fff; border-color: var(--primary-dark); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

.search-mini { position: relative; flex: 1; min-width: 250px; }
.input-mini { width: 100%; height: 50px; padding: 0 45px; border-radius: 12px; border: 1px solid var(--border); font-size: 15px; background: #fafafa; }

/* ═══ TABLE LUXE v5 (Matched to Registrasi) ═══ */
.table-luxe { background: #fff; border-radius: 15px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.02); }
.lx-header { 
    background: var(--cream, #F7F4EF) !important; border-bottom: 2px solid var(--gold); 
}
.lx-header th { 
    padding: 15px 20px; text-align: left; font-size: 11px; font-weight: 950; 
    color: var(--text, #1B3A4B) !important; text-transform: uppercase; letter-spacing: 0.5px;
    font-family: 'DM Sans', sans-serif; line-height: 1.6;
    position: relative;
}
.lx-sort-btn { 
    background: transparent; border: none; color: inherit; font: inherit; cursor: pointer; 
    display: flex; align-items: center; gap: 6px; padding: 0; width: 100%;
}
.sort-icons { display: flex; flex-direction: column; line-height: 0.5; font-size: 8px; opacity: 0.5; }
.sort-icons.active-up .up { opacity: 1; color: var(--primary); }
.sort-icons.active-down .down { opacity: 1; color: var(--primary); }

.lx-row td { padding: 14px 20px; border-bottom: 1px solid #f1f1f1; color: var(--primary); font-size: 13.5px; }
.lx-row:nth-child(even) { background: #fcfbf8; }
.lx-row:hover { background: #fdfaf3 !important; }

.badge-lx { padding: 4px 12px; border-radius: 50px; font-size: 9px; font-weight: 900; text-transform: uppercase; border: 1px solid; display: inline-block; }

/* Detail Button (Matched to Registrasi) */
.btn-auth-detail { 
    display: inline-flex; align-items: center; gap: 8px; 
    background: var(--cream, #F7F4EF) !important; color: var(--primary) !important; 
    padding: 8px 18px; border-radius: 50px; 
    font-size: 11.5px; font-weight: 800; 
    text-decoration: none; border: 1.5px solid var(--primary);
    transition: 0.2s; white-space: nowrap;
}
.btn-auth-detail:hover { background: var(--primary) !important; color: var(--cream, #F7F4EF) !important; }
.btn-auth-detail i { width: 14px; height: 14px; border: 1.5px solid var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8px; font-style: normal; }
.btn-auth-detail:hover i { border-color: var(--cream, #F7F4EF); }
</style>

<!-- Hero Section -->
<div class="hero-authority">
    <span class="hero-badge-auth">Layanan Klien & Administrasi Akta</span>
    <p class="hero-p-auth">Finalisasi Berkas Registrasi</p>
</div>

<!-- Command Area -->
<div class="action-vault-v5">
    <form method="GET" action="" id="finalFormv5">
        <input type="hidden" name="gate" value="finalisasi">
        <input type="hidden" name="filter" id="filterInput" value="<?= htmlspecialchars($filter) ?>">
        <input type="hidden" name="order" id="orderInput" value="<?= htmlspecialchars($order) ?>">

        <div class="f-row-v5">
            <!-- Fluid Tabs -->
            <div class="tabs-fluid">
                <button type="button" onclick="setFilter('all')" class="tab-v5 <?= $filter === 'all' ? 'active' : '' ?>">SEMUA (<?= $stats['review'] + $stats['batal'] ?>)</button>
                <button type="button" onclick="setFilter('review')" class="tab-v5 <?= $filter === 'review' ? 'active' : '' ?>">REVIEW (<?= $stats['review'] ?>)</button>
                <button type="button" onclick="setFilter('batal')" class="tab-v5 <?= $filter === 'batal' ? 'active' : '' ?>">BATAL (<?= $stats['batal'] ?>)</button>
            </div>

            <!-- Mini Search -->
            <div class="search-mini">
                <div style="position:absolute; left:12px; top:12px; color:#bbb;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <input type="text" name="search" class="input-mini" value="<?= htmlspecialchars($search) ?>" placeholder="Cari No. Reg atau Nama..." oninput="debounceSearch()">
            </div>
            
            <button type="button" onclick="window.location.href='?gate=finalisasi'" class="tab-v5" style="padding:10px; background:#fff; border:1px solid #eee;" title="Reset">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
            </button>
        </div>
    </form>
</div>

<!-- Luxe Table -->
<div class="table-luxe">
    <table style="width: 100%; border-collapse: collapse;">
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
                <tr><td colspan="6" style="padding: 60px; text-align: center; color: #ccc; font-weight: 600;">Data tidak ditemukan.</td></tr>
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
                        <a href="<?= APP_URL ?>/index.php?gate=registrasi_detail_finalisasi&id=<?= $p['id'] ?>" class="btn-auth-detail"><i>o</i> Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination UI v5.0 (Consistent Style) -->
<?php if ($result['pagination']['total_pages'] > 1): ?>
<div style="display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 30px; padding-bottom: 20px; font-family: 'DM Sans', sans-serif;">
    <div style="display: flex; gap: 6px; align-items: center;">
        <?php 
        $baseQuery = http_build_query([
            'gate'   => 'finalisasi',
            'filter' => $filter,
            'order'  => $order,
            'search' => $search
        ]);
        
        $p = $result['pagination'];
        $curr = $p['current_page'];
        $totalP = $p['total_pages'];
        
        $startPage = max(1, $curr - 2);
        $endPage = min($totalP, $curr + 2);
        ?>

        <?php if ($p['has_prev']): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $curr - 1 ?>" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700; transition: all 0.2s;">&laquo; Prev</a>
        <?php endif; ?>

        <?php if ($startPage > 1): ?>
            <a href="?<?= $baseQuery ?>&page=1" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700;">1</a>
            <?php if ($startPage > 2): ?><span style="padding: 8px; color: #999;">...</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $i ?>" style="padding: 8px 14px; background: <?= $i == $curr ? 'var(--primary)' : '#fff' ?>; border: 1px solid <?= $i == $curr ? 'var(--primary)' : '#ddd' ?>; border-radius: 8px; text-decoration: none; color: <?= $i == $curr ? 'var(--gold)' : 'var(--primary)' ?>; font-size: 13px; font-weight: 800;"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($endPage < $totalP): ?>
            <?php if ($endPage < $totalP - 1): ?><span style="padding: 8px; color: #999;">...</span><?php endif; ?>
            <a href="?<?= $baseQuery ?>&page=<?= $totalP ?>" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700;"><?= $totalP ?></a>
        <?php endif; ?>

        <?php if ($p['has_next']): ?>
            <a href="?<?= $baseQuery ?>&page=<?= $curr + 1 ?>" style="padding: 8px 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 700; transition: all 0.2s;">Next &raquo;</a>
        <?php endif; ?>
    </div>
    <div style="font-size: 11px; font-weight: 700; color: #99abb4; text-transform: uppercase; letter-spacing: 0.5px;">
        Menampilkan Halaman <?= $curr ?> dari <?= $totalP ?> (Total <?= $p['total'] ?> Data)
    </div>
</div>
<?php endif; ?>

<script>
let searchTimer;
function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { document.getElementById('finalFormv5').submit(); }, 800);
}
function setFilter(v) {
    document.getElementById('filterInput').value = v;
    document.getElementById('finalFormv5').submit();
}
function toggleSort() {
    const cur = document.getElementById('orderInput').value;
    document.getElementById('orderInput').value = (cur === 'baru') ? 'lama' : 'baru';
    document.getElementById('finalFormv5').submit();
}
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
