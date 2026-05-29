-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 11:33 AM
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
-- Database: `housing`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `actor_user_id` int(11) DEFAULT NULL,
  `actor_role` varchar(30) DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `actor_user_id`, `actor_role`, `activity_type`, `entity_type`, `entity_id`, `metadata`, `ip_address`, `created_at`) VALUES
(1, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-27 13:53:09'),
(2, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 13:56:04'),
(3, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-27 13:56:04'),
(4, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-27 14:22:38'),
(5, 0, 'guest', 'register', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 14:24:56'),
(6, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 14:25:06'),
(7, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 14:25:06'),
(8, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 14:25:15'),
(9, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 14:25:31'),
(10, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-27 14:25:31'),
(11, 0, 'guest', 'register', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-27 14:48:36'),
(12, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 14:49:00'),
(13, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-27 14:49:00'),
(14, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-27 14:49:27'),
(15, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 14:49:47'),
(16, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 14:49:47'),
(17, 160, 'seller', 'create', 'property', 20, '{\"title\":\"Duplex House\",\"price\":\"16000000\",\"location\":\"Cogon, Ormoc City\",\"source\":\"seller\"}', '::1', '2026-05-27 14:56:03'),
(18, 160, 'seller', 'archive', 'property', 19, '{\"title\":\"Modern House\",\"price\":\"13000000.00\",\"location\":\"Tacloban, City\",\"source\":\"seller\"}', '::1', '2026-05-27 15:05:33'),
(19, 160, 'seller', 'archive', 'property', 19, '{\"title\":\"Modern House\",\"price\":\"13000000.00\",\"location\":\"Tacloban, City\",\"source\":\"seller\"}', '::1', '2026-05-27 15:11:03'),
(20, 160, 'seller', 'unarchive', 'property', 19, '{\"title\":\"Modern House\",\"price\":\"13000000.00\",\"location\":\"Tacloban, City\",\"is_archived\":0,\"source\":\"seller\"}', '::1', '2026-05-27 15:11:12'),
(21, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 15:51:03'),
(22, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-27 15:51:03'),
(23, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 15:51:27'),
(24, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 16:01:32'),
(25, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 16:01:32'),
(26, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-27 16:01:54'),
(27, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 16:02:01'),
(28, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 22:58:48'),
(29, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-27 22:58:48'),
(30, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 23:11:24'),
(31, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-27 23:11:24'),
(32, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-27 23:11:39'),
(33, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 23:15:14'),
(34, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-27 23:15:14'),
(35, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-27 23:15:27'),
(36, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 23:16:49'),
(37, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 23:16:49'),
(38, 160, 'seller', 'update', 'property', 19, '{\"old\":{\"title\":\"Modern House\",\"price\":\"13000000.00\",\"location\":\"Tacloban, City\"},\"new\":{\"title\":\"Modern House\",\"price\":\"13000000.00\",\"location\":\"Tacloban, City\"},\"source\":\"seller\"}', '::1', '2026-05-27 23:17:19'),
(39, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-27 23:33:20'),
(40, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-27 23:33:33'),
(41, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-27 23:33:33'),
(42, 159, 'admin', 'delete', 'property', 20, '{\"title\":\"Duplex House\",\"price\":\"16000000.00\",\"location\":\"Cogon, Ormoc City\",\"seller_id\":\"160\"}', '::1', '2026-05-28 01:29:47'),
(43, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 01:33:04'),
(44, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-28 01:33:11'),
(45, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 01:33:49'),
(46, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-28 01:33:49'),
(47, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 01:34:11'),
(48, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 01:34:11'),
(49, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 01:34:26'),
(50, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 01:34:26'),
(51, 160, 'seller', 'create', 'property', 21, '{\"title\":\"Crystal Manor\",\"price\":\"56000000\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 02:51:23'),
(52, 160, 'seller', 'create', 'property', 22, '{\"title\":\"Crystal Manor\",\"price\":\"56000000\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 02:51:23'),
(53, 160, 'seller', 'archive', 'property', 22, '{\"title\":\"Crystal Manor\",\"price\":\"56000000.00\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 02:52:38'),
(54, 160, 'seller', 'update', 'property', 21, '{\"old\":{\"title\":\"Crystal Manor\",\"price\":\"56000000.00\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\"},\"new\":{\"title\":\"Crystal Manor\",\"price\":\"56000000.00\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 02:53:29'),
(55, 160, 'seller', 'create', 'property', 23, '{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 03:03:22'),
(56, 160, 'seller', 'update', 'property', 23, '{\"old\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 03:15:08'),
(57, 160, 'seller', 'update', 'property', 23, '{\"old\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 03:15:08'),
(58, 160, 'seller', 'update', 'property', 23, '{\"old\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 03:15:09'),
(59, 160, 'seller', 'update', 'property', 23, '{\"old\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 03:27:51'),
(60, 160, 'seller', 'update', 'property', 23, '{\"old\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 03:27:52'),
(61, 160, 'seller', 'update', 'property', 23, '{\"old\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 03:40:27'),
(62, 160, 'seller', 'update', 'property', 23, '{\"old\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 03:44:22'),
(63, 160, 'seller', 'update', 'property', 23, '{\"old\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 04:24:59'),
(64, 160, 'seller', 'archive', 'property', 23, '{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 04:26:13'),
(65, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 04:26:32'),
(66, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-28 04:26:32'),
(67, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 04:26:32'),
(68, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-28 04:26:32'),
(69, 160, 'seller', 'unarchive', 'property', 23, '{\"title\":\"Small House\",\"price\":\"33999999.99\",\"location\":\"San Miguel Palompon, Leyte\",\"is_archived\":0,\"source\":\"seller\"}', '::1', '2026-05-28 04:26:54'),
(70, 160, 'seller', 'create', 'property', 24, '{\"title\":\"Seaside House\",\"price\":\"45000000\",\"location\":\"San Juan Palonpon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 05:18:16'),
(71, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 05:39:20'),
(72, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 05:39:20'),
(73, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 05:39:42'),
(74, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 05:39:55'),
(75, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-28 05:40:02'),
(76, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 12:31:10'),
(77, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 12:31:10'),
(78, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 12:32:31'),
(79, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 12:32:31'),
(80, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 12:42:40'),
(81, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 12:42:40'),
(82, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 12:47:53'),
(83, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 12:48:05'),
(84, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 12:48:05'),
(85, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 12:58:43'),
(86, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 12:58:43'),
(87, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 12:58:43'),
(88, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 12:58:43'),
(89, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 13:02:36'),
(90, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 13:03:03'),
(91, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 13:03:03'),
(92, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 13:03:35'),
(93, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 13:03:35'),
(94, 160, 'seller', 'create', 'property', 25, '{\"title\":\"gfh\",\"price\":\"60000000\",\"location\":\"Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 13:34:03'),
(95, 160, 'seller', 'create', 'property', 26, '{\"title\":\"grg\",\"price\":\"57000000\",\"location\":\"Ormoc City\",\"source\":\"seller\"}', '::1', '2026-05-28 13:50:35'),
(96, 160, 'seller', 'create', 'property', 27, '{\"title\":\"rgz\",\"price\":\"79000000\",\"location\":\"Tacloban City\",\"source\":\"seller\"}', '::1', '2026-05-28 13:51:39'),
(97, 160, 'seller', 'create', 'property', 28, '{\"title\":\"hsbs\",\"price\":\"68000000\",\"location\":\"Ormoc City\",\"source\":\"seller\"}', '::1', '2026-05-28 15:11:41'),
(98, 160, 'seller', 'create', 'property', 29, '{\"title\":\"rga\",\"price\":\"4800000\",\"location\":\"Ormoc City\",\"source\":\"seller\"}', '::1', '2026-05-28 15:28:01'),
(99, 160, 'seller', 'create', 'property', 30, '{\"title\":\"thz\",\"price\":\"6700000\",\"location\":\"Ormoc City\",\"source\":\"seller\"}', '::1', '2026-05-28 15:37:36'),
(100, 160, 'seller', 'create', 'property', 31, '{\"title\":\"gaf\",\"price\":\"34000000\",\"location\":\"Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 15:38:21'),
(101, 160, 'seller', 'update', 'property', 31, '{\"old\":{\"title\":\"gaf\",\"price\":\"34000000.00\",\"location\":\"Palompon, Leyte\"},\"new\":{\"title\":\"gaf\",\"price\":\"34000000.00\",\"location\":\"Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 15:38:37'),
(102, 160, 'seller', 'update', 'property', 31, '{\"old\":{\"title\":\"gaf\",\"price\":\"34000000.00\",\"location\":\"Palompon, Leyte\"},\"new\":{\"title\":\"gaf\",\"price\":\"34000000.00\",\"location\":\"Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-28 15:38:37'),
(103, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 16:16:02'),
(104, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-28 16:16:02'),
(105, 160, 'seller', 'create', 'property', 32, '{\"title\":\"thx\",\"price\":\"34999999.99\",\"location\":\"Tacloban, City\",\"source\":\"seller\"}', '::1', '2026-05-28 16:17:22'),
(106, 160, 'seller', 'archive', 'property', 29, '{\"title\":\"rga\",\"price\":\"4800000.00\",\"location\":\"Ormoc City\",\"source\":\"seller\"}', '::1', '2026-05-28 16:26:22'),
(107, 160, 'seller', 'delete', 'property', 29, '{\"title\":\"rga\",\"price\":\"4800000.00\",\"location\":\"Ormoc City\",\"source\":\"seller\"}', '::1', '2026-05-28 16:26:54'),
(108, 160, 'seller', 'unarchive', 'property', 22, '{\"title\":\"Crystal Manor\",\"price\":\"56000000.00\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\",\"is_archived\":0,\"source\":\"seller\"}', '::1', '2026-05-28 16:27:07'),
(109, 160, 'seller', 'create', 'property', 33, '{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 16:29:51'),
(110, 160, 'seller', 'archive', 'property', 33, '{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 16:30:41'),
(111, 160, 'seller', 'archive', 'property', 33, '{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-28 16:30:41'),
(112, 160, 'seller', 'unarchive', 'property', 33, '{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\",\"is_archived\":0,\"source\":\"seller\"}', '::1', '2026-05-28 16:30:48'),
(113, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 16:32:24'),
(114, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 16:32:30'),
(115, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 16:36:24'),
(116, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 16:36:24'),
(117, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-28 17:18:03'),
(118, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 17:18:18'),
(119, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 17:18:18'),
(120, 160, 'seller', 'create', 'property', 34, '{\"title\":\"fgfd\",\"price\":\"29999999.99\",\"location\":\"Tacloban, City\",\"source\":\"seller\"}', '::1', '2026-05-28 17:19:01'),
(121, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 18:41:57'),
(122, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 18:47:43'),
(123, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 18:47:43'),
(124, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 18:48:26'),
(125, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-28 18:48:26'),
(126, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-28 18:48:37'),
(127, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 18:48:37'),
(128, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-28 18:55:39'),
(129, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 01:06:30'),
(130, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 01:06:30'),
(131, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 01:07:05'),
(132, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 01:07:16'),
(133, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 01:07:16'),
(134, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 01:09:27'),
(135, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 01:09:42'),
(136, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 01:09:42'),
(137, 160, 'seller', 'archive', 'property', 33, '{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 01:10:08'),
(138, 160, 'seller', 'archive', 'property', 33, '{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 01:10:08'),
(139, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 01:11:10'),
(140, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 01:11:28'),
(141, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 01:11:28'),
(142, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 01:16:01'),
(143, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 01:16:19'),
(144, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 01:16:19'),
(145, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 01:20:23'),
(146, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 01:24:07'),
(147, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 01:24:07'),
(148, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 01:30:14'),
(149, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 01:30:14'),
(150, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 03:26:04'),
(151, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 03:26:19'),
(152, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 03:26:19'),
(153, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 03:26:21'),
(154, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 03:26:35'),
(155, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 03:26:35'),
(156, 160, 'seller', 'create', 'property', 35, '{\"title\":\"3rsa\",\"price\":\"45000000\",\"location\":\"Ormoc City\",\"source\":\"seller\"}', '::1', '2026-05-29 04:46:07'),
(157, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 04:46:34'),
(158, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 04:46:50'),
(159, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 04:46:50'),
(160, 160, 'seller', 'create', 'property', 36, '{\"title\":\"edfwe\",\"price\":\"3200000\",\"location\":\"Guiwan 3, Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 04:56:10'),
(161, 160, 'seller', 'create', 'property', 37, '{\"title\":\"trerfd\",\"price\":\"56000000\",\"location\":\"Guiwan 3, Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 05:00:26'),
(162, 160, 'seller', 'create', 'property', 38, '{\"title\":\"trerfd\",\"price\":\"56000000\",\"location\":\"Guiwan 3, Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 05:00:26'),
(163, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 05:52:22'),
(164, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 05:52:36'),
(165, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 05:52:36'),
(166, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 07:04:30'),
(167, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 07:04:30'),
(168, 160, 'seller', 'archive', 'property', 37, '{\"title\":\"trerfd\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 07:05:08'),
(169, 160, 'seller', 'archive', 'property', 37, '{\"title\":\"trerfd\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 07:05:08'),
(170, 160, 'seller', 'unarchive', 'property', 37, '{\"title\":\"trerfd\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\",\"is_archived\":0,\"source\":\"seller\"}', '::1', '2026-05-29 07:05:16'),
(171, 160, 'seller', 'unarchive', 'property', 33, '{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\",\"is_archived\":0,\"source\":\"seller\"}', '::1', '2026-05-29 07:05:23'),
(172, 160, 'seller', 'unarchive', 'property', 33, '{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\",\"is_archived\":0,\"source\":\"seller\"}', '::1', '2026-05-29 07:05:23'),
(173, 160, 'seller', 'archive', 'property', 38, '{\"title\":\"trerfd\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 07:05:37'),
(174, 160, 'seller', 'update', 'property', 37, '{\"old\":{\"title\":\"trerfd\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"new\":{\"title\":\"trerfd\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 07:05:58'),
(175, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 07:07:46'),
(176, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 07:07:46'),
(177, 160, 'seller', 'create', 'property', 39, '{\"title\":\"tfgcj\",\"price\":\"79000000\",\"location\":\"Tacloban City\",\"source\":\"seller\"}', '::1', '2026-05-29 07:08:23'),
(178, 160, 'seller', 'archive', 'property', 39, '{\"title\":\"tfgcj\",\"price\":\"79000000.00\",\"location\":\"Tacloban City\",\"source\":\"seller\"}', '::1', '2026-05-29 07:15:50'),
(179, 160, 'seller', 'update', 'property', 37, '{\"old\":{\"title\":\"trerfd\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"new\":{\"title\":\"ggggggg\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 07:16:16'),
(180, 160, 'seller', 'update', 'property', 37, '{\"old\":{\"title\":\"ggggggg\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"new\":{\"title\":\"ggggggg\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 07:16:35'),
(181, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 07:18:15'),
(182, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 07:18:22'),
(183, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 07:18:33'),
(184, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 07:46:44'),
(185, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 07:46:44'),
(186, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 08:04:49'),
(187, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 08:04:49'),
(188, 160, 'seller', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 08:04:49'),
(189, 160, 'seller', 'login', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 08:04:49'),
(190, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 08:04:56'),
(191, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 08:04:56'),
(192, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 08:06:58'),
(193, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 08:07:03'),
(194, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 08:07:03'),
(195, 160, 'seller', 'update', 'property', 19, '{\"old\":{\"title\":\"Modern House\",\"price\":\"13000000.00\",\"location\":\"Tacloban, City\"},\"new\":{\"title\":\"Modern House\",\"price\":\"52000000.00\",\"location\":\"Sunflower Subdivision Tacloban City\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:19:08'),
(196, 160, 'seller', 'update', 'property', 19, '{\"old\":{\"title\":\"Modern House\",\"price\":\"52000000.00\",\"location\":\"Sunflower Subdivision Tacloban City\"},\"new\":{\"title\":\"Modern House\",\"price\":\"52000000.00\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:19:58'),
(197, 160, 'seller', 'update', 'property', 24, '{\"old\":{\"title\":\"Seaside House\",\"price\":\"45000000.00\",\"location\":\"San Juan Palonpon, Leyte\"},\"new\":{\"title\":\"Seaside House\",\"price\":\"45000000.00\",\"location\":\"San Juan Palonpon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:25:23'),
(198, 160, 'seller', 'update', 'property', 25, '{\"old\":{\"title\":\"gfh\",\"price\":\"60000000.00\",\"location\":\"Palompon, Leyte\"},\"new\":{\"title\":\"Farm House\",\"price\":\"60000000.00\",\"location\":\"Mansalip Matag-ob, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:28:25'),
(199, 160, 'seller', 'update', 'property', 26, '{\"old\":{\"title\":\"grg\",\"price\":\"57000000.00\",\"location\":\"Ormoc City\"},\"new\":{\"title\":\"grg\",\"price\":\"57000000.00\",\"location\":\"Cogon Ormoc City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:31:34'),
(200, 160, 'seller', 'update', 'property', 21, '{\"old\":{\"title\":\"Crystal Manor\",\"price\":\"56000000.00\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\"},\"new\":{\"title\":\"Rose Manor\",\"price\":\"71000000.00\",\"location\":\"Sunflower Subdivision Tacloban City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:32:35'),
(201, 160, 'seller', 'update', 'property', 26, '{\"old\":{\"title\":\"grg\",\"price\":\"57000000.00\",\"location\":\"Cogon Ormoc City, Leyte\"},\"new\":{\"title\":\"Rosewood Manor\",\"price\":\"57000000.00\",\"location\":\"Cogon Ormoc City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:33:54'),
(202, 160, 'seller', 'update', 'property', 27, '{\"old\":{\"title\":\"rgz\",\"price\":\"79000000.00\",\"location\":\"Tacloban City\"},\"new\":{\"title\":\"Maple Heights Duplex\",\"price\":\"79000000.00\",\"location\":\"Cogon Ormoc City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:37:42'),
(203, 160, 'seller', 'update', 'property', 28, '{\"old\":{\"title\":\"hsbs\",\"price\":\"68000000.00\",\"location\":\"Ormoc City\"},\"new\":{\"title\":\"Serenity Villa\",\"price\":\"68000000.00\",\"location\":\"Burgos Villaba, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:40:39'),
(204, 160, 'seller', 'update', 'property', 30, '{\"old\":{\"title\":\"thz\",\"price\":\"6700000.00\",\"location\":\"Ormoc City\"},\"new\":{\"title\":\"Sunnyside House\",\"price\":\"3070000.00\",\"location\":\"Ormoc City\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:45:00'),
(205, 160, 'seller', 'update', 'property', 31, '{\"old\":{\"title\":\"gaf\",\"price\":\"34000000.00\",\"location\":\"Palompon, Leyte\"},\"new\":{\"title\":\"Greenacres\",\"price\":\"34000000.00\",\"location\":\"Burgos Villaba, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:49:22'),
(206, 160, 'seller', 'update', 'property', 31, '{\"old\":{\"title\":\"Greenacres\",\"price\":\"34000000.00\",\"location\":\"Burgos Villaba, Leyte\"},\"new\":{\"title\":\"Greenacres\",\"price\":\"34000000.00\",\"location\":\"Burgos Villaba, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:49:22'),
(207, 160, 'seller', 'update', 'property', 32, '{\"old\":{\"title\":\"thx\",\"price\":\"34999999.99\",\"location\":\"Tacloban, City\"},\"new\":{\"title\":\"The Acorn House\",\"price\":\"39200000\",\"location\":\"Sunset Village Tacloban City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:53:53'),
(208, 160, 'seller', 'update', 'property', 33, '{\"old\":{\"title\":\"tttt\",\"price\":\"3799999.99\",\"location\":\"Palompon, Leyte\"},\"new\":{\"title\":\"The Nook \",\"price\":\"12900000\",\"location\":\"Rizal Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 08:57:21'),
(209, 160, 'seller', 'update', 'property', 34, '{\"old\":{\"title\":\"fgfd\",\"price\":\"29999999.99\",\"location\":\"Tacloban, City\"},\"new\":{\"title\":\"Grandview Duplex Homes\",\"price\":\"51000000\",\"location\":\"San Miguel Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 09:02:40'),
(210, 160, 'seller', 'update', 'property', 35, '{\"old\":{\"title\":\"3rsa\",\"price\":\"45000000.00\",\"location\":\"Ormoc City\"},\"new\":{\"title\":\"Small House\",\"price\":\"25900000.00\",\"location\":\"Sunset Village Tacloban City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 09:05:47'),
(211, 160, 'seller', 'update', 'property', 35, '{\"old\":{\"title\":\"Small House\",\"price\":\"25900000.00\",\"location\":\"Sunset Village Tacloban City, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"25900000.00\",\"location\":\"Sunset Village Tacloban City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 09:05:47'),
(212, 160, 'seller', 'update', 'property', 35, '{\"old\":{\"title\":\"Small House\",\"price\":\"25900000.00\",\"location\":\"Sunset Village Tacloban City, Leyte\"},\"new\":{\"title\":\"Small House\",\"price\":\"25900000.00\",\"location\":\"Sunset Village Tacloban City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 09:06:00'),
(213, 160, 'seller', 'update', 'property', 36, '{\"old\":{\"title\":\"edfwe\",\"price\":\"3200000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"new\":{\"title\":\"Springfield Villa\",\"price\":\"51600000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 09:13:15'),
(214, 160, 'seller', 'update', 'property', 36, '{\"old\":{\"title\":\"Springfield Villa\",\"price\":\"51600000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"new\":{\"title\":\"Springfield Villa\",\"price\":\"51600000.00\",\"location\":\"San Juan Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 09:13:41'),
(215, 160, 'seller', 'update', 'property', 36, '{\"old\":{\"title\":\"Springfield Villa\",\"price\":\"51600000.00\",\"location\":\"San Juan Palompon, Leyte\"},\"new\":{\"title\":\"Springfield Villa\",\"price\":\"51600000.00\",\"location\":\"San Juan Palompon, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 09:13:41'),
(216, 160, 'seller', 'update', 'property', 37, '{\"old\":{\"title\":\"ggggggg\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"new\":{\"title\":\"Palm Duplex\",\"price\":\"56000000.00\",\"location\":\"Sunset Village Tacloban City, Leyte\"},\"source\":\"seller\"}', '::1', '2026-05-29 09:17:04'),
(217, 160, 'seller', 'archive', 'property', 34, '{\"title\":\"Grandview Duplex Homes\",\"price\":\"51000000.00\",\"location\":\"San Miguel Palompon, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 09:18:13'),
(218, 160, 'seller', 'archive', 'property', 35, '{\"title\":\"Small House\",\"price\":\"25900000.00\",\"location\":\"Sunset Village Tacloban City, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 09:18:19'),
(219, 160, 'seller', 'archive', 'property', 27, '{\"title\":\"Maple Heights Duplex\",\"price\":\"79000000.00\",\"location\":\"Cogon Ormoc City, Leyte\",\"source\":\"seller\"}', '::1', '2026-05-29 09:18:24'),
(220, 160, 'seller', 'logout', 'user', 160, '{\"email\":\"davidcaudillo@gmail.com\",\"name\":\"David N. Caudillo\",\"role\":\"seller\"}', '::1', '2026-05-29 09:18:37'),
(221, 161, 'buyer', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 09:18:53'),
(222, 161, 'buyer', 'login', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 09:18:53'),
(223, 161, 'buyer', 'logout', 'user', 161, '{\"email\":\"josephfisk@gmail.com\",\"name\":\"Joseph M. Fisk\",\"role\":\"buyer\"}', '::1', '2026-05-29 09:20:53'),
(224, 159, 'admin', 'direct_test', NULL, NULL, NULL, NULL, '2026-05-29 09:21:19'),
(225, 159, 'admin', 'login', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 09:21:19'),
(226, 159, 'admin', 'update', 'property', 38, '{\"old\":{\"title\":\"trerfd\",\"price\":\"56000000.00\",\"location\":\"Guiwan 3, Palompon, Leyte\"},\"new\":{\"title\":\"Modern House\",\"price\":\"56000000.00\",\"location\":\"Burgos Villaba, Leyte\"}}', '::1', '2026-05-29 09:25:27'),
(227, 159, 'admin', 'update', 'property', 39, '{\"old\":{\"title\":\"tfgcj\",\"price\":\"79000000.00\",\"location\":\"Tacloban City\"},\"new\":{\"title\":\"Willow Creek Duplix\",\"price\":\"79000000.00\",\"location\":\"Tacloban City\"}}', '::1', '2026-05-29 09:28:50'),
(228, 159, 'admin', 'update', 'property', 39, '{\"old\":{\"title\":\"Willow Creek Duplix\",\"price\":\"79000000.00\",\"location\":\"Tacloban City\"},\"new\":{\"title\":\"Willow Creek Duplix\",\"price\":\"79000000.00\",\"location\":\"Sunflower Subdivision Tacloban City\"}}', '::1', '2026-05-29 09:29:17'),
(229, 159, 'admin', 'logout', 'user', 159, '{\"email\":\"admin@example.com\",\"name\":\"Dev Admin\",\"role\":\"admin\"}', '::1', '2026-05-29 09:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `buyer_id`, `property_id`, `created_at`) VALUES
(1, 5, 18, '2026-05-15 04:16:43'),
(2, 161, 28, '2026-05-29 07:09:17'),
(3, 161, 34, '2026-05-29 07:17:51'),
(5, 161, 32, '2026-05-29 09:19:51'),
(6, 161, 13, '2026-05-29 09:20:29');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `property_id`, `message`, `created_at`) VALUES
(15, 4, 6, 15, 'hii can i buy this?', '2025-11-22 06:38:02'),
(16, 6, 4, 15, 'of course ', '2025-11-22 06:41:58'),
(17, 4, 8, 18, 'ka gwapo ba ana miss', '2025-11-24 18:50:02'),
(18, 4, 8, 18, 'ka gwapo ba ana miss', '2025-11-24 18:50:03'),
(19, 161, 160, 32, 'Hi I would like to ask a few questions about this house.', '2026-05-28 08:24:30'),
(20, 160, 161, 32, 'Good morning ma\'am, What are your questions about this house?', '2026-05-28 08:25:17'),
(21, 160, 161, 32, 'I\'m ready to answer all your questions regarding this property.', '2026-05-28 17:19:39'),
(22, 161, 160, 34, 'Hi!', '2026-05-28 19:27:02'),
(23, 161, 160, 31, 'Hi', '2026-05-28 20:45:12'),
(24, 160, 161, 31, 'HI', '2026-05-28 20:45:35'),
(25, 160, 161, 31, 'How may I help you today?', '2026-05-28 20:45:44'),
(26, 161, 160, 35, 'Hi! I just want to ask a few questions', '2026-05-28 20:47:36'),
(27, 161, 160, 30, 'Hi', '2026-05-28 20:51:24'),
(28, 161, 160, 37, 'hi', '2026-05-28 21:00:42'),
(29, 161, 160, 36, 'Hi! I would like to inquire about this house.', '2026-05-28 23:13:49'),
(30, 160, 161, 36, 'ejfqwekcn', '2026-05-28 23:14:49'),
(31, 161, 160, 33, 'HI', '2026-05-28 23:15:23');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-05-09-000001', 'App\\Database\\Migrations\\CreateFavoritesTable', 'default', 'App', 1778379360, 1),
(2, '2026-05-09-000002', 'App\\Database\\Migrations\\CreatePaymentsTable', 'default', 'App', 1778379364, 1),
(3, '2026-05-27-041515', 'App\\Database\\Migrations\\CreateAuditLogsTable', 'default', 'App', 1779855638, 2);

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `property_id`, `buyer_id`, `amount`, `status`, `created_at`) VALUES
(5, 15, 4, 50000000.00, 'accepted', '2026-05-28 07:34:55'),
(6, 18, 4, 6000000.00, 'rejected', '2026-05-28 07:34:55'),
(8, 18, 161, 7000000.00, 'pending', '2026-05-28 07:48:00'),
(9, 14, 161, 8000000.00, 'pending', '2026-05-28 07:48:44'),
(10, 19, 161, 10000000.00, 'rejected', '2026-05-28 09:34:58'),
(11, 23, 161, 15000000.00, 'rejected', '2026-05-28 12:27:28'),
(12, 21, 161, 50000000.00, 'rejected', '2026-05-28 12:28:15'),
(13, 24, 161, 40000000.00, 'rejected', '2026-05-28 21:17:04'),
(14, 25, 161, 50000000.00, 'rejected', '2026-05-28 21:34:42'),
(15, 26, 161, 50000000.00, 'accepted', '2026-05-28 21:51:12'),
(16, 27, 161, 68000000.00, 'rejected', '2026-05-28 21:51:55'),
(17, 32, 161, 68000000.00, 'accepted', '2026-05-29 00:22:47'),
(18, 31, 161, 32500000.00, 'rejected', '2026-05-29 00:23:10'),
(19, 37, 161, 50000000.00, 'pending', '2026-05-29 15:04:54'),
(20, 36, 161, 34000000.00, 'accepted', '2026-05-29 15:09:53'),
(21, 35, 161, 42000000.00, 'accepted', '2026-05-29 16:05:32'),
(22, 33, 161, 10000000.00, 'pending', '2026-05-29 17:19:32');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) UNSIGNED NOT NULL,
  `offer_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'paid',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `seller_id`, `title`, `description`, `price`, `location`, `is_archived`, `image_path`) VALUES
(10, 3, 'Mansion ni Jeycel ', 'A spacious two-story family house featuring 3 bedrooms, 2 bathrooms, a modern kitchen, and a cozy living area. Located in a quiet neighborhood just 10 minutes from the city center. Perfect for growing families.', 6000000.00, 'Baybay City', 0, 'uploads/1759634295_house3.jpg'),
(12, 2, 'Elegant Condo Unit', 'A fully furnished 1-bedroom, 1-bathroom condo located in a prime business district. Includes access to a gym, swimming pool, and 24-hour security. Ideal for young professionals.', 3000000.00, 'Ormoc City', 0, 'uploads/1759634072_house1.jpg'),
(13, 6, 'Bahay Ni Kerwin ', 'baligya na kay pirme eh raid', 35000000.00, 'Albuera, Damulaan', 0, 'uploads/1759845854_house5.jpeg'),
(14, 6, 'Luxury House', 'Garage ni Boss K', 10000000.00, 'Albuera, Damulaan', 0, 'uploads/1759846048_house7.jpg'),
(15, 6, 'City House', 'Murag balay sa Larva', 40000000.00, 'Ormoc City ', 0, 'uploads/1759846310_house9.jpg'),
(17, 3, 'Guba nga balay', 'naguba sa baha', 40000.00, 'Ormoc City, Tambulilid', 1, 'writable/uploads/1763821172_210d58fb9e254353b190.jpg'),
(18, 8, 'Eskwa House', 'secret nga naay ungo', 5000000.00, 'Ormoc City, Curva', 0, 'uploads/1764038564_903dc7adc68a804699a3.jpeg'),
(19, 160, 'Modern House', 'This elegant modern house offers wide living spaces, large glass windows, and a relaxing ambiance perfect for family living.', 52000000.00, 'Sunflower Subdivision Tacloban City, Leyte', 0, 'uploads/1780042748_705f2f60822c867003a9.jpg'),
(21, 160, 'Rose Manor', 'This magnificent mansion-inspired villa showcases timeless architecture, elegant stone finishes, and grand castle-like design elements that create a truly luxurious atmosphere. Featuring multiple spacious bedrooms, sophisticated living areas, high ceilings, and expansive windows, the property combines classic elegance with modern comfort. The estate includes beautifully maintained grounds, a grand entrance, private parking spaces, and breathtaking exterior details that reflect prestige and exclusivity. Perfect for luxury living, private retreats, or high-end investment opportunities, this extraordinary residence offers unmatched charm, sophistication, and architectural beauty.', 71000000.00, 'Sunflower Subdivision Tacloban City, Leyte', 0, 'uploads/1779936809_78d243bb3d80651ba07c.jpeg'),
(22, 160, 'Crystal Manor', 'This magnificent mansion-inspired villa showcases timeless architecture, elegant stone finishes, and grand castle-like design elements that create a truly luxurious atmosphere. Featuring multiple spacious bedrooms, sophisticated living areas, high ceilings, and expansive windows, the property combines classic elegance with modern comfort. The estate includes beautifully maintained grounds, a grand entrance, private parking spaces, and breathtaking exterior details that reflect prestige and exclusivity. Perfect for luxury living, private retreats, or high-end investment opportunities, this extraordinary residence offers unmatched charm, sophistication, and architectural beauty.', 56000000.00, 'Sunflower Subdivision Tacloban City, Leyte', 0, 'uploads/1779936682_54ca887ccbd24f2ee3b6.jpg'),
(23, 160, 'Small House', 'This charming small house offers a cozy and comfortable living space perfect for small families, couples, or first-time homeowners. Designed with simplicity and functionality in mind, the home features a bright living area, a practical kitchen, comfortable bedrooms, and excellent natural ventilation. The property provides a peaceful atmosphere while remaining conveniently located near schools, markets, and public transportation. With its efficient layout and low-maintenance design, this home is ideal for those seeking affordable yet comfortable living.', 33999999.99, 'San Miguel Palompon, Leyte', 0, 'uploads/1779942299_1285495baa7c4237329e.webp'),
(24, 160, 'Seaside House', 'This charming seaside home offers direct beach access, airy interiors, and a peaceful environment perfect for relaxation and family vacations.', 45000000.00, 'San Juan Palonpon, Leyte', 0, 'uploads/1779945496_560f1114a3d3afae423d.avif'),
(25, 160, 'Farm House', 'Experience countryside living in this farmhouse complete with spacious bedrooms, a large garden, and relaxing natural scenery.', 60000000.00, 'Mansalip Matag-ob, Leyte', 0, 'uploads/1779975243_86ad022e9eccb9e5e094.png'),
(26, 160, 'Rosewood Manor', 'This grand residence offers a resort-style atmosphere with spacious outdoor areas and elegant interiors.', 57000000.00, 'Cogon Ormoc City, Leyte', 0, 'uploads/1779976235_b26ccf825da2ea05d7e7.webp'),
(27, 160, 'Maple Heights Duplex', 'A stylish duplex home featuring spacious living areas, modern interiors, and a cozy atmosphere perfect for growing families.', 79000000.00, 'Cogon Ormoc City, Leyte', 1, 'uploads/1779976299_ff5533d2e81cdc1570fe.jpg'),
(28, 160, 'Serenity Villa', 'This stunning villa combines sophistication and functionality with its spacious layout and elegant architectural design.', 68000000.00, 'Burgos Villaba, Leyte', 0, 'uploads/1779981101_87c0a69404e048c8bb97.jpg'),
(30, 160, 'Sunnyside House', 'Enjoy simple and comfortable living in this beautifully maintained small home located in a peaceful neighborhood.', 3070000.00, 'Ormoc City', 0, 'uploads/1779982656_d5bc185bef0ba672bb4e.jpg'),
(31, 160, 'Greenacres', 'A peaceful duplex property designed with comfort and convenience in mind, featuring spacious living and dining areas.', 34000000.00, 'Burgos Villaba, Leyte', 0, 'uploads/1779982717_dc0eec0083168471b511.jpg'),
(32, 160, 'The Acorn House', 'This compact home features a practical kitchen, comfortable bedrooms, and a relaxing living space.', 39200000.00, 'Sunset Village Tacloban City, Leyte', 0, 'uploads/1779985042_621e0975cfbd30047e0a.jpg'),
(33, 160, 'The Nook ', 'A well-maintained small house perfect for individuals or families seeking a comfortable living environment.', 12900000.00, 'Rizal Palompon, Leyte', 0, 'uploads/1779985791_518575b93869474d3a89.avif'),
(34, 160, 'Grandview Duplex Homes', 'This duplex property offers comfort, convenience, and a warm atmosphere perfect for family living.', 51000000.00, 'San Miguel Palompon, Leyte', 1, 'uploads/1780045360_f03f95460ed9b928f2ed.webp'),
(35, 160, 'Small House', 'A cozy small house featuring bright interiors, modern finishes, and a practical floor layout.', 25900000.00, 'Sunset Village Tacloban City, Leyte', 1, 'uploads/1780045560_16c7f44a44d894770e83.webp'),
(36, 160, 'Springfield Villa', 'Enjoy upscale living in this beautiful villa featuring elegant interiors and spacious family-friendly living areas.', 51600000.00, 'San Juan Palompon, Leyte', 0, 'uploads/1780030570_26a5c6f537847b110774.jpg'),
(37, 160, 'Palm Duplex', 'This elegant duplex offers a practical layout, cozy family spaces, and a beautifully maintained exterior.', 56000000.00, 'Sunset Village Tacloban City, Leyte', 0, 'uploads/1780046224_5d88a015f2ecd5375e6b.jpg'),
(38, 160, 'Modern House', 'Enjoy a perfect blend of luxury and simplicity in this modern home with sleek finishes and a cozy family-friendly layout.', 56000000.00, 'Burgos Villaba, Leyte', 1, 'uploads/1780046727_5546133eb3a27e259805.jpg'),
(39, 160, 'Willow Creek Duplix', 'A beautifully designed duplex home featuring functional spaces and stylish contemporary interiors.', 79000000.00, 'Sunflower Subdivision Tacloban City', 1, 'uploads/1780046930_ff984c22b8abe75819fe.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `property_history`
--

CREATE TABLE `property_history` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `property_title` varchar(255) NOT NULL,
  `action` varchar(50) NOT NULL,
  `changed_by` varchar(100) NOT NULL,
  `changed_by_id` int(11) NOT NULL,
  `changed_by_role` varchar(50) NOT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('buyer','seller','admin') NOT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default.png',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `contact`, `bio`, `profile_pic`, `created_at`) VALUES
(1, 'bibing', 'bebe@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09867588575', 'im Bandsexual', '1759729606_hehe.jpg', '2026-05-29 09:41:50'),
(2, 'sidney', 'sid@gmail.com', '$2y$10$4XqIjyCzaUMjlp7IyHjs9ujAjrha1wFPe9uaaqK9b4DPrYb7aFCRy', 'seller', NULL, NULL, 'default.png', '2026-05-29 09:41:50'),
(3, 'Jeycel Leoveras', 'jeycel@gmail.com', '$2y$10$x.vfaG.cd6wwgPOf6odrEOdp11/PekXKaIrHYWT74Je3R2OnrrT.y', 'seller', '09169327857', 'Chi shii', '1760154211_day1.jpg', '2026-05-29 09:41:50'),
(4, 'Mark Rabaya', 'mark@gmail.com', '$2y$10$O7EDirAD8kygpZwyODmXTOvjEewYQWqpPJ/8mB9/n.KiBloDE1JMy', 'buyer', NULL, NULL, 'default.png', '2026-05-29 09:41:50'),
(5, 'Felip Samuel', 'felipsamuel@gmail.com', '$2y$10$O5HpZtiBptpcVIJ67zvVcOdzGjFiq54pO2PHL5LUnMm0mGdhW66RC', 'buyer', '09382152064', 'i beat old people for fun', 'user_68e51802dd0f6.jpg', '2026-05-29 09:41:50'),
(6, 'Althea Centino', 'althea@gmail.com', '$2y$10$8pngt7GcWsBh1n9YsSJKmehymzCAVMfEPFMrjhcoLJj3Yxp/Fmp5G', 'seller', '09361425992', 'bagan tamo tanan', 'user_68e51a122abd3.jpeg', '2026-05-29 09:41:50'),
(8, 'Ma. Christina', 'maria@gmail.com', '$2y$10$DjqGL9wV/I7/FYL9S6/5I.qFHJquFZROdjhXh7csIM5.CWEtKIo5W', 'seller', '09839384758', 'From Cebu ', '1764038225_bf24a5dbb1f4dda7afef.jpg', '2026-05-29 09:41:50'),
(9, 'Lopez Rose Hernandez', 'lopez.hernandez19@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09386624192', 'Interested in luxury homes.', 'default.png', '2026-05-29 09:41:50'),
(10, 'John Rose Cruz', 'john.cruz13@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09659273731', 'Interested in luxury homes.', 'default.png', '2026-05-29 09:41:50'),
(11, 'Marie Rose Ramos', 'marie.ramos58@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09787166731', 'Interested in modern condos near the business district.', 'default.png', '2026-05-29 09:41:50'),
(12, 'Sebastian Michael Reyes', 'sebastian.reyes19@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09609552971', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(13, 'Robert Castillo', 'robert.castillo72@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09121553946', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(14, 'Rafael Ramos', 'rafael.ramos30@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09935119336', 'Interested in modern condos near the business district.', 'default.png', '2026-05-29 09:41:50'),
(15, 'Luis Joseph Bautista', 'luis.bautista85@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09914868087', 'Interested in luxury homes.', 'default.png', '2026-05-29 09:41:50'),
(16, 'Santos Grace Delgado', 'santos.delgado32@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09795910942', 'Searching for an investment property.', 'default.png', '2026-05-29 09:41:50'),
(17, 'Matthew Lynn Morales', 'matthew.morales30@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09558817861', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(18, 'Santos James Lopez', 'santos.lopez45@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09448215035', 'Looking for a spacious family house.', 'default.png', '2026-05-29 09:41:50'),
(19, 'Isabella Joseph Bautista', 'isabella.bautista34@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09497877859', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(20, 'Lucas Joseph Cruz', 'lucas.cruz64@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09275184125', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(21, 'Catherine Joseph Rivera', 'catherine.rivera73@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09748124382', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(22, 'Lucas Hernandez', 'lucas.hernandez12@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09285175563', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(23, 'Henry Lim', 'henry.lim50@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09134523562', 'Searching for an investment property.', 'default.png', '2026-05-29 09:41:50'),
(24, 'Henry Delgado', 'henry.delgado38@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09269562973', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(25, 'Sebastian Luis Bautista', 'sebastian.bautista34@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09515436130', 'Interested in luxury homes.', 'default.png', '2026-05-29 09:41:50'),
(26, 'Benjamin James Lim', 'benjamin.lim49@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09662374792', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(27, 'Victoria Santos', 'victoria.santos64@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09427424043', 'Seeking a modern townhouse.', 'default.png', '2026-05-29 09:41:50'),
(28, 'Christopher Morales', 'christopher.morales93@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09137909724', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(29, 'Antonio Santos', 'antonio.santos24@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09215698142', 'Interested in beachside properties.', 'default.png', '2026-05-29 09:41:50'),
(30, 'Ava Cruz', 'ava.cruz85@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09771090267', 'Seeking a modern townhouse.', 'default.png', '2026-05-29 09:41:50'),
(31, 'Michael Rivera', 'michael.rivera13@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09836692567', 'Interested in beachside properties.', 'default.png', '2026-05-29 09:41:50'),
(32, 'Victoria Lim', 'victoria.lim59@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09827357295', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(33, 'David Reyes', 'david.reyes58@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09141464433', 'Interested in beachside properties.', 'default.png', '2026-05-29 09:41:50'),
(34, 'John Luis Hernandez', 'john.hernandez45@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09647869751', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(35, 'Angelo Bautista', 'angelo.bautista24@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09725683275', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(36, 'Lopez Garcia', 'lopez.garcia14@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09828576800', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(37, 'Miguel Paul Reyes', 'miguel.reyes31@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09222646717', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(38, 'Gabriella Tan', 'gabriella.tan65@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09837313447', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(39, 'Elena James Mendoza', 'elena.mendoza25@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09978527406', 'Interested in modern condos near the business district.', 'default.png', '2026-05-29 09:41:50'),
(40, 'Angelo Marie Lopez', 'angelo.lopez79@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09482548971', 'Interested in modern condos near the business district.', 'default.png', '2026-05-29 09:41:50'),
(41, 'Eduardo Grace Ramos', 'eduardo.ramos81@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09847999295', 'Looking for a spacious family house.', 'default.png', '2026-05-29 09:41:50'),
(42, 'Emily Mendoza', 'emily.mendoza24@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09553609250', 'Looking for a spacious family house.', 'default.png', '2026-05-29 09:41:50'),
(43, 'Luis Delgado', 'luis.delgado50@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09262461861', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(44, 'Charlotte Garcia', 'charlotte.garcia64@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09953445761', 'Seeking a modern townhouse.', 'default.png', '2026-05-29 09:41:50'),
(45, 'Victoria Paul Garcia', 'victoria.garcia49@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09633067860', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(46, 'Catherine James Bautista', 'catherine.bautista64@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09788261338', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(47, 'Rafael Luis Santos', 'rafael.santos75@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09295999673', 'Looking for a spacious family house.', 'default.png', '2026-05-29 09:41:50'),
(48, 'Lynn Rose Tan', 'lynn.tan47@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09572706723', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(49, 'Eduardo Morales', 'eduardo.morales76@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09828325575', 'Interested in luxury homes.', 'default.png', '2026-05-29 09:41:50'),
(50, 'Emily Bautista', 'emily.bautista44@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09809083344', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(51, 'Matthew Marie Santos', 'matthew.santos14@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09616126578', 'Searching for an investment property.', 'default.png', '2026-05-29 09:41:50'),
(52, 'Michael Ramos', 'michael.ramos70@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09197359228', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(53, 'Alexander Ramos', 'alexander.ramos33@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09818183531', 'Looking for a spacious family house.', 'default.png', '2026-05-29 09:41:50'),
(54, 'John Anne Castillo', 'john.castillo83@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09375634750', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(55, 'Angelo Lopez', 'angelo.lopez91@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09884084462', 'Seeking a modern townhouse.', 'default.png', '2026-05-29 09:41:50'),
(56, 'Antonio Bautista', 'antonio.bautista49@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09732398675', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(57, 'Alexander Morales', 'alexander.morales95@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09459787032', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(58, 'Jacob Michael Tan', 'jacob.tan92@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09587456888', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(59, 'James Delgado', 'james.delgado50@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09856235766', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(60, 'Marie Delgado', 'marie.delgado18@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09521608950', 'Interested in modern condos near the business district.', 'default.png', '2026-05-29 09:41:50'),
(61, 'Patricia Joseph Reyes', 'patricia.reyes67@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09669886585', 'Looking for a spacious family house.', 'default.png', '2026-05-29 09:41:50'),
(62, 'David Rose Fernandez', 'david.fernandez87@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09334350777', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(63, 'Eduardo Ramos', 'eduardo.ramos76@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09357842296', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(64, 'Sophia Michael Tan', 'sophia.tan38@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09555286006', 'Interested in modern condos near the business district.', 'default.png', '2026-05-29 09:41:50'),
(65, 'Elena Cruz', 'elena.cruz73@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09393179025', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(66, 'Joseph Cruz', 'joseph.cruz10@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09151114475', 'Interested in modern condos near the business district.', 'default.png', '2026-05-29 09:41:50'),
(67, 'Lopez Ramos', 'lopez.ramos87@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09436430315', 'Interested in luxury homes.', 'default.png', '2026-05-29 09:41:50'),
(68, 'Elena Fernandez', 'elena.fernandez13@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09143726353', 'Looking for a spacious family house.', 'default.png', '2026-05-29 09:41:50'),
(69, 'Nathaniel Marie Lim', 'nathaniel.lim11@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09442326725', 'Looking for a family home in a quiet neighborhood.', 'default.png', '2026-05-29 09:41:50'),
(70, 'Eduardo Hernandez', 'eduardo.hernandez61@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09615595336', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(71, 'Isabella Rivera', 'isabella.rivera14@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09189670211', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(72, 'Elizabeth Morales', 'elizabeth.morales27@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09556355609', 'Seeking a modern townhouse.', 'default.png', '2026-05-29 09:41:50'),
(73, 'Mia Lopez', 'mia.lopez12@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09347414740', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(74, 'Patricia Garcia', 'patricia.garcia12@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09105898989', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(75, 'Rafael Anne Tan', 'rafael.tan53@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09793867602', 'Searching for an investment property.', 'default.png', '2026-05-29 09:41:50'),
(76, 'Daniel Reyes', 'daniel.reyes40@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09629147033', 'Interested in beachside properties.', 'default.png', '2026-05-29 09:41:50'),
(77, 'Matthew Joseph Cruz', 'matthew.cruz75@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09628571276', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(78, 'Angelo Castillo', 'angelo.castillo53@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09974858224', 'First-time home buyer looking for a cozy house.', 'default.png', '2026-05-29 09:41:50'),
(79, 'David Morales', 'david.morales26@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09209942491', 'Looking for a spacious family house.', 'default.png', '2026-05-29 09:41:50'),
(80, 'Henry Cruz', 'henry.cruz67@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09696182472', 'Looking for a property with good rental potential.', 'default.png', '2026-05-29 09:41:50'),
(81, 'Lynn Rose Bautista', 'lynn.bautista39@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09599936931', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(82, 'Michael Fernandez', 'michael.fernandez64@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09224282435', 'Planning to buy a property for retirement.', 'default.png', '2026-05-29 09:41:50'),
(83, 'Ava Anne Lim', 'ava.lim97@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09633619486', 'Interested in luxury homes.', 'default.png', '2026-05-29 09:41:50'),
(84, 'Maria Rose Mendoza', 'maria.mendoza99@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09133207709', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(85, 'Patricia Delgado', 'patricia.delgado25@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09337543151', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(86, 'Eduardo Morales', 'eduardo.morales88@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09552516341', 'Dedicated to helping clients find their dream home.', 'default.png', '2026-05-29 09:41:50'),
(87, 'Lynn Paul Ramos', 'lynn.ramos36@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09718727768', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(88, 'Jacob Joseph Cruz', 'jacob.cruz52@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09662245726', 'Selling residential properties in the Visayas region.', 'default.png', '2026-05-29 09:41:50'),
(89, 'Eduardo Villanueva', 'eduardo.villanueva67@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09862441570', 'Experienced real estate seller offering quality properties.', 'default.png', '2026-05-29 09:41:50'),
(90, 'Emma Villanueva', 'emma.villanueva23@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09406732061', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(91, 'Nathaniel Cruz', 'nathaniel.cruz45@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09679263602', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(92, 'Marie Villanueva', 'marie.villanueva92@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09353471258', 'Selling residential properties in the Visayas region.', 'default.png', '2026-05-29 09:41:50'),
(93, 'Daniel Castillo', 'daniel.castillo79@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09251227062', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(94, 'William Reyes', 'william.reyes83@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09997244446', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(95, 'Carlos Grace Delgado', 'carlos.delgado94@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09214769408', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(96, 'John Lim', 'john.lim46@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09494426893', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(97, 'Luis Bautista', 'luis.bautista10@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09195078211', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(98, 'Carlos Villanueva', 'carlos.villanueva29@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09892325627', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(99, 'Robert Mendoza', 'robert.mendoza96@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09786301231', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(100, 'Luis Joseph Morales', 'luis.morales43@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09552774746', 'Experienced real estate seller offering quality properties.', 'default.png', '2026-05-29 09:41:50'),
(101, 'Gabriella Morales', 'gabriella.morales94@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09828569815', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(102, 'Luis Morales', 'luis.morales45@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09639060159', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(103, 'Nathaniel Lim', 'nathaniel.lim21@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09261430392', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(104, 'Catherine Delgado', 'catherine.delgado38@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09286122320', 'Specializing in luxury and mid-range homes.', 'default.png', '2026-05-29 09:41:50'),
(105, 'Daniel Castillo', 'daniel.castillo67@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09993851013', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(106, 'Matthew Rivera', 'matthew.rivera22@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09826013339', 'Experienced real estate seller offering quality properties.', 'default.png', '2026-05-29 09:41:50'),
(107, 'James Grace Reyes', 'james.reyes78@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09857955578', 'Experienced real estate seller offering quality properties.', 'default.png', '2026-05-29 09:41:50'),
(108, 'Carlos Santos', 'carlos.santos71@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09559557016', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(109, 'Ava Michael Delgado', 'ava.delgado14@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09805912671', 'Helping families find their perfect home.', 'default.png', '2026-05-29 09:41:50'),
(110, 'Angelo Fernandez', 'angelo.fernandez96@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09675858838', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(111, 'Elizabeth Reyes', 'elizabeth.reyes66@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09209002391', 'Selling residential properties in the Visayas region.', 'default.png', '2026-05-29 09:41:50'),
(112, 'Anna Rose Cruz', 'anna.cruz87@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09605109930', 'Selling residential properties in the Visayas region.', 'default.png', '2026-05-29 09:41:50'),
(113, 'Lopez Marie Cruz', 'lopez.cruz29@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09885213918', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(114, 'Matthew Morales', 'matthew.morales86@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09983312484', 'Helping families find their perfect home.', 'default.png', '2026-05-29 09:41:50'),
(115, 'Camila Michael Rivera', 'camila.rivera52@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09263528258', 'Dedicated to helping clients find their dream home.', 'default.png', '2026-05-29 09:41:50'),
(116, 'Emma James Tan', 'emma.tan54@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09281744085', 'Dedicated to helping clients find their dream home.', 'default.png', '2026-05-29 09:41:50'),
(117, 'Catherine Lim', 'catherine.lim75@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09386618153', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(118, 'Miguel Bautista', 'miguel.bautista21@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09216475214', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(119, 'Joseph Reyes', 'joseph.reyes64@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09914799977', 'Offering well-maintained properties.', 'default.png', '2026-05-29 09:41:50'),
(120, 'Amanda Fernandez', 'amanda.fernandez89@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09574513461', 'Dedicated to helping clients find their dream home.', 'default.png', '2026-05-29 09:41:50'),
(121, 'Ava Rose Lim', 'ava.lim86@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09136404133', 'Offering well-maintained properties.', 'default.png', '2026-05-29 09:41:50'),
(122, 'Grace Bautista', 'grace.bautista32@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09804500002', 'Dedicated to helping clients find their dream home.', 'default.png', '2026-05-29 09:41:50'),
(123, 'Santos Tan', 'santos.tan39@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09803783036', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(124, 'Christopher Lim', 'christopher.lim34@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09374224647', 'Offering well-maintained properties.', 'default.png', '2026-05-29 09:41:50'),
(125, 'Christopher Cruz', 'christopher.cruz65@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09957365920', 'Offering well-maintained properties.', 'default.png', '2026-05-29 09:41:50'),
(126, 'Emily Garcia', 'emily.garcia74@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09225680788', 'Experienced real estate seller offering quality properties.', 'default.png', '2026-05-29 09:41:50'),
(127, 'Paul Joseph Delgado', 'paul.delgado72@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09294192376', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(128, 'Marie Mendoza', 'marie.mendoza62@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09257865996', 'Experienced real estate seller offering quality properties.', 'default.png', '2026-05-29 09:41:50'),
(129, 'Olivia Paul Lim', 'olivia.lim94@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09877852727', 'Selling residential properties in the Visayas region.', 'default.png', '2026-05-29 09:41:50'),
(130, 'Angelo Torres', 'angelo.torres16@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09926682917', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(131, 'Catherine Marie Rivera', 'catherine.rivera21@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09192799236', 'Selling residential properties in the Visayas region.', 'default.png', '2026-05-29 09:41:50'),
(132, 'Daniel Joseph Santos', 'daniel.santos87@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09557579071', 'Helping families find their perfect home.', 'default.png', '2026-05-29 09:41:50'),
(133, 'Sophia Fernandez', 'sophia.fernandez55@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09652924381', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(134, 'Grace Marie Morales', 'grace.morales58@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09403970624', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(135, 'Mia Grace Reyes', 'mia.reyes17@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09444028837', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(136, 'Elena Ramos', 'elena.ramos60@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09931001700', 'Specializing in luxury and mid-range homes.', 'default.png', '2026-05-29 09:41:50'),
(137, 'Emily Lim', 'emily.lim41@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09285258896', 'Selling residential properties in the Visayas region.', 'default.png', '2026-05-29 09:41:50'),
(138, 'Ava Mendoza', 'ava.mendoza50@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09952987043', 'Specializing in luxury and mid-range homes.', 'default.png', '2026-05-29 09:41:50'),
(139, 'Grace Michael Mendoza', 'grace.mendoza27@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09995635872', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(140, 'Olivia Marie Lim', 'olivia.lim85@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09131953703', 'Dedicated to helping clients find their dream home.', 'default.png', '2026-05-29 09:41:50'),
(141, 'Isabella Ramos', 'isabella.ramos34@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09839352150', 'Selling residential properties in the Visayas region.', 'default.png', '2026-05-29 09:41:50'),
(142, 'Lucas Morales', 'lucas.morales22@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09496888911', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(143, 'Anna Garcia', 'anna.garcia38@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09255181998', 'Specializing in luxury and mid-range homes.', 'default.png', '2026-05-29 09:41:50'),
(144, 'Ava Torres', 'ava.torres36@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09866616739', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(145, 'Elizabeth Santos', 'elizabeth.santos49@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09684691425', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(146, 'James Delgado', 'james.delgado69@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09674296770', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(147, 'Amanda Delgado', 'amanda.delgado25@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09929361532', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(148, 'Nathaniel Garcia', 'nathaniel.garcia50@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09719130309', 'Specializing in luxury and mid-range homes.', 'default.png', '2026-05-29 09:41:50'),
(149, 'Marie James Delgado', 'marie.delgado19@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09579633411', 'Dedicated to helping clients find their dream home.', 'default.png', '2026-05-29 09:41:50'),
(150, 'Joseph Marie Lopez', 'joseph.lopez53@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09607387360', 'Focus on affordable and family homes.', 'default.png', '2026-05-29 09:41:50'),
(151, 'Benjamin Michael Tan', 'benjamin.tan45@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09506043470', 'Professional seller with many years of experience.', 'default.png', '2026-05-29 09:41:50'),
(152, 'Mia Lim', 'mia.lim52@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09997216211', 'Experienced real estate seller offering quality properties.', 'default.png', '2026-05-29 09:41:50'),
(153, 'John Mendoza', 'john.mendoza76@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09235118483', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(154, 'Eduardo Rivera', 'eduardo.rivera47@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09289768385', 'Specializing in luxury and mid-range homes.', 'default.png', '2026-05-29 09:41:50'),
(155, 'Joseph Rose Garcia', 'joseph.garcia54@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09494239507', 'Offering well-maintained properties.', 'default.png', '2026-05-29 09:41:50'),
(156, 'Gabriella Hernandez', 'gabriella.hernandez47@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09268398433', 'Reliable seller of residential real estate.', 'default.png', '2026-05-29 09:41:50'),
(157, 'Anna Santos', 'anna.santos77@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09845898385', 'Selling prime location properties.', 'default.png', '2026-05-29 09:41:50'),
(158, 'Amanda Morales', 'amanda.morales67@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'seller', '09277334037', 'Offering well-maintained properties.', 'default.png', '2026-05-29 09:41:50'),
(159, 'Dev Admin', 'admin@example.com', '$2y$10$c7kCLpQfpxJLYwDYZrdfgu0s/v46R.3skckfkGC.UxXlTzg7UCO2a', 'admin', NULL, NULL, NULL, '2026-05-29 09:41:50'),
(160, 'David N. Caudillo', 'davidcaudillo@gmail.com', '$2y$10$z6kr1Vp4.0DZnwrqsPc3Dexbiyalmxvp3qOvGenHEA86Nhi5UgcRu', 'seller', '09713736272', 'Sporty', '1779891896_dea0093bbba8b5a70608.webp', '2026-05-29 09:41:50'),
(161, 'Joseph M. Fisk', 'josephfisk@gmail.com', '$2y$10$JMAyyJKwDBLOh1ocT5HxpuBMpUK/w5ntadg/uB4gYY0G.DcvqNNvm', 'buyer', '09636230608', 'Likes to hike', '1779893316_1d89599684ed4c88adc4.webp', '2026-05-29 09:41:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_buyer_property_favorite` (`buyer_id`,`property_id`),
  ADD KEY `fk_favorites_property` (`property_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_offer` (`offer_id`),
  ADD KEY `fk_payments_buyer` (`buyer_id`),
  ADD KEY `fk_payments_seller` (`seller_id`),
  ADD KEY `fk_payments_property` (`property_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `property_history`
--
ALTER TABLE `property_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_property_id` (`property_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `property_history`
--
ALTER TABLE `property_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favorites_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payments_offer` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_payments_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payments_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
