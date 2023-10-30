-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2023 at 12:50 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_hr_appraisal`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_and_punctuality`
--

CREATE TABLE `attendance_and_punctuality` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `absences` varchar(30) DEFAULT NULL,
  `tardiness` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `core_competency_grade`
--

CREATE TABLE `core_competency_grade` (
  `id` int(11) NOT NULL,
  `title` varchar(30) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id` int(11) NOT NULL,
  `title` varchar(30) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `kra_kpi_grade`
--

CREATE TABLE `kra_kpi_grade` (
  `id` int(11) NOT NULL,
  `weight` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kra_kpi_grade`
--

INSERT INTO `kra_kpi_grade` (`id`, `weight`) VALUES
(1, 0.33),
(2, 0.33),
(3, 0.33);

-- --------------------------------------------------------

--
-- Table structure for table `performance_criteria`
--

CREATE TABLE `performance_criteria` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `minimum_score` decimal(10,2) NOT NULL,
  `maximum_score` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_criteria`
--

INSERT INTO `performance_criteria` (`id`, `name`, `minimum_score`, `maximum_score`) VALUES
(1, 'Exceptional Performance', 4.90, 5.00),
(2, 'Above Expectations', 4.00, 4.89),
(3, 'Reliable Performance', 3.00, 4.99),
(4, 'Improvement Needed', 2.00, 2.99),
(5, 'Unsatisfactory', 1.00, 1.99);

-- --------------------------------------------------------

--
-- Table structure for table `rating_scale`
--

CREATE TABLE `rating_scale` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating_scale`
--

INSERT INTO `rating_scale` (`id`, `name`, `description`) VALUES
(1, 'Exceptional', 'A role model, mentor or leader that embodies values & exhibits outstanding behaviors, skills, and expertise.'),
(2, 'Highly Effective', 'Generally demonstartes competencies at levels exceeding expectations for their position.'),
(3, 'Effective', 'Achieves performance and desmostrates competencies at a level consistent with their position.'),
(4, 'Needs Improvement', 'Sometimes meets expectations or goals'),
(5, 'Needs Coaching', 'Regularly demonstrates behaviors incosistent with competencies and values.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_and_punctuality`
--
ALTER TABLE `attendance_and_punctuality`
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
-- Indexes for table `kra_kpi_grade`
--
ALTER TABLE `kra_kpi_grade`
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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_and_punctuality`
--
ALTER TABLE `attendance_and_punctuality`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `core_competency_grade`
--
ALTER TABLE `core_competency_grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `functional_competency_and_punctuality_grade`
--
ALTER TABLE `functional_competency_and_punctuality_grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kra_kpi_grade`
--
ALTER TABLE `kra_kpi_grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
