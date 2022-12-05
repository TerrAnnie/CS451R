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
-- Table structure for table `professor_teaches`
--

DROP TABLE IF EXISTS `professor_teaches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professor_teaches` (
  `professor_ID` int NOT NULL,
  `course_id` int NOT NULL,
  `semester` varchar(45) NOT NULL COMMENT 'Fall, Spring, Summer',
  `year` int NOT NULL,
  `managing_admin` int DEFAULT NULL,
  PRIMARY KEY (`professor_ID`,`course_id`,`semester`,`year`),
  KEY `course_ID_idx` (`course_id`),
  KEY `managing_admin_idx` (`managing_admin`),
  CONSTRAINT `course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
  CONSTRAINT `managing_admin` FOREIGN KEY (`managing_admin`) REFERENCES `admin` (`admin_id`),
  CONSTRAINT `professor_ID` FOREIGN KEY (`professor_ID`) REFERENCES `professor` (`professor_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `professor_teaches`
--

LOCK TABLES `professor_teaches` WRITE;
/*!40000 ALTER TABLE `professor_teaches` DISABLE KEYS */;
INSERT INTO `professor_teaches` VALUES (4,3,'Fall',2023,1),(4,4,'Fall',2023,1),(4,4,'Spring',2023,1),(4,5,'Summer',2023,1),(5,1,'Spring',2023,1),(5,1,'Summer',2023,1),(5,6,'Fall',2023,2),(5,6,'Spring',2023,2),(5,46,'Summer',2023,2),(6,1,'Summer',2023,2),(6,10,'Summer',2023,2);
/*!40000 ALTER TABLE `professor_teaches` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-05 11:05:40
