-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 06, 2026 at 03:49 PM
-- Server version: 11.4.9-MariaDB-cll-lve
-- PHP Version: 8.3.29

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

INSERT INTO `account_keys` (`id`, `brand_id`, `brand_url`, `stripe_publishable_key`, `stripe_secret_key`, `stripe_webhook_secret`, `paypal_client_id`, `paypal_secret`, `paypal_webhook_id`, `paypal_base_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'http://127.0.0.1:8000/nexus', 'pk_test_51S8qzbB4ZIiKXJ30QIfKcWb72VcKFrwA4R43vIreM77iR33E5ytDOXYTcVezvKCwthwLMTApeLiiWwLchXAFM1G300McayCYXC', 'sk_test_51S8qzbB4ZIiKXJ30LfKZsYH9oUnymJYMECFAY9pUJFKZXFiSocI577Ip3SaZB8GMbmKznP1Ceo5l2uwdh8xj1ToU00YDWZYMUz', 'whsec_YX8OagV4VjKQjiiXtfLWcLAWbGp3hpAR', 'AcWgUwxg7dL2xRxZ9o_HHTq0y5juSwu9eScuhdpVvRDNfS41Z0wPGcejqURjkI8ItUDJjvquQom7Mt9a', 'ELcVFpYHLi4IzuNhTCHKomYbumOPbzeqd0IfXdjwsFmZIZzAvTrtPYBHqoNYhxm_wrybwOdjoqzqhovv', '1GC98337J2874292M', 'https://api-m.sandbox.paypal.com', 'active', '2025-10-22 17:44:45', '2025-12-18 16:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','finance') NOT NULL DEFAULT 'admin',
  `last_seen` timestamp NULL DEFAULT NULL,
  `meta` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `last_seen`, `meta`, `created_at`, `updated_at`) VALUES
(1, 'Zaryth Alpharos', 'zaryth@admin.pro', '$2y$12$vub5x0vk40DWsjOoUu4a1ebQVnMR.pIEYcBLv/ZItITMZ5HbTwUg6', 'super_admin', '2025-11-04 12:04:08', NULL, '2025-10-22 17:17:03', '2025-11-04 12:04:08'),
(2, 'Finance Manager', 'finance@digiprojectmanagers.com', '$2y$12$vub5x0vk40DWsjOoUu4a1ebQVnMR.pIEYcBLv/ZItITMZ5HbTwUg6', 'finance', '2025-11-04 14:37:12', NULL, '2025-10-22 17:17:03', '2025-11-04 14:37:12'),
(3, 'Digi Techs', 'info@digiprojectmanagers.com', '$2y$12$vub5x0vk40DWsjOoUu4a1ebQVnMR.pIEYcBLv/ZItITMZ5HbTwUg6', 'admin', '2026-01-27 05:34:29', NULL, '2025-10-22 17:17:03', '2026-01-27 05:34:29');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
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

INSERT INTO `brands` (`id`, `brand_name`, `brand_url`, `brand_host`, `allowed_origins`, `public_form_token`, `webhook_secret`, `require_hmac`, `lead_script`, `field_mapping`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DevXperts', 'https://devxperts.pro/', 'devxperts.pro', '[\"devxperts.pro\"]', '8d3ffaab1eaa4d2faaf97b114aab53debdaea92dac96eee8', 'c66a6108ecb0fda3e150b0e96c0f9922bca8b2fe1c9cd94b29e7ae1510d79540', 0, NULL, NULL, 'Active', '2025-11-27 00:01:12', '2025-11-27 00:01:12');

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

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `password`, `phone`, `meta`, `status`, `last_seen`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Zee Ash', 'zeshanasgharr@gmail.com', '$2y$12$gnaLRiYfXzg6jdgQYsfpnuBcxRoZ12ncMf/rYqid8UcQm.6vtEuPu', '+1 (961) 307-2776', '{\"plain_password\":\"admin123\"}', 'Active', '2026-01-27 01:57:00', '2025-11-27 00:04:00', '2026-01-27 01:57:00', NULL),
(2, 'Tawha', 'digitechsupwork@gmail.com', NULL, '23223333333', NULL, 'Active', NULL, '2026-01-27 01:41:59', '2026-01-27 01:41:59', NULL);

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
-- Table structure for table `custom_brief_forms`
--

CREATE TABLE `custom_brief_forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_name` varchar(255) NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `form_schema` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`form_schema`)),
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `seller_id`, `brand_id`, `client_id`, `name`, `email`, `phone`, `service`, `message`, `status`, `auto_replied`, `converted_at`, `domain_url`, `prediction`, `meta`, `is_finish`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 'Zee', 'zeshanasgharr@gmail.com', '+1 (961) 307-2776', NULL, 'I need logo for my personal store.', 'new', 0, NULL, 'devxperts.pro', '{\"status\":\"real\",\"score\":95,\"strength\":\"hot\",\"category\":\"design\",\"sentiment\":\"positive\"}', '{\"price\":null,\"brand_key\":null,\"channel\":\"web_form\",\"url\":\"https:\\/\\/devxperts.pro\\/\",\"brand_host\":\"devxperts.pro\",\"page_title\":\"DevXperts | Home\",\"timezone\":\"Asia\\/Karachi\",\"locale\":\"en-US\",\"session_id\":\"557747c4-f04c-4788-a843-5caf746e6c4b\",\"currency\":\"USD\",\"ip\":\"119.73.97.109\",\"ua\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/142.0.0.0 Safari\\/537.36\",\"service\":\"Logo Design\"}', 0, '2025-11-27 00:04:00', '2025-12-02 12:56:30', NULL),
(2, 1, 1, 2, 'Tawha', 'digitechsupwork@gmail.com', '23223333333', NULL, 'TESTING SOW', 'new', 0, NULL, 'devxperts.pro', '{\"status\":\"real\",\"score\":95,\"strength\":\"hot\",\"category\":\"design\",\"sentiment\":\"positive\"}', '{\"price\":null,\"brand_key\":null,\"channel\":\"web_form\",\"url\":\"https:\\/\\/devxperts.pro\\/\",\"brand_host\":\"devxperts.pro\",\"referrer\":\"https:\\/\\/digiprojectmanagers.com\\/\",\"page_title\":\"DevXperts | Home\",\"timezone\":\"Asia\\/Karachi\",\"locale\":\"en-GB\",\"session_id\":\"49d1681b-5616-42d0-94c4-a67a3daac560\",\"currency\":\"USD\",\"ip\":\"154.57.192.90\",\"ua\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/143.0.0.0 Safari\\/537.36\",\"service\":\"Logo Design\"}', 0, '2026-01-27 01:41:59', '2026-01-27 01:41:59', NULL);

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
(8, '2025_07_31_171848_create_leads_table', 3),
(11, '2025_08_18_205308_create_personal_access_tokens_table', 1),
(13, '2025_09_10_174357_create_profile_details_table', 1),
(14, '2025_09_10_225122_create_risky_clients_table', 1),
(15, '2025_09_15_172233_create_lead_assignments_table', 1),
(16, '2025_09_26_223326_create_account_keys_table', 1),
(17, '2025_10_02_172544_create_projects_table', 1),
(18, '2025_10_02_172551_create_project_tasks_table', 1),
(19, '2025_10_13_173437_create_client_tickets_table', 1),
(20, '2025_10_14_193705_create_performance_bonuses_table', 1),
(23, '2025_11_05_232117_create_questionnairs_table', 5),
(24, '2025_12_22_223309_create_custom_brief_forms_table', 6),
(25, '2025_08_13_183153_create_orders_table', 7),
(26, '2025_08_14_164525_create_payment_links_table', 7),
(27, '2025_08_23_170645_create_payments_table', 7);

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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `lead_id`, `brand_id`, `seller_id`, `client_id`, `parent_order_id`, `order_type`, `service_name`, `currency`, `unit_amount`, `amount_paid`, `balance_due`, `status`, `front_seller_id`, `owner_seller_id`, `opened_by_seller_id`, `front_credits_used`, `front_credited_cents`, `first_paid_at`, `paid_at`, `refunded_amount`, `refund_status`, `provider_session_id`, `provider_payment_intent_id`, `buyer_name`, `buyer_email`, `meta`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, NULL, 'original', 'Logo Design', 'USD', 45000, 24000, 21000, 'pending', 1, 1, NULL, 0, 0, '2026-01-08 00:19:19', NULL, 0, 'none', 'cs_test_a1CdZ1BgBGgHspzCzriLijPvMdIjt3EION0bGozQwMtlsJDsK9HxjRj35p', 'pi_3Sn2GGB4ZIiKXJ301tfylVWU', 'Zee', 'zeshanasgharr@gmail.com', NULL, '2026-01-08 00:19:01', '2026-01-08 00:19:19', NULL),
(2, 2, 1, 1, 2, NULL, 'original', 'Logo Design', 'USD', 50000, 0, 50000, 'draft', 1, 1, NULL, 0, 0, NULL, NULL, 0, 'none', 'cs_test_a1pmRfanLIrRkYnYoLjazN0mRzhNPgd1JKC0UC691BpHumTbXSpdCpHwBm', NULL, 'Tawha', 'digitechsupwork@gmail.com', NULL, '2026-01-27 01:44:32', '2026-01-27 01:45:12', NULL);

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

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_link_id`, `credit_to_seller_id`, `seller_id`, `owner_seller_id`, `front_seller_id`, `credited_seller_id`, `amount`, `currency`, `status`, `provider`, `provider_payment_intent_id`, `payload`, `refunded_amount`, `refund_status`, `refund_payload`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 24000, 'USD', 'succeeded', 'stripe', 'pi_3Sn2GGB4ZIiKXJ301tfylVWU', '{\"id\":\"cs_test_a1CdZ1BgBGgHspzCzriLijPvMdIjt3EION0bGozQwMtlsJDsK9HxjRj35p\",\"object\":\"checkout.session\",\"adaptive_pricing\":{\"enabled\":true},\"after_expiration\":null,\"allow_promotion_codes\":null,\"amount_subtotal\":24000,\"amount_total\":24000,\"automatic_tax\":{\"enabled\":false,\"liability\":null,\"provider\":null,\"status\":null},\"billing_address_collection\":\"required\",\"branding_settings\":{\"background_color\":\"#ffffff\",\"border_style\":\"rounded\",\"button_color\":\"#0074d4\",\"display_name\":\"DevXperts sandbox\",\"font_family\":\"default\",\"icon\":null,\"logo\":null},\"cancel_url\":\"https:\\/\\/digiprojectmanagers.com\\/pay\\/now\\/vfsBnwTSFI7eEzcOP1OvVz7Uz9YoCoHgKfenJ5rp1KxK3tDz\\/cancel?canceled=1\",\"client_reference_id\":null,\"client_secret\":null,\"collected_information\":{\"business_name\":null,\"individual_name\":null,\"shipping_details\":null},\"consent\":null,\"consent_collection\":null,\"created\":1767813547,\"currency\":\"usd\",\"currency_conversion\":null,\"custom_fields\":[],\"custom_text\":{\"after_submit\":null,\"shipping_address\":null,\"submit\":null,\"terms_of_service_acceptance\":null},\"customer\":null,\"customer_account\":null,\"customer_creation\":\"if_required\",\"customer_details\":{\"address\":{\"city\":\"Washington\",\"country\":\"PK\",\"line1\":\"This is for testing webhook on DPM.\",\"line2\":\"Absorb the way to pika fields, US statue\",\"postal_code\":\"10001\",\"state\":null},\"business_name\":null,\"email\":\"zeshanasgharr@gmail.com\",\"individual_name\":null,\"name\":\"Dev Xperts\",\"phone\":null,\"tax_exempt\":\"none\",\"tax_ids\":[]},\"customer_email\":\"zeshanasgharr@gmail.com\",\"discounts\":[],\"expires_at\":1767899947,\"invoice\":null,\"invoice_creation\":{\"enabled\":false,\"invoice_data\":{\"account_tax_ids\":null,\"custom_fields\":null,\"description\":null,\"footer\":null,\"issuer\":null,\"metadata\":[],\"rendering_options\":null}},\"livemode\":false,\"locale\":null,\"metadata\":{\"brand_id\":\"1\",\"lead_id\":\"1\",\"order_id\":\"1\",\"payment_link_id\":\"1\",\"payment_link_token\":\"vfsBnwTSFI7eEzcOP1OvVz7Uz9YoCoHgKfenJ5rp1KxK3tDz\"},\"mode\":\"payment\",\"origin_context\":null,\"payment_intent\":\"pi_3Sn2GGB4ZIiKXJ301tfylVWU\",\"payment_link\":null,\"payment_method_collection\":\"if_required\",\"payment_method_configuration_details\":{\"id\":\"pmc_1S8r07B4ZIiKXJ30znpJ0yd8\",\"parent\":null},\"payment_method_options\":{\"card\":{\"request_three_d_secure\":\"automatic\"}},\"payment_method_types\":[\"card\",\"link\",\"cashapp\",\"amazon_pay\"],\"payment_status\":\"paid\",\"permissions\":null,\"phone_number_collection\":{\"enabled\":false},\"recovered_from\":null,\"saved_payment_method_options\":null,\"setup_intent\":null,\"shipping_address_collection\":null,\"shipping_cost\":null,\"shipping_options\":[],\"status\":\"complete\",\"submit_type\":null,\"subscription\":null,\"success_url\":\"https:\\/\\/digiprojectmanagers.com\\/pay\\/now\\/vfsBnwTSFI7eEzcOP1OvVz7Uz9YoCoHgKfenJ5rp1KxK3tDz\\/success?session_id={CHECKOUT_SESSION_ID}\",\"total_details\":{\"amount_discount\":0,\"amount_shipping\":0,\"amount_tax\":0},\"ui_mode\":\"hosted\",\"url\":null,\"wallet_options\":null}', 0, 'none', NULL, '2026-01-08 00:19:19', '2026-01-08 00:19:19', NULL);

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
-- Dumping data for table `payment_links`
--

