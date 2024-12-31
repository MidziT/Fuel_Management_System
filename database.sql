-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: fuel_sys
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `add_fuel_logs_tb`
--

DROP TABLE IF EXISTS `add_fuel_logs_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `add_fuel_logs_tb` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `add_id` int(11) NOT NULL,
  `ec_number` varchar(50) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `fuel_type` enum('Diesel','Petrol') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `otp_verification` varchar(10) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `log_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `add_fuel_logs_tb`
--

LOCK TABLES `add_fuel_logs_tb` WRITE;
/*!40000 ALTER TABLE `add_fuel_logs_tb` DISABLE KEYS */;
INSERT INTO `add_fuel_logs_tb` VALUES (2,129,'6141558A','PU006B1234552','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(3,130,'6141558A','PU006B1234553','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(4,131,'6141558A','PU006B1234554','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(5,132,'6141558A','PU006B1234555','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(6,133,'6141558A','PU006B1234556','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(7,134,'6141558A','PU006B1234557','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(8,135,'6141558A','PU006B1234558','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(9,136,'6141558A','PU006B1234559','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(10,137,'6141558A','PU006B1234560','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(11,138,'6141558A','PU006B1234561','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:09'),(12,139,'6141558A','PU006B1234562','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(13,140,'6141558A','PU006B1234563','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(14,141,'6141558A','PU006B1234564','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(15,142,'6141558A','PU006B1234565','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(16,143,'6141558A','PU006B1234566','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(17,144,'6141558A','PU006B1234567','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(18,145,'6141558A','PU006B1234568','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(19,146,'6141558A','PU006B1234569','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(20,147,'6141558A','PU006B1234570','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(21,148,'6141558A','PU006B1234571','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(22,149,'6141558A','PU006B1234572','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(23,150,'6141558A','PU006B1234573','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(24,151,'6141558A','PU006B1234574','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(25,152,'6141558A','PU006B1234575','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(26,153,'6141558A','PU006B1234576','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(27,154,'6141558A','PU006B1234577','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(28,155,'6141558A','PU006B1234578','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(29,156,'6141558A','PU006B1234579','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(30,157,'6141558A','PU006B1234580','PETRO TRADE','Petrol',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:20:09','2024-11-08 13:20:10'),(31,158,'6141558A','DU006B1234552','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(32,159,'6141558A','DU006B1234553','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(33,160,'6141558A','DU006B1234554','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(34,161,'6141558A','DU006B1234555','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(35,162,'6141558A','DU006B1234556','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(36,163,'6141558A','DU006B1234557','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(37,164,'6141558A','DU006B1234558','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(38,165,'6141558A','DU006B1234559','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(39,166,'6141558A','DU006B1234560','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(40,167,'6141558A','DU006B1234561','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(41,168,'6141558A','DU006B1234562','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(42,169,'6141558A','DU006B1234563','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(43,170,'6141558A','DU006B1234564','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(44,171,'6141558A','DU006B1234565','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(45,172,'6141558A','DU006B1234566','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(46,173,'6141558A','DU006B1234567','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(47,174,'6141558A','DU006B1234568','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(48,175,'6141558A','DU006B1234569','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(49,176,'6141558A','DU006B1234570','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(50,177,'6141558A','DU006B1234571','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(51,178,'6141558A','DU006B1234572','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(52,179,'6141558A','DU006B1234573','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(53,180,'6141558A','DU006B1234574','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(54,181,'6141558A','DU006B1234575','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(55,182,'6141558A','DU006B1234576','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(56,183,'6141558A','DU006B1234577','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(57,184,'6141558A','DU006B1234578','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(58,185,'6141558A','DU006B1234579','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(59,186,'6141558A','DU006B1234580','REDAN','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-08 12:21:42','2024-11-08 13:21:42'),(60,187,'6141558A','DU006B1234581','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(61,188,'6141558A','DU006B1234582','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(62,189,'6141558A','DU006B1234583','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(63,190,'6141558A','DU006B1234584','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(64,191,'6141558A','DU006B1234585','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(65,192,'6141558A','DU006B1234586','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(66,193,'6141558A','DU006B1234587','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(67,194,'6141558A','DU006B1234588','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(68,195,'6141558A','DU006B1234589','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(69,196,'6141558A','DU006B1234590','PETRO TRADE','Diesel',20.00,'pjohns@gmail.com','Verified','2024-11-14 07:12:12','2024-11-14 08:12:12'),(70,197,'6141558A','DU006B1234591','REDAN','Diesel',20.00,'tichaonamidzi91@gmail.com','Verified','2024-11-15 05:13:08','2024-11-15 06:13:08');
/*!40000 ALTER TABLE `add_fuel_logs_tb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `add_fuel_tb`
--

DROP TABLE IF EXISTS `add_fuel_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `add_fuel_tb` (
  `add_id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(13) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `fuel_type` enum('Petrol','Diesel') NOT NULL,
  `amount` int(11) DEFAULT NULL CHECK (`amount` > 0),
  `email` varchar(100) NOT NULL,
  `otp_verification` enum('Verified','Pending') DEFAULT 'Pending',
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`add_id`),
  UNIQUE KEY `serial_number` (`serial_number`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `add_fuel_tb`
--

LOCK TABLES `add_fuel_tb` WRITE;
/*!40000 ALTER TABLE `add_fuel_tb` DISABLE KEYS */;
INSERT INTO `add_fuel_tb` VALUES (129,'PU006B1234552','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(130,'PU006B1234553','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(131,'PU006B1234554','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(132,'PU006B1234555','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(133,'PU006B1234556','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(134,'PU006B1234557','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(135,'PU006B1234558','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(136,'PU006B1234559','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(137,'PU006B1234560','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(138,'PU006B1234561','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(139,'PU006B1234562','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(140,'PU006B1234563','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(141,'PU006B1234564','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(142,'PU006B1234565','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(143,'PU006B1234566','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(144,'PU006B1234567','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(145,'PU006B1234568','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(146,'PU006B1234569','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(147,'PU006B1234570','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(148,'PU006B1234571','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(149,'PU006B1234572','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(150,'PU006B1234573','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(151,'PU006B1234574','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(152,'PU006B1234575','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(153,'PU006B1234576','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(154,'PU006B1234577','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(155,'PU006B1234578','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(156,'PU006B1234579','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(157,'PU006B1234580','PETRO TRADE','Petrol',20,'pjohns@gmail.com','Verified','2024-11-08 12:20:09'),(179,'DU006B1234573','REDAN','Diesel',20,'pjohns@gmail.com','Verified','2024-11-08 12:21:42'),(180,'DU006B1234574','REDAN','Diesel',20,'pjohns@gmail.com','Verified','2024-11-08 12:21:42'),(181,'DU006B1234575','REDAN','Diesel',20,'pjohns@gmail.com','Verified','2024-11-08 12:21:42'),(182,'DU006B1234576','REDAN','Diesel',20,'pjohns@gmail.com','Verified','2024-11-08 12:21:42'),(183,'DU006B1234577','REDAN','Diesel',20,'pjohns@gmail.com','Verified','2024-11-08 12:21:42'),(184,'DU006B1234578','REDAN','Diesel',20,'pjohns@gmail.com','Verified','2024-11-08 12:21:42'),(185,'DU006B1234579','REDAN','Diesel',20,'pjohns@gmail.com','Verified','2024-11-08 12:21:42'),(186,'DU006B1234580','REDAN','Diesel',20,'pjohns@gmail.com','Verified','2024-11-08 12:21:42'),(187,'DU006B1234581','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(188,'DU006B1234582','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(189,'DU006B1234583','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(190,'DU006B1234584','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(191,'DU006B1234585','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(192,'DU006B1234586','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(193,'DU006B1234587','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(194,'DU006B1234588','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(195,'DU006B1234589','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(196,'DU006B1234590','PETRO TRADE','Diesel',20,'pjohns@gmail.com','Verified','2024-11-14 07:12:12'),(197,'DU006B1234591','REDAN','Diesel',20,'tichaonamidzi91@gmail.com','Verified','2024-11-15 05:13:08');
/*!40000 ALTER TABLE `add_fuel_tb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `condition_service_tb`
--

DROP TABLE IF EXISTS `condition_service_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `condition_service_tb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(50) NOT NULL,
  `fuel_type` enum('petrol','diesel') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `issued_to` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp_verification` varchar(6) NOT NULL,
  `ec_number` varchar(20) NOT NULL,
  `transaction_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_serial_fuel` (`serial_number`,`fuel_type`),
  KEY `fk_ec_number` (`ec_number`),
  CONSTRAINT `fk_ec_number` FOREIGN KEY (`ec_number`) REFERENCES `user_login_tb` (`ec_number`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `condition_service_tb`
--

LOCK TABLES `condition_service_tb` WRITE;
/*!40000 ALTER TABLE `condition_service_tb` DISABLE KEYS */;
INSERT INTO `condition_service_tb` VALUES (1,'DU006B1234570','diesel',20.00,'Peter','pjohns@gmail.com','Verifi','6141558A','2024-11-14 12:38:35'),(2,'DU006B1234571','diesel',20.00,'Daniel','dani27@peterpan.org','Verifi','6141558A','2024-11-15 06:40:51'),(3,'DU006B1234572','diesel',20.00,'Hobbs','hobbz@gemstorn.com','Verifi','6141558A','2024-11-15 12:40:39');
/*!40000 ALTER TABLE `condition_service_tb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuel_details_tb`
--

DROP TABLE IF EXISTS `fuel_details_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fuel_details_tb` (
  `serial_number` varchar(13) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `fuel_type` enum('Petrol','Diesel') NOT NULL,
  `amount` int(11) DEFAULT NULL CHECK (`amount` >= 0),
  `date_supplied` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`serial_number`),
  CONSTRAINT `fk_fuel_details_serial_number` FOREIGN KEY (`serial_number`) REFERENCES `add_fuel_tb` (`serial_number`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_details_tb`
--

LOCK TABLES `fuel_details_tb` WRITE;
/*!40000 ALTER TABLE `fuel_details_tb` DISABLE KEYS */;
/*!40000 ALTER TABLE `fuel_details_tb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuel_transaction_logs`
--

DROP TABLE IF EXISTS `fuel_transaction_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fuel_transaction_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(13) DEFAULT NULL,
  `fuel_type` enum('Petrol','Diesel') NOT NULL,
  `amount` int(11) DEFAULT NULL CHECK (`amount` > 0),
  `issued_to` varchar(100) DEFAULT NULL,
  `issued_by` varchar(50) DEFAULT NULL,
  `purpose_of_issue` varchar(255) DEFAULT NULL,
  `otp_verification` enum('Verified','Pending') DEFAULT 'Pending',
  `transaction_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `serial_number` (`serial_number`),
  KEY `issued_by` (`issued_by`),
  CONSTRAINT `fuel_transaction_logs_ibfk_1` FOREIGN KEY (`serial_number`) REFERENCES `fuel_details_tb` (`serial_number`),
  CONSTRAINT `fuel_transaction_logs_ibfk_2` FOREIGN KEY (`issued_by`) REFERENCES `user_login_tb` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_transaction_logs`
--

LOCK TABLES `fuel_transaction_logs` WRITE;
/*!40000 ALTER TABLE `fuel_transaction_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `fuel_transaction_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuel_transaction_tb`
--

DROP TABLE IF EXISTS `fuel_transaction_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fuel_transaction_tb` (
  `serial_number` varchar(20) NOT NULL,
  `fuel_type` enum('petrol','diesel') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `car_registration_number` varchar(10) NOT NULL,
  `issued_to` varchar(50) NOT NULL,
  `purpose_of_issue` varchar(255) NOT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `otp_verification` varchar(10) NOT NULL,
  `ec_number` varchar(20) NOT NULL,
  `transaction_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `destination` varchar(100) DEFAULT NULL,
  `kilometres` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`serial_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_transaction_tb`
--

LOCK TABLES `fuel_transaction_tb` WRITE;
/*!40000 ALTER TABLE `fuel_transaction_tb` DISABLE KEYS */;
INSERT INTO `fuel_transaction_tb` VALUES ('DU006B1234555','diesel',20.00,'AEB168','Peter','ZITF','pjohns@gmail.com','Verified','6141558A','2024-11-12 09:04:08','Chinhoyi',20.00),('DU006B1234559','diesel',20.00,'AEB168','Peter','ZITF','pjohns@gmail.com','Verified','6141558A','2024-11-13 10:39:39','Chinhoyi',240.00),('DU006B1234560','diesel',20.00,'AEB168','Peter','ZITF','pjohns@gmail.com','Verified','6141558A','2024-11-13 10:39:39','Chinhoyi',240.00),('DU006B1234561','diesel',20.00,'AEB168','Peter','ZITF','pjohns@gmail.com','Verified','6141558A','2024-11-13 10:39:39','Chinhoyi',240.00),('DU006B1234562','diesel',20.00,'AEB168','Peter','ZITF','pjohns@gmail.com','Verified','6141558A','2024-11-13 10:39:39','Chinhoyi',240.00);
/*!40000 ALTER TABLE `fuel_transaction_tb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_login_tb`
--

DROP TABLE IF EXISTS `user_login_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_login_tb` (
  `ec_number` char(8) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`ec_number`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_login_tb`
--

LOCK TABLES `user_login_tb` WRITE;
/*!40000 ALTER TABLE `user_login_tb` DISABLE KEYS */;
INSERT INTO `user_login_tb` VALUES ('0157608M','GODFREY','CHIKWARA','dd','$2y$10$PvjaZ.vqvOaZ6pU4JSYet.pyw/T4S6fJwogqPl55FokrZkLJQJ6Sy','chikwaragody@gmail.com','2024-11-11 07:35:59'),('2912676B','Panze','Sibanda','SibsP','$2y$10$oQTeekNzq8jM4XTMuI4OU.aBiMBP3LZctXdakJz1tSJdZdKhoGkVC','psibanda@zhrc.org.zw','2024-11-15 10:56:39'),('6141558A','Peter','John','pjohns','$2y$10$nP7pnuQIZWx4Z/MZwZ0/oOhWjrCWDmF35ah83xRcKP5Pp6eDXtoEO','pjohns@gmail.com','2024-11-07 10:51:53'),('6141558F','Tichaona','Midzi','tmidzi','Admin123','tich@gmail.com','2024-11-06 12:23:00');
/*!40000 ALTER TABLE `user_login_tb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_logs_tb`
--

DROP TABLE IF EXISTS `user_logs_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_logs_tb` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `ec_number` char(8) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `ec_number` (`ec_number`),
  KEY `username` (`username`),
  CONSTRAINT `user_logs_tb_ibfk_1` FOREIGN KEY (`ec_number`) REFERENCES `user_login_tb` (`ec_number`),
  CONSTRAINT `user_logs_tb_ibfk_2` FOREIGN KEY (`username`) REFERENCES `user_login_tb` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logs_tb`
--

LOCK TABLES `user_logs_tb` WRITE;
/*!40000 ALTER TABLE `user_logs_tb` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_logs_tb` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-31 11:50:02
