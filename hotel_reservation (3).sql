-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 07:58 AM
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
-- Database: `hotel_reservation`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `hotel_id`, `check_in`, `check_out`, `status`, `created_at`) VALUES
(19, 8, 3, '2026-04-05', '2026-04-08', 'pending', '2026-04-05 07:07:13'),
(29, 13, 1, '2026-04-10', '2026-04-13', 'cancelled', '2026-04-10 13:29:08'),
(30, 14, 3, '2026-04-10', '2026-04-13', 'confirmed', '2026-04-10 16:20:39'),
(31, 13, 2, '2026-04-11', '2026-04-14', 'pending', '2026-04-11 15:49:15');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(150) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `location`, `price_per_night`, `description`, `image_url`, `created_at`) VALUES
(1, 'Grand Hyatt KL', 'Grand Hyatt KL', 550.00, 'Updated description', '/hotel-api/api/uploads/1775364450_69d1e96204318.jpg', '2026-03-30 03:44:33'),
(2, 'Sunway Resort', 'Sunway ResortJaya', 280.00, 'Family resort with theme park access', '/hotel-api/api/uploads/1775364462_69d1e96e83889.jpg', '2026-03-30 03:44:33'),
(3, 'Eastern & Oriental', 'Eastern & Oriental Hotel', 350.00, 'Heritage hotel on the Penang waterfront', '/hotel-api/api/uploads/1775364471_69d1e9777cd99.jpeg', '2026-03-30 03:44:33'),
(4, 'Marriott Putrajaya', 'Marriott Putrajaya', 320.00, 'Modern hotel near government buildings', '/hotel-api/api/uploads/1775364480_69d1e9805b5cf.webp', '2026-03-30 03:44:33'),
(5, 'Lexis Hibiscus', 'Lexis hibiscus imperial suite', 200.00, 'Private pool villa by the sea', '/hotel-api/api/uploads/1775364491_69d1e98b26397.jpg', '2026-03-30 03:44:33'),
(7, 'Hilton KL', 'Hilton KL', 500.00, 'Modern city hotel', '/hotel-api/api/uploads/1775364499_69d1e993113fa.jpg', '2026-03-30 04:40:16'),
(8, 'TRX Hotel', 'TRX Hotel', 450.00, 'City view', '/hotel-api/api/uploads/1775364506_69d1e99a16c6d.jpg', '2026-03-30 05:05:39');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` enum('unpaid','paid') DEFAULT 'unpaid',
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `method`, `status`, `paid_at`) VALUES
(10, 29, 350.00, 'Credit Card', 'paid', '2026-04-10 07:29:28'),
(11, 30, 350.00, 'Credit Card', 'paid', '2026-04-10 10:20:41');

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `requests` int(11) DEFAULT 1,
  `window_start` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `ip_address`, `requests`, `window_start`) VALUES
(1, '::1', 55, '2026-04-11 13:52:37'),
(2, '::1', 1, '2026-04-11 13:52:37'),
(3, '::1', 1, '2026-04-11 13:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(4, 'Administrator', 'admin@email.com', '$2y$10$xl4kinXOanE3RjYhoOLtC.6BRNRdbDdHEzfDiJ7vjpCpbx0ClOgIu', 'admin', '2026-03-30 04:54:42'),
(7, 'Demo Customer', 'customer@email.com', '$2y$10$htTWLRZWUtZe2VplLx/htuv4Cr.X7kJH8bIkrwzmvuz6UQQu3Hk4e', 'customer', '2026-04-04 04:14:58'),
(8, 'syaher', 'syaher@email.com', '$2y$10$bEYNTj6LITym0jN6cOJhle/PLkvMpmMhBmiyNr4bPgHgYsjFf8rfC', 'customer', '2026-04-05 07:00:38'),
(10, 'Hilmi', 'hilmi@email.com', '$2y$10$c0B5WnfgmghKks645V0SzOtUbjxGcZH.O.jmBPdcppBVKSlIYruSa', 'customer', '2026-04-05 07:13:54'),
(13, 'hanif', 'hanif@email.com', '$2y$10$IPQT2qPetV2cRRKagQpvdOKMsht1rMENP01MJushewW4K9wsKlW1K', 'customer', '2026-04-05 07:14:58'),
(14, 'pak', 'pak@gmail.com', '$2y$10$IpvVsBAVRNNxg9QRfZqBuu4JFU2rrgwgR6hRb75R07y1lm2OHRYqi', 'customer', '2026-04-10 16:20:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
