-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2026 at 03:54 AM
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
-- Database: `enrollna`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `fullname`, `email`, `password`, `created`) VALUES
(1, 'rainier acquisa', 'rainier@eyysat.edu.ph', '$2y$10$4DRQN3MzgK6RGzXiPb0Cxem5eVzUBqmKB2DSLQwGoMNxZo3FqUUIS', '2026-07-30 16:27:49');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `created`) VALUES
(1, 'BSIT', 'Bachelor of Science in Information Technology', '2026-07-19 05:21:08'),
(2, 'BSCS', 'Bachelor of Science in Computer Science', '2026-07-19 05:21:08'),
(3, 'BSIS', 'Bachelor of Science in Information Systems', '2026-07-19 05:21:08'),
(5, 'BSBA', 'Bachelor of Science in Business Administration', '2026-07-19 05:21:08'),
(6, 'BSA', 'Bachelor of Science in Accountancy', '2026-07-19 05:21:08'),
(7, 'BSHM', 'Bachelor of Science in Hospitality Management', '2026-07-19 05:21:08'),
(8, 'BSTM', 'Bachelor of Science in Tourism Management', '2026-07-19 05:21:08'),
(9, 'BSCRIM', 'Bachelor of Science in Criminology', '2026-07-19 05:21:08'),
(10, 'BSN', 'Bachelor of Science in Nursing', '2026-07-19 05:21:08'),
(15, 'BSCE', 'Bachelor of Science in Civil Engineering', '2026-07-19 05:21:08');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `school_year` varchar(20) DEFAULT NULL,
  `sem` int(11) DEFAULT NULL,
  `STATUS` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('Paid','Pending','Unpaid') DEFAULT 'Pending',
  `paymongo_link_id` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_option` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `cash_amount_requested` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_details`
--

CREATE TABLE `enrollment_details` (
  `id` int(11) NOT NULL,
  `enrollment_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `max_slots` int(11) DEFAULT 40,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_id`, `course_id`, `section_name`, `max_slots`, `created`) VALUES
