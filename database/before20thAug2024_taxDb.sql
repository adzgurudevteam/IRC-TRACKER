-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: tax_db
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
  `claimable_tax_amount` double DEFAULT 0,
  `penalty_amount` double NOT NULL DEFAULT 0,
  `date_of_issue` date DEFAULT NULL,
  `date_of_closure` date DEFAULT NULL,
  `ref_no` varchar(50) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '1 = Collection stage\r\n2 = Objection stage',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_assessment_data`
--

LOCK TABLES `audit_assessment_data` WRITE;
/*!40000 ALTER TABLE `audit_assessment_data` DISABLE KEYS */;
INSERT INTO `audit_assessment_data` VALUES (1,1001,1,15,1,52000,48000,'2024-07-24',NULL,'REF258035',1,'2024-07-24 12:43:07','2024-07-24 12:43:07');
/*!40000 ALTER TABLE `audit_assessment_data` ENABLE KEYS */;
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
INSERT INTO `audit_memo_data` VALUES (1,1001,1,43,15,'M01','13','2024-07-12','12','2024-07-24','A','2024-07-10 18:15:23','2024-07-10 18:15:23'),(2,1001,1,43,15,'M02','12','2024-07-12','7','2024-07-19','A','2024-07-10 18:22:48','2024-07-10 18:22:48'),(3,1001,10,15,43,'M12','12','2024-07-16','9','2024-07-25','A','2024-07-16 10:38:14','2024-07-16 10:38:14');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_tax_type_history`
--