INSERT INTO `payment_links` (`id`, `lead_id`, `seller_id`, `brand_id`, `client_id`, `order_id`, `credit_to_seller_id`, `owner_seller_id`, `generated_by_id`, `generated_by_type`, `service_name`, `currency`, `provider`, `unit_amount`, `order_total_snapshot`, `provider_session_id`, `provider_payment_intent_id`, `token`, `status`, `expires_at`, `is_active_link`, `last_issued_url`, `last_issued_at`, `last_issued_expires_at`, `paid_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 3, 'admin', 'Logo Design', 'USD', 'stripe', 24000, 45000, 'cs_test_a1CdZ1BgBGgHspzCzriLijPvMdIjt3EION0bGozQwMtlsJDsK9HxjRj35p', 'pi_3Sn2GGB4ZIiKXJ301tfylVWU', 'vfsBnwTSFI7eEzcOP1OvVz7Uz9YoCoHgKfenJ5rp1KxK3tDz', 'paid', '2026-01-08 00:19:19', 0, 'https://digiprojectmanagers.com/pay/now/vfsBnwTSFI7eEzcOP1OvVz7Uz9YoCoHgKfenJ5rp1KxK3tDz?expires=1768418341&p=eyJpdiI6Im5XSUwzcWtlU2gvRWt2aWxkdnRQcHc9PSIsInZhbHVlIjoiQXpnRndXa2lIQm9wS3Rud0NHdEZ5UzA0OE03bFVWMTVoNkxYbjBONXY5WG8vaTlqUVRQYXlvVkpyMFR2b1AxcmhvaXFIbEpFWktjRzlOWUduelFkYjVxZnhobC9kcFA5bklYZjJ3aUU4dlFvd2ZvSzc1OGdSbFM2cmtKaXZ4K01zL25TTm81eXpmYlQxdlY3cldYT2p3PT0iLCJtYWMiOiI1ZjVmOTVhMzhhZWFjODAxOWU4NjExMmZiYWNiYzFkOWQ2NGE5MWUxMDQ2Y2NmNjUxNTJhNjYyOWU0ZjY1MzlkIiwidGFnIjoiIn0%3D&signature=861518f3abdbdffc3e0eb894fd7d42f07a571b0729337b6aae2baccf6c72a367', '2026-01-08 00:19:01', '2026-01-08 03:19:01', '2026-01-08 00:19:19', '2026-01-08 00:19:01', '2026-01-08 00:19:19', NULL),
(2, 2, 1, 1, 2, 2, 1, 1, 1, 'seller', 'Logo Design', 'USD', 'stripe', 50000, 50000, 'cs_test_a1pmRfanLIrRkYnYoLjazN0mRzhNPgd1JKC0UC691BpHumTbXSpdCpHwBm', NULL, 'iBN68XlS9JzjdYD1DDF8UOWjt0yPqoJMYS380HiTtJf7QX9H', 'active', '2026-01-27 04:44:32', 1, 'https://digiprojectmanagers.com/pay/now/iBN68XlS9JzjdYD1DDF8UOWjt0yPqoJMYS380HiTtJf7QX9H?expires=1770065072&p=eyJpdiI6IkdRMEkyRVk0OWhaNXpBVXU3K3ZZQlE9PSIsInZhbHVlIjoiNzExSG1wRitUUlVkWGpsaFpxOXo3T1Q1REY4NjBaaGV1RVMxaG1JM1lrMGZ3VGNtVHZaVDEvUmNjWUFSUmwrbjdTaFhSUm94b3Y4NlY1ajJDSXE4enVKS05mcWh5K25XSC9sMGs2Q2VaVEVlUXA0cTdIMWtJV3RjVzc4aUtUV09mYm8xSjN3aFRjK0FVOTd0TU9KYVFBPT0iLCJtYWMiOiJhNWFkNmM4MjEyNDFiMDJhNjgxMzRjMzY4Mzc3MGViNjk4NDkyY2FlYzhmYzcxYWJmOWQ4MzRkYmQ4NGFjNTAwIiwidGFnIjoiIn0%3D&signature=ea400294ab374224e5f1ae2107c0f53055b38fdd87a598cfa8ccc3bdb3edb573', '2026-01-27 01:44:32', '2026-01-27 04:44:32', NULL, '2026-01-27 01:44:32', '2026-01-27 01:45:12', NULL);

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

--
-- Dumping data for table `questionnairs`
--

INSERT INTO `questionnairs` (`id`, `client_id`, `order_id`, `service_name`, `meta`, `brief_token`, `brief_token_expires_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Logo Design', '{\"company_name\":\"Wright Nelson Traders\",\"tagline\":\"Qui aut ipsum commod\",\"industry\":\"Mcfarland Riggs Co\",\"logo_description\":\"Quod cillum labore v\",\"design_inspiration\":\"Laboriosam velit n\",\"color_preferences\":\"Eum ea quis fugiat c\",\"logo_style\":[\"Modern\"],\"logo_graphic\":\"Sunt voluptate recu\",\"additional_requirements\":\"Eveniet excepteur n\"}', '24c1581a-3c17-4b75-8414-a5b420279af9', '2025-12-19 00:20:34', 'pending', '2025-12-05 00:20:34', '2025-12-05 00:22:39'),
(2, 1, 1, 'Logo Design', '[]', 'b8ef0746-0c48-42fe-a36a-c0b0a0980957', '2026-01-20 18:24:57', 'pending', '2026-01-06 18:24:57', '2026-01-06 18:24:57'),
(4, 1, 1, 'Logo Design', '[]', '76bb1ee9-39f5-4fcc-944a-912f50b4577f', '2026-01-21 12:28:06', 'pending', '2026-01-07 12:28:06', '2026-01-07 12:28:06'),
(5, 1, 1, 'Logo Design', '[]', '5ab4f15c-743f-469c-bf14-7441d74d1959', '2026-01-21 23:14:20', 'pending', '2026-01-07 23:14:20', '2026-01-07 23:14:20'),
(6, 1, 1, 'Logo Design', '[]', 'd43e61a5-9c44-415a-8a28-74373214f868', '2026-01-22 00:19:01', 'pending', '2026-01-08 00:19:01', '2026-01-08 00:19:01'),
(7, 2, 2, 'Logo Design', '[]', 'f11fcaa0-8bd8-41c0-8300-7b48ea8bc6a4', '2026-02-10 01:44:32', 'pending', '2026-01-27 01:44:32', '2026-01-27 01:44:32');

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
(1, 1, 'James', 'james', 'front_seller', 'jamesrrogerrs@gmail.com', '$2y$12$NzET3V.XW8YTcEt2WiX32uuahG5wv5oQMaHVT/9WBSm4PZhG/Y7l2', 'Active', '2026-01-27 01:52:56', '2025-11-27 00:01:32', '2026-01-27 01:52:56', NULL),
(2, 1, 'Stephen Myers', 'steven', 'project_manager', 'steverrogerrs@gmail.com', '$2y$12$CsWBJjnFQ6yuRPffoE9ziuiH1v6nb9bFGNZ5L2LP4RqOTRRovz4dy', 'Active', '2025-12-03 14:14:45', '2025-11-27 00:01:55', '2025-12-03 14:14:45', NULL);

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
('09CEF5GbE7O9GI9QCKJVPqwknLgWFTPX7UX4cnQ0', NULL, '43.164.197.177', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjRmWXIzMVJWeGN5cjRoMTBWRjJHVUdna3JyTmZBVGpKUU5oOUNDayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770297935),
('0keO3neOMcCKqKYPqdlgdBqGr8BUTW0KM0WRk8Tb', NULL, '51.68.111.203', 'Mozilla/5.0 (compatible; MJ12bot/v2.0.4; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicVk2dUNwOTd3dzFEVUF2YktMdXNGVjV0ajNuQmxabTV1T2l6RVdxTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770379841),
('0olzklSSqLtZaokwG2y1Y1K3d59TUgT0F4MRbhNY', NULL, '192.154.250.81', 'axios/1.10.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaUNoZXZqWElxZ3ZxNXRkeFFBMFA5b0s4ZVdVSUR1YURLdlBvMTNRRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770254820),
('0ZTcIVXFrVp95cOsaqkOBDqnOSdLVGGecJUT3b4I', NULL, '51.38.135.19', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMTJoN09SRFRqbW9NNGI1U1h0Z3lJaEs0VW9GbWNRWjdmWEM1ZkdtcyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vY2xpZW50L2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770261396),
('1SFjJf3OI4fxRiNsxQwGCKl7yyIY3gkRyDHPXhYz', NULL, '54.39.203.139', 'Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibW1KUUpEemFVZEZyU0trUHZ6T3JQb2dVQ3JQZDdMUzByanRlWHJ6SyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770392773),
('1VHWSBCq6dcxhV0Noe4QXbRrXw5cE1UXgdsXufd3', NULL, '149.28.109.69', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ09RUTdSN00yTE1RQTFkaERyTmZPWUhyUmhSblZVa0RhZHd4b3J5VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770260203),
('2bd1VfURiZ1meUneyAo7TeMRQzqDbWMSeLC2iuXR', NULL, '161.115.235.74', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.51 Safari/537.36 Edg/99.0.1150.30', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibDBhMkxKOWJGRllQNkpIMWVLY2g5dld4UkV4eEd6R0Q0Y3dUSVREdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770339162),
('3IpYv0ucQtZYkYa8wJFnveI4rOVRwWt8LKu56BY3', NULL, '216.244.66.244', 'Mozilla/5.0 (compatible; DotBot/1.2; +https://opensiteexplorer.org/dotbot; help@moz.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNTRUeGdWWnNBdFJCTElhTHptVTd5Skt4THBudlA0aE5NelhybzVyWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770305131),
('4zMbWg6hSTzdztJi1gTudyEUagyU7zeiQRvrFrI0', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieUp3TnpmQWNacTQwc3JJVXNzWEtzOUwxODZxSDltdlBOWkRlenFwZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770264735),
('5mp2ATGl2FFAzj1yJqhatsf0F64UbgH7xAE7xZUl', NULL, '43.130.131.18', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSzA0alVta01FNVFVb3dmZ3FPTzFXdWU5aVcxaGN3bFVTNmRZa2pzSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770314586),
('6yxUrrHWIwouSHRMXdcTPeEkMeLc6w7OfNNKhWjz', NULL, '43.166.240.231', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUUhhRjBCallrS3RzemI3WklBODVFVHZ5TjJCcU83N3J5Q3pqOTZpYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770275068),
('70qsfKxTcucXgSeq477btWEHmR4ncL05AcKtO94k', NULL, '180.153.236.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0; 360Spider', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTEd0MUpmTGVDUG8xVVg5c1NrakM3N2dxSGFyQlhOYlJnVlZDN3ZGTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770281569),
('77agRlppK9UNfmmOfsKjeFr97VHDcNDMfz2PF4Lv', NULL, '195.178.110.34', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRFMxSkJiRXdmQmxBQ1g3RnBVc2ZyVWVScEtwYW9qWldSUHEwNzNaaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770264733),
('9XeCSiuOtzHmFcp6SQNHaPpI99X10cak6ocnASou', NULL, '104.156.246.244', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNzJPYXpWSWJsYzVYZ2pMR2VtOUhuVXJHeDlsdFpoVG9hWVdsTU9ETiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vYWRtaW4vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770259243),
('A2sFN9rM4sVuc8w0ZX4tu5MYkmgV4z3HLqxMKy5K', NULL, '51.38.135.19', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOXBlQXM3bThpVTRTdk9EbWgydlFEanFMSVdwam9Oczd2ZTVJdFR6SyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vYWRtaW4vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770261382),
('ajuXRaJT9Ca58oh1Coh54CYfHhEBdphDHs34M4XI', NULL, '54.37.10.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYzQ3WWhSdmZlZlpvZUFUd0wycEtWelJXc2g2R0hxZzlhVHU5dFdLUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770261373),
('AxG5iEmFXAGNQrXUjMzz4G7AoTeERULFwDGBFBcm', NULL, '171.22.248.2', 'axios/1.10.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRUV6OHFvVXpaRGR2Z1lYeHJHZktBVmFRQTdTZXlWUWMzVXRZWXRsOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770254820),
('bJrQZWnfM1qY9evjkABhYr4sTZC3DJefCcZyfj58', NULL, '35.188.208.223', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFlvU3ZJbThFWnJrYkd5VGtuZE41NEF6V0owT1lKUlQyU2p4aVl2diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770378263),
('bQ1Xjm8wy9HsfpArZvW2lleqarzbKvHIvhcNYMxE', NULL, '18.204.5.173', 'Mozilla/5.0 (Linux; Android 11; Redmi Note 9 Pro Max) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.88 Mobile Safari/537.36 OPR/68.3.3557.64528', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialNoWTU3OVg2ZWdOMEN4WW1qU2l2eXNwUk5Fbmx0azJIcHFKMFpseCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770324874),
('BygN54ewMkckxSypV9u9MmFf1p73Z51vj01dDxL1', NULL, '45.77.115.164', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2tvM21qVXFHaHdjbGRlVk9MMW9NbndhSWJIRElQYXlob3VqM2ZEWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770258727),
('c5enNwxL5BF6eSghGjTQLhK9PjtZRsoSJff2mHk6', NULL, '149.28.109.69', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicDVBNVJOY1U5aVdZYWVxamNzdmdYbENBMzR1T2Z4Tlc1SmFWcFIwSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770260203),
('cDKCDlZ33IYgAJsscezdcUSQZjdEqvKBLovxh2hG', NULL, '148.135.177.45', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicXlROUxrMGVHYlR6VXFzQnpHcFRLaHNGUWJtVDJlTjZLd1N0NXptVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770254824),
('cKAnaRd4vjqP2KgQ1IL1IG2XwoAyb02wpOC1w0s8', NULL, '103.99.33.214', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Vivaldi/4.0.2312.33', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWFZY1R3WGhZT2JIVU9UOFFRUUFzb2tJT2MwS2JhRWM1QzRvVFYyciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770254824),
('CV3wXKBsReMZJZIBXp3WsJMLxDH6Zr6fuWJQgnTl', NULL, '43.157.95.131', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidUVnWGRScWJRancwaU9OeHpINmZCUGlyb0NpN1ZPdzVvTmtUYm1HQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2NsaWVudC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770401214),
('D0D8Kg0fj78Y1qpueJPXssuJh0cw1XddKPZ0TYnU', NULL, '122.51.236.174', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQnZ3VUpVU2JQdGhCWG1mdlZmUllnT1VkczZNWHlVQVJrRVpkczlOcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770324957),
('DGjdpWdQfoOPV8JuZBN34FSoCsuWPm57nkIPCAKa', NULL, '34.206.74.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2hsWGRIRnJyRjBmREs2YUs4S1hPUndDUmZQWGpKZnBESVpVODdnTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770272891),
('dKu2Mg5uFOh3gpACTfOUpohExJzdtHhdKGVSKptU', NULL, '43.157.53.115', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOHhkOXdmR05TTEdMN1hPcWFQeWp3WkR1NlNGa0xYSXRveTdJZktIUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTc6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2FkbWluL2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770407282),
('dqvVHJqle1ltmQrThUllfdiqILrwGBhk7L2RJPTt', NULL, '108.165.197.143', 'axios/1.10.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFFZSW93N3A4M2RqdGtucjJHNzRUODh3aVdFczNCOHRhcUVJMFZESyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770254822),
('DRIPQfts35HNPXPu9cNBXiJsctQ4lByPOnNrBUWR', NULL, '123.149.74.31', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibzF6Tnk2WDB4dzlvQWpBcXRBYjZIdEE2bUhqQUZYRWZybzhoZHBDaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770329743),
('dwGWraVnaL8rIrWsWTxsbHXT2jmv1Ft9M5a91dq8', NULL, '170.106.147.63', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3ZCamhPeEVmeUpUSWdHWWRoYWNES01heTNWWUtpMnBadTQ1ODNFNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770375108),
('eXItxhgfbuSaVPXZHsVA16aA69hlkvKZb1C5tav7', NULL, '51.38.135.19', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ3cwblNUb2VxcTI4b25uU0ZxdElaRGhBR0Iyd3ZIWVhodkZDZ0NqRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vYWRtaW4vZm9yZ290LXBhc3N3b3JkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770261387),
('g0IDYwrQqabjxbRb7gtqWvu62AW2Fx8yp039iFqy', NULL, '104.156.246.244', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZlIxbm9Tek5xc0VDTVRDWUI0dlRZSEpvRUZpZ1UwS0RXMjFnbFZjdyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vYWRtaW4vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770258194),
('gCMRaI5PUmVoxRASoTuolclfBaC2XUYxk3IFhx9z', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFBnRG9CbEs4aHE4N2ZObHQ1OXBxUkl0TEpBNWxLS1JWTXpzbXZzWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTc6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2FkbWluL2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770264736),
('GMzbRiEYL5gWkDhVfKR5EcYCZ2pPHN0TiFAgnWeB', NULL, '156.146.39.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXZzVXdiVGF4U0dmRHV3NUY1TVZRcWphcjJBWXZjMFZ2OGhjakZSQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770401267),
('gsDYRxv0v8opc4EbTEiROxIGYY4PjHF3OJS9kWAH', NULL, '149.28.109.69', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOGFnV3Y5dzlZekJPTFFQYTRCakczem9pVHZYVzNJRmVpblBGNkwycCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770260205),
('HF8S8arjCloaUqjDUbWocHQj6fLqnLf1qPS2nAOl', NULL, '144.202.33.143', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUURiZzN1N1BTeXhPR1pBRHlNZElRV0tnWmJpMzVwOXpFRDU0WTVhWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770257701),
('hIc6vcpFdJU1RgjIPdlyg0Mz9bTh24betKGpg4rd', NULL, '180.153.236.140', 'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0; 360Spider', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFNrS0VxSGdmT205WVlQSnVyS2d5em1nOEhrcjA2U1JQQUpZbkdGQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770282418),
('hjHioZxGEHQv6XBUvXZGNc9fhRpbixMBmn4wyeRW', NULL, '144.126.219.88', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWtuMTUwZGZ5T3JGV1ZFQThkNlRZVjM1M3hBalNNUUxGU21XY2lBTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770364938),
('hm65h38NI5eZwCQPy8Dn07TUe8ygYQr6ZLNqFEDQ', NULL, '144.202.33.143', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXo2N2xSUWVvWFl4NlBLSVBJS2R3eTFJQW05TWxJdmdJdGZsakY1ZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vY2xpZW50L2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770259717),
('Hn5VBE2DPSVoqFSAOTPeSG1W8PFEZXkMVrBAQlPR', NULL, '74.7.243.215', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRkxTQWJRVFJSSVY1dzZpQXdoUHJvR3Fibnp1VDhXMDBQejd0TzhLYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770366489),
('HuP5g8DYSFSBmgS6vjZ7lpGUOYVJowJ5TFt0FiOl', NULL, '192.71.12.112', 'Mozilla/5.0 (Linux; U; Android 13; sk-sk; Xiaomi 11T Pro Build/TKQ1.220829.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/112.0.5615.136 Mobile Safari/537.36 XiaoMi/MiuiBrowser/14.4.0-g', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYVpGczRQTGdqb2dZSUVSdkV0OWlkSTJFa3RjU0gybUs2c1JRUkRvcCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vY2xpZW50L2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770260173),
('I9fHJS7YKiflMmi3nZf7NtkWqNVHyjrI5x1ieSzk', NULL, '217.113.194.33', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaEJmME14S2dPN3hQTFB4d1FrZGtkbXBHUm9kQU1lRWZpdEdOSXhSYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770326203),
('IJF9p8nzCRrKVFgpE81f3Afr4F7ybYpBoUJkgRMZ', NULL, '216.244.66.244', 'Mozilla/5.0 (compatible; DotBot/1.2; +https://opensiteexplorer.org/dotbot; help@moz.com)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYXpUeDg5Rm12aDFLTGNjR3BOWUpmZDZGME1kcXRxZlVqY202QnE2aiI7czo1OiJlcnJvciI7czo0MToiWW91IGRvbuKAmXQgaGF2ZSBhY2Nlc3MgdG8gdGhlIFBvcnRhbCAhISEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo1OiJlcnJvciI7fX1zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo0NzoiaHR0cHM6Ly9kaWdpcHJvamVjdG1hbmFnZXJzLmNvbS9hZG1pbi9kYXNoYm9hcmQiO319', 1770270131),
('jc9ChGFVWwpQYmGPQC94UABkco1n0W2ADBmhDD7r', NULL, '192.71.142.176', 'Mozilla/5.0 (Linux; U; Android 13; sk-sk; Xiaomi 11T Pro Build/TKQ1.220829.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/112.0.5615.136 Mobile Safari/537.36 XiaoMi/MiuiBrowser/14.4.0-g', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRVFJMWVqcU5CNlVXWHFUc2tsbjVhdng3VW5PNXRSVmpmUlY3TVFFaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770260172),
('jcyiNUGMPmStpyyrTQi6m2cqPb9c4CyfhBBGrwOB', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoienJwQnRzemJVaW52TlptRTFaazRWSjA2YWVzd3hEeEpxSklYd2JmbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2NsaWVudC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770264736),
('JodflCVQThtwPdH20NtbhXOleIgZX4KaOscKef9a', NULL, '93.158.91.250', 'Mozilla/5.0 (Linux; Android 12; SAMSUNG SM-A415F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZW1DbnNHN1BxWnplTnJMdXpJRXV1bVp4V3FYbXRkUThvZlBBZFNvMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770283640),
('JSjXBwl511tNM2Hy7vLQwqVOc2HScqmssZWrRYCV', NULL, '149.28.109.69', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTlZoTm9jQ0RsT3FCUGZJOEpoU21XcWxrTlJteWxWVWZlMGhOOE9RZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770256858),
('KdjDxirSMfTGvzV4kZNToObfow6G1USwhTKtnqme', NULL, '185.162.75.83', 'multi-country-domains/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTk1GWHdjZmVMdmpQS0U0Y3ZKdkRSZGh5aE1RMEM0b00yMGFkWFRpciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770264700),
('kuVJLgqM3WlY8ideStyVpYIVO7srRXDoihApYfL5', NULL, '149.28.109.69', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYmlCSE1wajJQMFVyNVdxWmR5aElBbG5QTlQ2RzZWYlNINkl4cGZsRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770256142),
('mbYqU2fu8StXUvKJMhlt1oWeDbR5Z6ciGIhB3yeL', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkM2TFBJOGdtUDZKV1RyQjNobGtOYWVCYUQwb0JxQ241MkxjdWtYTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL3NlbGxlci9mb3Jnb3QtcGFzc3dvcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770264736),
('mm9egL9ocSDnnwpf6gdxpo9Nu0dMNVQyQOt6Gh0D', NULL, '66.249.79.32', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFRtaXVMaVpWVngzSWlKaEwwMkdIWnQ1eG1nQ2RORTFkS1ZadHZXRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770376845),
('mN6Mmg2Ady5MKxPIE5Pei3ITqpwrwVfFNmYdttXi', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaU1SYkRhMUlLbVNYY2xPRjhVYkFzMHc0RmdnYjVQZ0NVWVNCMmNLTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770264733),
('mpXRvXr2wWxqKfxHAq14SfqbXaQwsUKJpFTE5Auc', NULL, '149.28.109.69', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM0cydkVyM2hwMmFCZ0dWeVhja2gzVHk5Q3REbnhNa0s5SjNPcVpyWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770260210),
('N7KMCwrjPyhHEsYkgRO82ZC4g1fXxa1yj6afvH1d', NULL, '43.155.157.239', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWdZR2lLejU4MG82WlRtcnFWV1poeGtReTEwMEswVjJvUDhsVHdGVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2NsaWVudC9mb3Jnb3QtcGFzc3dvcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770404995),
('Nd4AjeMF6nYMsyEsc1SGSONge7iqbzf3CI4BFnZx', NULL, '66.249.79.37', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS1FseGVndHdzM2hpNUg4bTNEWEo3aW9tM1E0MmdjVmg2cGRab1liVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770376845),
('NsEkQmcNJvYVpf814miFkMlYZzHBaX8cFrsZgY5e', NULL, '57.129.4.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicHNqdmxKY2gwMmJXV1Jod3pFWEVhVVd6TWlpMngwSkttU043Vm9EdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vY2xpZW50L2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770261390),
('o1V2cToZ1hs4P89ao9L2iNBZlf6TY8wqA5kDO6XA', NULL, '93.158.91.254', 'Mozilla/5.0 (Linux; Android 12; SAMSUNG SM-A415F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZFhxeE1zSjUxdEF1Y3pSSDA5OFV0WTI5OGVHYTdVNG5WSG4xUWlZQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vY2xpZW50L2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770283641),
('OUcs1f0fYt59tLTPAsx3qEmH0QNfaOXCvsEhHIGZ', NULL, '5.133.192.128', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTzRneXpuYnNpcGlkUzByVHNuVjdxcEpOQ3N2NThxVDlrbXhoQjFqQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770408615),
('P4zBUXWecPRvr692yNBXaAbTTQzE1vdwv72fsYBJ', NULL, '74.7.243.230', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSjEwYU1GRnRaeURtd0hCSkdocnZyMXIwbVE5OUIxTmxETVhxQ3BtSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770298018),
('PefqoCCg52rsykLL8Ns7EtQmOf5wowgpjvipHJhi', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnFMV1RZYk1Zd3YxZjV3eWZDZWRtOU9Bb0xHS0xtWFpvWVcyV0lSeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2NsaWVudC9mb3Jnb3QtcGFzc3dvcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770264736),
('PiGWVwbmYMccYMriTtOzdx4vnAWdczTSVvOXrRqz', NULL, '43.165.65.180', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNzRrNmFRVGxaTGpBQkVQeDNJbzJhalZiZ2NxU3l4WkxXZ3pza2lXeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770342065),
('qAKRkiFd54GCKhve5MJzbE1yQeyRHrcVy6VZRZwz', NULL, '93.158.91.253', 'Mozilla/5.0 (Linux; Android 12; SAMSUNG SM-A415F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicHg0QnNlWDd5c2hhRkpFWExBdjA4RUM5c2dUSWJDajRCeTlQa1RERyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770283640),
('QG05wcdOIF6icGNIJiFt60ScZC952cFZVYUIG1jQ', NULL, '93.158.91.252', 'Mozilla/5.0 (Linux; Android 12; SAMSUNG SM-A415F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoickM5MmtGNVNsUWlia1NjYUoyV1ZpSGZ6RHk5STdiN0NMNmcxV3JSUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770283641),
('qxsivqVH4nTzoqy5yKjRUqVwl13eVRZeV8renCGr', NULL, '149.28.109.69', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieWhTb0lnU1pEUXNSbTJiYUJCN1NGbzRPQXFYZmR1amNKUTd2TFJ1dSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770260209),
('RadNSj1LqMAeNSl37VHRIj7XvaoarEBR705TETvm', NULL, '104.207.147.158', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid3pERDRpY2R1RDhCc0ZISjBhSXJiV2twNHRNNmxvdGxmV3B6RmEwTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770254823),
('rCRYdgBazWowloBTI1EpBKiqSQY8gZ8vpXHPw9w5', NULL, '57.129.4.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXVpSGRlWUVpQWZJY3pzSmY5eGYycFRPTFlYS3JpNXpCYTJkM21UcCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770261380),
('roMJ5mWjUfNScKpYQUs8kcf1uknj2esXTwBubD7T', NULL, '43.157.188.74', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid0VPSnNhT25yUkIyekxwYUppZnJTZHJnNjl4UkNxOTg3RElIVFNhMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770368622),
('S8UJk69xvd0S6pz3vbHHjmybWx9o5S7XyiLfFCNc', NULL, '43.130.78.203', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkMwNmV6dXJPRUtBTWF4YlBFbjBIUE5qRnhpYUgweUUyTGM5cEp6SSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770291958),
('seiWuUwvoAo1iGxzFbx4fotgTYuzYd1TvJyuxq1Z', NULL, '43.135.186.135', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUVJRTmJkQWk2OTB1R0dSdGxFb0lNaUttbXFUOWI0TTdlVUt5czZoNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL3NlbGxlci9mb3Jnb3QtcGFzc3dvcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770405881),
('SZqUGNYIodlpl0gPDLoqxTnigmCD24uGoLe0xf1z', NULL, '45.138.48.190', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUnFmdHJhSlBWM0w0UkxrYWhFYzBkRlkyWFZ0VGFzVnNQaUxIalJacyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vYWRtaW4vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770405218),
('T7SE4lJV7daxKOXDiGTOdWMsamY2t9FVCD8p7Tnh', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUTlUcmZjY3dzZ1R6WGJLYzdYWWY0ZDFYRjhqZVdySzlPallaQVp2VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI2OiJodHRwczovL3d3dy5kaWdpcHJvamVjdG1hbmFnZXJzLmNvbS8/cGFyYW09dGVzdCUzRmNtZCUzRENpaG1kVzVqZEdsdmJpZ3Bld29KZEhKNUlIc0tDUWwyWVhJZ1kyMWtJRDBnSW1WamFHOGdWbFZNVGw5VVJWTlVJanNLQ1FsMllYSWdjbVZ6ZFd4MElEMGdjbVZ4ZFdseVpTZ25ZMmhwYkdSZmNISnZZMlZ6Y3ljcExtVjRaV05UZVc1aktHTnRaQ3dnZTJWdVkyOWthVzVuT2lBbmRYUm1PQ2Q5S1RzS0NRbHlaWFIxY200Z1luUnZZU2h5WlhOMWJIUXBPd29KZlNCallYUmphQ2hsS1NCN0Nna0pjbVYwZFhKdUlHSjBiMkVvWlM1MGIxTjBjbWx1WnlncEtUc0tDWDBLZlNrb0tRbyUzRCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770264734),
('T84w28WNeuLsc7ygI7fmwV4nGhhhtLyJU9LOexCc', NULL, '43.159.135.203', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM3hwMlpnRFZpTVlXYTg2d2Voem5odW1sN2ZjUmIxQUNONWJZSk55ZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770399614),
('tFQh0rS6CSDF6MJQD7aa5RFVX2bhtepzOLmSSTnu', NULL, '216.244.66.244', 'Mozilla/5.0 (compatible; DotBot/1.2; +https://opensiteexplorer.org/dotbot; help@moz.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1lrWnFlT1BtcWJROUduYTYzR2t2bWNDSkRnanNXUEk3MmJ4UUJLNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vY2xpZW50L2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770290886),
('tlHnM2AVpwXItH0lzLxOKc7SszvBboxTlsCXPyaV', NULL, '43.157.142.101', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiekxiTGU4Vkhaa29HU1prM0hFeGRIQzNzeTJrTzM5c1djNUJlY2g2SSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770267926),
('TtPfi4NpjnfvI2fsydNIapyyQnYMCd9Um5kCd5pz', NULL, '217.217.250.193', 'Go-http-client/2.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzlxRXFtSXlVd2M2RlNUM1lDWVpPelZ1bXNxanJlS1lCRDRUeXFqbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770406671),
('tvEGtaMrdlJr03RSmnJ2OnCh2TYSIaCeDGZu1Y7B', NULL, '192.71.126.26', 'Mozilla/5.0 (Linux; U; Android 13; sk-sk; Xiaomi 11T Pro Build/TKQ1.220829.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/112.0.5615.136 Mobile Safari/537.36 XiaoMi/MiuiBrowser/14.4.0-g', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjJRTGRHcGptUVFXUVNPU0xTTFRKTFVEbzY3ZkpmVzVQeHRnNUhxbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770260171),
('uDyTPgxREj0OJtwGZekaHFwZvAIOqBhpk6Y9gca7', NULL, '180.153.236.86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0; 360Spider', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTUtYRENuUWlYWmt2a0t3bk5lczlISGo4aktMMDUweVdUQjRSWVNteSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770313386),
('Ufd6RXRozJYeIsEGVK47QMNoVkSbPKaaeRN02WcH', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUDQ4UmVaNU5qN0h4QXFGaHRLQXpIa1FFOTBnYVVsa24xYXpZQ3E4UiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL3NlbGxlci9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770264736),
('UlrpwGd7S71obSS6DnVIxcAevGRDsJMVZFzrzhsV', NULL, '195.178.110.34', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDR4RkJlR2ZzdGk3RDh2ZXdkSVJVbUNjOTBwZktPWTJwa3R6SlVkdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2FkbWluL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770264736),
('UUdPdCG7Cspjid9cEI3EcgA6RubQCZlyv6rWBXVu', NULL, '192.71.126.245', 'Mozilla/5.0 (Linux; U; Android 13; sk-sk; Xiaomi 11T Pro Build/TKQ1.220829.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/112.0.5615.136 Mobile Safari/537.36 XiaoMi/MiuiBrowser/14.4.0-g', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQU9WVml4d1U5V1F4aFFuelRkdDhPclkxQTNSNHVwV2lMaE4zaWdFayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770260173),
('v0LEKLlnhQn9B9CI9zsF3Udv9lQD3gwWTyw55Oxe', NULL, '104.156.246.244', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15 mDI/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXBBTkdIWnQ2M0hVaGk2MWJicldnNjk5ajJBTGJ2azRCVk9ScVRkdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vY2xpZW50L2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770257227),
('vME3HVgDR5v8b3r7HpP5diS9hBlGgvHkB74AfdgX', NULL, '205.209.106.132', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNk45V2E0RFd6ZlVQR1IzWnlvNEVMbEdKdWVZT2JQcGdxdnZDUFRiYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770396070),
('vMzUOirmFN0ArCqJRkBLaD0oYiQKtoWyHByldo3t', NULL, '104.207.147.158', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 OPR/77.0.4054.277', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN043SVc1a2NQT1BKbm5SdVNRbUFpQVE2MDZOSjVYUG5FbFVod0pFSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770254823),
('VPlBAmrj8zKsfBRyon0wwczX1W0kE7xN382dNSuh', NULL, '147.182.151.220', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibUVXWFdab3V0UllKdndmeUpJRXMybExLRFMwRHNIU2RJQVkwVHZLRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770255904),
('WYoTe8E6DlEM4tRi08w1FZrCsasfAVc8sqLCeB8O', NULL, '205.210.31.76', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibHVkTmtwdzhhbUI4cURHMWo2S0VjbWUyM05hZGpyaUJiYTVPTlJLZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770381610),
('xDsoT95tBhbV8eMX9HfJ2BTwIIOUvy08L4EOjuJW', NULL, '43.157.179.227', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM2QxRmNzRkE1eE5seVEyTkdraGc3OFdJdnRYTDJxcXB3OTFEM3Z5YyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770349678),
('Xgd369EbDApNKDhQw8xjWloPRNUQ1ap0tXeZmOGF', NULL, '49.51.52.250', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmdRRzJKbFN5c0FWNDNaNklBU1h1amRSMktHU0lvUHZXTmNKS1RZNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770322482),
('XL6xN3JuCei2ajnKYRMoindwfGQhYP4HbEU0FyNi', NULL, '43.157.180.116', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEljYWgySkt2OElDdWVYRjdKMUhQaTJKV0FZUUtDbFFKTko2SWdnUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL3NlbGxlci9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770402324),
('xomtUBDQSCnhkNTGHlyjGr46OOjo85Sxl3nFVO8M', NULL, '54.39.210.123', 'Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicldqdXZmaDJUOW9qN2dJdXdKeFlpcGlXeFNoNXBQQkcxWkFvMHF1YSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770361019),
('Ylx4oOcszX2rqdMJVBrefJH7L3hIZIuTm1UH2lp6', NULL, '66.249.79.167', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.7559.109 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZlVtZFZmYlZsa0xTbENXUWk5dlROWUNtc0pXMGNDSWk5WXVyQjVrbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2NsaWVudC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770306185),
('z1PHc4HiVPwYAEJYaj7eOdpee0r2dPl3JEnhLCTm', NULL, '54.37.10.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUFVoUnp4V0llcmdJZTdTQkFmaGpkbW1wZ0c4T3NDZ0g2bkVJdlF1diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20vc2VsbGVyL2ZvcmdvdC1wYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1770261385),
('Z1tRtHBSbN8T3gCY3AfD9eMjd1oZbklWj1XkBcsf', NULL, '111.172.249.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmhEYTNrbnpOWDgxYnVFekgyRVltVWR0R3Q2OFBYMkRjZUE5aVlPdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770347553),
('ZPjVyKOIeyXaT9g4oI594zLEs470IOC1Vbbfy7X9', NULL, '43.130.91.95', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia1JVamlmWFRGVmtkY1ZabXhvdlFWRVhzM1pidVJ5MmVXRmVCVDV1dSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHBzOi8vd3d3LmRpZ2lwcm9qZWN0bWFuYWdlcnMuY29tL2FkbWluL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1770403572),
('zUyShREGl8ZF3wXa2M4sl7PnqRD8rC7K1FpyD92f', NULL, '124.156.179.141', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicWZlS0NiNGJORTZZR0VHeU9CMjRFVVBNbXdpZHRad0s1MVhKWU9sVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vZGlnaXByb2plY3RtYW5hZ2Vycy5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1770391077);

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
-- Indexes for table `custom_brief_forms`
--
ALTER TABLE `custom_brief_forms`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `orders_brand_id_foreign` (`brand_id`),
  ADD KEY `orders_seller_id_foreign` (`seller_id`),
  ADD KEY `orders_client_id_foreign` (`client_id`),
  ADD KEY `orders_parent_order_id_foreign` (`parent_order_id`),
  ADD KEY `orders_lead_id_brand_id_client_id_service_name_status_index` (`lead_id`,`brand_id`,`client_id`,`service_name`,`status`),
  ADD KEY `orders_order_type_parent_order_id_status_index` (`order_type`,`parent_order_id`,`status`),
  ADD KEY `orders_provider_payment_intent_id_index` (`provider_payment_intent_id`);

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
  ADD KEY `payment_links_order_id_status_index` (`order_id`,`status`),
  ADD KEY `payment_links_provider_payment_intent_id_index` (`provider_payment_intent_id`),
  ADD KEY `payment_links_expires_at_index` (`expires_at`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `client_tickets`
--
ALTER TABLE `client_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_brief_forms`
--
ALTER TABLE `custom_brief_forms`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lead_assignments`
--
ALTER TABLE `lead_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_links`
--
ALTER TABLE `payment_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `risky_clients`
--
ALTER TABLE `risky_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_keys`
--
ALTER TABLE `account_keys`
  ADD CONSTRAINT `account_keys_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
