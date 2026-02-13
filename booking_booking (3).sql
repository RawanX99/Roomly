-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2026 at 02:36 PM
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
-- Database: `booking_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `hotel_id`, `check_in_date`, `check_out_date`, `status`, `created_at`) VALUES
(16, 37, 11, 22, '2025-04-21', '2025-04-25', 'pending', '2025-04-19 09:16:51'),
(17, 18, 11, 22, '2025-04-27', '2025-04-29', 'pending', '2025-04-19 09:17:29'),
(27, 18, 11, 22, '2025-04-28', '2025-04-30', 'pending', '2025-04-22 06:07:21');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`) VALUES
(1, 'Riyadh'),
(2, 'Jeddah'),
(3, 'Mecca'),
(4, 'Medina'),
(5, 'Dammam'),
(6, 'Khobar'),
(7, 'Dharan'),
(8, 'Buraidah'),
(9, 'Tabuk'),
(10, 'Abha'),
(11, 'Taif'),
(12, 'Yanbu');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `hotel_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `hotel_name`, `description`, `latitude`, `longitude`, `country_id`, `user_id`, `created_at`) VALUES
(20, 'Skyview Hotel', 'Welcome to Skyview Hotel, where luxury meets comfort in a stunning setting. Nestled in the heart of the city, our hotel offers breathtaking views and a serene atmosphere, making it the perfect retreat for both business and leisure travelers.\r\n\r\n\r\nHotel Amenities : Outdoor Pool - Spa and Wellness Center - Fitness Center - Restaurant', 24.470901, 39.612236, 4, 19, '2025-03-17 10:30:42'),
(21, 'Sheraton Hotel', 'Situated in the city center of Jeddah with an exclusive location on the fashionable North Corniche, our Saudi Arabia hotel offers breathtaking views of the Red Sea. ', 21.613690, 39.108850, 2, 27, '2025-04-04 06:37:18'),
(22, 'Novotel Hotel', 'Novotel Makkah Hotel offers modern and comfortable rooms with high service standards.', 21.445250, 39.834389, 3, 32, '2025-04-04 06:51:05'),
(28, 'MovenPick Hotel', '5-star hotel in Jeddah for relaxation', 21.561000, 39.176700, 2, 51, '2025-04-27 08:42:23');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_images`
--

CREATE TABLE `hotel_images` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_images`
--

INSERT INTO `hotel_images` (`id`, `hotel_id`, `image_path`) VALUES
(17, 20, 'uploads/hotels/67d7f9d235b48.jpeg'),
(18, 21, 'uploads/hotels/67ef7e1e8753e.jpg'),
(19, 22, 'uploads/hotels/67ef8159ab90c.jpg'),
(20, 22, 'uploads/hotels/67ef8159abeb6.jpg'),
(31, 28, 'uploads/hotels/680dedef45ca0.jpeg'),
(32, 28, 'uploads/hotels/680dedef460d1.jpeg'),
(33, 28, 'uploads/hotels/680dedef46853.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `hotel_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(11, 20, 2, 1, 'no wifi connection', '2025-03-18 11:49:28'),
(13, 22, 18, 5, 'Overall Good and nice place.', '2025-04-10 06:32:24'),
(15, 20, 18, 5, 'Great hotel near the prophet\'s Mosque', '2025-04-19 10:48:28');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `room_type` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('available','booked') DEFAULT 'available',
  `booked_from` date DEFAULT NULL,
  `booked_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `room_type`, `price`, `status`, `booked_from`, `booked_to`) VALUES
(5, 20, 'Single Room', 150.00, 'available', NULL, NULL),
(9, 20, 'Twin Room', 250.00, 'available', NULL, NULL),
(11, 22, 'single Room', 280.00, 'booked', '2025-04-28', '2025-04-30'),
(36, 20, 'Double Room', 200.00, 'available', NULL, NULL),
(41, 28, 'Single Room', 300.00, 'available', NULL, NULL),
(42, 28, 'Twin Room', 500.00, 'available', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `created_at`, `updated_at`) VALUES
(2, 'manal', 'manal@gmail.com', '$2y$10$G34SWvGfN0kJAQnTWnZMWuoDhfoj9k0CMTv6q6ae2Mtc4O34KSoJm', 0, '2025-01-14 13:34:29', '2025-04-08 03:28:11'),
(18, 'Rawan', 'raw12@gmail.com', '$2y$10$JeK86WarA1yp2lbYtDYz8u.O2OQ7OrSnNaMMALjm1wfMEbS3Jcfqa', 0, '2025-03-17 10:16:35', '2025-03-17 10:16:35'),
(19, 'Rawan', 'raw123@gmail.com', '$2y$10$KXJIgv2BJg5xQVSqu5VlleZYl1qfDW8ZzL5fFIeMN0eDACNasnCwu', 1, '2025-03-17 10:17:54', '2025-03-17 10:17:54'),
(27, 'eman', 'eman123@gmail.com', '$2y$10$yeecjk05iKNXNK6gXFPfdOV0HefvRdhIod1ITPXj3zv/.LACISW0S', 1, '2025-04-04 05:56:44', '2025-04-04 05:56:44'),
(32, 'noof', 'noof123@gmail.com', '$2y$10$Q/9HhC8VkSrG6UCynoKB1.yOP9pQOIUgTCHWhnCAPmz9bF0ayYguy', 1, '2025-04-04 06:44:30', '2025-04-04 06:44:30'),
(37, 'noor', 'noor123@gmail.com', '$2y$10$cczUBNhwd1uxkrufbqiate6XUMJVm/VLlswO8eXczfk/U9uvL.Ni2', 0, '2025-04-19 09:16:18', '2025-04-19 09:16:18'),
(51, 'Dania', 'dania123@gmail.com', '$2y$10$lV5wumdHD4YuE6ipte8FceaAky6KrL1n/AIiubYC6riIY1T37/dEG', 1, '2025-04-27 08:38:22', '2025-04-27 08:38:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_country` (`country_id`);

--
-- Indexes for table `hotel_images`
--
ALTER TABLE `hotel_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `hotel_images`
--
ALTER TABLE `hotel_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`);

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `fk_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`);

--
-- Constraints for table `hotel_images`
--
ALTER TABLE `hotel_images`
  ADD CONSTRAINT `hotel_images_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
