-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2026 at 11:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance_portal_beenishtasaffar`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `att_id` int(11) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `th_regid` varchar(255) NOT NULL,
  `att_status` enum('Present','Absent') DEFAULT NULL,
  `att_date` date DEFAULT NULL,
  `att_timerecorded` time DEFAULT NULL,
  `att_type` varchar(255) DEFAULT NULL,
  `stu_regId` varchar(255) NOT NULL,
  `leave_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`att_id`, `course_id`, `th_regid`, `att_status`, `att_date`, `att_timerecorded`, `att_type`, `stu_regId`, `leave_id`) VALUES
(329, 'CSE101', 'CS001', 'Present', '2026-05-20', '09:46:32', 'Makeup', '221775', NULL),
(330, 'CSE101', 'CS001', 'Absent', '2026-05-20', '09:46:32', 'Makeup', '221847', NULL),
(331, 'CSE101', 'CS001', 'Absent', '2026-05-20', '09:46:32', 'Makeup', '221899', NULL),
(332, 'CSE101', 'CS001', 'Present', '2026-05-20', '09:46:32', 'Makeup', '221775', NULL),
(333, 'CSE101', 'CS001', 'Present', '2026-02-10', '22:12:32', 'Regular', '221775', NULL),
(334, 'CSE101', 'CS001', 'Present', '2026-02-10', '22:12:32', 'Regular', '221847', NULL),
(335, 'CSE101', 'CS001', 'Present', '2026-02-10', '22:12:32', 'Regular', '221899', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`att_id`),
  ADD KEY `FK_attendance_course_id` (`course_id`),
  ADD KEY `FK_attendance_th_regid` (`th_regid`),
  ADD KEY `FK_attendance_stu_regid` (`stu_regId`),
  ADD KEY `fk_leave_id` (`leave_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `att_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `FK_attendance_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `FK_attendance_stu_regid` FOREIGN KEY (`stu_regId`) REFERENCES `students` (`stu_regId`),
  ADD CONSTRAINT `FK_attendance_th_regid` FOREIGN KEY (`th_regid`) REFERENCES `teachers` (`th_regId`),
  ADD CONSTRAINT `fk_leave_id` FOREIGN KEY (`leave_id`) REFERENCES `leave_application` (`leave_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
