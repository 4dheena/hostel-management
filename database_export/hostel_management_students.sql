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
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (2,'ST004',16,'Arjun','arjun@gmail.com','9000000004',1,26,'Male'),(3,'ST002',17,'Rahul','rahul@gmail.com','9000000002',2,36,'Male'),(4,'230011',31,'Student 11','student11@mail.com','9800000011',3,46,'Male'),(5,'230002',32,'Student 2','student2@mail.com','9800000002',4,54,'Female'),(6,'230009',33,'Student 9','student9@mail.com','9800000009',5,62,'Male'),(7,'230039',34,'Student 39','student39@mail.com','9800000039',1,26,'Male'),(8,'230075',35,'Student 75','student75@mail.com','9800000075',2,37,'Female'),(9,'230079',36,'Student 79','student79@mail.com','9800000079',3,46,'Male'),(10,'230094',37,'Student 94','student94@mail.com','9800000094',4,55,'Male'),(11,'230030',38,'Student 30','student30@mail.com','9800000030',5,63,'Female'),(12,'230061',39,'Student 61','student61@mail.com','9800000061',1,26,'Male'),(13,'230005',40,'Student 5','student5@mail.com','9800000005',2,37,'Female'),(14,'230018',41,'Student 18','student18@mail.com','9800000018',3,46,'Male'),(15,'230021',42,'Student 21','student21@mail.com','9800000021',4,55,'Male'),(16,'230027',43,'Student 27','student27@mail.com','9800000027',5,63,'Female'),(17,'230029',44,'Student 29','student29@mail.com','9800000029',1,27,'Female'),(18,'230037',45,'Student 37','student37@mail.com','9800000037',2,37,'Female'),(19,'230038',46,'Student 38','student38@mail.com','9800000038',3,47,'Male'),(20,'230040',47,'Student 40','student40@mail.com','9800000040',4,55,'Male'),(21,'230046',48,'Student 46','student46@mail.com','9800000046',5,64,'Female'),(22,'230054',49,'Student 54','student54@mail.com','9800000054',1,26,'Male'),(23,'230068',50,'Student 68','student68@mail.com','9800000068',2,37,'Female'),(24,'230069',51,'Student 69','student69@mail.com','9800000069',3,47,'Male'),(25,'230080',52,'Student 80','student80@mail.com','9800000080',4,56,'Male'),(26,'230086',53,'Student 86','student86@mail.com','9800000086',5,62,'Male'),(27,'230089',54,'Student 89','student89@mail.com','9800000089',1,27,'Female'),(28,'230090',55,'Student 90','student90@mail.com','9800000090',2,36,'Male'),(29,'230093',56,'Student 93','student93@mail.com','9800000093',3,48,'Female'),(30,'230023',57,'Student 23','student23@mail.com','9800000023',4,54,'Female'),(31,'230049',58,'Student 49','student49@mail.com','9800000049',5,64,'Female'),(32,'230064',59,'Student 64','student64@mail.com','9800000064',1,27,'Female'),(33,'230065',60,'Student 65','student65@mail.com','9800000065',2,36,'Male'),(34,'ST003',61,'Meera','meera@gmail.com','9000000003',3,48,'Female'),(35,'230013',62,'Student 13','student13@mail.com','9800000013',4,54,'Female'),(36,'230014',63,'Student 14','student14@mail.com','9800000014',5,65,'Female'),(37,'230017',64,'Student 17','student17@mail.com','9800000017',1,27,'Female'),(38,'230020',65,'Student 20','student20@mail.com','9800000020',2,36,'Male'),(39,'230025',66,'Student 25','student25@mail.com','9800000025',3,48,'Female'),(40,'230034',67,'Student 34','student34@mail.com','9800000034',4,56,'Male'),(41,'230035',68,'Student 35','student35@mail.com','9800000035',5,66,'Male'),(42,'230036',69,'Student 36','student36@mail.com','9800000036',1,28,'Male'),(43,'230041',70,'Student 41','student41@mail.com','9800000041',2,38,'Female'),(44,'230042',71,'Student 42','student42@mail.com','9800000042',3,49,'Female'),(45,'230044',72,'Student 44','student44@mail.com','9800000044',4,57,'Female'),(46,'230045',73,'Student 45','student45@mail.com','9800000045',5,65,'Female'),(47,'230053',74,'Student 53','student53@mail.com','9800000053',1,29,'Female'),(48,'230055',75,'Student 55','student55@mail.com','9800000055',2,38,'Female'),(49,'230060',76,'Student 60','student60@mail.com','9800000060',3,49,'Female'),(50,'230063',77,'Student 63','student63@mail.com','9800000063',4,57,'Female'),(51,'230072',78,'Student 72','student72@mail.com','9800000072',1,29,'Female'),(52,'230076',79,'Student 76','student76@mail.com','9800000076',2,38,'Female'),(53,'230081',80,'Student 81','student81@mail.com','9800000081',3,49,'Female'),(54,'230091',81,'Student 91','student91@mail.com','9800000091',4,57,'Female'),(55,'230095',82,'Student 95','student95@mail.com','9800000095',5,66,'Male'),(56,'230096',83,'Student 96','student96@mail.com','9800000096',1,28,'Male'),(57,'ST001',84,'Anu','anu@gmail.com','9000000001',2,38,'Female'),(58,'ST005',85,'Nithya','nithya@gmail.com','9000000005',3,50,'Female'),(59,'230004',86,'Student 4','student4@mail.com','9800000004',4,58,'Female'),(60,'230007',87,'Student 7','student7@mail.com','9800000007',1,29,'Female'),(61,'230008',88,'Student 8','student8@mail.com','9800000008',2,39,'Female'),(62,'230016',89,'Student 16','student16@mail.com','9800000016',3,47,'Male'),(63,'230019',90,'Student 19','student19@mail.com','9800000019',4,56,'Male'),(64,'230022',91,'Student 22','student22@mail.com','9800000022',1,29,'Female'),(65,'230028',92,'Student 28','student28@mail.com','9800000028',2,40,'Male'),(66,'230043',93,'Student 43','student43@mail.com','9800000043',3,50,'Female'),(67,'230048',94,'Student 48','student48@mail.com','9800000048',4,58,'Female'),(68,'230050',95,'Student 50','student50@mail.com','9800000050',1,28,'Male'),(69,'230051',96,'Student 51','student51@mail.com','9800000051',2,40,'Male'),(70,'230052',97,'Student 52','student52@mail.com','9800000052',3,51,'Male'),(71,'230057',98,'Student 57','student57@mail.com','9800000057',4,58,'Female'),(72,'230062',99,'Student 62','student62@mail.com','9800000062',1,30,'Female'),(73,'230066',100,'Student 66','student66@mail.com','9800000066',2,39,'Female'),(74,'230067',101,'Student 67','student67@mail.com','9800000067',3,51,'Male'),(75,'230070',102,'Student 70','student70@mail.com','9800000070',4,59,'Male'),(76,'230073',103,'Student 73','student73@mail.com','9800000073',1,28,'Male'),(77,'230077',104,'Student 77','student77@mail.com','9800000077',2,39,'Female'),(78,'230078',105,'Student 78','student78@mail.com','9800000078',3,51,'Male'),(79,'230085',106,'Student 85','student85@mail.com','9800000085',4,60,'Female'),(80,'230015',107,'Student 15','student15@mail.com','9800000015',1,30,'Female'),(81,'230099',108,'Student 99','student99@mail.com','9800000099',2,39,'Female'),(82,'230003',109,'Student 3','student3@mail.com','9800000003',3,50,'Female'),(83,'230006',110,'Student 6','student6@mail.com','9800000006',4,59,'Male'),(84,'230010',111,'Student 10','student10@mail.com','9800000010',1,30,'Female'),(85,'230024',112,'Student 24','student24@mail.com','9800000024',2,41,'Female'),(86,'230031',113,'Student 31','student31@mail.com','9800000031',3,52,'Female'),(87,'230032',114,'Student 32','student32@mail.com','9800000032',4,59,'Male'),(88,'230047',115,'Student 47','student47@mail.com','9800000047',1,30,'Female'),(89,'230056',116,'Student 56','student56@mail.com','9800000056',2,40,'Male'),(90,'230058',117,'Student 58','student58@mail.com','9800000058',3,53,'Male'),(91,'230059',118,'Student 59','student59@mail.com','9800000059',4,61,'Male'),(92,'230082',119,'Student 82','student82@mail.com','9800000082',1,31,'Female'),(93,'230084',120,'Student 84','student84@mail.com','9800000084',2,40,'Male'),(94,'230087',121,'Student 87','student87@mail.com','9800000087',3,52,'Female'),(95,'230088',122,'Student 88','student88@mail.com','9800000088',4,60,'Female'),(96,'230092',123,'Student 92','student92@mail.com','9800000092',1,31,'Female'),(97,'230098',124,'Student 98','student98@mail.com','9800000098',2,41,'Female'),(98,'230012',125,'Student 12','student12@mail.com','9800000012',3,53,'Male'),(99,'230026',126,'Student 26','student26@mail.com','9800000026',4,61,'Male'),(100,'230033',127,'Student 33','student33@mail.com','9800000033',1,31,'Female'),(101,'230074',128,'Student 74','student74@mail.com','9800000074',2,41,'Female'),(102,'230097',129,'Student 97','student97@mail.com','9800000097',3,52,'Female'),(103,'230001',130,'Student 1','student1@mail.com','9800000001',4,61,'Male'),(104,'230071',131,'Student 71','student71@mail.com','9800000071',1,32,'Male'),(105,'230083',132,'Student 83','student83@mail.com','9800000083',2,41,'Female'),(106,'230100',133,'Student 100','student100@mail.com','9800000100',3,53,'Male');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-14 11:27:25
