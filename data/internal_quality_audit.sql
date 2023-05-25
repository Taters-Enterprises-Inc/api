-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2023 at 01:14 PM
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
-- Database: `internal_quality_audit`
--

-- --------------------------------------------------------

--
-- Table structure for table `form_audit_type`
--

CREATE TABLE `form_audit_type` (
  `id` int(11) NOT NULL,
  `type_name` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_audit_type`
--

INSERT INTO `form_audit_type` (`id`, `type_name`) VALUES
(1, 'Mall Cinema'),
(2, 'Community'),
(3, 'Non Cinema'),
(4, 'Leisure'),
(5, 'Terminal'),
(6, 'Amusement');

-- --------------------------------------------------------

--
-- Table structure for table `form_category`
--

CREATE TABLE `form_category` (
  `id` int(11) NOT NULL,
  `Name` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_category`
--

INSERT INTO `form_category` (`id`, `Name`) VALUES
(1, 'Environment'),
(2, 'Customer Service'),
(3, 'Safety'),
(4, 'Product Standard'),
(5, 'Materials Management'),
(6, 'Cash Handling'),
(7, 'Equipment Maintenance'),
(8, 'Resource Management');

-- --------------------------------------------------------

--
-- Table structure for table `form_category_weight`
--

CREATE TABLE `form_category_weight` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type_id` int(11) NOT NULL,
  `weight` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_category_weight`
--

INSERT INTO `form_category_weight` (`id`, `category_id`, `type_id`, `weight`) VALUES
(1, 1, 4, '0.14'),
(2, 2, 4, '0.16'),
(3, 3, 4, '0.18'),
(4, 4, 4, '0.18'),
(5, 5, 4, '0.10'),
(6, 6, 4, '0.08'),
(7, 7, 4, '0.08'),
(8, 8, 4, '0.08'),
(9, 1, 1, '0.14'),
(10, 2, 1, '0.16'),
(11, 3, 1, '0.18'),
(12, 4, 1, '0.18'),
(13, 5, 1, '0.10'),
(14, 6, 1, '0.08'),
(15, 7, 1, '0.08'),
(16, 8, 1, '0.08'),
(17, 1, 5, '0.15'),
(18, 2, 5, '0.15'),
(19, 3, 5, '0.20'),
(20, 4, 5, '0.20'),
(21, 5, 5, '0.10'),
(22, 6, 5, '0.10'),
(23, 7, 5, '0.10'),
(24, 1, 3, '0.15'),
(25, 2, 3, '0.15'),
(26, 3, 3, '0.20'),
(27, 4, 3, '0.20'),
(28, 5, 3, '0.10'),
(29, 6, 3, '0.10'),
(30, 7, 3, '0.10'),
(31, 1, 2, '0.15'),
(32, 2, 2, '0.15'),
(33, 3, 2, '0.20'),
(34, 4, 2, '0.20'),
(35, 5, 2, '0.10'),
(36, 6, 2, '0.10'),
(37, 7, 2, '0.10');

-- --------------------------------------------------------

--
-- Table structure for table `form_criteria_availability`
--

CREATE TABLE `form_criteria_availability` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `audit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_criteria_availability`
--

INSERT INTO `form_criteria_availability` (`id`, `question_id`, `audit_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),
(18, 18, 1),
(19, 19, 1),
(20, 20, 1),
(21, 21, 1),
(22, 22, 1),
(23, 23, 1),
(24, 24, 1),
(25, 25, 1),
(26, 26, 1),
(27, 27, 1),
(28, 28, 1),
(29, 29, 1),
(30, 30, 1),
(31, 31, 1),
(32, 32, 1),
(33, 33, 1),
(34, 34, 1),
(35, 35, 1),
(36, 36, 1),
(37, 37, 1),
(38, 38, 1),
(39, 39, 1),
(40, 40, 1),
(41, 41, 1),
(42, 42, 1),
(43, 43, 1),
(44, 44, 1),
(45, 50, 1),
(46, 51, 1),
(47, 52, 1),
(48, 53, 1),
(49, 54, 1),
(50, 55, 1),
(51, 56, 1),
(52, 57, 1),
(53, 58, 1),
(54, 59, 1),
(55, 60, 1),
(56, 61, 1),
(57, 62, 1),
(58, 63, 1),
(59, 64, 1),
(60, 65, 1),
(61, 66, 1),
(62, 67, 1),
(63, 68, 1),
(64, 69, 1),
(65, 70, 1),
(66, 71, 1),
(67, 72, 1),
(68, 73, 1),
(69, 74, 1),
(70, 75, 1),
(71, 76, 1),
(72, 77, 1),
(73, 78, 1),
(74, 79, 1),
(75, 80, 1),
(76, 81, 1),
(77, 82, 1),
(78, 83, 1),
(79, 84, 1),
(80, 85, 1),
(81, 86, 1),
(82, 87, 1),
(83, 88, 1),
(84, 89, 1),
(85, 90, 1),
(86, 91, 1),
(87, 92, 1),
(88, 93, 1),
(89, 94, 1),
(90, 95, 1),
(91, 96, 1),
(92, 97, 1),
(93, 98, 1),
(94, 99, 1),
(95, 100, 1),
(96, 101, 1),
(97, 102, 1),
(98, 103, 1),
(99, 104, 1),
(100, 105, 1),
(101, 106, 1),
(102, 107, 1),
(103, 108, 1),
(104, 109, 1),
(105, 110, 1),
(106, 111, 1),
(107, 112, 1),
(108, 113, 1),
(109, 114, 1),
(110, 115, 1),
(111, 116, 1),
(112, 117, 1),
(113, 118, 1),
(114, 119, 1),
(115, 120, 1),
(116, 121, 1),
(117, 122, 1),
(118, 124, 1),
(119, 125, 1),
(120, 126, 1),
(121, 127, 1),
(122, 128, 1),
(123, 129, 1),
(124, 130, 1),
(125, 131, 1),
(126, 132, 1),
(127, 133, 1),
(128, 134, 1),
(129, 135, 1),
(130, 136, 1),
(131, 137, 1),
(132, 138, 1),
(133, 139, 1),
(134, 140, 1),
(135, 141, 1),
(136, 142, 1),
(137, 143, 1),
(138, 144, 1),
(139, 145, 1),
(140, 146, 1),
(141, 147, 1),
(142, 148, 1),
(143, 149, 1),
(144, 150, 1),
(145, 151, 1),
(146, 169, 1),
(147, 170, 1),
(148, 171, 1),
(149, 172, 1),
(150, 173, 1),
(151, 174, 1),
(152, 175, 1),
(153, 176, 1),
(154, 177, 1),
(155, 178, 1),
(156, 179, 1),
(157, 180, 1),
(158, 181, 1),
(159, 182, 1),
(160, 183, 1),
(161, 184, 1),
(162, 185, 1),
(163, 188, 1),
(164, 189, 1),
(165, 190, 1),
(166, 191, 1),
(167, 192, 1),
(168, 193, 1),
(169, 194, 1),
(170, 195, 1),
(171, 196, 1),
(172, 197, 1),
(173, 198, 1),
(174, 199, 1),
(175, 200, 1),
(176, 201, 1),
(177, 202, 1),
(178, 203, 1),
(179, 204, 1),
(180, 205, 1),
(181, 206, 1),
(182, 207, 1),
(183, 208, 1),
(184, 209, 1),
(185, 210, 1),
(186, 211, 1),
(187, 212, 1),
(188, 214, 1),
(189, 215, 1),
(190, 216, 1),
(191, 217, 1),
(192, 218, 1),
(193, 219, 1),
(194, 220, 1),
(195, 221, 1),
(196, 222, 1),
(197, 223, 1),
(198, 224, 1),
(199, 225, 1),
(200, 226, 1),
(201, 227, 1),
(202, 228, 1),
(203, 229, 1),
(204, 230, 1),
(205, 231, 1),
(206, 232, 1),
(207, 233, 1),
(208, 234, 1),
(209, 235, 1),
(210, 236, 1),
(211, 240, 1),
(212, 241, 1),
(213, 242, 1),
(214, 243, 1),
(215, 244, 1),
(216, 245, 1),
(217, 246, 1),
(218, 247, 1),
(219, 264, 1),
(220, 276, 1),
(221, 277, 1),
(222, 278, 1),
(223, 279, 1),
(224, 280, 1),
(225, 281, 1),
(226, 289, 1),
(227, 290, 1),
(228, 291, 1),
(229, 292, 1),
(230, 293, 1),
(231, 294, 1),
(232, 295, 1),
(233, 296, 1),
(234, 297, 1),
(235, 298, 1),
(236, 299, 1),
(237, 300, 1),
(238, 301, 1),
(239, 1, 2),
(240, 2, 2),
(241, 3, 2),
(242, 4, 2),
(243, 5, 2),
(244, 6, 2),
(245, 7, 2),
(246, 8, 2),
(247, 9, 2),
(248, 11, 2),
(249, 12, 2),
(250, 13, 2),
(251, 14, 2),
(252, 16, 2),
(253, 17, 2),
(254, 18, 2),
(255, 20, 2),
(256, 21, 2),
(257, 25, 2),
(258, 26, 2),
(259, 27, 2),
(260, 28, 2),
(261, 29, 2),
(262, 30, 2),
(263, 31, 2),
(264, 32, 2),
(265, 33, 2),
(266, 34, 2),
(267, 35, 2),
(268, 36, 2),
(269, 37, 2),
(270, 38, 2),
(271, 39, 2),
(272, 40, 2),
(273, 50, 2),
(274, 51, 2),
(275, 52, 2),
(276, 53, 2),
(277, 54, 2),
(278, 55, 2),
(279, 57, 2),
(280, 58, 2),
(281, 59, 2),
(282, 60, 2),
(283, 61, 2),
(284, 62, 2),
(285, 63, 2),
(286, 64, 2),
(287, 65, 2),
(288, 66, 2),
(289, 67, 2),
(290, 68, 2),
(291, 69, 2),
(292, 70, 2),
(293, 71, 2),
(294, 74, 2),
(295, 75, 2),
(296, 76, 2),
(297, 77, 2),
(298, 78, 2),
(299, 79, 2),
(300, 80, 2),
(301, 81, 2),
(302, 82, 2),
(303, 83, 2),
(304, 84, 2),
(305, 86, 2),
(306, 87, 2),
(307, 88, 2),
(308, 89, 2),
(309, 90, 2),
(310, 91, 2),
(311, 92, 2),
(312, 93, 2),
(313, 94, 2),
(314, 95, 2),
(315, 96, 2),
(316, 97, 2),
(317, 98, 2),
(318, 99, 2),
(319, 100, 2),
(320, 101, 2),
(321, 102, 2),
(322, 103, 2),
(323, 104, 2),
(324, 105, 2),
(325, 106, 2),
(326, 107, 2),
(327, 108, 2),
(328, 109, 2),
(329, 110, 2),
(330, 111, 2),
(331, 112, 2),
(332, 113, 2),
(333, 114, 2),
(334, 115, 2),
(335, 116, 2),
(336, 117, 2),
(337, 118, 2),
(338, 119, 2),
(339, 120, 2),
(340, 121, 2),
(341, 122, 2),
(342, 124, 2),
(343, 125, 2),
(344, 126, 2),
(345, 135, 2),
(346, 136, 2),
(347, 137, 2),
(348, 138, 2),
(349, 139, 2),
(350, 140, 2),
(351, 141, 2),
(352, 142, 2),
(353, 143, 2),
(354, 144, 2),
(355, 145, 2),
(356, 146, 2),
(357, 147, 2),
(358, 148, 2),
(359, 149, 2),
(360, 150, 2),
(361, 151, 2),
(362, 169, 2),
(363, 170, 2),
(364, 171, 2),
(365, 174, 2),
(366, 175, 2),
(367, 176, 2),
(368, 177, 2),
(369, 178, 2),
(370, 179, 2),
(371, 180, 2),
(372, 181, 2),
(373, 183, 2),
(374, 184, 2),
(375, 185, 2),
(376, 188, 2),
(377, 189, 2),
(378, 190, 2),
(379, 191, 2),
(380, 192, 2),
(381, 193, 2),
(382, 194, 2),
(383, 195, 2),
(384, 196, 2),
(385, 197, 2),
(386, 198, 2),
(387, 199, 2),
(388, 200, 2),
(389, 201, 2),
(390, 202, 2),
(391, 203, 2),
(392, 208, 2),
(393, 214, 2),
(394, 215, 2),
(395, 216, 2),
(396, 217, 2),
(397, 219, 2),
(398, 220, 2),
(399, 221, 2),
(400, 222, 2),
(401, 223, 2),
(402, 225, 2),
(403, 226, 2),
(404, 227, 2),
(405, 228, 2),
(406, 229, 2),
(407, 232, 2),
(408, 233, 2),
(409, 241, 2),
(410, 242, 2),
(411, 245, 2),
(412, 247, 2),
(413, 264, 2),
(414, 276, 2),
(415, 277, 2),
(416, 278, 2),
(417, 279, 2),
(418, 280, 2),
(419, 289, 2),
(420, 290, 2),
(421, 291, 2),
(422, 292, 2),
(423, 293, 2),
(424, 1, 3),
(425, 2, 3),
(426, 3, 3),
(427, 5, 3),
(428, 6, 3),
(429, 7, 3),
(430, 8, 3),
(431, 10, 3),
(432, 17, 3),
(433, 20, 3),
(434, 21, 3),
(435, 25, 3),
(436, 27, 3),
(437, 28, 3),
(438, 29, 3),
(439, 30, 3),
(440, 32, 3),
(441, 33, 3),
(442, 34, 3),
(443, 35, 3),
(444, 36, 3),
(445, 37, 3),
(446, 38, 3),
(447, 39, 3),
(448, 41, 3),
(449, 42, 3),
(450, 45, 3),
(451, 46, 3),
(452, 47, 3),
(453, 48, 3),
(454, 49, 3),
(455, 50, 3),
(456, 52, 3),
(457, 53, 3),
(458, 54, 3),
(459, 55, 3),
(460, 57, 3),
(461, 58, 3),
(462, 59, 3),
(463, 60, 3),
(464, 61, 3),
(465, 51, 3),
(466, 62, 3),
(467, 63, 3),
(468, 64, 3),
(469, 65, 3),
(470, 66, 3),
(471, 67, 3),
(472, 68, 3),
(473, 70, 3),
(474, 71, 3),
(475, 72, 3),
(476, 73, 3),
(477, 74, 3),
(478, 75, 3),
(479, 76, 3),
(480, 77, 3),
(481, 78, 3),
(482, 79, 3),
(483, 80, 3),
(484, 81, 3),
(485, 82, 3),
(486, 83, 3),
(487, 84, 3),
(488, 89, 3),
(489, 91, 3),
(490, 92, 3),
(491, 94, 3),
(492, 95, 3),
(493, 97, 3),
(494, 100, 3),
(495, 101, 3),
(496, 103, 3),
(497, 105, 3),
(498, 303, 3),
(499, 107, 3),
(500, 114, 3),
(501, 115, 3),
(502, 119, 3),
(503, 120, 3),
(504, 123, 3),
(505, 124, 3),
(506, 151, 3),
(507, 152, 3),
(508, 153, 3),
(509, 154, 3),
(510, 155, 3),
(511, 156, 3),
(512, 157, 3),
(513, 158, 3),
(514, 159, 3),
(515, 160, 3),
(516, 161, 3),
(517, 162, 3),
(518, 163, 3),
(519, 164, 3),
(520, 165, 3),
(521, 166, 3),
(522, 176, 3),
(523, 177, 3),
(524, 178, 3),
(525, 179, 3),
(526, 183, 3),
(527, 185, 3),
(528, 188, 3),
(529, 189, 3),
(530, 190, 3),
(531, 191, 3),
(532, 194, 3),
(533, 195, 3),
(534, 200, 3),
(535, 201, 3),
(536, 208, 3),
(537, 211, 3),
(538, 212, 3),
(539, 213, 3),
(540, 302, 3),
(541, 214, 3),
(542, 219, 3),
(543, 220, 3),
(544, 221, 3),
(545, 223, 3),
(546, 227, 3),
(547, 233, 3),
(548, 234, 3),
(549, 237, 3),
(550, 238, 3),
(551, 239, 3),
(552, 242, 3),
(553, 243, 3),
(554, 244, 3),
(555, 245, 3),
(556, 248, 3),
(557, 249, 3),
(558, 250, 3),
(559, 251, 3),
(560, 252, 3),
(561, 253, 3),
(562, 254, 3),
(563, 255, 3),
(564, 256, 3),
(565, 257, 3),
(566, 258, 3),
(567, 259, 3),
(568, 260, 3),
(569, 261, 3),
(570, 262, 3),
(571, 263, 3),
(572, 265, 3),
(573, 266, 3),
(574, 267, 3),
(575, 268, 3),
(576, 269, 3),
(577, 270, 3),
(578, 271, 3),
(579, 272, 3),
(580, 273, 3),
(581, 274, 3),
(582, 275, 3),
(583, 282, 3),
(584, 283, 3),
(585, 284, 3),
(586, 285, 3),
(587, 286, 3),
(588, 287, 3),
(589, 288, 3),
(590, 1, 4),
(591, 2, 4),
(592, 3, 4),
(593, 4, 4),
(594, 5, 4),
(595, 6, 4),
(596, 7, 4),
(597, 8, 4),
(598, 9, 4),
(599, 10, 4),
(600, 11, 4),
(601, 12, 4),
(602, 13, 4),
(603, 14, 4),
(604, 16, 4),
(605, 17, 4),
(606, 18, 4),
(607, 19, 4),
(608, 20, 4),
(609, 21, 4),
(610, 22, 4),
(611, 23, 4),
(612, 25, 4),
(613, 26, 4),
(614, 27, 4),
(615, 28, 4),
(616, 29, 4),
(617, 30, 4),
(618, 31, 4),
(619, 32, 4),
(620, 33, 4),
(621, 34, 4),
(622, 35, 4),
(623, 36, 4),
(624, 37, 4),
(625, 38, 4),
(626, 39, 4),
(627, 41, 4),
(628, 42, 4),
(629, 43, 4),
(630, 44, 4),
(631, 50, 4),
(632, 51, 4),
(633, 52, 4),
(634, 53, 4),
(635, 54, 4),
(636, 55, 4),
(637, 56, 4),
(638, 57, 4),
(639, 58, 4),
(640, 59, 4),
(641, 60, 4),
(642, 61, 4),
(643, 62, 4),
(644, 63, 4),
(645, 64, 4),
(646, 65, 4),
(647, 66, 4),
(648, 67, 4),
(649, 68, 4),
(650, 69, 4),
(651, 70, 4),
(652, 71, 4),
(653, 72, 4),
(654, 73, 4),
(655, 74, 4),
(656, 75, 4),
(657, 76, 4),
(658, 77, 4),
(659, 78, 4),
(660, 79, 4),
(661, 80, 4),
(662, 81, 4),
(663, 82, 4),
(664, 83, 4),
(665, 84, 4),
(666, 85, 4),
(667, 86, 4),
(668, 87, 4),
(669, 88, 4),
(670, 89, 4),
(671, 90, 4),
(672, 91, 4),
(673, 92, 4),
(674, 93, 4),
(675, 94, 4),
(676, 95, 4),
(677, 96, 4),
(678, 97, 4),
(679, 98, 4),
(680, 99, 4),
(681, 100, 4),
(682, 101, 4),
(683, 102, 4),
(684, 103, 4),
(685, 104, 4),
(686, 105, 4),
(687, 106, 4),
(688, 107, 4),
(689, 108, 4),
(690, 109, 4),
(691, 110, 4),
(692, 111, 4),
(693, 112, 4),
(694, 113, 4),
(695, 114, 4),
(696, 115, 4),
(697, 116, 4),
(698, 117, 4),
(699, 118, 4),
(700, 119, 4),
(701, 120, 4),
(702, 121, 4),
(703, 122, 4),
(704, 124, 4),
(705, 125, 4),
(706, 126, 4),
(707, 127, 4),
(708, 128, 4),
(709, 129, 4),
(710, 130, 4),
(711, 131, 4),
(712, 132, 4),
(713, 133, 4),
(714, 134, 4),
(715, 135, 4),
(716, 136, 4),
(717, 137, 4),
(718, 138, 4),
(719, 139, 4),
(720, 140, 4),
(721, 141, 4),
(722, 142, 4),
(723, 143, 4),
(724, 144, 4),
(725, 145, 4),
(726, 146, 4),
(727, 147, 4),
(728, 148, 4),
(729, 149, 4),
(730, 150, 4),
(731, 151, 4),
(732, 167, 4),
(733, 168, 4),
(734, 169, 4),
(735, 170, 4),
(736, 171, 4),
(737, 172, 4),
(738, 174, 4),
(739, 175, 4),
(740, 176, 4),
(741, 177, 4),
(742, 178, 4),
(743, 179, 4),
(744, 180, 4),
(745, 181, 4),
(746, 182, 4),
(747, 183, 4),
(748, 184, 4),
(749, 185, 4),
(750, 186, 4),
(751, 187, 4),
(752, 188, 4),
(753, 189, 4),
(754, 190, 4),
(755, 191, 4),
(756, 192, 4),
(757, 193, 4),
(758, 194, 4),
(759, 195, 4),
(760, 196, 4),
(761, 197, 4),
(762, 198, 4),
(763, 199, 4),
(764, 200, 4),
(765, 201, 4),
(766, 202, 4),
(767, 203, 4),
(768, 204, 4),
(769, 205, 4),
(770, 206, 4),
(771, 207, 4),
(772, 208, 4),
(773, 209, 4),
(774, 210, 4),
(775, 211, 4),
(776, 212, 4),
(777, 214, 4),
(778, 215, 4),
(779, 216, 4),
(780, 217, 4),
(781, 218, 4),
(782, 219, 4),
(783, 220, 4),
(784, 221, 4),
(785, 222, 4),
(786, 223, 4),
(787, 224, 4),
(788, 225, 4),
(789, 226, 4),
(790, 227, 4),
(791, 228, 4),
(792, 229, 4),
(793, 230, 4),
(794, 231, 4),
(795, 232, 4),
(796, 233, 4),
(797, 234, 4),
(798, 235, 4),
(799, 236, 4),
(800, 240, 4),
(801, 241, 4),
(802, 242, 4),
(803, 243, 4),
(804, 244, 4),
(805, 245, 4),
(806, 246, 4),
(807, 247, 4),
(808, 264, 4),
(809, 276, 4),
(810, 277, 4),
(811, 278, 4),
(812, 279, 4),
(813, 281, 4),
(814, 289, 4),
(815, 290, 4),
(816, 291, 4),
(817, 292, 4),
(818, 293, 4),
(819, 294, 4),
(820, 295, 4),
(821, 296, 4),
(822, 297, 4),
(823, 298, 4),
(824, 299, 4),
(825, 300, 4),
(826, 301, 4);

-- --------------------------------------------------------

--
-- Table structure for table `form_questions`
--

CREATE TABLE `form_questions` (
  `id` int(11) NOT NULL,
  `questions` varchar(516) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_questions`
--

INSERT INTO `form_questions` (`id`, `questions`, `is_active`) VALUES
(1, 'TATERS signage is not dusty', 1),
(2, 'TATERS signage is in good condition (evenly lighted, no signs of crack/damage)', 1),
(3, 'Menu Board/Mat Board is clean (free from dust, no smudges, no dirt)', 1),
(4, 'LED TV Menu is clean (free from dust, no smudges)', 1),
(5, 'LED poster box, wall arts, promo TV are clean', 1),
(6, 'All cleaning agents, supplies, & materials are complete based on checklist', 1),
(7, 'All cleaning supplies & materials are in good condition (no cracks, no any signs of damage)', 1),
(8, 'All cleaning agents are mixed based on standard proportions', 1),
(9, 'All counters/sneeze guard are clean (including underneath before operations & after cleaning)', 1),
(10, 'All counter laminates are not peeling off', 1),
(11, 'Wall tiles are clean & in good condition (e.g. not loosed, no cracks)', 1),
(12, 'Wall\'s paints are not faded & peeling off (front store)', 1),
(13, 'Grease trap is clean & monitoring form is updated, & signed by TC daily', 1),
(14, 'All Drains (floor, slop, & sink) are not clogged & with strainer (at all times)', 1),
(15, 'Mop/Slop Sink is clean & has no residue ', 1),
(16, 'Ceilings (upper portion) are clean & free from dust', 1),
(17, 'All lights (Front, Back) are clean & free from dust', 1),
(18, 'All lights (Front, Back) are in good condition', 1),
(19, 'POS/Computer should be clean (dust free, no visible adhesive marks, & no smudges)', 1),
(20, 'Housekeeping Schedule is available, updated & being followed', 1),
(21, 'All Points of Purchase (P.O.P.) are clean & presentable', 1),
(22, 'Standard table bussing procedures are being followed (w/ complete materials) - min. of 5 checkings', 1),
(23, 'All tables & chairs in the dining area are clean (rating starts 2 minutes after guest left the table) - including overall cleanliness of chairs & tables', 1),
(24, 'All tables & chairs in the dining area are in good condition (not wobbly/moving) ', 1),
(25, 'Floors in the dining area are free from stain & spillages (one time checking only)', 1),
(26, 'All faucets are in good condition (slop & back sink - e.g., no leak, no loose/broken handles)', 1),
(27, 'Garbage is properly segregated', 1),
(28, 'Only \"approved cleaners & chemicals\" are in the outlet', 1),
(29, 'All cabinets are clean (no dirt, no adhesive marks - inside & outside surfaces)', 1),
(30, 'All shelves are clean', 1),
(31, 'Water filter units are clean & housing has no leak', 1),
(32, 'Sanitizer (Antibac A) is available in a clean container', 1),
(33, 'Wares & utensils are clean', 1),
(34, 'Storage containers are clean ', 1),
(35, 'All cleaning agents are labeled with content use ', 1),
(36, 'All cleaning equipment/materials & chemicals are properly stored', 1),
(37, 'No evidence of insect, rodent or other pest infestation (all areas, including Dining Area & Stockroom)', 1),
(38, 'Pest Control Monitoring Form is available & updated (monitoring is documented)', 1),
(39, 'Able to conduct internal audit in Environment Category (show filled up form) - previous/current month', 1),
(40, 'Designated break areas & locker rooms are maintained in a clean & orderly manner', 1),
(41, 'New patches in the PW4CS talker is being followed?', 1),
(42, 'Standard product display label is being followed?', 1),
(43, 'Floors are free from litter, spillage, stains and puddles at stockroom', 1),
(44, 'All items on shelves / pallets match their respective labels ', 1),
(45, 'Is menu light box located above the menu TV?', 1),
(46, 'Are displays of bottled water, pre-packed popcorn, tofu chips, tortilla are located based on standard location?', 1),
(47, 'There are snacks display on the snack tray provided in the PW4CS divider?', 1),
(48, 'Counter facing the \"T\" signage is free from anything. This should be vacant space as specified on the standard visual.', 1),
(49, 'Glass partition surfaces & edges are clean ( free from dust & dirt)', 1),
(50, 'Greetings are made with direct eye contact (minimum of 5 checkings)', 1),
(51, 'Crew can correctly answer questions about menu items, food preparation & serving sizes', 1),
(52, 'Children are acknowledged/greeted & treated with respect', 1),
(53, 'Servers & order takers are attentive (minimum of 5 checkings)', 1),
(54, 'Servers & order takers offer a promotional item/suggest something from the menu (min. of 5 checkings)', 1),
(55, 'Confirmation of orders has been observed (minimum of 5 checkings)', 1),
(56, 'In case more than 3 customers in line, open another POS or someone take order to fasten the service', 1),
(57, 'Correct change is given to customer', 1),
(58, 'Direct customer to claim/waiting area', 1),
(59, 'Crew repeats order back when served (minimum of 5 checkings)', 1),
(60, 'Transactions end with a smile & thank you (minimum of 5 checkings)', 1),
(61, 'Should ask for repeat business upon completion of transaction (minimum of 5 checkings)', 1),
(62, 'Customers are treated with \"Respect\"', 1),
(63, 'Crew exhibit no rude behavior, no horseplay, & no vulgar languages', 1),
(64, 'Taters caps & jersey shouldn\'t be worn when not on duty (including Trainees)', 1),
(65, 'All team members (including TC/TL) wear complete & prescribed uniform (including Trainees)', 1),
(66, 'Customer receive complete order (minimum of 5 checkings)', 1),
(67, 'Customer receive accurate order (minimum of 5 checkings)', 1),
(68, 'Customer receive order within prescribe time (minimum of 5 checkings)', 1),
(69, 'Dispatcher greet the customer on his/her name during dispatch (minimum of 5 checkings)', 1),
(70, 'Ready to eat snack (Popcorn) are available at counter area as grab and go. Minimum of 4 pcs each (family & party)', 1),
(71, 'Pre-pack flavorings are available at counter area as grab and go. Minimum of 4 pcs', 1),
(72, 'Passers-by are being called ( maximum 2 meters away from kiosk) minimum 5 checking', 1),
(73, 'Free tasting of popcorn is being done ( using cup liner or tray) with the customer', 1),
(74, 'Alcohol or alcohol-based sanitizer should also available for customer use at all times', 1),
(75, 'Team member observes washing hands before handling food and utensils and after using toilets or touching face or nose, chemicals & raw foods', 1),
(76, 'Food handlers w/ cuts or sores on hand wear approved bandages & gloves also if touching food', 1),
(77, 'Hair does not fall below ear or not appearing in the holes of the cap for male, hairnet for female', 1),
(78, 'Store Manager, TL, & TC\'s should wear hairnet if preparing food', 1),
(79, 'There is no white extended on fingernail tip (trimmed), cleaned & unpolished (including Trainees)', 1),
(80, 'Uniforms of team members are clean & in good condition (including Trainees)', 1),
(81, 'All team members wear no jewelry, simple wrist watch for TC only (including Trainees)', 1),
(82, 'No face mask, no entry is implemented in the store', 1),
(83, 'All staff wear facemask properly at all times (covering nose and mouth)', 1),
(84, 'Did cashier and/or team member sanitize his/her hands after holding money? (min of 5 checking)', 1),
(85, 'Water filter change as per prescribed due dates', 1),
(86, 'Nothing other than ice is stored in the ice bin/ice chest', 1),
(87, 'Lettuce and tomato sanitize based on standard sanitizing procedure and sanitizing agent (Sanitizer B)', 1),
(88, 'White safeguard soap is used for handwashing ', 1),
(89, 'All wares & utensils being used are in good condition (no crack/damage)', 1),
(90, 'Approved ice scooper is use, clean and in good condition, & stored in a sanitary manner', 1),
(91, 'WASH RINSE SANITIZE is being observed', 1),
(92, 'Wares & utensils are stored in sanitary manner (personal wares/utensils are separated)', 1),
(93, 'No stapler seen in all Store work areas (including Stockroom)', 1),
(94, 'Check/sample individual packages within the case', 1),
(95, 'Product is not accepted if it is not in good condition (w/ Taters Delivery Adjustment)', 1),
(96, 'All refrigerated & frozen products are stored immediately', 1),
(97, 'Stock (stock center) is rotated so oldest is used first (FIFO)', 1),
(98, 'Stock (selling area) is rotated so oldest is used first (FIFO)', 1),
(99, 'Food that is out of shelflife or spoiled is discarded immediately or sealed, labeled/dated to prevent use', 1),
(100, 'All stocks are elevated or \"nothing on the floor\"', 1),
(101, 'All goods are stored on their respective storage conditions (dry/room, frozen, refrigerated)', 1),
(102, 'Freezer temperature is -23°C to -15°C & being monitored in temperature monitoring form', 1),
(103, 'All food is covered or in sealed containers', 1),
(104, 'Refrigerator temperature is 0 - 5°C and being monitored in temperature monitoring form', 1),
(105, 'All opened packed products have monitoring of its shelflife (includes wrong shelflife)', 1),
(106, 'Personal food items should not mix or store inside the Ref or Freezer', 1),
(107, 'All food items indicate their respective delivery or purchase date/s', 1),
(108, 'Prevent potentially hazardous food from spending more than 4 hours in the temperature danger zone', 1),
(109, 'Remove from the refrigerator only as much as you can prepare at one time', 1),
(110, 'Thaw food in a refrigerator at 0° to 5°C on lower shelf to prevent dripping (including following to standard thawing procedures)', 1),
(111, 'Follow the rule of not refreezing thawed food ', 1),
(112, 'Assign specific utensils to potentially hazardous food (for raw & cooked)', 1),
(113, 'Taste food by ladling into a disposable container & throw it afterwards', 1),
(114, 'Use proper holding equipment that can maintain required temperatures', 1),
(115, 'Never mix new food with old food', 1),
(116, 'Reheat leftover food (for sauces & ground beef only) and discard if not consumed within 2 hours', 1),
(117, 'Holding time of all food items are regularly monitored', 1),
(118, 'Sauces/toppings discarded if beyond holding time', 1),
(119, 'Avoid hands from touching food-contact surfaces of packaging. Use gloves when cooking popcorn and scooping popcorn into packaging', 1),
(120, 'Able to conduct internal audit in Safety category (show filled up form) - previous/current month', 1),
(121, 'Bottled drinks (iced tea and sf lemonade) with expiration date attched on it\'s bottle?', 1),
(122, 'All pre-packed items (frozen & ready to eat) with attached expiration date?', 1),
(123, 'Use food tong or clean gloves when putting silica gel inside the packaging', 1),
(124, 'Standard flavoring  proportion of popcorn is being followed', 1),
(125, 'Halfway procedures in flavoring popcorn is being done', 1),
(126, 'Mixing procedure & proportion of all drinks is being observed', 1),
(127, 'Ultimate snacks ingredients are served with standard proportion - Cornachos', 1),
(128, 'Ultimate snacks ingredients are served with standard proportion - Housefryes', 1),
(129, 'Ultimate snacks ingredients are served with standard proportion - Tater chips', 1),
(130, 'Ultimate snacks ingredients are served with standard proportion - Sandwiches', 1),
(131, 'Standard sequence of ingredients in ultimate products are being followed - Cornachos', 1),
(132, 'Standard sequence of ingredients in ultimate products are being followed - Housefryes', 1),
(133, 'Standard sequence of ingredients in ultimate products are being followed - Taters chips', 1),
(134, 'Standard sequence of ingredients in ultimate products are being followed - Sandwiches', 1),
(135, 'Mixed dip according to standard mixing proportion', 1),
(136, 'Sandwich condiments & ingredients are served with standard proportion', 1),
(137, 'Standard flavoring  proportion of fried products is being observed', 1),
(138, 'Pre packs fryes nachos, tortilla chips, tofu chips are proportion in size (±2.00g only is acceptable)', 1),
(139, 'Dips & toppings are served with standard serving proportion', 1),
(140, 'Patties/Hotdogs are not frozen when grilled', 1),
(141, 'Patties/Hotdogs are grilled based on standard cooking time & temperature', 1),
(142, 'Lettuce for sandwich are fresh & properly drained (not wilted, no dark spots)', 1),
(143, 'Tomato for sandwich are fresh (no seeds, 1 seed = 0 pt, no dark spots)', 1),
(144, 'Fried product are cooked according to proper cooking time & temperature', 1),
(145, 'Fried products under warmers does not exceed maximum holding time', 1),
(146, 'Fried products are drained under infra (minimum of 1 minute) before serving', 1),
(147, 'Fryer oil is in good condition (not foamy, no excessive smoke, not dark)', 1),
(148, 'Fried product have no signs of thawing', 1),
(149, 'Small bits/crushed chips, fryes, cornachos and tortillas are not being served', 1),
(150, 'Stirring sauces & wiping off pan\'s covers consistently done every 30 minutes', 1),
(151, 'Finish product is clean & presentable ', 1),
(152, 'Use tray when wiping the kettle rim after each popping batches', 1),
(153, 'Standard preparation & proportion of betaoil is being followed', 1),
(154, 'Holding time of popcorn inside the Popcorn machine is being monitored', 1),
(155, 'Use designated scooper per flavor.', 1),
(156, 'Check and read temperature every 2 hours using the oven thermometer', 1),
(157, 'Fill each compartment with flavored popcorn (max. capacity per compartment is 1.2kg for PW4CS & 0.750 for Vertical)', 1),
(158, 'Turn on thermostat. Pre-heat for 5 mins. Before filling popcorn', 1),
(159, 'Actual temp. should range 70° to 90°C', 1),
(160, 'Follow maximum weight of natural & glaze popcorn when flavoring popcorn', 1),
(161, 'Mix Popcorn by scooper to strain unpopped kernels into pan  every batch', 1),
(162, 'Tossing procedure of popcorn inside the warmer every 30 minutes is being followed', 1),
(163, 'Halfway procedures when flavoring popcorn is being followed', 1),
(164, 'Standard Shaking procedure when flavoring popcorn is being followed', 1),
(165, 'Standard cooking proportion of natural popcorn is being followed', 1),
(166, 'Standard cooking proportion of glaze popcorn is being followed', 1),
(167, 'Standard procedures on dispensing alcoholic drinks from bottle to cup is being followed?', 1),
(168, 'Standard ice is being followed for the alcoholic drinks?', 1),
(169, 'a.) Popcorn Machine', 1),
(170, 'b.) Popcorn warmer - PW4CS or Vertical Warmer', 1),
(171, 'd.) Sauce Warmer (Water Level Checking only)', 1),
(172, 'e.) Juice Dispenser', 1),
(173, 'f.) Post-Mix Dispenser', 1),
(174, 'g.) Griddle (Portable/Round or Parallel Coil)', 1),
(175, 'h.) Fryer (Ventless//Berjaya)', 1),
(176, 'Able to conduct internal audit in Quality category (show filled up form) - previous/current month', 1),
(177, 'Holding of pre-bagged popcorn is being followed (warmed: 7 hours, unwarmed: 2& 1/2 hours)', 1),
(178, 'Leftover popcorn from previous night are within acceptable (total of 1.050kg for Stores w/ 1 PW4CS/Vertical Warmer & total of 2.800kg for Stores w/ 2 PW4CS)', 1),
(179, 'Leftover popcorn are stored in double bagging', 1),
(180, 'Popcorn inside Popcorn Machine shouldn\'t exceed more than 4 hours (monitoring/label is available)', 1),
(181, 'Snack shop products follow standard weight & proportion', 1),
(182, 'Pre-packed popcorn are warmed 30 minutes before packing', 1),
(183, 'All pre-packed popcorn with silica gel', 1),
(184, 'Standard preparation of bottle drinks is being done?', 1),
(185, 'Standard labelling procedures of popcorn is being followed?', 1),
(186, 'Standard mixing proportion of coffee is being followed ( iced & hot)?', 1),
(187, 'Standard preparation and procedures in hot & cold cofee is being followed?', 1),
(188, 'All items on menu board are available for purchase', 1),
(189, 'All consumable items are available', 1),
(190, 'All items are physically counted & recorded in DRUF', 1),
(191, 'All items are accurately weigh & counted (actual counting is required)', 1),
(192, 'Stock requisition accomplished by Cashier, verified by TC, & released by Stock Clerk', 1),
(193, 'Cashier checks and verifies stocks released by Stock Clerk if correct, complete & in good condition', 1),
(194, 'Production Guide of Popcorn, sandwiches, juices, tortilla, nachos & dip and toppings are updated (at least updated from the last 5 months)', 1),
(195, 'Production Guide of Popcorn, sandwiches, juices, tortilla, nachos & dip and toppings are being used (w/ actual filled up form - Plan vs. Actual)', 1),
(196, 'Daily Requisition Guide is  available & being used', 1),
(197, 'Stock Projection Guide is available & being used', 1),
(198, 'All stocks released from stock center are being recorded accurately in both SCF & DRUF', 1),
(199, 'Cashier and stock clerk reconcile with regards to their requisition', 1),
(200, 'Stock Control Forms are updated & monitored', 1),
(201, 'All stock clerk files are complete, & organized/properly filed', 1),
(202, 'Supplier\'s Delivery Logsheet completely filled up & updated', 1),
(203, 'Only Stock Clerk & TCs are authorized to release stocks from stockroom', 1),
(204, 'All stock transactions is being encoded in IMS & updated (including Pending Status)', 1),
(205, 'All DRs\' and/or Invoices scanned/fax copies are sent within 24 hours (to Finance)', 1),
(206, 'All Original copies of DRs\' and/or Invoices are forwarded to Head Office within a week (including Provincial Stores)', 1),
(207, 'Data encoded in IMS counts vs. physical count in DRUF is accurate', 1),
(208, 'Equipment Service Report file is available & updated (soft copy or hard copy file)', 1),
(209, 'All snack shop transaction is being encoded in the Webpos under adjustment', 1),
(210, 'Items in stockroom are tally with the system based on Stock Status (minimum of 5 items to be checked)', 1),
(211, 'Count sheets of stocks ( EOM) inventory is being submitted to Finance on time? 3rd of the following month. Check previous month', 1),
(212, 'EOM inventory of equipment and utensils is being submitted to Finance on time? 3rd of the following month', 1),
(213, 'Daily usage is being computed manually in the provided form', 1),
(214, 'All cash funds issued are tally', 1),
(215, 'All cash inside the vault w/ complete documentation (only cash, gift certificate & phone are allowed)', 1),
(216, 'Vault Key should be under the custody of either the TM, TL or Head TC, a signed & approved endorsement is a must if being endorsed', 1),
(217, 'Cash drawer & coin fund key are being handled by TC during operation', 1),
(218, 'Cash drawer should be opened using \"OPEN DRAWER FUNCTION\"', 1),
(219, 'Beginning fund & change fund are counted, verified, & documented in cash notebook per shift', 1),
(220, 'Issued Petty Cash should be documented with acknowledgement signature (Received by)', 1),
(221, 'Petty Cash Fund should be replenished in case half of it is used (if no replenishment = 2 pts.)', 1),
(222, 'TC issues beginning fund to Cashiers', 1),
(223, 'Beginning fund verified by the Cashier', 1),
(224, 'Cashier is using his/her own code (including Trainees). Cashier Trainee code is not allowed.', 1),
(225, 'TC is not allowed to do cashiering', 1),
(226, 'Change Fund verified by TC before endorsing to next shift', 1),
(227, 'Cash Notebook with daily record of sales deposited w/ sign of TC, Cashier, & Manager, date deposited, reference #, & validated deposited amount', 1),
(228, 'TC verified beginning fund, sales & cash fund w/ witness', 1),
(229, 'Cashier\'s drop the sales at vault witness by TC & log in Deposit Notebook w/ sign', 1),
(230, 'TC generates POS reading', 1),
(231, 'TC perform EOD reports', 1),
(232, 'Cashier attached all supporting docs to Cashier\'s Report', 1),
(233, 'Sales is being deposited as per schedule', 1),
(234, 'Borrowed sales are approved by Finance Dept. (show approved documentation/e-mail, if none = n/a)', 1),
(235, 'Cashiering Variance Overage for the past 2 weeks is within acceptable % variance over sales of 0.05%', 1),
(236, 'Cashiering Variance Shortage for the past 2 weeks is within acceptable % variance over sales of -0.05%', 1),
(237, 'Cashier verified beginning fund, sales and cash fund w/ witness', 1),
(238, 'Cashier\'s drop the sales at vault witness by TC and log in deposit notebook w/ sign', 1),
(239, 'Cart is using sales invoice per transaction', 1),
(240, 'Satelite operations product monitoring form is udpated and being used', 1),
(241, 'Store google sheet in E commerce viber group is complete and updated', 1),
(242, 'BIR Journal is available & updated (Cash Register/Subsidiary Sales Journal)', 1),
(243, 'Used Sales Invoices should be submitted to Finance weekly (per Pad) - for TEI and TEI Managed Stores only', 1),
(244, 'Completeness of details on Sales Invoices are being followed', 1),
(245, 'Submission of SVR (soft copy) to Finance is being done? 1-7 on the 8th, 1-14 on the 15th, 1-21 on the 21st & 1-28 on the 29th & 1-30/31 on or before 3rd of the following month', 1),
(246, 'Bank Deposit Slips are being updated on Webpos (for TEI and TEI Managed Stores only)', 1),
(247, 'a.) Popcorn Machine', 1),
(248, 'Plug kettle well and securely ', 1),
(249, 'Turn ON motor when oil and kernel are inside', 1),
(250, 'Turn OFF motor as popping stops', 1),
(251, 'Check if popping is done in 2-3 (3-4 = 16oz) mins', 1),
(252, 'Follow maximum batches and rest for minimum of 30 minutes before popping a new batch', 1),
(253, 'Follow minimum batches when popping popcorn', 1),
(254, 'Remove the kettle plug from the female socket', 1),
(255, 'Wipe the kettle plug  with paper towel', 1),
(256, 'Wipe the female socket with paper towel', 1),
(257, 'Wipe the kettle conduit with dry or paper towel', 1),
(258, 'Wipe the kettle rim with paper towel.', 1),
(259, 'Wipe off oil drips from the kettle with heat proof brush', 1),
(260, 'Never pour or wet electric parts of kettle', 1),
(261, 'Wait for 30 minutes after you switched OFF before spraying divoplaq', 1),
(262, 'Ensure that the kettle is clean and free from stain', 1),
(263, 'Ensure that body and parts are clean', 1),
(264, 'b.) Popcorn Warmer - PW4CS or Vertical Warmer', 1),
(265, 'Is warmer body and glass clean inside & out? Should be shiny without smudges.', 1),
(266, 'Check the \"louver\" if opened ( this is the window under the heating coil)', 1),
(267, 'Switch ON lights & blower pan. Check if light & pan are working', 1),
(268, 'After 5 minutes, Set thermostat controls to 50°C to preheat.', 1),
(269, 'Attach oven thermometer to divider. Increase thermostat knob to 80C', 1),
(270, 'Switch OFF thermostat knob to 0C 30 mins before last full show', 1),
(271, 'Switch OFF buttons of light & blower pan 1 hour after turning OFF the thermostat knob', 1),
(272, 'Wipe the \"louver\" with brush with dustpan to remove any dirt or flavoring', 1),
(273, 'Unplug machine from socket. Wrap the plug in a plastic bag with a rubber band to avoid it from getting wet', 1),
(274, 'Fire Extinguisher is in good working condition (not expired, tank not rusty, & gauge arrow in center)', 1),
(275, 'Fire Extinguisher is hanged or place off the floor', 1),
(276, 'c.) Sauce Warmer', 1),
(277, 'd.) Juice Dispenser', 1),
(278, 'e.) Griddle (Portable/Round or Parallel Coil)', 1),
(279, 'f.) Fryer (Ventless/Berjaya)', 1),
(280, 'g.) Post-Mix Dispenser', 1),
(281, 'h.) Digital Weighing Scale', 1),
(282, 'Is equipment clean and dry?', 1),
(283, 'It is not positioned near high temp machine/equipment?', 1),
(284, 'Remove platter and clean with a damp rag', 1),
(285, 'Wipe body with a clean rag', 1),
(286, 'Never pour H20 or allow the electrical parts to get wet', 1),
(287, 'Return the platter in place & spray the surface with antibac. Airdry', 1),
(288, 'Store in a clean & dry cabinet without anything on top, and wrapped in a 20x30 plastic bag', 1),
(289, 'i.) Oven Toaster', 1),
(290, 'j.) Infra Warmer', 1),
(291, 'k.) Rangehood / Exhaust Fan', 1),
(292, 'Fire Extinguisher is in good working condition (not expired, tank not rusty, & gauge arrow in center)', 1),
(293, 'Fire Extinguisher is hanged or place off the floor', 1),
(294, 'Store is implementing a cost savings program on controllable expenses (except labor cost & repairs)', 1),
(295, 'Standard quantity of napkin is being followed', 1),
(296, 'Daily usage of water is being monitored & updated', 1),
(297, 'Inventory variance for the past 2 weeks are within acceptable % variance over sales, 2.00% & below for negative & positive variance (all are within acceptable variance = 3 pts, negative variance with 2.01-2.49% = less 1pt, all above acceptable variance = 0 pt)', 1),
(298, 'Store PNL is updated & being used', 1),
(299, 'Average labor cost for the previous month is within acceptable level (12% & below = 3 pts, 12.1% -14% = 2 pts, 14.1% - 15% = 1 pt, 15.1% & above: 0 pt) ', 1),
(300, 'Variances of Store Cost of Sales Theoretical vs Store PNL for the past month is within acceptable level of ±2.00%', 1),
(301, 'Daily monitoring form of actual COGS is updated', 1),
(302, 'Stock projection guide is being used', 1),
(303, 'All pre-packed popcorn and flavorings with expiration date attached on it', 1);

-- --------------------------------------------------------

--
-- Table structure for table `form_questions_information`
--

CREATE TABLE `form_questions_information` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `sub_section_id` int(11) DEFAULT NULL,
  `urgency_id` int(11) NOT NULL,
  `equivalent_point` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_questions_information`
--

INSERT INTO `form_questions_information` (`id`, `question_id`, `section_id`, `category_id`, `sub_section_id`, `urgency_id`, `equivalent_point`) VALUES
(1, 1, 2, 1, NULL, 2, 2),
(2, 2, 2, 1, NULL, 2, 2),
(3, 3, 2, 1, NULL, 2, 2),
(4, 4, 2, 1, NULL, 2, 2),
(5, 5, 2, 1, NULL, 2, 2),
(6, 6, 2, 1, NULL, 1, 3),
(7, 7, 2, 1, NULL, 1, 3),
(8, 8, 2, 1, NULL, 2, 2),
(9, 9, 2, 1, NULL, 2, 2),
(10, 10, 2, 1, NULL, 2, 2),
(11, 11, 2, 1, NULL, 2, 2),
(12, 12, 2, 1, NULL, 2, 2),
(13, 13, 2, 1, NULL, 2, 2),
(14, 14, 2, 1, NULL, 2, 2),
(15, 15, 2, 1, NULL, 2, 2),
(16, 16, 2, 1, NULL, 2, 2),
(17, 17, 2, 1, NULL, 2, 2),
(18, 18, 2, 1, NULL, 2, 2),
(19, 19, 2, 1, NULL, 2, 2),
(20, 20, 2, 1, NULL, 2, 2),
(21, 21, 2, 1, NULL, 2, 2),
(22, 22, 2, 1, NULL, 2, 2),
(23, 23, 2, 1, NULL, 2, 2),
(24, 24, 2, 1, NULL, 2, 2),
(25, 25, 2, 1, NULL, 2, 2),
(26, 26, 2, 1, NULL, 3, 1),
(27, 27, 2, 1, NULL, 3, 1),
(28, 28, 2, 1, NULL, 3, 1),
(29, 29, 2, 1, NULL, 3, 1),
(30, 30, 2, 1, NULL, 3, 1),
(31, 31, 2, 1, NULL, 2, 2),
(32, 32, 2, 1, NULL, 2, 2),
(33, 33, 2, 1, NULL, 2, 2),
(34, 34, 2, 1, NULL, 2, 2),
(35, 35, 2, 1, NULL, 2, 2),
(36, 36, 2, 1, NULL, 2, 2),
(37, 37, 2, 1, NULL, 1, 3),
(38, 38, 2, 1, NULL, 3, 1),
(39, 39, 2, 1, NULL, 1, 3),
(40, 40, 2, 1, NULL, 2, 2),
(41, 41, 2, 1, NULL, 1, 3),
(42, 42, 2, 1, NULL, 1, 3),
(43, 43, 2, 1, NULL, 2, 2),
(44, 44, 2, 1, NULL, 2, 2),
(45, 45, 2, 1, NULL, 1, 3),
(46, 46, 2, 1, NULL, 1, 3),
(47, 47, 2, 1, NULL, 1, 3),
(48, 48, 2, 1, NULL, 1, 3),
(49, 49, 2, 1, NULL, 2, 2),
(50, 50, 3, 2, NULL, 2, 2),
(51, 51, 3, 2, NULL, 1, 3),
(52, 52, 3, 2, NULL, 1, 3),
(53, 53, 3, 2, NULL, 1, 3),
(54, 54, 3, 2, NULL, 2, 2),
(55, 55, 3, 2, NULL, 1, 3),
(56, 56, 3, 2, NULL, 2, 2),
(57, 57, 3, 2, NULL, 1, 3),
(58, 58, 3, 2, NULL, 3, 1),
(59, 59, 3, 2, NULL, 2, 2),
(60, 60, 3, 2, NULL, 1, 3),
(61, 61, 3, 2, NULL, 2, 2),
(62, 62, 3, 2, NULL, 1, 3),
(63, 63, 3, 2, NULL, 1, 3),
(64, 64, 3, 2, NULL, 2, 2),
(65, 65, 3, 2, NULL, 2, 2),
(66, 66, 3, 2, NULL, 1, 3),
(67, 67, 3, 2, NULL, 1, 3),
(68, 68, 3, 2, NULL, 1, 3),
(69, 69, 3, 2, NULL, 1, 3),
(70, 70, 3, 2, NULL, 1, 3),
(71, 71, 3, 2, NULL, 1, 3),
(72, 72, 3, 2, NULL, 1, 3),
(73, 73, 3, 2, NULL, 1, 3),
(74, 74, 3, 2, NULL, 2, 2),
(75, 75, 4, 3, NULL, 1, 3),
(76, 76, 4, 3, NULL, 1, 3),
(77, 77, 4, 3, NULL, 2, 2),
(78, 78, 4, 3, NULL, 2, 2),
(79, 79, 4, 3, NULL, 1, 3),
(80, 80, 4, 3, NULL, 2, 2),
(81, 81, 4, 3, NULL, 3, 1),
(82, 82, 4, 3, NULL, 1, 3),
(83, 83, 4, 3, NULL, 1, 3),
(84, 84, 4, 3, NULL, 1, 3),
(85, 85, 4, 3, 1, 1, 3),
(86, 86, 4, 3, 1, 3, 1),
(87, 87, 4, 3, 1, 1, 3),
(88, 88, 4, 3, 1, 3, 1),
(89, 89, 4, 3, 1, 2, 2),
(90, 90, 4, 3, 1, 3, 1),
(91, 91, 4, 3, 1, 1, 3),
(92, 92, 4, 3, 1, 1, 3),
(93, 93, 4, 3, 1, 1, 3),
(94, 94, 5, 3, 2, 2, 2),
(95, 95, 5, 3, 2, 2, 2),
(96, 96, 5, 3, 2, 1, 3),
(97, 97, 5, 3, 3, 1, 3),
(98, 98, 5, 3, 3, 1, 3),
(99, 99, 5, 3, 3, 1, 3),
(100, 100, 5, 3, 3, 2, 2),
(101, 101, 5, 3, 3, 1, 3),
(102, 102, 5, 3, 3, 1, 3),
(103, 103, 5, 3, 3, 1, 3),
(104, 104, 5, 3, 3, 1, 3),
(105, 105, 5, 3, 3, 1, 3),
(106, 106, 5, 3, 3, 1, 3),
(107, 107, 5, 3, 3, 1, 3),
(108, 108, 5, 3, 4, 1, 3),
(109, 109, 5, 3, 4, 3, 1),
(110, 110, 5, 3, 4, 2, 2),
(111, 111, 5, 3, 4, 1, 3),
(112, 112, 5, 3, 4, 1, 3),
(113, 113, 5, 3, 5, 3, 1),
(114, 114, 5, 3, 5, 1, 3),
(115, 115, 5, 3, 5, 1, 3),
(116, 116, 5, 3, 5, 1, 3),
(117, 117, 5, 3, 5, 1, 3),
(118, 118, 5, 3, 5, 1, 3),
(119, 119, 5, 3, 6, 1, 3),
(120, 120, 5, 3, 7, 1, 3),
(121, 121, 5, 3, 7, 1, 3),
(122, 122, 5, 3, 7, 1, 3),
(123, 123, 5, 3, 7, 1, 3),
(124, 124, 6, 4, 7, 1, 3),
(125, 125, 6, 4, NULL, 1, 3),
(126, 126, 6, 4, NULL, 1, 3),
(127, 127, 6, 4, NULL, 1, 3),
(128, 128, 6, 4, NULL, 1, 3),
(129, 129, 6, 4, NULL, 1, 3),
(130, 130, 6, 4, NULL, 1, 3),
(131, 131, 6, 4, NULL, 1, 3),
(132, 132, 6, 4, NULL, 1, 3),
(133, 133, 6, 4, NULL, 1, 3),
(134, 134, 6, 4, NULL, 1, 3),
(135, 135, 6, 4, NULL, 1, 3),
(136, 136, 6, 4, NULL, 1, 3),
(137, 137, 6, 4, NULL, 1, 3),
(138, 138, 6, 4, NULL, 1, 3),
(139, 139, 6, 4, NULL, 1, 3),
(140, 140, 6, 4, NULL, 1, 3),
(141, 141, 6, 4, NULL, 1, 3),
(142, 142, 6, 4, NULL, 2, 2),
(143, 143, 6, 4, NULL, 2, 2),
(144, 144, 6, 4, NULL, 1, 3),
(145, 145, 6, 4, NULL, 1, 3),
(146, 146, 6, 4, NULL, 2, 2),
(147, 147, 6, 4, NULL, 2, 2),
(148, 148, 6, 4, NULL, 2, 2),
(149, 149, 6, 4, NULL, 1, 3),
(150, 150, 6, 4, NULL, 1, 3),
(151, 151, 6, 4, NULL, 1, 3),
(152, 152, 6, 4, NULL, 1, 3),
(153, 153, 6, 4, NULL, 1, 3),
(154, 154, 6, 4, NULL, 1, 3),
(155, 155, 6, 4, NULL, 1, 3),
(156, 156, 6, 4, NULL, 1, 3),
(157, 157, 6, 4, NULL, 1, 3),
(158, 158, 6, 4, NULL, 1, 3),
(159, 159, 6, 4, NULL, 1, 3),
(160, 160, 6, 4, NULL, 1, 3),
(161, 161, 6, 4, NULL, 1, 3),
(162, 162, 6, 4, NULL, 1, 3),
(163, 163, 6, 4, NULL, 1, 3),
(164, 164, 6, 4, NULL, 1, 3),
(165, 165, 6, 4, NULL, 1, 3),
(166, 166, 6, 4, NULL, 1, 3),
(167, 167, 6, 4, NULL, 1, 3),
(168, 168, 6, 4, NULL, 1, 3),
(169, 169, 6, 4, 8, 1, 3),
(170, 170, 6, 4, 8, 1, 3),
(171, 171, 6, 4, 8, 1, 3),
(172, 172, 6, 4, 8, 1, 3),
(173, 173, 6, 4, 8, 1, 3),
(174, 174, 6, 4, 8, 1, 3),
(175, 175, 6, 4, 8, 1, 3),
(176, 176, 6, 4, 7, 1, 3),
(177, 177, 6, 4, 7, 1, 3),
(178, 178, 6, 4, 7, 1, 3),
(179, 179, 6, 4, 7, 1, 3),
(180, 180, 6, 4, 7, 1, 3),
(181, 181, 6, 4, 7, 1, 3),
(182, 182, 6, 4, 7, 1, 3),
(183, 183, 6, 4, 7, 1, 3),
(184, 184, 6, 4, 7, 1, 3),
(185, 185, 6, 4, 7, 1, 3),
(186, 186, 6, 4, 7, 1, 3),
(187, 187, 6, 4, 7, 1, 3),
(188, 188, 7, 5, NULL, 1, 3),
(189, 189, 7, 5, NULL, 2, 2),
(190, 190, 7, 5, NULL, 1, 3),
(191, 191, 7, 5, NULL, 1, 3),
(192, 192, 7, 5, NULL, 2, 2),
(193, 193, 7, 5, NULL, 2, 2),
(194, 194, 7, 5, NULL, 1, 3),
(195, 195, 7, 5, NULL, 1, 3),
(196, 196, 7, 5, NULL, 1, 3),
(197, 197, 7, 5, NULL, 1, 3),
(198, 198, 7, 5, NULL, 1, 3),
(199, 199, 7, 5, NULL, 3, 1),
(200, 200, 7, 5, NULL, 2, 2),
(201, 201, 7, 5, NULL, 3, 1),
(202, 202, 7, 5, NULL, 2, 2),
(203, 203, 7, 5, NULL, 2, 2),
(204, 204, 7, 5, NULL, 1, 3),
(205, 205, 7, 5, NULL, 1, 3),
(206, 206, 7, 5, NULL, 1, 3),
(207, 207, 7, 5, NULL, 1, 3),
(208, 208, 7, 5, NULL, 1, 3),
(209, 209, 7, 5, NULL, 2, 2),
(210, 210, 7, 5, NULL, 1, 3),
(211, 211, 7, 5, NULL, 1, 3),
(212, 212, 7, 5, NULL, 1, 3),
(213, 213, 7, 5, NULL, 1, 3),
(214, 214, 8, 6, NULL, 2, 2),
(215, 215, 8, 6, NULL, 1, 3),
(216, 216, 8, 6, NULL, 1, 3),
(217, 217, 8, 6, NULL, 1, 3),
(218, 218, 8, 6, NULL, 1, 3),
(219, 219, 8, 6, NULL, 1, 3),
(220, 220, 8, 6, NULL, 1, 3),
(221, 221, 8, 6, NULL, 2, 2),
(222, 222, 8, 6, NULL, 2, 2),
(223, 223, 8, 6, NULL, 2, 2),
(224, 224, 8, 6, NULL, 1, 3),
(225, 225, 8, 6, NULL, 2, 2),
(226, 226, 8, 6, NULL, 1, 3),
(227, 227, 8, 6, NULL, 2, 2),
(228, 228, 8, 6, NULL, 2, 2),
(229, 229, 8, 6, NULL, 2, 2),
(230, 230, 8, 6, NULL, 2, 2),
(231, 231, 8, 6, NULL, 2, 2),
(232, 232, 8, 6, NULL, 2, 2),
(233, 233, 8, 6, NULL, 1, 3),
(234, 234, 8, 6, NULL, 1, 3),
(235, 235, 8, 6, NULL, 1, 3),
(236, 236, 8, 6, NULL, 1, 3),
(237, 237, 8, 6, NULL, 2, 2),
(238, 238, 8, 6, NULL, 2, 2),
(239, 239, 8, 6, NULL, 2, 2),
(240, 240, 8, 6, 7, 1, 3),
(241, 241, 8, 6, 7, 1, 3),
(242, 242, 8, 6, 7, 1, 3),
(243, 243, 8, 6, 7, 2, 2),
(244, 244, 8, 6, 7, 2, 2),
(245, 245, 8, 6, 7, 1, 3),
(246, 246, 8, 6, 7, 2, 2),
(247, 247, 9, 7, NULL, 1, 3),
(248, 248, 9, 7, NULL, 1, 3),
(249, 249, 9, 7, NULL, 1, 3),
(250, 250, 9, 7, NULL, 1, 3),
(251, 251, 9, 7, NULL, 1, 3),
(252, 252, 9, 7, NULL, 1, 3),
(253, 253, 9, 7, NULL, 1, 3),
(254, 254, 9, 7, NULL, 1, 3),
(255, 255, 9, 7, NULL, 1, 3),
(256, 256, 9, 7, NULL, 1, 3),
(257, 257, 9, 7, NULL, 1, 3),
(258, 258, 9, 7, NULL, 1, 3),
(259, 259, 9, 7, NULL, 1, 3),
(260, 260, 9, 7, NULL, 1, 3),
(261, 261, 9, 7, NULL, 1, 3),
(262, 262, 9, 7, NULL, 1, 3),
(263, 263, 9, 7, NULL, 1, 3),
(264, 264, 9, 7, NULL, 1, 3),
(265, 265, 9, 7, NULL, 1, 3),
(266, 266, 9, 7, NULL, 1, 3),
(267, 267, 9, 7, NULL, 1, 3),
(268, 268, 9, 7, NULL, 1, 3),
(269, 269, 9, 7, NULL, 1, 3),
(270, 270, 9, 7, NULL, 1, 3),
(271, 271, 9, 7, NULL, 1, 3),
(272, 272, 9, 7, NULL, 1, 3),
(273, 273, 9, 7, NULL, 1, 3),
(274, 274, 9, 7, NULL, 1, 3),
(275, 275, 9, 7, NULL, 1, 3),
(276, 276, 9, 7, NULL, 1, 3),
(277, 277, 9, 7, NULL, 1, 3),
(278, 278, 9, 7, NULL, 1, 3),
(279, 279, 9, 7, NULL, 1, 3),
(280, 280, 9, 7, NULL, 1, 3),
(281, 281, 9, 7, NULL, 1, 3),
(282, 282, 9, 7, NULL, 1, 3),
(283, 283, 9, 7, NULL, 1, 3),
(284, 284, 9, 7, NULL, 1, 3),
(285, 285, 9, 7, NULL, 1, 3),
(286, 286, 9, 7, NULL, 1, 3),
(287, 287, 9, 7, NULL, 1, 3),
(288, 288, 9, 7, NULL, 1, 3),
(289, 289, 9, 7, NULL, 1, 3),
(290, 290, 9, 7, NULL, 1, 3),
(291, 291, 9, 7, NULL, 1, 3),
(292, 292, 9, 7, NULL, 1, 3),
(293, 293, 9, 7, NULL, 1, 3),
(294, 294, 10, 8, NULL, 1, 3),
(295, 295, 10, 8, NULL, 1, 3),
(296, 296, 10, 8, NULL, 1, 3),
(297, 297, 10, 8, NULL, 1, 3),
(298, 298, 10, 8, NULL, 1, 3),
(299, 299, 10, 8, NULL, 1, 3),
(300, 300, 10, 8, NULL, 1, 3),
(301, 301, 10, 8, NULL, 1, 3),
(302, 302, 7, 5, NULL, 2, 2),
(303, 303, 5, 3, 3, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `form_rating`
--

CREATE TABLE `form_rating` (
  `id` int(11) NOT NULL,
  `rating` int(1) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `description` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_rating`
--

INSERT INTO `form_rating` (`id`, `rating`, `name`, `description`) VALUES
(1, 1, '1 point', 'Less Critical'),
(2, 2, '2 point', 'Critical'),
(3, 3, '3 point', 'Most Critical'),
(4, 0, '0 point', 'zero point');

-- --------------------------------------------------------

--
-- Table structure for table `form_responses`
--

CREATE TABLE `form_responses` (
  `id` int(11) NOT NULL,
  `attention` varchar(64) DEFAULT NULL,
  `audit_type_id` int(11) DEFAULT NULL,
  `store_id` int(11) NOT NULL,
  `audit_period` varchar(7) NOT NULL,
  `dateadded` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hash` varchar(265) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `form_responses_answers`
--

CREATE TABLE `form_responses_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `rating_id` int(11) DEFAULT NULL,
  `remarks` varchar(512) DEFAULT NULL,
  `urgency_rating` int(1) DEFAULT NULL,
  `equivalent_point` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `form_responses_result`
--

CREATE TABLE `form_responses_result` (
  `id` int(11) NOT NULL,
  `response_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `grade` decimal(10,2) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `final_score` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `form_sections`
--

CREATE TABLE `form_sections` (
  `id` decimal(11,0) NOT NULL,
  `section_name` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_sections`
--

INSERT INTO `form_sections` (`id`, `section_name`) VALUES
('1', 'INTERNAL QUALITY AUDIT GENERAL INFORMATION'),
('2', 'ENVIRONMENT'),
('3', 'CUSTOMER EXPERIENCE (SERVICE)'),
('4', 'SAFETY 3.1 SAFE FOOD HANDLING/ FACILITY SAFETY/FOOD SANITATION'),
('5', 'SAFETY 3.2 FOOD SAFETY PROCESS FLOW'),
('6', ' PRODUCT QUALITY/FOOD PREPARATION'),
('7', 'MATERIALS MANAGEMENT'),
('8', 'CASH HANDLING'),
('9', 'EQUIPMENT OPERATIONS MAINTENANCE'),
('10', 'RESOURCE MANAGEMENT');

-- --------------------------------------------------------

--
-- Table structure for table `form_sub_section`
--

CREATE TABLE `form_sub_section` (
  `id` int(11) NOT NULL,
  `sub_section_name` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_sub_section`
--

INSERT INTO `form_sub_section` (`id`, `sub_section_name`) VALUES
(1, 'SANITATION'),
(2, 'RECEIVING'),
(3, 'STORAGE'),
(4, 'FOOD PREPARATION'),
(5, 'COOKING & HOLDING'),
(6, 'SERVING'),
(7, 'OTHERS'),
(8, 'PROCEDURES AFFECTING QUALITY OF PRODUCTS');

-- --------------------------------------------------------

--
-- Table structure for table `form_urgency_level`
--

CREATE TABLE `form_urgency_level` (
  `id` int(11) NOT NULL,
  `level` int(1) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `form_urgency_level`
--

INSERT INTO `form_urgency_level` (`id`, `level`, `name`, `description`) VALUES
(1, 1, 'Most Crtitical', 'needs immediate corrective and p'),
(2, 2, 'Critical', 'needs immediate corrective actio'),
(3, 3, 'Less Critical', 'needs immediate corrective action & must provide preventive action within 72 hrs.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(46) DEFAULT NULL,
  `role` int(11) NOT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `company_name` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `phone_number` varchar(11) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `role_name` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `role_name`) VALUES
(1, 'Administrator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `form_audit_type`
--
ALTER TABLE `form_audit_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_category`
--
ALTER TABLE `form_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_category_weight`
--
ALTER TABLE `form_category_weight`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_criteria_availability`
--
ALTER TABLE `form_criteria_availability`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_questions`
--
ALTER TABLE `form_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_questions_information`
--
ALTER TABLE `form_questions_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_rating`
--
ALTER TABLE `form_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_responses`
--
ALTER TABLE `form_responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_responses_answers`
--
ALTER TABLE `form_responses_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_responses_result`
--
ALTER TABLE `form_responses_result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_sections`
--
ALTER TABLE `form_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_sub_section`
--
ALTER TABLE `form_sub_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_urgency_level`
--
ALTER TABLE `form_urgency_level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `form_audit_type`
--
ALTER TABLE `form_audit_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `form_category_weight`
--
ALTER TABLE `form_category_weight`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `form_criteria_availability`
--
ALTER TABLE `form_criteria_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=828;

--
-- AUTO_INCREMENT for table `form_questions`
--
ALTER TABLE `form_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=304;

--
-- AUTO_INCREMENT for table `form_questions_information`
--
ALTER TABLE `form_questions_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=304;

--
-- AUTO_INCREMENT for table `form_responses`
--
ALTER TABLE `form_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_responses_answers`
--
ALTER TABLE `form_responses_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_responses_result`
--
ALTER TABLE `form_responses_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
