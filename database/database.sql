-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 18, 2022 at 05:20 PM
-- Server version: 10.5.13-MariaDB
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shortener`
--

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE `domains` (
  `id` int(11) NOT NULL,
  `domain` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `domains`
--

INSERT INTO `domains` (`id`, `domain`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'shortener.local', 3, '2022-04-16 19:54:53', '2022-04-18 16:55:21');

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `id` int(11) NOT NULL,
  `url` varchar(1000) COLLATE utf8_persian_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_persian_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `domain_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(256) COLLATE utf8_persian_ci NOT NULL,
  `last_name` varchar(256) COLLATE utf8_persian_ci NOT NULL,
  `email` varchar(256) COLLATE utf8_persian_ci NOT NULL,
  `password` varchar(2048) COLLATE utf8_persian_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Bill', 'Gates', 'bill.gates@mail.com', '$2y$10$iUq89y4TSgTLEaWIqhKdV.5cZJxUg4QiN.YIswJY8xoXJttL2RJb.', '2022-04-13 19:00:11', '2022-04-13 19:00:11'),
(3, 'Steve', 'Jobs', 'steve.jobs@mail.com', '$2y$10$iUq89y4TSgTLEaWIqhKdV.5cZJxUg4QiN.YIswJY8xoXJttL2RJb.', '2022-04-13 19:00:49', '2022-04-13 19:00:49'),
(4, 'Mark', 'Zuckerberg', 'mark.zuckerberg@mail.com', '$2y$10$.WQDsuNg2ca9Kao2KX3Bk.xBMklcOl91KLXRD4ZvbF9PtSUioBxni', '2022-04-13 19:01:28', '2022-04-13 19:01:28'),
(5, 'Evan', 'Spiegel', 'evan.spiegel@mail.com', '$2y$10$.WQDsuNg2ca9Kao2KX3Bk.xBMklcOl91KLXRD4ZvbF9PtSUioBxni', '2022-04-13 19:02:30', '2022-04-13 19:02:30'),
(6, 'Jack', 'Dorsey', 'jack.dorsey@mail.com', '$2y$10$.WQDsuNg2ca9Kao2KX3Bk.xBMklcOl91KLXRD4ZvbF9PtSUioBxni', '2022-04-13 19:02:57', '2022-04-13 19:02:57'),
(17, 'Alex', 'Warden', 'alex.warden@mail.com', '$2y$10$.WQDsuNg2ca9Kao2KX3Bk.xBMklcOl91KLXRD4ZvbF9PtSUioBxni', '2022-04-15 11:09:31', '2022-04-17 13:05:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `domain` (`domain`);

--
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

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
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
