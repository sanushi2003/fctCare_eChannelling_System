-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2026 at 04:52 PM
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
(1, 'admin', '$2y$10$/f/yjilsZ3aAMRZqEk1L6uaUeI3cq2JDON7Gb.aqNLCRHb3nZnya2', '2026-09-22 16:15:39');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_name`, `patient_nic`, `patient_phone`, `patient_email`, `doctor_id`, `hospital_id`, `schedule_id`, `appointment_date`, `reason`, `doctor_fee`, `hospital_fee`, `total_payment`, `status`, `created_at`) VALUES
(1, 'sanushi nethmika', '200376411179', '+94783285295', 'sanushinethmika2003@gmail.com', 16, 11, 21, '2026-09-26', '', 3400.00, 1900.00, 5300.00, 'Completed', '2026-09-22 15:54:52'),
(2, 'University of Kelaniya', '200376411179', '+94446436868', 'sanushinethmika2003@gmail.com', 9, 2, 4, '2026-10-01', '', 3800.00, 2500.00, 6300.00, 'Cancelled', '2026-09-22 16:45:31'),
(3, 'University of Kelaniya', '200376411179', '+94111111111', 'sanushinethmika2003@gmail.com', 1, 1, 2, '2026-09-28', '', 3500.00, 2200.00, 5700.00, 'Completed', '2026-09-23 09:48:32');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `doctor_code`, `name`, `specialty`, `fee`, `created_at`) VALUES
(1, 'dr-silva', 'Dr. Aruni Silva', 'Cardiologist', 3500.00, '2026-09-22 14:06:42'),
(2, 'dr-perera', 'Dr. K.L.Perera', 'Pediatrician', 2500.00, '2026-09-22 14:06:42'),
(3, 'dr-mohamed', 'Dr. M.R.Mohommad', 'Neurologist', 3500.00, '2026-09-22 14:06:42'),
(4, 'dr-rumesh', 'Dr. D.D.Rumesh Dilhara', 'Dermatologist', 3000.00, '2026-09-22 14:06:42'),
(5, 'dr-shavindu', 'Dr. J.D.Shavindu Mewantha', 'Neurologist', 3700.00, '2026-09-22 14:06:42'),
(6, 'dr-mashi', 'Dr. Mashi S. Perera', 'Dentist', 2600.00, '2026-09-22 14:06:42'),
(7, 'dr-semini', 'Dr. Semini De Silva', 'Endocrinologist', 3300.00, '2026-09-22 14:06:42'),
(8, 'dr-viduranga', 'Dr. Viduranga Jayasinghe', 'Ophthalmologist', 3100.00, '2026-09-22 14:06:42'),
(9, 'dr-kanchana', 'Dr. Kanchana De Silva', 'Cardiologist', 3800.00, '2026-09-22 14:06:42'),
(10, 'dr-lahiru', 'Dr. R.R.Lahiru Gamage', 'Dermatologist', 3200.00, '2026-09-22 14:06:42'),
(11, 'dr-nethmika', 'Dr. S.Nethmika Kariyawasam', 'ENT Specialist', 2750.00, '2026-09-22 14:06:42'),
(12, 'dr-champika', 'Dr. S.H.Champika Ranaweera', 'Pediatrician', 2800.00, '2026-09-22 14:06:42'),
(13, 'dr-manusha', 'Dr. S.R.Manusha Damayanthi', 'Obstetrician', 3800.00, '2026-09-22 14:06:42'),
(14, 'dr-thusitha', 'Dr. Thusitha Amarasinghe', 'Cardiologist', 4000.00, '2026-09-22 14:06:42'),
(15, 'dr-chamara', 'Dr. Chamara Rajapaksha', 'Gynecologist', 3600.00, '2026-09-22 14:06:42'),
(16, 'dr-ariyarathne', 'Dr. E.M.Ariyarathne', 'Dermatologist', 3400.00, '2026-09-22 14:06:42'),
(17, 'dr-erandi', 'Dr. W.H.Erandi Nanayakkara', 'Dentist', 2900.00, '2026-09-22 14:06:42'),
(18, 'dr-ayesha', 'Dr. K.Ayesh Abeywardhana', 'Neurologist', 3900.00, '2026-09-22 14:06:42'),
(19, 'dr-muditha', 'Dr. H.R.Muditha Weerakkody', 'Gynecologist', 3950.00, '2026-09-22 14:06:42'),
(20, 'dr-aruna', 'Dr. Aruna Bandara', 'Orthopedic Surgeon', 3750.00, '2026-09-22 14:06:42'),
(21, 'dr-ruwan', 'Dr. Ruwan Dissanayake', 'Ophthalmologist', 3450.00, '2026-09-22 14:06:42'),
(22, 'dr-fathima', 'Dr. Fathima Azra', 'Oncologist', 3400.00, '2026-09-22 14:06:42'),
(23, 'dr-krishan', 'Dr. Krishna Kumar', 'Psychiatrist', 3300.00, '2026-09-22 14:06:42'),
(24, 'dr-chandrasena', 'Dr. W.Chandrasena', 'Hematologist', 2950.00, '2026-09-22 14:06:42'),
(25, 'dr-fernando', 'Dr. R.A.M.Fernando', 'Pulmonologist', 3100.00, '2026-09-22 14:06:42'),
(26, 'dr-hiruna', 'Dr. Hiruna Jayasinghe', 'Orthopedic Surgeon', 3850.00, '2026-09-22 14:06:42'),
(27, 'dr-chandana', 'Dr. Chandana Dahanayake', 'Hematologist', 3250.00, '2026-09-22 14:06:42'),
(28, 'dr-radhika', 'Dr. Radhika Fernando', 'Endocrinologist', 3600.00, '2026-09-22 14:06:42'),
(29, 'dr-madhusha', 'Dr. W.A.Madhusha Peris', 'Oncologist', 3650.00, '2026-09-22 14:06:42'),
(30, 'dr-prathibha', 'Dr. J.Prathibha Samarakkodi', 'Pulmonologist', 3500.00, '2026-09-22 14:06:42'),
(31, 'dr-lal', 'Dr. Lal Fernando', 'Urologist', 3600.00, '2026-09-22 14:06:42');

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
(4, 2, 3),
(6, 3, 7),
(8, 4, 9),
(7, 5, 8),
(11, 6, 12),
(2, 9, 2),
(9, 10, 10),
(5, 12, 4),
(3, 14, 5),
(10, 16, 11),
(12, 17, 13),
(18, 31, 1),
(16, 31, 4),
(17, 31, 8);

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
(1, 'city-care', 'City Care General Hospital', 'Colombo', '011-2345678', '24/7 Emergency, ICU, Lab Services', 2200.00, '2026-09-22 14:34:35'),
(2, 'lanka', 'Lanka Hospital', 'Colombo', '011-5430000', 'OPD Clinic, Pharmacy', 2500.00, '2026-09-22 14:34:35'),
(3, 'coop-gampaha', 'Co-operative Hospital', 'Gampaha', '033-2223456', 'Emergency care & OPD, Pharmacy, Ambulance', 1200.00, '2026-09-22 14:34:35'),
(4, 'apex', 'Apex Healthcare Center', 'Gampaha', '011-2912345', 'OPD Clinic, Pharmacy, Radiology', 1000.00, '2026-09-22 14:34:35'),
(5, 'hemas', 'Hemas Hospital', 'Gampaha', '011-7888888', 'OPD Clinic, Pharmacy, Radiology, Laboratory', 2000.00, '2026-09-22 14:34:35'),
(6, 'coop-matara', 'Co-operative Hospital', 'Matara', '041-2223456', 'Emergency care & OPD, Pharmacy, Ambulance', 1100.00, '2026-09-22 14:34:35'),
(7, 'ruhunu-matara', 'Ruhunu Hospital', 'Matara', '041-2225777', 'OPD Clinic, Laboratory, Radiology', 1500.00, '2026-09-22 14:34:35'),
(8, 'asiri-matara', 'Asiri Hospital', 'Matara', '041-4390000', 'Diagnostic, Pharmacy, Surgical, Laboratory', 1800.00, '2026-09-22 14:34:35'),
(9, 'coop-galle', 'Co-operative Hospital', 'Galle', '091-2234567', 'Emergency care & OPD, Pharmacy, Ambulance', 1300.00, '2026-09-22 14:34:35'),
(10, 'ruhunu-galle', 'Ruhunu Hospital', 'Galle', '091-2235777', 'OPD Clinic, Laboratory, Radiology', 1600.00, '2026-09-22 14:34:35'),
(11, 'asiri-galle', 'Asiri Hospital', 'Galle', '091-4640640', 'Diagnostic, Pharmacy, Surgical, Laboratory', 1900.00, '2026-09-22 14:34:35'),
(12, 'queensbury', 'Queensbury Hospital', 'Galle', '091-2228494', 'OPD Clinic, In House Pharmacy', 1500.00, '2026-09-22 14:34:35'),
(13, 'sahana', 'Sahana Hospital', 'Galle', '091-2246966', 'OPD Clinic, Pharmacy', 1350.00, '2026-09-22 14:34:35');

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
(1, 'University', 'sanushinethmika2003@gmail.com', 'esfgfcjghgvjg', 'Read', '2026-09-22 16:12:36'),
(2, 'University', 'sanushinethmika2003@gmail.com', 'I need more information about the appointment system.', 'Read', '2026-09-23 05:08:59');

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
(27, 4, 26, 'Saturday', '12:00:00', '17:00:00', 'Active');

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
  ADD UNIQUE KEY `doctor_id` (`doctor_id`,`hospital_id`),
  ADD KEY `hospital_id` (`hospital_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedules_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
