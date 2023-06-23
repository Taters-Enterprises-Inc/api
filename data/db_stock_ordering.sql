-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2023 at 11:03 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
