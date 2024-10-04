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
-- Database: `borrower`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `suffix` varchar(10) NOT NULL,
  `roles` enum('Student','Instructor','','') NOT NULL,
  `item_to_borrow` varchar(50) NOT NULL,
  `locations` varchar(50) NOT NULL,
  `borrow_datetime` datetime NOT NULL,
  `status` enum('returned','not_returned','lost','damaged') NOT NULL,
  `serials` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `roles`, `item_to_borrow`, `locations`, `borrow_datetime`, `status`, `serials`) VALUES
(11, 'Rosalie', 'Diocales', 'Dulay', '', 'Instructor', 'stove', 'room 3', '2024-05-27 18:14:30', 'not_returned', '0'),
(12, 'Jeff', 'Rodolfo', 'Dulay', '', 'Instructor', 'drill', 'room 2', '2024-05-27 18:15:08', 'not_returned', '0');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_history`
--

CREATE TABLE `borrow_history` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_phone` varchar(50) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `equipment_name` varchar(255) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date_received` datetime DEFAULT NULL,
  `borrow_datetime` datetime DEFAULT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `student_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_history`
--

INSERT INTO `borrow_history` (`id`, `user_name`, `user_phone`, `user_type`, `equipment_name`, `serial_number`, `location`, `date_received`, `borrow_datetime`, `status`, `student_id`) VALUES
(11, 'Jeff Dulay', '09814800058', 'student', 'Grinder', '109745', 'room 4A', '2024-09-21 00:00:00', '2024-09-24 11:43:13', 'pending', '2021-01255'),
(12, 'Jeff Dulay', '09814800058', 'student', 'Grinder', '109745', 'room 4A', '2024-09-21 00:00:00', '2024-09-24 12:15:05', 'pending', '2021-01255'),
(13, 'Jeff Dulay', '09814800058', 'student', 'Grinder', '109745', 'room 4A', '2024-09-21 00:00:00', '2024-09-24 12:18:04', 'pending', '2021-01255'),
(14, 'Jeff Dulay', '09814800058', 'student', 'Grinder', '109745', 'room 4A', '2024-09-21 00:00:00', '2024-09-24 12:18:52', 'pending', '2021-01255');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrow_history`
--
ALTER TABLE `borrow_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `borrow_history`
--
ALTER TABLE `borrow_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
