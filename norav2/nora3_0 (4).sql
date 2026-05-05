-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2026 at 12:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nora3.0`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `role` enum('notaris','admin') NOT NULL,
  `action` varchar(50) NOT NULL,
  `new_value` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log_backup_20260226`
--

CREATE TABLE `audit_log_backup_20260226` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `perkara_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `role` enum('notaris','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cleanup_log`
--

CREATE TABLE `cleanup_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `rows_affected` int(10) UNSIGNED NOT NULL,
  `cleanup_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE `cms_pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_key` varchar(51) NOT NULL,
  `page_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_by` int(11) DEFAULT 1,
  `version` int(11) DEFAULT 1,
  `content_json` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `page_key`, `page_name`, `is_active`, `updated_by`, `version`, `content_json`, `updated_at`) VALUES
(1, 'home', 'Beranda Utama', 1, 1, 1, NULL, '2026-03-08 22:58:14');

-- --------------------------------------------------------

--
-- Table structure for table `cms_page_sections`
--

CREATE TABLE `cms_page_sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_id` int(10) UNSIGNED NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `section_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_page_sections`
--

INSERT INTO `cms_page_sections` (`id`, `page_id`, `section_key`, `section_name`, `section_order`, `is_active`, `updated_at`) VALUES
(1, 1, 'hero', 'Hero Section', 1, 1, '2026-03-08 22:58:14'),
(2, 1, 'masalah', 'Masalah Klien', 2, 1, '2026-03-08 22:58:14'),
(3, 1, 'layanan', 'Layanan Kami', 3, 1, '2026-03-08 22:58:14'),
(4, 1, 'testimoni', 'Apa Kata Mereka', 4, 1, '2026-03-08 22:58:14'),
(5, 1, 'alur', 'Cara Kerja', 5, 1, '2026-03-08 22:58:14'),
(6, 1, 'tentang', 'Tentang Notaris', 6, 1, '2026-03-08 22:58:14'),
(7, 1, 'cta', 'Hubungi Sekarang', 7, 1, '2026-03-08 22:58:14'),
(8, 1, 'footer', 'Kontak & Footer', 8, 1, '2026-03-08 22:58:14');

-- --------------------------------------------------------

--
-- Table structure for table `cms_section_content`
--

