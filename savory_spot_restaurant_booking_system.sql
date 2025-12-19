-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2025 at 01:43 AM
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
-- Database: `savory_spot_restaurant_booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(16, 'admin', 'admin@gmail.com', '$2y$10$zLhMCIwTSEVbrzYyhwXJ7.6juMGeazjiT1wQuF7AbrA4vH.j9GsOe', '2025-12-13 15:58:34');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `reservation_id` varchar(50) NOT NULL,
  `card_last4` varchar(4) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `date` date NOT NULL,
  `time` time NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `guests` int(11) NOT NULL,
  `table_type` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`date`, `time`, `name`, `email`, `contact`, `guests`, `table_type`, `status`, `id`, `user_id`) VALUES
('2025-12-13', '19:00:00', 'cholo', 'cholowiaontirambulo@gmail.com', 'eqweqweqw', 2, 'Intimate Bistro Tables', 'Approved', 3, 17),
('2025-12-13', '19:00:00', 'marben', 'marben123@gmail.com', '312312323', 4, 'The Chef’s Table', 'Approved', 4, 18),
('2025-12-15', '19:00:00', 'fran', 'franco@gmail.com', '1232312313', 2, 'The Grand Salon', 'Cancelled', 6, 3),
('2025-12-19', '19:00:00', 'franco', 'franco@gmail.com', '09949518019', 4, 'Al Fresco Terrace', 'Approved', 7, 3),
('2026-01-02', '19:00:00', 'co', 'franco@gmail.com', '123', 4, 'The Chef’s Table', 'Pending', 8, 3),
('2026-01-10', '19:00:00', 'franc', 'franc123@gmail.com', '09123123234', 12, 'Intimate Bistro Tables', 'Pending', 9, 3),
('2025-12-19', '19:00:00', 'franco pamulam', 'franco321@gmail.com', '09949518019', 2, 'Intimate Bistro Tables', 'Approved', 11, 23);

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `seats` int(11) NOT NULL,
  `status` enum('available','reserved','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`) VALUES
(3, 'franco@gmail.com', '$2y$10$PQutyKEH6oRPy/JI6JwXiem2ewE7kdNpdv3j39OgmJzkbqgjEEKcG', '2025-11-22 23:30:24'),
(17, 'cholowiaontirambulo@gmail.com', '$2y$10$t1.r3.xJhLDmKHT.sFAeROfomZuLnrJYwix9aOkd6KdbRdYkpGpRy', '2025-12-13 16:35:42'),
(18, 'marben123@gmail.com', '$2y$10$TEbHdf4sKvHRpy8dDdurbOSRclOmw5HpCXm1v.Zl5pr3fz.JO.Lg2', '2025-12-13 16:53:31'),
(19, 'naruto@gmail.com', '$2y$10$I12nQIqVxxmbSLfxvOyvWOgfF0BABqVWQTLa2aedNRNeOQiw4xT8G', '2025-12-15 21:01:31'),
(20, 'cholo12345@gmail.com', '$2y$10$HcNSC3abdQ/XbEyoqwXiKOKMs1dauBXxh8/sG6EXosSvFpJuEwDcC', '2025-12-19 15:31:27'),
(21, 'dashvyn@gmail.com', '$2y$10$U8d7Cp3q5HDngA5p.yWNveEHd2KDFuxgIgSjj5dsmKNFwpVgvBi6W', '2025-12-19 15:59:05'),
(22, 'chol@gmail.com', '$2y$10$4o23cGOJ.aJraC9WVk8.V.UKYdPulm5vx4cTDFve02AyLkIwdStBm', '2025-12-19 16:13:44'),
(23, 'franco321@gmail.com', '$2y$10$QjuEc5tC3dptSmM45U4qM.elaBjC9PCwpDVPFcmHZx3UnDY9Gqd.C', '2025-12-19 16:15:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
