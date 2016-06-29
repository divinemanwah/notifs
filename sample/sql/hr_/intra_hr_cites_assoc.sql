CREATE DATABASE  IF NOT EXISTS `intra` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `intra`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: intra
-- ------------------------------------------------------
-- Server version	5.5.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `hr_cites_assoc`
--

DROP TABLE IF EXISTS `hr_cites_assoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_cites_assoc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cite_id` int(11) NOT NULL,
  `member_violation_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_cites_assoc`
--

LOCK TABLES `hr_cites_assoc` WRITE;
/*!40000 ALTER TABLE `hr_cites_assoc` DISABLE KEYS */;
INSERT INTO `hr_cites_assoc` VALUES (1,1,1,1),(2,1,2,1),(3,2,3,1),(4,2,4,1),(5,3,5,1),(6,3,6,1),(7,4,9,1),(8,4,10,1),(9,5,11,1),(10,6,12,1),(11,7,13,1),(12,8,14,1),(13,9,15,1),(14,10,16,1),(15,11,17,1),(16,12,18,1),(17,13,19,1),(18,14,21,1),(19,15,22,1),(20,16,23,1),(21,17,25,1),(22,18,26,1),(23,19,27,1),(24,20,28,1);
/*!40000 ALTER TABLE `hr_cites_assoc` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-14 14:22:38
