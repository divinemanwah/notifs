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
-- Table structure for table `hr_offenses`
--

DROP TABLE IF EXISTS `hr_offenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_offenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_offenses`
--

LOCK TABLES `hr_offenses` WRITE;
/*!40000 ALTER TABLE `hr_offenses` DISABLE KEYS */;
INSERT INTO `hr_offenses` VALUES (1,'Failure to file OBT (Late filing)',3,1,'2014-09-07 10:50:54',NULL,394,NULL),(2,'Tailgating',0,1,'2014-09-07 10:51:02',NULL,394,NULL),(3,'Habitual Tardiness (2 times in a week)',0,1,'2014-09-07 10:51:09','2014-09-24 12:08:41',394,394),(4,'Failure to submit medical certificate',0,1,'2014-09-07 10:51:15',NULL,394,NULL),(5,'AWOL - no info',2,1,'2014-09-07 10:51:21','2014-10-15 17:16:52',394,394),(6,'Misappropriation of Company Funds',3,1,'2014-09-07 10:51:30','2014-10-15 17:17:13',394,394),(7,'test',0,2,'2014-09-07 16:22:44','2014-10-15 17:21:32',394,394),(8,'aaa',0,2,'2014-09-08 17:43:46','2014-09-08 17:49:36',394,394),(9,'bbb',0,2,'2014-09-08 17:43:53','2014-09-08 17:49:17',394,394),(10,'test 123',0,0,'2014-09-12 16:23:34','2014-09-14 15:48:51',394,394),(11,'aaa',0,2,'2014-09-14 15:47:53','2014-09-14 15:48:28',394,394),(12,'aaa',3,1,'2014-10-16 10:01:59','2014-10-16 10:02:15',394,394);
/*!40000 ALTER TABLE `hr_offenses` ENABLE KEYS */;
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
