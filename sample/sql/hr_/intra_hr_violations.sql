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
-- Table structure for table `hr_violations`
--

DROP TABLE IF EXISTS `hr_violations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_violations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_violations`
--

LOCK TABLES `hr_violations` WRITE;
/*!40000 ALTER TABLE `hr_violations` DISABLE KEYS */;
INSERT INTO `hr_violations` VALUES (1,'test 1',2,'2014-09-07 17:03:36','2014-09-07 17:04:05',394,394),(2,'test 2',0,'2014-09-07 17:08:38','2014-09-14 15:45:23',394,394),(3,'',2,'2014-09-08 10:44:08','2014-09-08 10:44:20',394,394),(4,'aaa',2,'2014-09-08 17:43:14','2014-09-14 15:45:09',394,394),(5,'bbb',2,'2014-09-08 17:43:19','2014-09-14 12:38:47',394,394),(6,'aaa',1,'2014-09-10 12:01:51','2014-09-10 12:34:51',394,394),(7,'zzz',1,'2014-09-10 12:36:58','2014-10-02 18:13:49',394,394),(8,'No timein/timeout',1,'2014-09-10 12:52:56','2014-09-29 14:51:06',394,394),(9,'Using Cellphone',1,'2014-09-10 14:17:02','2014-09-29 14:50:30',394,394),(10,'Security',1,'2014-09-18 17:20:32','2014-09-29 14:49:40',394,394),(11,'345',2,'2014-09-18 17:20:50','2014-09-18 17:24:26',394,394),(12,'678',0,'2014-09-18 17:21:40','2014-09-18 17:24:22',394,394),(13,'nnn',0,'2014-09-28 15:23:55','2014-09-28 15:24:31',394,394),(14,'iii',0,'2014-09-28 15:24:38','2014-09-28 15:29:55',394,394),(15,'eee',2,'2014-09-28 15:30:00','2014-09-28 15:32:51',394,394),(16,'uuu',0,'2014-09-28 15:30:26','2014-09-28 15:32:48',394,394),(17,'Tardiness',1,'2014-09-28 15:32:42','2014-11-15 11:00:58',394,394),(18,'AWOL',1,'2014-09-30 12:08:54','2014-10-12 14:57:50',394,394);
/*!40000 ALTER TABLE `hr_violations` ENABLE KEYS */;
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