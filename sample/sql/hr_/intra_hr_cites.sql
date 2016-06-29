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
-- Table structure for table `hr_cites`
--

DROP TABLE IF EXISTS `hr_cites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_cites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `cite_code` varchar(10) NOT NULL,
  `offense_id` int(11) NOT NULL,
  `commission_date` datetime DEFAULT NULL,
  `nte_date` datetime DEFAULT NULL,
  `penalty_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `remarks` text,
  `created_date` datetime NOT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_cites`
--

LOCK TABLES `hr_cites` WRITE;
/*!40000 ALTER TABLE `hr_cites` DISABLE KEYS */;
INSERT INTO `hr_cites` VALUES (1,394,'test',3,NULL,NULL,6,3,NULL,'2014-09-14 18:34:07','2014-09-28 15:34:35',394,394),(2,394,'test',3,NULL,NULL,6,2,NULL,'2014-09-14 18:34:28','2014-09-28 15:34:30',394,394),(3,386,'123',3,NULL,NULL,6,1,NULL,'2014-09-15 16:49:07','2014-09-25 10:55:04',394,394),(4,394,'1234',3,NULL,NULL,2,3,'asd','2014-09-21 17:30:14','2014-09-24 17:27:24',394,394),(5,394,'test',6,NULL,NULL,6,2,NULL,'2014-09-28 12:00:21','2014-09-28 12:09:05',394,394),(6,394,'test',6,NULL,NULL,6,1,NULL,'2014-09-28 12:00:21','2014-09-28 12:08:51',394,394),(7,394,'test',1,NULL,NULL,6,1,NULL,'2014-09-28 15:33:15','2014-09-28 15:34:23',394,394),(8,393,'test',5,NULL,NULL,6,1,NULL,'2014-09-28 15:33:27','2014-09-28 15:34:18',394,394),(9,391,'test',4,NULL,NULL,6,1,NULL,'2014-09-28 15:33:37','2014-09-28 15:34:13',394,394),(10,108,'test',2,NULL,NULL,6,1,NULL,'2014-08-28 15:33:48','2014-09-28 15:34:08',394,394),(11,394,'test',1,NULL,NULL,7,1,NULL,'2014-09-30 11:27:56','2014-10-02 14:22:21',394,394),(12,394,'test',1,NULL,NULL,7,1,NULL,'2014-10-02 14:21:44','2014-10-02 14:22:16',394,394),(13,394,'test',1,NULL,NULL,7,2,NULL,'2014-10-02 14:21:59','2014-10-05 14:19:53',394,394),(14,394,'test',1,NULL,NULL,7,1,NULL,'2014-10-05 14:24:44','2014-10-05 18:50:00',394,394),(15,394,'test',1,'2014-10-05 00:00:00',NULL,7,1,NULL,'2014-10-05 16:24:55','2014-10-12 11:59:45',394,394),(16,394,'test',1,'2014-10-01 00:00:00',NULL,7,1,NULL,'2014-10-05 18:52:13','2014-10-12 11:59:26',394,394),(17,46,'test',6,'2014-10-16 00:00:00',NULL,7,2,NULL,'2014-10-16 14:29:40','2014-10-16 14:30:25',394,394),(18,46,'test',6,NULL,'2014-10-14 12:23:00',7,1,NULL,'2014-10-16 15:32:04','2014-10-17 12:27:19',394,394),(19,394,'test',6,'2014-10-16 00:00:00','2014-10-14 12:22:00',7,1,NULL,'2014-10-16 18:41:29','2014-10-17 16:23:33',394,394),(20,394,'test',1,NULL,'2014-12-08 11:14:00',2,1,NULL,'2014-12-08 11:13:21','2014-12-08 11:14:24',394,394);
/*!40000 ALTER TABLE `hr_cites` ENABLE KEYS */;
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