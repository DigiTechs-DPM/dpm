-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2026 at 01:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digiceze_dpm_crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_keys`
--

CREATE TABLE `account_keys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module` enum('upwork','ppc') NOT NULL DEFAULT 'ppc',
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_url` varchar(255) DEFAULT NULL,
  `stripe_publishable_key` text DEFAULT NULL,
  `stripe_secret_key` text DEFAULT NULL,
  `stripe_webhook_secret` varchar(255) DEFAULT NULL,
  `paypal_client_id` text DEFAULT NULL,
  `paypal_secret` text DEFAULT NULL,
  `paypal_webhook_id` varchar(255) DEFAULT NULL,
  `paypal_base_url` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_keys`
--

INSERT INTO `account_keys` (`id`, `module`, `brand_id`, `brand_url`, `stripe_publishable_key`, `stripe_secret_key`, `stripe_webhook_secret`, `paypal_client_id`, `paypal_secret`, `paypal_webhook_id`, `paypal_base_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ppc', 1, 'https://devxperts.pro', 'pk_test_51S8qzbB4ZIiKXJ30QIfKcWb72VcKFrwA4R43vIreM77iR33E5ytDOXYTcVezvKCwthwLMTApeLiiWwLchXAFM1G300McayCYXC', 'sk_test_51S8qzbB4ZIiKXJ30LfKZsYH9oUnymJYMECFAY9pUJFKZXFiSocI577Ip3SaZB8GMbmKznP1Ceo5l2uwdh8xj1ToU00YDWZYMUz', 'whsec_YX8OagV4VjKQjiiXtfLWcLAWbGp3hpAR', 'AcWgUwxg7dL2xRxZ9o_HHTq0y5juSwu9eScuhdpVvRDNfS41Z0wPGcejqURjkI8ItUDJjvquQom7Mt9a', 'ELcVFpYHLi4IzuNhTCHKomYbumOPbzeqd0IfXdjwsFmZIZzAvTrtPYBHqoNYhxm_wrybwOdjoqzqhovv', '1GC98337J2874292M', 'https://api-m.sandbox.paypal.com', 'active', '2025-10-22 22:44:45', '2025-12-18 22:10:20'),
(2, 'upwork', 2, 'https://apexcreativelab.com/', 'pk_test_51S8qzbB4ZIiKXJ30QIfKcWb72VcKFrwA4R43vIreM77iR33E5ytDOXYTcVezvKCwthwLMTApeLiiWwLchXAFM1G300McayCYXC', 'sk_test_51S8qzbB4ZIiKXJ30LfKZsYH9oUnymJYMECFAY9pUJFKZXFiSocI577Ip3SaZB8GMbmKznP1Ceo5l2uwdh8xj1ToU00YDWZYMUz', 'whsec_YX8OagV4VjKQjiiXtfLWcLAWbGp3hpAR', 'AcWgUwxg7dL2xRxZ9o_HHTq0y5juSwu9eScuhdpVvRDNfS41Z0wPGcejqURjkI8ItUDJjvquQom7Mt9a', 'ELcVFpYHLi4IzuNhTCHKomYbumOPbzeqd0IfXdjwsFmZIZzAvTrtPYBHqoNYhxm_wrybwOdjoqzqhovv', '1GC98337J2874292M', 'https://api-m.sandbox.paypal.com', 'active', '2025-10-22 22:44:45', '2025-12-18 22:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','finance','up_admin') NOT NULL DEFAULT 'admin',
  `last_seen` timestamp NULL DEFAULT NULL,
  `meta` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `last_seen`, `meta`, `created_at`, `updated_at`) VALUES
