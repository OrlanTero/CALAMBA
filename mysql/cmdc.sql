-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2024 at 08:39 PM
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
  `student_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `borrow_status` enum('not_returned','returned','lost') NOT NULL DEFAULT 'not_returned',
  `request_status` enum('pending','accepted','declined') DEFAULT 'pending',
  `item_condition` enum('good_condition','bad_condition','obsolete','no_longer_needed','damage','lost') DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `picture` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `quantity` int(11) NOT NULL,
  `borrow_availability` int(11) NOT NULL,
  `item_condition` enum('good_condition','bad_condition','obsolete','no_longer_needed','damage','lost') DEFAULT 'good_condition',
  `alert_level` int(11) DEFAULT NULL,
  `deleted` int(11) NOT NULL,
  `date_rcvd` timestamp NULL DEFAULT NULL,
  `in_used` enum('yes','no') NOT NULL DEFAULT 'no',
  `category` enum('equipment','tools','consumables') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `category` enum('equipment','tools','material') NOT NULL,
  `serials` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_get_requests`
--

CREATE TABLE `material_get_requests` (
  `request_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `qr_key` varchar(255) NOT NULL,
  `status` enum('pending','accepted','not_accepted','') NOT NULL DEFAULT 'pending',
  `borrow_status` enum('not_returned','returned','lost','returned_damaged','not_return_damaged') DEFAULT NULL,
  `item_condition` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `user_type` enum('instructor','student','admin') NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `course` enum('RAC Servicing (DomRAC)','Basic Shielded Metal Arc Welding','Advanced Shielded Metal Arc Welding','Pc operation','Bread and pastry production NC II','Computer aid design (CAD)','Culinary arts','Dressmaking NC II','Food and beverage service NC II','Hair care','Junior beautician','Gas metal Arc Welding -- GMAW NC I','Gas metal Arc Welding -- GMAW NC II') NOT NULL,
  `attempts` int(60) NOT NULL,
  `lockout_time` datetime DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `student_id`, `pword`, `user_type`, `profile_picture`, `phone`, `course`, `attempts`, `lockout_time`, `archived`) VALUES
(10, 'Jeff', 'Rodolfo', 'Dulay', 'N/A', '2021-01255', '$2y$10$w1biDl7HSs1agWMoCppB3eLVoWJtzr3ZhLbupdbQmNaHF6HTb5IKy', 'student', '', '09814800058', 'RAC Servicing (DomRAC)', 0, NULL, 0),
(12, 'Rosalie', 'Diocales', 'Dulay', 'N/A', '2023-51123', '$2y$10$/rVqNyw3XIwi.ngP05LYSO3YFcObSpHNyAoAmbtek2urcfni43dDe', 'admin', '', '09814800058', 'RAC Servicing (DomRAC)', 0, NULL, 0),
(30, 'Jeff', 'Rodolfo', 'Dulay', 'Jr', '2021-01258', '$2y$10$hIoKVtb6fKNTXYKUdma0J.O0nx92/0FeZX0BXEasNAsIVUnCEFG/y', 'instructor', '', '09814800058', 'Advanced Shielded Metal Arc Welding', 0, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrow_history`
--
ALTER TABLE `borrow_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `material_get_requests`
--
ALTER TABLE `material_get_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrow_history`
--
ALTER TABLE `borrow_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment_details`
--
ALTER TABLE `equipment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment_info`
--
ALTER TABLE `equipment_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_get_requests`
--
ALTER TABLE `material_get_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_history`
--
ALTER TABLE `borrow_history`
  ADD CONSTRAINT `borrow_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `equipment_details`
--
ALTER TABLE `equipment_details`
  ADD CONSTRAINT `equipment_details_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment_info` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