CREATE TABLE `cms_section_content` (
  `id` int(10) UNSIGNED NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `content_key` varchar(50) NOT NULL,
  `content_value` text DEFAULT NULL,
  `content_type` varchar(20) DEFAULT 'text',
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_section_content`
--

INSERT INTO `cms_section_content` (`id`, `section_id`, `content_key`, `content_value`, `content_type`, `sort_order`) VALUES
(4, 1, 'description', 'Pembuatan akta, pengurusan tanah, dan legalisasi dokumen dengan proses yang jelas dan sesuai ketentuan hukum.', 'text', 0),
(5, 2, 'title', 'sepertinya Apakah Anda Mengalami Hal Ini?', 'text', 0),
(6, 2, 'closing', 'Kantor Notaris Sri Anah SH.M.Kn hadir untuk memastikan setiap proses aman, jelas, dan sesuai hukum.', 'text', 0),
(7, 3, 'title', 'layanan kami', 'text', 0),
(8, 4, 'title', 'Apa Kata Klien Kami?                ', 'text', 0),
(9, 4, 'cta', 'Ingin berkonsultasi juga? Klik tombol WhatsApp di bawah ya!', 'text', 0),
(10, 5, 'title', 'Cara Kerja Kami                ', 'text', 0),
(11, 6, 'title', 'Profesionalitas & Integritas', 'text', 1),
(12, 6, 'quote', 'Menangani pembuatan akta notaris, akta tanah (PPAT), dan kebutuhan hukum lainnya secara tepat, jelas, dan sesuai ketentuan yang berlaku.', 'text', 2),
(13, 6, 'name', 'Sri Anah SH.M.Kn', 'text', 3),
(14, 6, 'role', 'Notaris & PPAT', 'text', 4),
(15, 6, 'experience', '15+', 'text', 5),
(16, 7, 'title', 'Siap Melayani Anda', 'text', 0),
(17, 7, 'description', 'Konsultasikan kebutuhan hukum Anda sekarang juga melalui WhatsApp', 'text', 0),
(18, 8, 'brand', 'Notaris Sri Anah SH.M.Kn', 'text', 0),
(19, 8, 'description', 'Layanan notaris dan PPAT untuk properti, usaha, dan kebutuhan hukum lainnya di Cirebon dan sekitarnya.', 'text', 0),
(20, 8, 'address', 'Jl. Sultan Ageng Tirtayasa No. 123, Kedawung, Cirebon, jawa barat.', 'text', 0),
(21, 8, 'phone', '+62 877-4877-8882', 'text', 0),
(22, 8, 'email', 'notaris.srianah@gmail.com', 'text', 0),
(23, 8, 'work_days', 'Senin - Jumat', 'text', 0),
(24, 8, 'work_hours', '08:00 - 16:00', 'text', 0),
(25, 8, 'work_days_sat', 'Sabtu', 'text', 0),
(26, 8, 'work_hours_sat', '09:00 - 12:00', 'text', 0),
(28, 1, 'badge', 'Notaris & PPAT Cirebon – Tengah Tani dan Sekitarnya aja', 'text', 0),
(29, 1, 'title', 'Layanan Notaris & PPAT untuk Akta, Tanah, dan Usaha', 'text', 0),
(30, 1, 'subtitle', 'Melayani kebutuhan notaris dan PPAT di Cirebon, Kedawung, Tengah Tani, dan sekitarnya.', 'text', 0),
(31, 1, 'wa_number', '6285747898811', 'text', 0),
(32, 1, 'wa_text', 'Konsultasi via WhatsApp', 'text', 0),
(33, 1, 'tracking_label', 'Lacak Perkara', 'text', 0),
(34, 1, 'tracking_sub', 'Cek status dokumen Anda', 'text', 0),
(35, 1, 'contact_label', 'Hubungi Kami', 'text', 0),
(36, 1, 'contact_sub', 'Respons cepat hari ini', 'text', 0),
(37, 4, 'cta_text', 'Ingin berkonsultasikan juga? Klik tombol WhatsApp di bawah ya!', 'text', 0),
(38, 4, 'cta_btn_text', 'Chat WhatsApp', 'text', 0),
(39, 8, 'operational_hours_full', 'Senin - Jumat: 08:00 - 16:00 | Sabtu: 08:00 - 12:00', 'text', 7),
(40, 1, 'work_days', 'Senin - Jumat', 'text', 8),
(41, 1, 'work_hours', '08:00 - 16:00', 'text', 9),
(42, 1, 'work_days_sat', 'Sabtu', 'text', 10),
(43, 1, 'work_hours_sat', '08:00 - 12:00', 'text', 11),
(44, 8, 'operational_hours_full', 'Senin - Jumat: 08:00 - 16:00 | Sabtu: 08:00 - 12:00', 'text', 7),
(49, 6, 'photo', 'image.php?id=v41JEEHgjGGoiAgvCGE5IzdwSFFIMmlMRW85Uy9oRjUrU09PQ3NqTTZLazQ4eWVObEM0a2xXKzl3bmNFblNKZGs5YnEvRzVMYm9Sa2xpczE%3D', 'image', 0),
(52, 8, 'copyright_text', '© 2026 Notaris Sri Anah SH.M.Kn. Hak Cipta Dilindungi.', 'text', 8);

-- --------------------------------------------------------

--
-- Table structure for table `cms_section_items`
--

CREATE TABLE `cms_section_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `extra_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra_data`)),
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_section_items`
--

INSERT INTO `cms_section_items` (`id`, `section_id`, `item_type`, `title`, `description`, `extra_data`, `sort_order`, `is_active`, `updated_at`) VALUES
(1, 1, 'button', 'Konsultasi via WhatsApp', 'No description', '{\"url\": \"https://wa.me/6285747898811\", \"style\": \"cta\", \"icon\": \"whatsapp\"}', 1, 1, '2026-03-09 16:24:47'),
(2, 1, 'button', 'Lihat Testimoni', NULL, '{\"url\": \"#testimoni\", \"style\": \"secondary\"}', 2, 1, '2026-03-08 22:58:14'),
(3, 2, 'card', 'Takut dokumen salah dan berujung sengketa\n                                                    ', 'Kesalahan kecil pada dokumen hukum dapat menimbulkan masalah besar di kemudian hari.', NULL, 1, 1, '2026-03-11 14:11:35'),
(4, 2, 'card', 'Bingung syarat dan prosedur hukum\n                                                    ', 'Persyaratan administrasi sering kali rumit dan tidak mudah dipahami.\n', NULL, 2, 1, '2026-03-11 13:18:25'),
(5, 2, 'card', 'Proses lama dan tidak transparan\n                                                    ', 'Klien sering tidak mengetahui tahapan proses yang sedang berjalan.\n', NULL, 3, 1, '2026-03-11 13:18:25'),
(6, 2, 'card', 'Sulit menghubungi notaris saat butuh cepat', 'Respons lambat saat kondisi mendesak', NULL, 4, 1, '2026-03-08 22:58:14'),
(7, 3, 'card', 'Akta Properti', 'Untuk jual beli rumah, tanah, dan pengalihan hak', '{\"benefits\": [\"Aman secara hukum\", \"Menghindari sengketa\"]}', 1, 1, '2026-03-08 22:58:14'),
(8, 3, 'card', 'Pendirian Usaha', 'PT, CV, Yayasan, dan perubahan anggaran dasar', '{\"benefits\": [\"Legalitas lengkap\", \"Siap operasional\"]}', 2, 1, '2026-03-24 08:25:01'),
(9, 3, 'card', 'Proses Legalisasi', 'Legalisasi dokumen dan pengesahan tanda tangan', '{\"benefits\": [\"Diterima semua lembaga\"]}', 3, 1, '2026-03-09 00:31:22'),
(10, 3, 'card', 'Akta Waris & Hibah', 'Pembuatan akta wasiat, waris, serta hibah.\n', '{\"benefits\": [\"Aman untuk keluarga\"]}', 4, 1, '2026-03-11 13:24:33'),
(11, 3, 'card', 'Layanan PPAT', 'Pembuatan akta tanah dan perbuatan hukum lainnya', '{\"benefits\": [\"Terdaftar resmi\"]}', 5, 1, '2026-03-24 08:25:01'),
(12, 3, 'card', 'Konsultasi Hukum', 'Untuk kebutuhan hukum personal atau bisnis Anda', '{\"has_button\": true, \"button_label\": \"Hubungi Kami\"}', 6, 1, '2026-03-08 22:58:14'),
(20, 7, 'button', 'Hubungi via WhatsApp', NULL, '{\"url\": \"https://wa.me/6285747898811\", \"style\": \"cta\", \"icon\": \"whatsapp\"}', 1, 1, '2026-03-08 22:58:15'),
(21, 4, 'testimonial', 'Swarta Sharia Property', 'Proses cepat dan transparan. Semua tahapan dijelaskan dengan jelas. Sangat direkomendasikan untuk kebutuhan notaris di Cirebon.', '{\"role\": \"Klien Bisnis\", \"avatar\": \"B\", \"rating\": 5}', 1, 1, '2026-03-24 08:27:18'),
(22, 4, 'testimonial', 'Siti Rahayu', 'Rekomendasi PPAT di Cirebon. Tim responsif dan membantu. Dokumen akta PT selesai tepat waktu.', '{\"role\": \"Pendirian PT\", \"avatar\": \"S\", \"rating\": 5}', 2, 1, '2026-03-24 08:27:18'),
(23, 4, 'testimonial', 'Ahmad Fauzi', 'Alhamdullillah semua berjalan Lancar. Tidak perlu bolak-balik, cukup ikuti petunjuk dari tim. Sangat membantu!', '{\"role\": \"Legalisasi Dokumen\", \"avatar\": \"A\", \"rating\": 5}', 3, 1, '2026-03-09 00:27:16'),
(24, 5, 'step', 'Konsultasi', 'Konsultasi terkait permasalahan', NULL, 1, 1, '2026-03-09 00:33:04'),
(25, 5, 'step', 'Analisis', 'Kami cek kelengkapan dokumen', NULL, 2, 1, '2026-03-09 00:27:16'),
(26, 5, 'step', 'Proses\n                                                    ', 'Pembuatan akta dimulai', NULL, 3, 1, '2026-03-11 14:06:57'),
(27, 5, 'step', 'Verifikasi', 'Pengecekan akhir & validasi', NULL, 4, 1, '2026-03-09 00:27:16'),
(28, 5, 'step', 'Selesai', 'Dokumen siap diambil                        ', NULL, 5, 1, '2026-03-11 14:07:24'),
(29, 6, 'benefit', 'Berizin resmi sebagai Notaris & PPAT                            ', NULL, NULL, 1, 1, '2026-03-11 14:08:00'),
(30, 6, 'benefit', 'Pengalaman lebih dari 15 tahun                            ', NULL, NULL, 2, 1, '2026-03-11 13:28:17'),
(31, 6, 'benefit', 'Tim profesional & berpengalaman                            ', NULL, NULL, 3, 1, '2026-03-11 13:28:17'),
(32, 6, 'benefit', 'Proses yang jelas dan transparan', NULL, NULL, 4, 1, '2026-03-24 08:26:52'),
(33, 8, 'link', 'Lacak Perkara', NULL, '{\"url\": \"index.php?gate=lacak\"}', 1, 1, '2026-03-09 01:04:03'),
(34, 8, 'link', 'Hubungi Kami', NULL, '{\"url\": \"https://wa.me/6285747898811\"}', 2, 1, '2026-03-09 01:04:03'),
(35, 8, 'link', 'Testimoni', NULL, '{\"url\": \"#testimoni\"}', 3, 1, '2026-03-09 01:04:03'),
(36, 8, 'quick_link', '\n                                Lacak Registrasi', 'Cek status dokumen Anda', '{\"url\": \"/index.php?gate=lacak\", \"icon\": \"search\"}', 1, 1, '2026-03-11 13:24:35'),
(37, 8, 'quick_link', '\n                                \n                                Hubungi Kami', '', '{\"url\": \"https://wa.me/6285747898811\", \"icon\": \"whatsapp\"}', 2, 1, '2026-03-11 13:24:35'),
(38, 8, 'quick_link', '\n                                \n                                Testimoni', '', '{\"url\": \"#testimoni\", \"icon\": \"star\"}', 3, 1, '2026-03-11 13:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `kendala`
--

CREATE TABLE `kendala` (
  `id` int(10) UNSIGNED NOT NULL,
  `registrasi_id` int(10) UNSIGNED NOT NULL,
  `workflow_step_id` int(11) DEFAULT NULL,
  `flag_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `klien`
