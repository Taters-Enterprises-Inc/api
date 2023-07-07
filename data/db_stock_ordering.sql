-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2023 at 09:18 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_stock_ordering`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing_information_tb`
--

CREATE TABLE `billing_information_tb` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `billing_id` varchar(128) DEFAULT NULL,
  `billing_amount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_information_tb`
--

INSERT INTO `billing_information_tb` (`id`, `user_id`, `billing_id`, `billing_amount`) VALUES
(1, NULL, '20321321', 20321321),
(2, NULL, '32132131', 323123211),
(3, NULL, '0000001', 100),
(4, NULL, '0001500800', 100000),
(5, NULL, '1000000', 258),
(6, NULL, '0001', 200),
(7, NULL, '000001', 100),
(8, NULL, '01', 2),
(9, NULL, '100000', 1000000),
(10, NULL, '258', 1),
(11, NULL, '200', 3),
(12, NULL, '000041005080', 100000),
(13, NULL, '0001520700', 100000),
(14, NULL, '100000', 1),
(15, NULL, '100200300', 100000),
(16, NULL, '100200300', 1000000),
(17, NULL, '100200500', 100);

-- --------------------------------------------------------

--
-- Table structure for table `category_tb`
--

CREATE TABLE `category_tb` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_tb`
--

INSERT INTO `category_tb` (`category_id`, `category_name`) VALUES
(1, 'Frozen'),
(2, 'Dry');

-- --------------------------------------------------------

--
-- Table structure for table `order_information_tb`
--

CREATE TABLE `order_information_tb` (
  `id` int(11) NOT NULL,
  `store_id` int(11) DEFAULT NULL,
  `order_type_id` int(11) DEFAULT NULL,
  `status_id` int(2) NOT NULL,
  `order_placement_date` datetime NOT NULL,
  `requested_delivery_date` datetime NOT NULL,
  `commited_delivery_date` datetime DEFAULT NULL,
  `order_confirmation_date` datetime DEFAULT NULL,
  `actual_delivery_date` datetime DEFAULT NULL,
  `billing_information_id` varchar(128) DEFAULT NULL,
  `payment_status_id` int(1) NOT NULL,
  `reviewed_date` datetime DEFAULT NULL,
  `dispatch_date` datetime DEFAULT NULL,
  `enroute_date` datetime DEFAULT NULL,
  `transportation_id` int(11) NOT NULL,
  `payment_confirmation_date` datetime DEFAULT NULL,
  `delivery_receipt` varchar(128) DEFAULT NULL,
  `updated_delivery_receipt` varchar(128) DEFAULT NULL,
  `payment_detail_image` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item_tb`
--

