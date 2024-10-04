-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2024 at 02:15 PM
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
-- Database: `login_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `suffix` varchar(10) NOT NULL,
  `student_id` text NOT NULL,
  `pword` varchar(255) NOT NULL,
  `user_type` enum('instuctor','student','admin') NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `course` enum('RAC Servicing (DomRAC)','Basic Shielded Metal Arc Welding','Advanced Shielded Metal Arc Welding','Pc operation','Bread and pastry production NC II','Computer aid design (CAD)','Culinary arts','Dressmaking NC II','Food and beverage service NC II','Hair care','Junior beautician','Gas metal Arc Welding -- GMAW NC I','Gas metal Arc Welding -- GMAW NC II') NOT NULL,
  `attempts` int(60) NOT NULL,
  `lockout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `student_id`, `pword`, `user_type`, `profile_picture`, `phone`, `course`, `attempts`, `lockout_time`) VALUES
(10, 'Jeff', 'Rodolfo', 'Dulay', 'N/A', '2021-01255', '$2y$10$w1biDl7HSs1agWMoCppB3eLVoWJtzr3ZhLbupdbQmNaHF6HTb5IKy', 'student', '1by1.png', '09814800058', 'RAC Servicing (DomRAC)', 0, NULL),
(12, 'Rosalie', 'Diocales', 'Dulay', 'N/A', '2023-51123', '$2y$10$/rVqNyw3XIwi.ngP05LYSO3YFcObSpHNyAoAmbtek2urcfni43dDe', 'admin', 'picture.png', '09814800058', 'RAC Servicing (DomRAC)', 0, NULL),
(17, 'Ror', 'Pot', 'Rar', 'Jr.', '2023-51124', '$2y$10$D8KqiqxLigUhvK3vLpeIy.zeFRLiNf67UDRecp5surMvITEiJvCsq', 'instuctor', '', '0971452', '', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
