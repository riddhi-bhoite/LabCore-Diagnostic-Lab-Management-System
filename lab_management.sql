-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2026 at 07:39 AM
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
-- Database: `lab_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `Booking_ID` int(11) NOT NULL,
  `Patient_ID` int(11) NOT NULL,
  `Doctor_ID` int(11) NOT NULL,
  `Booking_Date` date NOT NULL,
  `Booking_Status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `Total_Amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Payment_Status` enum('Unpaid','Paid','Partial') NOT NULL DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_test`
--

CREATE TABLE `booking_test` (
  `Booking_Test_ID` int(11) NOT NULL,
  `Booking_ID` int(11) NOT NULL,
  `Test_ID` int(11) NOT NULL,
  `Test_Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `Doctor_ID` int(11) NOT NULL,
  `Person_ID` int(11) NOT NULL,
  `Specialization` varchar(100) NOT NULL,
  `Hospital_Name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`Doctor_ID`, `Person_ID`, `Specialization`, `Hospital_Name`) VALUES
(3, 11, 'Oncologist', 'LabCore Diagnostics');

-- --------------------------------------------------------

--
-- Table structure for table `lab_technician`
--

CREATE TABLE `lab_technician` (
  `Staff_ID` int(11) NOT NULL,
  `Person_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_technician`
--

INSERT INTO `lab_technician` (`Staff_ID`, `Person_ID`) VALUES
(2, 12);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `Patient_ID` int(11) NOT NULL,
  `Person_ID` int(11) NOT NULL,
  `Blood_Group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `Address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`Patient_ID`, `Person_ID`, `Blood_Group`, `Address`) VALUES
(4, 13, 'O+', 'Pune, Maharashtra');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL,
  `Booking_ID` int(11) NOT NULL,
  `Amount_Paid` decimal(10,2) NOT NULL,
  `Payment_Date` date NOT NULL DEFAULT curdate(),
  `Payment_Mode` enum('Cash','Card','UPI','Net_Banking','Cheque') NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `Person_ID` int(11) NOT NULL,
  `First_Name` varchar(50) NOT NULL,
  `Last_Name` varchar(50) NOT NULL,
  `DOB` date NOT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `Phone_No` varchar(15) NOT NULL,
  `Email_ID` varchar(100) NOT NULL,
  `Age` int(11) GENERATED ALWAYS AS (year(curdate()) - year(`DOB`)) VIRTUAL,
  `Role` enum('Patient','Doctor','Lab_Technician','Admin') NOT NULL,
  `Password_Hash` varchar(255) NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`Person_ID`, `First_Name`, `Last_Name`, `DOB`, `Gender`, `Phone_No`, `Email_ID`, `Role`, `Password_Hash`, `Created_At`) VALUES
(10, 'Admin', 'User', '2000-01-01', 'Other', '9999999999', 'admin@labcore.com', 'Admin', '$2y$10$iO6I9mDMGPlni79nmoh2HuUfCQ6k0VmMkQmKmvz/cxVAROjGkDdgC', '2026-07-04 04:55:12'),
(11, 'Riddhi', 'Bhoite', '1988-06-15', 'Female', '9999999992', 'doctor@labcore.com', 'Doctor', '$2y$10$TEHKmRs0Uv06xdKyLGSA6uHRS3FbeCSlZEn.9klYTam7NyMRgYs3a', '2026-07-04 05:05:50'),
(12, 'Siddhi Bhoite', 'Patil', '1998-09-20', 'Female', '9999999993', 'technician@labcore.com', 'Lab_Technician', '$2y$10$PBf3ReTreHh8hS4vN6F8auW.HIQxGHOZz7mJri0kLIN6cykoLSvyO', '2026-07-04 05:05:50'),
(13, 'Ananya', 'Deshmukh', '2002-03-10', 'Female', '9999999994', 'patient@labcore.com', 'Patient', '$2y$10$BHgnzSX0U9CO4bTiC3C.ReNT6jd.5KDzdfdt6JwIdmn7VogtY7h1W', '2026-07-04 05:05:51');

-- --------------------------------------------------------

--
-- Table structure for table `report_details`
--

CREATE TABLE `report_details` (
  `Report_Detail_ID` int(11) NOT NULL,
  `Report_ID` int(11) NOT NULL,
  `Test_ID` int(11) NOT NULL,
  `Result_Value` varchar(100) NOT NULL,
  `Units` varchar(50) DEFAULT NULL,
  `Interpretation` enum('Normal','Abnormal','Critical') NOT NULL DEFAULT 'Normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sample_collection`
--

