-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2026 at 09:02 AM
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
-- Database: `homecare_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(128) NOT NULL,
  `role` enum('admin','caregiver') NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auth_tokens`
--

INSERT INTO `auth_tokens` (`id`, `user_id`, `token`, `role`, `expires_at`, `created_at`) VALUES
(1, 2, 'c5dafdbe441173ff8cabe134140a5fbf29ae5f8e9e19021263bfab2f50f0a4ed', 'caregiver', '2026-10-01 09:25:04', '2026-09-01 07:25:04'),
(2, 1, 'c523bec17d2f9538e0d5b9deb71949e7120a36295863b1e340323024f750f771', 'admin', '2026-10-01 09:25:04', '2026-09-01 07:25:04'),
(3, 2, '69f7697ffc0e4216537e41141114f7c8faec952530d7ed5fac68b7bea7fd300c', 'caregiver', '2026-10-01 09:29:08', '2026-09-01 07:29:08'),
(4, 2, 'a07887280a27c5ba64a943df58efade974ed447ca45dc0eac73c2c854b537234', 'caregiver', '2026-10-01 09:29:19', '2026-09-01 07:29:19'),
(5, 2, 'df2568899235b32e5f5760fc74470bcf2d1063cac6964bd3b9e04b28c0353d25', 'caregiver', '2026-10-01 09:40:39', '2026-09-01 07:40:39'),
(6, 2, '70014867fbc52fa4926cb2025f4051d30a1fa4a5d328f27a01d99119f07de67d', 'caregiver', '2026-10-01 11:38:59', '2026-09-01 09:38:59'),
(7, 1, '208781b891b692644af28b883b3817e63d2b3cb298aaf6cd2891b5a62b4d3836', 'admin', '2026-10-01 11:40:18', '2026-09-01 09:40:18'),
(8, 1, '47ded4db4bad504fdebfa7ff26babb9857627fd9869ebcdf7147febee5c129b7', 'admin', '2026-10-01 11:40:45', '2026-09-01 09:40:45'),
(9, 1, '7cfe90a15ffb81da5d845b57b8e44a6cac2c64aa972722bb24f4d4fdd7f8fec1', 'admin', '2026-10-01 11:49:59', '2026-09-01 09:49:59'),
(10, 1, '21c38b082932655543a2881356b67424cf737d0557a1aa747febefa74756c73c', 'admin', '2026-10-01 11:50:19', '2026-09-01 09:50:19'),
(11, 1, '8ba57e23366b87f0a5e5d60113ca4203d3e6c7d099ac067b9420f55549c07119', 'admin', '2026-10-01 12:10:48', '2026-09-01 10:10:48'),
(12, 2, '2c5c31be354e9d486266a649aa0e67b93c2065531945a3708f9ff18daf8e6d96', 'caregiver', '2026-10-01 14:26:05', '2026-09-01 12:26:05'),
(13, 1, 'f8e7b046ed2c98cde67e1efd1b86cb1db4f7137749a492bf0211d41dae41fdee', 'admin', '2026-10-01 14:26:59', '2026-09-01 12:26:59'),
(14, 2, '69f7e80f9366f0623ad6f797821df8cb80e76dfbef48ed156b5490807e030d00', 'caregiver', '2026-10-22 08:00:45', '2026-09-22 06:00:45'),
(15, 1, '258a3aaef2e09281cb20b1a03211aa28ba4c1f7c57a009f98d00e61f85691543', 'admin', '2026-10-22 08:00:58', '2026-09-22 06:00:58'),
(16, 1, '5dc453041be923612e2224e2f37e2e8baec18f80fb3579b0197be36e62fcf4bd', 'admin', '2026-10-22 08:29:39', '2026-09-22 06:29:39'),
(17, 2, 'e477fef03403314f04126781e2b5d02277379f3e435708587bc5acd17efa1db4', 'caregiver', '2026-10-22 08:31:29', '2026-09-22 06:31:29'),
(18, 2, 'c95b29bd117b265f67a986455cc079bdc7b579ac551dcfb6ccc90216fa957894', 'caregiver', '2026-09-22 09:49:57', '2026-09-22 06:49:57'),
(19, 1, '9fd96faa35c441753ac4f37ddc1fd8e8503d5dab1879f55b3046481592bf5fd1', 'admin', '2026-10-22 08:55:01', '2026-09-22 06:55:01'),
(20, 2, '542dbd70cbe7050a927a43ab424525e4f85fbccfaacb3f1b43eeabc1dda94a44', 'caregiver', '2026-10-22 08:55:42', '2026-09-22 06:55:42');

