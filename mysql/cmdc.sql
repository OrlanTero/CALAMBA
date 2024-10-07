-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2024 at 01:03 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmdc`
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

-- --------------------------------------------------------

--
-- Table structure for table `borrow_requests`
--

CREATE TABLE `borrow_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qr_key` text NOT NULL,
  `reason` text NOT NULL,
  `borrowed_date` date NOT NULL,
  `borrow_status` enum('not_returned','returned','lost','returned_damaged','not_return_damaged') NOT NULL DEFAULT 'not_returned',
  `request_status` varchar(255) DEFAULT 'pending',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_requests`
--

INSERT INTO `borrow_requests` (`request_id`, `user_id`, `item_id`, `qr_key`, `reason`, `borrowed_date`, `borrow_status`, `request_status`, `date_created`) VALUES
(3, 10, 9, 'i1gJVTSOPC', '', '0000-00-00', 'returned', 'accepted', '2024-10-06 01:02:31'),
(4, 10, 8, 'FpIP1ygSoO', '', '0000-00-00', 'not_returned', 'accepted', '2024-10-06 01:10:07');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_details`
--

CREATE TABLE `equipment_details` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `serials` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `qr_key` varchar(255) NOT NULL,
  `date_rcvd` date DEFAULT NULL,
  `in_used` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_details`
--

INSERT INTO `equipment_details` (`id`, `equipment_id`, `serials`, `location`, `qr_key`, `date_rcvd`, `in_used`) VALUES
(5, 2, '15213521', 'room 4A', 'acd', '2024-09-10', 'no'),
(6, 1, '35123', 'room 4A', 'efg', '2024-09-10', 'no'),
(7, 3, '213515', 'room 4A', 'hh', '2024-09-10', 'no'),
(8, 3, '352234', 'room 4A', 'iii', '2024-09-10', 'yes'),
(9, 3, '35r2552', 'room 4B', 'hhh', '2024-09-10', 'yes'),
(10, 4, '97542', 'Rizal Building 203', 'awdw', '2024-09-11', 'yes'),
(11, 4, '51123', 'Room 511', '2321aa', '2024-09-11', 'no'),
(12, 5, '109745', 'room 4A', 'bba', '2024-09-21', 'no'),
(13, 3, '405216', 'room 375', 'cddw', '2024-09-21', 'no'),
(15, 3, '123456', 'Room A2', '11232', NULL, 'yes'),
(16, 3, 'b', 'A', 'awdawas21', NULL, 'yes'),
(17, 4, '1234', 'SAA', 'sRMh7ix0Aa', NULL, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_info`
--

CREATE TABLE `equipment_info` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_quantity` int(255) NOT NULL,
  `alert_level` int(255) NOT NULL,
  `description` text NOT NULL,
  `available` int(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `borrowed` int(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `course` enum('RAC Servicing (DomRAC)','Basic Shielded Metal Arc Welding','Advanced Shielded Metal Arc Welding','Pc operation','Bread and pastry production NC II','Computer aid design (CAD)','Culinary arts','Dressmaking NC II','Food and beverage service NC II','Hair care','Junior beautician','Gas metal Arc Welding -- GMAW NC I','Gas metal Arc Welding -- GMAW NC II') NOT NULL,
  `category` enum('equipment','tools','material') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_info`
--

INSERT INTO `equipment_info` (`id`, `name`, `total_quantity`, `alert_level`, `description`, `available`, `picture`, `borrowed`, `price`, `course`, `category`) VALUES
(1, 'Grinder', 1, 3, 'This is used for grinding', 1, 'grinder.png', 0, '299.00', 'Basic Shielded Metal Arc Welding', 'equipment'),
(2, 'Stove', 1, 5, 'Used for cooking', 1, 'stove.jpg', 0, '511.00', 'Culinary arts', 'equipment'),
(3, 'Drill', 4, 5, 'Used for Drilling', 4, 'drill.jpg', 0, '799.00', 'RAC Servicing (DomRAC)', 'tools'),
(4, 'Hair Dryer', 2, 3, 'Used for Drying Hair', 2, 'e5d6ddad84cf4c77943e7a55247a777c.png', 0, '699.00', 'Hair care', 'equipment'),
(5, 'Grinder', 1, 3, 'This is used for Grinding', 1, 'ed0fdfcd4bde6d3b93a9837c16daa32b.jpg', 0, '699.00', 'RAC Servicing (DomRAC)', 'equipment'),
(6, 'EEE', 0, 5, '1awdwa', 0, 'mxzo5wBXtd.png', 0, '3.00', 'Food and beverage service NC II', 'equipment');

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
(12, 'Rosalie', 'Diocales', 'Dulay', 'N/A', '2023-51123', '$2y$10$/rVqNyw3XIwi.ngP05LYSO3YFcObSpHNyAoAmbtek2urcfni43dDe', 'admin', '2.PNG', '09814800058', 'RAC Servicing (DomRAC)', 0, NULL),
(17, 'Ror', 'Pot', 'Rar', 'Jr.', '2023-51124', '$2y$10$D8KqiqxLigUhvK3vLpeIy.zeFRLiNf67UDRecp5surMvITEiJvCsq', 'instuctor', '', '0971452', '', 0, NULL);

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
-- Indexes for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `equipment_details`
--
ALTER TABLE `equipment_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial` (`serials`),
  ADD KEY `idx_equipment_id` (`equipment_id`);

--
-- Indexes for table `equipment_info`
--
ALTER TABLE `equipment_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
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

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `equipment_details`
--
ALTER TABLE `equipment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `equipment_info`
--
ALTER TABLE `equipment_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `equipment_details`
--
ALTER TABLE `equipment_details`
  ADD CONSTRAINT `equipment_details_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment_info` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
