-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 10:13 AM
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
-- Database: `norasblmupdate2`
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
(49, 6, 'photo', 'image.php?id=IotWlCFwKCU4ACBNnrJqhU9SSEIvZFI2Ny9VTE5SOXZDMC9CbzM5WnJkd3AxQnlVS3BtdDZIQWpvMyszWHE4bmxvRUlhc3p1V041K1BBZmU%3D', 'image', 0),
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
(1, 2, 2, 0, '2026-02-25 17:35:19', '2026-03-30 14:07:13'),
(2, 2, 3, 0, '2026-02-25 17:47:35', '2026-03-30 14:07:13'),
(3, 2, 3, 0, '2026-02-26 13:08:13', '2026-03-30 14:07:13'),
(4, 2, 3, 0, '2026-02-26 13:18:30', '2026-03-30 14:07:13'),
(5, 2, 4, 0, '2026-02-26 13:19:17', '2026-03-30 14:07:13'),
(6, 3, 7, 0, '2026-02-26 14:19:12', '2026-03-30 14:07:13'),
(7, 3, 10, 0, '2026-02-26 15:33:36', '2026-03-30 14:07:13'),
(8, 8, 1, 0, '2026-02-27 01:34:29', '2026-03-30 14:07:13'),
(9, 5, 12, 0, '2026-02-27 01:40:23', '2026-03-30 14:07:13'),
(10, 8, 1, 0, '2026-02-27 01:41:20', '2026-03-30 14:07:13'),
(11, 9, 1, 0, '2026-02-27 02:00:49', '2026-03-30 14:07:13'),
(12, 9, 1, 0, '2026-03-01 15:18:55', '2026-03-30 14:07:13'),
(13, 8, 2, 0, '2026-03-01 15:26:09', '2026-03-30 14:07:13'),
(14, 6, 2, 0, '2026-03-01 15:26:24', '2026-03-30 14:07:13'),
(15, 9, 1, 0, '2026-03-01 15:26:44', '2026-03-30 14:07:13'),
(16, 9, 1, 0, '2026-03-01 15:27:30', '2026-03-30 14:07:13'),
(17, 8, 1, 0, '2026-03-02 03:43:17', '2026-03-30 14:07:13'),
(18, 8, 1, 0, '2026-03-02 03:43:37', '2026-03-30 14:07:13'),
(19, 9, 3, 0, '2026-03-02 03:49:49', '2026-03-30 14:07:13'),
(20, 9, 4, 0, '2026-03-02 04:03:41', '2026-03-30 14:07:13'),
(21, 7, 2, 0, '2026-03-02 04:12:37', '2026-03-30 14:07:13'),
(22, 7, 12, 0, '2026-03-02 04:27:16', '2026-03-30 14:07:13'),
(23, 11, 2, 0, '2026-03-02 04:34:33', '2026-03-30 14:07:13'),
(24, 10, 2, 0, '2026-03-02 04:36:03', '2026-03-30 14:07:13'),
(25, 12, 12, 0, '2026-03-02 04:47:38', '2026-03-30 14:07:13'),
(26, 15, 4, 0, '2026-03-02 04:54:26', '2026-03-30 14:07:13'),
(27, 14, 2, 0, '2026-03-02 04:56:36', '2026-03-30 14:07:13'),
(28, 14, 12, 0, '2026-03-02 04:57:01', '2026-03-30 14:07:13'),
(29, 16, 3, 0, '2026-03-02 05:00:31', '2026-03-30 14:07:13'),
(30, 17, 2, 0, '2026-03-02 05:19:21', '2026-03-30 14:07:13'),
(31, 19, 3, 0, '2026-03-02 05:29:00', '2026-03-30 14:07:13'),
(32, 19, 15, 0, '2026-03-02 05:29:13', '2026-03-30 14:07:13'),
(33, 25, 3, 0, '2026-03-03 07:53:23', '2026-03-30 14:07:13'),
(34, 29, 1, 0, '2026-03-06 18:04:55', '2026-03-30 14:07:13'),
(35, 29, 12, 0, '2026-03-07 15:43:08', '2026-03-30 14:07:13'),
(36, 30, 13, 0, '2026-03-07 15:45:53', '2026-03-30 14:07:13'),
(37, 27, 13, 0, '2026-03-08 03:04:24', '2026-03-30 14:07:13'),
(38, 20, 1, 1, '2026-03-08 03:48:45', '2026-03-30 14:07:13'),
(39, 36, 3, 1, '2026-03-13 03:03:50', '2026-03-30 14:07:13'),
(40, 43, 2, 1, '2026-03-25 05:36:39', '2026-03-30 14:07:13'),
(41, 3, 3, 1, '2026-03-28 13:56:25', '2026-03-30 14:07:13'),
(42, 59, 8, 1, '2026-03-28 15:18:35', '2026-03-30 14:07:13'),
(43, 62, 3, 0, '2026-03-29 09:08:56', '2026-03-30 14:07:13'),
(44, 62, 5, 0, '2026-03-29 09:52:34', '2026-03-30 14:07:13'),
(45, 62, 5, 0, '2026-03-29 09:53:45', '2026-03-30 14:07:13'),
(46, 62, 6, 0, '2026-03-29 09:54:06', '2026-03-30 14:07:13'),
(47, 61, 4, 1, '2026-03-29 13:41:59', '2026-03-30 14:07:13'),
(48, 68, 1, 0, '2026-03-30 06:38:09', '2026-03-30 14:07:13'),
(49, 68, 1, 0, '2026-03-30 06:57:26', '2026-03-30 14:07:13'),
(50, 68, 4, 0, '2026-03-30 06:58:07', '2026-03-30 14:07:13'),
(51, 68, 5, 0, '2026-03-30 07:00:12', '2026-03-30 14:07:13'),
(52, 68, 5, 0, '2026-03-30 07:13:50', '2026-03-30 14:07:13'),
(53, 68, 6, 0, '2026-03-30 07:19:28', '2026-03-30 14:07:13'),
(54, 69, 4, 0, '2026-03-30 08:44:21', '2026-03-30 14:07:13'),
(55, 69, 4, 0, '2026-03-30 08:44:59', '2026-03-30 14:07:13'),
(56, 69, 4, 0, '2026-03-30 08:47:51', '2026-03-30 14:07:13'),
(57, 69, 4, 0, '2026-03-30 08:48:35', '2026-03-30 14:07:13'),
(58, 69, 4, 0, '2026-03-30 09:07:15', '2026-03-30 14:07:13'),
(59, 69, 4, 0, '2026-03-30 09:24:43', '2026-03-30 14:07:13'),
(60, 69, 5, 0, '2026-03-30 09:31:20', '2026-03-30 14:07:13'),
(61, 69, 5, 0, '2026-03-30 09:31:35', '2026-03-30 14:07:13'),
(62, 69, 5, 0, '2026-03-30 09:50:56', '2026-03-30 14:07:13'),
(63, 66, 1, 0, '2026-03-30 12:04:31', '2026-03-30 14:07:13'),
(64, 66, 1, 0, '2026-03-30 12:12:49', '2026-03-30 14:07:13'),
(65, 65, 10, 0, '2026-03-30 12:46:10', '2026-03-30 14:07:13'),
(66, 63, 8, 1, '2026-03-30 14:59:05', '2026-03-30 14:59:05'),
(67, 70, 1, 0, '2026-03-30 15:22:34', '2026-03-30 15:22:39'),
(68, 1, 1, 0, '2026-04-01 02:08:34', '2026-04-03 13:04:29');

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
(31, 'ini oengecekan ti', 'k', NULL, '2026-04-05 07:58:45', '2026-04-05 07:58:45');

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
  `locked` tinyint(1) DEFAULT 0 COMMENT 'Lock mechanism to prevent concurrent edits',
  `batal_flag` tinyint(1) DEFAULT 0 COMMENT 'Flag to indicate cancellation status',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registrasi`
--

INSERT INTO `registrasi` (`id`, `klien_id`, `layanan_id`, `nomor_registrasi`, `current_step_id`, `step_started_at`, `target_completion_at`, `selesai_batal_at`, `diserahkan_at`, `ditutup_at`, `keterangan`, `verification_code`, `tracking_token`, `catatan_internal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'NP-20260224-0620', 4, '2026-04-03 15:04:29', '2026-04-30 20:07:24', '2026-03-21 00:24:27', NULL, '2026-03-31 14:22:47', 'alaka', '5432', 'eyJpZCI6MSwiY29kZSI6IjU0MzIiLCJ0aW1lIjoxNzcxOTUxMDk1fQ==.65b91df87eb35e13bc2dccd9cdbbc83ccb709ca799833e4b06e7d4a06ab2d62d', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', '2026-02-24 13:07:24', '2026-04-03 13:04:29'),
(2, 2, 2, 'NP-20260224-7260', 15, '2026-03-28 21:59:23', '2026-04-30 21:59:45', NULL, NULL, NULL, NULL, '2e12', 'tk_8f8e53a1d5933cc264af3aea046a2fe2', 'Perkara ini dinyatakan batal dan tidak dilanjutkan ke tahap berikutnya.', '2026-02-24 14:59:45', '2026-03-28 16:15:27'),
(3, 3, 2, 'NP-20260224-8112', 3, '2026-03-28 21:59:23', '2026-04-30 22:20:36', '2026-03-12 12:41:44', NULL, NULL, NULL, '8885', 'eyJpZCI6MywiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzcyMTE2NDkxfQ==.47ae0f2815bda30ded8076e55d80302e05f5ff6a78716685468a92bf18657baa', 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', '2026-02-24 15:20:36', '2026-03-30 15:52:47'),
(4, 3, 2, 'NP-20260224-3298', 11, '2026-04-01 04:44:08', '2026-04-30 22:27:32', NULL, '2026-03-31 00:18:35', '2026-03-04 14:47:07', NULL, '8885', 'eyJpZCI6NCwiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzczMjk2MjM0fQ==.b62f6374d6ee6cf4a324e0c852ca456f785e9e32fc7803041f6a8e1439db3c30', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini dirubah', '2026-02-24 15:27:32', '2026-04-01 02:44:08'),
(5, 3, 6, 'NP-20260224-2588', 12, '2026-03-28 21:59:23', '2026-04-30 22:28:03', NULL, NULL, NULL, NULL, '8885', 'eyJpZCI6NSwiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzcyMDM1MTg0fQ==.3c4a11e3430de97b57f8fd5346d8bde5665d44ad95da1e759aef2c1d52ef6054', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', '2026-02-24 15:28:03', '2026-03-28 16:15:27'),
(6, 3, 1, 'NP-20260226-5134', 11, '2026-03-28 21:59:23', '2026-05-02 21:09:29', '2026-03-25 12:37:06', NULL, NULL, NULL, NULL, 'eyJpZCI6NiwiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzcyMTE0OTY5fQ==.293c908503d6c851f8efbca207683362c11c8b0597634e2f1fc4a06a2b0cd1ed', 'Proses ulang dari halaman Detail - Back to Perbaikan', '2026-02-26 14:09:29', '2026-03-30 15:52:47'),
(7, 3, 1, 'NP-20260226-6563', 11, '2026-03-28 21:59:23', '2026-05-02 21:09:29', '2026-03-26 10:25:50', NULL, NULL, NULL, NULL, 'eyJpZCI6NywiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzcyMTE0OTY5fQ==.9853f9d8f1bd0cc6bd60666c6aa55f8203714fa856ac5b041a607d2a225fdfff', 'Proses ulang dari halaman Detail - Back to Perbaikan', '2026-02-26 14:09:29', '2026-03-30 15:52:47'),
(8, 3, 5, 'NP-20260227-7190', 15, '2026-03-28 21:59:23', '2026-05-03 08:12:55', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6OCwiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzcyMTU0Nzc1fQ==.16f0f6270f360ee90561250f54dc74096bde6f09b5bd539a9ac93a041274d56c', 'Perkara ini dinyatakan batal dan tidak dilanjutkan ke tahap berikutnya.', '2026-02-27 01:12:55', '2026-03-28 16:15:27'),
(9, 3, 4, 'NP-20260227-4316', 12, '2026-03-28 21:59:23', '2026-05-03 09:00:28', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6OSwiY29kZSI6Ijg4ODUiLCJ0aW1lIjoxNzcyMTU3NjI4fQ==.c580ae71be976872e2890438bdb0a03544b785649be48f13f055f1405401d20a', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', '2026-02-27 02:00:28', '2026-03-28 16:15:27'),
(10, 3, 3, 'NP-20260302-1925', 11, '2026-03-28 21:59:23', '2026-05-06 11:33:49', '2026-03-12 12:42:37', NULL, NULL, NULL, NULL, 'eyJpZCI6MTAsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyNjAyOX0=.3b452fc3e3b7e6c9ba0a53db432946367711678e7d9bdcd3c20865ac64831a34', 'Proses ulang dari halaman Detail - Back to Perbaikan', '2026-03-02 04:33:49', '2026-03-30 15:52:47'),
(11, 3, 1, 'NP-20260302-5491', 15, '2026-03-28 21:59:23', '2026-05-06 11:34:18', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MTEsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyNjA1OX0=.2b724853b99e7ed1e569785503e24f9218eb99ea6b6cfd80eca6f7f055614160', 'Perkara ini dinyatakan batal dan tidak dilanjutkan ke tahap berikutnya.', '2026-03-02 04:34:18', '2026-03-28 16:15:27'),
(12, 3, 1, 'NP-20260302-9541', 14, '2026-03-28 21:59:23', '2026-05-06 11:46:35', NULL, NULL, '2026-03-04 14:49:08', NULL, NULL, 'eyJpZCI6MTIsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyNjc5NX0=.b94ad143d569684c83b585486e0f4ec6369105ccf4ec25ec2952c8c8c251b057', 'Perkara ditutup dari halaman Detail', '2026-03-02 04:46:35', '2026-03-30 15:52:47'),
(13, 3, 2, 'NP-20260302-8286', 14, '2026-03-28 21:59:23', '2026-05-06 11:51:55', NULL, NULL, '2026-03-02 14:49:19', NULL, NULL, 'eyJpZCI6MTMsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyNzExNX0=.3eca4d89d93c0c68a3e1c8d6626cc0541aa4e38cbb797d90ef2583b9e6229196', 'Perkara ditutup dari halaman Detail', '2026-03-02 04:51:55', '2026-03-30 15:52:47'),
(14, 3, 2, 'NP-20260302-4508', 11, '2026-03-28 21:59:23', '2026-05-06 11:51:58', '2026-03-03 14:53:42', NULL, NULL, NULL, NULL, 'eyJpZCI6MTQsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyNzExOH0=.431f6bc43e770e306296b93e45b5cac847c06d6ad1052ec24f1c3478dabe3790', 'Proses ulang dari halaman Detail - Back to Perbaikan', '2026-03-02 04:51:58', '2026-03-30 15:52:47'),
(15, 3, 2, 'NP-20260302-3825', 14, '2026-03-28 21:59:23', '2026-05-06 11:52:01', NULL, NULL, '2026-03-04 14:45:56', NULL, NULL, 'eyJpZCI6MTUsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyNzEyMX0=.afee44b854bb1f033ba0760e3eddd9589ae6b14891e64149eda2df504507cd0d', 'Perkara ditutup dari halaman Detail', '2026-03-02 04:52:01', '2026-03-30 15:52:47'),
(16, 3, 6, 'NP-20260302-9979', 14, '2026-03-28 21:59:23', '2026-05-06 12:00:11', NULL, NULL, '2026-03-02 14:49:42', NULL, NULL, 'eyJpZCI6MTYsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyNzYxMX0=.7ebffd572564d4aede1cc65c03302adef7681cf41e536db6998c759e51a3155d', 'Perkara ditutup dari halaman Detail', '2026-03-02 05:00:11', '2026-03-30 15:52:47'),
(17, 3, 6, 'NP-20260302-6054', 14, '2026-03-28 21:59:23', '2026-05-06 12:18:17', NULL, NULL, '2026-03-02 14:48:54', NULL, NULL, 'eyJpZCI6MTcsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyODY5N30=.586315a5c5bbd7c8c9f8cb0c6b90be4401a136a8dcf975684f9a2e52587053d3', 'Perkara ditutup dari halaman Detail', '2026-03-02 05:18:17', '2026-03-30 15:52:47'),
(18, 3, 1, 'NP-20260302-8398', 1, '2026-03-28 21:59:23', '2026-05-06 12:28:10', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MTgsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyOTI5MH0=.4150a8245a045ee6404d8905f4117964d17ad551cd1a68d335e0ee218e9f3b91', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', '2026-03-02 05:28:10', '2026-03-28 16:15:27'),
(19, 3, 1, 'NP-20260302-3144', 14, '2026-03-28 21:59:23', '2026-05-06 12:28:35', '2026-03-02 12:29:13', NULL, '2026-03-02 14:47:00', NULL, NULL, 'eyJpZCI6MTksImNvZGUiOm51bGwsInRpbWUiOjE3NzI2MDg3NTF9.a72e9cd9def7e16d2e68e44bc67437cfa3e270c66c61fe1fbcc0eaebc29bf16b', 'Perkara ditutup dari halaman Detail', '2026-03-02 05:28:35', '2026-03-30 15:52:47'),
(20, 3, 2, 'NP-20260302-3549', 1, '2026-03-28 21:59:23', '2026-05-06 12:36:34', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MjAsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyOTc5NH0=.e7689ff2f8143cbb57d01d68684ea0b04b6e90947513c627c381098f3161f216', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', '2026-03-02 05:36:34', '2026-03-28 16:15:27'),
(21, 3, 2, 'NP-20260302-8619', 1, '2026-03-28 21:59:23', '2026-05-06 12:37:36', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MjEsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyOTg1Nn0=.fca2e29a94bc66bc60212b116a0a116aa7ec94087a7cea1809a548bc8a3ce2eb', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', '2026-03-02 05:37:36', '2026-03-28 16:15:27'),
(22, 3, 1, 'NP-20260302-0388', 1, '2026-03-28 21:59:23', '2026-05-06 12:38:05', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MjIsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQyOTg4NX0=.b1293a1e22ac0bf324c5ffd5750eb5602a9d73a792f3c7f3f969c286dc9d975e', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', '2026-03-02 05:38:05', '2026-03-28 16:15:27'),
(23, 3, 2, 'NP-20260302-5069', 1, '2026-03-28 21:59:23', '2026-05-06 12:40:02', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MjMsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQzMDAwMn0=.c544a4a71fe16b526d764815dcb5e00388d84b0465231c4066bd33d1b91b6d3c', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', '2026-03-02 05:40:02', '2026-03-28 16:15:27'),
(24, 3, 5, 'NP-20260302-3483', 1, '2026-03-28 21:59:23', '2026-05-06 12:43:47', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MjQsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjQzMDIyN30=.5875e04cb87f87b2e8745669814aea7af317af97f8b05119fc267780cc145008', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', '2026-03-02 05:43:47', '2026-03-28 16:15:27'),
(25, 4, 1, 'NP-20260302-0782', 3, '2026-03-28 21:59:23', '2026-05-06 12:44:43', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MjUsImNvZGUiOm51bGwsInRpbWUiOjE3NzI2MDg3NzR9.e4972c21b7a552e47e0e4ed524ade345c4d3556939f291421ac9a9962a6e502b', 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', '2026-03-02 05:44:43', '2026-03-28 16:15:27'),
(26, 3, 1, 'NP-20260304-4996', 1, '2026-03-28 21:59:23', '2026-05-08 14:22:17', '2026-03-04 14:44:54', NULL, NULL, NULL, NULL, 'eyJpZCI6MjYsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjYwODkzN30=.127c04c06bea42edba5d3a2abd330d8221023759dc26b39609d408b90817afbf', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', '2026-03-04 07:22:17', '2026-03-30 15:52:47'),
(27, 3, 2, 'NP-20260304-9295', 13, '2026-03-28 21:59:23', '2026-05-08 14:30:32', NULL, '2026-03-08 10:04:24', NULL, NULL, NULL, 'eyJpZCI6MjcsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjYwOTQzMn0=.3c715bd3ccd6e6ce3587b5501f28151f8cd76bf2ab5da6ae359d878fc4df92f2', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang diselesaikan.', '2026-03-04 07:30:32', '2026-03-30 15:52:47'),
(28, 3, 1, 'NP-20260304-8855', 5, '2026-03-28 21:59:23', '2026-05-08 14:43:12', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MjgsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjYxMDE5Mn0=.d1fe157781fa3c5880d966ec658ee1033593a65e0b322c8e2709ff305f47d741', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', '2026-03-04 07:43:12', '2026-03-28 16:15:27'),
(29, 3, 1, 'NP-20260306-4773', 13, '2026-03-28 21:59:23', '2026-05-11 01:03:45', NULL, '2026-03-07 22:43:56', NULL, NULL, NULL, 'eyJpZCI6MjksImNvZGUiOiI4ODg1IiwidGltZSI6MTc3MjgyMDIyNX0=.1d3c11ba83bcb3c04d51afde0c3e89b003e409b0bacb19ac325935758090dd30', 'Perkara diserahkan ke klien ahmad', '2026-03-06 18:03:45', '2026-03-30 15:52:47'),
(30, 3, 1, 'NP-20260307-3068', 13, '2026-03-28 21:59:23', '2026-05-11 22:45:25', NULL, '2026-03-07 22:45:53', NULL, NULL, NULL, 'eyJpZCI6MzAsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3Mjg5ODMyNX0=.cf4ada2a306b1f68c462452b358a9765a34a89ad997e09165499c1dfa04e8ced', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', '2026-03-07 15:45:25', '2026-03-30 15:52:47'),
(31, 5, 1, 'NP-20260311-6326', 2, '2026-03-28 21:59:23', '2026-05-16 00:55:28', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6MzEsImNvZGUiOiI5ODc2IiwidGltZSI6MTc3MzI1MTcyOH0=.fe6942959aad90a12ea5cfee68f17f5b20c62fe05c25d2d905b181b3dba1bf7f', 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', '2026-03-11 17:55:28', '2026-03-28 16:15:27'),
(32, 6, 4, 'NP-20260313-4214', 1, '2026-03-28 21:59:23', '2026-05-17 09:30:12', NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-03-13 02:30:12', '2026-03-28 16:15:27'),
(33, 6, 4, 'NP-20260313-6115', 1, '2026-03-28 21:59:23', '2026-05-17 09:30:13', NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-03-13 02:30:13', '2026-03-28 16:15:27'),
(34, 6, 4, 'NP-20260313-5348', 1, '2026-03-28 21:59:23', '2026-05-17 09:30:20', NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-03-13 02:30:20', '2026-03-28 16:15:27'),
(35, 7, 2, 'NP-20260313-4062', 2, '2026-03-28 21:59:23', '2026-05-17 09:38:14', NULL, NULL, NULL, NULL, NULL, NULL, 'kjbs,', '2026-03-13 02:38:14', '2026-03-28 16:15:27'),
(36, 7, 2, 'NP-20260313-4701', 3, '2026-03-28 21:59:23', '2026-05-17 10:03:13', NULL, NULL, NULL, NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', '2026-03-13 03:03:13', '2026-03-28 16:15:27'),
(37, 8, 5, 'NP-20260313-3052', 1, '2026-03-28 21:59:23', '2026-05-17 10:04:27', NULL, NULL, NULL, NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-13 03:04:27', '2026-03-28 16:15:27'),
(38, 8, 5, 'NP-20260313-9552', 1, '2026-03-28 21:59:23', '2026-05-17 10:04:49', NULL, NULL, NULL, NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-13 03:04:49', '2026-03-28 16:15:27'),
(39, 8, 5, 'NP-20260313-6289', 1, '2026-03-28 21:59:23', '2026-05-17 10:05:02', NULL, NULL, NULL, NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-13 03:05:02', '2026-03-28 16:15:27'),
(40, 8, 5, 'NP-20260313-7716', 1, '2026-03-28 21:59:23', '2026-05-17 10:05:02', NULL, NULL, NULL, NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-13 03:05:02', '2026-03-28 16:15:27'),
(41, 9, 1, 'NP-20260313-7021', 2, '2026-03-28 21:59:23', '2026-05-17 10:05:18', NULL, NULL, NULL, NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', '2026-03-13 03:05:18', '2026-03-28 16:15:27'),
(42, 10, 1, 'NP-20260313-1804', 1, '2026-03-28 21:59:23', '2026-05-17 10:24:33', NULL, NULL, NULL, NULL, NULL, NULL, 'Test catatan', '2026-03-13 03:24:33', '2026-03-28 16:15:27'),
(43, 3, 2, 'NP-20260325-7528', 2, '2026-03-28 21:59:23', '2026-05-29 12:36:14', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NDMsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDQxNjk3NH0=.300f5feabc6a7266396e5513e614a36f28a8906cb26b037747bb480f4c8f9e03', 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', '2026-03-25 05:36:14', '2026-03-28 16:15:27'),
(44, 3, 1, 'NP-20260326-0253', 1, '2026-03-28 21:59:23', '2026-05-30 10:11:59', NULL, NULL, NULL, NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:11:59', '2026-03-28 16:15:27'),
(45, 3, 1, 'NP-20260326-8196', 1, '2026-03-28 21:59:23', '2026-05-30 10:12:56', NULL, NULL, NULL, NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:12:56', '2026-03-28 16:15:27'),
(46, 3, 4, 'NP-20260326-2592', 3, '2026-03-28 21:59:23', '2026-05-30 10:15:25', NULL, NULL, NULL, NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', '2026-03-26 03:15:25', '2026-03-28 16:15:27'),
(47, 3, 1, 'NP-20260326-0027', 1, '2026-03-28 21:59:23', '2026-05-30 10:26:28', NULL, NULL, NULL, NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:26:28', '2026-03-28 16:15:27'),
(48, 3, 1, 'NP-20260326-5214', 1, '2026-03-28 21:59:23', '2026-05-30 10:32:08', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NDgsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDQ5NTkyOH0=.02c9131b361cbbb9014360c335f7fb980dbc5de19bd8b610b4a35adfe44ba1db', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:32:08', '2026-03-28 16:15:27'),
(49, 3, 1, 'NP-20260326-0241', 1, '2026-03-28 21:59:23', '2026-05-30 10:33:32', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NDksImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDQ5NjAxMn0=.01017dedfd5bc33475e52e8182d1bc7ae3b19c73dd72298e4fa3ae20c7082fde', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:33:32', '2026-03-28 16:15:27'),
(50, 3, 1, 'NP-20260326-2710', 1, '2026-03-28 21:59:23', '2026-05-30 10:35:33', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTAsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDQ5NjEzM30=.1a15c7ae44ad4d43800837d470f5574f315d114a932c00496870a29d73c48206', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:35:33', '2026-03-28 16:15:27'),
(51, 3, 4, 'NP-20260326-8484', 2, '2026-03-28 21:59:23', '2026-05-30 10:36:55', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTEsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDQ5NjIxNX0=.2d2cf9a01b8fe3f46e5fa4f13ab1b838ae70ec316ac2f3fcdafbaf9a0b2d8114', 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', '2026-03-26 03:36:55', '2026-03-28 16:15:27'),
(52, 3, 2, 'NP-20260326-9947', 1, '2026-03-28 21:59:23', '2026-05-30 10:37:34', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTIsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDQ5NjI1NH0=.0abebce9f3254accd96fc2a59006d9eae8fe20197bfa15db588d8f593ddbe129', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:37:34', '2026-03-28 16:15:27'),
(53, 11, 1, 'NP-20260326-2298', 1, '2026-03-28 21:59:23', '2026-05-30 10:43:24', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTMsImNvZGUiOiI4NTgyIiwidGltZSI6MTc3NDQ5NjYwNH0=.7fc9e06f98fb929734ea9f68bb9df23c59b29dd90724af005fdd28450d21b4c0', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:43:24', '2026-03-28 16:15:27'),
(54, 12, 1, 'NP-20260326-0779', 1, '2026-03-28 21:59:23', '2026-05-30 10:52:06', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTQsImNvZGUiOiI4NTgyIiwidGltZSI6MTc3NDQ5NzEyNn0=.4f4f6f693616ba38ccb77911d857fb6a924e71918bef009befb9fd9eb4727049', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-26 03:52:06', '2026-03-28 16:15:27'),
(55, 13, 1, 'NP-20260327-9581', 1, '2026-03-28 21:59:23', '2026-05-31 09:34:54', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTUsImNvZGUiOiI4NTgyIiwidGltZSI6MTc3NDU3ODg5NH0=.1ae9053018ac70a173b88219d485414826588efcb797b357cdae9d1570290673', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-27 02:34:54', '2026-03-28 16:15:27'),
(56, 14, 1, 'NP-20260328-6744', 1, '2026-03-28 21:59:23', '2026-06-01 21:33:50', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTYsImNvZGUiOiI4NTgyIiwidGltZSI6MTc3NDcwODQzMH0=.2bd8e58a62416750dcfffc8865f224857a931874b935bbd34b33df7151263846', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-28 14:33:50', '2026-03-28 16:15:27'),
(57, 15, 3, 'NP-20260328-0541', 1, '2026-03-28 21:59:23', '2026-06-01 21:34:12', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTcsImNvZGUiOiI4NTgyIiwidGltZSI6MTc3NDcwODQ1Mn0=.20f031b0e44a8c9f4c9afd1d67faf51510f982cbc483f306f378df66b7eda0da', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', '2026-03-28 14:34:12', '2026-03-28 16:15:27'),
(58, 16, 1, 'NP-20260328-7870', 1, '2026-03-28 21:59:23', '2026-06-01 21:34:33', NULL, NULL, NULL, NULL, NULL, 'eyJpZCI6NTgsImNvZGUiOiI4NTgyIiwidGltZSI6MTc3NDcwODQ3M30=.c461b6a6f212fbc7c0b827fb5c1507b2666fa8bc55124b746e3d9dbb371ce149', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', '2026-03-28 14:34:33', '2026-03-28 16:15:27'),
(59, 17, 1, 'NP-20260328-0141', 1, '2026-03-28 16:35:42', '2026-06-01 21:38:52', '2026-03-28 22:18:57', NULL, NULL, 'apa', NULL, 'eyJpZCI6NTksImNvZGUiOiI4NTgyIiwidGltZSI6MTc3NDcwODczMn0=.5840c365a8100425cdecab523ebf0888d00e5ee48205884952e62de21da13a14', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-28 14:38:52', '2026-03-30 15:52:47'),
(60, 18, 2, 'NP-20260328-7720', 1, '2026-03-28 17:30:58', NULL, NULL, NULL, NULL, 'sAa', NULL, 'eyJpZCI6NjAsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDcxNTQ1OH0=.f38a432b80ef15e08dd9f9eacaae6ca1a4f802e77423816acc145307be291f4a', 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', '2026-03-28 16:30:58', '2026-03-28 16:30:58'),
(61, 19, 6, 'NP-20260328-9644', 4, '2026-03-29 15:41:59', '2026-06-30 23:59:59', NULL, NULL, NULL, 'asasasaxaaaaasakakk kAMK', NULL, 'eyJpZCI6NjEsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDcxNjgyMH0=.1f21c108332eed0ddc89320b4f2093bf04d9ee88c5db7bc4bab60cc3534c791d', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', '2026-03-28 16:53:40', '2026-03-29 13:41:59'),
(62, 20, 14, 'NP-20260329-5251', 12, '2026-03-29 13:44:19', '2026-05-31 23:59:59', NULL, NULL, NULL, 'asasa', NULL, 'eyJpZCI6NjIsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDc3MzEwNH0=.3d2230e81074dd49ed4bb2f6911cf01ac9b24ba430f3b83fc2f14d20a6c7eaee', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', '2026-03-29 08:31:44', '2026-03-29 11:44:19'),
(63, 21, 1, 'NP-20260329-8481', 8, '2026-03-30 16:59:05', '2026-03-31 23:59:59', NULL, NULL, NULL, 'lkaa', NULL, 'eyJpZCI6NjMsImNvZGUiOiIiLCJ0aW1lIjoxNzc0NzkyMTgwfQ==.a69aaa9c595473ef784fe8d93aad22130fbf82af90a540ee7bd1a926d31c8c48', 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang. [catatan]', '2026-03-29 13:49:40', '2026-03-30 14:59:05'),
(64, 22, 1, 'NP-20260329-2474', 11, '2026-04-01 04:00:04', '2026-03-26 23:59:59', NULL, '2026-03-30 17:53:18', '2026-04-01 03:13:12', 'k;', NULL, 'eyJpZCI6NjQsImNvZGUiOiIiLCJ0aW1lIjoxNzc0Nzk0NTAxfQ==.20e6681bba209f5d156923912e744ff9f4ae48aa8ee023ff2cc452bd201bd646', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini', '2026-03-29 14:28:21', '2026-04-01 02:00:04'),
(65, 23, 2, 'NP-20260330-5507', 12, '2026-03-30 14:46:18', '2026-05-30 23:59:59', NULL, NULL, NULL, 'akjls', NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-03-30 06:21:16', '2026-03-30 12:46:18'),
(66, 24, 2, 'NP-20260330-1191', 12, '2026-03-30 14:13:00', '2026-05-30 23:59:59', NULL, NULL, NULL, 'akjls', NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-03-30 06:21:20', '2026-03-30 12:13:00'),
(67, 25, 2, 'NP-20260330-9369', 12, '2026-03-30 14:03:28', '2026-05-30 23:59:59', NULL, NULL, NULL, 'akjls', NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', '2026-03-30 06:21:33', '2026-03-30 12:03:28'),
(68, 26, 2, 'NP-20260330-7856', 13, '2026-03-30 13:29:56', '2026-05-30 23:59:59', NULL, '2026-03-30 18:29:56', NULL, 'akjls', NULL, NULL, 'Berkas dengan nomor NP-20260330-7856 telah diterima oleh tes penerima pada tanggal Monday, 30 March 2026', '2026-03-30 06:21:42', '2026-03-30 15:52:47'),
(69, 27, 2, 'NP-20260330-5448', 13, '2026-03-30 12:51:47', '2026-05-30 23:59:59', NULL, '2026-03-30 17:51:47', NULL, 'jlkm/,.', NULL, 'eyJpZCI6NjksImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDg1NTIxNn0=.dfa2048c4f2f922c1c012e32d9d41e4f64fee708d08a7ecb5e7d9f4062e12034', 'Berkas dengan nomor NP-20260330-5448 telah diterima oleh agus pada tanggal Monday, 30 March 2026', '2026-03-30 07:20:16', '2026-03-30 15:52:47'),
(70, 28, 5, 'NP-20260330-5373', 13, '2026-03-30 17:38:29', '2026-02-28 23:59:59', NULL, '2026-03-30 22:38:29', NULL, 'apaaja', NULL, 'eyJpZCI6NzAsImNvZGUiOiI4ODg1IiwidGltZSI6MTc3NDg4NDE0M30=.0a5dbd9bef793629d74567eb06b3ad07bbe273c92fc98f68c5551726bedb2212', 'Berkas dengan nomor NP-20260330-5373 telah diterima oleh ujicioba pada tanggal Monday, 30 March 2026', '2026-03-30 15:22:23', '2026-03-30 15:52:47'),
(71, 29, 2, 'NP-20260404-8541', 1, NULL, '2026-06-04 23:59:59', NULL, NULL, NULL, 'ascs', '', 'cc951463575defe0fd963fac8ec72824', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-04 09:37:59', '2026-04-04 09:37:59'),
(72, 30, 1, 'NP-20260404-0706', 3, NULL, '2026-06-04 23:59:59', NULL, NULL, NULL, 'jhkl', '', '3612dc8dfaabd3bde4a4d90ecc50a9b8', 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]', '2026-04-04 11:39:19', '2026-04-04 13:06:32'),
(73, 31, 1, 'NP-20260405-2917', 1, '2026-04-05 14:58:45', '2026-06-05 23:59:59', NULL, NULL, NULL, 'jlkl', NULL, 'eyJpZCI6NzMsImNvZGUiOiIiLCJ0aW1lIjoxNzc1Mzc1OTI1fQ==.f617584461e3be927863598be55813db821321216b6ae59f34b6217531395846', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', '2026-04-05 07:58:45', '2026-04-05 07:58:45');

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
(1, 6, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-02-26 14:15:04'),
(2, 1, 1, 2, 'Update', NULL, NULL, NULL, NULL, 0, NULL, 1, NULL, '2026-02-26 14:16:56'),
(3, 3, 5, 7, 'Update', NULL, NULL, NULL, 'Akta sedang dalam proses penomoran sebagai bagian dari penyelesaian dokumen.', 0, NULL, 2, '::1', '2026-02-26 14:18:20'),
(4, 3, 7, 7, 'Update', NULL, NULL, NULL, 'Akta sedang dalam proses penomoran sebagai bagian dari penyelesaian dokumen.', 0, 'Penomoran Akta', 2, '::1', '2026-02-26 14:19:12'),
(5, 3, 7, 8, 'Update', NULL, NULL, NULL, 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang.', 0, 'Pendaftaran', 2, '::1', '2026-02-26 15:23:35'),
(6, 3, 8, 9, 'Update', NULL, NULL, NULL, 'Pembayaran PNBP sedang diproses sebagai bagian dari tahapan lanjutan.', 0, NULL, 2, '::1', '2026-02-26 15:24:11'),
(7, 3, 9, 10, 'Update', NULL, NULL, NULL, 'Berkas perkara sedang dalam tahap pemeriksaan oleh instansi pertanahan.', 0, NULL, 2, '::1', '2026-02-26 15:33:08'),
(8, 3, 10, 10, 'Update', NULL, NULL, NULL, 'Berkas perkara sedang dalam tahap pemeriksaan oleh instansi pertanahan.', 0, 'Pemeriksaan BPN', 2, '::1', '2026-02-26 15:33:36'),
(9, 3, 10, 11, 'Update', NULL, NULL, NULL, 'Terdapat penyesuaian atau perbaikan administrasi yang sedang diselesaikan.', 0, 'Perbaikan', 2, '::1', '2026-02-26 15:39:36'),
(10, 8, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-02-27 01:34:29'),
(11, 5, 12, 12, 'Update', NULL, NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', 0, 'Selesai', 2, '::1', '2026-02-27 01:40:23'),
(12, 8, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-02-27 01:41:02'),
(13, 8, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-02-27 01:41:20'),
(14, 8, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-02-27 01:41:32'),
(15, 9, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-02-27 02:00:28'),
(16, 9, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-02-27 02:00:49'),
(17, 8, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-02-27 02:27:17'),
(18, 9, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-02-27 02:33:56'),
(19, 9, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, 'Draft / Pengumpulan Persyaratan', 2, '127.0.0.1', '2026-03-01 15:18:55'),
(20, 9, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-01 15:25:55'),
(21, 8, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-01 15:26:09'),
(22, 6, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-01 15:26:24'),
(23, 9, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-01 15:26:44'),
(24, 9, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-01 15:27:01'),
(25, 9, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-01 15:27:30'),
(26, 5, 12, 12, 'Update', NULL, NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', 0, NULL, 2, '::1', '2026-03-01 15:31:55'),
(27, 9, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-03-01 16:13:17'),
(28, 3, 11, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 03:01:11'),
(29, 8, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, '', 2, '::1', '2026-03-02 03:43:17'),
(30, 8, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, '', 2, '::1', '2026-03-02 03:43:30'),
(31, 8, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, '', 2, '::1', '2026-03-02 03:43:37'),
(32, 9, 2, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, 'Validasi Sertifikat', 2, '::1', '2026-03-02 03:49:49'),
(33, 9, 3, 4, 'Update', NULL, NULL, NULL, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, NULL, 2, '::1', '2026-03-02 03:50:45'),
(34, 9, 4, 4, 'Update', NULL, NULL, NULL, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, 'Pengecekan Sertifikat', 2, '::1', '2026-03-02 04:03:41'),
(35, 9, 4, 4, 'Update', NULL, NULL, NULL, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, NULL, 2, '::1', '2026-03-02 04:04:22'),
(36, 9, 4, 5, 'Update', NULL, NULL, NULL, 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-02 04:07:08'),
(37, 7, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-03-02 04:12:21'),
(38, 7, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-02 04:12:37'),
(39, 7, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara. aja', 0, NULL, 2, '::1', '2026-03-02 04:13:19'),
(40, 7, 2, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-02 04:13:34'),
(41, 7, 3, 9, 'Update', NULL, NULL, NULL, 'Pembayaran PNBP sedang diproses sebagai bagian dari tahapan lanjutan.', 0, NULL, 2, '::1', '2026-03-02 04:15:02'),
(42, 1, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-03-02 04:17:51'),
(43, 7, 9, 10, 'Update', NULL, NULL, NULL, 'Berkas perkara sedang dalam tahap pemeriksaan oleh instansi pertanahan.', 0, NULL, 2, '::1', '2026-03-02 04:26:53'),
(44, 7, 10, 12, 'Update', NULL, NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', 0, 'Selesai', 2, '::1', '2026-03-02 04:27:16'),
(45, 10, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 04:33:49'),
(46, 11, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 04:34:18'),
(47, 11, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-02 04:34:33'),
(48, 11, 2, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-03-02 04:34:40'),
(49, 10, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-02 04:36:03'),
(50, 10, 2, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, 'Validasi Sertifikat', 2, '::1', '2026-03-02 04:36:32'),
(51, 10, 3, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-02 04:37:12'),
(52, 12, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 04:46:35'),
(53, 12, 1, 12, 'Update', NULL, NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', 0, NULL, 2, '::1', '2026-03-02 04:47:38'),
(54, 13, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 04:51:55'),
(55, 14, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 04:51:58'),
(56, 15, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 04:52:01'),
(57, 15, 1, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-02 04:54:04'),
(58, 15, 3, 4, 'Update', NULL, NULL, NULL, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, 'Pengecekan Sertifikat', 2, '::1', '2026-03-02 04:54:26'),
(59, 14, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-02 04:56:36'),
(60, 14, 2, 5, 'Update', NULL, NULL, NULL, 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-02 04:56:48'),
(61, 14, 5, 12, 'Update', NULL, NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', 0, NULL, 2, '::1', '2026-03-02 04:57:01'),
(62, 16, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:00:11'),
(63, 16, 1, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, 'Validasi Sertifikat', 2, '::1', '2026-03-02 05:00:31'),
(64, 16, 3, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-02 05:00:39'),
(65, 17, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:18:17'),
(66, 17, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-02 05:19:21'),
(67, 18, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:28:10'),
(68, 19, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:28:35'),
(69, 19, 1, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, 'Validasi Sertifikat', 2, '::1', '2026-03-02 05:29:00'),
(70, 19, 3, 15, 'Update', NULL, NULL, NULL, 'Perkara ini dinyatakan batal dan tidak dilanjutkan ke tahap berikutnya.', 0, NULL, 2, '::1', '2026-03-02 05:29:13'),
(71, 20, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:36:34'),
(72, 21, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:37:36'),
(73, 22, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:38:05'),
(74, 23, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:40:02'),
(75, 24, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:43:47'),
(76, 25, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-02 05:44:43'),
(77, 19, 15, NULL, 'Update', NULL, NULL, NULL, '', 0, NULL, 2, '::1', '2026-03-02 07:01:06'),
(78, 19, 15, 14, 'Update', NULL, NULL, NULL, 'Perkara ditutup dari halaman Detail', 0, NULL, 2, '::1', '2026-03-02 07:47:00'),
(79, 17, 15, 14, 'Update', NULL, NULL, NULL, 'Perkara ditutup dari halaman Detail', 0, NULL, 2, '::1', '2026-03-02 07:48:54'),
(80, 13, 15, 14, 'Update', NULL, NULL, NULL, 'Perkara ditutup dari halaman Detail', 0, NULL, 2, '::1', '2026-03-02 07:49:19'),
(81, 16, 15, 14, 'Update', NULL, NULL, NULL, 'Perkara ditutup dari halaman Detail', 0, NULL, 2, '::1', '2026-03-02 07:49:42'),
(82, 25, 1, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, 'Validasi Sertifikat', 2, '::1', '2026-03-03 07:53:23'),
(83, 14, 12, 11, 'Update', NULL, NULL, NULL, 'Proses ulang dari halaman Detail - Back to Perbaikan', 0, NULL, 2, '::1', '2026-03-03 07:53:42'),
(84, 25, 3, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-04 07:20:41'),
(85, 26, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-04 07:22:17'),
(86, 26, 1, 6, 'Update', NULL, NULL, NULL, 'Pembayaran pajak sedang dalam tahap pemeriksaan dan validasi oleh pihak terkait.', 0, NULL, 2, '::1', '2026-03-04 07:22:53'),
(87, 27, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-04 07:30:32'),
(88, 28, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-04 07:43:12'),
(89, 28, 1, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-04 07:43:50'),
(90, 26, 6, 9, 'Update', NULL, NULL, NULL, 'Pembayaran PNBP sedang diproses sebagai bagian dari tahapan lanjutan.', 0, NULL, 2, '::1', '2026-03-04 07:44:41'),
(91, 26, 9, 11, 'Update', NULL, NULL, NULL, 'Terdapat penyesuaian atau perbaikan administrasi yang sedang diselesaikan.', 0, NULL, 2, '::1', '2026-03-04 07:44:54'),
(92, 26, 11, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-04 07:45:17'),
(93, 15, 15, 14, 'Update', NULL, NULL, NULL, 'Perkara ditutup dari halaman Detail', 0, NULL, 2, '::1', '2026-03-04 07:45:56'),
(94, 4, 12, 14, 'Update', NULL, NULL, NULL, 'Perkara ditutup dari halaman Detail', 0, NULL, 2, '::1', '2026-03-04 07:47:07'),
(95, 28, 3, 5, 'Update', NULL, NULL, NULL, 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-04 07:47:38'),
(96, 12, 12, 14, 'Update', NULL, NULL, NULL, 'Perkara ditutup dari halaman Detail', 0, NULL, 2, '::1', '2026-03-04 07:49:08'),
(97, 29, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-06 18:03:45'),
(98, 29, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-06 18:04:55'),
(99, 29, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-06 18:05:11'),
(100, 29, 2, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, 'Validasi Sertifikat', 2, '::1', '2026-03-07 03:09:48'),
(101, 29, 3, 12, 'Update', NULL, NULL, NULL, 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', 0, NULL, 1, '::1', '2026-03-07 15:43:08'),
(102, 29, 12, 13, 'Update', NULL, NULL, NULL, 'Perkara diserahkan ke klien', 0, NULL, 1, '::1', '2026-03-07 15:43:24'),
(103, 29, 13, 13, 'Update', NULL, NULL, NULL, 'Perkara diserahkan ke klien ahmad', 0, NULL, 1, '::1', '2026-03-07 15:43:56'),
(104, 30, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 1, '::1', '2026-03-07 15:45:25'),
(105, 30, 1, 13, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 1, '::1', '2026-03-07 15:45:53'),
(106, 27, 1, 13, 'Update', NULL, NULL, NULL, 'Terdapat penyesuaian atau perbaikan administrasi yang sedang diselesaikan.', 0, NULL, 2, '127.0.0.1', '2026-03-08 03:04:24'),
(107, 20, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, 'Draft / Pengumpulan Persyaratan', 2, '127.0.0.1', '2026-03-08 03:48:45'),
(108, 31, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan.', 0, NULL, 2, '::1', '2026-03-11 17:55:28'),
(109, 31, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-03-11 17:56:33'),
(110, 3, 15, 11, 'Update', NULL, NULL, NULL, 'Proses ulang dari halaman Detail - Back to Perbaikan', 0, NULL, 2, '::1', '2026-03-12 05:41:44'),
(111, 10, 15, 11, 'Update', NULL, NULL, NULL, 'Proses ulang dari halaman Detail - Back to Perbaikan', 0, NULL, 2, '::1', '2026-03-12 05:42:37'),
(112, 36, 1, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, 'Validasi Sertifikat', 2, '::1', '2026-03-13 03:03:50'),
(113, 43, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-25 05:36:14'),
(114, 43, 1, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-25 05:36:39'),
(115, 6, 12, 11, 'Update', NULL, NULL, NULL, 'Proses ulang dari halaman Detail - Back to Perbaikan', 0, NULL, 2, '::1', '2026-03-25 05:37:06'),
(116, 46, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-26 03:15:25'),
(117, 7, 12, 11, 'Update', NULL, NULL, NULL, 'Proses ulang dari halaman Detail - Back to Perbaikan', 0, NULL, 2, '::1', '2026-03-26 03:25:50'),
(118, 46, 1, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-26 03:26:16'),
(119, 47, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-26 03:26:28'),
(120, 48, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-26 03:32:08'),
(121, 49, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-26 03:33:32'),
(122, 50, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-26 03:35:33'),
(123, 51, NULL, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-03-26 03:36:55'),
(124, 52, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-26 03:37:34'),
(125, 53, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-26 03:43:24'),
(126, 54, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-26 03:52:06'),
(127, 55, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-27 02:34:54'),
(128, 3, 11, 3, 'Update', NULL, NULL, NULL, 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, 'Validasi Sertifikat', 2, '::1', '2026-03-28 13:56:25'),
(129, 56, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-28 14:33:50'),
(130, 57, NULL, 1, 'Update', NULL, NULL, NULL, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, NULL, 2, '::1', '2026-03-28 14:34:12'),
(131, 58, NULL, 1, 'Update', NULL, NULL, NULL, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, NULL, 2, '::1', '2026-03-28 14:34:33'),
(132, 59, NULL, 4, 'Update', NULL, NULL, NULL, 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, NULL, 2, '::1', '2026-03-28 14:38:52'),
(133, 59, 4, 8, 'Update', NULL, NULL, NULL, 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang.', 0, NULL, 2, '::1', '2026-03-28 15:18:35'),
(134, 59, 8, 8, 'Update', NULL, NULL, NULL, 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang.', 0, NULL, 2, '::1', '2026-03-28 15:18:35'),
(135, 59, 8, 11, 'Update', NULL, NULL, NULL, 'Terdapat penyesuaian atau perbaikan administrasi yang sedang diselesaikan.', 0, NULL, 2, '::1', '2026-03-28 15:18:56'),
(136, 59, 11, 11, 'Update', NULL, NULL, NULL, 'Terdapat penyesuaian atau perbaikan administrasi yang sedang diselesaikan.', 0, NULL, 2, '::1', '2026-03-28 15:18:57'),
(137, 59, 11, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-28 15:35:42'),
(138, 59, 1, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-28 15:35:42'),
(139, 60, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-28 16:30:58'),
(140, 61, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda notaris telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 2, '::1', '2026-03-28 16:53:40'),
(141, 61, 1, 1, 'Update', NULL, NULL, NULL, 'Update administratif data klien/SLA.', 0, NULL, 2, '::1', '2026-03-28 17:03:48'),
(142, 62, NULL, 2, 'Update', NULL, NULL, NULL, 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara.', 0, NULL, 2, '::1', '2026-03-29 08:31:44'),
(143, 62, 2, 3, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'asasa', 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-29 09:08:56'),
(144, 62, 3, 3, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'asasa', 'Sertifikat sedang diperiksa untuk memastikan data dan informasi sesuai dengan ketentuan yang berlaku.', 0, NULL, 2, '::1', '2026-03-29 09:08:56'),
(145, 62, 3, 3, 'Update', '2026-05-31 23:59:59', '2026-05-30 23:59:59', NULL, 'Update administratif data klien/SLA.', 0, NULL, 2, '::1', '2026-03-29 09:31:32'),
(146, 62, 3, 4, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, NULL, 2, '::1', '2026-03-29 09:32:04'),
(147, 62, 4, 4, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, NULL, 2, '::1', '2026-03-29 09:32:04'),
(148, 62, 4, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-29 09:47:30'),
(149, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-29 09:47:30'),
(150, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, 'Pembayaran Pajak', 2, '::1', '2026-03-29 09:52:11'),
(151, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-29 09:52:27'),
(152, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-29 09:52:27'),
(153, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, 'Pembayaran Pajak', 2, '::1', '2026-03-29 09:52:34'),
(154, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, 'Pembayaran Pajak', 2, '::1', '2026-03-29 09:52:34'),
(155, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-29 09:53:39'),
(156, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, 'Pembayaran Pajak', 2, '::1', '2026-03-29 09:53:45'),
(157, 62, 5, 5, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 2, '::1', '2026-03-29 09:53:58'),
(158, 62, 5, 6, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Pembayaran pajak sedang dalam tahap pemeriksaan dan validasi oleh pihak terkait.', 0, 'Validasi Pajak', 2, '::1', '2026-03-29 09:54:06'),
(159, 62, 6, 12, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', 0, 'Selesai', 2, '::1', '2026-03-29 11:44:19'),
(160, 62, 12, 12, 'Update', '2026-05-31 23:59:59', '2026-05-31 23:59:59', 'asasa', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir.', 0, 'Selesai', 2, '::1', '2026-03-29 11:44:19'),
(161, 61, 1, 4, 'Update', '2026-06-30 23:59:59', '2026-06-30 23:59:59', 'asasasaxaaaaasakakk kAMK', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat tidak memiliki kendala administrasi.', 0, 'Pengecekan Sertifikat', 2, '::1', '2026-03-29 13:41:59'),
(162, 63, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda admin telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 1, '::1', '2026-03-29 13:49:40'),
(163, 64, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda admin telah terdaftar dan saat ini sedang dalam tahap pengumpulan serta pemeriksaan awal persyaratan. untuk didaftarkan sama saya dong', 0, NULL, 1, '::1', '2026-03-29 14:28:21'),
(164, 64, 1, 1, 'Update', '2026-03-26 23:59:59', '2026-05-29 23:59:59', NULL, 'Update administratif data klien/SLA.', 0, NULL, 1, '::1', '2026-03-29 14:43:09'),
(165, 63, 1, 5, 'Update', '2026-03-31 23:59:59', '2026-03-31 23:59:59', 'lkaa', 'Proses pembayaran pajak yang berkaitan dengan perkara sedang dilaksanakan sesuai ketentuan.', 0, NULL, 1, '::1', '2026-03-29 15:46:26'),
(166, 64, 1, 7, 'Update', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Akta sedang dalam proses penomoran sebagai bagian dari legalitas dokumen Anda. [catatan]', 0, NULL, 2, '::1', '2026-03-30 04:47:31'),
(167, 63, 5, 6, 'Update', '2026-03-31 23:59:59', '2026-03-31 23:59:59', 'lkaa', 'Pembayaran pajak sedang dalam tahap pemeriksaan dan validasi oleh instansi terkait. [catatan]', 0, NULL, 2, '::1', '2026-03-30 04:48:08'),
(168, 63, 6, 7, 'Update', '2026-03-31 23:59:59', '2026-03-31 23:59:59', 'lkaa', 'Akta sedang dalam proses penomoran sebagai bagian dari legalitas dokumen Anda. [catatan]', 0, NULL, 2, '::1', '2026-03-30 04:48:17'),
(169, 63, 7, 8, 'Update', '2026-03-31 23:59:59', '2026-03-31 23:59:59', 'lkaa', 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang. [catatan]', 0, NULL, 2, '::1', '2026-03-30 04:48:24'),
(170, 64, 7, 8, 'Update', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang. [catatan]', 0, NULL, 2, '::1', '2026-03-30 04:52:07'),
(171, 64, 8, 9, 'Update', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Pembayaran PNBP sedang diproses sebagai bagian dari biaya resmi pendaftaran perkara. [catatan]', 0, NULL, 2, '::1', '2026-03-30 04:52:19'),
(172, 64, 9, 10, 'Update', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Berkas perkara sedang dalam tahap pemeriksaan oleh pihak BPN. [catatan]', 0, NULL, 2, '::1', '2026-03-30 04:55:18'),
(173, 64, 10, 10, 'Update', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Berkas perkara sedang dalam tahap pemeriksaan oleh pihak BPN. [catatan]', 0, NULL, 2, '::1', '2026-03-30 04:55:18'),
(174, 68, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 06:38:09'),
(175, 68, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 06:38:09'),
(176, 68, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 06:47:05'),
(177, 68, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 06:54:47'),
(178, 68, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 06:57:18'),
(179, 68, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 06:57:18'),
(180, 68, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 06:57:26'),
(181, 68, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 06:57:26'),
(182, 68, 1, 2, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-30 06:57:35'),
(183, 68, 2, 2, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-30 06:57:35'),
(184, 68, 2, 2, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pembayaran Administrasi', 2, '::1', '2026-03-30 06:57:41'),
(185, 68, 2, 3, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 06:57:55'),
(186, 68, 3, 3, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 06:57:55'),
(187, 68, 3, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pengecekan Sertifikat', 2, '::1', '2026-03-30 06:58:07'),
(188, 68, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pengecekan Sertifikat', 2, '::1', '2026-03-30 06:58:07'),
(189, 68, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 06:58:18'),
(190, 68, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 06:58:18'),
(191, 68, 4, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pembayaran Pajak', 2, '::1', '2026-03-30 07:00:12'),
(192, 68, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pembayaran Pajak', 2, '::1', '2026-03-30 07:00:13'),
(193, 68, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 07:00:19'),
(194, 68, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 07:00:19'),
(195, 68, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pembayaran Pajak', 2, '::1', '2026-03-30 07:13:50'),
(196, 68, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Pembayaran Pajak', 2, '::1', '2026-03-30 07:13:50'),
(197, 68, 5, 6, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Validasi Pajak', 2, '::1', '2026-03-30 07:14:02'),
(198, 68, 6, 6, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Validasi Pajak', 2, '::1', '2026-03-30 07:14:02'),
(199, 68, 6, 6, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 07:14:08'),
(200, 68, 6, 6, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 07:14:08'),
(201, 68, 6, 6, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, 'Validasi Pajak', 2, '::1', '2026-03-30 07:19:28'),
(202, 68, 6, 7, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 07:19:38'),
(203, 69, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 07:20:16'),
(204, 69, 1, 2, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 08:06:06'),
(205, 69, 2, 3, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 08:06:19'),
(206, 69, 3, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 1, 'Pengecekan Sertifikat', 2, '::1', '2026-03-30 08:44:21'),
(207, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 0, NULL, 2, '::1', '2026-03-30 08:44:30'),
(208, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 1, 'Pengecekan Sertifikat', 2, '::1', '2026-03-30 08:44:59'),
(209, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 0, NULL, 2, '::1', '2026-03-30 08:47:10'),
(210, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 1, 'Pengecekan Sertifikat', 2, '::1', '2026-03-30 08:47:51'),
(211, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 0, NULL, 2, '::1', '2026-03-30 08:48:19'),
(212, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 1, 'Pengecekan Sertifikat', 2, '::1', '2026-03-30 08:48:35'),
(213, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 0, NULL, 2, '::1', '2026-03-30 09:06:44'),
(214, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 1, 'Pengecekan Sertifikat', 2, '::1', '2026-03-30 09:07:15'),
(215, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 0, NULL, 2, '::1', '2026-03-30 09:24:36'),
(216, 69, 4, 4, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 1, 'Pengecekan Sertifikat', 2, '::1', '2026-03-30 09:24:43'),
(217, 69, 4, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', 0, NULL, 2, '::1', '2026-03-30 09:24:59'),
(218, 69, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', 1, 'Pembayaran Pajak', 2, '::1', '2026-03-30 09:31:20'),
(219, 69, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', 0, NULL, 2, '::1', '2026-03-30 09:31:29'),
(220, 69, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', 1, 'Pembayaran Pajak', 2, '::1', '2026-03-30 09:31:35'),
(221, 69, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', 0, NULL, 2, '::1', '2026-03-30 09:47:09'),
(222, 69, 5, 5, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]', 1, 'Pembayaran Pajak', 2, '::1', '2026-03-30 09:50:56'),
(223, 69, 5, 12, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 1, 'Selesai', 2, '::1', '2026-03-30 09:58:21'),
(224, 69, 12, 13, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'jlkm/,.', 'Berkas dengan nomor NP-20260330-5448 telah diterima oleh agus pada tanggal Monday, 30 March 2026', 0, NULL, 2, '::1', '2026-03-30 10:51:47'),
(225, 68, 7, 12, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 0, NULL, 2, '::1', '2026-03-30 11:29:27'),
(226, 68, 12, 13, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Berkas dengan nomor NP-20260330-7856 telah diterima oleh tes penerima pada tanggal Monday, 30 March 2026', 0, NULL, 2, '::1', '2026-03-30 11:29:56'),
(227, 67, 1, 12, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 1, 'Selesai', 2, '::1', '2026-03-30 12:03:28'),
(228, 66, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 1, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 12:04:31'),
(229, 66, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 12:12:41'),
(230, 66, 1, 1, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 1, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 12:12:49'),
(231, 66, 1, 12, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 1, 'Selesai', 2, '::1', '2026-03-30 12:13:00'),
(232, 65, 1, 10, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Berkas perkara sedang dalam tahap pemeriksaan oleh pihak BPN. [catatan]', 1, 'Pemeriksaan BPN', 2, '::1', '2026-03-30 12:46:10'),
(233, 65, 10, 12, 'Update', '2026-05-30 23:59:59', '2026-05-30 23:59:59', 'akjls', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 0, NULL, 2, '::1', '2026-03-30 12:46:18'),
(234, 63, 8, 8, 'Update', '2026-03-31 23:59:59', '2026-03-31 23:59:59', 'lkaa', 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang. [catatan]', 1, 'Pendaftaran', 2, '::1', '2026-03-30 14:59:05'),
(235, 70, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 15:22:23'),
(236, 70, 1, 1, 'Update', '2026-02-28 23:59:59', '2026-02-28 23:59:59', 'apaaja', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 1, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-03-30 15:22:34'),
(237, 70, 1, 1, 'Update', '2026-02-28 23:59:59', '2026-02-28 23:59:59', 'apaaja', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-03-30 15:22:39'),
(238, 70, 1, 12, 'Update', '2026-02-28 23:59:59', '2026-02-28 23:59:59', 'apaaja', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 0, NULL, 2, '::1', '2026-03-30 15:38:14'),
(239, 70, 12, 13, 'Update', '2026-02-28 23:59:59', '2026-02-28 23:59:59', 'apaaja', 'Berkas dengan nomor NP-20260330-5373 telah diterima oleh ujicioba pada tanggal Monday, 30 March 2026', 0, NULL, 2, '::1', '2026-03-30 15:38:29'),
(240, 64, 10, 12, 'Update', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]', 0, NULL, 2, '::1', '2026-03-30 15:53:09'),
(241, 64, 12, 13, 'Update', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Berkas dengan nomor NP-20260329-2474 telah diterima oleh ujicioba pada tanggal Monday, 30 March 2026', 0, NULL, 2, '::1', '2026-03-30 15:53:18'),
(242, 1, 15, 14, 'Finalisasi', '2026-04-30 20:07:24', '2026-04-30 20:07:24', 'alaka', 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk', 0, NULL, 2, '::1', '2026-03-31 12:22:48'),
(243, 64, 13, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda. yakkk bro', 0, NULL, 2, '::1', '2026-03-31 12:24:04'),
(244, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini cui nguba balikin', 0, NULL, 2, '::1', '2026-03-31 12:24:50'),
(245, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini', 0, NULL, 2, '::1', '2026-03-31 12:28:08'),
(246, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini saya nyobba tinjau', 0, NULL, 2, '::1', '2026-03-31 12:32:26');
INSERT INTO `registrasi_history` (`id`, `registrasi_id`, `status_old_id`, `status_new_id`, `action`, `target_completion_at_new`, `target_completion_at_old`, `keterangan`, `catatan`, `flag_kendala_active`, `flag_kendala_tahap`, `user_id`, `ip_address`, `created_at`) VALUES
(247, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini tinjau jadi gak', 0, NULL, 2, '::1', '2026-03-31 12:33:46'),
(248, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini', 0, NULL, 2, '::1', '2026-03-31 12:36:15'),
(249, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini ybku,', 0, NULL, 2, '::1', '2026-03-31 12:36:36'),
(250, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini', 0, NULL, 2, '::1', '2026-03-31 12:38:04'),
(251, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kamjjlnkjm,i proses untuk kelancaran perkara. [catatan] ini', 0, NULL, 2, '::1', '2026-03-31 12:38:57'),
(252, 64, 14, 14, 'Finalisasi', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini yak', 0, NULL, 2, '::1', '2026-04-01 01:13:12'),
(253, 64, 14, 11, 'Re-open', '2026-03-26 23:59:59', '2026-03-26 23:59:59', 'k;', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini', 0, NULL, 2, '::1', '2026-04-01 02:00:04'),
(254, 1, 14, 11, 'Re-open', '2026-04-30 20:07:24', '2026-04-30 20:07:24', 'alaka', 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini yak ditinjau', 0, NULL, 2, '::1', '2026-04-01 02:00:37'),
(255, 1, 11, 1, 'Update', '2026-04-30 20:07:24', '2026-04-30 20:07:24', 'alaka', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-01 02:08:27'),
(256, 1, 1, 1, 'Update', '2026-04-30 20:07:24', '2026-04-30 20:07:24', 'alaka', 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 1, 'Draft / Pengumpulan Persyaratan', 2, '::1', '2026-04-01 02:08:34'),
(257, 1, 1, 3, 'Update', '2026-04-30 20:07:24', '2026-04-30 20:07:24', 'alaka', 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]', 1, 'Validasi Sertifikat', 2, '::1', '2026-04-01 02:43:48'),
(258, 4, 14, 11, 'Re-open', '2026-04-30 22:27:32', '2026-04-30 22:27:32', NULL, 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan] ini dirubah', 0, NULL, 2, '::1', '2026-04-01 02:44:08'),
(259, 1, 3, 4, 'Update', '2026-04-30 20:07:24', '2026-04-30 20:07:24', 'alaka', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 1, 'Pengecekan Sertifikat', 2, '::1', '2026-04-03 13:02:48'),
(260, 1, 4, 4, 'Update', '2026-04-30 20:07:24', '2026-04-30 20:07:24', 'alaka', 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]', 0, NULL, 2, '::1', '2026-04-03 13:04:29'),
(261, 71, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-04 09:37:59'),
(262, 72, NULL, 1, 'Update', NULL, NULL, NULL, '', 0, NULL, 1, '::1', '2026-04-04 11:39:19'),
(263, 73, NULL, 1, 'Update', NULL, NULL, NULL, 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]', 0, NULL, 2, '::1', '2026-04-05 07:58:45');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `klien`
--
ALTER TABLE `klien`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `registrasi_history`
--
ALTER TABLE `registrasi_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

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
-- Constraints for table `kendala`
--
ALTER TABLE `kendala`
  ADD CONSTRAINT `kendala_ibfk_1` FOREIGN KEY (`registrasi_id`) REFERENCES `registrasi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_templates`
--
ALTER TABLE `note_templates`
  ADD CONSTRAINT `fk_workflow_step_id` FOREIGN KEY (`workflow_step_id`) REFERENCES `workflow_steps` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `registrasi`
--
ALTER TABLE `registrasi`
  ADD CONSTRAINT `registrasi_ibfk_1` FOREIGN KEY (`klien_id`) REFERENCES `klien` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrasi_ibfk_2` FOREIGN KEY (`layanan_id`) REFERENCES `layanan` (`id`);

--
-- Constraints for table `registrasi_history`
--
ALTER TABLE `registrasi_history`
  ADD CONSTRAINT `fk_hist_status_new` FOREIGN KEY (`status_new_id`) REFERENCES `workflow_steps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hist_status_old` FOREIGN KEY (`status_old_id`) REFERENCES `workflow_steps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `registrasi_history_ibfk_1` FOREIGN KEY (`registrasi_id`) REFERENCES `registrasi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrasi_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
