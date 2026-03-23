-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 03:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
(16, 6, 4, 15, 'of course ', '2025-11-22 06:41:58');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `property_id`, `buyer_id`, `amount`, `status`) VALUES
(5, 15, 4, 50000000.00, 'accepted');

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
(14, 6, 'Luxury House', 'Garage ni Boss K', 30000000.00, 'Albuera, Tinag-an', 0, 'uploads/1759846048_house7.jpg'),
(15, 6, 'City House', 'Murag balay sa Larva', 40000000.00, 'Ormoc City ', 0, 'uploads/1759846310_house9.jpg'),
(17, 3, 'Guba nga balay', 'naguba sa baha', 40000.00, 'Ormoc City, Tambulilid', 0, 'writable/uploads/1763821172_210d58fb9e254353b190.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('buyer','seller') NOT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `contact`, `bio`, `profile_pic`) VALUES
(1, 'bibing', 'bebe@gmail.com', '$2y$10$ZnugIzcNNtevkDorf2xz1e.U7m4mrLl5UxASifwDwcoqRFeW6yfd.', 'buyer', '09867588575', 'im Bandsexual', '1759729606_hehe.jpg'),
(2, 'sidney', 'sid@gmail.com', '$2y$10$4XqIjyCzaUMjlp7IyHjs9ujAjrha1wFPe9uaaqK9b4DPrYb7aFCRy', 'seller', NULL, NULL, 'default.png'),
(3, 'Jeycel Leoveras', 'jeycel@gmail.com', '$2y$10$x.vfaG.cd6wwgPOf6odrEOdp11/PekXKaIrHYWT74Je3R2OnrrT.y', 'seller', '09169327857', 'Chi shii', '1760154211_day1.jpg'),
(4, 'Mark Rabaya', 'mark@gmail.com', '$2y$10$O7EDirAD8kygpZwyODmXTOvjEewYQWqpPJ/8mB9/n.KiBloDE1JMy', 'buyer', NULL, NULL, 'default.png'),
(5, 'Felip Samuel', 'felipsamuel@gmail.com', '$2y$10$O5HpZtiBptpcVIJ67zvVcOdzGjFiq54pO2PHL5LUnMm0mGdhW66RC', 'buyer', '09382152064', 'i beat old people for fun', 'user_68e51802dd0f6.jpg'),
(6, 'Althea Centino', 'althea@gmail.com', '$2y$10$8pngt7GcWsBh1n9YsSJKmehymzCAVMfEPFMrjhcoLJj3Yxp/Fmp5G', 'seller', '09361425992', 'bagan tamo tanan', 'user_68e51a122abd3.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

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
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
