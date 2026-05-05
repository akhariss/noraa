-- ============================================================
-- Cleanup Script: Reset Data Transaksi yang Corrupt/Absurd
-- Tanggal: 2026-04-12
-- WARNING: Jalankan HANYA jika ada data dengan angka tidak realistis
-- ============================================================

-- 1. Cek data yang suspicious (lebih dari 1 triliun)
SELECT 
    t.id,
    t.registrasi_id,
    r.nomor_registrasi,
    t.total_tagihan,
    t.jumlah_bayar,
    (SELECT COUNT(*) FROM transaksi_history th WHERE th.transaksi_id = t.id) as history_count
FROM transaksi t
LEFT JOIN registrasi r ON r.id = t.registrasi_id
WHERE t.total_tagihan > 999999999999.99
   OR t.jumlah_bayar > 999999999999.99;

-- 2. Jika yakin data corrupt, reset ke 0
-- Uncomment untuk eksekusi:
/*
UPDATE transaksi t
LEFT JOIN registrasi r ON r.id = t.registrasi_id
SET t.total_tagihan = 0, t.jumlah_bayar = 0
WHERE t.total_tagihan > 999999999999.99
   OR t.jumlah_bayar > 999999999999.99;
*/

-- 3. Hapus history yang nominalnya absurd
-- Uncomment untuk eksekusi:
/*
DELETE th FROM transaksi_history th
JOIN transaksi t ON t.id = th.transaksi_id
WHERE th.nominal_bayar > 999999999999.99
   OR th.nominal_bayar < -999999999999.99;
*/

-- 4. Verifikasi setelah cleanup
SELECT 
    COUNT(*) as total_transaksi,
    SUM(CASE WHEN total_tagihan > 0 THEN 1 ELSE 0 END) as dengan_tagihan,
    MAX(total_tagihan) as max_tagihan,
    MAX(jumlah_bayar) as max_bayar
FROM transaksi;
