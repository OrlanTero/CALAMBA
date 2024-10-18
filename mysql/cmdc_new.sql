-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 01:58 AM
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

--
-- Dumping data for table `borrow_requests`
--

INSERT INTO `borrow_requests` (`request_id`, `user_id`, `item_id`, `qr_key`, `reason`, `borrowed_date`, `borrow_status`, `request_status`, `item_condition`, `date_created`) VALUES
(17, 10, 112, 'jWQkPbvAON', '', '0000-00-00', 'returned', 'accepted', NULL, '2024-10-16 18:26:26'),
(18, 10, 113, 'SYD7GhrZ1C', '', '0000-00-00', 'not_returned', 'pending', NULL, '2024-10-16 18:28:56'),
(19, 10, 113, 'lq1jRhPz4I', '', '0000-00-00', 'returned', 'accepted', 'good_condition', '2024-10-09 19:03:00'),
(20, 10, 112, 'bUaRzH3KkO', '', '0000-00-00', 'not_returned', 'accepted', NULL, '2024-10-14 16:00:00'),
(21, 10, 113, 'EJFkGWDCbT', '', '0000-00-00', 'not_returned', 'pending', NULL, '2024-10-17 12:46:16');

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
  `quantity` int(11) NOT NULL,
  `borrow_availability` int(11) NOT NULL,
  `item_condition` enum('good_condition','bad_condition','obsolete','no_longer_needed','damage','lost') DEFAULT 'good_condition',
  `alert_level` int(11) DEFAULT NULL,
  `deleted` int(11) NOT NULL,
  `date_rcvd` timestamp NULL DEFAULT NULL,
  `in_used` enum('yes','no') NOT NULL DEFAULT 'no',
  `category` enum('equipment','tools','consumables') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_details`
--

INSERT INTO `equipment_details` (`id`, `equipment_id`, `serials`, `location`, `qr_key`, `quantity`, `borrow_availability`, `item_condition`, `alert_level`, `deleted`, `date_rcvd`, `in_used`, `category`) VALUES
(32, 27, '4353', 'room 2', 'toH5enGRzc', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(33, 28, '444', '442', '4fuDzKLmpB', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(34, 29, 'k,;kl;', 'klm;mkl', 'BklGc9R7i6', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(35, 30, 'ss', 'ss', 'DBswf2GvU6', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(36, 30, 'sas', 'sasa', '3MRoFSe0Bw', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(37, 30, 'aa', 'aa', 'N8sdrGAZVx', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(38, 30, 'dd', 'dd', 'Qhz4dKVnkm', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(39, 30, 'xx', 'xx', 'eaLuGz2my0', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(40, 30, 'zxz', 'zxz', 'BLecJ76KwS', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(41, 31, '4055237', 'Laboratory R-07', 'mKBYj95MhR', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(42, 31, '1548197', 'laboratory R-07', 'XJHi3fwaDc', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(45, 31, '6278427', 'Laboratory R-07', 'XKHzkMfIur', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(46, 31, '6292908', 'Laboratory R-07', 'uMdfG5Ehvq', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(50, 31, '8495370', 'Laboratory R-07', 'Vei72mSBra', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(51, 32, '6396573', 'Laboratory R-07', 'zvgiAt3OKZ', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(52, 32, '3048560', 'Laboratory R-07', 'cry6QWHTvi', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(53, 32, '3684649', 'Laboratory R-07', '34EL9BgVWK', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(54, 32, '9193332', 'Laboratory R-07', 'Qng9HCyp6m', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(55, 33, '4782986', 'Laboratory R-07', 'bV8qNUwQ4C', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(56, 33, '2529029', 'Laboratory R-07', 'e2rjyhz9Fc', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(57, 33, '8007337', 'Laboratory R-07', 'NVS10js5Gz', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(58, 34, '6071464', 'Laboratory R-07', '2wuNS6OEvH', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(59, 34, '1852884', 'Laboratory R-07', 'Y6nufFBKkj', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(60, 34, '0776817', 'Laboratory R-07', 'qvMLBnDze9', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(61, 34, '5784586', 'Laboratory R-07', 'ZQ6AErlbeM', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(62, 35, '1644154', 'Laboratory R-07', 'ekhHCiAs4x', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(63, 35, '0586330', 'Laboratory R-07', 'RmZK4GLscb', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(64, 35, '6882535', 'Laboratory R-07', 'Am7pBrDOsS', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(65, 36, 'SMAWI-E-AWM7-15', 'Laboratory R-07', 'UKdDqtWj9o', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(66, 36, 'SMAWI-E-AWM8-15', 'Laboratory R-07', 'Tl6fihKxWF', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(67, 36, 'SMAWI-E-AWM10-15', 'Laboratory R-07', 'tCXcbeS8R9', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(68, 37, 'Control # 1-2', 'Laboratory R-07', '65Mx3Sc4lO', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(69, 37, 'Control # 2-2', 'Laboratory R-07', 'lZFvYOJQ2r', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(70, 38, '2738503', 'Laboratory R-07', 'Envlhi1fkW', 25, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(71, 38, '6202103', 'Laboratory R-07', 'gK5E3oVjer', 50, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(72, 38, '1077817', 'Laboratory R-07', 'pohgkun7D0', 50, 0, 'good_condition', 50, 0, NULL, 'no', 'equipment'),
(73, 39, '\"9870732\" ', 'Laboratory R-07', 'idQWImFk59', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(74, 39, '\"5599531\" ', 'Laboratory R-07', 'NC5OvJrz3B', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(75, 39, '0590168', 'Laboratory R-07', 'gyaHQ5fGwk', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(76, 39, '0726393', 'Laboratory R-07', 'QnmOwCg1u7', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(77, 39, '3256704', 'Laboratory R-07', 'vfW6K5oPpt', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(78, 40, 'G150908', 'Laboratory R-10', 'SBN4AkodMg', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(79, 40, 'G150906', 'Laboratory R-10', 'eOVKFgHN0Q', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(80, 40, 'G160769', 'Laboratory R-10', 'nJuLjwPm9A', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(81, 41, '5895369', 'Laboratory R-10', 'UEaFgcRGdo', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(82, 41, '7139896', 'Laboratory R-10', 'pa1sNGWiZA', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(83, 42, 'SMAWI-E-BGM1', 'Laboratory R-10', 'ky9vhrEeCq', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(84, 43, '1060412', 'Laboratory R-10', '6P2qfn5x87', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(85, 44, '5018216', 'CMDC Second Floor', 'hnLw4ORm9y', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(86, 44, '2254878', 'CMDC Second Floor', 'LHkK3GeWDt', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(87, 45, '0703312', 'CMDC Second Floor', 'fJ3APgcWOX', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(88, 45, '8284479', 'CMDC Second Floor', 'prnGm4diwF', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(89, 45, '2321877', 'CMDC Second Floor', '4k1reH0DhT', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(90, 45, '3669863', 'CMDC Second Floor', 'ILswbGMi0V', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(91, 46, '4085768', 'CMDC Second Floor', 'AIDHdz2tZx', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(92, 46, '5036245', 'CMDC Second Floor', 'U9t82Zarsj', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(93, 46, '9076345', 'CMDC Second Floor', '2QXadV9IH1', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(94, 46, '9473938', 'CMDC Second Floor', 'H7ZUMe8BYJ', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(95, 47, '6478853', 'CMDC Second Floor', 'tO41ZrBsU7', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(96, 47, '6901402', 'CMDC Second Floor', 'Ha2jGnBMv4', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(97, 47, '1392646', 'CMDC Second Floor', 'sr0CeTADlb', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(98, 48, '2638891', 'CMDC Second Floor', 'iE59slDOv3', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(99, 48, '5892446', 'CMDC Second Floor', 'dLawgU3sph', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(100, 48, '6830548', 'CMDC Second Floor', 'fFJDw3QyWZ', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(101, 48, '1708628', 'CMDC Second Floor', 'zoJERx24VM', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(102, 48, '8144425', 'CMDC Second Floor', 'HD45ajChpo', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(103, 48, '1970803', 'CMDC Second Floor', 'unFdXt9qPJ', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(104, 49, '9020175', 'CMDC Second Floor', 'gaTw6KuY0n', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(105, 49, '1176107', 'CMDC Second Floor', 'dmPjqy8CHI', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment'),
(106, 51, '002', 'room 23', 'M3xVBJqHNc', 80, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(107, 52, '32525', 'room 23', 'toARjD24Zg', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(108, 53, '0042', 'room 23', 'eFaXv9OJUi', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(110, 54, '5twe', 'room 23', 'GYFzQcBO2j', 0, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(111, 55, '2421', 'room 321', 'pn0tSo315Q', 30, 0, 'good_condition', NULL, 1, NULL, 'no', 'equipment'),
(112, 56, '111', 'ABCD', '7jod9rnBmV', 0, 0, 'good_condition', NULL, 0, NULL, 'yes', 'equipment'),
(113, 57, '122', 'Location1', 'e5L1jT93fm', 0, 0, 'good_condition', NULL, 0, NULL, 'no', 'equipment');

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

--
-- Dumping data for table `equipment_info`
--

INSERT INTO `equipment_info` (`id`, `name`, `total_quantity`, `alert_level`, `description`, `available`, `picture`, `borrowed`, `price`, `course`, `category`, `serials`, `deleted`) VALUES
(27, 'test', 0, 12, 'sata', 0, 'BbjSernB3H.jpg', 0, '12.00', 'RAC Servicing (DomRAC)', 'equipment', '', 1),
(28, 'oven', 0, 1, 'dxfg', 0, 'Rot5jiISVt.jpg', 0, '14.00', 'Basic Shielded Metal Arc Welding', 'equipment', '', 1),
(29, 'Chipping Hammer', 0, 1, 'ugi', 0, 'tg9NXA8YVa.jpeg', 0, '14.00', 'Basic Shielded Metal Arc Welding', 'equipment', '', 1),
(30, 'sas', 0, 1, 'sasa', 0, 'u1PuRa3607.jfif', 0, '2.00', 'Basic Shielded Metal Arc Welding', 'equipment', '', 1),
(31, 'Chipping Hammer', 0, 2, 'Tolsen 500g, Plastic Handle, Yellow, Power House, lotus , Butterfly, Greenfield, Wood Handle', 0, 'VGtpowawrq.jpeg', 0, '220.00', 'Basic Shielded Metal Arc Welding', 'tools', '', 0),
(32, 'Steel Brush', 0, 3, 'Wooden handle', 0, '6Ailwn4Vf5.png', 0, '80.00', 'Basic Shielded Metal Arc Welding', 'tools', '', 0),
(33, 'Plier/tongs', 0, 2, 'Ingco, 8\"  Orange 3pcs Pliers Set', 0, 'W3bIKRaxG6.jpeg', 0, '770.00', 'Basic Shielded Metal Arc Welding', 'tools', '', 0),
(34, 'Welding Mask ', 0, 3, 'Welding Mask, Plastic,Color  Black ', 0, 'eCeqSrA1L9.jpeg', 0, '265.00', 'Basic Shielded Metal Arc Welding', 'tools', '', 0),
(35, 'Oxy-acetylene Goggles', 0, 3, 'Tolsen 45075, Black, Plastic', 0, 'blKqhtak4E.jpeg', 0, '150.00', 'Basic Shielded Metal Arc Welding', 'tools', '', 0),
(36, 'Arc Welding machine AC/DC and accessories', 0, 2, 'KW-330A KorWeldInverter DC Arc Welding Machine, 330A, 17KVA, 50/60hz ', 0, 'LHAex0IBOn.png', 0, '20000.00', 'Basic Shielded Metal Arc Welding', 'equipment', '', 0),
(37, 'Electrode Oven ', 0, 1, 'Power Craft 5kg 150C', 0, 'oeTuxFyiXs.jpeg', 0, '30000.00', 'Basic Shielded Metal Arc Welding', 'equipment', '', 0),
(38, 'electrode welding rod', 0, 2, 'to create an electric arc when an electrical current passes through them, and the arc generates enough heat to melt and fuse the metals.', 0, '6FcygCuVZP.jfif', 0, '200.00', 'Basic Shielded Metal Arc Welding', 'material', '', 0),
(39, 'Portable Disc Grinder', 0, 2, 'Makita 5400W, Green Makita Grinder ', 0, 'v7QBmm1h4n.jpeg', 0, '10000.00', 'Basic Shielded Metal Arc Welding', 'equipment', '', 0),
(40, 'GMAW/MIG Welding machine, Mig gun and accessories', 0, 2, 'KW-350M KorWeld Inverter COÂ²/MAG Welding Machine, 350A, 14.5 KVA, 50/60 Hz', 0, 'ch1Hb5aL2r.png', 0, '126600.00', 'Gas metal Arc Welding -- GMAW NC I', 'equipment', '', 0),
(41, 'Oxyacetylene/ Oxy-LPG cutting outfit', 0, 0, 'Vector, Cutting Outfit, Torch, Tip, Regulator, Heating Torch, Flash, Back Arrestors, Twin Hose', 0, '9LbjfLk3v8.jpeg', 0, '4500.00', 'Gas metal Arc Welding -- GMAW NC I', 'equipment', '', 0),
(42, 'Pedestal /bench grinding machine', 0, 0, 'Bench Grinding Machine 6\" Continuous Rating Input 250W Wheel dm 150mm (6\") Wheel width 6.4mm (1/4\")/ 16mm (5/8\"), Hole dm 12.7mm (1/2\"), HP 1/3 No load speed 2850rpm (50Hz)/ 3450rpm (60Hz) Dimensions L375mm xW348mm xH286mm Net wt. 9.5Kg With Safety eye shield, Protection glasses, 2 wrenches', 0, 'uwlPBawf4U.jpeg', 0, '10000.00', 'Gas metal Arc Welding -- GMAW NC I', 'equipment', '', 0),
(43, 'Industrial fan', 0, 0, 'Standard, Green, Metal Blade SHF-24', 0, 'bGcmQC6bNf.jpeg', 0, '5000.00', 'Gas metal Arc Welding -- GMAW NC I', 'equipment', '', 0),
(44, 'Measuring cup, solid', 0, 1, 'Plastic, , 5pcs per set', 0, '0iUktQ63UX.jpg', 0, '100.00', 'Bread and pastry production NC II', 'tools', '', 0),
(45, 'Measuring cup, liquid ', 0, 2, 'Plastic, 500 ml (250ml actual size) ', 0, 'us8SpHTkE0.jpg', 0, '60.00', 'Bread and pastry production NC II', 'tools', '', 0),
(46, 'Measuring spoon', 0, 3, 'Stainless, 6 pcs per set', 0, 'GRLMCjyh0O.jpg', 0, '100.00', 'Bread and pastry production NC II', 'tools', '', 0),
(47, 'Cake turn table', 0, 1, 'white, hard plastic 14\"', 0, '8fOuRhBrz0.jpg', 0, '150.00', 'Bread and pastry production NC II', 'tools', '', 0),
(48, 'Rolling pins', 0, 2, '(xinhui) dough roller-silicon, plastic 12\"', 0, 'OEFodxDQSg.jpg', 0, '80.00', 'Bread and pastry production NC II', 'tools', '', 0),
(49, 'Pie pan sizes 10', 0, 2, 'aluminum, 10\" diam', 0, '9QhZlzEGtD.jpg', 0, '90.00', 'Bread and pastry production NC II', 'tools', '', 0),
(50, 'test equipment', 0, 1, 'testr', 0, 'Vlt7vsVLWC.png', 0, '2033.00', 'Pc operation', 'equipment', '', 1),
(51, '10 Inches Nails', 0, 20, 'pako', 0, 'HMLlgE1mKv.png', 0, '12.00', 'RAC Servicing (DomRAC)', 'material', '', 1),
(52, '10 Inches Nails', 0, 12, 'tras', 0, '4KG5NTmEsd.jpg', 0, '12.00', 'Basic Shielded Metal Arc Welding', 'equipment', '', 1),
(53, 'hammer', 0, 4, 'hammer', 0, 'MOYtWaqaGv.jpg', 0, '1.00', 'RAC Servicing (DomRAC)', 'equipment', '', 1),
(54, 'hammer', 0, 12, 'rfasf', 0, '3GUJK4ZpkC.jpg', 0, '123.00', 'RAC Servicing (DomRAC)', 'tools', '', 1),
(55, 'nail', 0, 12, 'nail', 0, 'oebUb3Zlb2.webp', 0, '12.00', 'RAC Servicing (DomRAC)', 'material', '', 1),
(56, 'AACC', 0, 5, 'awdwaaw', 0, 'FcMaua08lp.png', 0, '1111.00', 'RAC Servicing (DomRAC)', 'equipment', '', 0),
(57, 'Tool1', 0, 5, 'awdaw', 0, 'Fg4qOxjKng.png', 0, '100.00', 'RAC Servicing (DomRAC)', 'tools', '', 0),
(58, 'awdaw', 0, 0, '111', 0, 'JyzMslnO2j.png', 0, '111.00', 'Dressmaking NC II', 'equipment', 'EQU-00000016', 0);

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
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material_get_requests`
--

