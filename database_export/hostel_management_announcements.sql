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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'Hostel Applications Open – Academic Year 2025–2026','Hostel applications for the academic year 2025–2026 are now open.\r\n\r\nStudents may submit their hostel applications through the official hostel portal from 12 January to 14 January.\r\n\r\nApplicants are advised to carefully read the Hostel Application Guidelines before submitting their application.\r\n\r\nThe guidelines document contains important information regarding eligibility, required documents, and the hostel allotment process.\r\n\r\nStudents are responsible for ensuring that the information provided in the application is accurate and complete.\r\n\r\nApplications are open from 12-01-25 to 14-01-25','general','uploads/announcements/69ac36394d731.pdf','2026-03-07 14:29:13','admin'),(2,'Hostel Application Deadline Extended','The deadline for submitting hostel applications for the academic year 2025–2026 has been extended.\r\n\r\nStudents who have not yet submitted their application may now complete the process before the revised deadline.\r\n\r\nRevised deadline: 14-01-25\r\n\r\nApplicants are advised to submit their applications before the closing date to avoid last-minute issues.','general',NULL,'2026-03-07 14:30:02','admin'),(3,'Hostel Application Edit Window Open','The edit window for hostel applications will be open on 28 January.\r\n\r\nStudents who have already submitted their application may review and make necessary corrections during this period.\r\n\r\nEdit Window:\r\n28-01-25\r\n\r\nAfter the edit window closes, no further changes to the application will be permitted.\r\nStudents are advised to carefully verify all information before final submission.','general',NULL,'2026-03-07 14:30:54','admin'),(16,'Hostel Rank List Published','The hostel rank list has been published. Click to download.','general','ranklist_20260310_070809.pdf','2026-03-10 06:08:09','admin');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-14 11:27:24
