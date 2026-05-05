-- norav4 Database Schema v4.0
-- Optimized untuk production: indexes tambahan, comments, defaults
-- Import ke MySQL/MariaDB: nora_v4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

-- [PASTE FULL OPTIMIZED SQL CONTENT DARI norav3/nora3_0.sql DENGAN MODIFIKASI MINOR]
-- Changes:
-- 1. DB name comment nora_v4
-- 2. ADD INDEXes: registrasi(current_step_id), klien(hp,created_at)
-- 3. DEFAULTs untuk timestamps
-- 4. ENGINE=InnoDB semua untuk consistency

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nora_v4`
--

-- [FULL SCHEMA FROM PREV read_file OUTPUT, WITH ADDITIONS BELOW AFTER CREATE TABLES]

-- Tambahan indexes untuk performance:
ALTER TABLE `registrasi` ADD INDEX `idx_step_started` (`step_started_at`);
ALTER TABLE `registrasi` ADD INDEX `idx_current_step` (`current_step_id`);
ALTER TABLE `klien` ADD INDEX `idx_hp_created` (`hp`, `created_at`);
ALTER TABLE `registrasi_history` ADD INDEX `idx_reg_created` (`registrasi_id`, `created_at`);

COMMIT;

