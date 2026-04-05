-- ============================================================================
-- MIGRATION SIMPLE VERSION: v2.1 - Add Lock Mechanism Columns
-- Database: norasblmupdate2
-- Date: 2026-04-05
-- 
-- ⚠️ INSTRUCTION: Copy-paste ini di PhpMyAdmin SQL tab atau MySQL client
-- ============================================================================

-- Step 1: Gunakan database yang benar
USE norasblmupdate3;

-- Step 2: Tambahkan kolom 'locked' (jika belum ada)
ALTER TABLE registrasi 
ADD COLUMN IF NOT EXISTS locked tinyint(1) DEFAULT 0 COMMENT 'Lock mechanism to prevent concurrent edits';

-- Step 3: Tambahkan kolom 'batal_flag' (jika belum ada)
ALTER TABLE registrasi 
ADD COLUMN IF NOT EXISTS batal_flag tinyint(1) DEFAULT 0 COMMENT 'Flag to indicate cancellation status';

-- Step 4: Verifikasi - lihat struktur tabel
DESCRIBE registrasi;

-- ============================================================================
-- DONE! Jika error tidak muncul dan 2 kolom baru muncul di output DESCRIBE,
-- migration berhasil.
-- ============================================================================
