-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 10, 2024 at 01:04 PM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adzguzzg_tax_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audits_data`
--

CREATE TABLE `audits_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `audit_expected_complete_date` date DEFAULT NULL,
  `audit_start_date` date DEFAULT NULL,
  `audit_end_date` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '1 = Active / Open\r\n2 = Closed',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audits_data`
--

INSERT INTO `audits_data` (`id`, `client_id`, `company_id`, `user_id`, `audit_expected_complete_date`, `audit_start_date`, `audit_end_date`, `active`, `status`, `created_at`, `updated_at`) VALUES
(1, 1001, 4, 49, NULL, '2024-08-08', NULL, 1, 'A', '2024-08-08 09:08:33', '2024-08-08 09:08:33'),
(2, 1001, 1, 45, NULL, '2024-08-08', '2024-08-30', 2, 'A', '2024-08-08 09:41:18', '2024-08-08 09:41:18'),
(3, 1001, 2, 47, NULL, '2024-08-09', NULL, 1, 'A', '2024-08-09 06:23:07', '2024-08-09 06:23:07'),
(4, 1001, 10, 45, '2024-09-08', '2024-08-30', NULL, 1, 'A', '2024-08-30 21:32:25', '2024-08-30 21:32:25');

-- --------------------------------------------------------

--
-- Table structure for table `audit_assessment_data`
--

CREATE TABLE `audit_assessment_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `query_id` int(11) NOT NULL DEFAULT '0',
  `position_paper_id` int(11) NOT NULL DEFAULT '0',
  `claimable_tax_amount` double DEFAULT '0',
  `penalty_amount` double NOT NULL DEFAULT '0',
  `omitted_income_amount` double NOT NULL DEFAULT '0',
  `date_of_issue` date DEFAULT NULL,
  `date_of_closure` date DEFAULT NULL,
  `ref_no` varchar(50) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '1 = Collection stage\r\n2 = Objection stage',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit_assessment_data`
--

INSERT INTO `audit_assessment_data` (`id`, `client_id`, `company_id`, `user_id`, `query_id`, `position_paper_id`, `claimable_tax_amount`, `penalty_amount`, `omitted_income_amount`, `date_of_issue`, `date_of_closure`, `ref_no`, `active`, `created_at`, `updated_at`) VALUES
(1, 1001, 1, 45, 0, 1, 5678678, 45467, 45678904567, '2024-08-23', '2024-08-26', 'yudf45678', 1, '2024-08-23 11:21:35', '2024-08-26 14:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `audit_close_request_data`
--

CREATE TABLE `audit_close_request_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `audit_id` int(11) NOT NULL,
  `auditor_id` int(11) NOT NULL,
  `approved_by` int(11) NOT NULL DEFAULT '0' COMMENT 'User ID',
  `approval_status` int(11) NOT NULL DEFAULT '0' COMMENT '0 = Not Approved\r\n1 = Approved 2 = Rejected',
  `request_date` datetime DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `reason` varchar(300) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '0 = Inactive / Closed\r\n1 = Active / Open',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `audit_memo_data`
--