(21, NULL, 1, 'Section A', 40, '2026-07-31 00:13:11'),
(22, NULL, 1, 'Section B', 40, '2026-07-31 00:13:11'),
(23, NULL, 2, 'Section A', 40, '2026-07-31 00:13:11'),
(24, NULL, 2, 'Section B', 40, '2026-07-31 00:13:11'),
(25, NULL, 3, 'Section A', 40, '2026-07-31 00:13:11'),
(26, NULL, 3, 'Section B', 40, '2026-07-31 00:13:11'),
(27, NULL, 5, 'Section A', 40, '2026-07-31 00:13:11'),
(28, NULL, 5, 'Section B', 40, '2026-07-31 00:13:11'),
(29, NULL, 6, 'Section A', 40, '2026-07-31 00:13:11'),
(30, NULL, 6, 'Section B', 40, '2026-07-31 00:13:11'),
(31, NULL, 7, 'Section A', 40, '2026-07-31 00:13:11'),
(32, NULL, 7, 'Section B', 40, '2026-07-31 00:13:11'),
(33, NULL, 8, 'Section A', 40, '2026-07-31 00:13:11'),
(34, NULL, 8, 'Section B', 40, '2026-07-31 00:13:11'),
(35, NULL, 9, 'Section A', 40, '2026-07-31 00:13:11'),
(36, NULL, 9, 'Section B', 40, '2026-07-31 00:13:11'),
(37, NULL, 10, 'Section A', 40, '2026-07-31 00:13:11'),
(38, NULL, 10, 'Section B', 40, '2026-07-31 00:13:11'),
(39, NULL, 15, 'Section A', 40, '2026-07-31 00:13:11'),
(40, NULL, 15, 'Section B', 40, '2026-07-31 00:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_number` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `guardian` varchar(100) DEFAULT NULL,
  `guardian_contact` varchar(100) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `payment_status` enum('Pending','Paid') DEFAULT 'Pending',
  `enrollment_status` enum('Pending','Enrolled') DEFAULT 'Pending',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `remaining_balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `units` int(11) DEFAULT NULL,
  `sem` int(11) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `section_id` int(11) DEFAULT NULL,
  `schedule_day` varchar(20) DEFAULT NULL,
  `schedule_time` varchar(50) DEFAULT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `course_id`, `subject_code`, `subject_name`, `units`, `sem`, `year_level`, `created`, `section_id`, `schedule_day`, `schedule_time`, `room_number`, `is_available`) VALUES
(1, 1, 'IT101', 'Programming I', 3, 1, 1, '2026-07-30 16:24:50', 1, 'Mon', '8:00 AM', 'LAB 1', 1),
(2, 1, 'IT102', 'Database Systems', 3, 1, 1, '2026-07-30 16:24:50', 2, 'Tue', '10:00 AM', 'LAB 2', 1),
(3, 1, 'IT103', 'Networking', 3, 1, 1, '2026-07-30 16:24:50', 1, 'Wed', '1:00 PM', 'LAB 3', 1),
(4, 1, 'IT104', 'Web Development', 3, 1, 1, '2026-07-30 16:24:50', 2, 'Thu', '3:00 PM', 'LAB 4', 1),
(5, 1, 'IT105', 'Systems Analysis', 3, 1, 1, '2026-07-30 16:24:50', 1, 'Fri', '9:00 AM', 'RM 101', 1),
(6, 1, 'IT106', 'Ethics in Computing', 3, 1, 1, '2026-07-30 16:24:50', 2, 'Mon', '2:00 PM', 'RM 102', 1),
(7, 2, 'CS101', 'Programming Fundamentals', 3, 1, 1, '2026-07-30 16:24:50', 3, 'Mon', '8:00 AM', 'LAB 1', 1),
(8, 2, 'CS102', 'Data Structures', 3, 1, 1, '2026-07-30 16:24:50', 4, 'Tue', '10:00 AM', 'LAB 2', 1),
(9, 2, 'CS103', 'Discrete Mathematics', 3, 1, 1, '2026-07-30 16:24:50', 3, 'Wed', '1:00 PM', 'LAB 3', 1),
(10, 2, 'CS104', 'Algorithms', 3, 1, 1, '2026-07-30 16:24:50', 4, 'Thu', '3:00 PM', 'LAB 4', 1),
(11, 2, 'CS105', 'Computer Architecture', 3, 1, 1, '2026-07-30 16:24:50', 3, 'Fri', '9:00 AM', 'RM 101', 1),
(12, 2, 'CS106', 'Operating Systems', 3, 1, 1, '2026-07-30 16:24:50', 4, 'Mon', '2:00 PM', 'RM 102', 1),
(13, 3, 'IS101', 'Information Systems Fundamentals', 3, 1, 1, '2026-07-30 16:24:50', 5, 'Mon', '8:00 AM', 'LAB 1', 1),
(14, 3, 'IS102', 'Business Process Management', 3, 1, 1, '2026-07-30 16:24:50', 6, 'Tue', '10:00 AM', 'LAB 2', 1),
(15, 3, 'IS103', 'Database Management', 3, 1, 1, '2026-07-30 16:24:50', 5, 'Wed', '1:00 PM', 'LAB 3', 1),
(16, 3, 'IS104', 'Systems Analysis and Design', 3, 1, 1, '2026-07-30 16:24:50', 6, 'Thu', '3:00 PM', 'LAB 4', 1),
(17, 3, 'IS105', 'Enterprise Systems', 3, 1, 1, '2026-07-30 16:24:50', 5, 'Fri', '9:00 AM', 'RM 101', 1),
(18, 3, 'IS106', 'IT Governance', 3, 1, 1, '2026-07-30 16:24:50', 6, 'Mon', '2:00 PM', 'RM 102', 1),
(19, 5, 'BA101', 'Principles of Management', 3, 1, 1, '2026-07-30 16:24:50', 7, 'Mon', '8:00 AM', 'LAB 1', 1),
(20, 5, 'BA102', 'Marketing Management', 3, 1, 1, '2026-07-30 16:24:50', 8, 'Tue', '10:00 AM', 'LAB 2', 1),
(21, 5, 'BA103', 'Human Resource Management', 3, 1, 1, '2026-07-30 16:24:50', 7, 'Wed', '1:00 PM', 'LAB 3', 1),
(22, 5, 'BA104', 'Operations Management', 3, 1, 1, '2026-07-30 16:24:50', 8, 'Thu', '3:00 PM', 'LAB 4', 1),
(23, 5, 'BA105', 'Business Finance', 3, 1, 1, '2026-07-30 16:24:50', 7, 'Fri', '9:00 AM', 'RM 101', 1),
(24, 5, 'BA106', 'Entrepreneurship', 3, 1, 1, '2026-07-30 16:24:50', 8, 'Mon', '2:00 PM', 'RM 102', 1),
(25, 6, 'ACCT101', 'Financial Accounting', 3, 1, 1, '2026-07-30 16:24:50', 9, 'Mon', '8:00 AM', 'LAB 1', 1),
(26, 6, 'ACCT102', 'Managerial Accounting', 3, 1, 1, '2026-07-30 16:24:50', 10, 'Tue', '10:00 AM', 'LAB 2', 1),
(27, 6, 'ACCT103', 'Auditing Principles', 3, 1, 1, '2026-07-30 16:24:50', 9, 'Wed', '1:00 PM', 'LAB 3', 1),
(28, 6, 'ACCT104', 'Taxation', 3, 1, 1, '2026-07-30 16:24:50', 10, 'Thu', '3:00 PM', 'LAB 4', 1),
(29, 6, 'ACCT105', 'Accounting Information Systems', 3, 1, 1, '2026-07-30 16:24:50', 9, 'Fri', '9:00 AM', 'RM 101', 1),
(30, 6, 'ACCT106', 'Business Law', 3, 1, 1, '2026-07-30 16:24:50', 10, 'Mon', '2:00 PM', 'RM 102', 1),
(31, 7, 'HM101', 'Introduction to Hospitality', 3, 1, 1, '2026-07-30 16:24:50', 11, 'Mon', '8:00 AM', 'LAB 1', 1),
(32, 7, 'HM102', 'Food and Beverage Service', 3, 1, 1, '2026-07-30 16:24:50', 12, 'Tue', '10:00 AM', 'LAB 2', 1),
(33, 7, 'HM103', 'Housekeeping Operations', 3, 1, 1, '2026-07-30 16:24:50', 11, 'Wed', '1:00 PM', 'LAB 3', 1),
(34, 7, 'HM104', 'Front Office Management', 3, 1, 1, '2026-07-30 16:24:50', 12, 'Thu', '3:00 PM', 'LAB 4', 1),
(35, 7, 'HM105', 'Culinary Fundamentals', 3, 1, 1, '2026-07-30 16:24:50', 11, 'Fri', '9:00 AM', 'RM 101', 1),
(36, 7, 'HM106', 'Events Management', 3, 1, 1, '2026-07-30 16:24:50', 12, 'Mon', '2:00 PM', 'RM 102', 1),
(37, 8, 'TM101', 'Introduction to Tourism', 3, 1, 1, '2026-07-30 16:24:50', 13, 'Mon', '8:00 AM', 'LAB 1', 1),
(38, 8, 'TM102', 'Tour Operations', 3, 1, 1, '2026-07-30 16:24:50', 14, 'Tue', '10:00 AM', 'LAB 2', 1),
(39, 8, 'TM103', 'Travel Management', 3, 1, 1, '2026-07-30 16:24:50', 13, 'Wed', '1:00 PM', 'LAB 3', 1),
(40, 8, 'TM104', 'Tourism Planning', 3, 1, 1, '2026-07-30 16:24:50', 14, 'Thu', '3:00 PM', 'LAB 4', 1),
(41, 8, 'TM105', 'Cultural Heritage Tourism', 3, 1, 1, '2026-07-30 16:24:50', 13, 'Fri', '9:00 AM', 'RM 101', 1),
(42, 8, 'TM106', 'Sustainable Tourism', 3, 1, 1, '2026-07-30 16:24:50', 14, 'Mon', '2:00 PM', 'RM 102', 1),
(43, 9, 'CRIM101', 'Introduction to Criminology', 3, 1, 1, '2026-07-30 16:24:50', 15, 'Mon', '8:00 AM', 'LAB 1', 0),
(44, 9, 'CRIM102', 'Criminal Law', 3, 1, 1, '2026-07-30 16:24:50', 16, 'Tue', '10:00 AM', 'LAB 2', 1),
(45, 9, 'CRIM103', 'Forensic Science', 3, 1, 1, '2026-07-30 16:24:50', 15, 'Wed', '1:00 PM', 'LAB 3', 1),
(46, 9, 'CRIM104', 'Criminal Investigation', 3, 1, 1, '2026-07-30 16:24:50', 16, 'Thu', '3:00 PM', 'LAB 4', 1),
(47, 9, 'CRIM105', 'Police Organization', 3, 1, 1, '2026-07-30 16:24:50', 15, 'Fri', '9:00 AM', 'RM 101', 1),
(48, 9, 'CRIM106', 'Corrections', 3, 1, 1, '2026-07-30 16:24:50', 16, 'Mon', '2:00 PM', 'RM 102', 1),
(49, 10, 'NURS101', 'Fundamentals of Nursing', 3, 1, 1, '2026-07-30 16:24:50', 17, 'Mon', '8:00 AM', 'LAB 1', 1),
(50, 10, 'NURS102', 'Health Assessment', 3, 1, 1, '2026-07-30 16:24:50', 18, 'Tue', '10:00 AM', 'LAB 2', 1),
(51, 10, 'NURS103', 'Community Health Nursing', 3, 1, 1, '2026-07-30 16:24:50', 17, 'Wed', '1:00 PM', 'LAB 3', 1),
(52, 10, 'NURS104', 'Medical Surgical Nursing', 3, 1, 1, '2026-07-30 16:24:50', 18, 'Thu', '3:00 PM', 'LAB 4', 1),
(53, 10, 'NURS105', 'Pharmacology', 3, 1, 1, '2026-07-30 16:24:50', 17, 'Fri', '9:00 AM', 'RM 101', 1),
(54, 10, 'NURS106', 'Anatomy and Physiology', 3, 1, 1, '2026-07-30 16:24:50', 18, 'Mon', '2:00 PM', 'RM 102', 1),
(55, 15, 'CE101', 'Engineering Mechanics', 3, 1, 1, '2026-07-30 16:24:50', 19, 'Mon', '8:00 AM', 'LAB 1', 1),
(56, 15, 'CE102', 'Surveying', 3, 1, 1, '2026-07-30 16:24:50', 20, 'Tue', '10:00 AM', 'LAB 2', 1),
(57, 15, 'CE103', 'Construction Materials', 3, 1, 1, '2026-07-30 16:24:50', 19, 'Wed', '1:00 PM', 'LAB 3', 1),
(58, 15, 'CE104', 'Structural Theory', 3, 1, 1, '2026-07-30 16:24:50', 20, 'Thu', '3:00 PM', 'LAB 4', 1),
(59, 15, 'CE105', 'Hydraulics', 3, 1, 1, '2026-07-30 16:24:50', 19, 'Fri', '9:00 AM', 'RM 101', 1),
(60, 15, 'CE106', 'Construction Management', 3, 1, 1, '2026-07-30 16:24:50', 20, 'Mon', '2:00 PM', 'RM 102', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','cashier','student') DEFAULT 'student',
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `enrollment_details`
--
ALTER TABLE `enrollment_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `fk_students_section` (`section_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `enrollment_details`
--
ALTER TABLE `enrollment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `enrollment_details`
--
ALTER TABLE `enrollment_details`
  ADD CONSTRAINT `enrollment_details_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`),
  ADD CONSTRAINT `enrollment_details_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
