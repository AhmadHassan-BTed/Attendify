-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2024 at 05:17 PM
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
-- Database: `air_attendance_portal_byted`
--

-- --------------------------------------------------------

--
-- Table structure for table `atten`
--

CREATE TABLE `atten` (
  `Name` varchar(140) NOT NULL,
  `Status` varchar(150) NOT NULL,
  `Reg_Id` int(12) NOT NULL,
  `percentage` decimal(12,0) NOT NULL,
  `semester` int(12) NOT NULL,
  `reqStatus` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `atten`
--

INSERT INTO `atten` (`Name`, `Status`, `Reg_Id`, `percentage`, `semester`, `reqStatus`) VALUES
('Nimra Marrium', 'absent', 22237, 100, 6, 'Live'),
('Voldemort', 'Leave', 221312, 69, 9, 'accepted'),
('Abdullah Hassan', 'Present', 221754, 21, 1, 'accepted'),
('Ahmad Hassan', 'Absent', 221775, 89, 4, 'rejected'),
('Faraz Ashraf', 'present', 221847, 74, 6, 'present');

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
(329, 'CSE101', 'CS001', 'Absent', '2024-05-20', '09:46:32', 'Makeup', '221775', NULL),
(330, 'CSE101', 'CS001', 'Absent', '2024-05-20', '09:46:32', 'Makeup', '221847', NULL),
(331, 'CSE101', 'CS001', 'Absent', '2024-05-20', '09:46:32', 'Makeup', '221899', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` varchar(255) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_credit_hour` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_code`, `course_credit_hour`, `department_id`) VALUES
('CSE101', 'Introduction to Programming', 'CS-101', 3, 1),
('CSE220', 'Data Structures and Algorithms', 'CS-220', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dep_id` int(11) NOT NULL,
  `dep_name` varchar(255) NOT NULL,
  `dep_dean` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dep_id`, `dep_name`, `dep_dean`) VALUES
(1, 'Computer Science', 'Dr. Ahsan Ali'),
(2, 'Electrical Engineering', 'Dr. Nadia Khan'),
(3, 'Mathematics', 'Dr. Ali Hassan');

-- --------------------------------------------------------

--
-- Table structure for table `leave_application`
--

CREATE TABLE `leave_application` (
  `leave_id` int(11) NOT NULL,
  `stu_regId` varchar(255) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `leave_title` varchar(255) NOT NULL,
  `leave_date` date NOT NULL,
  `leave_message` text DEFAULT NULL,
  `request_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `leave_application`
--

INSERT INTO `leave_application` (`leave_id`, `stu_regId`, `course_id`, `leave_title`, `leave_date`, `leave_message`, `request_status`) VALUES
(1, '221775', 'CSE101', 'Sickness', '2024-05-17', 'I\'m sick of your sweat suits and cornyAss lets talk about it', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `th_regId` varchar(255) NOT NULL,
  `l_username` varchar(255) NOT NULL,
  `l_password` varchar(255) NOT NULL,
  `l_recmail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`th_regId`, `l_username`, `l_password`, `l_recmail`) VALUES
('CS001', 'ted', 'ted', 'ted@gmail.com'),
('CS001', 'ted', 'ted', 'ted@gmail.com'),
('CS002', 'emial', 'username', 'pass');

-- --------------------------------------------------------

--
-- Table structure for table `studentregisteredcourses`
--

CREATE TABLE `studentregisteredcourses` (
  `stu_regId` varchar(255) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `src_totalPresent` int(11) DEFAULT NULL,
  `src_dateRegistered` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `studentregisteredcourses`
--

INSERT INTO `studentregisteredcourses` (`stu_regId`, `course_id`, `src_totalPresent`, `src_dateRegistered`) VALUES
('221775', 'CSE101', 11, '0000-00-00'),
('221847', 'CSE101', 11, '0000-00-00'),
('221899', 'CSE101', 12, '2024-05-01'),
('221909', 'CSE220', 12, '2024-05-08');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `stu_name` varchar(255) NOT NULL,
  `stu_regId` varchar(255) NOT NULL,
  `stu_email` varchar(255) NOT NULL,
  `stu_password` varchar(255) NOT NULL,
  `stu_batch` varchar(255) NOT NULL,
  `stu_section` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`stu_name`, `stu_regId`, `stu_email`, `stu_password`, `stu_batch`, `stu_section`) VALUES
('Ahmad Hassan', '221775', 'ahmad@gmail.com', 'password123', '2024', 'A'),
('Faraz Ashraf', '221847', 'faraz@gmail.com', 'password456', '2024', 'A'),
('Nimra Marrium', '221899', 'nimra@female.com', 'password789', '2024', 'A'),
('Hafiz B', '221909', 'sectionb.com', 'knasdnfkj', '2024', 'B'),
('Hafiz BB', '2219809', 'sklksd@gmailcom', 'pasokdjf', '2024', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `teacherregisteredcourses`
--

CREATE TABLE `teacherregisteredcourses` (
  `th_regId` varchar(255) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `trc_dateRegistered` date NOT NULL,
  `trc_totalClassesTaken` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `teacherregisteredcourses`
--

INSERT INTO `teacherregisteredcourses` (`th_regId`, `course_id`, `trc_dateRegistered`, `trc_totalClassesTaken`) VALUES
('CS001', 'CSE101', '2024-05-07', 32);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `th_regId` varchar(255) NOT NULL,
  `th_name` varchar(255) NOT NULL,
  `dep_id` int(11) NOT NULL,
  `th_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`th_regId`, `th_name`, `dep_id`, `th_email`) VALUES
('CS001', 'Dr. Michael Smith', 1, 'michael.smith@university.edu'),
('CS002', 'Ms. Sophia Garcia', 1, 'sophia.garcia@university.edu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atten`
--
ALTER TABLE `atten`
  ADD PRIMARY KEY (`Reg_Id`);

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
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dep_id`);

--
-- Indexes for table `leave_application`
--
ALTER TABLE `leave_application`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `FK_leave_application_course_id` (`course_id`),
  ADD KEY `FK_leave_application_stu_regld` (`stu_regId`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD KEY `FK_login_th_regid` (`th_regId`);

--
-- Indexes for table `studentregisteredcourses`
--
ALTER TABLE `studentregisteredcourses`
  ADD KEY `FK_studentRegisteredCourses_stu_regld` (`stu_regId`),
  ADD KEY `FK_studentRegisteredCourses_course_id` (`course_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`stu_regId`);

--
-- Indexes for table `teacherregisteredcourses`
--
ALTER TABLE `teacherregisteredcourses`
  ADD KEY `th_regId` (`th_regId`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`th_regId`),
  ADD KEY `FK_teachers_dep_id` (`dep_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `att_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=332;

--
-- AUTO_INCREMENT for table `leave_application`
--
ALTER TABLE `leave_application`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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

--
-- Constraints for table `leave_application`
--
ALTER TABLE `leave_application`
  ADD CONSTRAINT `FK_leave_application_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `FK_leave_application_stu_regld` FOREIGN KEY (`stu_regId`) REFERENCES `students` (`stu_regId`);

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `FK_login_th_regid` FOREIGN KEY (`th_regId`) REFERENCES `teachers` (`th_regId`);

--
-- Constraints for table `studentregisteredcourses`
--
ALTER TABLE `studentregisteredcourses`
  ADD CONSTRAINT `FK_studentRegisteredCourses_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `FK_studentRegisteredCourses_stu_regld` FOREIGN KEY (`stu_regId`) REFERENCES `students` (`stu_regId`);

--
-- Constraints for table `teacherregisteredcourses`
--
ALTER TABLE `teacherregisteredcourses`
  ADD CONSTRAINT `teacherregisteredcourses_ibfk_1` FOREIGN KEY (`th_regId`) REFERENCES `teachers` (`th_regId`),
  ADD CONSTRAINT `teacherregisteredcourses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `FK_teachers_dep_id` FOREIGN KEY (`dep_id`) REFERENCES `department` (`dep_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
