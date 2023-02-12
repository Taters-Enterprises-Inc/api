-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2023 at 04:15 PM
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
-- Database: `staging_balance_score_card`
--

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
-- Table structure for table `customer_survey_responses`
--

CREATE TABLE `customer_survey_responses` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `fb_user_id` int(10) UNSIGNED DEFAULT NULL,
  `mobile_user_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `catering_transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_survey_response_order_type_id` int(10) UNSIGNED NOT NULL,
  `order_date` datetime NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL,
  `hash` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customer_survey_response_answers`
--

CREATE TABLE `customer_survey_response_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `survey_question_id` int(10) UNSIGNED NOT NULL,
  `survey_question_answer_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_survey_response_id` int(10) UNSIGNED NOT NULL,
  `text` varchar(265) DEFAULT NULL,
  `others` varchar(265) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customer_survey_response_order_types`
--

CREATE TABLE `customer_survey_response_order_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer_survey_response_order_types`
--

INSERT INTO `customer_survey_response_order_types` (`id`, `name`) VALUES
(1, 'Walk-in'),
(2, 'Snackshop'),
(3, 'Catering'),
(4, 'Popclub Store Visit');

-- --------------------------------------------------------

--
-- Table structure for table `customer_survey_response_ratings`
--

CREATE TABLE `customer_survey_response_ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `rate` int(11) NOT NULL,
  `survey_question_id` int(10) UNSIGNED NOT NULL,
  `survey_question_rating_id` int(10) UNSIGNED NOT NULL,
  `customer_survey_response_id` int(10) UNSIGNED NOT NULL,
  `others` varchar(265) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(2, 'members', 'General User');

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

-- --------------------------------------------------------

--
-- Table structure for table `survey_questions`
--

CREATE TABLE `survey_questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(265) NOT NULL,
  `is_text_field` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_text_area` tinyint(1) NOT NULL DEFAULT 0,
  `is_email` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `others` int(11) NOT NULL DEFAULT 0,
  `survey_section_id` int(10) UNSIGNED DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_questions`
--

