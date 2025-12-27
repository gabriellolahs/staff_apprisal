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
-- Database: `staff_appraisal`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_appraisal`
--

CREATE TABLE `academic_appraisal` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `courses_attended` text DEFAULT NULL,
  `teaching_exp` text DEFAULT NULL,
  `courses_taught` text DEFAULT NULL,
  `supervision` text DEFAULT NULL,
  `research_progress` text DEFAULT NULL,
  `research_completed` text DEFAULT NULL,
  `thesis` text DEFAULT NULL,
  `authored_books` text DEFAULT NULL,
  `edited_books` text DEFAULT NULL,
  `book_contrib` text DEFAULT NULL,
  `journal_articles` text DEFAULT NULL,
  `accepted_papers` text DEFAULT NULL,
  `submitted_manuscripts` text DEFAULT NULL,
  `conferences` text DEFAULT NULL,
  `poly_activities` text DEFAULT NULL,
  `external_activities` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `is_locked` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(200) NOT NULL,
  `school_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `school_id`) VALUES
(4, 'Science and Laboratory Technology', 2),
(8, 'Civil Engineering', 1),
(9, 'Statistics', 2),
(10, 'Computer Science', 2),
(11, 'Electrical Engineering', 1),
(12, 'Accountancy', 3);

-- --------------------------------------------------------

--
-- Table structure for table `edit_requests`
--

CREATE TABLE `edit_requests` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `status` enum('pending_hod','pending_dean','pending_admin','approved','rejected') NOT NULL DEFAULT 'pending_hod',
  `staff_comment` text DEFAULT NULL,
  `approver_comment` text DEFAULT NULL,
  `current_stage` varchar(50) DEFAULT 'hod',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `save_biodata`
--

CREATE TABLE `save_biodata` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `school` int(11) NOT NULL,
  `department` int(11) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `marital_status` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `pob` varchar(100) DEFAULT NULL,
  `perm_address` text DEFAULT NULL,
  `contact_address` text DEFAULT NULL,
  `res_address` text DEFAULT NULL,
  `gsm` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `genotype` varchar(10) DEFAULT NULL,
  `nok_name` varchar(100) DEFAULT NULL,
  `nok_relation` varchar(50) DEFAULT NULL,
  `nok_phone` varchar(20) DEFAULT NULL,
  `nok_address` text DEFAULT NULL,
  `first_place_before` varchar(100) DEFAULT NULL,
  `first_date_before` date DEFAULT NULL,
  `first_place_fpi` varchar(100) DEFAULT NULL,
  `appt_type` varchar(50) DEFAULT NULL,
  `post_appt` varchar(50) DEFAULT NULL,
  `present_appt` varchar(50) DEFAULT NULL,
  `regularization` date DEFAULT NULL,
  `gl` varchar(10) DEFAULT NULL,
  `step` varchar(10) DEFAULT NULL,
  `confirmation` date DEFAULT NULL,
  `first_appt_pub` date DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `union_name` varchar(100) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `pfa` varchar(100) DEFAULT NULL,
  `pin_code` varchar(50) DEFAULT NULL,
  `spouse_name` varchar(100) DEFAULT NULL,
  `children` varchar(100) DEFAULT NULL,
  `office_held` varchar(100) DEFAULT NULL,
  `accommodation` varchar(50) DEFAULT NULL,
  `hobbies` text DEFAULT NULL,
  `extra_data` longtext DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `unlocked_by` varchar(50) DEFAULT NULL,
  `unlock_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `school_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `school_name`) VALUES
(2, 'School of Applied Science'),
(3, 'School of Business Studies and Management'),
(1, 'School of Engineering'),
(4, 'School of Environmental Design and Technology');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `category` varchar(150) NOT NULL,
  `sub_category` varchar(20) NOT NULL DEFAULT '',
  `passport` varchar(500) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `role` enum('STAFF','HOD','DEAN','SUPER_ADMIN') DEFAULT 'STAFF'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staff_id`, `surname`, `firstname`, `lastname`, `gender`, `category`, `sub_category`, `passport`, `password`, `is_active`, `created_at`, `updated_at`, `role`) VALUES
(4, 'STF002', 'JIMOH', 'MUMIN', 'OLUWATOBILOBA', 'Male', 'Teaching Staff', '', 'uploads/passports/STF002_1765093601.png', NULL, 1, '2025-12-07 07:46:41', NULL, 'STAFF'),
(6, 'STF001', 'OJO', 'MUMIN', 'AYOMIDE', 'Female', 'Non Teaching Staff', 'Junior Staff', 'uploads/passports/STF001_1765093937.png', NULL, 1, '2025-12-07 07:52:17', NULL, 'STAFF');

-- --------------------------------------------------------

--
-- Table structure for table `staff_appraisal`
--

CREATE TABLE `staff_appraisal` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `entry_type` enum('promotion','education','academic','professional','publication') NOT NULL,
  `field1` varchar(255) DEFAULT NULL,
  `field2` varchar(255) DEFAULT NULL,
  `field3` varchar(255) DEFAULT NULL,
  `field4` varchar(255) DEFAULT NULL,
  `field5` varchar(255) DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `admin_approved` tinyint(1) DEFAULT 0,
  `admin_comment` text DEFAULT NULL,
  `staff_response` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_biodata`
--

CREATE TABLE `staff_biodata` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `category` varchar(150) NOT NULL,
  `sub_category` varchar(20) DEFAULT NULL,
  `passport` varchar(500) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_locked` tinyint(1) DEFAULT 0,
  `verified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) DEFAULT NULL,
  `role` enum('staff','hod','dean','super_admin') NOT NULL,
  `school` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_appraisal`
--
ALTER TABLE `academic_appraisal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD KEY `staff_id_2` (`staff_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `allowed_staff`
--
ALTER TABLE `allowed_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `edit_requests`
--
ALTER TABLE `edit_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_edit_requests_staff` (`staff_id`);

--
-- Indexes for table `save_biodata`
--
ALTER TABLE `save_biodata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_name` (`school_name`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD KEY `idx_staff_id` (`staff_id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `staff_appraisal`
--
ALTER TABLE `staff_appraisal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `staff_biodata`
--
ALTER TABLE `staff_biodata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD KEY `staff_id_2` (`staff_id`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_appraisal`
--
ALTER TABLE `academic_appraisal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `allowed_staff`
--
ALTER TABLE `allowed_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `edit_requests`
--
ALTER TABLE `edit_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `save_biodata`
--
ALTER TABLE `save_biodata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff_appraisal`
--
ALTER TABLE `staff_appraisal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff_biodata`
--
ALTER TABLE `staff_biodata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_appraisal`
--
ALTER TABLE `academic_appraisal`
  ADD CONSTRAINT `academic_appraisal_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
