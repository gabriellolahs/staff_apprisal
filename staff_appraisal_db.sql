-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 08:11 PM
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
-- Database: `staff_appraisal_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'FedpolelAdmin', 'FedpolelAdmin123');

-- --------------------------------------------------------

--
-- Table structure for table `allowed_staff`
--

CREATE TABLE `allowed_staff` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `allowed_staff`
--

INSERT INTO `allowed_staff` (`id`, `staff_id`, `created_at`) VALUES
(15, 'STF001', '2025-12-02 05:40:50'),
(16, 'STF00', '2025-12-02 06:35:40');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `passport` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_appraisal`
--

CREATE TABLE `staff_appraisal` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `promo_date` date DEFAULT NULL,
  `promo_from_position` varchar(100) DEFAULT NULL,
  `promo_from_gl` varchar(10) DEFAULT NULL,
  `promo_from_step` varchar(10) DEFAULT NULL,
  `promo_to_position` varchar(100) DEFAULT NULL,
  `promo_to_gl` varchar(10) DEFAULT NULL,
  `promo_to_step` varchar(10) DEFAULT NULL,
  `edu_school_name` varchar(200) DEFAULT NULL,
  `edu_year_admit` year(4) DEFAULT NULL,
  `edu_year_grad` year(4) DEFAULT NULL,
  `acad_certificate` varchar(200) DEFAULT NULL,
  `acad_award_date` date DEFAULT NULL,
  `acad_grade` varchar(50) DEFAULT NULL,
  `acad_award_body` varchar(200) DEFAULT NULL,
  `prof_certificate` varchar(200) DEFAULT NULL,
  `prof_award_date` date DEFAULT NULL,
  `prof_award_body` varchar(200) DEFAULT NULL,
  `publication_title` varchar(500) DEFAULT NULL,
  `publication_journal` varchar(300) DEFAULT NULL,
  `publication_year` year(4) DEFAULT NULL,
  `publication_file` varchar(500) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `is_locked` tinyint(1) DEFAULT 0,
  `admin_approved` tinyint(1) DEFAULT 0,
  `admin_comment` text DEFAULT NULL,
  `staff_response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allowed_staff`
--
ALTER TABLE `allowed_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- Indexes for table `staff_appraisal`
--
ALTER TABLE `staff_appraisal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `is_locked` (`is_locked`),
  ADD KEY `admin_approved` (`admin_approved`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `allowed_staff`
--
ALTER TABLE `allowed_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff_appraisal`
--
ALTER TABLE `staff_appraisal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