INSERT INTO `survey_questions` (`id`, `description`, `is_text_field`, `is_text_area`, `is_email`, `others`, `survey_section_id`, `status`) VALUES
(1, 'Name', 1, 0, 0, 0, 1, 1),
(2, 'Tel. No.', 1, 0, 0, 0, 1, 1),
(3, 'E-mail', 1, 0, 1, 0, 1, 1),
(4, 'Gender', 0, 0, 0, 0, 1, 1),
(5, 'Are you ?', 0, 0, 0, 0, 1, 1),
(6, 'Age', 0, 0, 0, 0, 1, 1),
(7, 'How often do you visit our store?', 0, 0, 0, 0, 2, 1),
(8, 'How did you hear about us?', 1, 0, 0, 1, 2, 1),
(9, 'Taste', 0, 0, 0, 0, 3, 1),
(10, 'Freshness', 0, 0, 0, 0, 3, 1),
(11, 'Temperature', 0, 0, 0, 0, 3, 1),
(12, 'Presentation', 0, 0, 0, 0, 3, 1),
(13, 'Courtesy', 0, 0, 0, 0, 4, 1),
(14, 'Cheerfulness', 0, 0, 0, 0, 4, 1),
(15, 'Speed of Service', 0, 0, 0, 0, 4, 1),
(16, 'Knowledge of staff', 0, 0, 0, 0, 4, 1),
(17, 'Appearance of staff', 0, 0, 0, 0, 4, 1),
(18, 'Cleanliness', 0, 0, 0, 0, 5, 1),
(19, 'Comfort', 0, 0, 0, 0, 5, 1),
(20, 'Decor', 0, 0, 0, 0, 5, 1),
(21, 'Price', 0, 0, 0, 0, 6, 1),
(22, 'Variety', 0, 0, 0, 0, 6, 1),
(23, 'Overall Experience', 0, 1, 0, 0, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `survey_question_answers`
--

CREATE TABLE `survey_question_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `survey_question_id` int(10) UNSIGNED NOT NULL,
  `survey_question_offered_answer_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_question_answers`
--

INSERT INTO `survey_question_answers` (`id`, `survey_question_id`, `survey_question_offered_answer_id`) VALUES
(1, 4, 1),
(2, 4, 2),
(3, 5, 3),
(4, 5, 4),
(5, 6, 5),
(6, 6, 6),
(7, 6, 7),
(8, 6, 8),
(9, 6, 9),
(10, 7, 10),
(11, 7, 11),
(12, 7, 12),
(13, 7, 13),
(14, 8, 14),
(15, 8, 15),
(16, 8, 16);

-- --------------------------------------------------------

--
-- Table structure for table `survey_question_offered_answers`
--

CREATE TABLE `survey_question_offered_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` varchar(265) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_question_offered_answers`
--

INSERT INTO `survey_question_offered_answers` (`id`, `text`) VALUES
(1, 'Male'),
(2, 'Female'),
(3, 'Employed'),
(4, 'Self-Employed'),
(5, '15 - 20'),
(6, '21 - 27'),
(7, '28 - 34'),
(8, '35 - 40'),
(9, 'over 40'),
(10, 'First time'),
(11, 'At least once a week'),
(12, 'At least once a month'),
(13, 'Rarely'),
(14, 'Advertisement'),
(15, 'Word of Mouth'),
(16, 'Promotional leaflet');

-- --------------------------------------------------------

--
-- Table structure for table `survey_question_offered_ratings`
--

CREATE TABLE `survey_question_offered_ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(265) NOT NULL,
  `description` varchar(265) NOT NULL,
  `lowest_rate_text` varchar(265) NOT NULL,
  `lowest_rate` int(11) NOT NULL,
  `highest_rate_text` varchar(265) NOT NULL,
  `highest_rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_question_offered_ratings`
--

INSERT INTO `survey_question_offered_ratings` (`id`, `name`, `description`, `lowest_rate_text`, `lowest_rate`, `highest_rate_text`, `highest_rate`) VALUES
(1, 'How much do you prioritize taste?\n', 'Please rate your priorities on a scale of 10 to 1.\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(2, 'Please rate your overall Satisfaction on taste.\n', 'Please rate your overall satisfaction on a scale of 1 to 5\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(3, 'How much do you prioritize freshness?\r\n', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(4, 'Please rate your overall Satisfaction on freshness.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(5, 'How much do you prioritize temperature?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(6, 'Please rate your overall Satisfaction on temperature.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(7, 'How much do you prioritize presentation?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(8, 'Please rate your overall Satisfaction on presentation.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(9, 'How much do you prioritize courtesy?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(10, 'Please rate your overall Satisfaction on courtesy.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(11, 'How much do you prioritize cheerfulness?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(12, 'Please rate your overall Satisfaction on cheerfulness.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(13, 'How much do you prioritize speed of service?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(14, 'Please rate your overall Satisfaction on speed of service.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(15, 'How much do you prioritize knowledge of staff?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(16, 'Please rate your overall Satisfaction on knowledge of staff.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(17, 'How much do you prioritize appearance of Staff?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(18, 'Please rate your overall Satisfaction on appearance of Staff.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(19, 'How much do you prioritize cleanliness?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(20, 'Please rate your overall Satisfaction on cleanliness.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(21, 'How much do you prioritize comfort?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(22, 'Please rate your overall Satisfaction on comfort.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(23, 'How much do you prioritize décor?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(24, 'Please rate your overall Satisfaction on décor.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(25, 'How much do you prioritize price?', 'Please rate your priorities on a scale of 10 to 1.\r\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(26, 'Please rate your overall Satisfaction on price.', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5),
(27, 'How much do you prioritize variety?', 'Please rate your priorities on a scale of 10 to 1.\n1 = highest and 10 = lowest priority', 'Lowest', 10, 'Highest', 1),
(28, 'Please rate your overall Satisfaction on variety.\r\n', 'Please rate your overall satisfaction on a scale of 1 to 5\r\n5 = Highly Satisfied and 1 = dissatisfied', 'Dissatisfied', 1, 'Highly Satisfied', 5);

-- --------------------------------------------------------

--
-- Table structure for table `survey_question_ratings`
--

CREATE TABLE `survey_question_ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `survey_question_id` int(10) UNSIGNED NOT NULL,
  `survey_question_offered_rating_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_question_ratings`
--

INSERT INTO `survey_question_ratings` (`id`, `survey_question_id`, `survey_question_offered_rating_id`) VALUES
(1, 9, 1),
(2, 9, 2),
(3, 10, 3),
(4, 10, 4),
(5, 11, 5),
(6, 11, 6),
(7, 12, 7),
(8, 12, 8),
(9, 13, 9),
(10, 13, 10),
(11, 14, 11),
(12, 14, 12),
(13, 15, 13),
(14, 15, 14),
(15, 16, 15),
(16, 16, 16),
(17, 17, 17),
(18, 17, 18),
(19, 18, 19),
(20, 18, 20),
(21, 19, 21),
(22, 19, 22),
(23, 20, 23),
(24, 20, 24),
(25, 21, 25),
(26, 21, 26),
(27, 22, 27),
(28, 22, 28);

-- --------------------------------------------------------

--
-- Table structure for table `survey_question_sections`
--

CREATE TABLE `survey_question_sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(365) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_question_sections`
--

INSERT INTO `survey_question_sections` (`id`, `name`) VALUES
(1, 'Personal Information'),
(2, 'Information Taters'),
(3, 'Food and Drinks'),
(4, 'Service'),
(5, 'Ambience'),
(6, 'Our Menu'),
(7, 'Overall Experience');

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
(1, '127.0.0.1', 'administrator', '$2y$08$200Z6ZZbp3RAEXoaWcMA6uJOFicwNZaqk4oDhqTUiFXFe63MG.Daa', 'admin@admin.com', NULL, '', NULL, NULL, NULL, NULL, NULL, 1268889823, 1668674052, 1);

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
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_companies`
--

CREATE TABLE `user_companies` (
  `id` int(11) UNSIGNED NOT NULL,
  `company_id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `user_status_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `user_id`, `first_name`, `last_name`, `designation`, `email`, `phone_number`, `user_status_id`) VALUES
(1, 1, 'Administrator', '', 'MIS Department', 'admin@admin.com', '09084741500', 2);

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

-- --------------------------------------------------------

--
-- Table structure for table `user_stores`
--

CREATE TABLE `user_stores` (
  `id` int(11) UNSIGNED NOT NULL,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_survey_responses`
--
ALTER TABLE `customer_survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `customer_survey_response_order_type_id` (`customer_survey_response_order_type_id`),
  ADD KEY `catering_transaction_id` (`catering_transaction_id`),
  ADD KEY `fb_user_id` (`fb_user_id`),
  ADD KEY `mobile_user_id` (`mobile_user_id`);

--
-- Indexes for table `customer_survey_response_answers`
--
ALTER TABLE `customer_survey_response_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_survey_id` (`customer_survey_response_id`),
  ADD KEY `offered_answer_id` (`text`),
  ADD KEY `survey_question_id` (`survey_question_id`),
  ADD KEY `survey_question_answer_id` (`survey_question_answer_id`);

--
-- Indexes for table `customer_survey_response_order_types`
--
ALTER TABLE `customer_survey_response_order_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_survey_response_ratings`
--
ALTER TABLE `customer_survey_response_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_question_id` (`survey_question_id`),
  ADD KEY `customer_survey_response_id` (`customer_survey_response_id`),
  ADD KEY `customer_survey_response_ratings_ibfk_2` (`survey_question_rating_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_section_id` (`survey_section_id`);

--
-- Indexes for table `survey_question_answers`
--
ALTER TABLE `survey_question_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_question_id`),
  ADD KEY `question_id` (`survey_question_offered_answer_id`);

--
-- Indexes for table `survey_question_offered_answers`
--
ALTER TABLE `survey_question_offered_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `survey_question_offered_ratings`
--
ALTER TABLE `survey_question_offered_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `survey_question_ratings`
--
ALTER TABLE `survey_question_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_question_id` (`survey_question_id`),
  ADD KEY `survey_offered_rating_id` (`survey_question_offered_rating_id`);

--
-- Indexes for table `survey_question_sections`
--
ALTER TABLE `survey_question_sections`
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
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `user_stores`
--
ALTER TABLE `user_stores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_survey_responses`
--
ALTER TABLE `customer_survey_responses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_survey_response_answers`
--
ALTER TABLE `customer_survey_response_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_survey_response_order_types`
--
ALTER TABLE `customer_survey_response_order_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_survey_response_ratings`
--
ALTER TABLE `customer_survey_response_ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_questions`
--
ALTER TABLE `survey_questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `survey_question_answers`
--
ALTER TABLE `survey_question_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `survey_question_offered_answers`
--
ALTER TABLE `survey_question_offered_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `survey_question_offered_ratings`
--
ALTER TABLE `survey_question_offered_ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `survey_question_ratings`
--
ALTER TABLE `survey_question_ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `survey_question_sections`
--
ALTER TABLE `survey_question_sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_companies`
--
ALTER TABLE `user_companies`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_status`
--
ALTER TABLE `user_status`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_stores`
--
ALTER TABLE `user_stores`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_survey_responses`
--
ALTER TABLE `customer_survey_responses`
  ADD CONSTRAINT `customer_survey_responses_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `staging_newteishop_feb_11_2023`.`transaction_tb` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_2` FOREIGN KEY (`catering_transaction_id`) REFERENCES `staging_newteishop_feb_11_2023`.`catering_transaction_tb` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_5` FOREIGN KEY (`customer_survey_response_order_type_id`) REFERENCES `customer_survey_response_order_types` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_6` FOREIGN KEY (`fb_user_id`) REFERENCES `staging_newteishop_feb_11_2023`.`fb_users` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_7` FOREIGN KEY (`mobile_user_id`) REFERENCES `staging_newteishop_feb_11_2023`.`mobile_users` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `customer_survey_response_answers`
--
ALTER TABLE `customer_survey_response_answers`
  ADD CONSTRAINT `customer_survey_response_answers_ibfk_1` FOREIGN KEY (`customer_survey_response_id`) REFERENCES `customer_survey_responses` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_response_answers_ibfk_2` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_response_answers_ibfk_3` FOREIGN KEY (`survey_question_answer_id`) REFERENCES `survey_question_answers` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `customer_survey_response_ratings`
--
ALTER TABLE `customer_survey_response_ratings`
  ADD CONSTRAINT `customer_survey_response_ratings_ibfk_1` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_response_ratings_ibfk_2` FOREIGN KEY (`survey_question_rating_id`) REFERENCES `survey_question_ratings` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_response_ratings_ibfk_3` FOREIGN KEY (`customer_survey_response_id`) REFERENCES `customer_survey_responses` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD CONSTRAINT `survey_questions_ibfk_1` FOREIGN KEY (`survey_section_id`) REFERENCES `survey_question_sections` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `survey_question_answers`
--
ALTER TABLE `survey_question_answers`
  ADD CONSTRAINT `survey_question_answers_ibfk_1` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `survey_question_answers_ibfk_2` FOREIGN KEY (`survey_question_offered_answer_id`) REFERENCES `survey_question_offered_answers` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `survey_question_ratings`
--
ALTER TABLE `survey_question_ratings`
  ADD CONSTRAINT `survey_question_ratings_ibfk_1` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `survey_question_ratings_ibfk_2` FOREIGN KEY (`survey_question_offered_rating_id`) REFERENCES `survey_question_offered_ratings` (`id`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
