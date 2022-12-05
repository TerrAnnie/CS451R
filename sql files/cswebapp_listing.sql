CREATE DATABASE  IF NOT EXISTS `cswebapp` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cswebapp`;
-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: cs456webapp.mysql.database.azure.com    Database: cswebapp
-- ------------------------------------------------------
-- Server version	8.0.28

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
-- Table structure for table `listing`
--

DROP TABLE IF EXISTS `listing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `listing` (
  `listing_id` int NOT NULL,
  `course_ID` int DEFAULT NULL,
  `professor_ID` int DEFAULT NULL,
  `grade_level_requirement` int DEFAULT NULL COMMENT 'Undergrad, Masters, PHD',
  `gpa_requirement` double DEFAULT NULL,
  `position_type` varchar(45) DEFAULT NULL COMMENT 'Accepted Values = "Grader, GTA, Lab instructor" Please follow this syntax',
  `completed_hours_requirement` int DEFAULT NULL,
  `semester` varchar(45) DEFAULT NULL,
  `year` int DEFAULT NULL,
  PRIMARY KEY (`listing_id`),
  KEY `course_id_idx` (`course_ID`) /*!80000 INVISIBLE */,
  KEY `professor_id_idx` (`professor_ID`),
  CONSTRAINT `course_id_1` FOREIGN KEY (`course_ID`) REFERENCES `course` (`course_id`),
  CONSTRAINT `professor_id_1` FOREIGN KEY (`professor_ID`) REFERENCES `professor` (`professor_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listing`
--

LOCK TABLES `listing` WRITE;
/*!40000 ALTER TABLE `listing` DISABLE KEYS */;
INSERT INTO `listing` VALUES (1,4,4,1,3,'Grader',200,'Fall',2023),(3,4,4,0,4,'Lab instructor',1000,'Spring',2023),(4,46,4,1,2.5,'Lab Instructor',80,'Fall',2023),(8,46,5,0,4,'GTA',100,'Summer',2023),(9,6,5,0,3.5,'Grader',150,'Fall',2023),(13,1,5,0,3.2,'Grader',20,'Spring',2023),(14,6,5,0,3.5,'GTA',200,'Spring',2023),(15,3,4,0,3.5,'Grader',100,'Fall',2023),(16,3,4,0,3.5,'GTA',200,'Spring',2023),(17,13,5,0,3,'GTA',100,'Spring',2023),(20,1,4,0,4,'Grader',200,'Spring',2023),(22,5,4,0,3.6,'Grader',40,'Summer',2023);
/*!40000 ALTER TABLE `listing` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-05 11:05:26