CREATE TABLE `sample_collection` (
  `Sample_ID` int(11) NOT NULL,
  `Booking_ID` int(11) NOT NULL,
  `Staff_ID` int(11) NOT NULL,
  `Collection_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `Test_ID` int(11) NOT NULL,
  `Test_Name` varchar(150) NOT NULL,
  `Test_Category` varchar(100) NOT NULL,
  `Sample_Type` varchar(100) NOT NULL,
  `Test_Price` decimal(10,2) NOT NULL,
  `Normal_Range` varchar(100) DEFAULT NULL
) ;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`Test_ID`, `Test_Name`, `Test_Category`, `Sample_Type`, `Test_Price`, `Normal_Range`) VALUES
(1, 'Pet Scan', 'Radiology', 'Blood', 15000.00, NULL),
(2, 'CBC', 'Pathology', 'Urine', 500.00, NULL),
(3, 'Blood Sugar Counting', 'Biochemistry', 'Blood', 150.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `test_report`
--

CREATE TABLE `test_report` (
  `Report_ID` int(11) NOT NULL,
  `Booking_ID` int(11) NOT NULL,
  `Generated_Date` date NOT NULL DEFAULT curdate(),
  `Report_Status` enum('Pending','Ready','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `Approved_By` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_booking_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_booking_summary` (
`Booking_ID` int(11)
,`Booking_Date` date
,`Booking_Status` enum('Pending','Confirmed','Completed','Cancelled')
,`Total_Amount` decimal(10,2)
,`Payment_Status` enum('Unpaid','Paid','Partial')
,`Patient_Name` varchar(101)
,`Doctor_Name` varchar(101)
,`Specialization` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_patients`
-- (See below for the actual view)
--
CREATE TABLE `v_patients` (
`Person_ID` int(11)
,`First_Name` varchar(50)
,`Last_Name` varchar(50)
,`Email_ID` varchar(100)
,`Phone_No` varchar(15)
,`DOB` date
,`Age` int(11)
,`Gender` enum('Male','Female','Other')
,`Patient_ID` int(11)
,`Blood_Group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-')
,`Address` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_report_details`
-- (See below for the actual view)
--
CREATE TABLE `v_report_details` (
`Report_ID` int(11)
,`Booking_ID` int(11)
,`Generated_Date` date
,`Report_Status` enum('Pending','Ready','Approved','Rejected')
,`Test_Name` varchar(150)
,`Test_Category` varchar(100)
,`Normal_Range` varchar(100)
,`Result_Value` varchar(100)
,`Units` varchar(50)
,`Interpretation` enum('Normal','Abnormal','Critical')
);

-- --------------------------------------------------------

--
-- Structure for view `v_booking_summary`
--
DROP TABLE IF EXISTS `v_booking_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_booking_summary`  AS SELECT `b`.`Booking_ID` AS `Booking_ID`, `b`.`Booking_Date` AS `Booking_Date`, `b`.`Booking_Status` AS `Booking_Status`, `b`.`Total_Amount` AS `Total_Amount`, `b`.`Payment_Status` AS `Payment_Status`, concat(`pp`.`First_Name`,' ',`pp`.`Last_Name`) AS `Patient_Name`, concat(`dp`.`First_Name`,' ',`dp`.`Last_Name`) AS `Doctor_Name`, `d`.`Specialization` AS `Specialization` FROM ((((`booking` `b` join `patient` `pt` on(`b`.`Patient_ID` = `pt`.`Patient_ID`)) join `person` `pp` on(`pt`.`Person_ID` = `pp`.`Person_ID`)) join `doctor` `d` on(`b`.`Doctor_ID` = `d`.`Doctor_ID`)) join `person` `dp` on(`d`.`Person_ID` = `dp`.`Person_ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_patients`
--
DROP TABLE IF EXISTS `v_patients`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_patients`  AS SELECT `p`.`Person_ID` AS `Person_ID`, `p`.`First_Name` AS `First_Name`, `p`.`Last_Name` AS `Last_Name`, `p`.`Email_ID` AS `Email_ID`, `p`.`Phone_No` AS `Phone_No`, `p`.`DOB` AS `DOB`, `p`.`Age` AS `Age`, `p`.`Gender` AS `Gender`, `pt`.`Patient_ID` AS `Patient_ID`, `pt`.`Blood_Group` AS `Blood_Group`, `pt`.`Address` AS `Address` FROM (`person` `p` join `patient` `pt` on(`p`.`Person_ID` = `pt`.`Person_ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_report_details`
--
DROP TABLE IF EXISTS `v_report_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_report_details`  AS SELECT `tr`.`Report_ID` AS `Report_ID`, `tr`.`Booking_ID` AS `Booking_ID`, `tr`.`Generated_Date` AS `Generated_Date`, `tr`.`Report_Status` AS `Report_Status`, `t`.`Test_Name` AS `Test_Name`, `t`.`Test_Category` AS `Test_Category`, `t`.`Normal_Range` AS `Normal_Range`, `rd`.`Result_Value` AS `Result_Value`, `rd`.`Units` AS `Units`, `rd`.`Interpretation` AS `Interpretation` FROM ((`test_report` `tr` join `report_details` `rd` on(`tr`.`Report_ID` = `rd`.`Report_ID`)) join `test` `t` on(`rd`.`Test_ID` = `t`.`Test_ID`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`Booking_ID`),
  ADD KEY `idx_booking_patient` (`Patient_ID`),
  ADD KEY `idx_booking_doctor` (`Doctor_ID`),
  ADD KEY `idx_booking_date` (`Booking_Date`);

--
-- Indexes for table `booking_test`
--
ALTER TABLE `booking_test`
  ADD PRIMARY KEY (`Booking_Test_ID`),
  ADD UNIQUE KEY `uq_booking_test` (`Booking_ID`,`Test_ID`),
  ADD KEY `fk_bt_test` (`Test_ID`),
  ADD KEY `idx_bt_booking` (`Booking_ID`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`Doctor_ID`),
  ADD UNIQUE KEY `Person_ID` (`Person_ID`);

--
-- Indexes for table `lab_technician`
--
ALTER TABLE `lab_technician`
  ADD PRIMARY KEY (`Staff_ID`),
  ADD UNIQUE KEY `Person_ID` (`Person_ID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`Patient_ID`),
  ADD UNIQUE KEY `Person_ID` (`Person_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_ID`),
  ADD KEY `idx_payment_booking` (`Booking_ID`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`Person_ID`),
  ADD UNIQUE KEY `Phone_No` (`Phone_No`),
  ADD UNIQUE KEY `Email_ID` (`Email_ID`);

--
-- Indexes for table `report_details`
--
ALTER TABLE `report_details`
  ADD PRIMARY KEY (`Report_Detail_ID`),
  ADD KEY `fk_rd_test` (`Test_ID`),
  ADD KEY `idx_rd_report` (`Report_ID`);

--
-- Indexes for table `sample_collection`
--
ALTER TABLE `sample_collection`
  ADD PRIMARY KEY (`Sample_ID`),
  ADD KEY `fk_sc_staff` (`Staff_ID`),
  ADD KEY `idx_sc_booking` (`Booking_ID`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`Test_ID`);

--
-- Indexes for table `test_report`
--
ALTER TABLE `test_report`
  ADD PRIMARY KEY (`Report_ID`),
  ADD KEY `fk_tr_doctor` (`Approved_By`),
  ADD KEY `idx_report_booking` (`Booking_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `Booking_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking_test`
--
ALTER TABLE `booking_test`
  MODIFY `Booking_Test_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `Doctor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lab_technician`
--
ALTER TABLE `lab_technician`
  MODIFY `Staff_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `Patient_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `Person_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `report_details`
--
ALTER TABLE `report_details`
  MODIFY `Report_Detail_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sample_collection`
--
ALTER TABLE `sample_collection`
  MODIFY `Sample_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `Test_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_report`
--
ALTER TABLE `test_report`
  MODIFY `Report_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_doctor` FOREIGN KEY (`Doctor_ID`) REFERENCES `doctor` (`Doctor_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_patient` FOREIGN KEY (`Patient_ID`) REFERENCES `patient` (`Patient_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `booking_test`
--
ALTER TABLE `booking_test`
  ADD CONSTRAINT `fk_bt_booking` FOREIGN KEY (`Booking_ID`) REFERENCES `booking` (`Booking_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bt_test` FOREIGN KEY (`Test_ID`) REFERENCES `test` (`Test_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `fk_doctor_person` FOREIGN KEY (`Person_ID`) REFERENCES `person` (`Person_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lab_technician`
--
ALTER TABLE `lab_technician`
  ADD CONSTRAINT `fk_tech_person` FOREIGN KEY (`Person_ID`) REFERENCES `person` (`Person_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `fk_patient_person` FOREIGN KEY (`Person_ID`) REFERENCES `person` (`Person_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_booking` FOREIGN KEY (`Booking_ID`) REFERENCES `booking` (`Booking_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `report_details`
--
ALTER TABLE `report_details`
  ADD CONSTRAINT `fk_rd_report` FOREIGN KEY (`Report_ID`) REFERENCES `test_report` (`Report_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rd_test` FOREIGN KEY (`Test_ID`) REFERENCES `test` (`Test_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `sample_collection`
--
ALTER TABLE `sample_collection`
  ADD CONSTRAINT `fk_sc_booking` FOREIGN KEY (`Booking_ID`) REFERENCES `booking` (`Booking_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sc_staff` FOREIGN KEY (`Staff_ID`) REFERENCES `lab_technician` (`Staff_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `test_report`
--
ALTER TABLE `test_report`
  ADD CONSTRAINT `fk_tr_booking` FOREIGN KEY (`Booking_ID`) REFERENCES `booking` (`Booking_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tr_doctor` FOREIGN KEY (`Approved_By`) REFERENCES `doctor` (`Doctor_ID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
