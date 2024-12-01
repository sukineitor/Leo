-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 11:27 PM
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
-- Database: `link_sharing_site`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `link_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `link_id`, `user_id`, `content`, `created_at`) VALUES
(44, 30, 15, 'Wueno', '2024-11-28 00:13:43'),
(45, 30, 15, 'good', '2024-11-28 00:24:31'),
(46, 35, 11, 'wuenaas', '2024-11-28 00:27:55');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `link_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `link_id`, `user_id`) VALUES
(17, 26, 9),
(18, 26, 10),
(19, 26, 11),
(28, 28, 10),
(20, 28, 11),
(21, 29, 11),
(27, 29, 15),
(23, 30, 10),
(22, 30, 11),
(24, 30, 15),
(25, 35, 11),
(26, 35, 15);

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`id`, `user_id`, `url`, `title`, `description`, `likes`, `created_at`, `image`, `expires_at`) VALUES
(26, 9, 'https://treeworldgames.netlify.app/', 'GAME GALLERY', 'GALLERY OF GAMES WHERE YOU CAN DOWNLOAD THEM FOR FREE', 3, '2024-11-27 03:39:22', NULL, '2024-12-01 04:11:29'),
(28, 11, 'https://youtu.be/zcBMlepkiI4?si=-PwqnW5IbiyHw1cV', '5 Days with Me', 'Youtube video about me, support please', 2, '2024-11-27 03:46:21', NULL, '2024-11-29 04:11:53'),
(29, 11, 'https://www.facebook.com/reel/2236044106781673', 'facebook rell', 'Help me by liking my Facebook', 2, '2024-11-27 03:48:20', NULL, '2024-11-28 05:30:57'),
(30, 11, 'https://www.tiktok.com/@leojhonv?_t=8rjSsxuEgMC&_r=1', 'TikTok', 'follow me on my account', 3, '2024-11-27 04:25:26', NULL, '2024-11-28 10:25:26'),
(35, 15, 'https://treeworldgames.netlify.app/', 'Arbol gamer', 'Sitio web para descargaar juegos ', 2, '2024-11-28 00:15:17', NULL, '2024-11-30 06:15:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `profile_image`) VALUES
(9, 'NardeLink', 'nardelink@gmail.com', '$2y$10$mYNw4FcECGnRXxAXGNIflOtXTY.M97fNC0cD1ZzIb.hsKHalKfTL.', '2024-11-27 03:36:40', NULL),
(10, 'LeoJhonV', 'leonardo@gmail.com', '$2y$10$1fxGzUab6BXmYvdNv9UT.OcdA8FHEeEXr5Da/rzJL6t.uIP6IR9wq', '2024-11-27 03:40:33', NULL),
(11, 'Nardella', 'nardella@gmail.com', '$2y$10$sOUCjRetQXmMDgwL.fq96OlKp4qM7f/eQ4KOV4OtZX8mEIxtSvRTy', '2024-11-27 03:44:32', NULL),
(12, 'Juan', 'juan@gmail.com', '$2y$10$F0oM/AEgtwhJucQ9dA8Ao.PRI82QnQtXN6ijvgbfYO3p/DGHUDBjC', '2024-11-27 23:56:56', NULL),
(13, 'leo', 'leo@gmail.com', '$2y$10$N9KuhseD9va0KmCiMSJsLuAbhB.frpw9eZDbjYv5qbEy2t/64HPpC', '2024-11-28 00:03:17', NULL),
(14, 'jhona', 'jhona@gmail.com', '$2y$10$gY.vo8v5cQSh9nKK9HGy0.95FWNcvVFdbH0/wQKYFWApKvoTdaAYi', '2024-11-28 00:09:12', NULL),
(15, 'boby', 'boby@gmail.com', '$2y$10$i1Al9NlGSb9x2ddfNst.Gu6w9eXiDFx3kZpnEDga/EFPlOo7wMqeS', '2024-11-28 00:12:52', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_id` (`link_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `link_id` (`link_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `links`
--
ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
