-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 25, 2026 at 05:56 PM
-- Server version: 10.11.16-MariaDB-cll-lve
-- PHP Version: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cfcy1736_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `latitude` decimal(11,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `foto_masuk` varchar(255) DEFAULT NULL,
  `foto_pulang` varchar(255) DEFAULT NULL,
  `face_valid` tinyint(1) NOT NULL DEFAULT 0,
  `face_confidence` decimal(5,2) DEFAULT NULL,
  `face_distance` decimal(10,6) DEFAULT NULL,
  `verification_method` enum('face','manual','qr','system') NOT NULL DEFAULT 'face',
  `status` enum('hadir','terlambat','cuti','alpa','sakit','libur') NOT NULL,
  `jenis_libur_pengganti` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `karyawan_id`, `tanggal`, `jam_masuk`, `jam_pulang`, `latitude`, `longitude`, `foto_masuk`, `foto_pulang`, `face_valid`, `face_confidence`, `face_distance`, `verification_method`, `status`, `jenis_libur_pengganti`, `created_at`, `updated_at`) VALUES
(3, 5, '2026-05-01', '08:41:00', '18:09:00', -6.93280000, 107.58980000, 'absensi/absensi_5_masuk_20260501_121608_ea61a7.jpg', 'absensi/absensi_5_pulang_20260501_121627_cdc1e6.jpg', 1, 78.22, 0.340662, 'face', 'hadir', NULL, '2026-05-01 05:14:26', '2026-05-01 05:19:25'),
(4, 6, '2026-05-01', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-01 05:14:26', '2026-05-01 05:14:26'),
(5, 5, '2026-05-02', '09:38:00', '18:00:00', -6.93280000, 107.58980000, 'absensi/absensi_5_masuk_20260502_093824_1e21d5.jpg', NULL, 1, 94.24, 0.342927, 'face', 'hadir', NULL, '2026-05-02 02:38:24', '2026-05-09 02:42:27'),
(6, 5, '2026-05-09', '10:10:52', '10:11:14', -6.93280000, 107.58980000, 'absensi/absensi_5_masuk_20260509_101051_bbfc55.jpg', 'absensi/absensi_5_pulang_20260509_101114_bf8647.jpg', 1, 87.15, 0.337756, 'face', 'terlambat', NULL, '2026-05-09 03:10:49', '2026-05-09 03:11:14'),
(7, 5, '2026-05-10', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-10 11:21:02', '2026-05-10 11:21:02'),
(8, 6, '2026-05-10', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-10 11:21:02', '2026-05-10 11:21:02'),
(9, 7, '2026-05-10', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-10 11:21:02', '2026-05-10 11:21:02'),
(10, 5, '2026-05-11', '11:00:55', '11:01:30', -6.93010005, 107.58703588, 'absensi/absensi_5_masuk_20260511_110055_8f93ab.jpg', 'absensi/absensi_5_pulang_20260511_110130_86ca7c.jpg', 1, 93.51, 0.367023, 'face', 'terlambat', NULL, '2026-05-10 17:00:04', '2026-05-11 04:01:30'),
(11, 6, '2026-05-11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-10 17:00:04', '2026-05-10 17:00:04'),
(12, 7, '2026-05-11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-10 17:00:04', '2026-05-10 17:00:04'),
(13, 5, '2026-05-12', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'libur', NULL, '2026-05-11 17:00:04', '2026-05-11 17:00:04'),
(14, 6, '2026-05-12', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-11 17:00:04', '2026-05-11 17:00:04'),
(15, 7, '2026-05-12', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-11 17:00:04', '2026-05-11 17:00:04'),
(16, 5, '2026-05-13', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-12 17:00:04', '2026-05-12 17:00:04'),
(17, 6, '2026-05-13', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-12 17:00:04', '2026-05-12 17:00:04'),
(18, 7, '2026-05-13', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-12 17:00:04', '2026-05-12 17:00:04'),
(19, 5, '2026-05-14', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'libur', NULL, '2026-05-13 17:00:05', '2026-05-13 17:00:05'),
(20, 6, '2026-05-14', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'libur', NULL, '2026-05-13 17:00:05', '2026-05-13 17:00:05'),
(21, 7, '2026-05-14', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'libur', NULL, '2026-05-13 17:00:05', '2026-05-13 17:00:05'),
(22, 5, '2026-05-15', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-14 17:00:04', '2026-05-14 17:00:04'),
(23, 6, '2026-05-15', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-14 17:00:04', '2026-05-14 17:00:04'),
(24, 7, '2026-05-15', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-14 17:00:04', '2026-05-14 17:00:04'),
(25, 5, '2026-05-16', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-15 17:00:04', '2026-05-15 17:00:04'),
(26, 6, '2026-05-16', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-15 17:00:04', '2026-05-15 17:00:04'),
(27, 7, '2026-05-16', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-15 17:00:04', '2026-05-15 17:00:04'),
(28, 5, '2026-05-17', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-16 17:00:04', '2026-05-16 17:00:04'),
(29, 6, '2026-05-17', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-16 17:00:04', '2026-05-16 17:00:04'),
(30, 7, '2026-05-17', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-16 17:00:04', '2026-05-16 17:00:04'),
(31, 5, '2026-05-18', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-17 17:00:03', '2026-05-17 17:00:03'),
(32, 6, '2026-05-18', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-17 17:00:03', '2026-05-17 17:00:03'),
(33, 7, '2026-05-18', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-17 17:00:03', '2026-05-17 17:00:03'),
(34, 5, '2026-05-19', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'libur', NULL, '2026-05-18 17:00:04', '2026-05-18 17:00:04'),
(35, 6, '2026-05-19', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-18 17:00:04', '2026-05-18 17:00:04'),
(36, 7, '2026-05-19', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-18 17:00:04', '2026-05-18 17:00:04'),
(37, 5, '2026-05-20', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-19 17:00:03', '2026-05-19 17:00:03'),
(38, 6, '2026-05-20', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-19 17:00:03', '2026-05-19 17:00:03'),
(39, 7, '2026-05-20', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-19 17:00:03', '2026-05-19 17:00:03'),
(40, 5, '2026-05-21', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-20 17:00:04', '2026-05-20 17:00:04'),
(41, 6, '2026-05-21', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-20 17:00:04', '2026-05-20 17:00:04'),
(42, 7, '2026-05-21', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-20 17:00:04', '2026-05-20 17:00:04'),
(43, 5, '2026-05-22', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-21 17:00:03', '2026-05-21 17:00:03'),
(44, 6, '2026-05-22', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-21 17:00:03', '2026-05-21 17:00:03'),
(45, 7, '2026-05-22', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-21 17:00:03', '2026-05-21 17:00:03'),
(46, 5, '2026-05-23', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-22 17:00:04', '2026-05-22 17:00:04'),
(47, 6, '2026-05-23', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-22 17:00:04', '2026-05-22 17:00:04'),
(48, 7, '2026-05-23', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-22 17:00:04', '2026-05-22 17:00:04'),
(49, 5, '2026-05-24', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-23 17:00:03', '2026-05-23 17:00:03'),
(50, 6, '2026-05-24', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-23 17:00:03', '2026-05-23 17:00:03'),
(51, 7, '2026-05-24', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-23 17:00:03', '2026-05-23 17:00:03'),
(52, 5, '2026-05-25', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-24 17:00:03', '2026-05-24 17:00:03'),
(53, 6, '2026-05-25', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-24 17:00:03', '2026-05-24 17:00:03'),
(54, 7, '2026-05-25', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'system', 'alpa', NULL, '2026-05-24 17:00:03', '2026-05-24 17:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `role`, `module`, `action`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 02:44:34', '2026-04-30 02:44:34'),
(2, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:06:16', '2026-04-30 03:06:16'),
(3, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:06:19', '2026-04-30 03:06:19'),
(4, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:06:23', '2026-04-30 03:06:23'),
(5, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:06:45', '2026-04-30 03:06:45'),
(6, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:13:30', '2026-04-30 03:13:30'),
(7, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:13:31', '2026-04-30 03:13:31'),
(8, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:14:01', '2026-04-30 03:14:01'),
(9, NULL, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: sandi@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:14:12', '2026-04-30 03:14:12'),
(10, NULL, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: sandi@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:14:34', '2026-04-30 03:14:34'),
(11, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:14:37', '2026-04-30 03:14:37'),
(12, 1, 'super_admin', 'karyawan', 'create', 'Menambahkan karyawan sandi', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:18:06', '2026-04-30 03:18:06'),
(13, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:18:13', '2026-04-30 03:18:13'),
(14, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:18:15', '2026-04-30 03:18:15'),
(15, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:18:19', '2026-04-30 03:18:19'),
(16, NULL, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: sandi@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:18:24', '2026-04-30 03:18:24'),
(17, NULL, 'karyawan', 'wajah', 'register', 'Berhasil mendaftarkan wajah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:32:37', '2026-04-30 03:32:37'),
(18, NULL, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:32:56', '2026-04-30 03:32:56'),
(19, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:32:58', '2026-04-30 03:32:58'),
(20, 1, 'super_admin', 'karyawan', 'create', 'Menambahkan karyawan Super Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:34:03', '2026-04-30 03:34:03'),
(21, 1, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:34:09', '2026-04-30 03:34:09'),
(22, 1, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: karyawan)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:34:17', '2026-04-30 03:34:17'),
(23, NULL, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: sandi@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:34:27', '2026-04-30 03:34:27'),
(24, NULL, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:35:01', '2026-04-30 03:35:01'),
(25, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:35:04', '2026-04-30 03:35:04'),
(26, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:35:07', '2026-04-30 03:35:07'),
(27, 1, 'admin', 'shift', 'create', 'Menambahkan shift pagi', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:36:22', '2026-04-30 03:36:22'),
(28, 1, 'admin', 'jadwal_shift', 'create', 'Menambahkan jadwal shift untuk departemen BOH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:36:46', '2026-04-30 03:36:46'),
(29, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:36:53', '2026-04-30 03:36:53'),
(30, NULL, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: sandi@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:36:59', '2026-04-30 03:36:59'),
(31, NULL, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:37:32', '2026-04-30 03:37:32'),
(32, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:37:34', '2026-04-30 03:37:34'),
(33, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:37:37', '2026-04-30 03:37:37'),
(34, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:37:55', '2026-04-30 03:37:55'),
(35, NULL, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: sandi@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:38:00', '2026-04-30 03:38:00'),
(36, NULL, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:38:40', '2026-04-30 03:38:40'),
(37, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:38:42', '2026-04-30 03:38:42'),
(38, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:38:45', '2026-04-30 03:38:45'),
(39, 1, 'admin', 'karyawan', 'update', 'Mengubah data karyawan Super Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 03:39:03', '2026-04-30 03:39:03'),
(40, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 04:01:19', '2026-04-30 04:01:19'),
(41, NULL, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: sandi@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 04:01:23', '2026-04-30 04:01:23'),
(42, NULL, 'karyawan', 'absensi', 'clock_in', 'Absen masuk pukul 11:02', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 04:02:02', '2026-04-30 04:02:02'),
(43, NULL, 'karyawan', 'absensi', 'clock_out', 'Absen pulang pukul 11:03', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 04:03:02', '2026-04-30 04:03:02'),
(44, NULL, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 04:03:27', '2026-04-30 04:03:27'),
(45, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 04:03:30', '2026-04-30 04:03:30'),
(46, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 04:03:33', '2026-04-30 04:03:33'),
(47, 1, 'admin', 'karyawan', 'create', 'Menambahkan karyawan arief', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 06:54:59', '2026-04-30 06:54:59'),
(48, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 06:55:54', '2026-04-30 06:55:54'),
(49, NULL, 'manager', 'auth', 'login', 'Login ke sistem dengan email: arief@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 06:56:03', '2026-04-30 06:56:03'),
(50, NULL, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 06:56:10', '2026-04-30 06:56:10'),
(51, NULL, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 07:30:43', '2026-04-30 07:30:43'),
(52, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 07:31:10', '2026-04-30 07:31:10'),
(53, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 07:31:14', '2026-04-30 07:31:14'),
(54, 1, 'admin', 'karyawan', 'create', 'Menambahkan karyawan arief', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 07:36:47', '2026-04-30 07:36:47'),
(55, 1, 'admin', 'karyawan', 'delete', 'Menghapus karyawan IT', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 07:36:54', '2026-04-30 07:36:54'),
(56, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 08:19:04', '2026-04-30 08:19:04'),
(57, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 08:19:21', '2026-04-30 08:19:21'),
(58, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 08:19:25', '2026-04-30 08:19:25'),
(59, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 08:19:57', '2026-04-30 08:19:57'),
(60, NULL, 'admin', 'auth', 'login', 'Login ke sistem dengan email: arief@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 08:20:25', '2026-04-30 08:20:25'),
(61, NULL, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 08:20:29', '2026-04-30 08:20:29'),
(62, NULL, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 10:01:35', '2026-04-30 10:01:35'),
(63, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 10:01:43', '2026-04-30 10:01:43'),
(64, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 10:01:47', '2026-04-30 10:01:47'),
(65, 1, 'admin', 'karyawan', 'update', 'Mengubah data karyawan arief', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 10:02:48', '2026-04-30 10:02:48'),
(66, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 14:01:50', '2026-04-30 14:01:50'),
(67, 1, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 14:01:57', '2026-04-30 14:01:57'),
(68, 1, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: karyawan)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 14:02:19', '2026-04-30 14:02:19'),
(69, NULL, 'admin', 'auth', 'login', 'Login ke sistem dengan email: arief@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 14:02:30', '2026-04-30 14:02:30'),
(70, NULL, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 14:02:44', '2026-04-30 14:02:44'),
(71, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 02:27:07', '2026-05-01 02:27:07'),
(72, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 02:27:25', '2026-05-01 02:27:25'),
(73, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 02:27:27', '2026-05-01 02:27:27'),
(74, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 02:39:23', '2026-05-01 02:39:23'),
(75, NULL, 'admin', 'auth', 'login', 'Login ke sistem dengan email: arief@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 02:39:33', '2026-05-01 02:39:33'),
(76, NULL, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 02:39:38', '2026-05-01 02:39:38'),
(77, NULL, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:45:31', '2026-05-01 03:45:31'),
(78, NULL, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: karyawan)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:46:49', '2026-05-01 03:46:49'),
(79, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:46:56', '2026-05-01 03:46:56'),
(80, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:47:01', '2026-05-01 03:47:01'),
(81, 1, 'admin', 'pengumuman', 'create', 'Membuat pengumuman: Libur may day', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:47:39', '2026-05-01 03:47:39'),
(82, 1, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:47:48', '2026-05-01 03:47:48'),
(83, 1, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: karyawan)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:48:02', '2026-05-01 03:48:02'),
(84, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:57:41', '2026-05-01 03:57:41'),
(85, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:57:45', '2026-05-01 03:57:45'),
(86, 1, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 03:58:25', '2026-05-01 03:58:25'),
(87, 1, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: karyawan)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 04:00:02', '2026-05-01 04:00:02'),
(88, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 04:00:16', '2026-05-01 04:00:16'),
(89, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 04:00:19', '2026-05-01 04:00:19'),
(90, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 04:00:26', '2026-05-01 04:00:26'),
(91, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 04:07:24', '2026-05-01 04:07:24'),
(92, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 04:07:28', '2026-05-01 04:07:28'),
(97, 1, 'admin', 'karyawan', 'delete', 'Menghapus karyawan arief', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 04:08:55', '2026-05-01 04:08:55'),
(99, 1, 'admin', 'karyawan', 'delete', 'Menghapus profil karyawan Super Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 04:17:12', '2026-05-01 04:17:12'),
(100, 1, 'admin', 'karyawan', 'create', 'Menambahkan karyawan FIKRI KURNIA PRADANA', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:01:00', '2026-05-01 05:01:00'),
(101, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:01:11', '2026-05-01 05:01:11'),
(102, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:01:23', '2026-05-01 05:01:23'),
(103, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:02:03', '2026-05-01 05:02:03'),
(104, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:02:14', '2026-05-01 05:02:14'),
(105, 19, 'karyawan', 'wajah', 'register', 'Berhasil mendaftarkan wajah', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:03:03', '2026-05-01 05:03:03'),
(106, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:03:29', '2026-05-01 05:03:29'),
(107, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:03:33', '2026-05-01 05:03:33'),
(108, 1, 'super_admin', 'karyawan', 'create', 'Menambahkan karyawan arief', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:04:35', '2026-05-01 05:04:35'),
(109, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:04:45', '2026-05-01 05:04:45'),
(110, 20, 'admin', 'auth', 'login', 'Login ke sistem dengan email: arief@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:04:55', '2026-05-01 05:04:55'),
(111, 20, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:04:58', '2026-05-01 05:04:58'),
(112, 20, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:07:46', '2026-05-01 05:07:46'),
(113, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:07:57', '2026-05-01 05:07:57'),
(114, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:08:27', '2026-05-01 05:08:27'),
(115, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:08:30', '2026-05-01 05:08:30'),
(116, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:08:53', '2026-05-01 05:08:53'),
(117, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:09:02', '2026-05-01 05:09:02'),
(118, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:09:19', '2026-05-01 05:09:19'),
(119, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:09:25', '2026-05-01 05:09:25'),
(120, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:09:47', '2026-05-01 05:09:47'),
(121, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:09:57', '2026-05-01 05:09:57'),
(122, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:10:51', '2026-05-01 05:10:51'),
(123, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:10:55', '2026-05-01 05:10:55'),
(124, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:15:47', '2026-05-01 05:15:47'),
(125, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:15:59', '2026-05-01 05:15:59'),
(126, 19, 'karyawan', 'absensi', 'clock_in', 'Absen masuk pukul 12:16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:16:08', '2026-05-01 05:16:08'),
(127, 19, 'karyawan', 'absensi', 'clock_out', 'Absen pulang pukul 12:16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:16:27', '2026-05-01 05:16:27'),
(128, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:18:33', '2026-05-01 05:18:33'),
(129, 20, 'admin', 'auth', 'login', 'Login ke sistem dengan email: arief@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:18:39', '2026-05-01 05:18:39'),
(130, 20, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:18:42', '2026-05-01 05:18:42'),
(131, 20, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:19:38', '2026-05-01 05:19:38'),
(132, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:19:44', '2026-05-01 05:19:44'),
(133, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:22:40', '2026-05-01 05:22:40'),
(134, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:22:45', '2026-05-01 05:22:45'),
(135, 1, 'super_admin', 'karyawan', 'update', 'Mengubah data karyawan FIKRI KURNIA PRADANA', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:22:54', '2026-05-01 05:22:54'),
(136, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:23:20', '2026-05-01 05:23:20'),
(137, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:27:45', '2026-05-01 05:27:45'),
(138, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 05:59:13', '2026-05-01 05:59:13'),
(139, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 06:20:55', '2026-05-01 06:20:55'),
(140, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 06:21:37', '2026-05-01 06:21:37'),
(141, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 06:21:49', '2026-05-01 06:21:49'),
(142, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 06:30:11', '2026-05-01 06:30:11'),
(143, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 06:30:16', '2026-05-01 06:30:16'),
(144, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 06:40:56', '2026-05-01 06:40:56'),
(145, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 06:41:09', '2026-05-01 06:41:09'),
(146, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 06:44:11', '2026-05-01 06:44:11'),
(147, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:43:38', '2026-05-01 07:43:38'),
(148, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:44:25', '2026-05-01 07:44:25'),
(149, 20, 'hrd', 'auth', 'login', 'Login ke sistem dengan email: arief@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:44:34', '2026-05-01 07:44:34'),
(150, 20, 'hrd', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:44:42', '2026-05-01 07:44:42'),
(151, 20, 'hrd', 'auth', 'login', 'Login ke sistem dengan email: arief@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:44:49', '2026-05-01 07:44:49'),
(152, 20, 'hrd', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:45:44', '2026-05-01 07:45:44'),
(153, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:52:18', '2026-05-01 07:52:18'),
(154, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:53:15', '2026-05-01 07:53:15'),
(155, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:53:23', '2026-05-01 07:53:23'),
(156, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:57:53', '2026-05-01 07:57:53'),
(157, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 07:57:56', '2026-05-01 07:57:56'),
(158, 1, 'super_admin', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-01 08:21:17', '2026-05-01 08:21:17'),
(159, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:30:21', '2026-05-02 02:30:21'),
(160, 1, 'super_admin', 'karyawan', 'create', 'Menambahkan karyawan ismail', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:32:35', '2026-05-02 02:32:35'),
(161, 1, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:32:41', '2026-05-02 02:32:41'),
(162, 1, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: karyawan)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:34:04', '2026-05-02 02:34:04'),
(163, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:34:14', '2026-05-02 02:34:14'),
(164, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:34:17', '2026-05-02 02:34:17'),
(165, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:36:22', '2026-05-02 02:36:22'),
(166, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:36:39', '2026-05-02 02:36:39'),
(167, 19, 'karyawan', 'absensi', 'clock_in', 'Absen masuk pukul 09:38', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 02:38:24', '2026-05-02 02:38:24'),
(168, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 09:51:28', '2026-05-04 09:51:28'),
(169, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-04 09:57:49', '2026-05-04 09:57:49'),
(170, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-04 09:58:05', '2026-05-04 09:58:05'),
(171, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 02:31:10', '2026-05-08 02:31:10'),
(172, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 02:31:19', '2026-05-08 02:31:19'),
(173, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 09:08:06', '2026-05-08 09:08:06'),
(174, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 09:08:09', '2026-05-08 09:08:09'),
(175, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 02:25:31', '2026-05-09 02:25:31'),
(176, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 02:25:40', '2026-05-09 02:25:40'),
(177, 1, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 03:08:27', '2026-05-09 03:08:27'),
(178, 1, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: karyawan)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 03:09:35', '2026-05-09 03:09:35'),
(179, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 03:10:13', '2026-05-09 03:10:13'),
(180, 19, 'karyawan', 'absensi', 'clock_in', 'Absen masuk pukul 10:10', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 03:10:52', '2026-05-09 03:10:52'),
(181, 19, 'karyawan', 'absensi', 'clock_out', 'Absen pulang pukul 10:11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 03:11:14', '2026-05-09 03:11:14'),
(182, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 03:28:50', '2026-05-09 03:28:50'),
(183, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 03:34:32', '2026-05-09 03:34:32'),
(184, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 05:14:50', '2026-05-09 05:14:50'),
(185, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 05:14:54', '2026-05-09 05:14:54'),
(186, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 05:14:58', '2026-05-09 05:14:58'),
(187, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 05:17:16', '2026-05-09 05:17:16'),
(188, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 05:17:27', '2026-05-09 05:17:27'),
(189, 1, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-10 11:22:04', '2026-05-10 11:22:04'),
(190, 1, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: karyawan)', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-10 11:22:43', '2026-05-10 11:22:43'),
(191, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-10 11:22:57', '2026-05-10 11:22:57'),
(192, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-10 11:23:00', '2026-05-10 11:23:00'),
(193, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '103.165.227.154', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 03:44:41', '2026-05-11 03:44:41'),
(194, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-11 03:48:45', '2026-05-11 03:48:45'),
(195, 19, 'karyawan', 'auth', 'logout', 'Logout dari sistem (mode: default)', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-11 03:55:13', '2026-05-11 03:55:13'),
(196, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-11 03:55:25', '2026-05-11 03:55:25'),
(197, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-11 03:55:28', '2026-05-11 03:55:28'),
(198, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '202.146.38.70', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2026-05-11 03:59:57', '2026-05-11 03:59:57'),
(199, 19, 'karyawan', 'absensi', 'clock_in', 'Absen masuk pukul 11:00', '202.146.38.70', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2026-05-11 04:00:55', '2026-05-11 04:00:55'),
(200, 19, 'karyawan', 'absensi', 'clock_out', 'Absen pulang pukul 11:01', '202.146.38.70', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2026-05-11 04:01:30', '2026-05-11 04:01:30');
INSERT INTO `activity_logs` (`id`, `user_id`, `role`, `module`, `action`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(201, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '202.146.38.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-11 04:06:48', '2026-05-11 04:06:48'),
(202, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '103.165.227.154', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 08:06:54', '2026-05-11 08:06:54'),
(203, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '103.165.227.154', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 08:06:59', '2026-05-11 08:06:59'),
(204, 1, 'admin', 'auth', 'logout', 'Logout dari sistem (mode: admin)', '103.165.227.154', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 08:07:35', '2026-05-11 08:07:35'),
(205, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '114.10.145.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 11:55:46', '2026-05-15 11:55:46'),
(206, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '114.10.145.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 11:55:54', '2026-05-15 11:55:54'),
(207, 1, 'karyawan', 'auth', 'select_mode', 'Memilih mode: Karyawan', '114.10.148.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 11:58:02', '2026-05-15 11:58:02'),
(208, 19, 'karyawan', 'auth', 'login', 'Login ke sistem dengan email: fikrikp92@gmail.com', '103.165.227.154', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-17 08:12:51', '2026-05-17 08:12:51'),
(209, 1, 'super_admin', 'auth', 'login', 'Login ke sistem dengan email: super_admin@gmail.com', '114.10.144.95', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-25 10:04:34', '2026-05-25 10:04:34'),
(210, 1, 'admin', 'auth', 'select_mode', 'Memilih mode: Admin', '114.10.144.95', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-25 10:04:38', '2026-05-25 10:04:38');

-- --------------------------------------------------------

--
-- Table structure for table `ajukan_shifts`
--

CREATE TABLE `ajukan_shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `departemen_id` bigint(20) UNSIGNED NOT NULL,
  `shift_lama_id` bigint(20) UNSIGNED NOT NULL,
  `shift_baru_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jenis` enum('sementara','permanen') NOT NULL DEFAULT 'sementara',
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `alasan` text DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `current_step` enum('manager','hrd','gm') DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cuti`
--

CREATE TABLE `cuti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan` text NOT NULL,
  `file_pendukung` varchar(255) DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `current_step` varchar(255) DEFAULT NULL,
  `is_bentrok` tinyint(1) NOT NULL DEFAULT 0,
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cuti_approvals`
--

CREATE TABLE `cuti_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cuti_id` bigint(20) UNSIGNED NOT NULL,
  `step` enum('manager','gm','hrd') NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--

CREATE TABLE `departemen` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'BOH', '2026-04-30 03:15:08', '2026-04-30 03:15:08'),
(2, 'Admin & General', '2026-04-30 07:36:04', '2026-04-30 07:36:04');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gaji`
--

CREATE TABLE `gaji` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `bulan` tinyint(3) UNSIGNED NOT NULL,
  `tahun` smallint(5) UNSIGNED NOT NULL,
  `gaji_harian` bigint(20) NOT NULL,
  `total_hadir` int(11) NOT NULL DEFAULT 0,
  `tanggal_hitung` date NOT NULL,
  `total_gaji` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gaji`
--

INSERT INTO `gaji` (`id`, `karyawan_id`, `bulan`, `tahun`, `gaji_harian`, `total_hadir`, `tanggal_hitung`, `total_gaji`, `created_at`, `updated_at`) VALUES
(11, 5, 5, 2026, 250000, 2, '2026-05-09', 500000, '2026-05-09 03:02:17', '2026-05-09 03:02:17'),
(12, 7, 5, 2026, 2222200, 0, '2026-05-09', 0, '2026-05-09 03:02:23', '2026-05-09 03:02:23'),
(13, 6, 5, 2026, 2222200, 0, '2026-05-09', 0, '2026-05-09 03:03:48', '2026-05-09 03:03:48');

-- --------------------------------------------------------

--
-- Table structure for table `hari_libur_nasional`
--

CREATE TABLE `hari_libur_nasional` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tipe` enum('fixed','dynamic','manual') NOT NULL DEFAULT 'dynamic',
  `bulan_tetap` tinyint(3) UNSIGNED DEFAULT NULL,
  `hari_tetap` tinyint(3) UNSIGNED DEFAULT NULL,
  `tahun` smallint(5) UNSIGNED DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hari_libur_nasional`
--

INSERT INTO `hari_libur_nasional` (`id`, `tanggal`, `nama`, `tipe`, `bulan_tetap`, `hari_tetap`, `tahun`, `is_recurring`, `keterangan`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '2026-01-01', 'Tahun Baru Masehi', 'dynamic', NULL, NULL, 2026, 0, 'Synced from API (ID)', 1, '2026-05-09 02:30:30', '2026-05-09 02:30:30'),
(2, '2026-04-03', 'Wafat Isa Almasih', 'dynamic', NULL, NULL, 2026, 0, 'Synced from API (ID)', 1, '2026-05-09 02:30:30', '2026-05-09 02:30:30'),
(3, '2026-04-05', 'Paskah', 'dynamic', NULL, NULL, 2026, 0, 'Synced from API (ID)', 1, '2026-05-09 02:30:30', '2026-05-09 02:30:30'),
(4, '2026-05-01', 'Hari Buruh Internasional', 'dynamic', NULL, NULL, 2026, 0, 'Synced from API (ID)', 1, '2026-05-09 02:30:30', '2026-05-09 02:30:30'),
(5, '2026-05-14', 'Kenaikan Isa Almasih', 'dynamic', NULL, NULL, 2026, 0, 'Synced from API (ID)', 1, '2026-05-09 02:30:30', '2026-05-09 02:30:30'),
(6, '2026-06-01', 'Hari Lahir Pancasila', 'dynamic', NULL, NULL, 2026, 0, 'Synced from API (ID)', 1, '2026-05-09 02:30:30', '2026-05-09 02:30:30'),
(7, '2026-08-17', 'Hari Ulang Tahun Kemerdekaan Republik Indonesia', 'dynamic', NULL, NULL, 2026, 0, 'Synced from API (ID)', 1, '2026-05-09 02:30:30', '2026-05-09 02:30:30'),
(8, '2026-12-25', 'Hari Raya Natal', 'dynamic', NULL, NULL, 2026, 0, 'Synced from API (ID)', 1, '2026-05-09 02:30:30', '2026-05-09 02:30:30'),
(20, '2026-01-16', 'Isra Mikraj Nabi Muhammad', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(21, '2026-02-16', 'Cuti Bersama Tahun Baru Imlek', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(22, '2026-02-17', 'Tahun Baru Imlek', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(23, '2026-02-19', '1 Ramadan', 'dynamic', NULL, NULL, 2026, 0, 'Perayaan', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(24, '2026-03-18', 'Cuti Bersama Hari Suci Nyepi (Tahun Baru Saka)', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(25, '2026-03-19', 'Hari Suci Nyepi (Tahun Baru Saka)', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(26, '2026-03-20', 'Cuti Bersama Idul Fitri', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(27, '2026-03-21', 'Hari Idul Fitri', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(28, '2026-03-22', 'Hari Idul Fitri', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(29, '2026-03-23', 'Cuti Bersama Idul Fitri', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(30, '2026-03-24', 'Cuti Bersama Idul Fitri', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(32, '2026-05-27', 'Idul Adha (Lebaran Haji)', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 05:16:50'),
(34, '2026-05-31', 'Hari Raya Waisak', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 05:17:06'),
(35, '2026-06-16', 'Satu Muharam / Tahun Baru Hijriah (belum pasti)', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(36, '2026-08-25', 'Maulid Nabi Muhammad (belum pasti)', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(37, '2026-12-24', 'Cuti Bersama Natal (Malam Natal)', 'dynamic', NULL, NULL, 2026, 0, 'Hari libur nasional', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00'),
(38, '2026-12-31', 'Malam Tahun Baru', 'dynamic', NULL, NULL, 2026, 0, 'Perayaan', 1, '2026-05-09 02:39:00', '2026-05-09 02:39:00');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_jabatan` varchar(255) NOT NULL,
  `jatah_cuti_bulanan` decimal(8,1) NOT NULL,
  `tipe_gaji` enum('harian','bulanan') NOT NULL,
  `gaji_pokok` bigint(20) DEFAULT NULL,
  `gaji_harian` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `nama_jabatan`, `jatah_cuti_bulanan`, `tipe_gaji`, `gaji_pokok`, `gaji_harian`, `created_at`, `updated_at`) VALUES
(1, 'Staff IT', 1.0, 'bulanan', 4700000, 250000, '2026-04-30 03:15:55', '2026-04-30 06:49:24'),
(2, 'Manager', 2.0, 'bulanan', 12222222, 2222200, '2026-04-30 06:54:09', '2026-04-30 06:54:18');

-- --------------------------------------------------------

--
-- Table structure for table `jatah_cuti`
--

CREATE TABLE `jatah_cuti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `tahun` year(4) NOT NULL,
  `jatah_awal` decimal(8,1) NOT NULL,
  `jatah` decimal(8,1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jatah_cuti`
--

INSERT INTO `jatah_cuti` (`id`, `karyawan_id`, `tahun`, `jatah_awal`, `jatah`, `created_at`, `updated_at`) VALUES
(5, 5, '2026', 0.0, 0.0, '2026-05-01 05:01:00', '2026-05-01 05:01:00'),
(6, 6, '2026', 0.0, 0.0, '2026-05-01 05:04:35', '2026-05-01 05:04:35'),
(7, 7, '2026', 0.0, 0.0, '2026-05-02 02:32:35', '2026-05-02 02:32:35');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_cuti`
--

CREATE TABLE `jenis_cuti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `butuh_file` tinyint(1) NOT NULL DEFAULT 0,
  `potong_jatah` tinyint(1) NOT NULL DEFAULT 1,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `departemen_id` bigint(20) UNSIGNED NOT NULL,
  `jabatan_id` bigint(20) UNSIGNED NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `no_telepon_tambahan` varchar(20) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') DEFAULT NULL,
  `status_pernikahan` enum('belum_menikah','menikah','cerai') DEFAULT NULL,
  `golongan_darah` varchar(5) DEFAULT NULL,
  `agama` varchar(20) DEFAULT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `alamat_ktp` text DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `alamat_tinggal` text DEFAULT NULL,
  `no_paspor` varchar(30) DEFAULT NULL,
  `masa_berlaku_paspor` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif',
  `wajah_terdaftar` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nip`, `user_id`, `departemen_id`, `jabatan_id`, `foto_profil`, `no_telepon`, `no_telepon_tambahan`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `status_pernikahan`, `golongan_darah`, `agama`, `nik`, `alamat_ktp`, `kode_pos`, `alamat_tinggal`, `no_paspor`, `masa_berlaku_paspor`, `alamat`, `status`, `wajah_terdaftar`, `created_at`, `updated_at`) VALUES
(5, '2331', 19, 1, 1, 'profiles/profile_5_1778472019.jpg', '085759323813', '-', 'Bandung', '2002-04-20', 'laki-laki', 'belum_menikah', 'O', 'islam', '327302004020002', 'jl. kaum cipaganti no 21/35 A RT 4 RW 9', '-', 'jl. kaum cipaganti no 21/35 A RT 4 RW 9', '-', NULL, NULL, 'aktif', 1, '2026-05-01 05:01:00', '2026-05-11 04:00:19'),
(6, '1232', 20, 1, 2, 'profiles/profile_6_1778412097.jpeg', '08272627716', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jl.bandung no22', 'aktif', 0, '2026-05-01 05:04:35', '2026-05-10 11:21:37'),
(7, '1884', 1, 2, 2, NULL, '09877899876675', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jl.kmo no 567', 'aktif', 0, '2026-05-02 02:32:35', '2026-05-02 02:32:35');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan_shift_pattern`
--

CREATE TABLE `karyawan_shift_pattern` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `hari` enum('minggu','senin','selasa','rabu','kamis','jumat','sabtu') NOT NULL,
  `tipe` enum('kerja','libur') NOT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `minggu_ke` tinyint(3) UNSIGNED DEFAULT NULL,
  `tahun` smallint(5) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karyawan_shift_pattern`
--

INSERT INTO `karyawan_shift_pattern` (`id`, `karyawan_id`, `hari`, `tipe`, `shift_id`, `is_default`, `minggu_ke`, `tahun`, `is_active`, `created_at`, `updated_at`) VALUES
(64, 5, 'senin', 'kerja', 1, 1, NULL, NULL, 1, '2026-05-11 03:59:19', '2026-05-11 03:59:19'),
(65, 5, 'selasa', 'libur', NULL, 1, NULL, NULL, 1, '2026-05-11 03:59:19', '2026-05-11 03:59:19'),
(66, 5, 'rabu', 'kerja', 1, 1, NULL, NULL, 1, '2026-05-11 03:59:19', '2026-05-11 03:59:19'),
(67, 5, 'kamis', 'kerja', 1, 1, NULL, NULL, 1, '2026-05-11 03:59:19', '2026-05-11 03:59:19'),
(68, 5, 'jumat', 'kerja', 1, 1, NULL, NULL, 1, '2026-05-11 03:59:19', '2026-05-11 03:59:19'),
(69, 5, 'sabtu', 'kerja', 1, 1, NULL, NULL, 1, '2026-05-11 03:59:19', '2026-05-11 03:59:19'),
(70, 5, 'minggu', 'kerja', 1, 1, NULL, NULL, 1, '2026-05-11 03:59:19', '2026-05-11 03:59:19');

-- --------------------------------------------------------

--
-- Table structure for table `libur_pengganti`
--

CREATE TABLE `libur_pengganti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `saldo` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `terakhir_diupdate` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `libur_pengganti_approvals`
--

CREATE TABLE `libur_pengganti_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengajuan_id` bigint(20) UNSIGNED NOT NULL,
  `step` enum('manager','hrd','gm') NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lokasi_kantor`
--

CREATE TABLE `lokasi_kantor` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_lokasi` varchar(255) NOT NULL,
  `latitude` decimal(11,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `radius` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lokasi_kantor`
--

INSERT INTO `lokasi_kantor` (`id`, `nama_lokasi`, `latitude`, `longitude`, `radius`, `created_at`, `updated_at`) VALUES
(1, 'HARRIS Hotel & Conventions Festival Citylink Bandung', -6.92484400, 107.58914400, 1000, '2026-04-30 03:17:34', '2026-05-01 05:09:40');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_01_14_162901_create_departemens_table', 1),
(6, '2025_11_01_155300_create_table_jenis_cuti', 1),
(7, '2025_12_17_020105_create_jabatans_table', 1),
(8, '2025_12_17_034904_create_karyawans_table', 1),
(9, '2025_12_17_041205_create_absensis_table', 1),
(10, '2025_12_17_044609_create_gajis_table', 1),
(11, '2025_12_17_082240_create_lokasi_kantors_table', 1),
(12, '2025_12_25_094009_create_cutis_table', 1),
(13, '2025_12_31_081202_create_wajah_karyawan_table', 1),
(14, '2026_01_08_135002_create_notifikasis_table', 1),
(15, '2026_01_15_143826_create_shifts_table', 1),
(16, '2026_01_15_144828_create_ajukan_shifts_table', 1),
(17, '2026_01_15_164147_create_jadwal_shift_table', 1),
(18, '2026_01_24_155445_create_jatah_cutis_table', 1),
(19, '2026_01_29_112250_create_activity_logs_table', 1),
(20, '2026_02_03_155620_create_cuti_approvals_table', 1),
(21, '2026_03_15_134306_create_wajah_requests_table', 1),
(22, '2026_03_17_121954_add_alamat_telepon_to_karyawan_table', 1),
(23, '2026_03_24_093231_create_karyawan_shift_patterns_table', 1),
(24, '2026_03_25_112455_create_libur_penggantis_table', 1),
(25, '2026_03_25_113442_create_hari_libur_nasionals_table', 1),
(26, '2026_04_15_023133_create_jobs_table', 2),
(27, '2026_04_17_190810_create_pengajuan_libur_penggantis_table', 3),
(28, '2026_04_17_191046_create_libur_pengganti_approvals_table', 3),
(29, '2026_04_19_135614_add_current_step_to_ajukan_shifts_table', 4),
(31, '2026_04_19_143122_add_current_step_to_ajukan_shifts_table', 5),
(32, '2026_04_19_143301_create_shift_approvals_table', 5),
(33, '2026_04_30_111514_add_shift_id_to_karyawan_shift_pattern', 6),
(34, '2026_04_30_111818_drop_jadwal_shift_table', 6),
(35, '2026_04_30_132613_add_jatah_cuti_bulanan_to_jabatan_table', 7),
(36, '2026_05_01_100610_create_pengumumen_table', 8),
(37, '2026_05_01_130400_add_personal_identity_to_karyawan_table', 9),
(38, '2026_05_08_105440_change_cuti_columns_to_decimal', 10),
(39, '2026_05_09_100057_fix_unique_index_on_gaji_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `target_role` varchar(255) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pesan` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `user_id`, `target_role`, `judul`, `pesan`, `type`, `is_read`, `created_at`, `updated_at`) VALUES
(3, 19, 'karyawan', 'Absen Berhasil', 'Anda berhasil melakukan absen masuk hari ini di HARRIS Hotel & Conventions Festival Citylink Bandung.', 'absensi', 0, '2026-05-01 05:16:08', '2026-05-01 05:16:08'),
(4, 19, 'karyawan', 'Absen Pulang Berhasil', 'Anda berhasil melakukan absen pulang. Hati-hati di jalan!', 'absensi', 0, '2026-05-01 05:16:27', '2026-05-01 05:16:27'),
(5, 19, 'karyawan', 'Absen Berhasil', 'Anda berhasil melakukan absen masuk hari ini di HARRIS Hotel & Conventions Festival Citylink Bandung.', 'absensi', 0, '2026-05-02 02:38:24', '2026-05-02 02:38:24'),
(6, 19, 'karyawan', 'Absen Berhasil', 'Anda berhasil melakukan absen masuk hari ini di HARRIS Hotel & Conventions Festival Citylink Bandung.', 'absensi', 0, '2026-05-09 03:10:52', '2026-05-09 03:10:52'),
(7, 19, 'karyawan', 'Absen Pulang Berhasil', 'Anda berhasil melakukan absen pulang. Hati-hati di jalan!', 'absensi', 0, '2026-05-09 03:11:14', '2026-05-09 03:11:14'),
(8, 19, 'karyawan', 'Absen Berhasil', 'Anda berhasil melakukan absen masuk hari ini di HARRIS Hotel & Conventions Festival Citylink Bandung.', 'absensi', 0, '2026-05-11 04:00:55', '2026-05-11 04:00:55'),
(9, 19, 'karyawan', 'Absen Pulang Berhasil', 'Anda berhasil melakukan absen pulang. Hati-hati di jalan!', 'absensi', 0, '2026-05-11 04:01:30', '2026-05-11 04:01:30');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_libur_pengganti`
--

CREATE TABLE `pengajuan_libur_pengganti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `alasan` text NOT NULL,
  `file_pendukung` varchar(255) DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `current_step` enum('manager','hrd','gm') DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pembuat_id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text NOT NULL,
  `tipe` enum('global','departemen') NOT NULL DEFAULT 'global',
  `departemen_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `pembuat_id`, `judul`, `konten`, `tipe`, `departemen_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Libur may day', 'selamat beristirahat', 'global', NULL, '2026-05-01 03:47:39', '2026-05-01 03:47:39');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `toleransi_menit` int(11) NOT NULL DEFAULT 0,
  `lintas_hari` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`id`, `kode`, `jam_masuk`, `jam_pulang`, `toleransi_menit`, `lintas_hari`, `created_at`, `updated_at`) VALUES
(1, 'pagi', '09:00:00', '18:00:00', 30, 0, '2026-04-30 03:36:22', '2026-04-30 03:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `shift_approvals`
--

CREATE TABLE `shift_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ajukan_shift_id` bigint(20) UNSIGNED NOT NULL,
  `step` enum('manager','hrd','gm') NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(40) NOT NULL,
  `status` enum('aktif','pending','nonaktif') NOT NULL DEFAULT 'pending',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'ismail', 'super_admin@gmail.com', '$2y$10$QsKOMNLxpyZbrK/G730vEu1qB8KusQYetQ2ZIeWCpObHNmwX6Uhhq', 'super_admin', 'aktif', NULL, '2026-03-26 04:22:41', '2026-05-02 02:31:29'),
(18, 'kris', 'kris@gmail.com', '$2y$10$ZBeQ80HC5dHxkq/TdMKVOejwK8lTxe3G8.CAFLkKTknfKSPFYQjfy', 'gm', 'aktif', NULL, '2026-04-30 10:02:24', '2026-04-30 10:02:24'),
(19, 'FIKRI KURNIA PRADANA', 'fikrikp92@gmail.com', '$2y$10$QgPuLTj71g0VaHM8ZSZpvelxfAU2V0H/9FeLPCPtEsbFa5B4QVX8G', 'karyawan', 'aktif', NULL, '2026-05-01 05:00:09', '2026-05-01 05:00:09'),
(20, 'arief', 'arief@gmail.com', '$2y$10$GwUHikrok41FYfI4XxgZU.AOgtDrzSHHFQK.dKdRJb3jJEfoX6.tu', 'admin', 'aktif', NULL, '2026-05-01 05:04:01', '2026-05-01 07:52:45');

-- --------------------------------------------------------

--
-- Table structure for table `wajah_karyawan`
--

CREATE TABLE `wajah_karyawan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `face_encoding` longtext DEFAULT NULL,
  `face_image` longtext DEFAULT NULL,
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `registered_at` timestamp NULL DEFAULT NULL,
  `registered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wajah_karyawan`
--

INSERT INTO `wajah_karyawan` (`id`, `karyawan_id`, `face_encoding`, `face_image`, `confidence_score`, `registered_at`, `registered_by`, `created_at`, `updated_at`) VALUES
(4, 5, '\"[-0.12082764506340027,0.10963604599237442,0.03167632967233658,-0.03314010053873062,-0.04722192510962486,-0.05086836218833923,-0.015455370768904686,-0.1164502277970314,0.14853423833847046,-0.08634886890649796,0.23255988955497742,0.014706038869917393,-0.16561131179332733,-0.17076559364795685,-0.02736734226346016,0.15163832902908325,-0.1873919516801834,-0.20153675973415375,0.02272123470902443,-0.047076888382434845,0.03859654814004898,-0.03416074067354202,0.0004555823397822678,0.0684208869934082,-0.05790761113166809,-0.3448905944824219,-0.08738579601049423,-0.11988937854766846,0.003498523496091366,-0.03080563247203827,0.028361696749925613,0.07253280282020569,-0.18594659864902496,-0.06777817010879517,-0.01619001105427742,0.09907764941453934,0.00429233442991972,0.04505375400185585,0.2028018981218338,0.022085487842559814,-0.16120439767837524,-0.0389443077147007,-0.017417918890714645,0.3181215524673462,0.24657942354679108,0.05128622055053711,0.014899837784469128,-0.034466780722141266,0.07832764089107513,-0.1695706695318222,0.019881408661603928,0.14311881363391876,0.14283767342567444,0.03930293768644333,-0.02437770739197731,-0.09426948428153992,0.001153913326561451,0.053839266300201416,-0.16783000528812408,0.03163134306669235,-0.008493001572787762,-0.16660431027412415,0.010698677971959114,-0.027653152123093605,0.3107357621192932,0.05017370730638504,-0.08655064553022385,-0.15131278336048126,0.1893187314271927,-0.16006402671337128,-0.12649370729923248,0.05869762599468231,-0.14051169157028198,-0.13337334990501404,-0.31403785943984985,0.05206611007452011,0.39923977851867676,0.08716575056314468,-0.1838783323764801,0.06160412356257439,-0.079742930829525,-0.03982562571763992,0.09185099601745605,0.12946327030658722,-0.026640987023711205,0.008830545470118523,-0.1258198767900467,-0.051103901118040085,0.21787573397159576,-0.027114296332001686,-0.11239564418792725,0.16415148973464966,-0.07488670945167542,0.09377396106719971,0.06353926658630371,-0.0063657453283667564,-0.0022448496893048286,0.0732552781701088,-0.1399243175983429,-0.06642909348011017,0.008582907728850842,-0.04746052995324135,0.007041171658784151,0.09475568681955338,-0.1219044104218483,0.0927048772573471,-0.024460868909955025,0.06035982817411423,-0.0378076434135437,0.016894763335585594,-0.13361427187919617,-0.07417669147253036,0.18097613751888275,-0.19442711770534515,0.25171783566474915,0.17932617664337158,0.016313333064317703,0.13743944466114044,0.11523763090372086,0.07295394688844681,-0.03327148035168648,-0.058292850852012634,-0.15482716262340546,-0.006537375971674919,0.09509029984474182,-0.02549925446510315,0.1530235856771469,0.03327149897813797]\"', NULL, NULL, NULL, NULL, '2026-05-01 05:03:03', '2026-05-01 05:03:03');

-- --------------------------------------------------------

--
-- Table structure for table `wajah_requests`
--

CREATE TABLE `wajah_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `catatan_admin` varchar(255) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `captured_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensi_karyawan_id_foreign` (`karyawan_id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `ajukan_shifts`
--
ALTER TABLE `ajukan_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ajukan_shifts_departemen_id_foreign` (`departemen_id`),
  ADD KEY `ajukan_shifts_shift_lama_id_foreign` (`shift_lama_id`),
  ADD KEY `ajukan_shifts_shift_baru_id_foreign` (`shift_baru_id`),
  ADD KEY `ajukan_shifts_requested_by_foreign` (`requested_by`),
  ADD KEY `ajukan_shifts_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `cuti`
--
ALTER TABLE `cuti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuti_karyawan_id_foreign` (`karyawan_id`),
  ADD KEY `cuti_jenis_id_foreign` (`jenis_id`);

--
-- Indexes for table `cuti_approvals`
--
ALTER TABLE `cuti_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuti_approvals_cuti_id_foreign` (`cuti_id`),
  ADD KEY `cuti_approvals_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `departemen`
--
ALTER TABLE `departemen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gaji`
--
ALTER TABLE `gaji`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gaji_karyawan_bulan_tahun_unique` (`karyawan_id`,`bulan`,`tahun`),
  ADD KEY `gaji_karyawan_id_foreign` (`karyawan_id`);

--
-- Indexes for table `hari_libur_nasional`
--
ALTER TABLE `hari_libur_nasional`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hari_libur_nasional_tanggal_unique` (`tanggal`),
  ADD KEY `hari_libur_nasional_tanggal_is_active_index` (`tanggal`,`is_active`),
  ADD KEY `hari_libur_nasional_tipe_tahun_index` (`tipe`,`tahun`),
  ADD KEY `hari_libur_nasional_is_recurring_is_active_index` (`is_recurring`,`is_active`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jatah_cuti`
--
ALTER TABLE `jatah_cuti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jatah_cuti_karyawan_id_foreign` (`karyawan_id`);

--
-- Indexes for table `jenis_cuti`
--
ALTER TABLE `jenis_cuti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `karyawan_nip_unique` (`nip`),
  ADD KEY `karyawan_user_id_foreign` (`user_id`),
  ADD KEY `karyawan_departemen_id_foreign` (`departemen_id`),
  ADD KEY `karyawan_jabatan_id_foreign` (`jabatan_id`);

--
-- Indexes for table `karyawan_shift_pattern`
--
ALTER TABLE `karyawan_shift_pattern`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ksp_default_pattern` (`karyawan_id`,`is_default`,`is_active`),
  ADD KEY `idx_ksp_weekly_pattern` (`karyawan_id`,`minggu_ke`,`tahun`,`is_active`),
  ADD KEY `idx_ksp_karyawan_hari` (`karyawan_id`,`hari`,`is_active`),
  ADD KEY `karyawan_shift_pattern_shift_id_foreign` (`shift_id`);

--
-- Indexes for table `libur_pengganti`
--
ALTER TABLE `libur_pengganti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `libur_pengganti_karyawan_id_unique` (`karyawan_id`),
  ADD KEY `libur_pengganti_karyawan_id_index` (`karyawan_id`);

--
-- Indexes for table `libur_pengganti_approvals`
--
ALTER TABLE `libur_pengganti_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `libur_pengganti_approvals_pengajuan_id_foreign` (`pengajuan_id`),
  ADD KEY `libur_pengganti_approvals_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `lokasi_kantor`
--
ALTER TABLE `lokasi_kantor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifikasi_user_id_is_read_index` (`user_id`,`is_read`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pengajuan_libur_pengganti`
--
ALTER TABLE `pengajuan_libur_pengganti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_libur_pengganti_karyawan_id_foreign` (`karyawan_id`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengumuman_pembuat_id_foreign` (`pembuat_id`),
  ADD KEY `pengumuman_departemen_id_foreign` (`departemen_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift_approvals`
--
ALTER TABLE `shift_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shift_approvals_ajukan_shift_id_foreign` (`ajukan_shift_id`),
  ADD KEY `shift_approvals_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wajah_karyawan`
--
ALTER TABLE `wajah_karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wajah_karyawan_karyawan_id_unique` (`karyawan_id`),
  ADD KEY `wajah_karyawan_registered_by_foreign` (`registered_by`);

--
-- Indexes for table `wajah_requests`
--
ALTER TABLE `wajah_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wajah_requests_karyawan_id_foreign` (`karyawan_id`),
  ADD KEY `wajah_requests_user_id_foreign` (`user_id`),
  ADD KEY `wajah_requests_reviewed_by_foreign` (`reviewed_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `ajukan_shifts`
--
ALTER TABLE `ajukan_shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cuti`
--
ALTER TABLE `cuti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cuti_approvals`
--
ALTER TABLE `cuti_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departemen`
--
ALTER TABLE `departemen`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gaji`
--
ALTER TABLE `gaji`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `hari_libur_nasional`
--
ALTER TABLE `hari_libur_nasional`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jatah_cuti`
--
ALTER TABLE `jatah_cuti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jenis_cuti`
--
ALTER TABLE `jenis_cuti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `karyawan_shift_pattern`
--
ALTER TABLE `karyawan_shift_pattern`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `libur_pengganti`
--
ALTER TABLE `libur_pengganti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `libur_pengganti_approvals`
--
ALTER TABLE `libur_pengganti_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lokasi_kantor`
--
ALTER TABLE `lokasi_kantor`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pengajuan_libur_pengganti`
--
ALTER TABLE `pengajuan_libur_pengganti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shift_approvals`
--
ALTER TABLE `shift_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `wajah_karyawan`
--
ALTER TABLE `wajah_karyawan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wajah_requests`
--
ALTER TABLE `wajah_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ajukan_shifts`
--
ALTER TABLE `ajukan_shifts`
  ADD CONSTRAINT `ajukan_shifts_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ajukan_shifts_departemen_id_foreign` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ajukan_shifts_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ajukan_shifts_shift_baru_id_foreign` FOREIGN KEY (`shift_baru_id`) REFERENCES `shift` (`id`),
  ADD CONSTRAINT `ajukan_shifts_shift_lama_id_foreign` FOREIGN KEY (`shift_lama_id`) REFERENCES `shift` (`id`);

--
-- Constraints for table `cuti`
--
ALTER TABLE `cuti`
  ADD CONSTRAINT `cuti_jenis_id_foreign` FOREIGN KEY (`jenis_id`) REFERENCES `jenis_cuti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cuti_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cuti_approvals`
--
ALTER TABLE `cuti_approvals`
  ADD CONSTRAINT `cuti_approvals_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cuti_approvals_cuti_id_foreign` FOREIGN KEY (`cuti_id`) REFERENCES `cuti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gaji`
--
ALTER TABLE `gaji`
  ADD CONSTRAINT `gaji_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jatah_cuti`
--
ALTER TABLE `jatah_cuti`
  ADD CONSTRAINT `jatah_cuti_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_departemen_id_foreign` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `karyawan_jabatan_id_foreign` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `karyawan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `karyawan_shift_pattern`
--
ALTER TABLE `karyawan_shift_pattern`
  ADD CONSTRAINT `karyawan_shift_pattern_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `karyawan_shift_pattern_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shift` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `libur_pengganti`
--
ALTER TABLE `libur_pengganti`
  ADD CONSTRAINT `libur_pengganti_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `libur_pengganti_approvals`
--
ALTER TABLE `libur_pengganti_approvals`
  ADD CONSTRAINT `libur_pengganti_approvals_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `libur_pengganti_approvals_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_libur_pengganti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_libur_pengganti`
--
ALTER TABLE `pengajuan_libur_pengganti`
  ADD CONSTRAINT `pengajuan_libur_pengganti_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD CONSTRAINT `pengumuman_departemen_id_foreign` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengumuman_pembuat_id_foreign` FOREIGN KEY (`pembuat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shift_approvals`
--
ALTER TABLE `shift_approvals`
  ADD CONSTRAINT `shift_approvals_ajukan_shift_id_foreign` FOREIGN KEY (`ajukan_shift_id`) REFERENCES `ajukan_shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shift_approvals_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wajah_karyawan`
--
ALTER TABLE `wajah_karyawan`
  ADD CONSTRAINT `wajah_karyawan_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wajah_karyawan_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wajah_requests`
--
ALTER TABLE `wajah_requests`
  ADD CONSTRAINT `wajah_requests_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wajah_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `wajah_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
