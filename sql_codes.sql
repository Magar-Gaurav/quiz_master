-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 08:23 AM
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
-- Database: `quizmaster`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `profile_image`) VALUES
(3, 'Administrator', 'admin@gmail.com', '$2y$10$c4X9Z3wSBh2Ium7LHi9RfuUNSNd9KHtmn4/iEh5GJp.MHFlFXXzoi', '1772432306_wp5208001.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard`
--

INSERT INTO `leaderboard` (`id`, `username`, `score`) VALUES
(4, 'Gourav Magar', 1000),
(5, 'Sneha Rai', 950),
(6, 'Deepak Magar', 920);

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(51, 12, 'Lady Augusta LoveLace', 0),
(52, 12, 'Charles Babbage', 1),
(53, 12, 'James Gossling', 0),
(54, 12, 'Guido van Rossum', 0),
(55, 13, 'Lady Augusta LoveLace', 0),
(56, 13, 'Charles Babbage', 0),
(57, 13, 'James Gossling', 1),
(58, 13, 'Guido van Rossum', 0),
(63, 16, 'Au', 1),
(64, 16, 'Ag', 0),
(65, 16, 'Gd', 0),
(66, 16, 'Go', 0),
(67, 17, 'Venus', 0),
(68, 17, 'Mars', 1),
(69, 17, 'Jupiter', 0),
(70, 17, 'Mercury', 0),
(79, 20, 'Prithivi Narayan Shah', 1),
(80, 20, 'Pratap Malla', 0),
(81, 20, 'Bir Bikram Shah', 0),
(82, 20, 'Mahendra Bir Bikram Shah', 0);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `created_at`) VALUES
(12, 4, 'Who is the father of computer science?', '2026-03-02 12:42:50'),
(13, 4, 'Who developed C?', '2026-03-02 12:50:06'),
(16, 7, 'What is the chemical symbol for gold?', '2026-03-04 09:47:06'),
(17, 7, 'Which planet is known as the \"Red Planet\"?', '2026-03-04 09:47:55'),
(20, 10, 'Who is the founder of greater Nepal?', '2026-03-07 11:30:44');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `created_at`) VALUES
(4, 'Computer Science', '2026-03-02 12:41:41'),
(7, 'Science', '2026-03-02 13:29:24'),
(10, 'history', '2026-03-07 11:23:43'),
(11, 'Science', '2026-03-09 20:47:08');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_history`
--

CREATE TABLE `quiz_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_history`
--

INSERT INTO `quiz_history` (`id`, `user_id`, `quiz_id`, `score`, `attempted_at`) VALUES
(1, 19, 7, 0, '2026-03-04 03:58:53'),
(2, 19, 7, 1, '2026-03-04 04:03:37'),
(3, 19, 7, 2, '2026-03-04 04:03:45'),
(4, 19, 4, 2, '2026-03-04 04:04:08'),
(5, 19, 7, 1, '2026-03-04 04:34:32'),
(6, 19, 4, 0, '2026-03-04 04:58:02'),
(7, 14, 4, 1, '2026-03-06 07:31:25'),
(8, 25, 10, 1, '2026-03-07 05:47:20'),
(9, 25, 7, 1, '2026-03-07 05:47:28'),
(10, 25, 4, 1, '2026-03-07 05:47:35'),
(11, 27, 10, 1, '2026-03-07 06:20:14'),
(12, 27, 7, 1, '2026-03-07 06:20:23'),
(13, 27, 7, 0, '2026-03-07 06:20:32'),
(14, 27, 7, 0, '2026-03-07 06:20:39'),
(15, 27, 7, 0, '2026-03-07 06:20:46'),
(16, 27, 7, 0, '2026-03-07 06:20:50'),
(17, 27, 7, 0, '2026-03-07 06:20:58'),
(18, 27, 4, 2, '2026-03-07 06:21:05'),
(19, 27, 7, 1, '2026-03-07 06:21:13'),
(20, 27, 7, 1, '2026-03-07 06:21:15'),
(21, 27, 7, 0, '2026-03-07 06:21:20'),
(22, 27, 7, 0, '2026-03-07 06:21:23'),
(23, 27, 10, 1, '2026-03-07 06:21:28'),
(24, 27, 7, 0, '2026-03-07 06:21:36'),
(25, 27, 7, 0, '2026-03-07 06:21:41'),
(26, 27, 10, 1, '2026-03-07 06:23:16'),
(27, 27, 7, 1, '2026-03-07 06:23:21'),
(28, 27, 7, 2, '2026-03-07 06:23:28'),
(29, 27, 4, 2, '2026-03-07 06:23:35'),
(30, 27, 7, 0, '2026-03-07 06:24:14'),
(31, 28, 10, 1, '2026-03-15 19:29:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `profile_pic`) VALUES
(14, 'Gaurav Magar', 'gauravmagar664@gmail.com', '$2y$10$93i3JaWQjvE0IavaGufWvepyBQTcp6RoBVeCjAH7UA/se7ZRlCt9K', '2026-01-02 16:46:54', NULL),
(16, 'Gaurav Magar', 'gourav@example.com', '', '2026-03-02 07:17:46', NULL),
(17, 'Sandy', 'sandy@gmail.com', '$2y$10$kYE8pMQZ6vnvaZoVB2rAzOdC4b0RWUrGjHP763i7XYql/eyeKQt0u', '2026-03-02 07:19:26', NULL),
(19, 'Saurav Magar', 'sourav@gmail.com', '$2y$10$m3nfKXuNmQ4WK434tyUjD.fX5Ik2sdmaWZtvLSyqbdhUXq0L5HY52', '2026-03-04 03:41:48', '1772600332_noob.avif'),
(20, 'Saurav Magar', 'sande@gmail.com', '$2y$10$OgnGypL71N8dxWLpusVO9eCfomCmQBkXCsCA9aLN2u2woU3y19/x6', '2026-03-06 06:25:00', NULL),
(23, 'Sndesh', 'sande@email.com', '$2y$10$Sccx8qn6ZwQmCrqVooBqoum9soWjY8XjgTtIewNPi8pTc1qeXDbTK', '2026-03-07 05:39:33', NULL),
(24, 'gourav', 'gourav@gmail.com', '$2y$10$8.rKnvJph/Q0wFK.ZqpD7OJYleyw6fojqb9Q25jZjlZURyFv7yTti', '2026-03-07 05:46:48', NULL),
(25, 'gourav', 'gourav@ex.com', '$2y$10$vW2CYnydbao6Oj.l7z23..lwAbKWLWye2lzaLbscv4eWghk5GwgjO', '2026-03-07 05:47:02', '1772862471_wp5208001.jpg'),
(26, 'no_named', 'no_named@gmail.com', '$2y$10$3dxA.e8QyXE08rMhbJSybO5d0y5bQylCbIdXojpOyEQ42S4zFm7K6', '2026-03-07 06:18:14', NULL),
(27, 'noob', 'noob@gmail.com', '$2y$10$H15U1uwvWstlm4sC5sxCbeO34.2WNxWrni4dokQ13roWLEcKc9mRS', '2026-03-07 06:19:41', NULL),
(28, 'user1', 'user@email.com', '$2y$10$lrkU02Incik.O/K9D2XiOegJmZM1HNob0YFUX15V..kwQpM3Ir40S', '2026-03-15 19:29:11', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_history`
--
ALTER TABLE `quiz_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `quiz_history`
--
ALTER TABLE `quiz_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_history`
--
ALTER TABLE `quiz_history`
  ADD CONSTRAINT `quiz_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `quiz_history_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
