<?php
/**
 * Ringkasan Audit Pelaksanaan Component
 * Michelin Unified Component v7.0 (Symmetrical Sync)
 */

if (!isset($registrasi) || !isset($timelineSteps)) return;

// 1. TIMELINE CALCULATION (Real Lead Time)
$time_in_steps = []; 
$historyAsc = array_reverse($history ?? []);
$lastTime = strtotime($registrasi['created_at']);

if (!empty($historyAsc)) {
    foreach ($historyAsc as $h) {
        $oldId = $h['status_old_id'];
        if ($oldId) {
            if (!isset($time_in_steps[$oldId])) $time_in_steps[$oldId] = 0;
            $time_in_steps[$oldId] += (strtotime($h['created_at']) - $lastTime);
        }
        $lastTime = strtotime($h['created_at']);
    }
}
$currentStepId = $registrasi['current_step_id'];
if (!isset($time_in_steps[$currentStepId])) $time_in_steps[$currentStepId] = 0;

$roleStop = in_array((int)($registrasi['behavior_role'] ?? 0), [4,5,6,7]);
if (!$roleStop) {
    $time_in_steps[$currentStepId] += (time() - $lastTime);
} else if (!empty($registrasi['selesai_batal_at'])) {
    $time_in_steps[$currentStepId] += (strtotime($registrasi['selesai_batal_at']) - $lastTime);
}

// 2. PAYMENT DATA
$ps = $paymentSummary ?? [];
$pRiwayat = $ps['riwayat'] ?? [];
$isLunas = $ps['lunas'] ?? false;
$badgeColor = $isLunas ? '#2e7d32' : '#c62828';
$badgeBg = $isLunas ? '#e8f5e9' : '#fff5f5';

if (!function_exists('formatDurationPart')) {
    function formatDurationPart($seconds) {
        if ($seconds <= 0) return '-';
        if ($seconds < 3600) return round($seconds / 60) . 'm';
        if ($seconds < 86400) return round($seconds / 3600) . 'j';
        return round($seconds / 86400) . ' Hari';
    }
}
?>

