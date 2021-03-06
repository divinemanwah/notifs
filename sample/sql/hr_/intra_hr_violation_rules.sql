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
-- Table structure for table `hr_violation_rules`
--

DROP TABLE IF EXISTS `hr_violation_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_violation_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `violation_id` int(11) NOT NULL,
  `repetition` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `offense_id` int(11) NOT NULL,
  `minus` float DEFAULT '0',
  `subsequent_minus` float DEFAULT '0',
  `status` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_violation_rules`
--

LOCK TABLES `hr_violation_rules` WRITE;
/*!40000 ALTER TABLE `hr_violation_rules` DISABLE KEYS */;
INSERT INTO `hr_violation_rules` VALUES (1,9,1,1,0,0,0,2,'2014-09-14 11:16:19','2014-09-14 12:54:13',394,394),(2,9,2,2,5,1,1.5,2,'2014-09-14 11:16:48','2014-09-14 12:48:57',394,394),(3,9,1,2,0,0,0,2,'2014-09-14 11:17:07','2014-09-14 12:53:47',394,394),(4,8,1,1,5,0,0,1,'2014-09-14 12:34:02','2014-09-28 15:09:25',394,394),(5,9,1,1,6,0,0,2,'2014-09-14 12:53:36','2014-09-14 12:53:53',394,394),(6,9,1,1,6,0,0,2,'2014-09-14 12:57:04','2014-09-14 12:57:15',394,394),(7,9,2,1,3,0,0,1,'2014-09-14 13:11:40','2014-09-14 18:17:56',394,394),(8,9,3,1,6,0,0,2,'2014-09-14 13:11:53','2014-09-14 15:35:57',394,394),(9,9,1,1,6,0,0,2,'2014-09-14 16:40:32','2014-09-14 18:17:44',394,394),(10,10,1,1,6,0,0,1,'2014-09-28 11:53:59',NULL,394,NULL),(11,7,1,1,4,0,0,1,'2014-09-28 15:09:35',NULL,394,NULL),(12,6,1,1,2,0,0,1,'2014-09-28 15:23:48',NULL,394,NULL),(13,17,1,1,1,1,0.5,1,'2014-09-28 15:32:58','2014-10-12 15:35:00',394,394),(14,17,4,2,6,0,0,1,'2014-09-30 11:48:12',NULL,394,NULL);
/*!40000 ALTER TABLE `hr_violation_rules` ENABLE KEYS */;
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