(1, 'Zaryth Alpharos', 'zaryth@admin.pro', '$2y$12$vub5x0vk40DWsjOoUu4a1ebQVnMR.pIEYcBLv/ZItITMZ5HbTwUg6', 'super_admin', '2025-11-04 18:04:08', NULL, '2025-10-22 22:17:03', '2025-11-04 18:04:08'),
(2, 'Finance Manager', 'finance@digiprojectmanagers.com', '$2y$12$vub5x0vk40DWsjOoUu4a1ebQVnMR.pIEYcBLv/ZItITMZ5HbTwUg6', 'finance', '2025-11-04 20:37:12', NULL, '2025-10-22 22:17:03', '2025-11-04 20:37:12'),
(3, 'Digi Techs', 'info@digiprojectmanagers.com', '$2y$12$vub5x0vk40DWsjOoUu4a1ebQVnMR.pIEYcBLv/ZItITMZ5HbTwUg6', 'admin', '2026-02-17 05:38:43', NULL, '2025-10-22 22:17:03', '2026-02-17 05:38:43'),
(4, 'Upwork Admin', 'upwork@admin.com', '$2y$12$vub5x0vk40DWsjOoUu4a1ebQVnMR.pIEYcBLv/ZItITMZ5HbTwUg6', 'up_admin', '2026-02-17 06:14:04', NULL, '2025-10-22 22:17:03', '2026-02-17 06:14:04');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module` enum('upwork','ppc') NOT NULL DEFAULT 'ppc',
  `brand_name` varchar(255) NOT NULL,
  `brand_url` varchar(255) NOT NULL,
  `brand_host` varchar(255) DEFAULT NULL,
  `allowed_origins` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_origins`)),
  `public_form_token` varchar(255) DEFAULT NULL,
  `webhook_secret` varchar(255) DEFAULT NULL,
  `require_hmac` tinyint(1) NOT NULL DEFAULT 0,
  `lead_script` longtext DEFAULT NULL,
  `field_mapping` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`field_mapping`)),
  `status` enum('Pending','Active') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `module`, `brand_name`, `brand_url`, `brand_host`, `allowed_origins`, `public_form_token`, `webhook_secret`, `require_hmac`, `lead_script`, `field_mapping`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ppc', 'DevXperts', 'https://devxperts.pro/', 'devxperts.pro', '[\"devxperts.pro\"]', '8d3ffaab1eaa4d2faaf97b114aab53debdaea92dac96eee8', 'c66a6108ecb0fda3e150b0e96c0f9922bca8b2fe1c9cd94b29e7ae1510d79540', 0, NULL, NULL, 'Active', '2025-11-27 06:01:12', '2025-11-27 06:01:12'),
(2, 'upwork', 'Apex Creative Lab', 'https://apexcreativelab.com/', 'apexcreativelab.com', '[\"apexcreativelab.com\"]', '82bfccc65aa9a4d4bb65e79f943b069e445a9d7c61400a5b', 'f4ae2c3e5f186654e4db2ac6ed183cb7eed5701ab48637a7eddcd792e8de31d1', 0, NULL, NULL, 'Active', '2026-02-07 05:28:57', '2026-02-07 05:28:57');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `last_seen` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_tickets`
--

CREATE TABLE `client_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','on_hold','resolved','closed','reopened') NOT NULL DEFAULT 'open',
  `source` varchar(255) NOT NULL DEFAULT 'crm',
  `is_client_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_internal` tinyint(1) NOT NULL DEFAULT 0,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `status` enum('new','contacted','qualified','proposal_sent','first_paid','in_progress','completed','renewal_due','on_hold','disqualified','cancelled') NOT NULL DEFAULT 'new',
  `auto_replied` tinyint(1) NOT NULL DEFAULT 0,
  `converted_at` timestamp NULL DEFAULT NULL,
  `domain_url` varchar(255) DEFAULT NULL,
  `prediction` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prediction`)),
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `is_finish` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_assignments`
--

CREATE TABLE `lead_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to` bigint(20) UNSIGNED NOT NULL,
  `assigned_role` varchar(255) NOT NULL,
  `assigned_by` bigint(20) UNSIGNED NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','assigned','in_progress','on_hold','completed','refund_requested','chargeback','rejected_by_client','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_13_171524_create_brands_table', 1),
