-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2026 at 05:17 PM
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
-- Database: `fctcare_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$/f/yjilsZ3aAMRZqEk1L6uaUeI3cq2JDON7Gb.aqNLCRHb3nZnya2', '2026-09-22 10:45:39');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_name` varchar(150) NOT NULL,
  `patient_nic` varchar(20) NOT NULL,
  `patient_phone` varchar(20) NOT NULL,
  `patient_email` varchar(150) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `doctor_fee` decimal(10,2) NOT NULL,
  `hospital_fee` decimal(10,2) NOT NULL,
  `total_payment` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `patient_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_name`, `patient_nic`, `patient_phone`, `patient_email`, `doctor_id`, `hospital_id`, `schedule_id`, `appointment_date`, `reason`, `doctor_fee`, `hospital_fee`, `total_payment`, `status`, `created_at`, `patient_id`) VALUES
(1, 'sanushi nethmika', '200376411179', '+94783285295', 'sanushinethmika2003@gmail.com', 16, 11, 21, '2026-09-26', '', 3400.00, 1900.00, 5300.00, 'Completed', '2026-09-22 10:24:52', 2),
(2, 'University of Kelaniya', '200376411179', '+94446436868', 'sanushinethmika2003@gmail.com', 9, 2, 4, '2026-10-01', '', 3800.00, 2500.00, 6300.00, 'Cancelled', '2026-09-22 11:15:31', 2),
(3, 'University of Kelaniya', '200376411179', '+94111111111', 'sanushinethmika2003@gmail.com', 1, 1, 2, '2026-09-28', '', 3500.00, 2200.00, 5700.00, 'Completed', '2026-09-23 04:18:32', 2),
(4, 'Nelika neelanii', '200376411178', '+94147852369', 'sanushinethmika2004@gmail.com', 1, 1, 2, '2026-10-19', '', 3500.00, 2200.00, 5700.00, 'Confirmed', '2026-10-13 15:20:02', 3),
(5, 'Ayush Nethsara', '200376411178', '+94456123978', 'sanushinethmika2004@gmail.com', 1, 1, 1, '2026-10-26', '', 3500.00, 2200.00, 5700.00, 'Confirmed', '2026-10-21 11:02:47', 2),
(6, 'University of Kelaniya', '200376411179', '+94123456789', 'sanushinethmika2003@gmail.com', 1, 5, 31, '2026-10-26', '', 3500.00, 2000.00, 5500.00, 'Pending', '2026-10-21 14:50:38', 2);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_type` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(150) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `doctor_code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `availability` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `doctor_code`, `name`, `specialty`, `fee`, `created_at`, `availability`) VALUES
(1, 'dr-silva', 'Dr. Aruni Silva', 'Cardiologist', 3500.00, '2026-09-22 08:36:42', 'Monday'),
(2, 'dr-perera', 'Dr. K.L.Perera', 'Pediatrician', 2500.00, '2026-09-22 08:36:42', 'Tuesday'),
(3, 'dr-mohamed', 'Dr. M.R.Mohommad', 'Neurologist', 3500.00, '2026-09-22 08:36:42', 'Wednesday'),
(4, 'dr-rumesh', 'Dr. D.D.Rumesh Dilhara', 'Dermatologist', 3000.00, '2026-09-22 08:36:42', 'Thursday'),
(5, 'dr-shavindu', 'Dr. J.D.Shavindu Mewantha', 'Neurologist', 3700.00, '2026-09-22 08:36:42', 'Friday'),
(6, 'dr-mashi', 'Dr. Mashi S. Perera', 'Dentist', 2600.00, '2026-09-22 08:36:42', 'Monday'),
(7, 'dr-semini', 'Dr. Semini De Silva', 'Endocrinologist', 3300.00, '2026-09-22 08:36:42', 'Monday'),
(8, 'dr-viduranga', 'Dr. Viduranga Jayasinghe', 'Ophthalmologist', 3100.00, '2026-09-22 08:36:42', 'Tuesday'),
(9, 'dr-kanchana', 'Dr. Kanchana De Silva', 'Cardiologist', 3800.00, '2026-09-22 08:36:42', 'Tuesday'),
(10, 'dr-lahiru', 'Dr. R.R.Lahiru Gamage', 'Dermatologist', 3200.00, '2026-09-22 08:36:42', 'Wednesday'),
(11, 'dr-nethmika', 'Dr. S.Nethmika Kariyawasam', 'ENT Specialist', 2750.00, '2026-09-22 08:36:42', 'Monday'),
(12, 'dr-champika', 'Dr. S.H.Champika Ranaweera', 'Pediatrician', 2800.00, '2026-09-22 08:36:42', 'Thursday'),
(13, 'dr-manusha', 'Dr. S.R.Manusha Damayanthi', 'Obstetrician', 3800.00, '2026-09-22 08:36:42', 'Friday'),
(14, 'dr-thusitha', 'Dr. Thusitha Amarasinghe', 'Cardiologist', 4000.00, '2026-09-22 08:36:42', 'Monday'),
(15, 'dr-chamara', 'Dr. Chamara Rajapaksha', 'Gynecologist', 3600.00, '2026-09-22 08:36:42', 'Tuesday'),
(16, 'dr-ariyarathne', 'Dr. E.M.Ariyarathne', 'Dermatologist', 3400.00, '2026-09-22 08:36:42', 'Wednesday'),
(17, 'dr-erandi', 'Dr. W.H.Erandi Nanayakkara', 'Dentist', 2900.00, '2026-09-22 08:36:42', 'Thursday'),
(18, 'dr-ayesha', 'Dr. K.Ayesh Abeywardhana', 'Neurologist', 3900.00, '2026-09-22 08:36:42', 'Friday'),
(19, 'dr-muditha', 'Dr. H.R.Muditha Weerakkody', 'Gynecologist', 3950.00, '2026-09-22 08:36:42', 'Monday'),
(20, 'dr-aruna', 'Dr. Aruna Bandara', 'Orthopedic Surgeon', 3750.00, '2026-09-22 08:36:42', 'Tuesday'),
(21, 'dr-ruwan', 'Dr. Ruwan Dissanayake', 'Ophthalmologist', 3450.00, '2026-09-22 08:36:42', 'Wednesday'),
(22, 'dr-fathima', 'Dr. Fathima Azra', 'Oncologist', 3400.00, '2026-09-22 08:36:42', 'Thursday'),
(23, 'dr-krishan', 'Dr. Krishna Kumar', 'Psychiatrist', 3300.00, '2026-09-22 08:36:42', 'Friday'),
(24, 'dr-chandrasena', 'Dr. W.Chandrasena', 'Hematologist', 2950.00, '2026-09-22 08:36:42', 'Monday'),
(25, 'dr-fernando', 'Dr. R.A.M.Fernando', 'Pulmonologist', 3100.00, '2026-09-22 08:36:42', 'Tuesday'),
(26, 'dr-hiruna', 'Dr. Hiruna Jayasinghe', 'Orthopedic Surgeon', 3850.00, '2026-09-22 08:36:42', NULL),
(27, 'dr-chandana', 'Dr. Chandana Dahanayake', 'Hematologist', 3250.00, '2026-09-22 08:36:42', NULL),
(28, 'dr-radhika', 'Dr. Radhika Fernando', 'Endocrinologist', 3600.00, '2026-09-22 08:36:42', NULL),
(29, 'dr-madhusha', 'Dr. W.A.Madhusha Peris', 'Oncologist', 3650.00, '2026-09-22 08:36:42', NULL),
(30, 'dr-prathibha', 'Dr. J.Prathibha Samarakkodi', 'Pulmonologist', 3500.00, '2026-09-22 08:36:42', NULL),
(31, 'dr-lal', 'Dr. Lal Fernando', 'Urologist', 3600.00, '2026-09-22 08:36:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_hospitals`
--

CREATE TABLE `doctor_hospitals` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_hospitals`
--

INSERT INTO `doctor_hospitals` (`id`, `doctor_id`, `hospital_id`) VALUES
(1, 1, 1),
(34, 1, 5),
(28, 1, 8),
(4, 2, 3),
(24, 2, 9),
(6, 3, 7),
(19, 4, 2),
(8, 4, 9),
(7, 5, 8),
(26, 5, 12),
(20, 6, 3),
(11, 6, 12),
(22, 7, 5),
(21, 8, 4),
(2, 9, 2),
(25, 9, 10),
(9, 10, 10),
(23, 11, 6),
(5, 12, 4),
(3, 14, 5),
(10, 16, 11),
(12, 17, 13),
(27, 26, 4),
(18, 31, 1),
(16, 31, 4),
(17, 31, 8);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_users`
--

CREATE TABLE `doctor_users` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_users`
--

INSERT INTO `doctor_users` (`id`, `doctor_id`, `email`, `password`, `status`, `created_at`) VALUES
(1, 1, 'aruni@example.com', '$2y$10$Ps4N5CsxtKtcB.Dd8u5oiuLbh1td4s4N4Kh5OZPC.zuOPR0Wck3Nq', 'Active', '2026-10-11 20:09:46');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `hospital_code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `district` varchar(50) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `hospital_code`, `name`, `district`, `telephone`, `facilities`, `fee`, `created_at`) VALUES
(1, 'city-care', 'City Care General Hospital', 'Colombo', '011-2345678', '24/7 Emergency, ICU, Lab Services', 2200.00, '2026-09-22 09:04:35'),
(2, 'lanka', 'Lanka Hospital', 'Colombo', '011-5430000', 'OPD Clinic, Pharmacy', 2500.00, '2026-09-22 09:04:35'),
(3, 'coop-gampaha', 'Co-operative Hospital', 'Gampaha', '033-2223456', 'Emergency care & OPD, Pharmacy, Ambulance', 1200.00, '2026-09-22 09:04:35'),
(4, 'apex', 'Apex Healthcare Center', 'Gampaha', '011-2912345', 'OPD Clinic, Pharmacy, Radiology', 1000.00, '2026-09-22 09:04:35'),
(5, 'hemas', 'Hemas Hospital', 'Gampaha', '011-7888888', 'OPD Clinic, Pharmacy, Radiology, Laboratory', 2000.00, '2026-09-22 09:04:35'),
(6, 'coop-matara', 'Co-operative Hospital', 'Matara', '041-2223456', 'Emergency care & OPD, Pharmacy, Ambulance', 1100.00, '2026-09-22 09:04:35'),
(7, 'ruhunu-matara', 'Ruhunu Hospital', 'Matara', '041-2225777', 'OPD Clinic, Laboratory, Radiology', 1500.00, '2026-09-22 09:04:35'),
(8, 'asiri-matara', 'Asiri Hospital', 'Matara', '041-4390000', 'Diagnostic, Pharmacy, Surgical, Laboratory', 1800.00, '2026-09-22 09:04:35'),
(9, 'coop-galle', 'Co-operative Hospital', 'Galle', '091-2234567', 'Emergency care & OPD, Pharmacy, Ambulance', 1300.00, '2026-09-22 09:04:35'),
(10, 'ruhunu-galle', 'Ruhunu Hospital', 'Galle', '091-2235777', 'OPD Clinic, Laboratory, Radiology', 1600.00, '2026-09-22 09:04:35'),
(11, 'asiri-galle', 'Asiri Hospital', 'Galle', '091-4640640', 'Diagnostic, Pharmacy, Surgical, Laboratory', 1900.00, '2026-09-22 09:04:35'),
(12, 'queensbury', 'Queensbury Hospital', 'Galle', '091-2228494', 'OPD Clinic, In House Pharmacy', 1500.00, '2026-09-22 09:04:35'),
(13, 'sahana', 'Sahana Hospital', 'Galle', '091-2246966', 'OPD Clinic, Pharmacy', 1350.00, '2026-09-22 09:04:35');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `message`, `status`, `created_at`) VALUES
(1, 'University', 'sanushinethmika2003@gmail.com', 'esfgfcjghgvjg', 'Read', '2026-09-22 10:42:36'),
(2, 'University', 'sanushinethmika2003@gmail.com', 'I need more information about the appointment system.', 'Read', '2026-09-22 23:38:59');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_type` enum('patient','doctor','admin') NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `nic` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `gender` enum('Male','Female','Other') DEFAULT 'Other',
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `full_name`, `nic`, `phone`, `email`, `password`, `created_at`, `status`, `gender`, `address`, `date_of_birth`) VALUES
(2, 'sanushi nethmika', '200376411179', '0783285295', 'sanushinethmika2003@gmail.com', '$2y$10$rxCA44D79AfU9jUUZkvqmOOTdAfb42zPUThtluNe2bwKEZ5KHXPdK', '2026-10-11 20:49:01', 'Active', 'Female', 'university of kelaniya, sri lanka', '2003-09-20'),
(3, 'Nelika Neelanii', '200376411178', '0783285295', 'sanushinethmika2004@gmail.com', '$2y$10$0YYJL0QGVlOgCh6T9NIoXulohh.OkULfnNn22Uo5YnJZZ0WlVd.RO', '2026-10-13 15:16:32', 'Active', 'Female', 'matara , sri lanka', '2001-05-07');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `day_of_week` varchar(20) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `hospital_id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`, `status`) VALUES
(1, 1, 1, 'Monday', '08:30:00', '11:30:00', 'Active'),
(2, 1, 1, 'Monday', '15:30:00', '18:30:00', 'Active'),
(3, 2, 4, 'Tuesday', '12:00:00', '15:00:00', 'Active'),
(4, 2, 4, 'Tuesday', '18:30:00', '21:30:00', 'Active'),
(5, 3, 6, 'Wednesday', '08:30:00', '11:30:00', 'Active'),
(6, 3, 6, 'Wednesday', '18:30:00', '21:30:00', 'Active'),
(7, 4, 8, 'Thursday', '12:00:00', '15:00:00', 'Active'),
(8, 4, 8, 'Thursday', '15:30:00', '18:30:00', 'Active'),
(9, 5, 7, 'Friday', '08:30:00', '11:30:00', 'Active'),
(10, 5, 7, 'Friday', '15:30:00', '18:30:00', 'Active'),
(11, 6, 11, 'Monday', '12:00:00', '15:00:00', 'Active'),
(12, 6, 11, 'Monday', '18:30:00', '21:30:00', 'Active'),
(13, 7, NULL, NULL, '08:30:00', '11:30:00', 'Active'),
(14, 7, NULL, NULL, '15:30:00', '18:30:00', 'Active'),
(15, 8, NULL, NULL, '15:30:00', '18:30:00', 'Active'),
(16, 8, NULL, NULL, '18:30:00', '21:30:00', 'Active'),
(17, 9, 2, 'Tuesday', '08:30:00', '11:30:00', 'Active'),
(18, 9, 2, 'Tuesday', '18:30:00', '21:30:00', 'Active'),
(19, 10, 9, 'Wednesday', '12:00:00', '15:00:00', 'Active'),
(20, 10, 9, 'Wednesday', '15:30:00', '18:30:00', 'Active'),
(21, 11, NULL, NULL, '08:30:00', '11:30:00', 'Active'),
(22, 11, NULL, NULL, '15:30:00', '18:30:00', 'Active'),
(23, 12, 5, 'Thursday', '12:00:00', '15:00:00', 'Active'),
(24, 12, 5, 'Thursday', '18:30:00', '21:30:00', 'Active'),
(25, 13, NULL, NULL, '15:30:00', '18:30:00', 'Active'),
(26, 13, NULL, NULL, '18:30:00', '21:30:00', 'Active'),
(27, 4, 26, 'Saturday', '12:00:00', '17:00:00', 'Active'),
(28, 8, 1, 'Thursday', '08:30:00', '17:00:00', 'Active'),
(30, 1, 31, 'Wednesday', '17:00:00', '20:00:00', 'Active'),
(31, 5, 1, 'Monday', '04:00:00', '10:00:00', 'Active');

--
-- Triggers `schedules`
--
DELIMITER $$
CREATE TRIGGER `schedules_assign_after_insert` AFTER INSERT ON `schedules` FOR EACH ROW INSERT INTO doctor_hospitals (doctor_id, hospital_id)
SELECT NEW.doctor_id, NEW.hospital_id
FROM DUAL
WHERE NEW.doctor_id IS NOT NULL
  AND NEW.hospital_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1
      FROM doctor_hospitals
      WHERE doctor_id   = NEW.doctor_id
        AND hospital_id = NEW.hospital_id
  )
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `schedules_assign_after_update` AFTER UPDATE ON `schedules` FOR EACH ROW INSERT INTO doctor_hospitals (doctor_id, hospital_id)
SELECT NEW.doctor_id, NEW.hospital_id
FROM DUAL
WHERE NEW.doctor_id IS NOT NULL
  AND NEW.hospital_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1
      FROM doctor_hospitals
      WHERE doctor_id   = NEW.doctor_id
        AND hospital_id = NEW.hospital_id
  )
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctor_code` (`doctor_code`);

--
-- Indexes for table `doctor_hospitals`
--
ALTER TABLE `doctor_hospitals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_doctor_hospital` (`doctor_id`,`hospital_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `doctor_users`
--
ALTER TABLE `doctor_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctor_id` (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hospital_code` (`hospital_code`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `fk_schedules_doctor` (`doctor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `doctor_hospitals`
--
ALTER TABLE `doctor_hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `doctor_users`
--
ALTER TABLE `doctor_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`);

--
-- Constraints for table `doctor_hospitals`
--
ALTER TABLE `doctor_hospitals`
  ADD CONSTRAINT `doctor_hospitals_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_hospitals_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_users`
--
ALTER TABLE `doctor_users`
  ADD CONSTRAINT `doctor_users_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedules_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
