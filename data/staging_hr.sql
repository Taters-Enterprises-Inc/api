-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2023 at 10:53 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `staging_hr`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_items`
--

CREATE TABLE `action_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `status` int(11) UNSIGNED NOT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `action_items`
--

INSERT INTO `action_items` (`id`, `user_id`, `module_id`, `item_id`, `status`, `dateadded`, `dateupdated`) VALUES
(2, 1, 1, 1, 2, '2023-11-06 18:16:28', '2023-11-06 18:47:23'),
(3, 2, 1, 1, 3, '2023-11-06 18:16:28', '2023-11-07 06:59:02'),
(46, 3, 1, 2, 1, '2023-11-07 02:39:15', '2023-11-07 02:39:15'),
(50, 2, 1, 3, 1, '2023-11-07 06:59:02', '2023-11-07 06:59:03'),
(51, 4, 1, 1, 3, '2023-11-07 09:12:31', '2023-11-07 09:16:04'),
(52, 4, 1, 3, 1, '2023-11-07 09:16:04', '2023-11-07 09:16:05'),
(53, 5, 1, 1, 3, '2023-11-07 09:29:01', '2023-11-07 09:31:13'),
(54, 5, 1, 3, 1, '2023-11-07 09:31:13', '2023-11-07 09:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `action_item_status`
--

CREATE TABLE `action_item_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `action_item_status`
--

INSERT INTO `action_item_status` (`id`, `name`) VALUES
(1, 'Pending'),
(2, 'Completed'),
(3, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_kras_or_kpi`
--

CREATE TABLE `appraisal_kras_or_kpi` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `details` varchar(265) NOT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `action_item_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_kras_or_kpi`
--

INSERT INTO `appraisal_kras_or_kpi` (`id`, `user_id`, `details`, `dateadded`, `dateupdated`, `action_item_id`) VALUES
(43, 2, 'dsad', '2023-11-07 02:53:20', '2023-11-07 02:53:20', 3),
(44, 2, 'asd', '2023-11-07 02:53:20', '2023-11-07 02:53:20', 3),
(45, 2, 'asd', '2023-11-07 02:53:20', '2023-11-07 02:53:20', 3),
(46, 4, 'ab', '2023-11-07 09:12:52', '2023-11-07 09:12:52', 51),
(47, 4, 'cd', '2023-11-07 09:12:52', '2023-11-07 09:12:52', 51),
(48, 4, 'ef', '2023-11-07 09:12:52', '2023-11-07 09:12:52', 51),
(49, 5, 'adf', '2023-11-07 09:30:03', '2023-11-07 09:30:03', 53),
(50, 5, 'adf', '2023-11-07 09:30:03', '2023-11-07 09:30:03', 53),
(51, 5, 'asdf', '2023-11-07 09:30:03', '2023-11-07 09:30:03', 53);

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_responses`
--

CREATE TABLE `appraisal_responses` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL,
  `hash` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_responses`
--

INSERT INTO `appraisal_responses` (`id`, `user_id`, `dateadded`, `status`, `hash`) VALUES
(1, 1, '2023-11-06 15:40:09', 0, '650a903a8ee9c5b3a0d0'),
(2, 1, '2023-11-06 15:47:28', 0, '1afafb03e37411d05d42'),
(3, 1, '2023-11-06 15:47:30', 0, 'c5c047f790e1da3b6396'),
(4, 1, '2023-11-06 15:48:23', 0, '721d7f2a7be616d0d062'),
(5, 1, '2023-11-06 15:48:35', 0, '7068144dde55fb247374'),
(6, 1, '2023-11-06 15:53:33', 0, 'b9481559255d42c273f4'),
(7, 1, '2023-11-06 15:57:37', 0, 'b8949985de519eaf94d9'),
(8, 1, '2023-11-06 15:59:46', 0, '92cde808f1f9a582efdb'),
(9, 1, '2023-11-06 15:59:48', 0, 'dece1721aefb684f63bf'),
(10, 1, '2023-11-06 16:01:04', 0, '18767234b41f7863eac9'),
(11, 1, '2023-11-06 16:01:33', 0, 'd50327e6647298754b1a'),
(12, 1, '2023-11-06 16:02:37', 0, 'c0aae69cce9e14fc6c4b'),
(13, 1, '2023-11-06 16:05:24', 0, '24a67c10ba4aa8043a08'),
(14, 5, '2023-11-07 09:34:07', 0, 'ebc4f0cee9dd139ebeca'),
(15, 5, '2023-11-07 09:34:44', 0, '12c1664e7bca82c82357'),
(16, 5, '2023-11-07 09:37:54', 0, 'f399eb74c6fd2c012e0f'),
(17, 2, '2023-11-07 09:46:42', 0, 'e1ac1d06258d550cd4a9');

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_response_comments`
--

CREATE TABLE `appraisal_response_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `appraisal_response_id` int(10) UNSIGNED NOT NULL,
  `key_strengths` varchar(265) NOT NULL,
  `areas_for_development` varchar(265) NOT NULL,
  `major_development_plans_for_next_year` varchar(265) NOT NULL,
  `comments_on_your_overall_performance_and_development_plan` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_response_comments`
--

INSERT INTO `appraisal_response_comments` (`id`, `appraisal_response_id`, `key_strengths`, `areas_for_development`, `major_development_plans_for_next_year`, `comments_on_your_overall_performance_and_development_plan`) VALUES
(1, 13, 'asd', 'asd', 'asd', 'asd'),
(2, 14, 'dsad', 'asd', 'asd', 'asd'),
(3, 15, 'dsad', 'asd', 'asd', 'asd'),
(4, 16, 'asd', 'asd', 'asd', 'asd'),
(5, 17, 'asd', 'asd', 'asd', 'asdasdas');

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_response_core_competency_grades`
--

CREATE TABLE `appraisal_response_core_competency_grades` (
  `id` int(10) UNSIGNED NOT NULL,
  `appraisal_response_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_response_core_competency_grades`
--

INSERT INTO `appraisal_response_core_competency_grades` (`id`, `appraisal_response_id`) VALUES
(1, 6),
(2, 7),
(3, 8),
(4, 9),
(5, 10),
(6, 11),
(7, 12),
(8, 13),
(9, 14),
(10, 15),
(11, 16),
(12, 17);

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_response_core_competency_grade_answers`
--

CREATE TABLE `appraisal_response_core_competency_grade_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `appraisal_response_core_competency_grade_id` int(10) UNSIGNED NOT NULL,
  `core_competency_grade_id` int(10) UNSIGNED NOT NULL,
  `rating` decimal(10,2) NOT NULL,
  `critical_incidents_or_comments` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_response_core_competency_grade_answers`
--

INSERT INTO `appraisal_response_core_competency_grade_answers` (`id`, `appraisal_response_core_competency_grade_id`, `core_competency_grade_id`, `rating`, `critical_incidents_or_comments`) VALUES
(1, 1, 1, '5.00', 'asd'),
(2, 1, 2, '5.00', 'asd'),
(3, 1, 3, '5.00', 'asd'),
(4, 1, 4, '5.00', 'asd'),
(5, 1, 5, '5.00', 'asd'),
(6, 1, 6, '5.00', 'asd'),
(7, 1, 7, '5.00', 'sad'),
(8, 2, 1, '5.00', 'asd'),
(9, 2, 2, '5.00', 'asd'),
(10, 2, 3, '5.00', 'asd'),
(11, 2, 4, '5.00', 'asd'),
(12, 2, 5, '5.00', 'asd'),
(13, 2, 6, '5.00', 'asd'),
(14, 2, 7, '5.00', 'sad'),
(15, 3, 1, '5.00', 'asd'),
(16, 3, 2, '5.00', 'asd'),
(17, 3, 3, '5.00', 'asd'),
(18, 3, 4, '5.00', 'asd'),
(19, 3, 5, '5.00', 'asd'),
(20, 3, 6, '5.00', 'asd'),
(21, 3, 7, '5.00', 'sad'),
(22, 4, 1, '5.00', 'asd'),
(23, 4, 2, '5.00', 'asd'),
(24, 4, 3, '5.00', 'asd'),
(25, 4, 4, '5.00', 'asd'),
(26, 4, 5, '5.00', 'asd'),
(27, 4, 6, '5.00', 'asd'),
(28, 4, 7, '5.00', 'sad'),
(29, 5, 1, '5.00', 'asd'),
(30, 5, 2, '5.00', 'asd'),
(31, 5, 3, '5.00', 'asd'),
(32, 5, 4, '5.00', 'asd'),
(33, 5, 5, '5.00', 'asd'),
(34, 5, 6, '5.00', 'asd'),
(35, 5, 7, '5.00', 'sad'),
(36, 6, 1, '5.00', 'asd'),
(37, 6, 2, '5.00', 'asd'),
(38, 6, 3, '5.00', 'asd'),
(39, 6, 4, '5.00', 'asd'),
(40, 6, 5, '5.00', 'asd'),
(41, 6, 6, '5.00', 'asd'),
(42, 6, 7, '5.00', 'sad'),
(43, 7, 1, '5.00', 'asd'),
(44, 7, 2, '5.00', 'asd'),
(45, 7, 3, '5.00', 'asd'),
(46, 7, 4, '5.00', 'asd'),
(47, 7, 5, '5.00', 'asd'),
(48, 7, 6, '5.00', 'asd'),
(49, 7, 7, '5.00', 'sad'),
(50, 8, 1, '5.00', 'asd'),
(51, 8, 2, '5.00', 'asd'),
(52, 8, 3, '5.00', 'asd'),
(53, 8, 4, '5.00', 'asd'),
(54, 8, 5, '5.00', 'asd'),
(55, 8, 6, '5.00', 'asd'),
(56, 8, 7, '5.00', 'sad'),
(57, 9, 1, '4.00', 'asd'),
(58, 9, 2, '4.00', 'asd'),
(59, 9, 3, '4.00', 'asd'),
(60, 9, 4, '4.00', 'asd'),
(61, 9, 5, '4.00', 'asd'),
(62, 9, 6, '4.00', 'asd'),
(63, 9, 7, '4.00', 'asd'),
(64, 10, 1, '4.00', 'asd'),
(65, 10, 2, '4.00', 'asd'),
(66, 10, 3, '4.00', 'asd'),
(67, 10, 4, '4.00', 'asd'),
(68, 10, 5, '4.00', 'asd'),
(69, 10, 6, '4.00', 'asd'),
(70, 10, 7, '4.00', 'asd'),
(71, 11, 1, '4.00', 'aasd'),
(72, 11, 2, '4.00', 'asd'),
(73, 11, 3, '4.00', 'asd'),
(74, 11, 4, '4.00', 'asd'),
(75, 11, 5, '4.00', 'asd'),
(76, 11, 6, '4.00', 'asd'),
(77, 11, 7, '4.00', 'asdasd'),
(78, 12, 1, '4.00', 'asd'),
(79, 12, 2, '4.00', 'asd'),
(80, 12, 3, '4.00', 'asd'),
(81, 12, 4, '44.00', 'asd'),
(82, 12, 5, '4.00', 'asd'),
(83, 12, 6, '4.00', 'asd'),
(84, 12, 7, '4.00', 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_response_functional_competency_grades`
--

CREATE TABLE `appraisal_response_functional_competency_grades` (
  `id` int(10) UNSIGNED NOT NULL,
  `appraisal_response_id` int(10) UNSIGNED NOT NULL,
  `absences` decimal(10,2) NOT NULL,
  `tardiness` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_response_functional_competency_grades`
--

INSERT INTO `appraisal_response_functional_competency_grades` (`id`, `appraisal_response_id`, `absences`, `tardiness`) VALUES
(1, 11, '4.00', '4.00'),
(2, 12, '4.00', '4.00'),
(3, 13, '4.00', '4.00'),
(4, 14, '4.00', '4.00'),
(5, 15, '4.00', '4.00'),
(6, 16, '4.00', '4.00'),
(7, 17, '4.00', '4.00');

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_response_functional_competency_grade_answers`
--

CREATE TABLE `appraisal_response_functional_competency_grade_answers` (
  `id` int(11) NOT NULL,
  `appraisal_response_functional_competency_and_punctuality_grade_i` int(10) UNSIGNED NOT NULL,
  `functional_competency_and_punctuality_grade_id` int(10) UNSIGNED NOT NULL,
  `rating` decimal(10,2) NOT NULL,
  `critical_incidents_or_comments` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_response_functional_competency_grade_answers`
--

INSERT INTO `appraisal_response_functional_competency_grade_answers` (`id`, `appraisal_response_functional_competency_and_punctuality_grade_i`, `functional_competency_and_punctuality_grade_id`, `rating`, `critical_incidents_or_comments`) VALUES
(1, 2, 1, '5.00', 'sad'),
(2, 2, 2, '5.00', 'asd'),
(3, 2, 3, '5.00', 'asd'),
(4, 2, 4, '5.00', 'asd'),
(5, 2, 5, '5.00', 'asd'),
(6, 2, 6, '5.00', 'asd'),
(7, 2, 7, '5.00', 'asd'),
(8, 2, 8, '5.00', 'sad'),
(9, 3, 1, '5.00', 'sad'),
(10, 3, 2, '5.00', 'asd'),
(11, 3, 3, '5.00', 'asd'),
(12, 3, 4, '5.00', 'asd'),
(13, 3, 5, '5.00', 'asd'),
(14, 3, 6, '5.00', 'asd'),
(15, 3, 7, '5.00', 'asd'),
(16, 3, 8, '5.00', 'sad'),
(17, 4, 1, '4.00', 'asd'),
(18, 4, 2, '4.00', 'sad'),
(19, 4, 3, '4.00', 'asd'),
(20, 4, 4, '4.00', 'asd'),
(21, 4, 5, '4.00', 'asd'),
(22, 4, 6, '4.00', 'asd'),
(23, 4, 7, '4.00', 'asd'),
(24, 4, 8, '4.00', 'asd'),
(25, 5, 1, '4.00', 'asd'),
(26, 5, 2, '4.00', 'sad'),
(27, 5, 3, '4.00', 'asd'),
(28, 5, 4, '4.00', 'asd'),
(29, 5, 5, '4.00', 'asd'),
(30, 5, 6, '4.00', 'asd'),
(31, 5, 7, '4.00', 'asd'),
(32, 5, 8, '4.00', 'asd'),
(33, 6, 1, '4.00', 'asd'),
(34, 6, 2, '4.00', 'asd'),
(35, 6, 3, '4.00', 'asd'),
(36, 6, 4, '4.00', 'asd'),
(37, 6, 5, '4.00', 'sad'),
(38, 6, 6, '4.00', 'asd'),
(39, 6, 7, '4.00', 'asd'),
(40, 6, 8, '4.00', 'asd'),
(41, 7, 1, '4.00', 'asdsad'),
(42, 7, 2, '4.00', 'asd'),
(43, 7, 3, '4.00', 'asd'),
(44, 7, 4, '4.00', 'sad'),
(45, 7, 5, '4.00', 'asd'),
(46, 7, 6, '4.00', 'asd'),
(47, 7, 7, '4.00', 'asd'),
(48, 7, 8, '4.00', 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_response_kra_or_kpi_grades`
--

CREATE TABLE `appraisal_response_kra_or_kpi_grades` (
  `id` int(10) UNSIGNED NOT NULL,
  `appraisal_response_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_response_kra_or_kpi_grades`
--

INSERT INTO `appraisal_response_kra_or_kpi_grades` (`id`, `appraisal_response_id`) VALUES
(1, 4),
(2, 5),
(3, 6),
(4, 7),
(5, 8),
(6, 9),
(7, 10),
(8, 11),
(9, 12),
(10, 13),
(11, 14),
(12, 15),
(13, 16),
(14, 17);

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_response_kra_or_kpi_grade_answers`
--

CREATE TABLE `appraisal_response_kra_or_kpi_grade_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `appraisal_response_kra_or_kpi_grade_id` int(10) UNSIGNED NOT NULL,
  `kra_kpi_grade_id` int(10) UNSIGNED NOT NULL,
  `key_result_areas_or_key_performance_indicators` varchar(265) NOT NULL,
  `result_achieved_or_not_achieved` varchar(265) NOT NULL,
  `rating` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appraisal_response_kra_or_kpi_grade_answers`
--

INSERT INTO `appraisal_response_kra_or_kpi_grade_answers` (`id`, `appraisal_response_kra_or_kpi_grade_id`, `kra_kpi_grade_id`, `key_result_areas_or_key_performance_indicators`, `result_achieved_or_not_achieved`, `rating`) VALUES
(1, 2, 1, 'asd', 'asd', '5.00'),
(2, 2, 2, 'asd', 'asd', '5.00'),
(3, 2, 3, 'asd', 'asd', '5.00'),
(4, 3, 1, 'asd', 'asd', '5.00'),
(5, 3, 2, 'asd', 'asd', '5.00'),
(6, 3, 3, 'asd', 'asd', '5.00'),
(7, 4, 1, 'asd', 'asd', '5.00'),
(8, 4, 2, 'asd', 'asd', '5.00'),
(9, 4, 3, 'asd', 'asd', '5.00'),
(10, 5, 1, 'asd', 'asd', '5.00'),
(11, 5, 2, 'asd', 'asd', '5.00'),
(12, 5, 3, 'asd', 'asd', '5.00'),
(13, 6, 1, 'asd', 'asd', '5.00'),
(14, 6, 2, 'asd', 'asd', '5.00'),
(15, 6, 3, 'asd', 'asd', '5.00'),
(16, 7, 1, 'asd', 'asd', '5.00'),
(17, 7, 2, 'asd', 'asd', '5.00'),
(18, 7, 3, 'asd', 'asd', '5.00'),
(19, 8, 1, 'asd', 'asd', '5.00'),
(20, 8, 2, 'asd', 'asd', '5.00'),
(21, 8, 3, 'asd', 'asd', '5.00'),
(22, 9, 1, 'asd', 'asd', '5.00'),
(23, 9, 2, 'asd', 'asd', '5.00'),
(24, 9, 3, 'asd', 'asd', '5.00'),
(25, 10, 1, 'asd', 'asd', '5.00'),
(26, 10, 2, 'asd', 'asd', '5.00'),
(27, 10, 3, 'asd', 'asd', '5.00'),
(28, 11, 1, 'sad', 'sad', '4.00'),
(29, 11, 2, 'sad', 'asd', '4.00'),
(30, 11, 3, 'sad', 'asd', '4.00'),
(31, 12, 1, 'sad', 'sad', '4.00'),
(32, 12, 2, 'sad', 'asd', '4.00'),
(33, 12, 3, 'sad', 'asd', '4.00'),
(34, 13, 1, 'asd', '4', '4.00'),
(35, 13, 2, 'asd', '4', '4.00'),
(36, 13, 3, 'asd', '4', '4.00'),
(37, 14, 1, 'asd', 'asd', '4.00'),
(38, 14, 2, 'asd', 'asd', '4.00'),
(39, 14, 3, 'asd', 'asd', '4.00');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_and_punctuality`
--

CREATE TABLE `attendance_and_punctuality` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `absences` varchar(30) DEFAULT NULL,
  `tardiness` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attendance_and_punctuality`
--

INSERT INTO `attendance_and_punctuality` (`id`, `name`, `absences`, `tardiness`) VALUES
(1, 'Excellent (5)', 'No abesences for the period', 'No tardiness'),
(2, 'Very Satisfactory (4)', '1 day per period', '1 - 2 instances of tardiness per period'),
(3, 'Satisfactory (3)', '2 days per period', '3 - 5 instances of tardiness per period'),
(4, 'Fair (2)', '3 days per period', '6 - 12 instances of tardiness per period'),
(5, 'Poor (1)', 'More than 3 days per period', 'More than 12 instances of tardiness per period');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`) VALUES
(1, 'Taters Enterprises Inc.');

-- --------------------------------------------------------

--
-- Table structure for table `core_competency_grade`
--

CREATE TABLE `core_competency_grade` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(30) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `core_competency_grade`
--

INSERT INTO `core_competency_grade` (`id`, `title`, `description`) VALUES
(1, 'Honesty', 'Uprightness of character and action. Has a strong inclination to tollie, steal, or deceive in any way. An active disposition regard for the standards of one\'s profession, calling, or ition.'),
(2, 'Excellence', 'Being good at a good thing. Has the ability to strive to be the best one can be and to do the best that one can do.'),
(3, 'Responsiblity', 'Understands and knows the obligations of his/her duties and able to perform the duties or tasks assigned.'),
(4, 'Respect', 'Ability to show appreciation for someone\'s traits or qualities and treating people with dignity and gratitude. Able to abide with the company\'s Code of Conduct.'),
(5, 'Ownership', 'Displays proactive, solution-oriented, accountable, transparent and commitment in the performance of his/her duties. Displays the ability to take on challenges.'),
(6, 'Integrity', 'Possessing upstanding character traits, work ethics, sound judgement, and loyalty. Consistently doing the right thing through words and actions.'),
(7, 'Customer 1st', 'Consistently seeking ways to deliver a positive Customer experience by designing and delivering services with the Customer in mind. Able to go above and beyond to deliver exceptional service.');

-- --------------------------------------------------------

--
-- Table structure for table `functional_competency_and_punctuality_grade`
--

CREATE TABLE `functional_competency_and_punctuality_grade` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(30) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `functional_competency_and_punctuality_grade`
--

INSERT INTO `functional_competency_and_punctuality_grade` (`id`, `title`, `description`) VALUES
(1, 'Analytical Thinking', 'Ability of a process thinker to recognize and own a problem, implement a temporary fix, gather and analyze data, identify root causes, develop plausible alternatives from which to select the best solution/s; uses established tools and techniques, evaluates results, then| standardizes process for continuous improvement.'),
(2, 'Collaborative Partnering', 'Ability to build, maintain, develop, and ! utilize collaborative alliances or relationships with business partners within and outside the company in order to gain support for each other and achieve mutually beneficial goals.'),
(3, 'Communication', 'Expresses oneself accurately, receiving and transmitting information through an effective and organized manner, conveying information clearly and concisely, ensuring clarity and commons understanding using various communication media'),
(4, 'Creativity', 'Cultivating innovative mind set, generating new ideas, inciting enthusiastic interest, influencing positive participation through -of-the-box thinking to yield value for TEI stakeholders.'),
(5, 'Problem Solving', 'Ability of a process thinker to recognize and own a problem, implement a temporary fix, gather and analyze data, identify causes, develop plausible alternatives from which to select the best jution/s; uses established tools and techniques, evaluates results, then standardizes process for continuous improvement.'),
(6, 'Reporting', 'The ability to gather, process, analyze, and share data, ensuring that the protocols for reporting are observed or complied with in a timely manner.'),
(7, 'Technical Expertise', 'Excels in the technical/functional skill and professionals knowledge required of the job position being occupied (computer skill marketing skills, human resource management skills, c.) and continuously develops himself/herself in that field of expertise'),
(8, 'Work Management', 'Managing the various aspects of one’s duties and functions by being keen on the minute details, applying appropriate knowledge, skills, and experiences; monitoring one’s own progress and valuating the work to check adherence to work systems and processes 0 achieve an accurate, complete, consistent, and timely output.');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'manager', 'Manager'),
(3, 'staff', 'Staff');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`) VALUES
(1, 'Submit KRA'),
(2, 'Review and Approve KRA'),
(3, 'Submit Self Assessment');

-- --------------------------------------------------------

--
-- Table structure for table `kra_kpi_grade`
--

CREATE TABLE `kra_kpi_grade` (
  `id` int(11) UNSIGNED NOT NULL,
  `weight` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kra_kpi_grade`
--

INSERT INTO `kra_kpi_grade` (`id`, `weight`) VALUES
(1, '0.33'),
(2, '0.33'),
(3, '0.33');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip_address`, `login`, `time`) VALUES
(7, '127.0.0.1', 'jerico.villaraza@tatersgroup.com', 1699322462),
(8, '127.0.0.1', 'jerico.villaraza@tatersgroup.com', 1699322464),
(9, '127.0.0.1', 'jerico.villaraza@tatersgroup.com', 1699322467),
(10, '127.0.0.1', 'allan.solis@tatersgroup.com', 1699349439),
(11, '127.0.0.1', 'jerico.villaraza@tateresgr.com', 1699350303),
(12, '127.0.0.1', 'jerico.villaraza@tateresgr.com', 1699350304);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`) VALUES
(1, 'Appraisal');

-- --------------------------------------------------------

--
-- Table structure for table `performance_criteria`
--

CREATE TABLE `performance_criteria` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `minimum_score` decimal(10,2) NOT NULL,
  `maximum_score` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `performance_criteria`
--

INSERT INTO `performance_criteria` (`id`, `name`, `minimum_score`, `maximum_score`) VALUES
(1, 'Exceptional <br/> Performance', '4.90', '5.00'),
(2, 'Above Expectations', '4.00', '4.89'),
(3, 'Reliable <br/> Performance', '3.00', '3.99'),
(4, 'Improvement <br/> Needed', '2.00', '2.99'),
(5, 'Unsatisfactory', '1.00', '1.99');

-- --------------------------------------------------------

--
-- Table structure for table `rating_scale`
--

CREATE TABLE `rating_scale` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rating_scale`
--

INSERT INTO `rating_scale` (`id`, `name`, `description`, `rate`) VALUES
(1, 'Exceptional', 'A role model, mentor or leader that embodies values & exhibits outstanding behaviors, skills, and expertise.', 5),
(2, 'Highly Effective', 'Generally demonstartes competencies at levels exceeding expectations for their position.', 4),
(3, 'Effective', 'Achieves performance and desmostrates competencies at a level consistent with their position.', 3),
(4, 'Needs Improvement', 'Sometimes meets expectations or goals', 2),
(5, 'Needs Coaching', 'Regularly demonstrates behaviors incosistent with competencies and values.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`) VALUES
(1, '127.0.0.1', 'administrator', '$2y$08$200Z6ZZbp3RAEXoaWcMA6uJOFicwNZaqk4oDhqTUiFXFe63MG.Daa', 'admin@admin.com', NULL, '', NULL, NULL, NULL, NULL, NULL, 1268889823, 1699283384, 1),
(2, '127.0.0.1', 'jerico.villaraza@tatersgr.com', '$2y$10$tMuJIB.uG3SdSH1UcNx.P.Kd7Zb5GTW254sKloRMv1EszNj24KVby', 'jerico.villaraza@tatersgr.com', NULL, '', NULL, NULL, NULL, NULL, NULL, 1268889823, 1699350310, 1),
(3, '127.0.0.1', 'allan.solis@tatersgr.com', '$2y$10$nz2Zqbw6Mh9q0jQkYMZ0betPTfGuEQXNCYknBZe2zgbfq7oMnv5TO', 'allan.solis@tatersgr.com', NULL, '', NULL, NULL, NULL, NULL, NULL, 1268889823, 1699349448, 1),
(4, '127.0.0.1', 'mike.aquino@tatersgr.com', '$2y$10$tMuJIB.uG3SdSH1UcNx.P.Kd7Zb5GTW254sKloRMv1EszNj24KVby', 'mike.aquino@tatersgr.com', NULL, '', NULL, NULL, NULL, NULL, NULL, 1268889823, 1699348361, 1),
(5, '127.0.0.1', 'ken@tatersgroup.com', '$2y$10$tMuJIB.uG3SdSH1UcNx.P.Kd7Zb5GTW254sKloRMv1EszNj24KVby', 'ken@tatersgroup.com', NULL, '', NULL, NULL, NULL, NULL, NULL, 1268889823, 1699349488, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1),
(2, 2, 3),
(3, 3, 2),
(4, 4, 3),
(5, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_companies`
--

CREATE TABLE `user_companies` (
  `id` int(11) UNSIGNED NOT NULL,
  `company_id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_companies`
--

INSERT INTO `user_companies` (`id`, `company_id`, `user_id`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 1, 4),
(4, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `user_direct_reports`
--

CREATE TABLE `user_direct_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `direct_user_id` int(10) UNSIGNED NOT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_direct_reports`
--

INSERT INTO `user_direct_reports` (`id`, `user_id`, `direct_user_id`, `dateadded`, `dateupdated`) VALUES
(1, 2, 3, '2023-11-07 01:53:20', '2023-11-07 01:53:20'),
(2, 4, 3, '2023-11-07 01:53:20', '2023-11-07 01:53:20'),
(3, 5, 3, '2023-11-07 01:53:20', '2023-11-07 01:53:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(265) NOT NULL,
  `last_name` varchar(265) NOT NULL,
  `designation` varchar(265) NOT NULL,
  `email` varchar(256) NOT NULL,
  `phone_number` varchar(256) NOT NULL,
  `user_status_id` int(11) UNSIGNED DEFAULT NULL,
  `position` varchar(265) NOT NULL,
  `employee_number` varchar(265) NOT NULL,
  `date_hired` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `user_id`, `first_name`, `last_name`, `designation`, `email`, `phone_number`, `user_status_id`, `position`, `employee_number`, `date_hired`) VALUES
(1, 1, 'Administrator', '', 'MIS Department', 'admin@admin.com', '09084741500', 2, '', '', NULL),
(2, 2, 'Jerico', 'Villaraza', 'MIS Department', 'jerico.villaraza@tatersgr.com', '09686097100', 2, 'Software Developer', 'T0001', '2023-11-07 09:48:43'),
(4, 3, 'Allan', 'Solis', 'MIS Department', 'allan.solis@tatersgr.com', '09084741500', 2, 'MIS Manager', 'T0002', '2023-11-07 09:48:43'),
(5, 4, 'Mike', 'Aquino', 'MIS Department', 'mike.aquino@tatersgr.com', '09686097100', 2, 'Software Developer', 'T0003', '2023-11-07 09:48:43'),
(6, 5, 'Ken', 'Orcana', 'MIS Department', 'ken@tatersgr.com', '09686097100', 2, 'Software Developer', 'T0004', '2023-11-07 09:48:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_status`
--

CREATE TABLE `user_status` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_status`
--

INSERT INTO `user_status` (`id`, `name`) VALUES
(1, 'New'),
(2, 'Verified'),
(3, 'Rejected');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_items`
--
ALTER TABLE `action_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `action_item_status`
--
ALTER TABLE `action_item_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appraisal_kras_or_kpi`
--
ALTER TABLE `appraisal_kras_or_kpi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_item_id` (`action_item_id`);

--
-- Indexes for table `appraisal_responses`
--
ALTER TABLE `appraisal_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `appraisal_response_comments`
--
ALTER TABLE `appraisal_response_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appraisal_response_id` (`appraisal_response_id`);

--
-- Indexes for table `appraisal_response_core_competency_grades`
--
ALTER TABLE `appraisal_response_core_competency_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appraisal_response_id` (`appraisal_response_id`);

--
-- Indexes for table `appraisal_response_core_competency_grade_answers`
--
ALTER TABLE `appraisal_response_core_competency_grade_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appraisal_response_core_competency_grade_id` (`appraisal_response_core_competency_grade_id`),
  ADD KEY `core_competency_grade_id` (`core_competency_grade_id`);

--
-- Indexes for table `appraisal_response_functional_competency_grades`
--
ALTER TABLE `appraisal_response_functional_competency_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appraisal_response_id` (`appraisal_response_id`);

--
-- Indexes for table `appraisal_response_functional_competency_grade_answers`
--
ALTER TABLE `appraisal_response_functional_competency_grade_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appraisal_response_functional_competency_and_punctuality_grade_i` (`appraisal_response_functional_competency_and_punctuality_grade_i`),
  ADD KEY `functional_competency_and_punctuality_grade_id` (`functional_competency_and_punctuality_grade_id`);

--
-- Indexes for table `appraisal_response_kra_or_kpi_grades`
--
ALTER TABLE `appraisal_response_kra_or_kpi_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appraisal_response_id` (`appraisal_response_id`);

--
-- Indexes for table `appraisal_response_kra_or_kpi_grade_answers`
--
ALTER TABLE `appraisal_response_kra_or_kpi_grade_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appraisal_response_kra_or_kpi_grade_id` (`appraisal_response_kra_or_kpi_grade_id`),
  ADD KEY `kra_kpi_grade_id` (`kra_kpi_grade_id`);

--
-- Indexes for table `attendance_and_punctuality`
--
ALTER TABLE `attendance_and_punctuality`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_competency_grade`
--
ALTER TABLE `core_competency_grade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `functional_competency_and_punctuality_grade`
--
ALTER TABLE `functional_competency_and_punctuality_grade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kra_kpi_grade`
--
ALTER TABLE `kra_kpi_grade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performance_criteria`
--
ALTER TABLE `performance_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_scale`
--
ALTER TABLE `rating_scale`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_email` (`email`),
  ADD UNIQUE KEY `uc_activation_selector` (`activation_selector`),
  ADD UNIQUE KEY `uc_forgotten_password_selector` (`forgotten_password_selector`),
  ADD UNIQUE KEY `uc_remember_selector` (`remember_selector`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  ADD KEY `fk_users_groups_users1_idx` (`user_id`),
  ADD KEY `fk_users_groups_groups1_idx` (`group_id`);

--
-- Indexes for table `user_companies`
--
ALTER TABLE `user_companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_direct_reports`
--
ALTER TABLE `user_direct_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `direct_user_id` (`direct_user_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `user_profile_ibfk_3` (`user_status_id`);

--
-- Indexes for table `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_items`
--
ALTER TABLE `action_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `action_item_status`
--
ALTER TABLE `action_item_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `appraisal_kras_or_kpi`
--
ALTER TABLE `appraisal_kras_or_kpi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `appraisal_responses`
--
ALTER TABLE `appraisal_responses`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `appraisal_response_comments`
--
ALTER TABLE `appraisal_response_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `appraisal_response_core_competency_grades`
--
ALTER TABLE `appraisal_response_core_competency_grades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `appraisal_response_core_competency_grade_answers`
--
ALTER TABLE `appraisal_response_core_competency_grade_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `appraisal_response_functional_competency_grades`
--
ALTER TABLE `appraisal_response_functional_competency_grades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `appraisal_response_functional_competency_grade_answers`
--
ALTER TABLE `appraisal_response_functional_competency_grade_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `appraisal_response_kra_or_kpi_grades`
--
ALTER TABLE `appraisal_response_kra_or_kpi_grades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `appraisal_response_kra_or_kpi_grade_answers`
--
ALTER TABLE `appraisal_response_kra_or_kpi_grade_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `attendance_and_punctuality`
--
ALTER TABLE `attendance_and_punctuality`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `core_competency_grade`
--
ALTER TABLE `core_competency_grade`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `functional_competency_and_punctuality_grade`
--
ALTER TABLE `functional_competency_and_punctuality_grade`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kra_kpi_grade`
--
ALTER TABLE `kra_kpi_grade`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `performance_criteria`
--
ALTER TABLE `performance_criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rating_scale`
--
ALTER TABLE `rating_scale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_companies`
--
ALTER TABLE `user_companies`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_direct_reports`
--
ALTER TABLE `user_direct_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_status`
--
ALTER TABLE `user_status`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `action_items`
--
ALTER TABLE `action_items`
  ADD CONSTRAINT `action_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `action_items_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `action_items_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `action_items_ibfk_4` FOREIGN KEY (`status`) REFERENCES `action_item_status` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `appraisal_kras_or_kpi`
--
ALTER TABLE `appraisal_kras_or_kpi`
  ADD CONSTRAINT `appraisal_kras_or_kpi_ibfk_1` FOREIGN KEY (`action_item_id`) REFERENCES `action_items` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `appraisal_responses`
--
ALTER TABLE `appraisal_responses`
  ADD CONSTRAINT `appraisal_responses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `appraisal_response_comments`
--
ALTER TABLE `appraisal_response_comments`
  ADD CONSTRAINT `appraisal_response_comments_ibfk_1` FOREIGN KEY (`appraisal_response_id`) REFERENCES `appraisal_responses` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `appraisal_response_core_competency_grades`
--
ALTER TABLE `appraisal_response_core_competency_grades`
  ADD CONSTRAINT `appraisal_response_core_competency_grades_ibfk_1` FOREIGN KEY (`appraisal_response_id`) REFERENCES `appraisal_responses` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `appraisal_response_core_competency_grade_answers`
--
ALTER TABLE `appraisal_response_core_competency_grade_answers`
  ADD CONSTRAINT `appraisal_response_core_competency_grade_answers_ibfk_1` FOREIGN KEY (`appraisal_response_core_competency_grade_id`) REFERENCES `appraisal_response_core_competency_grades` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `appraisal_response_core_competency_grade_answers_ibfk_2` FOREIGN KEY (`core_competency_grade_id`) REFERENCES `core_competency_grade` (`id`);

--
-- Constraints for table `appraisal_response_functional_competency_grades`
--
ALTER TABLE `appraisal_response_functional_competency_grades`
  ADD CONSTRAINT `appraisal_response_functional_competency_grades_ibfk_1` FOREIGN KEY (`appraisal_response_id`) REFERENCES `appraisal_responses` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `appraisal_response_functional_competency_grade_answers`
--
ALTER TABLE `appraisal_response_functional_competency_grade_answers`
  ADD CONSTRAINT `appraisal_response_functional_competency_grade_answers_ibfk_1` FOREIGN KEY (`appraisal_response_functional_competency_and_punctuality_grade_i`) REFERENCES `appraisal_response_functional_competency_grades` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `appraisal_response_functional_competency_grade_answers_ibfk_2` FOREIGN KEY (`functional_competency_and_punctuality_grade_id`) REFERENCES `functional_competency_and_punctuality_grade` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `appraisal_response_kra_or_kpi_grades`
--
ALTER TABLE `appraisal_response_kra_or_kpi_grades`
  ADD CONSTRAINT `appraisal_response_kra_or_kpi_grades_ibfk_1` FOREIGN KEY (`appraisal_response_id`) REFERENCES `appraisal_responses` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `appraisal_response_kra_or_kpi_grade_answers`
--
ALTER TABLE `appraisal_response_kra_or_kpi_grade_answers`
  ADD CONSTRAINT `appraisal_response_kra_or_kpi_grade_answers_ibfk_1` FOREIGN KEY (`appraisal_response_kra_or_kpi_grade_id`) REFERENCES `appraisal_response_kra_or_kpi_grades` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `appraisal_response_kra_or_kpi_grade_answers_ibfk_2` FOREIGN KEY (`kra_kpi_grade_id`) REFERENCES `kra_kpi_grade` (`id`);

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `users_groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `users_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `user_companies`
--
ALTER TABLE `user_companies`
  ADD CONSTRAINT `user_companies_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_companies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `user_direct_reports`
--
ALTER TABLE `user_direct_reports`
  ADD CONSTRAINT `user_direct_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_direct_reports_ibfk_2` FOREIGN KEY (`direct_user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_profile_ibfk_2` FOREIGN KEY (`user_status_id`) REFERENCES `user_status` (`id`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