(5, '2025_07_14_175826_create_clients_table', 1),
(6, '2025_07_15_181835_create_sellers_table', 1),
(7, '2025_07_31_170523_create_admins_table', 1),
(8, '2025_07_31_171848_create_leads_table', 1),
(9, '2025_08_13_183153_create_orders_table', 1),
(10, '2025_08_14_164525_create_payment_links_table', 1),
(11, '2025_08_18_205308_create_personal_access_tokens_table', 1),
(12, '2025_08_23_170645_create_payments_table', 1),
(13, '2025_09_10_174357_create_profile_details_table', 1),
(14, '2025_09_10_225122_create_risky_clients_table', 1),
(15, '2025_09_15_172233_create_lead_assignments_table', 1),
(16, '2025_09_26_223326_create_account_keys_table', 1),
(17, '2025_10_02_172544_create_projects_table', 1),
(18, '2025_10_02_172551_create_project_tasks_table', 1),
(19, '2025_10_13_173437_create_client_tickets_table', 1),
(20, '2025_10_14_193705_create_performance_bonuses_table', 1),
(21, '2025_11_05_232117_create_questionnairs_table', 1),
(26, '2026_01_30_181203_create_upwork_clients_table', 2),
(27, '2026_01_30_181234_create_upwork_orders_table', 2),
(28, '2026_01_30_181237_create_upwork_payment_links_table', 2),
(29, '2026_01_30_181241_create_upwork_payments_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `parent_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_type` enum('original','renewal') NOT NULL DEFAULT 'original',
  `service_name` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `unit_amount` int(10) UNSIGNED NOT NULL,
  `amount_paid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `balance_due` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('draft','pending','paid','in_progress','revision','completed','refunded','canceled') NOT NULL DEFAULT 'draft',
  `front_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opened_by_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `front_credits_used` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `front_credited_cents` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `first_paid_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `refunded_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `refund_status` enum('none','partial','full','chargeback') NOT NULL DEFAULT 'none',
  `provider_session_id` varchar(255) DEFAULT NULL,
  `provider_payment_intent_id` varchar(255) DEFAULT NULL,
  `buyer_name` varchar(255) DEFAULT NULL,
  `buyer_email` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `payment_link_id` bigint(20) UNSIGNED DEFAULT NULL,
  `credit_to_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `front_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `credited_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `status` enum('pending','succeeded','failed','refunded','partially_refunded') NOT NULL DEFAULT 'pending',
  `provider` varchar(255) NOT NULL DEFAULT 'stripe',
  `provider_payment_intent_id` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `refunded_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `refund_status` enum('none','partial','full','chargeback') NOT NULL DEFAULT 'none',
  `refund_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`refund_payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_links`
--

CREATE TABLE `payment_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `credit_to_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generated_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generated_by_type` varchar(30) DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `provider` enum('stripe','paypal') NOT NULL DEFAULT 'stripe',
  `unit_amount` int(10) UNSIGNED NOT NULL,
  `order_total_snapshot` int(10) UNSIGNED DEFAULT NULL,
  `provider_session_id` varchar(255) DEFAULT NULL,
  `provider_payment_intent_id` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `status` enum('draft','active','paid','completed','canceled','expired') NOT NULL DEFAULT 'active',
  `expires_at` varchar(255) DEFAULT NULL,
  `is_active_link` tinyint(1) NOT NULL DEFAULT 1,
  `last_issued_url` text DEFAULT NULL,
  `last_issued_at` timestamp NULL DEFAULT NULL,
  `last_issued_expires_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance_bonuses`
--

CREATE TABLE `performance_bonuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_revenue` decimal(12,2) NOT NULL,
  `bonus_amount` decimal(12,2) NOT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile_details`
--

CREATE TABLE `profile_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `profile` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `alternate_email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `front_seller_id` bigint(20) UNSIGNED NOT NULL,
  `owner_seller_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `pm_assigned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_tasks`
--

CREATE TABLE `project_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','in_progress','completed','blocked') NOT NULL DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'low',
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questionnairs`
--

CREATE TABLE `questionnairs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `brief_token` char(36) DEFAULT NULL,
  `brief_token_expires_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','progress','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `risky_clients`
--

CREATE TABLE `risky_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `risk_level` enum('low','medium','high') NOT NULL,
  `risk_score` decimal(5,2) DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `sudo_name` varchar(255) DEFAULT NULL,
  `is_seller` enum('project_manager','front_seller') NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `last_seen` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `brand_id`, `name`, `sudo_name`, `is_seller`, `email`, `password`, `status`, `last_seen`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'James', 'james', 'front_seller', 'jamesrrogerrs@gmail.com', '$2y$12$dVypHE6rAOvOftW4SAYkZeNZrUmEHYR7/5J5.z0F/wratiDfcfLBK', 'Active', NULL, '2026-02-07 06:19:52', '2026-02-07 06:19:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('3yxB27Ny2ZtCeJluCvghQ5pdEyUoJnMu7Gk9xgrV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUU4zVHRWZmtZZjd6bFFKeEhTTHJUVTZTUndHS25BSkhybUVSaWtFaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcGF5L25vdy9PemZsdEZuTVpyV1FxTVkwS055dW5TNmRiNEFCTkpHY0FUNFNGT1VmMzk0eUE1VlI/ZXhwaXJlcz0xNzcxODg1MDQ2JnA9ZXlKcGRpSTZJazF0UVhBd1dqbFFjRXBaY1V4cVozcENlRlY2TW1jOVBTSXNJblpoYkhWbElqb2lSUzh6UjNoNlZYbFhjRXhEU0U5MlZTdEdURkJyYlVrMVFqQnpVMWN4Y0V0emRYZFJZMG92T1dKeVMxQnNiWFJrTWs5Q05HOVRkVkp2TkRGNlpWaEhXWGhZYkZGQ2VraFhNbU5YVVZWYVJXSTJSWEl5YjFoMmREQk5aSFpMZFhoNFFqbFdkRll2ZW1ZNU4ydDJNQ3RZT1hJclp5dE9ZM0JoVDFReVREVmxVMjByVlhSNEsyZEpPWFpETkZGVVpWY3ZWaXRQZDFsRWVtRlJSV3N4VWsxWVdHcHliakpwVVROcllrOUxhMll2VGxwb1RtWkZVa2hhUlRsNVJuUlVRVlJHSWl3aWJXRmpJam9pWlRJME5HSXlZV0kzWkdFMU5qTmlZV1ZsWWpFd09HUm1NR014TW1NMVlqWTRZMlkwT1RjMVlUTTRZakJtWTJNd1pqbGpNMlJsWXpBM09XUXlOV1kyT1NJc0luUmhaeUk2SWlKOSZzaWduYXR1cmU9MzQ0MTA5MWQzYzhkMWUyY2JmYTc2ZThiZWNmNTUzN2Q4NGVhYjdhMmEyMDFkN2Q5ZWQxNDEyMDliMDg2YWJjZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771280272),
('lLPHprqV4fruahE1V44mRamb5EYS2DYWZruJlfue', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmJRUkN5Q0ZLMEFJRTcwSTN1Ukd1b1F5dEx6NkNDSzRRbEM0aVFINiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC91cHdvcmsvbG9naW4iO319', 1771287244);

-- --------------------------------------------------------

--
-- Table structure for table `upwork_clients`
--

CREATE TABLE `upwork_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upwork_clients`
--

INSERT INTO `upwork_clients` (`id`, `brand_id`, `name`, `email`, `password`, `phone`, `meta`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Liberty Meyer', 'qasudov@mailinator.com', NULL, '+1 (918) 179-2258', NULL, 'Active', '2026-02-17 04:58:56', '2026-02-17 04:58:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `upwork_orders`
--

CREATE TABLE `upwork_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `parent_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_type` enum('original','renewal') NOT NULL DEFAULT 'original',
  `sell_type` enum('front','upsell') NOT NULL DEFAULT 'upsell',
  `service_name` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `unit_amount` int(10) UNSIGNED NOT NULL,
  `amount_paid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `balance_due` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('draft','pending','paid','in_progress','revision','completed','refunded','canceled') NOT NULL DEFAULT 'draft',
  `paid_at` timestamp NULL DEFAULT NULL,
  `refunded_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `refund_status` enum('none','partial','full','chargeback') NOT NULL DEFAULT 'none',
  `provider_session_id` varchar(255) DEFAULT NULL,
  `provider_payment_intent_id` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upwork_orders`
--

INSERT INTO `upwork_orders` (`id`, `brand_id`, `client_id`, `parent_order_id`, `order_type`, `sell_type`, `service_name`, `currency`, `unit_amount`, `amount_paid`, `balance_due`, `status`, `paid_at`, `refunded_amount`, `refund_status`, `provider_session_id`, `provider_payment_intent_id`, `meta`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, NULL, 'original', 'upsell', 'Social Media Marketing', 'USD', 4500, 4500, 0, 'paid', '2026-02-17 05:58:52', 0, 'none', 'cs_test_a1iHDyGMwNuNWnOb3rYGpKzEW8KhxshMcYwvOUgNSgHXqlUCZekal9uaco', 'pi_3T1bgYB4ZIiKXJ3010fbi6Tu', NULL, '2026-02-17 04:58:56', '2026-02-17 05:58:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `upwork_payments`
--

CREATE TABLE `upwork_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_link_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `status` enum('pending','succeeded','failed','refunded','partially_refunded') NOT NULL DEFAULT 'pending',
  `provider` varchar(255) NOT NULL DEFAULT 'stripe',
  `provider_payment_intent_id` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `refunded_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `refund_status` enum('none','partial','full','chargeback') NOT NULL DEFAULT 'none',
  `refund_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`refund_payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upwork_payments`
--

INSERT INTO `upwork_payments` (`id`, `order_id`, `payment_link_id`, `amount`, `currency`, `status`, `provider`, `provider_payment_intent_id`, `payload`, `refunded_amount`, `refund_status`, `refund_payload`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 4500, 'USD', 'succeeded', 'stripe', 'pi_3T1bgYB4ZIiKXJ3010fbi6Tu', '{\"id\":\"cs_test_a1iHDyGMwNuNWnOb3rYGpKzEW8KhxshMcYwvOUgNSgHXqlUCZekal9uaco\",\"object\":\"checkout.session\",\"adaptive_pricing\":{\"enabled\":true},\"after_expiration\":null,\"allow_promotion_codes\":null,\"amount_subtotal\":4500,\"amount_total\":4500,\"automatic_tax\":{\"enabled\":false,\"liability\":null,\"provider\":null,\"status\":null},\"billing_address_collection\":\"required\",\"branding_settings\":{\"background_color\":\"#ffffff\",\"border_style\":\"rounded\",\"button_color\":\"#0074d4\",\"display_name\":\"DevXperts sandbox\",\"font_family\":\"default\",\"icon\":null,\"logo\":null},\"cancel_url\":\"http:\\/\\/127.0.0.1:8000\\/pay\\/now\\/xDww1MQ4V4d70QgFB0n7h4qRqQP0VBYaneimuRWn289NMoz3\\/cancel?canceled=1\",\"client_reference_id\":null,\"client_secret\":null,\"collected_information\":{\"business_name\":null,\"individual_name\":null,\"shipping_details\":null},\"consent\":null,\"consent_collection\":null,\"created\":1771286309,\"currency\":\"usd\",\"currency_conversion\":null,\"custom_fields\":[],\"custom_text\":{\"after_submit\":null,\"shipping_address\":null,\"submit\":null,\"terms_of_service_acceptance\":null},\"customer\":null,\"customer_account\":null,\"customer_creation\":\"if_required\",\"customer_details\":{\"address\":{\"city\":\"Khi\",\"country\":\"PK\",\"line1\":\"Testing\",\"line2\":\"testing 2, Subcribes to the web\",\"postal_code\":\"10001\",\"state\":null},\"business_name\":null,\"email\":\"qasudov@mailinator.com\",\"individual_name\":null,\"name\":\"Steven\",\"phone\":null,\"tax_exempt\":\"none\",\"tax_ids\":[]},\"customer_email\":\"qasudov@mailinator.com\",\"discounts\":[],\"expires_at\":1771372708,\"invoice\":null,\"invoice_creation\":{\"enabled\":false,\"invoice_data\":{\"account_tax_ids\":null,\"custom_fields\":null,\"description\":null,\"footer\":null,\"issuer\":null,\"metadata\":[],\"rendering_options\":null}},\"livemode\":false,\"locale\":null,\"metadata\":{\"brand_id\":\"2\",\"module\":\"upwork\",\"payment_link_token\":\"xDww1MQ4V4d70QgFB0n7h4qRqQP0VBYaneimuRWn289NMoz3\",\"upwork_link_id\":\"1\",\"upwork_order_id\":\"1\"},\"mode\":\"payment\",\"origin_context\":null,\"payment_intent\":\"pi_3T1bgYB4ZIiKXJ3010fbi6Tu\",\"payment_link\":null,\"payment_method_collection\":\"if_required\",\"payment_method_configuration_details\":{\"id\":\"pmc_1S8r07B4ZIiKXJ30znpJ0yd8\",\"parent\":null},\"payment_method_options\":{\"card\":{\"request_three_d_secure\":\"automatic\"}},\"payment_method_types\":[\"card\",\"link\",\"cashapp\",\"amazon_pay\"],\"payment_status\":\"paid\",\"permissions\":null,\"phone_number_collection\":{\"enabled\":false},\"recovered_from\":null,\"saved_payment_method_options\":null,\"setup_intent\":null,\"shipping_address_collection\":null,\"shipping_cost\":null,\"shipping_options\":[],\"status\":\"complete\",\"submit_type\":null,\"subscription\":null,\"success_url\":\"http:\\/\\/127.0.0.1:8000\\/pay\\/now\\/xDww1MQ4V4d70QgFB0n7h4qRqQP0VBYaneimuRWn289NMoz3\\/success?session_id={CHECKOUT_SESSION_ID}\",\"total_details\":{\"amount_discount\":0,\"amount_shipping\":0,\"amount_tax\":0},\"ui_mode\":\"hosted\",\"url\":null,\"wallet_options\":null}', 0, 'none', NULL, '2026-02-17 05:58:52', '2026-02-17 05:58:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `upwork_payment_links`
--

CREATE TABLE `upwork_payment_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `generated_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generated_by_type` varchar(30) DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `provider` enum('stripe','paypal') NOT NULL DEFAULT 'stripe',
  `unit_amount` int(10) UNSIGNED NOT NULL,
  `order_total_snapshot` int(10) UNSIGNED DEFAULT NULL,
  `provider_session_id` varchar(255) DEFAULT NULL,
  `provider_payment_intent_id` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `status` enum('draft','active','paid','completed','canceled','expired') NOT NULL DEFAULT 'active',
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active_link` tinyint(1) NOT NULL DEFAULT 1,
  `last_issued_url` text DEFAULT NULL,
  `last_issued_at` timestamp NULL DEFAULT NULL,
  `last_issued_expires_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upwork_payment_links`
--

INSERT INTO `upwork_payment_links` (`id`, `brand_id`, `order_id`, `client_id`, `generated_by_id`, `generated_by_type`, `service_name`, `currency`, `provider`, `unit_amount`, `order_total_snapshot`, `provider_session_id`, `provider_payment_intent_id`, `token`, `status`, `expires_at`, `is_active_link`, `last_issued_url`, `last_issued_at`, `last_issued_expires_at`, `paid_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 1, 4, 'Admin', 'Social Media Marketing', 'USD', 'stripe', 4500, 4500, 'cs_test_a1iHDyGMwNuNWnOb3rYGpKzEW8KhxshMcYwvOUgNSgHXqlUCZekal9uaco', 'pi_3T1bgYB4ZIiKXJ3010fbi6Tu', 'xDww1MQ4V4d70QgFB0n7h4qRqQP0VBYaneimuRWn289NMoz3', 'paid', '2026-02-17 05:58:52', 0, 'http://127.0.0.1:8000/pay/now/xDww1MQ4V4d70QgFB0n7h4qRqQP0VBYaneimuRWn289NMoz3?expires=1771887536&p=eyJpdiI6Ii9DeEpiQzJNVkN0TldVV3prWCtBeGc9PSIsInZhbHVlIjoieTk3R2pHa0xWdi9XblhLYy82aWlQT0FQYnFEdlhkd0o4SnM1T1RWR1Vsd2Vmbm5KMFVHanJCZmFTdGhCMzBxWW55MGtiM2E0Q2lKT1FLeStmc0dUbnpob1E1TC9XNm9RS1dJVmRrWk5sNG16SHBzUEVROVlqVFFrdzBBa0tNRUYvRDRYUldZcGpPK2M3Z28zZkM3bk9oVW4vcnBMak5QR2pYYkt6L0Fmelh3PSIsIm1hYyI6IjYwY2YwMjY2MDNhOWFlNzM5NTIyOWJmZWFjNzAyMmFmYTA1ZTJmYzRjZTZjOTQ5OGVjNGMwZjA4NWViMmU4NWIiLCJ0YWciOiIifQ%3D%3D&signature=151faa8190d2f5edfe02d2e45ba0afe07a8f3259a2a4b670c765fe6785a8580e', '2026-02-17 04:58:56', '2026-02-17 07:58:56', '2026-02-17 05:58:52', '2026-02-17 04:58:56', '2026-02-17 05:58:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '0',
  `email_verified_at` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `status`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', '$2y$12$axHI2QWefwg54L1Jfnh9TOPV1fX3qgfWuPyscU8.EwMOgGf38G4ry', '0', '2026-02-06 21:03:45', 'LLHqxlL3UU', '2026-02-07 03:03:45', '2026-02-07 03:03:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_keys`
--
ALTER TABLE `account_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_keys_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brands_brand_host_index` (`brand_host`),
  ADD KEY `brands_require_hmac_index` (`require_hmac`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_email_unique` (`email`);

--
-- Indexes for table `client_tickets`
--
ALTER TABLE `client_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_tickets_brand_id_foreign` (`brand_id`),
  ADD KEY `client_tickets_client_id_foreign` (`client_id`),
  ADD KEY `client_tickets_seller_id_foreign` (`seller_id`),
  ADD KEY `client_tickets_order_id_foreign` (`order_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_seller_id_foreign` (`seller_id`),
  ADD KEY `leads_client_id_foreign` (`client_id`),
  ADD KEY `leads_brand_id_seller_id_status_created_at_index` (`brand_id`,`seller_id`,`status`,`created_at`);

--
-- Indexes for table `lead_assignments`
--
ALTER TABLE `lead_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_assignments_lead_id_foreign` (`lead_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_lead_id_foreign` (`lead_id`),
  ADD KEY `orders_brand_id_foreign` (`brand_id`),
  ADD KEY `orders_seller_id_foreign` (`seller_id`),
  ADD KEY `orders_client_id_foreign` (`client_id`),
  ADD KEY `orders_parent_order_id_foreign` (`parent_order_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_provider_provider_payment_intent_id_unique` (`provider`,`provider_payment_intent_id`),
  ADD KEY `payments_order_id_foreign` (`order_id`),
  ADD KEY `payments_payment_link_id_foreign` (`payment_link_id`),
  ADD KEY `payments_provider_payment_intent_id_index` (`provider_payment_intent_id`);

--
-- Indexes for table `payment_links`
--
ALTER TABLE `payment_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_links_token_unique` (`token`),
  ADD KEY `payment_links_lead_id_foreign` (`lead_id`),
  ADD KEY `payment_links_seller_id_foreign` (`seller_id`),
  ADD KEY `payment_links_brand_id_foreign` (`brand_id`),
  ADD KEY `payment_links_client_id_foreign` (`client_id`),
  ADD KEY `payment_links_order_id_foreign` (`order_id`);

--
-- Indexes for table `performance_bonuses`
--
ALTER TABLE `performance_bonuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_bonuses_seller_id_foreign` (`seller_id`),
  ADD KEY `performance_bonuses_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `profile_details`
--
ALTER TABLE `profile_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `profile_details_email_unique` (`email`),
  ADD KEY `profile_details_user_type_user_id_index` (`user_type`,`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_lead_id_foreign` (`lead_id`),
  ADD KEY `projects_order_id_foreign` (`order_id`),
  ADD KEY `projects_front_seller_id_foreign` (`front_seller_id`),
  ADD KEY `projects_owner_seller_id_foreign` (`owner_seller_id`);

--
-- Indexes for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_tasks_project_id_foreign` (`project_id`),
  ADD KEY `project_tasks_assigned_to_foreign` (`assigned_to`);

--
-- Indexes for table `questionnairs`
--
ALTER TABLE `questionnairs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questionnairs_client_id_foreign` (`client_id`),
  ADD KEY `questionnairs_order_id_foreign` (`order_id`);

--
-- Indexes for table `risky_clients`
--
ALTER TABLE `risky_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `risky_clients_client_id_foreign` (`client_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sellers_email_unique` (`email`),
  ADD KEY `sellers_brand_id_foreign` (`brand_id`),
  ADD KEY `sellers_is_seller_index` (`is_seller`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `upwork_clients`
--
ALTER TABLE `upwork_clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `upwork_clients_email_unique` (`email`),
  ADD KEY `upwork_clients_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `upwork_orders`
--
ALTER TABLE `upwork_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upwork_orders_brand_id_foreign` (`brand_id`),
  ADD KEY `upwork_orders_client_id_foreign` (`client_id`),
  ADD KEY `upwork_orders_parent_order_id_foreign` (`parent_order_id`);

--
-- Indexes for table `upwork_payments`
--
ALTER TABLE `upwork_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `upwork_payments_provider_provider_payment_intent_id_unique` (`provider`,`provider_payment_intent_id`),
  ADD KEY `upwork_payments_order_id_foreign` (`order_id`),
  ADD KEY `upwork_payments_payment_link_id_foreign` (`payment_link_id`),
  ADD KEY `upwork_payments_provider_payment_intent_id_index` (`provider_payment_intent_id`);

--
-- Indexes for table `upwork_payment_links`
--
ALTER TABLE `upwork_payment_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `upwork_payment_links_token_unique` (`token`),
  ADD KEY `upwork_payment_links_brand_id_foreign` (`brand_id`),
  ADD KEY `upwork_payment_links_order_id_foreign` (`order_id`),
  ADD KEY `upwork_payment_links_client_id_foreign` (`client_id`);

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
-- AUTO_INCREMENT for table `account_keys`
--
ALTER TABLE `account_keys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_tickets`
--
ALTER TABLE `client_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_assignments`
--
ALTER TABLE `lead_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_links`
--
ALTER TABLE `payment_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance_bonuses`
--
ALTER TABLE `performance_bonuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile_details`
--
ALTER TABLE `profile_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_tasks`
--
ALTER TABLE `project_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questionnairs`
--
ALTER TABLE `questionnairs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `risky_clients`
--
ALTER TABLE `risky_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `upwork_clients`
--
ALTER TABLE `upwork_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `upwork_orders`
--
ALTER TABLE `upwork_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `upwork_payments`
--
ALTER TABLE `upwork_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `upwork_payment_links`
--
ALTER TABLE `upwork_payment_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_keys`
--
ALTER TABLE `account_keys`
  ADD CONSTRAINT `account_keys_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_tickets`
--
ALTER TABLE `client_tickets`
  ADD CONSTRAINT `client_tickets_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `client_tickets_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `client_tickets_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `client_tickets_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leads_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lead_assignments`
--
ALTER TABLE `lead_assignments`
  ADD CONSTRAINT `lead_assignments_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_parent_order_id_foreign` FOREIGN KEY (`parent_order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_payment_link_id_foreign` FOREIGN KEY (`payment_link_id`) REFERENCES `payment_links` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_links`
--
ALTER TABLE `payment_links`
  ADD CONSTRAINT `payment_links_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_links_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_links_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_links_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_links_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performance_bonuses`
--
ALTER TABLE `performance_bonuses`
  ADD CONSTRAINT `performance_bonuses_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_bonuses_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_front_seller_id_foreign` FOREIGN KEY (`front_seller_id`) REFERENCES `sellers` (`id`),
  ADD CONSTRAINT `projects_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_owner_seller_id_foreign` FOREIGN KEY (`owner_seller_id`) REFERENCES `sellers` (`id`);

--
-- Constraints for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `project_tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `sellers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questionnairs`
--
ALTER TABLE `questionnairs`
  ADD CONSTRAINT `questionnairs_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questionnairs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risky_clients`
--
ALTER TABLE `risky_clients`
  ADD CONSTRAINT `risky_clients_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `upwork_clients`
--
ALTER TABLE `upwork_clients`
  ADD CONSTRAINT `upwork_clients_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `upwork_orders`
--
ALTER TABLE `upwork_orders`
  ADD CONSTRAINT `upwork_orders_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `upwork_orders_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `upwork_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upwork_orders_parent_order_id_foreign` FOREIGN KEY (`parent_order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `upwork_payments`
--
ALTER TABLE `upwork_payments`
  ADD CONSTRAINT `upwork_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `upwork_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upwork_payments_payment_link_id_foreign` FOREIGN KEY (`payment_link_id`) REFERENCES `upwork_payment_links` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `upwork_payment_links`
--
ALTER TABLE `upwork_payment_links`
  ADD CONSTRAINT `upwork_payment_links_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `upwork_payment_links_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `upwork_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upwork_payment_links_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `upwork_orders` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
