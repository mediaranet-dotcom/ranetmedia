-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 03, 2025 at 05:20 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `provider-internet`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing_cycles`
--

CREATE TABLE `billing_cycles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `interval_count` int NOT NULL,
  `interval_type` enum('day','week','month','year') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'month',
  `billing_day` int NOT NULL DEFAULT '1',
  `due_days` int NOT NULL DEFAULT '7',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `billing_cycles`
--

INSERT INTO `billing_cycles` (`id`, `name`, `interval_count`, `interval_type`, `billing_day`, `due_days`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Monthly', 1, 'month', 1, 7, 1, 'Monthly billing cycle', '2025-08-01 09:35:43', '2025-08-01 09:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'RANET Provider',
  `company_address` text COLLATE utf8mb4_unicode_ci,
  `company_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'NPWP',
  `business_license` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'NIB/SIUP',
  `bank_details` text COLLATE utf8mb4_unicode_ci,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_settings` json DEFAULT NULL,
  `email_settings` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `company_address`, `company_phone`, `company_email`, `company_website`, `tax_number`, `business_license`, `bank_details`, `logo_path`, `invoice_settings`, `email_settings`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'RANET', 'Desa Wonodadi\nKecamatan Plantungan\nKabupaten Kendal', '(021) 1234-5678', 'ranet@gmail.com', NULL, '-', '-', 'Bank BRI: 5919-0101-1224-534\nBank Mandiri: 0987654321\nAtas Nama: M SAIFUL KHADZIQ\n\nE-Wallet:\nGoPay/OVO: 0852-2620-5548\nDANA: 0852-2620-5548', 'logos/01K1M9RJ07HN6XJY8NFA9FBQPR.jpg', '{\"show_logo\": true, \"footer_text\": \"Terima kasih atas kepercayaan Anda menggunakan layanan RANET Provider. Untuk informasi lebih lanjut hubungi customer service kami.\", \"show_tax_number\": false, \"show_bank_details\": true, \"show_business_license\": false}', '{\"reply_to\": \"ranet@gmail.com\", \"from_name\": \"RANET\"}', 1, '2025-08-01 11:11:02', '2025-08-01 19:28:49'),
(2, 'RANET Provider', 'Jl. Teknologi Digital No. 123\nJakarta Selatan 12345\nIndonesia', '(021) 1234-5678', 'info@ranetprovider.com', 'www.ranetprovider.com', '12.345.678.9-012.000', '1234567890123456', 'Bank BCA: 1234567890\nBank Mandiri: 0987654321\nAtas Nama: PT RANET Provider\n\nE-Wallet:\nGoPay/OVO: 081234567890\nDANA: 081234567890', NULL, '{\"show_logo\": true, \"footer_text\": \"Terima kasih atas kepercayaan Anda menggunakan layanan RANET Provider. Untuk informasi lebih lanjut hubungi customer service kami.\", \"show_tax_number\": true, \"show_bank_details\": true, \"show_business_license\": true}', '{\"reply_to\": \"noreply@ranetprovider.com\", \"from_name\": \"RANET Provider\"}', 1, '2025-08-02 07:52:37', '2025-08-02 07:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `village` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hamlet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_notes` text COLLATE utf8mb4_unicode_ci,
  `identity_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_id`, `customer_number`, `name`, `email`, `phone`, `address`, `province`, `regency`, `district`, `village`, `hamlet`, `rt`, `rw`, `postal_code`, `address_notes`, `identity_number`, `status`, `created_at`, `updated_at`) VALUES