INSERT INTO `material_get_requests` (`request_id`, `item_id`, `quantity`, `user_id`, `qr_key`, `status`, `borrow_status`, `date_created`) VALUES
(5, 65, 0, 27, '4PXpdM3rm9', 'pending', NULL, '2024-10-14 11:30:59'),
(6, 107, 0, 27, 'm4Sq5vZ8R6', 'pending', NULL, '2024-10-14 11:33:25'),
(7, 106, 20, 10, 'nYxfjgHIR0', 'accepted', NULL, '2024-10-14 11:34:27'),
(8, 108, 0, 10, 'TCWIwlMjAQ', 'pending', NULL, '2024-10-14 12:25:35'),
(9, 110, 0, 10, 'BwzkXMUFWb', 'pending', NULL, '2024-10-14 12:28:42'),
(10, 70, 25, 27, 'UtpamXCAIW', 'accepted', NULL, '2024-10-14 17:48:51'),
(11, 111, 20, 10, 'nSmI1BfeD4', 'accepted', NULL, '2024-10-15 01:34:57'),
(12, 50, 0, 27, '013bimgzRt', 'pending', NULL, '2024-10-16 04:26:55');

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
(17, 'Ror', 'Pot', 'Rawr', 'Jr.', '2023-51124', '$2y$10$D8KqiqxLigUhvK3vLpeIy.zeFRLiNf67UDRecp5surMvITEiJvCsq', '', '', '0971452', 'Hair care', 0, NULL, 0),
(18, 'a', 'a', 'a', 'a', 'a', '$2y$10$wjra3oIAIQ3XDOC7bVn6D.07zsE29VsS.1xzSLj4ln0f.HgN65oHO', 'student', '', 'a', 'Basic Shielded Metal Arc Welding', 0, NULL, 0),
(19, 'a', 'awd', 'awd', 'awdaw', 'awdaw', '$2y$10$EP0bhpkGFAxW19M6CRJW4u6JUCYkn7gFRchZhJk/cWCCoNPsaGfL2', 'student', '', 'awdaw', 'Basic Shielded Metal Arc Welding', 0, NULL, 0),
(23, 'Jhon Orlan', 'Gene', 'Tero', 'N/a', '123456', '$2y$10$w1biDl7HSs1agWMoCppB3eLVoWJtzr3ZhLbupdbQmNaHF6HTb5IKy', 'student', '', '09751570684', 'Advanced Shielded Metal Arc Welding', 0, NULL, 1),
(24, 'Israel ', 'D', 'Silva', 'N/a', '2021-00795', '$2y$10$snK1OcCllJGW0s.Xn9Y8qu7f7YUP17UmcJLUPiqx37l4SLeULSAwC', 'student', '', '09481576302', 'Basic Shielded Metal Arc Welding', 0, NULL, 0),
(25, 'Spider', 'Web', 'Man', 'Na', '2021-12345', '$2y$10$IqEejvs2II9/HlmAVv8on.nZHR2kacwYkXqq8.CNrxQnuBjnC0iaW', '', '', '09123456784', 'Junior beautician', 0, NULL, 0),
(26, 'Test', 'New', 'Account', 'N/a', '2021-012551', '$2y$10$w1biDl7HSs1agWMoCppB3eLVoWJtzr3ZhLbupdbQmNaHF6HTb5IKy', 'instructor', '', '09814800058', 'RAC Servicing (DomRAC)', 0, NULL, 0),
(27, 'Ace', 'D', 'Silva', 'N/a', '2020-2020', '$2y$10$BYuzB4yseR5.9fnXMxEq9eLEhlok4TtuZWYOPpmBbVyUR.qBDjEk.', 'student', '', '09481576302', 'Basic Shielded Metal Arc Welding', 0, NULL, 0),
(28, 'Angella', 'L', 'Rafael', 'N/a', '2021-2021', '$2y$10$SunAiQThnOZvSFfRTyj/x.TZcB.7khu0pqeLBQLdFMS1de3o5wenu', 'instructor', '', '09481576302', 'Basic Shielded Metal Arc Welding', 0, NULL, 0),
(29, 'Awdrwrf', 'Awdfawf', 'Awrqwa', 'Awr', '2021-01256', '$2y$10$ThBbBQwF3Tkk18Dk2SHb.e2ghTBeepPrf3hXDfUxLiIaXJVB1G/AO', 'instructor', '', '09814800058', 'Gas metal Arc Welding -- GMAW NC I', 0, NULL, 0),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `equipment_details`
--
ALTER TABLE `equipment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `equipment_info`
--
ALTER TABLE `equipment_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `material_get_requests`
--
ALTER TABLE `material_get_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
