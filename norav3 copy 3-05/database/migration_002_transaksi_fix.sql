-- ============================================================
-- Migration 002: Fix Transaksi - Constraints & Validation
-- Tanggal: 2026-04-12
-- Deskripsi: Tambah FOREIGN KEY, CHECK constraints, dan max limit
-- ============================================================

-- 1. Tambah FOREIGN KEY untuk data integrity
ALTER TABLE `transaksi`
    ADD CONSTRAINT `fk_transaksi_registrasi`
    FOREIGN KEY (`registrasi_id`) REFERENCES `registrasi`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `transaksi_history`
    ADD CONSTRAINT `fk_history_transaksi`
    FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `transaksi_history`
    ADD CONSTRAINT `fk_history_user`
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- 2. Tambah CHECK constraints (MySQL 8.0.16+)
-- Pastikan nilai tidak negatif
ALTER TABLE `transaksi`
    ADD CONSTRAINT `chk_total_tagihan_positive`
    CHECK (`total_tagihan` >= 0);

ALTER TABLE `transaksi`
    ADD CONSTRAINT `chk_jumlah_bayar_positive`
    CHECK (`jumlah_bayar` >= 0);

ALTER TABLE `transaksi_history`
    ADD CONSTRAINT `chk_nominal_bayar_range`
    CHECK (`nominal_bayar` BETWEEN -999999999999.99 AND 999999999999.99);

-- 3. (Opsional) Reset data yang corrupt/absurd
-- Uncomment jika perlu clean up data yang sudah rusak:
/*
UPDATE `transaksi`
SET `total_tagihan` = 0, `jumlah_bayar` = 0
WHERE `total_tagihan` > 999999999999.99
   OR `jumlah_bayar` > 999999999999.99;

DELETE FROM `transaksi_history`
WHERE `nominal_bayar` > 999999999999.99
   OR `nominal_bayar` < -999999999999.99;
*/
