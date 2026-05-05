-- ============================================================
-- Migration 001: Sistem Transaksi Pembayaran
-- Tanggal: 2026-04-11
-- Deskripsi: Tambah tabel transaksi + transaksi_history
-- ============================================================

-- 1. Tabel transaksi (1 per registrasi)
CREATE TABLE IF NOT EXISTS `transaksi` (
    `id`              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `registrasi_id`   INT(10) UNSIGNED NOT NULL,
    `total_tagihan`   DECIMAL(15,2) NOT NULL DEFAULT 0,
    `jumlah_bayar`    DECIMAL(15,2) NOT NULL DEFAULT 0,
    `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unik_registrasi` (`registrasi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel transaksi_history (banyak per registrasi — audit trail cicilan)
CREATE TABLE IF NOT EXISTS `transaksi_history` (
    `id`            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `transaksi_id`  INT(10) UNSIGNED NOT NULL,
    `nominal_bayar` DECIMAL(15,2) NOT NULL,
    `tanggal_bayar` DATE NOT NULL,
    `catatan`       TEXT,
    `created_by`    INT(10) UNSIGNED DEFAULT NULL,
    `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_transaksi` (`transaksi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Index buat laporan bulanan (SLA nanti)
-- Note: Abaikan error jika index sudah ada
ALTER TABLE `registrasi_history`
    ADD INDEX `idx_created_at` (`created_at`);