-- --------------------------------------------------------

--
-- Table structure for table `daily_progress`
--

CREATE TABLE `daily_progress` (
  `id` int(11) NOT NULL,
  `time_entry_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `bathing` varchar(2) DEFAULT NULL,
  `mobility` varchar(2) DEFAULT NULL,
  `bed_chair` varchar(2) DEFAULT NULL,
  `continence` varchar(2) DEFAULT NULL,
  `eating` varchar(2) DEFAULT NULL,
  `toileting` varchar(2) DEFAULT NULL,
  `dressing` varchar(2) DEFAULT NULL,
  `medication` varchar(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_progress`
--

INSERT INTO `daily_progress` (`id`, `time_entry_id`, `date`, `bathing`, `mobility`, `bed_chair`, `continence`, `eating`, `toileting`, `dressing`, `medication`, `created_at`, `updated_at`) VALUES
(2, 1, '2026-09-01', 'I', 'H', 'A', 'A', 'A', 'H', 'A', 'H', '2026-09-01 07:44:11', '2026-09-01 07:44:11'),
(3, 2, '2026-09-01', 'I', 'A', 'A', 'A', 'A', 'A', 'A', 'S', '2026-09-01 07:51:45', '2026-09-01 07:51:45'),
(4, 3, '2026-09-22', 'I', 'A', 'A', 'A', 'S', 'A', 'H', 'A', '2026-09-22 06:52:08', '2026-09-22 06:52:08');

-- --------------------------------------------------------

--
-- Table structure for table `iadls`
--

CREATE TABLE `iadls` (
  `id` int(11) NOT NULL,
  `time_entry_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `housekeeping` tinyint(1) DEFAULT 0,
  `meal_prep` tinyint(1) DEFAULT 0,
  `shopping` tinyint(1) DEFAULT 0,
  `transportation` tinyint(1) DEFAULT 0,
  `managing_medicines` tinyint(1) DEFAULT 0,
  `laundry` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `iadls`
--

INSERT INTO `iadls` (`id`, `time_entry_id`, `date`, `housekeeping`, `meal_prep`, `shopping`, `transportation`, `managing_medicines`, `laundry`, `created_at`, `updated_at`) VALUES
(2, 1, '2026-09-01', 1, 1, 1, 1, 1, 1, '2026-09-01 07:44:11', '2026-09-01 07:44:11'),
(3, 2, '2026-09-01', 1, 1, 1, 1, 1, 1, '2026-09-01 07:51:45', '2026-09-01 07:51:45'),
(4, 3, '2026-09-22', 1, 1, 1, 1, 1, 1, '2026-09-22 06:52:08', '2026-09-22 06:52:08');

-- --------------------------------------------------------

--
-- Table structure for table `policyholders`
--

CREATE TABLE `policyholders` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `policy_number` varchar(80) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `policyholders`
--

INSERT INTO `policyholders` (`id`, `name`, `policy_number`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Mary Johnson', 'POL-1001', '7777777777', '45 Elm Avenue', '2026-09-01 07:24:39', '2026-09-01 07:24:39'),
(2, 'Robert Smith', 'POL-1002', '6666666666', '78 Oak Lane', '2026-09-01 07:24:39', '2026-09-01 07:24:39'),
(5, 'samarjit', 'POL-1003', '', '', '2026-09-22 06:53:26', '2026-09-22 06:53:26'),
(6, 'ddddddd', 'POL-1004', '', '', '2026-09-22 06:53:39', '2026-09-22 06:53:39'),
(7, 'wwww', 'POL-1005', '', '', '2026-09-22 06:53:48', '2026-09-22 06:53:48');

-- --------------------------------------------------------

--
-- Table structure for table `signatures`
--

CREATE TABLE `signatures` (
  `id` int(11) NOT NULL,
  `time_entry_id` int(11) NOT NULL,
  `caregiver_signature` longtext DEFAULT NULL,
  `policyholder_signature` longtext DEFAULT NULL,
  `caregiver_signed_date` datetime DEFAULT NULL,
  `policyholder_signed_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `signatures`
--

INSERT INTO `signatures` (`id`, `time_entry_id`, `caregiver_signature`, `policyholder_signature`, `caregiver_signed_date`, `policyholder_signed_date`, `created_at`, `updated_at`) VALUES
(2, 1, 'data:image/png;base64,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', 'data:image/png;base64,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', '2026-09-01 09:49:44', '2026-09-01 09:49:44', '2026-09-01 07:49:44', '2026-09-01 07:49:44'),
(3, 2, 'data:image/png;base64,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', 'data:image/png;base64,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', '2026-09-01 09:51:51', '2026-09-01 09:51:51', '2026-09-01 07:51:51', '2026-09-01 07:51:51'),
(4, 3, 'data:image/png;base64,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', 'data:image/png;base64,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', '2026-09-22 08:52:15', '2026-09-22 08:52:15', '2026-09-22 06:52:15', '2026-09-22 06:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `time_entries`
--

CREATE TABLE `time_entries` (
  `id` int(11) NOT NULL,
  `caregiver_id` int(11) NOT NULL,
  `policyholder_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` varchar(8) NOT NULL,
  `time_out` varchar(8) DEFAULT NULL,
  `total_hours` decimal(6,2) DEFAULT 0.00,
  `rate` decimal(8,2) DEFAULT 0.00,
  `total_charge` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','completed') NOT NULL DEFAULT 'active',
  `admin_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `time_entries`
--

INSERT INTO `time_entries` (`id`, `caregiver_id`, `policyholder_id`, `date`, `time_in`, `time_out`, `total_hours`, `rate`, `total_charge`, `status`, `admin_status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-09-01', '13:13', '13:20', 0.12, 150.00, 18.00, 'completed', 'approved', '2026-09-01 07:43:44', '2026-09-01 09:52:13'),
(2, 2, 2, '2026-09-01', '13:21', '13:21', 0.00, 200.00, 0.00, 'completed', 'approved', '2026-09-01 07:51:21', '2026-09-01 09:52:12'),
(3, 2, 1, '2026-09-22', '12:20', '12:22', 0.03, 69.00, 2.07, 'completed', 'pending', '2026-09-22 06:51:32', '2026-09-22 06:52:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` enum('admin','caregiver') NOT NULL DEFAULT 'caregiver',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@homecare.com', '$2y$10$7NUWJSt3meECsb0yO639mu0APm6zL8DFZkQeRJ/n2zgTinKHmDO7.', '9999999999', 'Head Office', 'admin', '2026-09-01 07:24:40', '2026-09-01 07:26:30'),
(2, 'John Caregiver', 'caregiver@homecare.com', '$2y$10$7NUWJSt3meECsb0yO639mu0APm6zL8DFZkQeRJ/n2zgTinKHmDO7.', '8888888888', '123 Care Street', 'caregiver', '2026-09-01 07:24:40', '2026-09-01 07:26:30'),
(3, 'Jane Doe', 'jane@homecare.com', '$2y$10$XyT23QUu4gMdha3./.J.XOS5.sfDC.3skv9kd6ido2/rwhjCsN5OS', '5551234', '', 'caregiver', '2026-09-01 07:25:31', '2026-09-01 07:25:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `fk_tok_user` (`user_id`);

--
-- Indexes for table `daily_progress`
--
ALTER TABLE `daily_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_dp_entry` (`time_entry_id`);

--
-- Indexes for table `iadls`
--
ALTER TABLE `iadls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_iadl_entry` (`time_entry_id`);

--
-- Indexes for table `policyholders`
--
ALTER TABLE `policyholders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signatures`
--
ALTER TABLE `signatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sig_entry` (`time_entry_id`);

--
-- Indexes for table `time_entries`
--
ALTER TABLE `time_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_caregiver` (`caregiver_id`),
  ADD KEY `idx_policyholder` (`policyholder_id`),
  ADD KEY `idx_date` (`date`);

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
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `daily_progress`
--
ALTER TABLE `daily_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `iadls`
--
ALTER TABLE `iadls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `policyholders`
--
ALTER TABLE `policyholders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `signatures`
--
ALTER TABLE `signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `time_entries`
--
ALTER TABLE `time_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `fk_tok_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_progress`
--
ALTER TABLE `daily_progress`
  ADD CONSTRAINT `fk_dp_entry` FOREIGN KEY (`time_entry_id`) REFERENCES `time_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `iadls`
--
ALTER TABLE `iadls`
  ADD CONSTRAINT `fk_iadl_entry` FOREIGN KEY (`time_entry_id`) REFERENCES `time_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `signatures`
--
ALTER TABLE `signatures`
  ADD CONSTRAINT `fk_sig_entry` FOREIGN KEY (`time_entry_id`) REFERENCES `time_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_entries`
--
ALTER TABLE `time_entries`
  ADD CONSTRAINT `fk_te_caregiver` FOREIGN KEY (`caregiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_te_policyholder` FOREIGN KEY (`policyholder_id`) REFERENCES `policyholders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