CREATE TABLE `order_item_tb` (
  `id` int(11) NOT NULL,
  `order_information_id` int(11) DEFAULT NULL,
  `product_id` varchar(11) DEFAULT NULL,
  `order_qty` int(7) DEFAULT NULL,
  `commited_qty` int(7) DEFAULT NULL,
  `delivered_qty` int(7) DEFAULT NULL,
  `total_cost` int(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_place_schedule_logic_tb`
--

CREATE TABLE `order_place_schedule_logic_tb` (
  `id` int(11) NOT NULL,
  `category_type_id` int(11) DEFAULT NULL,
  `order_date` tinyint(4) NOT NULL,
  `order_cutoff` time DEFAULT NULL,
  `available_delivery_date` tinyint(4) DEFAULT NULL,
  `available_delivery_date_after_cutoff` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_place_schedule_logic_tb`
--

INSERT INTO `order_place_schedule_logic_tb` (`id`, `category_type_id`, `order_date`, `order_cutoff`, `available_delivery_date`, `available_delivery_date_after_cutoff`) VALUES
(1, 1, 1, '14:00:00', 5, 1),
(2, 1, 2, '14:00:00', 5, 1),
(3, 1, 3, '14:00:00', 1, 3),
(4, 1, 4, '14:00:00', 1, 3),
(5, 1, 5, '14:00:00', 3, 5),
(6, 1, 6, '14:00:00', 5, 5),
(7, 1, 7, '14:00:00', 5, 5),
(8, 2, 1, '14:00:00', 4, 2),
(9, 2, 2, '14:00:00', 4, 2),
(10, 2, 3, '14:00:00', 2, 4),
(11, 2, 4, '14:00:00', 2, 4),
(12, 2, 5, '14:00:00', 4, 4),
(13, 2, 6, '14:00:00', 4, 4),
(14, 2, 7, '14:00:00', 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL,
  `short_name` varchar(256) DEFAULT NULL,
  `description` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`id`, `short_name`, `description`) VALUES
(1, NULL, 'Newly placed order (Store)'),
(2, NULL, 'UPDATE ORDER (SUPPLIER)'),
(3, NULL, 'ORDER REVEIEWED (PROCUREMENT)'),
(4, NULL, 'ORDER CONFIRMED (PROCUREMENT)'),
(5, NULL, 'ORDER DISPATCHED (SUPPLIER)'),
(6, NULL, 'ORDER EN ROUTE (SUPPLIER)'),
(7, NULL, 'ORDER RECIEVED (STORE)'),
(8, NULL, 'BILLING UPDATED (SUPPLIER)'),
(9, NULL, 'BILLING PAYED (STORE)'),
(10, NULL, 'ORDER CONFIRMED & COMPLETED (SUPPLIER)'),
(11, NULL, 'UPDATE STOCKS (SUPPLIER)');

-- --------------------------------------------------------

--
-- Table structure for table `payment_status_tb`
--

CREATE TABLE `payment_status_tb` (
  `id` int(11) NOT NULL,
  `short_name` varchar(32) DEFAULT NULL,
  `description` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_status_tb`
--

INSERT INTO `payment_status_tb` (`id`, `short_name`, `description`) VALUES
(1, 'unpaid', 'Order/s are not paid'),
(2, 'paid', 'Order/s are paid');

-- --------------------------------------------------------

--
-- Table structure for table `product_cost_per_store_tb`
--

CREATE TABLE `product_cost_per_store_tb` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_multiplier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `product_cost_per_store_tb`
--

INSERT INTO `product_cost_per_store_tb` (`id`, `store_id`, `category_id`, `product_multiplier`) VALUES
(1, 1, 2, 12),
(2, 2, 2, 12),
(3, 3, 2, 12),
(4, 4, 2, 12),
(5, 5, 2, 12),
(6, 6, 2, 12),
(7, 7, 2, 12),
(8, 8, 2, 12),
(9, 9, 2, 12),
(10, 10, 2, 12),
(11, 11, 2, 12),
(12, 12, 2, 12),
(13, 13, 2, 12),
(14, 14, 2, 12),
(15, 15, 2, 12),
(16, 16, 2, 12),
(17, 17, 2, 12),
(18, 18, 2, 12),
(19, 19, 2, 12),
(20, 20, 2, 12),
(21, 21, 2, 12),
(22, 22, 2, 12),
(23, 23, 2, 12),
(24, 24, 2, 12),
(25, 25, 2, 12),
(26, 26, 2, 12),
(27, 27, 2, 12),
(28, 28, 2, 12),
(29, 29, 2, 12),
(30, 30, 2, 12),
(31, 31, 2, 12),
(32, 32, 2, 12),
(33, 33, 2, 12),
(34, 34, 2, 12),
(35, 35, 2, 12),
(36, 36, 2, 12),
(37, 37, 2, 12),
(38, 38, 2, 12),
(39, 39, 2, 12),
(40, 40, 2, 12),
(41, 41, 2, 9),
(42, 42, 2, 9),
(43, 43, 2, 9),
(44, 44, 2, 9),
(45, 45, 2, 9),
(46, 46, 2, 9),
(47, 47, 2, 9),
(48, 48, 2, 9),
(49, 49, 2, 9),
(50, 50, 2, 9),
(51, 51, 2, 9),
(52, 52, 2, 9),
(53, 53, 2, 9),
(54, 54, 2, 9),
(55, 55, 2, 9),
(56, 56, 2, 9),
(57, 57, 2, 9),
(58, 58, 2, 9),
(59, 59, 2, 9),
(60, 60, 2, 9),
(61, 61, 2, 9),
(62, 62, 2, 9),
(63, 63, 2, 9),
(64, 64, 2, 9),
(65, 65, 2, 9),
(66, 66, 2, 9),
(67, 67, 2, 9),
(68, 68, 2, 9),
(69, 69, 2, 9),
(70, 70, 2, 9),
(71, 71, 2, 9),
(72, 72, 2, 9),
(73, 73, 2, 9),
(74, 74, 2, 9),
(75, 75, 2, 9),
(76, 76, 2, 9),
(77, 77, 2, 9),
(78, 78, 2, 9),
(79, 79, 2, 9),
(80, 80, 2, 9),
(81, 81, 2, 9),
(82, 82, 2, 9),
(83, 83, 2, 9),
(84, 84, 2, 9),
(85, 85, 2, 9),
(86, 86, 2, 9),
(87, 87, 2, 9),
(88, 88, 2, 9),
(89, 89, 2, 9),
(90, 90, 2, 9),
(91, 91, 2, 9),
(92, 92, 2, 9),
(93, 93, 2, 9),
(94, 94, 2, 9),
(95, 95, 2, 9),
(96, 96, 2, 9),
(97, 97, 2, 9),
(98, 98, 2, 9),
(99, 99, 2, 9),
(100, 100, 2, 9),
(101, 1, 1, 10),
(102, 2, 1, 10),
(103, 3, 1, 10),
(104, 4, 1, 10),
(105, 5, 1, 10),
(106, 6, 1, 10),
(107, 7, 1, 10),
(108, 8, 1, 10),
(109, 9, 1, 10),
(110, 10, 1, 10),
(111, 11, 1, 10),
(112, 12, 1, 10),
(113, 13, 1, 10),
(114, 14, 1, 10),
(115, 15, 1, 10),
(116, 16, 1, 10),
(117, 17, 1, 10),
(118, 18, 1, 10),
(119, 19, 1, 10),
(120, 20, 1, 10),
(121, 21, 1, 10),
(122, 22, 1, 10),
(123, 23, 1, 10),
(124, 24, 1, 10),
(125, 25, 1, 10),
(126, 26, 1, 10),
(127, 27, 1, 10),
(128, 28, 1, 10),
(129, 29, 1, 10),
(130, 30, 1, 10),
(131, 31, 1, 10),
(132, 32, 1, 10),
(133, 33, 1, 10),
(134, 34, 1, 10),
(135, 35, 1, 10),
(136, 36, 1, 10),
(137, 37, 1, 10),
(138, 38, 1, 10),
(139, 39, 1, 10),
(140, 40, 1, 10),
(141, 41, 1, 9),
(142, 42, 1, 9),
(143, 43, 1, 9),
(144, 44, 1, 9),
(145, 45, 1, 9),
(146, 46, 1, 9),
(147, 47, 1, 9),
(148, 48, 1, 9),
(149, 49, 1, 9),
(150, 50, 1, 9),
(151, 51, 1, 9),
(152, 52, 1, 9),
(153, 53, 1, 9),
(154, 54, 1, 9),
(155, 55, 1, 9),
(156, 56, 1, 9),
(157, 57, 1, 9),
(158, 58, 1, 9),
(159, 59, 1, 9),
(160, 60, 1, 9),
(161, 61, 1, 9),
(162, 62, 1, 9),
(163, 63, 1, 9),
(164, 64, 1, 9),
(165, 65, 1, 9),
(166, 66, 1, 9),
(167, 67, 1, 9),
(168, 68, 1, 9),
(169, 69, 1, 9),
(170, 70, 1, 9),
(171, 71, 1, 9),
(172, 72, 1, 9),
(173, 73, 1, 9),
(174, 74, 1, 9),
(175, 75, 1, 9),
(176, 76, 1, 9),
(177, 77, 1, 9),
(178, 78, 1, 9),
(179, 79, 1, 9),
(180, 80, 1, 9),
(181, 81, 1, 9),
(182, 82, 1, 9),
(183, 83, 1, 9),
(184, 84, 1, 9),
(185, 85, 1, 9),
(186, 86, 1, 9),
(187, 87, 1, 9),
(188, 88, 1, 9),
(189, 89, 1, 9),
(190, 90, 1, 9),
(191, 91, 1, 9),
(192, 92, 1, 9),
(193, 93, 1, 9),
(194, 94, 1, 9),
(195, 95, 1, 9),
(196, 96, 1, 9),
(197, 97, 1, 9),
(198, 98, 1, 9),
(199, 99, 1, 9),
(200, 100, 1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `product_cost_tb`
--

CREATE TABLE `product_cost_tb` (
  `id` int(11) NOT NULL,
  `product_id` varchar(6) DEFAULT NULL,
  `store_id` varchar(3) DEFAULT NULL,
  `cost` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `product_cost_tb`
--

INSERT INTO `product_cost_tb` (`id`, `product_id`, `store_id`, `cost`) VALUES
(1, '10014A', 'N/A', '159.04'),
(2, '100421', 'N/A', '184.8'),
(3, '100423', 'N/A', '5.38'),
(4, '100751', 'N/A', '4.6'),
(5, '119141', 'N/A', '265.44'),
(6, '131132', 'N/A', '47.04'),
(7, '131133', 'N/A', '63.22'),
(8, '131432', 'N/A', '21.6'),
(9, '131441', 'N/A', '288.17'),
(10, '131631', 'N/A', '419.12'),
(11, '132230', 'N/A', '16.8'),
(12, '132211', 'N/A', '7.06'),
(13, '132220', 'N/A', '145.6'),
(14, '134341', 'N/A', '22.34'),
(15, '134431', 'N/A', '18.29'),
(16, '141211', 'N/A', '11.39'),
(17, '162153', 'N/A', '12.6'),
(18, '162154', 'N/A', '16.8'),
(19, '211212', 'N/A', '250'),
(20, '212211', 'N/A', '200'),
(21, '219001', 'N/A', '100'),
(22, '219002', 'N/A', '380.8'),
(23, '259870', 'N/A', '364'),
(24, '261241', 'N/A', '263.2'),
(25, '265141', 'N/A', '145.85'),
(26, '266351', 'N/A', '5.04'),
(27, '266542', 'N/A', '196'),
(28, '266741', 'N/A', '247.8'),
(29, '291731', 'N/A', '311.11'),
(30, '305112', 'N/A', '64.2'),
(31, '305401', 'N/A', '206.08'),
(32, '305421', 'N/A', '227.96'),
(33, '305431', 'N/A', '231.93'),
(34, '305441', 'N/A', '218.05'),
(35, '305451', 'N/A', '224'),
(36, '326343', 'N/A', '672'),
(37, '350712', 'N/A', '369.6'),
(38, '361012', 'N/A', '208.99'),
(39, '381232', 'N/A', '7.28'),
(40, '381252', 'N/A', '7.28'),
(41, '382532', 'N/A', '5.04'),
(42, '389273', 'N/A', '57.29'),
(43, '390100', 'N/A', '246.4'),
(44, '390111', 'N/A', '47.04'),
(45, '390113', 'N/A', '48.16'),
(46, '390134', 'N/A', '408.18'),
(47, '390141', 'N/A', '386.4'),
(48, '390143', 'N/A', '412.16'),
(49, '390200', 'N/A', '336'),
(50, '390201', 'N/A', '273.46'),
(51, '390211', 'N/A', '40.88'),
(52, '390241', 'N/A', '322.56'),
(53, '390311', 'N/A', '45.92'),
(54, '390341', 'N/A', '367.36'),
(55, '390464', 'N/A', '537.6'),
(56, '390511', 'N/A', '49'),
(57, '390531', 'N/A', '463.44'),
(58, '390731', 'N/A', '257.6'),
(59, '390732', 'N/A', '188.16'),
(60, '391002', 'N/A', '403.2'),
(61, '391411', 'N/A', '73.92'),
(62, '391631', 'N/A', '145.6'),
(63, '391632', 'N/A', '168'),
(64, '392001', 'N/A', '73.14'),
(65, '392061', 'N/A', '1.55'),
(66, '392241', 'N/A', '88'),
(67, '392261', 'N/A', '1.87'),
(68, '392441', 'N/A', '109.44'),
(69, '393322', 'N/A', '60.02'),
(70, '394531', 'N/A', '23.95'),
(71, '394561', 'N/A', '0.25'),
(72, '396141', 'N/A', '381.7'),
(73, '396432', 'N/A', '14,174.72'),
(74, '397630', 'N/A', '414.4'),
(75, '397652', 'N/A', '980.52'),
(76, '397730', 'N/A', '619'),
(77, '398311', 'N/A', '115.5'),
(78, '399291', 'N/A', '120.69'),
(79, '399322', 'N/A', '149.6'),
(80, '399323', 'N/A', '168.56'),
(81, '401061', 'N/A', '28.4'),
(82, '401151', 'N/A', '269.78'),
(83, '401152', 'N/A', '269.78'),
(84, '401153', 'N/A', '269.78'),
(85, '401251', 'N/A', '269.78'),
(86, '401351', 'N/A', '269.78'),
(87, '401451', 'N/A', '269.78'),
(88, '401551', 'N/A', '269.78'),
(89, '402123', 'N/A', '28.28'),
(90, '402124', 'N/A', '28.28'),
(91, '402192', 'N/A', '28.28'),
(92, '402193', 'N/A', '28.28'),
(93, '402194', 'N/A', '28.28'),
(94, '402195', 'N/A', '28.28'),
(95, '403111', 'N/A', '24.64'),
(96, '403112', 'N/A', '24.64'),
(97, '403113', 'N/A', '24.64'),
(98, '403171', 'N/A', '24.64'),
(99, '403212', 'N/A', '24.64'),
(100, '45005A', 'N/A', '156.81'),
(101, '412521', 'N/A', '52.64'),
(102, '414132', 'N/A', '1,328.32'),
(103, '424121', 'N/A', '310.63'),
(104, '424233', 'N/A', '1,747.20'),
(105, '460224', 'N/A', '10.73'),
(106, '460264', 'N/A', '8.06'),
(107, '469131', 'N/A', '3.4'),
(108, '502370', 'N/A', '5.4'),
(109, '510010', 'N/A', '0.14'),
(110, '510171', 'N/A', '1.34'),
(111, '511110', 'N/A', '0.78'),
(112, '511371', 'N/A', '0.73'),
(113, '512352', 'N/A', '1.06'),
(114, '512361', 'N/A', '1.62'),
(115, '512372', 'N/A', '2.63'),
(116, '515130', 'N/A', '0.56'),
(117, '525152', 'N/A', '1.45'),
(118, '525153', 'N/A', '4.14'),
(119, '525165', 'N/A', '1.68'),
(120, '525172', 'N/A', '2.24'),
(121, '527110', 'N/A', '1.34'),
(122, '527120', 'N/A', '1.34'),
(123, '527140', 'N/A', '1.34'),
(124, '527360', 'N/A', '0.2'),
(125, '534160', 'N/A', '2.3'),
(126, '534312', 'N/A', '2.61'),
(127, '534322', 'N/A', '3.14'),
(128, '538311', 'N/A', '4.37'),
(129, '539150', 'N/A', '3.26'),
(130, '539151', 'N/A', '3.26'),
(131, '539160', 'N/A', '4.7'),
(132, '53A9A5', 'N/A', '1.06'),
(133, '542074', 'N/A', '1.26'),
(134, '542100', 'N/A', '0.56'),
(135, '542110', 'N/A', '0.84'),
(136, '542120', 'N/A', '1.01'),
(137, '542180', 'N/A', '2.46'),
(138, '545115', 'N/A', '0.28'),
(139, '545120', 'N/A', '0.6'),
(140, '545140', 'N/A', '1.49'),
(141, '546020', 'N/A', '0.39'),
(142, '546030', 'N/A', '0'),
(143, '546050', 'N/A', '0.4'),
(144, '546061', 'N/A', '0.67'),
(145, '546080', 'N/A', '1.23'),
(146, '546110', 'N/A', '0.28'),
(147, '548022', 'N/A', '0.18'),
(148, '549011', 'N/A', '0.81'),
(149, '554010', 'N/A', '3.52'),
(150, '556010', 'N/A', '1.71'),
(151, '562020', 'N/A', '0'),
(152, '562050', 'N/A', '1.38'),
(153, '562060', 'N/A', '2.01'),
(154, '562070', 'N/A', '2.35'),
(155, '562080', 'N/A', '5.64'),
(156, '562083', 'N/A', '1.57'),
(157, '562090', 'N/A', '8.65'),
(158, '562091', 'N/A', '14.25'),
(159, '562092', 'N/A', '5.54'),
(160, '562094', 'N/A', '1.85'),
(161, '582830', 'N/A', '1.74'),
(162, '582860', 'N/A', '0.58'),
(163, '582861', 'N/A', '0.65'),
(164, '582870', 'N/A', '2.04'),
(165, '584150', 'N/A', '1.85'),
(166, '584160', 'N/A', '2.97'),
(167, '591010', 'N/A', '0.51'),
(168, '592321', 'N/A', '14.11'),
(169, '789015', 'N/A', '1.68'),
(170, '911142', 'N/A', '0.28'),
(171, '912211', 'N/A', '0.19'),
(172, '914011', 'N/A', '0.48'),
(173, '916112', 'N/A', '0.29'),
(174, '920111', 'N/A', '1.9'),
(175, '921041', 'N/A', '4.2'),
(176, '921051', 'N/A', '5.88'),
(177, '921141', 'N/A', '4.2'),
(178, '922021', 'N/A', '9.33'),
(179, '923240', 'N/A', '36.94'),
(180, '923351', 'N/A', '193.89'),
(181, '923451', 'N/A', '322.61'),
(182, '924011', 'N/A', '4.84'),
(183, '931000', 'N/A', '0.9'),
(184, '931050', 'N/A', '0.17'),
(185, '931211', 'N/A', '0.84'),
(186, '931411', 'N/A', '0.84'),
(187, '933121', 'N/A', '2.36'),
(188, '933501', 'N/A', '2.35'),
(189, '933502', 'N/A', '8.96'),
(190, '933511', 'N/A', '1.9'),
(191, '933531', 'N/A', '1.67'),
(192, '933611', 'N/A', '2.78'),
(193, '934242', 'N/A', '0.84'),
(194, '934424', 'N/A', '1.57'),
(195, '937343', 'N/A', '0.32'),
(196, '937531', 'N/A', '190'),
(197, '937532', 'N/A', '190'),
(198, '938171', 'N/A', '0.9'),
(199, '941520', 'N/A', '22.4'),
(200, '951161', 'N/A', '414.73'),
(201, '952131', 'N/A', '280.01'),
(202, '953170', 'N/A', '313.6'),
(203, '953180', 'N/A', '425.61'),
(204, '953190', 'N/A', '369.6'),
(205, '955180', 'N/A', '413.95'),
(206, '957170', 'N/A', '313.6'),
(207, '957180', 'N/A', '257.6'),
(208, '957190', 'N/A', '425.61'),
(209, '961211', 'N/A', '14.56'),
(210, '961230', 'N/A', '22.96'),
(211, '962110', 'N/A', '100.8'),
(212, '968811', 'N/A', '0.59'),
(213, '291731', 'N/A', '311.11'),
(214, '305111', 'N/A', '61.73'),
(215, '305112', 'N/A', '64.2'),
(216, '305527', 'N/A', '146.19'),
(217, '361012', 'N/A', '208.99'),
(218, '390134', 'N/A', '408.18'),
(219, '390141', 'N/A', '356.16'),
(220, '390143', 'N/A', '380.8'),
(221, '390241', 'N/A', '322.56'),
(222, '390341', 'N/A', '367.36'),
(223, '390731', 'N/A', '244.16'),
(224, '390732', 'N/A', '156.8'),
(225, '392001', 'N/A', '73.14'),
(226, '392061', 'N/A', '1.51'),
(227, '392261', 'N/A', '1.85'),
(228, '393322', 'N/A', '93.16'),
(229, '39566A', 'N/A', '213.25'),
(230, '396434', 'N/A', '10,976.00'),
(231, '394531', 'N/A', '25.09'),
(232, '394561', 'N/A', '0.25'),
(233, '396141', 'N/A', '381.7'),
(234, '450055', 'N/A', '352'),
(235, '399322', 'N/A', '149.6'),
(236, '414132', 'N/A', '1,328.32'),
(237, '43235A', 'N/A', '376.2'),
(238, '510010', 'N/A', '0.14'),
(239, '510171', 'N/A', '1.34'),
(240, '511371', 'N/A', '0.73'),
(241, '512352', 'N/A', '1.06'),
(242, '512361', 'N/A', '1.62'),
(243, '512372', 'N/A', '2.63'),
(244, '525152', 'N/A', '1.45'),
(245, '525153', 'N/A', '4.14'),
(246, '525165', 'N/A', '1.68'),
(247, '525172', 'N/A', '2.24'),
(248, '534312', 'N/A', '2.5'),
(249, '534322', 'N/A', '3.14'),
(250, '545115', 'N/A', '0.28'),
(251, '545120', 'N/A', '0.6'),
(252, '545140', 'N/A', '1.49'),
(253, '546020', 'N/A', '0.39'),
(254, '546050', 'N/A', '0.4'),
(255, '546061', 'N/A', '0.67'),
(256, '546080', 'N/A', '1.23'),
(257, '546110', 'N/A', '0.28'),
(258, '548022', 'N/A', '0.18'),
(259, '549011', 'N/A', '0.81'),
(260, '515130', 'N/A', '0.56'),
(261, '542074', 'N/A', '1.26'),
(262, '562050', 'N/A', '1.38'),
(263, '562060', 'N/A', '2.01'),
(264, '562080', 'N/A', '5.64'),
(265, '562090', 'N/A', '8.65'),
(266, '56209D', 'N/A', '5.49'),
(267, '562091', 'N/A', '14.25'),
(268, '56209F', 'N/A', '4.2'),
(269, '582830', 'N/A', '1.74'),
(270, '582861', 'N/A', '1.74'),
(271, '542100', 'N/A', '0.56'),
(272, '542110', 'N/A', '0.84'),
(273, '542120', 'N/A', '1.01'),
(274, '542180', 'N/A', '2.46'),
(275, '562094', 'N/A', '1.85'),
(276, '562083', 'N/A', '1.57'),
(277, '572011', 'N/A', '9'),
(278, '911142', 'N/A', '0.28'),
(279, '912211', 'N/A', '0.19'),
(280, '914011', 'N/A', '0.48'),
(281, '921041', 'N/A', '4.2'),
(282, '921051', 'N/A', '5.88'),
(283, '921141', 'N/A', '4.2'),
(284, '922021', 'N/A', '9.33'),
(285, '923240', 'N/A', '36.94'),
(286, '923351', 'N/A', '184.66'),
(287, '923451', 'N/A', '322.61'),
(288, '924011', 'N/A', '4.84'),
(289, '961211', 'N/A', '14.56'),
(290, '961230', 'N/A', '22.96'),
(291, '962110', 'N/A', '100.8'),
(292, '920111', 'N/A', '1.9'),
(293, '920121', 'N/A', '1.51'),
(294, '707006', 'N/A', '0'),
(295, '707016', 'N/A', '0'),
(296, '707036', 'N/A', '0'),
(297, '707056', 'N/A', '0'),
(298, '707026', 'N/A', '0');

-- --------------------------------------------------------

--
-- Table structure for table `product_tb`
--

CREATE TABLE `product_tb` (
  `id` int(11) NOT NULL,
  `product_id` varchar(6) DEFAULT NULL,
  `product_name` varchar(60) DEFAULT NULL,
  `uom` varchar(4) DEFAULT NULL,
  `category_id` int(1) DEFAULT NULL,
  `cost` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `product_tb`
--

INSERT INTO `product_tb` (`id`, `product_id`, `product_name`, `uom`, `category_id`, `cost`) VALUES
(1, '10014A', 'Potato KIngdom Fries 1kg/bag', 'KG', 1, '159.04'),
(2, '100421', 'Potato Chips-McCain Original 1.8144kg', 'KG', 1, '184.8'),
(3, '100423', 'Mexican Munchers 86pcs/bag', 'PCS', 1, '5.38'),
(4, '100751', 'Cheddar Munchers-80PC/bag', 'PCS', 1, '4.6'),
(5, '119141', 'OnionRings-MooresBtrd 1.133kg', 'KG', 1, '265.44'),
(6, '131132', 'AngusBeefBurgerPatty-Prime10pc', 'PCS', 1, '47.04'),
(7, '131133', 'AngusBeefBurgerPatty-Primebeef (6 pcs)', 'PCS', 1, '63.22'),
(8, '131432', 'All Beef Franks (Alpha) 7PC/pk', 'PCS', 1, '21.6'),
(9, '131441', 'Classic Beef Mixture - Jaka .960kg', 'KG', 1, '288.17'),
(10, '131631', 'Ground Beef - Monterey', 'KG', 1, '419.12'),
(11, '132230', 'Bonanza Chicken Patty 4pcs/pack', 'PCS', 1, '16.8'),
(12, '132211', 'Chicken Fingers(50PC/pk)', 'PCS', 1, '7.06'),
(13, '132220', 'Chicken Nuggets Bites 500g', 'PACK', 1, '145.6'),
(14, '134341', 'ChickenTurkeyBurger90g 10pc/pk', 'PCS', 1, '22.34'),
(15, '134431', 'Chicken-TurkeyHotdog-15PC', 'PCS', 1, '18.29'),
(16, '141211', 'Fish Fillet Stick Breaded-30pc', 'PCS', 1, '11.39'),
(17, '162153', 'Ice Cream Sweet Corn (40pcs/box)', 'PCS', 1, '12.6'),
(18, '162154', 'Ice Cream Slush Mango 30pcs/box', 'PCS', 1, '16.8'),
(19, '211212', 'Fresh Lettuce-Generic', 'KG', 1, '250'),
(20, '212211', 'FreshTomato-Extrahalf-ripe', 'KG', 1, '200'),
(21, '219001', 'Fresh Onion White 1kg', 'KG', 1, '100'),
(22, '219002', 'Spring Onion Chopped', 'KG', 1, '380.8'),
(23, '259870', 'Barney Bacon Bit', 'KG', 1, '364'),
(24, '261241', 'Sour Cream Dip-Nestle 3.8kg/gal', 'KG', 1, '263.2'),
(25, '265141', 'Margarine-Bakers Best 5kg', 'KG', 1, '145.85'),
(26, '266351', 'Cheese Slice 192pcs/blk', 'PCS', 1, '5.04'),
(27, '266542', 'Chiz Mozza-Heart Shape 10pcs/pack', 'PACK', 1, '196'),
(28, '266741', 'Queso-O Cheese Food', 'KG', 1, '247.8'),
(29, '291731', 'Coney Island Premix Indofine 900g/pack', 'KG', 1, '311.11'),
(30, '305112', 'Popcorn Kernel-Morrison22.68kg/sack', 'KG', 1, '64.2'),
(31, '305401', 'Tortilla Chips-WhtCornCPln500g', 'KG', 1, '206.08'),
(32, '305421', 'Tortilla Chips-WhtCornC NC565g', 'KG', 1, '227.96'),
(33, '305431', 'Tortilla Chips-WhtCornC WC565g', 'KG', 1, '231.93'),
(34, '305441', 'Tortilla Chips-WhtCornC SC565g', 'KG', 1, '218.05'),
(35, '305451', 'Tortilla Chips-WhtCornC TB565g', 'KG', 1, '224'),
(36, '326343', 'Peanut Trail Mix (1kg/pack)', 'KG', 1, '672'),
(37, '350712', 'TofuChips-w/blk&whtSSds.250kg', 'KG', 1, '369.6'),
(38, '361012', 'All Purpose Cream Magnolia250ml/pk', 'KG', 1, '208.99'),
(39, '381232', 'SandwichBun-RoundWhtnseed6pc', 'PCS', 1, '7.28'),
(40, '381252', 'SandwichBun-HotdogWhtnseed6pc', 'PCS', 1, '7.28'),
(41, '382532', 'Churros 60pcs/pack', 'PCS', 1, '5.04'),
(42, '389273', 'Taters Dark Chocolate 55% - Plain 27g', 'PCS', 1, '57.29'),
(43, '390100', 'Butter Seasoning 1kg', 'KG', 1, '246.4'),
(44, '390111', 'Flavorites-Nacho Cheese(100g)', 'PCS', 1, '47.04'),
(45, '390113', 'Flavorites-White Cheddar(100g)', 'PCS', 1, '48.16'),
(46, '390134', 'Cheese Sauce Powder 900g/pk', 'KG', 1, '408.18'),
(47, '390141', 'Flavorings-Nacho Cheese 3kg/bag', 'KG', 1, '386.4'),
(48, '390143', 'Flavorings-WhiteCheddar3kg/bag', 'KG', 1, '412.16'),
(49, '390200', 'Flavorings - Honey Butter Seasoning 1kg', 'KG', 1, '336'),
(50, '390201', 'Kebab Seasoning', 'KG', 1, '273.46'),
(51, '390211', 'Flavorites-SourCrm&Chvs(100g)', 'PCS', 1, '40.88'),
(52, '390241', 'Flavorings-SourCrm&Chvs3kg/bag', 'KG', 1, '322.56'),
(53, '390311', 'Flavorites-Texan Bbq(100g)', 'PCS', 1, '45.92'),
(54, '390341', 'Flavorings-TexanBBQ 3kg/bag', 'KG', 1, '367.36'),
(55, '390464', 'Flavorings - Chili Seasoning 1kg', 'KG', 1, '537.6'),
(56, '390511', 'Flavorites-Wasabi(100g)', 'PCS', 1, '49'),
(57, '390531', 'Flavoring-Wasabi 1kg', 'KG', 1, '463.44'),
(58, '390731', 'Flavoring-SweetGlzCaramel 1kg', 'KG', 1, '257.6'),
(59, '390732', 'Flavoring-StrawberryGlaze 1kg', 'KG', 1, '188.16'),
(60, '391002', 'Mushroom Gravy Mix', 'KG', 1, '403.2'),
(61, '391411', 'Molinera Crushed Tomatoes2.5kg/can', 'KG', 1, '73.92'),
(62, '391631', 'Barbecue Sauce-GCHI 1kg', 'KG', 1, '145.6'),
(63, '391632', 'El Mexicano Nacho Cheese Sauce 425g', 'PCS', 1, '168'),
(64, '392001', 'DM Original Blend Ketchup 1kg', 'KG', 1, '73.14'),
(65, '392061', 'Catsup-generic sachets', 'PCS', 1, '1.55'),
(66, '392241', 'Mayonnaise-Golden Valley 3.5kg/gal', 'KG', 1, '88'),
(67, '392261', 'Mayonnaise-generic sachets', 'PCS', 1, '1.87'),
(68, '392441', 'Prepared Mustard-McC 3.4kg', 'KG', 1, '109.44'),
(69, '393322', 'Marca Leon Oil-Frito Plus Palm Olein 17kg/tin', 'KG', 1, '60.02'),
(70, '394531', 'Iodized Salt 1kg', 'KG', 1, '23.95'),
(71, '394561', 'Salt-generic sachet', 'PCS', 1, '0.25'),
(72, '396141', 'Vanilla Extract Flvr-McC3.5kg', 'KG', 1, '381.7'),
(73, '396432', 'Betacarotene 30% FS 1L-Four Corners', 'KG', 1, '14,174.72'),
(74, '397630', 'Chili Powder .05kg/pk', 'KG', 1, '414.4'),
(75, '397652', 'J&Y Red Cayenne 30g', 'KG', 1, '980.52'),
(76, '397730', 'Ground Black Pepper', 'KG', 1, '619'),
(77, '398311', 'Mushrooms-JollyChampignons400g', 'KG', 1, '115.5'),
(78, '399291', 'SweetPickle Relish(RAM)3.7/gal', 'KG', 1, '120.69'),
(79, '399322', 'Jalape?oPeppers-Generic 2.8kg/can', 'KG', 1, '149.6'),
(80, '399323', 'La Costena Red Salsa Dip 453g', 'BTLS', 1, '168.56'),
(81, '401061', 'CO2-20lb 9.072kg', 'KG', 1, '28.4'),
(82, '401151', 'Sodas-BIB Pepsi Cola 23.60kg', 'KG', 1, '269.78'),
(83, '401152', 'Sodas-BIB Pepsi Light 18.925kg', 'KG', 1, '269.78'),
(84, '401153', 'Sodas-BIB Pepsi Max 24.23kg', 'KG', 1, '269.78'),
(85, '401251', 'Sodas-BIB 7-Up 23.69kg', 'KG', 1, '269.78'),
(86, '401351', 'Sodas-BIB Mug Rootbeer 23.20kg', 'KG', 1, '269.78'),
(87, '401451', 'Sodas-BIB Mirinda Orange 23.44kg', 'KG', 1, '269.78'),
(88, '401551', 'Sodas-BIB Mountain Dew 19.80kg', 'KG', 1, '269.78'),
(89, '402123', 'Sodas Pepsi Max - PET Bottle 600ml', 'BTLS', 1, '28.28'),
(90, '402124', 'Sodas Pepsi Cola - PET Bottle 600ml', 'BTLS', 1, '28.28'),
(91, '402192', 'Sodas Mountain Dew - PET Bottle 600ml', 'BTLS', 1, '28.28'),
(92, '402193', 'Sodas Mug Rootbeer - PET Bottle 600ml', 'BTLS', 1, '28.28'),
(93, '402194', 'Sodas- Mirinda Orange PET Bottle 600ml', 'BTLS', 1, '28.28'),
(94, '402195', 'Sodas 7Up PET Bottle 600ml', 'BTLS', 1, '28.28'),
(95, '403111', 'Sodas - Pepsi 330ml Can', 'PCS', 1, '24.64'),
(96, '403112', 'Sodas - Pepsi Light 330ml Can', 'PCS', 1, '24.64'),
(97, '403113', 'Sodas - Pepsi Max 330ml Can', 'PCS', 1, '24.64'),
(98, '403171', 'Sodas - Mtdew 330ml Can', 'PCS', 1, '24.64'),
(99, '403212', 'Sodas - 7-Up 330ml Can', 'PCS', 1, '24.64'),
(100, '45005A', 'Yogurt Yowell Unsweetened', 'KG', 1, '156.81'),
(101, '412521', 'Buko Juice LipaFrshBottled500ml', 'BTLS', 1, '52.64'),
(102, '414132', 'Lemonade SF Ferna 1kg', 'KG', 1, '1,328.32'),
(103, '424121', 'Ice Tea-Lipton Lemon 640g-pk', 'KG', 1, '310.63'),
(104, '424233', 'Green Tea SF Ferna 1kg', 'KG', 1, '1,747.20'),
(105, '460224', 'Drinking Water - Aquafina 500ml', 'BTLS', 1, '10.73'),
(106, '460264', 'Drinking Water - Aquafina 350ml', 'BTLS', 1, '8.06'),
(107, '469131', 'Ice Tube 5kg/bag', 'KG', 1, '3.4'),
(108, '502370', 'EcoBag Sando Large (Red) Blank', 'PCS', 1, '5.4'),
(109, '510010', 'Glacine Paper', 'PCS', 1, '0.14'),
(110, '510171', 'PaperWaxSndwchWrapPlain15x15', 'PCS', 1, '1.34'),
(111, '511110', 'HotdogPouch-GPPaperPln', 'PCS', 1, '0.78'),
(112, '511371', 'Paper Pouch-Large Fry Bag', 'PCS', 1, '0.73'),
(113, '512352', 'Paper Bag-Taters SOS Jr 2017', 'PCS', 1, '1.06'),
(114, '512361', 'Paper Bag-Taters SOS#4 (2017)', 'PCS', 1, '1.62'),
(115, '512372', 'Paper Bag-Taters SOS#8 2017', 'PCS', 1, '2.63'),
(116, '515130', 'Paper Cup-Courtesy 6.5oz', 'PCS', 1, '0.56'),
(117, '525152', 'Paper Cup - Taters 12oz 2017 design', 'PCS', 1, '1.45'),
(118, '525153', 'Paper Cup 32oz 2017 Design', 'PCS', 1, '4.14'),
(119, '525165', 'Paper Cup Taters 16 oz 2017', 'PCS', 1, '1.68'),
(120, '525172', 'Paper Cup-Taters 22oz 2017', 'PCS', 1, '2.24'),
(121, '527110', 'Plastic White-Spoon', 'PCS', 1, '1.34'),
(122, '527120', 'Plastic White-Fork', 'PCS', 1, '1.34'),
(123, '527140', 'Plastic White-Knife', 'PCS', 1, '1.34'),
(124, '527360', 'Plastic Strirrer-Red', 'PCS', 1, '0.2'),
(125, '534160', 'Taters Hotdog Box (2020)', 'PCS', 1, '2.3'),
(126, '534312', 'Burger Box Printed-2017', 'PCS', 1, '2.61'),
(127, '534322', 'Large Tray Box Printed 2017', 'PCS', 1, '3.14'),
(128, '538311', 'Paper Cup Holder Printed', 'PCS', 1, '4.37'),
(129, '539150', 'Eco Drink Holder 7.5cm (White Hole)', 'PCS', 1, '3.26'),
(130, '539151', 'Eco Drink Holder 6cm (Red Hole)', 'PCS', 1, '3.26'),
(131, '539160', 'Paper Cup Holder 4hole', 'PCS', 1, '4.7'),
(132, '53A9A5', 'Taters Box Strap (2.7\" x 10.5\")', 'PCS', 1, '1.06'),
(133, '542074', 'Plastic bag 20x30 thick(pop)', 'PCS', 1, '1.26'),
(134, '542100', 'Biodegradable Sando Bag - Small', 'PCS', 1, '0.56'),
(135, '542110', 'Biodegradable Sando Bag - Medium', 'PCS', 1, '0.84'),
(136, '542120', 'Biodegradable Sando Bag - Large', 'PCS', 1, '1.01'),
(137, '542180', 'Biodegradable Sando Bag - Extra large', 'PCS', 1, '2.46'),
(138, '545115', 'Plastic Sauce CupWht30ml', 'PCS', 1, '0.28'),
(139, '545120', 'Plastic Sauce CupWht44ml', 'PCS', 1, '0.6'),
(140, '545140', 'Paper Sauce Cup 3oz', 'PCS', 1, '1.49'),
(141, '546020', 'Plastic SauceLidclear44ml', 'PCS', 1, '0.39'),
(142, '546030', 'Pepsi Tumbler w/ Lid & Straw', 'PCS', 1, '0'),
(143, '546050', 'Plastic Lid 12oz cup', 'PCS', 1, '0.4'),
(144, '546061', 'Plastic Lid 16/22oz cup', 'PCS', 1, '0.67'),
(145, '546080', 'Plastic Lid 32oz cup Yihrong', 'PCS', 1, '1.23'),
(146, '546110', 'Plastic SauceLidclear30ml', 'PCS', 1, '0.28'),
(147, '548022', 'Plastic Straw-Transparent', 'PCS', 1, '0.18'),
(148, '549011', 'Paper Straw Coated White', 'PCS', 1, '0.81'),
(149, '554010', 'Plastic Microwaveable Tub-P250', 'PCS', 1, '3.52'),
(150, '556010', 'Plastic Microwaveable Lid-250', 'PCS', 1, '1.71'),
(151, '562020', 'Plastic Resealable Bag - Plain (Sunglobe)', 'PCS', 1, '0'),
(152, '562050', 'PlasticResealableBagZiplock#7', 'PCS', 1, '1.38'),
(153, '562060', 'PlasticResealableBagZiplock#8', 'PCS', 1, '2.01'),
(154, '562070', 'PlasticResealableZiplock#9', 'PCS', 1, '2.35'),
(155, '562080', 'Plastic Resealable Bag Ziplock#11', 'PCS', 1, '5.64'),
(156, '562083', 'Ziplock Standing Plain 11x18', 'PCS', 1, '1.57'),
(157, '562090', 'Ziplock Family Pack', 'PCS', 1, '8.65'),
(158, '562091', 'Ziplock Party Pack', 'PCS', 1, '14.25'),
(159, '562092', 'Ziplock Mini Pack - Taters', 'PCS', 1, '5.54'),
(160, '562094', 'Ziplock Standing Plain 12x20', 'PCS', 1, '1.85'),
(161, '582830', 'Paper Bag-Brown TakeOut#3', 'PCS', 1, '1.74'),
(162, '582860', 'Paper Bag-Brown Regular#12', 'PCS', 1, '0.58'),
(163, '582861', 'Paper Bag-Brown Plain#8', 'PCS', 1, '0.65'),
(164, '582870', 'Paper Bag-Brown Regular#16', 'PCS', 1, '2.04'),
(165, '584150', 'Taters Take-Out Box w/ Cover - Small (Foldcote#12)', 'PCS', 1, '1.85'),
(166, '584160', 'Taters Take-Out Box w/ Cover - Medium (Foldcote#15)', 'PCS', 1, '2.97'),
(167, '591010', 'Plastic Bag- 5x7 pln adhesive', 'PCS', 1, '0.51'),
(168, '592321', 'Plastic Resealable Bag-Printed', 'PCS', 1, '14.11'),
(169, '789015', 'Popclub Renewal Voucher C2S160', 'PCS', 1, '1.68'),
(170, '911142', 'Paper Table Napkins - Taters Printed prefolded 13\" x 13\" 200', 'PCS', 1, '0.28'),
(171, '912211', 'Paper Towel - Interfolded Regular (Livi) 175', 'PCS', 1, '0.19'),
(172, '914011', 'Plastic Gloves - Disposable transp. Sinmag (100s)', 'PCS', 1, '0.48'),
(173, '916112', 'Bamboo Stick', 'PCS', 1, '0.29'),
(174, '920111', 'Silica Gel Solupak 5g', 'PCS', 1, '1.9'),
(175, '921041', 'Trash Bag - Transparent XL (10PC/roll)', 'PCS', 1, '4.2'),
(176, '921051', 'Trash Bag - Transparent XXL (10PC/roll)', 'PCS', 1, '5.88'),
(177, '921141', 'Trash Bag - Black XL (10PC/roll)', 'PCS', 1, '4.2'),
(178, '922021', 'Hand Towel - Kau Thong (12s)', 'PCS', 1, '9.33'),
(179, '923240', 'Detergent Powder - All-purpose 1kg', 'KGS', 1, '36.94'),
(180, '923351', 'Deogen - Suma Total 5L', 'KGS', 1, '193.89'),
(181, '923451', 'Divoplaq - Suma Total N3 5L', 'KGS', 1, '322.61'),
(182, '924011', 'Antibac - Diverseylever', 'PCS', 1, '4.84'),
(183, '931000', 'Sticker Generic Branding (T) 12/sheet', 'PCS', 1, '0.9'),
(184, '931050', 'Sticker Flavor (288pcs/sheet)', 'PCS', 1, '0.17'),
(185, '931211', 'Sticker-Taters Blank Label', 'PCS', 1, '0.84'),
(186, '931411', 'Sticker-Taters\"Popcorn\" label', 'PCS', 1, '0.84'),
(187, '933121', 'Gift Certificate-Taters P100', 'PCS', 1, '2.36'),
(188, '933501', 'Popclub Welcome Kit', 'PCS', 1, '2.35'),
(189, '933502', 'Popclub 2017 Membership Card', 'PCS', 1, '8.96'),
(190, '933511', 'Gift Card 100 (2016)', 'PCS', 1, '1.9'),
(191, '933531', 'Taters Gift Card P200', 'PCS', 1, '1.67'),
(192, '933611', 'Gift Card Holder (2016)', 'PCS', 1, '2.78'),
(193, '934242', 'Taters Gift Tag', 'PCS', 1, '0.84'),
(194, '934424', 'Flavorites Card', 'PCS', 1, '1.57'),
(195, '937343', 'Ribbon Curly (Green) 106pcs/roll', 'PCS', 1, '0.32'),
(196, '937531', 'Taters Babies Doll-Boy', 'PCS', 1, '190'),
(197, '937532', 'Taters Babies Doll-Girl', 'PCS', 1, '190'),
(198, '938171', 'Sticker Product Instruction  (24pcs/sheet)', 'PCS', 1, '0.9'),
(199, '941520', 'Magnetic Swipe Card-Blank', 'PCS', 1, '22.4'),
(200, '951161', 'Infra Bulb - Kandolite 250W', 'PCS', 1, '414.73'),
(201, '952131', 'Bulb (Popc Mach) - 100W', 'PCS', 1, '280.01'),
(202, '953170', 'Fluorescent - T5 14W', 'PCS', 1, '313.6'),
(203, '953180', 'Fluorescent - T5 28W', 'PCS', 1, '425.61'),
(204, '953190', 'Fluorescent - T5 21W', 'PCS', 1, '369.6'),
(205, '955180', 'Fluorescent - T5 21W (Warm Light)', 'PCS', 1, '413.95'),
(206, '957170', 'Fluorescent - T5 14W (Warm Light)', 'PCS', 1, '313.6'),
(207, '957180', 'Fluorescent - T5 8W (Warm Light)', 'PCS', 1, '257.6'),
(208, '957190', 'Fluorescent - T5 28W (Warm Light)', 'PCS', 1, '425.61'),
(209, '961211', 'Journal Tape (POS) 1ply 76mm x70mm', 'PCS', 1, '14.56'),
(210, '961230', 'Journal Tape (POS) 2ply 76mmx70mm', 'PCS', 1, '22.96'),
(211, '962110', 'POS Ribbon - ERC 38 black (generic)', 'PCS', 1, '100.8'),
(212, '968811', 'Ziplock Card with Adhesive Tape', 'PCS', 1, '0.59'),
(213, '291731', 'Coney Island Premix Indofine 900g/pack', 'KGS', 2, '311.11'),
(214, '305111', 'Popcorn Kernel-Preferred 22.68kg/sack', 'KGS', 2, '61.73'),
(215, '305112', 'Popcorn Kernel-Morrison22.68kg/sack', 'KGS', 2, '64.2'),
(216, '305527', 'Cornachos-Yellow Salted Corn Pln 475g/bag', 'KGS', 2, '146.19'),
(217, '361012', 'All Purpose Cream Magnolia250ml/pk', 'KGS', 2, '208.99'),
(218, '390134', 'Cheese Sauce Powder 900g/pk', 'KGS', 2, '408.18'),
(219, '390141', 'Flavorings-Nacho Cheese 3kg/bag', 'KGS', 2, '356.16'),
(220, '390143', 'Flavorings-WhiteCheddar3kg/bag', 'KGS', 2, '380.8'),
(221, '390241', 'Flavorings-SourCrm&Chvs3kg/bag', 'KGS', 2, '322.56'),
(222, '390341', 'Flavorings-TexanBBQ 3kg/bag', 'KGS', 2, '367.36'),
(223, '390731', 'Flavoring-SweetGlzCaramel 1kg', 'KGS', 2, '244.16'),
(224, '390732', 'Flavoring-StrawberryGlaze 1kg', 'KGS', 2, '156.8'),
(225, '392001', 'DM Original Blend Ketchup 1kg', 'KGS', 2, '73.14'),
(226, '392061', 'Catsup-generic sachets', 'PCS', 2, '1.51'),
(227, '392261', 'Mayonnaise-generic sachets', 'PCS', 2, '1.85'),
(228, '393322', 'Marca Leon Oil-Frito Plus Palm Olein 17kg/tin', 'KGS', 2, '93.16'),
(229, '39566A', 'Coffeemate Creamer - Nestle 450g', 'KGS', 2, '213.25'),
(230, '396434', 'Beta Carotene - Lucarotin 30 Sun 2kg/tin', 'KGS', 2, '10,976.00'),
(231, '394531', 'Iodized Salt 1kg', 'KGS', 2, '25.09'),
(232, '394561', 'Salt-generic sachet', 'PCS', 2, '0.25'),
(233, '396141', 'Vanilla Extract Flvr-McC3.5kg', 'KGS', 2, '381.7'),
(234, '450055', 'Yogurt Syrup', 'GAL', 2, '352'),
(235, '399322', 'Jalape?oPeppers-Generic 2.8kg/can', 'KGS', 2, '149.6'),
(236, '414132', 'Lemonade SF Ferna 1kg', 'KGS', 2, '1,328.32'),
(237, '43235A', 'Benguet Ground Coffee 1kg', 'KGS', 2, '376.2'),
(238, '510010', 'Glacine Paper', 'PCS', 2, '0.14'),
(239, '510171', 'PaperWaxSndwchWrapPlain15x15', 'PCS', 2, '1.34'),
(240, '511371', 'Paper Pouch-Large Fry Bag', 'PCS', 2, '0.73'),
(241, '512352', 'Paper Bag-Taters SOS Jr 2017', 'PCS', 2, '1.06'),
(242, '512361', 'Paper Bag-Taters SOS#4 (2017)', 'PCS', 2, '1.62'),
(243, '512372', 'Paper Bag-Taters SOS#8 2017', 'PCS', 2, '2.63'),
(244, '525152', 'Paper Cup - Taters 12oz 2017 design', 'PCS', 2, '1.45'),
(245, '525153', 'Paper Cup 32oz 2017 Design', 'PCS', 2, '4.14'),
(246, '525165', 'Paper Cup-Taters 16oz 2017', 'PCS', 2, '1.68'),
(247, '525172', 'Paper Cup-Taters 22oz 2017', 'PCS', 2, '2.24'),
(248, '534312', 'Burger Box Printed-2017', 'PCS', 2, '2.5'),
(249, '534322', 'Large Tray Box Printed2017', 'PCS', 2, '3.14'),
(250, '545115', 'Plastic Sauce CupWht30ml', 'PCS', 2, '0.28'),
(251, '545120', 'Plastic Sauce CupWht44ml', 'PCS', 2, '0.6'),
(252, '545140', 'Paper Sauce Cup 3oz', 'PCS', 2, '1.49'),
(253, '546020', 'Plastic SauceLidclear44ml', 'PCS', 2, '0.39'),
(254, '546050', 'Plastic Lid 12oz cup', 'PCS', 2, '0.4'),
(255, '546061', 'Plastic Lid 16/22oz cup', 'PCS', 2, '0.67'),
(256, '546080', 'Plastic Lid 32oz cup Yihrong', 'PCS', 2, '1.23'),
(257, '546110', 'Plastic SauceLidclear30ml', 'PCS', 2, '0.28'),
(258, '548022', 'Plastic Straw-Transparent 1kg', 'PCS', 2, '0.18'),
(259, '549011', 'Paper Straw Coated White', 'PCS', 2, '0.81'),
(260, '515130', 'Paper Cup-Courtesy 6.5oz', 'PCS', 2, '0.56'),
(261, '542074', 'Plastic bag 20x30 thick(pop)', 'PCS', 2, '1.26'),
(262, '562050', 'PlasticResealableBagZiplock#7', 'PCS', 2, '1.38'),
(263, '562060', 'PlasticResealableBagZiplock#8', 'PCS', 2, '2.01'),
(264, '562080', 'Plastic Resealable Bag Ziplock#11', 'PCS', 2, '5.64'),
(265, '562090', 'Ziplock Family Pack', 'PCS', 2, '8.65'),
(266, '56209D', 'Ziplock Tall - Tortilla Chips Packaging', 'PCS', 2, '5.49'),
(267, '562091', 'Ziplock Party Pack', 'PCS', 2, '14.25'),
(268, '56209F', 'Taters Mini Ziplock (matte and Glossy)', 'PCS', 2, '4.2'),
(269, '582830', 'Paper Bag-Brown TakeOut#3', 'PCS', 2, '1.74'),
(270, '582861', 'Paper Bag-Brown TakeOut#8', 'PCS', 2, '1.74'),
(271, '542100', 'Biodegradable Sando Bags - Small', 'PCS', 2, '0.56'),
(272, '542110', 'Biodegradable Sando Bags - Medium', 'PCS', 2, '0.84'),
(273, '542120', 'Biodegradable Sando Bags - Large', 'PCS', 2, '1.01'),
(274, '542180', 'Biodegradable Sando Bag - Extra large', 'PCS', 2, '2.46'),
(275, '562094', 'Ziplock Standing Plain 12x20', 'PCS', 2, '1.85'),
(276, '562083', 'Ziplock Standing Plain 11x18', 'PCS', 2, '1.57'),
(277, '572011', 'Juice (Square 500ml) PET Bottle', 'PCS', 2, '9'),
(278, '911142', 'Paper Table Napkins - Taters Printed prefolded 13\" x 13\"', 'PCS', 2, '0.28'),
(279, '912211', 'Paper Towel - Interfolded Regular (Livi)', 'PCS', 2, '0.19'),
(280, '914011', 'Plastic Gloves - Disposable transp. Sinmag (100s)', 'PCS', 2, '0.48'),
(281, '921041', 'Trash Bag - Transparent XL (10pcs/roll)', 'PCS', 2, '4.2'),
(282, '921051', 'Trash Bag - Transparent XXL (10pcs/roll)', 'PCS', 2, '5.88'),
(283, '921141', 'Trash Bag - Black XL (10PC/roll)', 'PCS', 2, '4.2'),
(284, '922021', 'Hand Towel - Kau Thong (12s)', 'PCS', 2, '9.33'),
(285, '923240', 'Detergent Powder - All-purpose 1kg', 'KGS', 2, '36.94'),
(286, '923351', 'Deogen - Suma Total 5L', 'KGS', 2, '184.66'),
(287, '923451', 'Divoplaq - Suma Total N3 5L', 'KGS', 2, '322.61'),
(288, '924011', 'Antibac - Diverseylever', 'PCS', 2, '4.84'),
(289, '961211', 'Journal Tape (POS) 1ply 76mm x70mm', 'PCS', 2, '14.56'),
(290, '961230', 'Journal Tape (POS) 2ply 76mmx70mm', 'PCS', 2, '22.96'),
(291, '962110', 'POS Ribbon - ERC 38 black', 'PCS', 2, '100.8'),
(292, '920111', 'Silica Gel Solupak 5g', 'PCS', 2, '1.9'),
(293, '920121', 'Silica Gel Solupak 2g', 'PCS', 2, '1.51'),
(294, '707006', 'Tofu Chips-Natural Tall', 'PCS', 2, '0'),
(295, '707016', 'Tofu Chips-Nacho Cheese Tall', 'PCS', 2, '0'),
(296, '707036', 'Tofu Chips-Texan BBQ Tall', 'PCS', 2, '0'),
(297, '707056', 'Tofu Chips- White Cheddar Tall', 'PCS', 2, '0'),
(298, '707026', 'Tofu Chips- Sour Cream Tall', 'PCS', 2, '0');

-- --------------------------------------------------------

--
-- Table structure for table `product_tb_backup`
--

CREATE TABLE `product_tb_backup` (
  `id` int(11) NOT NULL,
  `product_id` varchar(6) DEFAULT NULL,
  `product_name` varchar(60) DEFAULT NULL,
  `uom` varchar(4) DEFAULT NULL,
  `category_id` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `product_tb_backup`
--

INSERT INTO `product_tb_backup` (`id`, `product_id`, `product_name`, `uom`, `category_id`) VALUES
(1, '10014A', 'Potato KIngdom Fries 1kg/bag', 'KG', 1),
(2, '100421', 'Potato Chips-McCain Original 1.8144kg', 'KG', 1),
(3, '100423', 'Mexican Munchers 86pcs/bag', 'PCS', 1),
(4, '100751', 'Cheddar Munchers-80PC/bag', 'PCS', 1),
(5, '119141', 'OnionRings-MooresBtrd 1.133kg', 'KG', 1),
(6, '131132', 'AngusBeefBurgerPatty-Prime10pc', 'PCS', 1),
(7, '131133', 'AngusBeefBurgerPatty-Primebeef (6 pcs)', 'PCS', 1),
(8, '131432', 'All Beef Franks (Alpha) 7PC/pk', 'PCS', 1),
(9, '131441', 'Classic Beef Mixture - Jaka .960kg', 'KG', 1),
(10, '131631', 'Ground Beef - Monterey', 'KG', 1),
(11, '132230', 'Bonanza Chicken Patty 4pcs/pack', 'PCS', 1),
(12, '132211', 'Chicken Fingers(50PC/pk)', 'PCS', 1),
(13, '132220', 'Chicken Nuggets Bites 500g', 'PACK', 1),
(14, '134341', 'ChickenTurkeyBurger90g 10pc/pk', 'PCS', 1),
(15, '134431', 'Chicken-TurkeyHotdog-15PC', 'PCS', 1),
(16, '141211', 'Fish Fillet Stick Breaded-30pc', 'PCS', 1),
(17, '162153', 'Ice Cream Sweet Corn (40pcs/box)', 'PCS', 1),
(18, '162154', 'Ice Cream Slush Mango 30pcs/box', 'PCS', 1),
(19, '211212', 'Fresh Lettuce-Generic', 'KG', 1),
(20, '212211', 'FreshTomato-Extrahalf-ripe', 'KG', 1),
(21, '219001', 'Fresh Onion White 1kg', 'KG', 1),
(22, '219002', 'Spring Onion Chopped', 'KG', 1),
(23, '259870', 'Barney Bacon Bit', 'KG', 1),
(24, '261241', 'Sour Cream Dip-Nestle 3.8kg/gal', 'KG', 1),
(25, '265141', 'Margarine-Bakers Best 5kg', 'KG', 1),
(26, '266351', 'Cheese Slice 192pcs/blk', 'PCS', 1),
(27, '266542', 'Chiz Mozza-Heart Shape 10pcs/pack', 'PACK', 1),
(28, '266741', 'Queso-O Cheese Food', 'KG', 1),
(29, '291731', 'Coney Island Premix Indofine 900g/pack', 'KG', 1),
(30, '305112', 'Popcorn Kernel-Morrison22.68kg/sack', 'KG', 1),
(31, '305401', 'Tortilla Chips-WhtCornCPln500g', 'KG', 1),
(32, '305421', 'Tortilla Chips-WhtCornC NC565g', 'KG', 1),
(33, '305431', 'Tortilla Chips-WhtCornC WC565g', 'KG', 1),
(34, '305441', 'Tortilla Chips-WhtCornC SC565g', 'KG', 1),
(35, '305451', 'Tortilla Chips-WhtCornC TB565g', 'KG', 1),
(36, '326343', 'Peanut Trail Mix (1kg/pack)', 'KG', 1),
(37, '350712', 'TofuChips-w/blk&whtSSds.250kg', 'KG', 1),
(38, '361012', 'All Purpose Cream Magnolia250ml/pk', 'KG', 1),
(39, '381232', 'SandwichBun-RoundWhtnseed6pc', 'PCS', 1),
(40, '381252', 'SandwichBun-HotdogWhtnseed6pc', 'PCS', 1),
(41, '382532', 'Churros 60pcs/pack', 'PCS', 1),
(42, '389273', 'Taters Dark Chocolate 55% - Plain 27g', 'PCS', 1),
(43, '390100', 'Butter Seasoning 1kg', 'KG', 1),
(44, '390111', 'Flavorites-Nacho Cheese(100g)', 'PCS', 1),
(45, '390113', 'Flavorites-White Cheddar(100g)', 'PCS', 1),
(46, '390134', 'Cheese Sauce Powder 900g/pk', 'KG', 1),
(47, '390141', 'Flavorings-Nacho Cheese 3kg/bag', 'KG', 1),
(48, '390143', 'Flavorings-WhiteCheddar3kg/bag', 'KG', 1),
(49, '390200', 'Flavorings - Honey Butter Seasoning 1kg', 'KG', 1),
(50, '390201', 'Kebab Seasoning', 'KG', 1),
(51, '390211', 'Flavorites-SourCrm&Chvs(100g)', 'PCS', 1),
(52, '390241', 'Flavorings-SourCrm&Chvs3kg/bag', 'KG', 1),
(53, '390311', 'Flavorites-Texan Bbq(100g)', 'PCS', 1),
(54, '390341', 'Flavorings-TexanBBQ 3kg/bag', 'KG', 1),
(55, '390464', 'Flavorings - Chili Seasoning 1kg', 'KG', 1),
(56, '390511', 'Flavorites-Wasabi(100g)', 'PCS', 1),
(57, '390531', 'Flavoring-Wasabi 1kg', 'KG', 1),
(58, '390731', 'Flavoring-SweetGlzCaramel 1kg', 'KG', 1),
(59, '390732', 'Flavoring-StrawberryGlaze 1kg', 'KG', 1),
(60, '391002', 'Mushroom Gravy Mix', 'KG', 1),
(61, '391411', 'Molinera Crushed Tomatoes2.5kg/can', 'KG', 1),
(62, '391631', 'Barbecue Sauce-GCHI 1kg', 'KG', 1),
(63, '391632', 'El Mexicano Nacho Cheese Sauce 425g', 'PCS', 1),
(64, '392001', 'DM Original Blend Ketchup 1kg', 'KG', 1),
(65, '392061', 'Catsup-generic sachets', 'PCS', 1),
(66, '392241', 'Mayonnaise-Golden Valley 3.5kg/gal', 'KG', 1),
(67, '392261', 'Mayonnaise-generic sachets', 'PCS', 1),
(68, '392441', 'Prepared Mustard-McC 3.4kg', 'KG', 1),
(69, '393322', 'Marca Leon Oil-Frito Plus Palm Olein 17kg/tin', 'KG', 1),
(70, '394531', 'Iodized Salt 1kg', 'KG', 1),
(71, '394561', 'Salt-generic sachet', 'PCS', 1),
(72, '396141', 'Vanilla Extract Flvr-McC3.5kg', 'KG', 1),
(73, '396432', 'Betacarotene 30% FS 1L-Four Corners', 'KG', 1),
(74, '397630', 'Chili Powder .05kg/pk', 'KG', 1),
(75, '397652', 'J&Y Red Cayenne 30g', 'KG', 1),
(76, '397730', 'Ground Black Pepper', 'KG', 1),
(77, '398311', 'Mushrooms-JollyChampignons400g', 'KG', 1),
(78, '399291', 'SweetPickle Relish(RAM)3.7/gal', 'KG', 1),
(79, '399322', 'Jalape?oPeppers-Generic 2.8kg/can', 'KG', 1),
(80, '399323', 'La Costena Red Salsa Dip 453g', 'BTLS', 1),
(81, '401061', 'CO2-20lb 9.072kg', 'KG', 1),
(82, '401151', 'Sodas-BIB Pepsi Cola 23.60kg', 'KG', 1),
(83, '401152', 'Sodas-BIB Pepsi Light 18.925kg', 'KG', 1),
(84, '401153', 'Sodas-BIB Pepsi Max 24.23kg', 'KG', 1),
(85, '401251', 'Sodas-BIB 7-Up 23.69kg', 'KG', 1),
(86, '401351', 'Sodas-BIB Mug Rootbeer 23.20kg', 'KG', 1),
(87, '401451', 'Sodas-BIB Mirinda Orange 23.44kg', 'KG', 1),
(88, '401551', 'Sodas-BIB Mountain Dew 19.80kg', 'KG', 1),
(89, '402123', 'Sodas Pepsi Max - PET Bottle 600ml', 'BTLS', 1),
(90, '402124', 'Sodas Pepsi Cola - PET Bottle 600ml', 'BTLS', 1),
(91, '402192', 'Sodas Mountain Dew - PET Bottle 600ml', 'BTLS', 1),
(92, '402193', 'Sodas Mug Rootbeer - PET Bottle 600ml', 'BTLS', 1),
(93, '402194', 'Sodas- Mirinda Orange PET Bottle 600ml', 'BTLS', 1),
(94, '402195', 'Sodas 7Up PET Bottle 600ml', 'BTLS', 1),
(95, '403111', 'Sodas - Pepsi 330ml Can', 'PCS', 1),
(96, '403112', 'Sodas - Pepsi Light 330ml Can', 'PCS', 1),
(97, '403113', 'Sodas - Pepsi Max 330ml Can', 'PCS', 1),
(98, '403171', 'Sodas - Mtdew 330ml Can', 'PCS', 1),
(99, '403212', 'Sodas - 7-Up 330ml Can', 'PCS', 1),
(100, '45005A', 'Yogurt Yowell Unsweetened', 'KG', 1),
(101, '412521', 'Buko Juice LipaFrshBottled500ml', 'BTLS', 1),
(102, '414132', 'Lemonade SF Ferna 1kg', 'KG', 1),
(103, '424121', 'Ice Tea-Lipton Lemon 640g-pk', 'KG', 1),
(104, '424233', 'Green Tea SF Ferna 1kg', 'KG', 1),
(105, '460224', 'Drinking Water - Aquafina 500ml', 'BTLS', 1),
(106, '460264', 'Drinking Water - Aquafina 350ml', 'BTLS', 1),
(107, '469131', 'Ice Tube 5kg/bag', 'KG', 1),
(108, '502370', 'EcoBag Sando Large (Red) Blank', 'PCS', 1),
(109, '510010', 'Glacine Paper', 'PCS', 1),
(110, '510171', 'PaperWaxSndwchWrapPlain15x15', 'PCS', 1),
(111, '511110', 'HotdogPouch-GPPaperPln', 'PCS', 1),
(112, '511371', 'Paper Pouch-Large Fry Bag', 'PCS', 1),
(113, '512352', 'Paper Bag-Taters SOS Jr 2017', 'PCS', 1),
(114, '512361', 'Paper Bag-Taters SOS#4 (2017)', 'PCS', 1),
(115, '512372', 'Paper Bag-Taters SOS#8 2017', 'PCS', 1),
(116, '515130', 'Paper Cup-Courtesy 6.5oz', 'PCS', 1),
(117, '525152', 'Paper Cup - Taters 12oz 2017 design', 'PCS', 1),
(118, '525153', 'Paper Cup 32oz 2017 Design', 'PCS', 1),
(119, '525165', 'Paper Cup Taters 16 oz 2017', 'PCS', 1),
(120, '525172', 'Paper Cup-Taters 22oz 2017', 'PCS', 1),
(121, '527110', 'Plastic White-Spoon', 'PCS', 1),
(122, '527120', 'Plastic White-Fork', 'PCS', 1),
(123, '527140', 'Plastic White-Knife', 'PCS', 1),
(124, '527360', 'Plastic Strirrer-Red', 'PCS', 1),
(125, '534160', 'Taters Hotdog Box (2020)', 'PCS', 1),
(126, '534312', 'Burger Box Printed-2017', 'PCS', 1),
(127, '534322', 'Large Tray Box Printed 2017', 'PCS', 1),
(128, '538311', 'Paper Cup Holder Printed', 'PCS', 1),
(129, '539150', 'Eco Drink Holder 7.5cm (White Hole)', 'PCS', 1),
(130, '539151', 'Eco Drink Holder 6cm (Red Hole)', 'PCS', 1),
(131, '539160', 'Paper Cup Holder 4hole', 'PCS', 1),
(132, '53A9A5', 'Taters Box Strap (2.7\" x 10.5\")', 'PCS', 1),
(133, '542074', 'Plastic bag 20x30 thick(pop)', 'PCS', 1),
(134, '542100', 'Biodegradable Sando Bag - Small', 'PCS', 1),
(135, '542110', 'Biodegradable Sando Bag - Medium', 'PCS', 1),
(136, '542120', 'Biodegradable Sando Bag - Large', 'PCS', 1),
(137, '542180', 'Biodegradable Sando Bag - Extra large', 'PCS', 1),
(138, '545115', 'Plastic Sauce CupWht30ml', 'PCS', 1),
(139, '545120', 'Plastic Sauce CupWht44ml', 'PCS', 1),
(140, '545140', 'Paper Sauce Cup 3oz', 'PCS', 1),
(141, '546020', 'Plastic SauceLidclear44ml', 'PCS', 1),
(142, '546030', 'Pepsi Tumbler w/ Lid & Straw', 'PCS', 1),
(143, '546050', 'Plastic Lid 12oz cup', 'PCS', 1),
(144, '546061', 'Plastic Lid 16/22oz cup', 'PCS', 1),
(145, '546080', 'Plastic Lid 32oz cup Yihrong', 'PCS', 1),
(146, '546110', 'Plastic SauceLidclear30ml', 'PCS', 1),
(147, '548022', 'Plastic Straw-Transparent', 'PCS', 1),
(148, '549011', 'Paper Straw Coated White', 'PCS', 1),
(149, '554010', 'Plastic Microwaveable Tub-P250', 'PCS', 1),
(150, '556010', 'Plastic Microwaveable Lid-250', 'PCS', 1),
(151, '562020', 'Plastic Resealable Bag - Plain (Sunglobe)', 'PCS', 1),
(152, '562050', 'PlasticResealableBagZiplock#7', 'PCS', 1),
(153, '562060', 'PlasticResealableBagZiplock#8', 'PCS', 1),
(154, '562070', 'PlasticResealableZiplock#9', 'PCS', 1),
(155, '562080', 'Plastic Resealable Bag Ziplock#11', 'PCS', 1),
(156, '562083', 'Ziplock Standing Plain 11x18', 'PCS', 1),
(157, '562090', 'Ziplock Family Pack', 'PCS', 1),
(158, '562091', 'Ziplock Party Pack', 'PCS', 1),
(159, '562092', 'Ziplock Mini Pack - Taters', 'PCS', 1),
(160, '562094', 'Ziplock Standing Plain 12x20', 'PCS', 1),
(161, '582830', 'Paper Bag-Brown TakeOut#3', 'PCS', 1),
(162, '582860', 'Paper Bag-Brown Regular#12', 'PCS', 1),
(163, '582861', 'Paper Bag-Brown Plain#8', 'PCS', 1),
(164, '582870', 'Paper Bag-Brown Regular#16', 'PCS', 1),
(165, '584150', 'Taters Take-Out Box w/ Cover - Small (Foldcote#12)', 'PCS', 1),
(166, '584160', 'Taters Take-Out Box w/ Cover - Medium (Foldcote#15)', 'PCS', 1),
(167, '591010', 'Plastic Bag- 5x7 pln adhesive', 'PCS', 1),
(168, '592321', 'Plastic Resealable Bag-Printed', 'PCS', 1),
(169, '789015', 'Popclub Renewal Voucher C2S160', 'PCS', 1),
(170, '911142', 'Paper Table Napkins - Taters Printed prefolded 13\" x 13\" 200', 'PCS', 1),
(171, '912211', 'Paper Towel - Interfolded Regular (Livi) 175', 'PCS', 1),
(172, '914011', 'Plastic Gloves - Disposable transp. Sinmag (100s)', 'PCS', 1),
(173, '916112', 'Bamboo Stick', 'PCS', 1),
(174, '920111', 'Silica Gel Solupak 5g', 'PCS', 1),
(175, '921041', 'Trash Bag - Transparent XL (10PC/roll)', 'PCS', 1),
(176, '921051', 'Trash Bag - Transparent XXL (10PC/roll)', 'PCS', 1),
(177, '921141', 'Trash Bag - Black XL (10PC/roll)', 'PCS', 1),
(178, '922021', 'Hand Towel - Kau Thong (12s)', 'PCS', 1),
(179, '923240', 'Detergent Powder - All-purpose 1kg', 'KGS', 1),
(180, '923351', 'Deogen - Suma Total 5L', 'KGS', 1),
(181, '923451', 'Divoplaq - Suma Total N3 5L', 'KGS', 1),
(182, '924011', 'Antibac - Diverseylever', 'PCS', 1),
(183, '931000', 'Sticker Generic Branding (T) 12/sheet', 'PCS', 1),
(184, '931050', 'Sticker Flavor (288pcs/sheet)', 'PCS', 1),
(185, '931211', 'Sticker-Taters Blank Label', 'PCS', 1),
(186, '931411', 'Sticker-Taters\"Popcorn\" label', 'PCS', 1),
(187, '933121', 'Gift Certificate-Taters P100', 'PCS', 1),
(188, '933501', 'Popclub Welcome Kit', 'PCS', 1),
(189, '933502', 'Popclub 2017 Membership Card', 'PCS', 1),
(190, '933511', 'Gift Card 100 (2016)', 'PCS', 1),
(191, '933531', 'Taters Gift Card P200', 'PCS', 1),
(192, '933611', 'Gift Card Holder (2016)', 'PCS', 1),
(193, '934242', 'Taters Gift Tag', 'PCS', 1),
(194, '934424', 'Flavorites Card', 'PCS', 1),
(195, '937343', 'Ribbon Curly (Green) 106pcs/roll', 'PCS', 1),
(196, '937531', 'Taters Babies Doll-Boy', 'PCS', 1),
(197, '937532', 'Taters Babies Doll-Girl', 'PCS', 1),
(198, '938171', 'Sticker Product Instruction  (24pcs/sheet)', 'PCS', 1),
(199, '941520', 'Magnetic Swipe Card-Blank', 'PCS', 1),
(200, '951161', 'Infra Bulb - Kandolite 250W', 'PCS', 1),
(201, '952131', 'Bulb (Popc Mach) - 100W', 'PCS', 1),
(202, '953170', 'Fluorescent - T5 14W', 'PCS', 1),
(203, '953180', 'Fluorescent - T5 28W', 'PCS', 1),
(204, '953190', 'Fluorescent - T5 21W', 'PCS', 1),
(205, '955180', 'Fluorescent - T5 21W (Warm Light)', 'PCS', 1),
(206, '957170', 'Fluorescent - T5 14W (Warm Light)', 'PCS', 1),
(207, '957180', 'Fluorescent - T5 8W (Warm Light)', 'PCS', 1),
(208, '957190', 'Fluorescent - T5 28W (Warm Light)', 'PCS', 1),
(209, '961211', 'Journal Tape (POS) 1ply 76mm x70mm', 'PCS', 1),
(210, '961230', 'Journal Tape (POS) 2ply 76mmx70mm', 'PCS', 1),
(211, '962110', 'POS Ribbon - ERC 38 black (generic)', 'PCS', 1),
(212, '968811', 'Ziplock Card with Adhesive Tape', 'PCS', 1),
(213, '291731', 'Coney Island Premix Indofine 900g/pack', 'KGS', 2),
(214, '305111', 'Popcorn Kernel-Preferred 22.68kg/sack', 'KGS', 2),
(215, '305112', 'Popcorn Kernel-Morrison22.68kg/sack', 'KGS', 2),
(216, '305527', 'Cornachos-Yellow Salted Corn Pln 475g/bag', 'KGS', 2),
(217, '361012', 'All Purpose Cream Magnolia250ml/pk', 'KGS', 2),
(218, '390134', 'Cheese Sauce Powder 900g/pk', 'KGS', 2),
(219, '390141', 'Flavorings-Nacho Cheese 3kg/bag', 'KGS', 2),
(220, '390143', 'Flavorings-WhiteCheddar3kg/bag', 'KGS', 2),
(221, '390241', 'Flavorings-SourCrm&Chvs3kg/bag', 'KGS', 2),
(222, '390341', 'Flavorings-TexanBBQ 3kg/bag', 'KGS', 2),
(223, '390731', 'Flavoring-SweetGlzCaramel 1kg', 'KGS', 2),
(224, '390732', 'Flavoring-StrawberryGlaze 1kg', 'KGS', 2),
(225, '392001', 'DM Original Blend Ketchup 1kg', 'KGS', 2),
(226, '392061', 'Catsup-generic sachets', 'PCS', 2),
(227, '392261', 'Mayonnaise-generic sachets', 'PCS', 2),
(228, '393322', 'Marca Leon Oil-Frito Plus Palm Olein 17kg/tin', 'KGS', 2),
(229, '39566A', 'Coffeemate Creamer - Nestle 450g', 'KGS', 2),
(230, '396434', 'Beta Carotene - Lucarotin 30 Sun 2kg/tin', 'KGS', 2),
(231, '394531', 'Iodized Salt 1kg', 'KGS', 2),
(232, '394561', 'Salt-generic sachet', 'PCS', 2),
(233, '396141', 'Vanilla Extract Flvr-McC3.5kg', 'KGS', 2),
(234, '450055', 'Yogurt Syrup', 'GAL', 2),
(235, '399322', 'Jalape?oPeppers-Generic 2.8kg/can', 'KGS', 2),
(236, '414132', 'Lemonade SF Ferna 1kg', 'KGS', 2),
(237, '43235A', 'Benguet Ground Coffee 1kg', 'KGS', 2),
(238, '510010', 'Glacine Paper', 'PCS', 2),
(239, '510171', 'PaperWaxSndwchWrapPlain15x15', 'PCS', 2),
(240, '511371', 'Paper Pouch-Large Fry Bag', 'PCS', 2),
(241, '512352', 'Paper Bag-Taters SOS Jr 2017', 'PCS', 2),
(242, '512361', 'Paper Bag-Taters SOS#4 (2017)', 'PCS', 2),
(243, '512372', 'Paper Bag-Taters SOS#8 2017', 'PCS', 2),
(244, '525152', 'Paper Cup - Taters 12oz 2017 design', 'PCS', 2),
(245, '525153', 'Paper Cup 32oz 2017 Design', 'PCS', 2),
(246, '525165', 'Paper Cup-Taters 16oz 2017', 'PCS', 2),
(247, '525172', 'Paper Cup-Taters 22oz 2017', 'PCS', 2),
(248, '534312', 'Burger Box Printed-2017', 'PCS', 2),
(249, '534322', 'Large Tray Box Printed2017', 'PCS', 2),
(250, '545115', 'Plastic Sauce CupWht30ml', 'PCS', 2),
(251, '545120', 'Plastic Sauce CupWht44ml', 'PCS', 2),
(252, '545140', 'Paper Sauce Cup 3oz', 'PCS', 2),
(253, '546020', 'Plastic SauceLidclear44ml', 'PCS', 2),
(254, '546050', 'Plastic Lid 12oz cup', 'PCS', 2),
(255, '546061', 'Plastic Lid 16/22oz cup', 'PCS', 2),
(256, '546080', 'Plastic Lid 32oz cup Yihrong', 'PCS', 2),
(257, '546110', 'Plastic SauceLidclear30ml', 'PCS', 2),
(258, '548022', 'Plastic Straw-Transparent 1kg', 'PCS', 2),
(259, '549011', 'Paper Straw Coated White', 'PCS', 2),
(260, '515130', 'Paper Cup-Courtesy 6.5oz', 'PCS', 2),
(261, '542074', 'Plastic bag 20x30 thick(pop)', 'PCS', 2),
(262, '562050', 'PlasticResealableBagZiplock#7', 'PCS', 2),
(263, '562060', 'PlasticResealableBagZiplock#8', 'PCS', 2),
(264, '562080', 'Plastic Resealable Bag Ziplock#11', 'PCS', 2),
(265, '562090', 'Ziplock Family Pack', 'PCS', 2),
(266, '56209D', 'Ziplock Tall - Tortilla Chips Packaging', 'PCS', 2),
(267, '562091', 'Ziplock Party Pack', 'PCS', 2),
(268, '56209F', 'Taters Mini Ziplock (matte and Glossy)', 'PCS', 2),
(269, '582830', 'Paper Bag-Brown TakeOut#3', 'PCS', 2),
(270, '582861', 'Paper Bag-Brown TakeOut#8', 'PCS', 2),
(271, '542100', 'Biodegradable Sando Bags - Small', 'PCS', 2),
(272, '542110', 'Biodegradable Sando Bags - Medium', 'PCS', 2),
(273, '542120', 'Biodegradable Sando Bags - Large', 'PCS', 2),
(274, '542180', 'Biodegradable Sando Bag - Extra large', 'PCS', 2),
(275, '562094', 'Ziplock Standing Plain 12x20', 'PCS', 2),
(276, '562083', 'Ziplock Standing Plain 11x18', 'PCS', 2),
(277, '572011', 'Juice (Square 500ml) PET Bottle', 'PCS', 2),
(278, '911142', 'Paper Table Napkins - Taters Printed prefolded 13\" x 13\"', 'PCS', 2),
(279, '912211', 'Paper Towel - Interfolded Regular (Livi)', 'PCS', 2),
(280, '914011', 'Plastic Gloves - Disposable transp. Sinmag (100s)', 'PCS', 2),
(281, '921041', 'Trash Bag - Transparent XL (10pcs/roll)', 'PCS', 2),
(282, '921051', 'Trash Bag - Transparent XXL (10pcs/roll)', 'PCS', 2),
(283, '921141', 'Trash Bag - Black XL (10PC/roll)', 'PCS', 2),
(284, '922021', 'Hand Towel - Kau Thong (12s)', 'PCS', 2),
(285, '923240', 'Detergent Powder - All-purpose 1kg', 'KGS', 2),
(286, '923351', 'Deogen - Suma Total 5L', 'KGS', 2),
(287, '923451', 'Divoplaq - Suma Total N3 5L', 'KGS', 2),
(288, '924011', 'Antibac - Diverseylever', 'PCS', 2),
(289, '961211', 'Journal Tape (POS) 1ply 76mm x70mm', 'PCS', 2),
(290, '961230', 'Journal Tape (POS) 2ply 76mmx70mm', 'PCS', 2),
(291, '962110', 'POS Ribbon - ERC 38 black', 'PCS', 2),
(292, '920111', 'Silica Gel Solupak 5g', 'PCS', 2),
(293, '920121', 'Silica Gel Solupak 2g', 'PCS', 2),
(294, '707006', 'Tofu Chips-Natural Tall', 'PCS', 2),
(295, '707016', 'Tofu Chips-Nacho Cheese Tall', 'PCS', 2),
(296, '707036', 'Tofu Chips-Texan BBQ Tall', 'PCS', 2),
(297, '707056', 'Tofu Chips- White Cheddar Tall', 'PCS', 2),
(298, '707026', 'Tofu Chips- Sour Cream Tall', 'PCS', 2);

-- --------------------------------------------------------

--
-- Table structure for table `transportation_tb`
--

CREATE TABLE `transportation_tb` (
  `id` int(11) NOT NULL,
  `label` varchar(32) DEFAULT NULL,
  `description` varchar(65) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transportation_tb`
--

INSERT INTO `transportation_tb` (`id`, `label`, `description`) VALUES
(1, 'By Land', 'Ground Transport'),
(2, 'By Sea', 'Ocean Freight'),
(3, 'By Air', 'Air Freight');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `usertype_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `last_name`, `first_name`, `email`, `password`, `usertype_id`) VALUES
(1, 'Tanchanco', 'Ana Maria', 'actanchanco@gmail.com', 'password', 1),
(2, 'Tanchanco', 'Joseph Brian', 'briantanchanco@gmail.com', 'password', 1),
(3, 'Tiong', 'Alaine Czarina', 'alaine.tiong.tei@gmail.com', 'password', 1),
(4, 'Lumalang', 'Jocel ', 'jocel.lumalang.tei@gmail.com', 'password', 1),
(5, 'Pe', 'Pablo ', 'pablo.penalba.tei@gmail.com', 'password', 1),
(6, 'Sarmiento', 'Domingo ', 'domingo.sarmiento.tei@gmail.com', 'password', 1),
(7, 'Tiongco', 'Noel ', 'noel.tiongco.tei@gmail.com', 'password', 1),
(8, 'Tolentino', 'Rainier Philip', 'rainier.tolentino.tei@gmail.com', 'password', 1),
(9, 'Peralta', 'Alberto ', 'alberto.peralta.tei@gmail.com', 'password', 1),
(10, 'Jaspe', 'Jessa ', 'jessa.jaspe.tei@gmail.com', 'password', 1),
(11, 'Lavarro', 'Aj ', 'aj.lavarro.tei@gmail.com', 'password', 1),
(12, 'Amper', 'Jezreel ', 'jezreel.amper.tei@gmail.com', 'password', 1),
(13, 'Abuy', 'Jay-ar ', 'jayar.abuy.tei@gmail.com', 'password', 1),
(14, 'Angel', 'Neil Bryan', 'neil.angel.tei@gmail.com', 'password', 1),
(15, 'Borromeo', 'John David', 'david.borromeo.tei@gmail.com', 'password', 1),
(16, 'Malonzo', 'Daica ', 'daica.malonzo.tei@gmail.com', 'password', 1),
(17, 'Dumlao', 'Jovet Christian', 'jovet.dumlao.tei@gmail.com', 'password', 1),
(18, 'Fabro', 'Marcus Lloyd', 'marcus.fabro.tei@gmail.com', 'password', 1),
(19, 'Garbin', 'Michael Angelo', 'michael.garbin.tei@gmail.com', 'password', 1),
(20, 'Mamaril', 'Julius Victor', 'julius.mamaril.tei@gmail.com', 'password', 1),
(21, 'Dangel', 'Emelito ', 'emelito.dangel.tei@gmail.com', 'password', 1),
(22, 'Dizon', 'Lander Jade', 'lander.dizon.tei@gmail.com', 'password', 1),
(23, 'Solis', 'Edgar Allan', 'allan.solis.tei@gmail.com', 'password', 1),
(24, 'Cruz', 'Femma Dela', 'femsanchez.tei@gmail.com', 'password', 1),
(25, 'Firmalino', 'Darelle John', 'darelle.firmalino.tei@gmail.com', 'password', 1),
(26, 'Abalon', 'Jayson', 'jayson.abalon.tei@gmail.com', 'password', 1),
(27, 'Bugay', 'Crisnald', 'crisnald.bugay.tei@gmail.com', 'password', 1),
(28, 'Villaraza', 'Jerico ', 'jerico.villaraza.tei@gmail.com', 'password', 1),
(29, 'Aquino', 'Michael Ryan', 'michael.aquino.tei@gmail.com', 'password', 1),
(30, 'Vera', 'Andrew De', 'andrew.devera.tei@gmail.com', 'password', 1),
(31, 'Oresca', 'Ken ', 'ken.oresca.tei@gmail.com', 'password', 1),
(32, 'Lungca', 'Alexander ', 'alexander.lungca.tei@gmail.com', 'password', 1),
(33, 'Mosquera', 'Jeneth ', 'jeneth.mosquera.tei@gmail.com', 'password', 1),
(34, 'Paez', 'Riza ', 'riza.paez.tei@gmail.com', 'password', 1),
(35, 'Pilla', 'Jecel ', 'jecel.pilla.tei@gmail.com', 'password', 1),
(36, 'Valenzuela', 'Sheryl ', 'sheryl.valenzuela.tei@gmail.com', 'password', 1),
(37, 'Federico', 'Jonna ', 'jonna.federico.tei@gmail.com', 'password', 1),
(38, 'Martinez', 'Marilou ', 'malou.martinez.tei@gmail.com', 'password', 1),
(39, 'Barte', 'Ivana ', 'ivana.barte.tei@gmail.com', 'password', 1),
(40, 'Castro', 'Elena De', 'elena.decastro.tei@gmail.com', 'password', 1),
(41, 'Nilo', 'Gemma ', 'gemma.nilo.tei@gmail.com', 'password', 1),
(42, 'Sibayan', 'Jericho ', 'jericho.sibayan.tei@gmail.com', 'password', 1),
(43, 'Rojo', 'Jonalyn ', 'jonalyn.rojo.tei@gmail.com', 'password', 1),
(44, 'Tango', 'Reazel ', 'reazel.tango.tei@gmail.com', 'password', 1),
(45, 'Firmante', 'Jayson ', 'jayson.firmante.tei@gmail.com', 'password', 1),
(46, 'Bautista', 'Jennylyn ', 'jennylyn.bautista.tei@gmail.com', 'password', 1),
(47, 'Bautista', 'Margarette Ann', 'margarette.bautista.tei@gmail.com', 'password', 1),
(48, 'Dominise', 'Roy ', 'roy.dominise.tei@gmail.com', 'password', 1),
(49, 'Rosal', 'Crisanto ', 'cris.rosal.tei@gmail.com\n', 'password', 1),
(50, 'Upod', 'Hansel ', 'hansel.upod.tei@gmail.com', 'password', 1),
(51, 'Monsalud', 'Michael ', 'michael.monsalud.tei@gmail.com', 'password', 1),
(52, 'Mejio', 'Jomar ', 'jomarmejio.tei@gmail.com', 'password', 1),
(53, 'Lavarias', 'Maricris ', 'maricris.lavarias.tei@gmail.com', 'password', 1),
(54, 'Palomique', 'EJ ', 'ej.palomique.tei@gmail.com', 'password', 1),
(55, 'Diato', 'Edvin Patrick', 'patrick.diato.tei@gmail.com', 'password', 1),
(56, 'Gerez', 'Grace Mae', 'grace.gerez.tei@gmail.com', 'password', 1),
(57, 'Aguila', 'Stephanie Gail', 'stephaniegail.aguila.tei@gmail.com', 'password', 1),
(58, 'Lachica', 'Sarah May', 'sarah.lachica.tei@gmail.com', 'password', 1),
(59, 'Acedera', 'Ben Joseph', 'benjoseph.acederaIII.tei@gmail.com', 'password', 1),
(60, 'Zaldivia', 'Ellen ', 'ellen.zaldivia.tei@gmail.com', 'password', 1),
(61, 'Medina', 'Rozano ', 'rozano.medina.tei@gmail.com', 'password', 1),
(62, 'Taban-ud', 'Arleen ', 'arleen.tabanud.tei@gmail.com', 'password', 1),
(63, 'Sabado', 'Francis ', 'francis.sabado.tei@gmail.com', 'password', 1),
(64, 'Sanchez', 'Virginia ', 'virginia.sanchez.tei@gmail.com', 'password', 1),
(65, 'Daguman', 'Vincent Howell', 'howell.daguman.tei@gmail.com', 'password', 1),
(66, 'Llanera', 'Ederlyn ', 'ederlyn.llanera.tei@gmail.com', 'password', 1),
(67, 'Villarente', 'Raymart ', 'raymart.villarente.tei@gmail.com', 'password', 1),
(68, 'Puno', 'Emily ', 'emily.puno.tei@gmail.com', 'password', 1),
(69, 'Lunar', 'Ruselo ', 'ruselo.lunar.tei@gmail.com', 'password', 1),
(70, 'Nero', 'Jeffrey ', 'jeffrey.nero.tei@gmail.com', 'password', 1),
(71, 'Andino', 'Karl Angelo', 'karl.andino.tei@gmail.com', 'password', 1),
(72, 'Tolentino', 'Ken Lester', 'ken.tolentino.tei@gmail.com', 'password', 1),
(73, 'Order', 'TEI Shell', 'orders.shell.tei@gmail.com', 'password', 1),
(74, 'Castro', 'Tara De', 'tara.decastro.tei@gmail.com', 'password', 1),
(75, 'Palmares', 'Careyl Jay', 'careyl.palmares.tei@gmail.com', 'password', 1),
(76, 'Jimenez', 'Raffy ', 'raffy.jimenez.tei@gmail.com', 'password', 1),
(77, 'Tariman', 'Danica ', 'danica.tariman.tei@gmail.com', 'password', 1),
(78, 'De Villa', 'Jasmine ', 'jasmine.devilla.tei@gmail.com', 'password', 1),
(79, 'Sabello', 'Ena Claire', 'enaclaire.sabello.tei@gmail.com', 'password', 1),
(80, 'Administrator', 'TEI', 'admin@admin.com', 'password', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE `user_type` (
  `id` int(10) NOT NULL,
  `usertype_id` int(10) NOT NULL,
  `user_type_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`id`, `usertype_id`, `user_type_description`) VALUES
(1, 1, 'Store Staff'),
(2, 2, 'Store Manager'),
(3, 3, 'Procurement'),
(4, 4, 'Supplier'),
(5, 5, 'Operations (OPS)'),
(6, 6, 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing_information_tb`
--
ALTER TABLE `billing_information_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_tb`
--
ALTER TABLE `category_tb`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `order_information_tb`
--
ALTER TABLE `order_information_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_item_tb`
--
ALTER TABLE `order_item_tb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sos_order_id` (`order_information_id`);

--
-- Indexes for table `order_place_schedule_logic_tb`
--
ALTER TABLE `order_place_schedule_logic_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_status_tb`
--
ALTER TABLE `payment_status_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_cost_per_store_tb`
--
ALTER TABLE `product_cost_per_store_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_cost_tb`
--
ALTER TABLE `product_cost_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_tb`
--
ALTER TABLE `product_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_tb_backup`
--
ALTER TABLE `product_tb_backup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transportation_tb`
--
ALTER TABLE `transportation_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing_information_tb`
--
ALTER TABLE `billing_information_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `category_tb`
--
ALTER TABLE `category_tb`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_information_tb`
--
ALTER TABLE `order_information_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item_tb`
--
ALTER TABLE `order_item_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_place_schedule_logic_tb`
--
ALTER TABLE `order_place_schedule_logic_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_status`
--
ALTER TABLE `order_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payment_status_tb`
--
ALTER TABLE `payment_status_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_cost_per_store_tb`
--
ALTER TABLE `product_cost_per_store_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `product_cost_tb`
--
ALTER TABLE `product_cost_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT for table `product_tb`
--
ALTER TABLE `product_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT for table `product_tb_backup`
--
ALTER TABLE `product_tb_backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT for table `transportation_tb`
--
ALTER TABLE `transportation_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_item_tb`
--
ALTER TABLE `order_item_tb`
  ADD CONSTRAINT `order_item_tb_ibfk_1` FOREIGN KEY (`order_information_id`) REFERENCES `order_information_tb` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
