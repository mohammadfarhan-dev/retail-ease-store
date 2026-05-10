-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2026 at 06:22 PM
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
-- Database: `retail_ease_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'Login', 'Ahmed Khan logged in.', '2026-05-08 11:02:57'),
(2, 1, 'Logout', 'Ahmed Khan logged out.', '2026-05-08 11:03:19'),
(3, 2, 'Login', 'Sara Ali logged in.', '2026-05-08 11:03:36'),
(4, 2, 'Logout', 'Sara Ali logged out.', '2026-05-08 11:07:00'),
(5, 1, 'Login', 'Ahmed Khan logged in.', '2026-05-08 11:07:25'),
(6, 1, 'Logout', 'Ahmed Khan logged out.', '2026-05-08 11:12:14'),
(7, 2, 'Login', 'Sara Ali logged in.', '2026-05-08 11:12:23'),
(8, 2, 'Order Created', 'Sara Ali placed an order for Wireless Mouse', '2026-05-08 11:12:37'),
(9, 2, 'Logout', 'Sara Ali logged out.', '2026-05-08 11:12:52'),
(10, 1, 'Login', 'Ahmed Khan logged in.', '2026-05-08 11:13:07'),
(11, 1, 'Order Status Updated', 'Admin changed order #1 from pending to pending', '2026-05-08 11:13:14'),
(12, 1, 'Order Status Updated', 'Admin changed order #1 from pending to approved', '2026-05-08 11:13:18'),
(13, 1, 'Order Status Updated', 'Admin changed order #1 from approved to completed', '2026-05-08 11:13:22'),
(14, 1, 'Logout', 'Ahmed Khan logged out.', '2026-05-08 11:16:10'),
(15, 2, 'Login', 'Sara Ali logged in.', '2026-05-08 11:16:22'),
(16, 2, 'Smart Assistant Used', 'Sara Ali reviewed and saved a Smart Assistant response.', '2026-05-08 11:17:14'),
(17, 2, 'Logout', 'Sara Ali logged out.', '2026-05-08 11:17:17'),
(18, 1, 'Login', 'Ahmed Khan logged in.', '2026-05-08 11:17:32'),
(19, 1, 'Login', 'Ahmed Khan logged in.', '2026-05-08 13:25:22'),
(20, 1, 'Logout', 'Ahmed Khan logged out.', '2026-05-08 13:27:39'),
(21, 2, 'Login', 'Sara Ali logged in.', '2026-05-08 13:28:05'),
(22, 2, 'Logout', 'Sara Ali logged out.', '2026-05-08 13:29:13'),
(23, 1, 'Login', 'Ahmed Khan logged in.', '2026-05-08 13:39:27'),
(24, 1, 'Product Updated', 'Admin updated product: Wireless Mouse', '2026-05-08 13:40:31'),
(25, 1, 'Product Created', 'Admin created product: Table', '2026-05-08 13:42:09'),
(26, 1, 'Product Created', 'Admin created product: Table', '2026-05-08 13:42:14'),
(27, 1, 'Product Deleted', 'Admin deleted product: Table', '2026-05-08 13:42:30'),
(28, 1, 'Product Updated', 'Admin updated product: Table', '2026-05-08 13:43:23'),
(29, 1, 'Logout', 'Ahmed Khan logged out.', '2026-05-08 13:44:20'),
(30, 2, 'Login', 'Sara Ali logged in.', '2026-05-08 13:44:42'),
(31, 2, 'Logout', 'Sara Ali logged out.', '2026-05-08 14:28:49'),
(32, 2, 'Login', 'Sara Ali logged in.', '2026-05-08 14:29:26'),
(33, 2, 'Logout', 'Sara Ali logged out.', '2026-05-08 14:29:33'),
(34, 1, 'Login', 'Ahmed Khan logged in.', '2026-05-08 14:29:43'),
(35, 1, 'Logout', 'Ahmed Khan logged out.', '2026-05-08 14:33:40'),
(36, 3, 'Login', 'User 2 logged in.', '2026-05-08 16:18:13'),
(37, 3, 'Logout', 'User 2 logged out.', '2026-05-08 16:18:25');

-- --------------------------------------------------------

--
-- Table structure for table `ai_logs`
--

CREATE TABLE `ai_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `assistant_response` text NOT NULL,
  `reviewed_by_user` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_logs`
--

INSERT INTO `ai_logs` (`id`, `user_id`, `question`, `assistant_response`, `reviewed_by_user`, `created_at`) VALUES
(1, 2, 'How do I check my order status?', 'You can check your order status from the My Orders page. Pending means the admin has not reviewed it yet, approved means it has been accepted, rejected means it was declined, and completed means the order process is finished.', 1, '2026-05-08 11:17:14');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `order_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `total_price`, `status`, `order_note`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 25.99, 'completed', '', '2026-05-08 11:12:37', '2026-05-08 11:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `stock`, `image`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Wireless Mouse', 'A comfortable wireless mouse suitable for office and study use.', 'Electronics', 25.99, 40, 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&amp;fit=crop&amp;w=800&amp;q=80', 'active', NULL, 1, '2026-05-08 10:42:04', '2026-05-08 13:40:31'),
(2, 'Bluetooth Headphones', 'High-quality wireless headphones with long battery life.', 'Electronics', 59.99, 25, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80', 'active', NULL, NULL, '2026-05-08 10:42:04', '2026-05-08 13:37:17'),
(3, 'Notebook Pack', 'Pack of 5 ruled notebooks for students and office users.', 'Stationery', 12.50, 80, 'https://images.unsplash.com/photo-1517842645767-c639042777db?auto=format&fit=crop&w=800&q=80', 'active', NULL, NULL, '2026-05-08 10:42:04', '2026-05-08 13:37:17'),
(4, 'Desk Lamp', 'Adjustable LED desk lamp for reading and workspaces.', 'Home Office', 34.99, 30, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=800&q=80', 'active', NULL, NULL, '2026-05-08 10:42:04', '2026-05-08 13:37:17'),
(5, 'Water Bottle', 'Reusable stainless steel water bottle.', 'Lifestyle', 18.00, 60, 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=800&q=80', 'active', NULL, NULL, '2026-05-08 10:42:04', '2026-05-08 13:37:17'),
(6, 'Table', 'Table for study', 'Furniture', 50.00, 10, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSyUVoswl4nkv3t3YDR4NLLtchtbRTrjyn6bg&amp;s', 'active', 1, 1, '2026-05-08 13:42:09', '2026-05-08 13:43:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@retailease.com', '$2y$10$G/lrZxk4689Yp/kWBIZm0./lcg2VsQBt3r93KHl7fHykE9atdvaGu', 'admin', '2026-05-08 11:02:24', '2026-05-08 16:20:07'),
(2, 'User 1', 'user@retailease.com', '$2y$10$Imc59OkH97pYCtF7rVQhNOeIBLlF/.mlAfHjaQTsw76/ZQHzXIo02', 'user', '2026-05-08 11:02:24', '2026-05-08 16:20:12'),
(3, 'User 2', 'User2@retailease.com', '$2y$10$c.IQy/W2eUySNvkJFic1wul4FbQDrKfHEHgAoqhh92OKbM6qW7ou6', 'user', '2026-05-08 16:17:03', '2026-05-08 16:17:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ai_logs`
--
ALTER TABLE `ai_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `ai_logs`
--
ALTER TABLE `ai_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_logs`
--
ALTER TABLE `ai_logs`
  ADD CONSTRAINT `ai_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