LOCK TABLES `audit_tax_type_history` WRITE;
/*!40000 ALTER TABLE `audit_tax_type_history` DISABLE KEYS */;
INSERT INTO `audit_tax_type_history` VALUES (1,1001,3,'1',5,'2024-07-05',NULL,1,'A','2024-07-05 15:33:49','2024-07-05 15:33:49'),(3,1001,1,'2,4',7,'2024-07-05',NULL,1,'A','2024-07-05 15:38:00','2024-07-05 15:38:00'),(8,1001,1,'',13,'2024-07-05','2024-07-30',0,'A','2024-07-05 16:39:07','2024-07-30 15:08:06'),(9,1001,1,'2,3',14,'2024-07-08',NULL,1,'A','2024-07-08 13:50:10','2024-07-08 13:50:10'),(10,1001,2,'1',15,'2024-07-25',NULL,1,'A','2024-07-25 15:56:02','2024-07-25 15:56:02'),(11,1001,1,'3,4',16,'2024-07-26','2024-07-26',0,'A','2024-07-26 17:24:31','2024-07-26 17:25:11'),(12,1001,1,'1,3',16,'2024-07-26','2024-07-26',0,'A','2024-07-26 17:25:11','2024-07-26 17:25:34'),(13,1001,2,'2',16,'2024-07-26','2024-07-26',0,'A','2024-07-26 17:25:34','2024-07-26 17:25:52'),(14,1001,1,'3,4',16,'2024-07-26','2024-07-26',0,'A','2024-07-26 17:25:52','2024-07-26 17:26:08'),(15,1001,1,'1,4',16,'2024-07-26',NULL,1,'A','2024-07-26 17:26:08','2024-07-26 17:26:08'),(16,1001,1,'1',13,'2024-07-30',NULL,1,'A','2024-07-30 15:08:06','2024-07-30 15:08:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_time_spent_data`
--

LOCK TABLES `audit_time_spent_data` WRITE;
/*!40000 ALTER TABLE `audit_time_spent_data` DISABLE KEYS */;
INSERT INTO `audit_time_spent_data` VALUES (1,1001,1,1,15,'2024-08-01',2,'2024-08-01 17:33:16','2024-08-01 17:33:16'),(2,1001,1,1,15,'2024-08-02',2,'2024-08-02 14:30:58','2024-08-02 14:30:58'),(3,1001,1,1,43,'2024-08-02',5,'2024-08-02 16:02:18','2024-08-02 16:02:18'),(4,1001,2,2,43,'2024-08-02',3,'2024-08-02 16:03:36','2024-08-02 16:03:36'),(5,1001,1,1,15,'2024-08-05',5,'2024-08-05 18:07:07','2024-08-05 18:07:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_types`
--

LOCK TABLES `audit_types` WRITE;
/*!40000 ALTER TABLE `audit_types` DISABLE KEYS */;
INSERT INTO `audit_types` VALUES (1,1001,'Comprehensive audit',15,'A','2024-07-02 15:36:07','2024-07-02 15:36:07'),(2,1001,'Single issue audit',15,'A','2024-07-02 15:36:07','2024-07-02 15:36:07'),(3,1001,'Extractive Industries and Complex Audit',15,'A','2024-07-02 15:36:51','2024-07-02 15:36:51'),(4,1001,'Desk audits',15,'A','2024-07-02 15:36:51','2024-07-02 15:36:51'),(5,1001,'Demo Audit Type',6,'A','2024-07-26 11:17:11','2024-07-26 11:17:11'),(6,1001,'New Aud Type',6,'D','2024-07-26 12:38:31','2024-07-26 12:38:31'),(7,1001,'edited aud type',6,'D','2024-07-26 12:40:41','2024-07-26 12:43:26'),(8,1001,'new aud type',6,'D','2024-07-26 13:15:03','2024-07-26 13:15:03'),(9,1001,'new aud edited',6,'A','2024-07-26 13:16:06','2024-07-26 13:16:15');
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
  `audit_start_date` date DEFAULT NULL,
  `audit_end_date` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audits_data`
--

LOCK TABLES `audits_data` WRITE;
/*!40000 ALTER TABLE `audits_data` DISABLE KEYS */;
INSERT INTO `audits_data` VALUES (1,1001,1,15,'2024-07-02',NULL,1,'A','2024-07-02 12:25:41','2024-07-02 12:25:41'),(2,1001,2,43,'2024-07-09',NULL,1,'A','2024-07-09 18:47:35','2024-07-09 18:47:35'),(3,1001,3,15,'2024-07-10',NULL,1,'A','2024-07-10 14:43:04','2024-07-10 14:43:04'),(4,1001,10,43,'2024-07-16',NULL,1,'A','2024-07-16 10:39:02','2024-07-16 10:39:02'),(5,1001,14,15,'2024-07-18','2024-07-30',2,'A','2024-07-18 14:53:19','2024-07-18 14:53:19'),(6,1001,13,15,'2024-07-18',NULL,1,'A','2024-07-18 14:59:36','2024-07-18 14:59:36');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,1001,'ABC company',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2024-08-05','A','2024-07-02 10:28:51','2024-08-05 18:06:56'),(2,1001,'XYZ Company',4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'A','2024-07-04 10:36:37','2024-07-04 10:36:37'),(3,1001,'PQR Company',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2024-08-05','A','2024-07-04 10:36:37','2024-08-05 12:34:42'),(9,1001,'with no tax',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'dswgvsdbv5',NULL,NULL,1,NULL,'A','2024-07-05 16:24:26','2024-07-05 16:24:26'),(10,1001,'sdvdsvdsv',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'sdvdsvdsvds',NULL,NULL,1,'2024-08-05','A','2024-07-05 16:28:52','2024-08-05 12:15:06'),(11,1001,'dsvvvvv',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'fsdbfbvfdbfd',NULL,NULL,1,NULL,'A','2024-07-05 16:35:35','2024-07-24 14:01:21'),(12,1001,'no data',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'dvdsvsdvdsv',NULL,NULL,1,NULL,'A','2024-07-05 16:38:35','2024-07-05 16:38:35'),(13,1001,'part data',1,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'dvdsvsdv',NULL,'',1,NULL,'A','2024-07-05 16:39:07','2024-07-30 15:08:06'),(14,1001,'New Company',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'uyegrib54168465465',NULL,NULL,1,NULL,'A','2024-07-08 13:50:10','2024-07-08 13:50:10'),(15,1001,'Jsaha International',5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'uyegri416844650',NULL,NULL,1,NULL,'A','2024-07-25 15:56:02','2024-07-25 15:56:02'),(16,1001,'Joy Com',2,'CIO55512',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TINJG54545',NULL,'AUI7892',1,NULL,'A','2024-07-26 17:24:31','2024-07-26 17:26:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_assigned_data`
--

LOCK TABLES `company_assigned_data` WRITE;
/*!40000 ALTER TABLE `company_assigned_data` DISABLE KEYS */;
INSERT INTO `company_assigned_data` VALUES (1,1001,6,15,1,1,'A','2024-07-09 13:52:45','2024-07-23 16:22:58'),(4,1001,6,43,2,1,'A','2024-07-09 14:48:49','2024-07-09 14:48:49'),(5,1001,6,15,3,1,'A','2024-07-10 14:42:54','2024-07-10 14:42:54'),(6,1001,6,43,9,1,'A','2024-07-12 16:15:58','2024-07-12 16:15:58'),(7,1001,6,44,9,2,'A','2024-07-12 16:15:58','2024-07-12 16:15:58'),(8,1001,6,15,1,1,'A','2024-07-15 16:37:29','2024-07-23 16:22:58'),(9,1001,6,43,10,1,'A','2024-07-16 10:37:10','2024-07-16 10:37:10'),(10,1001,6,15,10,2,'A','2024-07-16 10:37:10','2024-07-16 10:37:10'),(11,1001,6,44,10,2,'A','2024-07-16 10:37:10','2024-07-16 10:37:10'),(12,1001,6,15,14,1,'A','2024-07-16 10:50:00','2024-07-16 10:50:00'),(13,1001,6,43,14,2,'A','2024-07-16 10:50:00','2024-07-16 10:50:00'),(14,1001,6,15,13,1,'A','2024-07-18 14:59:27','2024-07-18 14:59:27'),(15,1001,6,43,13,2,'A','2024-07-18 14:59:27','2024-07-18 14:59:27'),(17,1001,6,43,1,2,'A','2024-07-22 15:33:24','2024-07-22 15:33:24'),(18,1001,6,44,1,2,'A','2024-07-22 15:33:24','2024-07-22 15:33:24');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_industry_type`
--

LOCK TABLES `company_industry_type` WRITE;
/*!40000 ALTER TABLE `company_industry_type` DISABLE KEYS */;
INSERT INTO `company_industry_type` VALUES (1,1001,'Construction Audit',6,'A','2024-07-05 12:23:13','2024-07-05 12:23:13'),(2,1001,'Other LT Audit',6,'A','2024-07-05 12:25:23','2024-07-05 12:25:23'),(3,1001,'some industry',6,'A','2024-07-05 19:18:33','2024-07-05 19:18:33'),(4,1001,'tesat',6,'A','2024-07-08 18:21:17','2024-07-08 18:21:17'),(5,1001,'New industry',6,'D','2024-07-16 10:49:24','2024-07-16 10:49:24'),(6,1001,'demo industry',3,'D','2024-07-25 11:25:08','2024-07-25 11:25:08'),(7,1001,'new ind edited',6,'A','2024-07-26 13:16:32','2024-07-26 13:16:40');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_notice_data`
--

LOCK TABLES `company_notice_data` WRITE;
/*!40000 ALTER TABLE `company_notice_data` DISABLE KEYS */;
INSERT INTO `company_notice_data` VALUES (1,1001,1,'1,4','N01',NULL,'2024-07-10','10','2024-07-20','2024-07-11',1,'2024-07-10 16:25:33','2024-07-11 17:27:17'),(2,1001,1,'4,5','N02',NULL,'2024-07-15','12','2024-07-27',NULL,2,'2024-07-15 16:26:12','2024-07-15 16:26:12'),(3,1001,10,'7,8','n12',NULL,'2024-07-16','5','2024-07-21','2024-07-16',1,'2024-07-16 10:43:10','2024-07-16 10:47:18'),(4,1001,1,'12','N78',NULL,'2024-07-23','2','2024-07-25',NULL,2,'2024-07-23 17:24:03','2024-07-23 17:24:03');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,1001,'Sales',3,'A','2024-01-02 17:48:57'),(2,1001,'Back-End',3,'A','2024-01-02 17:49:10'),(3,1001,'HR',3,'A','2024-01-02 17:49:17'),(4,1001,'IT ',6,'A','2024-01-12 18:38:09'),(5,1001,'Domestic Sales',6,'A','2024-01-12 18:38:26'),(6,1001,'International Sales',6,'A','2024-01-12 18:38:41'),(7,1001,'E-commerce ed',6,'A','2024-02-19 19:38:55'),(8,1001,'new dept ed',6,'D','2024-07-26 15:07:54');
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `designations`
--

LOCK TABLES `designations` WRITE;
/*!40000 ALTER TABLE `designations` DISABLE KEYS */;
INSERT INTO `designations` VALUES (1,1001,'Developer',NULL,'2 Years',1,1,'D','2023-11-20 14:01:10','2023-11-20 14:01:10'),(2,1001,'Designer',NULL,NULL,1,1,'D','2023-11-20 14:03:11','2023-11-20 14:03:11'),(3,1001,'Sales',NULL,NULL,1,1,'D','2023-11-20 14:06:08','2023-11-20 14:06:08'),(7,1001,'HR Manager',NULL,NULL,3,1,'A','2024-01-02 17:50:03','2024-01-02 17:50:03'),(8,1001,'Sales Manager',NULL,NULL,6,1,'A','2024-01-03 13:17:00','2024-01-03 13:17:00'),(9,1001,'Operations Head',NULL,NULL,6,1,'A','2024-01-12 18:40:14','2024-01-12 18:40:14'),(10,1001,'Manager - IT & Admin',NULL,NULL,6,1,'A','2024-01-12 18:40:45','2024-01-12 18:40:45'),(11,1001,'Sr IT Technician ',NULL,NULL,6,1,'A','2024-01-12 18:41:23','2024-01-12 18:41:23'),(12,1001,'Business Development Manager ',NULL,NULL,6,1,'A','2024-01-12 18:41:58','2024-01-12 18:41:58'),(13,1001,'BDM & Talent Advisor',NULL,NULL,6,1,'A','2024-01-12 18:42:50','2024-01-12 18:42:50'),(14,1001,'Recruiter',NULL,NULL,6,1,'A','2024-01-12 18:43:17','2024-01-12 18:43:17'),(15,1001,'Tele calling Executive ',NULL,NULL,6,1,'A','2024-01-12 18:43:50','2024-01-12 18:43:50'),(16,1001,'BDE Domestic Sales',NULL,NULL,6,1,'A','2024-01-12 18:44:10','2024-01-12 18:44:10'),(17,1001,'Web Consultant',NULL,NULL,6,1,'A','2024-01-12 18:44:28','2024-01-12 18:44:28'),(18,1001,'Sr. Web Consultant',NULL,NULL,6,1,'A','2024-01-12 18:44:44','2024-01-12 18:44:44'),(19,1001,'Full Stack Developer','Full Stack Development','3',6,1,'A','2024-01-30 17:24:34','2024-01-30 17:24:34'),(20,1001,'Web Developer',NULL,'2',6,1,'A','2024-01-30 18:02:00','2024-01-30 18:02:00'),(21,1001,'Graphics Designer',NULL,'2',6,1,'A','2024-01-30 18:02:32','2024-01-30 18:02:32'),(22,1001,'IT - Head',NULL,'10',6,1,'A','2024-01-31 18:12:58','2024-01-31 18:12:58'),(23,1001,'Sales & Marketing Executive',NULL,'3',6,1,'A','2024-02-19 19:39:30','2024-02-19 19:39:30'),(24,1001,'Ads Manager',NULL,NULL,6,1,'A','2024-02-20 20:23:22','2024-02-20 20:23:22'),(25,1001,'Voice & Accent Trainer','Training','3',6,1,'A','2024-02-26 17:52:36','2024-02-26 17:52:36'),(26,1001,'Sr HR Recruiter','Recruitment','2',6,1,'A','2024-02-26 17:53:21','2024-02-26 17:53:21'),(27,1001,'New Desig ed',NULL,NULL,6,1,'D','2024-07-26 15:25:47','2024-07-26 15:25:47');
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
  `active` int(11) NOT NULL DEFAULT 1 COMMENT '0 = Inactive\r\n1 = Active\r\n2 = RESIGNED\r\n3 = ABSCONDED\r\n4 = SERVING_NOTICE',
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active / Available\r\nD = Deleted',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_update_date` datetime NOT NULL DEFAULT current_timestamp(),
  `reporting_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_details`
--

LOCK TABLES `employee_details` WRITE;
/*!40000 ALTER TABLE `employee_details` DISABLE KEYS */;
INSERT INTO `employee_details` VALUES (2,1001,'Tax Admin','9561237890','tax.admin@email.com',NULL,NULL,NULL,NULL,7,'2023-04-05','10',1,4,'800145',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',3,3,NULL,1,'A','2024-01-03 12:16:58','2024-01-31 18:48:02',12),(11,1001,'Jyotirmoy Saha','8520741096','jsaha@email.com',NULL,NULL,NULL,NULL,20,'2023-07-28','2',1,4,'800184',2,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,NULL,1,'A','2024-01-30 18:24:14','2024-01-30 18:24:14',11),(39,1001,'New Auditor','7539512846','newauditor@email.com',NULL,NULL,NULL,NULL,0,'2024-07-09',NULL,1,4,'800185',0,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,NULL,1,'A','2024-07-09 13:24:16','2024-07-09 13:24:16',0),(40,1001,'New Auditor 2','7539513840','newauditor2@email.com',NULL,NULL,NULL,NULL,0,'2024-07-09',NULL,1,4,'800186',0,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,NULL,1,'A','2024-07-09 13:43:51','2024-07-09 13:43:51',0),(41,1001,'Demo','9258963210','demoaud@email.com',NULL,NULL,NULL,NULL,7,'2024-08-05',NULL,1,4,'800187',4,'0.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,1,'D','2024-08-05 10:39:24','2024-08-05 10:39:24',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_reporting_manager`
--

LOCK TABLES `employee_reporting_manager` WRITE;
/*!40000 ALTER TABLE `employee_reporting_manager` DISABLE KEYS */;
INSERT INTO `employee_reporting_manager` VALUES (2,1001,2,3,3,'2024-01-03',NULL,'A','2024-01-03 12:16:58'),(6,1001,6,6,6,'2024-01-12',NULL,'A','2024-01-12 19:03:34'),(7,1001,7,3,3,'2024-01-30',NULL,'A','2024-01-30 16:28:08'),(8,1001,8,6,6,'2024-01-30',NULL,'A','2024-01-30 16:46:45'),(9,1001,9,3,6,'2024-01-30',NULL,'A','2024-01-30 18:00:28'),(10,1001,10,3,6,'2024-01-30',NULL,'A','2024-01-30 18:14:00'),(11,1001,11,3,6,'2024-01-30',NULL,'A','2024-01-30 18:24:14'),(12,1001,12,3,6,'2024-01-30',NULL,'A','2024-01-30 18:31:30'),(13,1001,13,16,6,'2024-01-31',NULL,'A','2024-01-31 13:17:38'),(14,1001,14,16,6,'2024-01-31',NULL,'A','2024-01-31 13:40:38'),(15,1001,15,3,6,'2024-01-31',NULL,'A','2024-01-31 14:05:37'),(16,1001,16,19,6,'2024-01-31',NULL,'A','2024-01-31 17:33:48'),(17,1001,17,19,6,'2024-01-31',NULL,'A','2024-01-31 17:47:57'),(18,1001,18,19,6,'2024-01-31',NULL,'A','2024-01-31 17:58:53'),(19,1001,19,19,6,'2024-01-31',NULL,'A','2024-01-31 18:11:37'),(20,1001,20,3,6,'2024-01-31',NULL,'A','2024-01-31 18:28:46'),(21,1001,21,10,6,'2024-02-12',NULL,'A','2024-02-12 12:06:09'),(22,1001,22,10,6,'2024-02-12',NULL,'A','2024-02-12 14:07:56'),(23,1001,23,19,6,'2024-02-19',NULL,'A','2024-02-19 19:35:48'),(24,1001,24,3,6,'2024-02-19',NULL,'A','2024-02-19 19:47:37'),(25,1001,25,19,6,'2024-02-20',NULL,'A','2024-02-20 20:23:01'),(26,1001,26,3,6,'2024-02-20',NULL,'A','2024-02-20 20:27:04'),(27,1001,27,19,6,'2024-02-26',NULL,'A','2024-02-26 17:36:49'),(28,1001,28,19,6,'2024-02-26',NULL,'A','2024-02-26 17:46:12'),(29,1001,29,16,6,'2024-02-26',NULL,'A','2024-02-26 18:08:52'),(30,1001,30,6,6,'2024-02-27',NULL,'A','2024-02-27 17:49:47'),(31,1001,31,19,6,'2024-03-04',NULL,'A','2024-03-04 19:53:18'),(32,1001,32,19,6,'2024-03-04',NULL,'A','2024-03-04 20:01:39'),(33,1001,33,19,6,'2024-03-14',NULL,'A','2024-03-14 13:24:16'),(34,1001,34,19,6,'2024-03-14',NULL,'A','2024-03-14 13:33:23'),(35,1001,35,16,6,'2024-03-14',NULL,'A','2024-03-14 13:45:39'),(36,1001,36,16,6,'2024-03-15',NULL,'A','2024-03-15 15:11:36'),(37,1001,37,16,6,'2024-03-15',NULL,'A','2024-03-15 16:24:52'),(38,1001,38,16,6,'2024-03-15',NULL,'A','2024-03-15 16:46:19'),(39,1001,39,0,6,'2024-07-09',NULL,'A','2024-07-09 13:24:16'),(40,1001,40,0,6,'2024-07-09',NULL,'A','2024-07-09 13:43:51'),(41,1001,41,0,3,'2024-08-05',NULL,'A','2024-08-05 10:39:24');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_paper_data`
--

LOCK TABLES `position_paper_data` WRITE;
/*!40000 ALTER TABLE `position_paper_data` DISABLE KEYS */;
INSERT INTO `position_paper_data` VALUES (1,1001,10,0,43,'2024-07-16','2024-07-31',NULL,1,'2','2024-08-03','2024-07-16',0,'2024-07-16 12:50:55','2024-07-16 16:53:43'),(4,1001,1,1,15,'2024-07-19','2024-07-20',NULL,0,NULL,NULL,'2024-07-23',0,'2024-07-19 13:16:34','2024-07-23 11:05:41'),(5,1001,1,10,15,'2024-07-22','2024-07-25',NULL,0,NULL,NULL,NULL,1,'2024-07-22 12:24:38','2024-07-22 12:24:38'),(6,1001,1,11,15,'2024-07-23','2024-07-27',NULL,0,NULL,NULL,NULL,1,'2024-07-23 16:31:30','2024-07-23 16:31:30'),(7,1001,1,12,15,'2024-07-23','2024-07-27',NULL,0,NULL,NULL,NULL,1,'2024-07-23 17:39:25','2024-07-23 17:39:25');
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
INSERT INTO `position_paper_extention_dates` VALUES (1,1001,1,1,'2','2024-08-01','2024-08-03',1,'2024-07-16 15:41:56','2024-07-16 15:59:05');
/*!40000 ALTER TABLE `position_paper_extention_dates` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `query_data`
--

LOCK TABLES `query_data` WRITE;
/*!40000 ALTER TABLE `query_data` DISABLE KEYS */;
INSERT INTO `query_data` VALUES (1,1001,1,0,15,0,1,1,'QS01','10','2024-07-01','17','2024-07-18',0,NULL,NULL,'2024-07-09',1,'10','0',2,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-04 13:26:36','2024-07-09 15:40:37'),(4,1001,1,0,15,0,0,0,'QS02','14','2024-07-09','20','2024-07-29',0,NULL,NULL,'2024-07-09',1,'14','0',2,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-09 15:51:58','2024-07-09 18:45:29'),(5,1001,1,0,15,0,0,0,'QS03','12','2024-07-12','14','2024-07-26',1,'2','2024-07-29','2024-07-15',1,'12','0',2,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-12 13:23:21','2024-07-15 16:17:03'),(6,1001,1,0,15,0,0,0,'QS04','10','2024-07-15','12','2024-07-27',1,'5','2024-08-02','2024-07-23',4,'7','3',1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-15 16:18:15','2024-07-23 16:27:15'),(7,1001,10,0,43,3,0,0,'Q12','12','2024-07-16','9','2024-07-25',0,NULL,NULL,'2024-07-16',1,'12','0',2,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-16 10:39:32','2024-07-16 10:40:37'),(8,1001,10,0,43,0,0,0,'Q13','10','2024-07-16','10','2024-07-26',1,'2','2024-07-29',NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-16 10:41:11','2024-07-16 10:41:11'),(9,1001,14,0,15,0,1,2,'QS17','13','2024-07-17','1','2024-07-18',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-18 16:38:18','2024-07-18 16:38:18'),(10,1001,1,0,15,1,1,2,'Q19','13','2024-07-12','6','2024-07-18',0,NULL,NULL,NULL,0,NULL,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-19 17:06:14','2024-07-22 11:40:23'),(11,1001,1,0,15,0,1,1,'Q25','10','2024-07-16','2','2024-07-18',0,NULL,NULL,NULL,0,NULL,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-23 16:30:08','2024-07-23 16:30:44'),(12,1001,1,0,15,0,1,1,'Q78','10','2024-07-10','12','2024-07-22',0,NULL,NULL,NULL,0,NULL,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-23 16:55:14','2024-07-23 17:33:55'),(13,1001,1,0,15,0,1,1,'Q064','10','2024-07-01','5','2024-07-06',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-25 14:15:41','2024-07-25 14:15:41'),(14,1001,1,0,15,0,1,1,'006','5','2024-07-01','5','2024-07-06',0,NULL,NULL,'2024-07-31',4,'2','3',1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-25 14:32:44','2024-07-31 16:10:40'),(15,1001,1,0,15,2,1,2,'Q069','12','2024-07-12','7','2024-07-19',1,'12','2024-08-01','2024-07-31',4,'6','6',1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-31 15:25:17','2024-07-31 15:51:02'),(16,1001,1,0,15,0,1,4,'Q70','10','2024-07-22','11','2024-08-02',0,NULL,NULL,'2024-07-31',4,'3','7',1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-07-31 15:55:24','2024-07-31 16:13:06'),(17,1001,13,0,15,0,1,1,'Q902','8','2024-08-01','5','2024-08-06',0,NULL,NULL,NULL,0,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,0,'2024-08-05 13:13:11','2024-08-05 13:13:11');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `query_extension_dates`
--

LOCK TABLES `query_extension_dates` WRITE;
/*!40000 ALTER TABLE `query_extension_dates` DISABLE KEYS */;
INSERT INTO `query_extension_dates` VALUES (1,1001,5,1,'2','2024-07-27','2024-07-29',1,'2024-07-12 14:16:47','2024-07-12 18:03:33'),(2,1001,6,1,'5','2024-07-28','2024-08-02',1,'2024-07-15 16:23:04','2024-07-15 16:25:02'),(3,1001,8,1,'2','2024-07-27','2024-07-29',1,'2024-07-16 10:41:30','2024-07-16 10:42:10'),(4,1001,15,1,'12','2024-07-20','2024-08-01',1,'2024-07-31 15:51:25','2024-07-31 15:51:42');
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `query_reply`
--

LOCK TABLES `query_reply` WRITE;
/*!40000 ALTER TABLE `query_reply` DISABLE KEYS */;
INSERT INTO `query_reply` VALUES (1,1001,1,'2024-07-09','3','A','2024-07-09 15:37:09'),(2,1001,1,'2024-07-09','2','A','2024-07-09 15:39:54'),(3,1001,1,'2024-07-09','5','A','2024-07-09 15:40:37'),(4,1001,4,'2024-07-09','4','A','2024-07-09 18:42:40'),(5,1001,4,'2024-07-09','6','A','2024-07-09 18:43:49'),(6,1001,4,'2024-07-09','4','A','2024-07-09 18:45:29'),(7,1001,5,'2024-07-15','6','A','2024-07-15 10:46:33'),(8,1001,5,'2024-07-15','6','A','2024-07-15 16:17:03'),(9,1001,6,'2024-07-15','2','A','2024-07-15 16:19:52'),(10,1001,7,'2024-07-16','5','A','2024-07-16 10:39:59'),(11,1001,7,'2024-07-16','7','A','2024-07-16 10:40:37'),(12,1001,6,'2024-07-23','5','A','2024-07-23 16:27:15'),(13,1001,15,'2024-07-31','6','A','2024-07-31 15:51:02'),(14,1001,14,'2024-07-31','2','A','2024-07-31 16:10:40'),(15,1001,16,'2024-07-31','3','A','2024-07-31 16:13:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_collection_data`
--

LOCK TABLES `tax_collection_data` WRITE;
/*!40000 ALTER TABLE `tax_collection_data` DISABLE KEYS */;
INSERT INTO `tax_collection_data` VALUES (1,1001,1,1,52000,52000,0,'2024-07-24',48000,22000,26000,'2024-07-24',3,'2024-07-24 13:22:41','2024-07-24 13:25:39');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_payment_history`
--

LOCK TABLES `tax_payment_history` WRITE;
/*!40000 ALTER TABLE `tax_payment_history` DISABLE KEYS */;
INSERT INTO `tax_payment_history` VALUES (1,1001,1,1,11000,'2024-07-24',2,'A','2024-07-24 13:22:41','2024-07-24 13:22:41'),(2,1001,1,1,11000,'2024-07-24',2,'A','2024-07-24 13:24:00','2024-07-24 13:24:00'),(3,1001,1,1,8000,'2024-07-24',1,'A','2024-07-24 13:24:19','2024-07-24 13:24:19'),(4,1001,1,1,12000,'2024-07-24',1,'A','2024-07-24 13:25:07','2024-07-24 13:25:07'),(5,1001,1,1,32000,'2024-07-24',1,'A','2024-07-24 13:25:39','2024-07-24 13:25:39');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_of_tax`
--

LOCK TABLES `type_of_tax` WRITE;
/*!40000 ALTER TABLE `type_of_tax` DISABLE KEYS */;
INSERT INTO `type_of_tax` VALUES (1,1001,'CIT',15,'A','2024-07-02 15:34:26','2024-07-02 15:34:26'),(2,1001,'SWT',15,'A','2024-07-02 15:34:26','2024-07-02 15:34:26'),(3,1001,'DWT',15,'A','2024-07-02 15:35:08','2024-07-02 15:35:08'),(4,1001,'IWT',15,'A','2024-07-02 15:35:08','2024-07-02 15:35:08'),(5,1001,'edited tax',6,'D','2024-07-26 11:26:14','2024-07-26 12:22:01'),(6,1001,'test tax type',6,'A','2024-07-26 13:08:24','2024-07-26 13:08:24'),(7,1001,'New tax type',6,'D','2024-07-26 13:09:52','2024-07-26 13:09:52'),(8,1001,'new tax edited',6,'A','2024-07-26 13:14:27','2024-07-26 13:14:40');
/*!40000 ALTER TABLE `type_of_tax` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1001,0,6,'demo admin','jsaha.demo@gmail.com','9123456789','Admin.1234','9cdca6289d90c1b87395bfcb2a07e1b407710d11141d6c5080fbdfba5360cdff',0,1,'A','2022-04-20 11:09:54',NULL),(3,1001,1001,7,'Super Admin','superadmin@email.com','1234567890','Sadmin.1234','ad2d410ec36b60990d53022a0383713d404e3c2b8d1b604c25226541bc74c390',0,1,'A','2023-12-28 11:03:54',NULL),(6,1001,2,6,'Tax Admin','tax.admin@email.com','9561237890','Taxadmin.1234','d48ed964359f3d5c5db9a93f8c08dded7480f1336e5ae5a3469d17e5467a1f98',0,1,'A','2024-01-03 12:16:58',NULL),(15,1001,11,4,'Jyotirmoy Saha','jsaha@email.com','8520741096','Joy.1234','196650546410c8620f28703ca0cc63e842319fdaaa85945ac349591b8d2e0c14',0,1,'A','2024-01-30 18:24:14',NULL),(43,1001,39,4,'New Auditor','newauditor@email.com','7539512846','New.1234','f2faf162666d07d462e267365eccdb361aecb16b888656ae9499878af6d32470',0,1,'A','2024-07-09 13:24:16',NULL),(44,1001,40,4,'New Auditor 2','newauditor2@email.com','7539513840','New2.1234','e89cbf93c61e0d682e4d99dba24c74603e00f62c62325e38f7b762cb4f1d4b2d',0,1,'A','2024-07-09 13:43:51',NULL),(45,1001,41,4,'Demo','demoaud@email.com','9258963210','DemoAud@1234','19e6a25570cd57ba4cf1d5884e7f672ce36d52e9214ce803cdce4bfbf7d7cf68',0,1,'A','2024-08-05 10:39:24',NULL);
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

-- Dump completed on 2024-08-21  9:07:18