(7, 'CUST-001', 'RANET-202508-0001', 'Nelli', 'Nelli@email.com', '085727776352', 'Dusun Jung Kidul RT.006 RW.005', 'Jawa Tengah', 'Kendal', 'Plantungan', 'Wonodadi', 'Jung Kidul', '006', '005', '51362', 'Dusun Jung Kidul dekat dengan counter HP Zada', '3324012010990001', 'active', '2025-08-02 03:07:11', '2025-08-02 06:29:16'),
(8, NULL, 'RANET-202508-0002', 'Nanang', 'nanang@email.com', '085229002985', 'Jung kidul RT 006 RW 005', 'Jawa tengah', 'Kendal', 'Plantungan', 'Wonodadi', 'Jung kidul', '006', '005', '51362', NULL, '3324012507840001', 'active', '2025-08-02 07:44:59', '2025-08-02 07:56:34'),
(10, NULL, 'RANET-202508-0003', 'Rondi', 'ronditk.1@gmail.com', '08529029873', 'Rt 11 RW 02 Desa Manggungmangu Kec Plantungan Kab Kendal', 'JAWA TENGAH', 'KENDAL', 'Plantungan', 'Manggungmangu', 'Parakan', '12', '1', '51362', NULL, '3323', 'active', '2025-08-02 10:02:49', '2025-08-02 10:04:51');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `service_id` bigint UNSIGNED NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `billing_period_start` date NOT NULL,
  `billing_period_end` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT 'Tax rate as decimal (0.11 for 11%)',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `outstanding_amount` decimal(15,2) NOT NULL,
  `status` enum('draft','sent','paid','partial_paid','overdue','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `customer_id`, `service_id`, `invoice_date`, `due_date`, `billing_period_start`, `billing_period_end`, `subtotal`, `tax_rate`, `tax_amount`, `discount_amount`, `total_amount`, `paid_amount`, `outstanding_amount`, `status`, `notes`, `metadata`, `sent_at`, `paid_at`, `created_at`, `updated_at`) VALUES
(3, 'INV-202508-0001', 7, 2, '2025-01-01', '2025-01-08', '2025-01-01', '2025-01-31', '100000.00', '0.0000', '0.00', '0.00', '100000.00', '100000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 04:49:18', '2025-08-02 03:11:19', '2025-08-02 04:49:18'),
(8, 'INV-202508-0006', 7, 2, '2025-02-02', '2025-02-10', '2025-02-01', '2025-02-28', '100000.00', '0.0000', '0.00', '0.00', '100000.00', '100000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 05:03:44', '2025-08-02 04:12:02', '2025-08-02 05:03:44'),
(9, 'INV-202508-0007', 7, 2, '2025-03-01', '2025-03-08', '2025-03-01', '2025-03-31', '100000.00', '0.0000', '0.00', '0.00', '100000.00', '100000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 05:21:08', '2025-08-02 04:13:58', '2025-08-02 05:21:08'),
(12, 'INV-202508-0008', 7, 2, '2025-04-01', '2025-04-08', '2025-04-01', '2025-04-30', '100000.00', '0.0000', '0.00', '0.00', '100000.00', '100000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 08:43:28', '2025-08-02 06:10:49', '2025-08-02 08:43:28'),
(13, 'INV-202508-0009', 8, 3, '2025-08-02', '2025-08-09', '2025-08-01', '2025-08-31', '160000.00', '0.0000', '0.00', '0.00', '160000.00', '160000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 08:14:44', '2025-08-02 08:00:49', '2025-08-02 08:14:44'),
(15, 'INV-202508-0011', 8, 3, '2025-02-01', '2025-02-08', '2025-02-01', '2025-02-28', '160000.00', '0.0000', '0.00', '0.00', '160000.00', '160000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 08:47:32', '2025-08-02 08:16:29', '2025-08-02 08:47:32'),
(16, 'INV-202508-0012', 8, 3, '2025-01-01', '2025-01-08', '2025-01-01', '2025-01-31', '160000.00', '0.0000', '0.00', '0.00', '160000.00', '160000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 09:17:09', '2025-08-02 08:17:01', '2025-08-02 09:17:09'),
(17, 'INV-202508-0013', 8, 3, '2025-03-01', '2025-03-08', '2025-03-01', '2025-03-31', '160000.00', '0.0000', '0.00', '0.00', '160000.00', '160000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 09:18:09', '2025-08-02 08:17:36', '2025-08-02 09:18:09'),
(18, 'INV-202508-0014', 8, 3, '2025-04-01', '2025-04-08', '2025-04-01', '2025-04-30', '160000.00', '0.0000', '0.00', '0.00', '160000.00', '160000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 09:18:31', '2025-08-02 08:18:41', '2025-08-02 09:18:31'),
(22, 'INV-202508-0015', 7, 2, '2025-05-01', '2025-05-08', '2025-05-01', '2025-05-31', '100000.00', '0.0000', '0.00', '0.00', '100000.00', '100000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 08:49:53', '2025-08-02 08:48:58', '2025-08-02 08:49:53'),
(23, 'INV-202508-0016', 8, 3, '2025-06-01', '2025-06-08', '2025-06-01', '2025-06-30', '160000.00', '0.0000', '0.00', '0.00', '160000.00', '160000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 09:19:00', '2025-08-02 08:48:58', '2025-08-02 09:19:00'),
(24, 'INV-202508-0017', 7, 2, '2025-08-02', '2025-08-09', '2025-08-01', '2025-08-31', '100000.00', '0.0000', '0.00', '0.00', '100000.00', '100000.00', '0.00', 'paid', NULL, NULL, NULL, '2025-08-02 08:58:39', '2025-08-02 08:50:16', '2025-08-02 08:58:39'),
(26, 'INV-202508-0018', 10, 5, '2025-08-03', '2025-08-10', '2025-08-01', '2025-08-31', '125000.00', '0.0000', '0.00', '0.00', '125000.00', '0.00', '125000.00', 'sent', NULL, NULL, NULL, NULL, '2025-08-02 21:35:25', '2025-08-02 21:49:57');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('subscription','installation','equipment','penalty','discount','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'subscription',
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `service_period_start` date DEFAULT NULL,
  `service_period_end` date DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `description`, `type`, `quantity`, `unit_price`, `total_price`, `service_period_start`, `service_period_end`, `metadata`, `created_at`, `updated_at`) VALUES
(3, 3, 'Langganan Residance-3Mbps - Jan 2025', 'subscription', 1, '100000.00', '100000.00', '2025-01-01', '2025-01-31', NULL, '2025-08-02 03:11:19', '2025-08-02 03:11:19'),
(7, 9, 'Langganan Residance-3Mbps - Mar 2025', 'subscription', 1, '100000.00', '100000.00', '2025-03-01', '2025-03-31', NULL, '2025-08-02 04:13:58', '2025-08-02 04:13:58'),
(9, 12, 'Langganan Residance-3Mbps - Apr 2025', 'subscription', 1, '100000.00', '100000.00', '2025-04-01', '2025-04-30', NULL, '2025-08-02 06:10:49', '2025-08-02 06:10:49'),
(10, 13, 'Langganan Residance-10Mbps - Aug 2025', 'subscription', 1, '160000.00', '160000.00', '2025-08-01', '2025-08-31', NULL, '2025-08-02 08:00:49', '2025-08-02 08:00:49'),
(12, 15, 'Langganan Residance-10Mbps - Feb 2025', 'subscription', 1, '160000.00', '160000.00', '2025-02-01', '2025-02-28', NULL, '2025-08-02 08:16:29', '2025-08-02 08:16:29'),
(13, 16, 'Langganan Residance-10Mbps - Jan 2025', 'subscription', 1, '160000.00', '160000.00', '2025-01-01', '2025-01-31', NULL, '2025-08-02 08:17:01', '2025-08-02 08:17:01'),
(14, 17, 'Langganan Residance-10Mbps - Mar 2025', 'subscription', 1, '160000.00', '160000.00', '2025-03-01', '2025-03-31', NULL, '2025-08-02 08:17:36', '2025-08-02 08:17:36'),
(15, 18, 'Langganan Residance-10Mbps - Apr 2025', 'subscription', 1, '160000.00', '160000.00', '2025-04-01', '2025-04-30', NULL, '2025-08-02 08:18:41', '2025-08-02 08:18:41'),
(18, 22, 'Langganan Residance-3Mbps - May 2025', 'subscription', 1, '100000.00', '100000.00', '2025-05-01', '2025-05-31', NULL, '2025-08-02 08:48:58', '2025-08-02 08:48:58'),
(19, 23, 'Langganan Residance-10Mbps - Jun 2025', 'subscription', 1, '160000.00', '160000.00', '2025-06-01', '2025-06-30', NULL, '2025-08-02 08:48:58', '2025-08-02 08:48:58'),
(20, 24, 'Langganan Residance-3Mbps - Aug 2025', 'subscription', 1, '100000.00', '100000.00', '2025-08-01', '2025-08-31', NULL, '2025-08-02 08:50:16', '2025-08-02 08:50:16'),
(22, 26, 'Langganan Residance-5Mbps - Aug 2025', 'subscription', 1, '125000.00', '125000.00', '2025-08-01', '2025-08-31', NULL, '2025-08-02 21:35:25', '2025-08-02 21:35:25');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_07_26_051900_create_customers_table', 1),
(6, '2025_07_26_052320_create_packages_table', 1),
(7, '2025_07_26_052444_create_services_table', 1),
(8, '2025_07_26_052557_create_payments_table', 1),
(9, '2025_07_26_060144_add_year_and_month_to_payments', 1),
(10, '2025_07_27_043930_create_odps_table', 1),
(11, '2025_07_27_044112_add_odp_fields_to_services_table', 1),
(12, '2025_07_27_120719_create_invoices_table', 1),
(13, '2025_07_27_120730_create_invoice_items_table', 1),
(14, '2025_07_27_120742_create_billing_cycles_table', 1),
(15, '2025_07_27_120851_add_billing_fields_to_services_table', 1),
(16, '2025_07_27_120920_add_invoice_fields_to_payments_table', 1),
(17, '2025_07_27_150036_create_sessions_table', 1),
(18, '2025_07_30_105305_fix_month_column_type_in_payments_table', 1),
(19, '2025_07_30_115740_add_technology_type_to_packages_table', 1),
(20, '2025_07_30_120406_add_network_type_to_services_table', 1),
(21, '2025_07_30_140000_create_ticket_categories_table', 1),
(22, '2025_07_30_140100_create_ticket_priorities_table', 1),
(23, '2025_07_30_140200_create_tickets_table', 1),
(24, '2025_07_30_140300_create_ticket_comments_table', 1),
(25, '2025_07_30_140400_create_ticket_attachments_table', 1),
(26, '2025_07_30_140500_create_ticket_sla_tracking_table', 1),
(27, '2025_08_01_110702_add_customer_number_to_customers_table', 2),
(28, '2025_08_01_113131_create_service_applications_table', 3),
(29, '2025_08_01_164317_add_tax_rate_to_invoices_table', 4),
(30, '2025_08_01_175350_create_company_settings_table', 5),
(31, '2025_08_02_105301_fix_invoices_subtotal_constraint', 6),
(32, '2025_08_02_120000_add_unique_constraint_invoices', 7),
(33, '2025_08_02_132801_add_customer_id_to_customers_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `odps`
--

CREATE TABLE `odps` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_ports` int NOT NULL DEFAULT '8',
  `used_ports` int NOT NULL DEFAULT '0',
  `available_ports` int NOT NULL DEFAULT '8',
  `odp_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '8_port',
  `manufacturer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feeder_cable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fiber_count` int DEFAULT NULL,
  `splitter_ratio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','maintenance','damaged') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `condition` enum('excellent','good','fair','poor','damaged') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'good',
  `installation_date` date DEFAULT NULL,
  `last_maintenance` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `speed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `technology_type` enum('fiber','wireless','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fiber' COMMENT 'Technology type: fiber (ODP required), wireless (no ODP), hybrid (both)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `speed`, `price`, `description`, `technology_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Residance-3Mbps', '3 Mbps', '100000.00', 'Paket internet basic untuk kebutuhan sehari-hari. Cocok untuk browsing, email, dan streaming video SD.', 'hybrid', 1, '2025-08-01 04:50:24', '2025-08-01 07:45:18'),
(2, 'Residance-5Mbps', '5 Mbps', '125000.00', 'Paket internet premium untuk kebutuhan keluarga. Cocok untuk streaming HD, gaming, dan work from home.', 'hybrid', 1, '2025-08-01 04:50:24', '2025-08-01 07:47:56'),
(3, 'Paket Business', '50 Mbps', '1750000.00', 'Paket internet business untuk kebutuhan bisnis. Cocok untuk kantor, streaming 4K, dan multiple devices.', 'hybrid', 1, '2025-08-01 04:50:24', '2025-08-01 07:50:35'),
(4, 'Paket Ultra', '100 Mbps', '3500000.00', 'Paket internet ultra untuk kebutuhan enterprise. Cocok untuk server, cloud computing, dan high-performance applications.', 'hybrid', 1, '2025-08-01 04:50:24', '2025-08-01 07:50:58'),
(5, 'Residance-10Mbps', '10Mbps', '160000.00', NULL, 'hybrid', 1, '2025-08-01 07:49:29', '2025-08-01 07:49:34'),
(6, 'Resaidance-20Mbps', '20Mbps', '700000.00', NULL, 'hybrid', 1, '2025-08-02 09:56:48', '2025-08-02 09:56:48');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `service_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','bank_transfer','e_wallet','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `year` int DEFAULT NULL,
  `month` tinyint DEFAULT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_notes` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `service_id`, `amount`, `payment_date`, `payment_method`, `reference_number`, `notes`, `created_at`, `updated_at`, `year`, `month`, `invoice_id`, `status`, `transaction_id`, `payment_notes`) VALUES
(1, 2, '100000.00', '2025-01-07', 'cash', NULL, NULL, '2025-08-02 03:19:39', '2025-08-02 03:19:39', 2025, 1, 3, 'completed', NULL, NULL),
(2, 2, '100000.00', '2025-02-10', 'cash', NULL, NULL, '2025-08-02 05:03:44', '2025-08-02 05:03:44', 2025, 2, 8, 'completed', NULL, NULL),
(3, 2, '100000.00', '2025-03-05', 'cash', NULL, NULL, '2025-08-02 05:21:08', '2025-08-02 05:21:08', 2025, 3, 9, 'completed', NULL, NULL),
(4, 3, '160000.00', '2025-08-30', 'cash', NULL, NULL, '2025-08-02 08:14:44', '2025-08-02 08:14:44', 2025, 8, 13, 'completed', NULL, NULL),
(5, 2, '100000.00', '2025-04-18', 'cash', NULL, NULL, '2025-08-02 08:43:28', '2025-08-02 08:43:28', 2025, 4, 12, 'completed', NULL, NULL),
(6, 3, '160000.00', '2025-02-20', 'cash', NULL, NULL, '2025-08-02 08:47:32', '2025-08-02 08:47:32', 2025, 2, 15, 'completed', NULL, NULL),
(7, 2, '100000.00', '2025-05-10', 'cash', NULL, NULL, '2025-08-02 08:49:53', '2025-08-02 08:49:53', 2025, 5, 22, 'completed', NULL, NULL),
(8, 2, '100000.00', '2025-08-02', 'cash', NULL, NULL, '2025-08-02 08:58:39', '2025-08-02 08:58:39', 2025, 8, 24, 'completed', NULL, NULL),
(9, 3, '160000.00', '2025-01-10', 'cash', NULL, NULL, '2025-08-02 09:17:09', '2025-08-02 09:17:09', 2025, 1, 16, 'completed', NULL, NULL),
(10, 3, '160000.00', '2025-03-13', 'cash', NULL, NULL, '2025-08-02 09:18:09', '2025-08-02 09:18:09', 2025, 3, 17, 'completed', NULL, NULL),
(11, 3, '160000.00', '2025-04-10', 'cash', NULL, NULL, '2025-08-02 09:18:31', '2025-08-02 09:18:31', 2025, 4, 18, 'completed', NULL, NULL),
(12, 3, '160000.00', '2025-06-09', 'cash', NULL, NULL, '2025-08-02 09:19:00', '2025-08-02 09:19:00', 2025, 6, 23, 'completed', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `package_id` bigint UNSIGNED NOT NULL,
  `network_type` enum('odp','wireless','htb') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'odp' COMMENT 'Network infrastructure type: odp (fiber), wireless (radio), htb (hotspot)',
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `router_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `odp_id` bigint UNSIGNED DEFAULT NULL,
  `odp_port` int DEFAULT NULL,
  `fiber_cable_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signal_strength` decimal(5,2) DEFAULT NULL,
  `wireless_equipment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `antenna_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency` decimal(8,2) DEFAULT NULL,
  `htb_server` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_point` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `installation_notes` text COLLATE utf8mb4_unicode_ci,
  `billing_cycle_id` bigint UNSIGNED DEFAULT NULL,
  `billing_day` int NOT NULL DEFAULT '1',
  `next_billing_date` date DEFAULT NULL,
  `last_billed_date` date DEFAULT NULL,
  `monthly_fee` decimal(10,2) DEFAULT NULL,
  `auto_billing` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `customer_id`, `package_id`, `network_type`, `ip_address`, `router_name`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`, `odp_id`, `odp_port`, `fiber_cable_color`, `signal_strength`, `wireless_equipment`, `antenna_type`, `frequency`, `htb_server`, `access_point`, `installation_notes`, `billing_cycle_id`, `billing_day`, `next_billing_date`, `last_billed_date`, `monthly_fee`, `auto_billing`) VALUES
(2, 7, 1, 'wireless', '10.10.1.45', 'Tenda', '2025-01-01', NULL, 'active', '2025-08-02 03:09:10', '2025-08-02 08:50:16', NULL, NULL, NULL, NULL, 'Mikrotik LHG ', 'sectoral', '5.80', NULL, NULL, NULL, 1, 1, '2025-10-01', '2025-09-01', NULL, 1),
(3, 8, 5, 'odp', NULL, NULL, '2025-08-02', NULL, 'active', '2025-08-02 07:45:44', '2025-08-02 08:50:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-11-01', '2025-10-01', NULL, 1),
(5, 10, 2, 'odp', NULL, NULL, '2025-08-02', NULL, 'active', '2025-08-02 10:03:29', '2025-08-02 21:35:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2025-10-01', '2025-09-01', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `service_applications`
--

CREATE TABLE `service_applications` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `package_id` bigint UNSIGNED NOT NULL,
  `installation_address` text COLLATE utf8mb4_unicode_ci,
  `installation_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_applications`
--

INSERT INTO `service_applications` (`id`, `customer_id`, `package_id`, `installation_address`, `installation_notes`, `status`, `admin_notes`, `approved_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(3, 8, 5, 'Jung kidul RT 006 RW 005, Dusun Jung kidul, RT 006/RW 005, Desa/Kel. Wonodadi, Kec. Plantungan, Kendal, Jawa tengah, 51362', NULL, 'approved', NULL, NULL, NULL, '2025-08-02 07:44:59', '2025-08-02 07:45:44'),
(4, 10, 2, 'Rt 11 RW 02 Desa Manggungmangu Kec Plantungan Kab Kendal, Dusun Parakan, RT 12/RW 1, Desa/Kel. Manggungmangu, Kec. Plantungan, KENDAL, JAWA TENGAH, 51362', NULL, 'approved', NULL, NULL, NULL, '2025-08-02 10:02:49', '2025-08-02 10:03:29');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `service_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `priority_id` bigint UNSIGNED NOT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `status` enum('open','in_progress','pending_customer','pending_vendor','resolved','closed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `contact_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email',
  `contact_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `technical_details` json DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requires_field_visit` tinyint(1) NOT NULL DEFAULT '0',
  `scheduled_visit_at` datetime DEFAULT NULL,
  `sla_due_at` datetime DEFAULT NULL,
  `escalation_due_at` datetime DEFAULT NULL,
  `is_escalated` tinyint(1) NOT NULL DEFAULT '0',
  `escalation_level` int NOT NULL DEFAULT '0',
  `first_response_at` datetime DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `customer_satisfaction_rating` int DEFAULT NULL,
  `customer_feedback` text COLLATE utf8mb4_unicode_ci,
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `actual_cost` decimal(10,2) DEFAULT NULL,
  `is_billable` tinyint(1) NOT NULL DEFAULT '0',
  `is_warranty` tinyint(1) NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `total_comments` int NOT NULL DEFAULT '0',
  `total_attachments` int NOT NULL DEFAULT '0',
  `last_activity_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_attachments`
--

CREATE TABLE `ticket_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `comment_id` bigint UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint NOT NULL,
  `file_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('image','document','video','audio','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_width` int DEFAULT NULL,
  `image_height` int DEFAULT NULL,
  `thumbnail_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_categories`
--

CREATE TABLE `ticket_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'heroicon-o-ticket',
  `default_priority_level` int NOT NULL DEFAULT '2',
  `default_sla_hours` int NOT NULL DEFAULT '24',
  `requires_technical_team` tinyint(1) NOT NULL DEFAULT '0',
  `auto_assign_to_department` tinyint(1) NOT NULL DEFAULT '0',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comments`
--

CREATE TABLE `ticket_comments` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_type` enum('staff','customer','system') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('comment','status_change','assignment_change','priority_change','category_change','resolution','escalation','system_note') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comment',
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `notify_customer` tinyint(1) NOT NULL DEFAULT '1',
  `notify_assigned_staff` tinyint(1) NOT NULL DEFAULT '1',
  `old_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `time_spent_minutes` int DEFAULT NULL,
  `is_billable_time` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_priorities`
--

CREATE TABLE `ticket_priorities` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `level` int NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'heroicon-o-exclamation-circle',
  `sla_hours` int NOT NULL,
  `escalation_hours` int DEFAULT NULL,
  `requires_immediate_notification` tinyint(1) NOT NULL DEFAULT '0',
  `send_whatsapp_notification` tinyint(1) NOT NULL DEFAULT '0',
  `send_email_notification` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_sla_tracking`
--

CREATE TABLE `ticket_sla_tracking` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `sla_start_time` datetime NOT NULL,
  `sla_due_time` datetime NOT NULL,
  `first_response_time` datetime DEFAULT NULL,
  `resolution_time` datetime DEFAULT NULL,
  `response_time_minutes` int DEFAULT NULL,
  `resolution_time_minutes` int DEFAULT NULL,
  `total_business_hours` int DEFAULT NULL,
  `total_calendar_hours` int DEFAULT NULL,
  `response_sla_met` tinyint(1) DEFAULT NULL,
  `resolution_sla_met` tinyint(1) DEFAULT NULL,
  `response_sla_breach_minutes` int DEFAULT NULL,
  `resolution_sla_breach_minutes` int DEFAULT NULL,
  `sla_paused_at` datetime DEFAULT NULL,
  `sla_resumed_at` datetime DEFAULT NULL,
  `total_paused_minutes` int NOT NULL DEFAULT '0',
  `pause_reason` text COLLATE utf8mb4_unicode_ci,
  `business_hours_config` json DEFAULT NULL,
  `was_escalated` tinyint(1) NOT NULL DEFAULT '0',
  `escalated_at` datetime DEFAULT NULL,
  `escalation_level` int NOT NULL DEFAULT '0',
  `escalation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(7, 'Administrator', 'admin@admin.com', '2025-08-02 07:05:32', '$2y$12$DzxcM5YPWY0lkerub9YR1O5FHXfgwZkqaSvAgfAHM4UrGIMdxCLKi', NULL, '2025-08-02 07:05:32', '2025-08-02 07:05:32'),
(8, 'Admin Baru', 'user@admin.com', NULL, '$2y$12$AYOM240VBGjxLF6Jyl.qLuunzVBLyJK5ZVWXhcfr8muaihRUlKIKK', NULL, '2025-08-02 07:07:31', '2025-08-02 07:12:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing_cycles`
--
ALTER TABLE `billing_cycles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_cycles_is_active_index` (`is_active`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD UNIQUE KEY `customers_customer_number_unique` (`customer_number`),
  ADD UNIQUE KEY `customers_customer_id_unique` (`customer_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD UNIQUE KEY `unique_service_billing_period` (`service_id`,`billing_period_start`,`billing_period_end`),
  ADD KEY `invoices_customer_id_status_index` (`customer_id`,`status`),
  ADD KEY `invoices_due_date_status_index` (`due_date`,`status`),
  ADD KEY `invoices_invoice_date_index` (`invoice_date`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_invoice_id_index` (`invoice_id`),
  ADD KEY `invoice_items_type_index` (`type`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `odps`
--
ALTER TABLE `odps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `odps_name_unique` (`name`),
  ADD UNIQUE KEY `odps_code_unique` (`code`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_service_id_foreign` (`service_id`),
  ADD KEY `payments_invoice_id_index` (`invoice_id`),
  ADD KEY `payments_status_index` (`status`),
  ADD KEY `payments_transaction_id_index` (`transaction_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_customer_id_foreign` (`customer_id`),
  ADD KEY `services_package_id_foreign` (`package_id`),
  ADD KEY `services_odp_id_foreign` (`odp_id`),
  ADD KEY `services_billing_cycle_id_foreign` (`billing_cycle_id`),
  ADD KEY `services_next_billing_date_index` (`next_billing_date`),
  ADD KEY `services_auto_billing_index` (`auto_billing`);

--
-- Indexes for table `service_applications`
--
ALTER TABLE `service_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_applications_customer_id_foreign` (`customer_id`),
  ADD KEY `service_applications_package_id_foreign` (`package_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`),
  ADD KEY `tickets_service_id_foreign` (`service_id`),
  ADD KEY `tickets_created_by_foreign` (`created_by`),
  ADD KEY `tickets_status_created_at_index` (`status`,`created_at`),
  ADD KEY `tickets_customer_id_status_index` (`customer_id`,`status`),
  ADD KEY `tickets_assigned_to_status_index` (`assigned_to`,`status`),
  ADD KEY `tickets_category_id_status_index` (`category_id`,`status`),
  ADD KEY `tickets_priority_id_status_index` (`priority_id`,`status`),
  ADD KEY `tickets_sla_due_at_index` (`sla_due_at`),
  ADD KEY `tickets_escalation_due_at_index` (`escalation_due_at`);

--
-- Indexes for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_attachments_comment_id_foreign` (`comment_id`),
  ADD KEY `ticket_attachments_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `ticket_attachments_ticket_id_type_index` (`ticket_id`,`type`),
  ADD KEY `ticket_attachments_file_hash_index` (`file_hash`);

--
-- Indexes for table `ticket_categories`
--
ALTER TABLE `ticket_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_categories_slug_unique` (`slug`);

--
-- Indexes for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_comments_user_id_foreign` (`user_id`),
  ADD KEY `ticket_comments_ticket_id_created_at_index` (`ticket_id`,`created_at`),
  ADD KEY `ticket_comments_author_type_is_public_index` (`author_type`,`is_public`);

--
-- Indexes for table `ticket_priorities`
--
ALTER TABLE `ticket_priorities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_priorities_slug_unique` (`slug`);

--
-- Indexes for table `ticket_sla_tracking`
--
ALTER TABLE `ticket_sla_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_sla_tracking_ticket_id_foreign` (`ticket_id`),
  ADD KEY `ticket_sla_tracking_sla_due_time_index` (`sla_due_time`),
  ADD KEY `ticket_sla_tracking_response_sla_met_resolution_sla_met_index` (`response_sla_met`,`resolution_sla_met`),
  ADD KEY `ticket_sla_tracking_was_escalated_index` (`was_escalated`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing_cycles`
--
ALTER TABLE `billing_cycles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `odps`
--
ALTER TABLE `odps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_applications`
--
ALTER TABLE `service_applications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_categories`
--
ALTER TABLE `ticket_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_priorities`
--
ALTER TABLE `ticket_priorities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_sla_tracking`
--
ALTER TABLE `ticket_sla_tracking`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_billing_cycle_id_foreign` FOREIGN KEY (`billing_cycle_id`) REFERENCES `billing_cycles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `services_odp_id_foreign` FOREIGN KEY (`odp_id`) REFERENCES `odps` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_applications`
--
ALTER TABLE `service_applications`
  ADD CONSTRAINT `service_applications_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_applications_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `ticket_categories` (`id`),
  ADD CONSTRAINT `tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tickets_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_priority_id_foreign` FOREIGN KEY (`priority_id`) REFERENCES `ticket_priorities` (`id`),
  ADD CONSTRAINT `tickets_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD CONSTRAINT `ticket_attachments_comment_id_foreign` FOREIGN KEY (`comment_id`) REFERENCES `ticket_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_attachments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD CONSTRAINT `ticket_comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_sla_tracking`
--
ALTER TABLE `ticket_sla_tracking`
  ADD CONSTRAINT `ticket_sla_tracking_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
