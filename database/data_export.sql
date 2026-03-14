-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: hostel_management
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,9,'Anitha Thomas','anithaadmin@gmail.com','9876543210','2026-03-08',NULL);
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target` enum('general','warden','student','hostel') DEFAULT 'general',
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` enum('admin','warden') DEFAULT 'admin',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'Hostel Applications Open – Academic Year 2025–2026','Hostel applications for the academic year 2025–2026 are now open.\r\n\r\nStudents may submit their hostel applications through the official hostel portal from 12 January to 14 January.\r\n\r\nApplicants are advised to carefully read the Hostel Application Guidelines before submitting their application.\r\n\r\nThe guidelines document contains important information regarding eligibility, required documents, and the hostel allotment process.\r\n\r\nStudents are responsible for ensuring that the information provided in the application is accurate and complete.\r\n\r\nApplications are open from 12-01-25 to 14-01-25','general','uploads/announcements/69ac36394d731.pdf','2026-03-07 14:29:13','admin'),(2,'Hostel Application Deadline Extended','The deadline for submitting hostel applications for the academic year 2025–2026 has been extended.\r\n\r\nStudents who have not yet submitted their application may now complete the process before the revised deadline.\r\n\r\nRevised deadline: 14-01-25\r\n\r\nApplicants are advised to submit their applications before the closing date to avoid last-minute issues.','general',NULL,'2026-03-07 14:30:02','admin'),(3,'Hostel Application Edit Window Open','The edit window for hostel applications will be open on 28 January.\r\n\r\nStudents who have already submitted their application may review and make necessary corrections during this period.\r\n\r\nEdit Window:\r\n28-01-25\r\n\r\nAfter the edit window closes, no further changes to the application will be permitted.\r\nStudents are advised to carefully verify all information before final submission.','general',NULL,'2026-03-07 14:30:54','admin'),(16,'Hostel Rank List Published','The hostel rank list has been published. Click to download.','general','ranklist_20260310_070809.pdf','2026-03-10 06:08:09','admin');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application_settings`
--

DROP TABLE IF EXISTS `application_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `application_settings` (
  `id` int NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `edit_start` datetime DEFAULT NULL,
  `edit_end` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_settings`
--

LOCK TABLES `application_settings` WRITE;
/*!40000 ALTER TABLE `application_settings` DISABLE KEYS */;
INSERT INTO `application_settings` VALUES (1,'2026-01-12','2026-01-15',NULL,NULL);
/*!40000 ALTER TABLE `application_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaints` (
  `complaint_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text,
  `status` varchar(20) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  PRIMARY KEY (`complaint_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'Warden','Mr. Rajesh Kumar','98765 43210','2026-02-20 06:51:21'),(2,'Matron','Mrs. Anitha Thomas','91234 56789','2026-02-20 06:51:21'),(3,'Hostel Secretary','Mr./Ms. Secretary','90000 12345','2026-02-20 06:51:21');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_requests`
--

DROP TABLE IF EXISTS `guest_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guest_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `guest_student_id` varchar(20) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `hostel_id` int NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `request_message` text,
  `email_updates` tinyint(1) DEFAULT '0',
  `inmate_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `warden_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `overall_status` enum('pending','inmate_review','warden_review','admin_review','approved','rejected') DEFAULT 'pending',
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_requests`
--

LOCK TABLES `guest_requests` WRITE;
/*!40000 ALTER TABLE `guest_requests` DISABLE KEYS */;
INSERT INTO `guest_requests` VALUES (9,'ST909','Bheeshma','anaghaminnu681@gmail.com','9876543288',2,'Y101','',1,'pending','pending','pending','pending','2026-03-10 14:12:39'),(10,'ST909','Bheeshma','anaghaminnu681@gmail.com','9876543288',2,'Y101','',1,'pending','pending','pending','pending','2026-03-10 14:29:54');
/*!40000 ALTER TABLE `guest_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_roommate_approvals`
--

DROP TABLE IF EXISTS `guest_roommate_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guest_roommate_approvals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  CONSTRAINT `guest_roommate_approvals_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `guest_requests` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_roommate_approvals`
--

LOCK TABLES `guest_roommate_approvals` WRITE;
/*!40000 ALTER TABLE `guest_roommate_approvals` DISABLE KEYS */;
INSERT INTO `guest_roommate_approvals` VALUES (1,10,'ST002','approved','2026-03-10 14:58:28'),(2,10,'230090','pending',NULL),(3,10,'230065','pending',NULL),(4,10,'230020','pending',NULL);
/*!40000 ALTER TABLE `guest_roommate_approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hostel_applications`
--

DROP TABLE IF EXISTS `hostel_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hostel_applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `student_id` varchar(50) DEFAULT NULL,
  `personal_email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `year_semester` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `distance_km` int DEFAULT NULL,
  `annual_income` int DEFAULT NULL,
  `pwd_status` enum('Yes','No') DEFAULT NULL,
  `income_certificate` varchar(255) DEFAULT NULL,
  `pwd_certificate` varchar(255) DEFAULT NULL,
  `id_proof` varchar(255) DEFAULT NULL,
  `disability_percentage` tinyint DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `priority_score` int DEFAULT '0',
  `status` varchar(20) DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hostel_applications`
--

LOCK TABLES `hostel_applications` WRITE;
/*!40000 ALTER TABLE `hostel_applications` DISABLE KEYS */;
INSERT INTO `hostel_applications` VALUES (1,'Anu','IT','2026-03-02 16:08:56','2026-03-08 08:26:02','ST001','anu@gmail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9000000001','Female','S3','2004-05-10','682001',120,80000,'No',NULL,NULL,NULL,NULL,'2026-03-02 21:38:56',50,'approved'),(2,'Rahul','CSE','2026-03-02 16:08:56','2026-03-07 12:28:16','ST002','rahul@gmail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9000000002','Male','S5','2003-03-15','682002',350,150000,'Yes',NULL,NULL,NULL,50,'2026-03-02 21:38:56',75,'approved'),(3,'Meera','ECE','2026-03-02 16:08:56','2026-03-08 08:26:02','ST003','meera@gmail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9000000003','Female','S1','2005-01-20','682003',200,60000,'No',NULL,NULL,NULL,NULL,'2026-03-02 21:38:56',60,'approved'),(4,'Arjun','IT','2026-03-02 16:08:56','2026-03-08 11:18:17','ST004','arjun@gmail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9000000004','Male','S7','2002-08-25','682004',500,300000,'Yes',NULL,NULL,NULL,70,'2026-03-02 21:38:56',80,'approved'),(5,'Nithya','CSE','2026-03-02 16:08:56','2026-03-08 08:26:02','ST005','nithya@gmail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9000000005','Female','S3','2004-11-11','682005',80,90000,'No',NULL,NULL,NULL,NULL,'2026-03-02 21:38:56',50,'approved'),(6,'Student 1','ECE','2026-03-08 08:11:35','2026-03-08 10:28:00','230001','student1@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000001','Male','2','2003-11-30','682022',138,448252,'No',NULL,NULL,NULL,60,'2026-03-08 13:41:35',20,'rejected'),(7,'Student 2','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230002','student2@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000002','Female','4','2003-02-27','682042',656,211019,'Yes',NULL,NULL,NULL,60,'2026-03-08 13:41:35',80,'approved'),(8,'Student 3','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230003','student3@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000003','Female','2','2004-01-21','682045',118,118441,'No',NULL,NULL,NULL,12,'2026-03-08 13:41:35',40,'approved'),(9,'Student 4','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230004','student4@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000004','Female','3','2004-10-29','682029',361,253739,'Yes',NULL,NULL,NULL,0,'2026-03-08 13:41:35',50,'approved'),(10,'Student 5','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230005','student5@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000005','Female','1','2003-05-12','682061',501,298458,'Yes',NULL,NULL,NULL,16,'2026-03-08 13:41:35',70,'approved'),(11,'Student 6','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230006','student6@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000006','Male','3','2003-08-13','682014',95,412738,'Yes',NULL,NULL,NULL,62,'2026-03-08 13:41:35',40,'approved'),(12,'Student 7','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230007','student7@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000007','Female','2','2004-06-29','682061',351,232708,'No',NULL,NULL,NULL,6,'2026-03-08 13:41:35',50,'approved'),(13,'Student 8','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230008','student8@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000008','Female','2','2003-01-04','682060',60,155647,'Yes',NULL,NULL,NULL,39,'2026-03-08 13:41:35',50,'approved'),(14,'Student 9','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230009','student9@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000009','Male','4','2004-12-18','682061',656,375231,'Yes',NULL,NULL,NULL,62,'2026-03-08 13:41:35',80,'approved'),(15,'Student 10','ME','2026-03-08 08:11:35','2026-03-08 11:59:31','230010','student10@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000010','Female','1','2005-08-02','682022',234,348407,'No',NULL,NULL,NULL,11,'2026-03-08 13:41:35',40,'rejected'),(16,'Student 11','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230011','student11@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000011','Male','2','2003-07-28','682028',575,123056,'Yes',NULL,NULL,NULL,62,'2026-03-08 13:41:35',90,'approved'),(17,'Student 12','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230012','student12@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000012','Male','1','2004-01-15','682025',142,426870,'Yes',NULL,NULL,NULL,41,'2026-03-08 13:41:35',35,'approved'),(18,'Student 13','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230013','student13@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000013','Female','4','2004-03-10','682081',550,212408,'No',NULL,NULL,NULL,26,'2026-03-08 13:41:35',60,'approved'),(19,'Student 14','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230014','student14@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000014','Female','4','2004-10-10','682050',432,218714,'Yes',NULL,NULL,NULL,31,'2026-03-08 13:41:35',60,'approved'),(20,'Student 15','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230015','student15@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000015','Female','2','2003-12-08','682064',165,437053,'Yes',NULL,NULL,NULL,43,'2026-03-08 13:41:35',45,'approved'),(21,'Student 16','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230016','student16@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000016','Male','1','2005-07-28','682049',473,360639,'No',NULL,NULL,NULL,21,'2026-03-08 13:41:35',50,'approved'),(22,'Student 17','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230017','student17@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000017','Female','4','2005-06-05','682081',333,340049,'Yes',NULL,NULL,NULL,28,'2026-03-08 13:41:35',60,'approved'),(23,'Student 18','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230018','student18@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000018','Male','3','2003-07-29','682045',466,386377,'Yes',NULL,NULL,NULL,62,'2026-03-08 13:41:35',70,'approved'),(24,'Student 19','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230019','student19@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000019','Male','4','2005-05-26','682040',303,345975,'No',NULL,NULL,NULL,29,'2026-03-08 13:41:35',50,'approved'),(25,'Student 20','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230020','student20@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000020','Male','1','2005-07-30','682010',497,103272,'No',NULL,NULL,NULL,42,'2026-03-08 13:41:35',60,'approved'),(26,'Student 21','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230021','student21@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000021','Male','2','2003-06-25','682004',504,194871,'No',NULL,NULL,NULL,33,'2026-03-08 13:41:35',70,'approved'),(27,'Student 22','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230022','student22@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000022','Female','1','2003-08-14','682081',322,306662,'No',NULL,NULL,NULL,57,'2026-03-08 13:41:35',50,'approved'),(28,'Student 23','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230023','student23@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000023','Female','4','2005-02-06','682083',632,434579,'Yes',NULL,NULL,NULL,49,'2026-03-08 13:41:35',65,'approved'),(29,'Student 24','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230024','student24@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000024','Female','3','2004-01-12','682016',491,415391,'No',NULL,NULL,NULL,63,'2026-03-08 13:41:35',40,'approved'),(30,'Student 25','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230025','student25@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000025','Female','4','2003-07-15','682062',388,344677,'Yes',NULL,NULL,NULL,28,'2026-03-08 13:41:35',60,'approved'),(31,'Student 26','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230026','student26@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000026','Male','1','2003-01-18','682041',70,413804,'Yes',NULL,NULL,NULL,36,'2026-03-08 13:41:35',30,'approved'),(32,'Student 27','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230027','student27@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000027','Female','1','2005-01-04','682096',454,137544,'Yes',NULL,NULL,NULL,33,'2026-03-08 13:41:35',70,'approved'),(33,'Student 28','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230028','student28@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000028','Male','4','2005-02-03','682096',387,334321,'No',NULL,NULL,NULL,58,'2026-03-08 13:41:35',50,'approved'),(34,'Student 29','ECE','2026-03-08 08:11:35','2026-03-08 10:28:00','230029','student29@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000029','Female','2','2004-06-23','682092',689,113884,'No',NULL,NULL,NULL,52,'2026-03-08 13:41:35',70,'approved'),(35,'Student 30','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230030','student30@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000030','Female','2','2005-08-07','682029',445,117982,'Yes',NULL,NULL,NULL,42,'2026-03-08 13:41:35',75,'approved'),(36,'Student 31','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230031','student31@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000031','Female','4','2003-08-29','682078',181,311915,'No',NULL,NULL,NULL,26,'2026-03-08 13:41:35',40,'approved'),(37,'Student 32','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230032','student32@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000032','Male','4','2003-10-29','682098',61,104445,'No',NULL,NULL,NULL,50,'2026-03-08 13:41:35',40,'approved'),(38,'Student 33','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230033','student33@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000033','Female','4','2005-09-02','682060',101,286208,'No',NULL,NULL,NULL,56,'2026-03-08 13:41:35',30,'approved'),(39,'Student 34','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230034','student34@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000034','Male','3','2005-01-31','682005',679,326010,'No',NULL,NULL,NULL,46,'2026-03-08 13:41:35',60,'approved'),(40,'Student 35','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230035','student35@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000035','Male','2','2004-05-05','682004',536,294271,'No',NULL,NULL,NULL,14,'2026-03-08 13:41:35',60,'approved'),(41,'Student 36','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230036','student36@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000036','Male','3','2004-02-17','682033',331,114514,'No',NULL,NULL,NULL,4,'2026-03-08 13:41:35',60,'approved'),(42,'Student 37','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230037','student37@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000037','Female','4','2005-03-06','682013',233,54919,'Yes',NULL,NULL,NULL,1,'2026-03-08 13:41:35',70,'approved'),(43,'Student 38','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230038','student38@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000038','Male','3','2005-03-19','682019',404,108636,'Yes',NULL,NULL,NULL,2,'2026-03-08 13:41:35',70,'approved'),(44,'Student 39','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230039','student39@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000039','Male','1','2004-04-24','682006',622,135135,'Yes',NULL,NULL,NULL,33,'2026-03-08 13:41:35',80,'approved'),(45,'Student 40','CSE','2026-03-08 08:11:35','2026-03-08 10:25:57','230040','student40@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000040','Male','4','2004-12-02','682059',622,294295,'Yes',NULL,NULL,NULL,16,'2026-03-08 13:41:35',70,'approved'),(46,'Student 41','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230041','student41@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000041','Female','3','2005-05-21','682023',399,449375,'Yes',NULL,NULL,NULL,62,'2026-03-08 13:41:35',60,'approved'),(47,'Student 42','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230042','student42@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000042','Female','4','2004-08-02','682018',174,209722,'Yes',NULL,NULL,NULL,63,'2026-03-08 13:41:35',60,'approved'),(48,'Student 43','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230043','student43@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000043','Female','4','2003-06-24','682020',371,394848,'No',NULL,NULL,NULL,38,'2026-03-08 13:41:35',50,'approved'),(49,'Student 44','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230044','student44@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000044','Female','3','2004-12-24','682098',531,353200,'No',NULL,NULL,NULL,39,'2026-03-08 13:41:35',60,'approved'),(50,'Student 45','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230045','student45@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000045','Female','1','2005-07-06','682022',299,144931,'Yes',NULL,NULL,NULL,32,'2026-03-08 13:41:35',60,'approved'),(51,'Student 46','ECE','2026-03-08 08:11:35','2026-03-08 10:28:00','230046','student46@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000046','Female','3','2004-08-02','682074',679,298056,'Yes',NULL,NULL,NULL,7,'2026-03-08 13:41:35',70,'approved'),(52,'Student 47','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230047','student47@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000047','Female','3','2004-09-15','682023',235,342460,'No',NULL,NULL,NULL,57,'2026-03-08 13:41:35',40,'approved'),(53,'Student 48','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230048','student48@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000048','Female','3','2005-05-26','682082',369,445177,'Yes',NULL,NULL,NULL,24,'2026-03-08 13:41:35',50,'approved'),(54,'Student 49','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230049','student49@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000049','Female','1','2005-06-29','682027',482,246835,'Yes',NULL,NULL,NULL,58,'2026-03-08 13:41:35',65,'approved'),(55,'Student 50','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230050','student50@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000050','Male','4','2003-03-27','682076',405,231019,'No',NULL,NULL,NULL,52,'2026-03-08 13:41:35',50,'approved'),(56,'Student 51','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230051','student51@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000051','Male','2','2004-02-04','682076',444,351558,'No',NULL,NULL,NULL,33,'2026-03-08 13:41:35',50,'approved'),(57,'Student 52','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230052','student52@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000052','Male','3','2004-09-01','682001',223,161495,'No',NULL,NULL,NULL,8,'2026-03-08 13:41:35',50,'approved'),(58,'Student 53','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230053','student53@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000053','Female','3','2003-12-22','682009',330,394588,'Yes',NULL,NULL,NULL,32,'2026-03-08 13:41:35',60,'approved'),(59,'Student 54','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230054','student54@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000054','Male','1','2004-03-20','682020',500,390938,'Yes',NULL,NULL,NULL,24,'2026-03-08 13:41:35',70,'approved'),(60,'Student 55','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230055','student55@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000055','Female','1','2005-01-30','682045',686,264439,'No',NULL,NULL,NULL,8,'2026-03-08 13:41:35',60,'approved'),(61,'Student 56','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230056','student56@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000056','Male','4','2003-12-11','682001',76,114961,'No',NULL,NULL,NULL,66,'2026-03-08 13:41:35',40,'approved'),(62,'Student 57','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230057','student57@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000057','Female','4','2003-04-18','682056',381,390822,'No',NULL,NULL,NULL,6,'2026-03-08 13:41:35',50,'approved'),(63,'Student 58','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230058','student58@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000058','Male','3','2004-04-18','682075',296,296607,'No',NULL,NULL,NULL,61,'2026-03-08 13:41:35',40,'approved'),(64,'Student 59','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230059','student59@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000059','Male','4','2004-12-08','682016',495,427765,'No',NULL,NULL,NULL,34,'2026-03-08 13:41:35',40,'approved'),(65,'Student 60','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230060','student60@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000060','Female','3','2003-07-20','682080',317,309623,'Yes',NULL,NULL,NULL,7,'2026-03-08 13:41:35',60,'approved'),(66,'Student 61','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230061','student61@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000061','Male','3','2004-05-17','682058',319,177861,'Yes',NULL,NULL,NULL,57,'2026-03-08 13:41:35',75,'approved'),(67,'Student 62','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230062','student62@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000062','Female','1','2004-10-29','682099',693,431013,'No',NULL,NULL,NULL,7,'2026-03-08 13:41:35',50,'approved'),(68,'Student 63','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230063','student63@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000063','Female','2','2005-08-13','682076',671,243808,'No',NULL,NULL,NULL,22,'2026-03-08 13:41:35',60,'approved'),(69,'Student 64','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230064','student64@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000064','Female','2','2003-03-02','682034',416,359859,'Yes',NULL,NULL,NULL,40,'2026-03-08 13:41:35',65,'approved'),(70,'Student 65','ECE','2026-03-08 08:11:35','2026-03-08 11:59:31','230065','student65@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000065','Male','4','2005-08-14','682079',112,90197,'Yes',NULL,NULL,NULL,54,'2026-03-08 13:41:35',65,'approved'),(71,'Student 66','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230066','student66@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000066','Female','1','2005-01-26','682013',311,292379,'No',NULL,NULL,NULL,21,'2026-03-08 13:41:35',50,'approved'),(72,'Student 67','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230067','student67@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000067','Male','3','2003-06-05','682082',488,406101,'Yes',NULL,NULL,NULL,32,'2026-03-08 13:41:35',50,'approved'),(73,'Student 68','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230068','student68@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000068','Female','3','2004-02-04','682077',500,101672,'No',NULL,NULL,NULL,31,'2026-03-08 13:41:35',70,'approved'),(74,'Student 69','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230069','student69@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000069','Male','2','2004-08-05','682083',339,333794,'Yes',NULL,NULL,NULL,65,'2026-03-08 13:41:35',70,'approved'),(75,'Student 70','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230070','student70@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000070','Male','4','2005-03-23','682069',59,50138,'No',NULL,NULL,NULL,55,'2026-03-08 13:41:35',50,'approved'),(76,'Student 71','CSE','2026-03-08 08:11:35','2026-03-08 10:28:00','230071','student71@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000071','Male','1','2003-05-23','682050',115,445656,'No',NULL,NULL,NULL,16,'2026-03-08 13:41:35',20,'approved'),(77,'Student 72','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230072','student72@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000072','Female','3','2005-07-12','682082',275,154731,'Yes',NULL,NULL,NULL,39,'2026-03-08 13:41:35',60,'approved'),(78,'Student 73','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230073','student73@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000073','Male','3','2003-12-12','682071',392,249394,'No',NULL,NULL,NULL,3,'2026-03-08 13:41:35',50,'approved'),(79,'Student 74','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230074','student74@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000074','Female','2','2004-09-02','682023',260,420937,'No',NULL,NULL,NULL,37,'2026-03-08 13:41:35',30,'approved'),(80,'Student 75','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230075','student75@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000075','Female','3','2003-12-11','682078',640,120929,'Yes',NULL,NULL,NULL,19,'2026-03-08 13:41:35',80,'approved'),(81,'Student 76','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230076','student76@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000076','Female','2','2004-02-01','682061',641,326125,'No',NULL,NULL,NULL,37,'2026-03-08 13:41:35',60,'approved'),(82,'Student 77','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230077','student77@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000077','Female','2','2003-03-02','682007',176,348865,'Yes',NULL,NULL,NULL,36,'2026-03-08 13:41:35',50,'approved'),(83,'Student 78','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230078','student78@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000078','Male','2','2004-05-30','682036',228,161814,'No',NULL,NULL,NULL,2,'2026-03-08 13:41:35',50,'approved'),(84,'Student 79','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230079','student79@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000079','Male','2','2004-03-04','682002',592,90178,'No',NULL,NULL,NULL,48,'2026-03-08 13:41:35',80,'approved'),(85,'Student 80','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230080','student80@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000080','Male','1','2004-09-02','682073',584,417976,'Yes',NULL,NULL,NULL,63,'2026-03-08 13:41:35',70,'approved'),(86,'Student 81','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230081','student81@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000081','Female','4','2004-12-25','682008',225,84134,'No',NULL,NULL,NULL,57,'2026-03-08 13:41:35',60,'approved'),(87,'Student 82','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230082','student82@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000082','Female','2','2004-06-04','682038',278,293127,'No',NULL,NULL,NULL,7,'2026-03-08 13:41:35',40,'approved'),(88,'Student 83','IT','2026-03-08 08:11:35','2026-03-08 10:28:00','230083','student83@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000083','Female','1','2004-03-28','682020',482,336404,'No',NULL,NULL,NULL,53,'2026-03-08 13:41:35',50,'approved'),(89,'Student 84','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230084','student84@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000084','Male','4','2005-05-29','682039',282,287658,'No',NULL,NULL,NULL,48,'2026-03-08 13:41:35',40,'approved'),(90,'Student 85','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230085','student85@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000085','Female','4','2005-08-19','682009',433,315134,'No',NULL,NULL,NULL,51,'2026-03-08 13:41:35',50,'approved'),(91,'Student 86','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230086','student86@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000086','Male','2','2003-08-31','682028',509,319299,'Yes',NULL,NULL,NULL,14,'2026-03-08 13:41:35',70,'approved'),(92,'Student 87','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230087','student87@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000087','Female','3','2004-08-25','682099',161,398961,'No',NULL,NULL,NULL,42,'2026-03-08 13:41:35',40,'approved'),(93,'Student 88','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230088','student88@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000088','Female','1','2004-04-12','682093',222,262027,'No',NULL,NULL,NULL,46,'2026-03-08 13:41:35',40,'approved'),(94,'Student 89','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230089','student89@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000089','Female','2','2005-09-23','682087',304,180523,'Yes',NULL,NULL,NULL,22,'2026-03-08 13:41:35',70,'approved'),(95,'Student 90','ECE','2026-03-08 08:11:35','2026-03-08 08:26:02','230090','student90@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000090','Male','4','2005-05-12','682083',421,195558,'Yes',NULL,NULL,NULL,29,'2026-03-08 13:41:35',70,'approved'),(96,'Student 91','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230091','student91@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000091','Female','4','2003-12-25','682044',131,174138,'Yes',NULL,NULL,NULL,65,'2026-03-08 13:41:35',60,'approved'),(97,'Student 92','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230092','student92@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000092','Female','2','2003-12-31','682042',66,393559,'Yes',NULL,NULL,NULL,35,'2026-03-08 13:41:35',40,'approved'),(98,'Student 93','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230093','student93@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000093','Female','3','2004-03-11','682040',515,196033,'No',NULL,NULL,NULL,20,'2026-03-08 13:41:35',70,'approved'),(99,'Student 94','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230094','student94@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000094','Male','2','2003-12-13','682095',537,398901,'Yes',NULL,NULL,NULL,66,'2026-03-08 13:41:35',80,'approved'),(100,'Student 95','IT','2026-03-08 08:11:35','2026-03-08 08:26:02','230095','student95@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000095','Male','4','2004-06-15','682015',152,184999,'Yes',NULL,NULL,NULL,4,'2026-03-08 13:41:35',60,'approved'),(101,'Student 96','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230096','student96@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000096','Male','3','2003-10-29','682095',606,219874,'No',NULL,NULL,NULL,35,'2026-03-08 13:41:35',60,'approved'),(102,'Student 97','CE','2026-03-08 08:11:35','2026-03-08 08:26:02','230097','student97@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000097','Female','4','2004-03-07','682047',95,423724,'Yes',NULL,NULL,NULL,34,'2026-03-08 13:41:35',30,'approved'),(103,'Student 98','CSE','2026-03-08 08:11:35','2026-03-08 08:26:02','230098','student98@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000098','Female','3','2004-06-23','682057',210,256411,'No',NULL,NULL,NULL,45,'2026-03-08 13:41:35',40,'approved'),(104,'Student 99','ME','2026-03-08 08:11:35','2026-03-08 08:26:02','230099','student99@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000099','Female','1','2005-08-13','682019',106,395276,'Yes',NULL,NULL,NULL,48,'2026-03-08 13:41:35',45,'approved'),(105,'Student 100','ECE','2026-03-08 08:11:35','2026-03-08 11:59:31','230100','student100@mail.com','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','9800000100','Male','3','2003-11-09','682065',272,345860,'No',NULL,NULL,NULL,10,'2026-03-08 13:41:35',40,'approved');
/*!40000 ALTER TABLE `hostel_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hostels`
--

DROP TABLE IF EXISTS `hostels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hostels` (
  `hostel_id` int NOT NULL AUTO_INCREMENT,
  `hostel_name` varchar(50) DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `room_sharing` int DEFAULT NULL,
  PRIMARY KEY (`hostel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hostels`
--

LOCK TABLES `hostels` WRITE;
/*!40000 ALTER TABLE `hostels` DISABLE KEYS */;
INSERT INTO `hostels` VALUES (1,'Ganga Hostel',200,4),(2,'Yamuna Hostel',200,4),(3,'Narmada Hostel',180,3),(4,'Godavari Hostel',180,3),(5,'Kaveri Hostel',160,2);
/*!40000 ALTER TABLE `hostels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mess_menu`
--

DROP TABLE IF EXISTS `mess_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mess_menu` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `day` varchar(10) DEFAULT NULL,
  `meal_type` varchar(20) DEFAULT NULL,
  `items` text,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mess_menu`
--

LOCK TABLES `mess_menu` WRITE;
/*!40000 ALTER TABLE `mess_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `mess_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `hostel_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `type` varchar(50) DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `is_read` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,17,2,'Guest Stay Request','A student requested to stay in your room. Please review.','guest_request',10,0,'2026-03-10 14:29:54'),(2,55,2,'Guest Stay Request','A student requested to stay in your room. Please review.','guest_request',10,0,'2026-03-10 14:29:54'),(3,60,2,'Guest Stay Request','A student requested to stay in your room. Please review.','guest_request',10,0,'2026-03-10 14:29:54'),(4,65,2,'Guest Stay Request','A student requested to stay in your room. Please review.','guest_request',10,0,'2026-03-10 14:29:54');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pincode_distance`
--

DROP TABLE IF EXISTS `pincode_distance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pincode_distance` (
  `pincode` varchar(10) NOT NULL,
  `distance_km` int DEFAULT NULL,
  PRIMARY KEY (`pincode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pincode_distance`
--

LOCK TABLES `pincode_distance` WRITE;
/*!40000 ALTER TABLE `pincode_distance` DISABLE KEYS */;
INSERT INTO `pincode_distance` VALUES ('560001',350),('600001',12),('600045',28),('641001',180);
/*!40000 ALTER TABLE `pincode_distance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `room_id` int NOT NULL AUTO_INCREMENT,
  `hostel_id` int DEFAULT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`room_id`),
  KEY `hostel_id` (`hostel_id`),
  CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (26,1,'G101'),(27,1,'G102'),(28,1,'G103'),(29,1,'G104'),(30,1,'G105'),(31,1,'G106'),(32,1,'G107'),(33,1,'G108'),(34,1,'G109'),(35,1,'G110'),(36,2,'Y101'),(37,2,'Y102'),(38,2,'Y103'),(39,2,'Y104'),(40,2,'Y105'),(41,2,'Y106'),(42,2,'Y107'),(43,2,'Y108'),(44,2,'Y109'),(45,2,'Y110'),(46,3,'N101'),(47,3,'N102'),(48,3,'N103'),(49,3,'N104'),(50,3,'N105'),(51,3,'N106'),(52,3,'N107'),(53,3,'N108'),(54,4,'GD101'),(55,4,'GD102'),(56,4,'GD103'),(57,4,'GD104'),(58,4,'GD105'),(59,4,'GD106'),(60,4,'GD107'),(61,4,'GD108'),(62,5,'K101'),(63,5,'K102'),(64,5,'K103'),(65,5,'K104'),(66,5,'K105');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `hostel_id` int DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  KEY `user_id` (`user_id`),
  KEY `hostel_id` (`hostel_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `fk_students_application` FOREIGN KEY (`student_id`) REFERENCES `hostel_applications` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`),
  CONSTRAINT `students_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (2,'ST004',16,'Arjun','arjun@gmail.com','9000000004',1,26,'Male'),(3,'ST002',17,'Rahul','rahul@gmail.com','9000000002',2,36,'Male'),(4,'230011',31,'Student 11','student11@mail.com','9800000011',3,46,'Male'),(5,'230002',32,'Student 2','student2@mail.com','9800000002',4,54,'Female'),(6,'230009',33,'Student 9','student9@mail.com','9800000009',5,62,'Male'),(7,'230039',34,'Student 39','student39@mail.com','9800000039',1,26,'Male'),(8,'230075',35,'Student 75','student75@mail.com','9800000075',2,37,'Female'),(9,'230079',36,'Student 79','student79@mail.com','9800000079',3,46,'Male'),(10,'230094',37,'Student 94','student94@mail.com','9800000094',4,55,'Male'),(11,'230030',38,'Student 30','student30@mail.com','9800000030',5,63,'Female'),(12,'230061',39,'Student 61','student61@mail.com','9800000061',1,26,'Male'),(13,'230005',40,'Student 5','student5@mail.com','9800000005',2,37,'Female'),(14,'230018',41,'Student 18','student18@mail.com','9800000018',3,46,'Male'),(15,'230021',42,'Student 21','student21@mail.com','9800000021',4,55,'Male'),(16,'230027',43,'Student 27','student27@mail.com','9800000027',5,63,'Female'),(17,'230029',44,'Student 29','student29@mail.com','9800000029',1,27,'Female'),(18,'230037',45,'Student 37','student37@mail.com','9800000037',2,37,'Female'),(19,'230038',46,'Student 38','student38@mail.com','9800000038',3,47,'Male'),(20,'230040',47,'Student 40','student40@mail.com','9800000040',4,55,'Male'),(21,'230046',48,'Student 46','student46@mail.com','9800000046',5,64,'Female'),(22,'230054',49,'Student 54','student54@mail.com','9800000054',1,26,'Male'),(23,'230068',50,'Student 68','student68@mail.com','9800000068',2,37,'Female'),(24,'230069',51,'Student 69','student69@mail.com','9800000069',3,47,'Male'),(25,'230080',52,'Student 80','student80@mail.com','9800000080',4,56,'Male'),(26,'230086',53,'Student 86','student86@mail.com','9800000086',5,62,'Male'),(27,'230089',54,'Student 89','student89@mail.com','9800000089',1,27,'Female'),(28,'230090',55,'Student 90','student90@mail.com','9800000090',2,36,'Male'),(29,'230093',56,'Student 93','student93@mail.com','9800000093',3,48,'Female'),(30,'230023',57,'Student 23','student23@mail.com','9800000023',4,54,'Female'),(31,'230049',58,'Student 49','student49@mail.com','9800000049',5,64,'Female'),(32,'230064',59,'Student 64','student64@mail.com','9800000064',1,27,'Female'),(33,'230065',60,'Student 65','student65@mail.com','9800000065',2,36,'Male'),(34,'ST003',61,'Meera','meera@gmail.com','9000000003',3,48,'Female'),(35,'230013',62,'Student 13','student13@mail.com','9800000013',4,54,'Female'),(36,'230014',63,'Student 14','student14@mail.com','9800000014',5,65,'Female'),(37,'230017',64,'Student 17','student17@mail.com','9800000017',1,27,'Female'),(38,'230020',65,'Student 20','student20@mail.com','9800000020',2,36,'Male'),(39,'230025',66,'Student 25','student25@mail.com','9800000025',3,48,'Female'),(40,'230034',67,'Student 34','student34@mail.com','9800000034',4,56,'Male'),(41,'230035',68,'Student 35','student35@mail.com','9800000035',5,66,'Male'),(42,'230036',69,'Student 36','student36@mail.com','9800000036',1,28,'Male'),(43,'230041',70,'Student 41','student41@mail.com','9800000041',2,38,'Female'),(44,'230042',71,'Student 42','student42@mail.com','9800000042',3,49,'Female'),(45,'230044',72,'Student 44','student44@mail.com','9800000044',4,57,'Female'),(46,'230045',73,'Student 45','student45@mail.com','9800000045',5,65,'Female'),(47,'230053',74,'Student 53','student53@mail.com','9800000053',1,29,'Female'),(48,'230055',75,'Student 55','student55@mail.com','9800000055',2,38,'Female'),(49,'230060',76,'Student 60','student60@mail.com','9800000060',3,49,'Female'),(50,'230063',77,'Student 63','student63@mail.com','9800000063',4,57,'Female'),(51,'230072',78,'Student 72','student72@mail.com','9800000072',1,29,'Female'),(52,'230076',79,'Student 76','student76@mail.com','9800000076',2,38,'Female'),(53,'230081',80,'Student 81','student81@mail.com','9800000081',3,49,'Female'),(54,'230091',81,'Student 91','student91@mail.com','9800000091',4,57,'Female'),(55,'230095',82,'Student 95','student95@mail.com','9800000095',5,66,'Male'),(56,'230096',83,'Student 96','student96@mail.com','9800000096',1,28,'Male'),(57,'ST001',84,'Anu','anu@gmail.com','9000000001',2,38,'Female'),(58,'ST005',85,'Nithya','nithya@gmail.com','9000000005',3,50,'Female'),(59,'230004',86,'Student 4','student4@mail.com','9800000004',4,58,'Female'),(60,'230007',87,'Student 7','student7@mail.com','9800000007',1,29,'Female'),(61,'230008',88,'Student 8','student8@mail.com','9800000008',2,39,'Female'),(62,'230016',89,'Student 16','student16@mail.com','9800000016',3,47,'Male'),(63,'230019',90,'Student 19','student19@mail.com','9800000019',4,56,'Male'),(64,'230022',91,'Student 22','student22@mail.com','9800000022',1,29,'Female'),(65,'230028',92,'Student 28','student28@mail.com','9800000028',2,40,'Male'),(66,'230043',93,'Student 43','student43@mail.com','9800000043',3,50,'Female'),(67,'230048',94,'Student 48','student48@mail.com','9800000048',4,58,'Female'),(68,'230050',95,'Student 50','student50@mail.com','9800000050',1,28,'Male'),(69,'230051',96,'Student 51','student51@mail.com','9800000051',2,40,'Male'),(70,'230052',97,'Student 52','student52@mail.com','9800000052',3,51,'Male'),(71,'230057',98,'Student 57','student57@mail.com','9800000057',4,58,'Female'),(72,'230062',99,'Student 62','student62@mail.com','9800000062',1,30,'Female'),(73,'230066',100,'Student 66','student66@mail.com','9800000066',2,39,'Female'),(74,'230067',101,'Student 67','student67@mail.com','9800000067',3,51,'Male'),(75,'230070',102,'Student 70','student70@mail.com','9800000070',4,59,'Male'),(76,'230073',103,'Student 73','student73@mail.com','9800000073',1,28,'Male'),(77,'230077',104,'Student 77','student77@mail.com','9800000077',2,39,'Female'),(78,'230078',105,'Student 78','student78@mail.com','9800000078',3,51,'Male'),(79,'230085',106,'Student 85','student85@mail.com','9800000085',4,60,'Female'),(80,'230015',107,'Student 15','student15@mail.com','9800000015',1,30,'Female'),(81,'230099',108,'Student 99','student99@mail.com','9800000099',2,39,'Female'),(82,'230003',109,'Student 3','student3@mail.com','9800000003',3,50,'Female'),(83,'230006',110,'Student 6','student6@mail.com','9800000006',4,59,'Male'),(84,'230010',111,'Student 10','student10@mail.com','9800000010',1,30,'Female'),(85,'230024',112,'Student 24','student24@mail.com','9800000024',2,41,'Female'),(86,'230031',113,'Student 31','student31@mail.com','9800000031',3,52,'Female'),(87,'230032',114,'Student 32','student32@mail.com','9800000032',4,59,'Male'),(88,'230047',115,'Student 47','student47@mail.com','9800000047',1,30,'Female'),(89,'230056',116,'Student 56','student56@mail.com','9800000056',2,40,'Male'),(90,'230058',117,'Student 58','student58@mail.com','9800000058',3,53,'Male'),(91,'230059',118,'Student 59','student59@mail.com','9800000059',4,61,'Male'),(92,'230082',119,'Student 82','student82@mail.com','9800000082',1,31,'Female'),(93,'230084',120,'Student 84','student84@mail.com','9800000084',2,40,'Male'),(94,'230087',121,'Student 87','student87@mail.com','9800000087',3,52,'Female'),(95,'230088',122,'Student 88','student88@mail.com','9800000088',4,60,'Female'),(96,'230092',123,'Student 92','student92@mail.com','9800000092',1,31,'Female'),(97,'230098',124,'Student 98','student98@mail.com','9800000098',2,41,'Female'),(98,'230012',125,'Student 12','student12@mail.com','9800000012',3,53,'Male'),(99,'230026',126,'Student 26','student26@mail.com','9800000026',4,61,'Male'),(100,'230033',127,'Student 33','student33@mail.com','9800000033',1,31,'Female'),(101,'230074',128,'Student 74','student74@mail.com','9800000074',2,41,'Female'),(102,'230097',129,'Student 97','student97@mail.com','9800000097',3,52,'Female'),(103,'230001',130,'Student 1','student1@mail.com','9800000001',4,61,'Male'),(104,'230071',131,'Student 71','student71@mail.com','9800000071',1,32,'Male'),(105,'230083',132,'Student 83','student83@mail.com','9800000083',2,41,'Female'),(106,'230100',133,'Student 100','student100@mail.com','9800000100',3,53,'Male');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (9,'ADMIN01','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','admin','1773034815_WhatsApp Image 2026-03-08 at 1.30.43 AM.jpeg'),(10,'WARDEN01','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden','69af0934bacde.jpeg'),(11,'IT001','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(12,'IT002','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(13,'IT003','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(14,'IT004','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(15,'IT005','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(16,'ST004','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(17,'ST002','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(18,'WARDEN02','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(19,'WARDEN03','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(20,'WARDEN04','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(21,'WARDEN05','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(22,'WARDEN06','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(27,'WARDEN07','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(28,'WARDEN08','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(29,'WARDEN09','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(30,'WARDEN10','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','warden',NULL),(31,'230011','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(32,'230002','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(33,'230009','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(34,'230039','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(35,'230075','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(36,'230079','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(37,'230094','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(38,'230030','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(39,'230061','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(40,'230005','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(41,'230018','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(42,'230021','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(43,'230027','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(44,'230029','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(45,'230037','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(46,'230038','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(47,'230040','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(48,'230046','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(49,'230054','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(50,'230068','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(51,'230069','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(52,'230080','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(53,'230086','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(54,'230089','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(55,'230090','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(56,'230093','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(57,'230023','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(58,'230049','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(59,'230064','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(60,'230065','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(61,'ST003','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(62,'230013','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(63,'230014','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(64,'230017','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(65,'230020','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(66,'230025','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(67,'230034','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(68,'230035','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(69,'230036','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(70,'230041','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(71,'230042','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(72,'230044','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(73,'230045','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(74,'230053','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(75,'230055','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(76,'230060','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(77,'230063','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(78,'230072','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(79,'230076','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(80,'230081','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(81,'230091','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(82,'230095','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(83,'230096','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(84,'ST001','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(85,'ST005','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(86,'230004','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(87,'230007','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(88,'230008','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(89,'230016','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(90,'230019','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(91,'230022','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(92,'230028','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(93,'230043','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(94,'230048','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(95,'230050','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(96,'230051','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(97,'230052','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(98,'230057','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(99,'230062','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(100,'230066','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(101,'230067','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(102,'230070','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(103,'230073','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(104,'230077','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(105,'230078','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(106,'230085','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(107,'230015','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(108,'230099','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(109,'230003','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(110,'230006','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(111,'230010','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(112,'230024','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(113,'230031','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(114,'230032','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(115,'230047','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(116,'230056','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(117,'230058','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(118,'230059','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(119,'230082','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(120,'230084','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(121,'230087','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(122,'230088','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(123,'230092','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(124,'230098','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(125,'230012','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(126,'230026','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(127,'230033','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(128,'230074','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(129,'230097','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(130,'230001','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(131,'230071','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(132,'230083','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL),(133,'230100','$2y$10$h64H9eGQjUu8d4c/jwND0Off2aplR6/NXJsGJk.KaRHfhBvAv2M5q','student',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wardens`
--

DROP TABLE IF EXISTS `wardens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wardens` (
  `warden_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `hostel_id` int NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`warden_id`),
  KEY `user_id` (`user_id`),
  KEY `hostel_id` (`hostel_id`),
  CONSTRAINT `wardens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `wardens_ibfk_2` FOREIGN KEY (`hostel_id`) REFERENCES `hostels` (`hostel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wardens`
--

LOCK TABLES `wardens` WRITE;
/*!40000 ALTER TABLE `wardens` DISABLE KEYS */;
INSERT INTO `wardens` VALUES (1,10,'Rajesh Kumar',1,'Male','9876543210','warden1@aruvi.com','2026-03-08',NULL),(2,18,'Anila Kumari',1,'Female','9000000001','warden2@aruvi.com','2026-03-08',NULL),(3,19,'Meera Nair',2,'Male','9000000002','warden3@aruvi.com','2026-03-08',NULL),(4,20,'Rahul Menon',2,'Female','9000000003','warden4@aruvi.com','2026-03-08',NULL),(5,21,'Priya Das',3,'Male','9000000004','warden5@aruvi.com','2026-03-08',NULL),(6,22,'Arjun Pillai',3,'Female','9000000005','warden6@aruvi.com','2026-03-08',NULL),(7,27,'Sneha Joseph',4,'Male','9000000006','warden7@aruvi.com','2026-03-08',NULL),(8,28,'Kiran Nair',4,'Female','9000000007','warden8@aruvi.com','2026-03-08',NULL),(9,29,'Deepa Varma',5,'Male','9000000008','warden9@aruvi.com','2026-03-08',NULL),(10,30,'Suresh Kumar',5,'Female','9000000009','warden10@aruvi.com','2026-03-08',NULL);
/*!40000 ALTER TABLE `wardens` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-14 11:49:06
