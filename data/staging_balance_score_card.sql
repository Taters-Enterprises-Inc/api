-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2022 at 12:14 PM
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
  `order_no` varchar(100) DEFAULT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `catering_transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_survey_response_order_type_id` int(10) UNSIGNED NOT NULL,
  `deals_redeem_id` int(10) UNSIGNED DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customer_survey_response_answers`
--

CREATE TABLE `customer_survey_response_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `survey_question_offered_answer_id` int(10) UNSIGNED DEFAULT NULL,
  `other_text` varchar(265) DEFAULT NULL,
  `survey_question_id` int(10) UNSIGNED NOT NULL,
  `customer_survey_response_id` int(10) UNSIGNED NOT NULL
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
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  `description` varchar(265) NOT NULL,
  `is_comment` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_questions`
--

INSERT INTO `survey_questions` (`id`, `start_date`, `end_date`, `status`, `description`, `is_comment`) VALUES
(1, NULL, NULL, 1, 'Please rate your overall satisfaction with your Taters Experience.', 0),
(2, NULL, NULL, 2, 'Please select your Visit Type.', 0),
(3, NULL, NULL, 3, 'The friendlyness of the delivery driver.', 0),
(4, NULL, NULL, 4, 'Taste of your food.', 0),
(5, NULL, NULL, 5, 'The availability of the menu items.', 0),
(6, NULL, NULL, 6, 'Speed of service', 0),
(7, NULL, NULL, 7, 'Temperature of food', 0),
(8, NULL, NULL, 8, 'The accuracy of order', 0),
(9, NULL, NULL, 9, 'Portion of size you recieved.', 0),
(10, NULL, NULL, 10, 'The overall value of the price you paid.', 0),
(11, NULL, NULL, 11, 'Did you have a problem during your experience?', 0),
(13, NULL, NULL, 13, 'Based on this visit, what is the likelihood that you will\r\nRecommend this Taters to others in the next 30 days?', 0),
(14, NULL, NULL, 14, 'Please tell us in three or more sentences about your experience with Taters.', 1),
(15, NULL, NULL, 15, 'If you were to choose the next store destination of Taters, where would you want it located?', 1),
(16, NULL, NULL, 16, 'If you were to add a new permanent entree on the menu, what would you want to see?', 1),
(17, NULL, NULL, 17, 'Was your order delivered when promised?', 0),
(18, NULL, NULL, 18, 'Please select your Gender.\r\n', 0),
(19, NULL, NULL, 19, 'Please select your age.', 0),
(20, NULL, NULL, 20, 'Please select which of the following best describes your background.', 0);

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
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 2, 6),
(7, 2, 7),
(8, 2, 8),
(9, 3, 1),
(10, 3, 2),
(11, 3, 3),
(12, 3, 4),
(13, 3, 5),
(14, 4, 1),
(15, 4, 2),
(16, 4, 3),
(17, 4, 4),
(18, 4, 5),
(19, 5, 1),
(20, 5, 2),
(21, 5, 3),
(22, 5, 4),
(23, 5, 5),
(24, 6, 1),
(25, 6, 2),
(26, 6, 3),
(27, 6, 4),
(28, 6, 5),
(29, 7, 1),
(30, 7, 2),
(31, 7, 3),
(32, 7, 4),
(33, 7, 5),
(34, 8, 1),
(35, 8, 2),
(36, 8, 3),
(37, 8, 4),
(38, 8, 5),
(39, 9, 1),
(40, 9, 2),
(41, 9, 3),
(42, 9, 4),
(43, 9, 5),
(44, 10, 1),
(45, 10, 2),
(46, 10, 3),
(47, 10, 4),
(48, 10, 5),
(49, 11, 9),
(50, 11, 10),
(51, 13, 1),
(52, 13, 2),
(53, 13, 3),
(54, 13, 4),
(55, 13, 5),
(57, 17, 9),
(58, 17, 10),
(59, 18, 11),
(60, 18, 12),
(61, 18, 13),
(62, 18, 14),
(63, 18, 15),
(64, 19, 16),
(65, 19, 17),
(66, 19, 18),
(67, 19, 19),
(68, 19, 20),
(69, 19, 21),
(70, 19, 22),
(71, 20, 23),
(72, 20, 24),
(73, 20, 25),
(74, 20, 26),
(75, 20, 27),
(76, 20, 28),
(77, 20, 29);

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
(1, 'Highly Satisfied'),
(2, 'Satisfied'),
(3, 'Neither Satisfied nor Dissatisfied'),
(4, 'Dissastisfied'),
(5, 'Highly Dissatisfied'),
(6, 'Delivery'),
(7, 'Online order : Pick-up'),
(8, 'Walk-In'),
(9, 'Yes'),
(10, 'No'),
(11, 'Male\r\n'),
(12, 'Female\r\n'),
(13, 'Non-Binary / Third Gender\r\n'),
(14, 'Prefer to self-describe\r\n'),
(15, 'Prefer not to answer'),
(16, 'Under 18'),
(17, '18 to 24'),
(18, '25 to 34'),
(19, '35 to 44'),
(20, '45 to 49'),
(21, '50 to 64'),
(22, '65 or above.'),
(23, 'Asian\r\n'),
(24, 'Native Hawaiian or other Native Islander\r\n'),
(25, 'White or Caucassian\r\n'),
(26, 'American Indian or Alaska Native\r\n'),
(27, 'Hispanic or Latino\r\n'),
(28, 'Black or African American\r\n'),
(29, 'Other');

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
  ADD KEY `deals_redeem_id` (`deals_redeem_id`);

--
-- Indexes for table `customer_survey_response_answers`
--
ALTER TABLE `customer_survey_response_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_survey_id` (`customer_survey_response_id`),
  ADD KEY `offered_answer_id` (`other_text`),
  ADD KEY `question_id` (`survey_question_offered_answer_id`),
  ADD KEY `survey_id` (`survey_question_id`);

--
-- Indexes for table `customer_survey_response_order_types`
--
ALTER TABLE `customer_survey_response_order_types`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `survey_question_answers`
--
ALTER TABLE `survey_question_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `survey_question_offered_answers`
--
ALTER TABLE `survey_question_offered_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
  ADD CONSTRAINT `customer_survey_responses_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `staging_newteishop`.`transaction_tb` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_2` FOREIGN KEY (`catering_transaction_id`) REFERENCES `staging_newteishop`.`catering_transaction_tb` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_3` FOREIGN KEY (`deals_redeem_id`) REFERENCES `staging_newteishop`.`deals_redeems_tb` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_responses_ibfk_5` FOREIGN KEY (`customer_survey_response_order_type_id`) REFERENCES `customer_survey_response_order_types` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `customer_survey_response_answers`
--
ALTER TABLE `customer_survey_response_answers`
  ADD CONSTRAINT `customer_survey_response_answers_ibfk_1` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_questions` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_survey_response_answers_ibfk_2` FOREIGN KEY (`survey_question_offered_answer_id`) REFERENCES `survey_question_offered_answers` (`id`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