CREATE TABLE `audit_memo_data` (
  `id` int(11) NOT NULL,
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
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit_memo_data`
--

INSERT INTO `audit_memo_data` (`id`, `client_id`, `company_id`, `secondary_auditor_id`, `primary_auditor_id`, `memo_no`, `total_no_of_query`, `date_of_issue`, `days_to_reply`, `last_date_of_reply`, `status`, `created_at`, `updated_at`) VALUES
(1, 1001, 1, 46, 45, 'M01', '10', '2024-05-21', '14', '2024-06-04', 'A', '2024-08-08 09:48:55', '2024-08-08 09:48:55'),
(2, 1001, 1, 46, 45, 'M02', '7', '2024-05-27', '7', '2024-06-03', 'A', '2024-08-08 09:49:45', '2024-08-08 09:49:45'),
(3, 1001, 7, 45, 48, 'M01', '10', '2024-08-21', '2', '2024-08-22', 'A', '2024-08-20 16:20:22', '2024-08-20 16:20:22'),
(4, 1001, 10, 46, 45, '001', '5', '2024-08-30', '2', '2024-08-31', 'A', '2024-08-30 21:37:00', '2024-08-30 21:37:00'),
(5, 1001, 10, 46, 45, '002', '6', '2024-09-08', '2', '2024-09-09', 'A', '2024-08-30 21:39:37', '2024-08-30 21:39:37'),
(6, 1001, 10, 47, 45, '001', '5', '2024-08-30', '2', '2024-08-31', 'A', '2024-08-30 21:49:13', '2024-08-30 21:49:13'),
(7, 1001, 10, 47, 45, '002', '6', '2024-08-30', '2', '2024-08-31', 'A', '2024-08-30 21:49:30', '2024-08-30 21:49:30');

-- --------------------------------------------------------

--
-- Table structure for table `audit_tax_type_history`
--

CREATE TABLE `audit_tax_type_history` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `audit_type_id` int(11) NOT NULL DEFAULT '0',
  `type_of_tax_id` varchar(100) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '0 = Inactive, 1 = Active',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit_tax_type_history`
--

INSERT INTO `audit_tax_type_history` (`id`, `client_id`, `audit_type_id`, `type_of_tax_id`, `company_id`, `start_date`, `end_date`, `active`, `status`, `created_at`, `updated_at`) VALUES
(1, 1001, 1, '1,2', 1, '2024-08-08', '2024-08-09', 0, 'A', '2024-08-08 05:47:35', '2024-08-09 11:03:58'),
(2, 1001, 1, '1', 2, '2024-08-08', NULL, 1, 'A', '2024-08-08 05:50:10', '2024-08-08 05:50:10'),
(3, 1001, 1, '1', 3, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 05:51:35', '2024-08-08 07:10:59'),
(4, 1001, 1, '1', 4, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:01:57', '2024-08-08 07:11:14'),
(5, 1001, 1, '1', 5, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:10:28', '2024-08-08 07:11:51'),
(6, 1001, 1, '1', 3, '2024-08-08', NULL, 1, 'A', '2024-08-08 07:10:59', '2024-08-08 07:10:59'),
(7, 1001, 1, '1', 4, '2024-08-08', NULL, 1, 'A', '2024-08-08 07:11:14', '2024-08-08 07:11:14'),
(8, 1001, 1, '1', 5, '2024-08-08', NULL, 1, 'A', '2024-08-08 07:11:51', '2024-08-08 07:11:51'),
(9, 1001, 1, '1', 6, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:15:20', '2024-08-08 07:16:52'),
(10, 1001, 1, '1,5', 6, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:16:52', '2024-08-08 07:17:08'),
(11, 1001, 1, '1', 6, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:17:08', '2024-08-08 07:23:14'),
(12, 1001, 1, '1,5', 6, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:23:14', '2024-08-08 07:37:43'),
(13, 1001, 1, '1,5', 7, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:34:24', '2024-08-08 07:35:49'),
(14, 1001, 1, '1,5', 7, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:35:49', '2024-08-08 07:38:39'),
(15, 1001, 1, '1,5', 6, '2024-08-08', NULL, 1, 'A', '2024-08-08 07:37:43', '2024-08-08 07:37:43'),
(16, 1001, 1, '1,5', 7, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:38:39', '2024-08-08 07:38:57'),
(17, 1001, 1, '1,5', 7, '2024-08-08', NULL, 1, 'A', '2024-08-08 07:38:57', '2024-08-08 07:38:57'),
(18, 1001, 1, '1', 8, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 07:41:17', '2024-08-08 07:48:35'),
(19, 1001, 1, '1', 8, '2024-08-08', NULL, 1, 'A', '2024-08-08 07:48:35', '2024-08-08 07:48:35'),
(20, 1001, 1, '1,2', 9, '2024-08-08', NULL, 1, 'A', '2024-08-08 08:12:27', '2024-08-08 08:12:27'),
(21, 1001, 1, '1', 10, '2024-08-08', '2024-08-08', 0, 'A', '2024-08-08 08:23:29', '2024-08-08 08:23:51'),
(22, 1001, 1, '1', 10, '2024-08-08', '2024-08-09', 0, 'A', '2024-08-08 08:23:51', '2024-08-09 11:05:09'),
(23, 1001, 1, '1,2,4', 1, '2024-08-09', '2024-08-09', 0, 'A', '2024-08-09 11:03:58', '2024-08-09 11:04:40'),
(24, 1001, 1, '1,2,4', 1, '2024-08-09', NULL, 1, 'A', '2024-08-09 11:04:40', '2024-08-09 11:04:40'),
(25, 1001, 1, '1', 10, '2024-08-09', '2024-08-09', 0, 'A', '2024-08-09 11:05:09', '2024-08-09 11:05:34'),
(26, 1001, 1, '1', 10, '2024-08-09', '2024-08-12', 0, 'A', '2024-08-09 11:05:34', '2024-08-12 06:59:39'),
(27, 1001, 1, '1', 10, '2024-08-12', '2024-08-12', 0, 'A', '2024-08-12 06:59:39', '2024-08-12 07:00:12'),
(28, 1001, 1, '1', 10, '2024-08-12', '2024-08-12', 0, 'A', '2024-08-12 07:00:12', '2024-08-12 07:00:39'),
(29, 1001, 1, '1', 10, '2024-08-12', '2024-08-12', 0, 'A', '2024-08-12 07:00:39', '2024-08-12 07:01:11'),
(30, 1001, 1, '1', 10, '2024-08-12', '2024-08-12', 0, 'A', '2024-08-12 07:01:11', '2024-08-12 07:02:09'),
(31, 1001, 1, '1', 10, '2024-08-12', NULL, 1, 'A', '2024-08-12 07:02:09', '2024-08-12 07:02:09');

-- --------------------------------------------------------

--
-- Table structure for table `audit_time_spent_data`
--

CREATE TABLE `audit_time_spent_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `audit_id` int(11) NOT NULL,
  `auditor_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time_in_hrs` int(11) NOT NULL DEFAULT '0',
  `leave_hrs` double NOT NULL DEFAULT '0',
  `training_hrs` double NOT NULL DEFAULT '0',
  `other_duty_hrs` double NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit_time_spent_data`
--

INSERT INTO `audit_time_spent_data` (`id`, `client_id`, `company_id`, `audit_id`, `auditor_id`, `date`, `time_in_hrs`, `leave_hrs`, `training_hrs`, `other_duty_hrs`, `created_at`, `updated_at`) VALUES
(1, 1001, 1, 2, 45, '2024-08-13', 2, 0, 0, 0, '2024-08-13 09:50:25', '2024-08-13 09:50:25'),
(2, 1001, 1, 2, 45, '2024-08-23', 8, 0, 0, 0, '2024-08-23 10:42:36', '2024-08-23 10:42:36'),
(3, 1001, 0, 0, 45, '2024-08-22', 0, 0, 9, 0, '2024-08-23 11:32:10', '2024-08-23 11:32:10');

-- --------------------------------------------------------

--
-- Table structure for table `audit_types`
--

CREATE TABLE `audit_types` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `audit_type` varchar(200) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit_types`
--

INSERT INTO `audit_types` (`id`, `client_id`, `audit_type`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1001, 'Comprehensive audit', 15, 'A', '2024-07-02 15:36:07', '2024-07-02 15:36:07'),
(2, 1001, 'Single issue audit', 15, 'A', '2024-07-02 15:36:07', '2024-07-02 15:36:07'),
(3, 1001, 'Extractive Industries and Complex Audit', 15, 'A', '2024-07-02 15:36:51', '2024-07-02 15:36:51'),
(4, 1001, 'Desk audits', 15, 'A', '2024-07-02 15:36:51', '2024-07-02 15:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(11) NOT NULL,
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
  `rental_type` int(11) NOT NULL DEFAULT '0' COMMENT '0 - Monthly , 1 - Yearly',
  `rental_charge` double NOT NULL,
  `paid_amount` double NOT NULL,
  `due_amount` double NOT NULL DEFAULT '0',
  `payment_status` int(11) NOT NULL DEFAULT '0' COMMENT '0 - Due, 1 - Paid',
  `sms_enabled` int(11) NOT NULL DEFAULT '0' COMMENT '1 - Enable, 0 - Disable',
  `google_api_enable` int(11) NOT NULL DEFAULT '0' COMMENT '1 - Enable, 0 - Disable',
  `whatsapp_integration_enable` int(11) NOT NULL DEFAULT '0' COMMENT '1 - Enable, 0 - Disable',
  `live_location_tracking_enable` int(11) NOT NULL DEFAULT '0' COMMENT '1 - Enable, 0 - Disable',
  `multi_language_support_enable` int(11) NOT NULL DEFAULT '0' COMMENT '1 - Enable, 0 - Disable',
  `sms_package_code` varchar(50) DEFAULT NULL,
  `registration_charge` double NOT NULL DEFAULT '0',
  `sms_recharge_date` date DEFAULT NULL,
  `sms_validity_period` int(11) NOT NULL DEFAULT '0' COMMENT 'In Month',
  `sms_gateway_type` int(11) NOT NULL DEFAULT '1' COMMENT '1 - Saha CyberTech SMS API, 2 - Personal SMS api',
  `sms_gateway` varchar(10) DEFAULT NULL,
  `sms_endpoint` varchar(250) DEFAULT NULL,
  `sms_sid` varchar(10) DEFAULT NULL,
  `send_auto_sms` int(11) NOT NULL DEFAULT '0' COMMENT '0 - Manual, 1 - Automatic',
  `total_sms` int(11) NOT NULL DEFAULT '0',
  `sms_sent` int(11) NOT NULL DEFAULT '0',
  `sms_balance` int(11) NOT NULL DEFAULT '0',
  `sms_sid_enable` int(11) NOT NULL DEFAULT '0' COMMENT '0 - Disable, 1 - Enable',
  `max_product` int(11) NOT NULL DEFAULT '0',
  `max_user` int(11) NOT NULL DEFAULT '0',
  `max_manager` int(11) NOT NULL DEFAULT '0',
  `max_category` int(11) NOT NULL DEFAULT '0',
  `max_banner_content` int(11) NOT NULL,
  `max_special_menu` int(11) NOT NULL DEFAULT '4',
  `product_added` int(11) NOT NULL DEFAULT '0',
  `user_added` int(11) NOT NULL DEFAULT '0' COMMENT 'General Employee',
  `manager_added` int(11) NOT NULL DEFAULT '0',
  `category_added` int(11) NOT NULL DEFAULT '0',
  `feature_plan` int(11) NOT NULL COMMENT '7 - Basic, 2 - Standard, 1 - Premium, 3 - Custom',
  `project_service_type` int(11) NOT NULL COMMENT '1 - Grocery, 2 - Texttile, 3 - Home Appliances, 4 - All',
  `application_server` int(11) DEFAULT '1' COMMENT '1 - Saha CyberTech Server, 2 - Company Own server, 3 - 3rdf Party server',
  `mac_id` varchar(20) DEFAULT NULL COMMENT 'MAC ID of the server',
  `ip` varchar(15) DEFAULT NULL COMMENT 'ip of the server',
  `site_url` varchar(200) DEFAULT NULL,
  `trade_license` varchar(100) DEFAULT NULL COMMENT 'Document',
  `gstin_certificate` varchar(100) DEFAULT NULL COMMENT 'Document',
  `pan_card` varchar(100) DEFAULT NULL COMMENT 'Document',
  `company_director_list` varchar(100) DEFAULT NULL COMMENT 'Document',
  `company_master_data` varchar(100) DEFAULT NULL COMMENT 'Document',
  `company_type` int(11) NOT NULL DEFAULT '1' COMMENT '1 - PVT, 2 - LTD, 3 - LLP, 4 - Partnership, 5 - Propreitary, 6 - Others',
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
  `active` int(11) NOT NULL DEFAULT '1',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `user_id`, `name`, `mobile`, `email`, `address`, `city`, `district`, `state`, `pin_code`, `website_name`, `company_name`, `company_logo`, `company_email`, `company_email_password`, `company_mobile`, `company_phone`, `company_address`, `company_city`, `company_district`, `company_state`, `company_pincode`, `gstin_no`, `tan`, `pan`, `joined_date`, `validity_period`, `expiry_date`, `total_charge`, `rental_type`, `rental_charge`, `paid_amount`, `due_amount`, `payment_status`, `sms_enabled`, `google_api_enable`, `whatsapp_integration_enable`, `live_location_tracking_enable`, `multi_language_support_enable`, `sms_package_code`, `registration_charge`, `sms_recharge_date`, `sms_validity_period`, `sms_gateway_type`, `sms_gateway`, `sms_endpoint`, `sms_sid`, `send_auto_sms`, `total_sms`, `sms_sent`, `sms_balance`, `sms_sid_enable`, `max_product`, `max_user`, `max_manager`, `max_category`, `max_banner_content`, `max_special_menu`, `product_added`, `user_added`, `manager_added`, `category_added`, `feature_plan`, `project_service_type`, `application_server`, `mac_id`, `ip`, `site_url`, `trade_license`, `gstin_certificate`, `pan_card`, `company_director_list`, `company_master_data`, `company_type`, `cin_document`, `moa_aoa`, `partnership_deed`, `company_photograph_1`, `company_photograph_2`, `company_photograph_3`, `corporate_mail_id`, `account_number`, `account_holder_name`, `bank_name`, `ifsc_code`, `branch_address`, `cancelled_cheque`, `active`, `status`, `creation_date`) VALUES
(1001, NULL, 'IRC', '9123456789', 'info@adzguru.co', NULL, NULL, NULL, NULL, NULL, NULL, 'Revenue and Audit Insight Tracking Enabled System (RAITES)', 'logo.png', 'info@adzguru.co', '', '9123456789', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-01', 12, '2025-06-30', 50000, 1, 4000, 6000, 44000, 0, 1, 1, 1, 1, 1, NULL, 15000, '2022-01-01', 12, 1, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 0, 0, 0, 0, 1, 4, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'A', '2024-07-24 05:28:22');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `industry_type_id` int(11) NOT NULL DEFAULT '0',
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
  `active` int(11) NOT NULL DEFAULT '1',
  `active_inactive_date` date DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `client_id`, `company_name`, `industry_type_id`, `company_code`, `street_number`, `street_name`, `city_or_suburb`, `state`, `postcode`, `country`, `phone`, `fax`, `tax_identification_number`, `business_registration_number`, `case_code`, `active`, `active_inactive_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1001, 'Company A', 2, 'Other LT Audit #1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-1', NULL, '123456789-1', 1, NULL, 'A', '2024-08-08 05:47:35', '2024-08-09 11:04:40'),
(2, 1001, 'Company B', 1, 'Construction Audit #1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-2', NULL, '123456789-2', 1, NULL, 'A', '2024-08-08 05:50:10', '2024-08-08 05:50:10'),
(3, 1001, 'Company C', 2, 'Other LT Audit #2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-3', NULL, '123456789-3', 1, NULL, 'A', '2024-08-08 05:51:35', '2024-08-08 07:10:59'),
(4, 1001, 'Company D', 2, 'Other LT Audit #3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-4', NULL, '123456789-4', 1, NULL, 'A', '2024-08-08 07:01:57', '2024-08-08 07:11:14'),
(5, 1001, 'Company E', 2, 'Other LT Audit #4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-5', NULL, '123456789-5', 1, NULL, 'A', '2024-08-08 07:10:28', '2024-08-08 07:11:51'),
(6, 1001, 'Company F', 2, 'Other LT Audit #5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-6', NULL, '123456789-6', 1, NULL, 'A', '2024-08-08 07:15:20', '2024-08-08 07:37:43'),
(7, 1001, 'Company G', 2, 'Other LT Audit #6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-7', NULL, '123456789-7', 1, NULL, 'A', '2024-08-08 07:34:24', '2024-08-08 07:38:57'),
(8, 1001, 'Company H', 8, 'Other LT Audit #7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-8', NULL, '123456789-8', 1, NULL, 'A', '2024-08-08 07:41:17', '2024-08-08 07:48:35'),
(9, 1001, 'Company I', 2, 'Other LT Audit #8', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-9', NULL, '123456789-9', 1, NULL, 'A', '2024-08-08 08:12:27', '2024-08-08 08:12:27'),
(10, 1001, 'Company J', 2, 'Other LT Audit #8', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789-10', NULL, '123456789-10', 1, NULL, 'A', '2024-08-08 08:23:29', '2024-08-12 07:02:09');

-- --------------------------------------------------------

--
-- Table structure for table `company_assigned_data`
--

CREATE TABLE `company_assigned_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `auditor_id` int(11) NOT NULL,
  `company_ids` int(11) DEFAULT '0',
  `primary_secondary` int(11) NOT NULL DEFAULT '0' COMMENT '1 = Primary, 2 = Secondary',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `company_assigned_data`
--

INSERT INTO `company_assigned_data` (`id`, `client_id`, `user_id`, `auditor_id`, `company_ids`, `primary_secondary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1001, 3, 45, 1, 1, 'A', '2024-08-08 08:43:44', '2024-08-08 08:43:44'),
(2, 1001, 3, 46, 1, 2, 'A', '2024-08-08 08:43:44', '2024-08-08 08:43:44'),
(3, 1001, 3, 47, 2, 1, 'A', '2024-08-08 08:45:52', '2024-08-08 08:45:52'),
(4, 1001, 3, 46, 2, 2, 'A', '2024-08-08 08:45:52', '2024-08-08 08:45:52'),
(5, 1001, 3, 48, 2, 2, 'A', '2024-08-08 08:45:52', '2024-08-08 08:45:52'),
(6, 1001, 3, 48, 3, 1, 'A', '2024-08-08 08:46:44', '2024-08-08 08:46:44'),
(7, 1001, 3, 46, 3, 2, 'A', '2024-08-08 08:46:44', '2024-08-08 08:46:44'),
(8, 1001, 3, 47, 3, 2, 'A', '2024-08-08 08:46:44', '2024-08-08 08:46:44'),
(9, 1001, 3, 49, 4, 1, 'A', '2024-08-08 08:48:19', '2024-08-08 08:48:19'),
(10, 1001, 3, 47, 5, 1, 'A', '2024-08-08 08:48:55', '2024-08-08 08:50:47'),
(13, 1001, 3, 46, 5, 2, 'A', '2024-08-08 08:50:47', '2024-08-08 08:50:47'),
(14, 1001, 3, 49, 5, 2, 'A', '2024-08-08 08:50:47', '2024-08-08 08:50:47'),
(15, 1001, 3, 46, 6, 1, 'A', '2024-08-08 08:51:27', '2024-08-08 08:51:27'),
(16, 1001, 3, 48, 6, 2, 'A', '2024-08-08 08:51:27', '2024-08-08 08:51:27'),
(17, 1001, 3, 49, 6, 2, 'A', '2024-08-08 08:51:27', '2024-08-08 08:51:27'),
(18, 1001, 3, 48, 7, 1, 'A', '2024-08-08 08:56:07', '2024-08-08 08:56:07'),
(19, 1001, 3, 45, 7, 2, 'A', '2024-08-08 08:56:07', '2024-08-08 08:56:07'),
(20, 1001, 3, 47, 7, 2, 'A', '2024-08-08 08:56:07', '2024-08-08 08:56:07'),
(21, 1001, 3, 49, 8, 1, 'A', '2024-08-08 08:56:43', '2024-08-08 08:56:43'),
(22, 1001, 3, 46, 8, 2, 'A', '2024-08-08 08:56:43', '2024-08-08 08:56:43'),
(23, 1001, 3, 47, 8, 2, 'A', '2024-08-08 08:56:43', '2024-08-08 08:56:43'),
(24, 1001, 3, 46, 9, 1, 'A', '2024-08-08 09:01:23', '2024-08-13 09:29:34'),
(27, 1001, 3, 45, 10, 1, 'A', '2024-08-13 09:28:11', '2024-08-13 09:28:11'),
(28, 1001, 3, 46, 10, 2, 'A', '2024-08-13 09:28:11', '2024-08-13 09:28:11'),
(29, 1001, 3, 47, 10, 2, 'A', '2024-08-13 09:28:11', '2024-08-13 09:28:11'),
(30, 1001, 3, 47, 9, 2, 'A', '2024-08-13 09:29:34', '2024-08-13 09:29:34'),
(31, 1001, 3, 48, 9, 2, 'A', '2024-08-13 09:29:34', '2024-08-13 09:29:34');

-- --------------------------------------------------------

--
-- Table structure for table `company_industry_type`
--

CREATE TABLE `company_industry_type` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `industry_type` varchar(250) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `company_industry_type`
--

INSERT INTO `company_industry_type` (`id`, `client_id`, `industry_type`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1001, 'Construction Audit', 6, 'A', '2024-07-05 12:23:13', '2024-07-05 12:23:13'),
(2, 1001, 'Other LT Audit', 6, 'A', '2024-07-05 12:25:23', '2024-07-05 12:25:23'),
(3, 1001, 'some industry', 6, 'D', '2024-07-05 19:18:33', '2024-07-05 19:18:33'),
(4, 1001, 'tesat', 6, 'D', '2024-07-08 18:21:17', '2024-07-08 18:21:17'),
(5, 1001, 'New industry', 6, 'D', '2024-07-16 10:49:24', '2024-07-16 10:49:24'),
(6, 1001, 'demo industry', 3, 'D', '2024-07-25 11:25:08', '2024-07-25 11:25:08'),
(7, 1001, 'Extractives Audit (Mining)', 3, 'A', '2024-08-08 05:17:40', '2024-08-08 05:17:40'),
(8, 1001, 'Primary Production & Export', 3, 'A', '2024-08-08 05:17:52', '2024-08-08 05:17:52'),
(9, 1001, 'Finance & Banking Audit', 3, 'A', '2024-08-08 05:18:03', '2024-08-08 05:18:03'),
(10, 1001, 'Government & SOE Audit', 3, 'A', '2024-08-08 05:18:16', '2024-08-08 05:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `company_notice_data`
--

CREATE TABLE `company_notice_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `query_ids` varchar(200) DEFAULT NULL,
  `notice_no` varchar(100) DEFAULT NULL,
  `notice_section` varchar(100) DEFAULT NULL,
  `date_of_notice_issue` date DEFAULT NULL,
  `days_to_reply_notice` varchar(20) DEFAULT NULL,
  `last_date_of_reply` date DEFAULT NULL,
  `date_of_reply_notice` date DEFAULT NULL,
  `notice_status` int(11) NOT NULL DEFAULT '2' COMMENT '0 = No Input 1 = Submitted 2 = Pending 3 = Overdue',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `company_notice_data`
--

INSERT INTO `company_notice_data` (`id`, `client_id`, `company_id`, `query_ids`, `notice_no`, `notice_section`, `date_of_notice_issue`, `days_to_reply_notice`, `last_date_of_reply`, `date_of_reply_notice`, `notice_status`, `created_at`, `updated_at`) VALUES
(1, 1001, 1, '7,8,12', 'N001', NULL, '2024-08-10', '2', '2024-08-11', '2024-08-12', 1, '2024-08-13 09:36:09', '2024-08-13 09:37:07'),
(2, 1001, 1, '13', 'erw001', NULL, '2024-08-23', '4', '2024-08-26', NULL, 2, '2024-08-23 11:15:37', '2024-08-23 11:15:37'),
(3, 1001, 10, '15', '001', NULL, '2024-08-30', '2', '2024-08-31', NULL, 2, '2024-08-30 22:06:01', '2024-08-30 22:06:01'),
(4, 1001, 10, '17', '001', NULL, '2024-08-30', '2', '2024-08-31', NULL, 2, '2024-08-30 22:07:55', '2024-08-30 22:07:55'),
(5, 1001, 10, '18', '003', NULL, '2024-09-05', '1', '2024-09-05', NULL, 2, '2024-09-05 21:55:36', '2024-09-05 21:55:36');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `added_by` int(11) NOT NULL DEFAULT '0' COMMENT 'Current User ID',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `client_id`, `department_name`, `added_by`, `status`, `creation_date`) VALUES
(1, 1001, 'Sales', 3, 'D', '2024-01-02 17:48:57'),
(2, 1001, 'Back-End', 3, 'D', '2024-01-02 17:49:10'),
(3, 1001, 'HR', 3, 'D', '2024-01-02 17:49:17'),
(4, 1001, 'IT ', 6, 'D', '2024-01-12 18:38:09'),
(5, 1001, 'Domestic Sales', 6, 'D', '2024-01-12 18:38:26'),
(6, 1001, 'International Sales', 6, 'D', '2024-01-12 18:38:41'),
(7, 1001, 'E-commerce', 6, 'D', '2024-02-19 19:38:55'),
(8, 1001, 'Other Large Taxpayer Tax Audits, Refunds and Offsets', 3, 'A', '2024-07-26 06:59:50'),
(9, 1001, 'Extractive Industries and Complex Tax Audits', 3, 'A', '2024-07-26 07:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL DEFAULT '1001',
  `designation_title` varchar(150) NOT NULL COMMENT 'Name of the Designatioon',
  `responsibilities` mediumtext COMMENT 'Text Input',
  `experience_required` varchar(20) DEFAULT NULL COMMENT 'Text Input\r\nFormat = 00 Years 00 Months',
  `added_by` int(11) NOT NULL COMMENT 'Current User ID',
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '0 = Inactive 1 = Active',
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active / Available D = Deleted',
  `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `client_id`, `designation_title`, `responsibilities`, `experience_required`, `added_by`, `active`, `status`, `creation_date`, `last_update_date`) VALUES
(1, 1001, 'Developer', NULL, '2 Years', 1, 1, 'D', '2023-11-20 14:01:10', '2023-11-20 14:01:10'),
(2, 1001, 'Designer', NULL, NULL, 1, 1, 'D', '2023-11-20 14:03:11', '2023-11-20 14:03:11'),
(3, 1001, 'Sales', NULL, NULL, 1, 1, 'D', '2023-11-20 14:06:08', '2023-11-20 14:06:08'),
(7, 1001, 'HR Manager', NULL, NULL, 3, 1, 'D', '2024-01-02 17:50:03', '2024-01-02 17:50:03'),
(8, 1001, 'Sales Manager', NULL, NULL, 6, 1, 'D', '2024-01-03 13:17:00', '2024-01-03 13:17:00'),
(9, 1001, 'Operations Head', NULL, NULL, 6, 1, 'D', '2024-01-12 18:40:14', '2024-01-12 18:40:14'),
(10, 1001, 'Manager - IT & Admin', NULL, NULL, 6, 1, 'D', '2024-01-12 18:40:45', '2024-01-12 18:40:45'),
(11, 1001, 'Sr IT Technician ', NULL, NULL, 6, 1, 'D', '2024-01-12 18:41:23', '2024-01-12 18:41:23'),
(12, 1001, 'Business Development Manager ', NULL, NULL, 6, 1, 'D', '2024-01-12 18:41:58', '2024-01-12 18:41:58'),
(13, 1001, 'BDM & Talent Advisor', NULL, NULL, 6, 1, 'D', '2024-01-12 18:42:50', '2024-01-12 18:42:50'),
(14, 1001, 'Recruiter', NULL, NULL, 6, 1, 'D', '2024-01-12 18:43:17', '2024-01-12 18:43:17'),
(15, 1001, 'Tele calling Executive ', NULL, NULL, 6, 1, 'D', '2024-01-12 18:43:50', '2024-01-12 18:43:50'),
(16, 1001, 'BDE Domestic Sales', NULL, NULL, 6, 1, 'D', '2024-01-12 18:44:10', '2024-01-12 18:44:10'),
(17, 1001, 'Web Consultant', NULL, NULL, 6, 1, 'D', '2024-01-12 18:44:28', '2024-01-12 18:44:28'),
(18, 1001, 'Sr. Web Consultant', NULL, NULL, 6, 1, 'D', '2024-01-12 18:44:44', '2024-01-12 18:44:44'),
(19, 1001, 'Full Stack Developer', 'Full Stack Development', '3', 6, 1, 'D', '2024-01-30 17:24:34', '2024-01-30 17:24:34'),
(20, 1001, 'Web Developer', NULL, '2', 6, 1, 'D', '2024-01-30 18:02:00', '2024-01-30 18:02:00'),
(21, 1001, 'Graphics Designer', NULL, '2', 6, 1, 'D', '2024-01-30 18:02:32', '2024-01-30 18:02:32'),
(22, 1001, 'IT - Head', NULL, '10', 6, 1, 'D', '2024-01-31 18:12:58', '2024-01-31 18:12:58'),
(23, 1001, 'Sales & Marketing Executive', NULL, '3', 6, 1, 'D', '2024-02-19 19:39:30', '2024-02-19 19:39:30'),
(24, 1001, 'Ads Manager', NULL, NULL, 6, 1, 'D', '2024-02-20 20:23:22', '2024-02-20 20:23:22'),
(25, 1001, 'Voice & Accent Trainer', 'Training', '3', 6, 1, 'D', '2024-02-26 17:52:36', '2024-02-26 17:52:36'),
(26, 1001, 'Sr HR Recruiter', 'Recruitment', '2', 6, 1, 'D', '2024-02-26 17:53:21', '2024-02-26 17:53:21'),
(27, 1001, 'Principal Auditor - Extractive Industries Tax Audits - Team A', NULL, NULL, 3, 1, 'A', '2024-07-26 07:02:03', '2024-07-26 07:02:03'),
(28, 1001, 'Principal Auditor - Extractive Industries Tax Audits - Team B', NULL, NULL, 3, 1, 'A', '2024-07-26 07:02:18', '2024-07-26 07:02:18'),
(29, 1001, 'Principal Auditor -Complex Audits', NULL, NULL, 3, 1, 'A', '2024-07-26 07:02:46', '2024-07-26 07:02:46'),
(30, 1001, 'Senior Auditor - Extractive Industries Tax Audits - Team A', NULL, NULL, 3, 1, 'A', '2024-07-26 07:03:15', '2024-07-26 07:03:15'),
(31, 1001, 'Senior Auditor - Extractive Industries Tax Audits - Team B', NULL, NULL, 3, 1, 'A', '2024-07-26 07:03:36', '2024-07-26 07:03:36'),
(32, 1001, 'Senior Auditor - Complex Audits', NULL, NULL, 3, 1, 'A', '2024-07-26 07:04:01', '2024-07-26 07:04:01'),
(33, 1001, 'Auditor - Extractive Industries Tax Audits - Team A', NULL, NULL, 3, 1, 'A', '2024-07-26 07:04:37', '2024-07-26 07:04:37'),
(34, 1001, 'Auditor - Extractive Industries Tax Audits - Team B', NULL, NULL, 3, 1, 'A', '2024-07-26 07:04:46', '2024-07-26 07:04:46'),
(35, 1001, 'Auditor - Complex Audits', NULL, NULL, 3, 1, 'A', '2024-07-26 07:05:08', '2024-07-26 07:05:08'),
(36, 1001, 'Director - Extractive Industries and Complex Tax Audits', NULL, NULL, 3, 1, 'A', '2024-07-26 07:07:58', '2024-07-26 07:07:58'),
(37, 1001, 'Director - Other Large Taxpayer Tax Audits, Refunds and Offsets', NULL, NULL, 3, 1, 'A', '2024-07-26 10:11:51', '2024-07-26 10:11:51'),
(38, 1001, 'Manager - Refunds & Offsets, Large Taxpayers', NULL, NULL, 3, 1, 'A', '2024-07-26 10:12:53', '2024-07-26 10:12:53'),
(39, 1001, 'Senior Auditor - GST Refunds & Offsets - Team A', NULL, NULL, 3, 1, 'D', '2024-07-26 10:13:43', '2024-07-26 10:13:43'),
(40, 1001, 'Senior Auditor - GST Refunds & Offsets - Team A LTP', NULL, NULL, 3, 1, 'A', '2024-07-26 10:14:02', '2024-07-26 10:14:02'),
(41, 1001, 'Senior Auditor - GST Refunds & Offsets - Team B LTP', NULL, NULL, 3, 1, 'A', '2024-07-26 10:14:14', '2024-07-26 10:14:14'),
(42, 1001, 'Senior Auditor - GST S65A Refunds & Offsets LTP', NULL, NULL, 3, 1, 'A', '2024-07-26 10:18:58', '2024-07-26 10:18:58'),
(43, 1001, 'Senior Auditor - Other Tax Refunds & Offsets LTP', NULL, NULL, 3, 1, 'A', '2024-07-26 10:19:53', '2024-07-26 10:19:53'),
(44, 1001, 'Auditor - GST Refunds & Offsets - Team A LTP', NULL, NULL, 3, 1, 'A', '2024-07-26 10:20:38', '2024-07-26 10:20:38'),
(45, 1001, 'Auditor - GST Refunds & Offsets - Team B LTP', NULL, NULL, 3, 1, 'A', '2024-07-26 10:21:23', '2024-07-26 10:21:23'),
(46, 1001, 'Auditor - GST S65A Refunds & Offsets LTP', NULL, NULL, 3, 1, 'A', '2024-07-26 10:21:49', '2024-07-26 10:21:49'),
(47, 1001, 'Auditor - Other Tax Refunds & Offsets LTP', NULL, NULL, 3, 1, 'A', '2024-07-26 10:22:09', '2024-07-26 10:22:09');

-- --------------------------------------------------------

--
-- Table structure for table `employee_details`
--

CREATE TABLE `employee_details` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL DEFAULT '1001',
  `employee_name` varchar(100) NOT NULL,
  `employee_mobile` varchar(15) DEFAULT NULL,
  `employee_email` varchar(30) DEFAULT NULL,
  `employee_date_of_birth` date DEFAULT NULL,
  `employee_father_name` varchar(100) DEFAULT NULL,
  `employee_mother_name` varchar(100) DEFAULT NULL,
  `employee_blood_group` varchar(10) DEFAULT NULL,
  `employee_designation_id` int(11) NOT NULL DEFAULT '0',
  `employee_date_of_joinning` date DEFAULT NULL,
  `employee_experience_duration` varchar(20) DEFAULT NULL COMMENT 'Format = 00 Years 00 Months',
  `employee_payroll` int(11) NOT NULL DEFAULT '1' COMMENT '1 = company payroll\r\n2 = contact',
  `employee_grade` int(11) NOT NULL DEFAULT '4',
  `employee_id` varchar(10) DEFAULT NULL,
  `department_id` int(11) NOT NULL DEFAULT '0',
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
  `remarks` mediumtext,
  `remark_by` int(11) DEFAULT NULL COMMENT 'Current User ID',
  `employee_added_by` int(11) NOT NULL COMMENT 'Current User ID',
  `last_working_day` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '0 = Inactive\r\n1 = Active\r\n2 = RESIGNED\r\n3 = ABSCONDED\r\n4 = SERVING_NOTICE\r\n5 = OTHER REASON',
  `inactive_reason` varchar(300) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active / Available\r\nD = Deleted',
  `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reporting_time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `employee_details`
--

INSERT INTO `employee_details` (`id`, `client_id`, `employee_name`, `employee_mobile`, `employee_email`, `employee_date_of_birth`, `employee_father_name`, `employee_mother_name`, `employee_blood_group`, `employee_designation_id`, `employee_date_of_joinning`, `employee_experience_duration`, `employee_payroll`, `employee_grade`, `employee_id`, `department_id`, `salary_amount`, `webmail_address`, `current_address`, `permanent_address`, `emergency_contact_person_name`, `emergency_contact_person_mobile_number`, `aadhaar_number`, `pan_number`, `salary_account_number`, `salary_account_ifsc_code`, `uan_number`, `esic_ip_number`, `remarks`, `remark_by`, `employee_added_by`, `last_working_day`, `active`, `inactive_reason`, `status`, `creation_date`, `last_update_date`, `reporting_time`) VALUES
(2, 1001, 'Tax Admin', '9561237890', 'tax.admin@email.com', NULL, NULL, NULL, NULL, 7, '2023-04-05', '10', 1, 4, '800145', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', 3, 3, NULL, 1, NULL, 'A', '2024-01-03 12:16:58', '2024-01-31 18:48:02', 12),
(11, 1001, 'Jyotirmoy Saha', '8520741096', 'jsaha@email.com', NULL, NULL, NULL, NULL, 20, '2023-07-28', '2', 1, 4, '800184', 2, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL, 1, NULL, 'D', '2024-01-30 18:24:14', '2024-01-30 18:24:14', 11),
(39, 1001, 'New Auditor', '7539512846', 'newauditor@email.com', NULL, NULL, NULL, NULL, 0, '2024-07-09', NULL, 1, 4, '800185', 0, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL, 1, NULL, 'D', '2024-07-09 13:24:16', '2024-07-09 13:24:16', 0),
(40, 1001, 'New Auditor 2', '7539513840', 'newauditor2@email.com', NULL, NULL, NULL, NULL, 0, '2024-07-09', NULL, 1, 4, '800186', 0, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL, 1, NULL, 'D', '2024-07-09 13:43:51', '2024-07-09 13:43:51', 0),
(41, 1001, 'X', '123456789', 'x@xmail.com', NULL, NULL, NULL, NULL, 30, '2024-08-08', NULL, 1, 4, '00012', 8, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, NULL, 'A', '2024-08-08 05:04:14', '2024-08-08 05:04:14', 0),
(42, 1001, 'Y', '987654321', 'y@ymail.com', NULL, NULL, NULL, NULL, 33, '2024-08-08', NULL, 1, 4, '00014', 8, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, NULL, 'A', '2024-08-08 05:06:51', '2024-08-08 05:06:51', 0),
(43, 1001, 'Z', '987321654', 'z@zmail.com', NULL, NULL, NULL, NULL, 33, '2024-08-08', NULL, 1, 4, '00015', 8, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, NULL, 'A', '2024-08-08 05:09:19', '2024-08-08 05:09:19', 0),
(44, 1001, 'V', '987456321', 'v@vmail.com', NULL, NULL, NULL, NULL, 33, '2024-08-08', NULL, 1, 4, '00016', 8, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, NULL, 'A', '2024-08-08 08:38:16', '2024-08-08 08:38:16', 0),
(45, 1001, 'W', '321987456', 'w@wmail.com', NULL, NULL, NULL, NULL, 35, '2024-08-08', NULL, 1, 4, '00017', 8, '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, NULL, 'A', '2024-08-08 08:39:20', '2024-08-08 08:39:20', 0);

-- --------------------------------------------------------

--
-- Table structure for table `employee_reporting_manager`
--

CREATE TABLE `employee_reporting_manager` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `reporting_manager_user_id` int(11) NOT NULL,
  `assigned_by_user_id` int(11) NOT NULL COMMENT 'Current User ID',
  `assign_date` date DEFAULT NULL,
  `dismiss_date` date DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active, D = Deactive',
  `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `employee_reporting_manager`
--

INSERT INTO `employee_reporting_manager` (`id`, `client_id`, `employee_id`, `reporting_manager_user_id`, `assigned_by_user_id`, `assign_date`, `dismiss_date`, `status`, `creation_date`) VALUES
(2, 1001, 2, 3, 3, '2024-01-03', NULL, 'A', '2024-01-03 12:16:58'),
(6, 1001, 6, 6, 6, '2024-01-12', NULL, 'A', '2024-01-12 19:03:34'),
(7, 1001, 7, 3, 3, '2024-01-30', NULL, 'A', '2024-01-30 16:28:08'),
(8, 1001, 8, 6, 6, '2024-01-30', NULL, 'A', '2024-01-30 16:46:45'),
(9, 1001, 9, 3, 6, '2024-01-30', NULL, 'A', '2024-01-30 18:00:28'),
(10, 1001, 10, 3, 6, '2024-01-30', NULL, 'A', '2024-01-30 18:14:00'),
(11, 1001, 11, 3, 6, '2024-01-30', NULL, 'A', '2024-01-30 18:24:14'),
(12, 1001, 12, 3, 6, '2024-01-30', NULL, 'A', '2024-01-30 18:31:30'),
(13, 1001, 13, 16, 6, '2024-01-31', NULL, 'A', '2024-01-31 13:17:38'),
(14, 1001, 14, 16, 6, '2024-01-31', NULL, 'A', '2024-01-31 13:40:38'),
(15, 1001, 15, 3, 6, '2024-01-31', NULL, 'A', '2024-01-31 14:05:37'),
(16, 1001, 16, 19, 6, '2024-01-31', NULL, 'A', '2024-01-31 17:33:48'),
(17, 1001, 17, 19, 6, '2024-01-31', NULL, 'A', '2024-01-31 17:47:57'),
(18, 1001, 18, 19, 6, '2024-01-31', NULL, 'A', '2024-01-31 17:58:53'),
(19, 1001, 19, 19, 6, '2024-01-31', NULL, 'A', '2024-01-31 18:11:37'),
(20, 1001, 20, 3, 6, '2024-01-31', NULL, 'A', '2024-01-31 18:28:46'),
(21, 1001, 21, 10, 6, '2024-02-12', NULL, 'A', '2024-02-12 12:06:09'),
(22, 1001, 22, 10, 6, '2024-02-12', NULL, 'A', '2024-02-12 14:07:56'),
(23, 1001, 23, 19, 6, '2024-02-19', NULL, 'A', '2024-02-19 19:35:48'),
(24, 1001, 24, 3, 6, '2024-02-19', NULL, 'A', '2024-02-19 19:47:37'),
(25, 1001, 25, 19, 6, '2024-02-20', NULL, 'A', '2024-02-20 20:23:01'),
(26, 1001, 26, 3, 6, '2024-02-20', NULL, 'A', '2024-02-20 20:27:04'),
(27, 1001, 27, 19, 6, '2024-02-26', NULL, 'A', '2024-02-26 17:36:49'),
(28, 1001, 28, 19, 6, '2024-02-26', NULL, 'A', '2024-02-26 17:46:12'),
(29, 1001, 29, 16, 6, '2024-02-26', NULL, 'A', '2024-02-26 18:08:52'),
(30, 1001, 30, 6, 6, '2024-02-27', NULL, 'A', '2024-02-27 17:49:47'),
(31, 1001, 31, 19, 6, '2024-03-04', NULL, 'A', '2024-03-04 19:53:18'),
(32, 1001, 32, 19, 6, '2024-03-04', NULL, 'A', '2024-03-04 20:01:39'),
(33, 1001, 33, 19, 6, '2024-03-14', NULL, 'A', '2024-03-14 13:24:16'),
(34, 1001, 34, 19, 6, '2024-03-14', NULL, 'A', '2024-03-14 13:33:23'),
(35, 1001, 35, 16, 6, '2024-03-14', NULL, 'A', '2024-03-14 13:45:39'),
(36, 1001, 36, 16, 6, '2024-03-15', NULL, 'A', '2024-03-15 15:11:36'),
(37, 1001, 37, 16, 6, '2024-03-15', NULL, 'A', '2024-03-15 16:24:52'),
(38, 1001, 38, 16, 6, '2024-03-15', NULL, 'A', '2024-03-15 16:46:19'),
(39, 1001, 39, 0, 6, '2024-07-09', NULL, 'A', '2024-07-09 13:24:16'),
(40, 1001, 40, 0, 6, '2024-07-09', NULL, 'A', '2024-07-09 13:43:51'),
(41, 1001, 41, 0, 3, '2024-08-08', NULL, 'A', '2024-08-08 05:04:14'),
(42, 1001, 42, 0, 3, '2024-08-08', NULL, 'A', '2024-08-08 05:06:51'),
(43, 1001, 43, 0, 3, '2024-08-08', NULL, 'A', '2024-08-08 05:09:19'),
(44, 1001, 44, 0, 3, '2024-08-08', NULL, 'A', '2024-08-08 08:38:16'),
(45, 1001, 45, 0, 3, '2024-08-08', NULL, 'A', '2024-08-08 08:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `position_papers`
--

CREATE TABLE `position_papers` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `date_of_issue` date DEFAULT NULL,
  `initial_submission_date` date DEFAULT NULL,
  `date_of_reply` date DEFAULT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT '0',
  `extension_days` varchar(10) DEFAULT NULL,
  `extention_end_date_to_reply` date DEFAULT NULL,
  `open_close_status` int(11) NOT NULL DEFAULT '1' COMMENT '1 = open, 0 = closed',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `position_papers`
--

INSERT INTO `position_papers` (`id`, `client_id`, `reference_no`, `company_id`, `date_of_issue`, `initial_submission_date`, `date_of_reply`, `if_extension_granted`, `extension_days`, `extention_end_date_to_reply`, `open_close_status`, `created_at`, `updated_at`) VALUES
(1, 1001, '00125', 1, '2024-08-20', '2024-08-20', NULL, 0, NULL, NULL, 1, '2024-08-20 16:12:28', '2024-08-20 16:12:28'),
(2, 1001, '00123', 1, '2024-08-23', '2024-08-24', NULL, 0, NULL, NULL, 1, '2024-08-23 11:18:19', '2024-08-23 11:18:19'),
(3, 1001, '001', 10, '2024-09-06', '2024-09-07', NULL, 0, NULL, NULL, 1, '2024-09-06 09:43:25', '2024-09-06 09:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `position_paper_data`
--

CREATE TABLE `position_paper_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `query_id` int(11) NOT NULL DEFAULT '0',
  `position_paper_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `date_of_issue` date DEFAULT NULL,
  `initial_submission_date` date DEFAULT NULL,
  `extended_submission_date` date DEFAULT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT '0',
  `extension_days` varchar(10) DEFAULT NULL,
  `extention_end_date_to_reply` date DEFAULT NULL,
  `date_of_reply` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `position_paper_data`
--

INSERT INTO `position_paper_data` (`id`, `client_id`, `company_id`, `query_id`, `position_paper_id`, `user_id`, `date_of_issue`, `initial_submission_date`, `extended_submission_date`, `if_extension_granted`, `extension_days`, `extention_end_date_to_reply`, `date_of_reply`, `active`, `created_at`, `updated_at`) VALUES
(1, 1001, 1, 2, 1, 45, '2024-08-20', '2024-08-20', NULL, 0, NULL, NULL, NULL, 1, '2024-08-20 16:12:28', '2024-08-20 16:12:28'),
(2, 1001, 1, 4, 1, 45, '2024-08-20', '2024-08-20', NULL, 0, NULL, NULL, NULL, 1, '2024-08-20 16:12:28', '2024-08-20 16:12:28'),
(3, 1001, 1, 5, 1, 45, '2024-08-20', '2024-08-20', NULL, 0, NULL, NULL, NULL, 1, '2024-08-20 16:12:28', '2024-08-20 16:12:28'),
(4, 1001, 1, 3, 2, 45, '2024-08-23', '2024-08-24', NULL, 0, NULL, NULL, NULL, 1, '2024-08-23 11:18:19', '2024-08-23 11:18:19'),
(5, 1001, 10, 15, 3, 45, '2024-09-06', '2024-09-07', NULL, 0, NULL, NULL, NULL, 1, '2024-09-06 09:43:25', '2024-09-06 09:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `position_paper_extention_dates`
--

CREATE TABLE `position_paper_extention_dates` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `position_paper_id` int(11) NOT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT '0',
  `extension_days` varchar(20) DEFAULT NULL,
  `extention_start_date` date DEFAULT NULL,
  `extention_end_date` date DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `query_data`
--

CREATE TABLE `query_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `memo_id` int(11) NOT NULL DEFAULT '0',
  `audit_type_id` int(11) NOT NULL DEFAULT '0',
  `tax_type_id` int(11) NOT NULL DEFAULT '0',
  `query_no` varchar(50) DEFAULT NULL,
  `total_no_of_query` varchar(20) DEFAULT NULL,
  `date_of_issue` date DEFAULT NULL,
  `days_to_reply` varchar(10) DEFAULT NULL,
  `last_date_of_reply` date DEFAULT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT '0' COMMENT '0 = Not Granted, 1 = Granted',
  `extension_days` varchar(10) DEFAULT NULL,
  `extention_end_date_to_reply` date DEFAULT NULL,
  `date_of_reply` date DEFAULT NULL,
  `query_reply_is_submitted` int(11) NOT NULL DEFAULT '0' COMMENT '0 = No Input\r\n1 = Submitted\r\n2 = Overdue\r\n3 = Pending\r\n4 = Partially Submitted',
  `no_of_query_solved` varchar(20) DEFAULT NULL,
  `no_of_query_unsolved` varchar(20) DEFAULT NULL,
  `query_status` int(11) NOT NULL DEFAULT '1' COMMENT '1 = open\r\n2 = close\r\n3 = notice_issued\r\n4 = force_closed',
  `remarks` varchar(200) DEFAULT NULL,
  `notice_no` varchar(100) DEFAULT NULL,
  `notice_section` varchar(50) DEFAULT NULL,
  `date_of_notice_issue` date DEFAULT NULL,
  `days_to_reply_notice` varchar(10) DEFAULT NULL,
  `date_of_reply_notice` date DEFAULT NULL,
  `notice_status` int(11) NOT NULL DEFAULT '0' COMMENT '0 = No Input\r\n1 = Submitted\r\n2 = Pending\r\n3 = Overdue',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `query_data`
--

INSERT INTO `query_data` (`id`, `client_id`, `company_id`, `audit_id`, `user_id`, `memo_id`, `audit_type_id`, `tax_type_id`, `query_no`, `total_no_of_query`, `date_of_issue`, `days_to_reply`, `last_date_of_reply`, `if_extension_granted`, `extension_days`, `extention_end_date_to_reply`, `date_of_reply`, `query_reply_is_submitted`, `no_of_query_solved`, `no_of_query_unsolved`, `query_status`, `remarks`, `notice_no`, `notice_section`, `date_of_notice_issue`, `days_to_reply_notice`, `date_of_reply_notice`, `notice_status`, `created_at`, `updated_at`) VALUES
(1, 1001, 1, 0, 45, 0, 1, 2, 'QS01', '1', '2024-03-27', '14', '2024-04-10', 0, NULL, NULL, '2024-04-18', 1, '1', '0', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-08 09:42:54', '2024-08-08 09:58:48'),
(2, 1001, 1, 0, 45, 0, 1, 1, 'QS02', '1', '2024-04-18', '14', '2024-05-02', 0, NULL, NULL, '2024-05-09', 1, '1', '0', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-08 09:44:09', '2024-08-09 05:12:39'),
(3, 1001, 1, 0, 45, 0, 1, 1, 'QS03', '1', '2024-04-22', '7', '2024-04-29', 0, NULL, NULL, '2024-05-06', 1, '1', '0', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-08 09:45:12', '2024-08-09 05:12:55'),
(4, 1001, 1, 0, 45, 0, 1, 1, 'QS04', '1', '2024-05-10', '7', '2024-05-17', 0, NULL, NULL, '2024-05-24', 1, '1', '0', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-08 09:45:51', '2024-08-09 05:14:05'),
(5, 1001, 1, 0, 45, 0, 1, 1, 'QS05', '1', '2024-05-13', '7', '2024-05-20', 0, NULL, NULL, '2024-05-27', 1, '1', '0', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-08 09:46:44', '2024-08-09 05:14:24'),
(6, 1001, 1, 0, 45, 0, 1, 1, 'QS06', '1', '2024-05-20', '7', '2024-05-27', 0, NULL, NULL, '2024-05-27', 1, '1', '0', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-08 09:47:28', '2024-08-09 05:14:37'),
(7, 1001, 1, 0, 45, 1, 1, 1, 'QS07', '25', '2024-05-21', '14', '2024-06-04', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-08 09:52:29', '2024-08-08 09:52:29'),
(8, 1001, 1, 0, 45, 2, 1, 1, 'QS08', '14', '2024-05-27', '7', '2024-06-03', 0, NULL, NULL, NULL, 0, NULL, NULL, 4, 'force close', NULL, NULL, NULL, NULL, NULL, 0, '2024-08-08 09:53:14', '2024-08-13 09:41:10'),
(9, 1001, 2, 0, 47, 0, 1, 1, 'QS021', '1', '2024-07-30', '7', '2024-08-06', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-09 06:44:14', '2024-08-09 06:44:14'),
(10, 1001, 2, 0, 47, 0, 1, 1, 'QS022', '1', '2024-07-30', '7', '2024-08-06', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-09 06:45:08', '2024-08-09 06:45:08'),
(11, 1001, 2, 0, 47, 0, 1, 1, 'QS023', '1', '2024-07-30', '7', '2024-08-06', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-09 06:45:31', '2024-08-09 06:45:31'),
(12, 1001, 1, 0, 45, 0, 1, 4, 'QS09', '1', '2024-08-12', '2', '2024-08-14', 1, '5', '2024-08-18', NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-12 10:39:12', '2024-08-12 10:39:12'),
(13, 1001, 1, 0, 45, 0, 1, 4, 'QS354656', '5', '2024-08-23', '2', '2024-08-24', 1, '2', '2024-08-25', '2024-08-24', 4, '4', '1', 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-23 06:38:34', '2024-08-23 11:13:08'),
(14, 1001, 10, 0, 45, 0, 1, 1, '001', '5', '2024-08-30', '2', '2024-08-31', 0, NULL, NULL, '2024-09-06', 1, '5', '0', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-30 17:03:21', '2024-09-06 09:48:34'),
(15, 1001, 10, 0, 45, 0, 1, 1, '002', '10', '2024-08-30', '2', '2024-08-31', 0, NULL, NULL, NULL, 0, NULL, NULL, 4, 'The Tax Payer did not reply even after reply even after extension', NULL, NULL, NULL, NULL, NULL, 0, '2024-08-30 17:03:46', '2024-09-06 09:43:01'),
(16, 1001, 10, 0, 45, 0, 1, 1, '003', '5', '2024-08-30', '2', '2024-08-31', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-30 17:04:04', '2024-08-30 17:04:04'),
(17, 1001, 10, 0, 45, 0, 1, 1, '004', '5', '2024-08-30', '2', '2024-08-31', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-30 17:04:20', '2024-08-30 17:04:20'),
(18, 1001, 10, 0, 45, 0, 1, 1, '005', '5', '2024-08-30', '3', '2024-09-01', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-30 17:04:43', '2024-08-30 17:04:43'),
(19, 1001, 10, 0, 45, 4, 1, 1, '006', '10', '2024-08-30', '2', '2024-08-31', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-30 17:25:37', '2024-08-30 17:25:37'),
(20, 1001, 10, 0, 45, 6, 1, 1, '007', '5', '2024-08-30', '2', '2024-08-31', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-30 17:40:20', '2024-08-30 17:40:20'),
(21, 1001, 10, 0, 45, 5, 1, 1, '008', '9', '2024-08-30', '2', '2024-08-31', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-08-30 17:43:14', '2024-08-30 17:43:14'),
(22, 1001, 10, 0, 45, 0, 1, 1, '009', '5', '2024-09-05', '5', '2024-09-09', 0, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-09-05 17:40:40', '2024-09-05 17:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `query_extension_dates`
--

CREATE TABLE `query_extension_dates` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `query_id` int(11) NOT NULL,
  `if_extension_granted` int(11) NOT NULL DEFAULT '0',
  `extension_days` varchar(20) DEFAULT NULL,
  `extention_start_date` date NOT NULL,
  `extention_end_date` date NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `query_extension_dates`
--

INSERT INTO `query_extension_dates` (`id`, `client_id`, `query_id`, `if_extension_granted`, `extension_days`, `extention_start_date`, `extention_end_date`, `active`, `created_at`, `updated_at`) VALUES
(1, 1001, 2, 0, '7', '2024-05-03', '2024-05-10', 1, '2024-08-08 10:11:08', '2024-08-08 10:11:08'),
(2, 1001, 3, 0, '7', '2024-04-30', '2024-05-07', 1, '2024-08-09 05:07:51', '2024-08-09 05:07:51'),
(3, 1001, 4, 0, '7', '2024-05-18', '2024-05-25', 1, '2024-08-09 05:11:14', '2024-08-09 05:11:14'),
(4, 1001, 5, 0, '7', '2024-05-21', '2024-05-28', 1, '2024-08-09 05:11:24', '2024-08-09 05:11:24'),
(5, 1001, 7, 0, '14', '2024-06-05', '2024-06-19', 1, '2024-08-09 05:11:49', '2024-08-09 05:11:49'),
(6, 1001, 8, 0, '14', '2024-06-04', '2024-06-18', 1, '2024-08-09 05:12:02', '2024-08-09 05:12:02'),
(7, 1001, 12, 1, '5', '2024-08-14', '2024-08-18', 1, '2024-08-12 15:09:43', '2024-08-12 15:10:46'),
(8, 1001, 13, 1, '2', '2024-08-24', '2024-08-25', 1, '2024-08-23 11:09:42', '2024-08-23 11:10:39'),
(9, 1001, 14, 0, '8', '2024-08-31', '2024-09-07', 1, '2024-09-05 21:51:08', '2024-09-05 21:51:08'),
(10, 1001, 15, 0, '6', '2024-08-31', '2024-09-05', 1, '2024-09-05 21:54:15', '2024-09-05 21:54:15'),
(11, 1001, 16, 0, '8', '2024-08-31', '2024-09-07', 1, '2024-09-05 21:54:27', '2024-09-05 21:54:27');

-- --------------------------------------------------------

--
-- Table structure for table `query_reply`
--

CREATE TABLE `query_reply` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `query_id` int(11) NOT NULL DEFAULT '0',
  `date_of_reply` date DEFAULT NULL,
  `no_of_query_solved` varchar(20) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `query_reply`
--

INSERT INTO `query_reply` (`id`, `client_id`, `query_id`, `date_of_reply`, `no_of_query_solved`, `status`, `created_at`) VALUES
(1, 1001, 1, '2024-04-18', '1', 'A', '2024-08-08 09:58:48'),
(2, 1001, 2, '2024-05-09', '1', 'A', '2024-08-09 05:12:39'),
(3, 1001, 3, '2024-05-06', '1', 'A', '2024-08-09 05:12:55'),
(4, 1001, 4, '2024-05-24', '1', 'A', '2024-08-09 05:14:05'),
(5, 1001, 5, '2024-05-27', '1', 'A', '2024-08-09 05:14:24'),
(6, 1001, 6, '2024-05-27', '1', 'A', '2024-08-09 05:14:37'),
(7, 1001, 13, '2024-08-23', '3', 'A', '2024-08-23 11:11:40'),
(8, 1001, 13, '2024-08-24', '1', 'A', '2024-08-23 11:13:08'),
(9, 1001, 14, '2024-09-06', '5', 'A', '2024-09-06 09:48:34');

-- --------------------------------------------------------

--
-- Table structure for table `tax_collection_data`
--

CREATE TABLE `tax_collection_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `tax_amount` double NOT NULL DEFAULT '0',
  `paid_amount` double NOT NULL DEFAULT '0',
  `pending_amount` double NOT NULL DEFAULT '0',
  `last_payment_date` date DEFAULT NULL,
  `penalty_amount` double NOT NULL DEFAULT '0',
  `penalty_paid_amount` double NOT NULL DEFAULT '0',
  `penalty_pending_amount` double NOT NULL DEFAULT '0',
  `penalty_last_payment_date` date DEFAULT NULL,
  `payment_status` int(11) NOT NULL DEFAULT '1' COMMENT '1 = active\r\n2 = closed\r\n3 = partially paid',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tax_payment_history`
--

CREATE TABLE `tax_payment_history` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `tax_collection_id` int(11) NOT NULL,
  `payment_amount` double NOT NULL DEFAULT '0',
  `payment_date` date DEFAULT NULL,
  `payment_type` int(11) NOT NULL DEFAULT '1' COMMENT '1 = Tax Amount\r\n2 = Penalty Amount',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `type_of_tax`
--

CREATE TABLE `type_of_tax` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `type_of_tax` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'A',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `type_of_tax`
--

INSERT INTO `type_of_tax` (`id`, `client_id`, `type_of_tax`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1001, 'CIT', 15, 'A', '2024-07-02 15:34:26', '2024-07-02 15:34:26'),
(2, 1001, 'SWT', 15, 'A', '2024-07-02 15:34:26', '2024-07-02 15:34:26'),
(3, 1001, 'DWT', 15, 'A', '2024-07-02 15:35:08', '2024-07-02 15:35:08'),
(4, 1001, 'IWT', 15, 'A', '2024-07-02 15:35:08', '2024-07-02 15:35:08'),
(5, 1001, 'GST', 3, 'A', '2024-08-08 07:15:55', '2024-08-08 07:15:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL DEFAULT '0',
  `user_type` int(11) NOT NULL COMMENT '(SADMIN, 7)\r\n(ADMIN, 6)\r\n(MANAGER, 5)\r\n(EMPLOYEE, 4)',
  `name` varchar(100) NOT NULL,
  `email` varchar(70) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `password` varchar(20) NOT NULL,
  `pass_hash` varchar(100) NOT NULL,
  `ref_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Reference Table Id',
  `active` int(11) NOT NULL DEFAULT '1' COMMENT '1 = Active, 0 = Deactive',
  `status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A = Active, D = Deleted / Removed',
  `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password_updated_at` datetime DEFAULT NULL,
  `infotext` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `client_id`, `employee_id`, `user_type`, `name`, `email`, `mobile`, `password`, `pass_hash`, `ref_id`, `active`, `status`, `creation_date`, `updated_at`, `password_updated_at`, `infotext`) VALUES
(1, 1001, 0, 6, 'demo admin', 'jsaha.demo@gmail.com', '9123456789', 'Admin.1234', '9cdca6289d90c1b87395bfcb2a07e1b407710d11141d6c5080fbdfba5360cdff', 0, 1, 'A', '2022-04-20 11:09:54', '2024-08-29 17:47:52', NULL, NULL),
(3, 1001, 1001, 7, 'Super Admin', 'superadmin@email.com', '1234567890', 'Sadmin.1234', 'ad2d410ec36b60990d53022a0383713d404e3c2b8d1b604c25226541bc74c390', 0, 1, 'A', '2023-12-28 11:03:54', '2024-08-29 17:47:52', NULL, NULL),
(6, 1001, 2, 6, 'Tax Admin', 'tax.admin@email.com', '9561237890', 'Taxadmin.1234', 'd48ed964359f3d5c5db9a93f8c08dded7480f1336e5ae5a3469d17e5467a1f98', 0, 1, 'A', '2024-01-03 12:16:58', '2024-08-29 17:47:52', NULL, NULL),
(15, 1001, 11, 4, 'Jyotirmoy Saha', 'jsaha@email.com', '8520741096', 'Joy.1234', '196650546410c8620f28703ca0cc63e842319fdaaa85945ac349591b8d2e0c14', 0, 1, 'D', '2024-01-30 18:24:14', '2024-08-29 17:47:52', NULL, NULL),
(43, 1001, 39, 4, 'New Auditor', 'newauditor@email.com', '7539512846', 'New.1234', 'f2faf162666d07d462e267365eccdb361aecb16b888656ae9499878af6d32470', 0, 1, 'D', '2024-07-09 13:24:16', '2024-08-29 17:47:52', NULL, NULL),
(44, 1001, 40, 4, 'New Auditor 2', 'newauditor2@email.com', '7539513840', 'New2.1234', 'e89cbf93c61e0d682e4d99dba24c74603e00f62c62325e38f7b762cb4f1d4b2d', 0, 1, 'D', '2024-07-09 13:43:51', '2024-08-29 17:47:52', NULL, NULL),
(45, 1001, 41, 4, 'X', 'x@xmail.com', '123456789', 'x@123', '88a4d9b2eaa914073bd1bf853eaf8b17cce0773f381f2f5b106e0240eb00da88', 0, 1, 'A', '2024-08-08 05:04:14', '2024-08-29 17:47:52', NULL, NULL),
(46, 1001, 42, 4, 'Y', 'y@ymail.com', '987654321', 'y@123', '802e25b82917f1d77c4f9218af7cc290305beccaa78806e0bda081c96a817058', 0, 1, 'A', '2024-08-08 05:06:51', '2024-08-29 17:47:52', NULL, NULL),
(47, 1001, 43, 4, 'Z', 'z@zmail.com', '987321654', 'z@123', '7137ef63ca374b334a5dba8a2b5b006819195d8d4c21950abbbcb7e318ac103d', 0, 1, 'A', '2024-08-08 05:09:19', '2024-08-29 17:47:52', NULL, NULL),
(48, 1001, 44, 4, 'V', 'v@vmail.com', '987456321', 'v@123', '6c86183bfc1fa697680cd5070639f7ac43e2340c3272c105865845e1600946c2', 0, 1, 'A', '2024-08-08 08:38:16', '2024-08-29 17:47:52', NULL, NULL),
(49, 1001, 45, 4, 'W', 'w@wmail.com', '321987456', 'w@123', '9798d56676bbb0cadb9d74ea07fa4dabdd096e2032a5551163178d1b26aba765', 0, 1, 'A', '2024-08-08 08:39:20', '2024-08-29 17:47:52', NULL, NULL),
(50, 1001, 0, 3, 'it admin', 'it.admin@email.com', '9123456780', 'Admin.1234', '9cdca6289d90c1b87395bfcb2a07e1b407710d11141d6c5080fbdfba5360cdff', 0, 1, 'A', '2022-04-20 11:09:54', '2024-08-28 11:43:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_last_session_data`
--

CREATE TABLE `user_last_session_data` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_date` date DEFAULT NULL,
  `login_time` varchar(30) DEFAULT NULL,
  `logout_date` date DEFAULT NULL,
  `logout_time` varchar(30) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `infotext` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_last_session_data`
--

INSERT INTO `user_last_session_data` (`id`, `client_id`, `user_id`, `login_date`, `login_time`, `logout_date`, `logout_time`, `ip_address`, `infotext`) VALUES
(1, 1001, 3, '2024-09-09', '20:05:11', '2024-09-07', '22:14:57', '2401:4900:882a:6bbc:2012:3c66:f614:f6b5', '2401:4900:882a:6bbc:2012:3c66:f614:f6b5 || Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 || 2024-09-09 20:05:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audits_data`
--
ALTER TABLE `audits_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_assessment_data`
--
ALTER TABLE `audit_assessment_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_close_request_data`
--
ALTER TABLE `audit_close_request_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_memo_data`
--
ALTER TABLE `audit_memo_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_tax_type_history`
--
ALTER TABLE `audit_tax_type_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_time_spent_data`
--
ALTER TABLE `audit_time_spent_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_types`
--
ALTER TABLE `audit_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_assigned_data`
--
ALTER TABLE `company_assigned_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_industry_type`
--
ALTER TABLE `company_industry_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_notice_data`
--
ALTER TABLE `company_notice_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_reporting_manager`
--
ALTER TABLE `employee_reporting_manager`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `position_papers`
--
ALTER TABLE `position_papers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `position_paper_data`
--
ALTER TABLE `position_paper_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `position_paper_extention_dates`
--
ALTER TABLE `position_paper_extention_dates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `query_data`
--
ALTER TABLE `query_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `query_extension_dates`
--
ALTER TABLE `query_extension_dates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `query_reply`
--
ALTER TABLE `query_reply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_collection_data`
--
ALTER TABLE `tax_collection_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_payment_history`
--
ALTER TABLE `tax_payment_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type_of_tax`
--
ALTER TABLE `type_of_tax`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_last_session_data`
--
ALTER TABLE `user_last_session_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audits_data`
--
ALTER TABLE `audits_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_assessment_data`
--
ALTER TABLE `audit_assessment_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_close_request_data`
--
ALTER TABLE `audit_close_request_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_memo_data`
--
ALTER TABLE `audit_memo_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `audit_tax_type_history`
--
ALTER TABLE `audit_tax_type_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `audit_time_spent_data`
--
ALTER TABLE `audit_time_spent_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `audit_types`
--
ALTER TABLE `audit_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1002;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `company_assigned_data`
--
ALTER TABLE `company_assigned_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `company_industry_type`
--
ALTER TABLE `company_industry_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `company_notice_data`
--
ALTER TABLE `company_notice_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `employee_details`
--
ALTER TABLE `employee_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `employee_reporting_manager`
--
ALTER TABLE `employee_reporting_manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `position_papers`
--
ALTER TABLE `position_papers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `position_paper_data`
--
ALTER TABLE `position_paper_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `position_paper_extention_dates`
--
ALTER TABLE `position_paper_extention_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `query_data`
--
ALTER TABLE `query_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `query_extension_dates`
--
ALTER TABLE `query_extension_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `query_reply`
--
ALTER TABLE `query_reply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tax_collection_data`
--
ALTER TABLE `tax_collection_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_payment_history`
--
ALTER TABLE `tax_payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `type_of_tax`
--
ALTER TABLE `type_of_tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `user_last_session_data`
--
ALTER TABLE `user_last_session_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