<div class="detail-card" style="background: var(--white); border-radius: 12px; padding: 16px 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-bottom: 24px; border: 1px solid var(--border);">
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border); margin-bottom: 16px;">
        <h3 style="margin: 0; color: var(--primary); font-size: 14px; font-weight: 800; display: flex; align-items: center; gap: 8px; font-family: 'DM Sans', sans-serif;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: #5d4037;"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path><rect x="9" y="3" width="6" height="4" rx="2" ry="2"></rect><line x1="9" y1="14" x2="15" y2="14"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
            RINGKASAN AUDIT PELAKSANAAN
        </h3>
        <button type="button" onclick="const b=document.getElementById('timelineLegend'); const s=b.style.display==='none'; b.style.display=s?'block':'none'; this.querySelector('svg').style.transform=s?'rotate(180deg)':''; " style="background: none; border: 1px solid var(--border); color: var(--primary); font-size: 11px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 8px; transition: all 0.2s; font-family: 'DM Sans', sans-serif;">
            INFORMASI TAHAPAN <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="transition: transform 0.3s;"><path d="M6 9l6 6 6-6"/></svg>
        </button>
    </div>

    <!-- Legends (Collapsible) -->
    <div id="timelineLegend" style="display: none; background: #fdfcfb; border: 1px dashed var(--border); padding: 18px; border-radius: 10px; margin-bottom: 20px; animation: slideDown 0.3s ease-out;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; font-family: 'DM Sans', sans-serif;">
            <?php foreach($timelineSteps as $st): ?>
            <div style="font-size: 12px; display: flex; align-items: center; gap: 10px; color: #555;">
                <span style="background: var(--gold); color: white; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 10px; font-weight: 900; flex-shrink: 0;"><?= $st['sort_order'] ?></span>
                <span style="font-weight: 600;"><?= htmlspecialchars($st['label']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Timeline Grid -->
    <div style="margin-bottom: 20px;">
        <span style="display: flex; align-items: center; gap: 6px; font-size: 10px; font-weight: 800; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; font-family: 'DM Sans', sans-serif;">
            📊 PROGRES TIMELINE PER TAHAP
        </span>
        <div style="display: flex; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; background: #fff; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
            <?php 
                $i = 1;
                $total = count($timelineSteps);
                foreach($timelineSteps as $k => $st):
                    $sla = (int)$st['sla_days'];
                    if (isset($time_in_steps[$st['id']])) {
                        $spentDays = ceil($time_in_steps[$st['id']] / 86400);
                        $spentDays = max(1, $spentDays); 
                        $isDanger = $spentDays > $sla;
                        $boxBg = $isDanger ? '#fff5f5' : '#f0fff4';
                        $boxText = $isDanger ? '#e53e3e' : '#2f855a';
                        $valDesc = $spentDays . ' Hari';
                    } else {
                        $boxBg = '#fff5f5'; $boxText = '#e53e3e'; $valDesc = '-';
                    }
                    $borderRight = ($k === $total - 1) ? '' : 'border-right: 1px solid var(--border);';
            ?>
            <div style="<?= $borderRight ?> flex: 1; text-align: center; display: flex; flex-direction: column; min-width: 80px;">
                <div style="background: #f8f9fa; border-bottom: 1px solid var(--border); font-size: 10px; font-weight: 800; color: #666; padding: 10px 5px; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'DM Sans', sans-serif;">TAHAP <?= $i++ ?></div>
                <div style="background: <?= $boxBg ?>; padding: 16px 5px; font-size: 15px; font-weight: 800; color: <?= $boxText ?>; flex: 1; display: flex; align-items: center; justify-content: center; font-family: 'DM Sans', sans-serif;">
                    <?= $valDesc ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Financial Grid -->
    <?php
    $totalTagihanValue = (float)($ps['total_tagihan'] ?? 0);
    $totalMasukValue = 0;
    if (!empty($pRiwayat)) {
        foreach ($pRiwayat as $r) {
            $totalMasukValue += (float)($r['nominal_bayar'] ?? 0);
        }
    }
    $sisaValue = $totalTagihanValue - $totalMasukValue;
    $isLunasValue = ($sisaValue <= 0 && $totalTagihanValue > 0);
    
    // Status semantic colors
    $statusColor = $isLunasValue ? '#2e7d32' : '#c62828';
    $statusBg = $isLunasValue ? '#e8f5e9' : '#fff5f5';
    ?>
    <div>
        <span style="display: flex; align-items: center; gap: 6px; font-size: 10px; font-weight: 800; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; font-family: 'DM Sans', sans-serif;">
            💰 STATUS KEUANGAN & RIWAYAT (AUDIT)
        </span>
        <div style="display: grid; grid-template-columns: 140px 1fr 1fr 1fr 140px; gap: 12px; align-items: stretch; font-family: 'DM Sans', sans-serif;">
            <!-- Status Badge (Semantic Color) -->
            <div style="background: <?= $statusBg ?>; border-radius: 12px; border: 1px solid <?= $statusColor ?>33; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px;">
                <div style="font-size: 9px; font-weight: 800; color: <?= $statusColor ?>; opacity: 0.7; margin-bottom: 4px; text-transform: uppercase;">STATUS</div>
                <div style="font-size: 11px; font-weight: 900; color: <?= $statusColor ?>; text-transform: uppercase;"><?= $isLunasValue ? 'LUNAS' : 'BELUM LUNAS' ?></div>
            </div>

            <!-- Tagihan (Cream) -->
            <div style="background: #fffcf5; border-radius: 12px; border: 1px solid rgba(197, 163, 101, 0.15); padding: 12px 18px; display: flex; flex-direction: column; justify-content: center;">
                <div style="font-size: 9px; font-weight: 800; color: var(--gold); margin-bottom: 4px; text-transform: uppercase;">TAGIHAN</div>
                <div style="font-size: 16px; font-weight: 800; color: var(--primary);">Rp <?= number_format($totalTagihanValue, 0, ',', '.') ?></div>
            </div>

            <!-- Terbayar (Synced - Cream) -->
            <div style="background: #fffcf5; border-radius: 12px; border: 1px solid rgba(197, 163, 101, 0.15); padding: 12px 18px; display: flex; flex-direction: column; justify-content: center;">
                <div style="font-size: 9px; font-weight: 800; color: #888; margin-bottom: 4px; text-transform: uppercase;">TERBAYAR</div>
                <div style="font-size: 16px; font-weight: 800; color: #2e7d32; font-family: 'DM Sans', sans-serif;">Rp <?= number_format($totalMasukValue, 0, ',', '.') ?></div>
            </div>

            <!-- Sisa (Synced - Cream) -->
            <div style="background: #fffcf5; border-radius: 12px; border: 1px solid rgba(197, 163, 101, 0.15); padding: 12px 18px; display: flex; flex-direction: column; justify-content: center;">
                <div style="font-size: 9px; font-weight: 800; color: #888; margin-bottom: 4px; text-transform: uppercase;">SISA</div>
                <div style="font-size: 16px; font-weight: 800; color: #c62828; font-family: 'DM Sans', sans-serif;">Rp <?= number_format(max(0, $sisaValue), 0, ',', '.') ?></div>
            </div>

            <!-- Riwayat Toggle -->
            <button type="button" onclick="const b=document.getElementById('pembayaranRiwayatFull'); const s=b.style.display==='none'; b.style.display=s?'block':'none'; this.querySelector('svg').style.transform=s?'rotate(180deg)':''; " style="background: #1a3646; border: none; border-radius: 12px; color: white; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(26, 54, 70, 0.2); font-family: 'DM Sans', sans-serif;">
                <div style="font-size: 11px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;">RIWAYAT</div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" style="margin-top:6px; transition: transform 0.3s;"><path d="M6 9l6 6 6-6"/></svg>
            </button>
        </div>
    </div>

    <!-- Inline Riwayat Box (Calculated from DB) -->
    <div id="pembayaranRiwayatFull" style="display: none; margin-top: 24px; border-top: 1px dashed var(--border); padding-top: 20px; animation: fadeIn 0.4s ease-out;">
         <span style="display: flex; align-items: center; gap: 8px; font-size: 10px; font-weight: 800; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; font-family: 'DM Sans', sans-serif;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            DETIL TRANSAKSI MASUK
         </span>
         <div style="border: 1px solid var(--border); border-radius: 12px; overflow: hidden; font-family: 'DM Sans', sans-serif;">
             <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #fdfcfb; color: var(--primary);">
                        <th style="padding: 15px; border-bottom: 1px solid var(--border); text-align: left; font-weight: 800; font-size: 11px;">TANGGAL</th>
                        <th style="padding: 15px; border-bottom: 1px solid var(--border); text-align: left; font-weight: 800; font-size: 11px;">DITAMBAH OLEH</th>
                        <th style="padding: 15px; border-bottom: 1px solid var(--border); text-align: left; font-weight: 800; font-size: 11px;">KETERANGAN</th>
                        <th style="padding: 15px; border-bottom: 1px solid var(--border); text-align: right; font-weight: 800; font-size: 11px;">NOMINAL</th>
                    </tr>
                </thead>
                <tbody style="background: #fff;">
                    <?php if (empty($pRiwayat)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 30px; color: #bbb; font-weight: 600; font-style: italic;">Belum ada riwayat pembayaran yang tercatat.</td></tr>
                    <?php else: ?>
                        <?php 
                        $totalMasuk = 0;
                        foreach ($pRiwayat as $r): 
                            $totalMasuk += (float)($r['nominal_bayar'] ?? 0);
                        ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 15px; color: #444; font-weight: 500; font-size: 12px;"><?= date('d/m/Y', strtotime($r['tanggal_bayar'])) ?></td>
                                <td style="padding: 15px; color: var(--primary); font-weight: 700; font-size: 12px;"><?= htmlspecialchars($r['name'] ?: $r['username'] ?: '-') ?></td>
                                <td style="padding: 15px; color: #666; font-size: 12px;"><?= htmlspecialchars($r['catatan'] ?? '-') ?></td>
                                <td style="padding: 15px; text-align: right; font-weight: 700; color: #2e7d32; font-size: 14px;">
                                    Rp <?= number_format($r['nominal_bayar'] ?? 0, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($pRiwayat)): ?>
                <tfoot style="background: var(--cream);">
                    <tr>
                        <td colspan="3" style="padding: 15px; text-align: right; font-weight: 800; color: var(--primary); font-size: 11px; text-transform: uppercase;">Total Transaksi Terverifikasi</td>
                        <td style="padding: 15px; text-align: right; font-weight: 900; color: var(--primary); font-size: 15px;">
                            Rp <?= number_format($totalMasuk, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
         </div>
    </div>
</div>

<style>
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
