-- ============================================================
-- Migration 002b: Fix Existing Tables (jika sudah create sebelumnya)
-- Tanggal: 2026-04-12
-- Deskripsi: Ubah tipe data INT(11) → INT(10) UNSIGNED
-- HANYA JALANKAN JIKA MIGRATION 002 GAGAL KARENA TIPE DATA
-- ============================================================

-- 1. Ubah tipe data kolom di tabel transaksi
ALTER TABLE `transaksi`
    MODIFY COLUMN `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    MODIFY COLUMN `registrasi_id` INT(10) UNSIGNED NOT NULL;

-- 2. Ubah tipe data kolom di tabel transaksi_history
ALTER TABLE `transaksi_history`
    MODIFY COLUMN `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    MODIFY COLUMN `transaksi_id` INT(10) UNSIGNED NOT NULL,
    MODIFY COLUMN `created_by` INT(10) UNSIGNED DEFAULT NULL;

-- 3. Sekarang coba tambah FOREIGN KEY lagi
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