--

CREATE TABLE `klien` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `hp` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `klien`
--

INSERT INTO `klien` (`id`, `nama`, `hp`, `email`, `created_at`, `updated_at`) VALUES
(1, 'kenzy', '098765432', '', '2026-02-24 13:07:24', '2026-02-24 13:07:24'),
(2, 'adasfas3', '42312e12e12', '', '2026-02-24 14:59:45', '2026-02-24 14:59:45'),
(3, 'akharis nyoba in aja', '087748778885', NULL, '2026-02-24 15:20:36', '2026-03-25 05:39:19'),
(4, 'ujicoba', '087748778884', NULL, '2026-03-02 05:44:43', '2026-03-02 05:44:43'),
(5, 'kenzylkajks,naasj,cm.a,', '087748778886', NULL, '2026-03-11 17:55:28', '2026-03-13 00:36:18'),
(6, 'kenzylkajks,naasj,cm.a,', '0987y6tqyu', NULL, '2026-03-13 02:30:12', '2026-03-13 02:30:12'),
(7, 'gjvhmbj', 'hvnbm', NULL, '2026-03-13 02:38:10', '2026-03-13 02:38:10'),
(8, 'kenzylkajks,na', 'jlnk.', NULL, '2026-03-13 03:04:27', '2026-03-13 03:04:27'),
(9, 'yikbujl', 'g jhkj', NULL, '2026-03-13 03:05:18', '2026-03-13 03:05:18'),
(10, 'Test Klien 1773372273', '081234567890', NULL, '2026-03-13 03:24:33', '2026-03-13 03:24:33'),
(11, 'inimah ngecek ajaaaaaaa yak', '08774877888582', NULL, '2026-03-26 03:43:24', '2026-03-26 03:43:24'),
(12, 'inimah ngecek ajaaaaaaa yak ini', '08774877888582', NULL, '2026-03-26 03:52:06', '2026-03-26 03:52:06'),
(13, 'inimah ngecek ajaaaaaaa yak ini serius', '08774877888582', NULL, '2026-03-27 02:34:54', '2026-03-27 02:34:54'),
(14, 'inimah ngecek ajaaaaaaa yak ini serius ini sih ngecek', '08774877888582', NULL, '2026-03-28 14:33:50', '2026-03-28 14:33:50'),
(15, 'ah eror ini', '08774877888582', NULL, '2026-03-28 14:34:12', '2026-03-28 14:34:12'),
(16, 'ah eror ini', '08774877888582', NULL, '2026-03-28 14:34:33', '2026-03-28 14:34:33'),
(17, 'ah eror ini pastinya', '08774877888582', NULL, '2026-03-28 14:38:52', '2026-03-28 14:38:52'),
(18, 'kenzylkajks,na', '087748778885', NULL, '2026-03-28 16:30:58', '2026-03-28 16:30:58'),
(19, 'akharis nyoba in update', '087748778885', NULL, '2026-03-28 16:53:39', '2026-03-28 16:53:39'),
(20, 'akharis nyoba in update sla', '087748778885', NULL, '2026-03-29 08:31:44', '2026-03-29 08:31:44'),
(21, 'jkljLkjlnM', 'olNKM', NULL, '2026-03-29 13:49:40', '2026-03-29 13:49:40'),
(22, 'jkljLkjlnM', 'olNKM', NULL, '2026-03-29 14:28:21', '2026-03-29 14:28:21'),
(23, 'mencobaa', '087748778885', NULL, '2026-03-30 06:21:16', '2026-03-30 06:21:16'),
(24, 'mencobaa', '087748778885', NULL, '2026-03-30 06:21:20', '2026-03-30 06:21:20'),
(25, 'mencobaa', '087748778885', NULL, '2026-03-30 06:21:33', '2026-03-30 06:21:33'),
(26, 'mencobaa lagi', '087748778885', NULL, '2026-03-30 06:21:42', '2026-03-30 06:21:42'),
(27, 'mencobaa update', '087748778885', NULL, '2026-03-30 07:20:16', '2026-03-30 07:20:16'),
(28, 'mencobaa update kesekian', '087748778885', NULL, '2026-03-30 15:22:23', '2026-03-30 15:22:23'),
(29, 'ini oengecekan ', 'k', NULL, '2026-04-04 09:37:59', '2026-04-04 09:37:59'),
(30, 'ini oengecekan ti', 'k', NULL, '2026-04-04 11:39:19', '2026-04-04 11:39:19'),
(31, 'ini oengecekan ti', 'k', NULL, '2026-04-05 07:58:45', '2026-04-05 07:58:45'),
(32, 'ini oengecekan qwen', 'knoia;l', NULL, '2026-04-05 12:50:39', '2026-04-05 12:50:39'),
(33, 'kharis', '087748778885', NULL, '2026-04-10 06:57:10', '2026-04-10 06:57:10'),
(34, 'suhadi', '1234', NULL, '2026-04-10 08:11:53', '2026-04-10 08:11:53'),
(35, 'suhadi', '1234', NULL, '2026-04-10 08:16:37', '2026-04-10 08:16:37'),
(36, 'percoaaan', '1234', NULL, '2026-04-10 16:29:09', '2026-04-10 16:29:09'),
(37, 'percoaaan', '1234', NULL, '2026-04-11 11:02:10', '2026-04-11 11:02:10'),
(38, 'percoaaanaaasasf', '1234', NULL, '2026-04-11 11:32:19', '2026-04-11 11:32:19'),
(39, 'percoaaanaaasasf', '1234', NULL, '2026-04-11 13:36:19', '2026-04-11 13:36:19'),
(40, 'percoaaanaaasasf', '1234', NULL, '2026-04-12 07:27:47', '2026-04-12 07:27:47'),
(41, 'percoaaanaaasasf', '1234', NULL, '2026-04-12 07:48:03', '2026-04-12 07:48:03'),
(42, 'percoaaanaaasasf', '1234', NULL, '2026-04-12 08:12:03', '2026-04-12 08:12:03'),
(43, 'percoaaanaaasasf', '1234', NULL, '2026-04-12 08:42:54', '2026-04-12 08:42:54'),
(44, 'kharis', '1234876', NULL, '2026-04-13 04:02:23', '2026-04-13 06:00:13'),
(45, 'percoaaan', '1234876', NULL, '2026-04-13 06:52:38', '2026-04-18 11:40:05'),
(46, 'percoaaanaaasasf576890', '1234876', NULL, '2026-04-13 06:52:38', '2026-04-13 06:52:38'),
(47, 'tesssssss', '1235676543', NULL, '2026-04-13 07:49:00', '2026-04-13 07:49:00'),
(48, 'kenzylkajks,na', '087748778885', NULL, '2026-04-14 02:20:09', '2026-04-14 02:20:09'),
(49, 'kenzylkajks,na', '087748778885', NULL, '2026-04-16 17:45:20', '2026-04-16 17:45:20'),
(50, 'kenzy aja ya ini ni m', '765432111234', NULL, '2026-04-17 08:31:37', '2026-04-18 10:55:02'),
(51, 'kenzylkajks,na', '765432', NULL, '2026-04-17 08:33:10', '2026-04-17 08:33:10'),
(52, 'kenzylkajks,na', '087748778885', NULL, '2026-04-19 14:27:19', '2026-04-19 14:27:19'),
(53, 'akharis nyoba in update 1', '0976681', NULL, '2026-04-20 07:14:46', '2026-04-20 07:14:46'),
(54, 'akharis nyoba in update 1', '0976681', NULL, '2026-04-20 15:10:13', '2026-04-20 15:10:13'),
(55, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:16:05', '2026-04-20 15:16:05'),
(56, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:16:33', '2026-04-20 15:16:33'),
(57, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:17:40', '2026-04-20 15:17:40'),
(58, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:21:35', '2026-04-20 15:21:35'),
(59, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:24:33', '2026-04-20 15:24:33'),
(60, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:29:40', '2026-04-20 15:29:40'),
(61, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:30:02', '2026-04-20 15:30:02'),
(62, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:31:37', '2026-04-20 15:31:37'),
(63, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:33:22', '2026-04-20 15:33:22'),
(64, 'akharis nyoba in update 23', '087748778885', NULL, '2026-04-20 15:36:52', '2026-04-20 15:36:52'),
(65, '1e3wsf', '3133223299', NULL, '2026-04-20 15:41:36', '2026-04-20 15:43:58'),
(66, 'akaris', '31332232', NULL, '2026-04-20 15:46:00', '2026-04-20 16:07:16');

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_layanan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id`, `nama_layanan`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Lainnya', 'Layanan lainnya', '2026-02-24 11:49:44', '2026-03-11 16:26:10'),
(2, 'Hibah', 'Akta Hibah', '2026-02-24 11:49:44', '2026-02-25 03:25:23'),
(3, 'Waris', 'Akta Waris', '2026-02-24 11:49:44', '2026-02-25 03:25:33'),
(4, 'Pembagian Hak Bersama', 'Pembagian Hak Bersama', '2026-02-24 11:49:44', '2026-02-25 03:25:47'),
(5, 'Roya', 'Roya hipotik', '2026-02-24 11:49:44', '2026-02-25 03:25:59'),
(6, 'Jual Beli', 'Akta Jual Beli properti', '2026-02-24 11:49:44', '2026-03-11 16:26:03'),
(14, 'nikah', NULL, '2026-03-27 02:36:22', '2026-03-27 02:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `message_templates`
--

CREATE TABLE `message_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `template_key` varchar(50) NOT NULL COMMENT 'Unique key: wa_update, wa_create, etc.',
  `template_name` varchar(100) NOT NULL COMMENT 'Display name',
  `template_body` text NOT NULL COMMENT 'Message body with variable placeholders',
  `description` varchar(255) DEFAULT NULL COMMENT 'Description of when this template is used',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `message_templates`
--

INSERT INTO `message_templates` (`id`, `template_key`, `template_name`, `template_body`, `description`, `updated_at`, `updated_by`) VALUES
(1, 'wa_update', 'WA - Pembaruan Status', 'konichiwa Bapak/Ibu {nama_klien},\r\n\r\nKami dari Kantor Notaris {nama_pengirim} menginformasikan status perkara Anda saat ini.\r\n\r\nDetail Perkara:\r\n??? Nomor Registrasi: {nomor_registrasi}\r\n??? Status Saat Ini: {status}\r\n\r\nAnda dapat memantau status dan progres perkara secara mandiri melalui tautan tracking yang telah kami berikan.\r\n\r\nApabila terdapat pertanyaan lebih lanjut, silakan menghubungi kami di:\r\n???? {phone}\r\n???? {alamat}\r\n\r\nTerima kasih atas kepercayaan Anda.\r\n\r\nHormat kami,\r\nKantor Notaris {nama_pengirim}', 'Template WA saat mengirim pembaruan status perkara dari halaman detail', '2026-03-12 12:39:42', 2),
(2, 'wa_create', 'WA - Perkara Baru Dibuat', 'ohayo Bapak/Ibu {nama_klien},\r\n\r\nKami dari Kantor Notaris {nama_pengirim} menginformasikan bahwa perkara Anda telah terdaftar.\r\n\r\nDetail Perkara:\r\nNomor Registrasi: {nomor_registrasi}\r\n??? Status: {status}\r\n\r\nAnda dapat memantau status dan progres perkara secara mandiri melalui tautan tracking yang telah kami berikan.\r\n\r\nApabila terdapat pertanyaan lebih lanjut, silakan menghubungi kami di:\r\n???? {phone}\r\n???? {alamat}\r\n\r\nTerima kasih atas kepercayaan Anda.\r\n\r\nHormat kami,\r\nKantor Notaris {nama_pengirim}', 'Template WA saat perkara baru berhasil dibuat', '2026-03-12 00:56:12', 2);

-- --------------------------------------------------------

--
-- Table structure for table `note_templates`
--

CREATE TABLE `note_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `workflow_step_id` int(11) NOT NULL,
  `template_body` text NOT NULL COMMENT 'Note body with variable placeholders',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `note_templates`
--

INSERT INTO `note_templates` (`id`, `workflow_step_id`, `template_body`, `updated_at`, `updated_by`) VALUES
(1, 1, 'sepertinya Perkara Anda {nama_klien} telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-13 14:48:29', 2),
(3, 3, 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]', '2026-03-30 11:39:33', NULL),
(4, 4, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', '2026-03-30 11:39:33', NULL),
(5, 5, 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', '2026-03-30 11:39:33', NULL),
(6, 6, 'Pembayaran pajak sedang dalam tahap pemeriksaan dan validasi oleh instansi terkait. [catatan]', '2026-03-30 11:39:33', NULL),
(7, 7, 'Akta sedang dalam proses penomoran sebagai bagian dari legalitas dokumen Anda. [catatan]', '2026-03-30 11:39:33', NULL),
(8, 8, 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang. [catatan]', '2026-03-30 11:39:33', NULL),
(10, 10, 'Berkas perkara sedang dalam tahap pemeriksaan oleh pihak BPN. [catatan]', '2026-03-30 11:39:33', NULL),
(11, 11, 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini', '2026-03-30 23:45:18', NULL),
(12, 12, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-03-30 11:39:33', NULL),
(13, 13, 'Berkas dengan nomor {nomor_registrasi} telah diterima oleh {penerima} pada tanggal {tanggal}', '2026-03-30 17:40:19', NULL),
(14, 14, 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk', '2026-03-30 23:39:00', NULL),
(15, 15, 'Perkara ini dinyatakan batal dan tidak dilanjutkan. [catatan]', '2026-03-30 11:39:33', NULL),
(16, 16, 'semua tahap telah terpenuhi dan dinyatakan selesai', '2026-04-18 21:41:15', 2);

-- --------------------------------------------------------

--
-- Table structure for table `registrasi`
--

CREATE TABLE `registrasi` (
  `id` int(10) UNSIGNED NOT NULL,
  `klien_id` int(10) UNSIGNED NOT NULL,
  `layanan_id` int(10) UNSIGNED NOT NULL,
  `nomor_registrasi` varchar(50) NOT NULL,
  `current_step_id` int(11) DEFAULT NULL,
  `step_started_at` datetime DEFAULT NULL,
  `target_completion_at` datetime DEFAULT NULL,
  `selesai_batal_at` datetime DEFAULT NULL,
  `final_at` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `verification_code` varchar(10) DEFAULT NULL,
  `tracking_token` varchar(255) DEFAULT NULL,
  `catatan_internal` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registrasi`
--

INSERT INTO `registrasi` (`id`, `klien_id`, `layanan_id`, `nomor_registrasi`, `current_step_id`, `step_started_at`, `target_completion_at`, `selesai_batal_at`, `final_at`, `keterangan`, `verification_code`, `tracking_token`, `catatan_internal`, `created_at`, `updated_at`) VALUES
(1, 33, 3, 'NP-20260410-4877', 16, '2026-04-10 23:29:25', '2026-06-10 23:59:59', NULL, '2026-04-10 15:15:00', 'berkas pemindahan hak waris', NULL, 'eyJpZCI6MSwiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzc1ODA0MjMwfQ==.8b3547e9c1c8dbd05078766459bc44680f65e999b796c32e8ecafcac41cb1e4d', 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk bro', '2026-04-10 06:57:10', '2026-04-20 16:53:01'),
(2, 34, 3, 'NP-20260410-9810', 14, '2026-04-13 11:00:18', '2026-06-10 23:59:59', NULL, '2026-04-10 15:13:34', 'ui', NULL, 'eyJpZCI6MiwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1ODA4NzEzfQ==.e17c8e617b61e7509b09e55e84581c744272915a039542439025628adb821a2e', 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk', '2026-04-10 08:11:53', '2026-04-19 13:00:47'),
(3, 35, 5, 'NP-20260410-1711', 1, '2026-04-10 15:16:37', '2026-06-10 23:59:59', NULL, NULL, 'aukla', NULL, 'eyJpZCI6MywiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1ODA4OTk3fQ==.7d032434d05d9fb2594db97fafb58f20cf7497a14b14cb42309078a8cc1fc2e1', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-10 08:16:37', '2026-04-10 08:16:37'),
(4, 36, 1, '', 1, '2026-04-10 23:29:09', '2026-06-10 23:59:59', NULL, NULL, 'ahkjls', NULL, 'eyJpZCI6NCwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1ODM4NTQ5fQ==.9ae25342a4e7320229a62470ddf6d6ed11566e64d7e9f9203799199ab0b1d809', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-10 16:29:09', '2026-04-11 10:57:08'),
(5, 37, 1, 'NP-20260411-7672', 12, '2026-04-11 18:12:45', '2026-06-11 23:59:59', NULL, NULL, 'xvcbn', NULL, 'eyJpZCI6NSwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTA1MzMwfQ==.2b66ae930a0ab7600c11d1c4d2bd738c893884d51c3d87206195bd6d4ea90bec', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-04-11 11:02:10', '2026-04-11 11:12:45'),
(6, 38, 2, 'NP-20260411-5310', 13, '2026-04-13 13:04:42', '2026-06-11 23:59:59', NULL, '2026-04-13 13:04:42', 'afsfae', NULL, 'eyJpZCI6NiwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTA3MTM5fQ==.75bf5ca0ce82a301f135ca85d623486d5849d224a6a3f772bf0347a2d6b0680e', 'Berkas dengan nomor NP-20260411-5310 telah diterima oleh 4567897 pada tanggal Monday, 13 April 2026', '2026-04-11 11:32:19', '2026-04-19 13:00:47'),
(7, 39, 1, 'NP-20260411-5624', 3, '2026-04-12 14:10:39', '2026-06-11 23:59:59', NULL, NULL, 'vhjkn', NULL, 'eyJpZCI6NywiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTE0NTc5fQ==.8d149a9a20a364cd52e6146656b40cbad1ee9d04714e874b38d6aa3a4da8a81b', 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]', '2026-04-11 13:36:19', '2026-04-12 07:10:39'),
(8, 40, 2, 'NP-20260412-2416', 1, '2026-04-12 14:27:47', '2026-06-12 23:59:59', NULL, NULL, 'ujnlkm;', NULL, 'eyJpZCI6OCwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTc4ODY3fQ==.47dbfe2362c19373bfdcdcd5fe5d18b4aab4b913aa15014996a6ba9d0a68d4c6', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-12 07:27:47', '2026-04-12 07:27:47'),
(9, 41, 3, 'NP-20260412-8930', 1, '2026-04-12 14:48:03', '2026-06-12 23:59:59', NULL, NULL, 'iuyhkjnlm/.nkjn', NULL, 'eyJpZCI6OSwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTgwMDg0fQ==.923852abbfa13ea416e19d0eb1c8279da91c944010d8224c2b4b219567f94010', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-12 07:48:03', '2026-04-12 07:48:04'),
(10, 42, 1, 'NP-20260412-4288', 1, '2026-04-12 15:12:03', '2026-06-12 23:59:59', NULL, NULL, 'ssccs', NULL, 'eyJpZCI6MTAsImNvZGUiOiIxMjM0IiwidGltZSI6MTc3NTk4MTUyM30=.5b1fa6833320d2df31cf54077ed283bb075f96c105f7f9f76d4c19f1e6d11fd3', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-12 08:12:03', '2026-04-12 08:12:03'),
(11, 43, 1, 'NP-20260412-0581', 1, '2026-04-12 15:42:54', '2026-06-12 23:59:59', NULL, NULL, 'sa', NULL, 'eyJpZCI6MTEsImNvZGUiOiIxMjM0IiwidGltZSI6MTc3NTk4MzM3NH0=.4bba217a9420a5a7b8f64cc2cdb96bedfa7b80f129c0b96cab60d124e74e53a3', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-12 08:42:54', '2026-04-12 08:42:54'),
(12, 44, 2, 'NP-20260413-7030', 12, '2026-04-13 13:06:37', '2026-06-13 23:59:59', NULL, NULL, 'g jhkj.kn,mjy', NULL, 'eyJpZCI6MTIsImNvZGUiOiI0ODc2IiwidGltZSI6MTc3NjA1Mjk0M30=.679a5ef3ffef7e629aec24780197efa6185df206b0ec29b2965e4843ed7a6bc6', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-04-13 04:02:23', '2026-04-13 06:06:37'),
(13, 45, 2, 'NP-20260413-6058', 12, '2026-04-19 21:13:01', '2026-06-13 23:59:59', '2026-04-19 21:13:01', NULL, 'rstg', NULL, 'eyJpZCI6MTMsImNvZGUiOiI0ODc2IiwidGltZSI6MTc3NjA2MzE1OH0=.981153ed344f7f608458130b251fa191383ac47c97f1a060ac19e62753099c32', 'semua tahap telah terpenuhi dan dinyatakan selesai dong', '2026-04-13 06:52:38', '2026-04-19 14:13:01'),
(14, 46, 2, 'NP-20260413-8444', 12, '2026-04-13 14:27:56', '2026-06-13 23:59:59', NULL, NULL, 'rstg', NULL, 'eyJpZCI6MTQsImNvZGUiOiI0ODc2IiwidGltZSI6MTc3NjA2MzE1OX0=.17eac98832332add1d4b9bb28066300b5da7a810fa73804f998514ee7975a897', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-04-13 06:52:39', '2026-04-13 07:27:56'),
(15, 47, 1, 'NP-20260413-2700', 1, '2026-04-13 14:49:00', '2026-06-13 23:59:59', NULL, NULL, 'dvtr', NULL, 'eyJpZCI6MTUsImNvZGUiOiI2NTQzIiwidGltZSI6MTc3NjA2NjU0MH0=.8e3970896b026ab98a1fae583136407fc8905b8bbfff132cab083b805c404569', 'sepertinya Perkara Anda tesssssss telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-13 07:49:00', '2026-04-13 07:49:00'),
(16, 48, 2, 'NP-20260414-8122', 12, '2026-04-19 18:33:19', '2026-04-16 23:59:59', '2026-04-19 18:33:19', NULL, NULL, NULL, 'eyJpZCI6MTYsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjEzMzIwOX0=.65f656ea1aa2d15cabd8054768d6a4f3c9d2b30e850434bb91413894eeaa339a', 'semua tahap telah terpenuhi dan dinyatakan selesai', '2026-04-14 02:20:09', '2026-04-19 11:33:19'),
(17, 49, 2, 'NP-20260417-2880', 15, '2026-04-18 18:47:57', '2026-06-13 23:59:59', '2026-04-18 18:47:57', NULL, 'sjuw', NULL, 'eyJpZCI6MTcsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjM2MTUyMH0=.c951b76036dcaddebb3e7ffc5abe42fefe4c1a9707fa53f7109843ec1f5a6519', 'Perkara ini dinyatakan batal dan tidak dilanjutkan. [catatan]', '2026-04-16 17:45:20', '2026-04-18 11:47:57'),
(18, 50, 2, 'NP-20260417-2527', 12, '2026-04-18 21:50:01', '2026-06-13 23:59:59', NULL, NULL, 'iii', NULL, 'eyJpZCI6MTgsImNvZGUiOiI1NDMyIiwidGltZSI6MTc3NjQxNDY5N30=.1b4ded5bebafad46b368ccbeeb5429b143b049192b17133c0a10e56a4dd94a05', 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk', '2026-04-17 08:31:37', '2026-04-18 14:50:01'),
(19, 51, 1, 'NP-20260417-0660', 13, '2026-04-19 18:34:00', '2026-06-13 23:59:59', NULL, '2026-04-19 18:34:00', NULL, NULL, 'eyJpZCI6MTksImNvZGUiOiI1NDMyIiwidGltZSI6MTc3NjQxNDc5MH0=.8d04f847bfe090e015af1c7734d086fb611532e72fff29504ddab298b098ac52', 'Berkas dengan nomor NP-20260417-0660 telah diterima oleh akharis pada tanggal Sunday, 19 April 2026', '2026-04-17 08:33:10', '2026-04-19 13:00:47'),
(20, 52, 5, 'NP-20260419-8476', 1, '2026-04-19 21:27:19', '2026-06-11 23:59:59', NULL, NULL, '', NULL, 'eyJpZCI6MjAsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjYwODgzOX0=.9ed87f616462c25ce6693a36b85d24a79539549e1aeadbe7dac17ff0d66d9ae0', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-19 14:27:19', '2026-04-19 14:27:56'),
(21, 53, 1, 'NP-20260420-5050', 3, '2026-04-20 14:16:57', '2026-06-12 23:59:59', NULL, NULL, '', NULL, 'eyJpZCI6MjEsImNvZGUiOiI2NjgxIiwidGltZSI6MTc3NjY2OTI4Nn0=.f57845b81bf9a5eb55c10671843a59d4efe9067f511e0ae0317e1ad865384d4a', 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan] ya inii', '2026-04-20 07:14:46', '2026-04-20 07:16:57'),
(22, 54, 1, 'NP-20260420-1173', 1, '2026-04-20 22:10:13', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MjIsImNvZGUiOiI2NjgxIiwidGltZSI6MTc3NjY5NzgxNH0=.719582d03424f30b1aa9090fa48c155c4f7f3f0afdac841a2827acc27c23fa7a', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:10:13', '2026-04-20 15:10:14'),
(23, 55, 2, 'NP-20260420-0269', 1, '2026-04-20 22:16:05', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MjMsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5ODE2NX0=.9a21251e52460dc6a0192cac477f9c2ce490f11b76e996956aec1e4ab374bf12', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:16:05', '2026-04-20 15:16:05'),
(24, 56, 2, 'NP-20260420-6063', 1, '2026-04-20 22:16:57', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MjQsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5ODE5M30=.7bf9d242c0e4bea24acbc41b8b3b3f5d79c3b5707f61f6cb7720f7f6ff3fd848', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:16:33', '2026-04-20 15:16:57'),
(25, 57, 1, 'NP-20260420-2573', 1, '2026-04-20 22:17:41', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MjUsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5ODI2MX0=.71c1f3d788ae8fab029ea3c245f91d42121c118846122eb53a5eaf8912b12af7', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:17:41', '2026-04-20 15:17:41'),
(26, 58, 1, 'NP-20260420-1396', 1, '2026-04-20 22:21:35', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MjYsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5ODQ5NX0=.d821d2f0d6f652a9c03720f6f7f99040f0119e348943c0e96c2823c092da0880', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:21:35', '2026-04-20 15:21:35'),
(27, 59, 1, 'NP-20260420-7057', 1, '2026-04-20 22:24:33', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MjcsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5ODY3M30=.d177219bca2b1c3769ad2396248b7ba041b330b6900f63373ba85cd49bad2d5a', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:24:33', '2026-04-20 15:24:33'),
(28, 60, 1, 'NP-20260420-1667', 1, '2026-04-20 22:29:40', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MjgsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5ODk4MH0=.0cda1f7c2a0e14c9c52012787edb867844b66f6b6e596531f192101d80442c47', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:29:40', '2026-04-20 15:29:40'),
(29, 61, 1, 'NP-20260420-9733', 1, '2026-04-20 22:30:02', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MjksImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5OTAwMn0=.3baea64955c76f5181e5aa8a4748dd35e1e19f4dc3eb441146fca4d50004a1e6', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:30:02', '2026-04-20 15:30:02'),
(30, 62, 2, 'NP-20260420-7178', 1, '2026-04-20 22:31:37', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MzAsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5OTA5N30=.37c6d7ed4338a267575d723df26d100f97acab6be7601dce8be0e4720c9cfa43', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:31:37', '2026-04-20 15:31:37'),
(31, 63, 6, 'NP-20260420-0566', 1, '2026-04-20 22:33:22', '2026-06-12 23:59:59', NULL, NULL, NULL, NULL, 'eyJpZCI6MzEsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5OTIwMn0=.b5fdc61a7495223e98bbcb2b2a23599d18ff738406572ca3b50ea3280ed656f1', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:33:22', '2026-04-20 15:33:22'),
(32, 64, 2, 'NP-20260420-1229', 16, '2026-04-20 23:18:15', '2026-06-12 23:59:59', NULL, NULL, '', NULL, 'eyJpZCI6MzIsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NjY5OTQxMn0=.3bd369ff64ac2f50dd2775da6b3e0e1a37ddf467a22a8cacd73691e06d2ec027', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:36:52', '2026-04-20 16:18:15'),
(33, 65, 2, 'NP-20260420-2181', 1, '2026-04-20 22:45:20', '2026-06-12 23:59:59', NULL, NULL, '', NULL, 'eyJpZCI6MzMsImNvZGUiOiIyMjMyIiwidGltZSI6MTc3NjY5OTY5Nn0=.95ad8959b5037bbdd139c0964e9f0e30df17e6ea90a0e3343e04adc03da10fd9', 'sepertinya Perkara Anda [Nama Klien] telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-20 15:41:36', '2026-04-20 15:45:20'),
(34, 66, 2, 'NP-20260420-9177', 10, '2026-04-21 00:11:23', '2026-06-12 23:59:59', NULL, NULL, '', NULL, 'eyJpZCI6MzQsImNvZGUiOiIyMjMyIiwidGltZSI6MTc3NjY5OTk2MH0=.c68d8eb420f87c25ab1cf0f9b28d6bf559d61a14534b7c13e12268cd73d1616a', 'Berkas perkara sedang dalam tahap pemeriksaan oleh pihak BPN. [catatan]', '2026-04-20 15:46:00', '2026-04-20 17:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `registrasi_history`
--

CREATE TABLE `registrasi_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `registrasi_id` int(10) UNSIGNED NOT NULL,
  `status_old_id` int(11) DEFAULT NULL,
  `status_new_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT 'Update',
  `target_completion_at_new` datetime DEFAULT NULL,
  `target_completion_at_old` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `flag_kendala_active` tinyint(1) DEFAULT 0,
  `flag_kendala_tahap` varchar(100) DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(10) UNSIGNED NOT NULL,
  `registrasi_id` int(10) UNSIGNED NOT NULL,
  `total_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jumlah_bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_history`
--

CREATE TABLE `transaksi_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaksi_id` int(10) UNSIGNED NOT NULL,
  `nominal_bayar` decimal(15,2) NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi_history`
--

INSERT INTO `transaksi_history` (`id`, `transaksi_id`, `nominal_bayar`, `tanggal_bayar`, `catatan`, `created_by`, `created_at`) VALUES
(1, 1, 1234231.00, '2026-04-11', 'pertama', 2, '2026-04-11 11:31:39'),
(2, 1, 1234231.00, '2026-04-11', 'pertama', 2, '2026-04-11 11:31:41'),
(3, 3, 4567.00, '2026-04-11', 'j', 2, '2026-04-11 13:36:38'),
(4, 3, 187656789.00, '2026-04-12', '', 2, '2026-04-12 07:07:13'),
(5, 5, 989987656.00, '2026-04-12', '', 2, '2026-04-12 07:28:23'),
(7, 6, 1000.00, '2026-04-12', '', 2, '2026-04-12 07:48:41'),
(8, 6, 1999.00, '2026-04-12', '', 2, '2026-04-12 07:49:00'),
(9, 6, 987654323.00, '2026-04-12', '', 2, '2026-04-12 07:49:19'),
(10, 8, 9798799789.00, '2026-04-12', '', 2, '2026-04-12 08:43:27'),
(11, 9, 577.00, '2026-04-13', '', 2, '2026-04-13 05:41:06'),
(12, 10, 32.00, '2026-04-13', '', 2, '2026-04-13 07:00:32'),
(13, 13, 98999.00, '2026-04-17', 'Pembayaran awal saat registrasi', 2, '2026-04-16 17:45:20'),
(14, 13, 87.00, '2026-04-17', '', 2, '2026-04-17 08:30:15'),
(15, 14, 10000.00, '2026-04-17', 'Pembayaran awal saat registrasi', 2, '2026-04-17 08:31:37'),
(16, 15, 1000.00, '2026-04-17', 'Pembayaran awal saat registrasi', 2, '2026-04-17 08:33:10'),
(17, 13, 70000.00, '2026-04-18', '', 2, '2026-04-18 08:33:46'),
(18, 13, 123212.00, '2026-04-18', 'tes', 2, '2026-04-18 08:50:34'),
(19, 13, 297.00, '2026-04-18', '', 2, '2026-04-18 08:55:34'),
(20, 14, 74229.00, '2026-04-18', '8654uvh', 2, '2026-04-18 10:30:12'),
(21, 15, 9000.00, '2026-04-18', '', 2, '2026-04-18 10:32:48'),
(22, 14, 869.00, '2026-04-18', 'lnk', 2, '2026-04-18 10:51:22'),
(23, 14, -85098.00, '2026-04-18', '', 2, '2026-04-18 10:52:58'),
(24, 14, -1.00, '2026-04-18', '', 2, '2026-04-18 10:53:09'),
(25, 14, 89.00, '2026-04-18', '', 2, '2026-04-18 10:57:52'),
(26, 14, 99912.00, '2026-04-18', '', 2, '2026-04-18 11:18:00'),
(27, 12, 1234567.00, '2026-04-18', 'full', 2, '2026-04-18 14:56:14'),
(28, 16, 9.00, '2026-04-19', '', 2, '2026-04-19 14:08:02'),
(29, 17, 6.00, '2026-04-19', '', 2, '2026-04-19 14:40:12'),
(30, 18, 9876543.00, '2026-04-20', '', 2, '2026-04-20 07:15:27'),
(31, 18, 13580246.00, '2026-04-20', '', 2, '2026-04-20 07:15:46'),
(32, 20, 78.00, '2026-04-20', '', 2, '2026-04-20 15:44:17'),
(33, 20, 7.00, '2026-04-20', '', 2, '2026-04-20 15:45:09'),
(34, 21, 12.00, '2026-04-20', '', 2, '2026-04-20 15:47:50'),
(35, 19, 19021.00, '2026-04-20', '', 2, '2026-04-20 16:17:42'),
(36, 19, 180.00, '2026-04-20', '', 2, '2026-04-20 16:17:59'),
(37, 21, 109.00, '2026-04-20', '', 2, '2026-04-20 16:22:33'),
(38, 21, 21.00, '2026-04-20', 'a hskj.,/c;aujelbkn,g ulhjeiauhuiqh3aeuhfiuqauiiqui3aigqiawgygj', 2, '2026-04-20 17:01:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', '$2y$12$rV6tXBcGM78vlks0VL7FVu2vsdxp.85Mqzr9jCE4sWgMUSkcx2bCC', 'staff', '2026-02-24 11:49:44', '2026-03-29 11:20:58'),
(2, 'notaris', 'notaris', '$argon2id$v=19$m=65536,t=4,p=3$TTA0WUFieXROZDRJazdacQ$HSIpH94Za9+Y+rxr0aBhn9cqMr73nZWIe49fq65LBsI', 'administrator', '2026-02-24 11:49:44', '2026-03-29 11:20:58'),
(6, 'tes 123456', 'tes 123456', '$2y$12$rV6tXBcGM78vlks0VL7FVu2vsdxp.85Mqzr9jCE4sWgMUSkcx2bCC', 'staff', '2026-03-01 17:01:51', '2026-03-29 11:20:58');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_steps`
--

CREATE TABLE `workflow_steps` (
  `id` int(11) NOT NULL,
  `step_key` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `sla_days` int(11) DEFAULT 0,
  `behavior_role` int(11) DEFAULT 0 COMMENT '0:Normal, 2:Start, 2:Iteration(Perbaikan), 3:Success(Selesai), 4:Archive(Ditutup), 5:Failure(Batal)',
  `is_cancellable` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflow_steps`
--

INSERT INTO `workflow_steps` (`id`, `step_key`, `label`, `sort_order`, `sla_days`, `behavior_role`, `is_cancellable`) VALUES
(1, 'draft', 'ini Draft / Pengumpulan Persyaratan', 1, 2, 0, 1),
(3, 'validasi_sertifikat', 'Validasi Sertifikat', 2, 7, 1, 1),
(4, 'pencecekan_sertifikat', 'Pengecekan Sertifikat', 3, 7, 1, 1),
(5, 'pembayaran_pajak', 'Pembayaran Pajak', 4, 1, 2, 0),
(6, 'validasi_pajak', 'Validasi Pajak', 5, 5, 2, 0),
(7, 'penomoran_akta', 'Penomoran Akta', 6, 1, 2, 0),
(8, 'pendaftaran', 'Pendaftaran', 7, 7, 2, 0),
(10, 'pemeriksaan_bpn', 'Pemeriksaan BPN', 8, 10, 2, 0),
(11, 'perbaikan', 'Perbaikan', 10, 5, 3, 1),
(12, 'selesai', 'Selesai', 11, 1, 4, 0),
(13, 'diserahkan', 'Diserahkan', 12, 3, 5, 0),
(14, 'ditutup', 'Ditutup', 13, 1, 6, 0),
(15, 'batal', 'Batal', 14, 1, 7, 0),
(16, 'review', 'Review akhir', 9, 2, 8, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_timestamp` (`timestamp`);

--
-- Indexes for table `cleanup_log`
--
ALTER TABLE `cleanup_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_table_date` (`table_name`,`cleanup_date`);

--
-- Indexes for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_key` (`page_key`);

--
-- Indexes for table `cms_page_sections`
--
ALTER TABLE `cms_page_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_page_id` (`page_id`);

--
-- Indexes for table `cms_section_content`
--
ALTER TABLE `cms_section_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_content_section_id` (`section_id`);

--
-- Indexes for table `cms_section_items`
--
ALTER TABLE `cms_section_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_items_section_id` (`section_id`);

--
-- Indexes for table `kendala`
--
ALTER TABLE `kendala`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_perkara_id` (`registrasi_id`),
  ADD KEY `idx_flag_active` (`flag_active`),
  ADD KEY `idx_perkara_flag` (`registrasi_id`,`flag_active`),
  ADD KEY `idx_updated_at` (`updated_at`);

--
-- Indexes for table `klien`
--
ALTER TABLE `klien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hp` (`hp`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_templates`
--
ALTER TABLE `message_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_key` (`template_key`),
  ADD KEY `idx_template_key` (`template_key`);

--
-- Indexes for table `note_templates`
--
ALTER TABLE `note_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `workflow_step_id` (`workflow_step_id`);

--
-- Indexes for table `registrasi`
--
ALTER TABLE `registrasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_perkara` (`nomor_registrasi`),
  ADD KEY `layanan_id` (`layanan_id`),
  ADD KEY `idx_nomor_perkara` (`nomor_registrasi`),
  ADD KEY `idx_klien_id` (`klien_id`),
  ADD KEY `idx_verification_code` (`verification_code`),
  ADD KEY `idx_tracking_token` (`tracking_token`);

--
-- Indexes for table `registrasi_history`
--
ALTER TABLE `registrasi_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_perkara_created` (`registrasi_id`,`created_at`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `fk_hist_status_old` (`status_old_id`),
  ADD KEY `fk_hist_status_new` (`status_new_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_registrasi` (`registrasi_id`);

--
-- Indexes for table `transaksi_history`
--
ALTER TABLE `transaksi_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_transaksi` (`transaksi_id`),
  ADD KEY `fk_history_user` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `step_key` (`step_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cleanup_log`
--
ALTER TABLE `cleanup_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_pages`
--
ALTER TABLE `cms_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cms_page_sections`
--
ALTER TABLE `cms_page_sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cms_section_content`
--
ALTER TABLE `cms_section_content`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `cms_section_items`
--
ALTER TABLE `cms_section_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `kendala`
--
ALTER TABLE `kendala`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `klien`
--
ALTER TABLE `klien`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `message_templates`
--
ALTER TABLE `message_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `note_templates`
--
ALTER TABLE `note_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `registrasi`
--
ALTER TABLE `registrasi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `registrasi_history`
--
ALTER TABLE `registrasi_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_history`
--
ALTER TABLE `transaksi_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cms_page_sections`
--
ALTER TABLE `cms_page_sections`
  ADD CONSTRAINT `fk_page_id` FOREIGN KEY (`page_id`) REFERENCES `cms_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cms_section_content`
--
ALTER TABLE `cms_section_content`
  ADD CONSTRAINT `fk_content_section_id` FOREIGN KEY (`section_id`) REFERENCES `cms_page_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cms_section_items`
--
ALTER TABLE `cms_section_items`
  ADD CONSTRAINT `fk_items_section_id` FOREIGN KEY (`section_id`) REFERENCES `cms_page_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_templates`
--
ALTER TABLE `note_templates`
  ADD CONSTRAINT `fk_workflow_step_id` FOREIGN KEY (`workflow_step_id`) REFERENCES `workflow_steps` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_registrasi` FOREIGN KEY (`registrasi_id`) REFERENCES `registrasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_history`
--
ALTER TABLE `transaksi_history`
  ADD CONSTRAINT `fk_history_transaksi` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
