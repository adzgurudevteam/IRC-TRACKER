-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: tax_db_live
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Table structure for table `assessment_query_ids`
--

DROP TABLE IF EXISTS `assessment_query_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_query_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `query_id` int(11) NOT NULL,
  `position_paper_id` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_query_ids`
--

LOCK TABLES `assessment_query_ids` WRITE;
/*!40000 ALTER TABLE `assessment_query_ids` DISABLE KEYS */;
/*!40000 ALTER TABLE `assessment_query_ids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT 1001,
  `employee_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_month` int(11) NOT NULL,
  `attendance_year` int(11) NOT NULL,
  `reporting_time` time NOT NULL,
  `log_off_time` time DEFAULT NULL,
  `working_hours` varchar(50) DEFAULT NULL,
  `early_log_off_reason` varchar(250) DEFAULT NULL,
  `early_log_off_mints` varchar(50) DEFAULT NULL,
  `is_late_entry` int(11) NOT NULL DEFAULT 0 COMMENT '0 = NO, 1 = YEs',
  `late_mints` varchar(50) DEFAULT NULL,
  `late_entry_reason` varchar(200) DEFAULT NULL,
  `admin_approval_for_late_entry` int(11) NOT NULL DEFAULT 0 COMMENT '0 = NOT APPROVED, 1 = APPROVED',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '1 = Cycle Active,\r\n2 = Cycle closed',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_assessment_data`
--

DROP TABLE IF EXISTS `audit_assessment_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_assessment_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `query_id` int(11) NOT NULL DEFAULT 0,
  `position_paper_id` int(11) NOT NULL DEFAULT 0,
  `claimable_tax_amount` double DEFAULT 0,
  `penalty_amount` double NOT NULL DEFAULT 0,
  `omitted_income_amount` double NOT NULL DEFAULT 0,
  `date_of_issue` date DEFAULT NULL,
  `date_of_closure` date DEFAULT NULL,
  `ref_no` varchar(50) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '1 = Collection stage\r\n2 = Objection stage',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_assessment_data`
--

LOCK TABLES `audit_assessment_data` WRITE;
/*!40000 ALTER TABLE `audit_assessment_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_assessment_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_close_request_data`
--

DROP TABLE IF EXISTS `audit_close_request_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_close_request_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `audit_id` int(11) NOT NULL,
  `auditor_id` int(11) NOT NULL,
  `approved_by` int(11) NOT NULL DEFAULT 0 COMMENT 'User ID',
  `approval_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 = Not Approved\r\n1 = Approved',
  `request_date` datetime DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `reason` varchar(300) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '0 = Inactive / Closed\r\n1 = Active / Open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_close_request_data`
--

LOCK TABLES `audit_close_request_data` WRITE;
/*!40000 ALTER TABLE `audit_close_request_data` DISABLE KEYS */;
INSERT INTO `audit_close_request_data` VALUES (1,1001,2,45,3,1,'2024-08-12 21:33:18','2024-08-12 21:38:20','Test Reason',1,'2024-08-12 21:33:18','2024-08-12 21:38:20');
/*!40000 ALTER TABLE `audit_close_request_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_memo_data`
--

DROP TABLE IF EXISTS `audit_memo_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_memo_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `secondary_auditor_id` int(11) NOT NULL,
  `primary_auditor_id` int(11) NOT NULL,
  `memo_no` varchar(50) DEFAULT NULL,
  `total_no_of_query` varchar(20) DEFAULT NULL,
  `date_of_issue` date DEFAULT NULL,
  `days_to_reply` varchar(20) DEFAULT NULL,
  `last_date_of_reply` date DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_memo_data`
--

LOCK TABLES `audit_memo_data` WRITE;
/*!40000 ALTER TABLE `audit_memo_data` DISABLE KEYS */;
INSERT INTO `audit_memo_data` VALUES (1,1001,1,46,45,'M01','10','2024-05-21','14','2024-06-04','A','2024-08-08 09:48:55','2024-08-08 09:48:55'),(2,1001,1,46,45,'M02','7','2024-05-27','7','2024-06-03','A','2024-08-08 09:49:45','2024-08-08 09:49:45'),(3,1001,2,46,47,'M72','12','2024-08-14','2','2024-08-15','A','2024-08-14 14:50:53','2024-08-14 14:50:53');
/*!40000 ALTER TABLE `audit_memo_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_tax_type_history`
--

DROP TABLE IF EXISTS `audit_tax_type_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_tax_type_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `audit_type_id` int(11) NOT NULL DEFAULT 0,
  `type_of_tax_id` varchar(100) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '0 = Inactive, 1 = Active',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_tax_type_history`
--

LOCK TABLES `audit_tax_type_history` WRITE;
/*!40000 ALTER TABLE `audit_tax_type_history` DISABLE KEYS */;
INSERT INTO `audit_tax_type_history` VALUES (1,1001,1,'1,2',1,'2024-08-08',NULL,1,'A','2024-08-08 05:47:35','2024-08-08 05:47:35'),(2,1001,1,'1',2,'2024-08-08',NULL,1,'A','2024-08-08 05:50:10','2024-08-08 05:50:10'),(3,1001,1,'1',3,'2024-08-08','2024-08-08',0,'A','2024-08-08 05:51:35','2024-08-08 07:10:59'),(4,1001,1,'1',4,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:01:57','2024-08-08 07:11:14'),(5,1001,1,'1',5,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:10:28','2024-08-08 07:11:51'),(6,1001,1,'1',3,'2024-08-08',NULL,1,'A','2024-08-08 07:10:59','2024-08-08 07:10:59'),(7,1001,1,'1',4,'2024-08-08',NULL,1,'A','2024-08-08 07:11:14','2024-08-08 07:11:14'),(8,1001,1,'1',5,'2024-08-08','2024-08-12',0,'A','2024-08-08 07:11:51','2024-08-12 17:38:38'),(9,1001,1,'1',6,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:15:20','2024-08-08 07:16:52'),(10,1001,1,'1,5',6,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:16:52','2024-08-08 07:17:08'),(11,1001,1,'1',6,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:17:08','2024-08-08 07:23:14'),(12,1001,1,'1,5',6,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:23:14','2024-08-08 07:37:43'),(13,1001,1,'1,5',7,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:34:24','2024-08-08 07:35:49'),(14,1001,1,'1,5',7,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:35:49','2024-08-08 07:38:39'),(15,1001,1,'1,5',6,'2024-08-08',NULL,1,'A','2024-08-08 07:37:43','2024-08-08 07:37:43'),(16,1001,1,'1,5',7,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:38:39','2024-08-08 07:38:57'),(17,1001,1,'1,5',7,'2024-08-08',NULL,1,'A','2024-08-08 07:38:57','2024-08-08 07:38:57'),(18,1001,1,'1',8,'2024-08-08','2024-08-08',0,'A','2024-08-08 07:41:17','2024-08-08 07:48:35'),(19,1001,1,'1',8,'2024-08-08',NULL,1,'A','2024-08-08 07:48:35','2024-08-08 07:48:35'),(20,1001,1,'1,2',9,'2024-08-08',NULL,1,'A','2024-08-08 08:12:27','2024-08-08 08:12:27'),(21,1001,1,'1',10,'2024-08-08','2024-08-08',0,'A','2024-08-08 08:23:29','2024-08-08 08:23:51'),(22,1001,1,'1',10,'2024-08-08','2024-08-12',0,'A','2024-08-08 08:23:51','2024-08-12 17:33:43'),(23,1001,2,'1',11,'2024-08-08',NULL,1,'A','2024-08-08 12:43:44','2024-08-08 12:43:44'),(24,1001,1,'1',10,'2024-08-12','2024-08-12',0,'A','2024-08-12 17:33:43','2024-08-12 17:36:59'),(25,1001,1,'1',10,'2024-08-12',NULL,1,'A','2024-08-12 17:36:59','2024-08-12 17:36:59'),(26,1001,1,'1',5,'2024-08-12',NULL,1,'A','2024-08-12 17:38:38','2024-08-12 17:38:38');
/*!40000 ALTER TABLE `audit_tax_type_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_time_spent_data`
--

DROP TABLE IF EXISTS `audit_time_spent_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_time_spent_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `audit_id` int(11) NOT NULL,
  `auditor_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time_in_hrs` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_time_spent_data`
--

LOCK TABLES `audit_time_spent_data` WRITE;
/*!40000 ALTER TABLE `audit_time_spent_data` DISABLE KEYS */;
INSERT INTO `audit_time_spent_data` VALUES (1,1001,2,3,46,'2024-08-07',7,'2024-08-20 21:17:04','2024-08-20 21:17:04'),(2,1001,2,3,46,'2024-08-08',8,'2024-08-20 21:17:04','2024-08-20 21:17:04'),(3,1001,2,3,46,'2024-08-19',6,'2024-08-20 21:17:04','2024-08-20 21:17:04');
/*!40000 ALTER TABLE `audit_time_spent_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_types`
--

DROP TABLE IF EXISTS `audit_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `audit_type` varchar(200) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_types`
--

LOCK TABLES `audit_types` WRITE;
/*!40000 ALTER TABLE `audit_types` DISABLE KEYS */;
INSERT INTO `audit_types` VALUES (1,1001,'Comprehensive audit',15,'A','2024-07-02 15:36:07','2024-07-02 15:36:07'),(2,1001,'Single issue audit',15,'A','2024-07-02 15:36:07','2024-07-02 15:36:07'),(3,1001,'Extractive Industries and Complex Audit',15,'A','2024-07-02 15:36:51','2024-07-02 15:36:51'),(4,1001,'Desk audits',15,'A','2024-07-02 15:36:51','2024-07-02 15:36:51');
/*!40000 ALTER TABLE `audit_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audits_data`
--

DROP TABLE IF EXISTS `audits_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audits_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `audit_expected_complete_date` date DEFAULT NULL,
  `audit_start_date` date DEFAULT NULL,
  `audit_end_date` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audits_data`
--

LOCK TABLES `audits_data` WRITE;
/*!40000 ALTER TABLE `audits_data` DISABLE KEYS */;
INSERT INTO `audits_data` VALUES (1,1001,4,49,'2024-10-24','2024-08-08',NULL,1,'A','2024-08-08 09:08:33','2024-08-08 09:08:33'),(2,1001,1,45,NULL,'2024-08-08','2024-08-13',2,'A','2024-08-08 09:41:18','2024-08-19 18:59:30'),(3,1001,2,46,'2025-05-01','2024-08-13',NULL,1,'A','2024-08-13 15:53:45','2024-08-13 15:53:45');
/*!40000 ALTER TABLE `audits_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `pin_code` varchar(10) DEFAULT NULL,
  `website_name` varchar(50) DEFAULT NULL,
  `company_name` varchar(200) NOT NULL,
  `company_logo` varchar(100) DEFAULT NULL COMMENT 'Document',
  `company_email` varchar(50) NOT NULL,
  `company_email_password` varchar(100) DEFAULT NULL,
  `company_mobile` varchar(15) NOT NULL,
  `company_phone` varchar(15) DEFAULT NULL,
  `company_address` varchar(200) DEFAULT NULL,
  `company_city` varchar(50) DEFAULT NULL,
  `company_district` varchar(50) DEFAULT NULL,
  `company_state` varchar(20) DEFAULT NULL,
  `company_pincode` varchar(10) DEFAULT NULL,
  `gstin_no` varchar(20) DEFAULT NULL,
  `tan` varchar(20) DEFAULT NULL,
  `pan` varchar(20) DEFAULT NULL,
  `joined_date` date NOT NULL,
  `validity_period` int(11) NOT NULL COMMENT 'In months',
  `expiry_date` date NOT NULL,
  `total_charge` double NOT NULL COMMENT 'Regiustration charge',
  `rental_type` int(11) NOT NULL DEFAULT 0 COMMENT '0 - Monthly , 1 - Yearly',
  `rental_charge` double NOT NULL,
  `paid_amount` double NOT NULL,
  `due_amount` double NOT NULL DEFAULT 0,
  `payment_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 - Due, 1 - Paid',
  `sms_enabled` int(11) NOT NULL DEFAULT 0 COMMENT '1 - Enable, 0 - Disable',
  `google_api_enable` int(11) NOT NULL DEFAULT 0 COMMENT '1 - Enable, 0 - Disable',
  `whatsapp_integration_enable` int(11) NOT NULL DEFAULT 0 COMMENT '1 - Enable, 0 - Disable',
  `live_location_tracking_enable` int(11) NOT NULL DEFAULT 0 COMMENT '1 - Enable, 0 - Disable',
  `multi_language_support_enable` int(11) NOT NULL DEFAULT 0 COMMENT '1 - Enable, 0 - Disable',
  `sms_package_code` varchar(50) DEFAULT NULL,
  `registration_charge` double NOT NULL DEFAULT 0,
  `sms_recharge_date` date DEFAULT NULL,
  `sms_validity_period` int(11) NOT NULL DEFAULT 0 COMMENT 'In Month',
  `sms_gateway_type` int(11) NOT NULL DEFAULT 1 COMMENT '1 - Saha CyberTech SMS API, 2 - Personal SMS api',
  `sms_gateway` varchar(10) DEFAULT NULL,
  `sms_endpoint` varchar(250) DEFAULT NULL,
  `sms_sid` varchar(10) DEFAULT NULL,
  `send_auto_sms` int(11) NOT NULL DEFAULT 0 COMMENT '0 - Manual, 1 - Automatic',
  `total_sms` int(11) NOT NULL DEFAULT 0,
  `sms_sent` int(11) NOT NULL DEFAULT 0,
  `sms_balance` int(11) NOT NULL DEFAULT 0,
  `sms_sid_enable` int(11) NOT NULL DEFAULT 0 COMMENT '0 - Disable, 1 - Enable',
  `max_product` int(11) NOT NULL DEFAULT 0,
  `max_user` int(11) NOT NULL DEFAULT 0,
  `max_manager` int(11) NOT NULL DEFAULT 0,
  `max_category` int(11) NOT NULL DEFAULT 0,
  `max_banner_content` int(11) NOT NULL,
  `max_special_menu` int(11) NOT NULL DEFAULT 4,
  `product_added` int(11) NOT NULL DEFAULT 0,
  `user_added` int(11) NOT NULL DEFAULT 0 COMMENT 'General Employee',
  `manager_added` int(11) NOT NULL DEFAULT 0,
  `category_added` int(11) NOT NULL DEFAULT 0,
  `feature_plan` int(11) NOT NULL COMMENT '7 - Basic, 2 - Standard, 1 - Premium, 3 - Custom',
  `project_service_type` int(11) NOT NULL COMMENT '1 - Grocery, 2 - Texttile, 3 - Home Appliances, 4 - All',
  `application_server` int(11) DEFAULT 1 COMMENT '1 - Saha CyberTech Server, 2 - Company Own server, 3 - 3rdf Party server',
  `mac_id` varchar(20) DEFAULT NULL COMMENT 'MAC ID of the server',
  `ip` varchar(15) DEFAULT NULL COMMENT 'ip of the server',
  `site_url` varchar(200) DEFAULT NULL,
  `trade_license` varchar(100) DEFAULT NULL COMMENT 'Document',
  `gstin_certificate` varchar(100) DEFAULT NULL COMMENT 'Document',
  `pan_card` varchar(100) DEFAULT NULL COMMENT 'Document',
  `company_director_list` varchar(100) DEFAULT NULL COMMENT 'Document',
  `company_master_data` varchar(100) DEFAULT NULL COMMENT 'Document',
  `company_type` int(11) NOT NULL DEFAULT 1 COMMENT '1 - PVT, 2 - LTD, 3 - LLP, 4 - Partnership, 5 - Propreitary, 6 - Others',
  `cin_document` varchar(100) DEFAULT NULL COMMENT 'Document',
  `moa_aoa` varchar(100) DEFAULT NULL COMMENT 'Document',
  `partnership_deed` varchar(100) DEFAULT NULL COMMENT 'Document',
  `company_photograph_1` varchar(100) DEFAULT NULL COMMENT 'Document. Outside',
  `company_photograph_2` varchar(100) DEFAULT NULL COMMENT 'Document. Inside',
  `company_photograph_3` varchar(100) DEFAULT NULL COMMENT 'Document. Inside',
  `corporate_mail_id` varchar(100) DEFAULT NULL,
  `account_number` varchar(30) DEFAULT NULL,
  `account_holder_name` varchar(100) DEFAULT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `branch_address` varchar(200) DEFAULT NULL,
  `cancelled_cheque` varchar(100) DEFAULT NULL COMMENT 'Document',
  `active` int(11) NOT NULL DEFAULT 1,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` VALUES (1001,NULL,'IRC','9123456789','info@adzguru.co',NULL,NULL,NULL,NULL,NULL,NULL,'Revenue and Audit Insight Tracking Enabled System (RAITES)','logo.png','info@adzguru.co','','9123456789',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-06-01',12,'2025-06-30',50000,1,4000,6000,44000,0,1,1,1,1,1,NULL,15000,'2022-01-01',12,1,NULL,NULL,NULL,1,0,0,0,0,0,0,0,0,0,4,0,0,0,0,1,4,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'A','2024-07-24 05:28:22');
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `industry_type_id` int(11) NOT NULL DEFAULT 0,
  `company_code` varchar(100) DEFAULT NULL,
  `street_number` varchar(50) DEFAULT NULL,
  `street_name` varchar(100) DEFAULT NULL,
  `city_or_suburb` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `fax` varchar(100) DEFAULT NULL,
  `tax_identification_number` varchar(100) DEFAULT NULL,
  `business_registration_number` varchar(100) DEFAULT NULL,
  `case_code` varchar(50) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `active_inactive_date` date DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,1001,'Company A',2,'Other LT Audit #1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-1',NULL,'123456789-1',1,NULL,'A','2024-08-08 05:47:35','2024-08-08 05:47:35'),(2,1001,'Company B',1,'Construction Audit #1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-2',NULL,'123456789-2',1,NULL,'A','2024-08-08 05:50:10','2024-08-08 05:50:10'),(3,1001,'Company C',2,'Other LT Audit #2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-3',NULL,'123456789-3',1,NULL,'A','2024-08-08 05:51:35','2024-08-08 07:10:59'),(4,1001,'Company D',2,'Other LT Audit #3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-4',NULL,'123456789-4',1,NULL,'A','2024-08-08 07:01:57','2024-08-08 07:11:14'),(5,1001,'Company E',2,'Other LT Audit #4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-5',NULL,'123456789-5',1,NULL,'A','2024-08-08 07:10:28','2024-08-12 17:38:38'),(6,1001,'Company F',2,'Other LT Audit #5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-6',NULL,'123456789-6',1,NULL,'A','2024-08-08 07:15:20','2024-08-08 07:37:43'),(7,1001,'Company G',2,'Other LT Audit #6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-7',NULL,'123456789-7',1,NULL,'A','2024-08-08 07:34:24','2024-08-08 07:38:57'),(8,1001,'Company H',8,'Other LT Audit #7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-8',NULL,'123456789-8',1,NULL,'A','2024-08-08 07:41:17','2024-08-08 07:48:35'),(9,1001,'Company I',2,'Other LT Audit #8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-9',NULL,'123456789-9',1,NULL,'A','2024-08-08 08:12:27','2024-08-08 08:12:27'),(10,1001,'Company J',2,'Other LT Audit #9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123456789-10',NULL,'123456789-10',1,NULL,'A','2024-08-08 08:23:29','2024-08-12 17:36:59'),(11,1001,'Company K',2,'Other LT Audit #K',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TINJG545457890',NULL,'AUI78927890',1,NULL,'A','2024-08-08 12:43:43','2024-08-08 12:43:43');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_assigned_data`
--

DROP TABLE IF EXISTS `company_assigned_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_assigned_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auditor_id` int(11) NOT NULL,
  `company_ids` int(11) DEFAULT 0,
  `primary_secondary` int(11) NOT NULL DEFAULT 0 COMMENT '1 = Primary, 2 = Secondary',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_assigned_data`
--

LOCK TABLES `company_assigned_data` WRITE;
/*!40000 ALTER TABLE `company_assigned_data` DISABLE KEYS */;
INSERT INTO `company_assigned_data` VALUES (1,1001,3,45,1,1,'A','2024-08-08 08:43:44','2024-08-08 11:27:05'),(3,1001,3,46,2,1,'A','2024-08-08 08:45:52','2024-08-14 15:21:28'),(4,1001,3,46,2,2,'A','2024-08-08 08:45:52','2024-08-08 08:45:52'),(5,1001,3,48,2,2,'A','2024-08-08 08:45:52','2024-08-08 08:45:52'),(6,1001,3,48,3,1,'A','2024-08-08 08:46:44','2024-08-08 08:46:44'),(7,1001,3,46,3,2,'A','2024-08-08 08:46:44','2024-08-08 08:46:44'),(8,1001,3,47,3,2,'A','2024-08-08 08:46:44','2024-08-08 08:46:44'),(9,1001,3,49,4,1,'A','2024-08-08 08:48:19','2024-08-08 08:48:19'),(10,1001,3,47,5,1,'A','2024-08-08 08:48:55','2024-08-08 08:50:47'),(13,1001,3,46,5,2,'A','2024-08-08 08:50:47','2024-08-08 08:50:47'),(14,1001,3,49,5,2,'A','2024-08-08 08:50:47','2024-08-08 08:50:47'),(15,1001,3,46,6,1,'A','2024-08-08 08:51:27','2024-08-08 08:51:27'),(16,1001,3,48,6,2,'A','2024-08-08 08:51:27','2024-08-08 08:51:27'),(17,1001,3,49,6,2,'A','2024-08-08 08:51:27','2024-08-08 08:51:27'),(18,1001,3,48,7,1,'A','2024-08-08 08:56:07','2024-08-08 08:56:07'),(19,1001,3,45,7,2,'A','2024-08-08 08:56:07','2024-08-08 08:56:07'),(20,1001,3,47,7,2,'A','2024-08-08 08:56:07','2024-08-08 08:56:07'),(21,1001,3,49,8,1,'A','2024-08-08 08:56:43','2024-08-08 08:56:43'),(22,1001,3,46,8,2,'A','2024-08-08 08:56:43','2024-08-08 08:56:43'),(23,1001,3,47,8,2,'A','2024-08-08 08:56:43','2024-08-08 08:56:43'),(24,1001,3,49,9,1,'A','2024-08-08 09:01:23','2024-08-08 09:01:23'),(25,1001,3,45,9,2,'A','2024-08-08 09:01:23','2024-08-08 09:01:23'),(26,1001,3,46,9,2,'A','2024-08-08 09:01:23','2024-08-08 09:01:23'),(27,1001,3,15,1,2,'A','2024-08-08 11:27:05','2024-08-08 11:27:05'),(28,1001,3,46,1,2,'A','2024-08-08 11:27:05','2024-08-08 11:27:05'),(29,1001,3,45,11,1,'A','2024-08-08 17:36:52','2024-08-08 17:36:52'),(30,1001,3,48,11,2,'A','2024-08-08 17:36:53','2024-08-08 17:36:53');
/*!40000 ALTER TABLE `company_assigned_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_industry_type`
--

DROP TABLE IF EXISTS `company_industry_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_industry_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `industry_type` varchar(250) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_industry_type`
--

LOCK TABLES `company_industry_type` WRITE;
/*!40000 ALTER TABLE `company_industry_type` DISABLE KEYS */;
INSERT INTO `company_industry_type` VALUES (1,1001,'Construction Audit',6,'A','2024-07-05 12:23:13','2024-07-05 12:23:13'),(2,1001,'Other LT Audit',6,'A','2024-07-05 12:25:23','2024-07-05 12:25:23'),(3,1001,'some industry',6,'D','2024-07-05 19:18:33','2024-07-05 19:18:33'),(4,1001,'tesat',6,'D','2024-07-08 18:21:17','2024-07-08 18:21:17'),(5,1001,'New industry',6,'D','2024-07-16 10:49:24','2024-07-16 10:49:24'),(6,1001,'demo industry',3,'D','2024-07-25 11:25:08','2024-07-25 11:25:08'),(7,1001,'Extractives Audit (Mining)',3,'A','2024-08-08 05:17:40','2024-08-08 05:17:40'),(8,1001,'Primary Production & Export',3,'A','2024-08-08 05:17:52','2024-08-08 05:17:52'),(9,1001,'Finance & Banking Audit',3,'A','2024-08-08 05:18:03','2024-08-08 05:18:03'),(10,1001,'Government & SOE Audit',3,'A','2024-08-08 05:18:16','2024-08-08 05:18:16');
/*!40000 ALTER TABLE `company_industry_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_notice_data`
--

DROP TABLE IF EXISTS `company_notice_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_notice_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `query_ids` varchar(200) DEFAULT NULL,
  `notice_no` varchar(100) DEFAULT NULL,
  `notice_section` varchar(100) DEFAULT NULL,
  `date_of_notice_issue` date DEFAULT NULL,
  `days_to_reply_notice` varchar(20) DEFAULT NULL,
  `last_date_of_reply` date DEFAULT NULL,
  `date_of_reply_notice` date DEFAULT NULL,
  `notice_status` int(11) NOT NULL DEFAULT 2 COMMENT '0 = No Input 1 = Submitted 2 = Pending 3 = Overdue',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_notice_data`
--

LOCK TABLES `company_notice_data` WRITE;
/*!40000 ALTER TABLE `company_notice_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_notice_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `added_by` int(11) NOT NULL DEFAULT 0 COMMENT 'Current User ID',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,1001,'Sales',3,'D','2024-01-02 17:48:57'),(2,1001,'Back-End',3,'D','2024-01-02 17:49:10'),(3,1001,'HR',3,'D','2024-01-02 17:49:17'),(4,1001,'IT ',6,'D','2024-01-12 18:38:09'),(5,1001,'Domestic Sales',6,'D','2024-01-12 18:38:26'),(6,1001,'International Sales',6,'D','2024-01-12 18:38:41'),(7,1001,'E-commerce',6,'D','2024-02-19 19:38:55'),(8,1001,'Other Large Taxpayer Tax Audits, Refunds and Offsets',3,'A','2024-07-26 06:59:50'),(9,1001,'Extractive Industries and Complex Tax Audits',3,'A','2024-07-26 07:00:23');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `designations`
--

DROP TABLE IF EXISTS `designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `designations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT 1001,
  `designation_title` varchar(150) NOT NULL COMMENT 'Name of the Designatioon',
  `responsibilities` mediumtext DEFAULT NULL COMMENT 'Text Input',
  `experience_required` varchar(20) DEFAULT NULL COMMENT 'Text Input\r\nFormat = 00 Years 00 Months',
  `added_by` int(11) NOT NULL COMMENT 'Current User ID',
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '0 = Inactive 1 = Active',
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active / Available D = Deleted',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_update_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `designations`
--

LOCK TABLES `designations` WRITE;
/*!40000 ALTER TABLE `designations` DISABLE KEYS */;
INSERT INTO `designations` VALUES (1,1001,'Developer',NULL,'2 Years',1,1,'D','2023-11-20 14:01:10','2023-11-20 14:01:10'),(2,1001,'Designer',NULL,NULL,1,1,'D','2023-11-20 14:03:11','2023-11-20 14:03:11'),(3,1001,'Sales',NULL,NULL,1,1,'D','2023-11-20 14:06:08','2023-11-20 14:06:08'),(7,1001,'HR Manager',NULL,NULL,3,1,'D','2024-01-02 17:50:03','2024-01-02 17:50:03'),(8,1001,'Sales Manager',NULL,NULL,6,1,'D','2024-01-03 13:17:00','2024-01-03 13:17:00'),(9,1001,'Operations Head',NULL,NULL,6,1,'D','2024-01-12 18:40:14','2024-01-12 18:40:14'),(10,1001,'Manager - IT & Admin',NULL,NULL,6,1,'D','2024-01-12 18:40:45','2024-01-12 18:40:45'),(11,1001,'Sr IT Technician ',NULL,NULL,6,1,'D','2024-01-12 18:41:23','2024-01-12 18:41:23'),(12,1001,'Business Development Manager ',NULL,NULL,6,1,'D','2024-01-12 18:41:58','2024-01-12 18:41:58'),(13,1001,'BDM & Talent Advisor',NULL,NULL,6,1,'D','2024-01-12 18:42:50','2024-01-12 18:42:50'),(14,1001,'Recruiter',NULL,NULL,6,1,'D','2024-01-12 18:43:17','2024-01-12 18:43:17'),(15,1001,'Tele calling Executive ',NULL,NULL,6,1,'D','2024-01-12 18:43:50','2024-01-12 18:43:50'),(16,1001,'BDE Domestic Sales',NULL,NULL,6,1,'D','2024-01-12 18:44:10','2024-01-12 18:44:10'),(17,1001,'Web Consultant',NULL,NULL,6,1,'D','2024-01-12 18:44:28','2024-01-12 18:44:28'),(18,1001,'Sr. Web Consultant',NULL,NULL,6,1,'D','2024-01-12 18:44:44','2024-01-12 18:44:44'),(19,1001,'Full Stack Developer','Full Stack Development','3',6,1,'D','2024-01-30 17:24:34','2024-01-30 17:24:34'),(20,1001,'Web Developer',NULL,'2',6,1,'D','2024-01-30 18:02:00','2024-01-30 18:02:00'),(21,1001,'Graphics Designer',NULL,'2',6,1,'D','2024-01-30 18:02:32','2024-01-30 18:02:32'),(22,1001,'IT - Head',NULL,'10',6,1,'D','2024-01-31 18:12:58','2024-01-31 18:12:58'),(23,1001,'Sales & Marketing Executive',NULL,'3',6,1,'D','2024-02-19 19:39:30','2024-02-19 19:39:30'),(24,1001,'Ads Manager',NULL,NULL,6,1,'D','2024-02-20 20:23:22','2024-02-20 20:23:22'),(25,1001,'Voice & Accent Trainer','Training','3',6,1,'D','2024-02-26 17:52:36','2024-02-26 17:52:36'),(26,1001,'Sr HR Recruiter','Recruitment','2',6,1,'D','2024-02-26 17:53:21','2024-02-26 17:53:21'),(27,1001,'Principal Auditor - Extractive Industries Tax Audits - Team A',NULL,NULL,3,1,'A','2024-07-26 07:02:03','2024-07-26 07:02:03'),(28,1001,'Principal Auditor - Extractive Industries Tax Audits - Team B',NULL,NULL,3,1,'A','2024-07-26 07:02:18','2024-07-26 07:02:18'),(29,1001,'Principal Auditor -Complex Audits',NULL,NULL,3,1,'A','2024-07-26 07:02:46','2024-07-26 07:02:46'),(30,1001,'Senior Auditor - Extractive Industries Tax Audits - Team A',NULL,NULL,3,1,'A','2024-07-26 07:03:15','2024-07-26 07:03:15'),(31,1001,'Senior Auditor - Extractive Industries Tax Audits - Team B',NULL,NULL,3,1,'A','2024-07-26 07:03:36','2024-07-26 07:03:36'),(32,1001,'Senior Auditor - Complex Audits',NULL,NULL,3,1,'A','2024-07-26 07:04:01','2024-07-26 07:04:01'),(33,1001,'Auditor - Extractive Industries Tax Audits - Team A',NULL,NULL,3,1,'A','2024-07-26 07:04:37','2024-07-26 07:04:37'),(34,1001,'Auditor - Extractive Industries Tax Audits - Team B',NULL,NULL,3,1,'A','2024-07-26 07:04:46','2024-07-26 07:04:46'),(35,1001,'Auditor - Complex Audits',NULL,NULL,3,1,'A','2024-07-26 07:05:08','2024-07-26 07:05:08'),(36,1001,'Director - Extractive Industries and Complex Tax Audits',NULL,NULL,3,1,'A','2024-07-26 07:07:58','2024-07-26 07:07:58'),(37,1001,'Director - Other Large Taxpayer Tax Audits, Refunds and Offsets',NULL,NULL,3,1,'A','2024-07-26 10:11:51','2024-07-26 10:11:51'),(38,1001,'Manager - Refunds & Offsets, Large Taxpayers',NULL,NULL,3,1,'A','2024-07-26 10:12:53','2024-07-26 10:12:53'),(39,1001,'Senior Auditor - GST Refunds & Offsets - Team A',NULL,NULL,3,1,'D','2024-07-26 10:13:43','2024-07-26 10:13:43'),(40,1001,'Senior Auditor - GST Refunds & Offsets - Team A LTP',NULL,NULL,3,1,'A','2024-07-26 10:14:02','2024-07-26 10:14:02'),(41,1001,'Senior Auditor - GST Refunds & Offsets - Team B LTP',NULL,NULL,3,1,'A','2024-07-26 10:14:14','2024-07-26 10:14:14'),(42,1001,'Senior Auditor - GST S65A Refunds & Offsets LTP',NULL,NULL,3,1,'A','2024-07-26 10:18:58','2024-07-26 10:18:58'),(43,1001,'Senior Auditor - Other Tax Refunds & Offsets LTP',NULL,NULL,3,1,'A','2024-07-26 10:19:53','2024-07-26 10:19:53'),(44,1001,'Auditor - GST Refunds & Offsets - Team A LTP',NULL,NULL,3,1,'A','2024-07-26 10:20:38','2024-07-26 10:20:38'),(45,1001,'Auditor - GST Refunds & Offsets - Team B LTP',NULL,NULL,3,1,'A','2024-07-26 10:21:23','2024-07-26 10:21:23'),(46,1001,'Auditor - GST S65A Refunds & Offsets LTP',NULL,NULL,3,1,'A','2024-07-26 10:21:49','2024-07-26 10:21:49'),(47,1001,'Auditor - Other Tax Refunds & Offsets LTP',NULL,NULL,3,1,'A','2024-07-26 10:22:09','2024-07-26 10:22:09');
/*!40000 ALTER TABLE `designations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_details`
--

DROP TABLE IF EXISTS `employee_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT 1001,
  `employee_name` varchar(100) NOT NULL,
  `employee_mobile` varchar(15) DEFAULT NULL,
  `employee_email` varchar(30) DEFAULT NULL,
  `employee_date_of_birth` date DEFAULT NULL,
  `employee_father_name` varchar(100) DEFAULT NULL,
  `employee_mother_name` varchar(100) DEFAULT NULL,
  `employee_blood_group` varchar(10) DEFAULT NULL,
  `employee_designation_id` int(11) NOT NULL DEFAULT 0,
  `employee_date_of_joinning` date DEFAULT NULL,
  `employee_experience_duration` varchar(20) DEFAULT NULL COMMENT 'Format = 00 Years 00 Months',
  `employee_payroll` int(11) NOT NULL DEFAULT 1 COMMENT '1 = company payroll\r\n2 = contact',
  `employee_grade` int(11) NOT NULL DEFAULT 4,
  `employee_id` varchar(10) DEFAULT NULL,
  `department_id` int(11) NOT NULL DEFAULT 0,
  `salary_amount` varchar(10) DEFAULT NULL,
  `webmail_address` varchar(100) DEFAULT NULL,
  `current_address` varchar(200) DEFAULT NULL,
  `permanent_address` varchar(200) DEFAULT NULL,
  `emergency_contact_person_name` varchar(100) DEFAULT NULL,
  `emergency_contact_person_mobile_number` varchar(15) DEFAULT NULL,
  `aadhaar_number` varchar(20) DEFAULT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `salary_account_number` varchar(50) DEFAULT NULL,
  `salary_account_ifsc_code` varchar(20) DEFAULT NULL,
  `uan_number` varchar(20) DEFAULT NULL,
  `esic_ip_number` varchar(50) DEFAULT NULL,
  `remarks` mediumtext DEFAULT NULL,
  `remark_by` int(11) DEFAULT NULL COMMENT 'Current User ID',
  `employee_added_by` int(11) NOT NULL COMMENT 'Current User ID',
  `last_working_day` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '0 = Inactive\r\n1 = Active\r\n2 = RESIGNED\r\n3 = ABSCONDED\r\n4 = SERVING_NOTICE\r\n5 = OTHER REASON',
  `inactive_reason` varchar(300) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active / Available\r\nD = Deleted',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_update_date` datetime NOT NULL DEFAULT current_timestamp(),
  `reporting_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_details`
--

LOCK TABLES `employee_details` WRITE;
/*!40000 ALTER TABLE `employee_details` DISABLE KEYS */;
INSERT INTO `employee_details` VALUES (2,1001,'Tax Admin','9561237890','tax.admin@email.com',NULL,NULL,NULL,NULL,7,'2023-04-05','10',1,4,'800145',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',3,3,NULL,1,NULL,'A','2024-01-03 12:16:58','2024-01-31 18:48:02',12),(11,1001,'Jyotirmoy Saha','8520741096','jsaha@email.com',NULL,NULL,NULL,NULL,20,'2023-07-28','2',1,4,'800184',2,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,'0000-00-00',1,NULL,'D','2024-01-30 18:24:14','2024-01-30 18:24:14',11),(39,1001,'New Auditor','7539512846','newauditor@email.com',NULL,NULL,NULL,NULL,0,'2024-07-09',NULL,1,4,'800185',0,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,NULL,1,NULL,'D','2024-07-09 13:24:16','2024-07-09 13:24:16',0),(40,1001,'New Auditor 2','7539513840','newauditor2@email.com',NULL,NULL,NULL,NULL,0,'2024-07-09',NULL,1,4,'800186',0,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,NULL,1,NULL,'D','2024-07-09 13:43:51','2024-07-09 13:43:51',0),(41,1001,'X','123456789','x@xmail.com',NULL,NULL,NULL,NULL,30,'2024-08-08',NULL,1,4,'00012',8,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,1,NULL,'A','2024-08-08 05:04:14','2024-08-08 05:04:14',0),(42,1001,'Y','987654321','y@ymail.com',NULL,NULL,NULL,NULL,33,'2024-08-08',NULL,1,4,'00014',8,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,1,NULL,'A','2024-08-08 05:06:51','2024-08-08 05:06:51',0),(43,1001,'Z','987321654','z@zmail.com',NULL,NULL,NULL,NULL,33,'2024-08-08',NULL,1,4,'00015',8,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,1,NULL,'A','2024-08-08 05:09:19','2024-08-08 05:09:19',0),(44,1001,'V','987456321','v@vmail.com',NULL,NULL,NULL,NULL,33,'2024-08-08',NULL,1,4,'00016',8,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,1,NULL,'A','2024-08-08 08:38:16','2024-08-08 08:38:16',0),(45,1001,'W','321987456','w@wmail.com',NULL,NULL,NULL,NULL,35,'2024-08-08',NULL,1,4,'00017',8,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'2024-08-08',5,'Other Reason','A','2024-08-08 08:39:20','2024-08-08 08:39:20',0);
/*!40000 ALTER TABLE `employee_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_reporting_manager`
--

DROP TABLE IF EXISTS `employee_reporting_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_reporting_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `reporting_manager_user_id` int(11) NOT NULL,
  `assigned_by_user_id` int(11) NOT NULL COMMENT 'Current User ID',
  `assign_date` date DEFAULT NULL,
  `dismiss_date` date DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active, D = Deactive',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_reporting_manager`
--

LOCK TABLES `employee_reporting_manager` WRITE;
/*!40000 ALTER TABLE `employee_reporting_manager` DISABLE KEYS */;
INSERT INTO `employee_reporting_manager` VALUES (2,1001,2,3,3,'2024-01-03',NULL,'A','2024-01-03 12:16:58'),(6,1001,6,6,6,'2024-01-12',NULL,'A','2024-01-12 19:03:34'),(7,1001,7,3,3,'2024-01-30',NULL,'A','2024-01-30 16:28:08'),(8,1001,8,6,6,'2024-01-30',NULL,'A','2024-01-30 16:46:45'),(9,1001,9,3,6,'2024-01-30',NULL,'A','2024-01-30 18:00:28'),(10,1001,10,3,6,'2024-01-30',NULL,'A','2024-01-30 18:14:00'),(11,1001,11,3,6,'2024-01-30',NULL,'A','2024-01-30 18:24:14'),(12,1001,12,3,6,'2024-01-30',NULL,'A','2024-01-30 18:31:30'),(13,1001,13,16,6,'2024-01-31',NULL,'A','2024-01-31 13:17:38'),(14,1001,14,16,6,'2024-01-31',NULL,'A','2024-01-31 13:40:38'),(15,1001,15,3,6,'2024-01-31',NULL,'A','2024-01-31 14:05:37'),(16,1001,16,19,6,'2024-01-31',NULL,'A','2024-01-31 17:33:48'),(17,1001,17,19,6,'2024-01-31',NULL,'A','2024-01-31 17:47:57'),(18,1001,18,19,6,'2024-01-31',NULL,'A','2024-01-31 17:58:53'),(19,1001,19,19,6,'2024-01-31',NULL,'A','2024-01-31 18:11:37'),(20,1001,20,3,6,'2024-01-31',NULL,'A','2024-01-31 18:28:46'),(21,1001,21,10,6,'2024-02-12',NULL,'A','2024-02-12 12:06:09'),(22,1001,22,10,6,'2024-02-12',NULL,'A','2024-02-12 14:07:56'),(23,1001,23,19,6,'2024-02-19',NULL,'A','2024-02-19 19:35:48'),(24,1001,24,3,6,'2024-02-19',NULL,'A','2024-02-19 19:47:37'),(25,1001,25,19,6,'2024-02-20',NULL,'A','2024-02-20 20:23:01'),(26,1001,26,3,6,'2024-02-20',NULL,'A','2024-02-20 20:27:04'),(27,1001,27,19,6,'2024-02-26',NULL,'A','2024-02-26 17:36:49'),(28,1001,28,19,6,'2024-02-26',NULL,'A','2024-02-26 17:46:12'),(29,1001,29,16,6,'2024-02-26',NULL,'A','2024-02-26 18:08:52'),(30,1001,30,6,6,'2024-02-27',NULL,'A','2024-02-27 17:49:47'),(31,1001,31,19,6,'2024-03-04',NULL,'A','2024-03-04 19:53:18'),(32,1001,32,19,6,'2024-03-04',NULL,'A','2024-03-04 20:01:39'),(33,1001,33,19,6,'2024-03-14',NULL,'A','2024-03-14 13:24:16'),(34,1001,34,19,6,'2024-03-14',NULL,'A','2024-03-14 13:33:23'),(35,1001,35,16,6,'2024-03-14',NULL,'A','2024-03-14 13:45:39'),(36,1001,36,16,6,'2024-03-15',NULL,'A','2024-03-15 15:11:36'),(37,1001,37,16,6,'2024-03-15',NULL,'A','2024-03-15 16:24:52'),(38,1001,38,16,6,'2024-03-15',NULL,'A','2024-03-15 16:46:19'),(39,1001,39,0,6,'2024-07-09',NULL,'A','2024-07-09 13:24:16'),(40,1001,40,0,6,'2024-07-09',NULL,'A','2024-07-09 13:43:51'),(41,1001,41,0,3,'2024-08-08',NULL,'A','2024-08-08 05:04:14'),(42,1001,42,0,3,'2024-08-08',NULL,'A','2024-08-08 05:06:51'),(43,1001,43,0,3,'2024-08-08',NULL,'A','2024-08-08 05:09:19'),(44,1001,44,0,3,'2024-08-08',NULL,'A','2024-08-08 08:38:16'),(45,1001,45,0,3,'2024-08-08',NULL,'A','2024-08-08 08:39:20');
/*!40000 ALTER TABLE `employee_reporting_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_details`
--

DROP TABLE IF EXISTS `message_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_txt` varchar(400) DEFAULT NULL,
  `attachment_name` varchar(200) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_details`
--

LOCK TABLES `message_details` WRITE;
/*!40000 ALTER TABLE `message_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT 1001,
  `sender_user_id` int(11) NOT NULL,
  `receiver_user_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active, D = Deactive/Deleted',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `new_message_log`
--

DROP TABLE IF EXISTS `new_message_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `new_message_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `msg_receiver_user_id` int(11) NOT NULL,
  `msg_sender_user_id` int(11) NOT NULL,
  `new_msg` int(11) NOT NULL DEFAULT 1 COMMENT '0 = read, 1 = unread',
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT '0 = Active, 1 = Deactive',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `new_message_log`
--

LOCK TABLES `new_message_log` WRITE;
/*!40000 ALTER TABLE `new_message_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `new_message_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_paper_data`
--

DROP TABLE IF EXISTS `position_paper_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `position_paper_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `query_id` int(11) NOT NULL DEFAULT 0,
  `position_paper_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `date_of_issue` date DEFAULT NULL,
  `initial_submission_date` date DEFAULT NULL,
  `extended_submission_date` date DEFAULT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT 0,
  `extension_days` varchar(10) DEFAULT NULL,
  `extention_end_date_to_reply` date DEFAULT NULL,
  `date_of_reply` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_paper_data`
--

LOCK TABLES `position_paper_data` WRITE;
/*!40000 ALTER TABLE `position_paper_data` DISABLE KEYS */;
INSERT INTO `position_paper_data` VALUES (1,1001,2,9,1,46,'2024-08-14','2024-08-17',NULL,0,NULL,NULL,'2024-08-16',0,'2024-08-16 18:23:37','2024-08-16 18:51:15'),(2,1001,2,11,1,46,'2024-08-14','2024-08-17',NULL,0,NULL,NULL,'2024-08-16',0,'2024-08-16 18:23:37','2024-08-16 18:51:15');
/*!40000 ALTER TABLE `position_paper_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_paper_extention_dates`
--

DROP TABLE IF EXISTS `position_paper_extention_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `position_paper_extention_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `position_paper_id` int(11) NOT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT 0,
  `extension_days` varchar(20) DEFAULT NULL,
  `extention_start_date` date DEFAULT NULL,
  `extention_end_date` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_paper_extention_dates`
--

LOCK TABLES `position_paper_extention_dates` WRITE;
/*!40000 ALTER TABLE `position_paper_extention_dates` DISABLE KEYS */;
INSERT INTO `position_paper_extention_dates` VALUES (1,1001,1,1,'2','2024-08-17','2024-08-18',1,'2024-08-16 18:49:40','2024-08-16 18:50:19');
/*!40000 ALTER TABLE `position_paper_extention_dates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_papers`
--

DROP TABLE IF EXISTS `position_papers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `position_papers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `date_of_issue` date DEFAULT NULL,
  `initial_submission_date` date DEFAULT NULL,
  `date_of_reply` date DEFAULT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT 0,
  `extension_days` varchar(10) DEFAULT NULL,
  `extention_end_date_to_reply` date DEFAULT NULL,
  `open_close_status` int(11) NOT NULL DEFAULT 1 COMMENT '1 = open, 0 = closed',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_papers`
--

LOCK TABLES `position_papers` WRITE;
/*!40000 ALTER TABLE `position_papers` DISABLE KEYS */;
INSERT INTO `position_papers` VALUES (1,1001,'P001',2,'2024-08-14','2024-08-17','2024-08-16',1,'2','2024-08-18',0,'2024-08-16 18:23:36','2024-08-16 18:51:15');
/*!40000 ALTER TABLE `position_papers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `query_data`
--

DROP TABLE IF EXISTS `query_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `query_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `memo_id` int(11) NOT NULL DEFAULT 0,
  `audit_type_id` int(11) NOT NULL DEFAULT 0,
  `tax_type_id` int(11) NOT NULL DEFAULT 0,
  `query_no` varchar(50) DEFAULT NULL,
  `total_no_of_query` varchar(20) DEFAULT NULL,
  `date_of_issue` date DEFAULT NULL,
  `days_to_reply` varchar(10) DEFAULT NULL,
  `last_date_of_reply` date DEFAULT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT 0 COMMENT '0 = Not Granted, 1 = Granted',
  `extension_days` varchar(10) DEFAULT NULL,
  `extention_end_date_to_reply` date DEFAULT NULL,
  `date_of_reply` date DEFAULT NULL,
  `query_reply_is_submitted` int(11) NOT NULL DEFAULT 0 COMMENT '0 = No Input\r\n1 = Submitted\r\n2 = Overdue\r\n3 = Pending\r\n4 = Partially Submitted',
  `no_of_query_solved` varchar(20) DEFAULT NULL,
  `no_of_query_unsolved` varchar(20) DEFAULT NULL,
  `query_status` int(11) NOT NULL DEFAULT 1 COMMENT '1 = open\r\n2 = close\r\n3 = notice_issued\r\n4 = force_closed',
  `remarks` varchar(200) DEFAULT NULL,
  `notice_no` varchar(100) DEFAULT NULL,
  `notice_section` varchar(50) DEFAULT NULL,
  `date_of_notice_issue` date DEFAULT NULL,
  `days_to_reply_notice` varchar(10) DEFAULT NULL,
  `date_of_reply_notice` date DEFAULT NULL,
  `notice_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 = No Input\r\n1 = Submitted\r\n2 = Pending\r\n3 = Overdue',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `query_data`
--

LOCK TABLES `query_data` WRITE;
/*!40000 ALTER TABLE `query_data` DISABLE KEYS */;
INSERT INTO `query_data` VALUES (1,1001,1,0,45,0,1,2,'QS01','1','2024-03-27','14','2024-04-10',0,NULL,NULL,'2024-04-18',1,'1','0',2,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-08 09:42:54','2024-08-08 09:58:48'),(2,1001,1,0,45,0,1,1,'QS02','1','2024-04-18','14','2024-05-02',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-08 09:44:09','2024-08-08 09:44:09'),(3,1001,1,0,45,0,1,1,'QS03','1','2024-04-22','7','2024-04-29',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-08 09:45:12','2024-08-08 09:45:12'),(4,1001,1,0,45,0,1,1,'QS04','1','2024-05-10','7','2024-05-17',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-08 09:45:51','2024-08-08 09:45:51'),(5,1001,1,0,45,0,1,1,'QS05','1','2024-05-13','7','2024-05-20',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-08 09:46:44','2024-08-08 09:46:44'),(6,1001,1,0,45,0,1,1,'QS06','1','2024-05-20','7','2024-05-27',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-08 09:47:28','2024-08-08 09:47:28'),(7,1001,1,0,45,1,1,1,'QS07','25','2024-05-21','14','2024-06-04',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-08 09:52:29','2024-08-08 09:52:29'),(8,1001,1,0,45,0,1,1,'QS08','14','2024-05-27','7','2024-06-03',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-08 09:53:14','2024-08-08 09:53:14'),(9,1001,2,0,46,0,1,1,'Q045','7','2024-08-08','5','2024-08-12',0,NULL,NULL,NULL,0,NULL,NULL,4,'Overdue',NULL,NULL,NULL,NULL,NULL,0,'2024-08-13 11:24:30','2024-08-13 15:58:37'),(10,1001,2,0,46,0,1,1,'Q566','9','2024-08-10','5','2024-08-14',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-14 12:54:50','2024-08-14 12:54:50'),(11,1001,2,0,46,0,1,1,'Q567','5','2024-08-15','2','2024-08-16',0,NULL,NULL,'2024-08-16',1,'5','0',2,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-16 13:22:37','2024-08-16 17:52:51');
/*!40000 ALTER TABLE `query_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `query_extension_dates`
--

DROP TABLE IF EXISTS `query_extension_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `query_extension_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `query_id` int(11) NOT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT 0,
  `extension_days` varchar(20) DEFAULT NULL,
  `extention_start_date` date NOT NULL,
  `extention_end_date` date NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `query_extension_dates`
--

LOCK TABLES `query_extension_dates` WRITE;
/*!40000 ALTER TABLE `query_extension_dates` DISABLE KEYS */;
INSERT INTO `query_extension_dates` VALUES (1,1001,2,0,'7','2024-05-03','2024-05-10',1,'2024-08-08 10:11:08','2024-08-08 10:11:08');
/*!40000 ALTER TABLE `query_extension_dates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `query_reply`
--

DROP TABLE IF EXISTS `query_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `query_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT 0,
  `query_id` int(11) NOT NULL DEFAULT 0,
  `date_of_reply` date DEFAULT NULL,
  `no_of_query_solved` varchar(20) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `query_reply`
--

LOCK TABLES `query_reply` WRITE;
/*!40000 ALTER TABLE `query_reply` DISABLE KEYS */;
INSERT INTO `query_reply` VALUES (1,1001,1,'2024-04-18','1','A','2024-08-08 09:58:48'),(2,1001,11,'2024-08-16','5','A','2024-08-16 17:52:51');
/*!40000 ALTER TABLE `query_reply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_collection_data`
--

DROP TABLE IF EXISTS `tax_collection_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tax_collection_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `tax_amount` double NOT NULL DEFAULT 0,
  `paid_amount` double NOT NULL DEFAULT 0,
  `pending_amount` double NOT NULL DEFAULT 0,
  `last_payment_date` date DEFAULT NULL,
  `penalty_amount` double NOT NULL DEFAULT 0,
  `penalty_paid_amount` double NOT NULL DEFAULT 0,
  `penalty_pending_amount` double NOT NULL DEFAULT 0,
  `penalty_last_payment_date` date DEFAULT NULL,
  `payment_status` int(11) NOT NULL DEFAULT 1 COMMENT '1 = active\r\n2 = closed\r\n3 = partially paid',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_collection_data`
--

LOCK TABLES `tax_collection_data` WRITE;
/*!40000 ALTER TABLE `tax_collection_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_collection_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_payment_history`
--

DROP TABLE IF EXISTS `tax_payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tax_payment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `tax_collection_id` int(11) NOT NULL,
  `payment_amount` double NOT NULL DEFAULT 0,
  `payment_date` date DEFAULT NULL,
  `payment_type` int(11) NOT NULL DEFAULT 1 COMMENT '1 = Tax Amount\r\n2 = Penalty Amount',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_payment_history`
--

LOCK TABLES `tax_payment_history` WRITE;
/*!40000 ALTER TABLE `tax_payment_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_payment_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_of_tax`
--

DROP TABLE IF EXISTS `type_of_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type_of_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `type_of_tax` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_of_tax`
--

LOCK TABLES `type_of_tax` WRITE;
/*!40000 ALTER TABLE `type_of_tax` DISABLE KEYS */;
INSERT INTO `type_of_tax` VALUES (1,1001,'CIT',15,'A','2024-07-02 15:34:26','2024-07-02 15:34:26'),(2,1001,'SWT',15,'A','2024-07-02 15:34:26','2024-07-02 15:34:26'),(3,1001,'DWT',15,'A','2024-07-02 15:35:08','2024-07-02 15:35:08'),(4,1001,'IWT',15,'A','2024-07-02 15:35:08','2024-07-02 15:35:08'),(5,1001,'GST',3,'A','2024-08-08 07:15:55','2024-08-08 07:15:55');
/*!40000 ALTER TABLE `type_of_tax` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_last_session_data`
--

DROP TABLE IF EXISTS `user_last_session_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_last_session_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_date` date DEFAULT NULL,
  `login_time` varchar(30) DEFAULT NULL,
  `logout_date` date DEFAULT NULL,
  `logout_time` varchar(30) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `infotext` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_last_session_data`
--

LOCK TABLES `user_last_session_data` WRITE;
/*!40000 ALTER TABLE `user_last_session_data` DISABLE KEYS */;
INSERT INTO `user_last_session_data` VALUES (1,1001,45,'2024-08-21','21:40:32','2024-08-13','15:38:07','127.0.0.1','127.0.0.1 || Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36 || 2024-08-21 21:40:32');
/*!40000 ALTER TABLE `user_last_session_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL DEFAULT 0,
  `user_type` int(11) NOT NULL COMMENT '(SADMIN, 7)\r\n(ADMIN, 6)\r\n(MANAGER, 5)\r\n(EMPLOYEE, 4)',
  `name` varchar(100) NOT NULL,
  `email` varchar(70) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `password` varchar(20) NOT NULL,
  `pass_hash` varchar(100) NOT NULL,
  `ref_id` int(11) NOT NULL DEFAULT 0 COMMENT 'Reference Table Id',
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = Deactive',
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active, D = Deleted / Removed',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `infotext` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1001,0,6,'demo admin','jsaha.demo@gmail.com','9123456789','Admin.1234','9cdca6289d90c1b87395bfcb2a07e1b407710d11141d6c5080fbdfba5360cdff',0,1,'A','2022-04-20 11:09:54',NULL),(3,1001,1001,7,'Super Admin','superadmin@email.com','1234567890','Sadmin.1234','ad2d410ec36b60990d53022a0383713d404e3c2b8d1b604c25226541bc74c390',0,1,'A','2023-12-28 11:03:54',NULL),(6,1001,2,6,'Tax Admin','tax.admin@email.com','9561237890','Taxadmin.1234','d48ed964359f3d5c5db9a93f8c08dded7480f1336e5ae5a3469d17e5467a1f98',0,1,'A','2024-01-03 12:16:58',NULL),(15,1001,11,4,'Jyotirmoy Saha','jsaha@email.com','8520741096','Joy.1234','196650546410c8620f28703ca0cc63e842319fdaaa85945ac349591b8d2e0c14',0,1,'D','2024-01-30 18:24:14',NULL),(43,1001,39,4,'New Auditor','newauditor@email.com','7539512846','New.1234','f2faf162666d07d462e267365eccdb361aecb16b888656ae9499878af6d32470',0,1,'D','2024-07-09 13:24:16',NULL),(44,1001,40,4,'New Auditor 2','newauditor2@email.com','7539513840','New2.1234','e89cbf93c61e0d682e4d99dba24c74603e00f62c62325e38f7b762cb4f1d4b2d',0,1,'D','2024-07-09 13:43:51',NULL),(45,1001,41,4,'X','x@xmail.com','123456789','x@123','88a4d9b2eaa914073bd1bf853eaf8b17cce0773f381f2f5b106e0240eb00da88',0,1,'A','2024-08-08 05:04:14',NULL),(46,1001,42,4,'Y','y@ymail.com','987654321','y@123','802e25b82917f1d77c4f9218af7cc290305beccaa78806e0bda081c96a817058',0,1,'A','2024-08-08 05:06:51',NULL),(47,1001,43,4,'Z','z@zmail.com','987321654','z@123','7137ef63ca374b334a5dba8a2b5b006819195d8d4c21950abbbcb7e318ac103d',0,1,'A','2024-08-08 05:09:19',NULL),(48,1001,44,4,'V','v@vmail.com','987456321','v@123','6c86183bfc1fa697680cd5070639f7ac43e2340c3272c105865845e1600946c2',0,1,'A','2024-08-08 08:38:16',NULL),(49,1001,45,4,'W','w@wmail.com','321987456','w@123','9798d56676bbb0cadb9d74ea07fa4dabdd096e2032a5551163178d1b26aba765',0,0,'A','2024-08-08 08:39:20',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-08-21 18:17:44
