-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 09:44 AM
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

--
-- Dumping data for table `kendala`
--

INSERT INTO `kendala` (`id`, `registrasi_id`, `workflow_step_id`, `flag_active`, `created_at`, `updated_at`) VALUES
(69, 5, 2, 0, '2026-04-11 11:12:29', '2026-04-11 11:12:45'),
(70, 7, 3, 1, '2026-04-12 07:10:39', '2026-04-12 07:10:39');

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
(40, 'percoaaanaaasasf', '1234', NULL, '2026-04-12 07:27:47', '2026-04-12 07:27:47');

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
(1, 1, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-03-30 11:39:33', NULL),
(2, 2, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara. [catatan]', '2026-03-30 11:39:33', NULL),
(3, 3, 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]', '2026-03-30 11:39:33', NULL),
(4, 4, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', '2026-03-30 11:39:33', NULL),
(5, 5, 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', '2026-03-30 11:39:33', NULL),
(6, 6, 'Pembayaran pajak sedang dalam tahap pemeriksaan dan validasi oleh instansi terkait. [catatan]', '2026-03-30 11:39:33', NULL),
(7, 7, 'Akta sedang dalam proses penomoran sebagai bagian dari legalitas dokumen Anda. [catatan]', '2026-03-30 11:39:33', NULL),
(8, 8, 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang. [catatan]', '2026-03-30 11:39:33', NULL),
(9, 9, 'Pembayaran PNBP sedang diproses sebagai bagian dari biaya resmi pendaftaran perkara. [catatan]', '2026-03-30 11:39:33', NULL),
(10, 10, 'Berkas perkara sedang dalam tahap pemeriksaan oleh pihak BPN. [catatan]', '2026-03-30 11:39:33', NULL),
(11, 11, 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini', '2026-03-30 23:45:18', NULL),
(12, 12, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-03-30 11:39:33', NULL),
(13, 13, 'Berkas dengan nomor {nomor_registrasi} telah diterima oleh {penerima} pada tanggal {tanggal}', '2026-03-30 17:40:19', NULL),
(14, 14, 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk', '2026-03-30 23:39:00', NULL),
(15, 15, 'Perkara ini dinyatakan batal dan tidak dilanjutkan. [catatan]', '2026-03-30 11:39:33', NULL);

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
  `diserahkan_at` datetime DEFAULT NULL,
  `ditutup_at` datetime DEFAULT NULL,
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

INSERT INTO `registrasi` (`id`, `klien_id`, `layanan_id`, `nomor_registrasi`, `current_step_id`, `step_started_at`, `target_completion_at`, `selesai_batal_at`, `diserahkan_at`, `ditutup_at`, `keterangan`, `verification_code`, `tracking_token`, `catatan_internal`, `created_at`, `updated_at`) VALUES
(1, 33, 3, 'NP-20260410-4877', 14, '2026-04-10 23:29:25', '2026-06-10 23:59:59', NULL, '2026-04-10 15:15:00', '2026-04-10 23:29:25', 'berkas pemindahan hak waris', NULL, 'eyJpZCI6MSwiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzc1ODA0MjMwfQ==.8b3547e9c1c8dbd05078766459bc44680f65e999b796c32e8ecafcac41cb1e4d', 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk bro', '2026-04-10 06:57:10', '2026-04-10 16:29:25'),
(2, 34, 3, 'NP-20260410-9810', 13, '2026-04-10 15:13:34', '2026-06-10 23:59:59', NULL, '2026-04-10 15:13:34', NULL, 'ui', NULL, 'eyJpZCI6MiwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1ODA4NzEzfQ==.e17c8e617b61e7509b09e55e84581c744272915a039542439025628adb821a2e', 'Berkas dengan nomor NP-20260410-9810 telah diterima oleh agus pada tanggal Friday, 10 April 2026', '2026-04-10 08:11:53', '2026-04-10 08:13:34'),
(3, 35, 5, 'NP-20260410-1711', 1, '2026-04-10 15:16:37', '2026-06-10 23:59:59', NULL, NULL, NULL, 'aukla', NULL, 'eyJpZCI6MywiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1ODA4OTk3fQ==.7d032434d05d9fb2594db97fafb58f20cf7497a14b14cb42309078a8cc1fc2e1', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-10 08:16:37', '2026-04-10 08:16:37'),
(4, 36, 1, '', 1, '2026-04-10 23:29:09', '2026-06-10 23:59:59', NULL, NULL, NULL, 'ahkjls', NULL, 'eyJpZCI6NCwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1ODM4NTQ5fQ==.9ae25342a4e7320229a62470ddf6d6ed11566e64d7e9f9203799199ab0b1d809', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-10 16:29:09', '2026-04-11 10:57:08'),
(5, 37, 1, 'NP-20260411-7672', 12, '2026-04-11 18:12:45', '2026-06-11 23:59:59', NULL, NULL, NULL, 'xvcbn', NULL, 'eyJpZCI6NSwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTA1MzMwfQ==.2b66ae930a0ab7600c11d1c4d2bd738c893884d51c3d87206195bd6d4ea90bec', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-04-11 11:02:10', '2026-04-11 11:12:45'),
(6, 38, 2, 'NP-20260411-5310', 1, '2026-04-11 18:32:19', '2026-06-11 23:59:59', NULL, NULL, NULL, 'afsfae', NULL, 'eyJpZCI6NiwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTA3MTM5fQ==.75bf5ca0ce82a301f135ca85d623486d5849d224a6a3f772bf0347a2d6b0680e', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-11 11:32:19', '2026-04-11 11:32:19'),
(7, 39, 1, 'NP-20260411-5624', 3, '2026-04-12 14:10:39', '2026-06-11 23:59:59', NULL, NULL, NULL, 'vhjkn', NULL, 'eyJpZCI6NywiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTE0NTc5fQ==.8d149a9a20a364cd52e6146656b40cbad1ee9d04714e874b38d6aa3a4da8a81b', 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]', '2026-04-11 13:36:19', '2026-04-12 07:10:39'),
(8, 40, 2, 'NP-20260412-2416', 1, '2026-04-12 14:27:47', '2026-06-12 23:59:59', NULL, NULL, NULL, 'ujnlkm;', NULL, 'eyJpZCI6OCwiY29kZSI6IjEyMzQiLCJ0aW1lIjoxNzc1OTc4ODY3fQ==.47dbfe2362c19373bfdcdcd5fe5d18b4aab4b913aa15014996a6ba9d0a68d4c6', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-12 07:27:47', '2026-04-12 07:27:47');

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

--
-- Dumping data for table `registrasi_history`
--

INSERT INTO `registrasi_history` (`id`, `registrasi_id`, `status_old_id`, `status_new_id`, `action`, `target_completion_at_new`, `target_completion_at_old`, `keterangan`, `catatan`, `flag_kendala_active`, `flag_kendala_tahap`, `user_id`, `ip_address`, `created_at`) VALUES
(1, 1, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-10 06:57:10'),
(2, 2, NULL, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]', 0, NULL, 2, '::1', '2026-04-10 08:11:53'),
(3, 2, 3, 5, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', 'ui', 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', 0, NULL, 2, '::1', '2026-04-10 08:12:07'),
(4, 2, 5, 12, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', 'ui', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 0, NULL, 2, '::1', '2026-04-10 08:13:29'),
(5, 2, 12, 13, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', 'ui', 'Berkas dengan nomor NP-20260410-9810 telah diterima oleh agus pada tanggal Friday, 10 April 2026', 0, NULL, 2, '::1', '2026-04-10 08:13:34'),
(6, 1, 1, 12, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', 'berkas pemindahan hak waris', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 0, NULL, 2, '::1', '2026-04-10 08:14:00'),
(7, 1, 12, 13, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', 'berkas pemindahan hak waris', 'Berkas dengan nomor NP-20260410-4877 telah diterima oleh kharis pada tanggal Friday, 10 April 2026', 0, NULL, 2, '::1', '2026-04-10 08:15:00'),
(8, 3, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-10 08:16:37'),
(9, 4, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-10 16:29:09'),
(10, 1, 13, 14, 'Finalisasi', '2026-06-10 23:59:59', '2026-06-10 23:59:59', 'berkas pemindahan hak waris', 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk bro', 0, NULL, 2, '::1', '2026-04-10 16:29:25'),
(11, 4, 1, 1, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', NULL, 'Update administratif data klien/SLA.', 0, NULL, 2, '::1', '2026-04-11 10:57:08'),
(12, 5, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-11 11:02:10'),
(13, 4, 1, 1, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', NULL, 'Update administratif data klien/SLA.', 0, NULL, 2, '::1', '2026-04-11 11:02:35'),
(14, 4, 1, 1, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', NULL, 'Update administratif data klien/SLA.', 0, NULL, 2, '::1', '2026-04-11 11:02:48'),
(15, 4, 1, 1, 'Update', '2026-06-10 23:59:59', '2026-06-10 23:59:59', NULL, 'Update administratif data klien/SLA.', 0, NULL, 2, '::1', '2026-04-11 11:04:34'),
(16, 5, 1, 1, 'Update', '2026-06-11 23:59:59', '2026-06-11 23:59:59', NULL, 'Update administratif data klien/SLA.', 0, NULL, 2, '::1', '2026-04-11 11:05:05'),
(17, 5, 1, 2, 'Update', '2026-06-11 23:59:59', '2026-06-11 23:59:59', 'xvcbn', 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara. [catatan]', 1, 'Pembayaran Administrasi', 2, '::1', '2026-04-11 11:12:29'),
(18, 5, 2, 8, 'Update', '2026-06-11 23:59:59', '2026-06-11 23:59:59', 'xvcbn', 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang. [catatan]', 1, 'Pendaftaran', 2, '::1', '2026-04-11 11:12:39'),
(19, 5, 8, 12, 'Update', '2026-06-11 23:59:59', '2026-06-11 23:59:59', 'xvcbn', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 0, NULL, 2, '::1', '2026-04-11 11:12:45'),
(20, 5, 12, 12, 'Update', '2026-06-11 23:59:59', '2026-06-11 23:59:59', NULL, 'Update administratif data klien/SLA.', 0, NULL, 2, '::1', '2026-04-11 11:12:59'),
(21, 6, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-11 11:32:19'),
(22, 7, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-11 13:36:19'),
(23, 7, 1, 2, 'Update', '2026-06-11 23:59:59', '2026-06-11 23:59:59', 'vhjkn', 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara. [catatan]', 0, NULL, 2, '::1', '2026-04-12 07:07:01'),
(24, 7, 2, 3, 'Update', '2026-06-11 23:59:59', '2026-06-11 23:59:59', 'vhjkn', 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]', 1, 'Validasi Sertifikat', 2, '::1', '2026-04-12 07:10:39'),
(25, 8, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-12 07:27:47');

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

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `registrasi_id`, `total_tagihan`, `jumlah_bayar`, `created_at`, `updated_at`) VALUES
(1, 5, 123456789098.00, 2468462.00, '2026-04-11 11:02:10', '2026-04-11 11:31:41'),
(2, 4, 12300000.00, 0.00, '2026-04-11 11:02:35', '2026-04-11 11:04:34'),
(3, 7, 123456543.00, 187661356.00, '2026-04-11 13:36:19', '2026-04-12 07:07:13'),
(4, 6, 8765456.00, 0.00, '2026-04-12 07:24:28', '2026-04-12 07:24:28'),
(5, 8, 9999999999999.99, 9999999999999.99, '2026-04-12 07:28:03', '2026-04-12 07:28:40');

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
(6, 5, 9999999999999.99, '2026-04-12', '', 2, '2026-04-12 07:28:40');

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
(1, 'draft', 'Draft / Pengumpulan Persyaratan', 1, 2, 0, 0),
(2, 'pembayaran_admin', 'Pembayaran Administrasi', 2, 4, 1, 1),
(3, 'validasi_sertifikat', 'Validasi Sertifikat', 3, 7, 1, 0),
(4, 'pencecekan_sertifikat', 'Pengecekan Sertifikat', 4, 7, 1, 0),
(5, 'pembayaran_pajak', 'Pembayaran Pajak', 5, 1, 2, 0),
(6, 'validasi_pajak', 'Validasi Pajak', 6, 5, 2, 0),
(7, 'penomoran_akta', 'Penomoran Akta', 7, 1, 2, 0),
(8, 'pendaftaran', 'Pendaftaran', 8, 7, 2, 0),
(9, 'pembayaran_pnbp', 'Pembayaran PNBP', 9, 2, 2, 0),
(10, 'pemeriksaan_bpn', 'Pemeriksaan BPN', 10, 10, 2, 0),
(11, 'perbaikan', 'Perbaikan', 11, 5, 3, 1),
(12, 'selesai', 'Selesai', 12, 1, 4, 0),
(13, 'diserahkan', 'Diserahkan', 13, 3, 5, 0),
(14, 'ditutup', 'Ditutup', 14, 1, 6, 0),
(15, 'batal', 'Batal', 15, 1, 7, 0);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `klien`
--
ALTER TABLE `klien`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `registrasi`
--
ALTER TABLE `registrasi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `registrasi_history`
--
ALTER TABLE `registrasi_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transaksi_history`
--
ALTER TABLE `transaksi_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `workflow_steps`
--
ALTER TABLE `workflow_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
