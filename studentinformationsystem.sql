-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 05:21 AM
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
-- Database: `studentinformationsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `academics`
--

CREATE TABLE `academics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subject_code` varchar(255) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `schedule` varchar(255) NOT NULL,
  `room` varchar(255) DEFAULT NULL,
  `instructor` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) NOT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `units` int(11) NOT NULL DEFAULT 3,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'general',
  `target_audience` varchar(255) NOT NULL DEFAULT 'all',
  `target_course` varchar(255) DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendance_code_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_codes`
--

CREATE TABLE `attendance_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_session_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-boost.roster.scan', 'a:2:{s:6:\"roster\";O:21:\"Laravel\\Roster\\Roster\":3:{s:13:\"\0*\0approaches\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:8:{i:0;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^12.0\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:LARAVEL\";s:14:\"\0*\0packageName\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"12.41.1\";s:6:\"\0*\0dev\";b:0;}i:1;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:6:\"v0.3.8\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PROMPTS\";s:14:\"\0*\0packageName\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:5:\"0.3.8\";s:6:\"\0*\0dev\";b:0;}i:2;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:6:\"v0.3.4\";s:10:\"\0*\0package\";E:33:\"Laravel\\Roster\\Enums\\Packages:MCP\";s:14:\"\0*\0packageName\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"0.3.4\";s:6:\"\0*\0dev\";b:1;}i:3;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.24\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:PINT\";s:14:\"\0*\0packageName\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.26.0\";s:6:\"\0*\0dev\";b:1;}i:4;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.41\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:SAIL\";s:14:\"\0*\0packageName\";s:12:\"laravel/sail\";s:10:\"\0*\0version\";s:6:\"1.50.0\";s:6:\"\0*\0dev\";b:1;}i:5;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.8\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:PEST\";s:14:\"\0*\0packageName\";s:12:\"pestphp/pest\";s:10:\"\0*\0version\";s:5:\"3.8.4\";s:6:\"\0*\0dev\";b:1;}i:6;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:7:\"11.5.33\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PHPUNIT\";s:14:\"\0*\0packageName\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"11.5.33\";s:6:\"\0*\0dev\";b:1;}i:7;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:10:\"\0*\0package\";E:41:\"Laravel\\Roster\\Enums\\Packages:TAILWINDCSS\";s:14:\"\0*\0packageName\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:6:\"4.1.17\";s:6:\"\0*\0dev\";b:1;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:21:\"\0*\0nodePackageManager\";E:43:\"Laravel\\Roster\\Enums\\NodePackageManager:NPM\";}s:9:\"timestamp\";i:1765248964;}', 1765335364);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_sessions`
--

CREATE TABLE `class_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `course` varchar(255) DEFAULT NULL,
  `course_id` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `schedule` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `instructor` varchar(255) DEFAULT NULL,
  `room` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_sessions`
--

INSERT INTO `class_sessions` (`id`, `name`, `start_time`, `end_time`, `description`, `is_active`, `course`, `course_id`, `subject`, `schedule`, `time`, `instructor`, `room`, `created_at`, `updated_at`) VALUES
(13, 'CS 101 - Introduction to Computer Science', '07:00:00', '10:00:00', NULL, 1, 'Computer Science', 'CS 101', 'Introduction to Computer Science', 'MTW', '7:00 AM - 10:00 AM', 'Adrian Miguel Santos', 'LAB 3', '2025-12-09 00:49:40', '2025-12-09 00:49:40');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `gender` varchar(255) NOT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `course_selected` varchar(255) NOT NULL,
  `year_level` varchar(255) NOT NULL,
  `guardian_name` varchar(255) NOT NULL,
  `guardian_contact` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `full_name`, `address`, `email`, `birthday`, `gender`, `previous_school`, `course_selected`, `year_level`, `guardian_name`, `guardian_contact`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(23, 29, 'Juan Dela Cruz', 'SPC', 'juandelacruz@gmail.com', '2001-10-01', 'Male', 'School', 'Computer Science', '1st Year', 'Carlos Dela Cruz', '098187123', 'approved', 'Enrollment approved by admin.', '2025-12-09 00:30:11', '2025-12-09 00:35:51'),
(24, 30, 'John Doe', 'SPC', 'johndoe@gmail.com', '2001-01-02', 'Male', 'SCHOOL', 'Information Technology', '1st Year', 'Mary Doe', '12391841', 'approved', 'Enrollment approved by admin.', '2025-12-09 00:31:20', '2025-12-09 00:35:54'),
(25, 31, 'Anne D. Toako', 'SPC', 'annedtoako@gmail.com', '2002-02-02', 'Female', 'Secret', 'Business Administration', '1st Year', 'Sharon Toako', '121523634', 'approved', 'Enrollment approved by admin.', '2025-12-09 00:32:43', '2025-12-09 00:35:58'),
(26, 32, 'Boy Reyes', 'SPC', 'boyreyes@gmail.com', '2003-04-02', 'Male', 'School', 'Engineering', '1st Year', 'Lito Reyes', '1341834', 'approved', 'Enrollment approved by admin.', '2025-12-09 00:34:26', '2025-12-09 00:36:02'),
(27, 33, 'Mae Kaya', 'SPC', 'maekaya@gmail.com', '2002-03-14', 'Female', 'SCHOOL', 'Education', '1st Year', 'Mary Kaya', '2131412', 'approved', 'Enrollment approved by admin.', '2025-12-09 00:35:30', '2025-12-09 00:36:05');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `semester` varchar(255) NOT NULL,
  `academic_year` varchar(255) NOT NULL,
  `prelim` decimal(5,2) DEFAULT NULL,
  `midterm` varchar(10) DEFAULT NULL,
  `prefinal` decimal(5,2) DEFAULT NULL,
  `final` varchar(10) DEFAULT NULL,
  `average` decimal(5,2) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'approved',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_04_133408_add_student_id_and_role_to_users_table', 1),
(5, '2025_12_04_133851_create_students_table', 1),
(6, '2025_12_04_140000_create_students_table', 2),
(7, '2025_12_04_140100_create_school_events_table', 2),
(8, '2025_12_04_150000_add_student_profile_fields_to_users_table', 3),
(9, '2025_12_04_150100_create_enrollments_table', 3),
(10, '2025_12_04_150200_create_academics_table', 3),
(11, '2025_12_05_151649_add_monthly_tuition_to_users_table', 4),
(12, '2025_12_05_155328_create_student_documents_table', 5),
(13, '2025_12_05_155403_create_student_preferences_table', 5),
(14, '2025_12_05_155410_create_sections_table', 5),
(15, '2025_12_05_155417_create_subjects_table', 5),
(16, '2025_12_05_155422_create_grades_table', 5),
(17, '2025_12_05_155431_create_attendances_table', 5),
(18, '2025_12_05_155443_create_announcements_table', 5),
(19, '2025_12_05_155514_create_student_requests_table', 5),
(20, '2025_12_05_155521_add_section_id_to_users_table', 5),
(21, '2025_12_05_161359_add_deleted_at_to_users_table', 6),
(22, '2025_12_06_010912_create_attendance_codes_table', 7),
(23, '2025_12_06_010934_add_attendance_code_id_to_attendances_table', 7),
(24, '2025_12_06_012016_create_class_sessions_table', 8),
(25, '2025_12_06_012024_create_student_notifications_table', 9),
(26, '2025_12_06_012049_update_attendance_codes_to_use_class_sessions', 9),
(27, '2025_12_06_012228_create_user_class_session_table', 9),
(28, '2025_12_06_032722_create_messages_table', 10),
(29, '2025_12_06_032733_create_student_ids_table', 11),
(30, '2025_12_07_100408_change_midterm_final_to_string_for_inc', 12),
(31, '2025_12_08_060525_add_target_course_to_student_requests_table', 13),
(32, '2025_12_08_062310_add_file_fields_to_messages_table', 14),
(33, '2025_12_09_000000_add_profile_image_to_users_table', 15),
(34, '2025_12_06_025914_create_session_enrollments_table', 16),
(35, '2025_12_08_000001_add_position_to_users_table', 16),
(36, '2025_12_08_010000_add_status_to_grades_table', 17),
(37, '2025_12_08_120000_update_class_sessions_for_schedule', 18),
(38, '2025_12_09_010000_add_course_to_class_sessions_table', 18),
(39, '2025_12_09_035539_add_time_and_schedule_to_subjects_table', 19);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_events`
--

CREATE TABLE `school_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year_level` varchar(255) NOT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `academic_year` varchar(255) DEFAULT NULL,
  `adviser_id` bigint(20) UNSIGNED DEFAULT NULL,
  `max_students` int(11) NOT NULL DEFAULT 50,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `course`, `year_level`, `semester`, `academic_year`, `adviser_id`, `max_students`, `created_at`, `updated_at`) VALUES
(6, 'CS-1A', 'Computer Science', '1st Year', NULL, NULL, NULL, 10, '2025-12-09 00:35:51', '2025-12-09 00:35:51'),
(7, 'IT-1A', 'Information Technology', '1st Year', NULL, NULL, NULL, 10, '2025-12-09 00:35:54', '2025-12-09 00:35:54'),
(8, 'BA-1A', 'Business Administration', '1st Year', NULL, NULL, NULL, 10, '2025-12-09 00:35:58', '2025-12-09 00:35:58'),
(9, 'ENG-1A', 'Engineering', '1st Year', NULL, NULL, NULL, 10, '2025-12-09 00:36:02', '2025-12-09 00:36:02'),
(10, 'EDU-1A', 'Education', '1st Year', NULL, NULL, NULL, 10, '2025-12-09 00:36:05', '2025-12-09 00:36:05');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('G8jmxd7Y0Z2eS7iC1exu4kLMfUVaFav02uEqd9Id', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.50 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMXQ2eHZwemxsa3FNdWVBcWdGZFFpbk1HV05ZbENFZGMxSnp3TUhWdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1765267877),
('gwiKlvbDkpK72pbNGWinGxOmrfJmF1sIJYVppFl2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.50 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidUV3MzNJN2l6bU9hdDl6a1BXS05HSkl4NDBwbWpxVzZLZEdISEYxWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1765270400),
('hh0tG2IuTMJtExUWcvs6etfSMYd0ojCOHVojl3YX', 29, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSEJ2aWFmaVpWQzFuaUt1M3doY3hSdDlaZllNbG45ekJsekh2SlgyayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvbm90aWZpY2F0aW9ucyI7czo1OiJyb3V0ZSI7czoxOToibm90aWZpY2F0aW9ucy5mZXRjaCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI5O30=', 1765270310),
('lV32sRZ0LOpUDwJD3GmEiEmUnkNX4u5fSxZaW1YL', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicklTazZHdHFFSE42Z0N3Qnh0aFdObTZoWk9LZ2FGZlJTVUN6R3d0RiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1765270481);

-- --------------------------------------------------------

--
-- Table structure for table `session_enrollments`
--

CREATE TABLE `session_enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `class_session_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_code_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_date` date NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resigned_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `enrollment_status` varchar(255) NOT NULL DEFAULT 'not_enrolled',
  `enrolled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

CREATE TABLE `student_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_ids`
--

CREATE TABLE `student_ids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_ids`
--

INSERT INTO `student_ids` (`id`, `student_id`, `status`, `assigned_to`, `created_by`, `assigned_at`, `used_at`, `created_at`, `updated_at`) VALUES
(133, '0000-001', 'used', 29, 3, NULL, '2025-12-09 00:29:19', '2025-12-09 00:28:02', '2025-12-09 00:29:19'),
(134, '0000-002', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(135, '0000-003', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(136, '0000-004', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(137, '0000-005', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(138, '0000-006', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(139, '0000-007', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(140, '0000-008', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(141, '0000-009', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(142, '0000-010', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:02', '2025-12-09 00:28:02'),
(143, '0001-001', 'used', 30, 3, NULL, '2025-12-09 00:30:52', '2025-12-09 00:28:09', '2025-12-09 00:30:52'),
(144, '0001-002', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(145, '0001-003', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(146, '0001-004', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(147, '0001-005', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(148, '0001-006', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(149, '0001-007', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(150, '0001-008', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(151, '0001-009', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(152, '0001-010', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:09', '2025-12-09 00:28:09'),
(153, '0002-001', 'used', 31, 3, NULL, '2025-12-09 00:32:11', '2025-12-09 00:28:16', '2025-12-09 00:32:11'),
(154, '0002-002', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(155, '0002-003', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(156, '0002-004', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(157, '0002-005', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(158, '0002-006', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(159, '0002-007', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(160, '0002-008', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(161, '0002-009', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(162, '0002-010', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:16', '2025-12-09 00:28:16'),
(163, '0004-001', 'used', 32, 3, NULL, '2025-12-09 00:33:45', '2025-12-09 00:28:26', '2025-12-09 00:33:45'),
(164, '0004-002', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(165, '0004-003', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(166, '0004-004', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(167, '0004-005', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(168, '0004-006', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(169, '0004-007', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(170, '0004-008', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(171, '0004-009', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(172, '0004-010', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:26', '2025-12-09 00:28:26'),
(173, '0005-001', 'used', 33, 3, NULL, '2025-12-09 00:35:03', '2025-12-09 00:28:33', '2025-12-09 00:35:03'),
(174, '0005-002', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33'),
(175, '0005-003', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33'),
(176, '0005-004', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33'),
(177, '0005-005', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33'),
(178, '0005-006', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33'),
(179, '0005-007', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33'),
(180, '0005-008', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33'),
(181, '0005-009', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33'),
(182, '0005-010', 'available', NULL, 3, NULL, NULL, '2025-12-09 00:28:33', '2025-12-09 00:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `student_notifications`
--

CREATE TABLE `student_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_code_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_notifications`
--

INSERT INTO `student_notifications` (`id`, `user_id`, `attendance_code_id`, `type`, `title`, `message`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(121, 29, NULL, 'enrollment', 'Enrollment Submitted ⏳', 'Your enrollment has been submitted successfully. Please wait for the approval of registrar to access all features.', 0, NULL, '2025-12-09 00:30:11', '2025-12-09 00:30:11'),
(122, 30, NULL, 'enrollment', 'Enrollment Submitted ⏳', 'Your enrollment has been submitted successfully. Please wait for the approval of registrar to access all features.', 0, NULL, '2025-12-09 00:31:20', '2025-12-09 00:31:20'),
(123, 31, NULL, 'enrollment', 'Enrollment Submitted ⏳', 'Your enrollment has been submitted successfully. Please wait for the approval of registrar to access all features.', 0, NULL, '2025-12-09 00:32:43', '2025-12-09 00:32:43'),
(124, 32, NULL, 'enrollment', 'Enrollment Submitted ⏳', 'Your enrollment has been submitted successfully. Please wait for the approval of registrar to access all features.', 0, NULL, '2025-12-09 00:34:26', '2025-12-09 00:34:26'),
(125, 33, NULL, 'enrollment', 'Enrollment Submitted ⏳', 'Your enrollment has been submitted successfully. Please wait for the approval of registrar to access all features.', 0, NULL, '2025-12-09 00:35:30', '2025-12-09 00:35:30'),
(126, 29, NULL, 'enrollment', 'Enrollment Approved ✅', 'You have been successfully enrolled. Access to Attendance and Subjects is now enabled. You have been assigned to section CS-1A.', 0, NULL, '2025-12-09 00:35:51', '2025-12-09 00:35:51'),
(127, 30, NULL, 'enrollment', 'Enrollment Approved ✅', 'You have been successfully enrolled. Access to Attendance and Subjects is now enabled. You have been assigned to section IT-1A.', 0, NULL, '2025-12-09 00:35:54', '2025-12-09 00:35:54'),
(128, 31, NULL, 'enrollment', 'Enrollment Approved ✅', 'You have been successfully enrolled. Access to Attendance and Subjects is now enabled. You have been assigned to section BA-1A.', 0, NULL, '2025-12-09 00:35:58', '2025-12-09 00:35:58'),
(129, 32, NULL, 'enrollment', 'Enrollment Approved ✅', 'You have been successfully enrolled. Access to Attendance and Subjects is now enabled. You have been assigned to section ENG-1A.', 0, NULL, '2025-12-09 00:36:02', '2025-12-09 00:36:02'),
(130, 33, NULL, 'enrollment', 'Enrollment Approved ✅', 'You have been successfully enrolled. Access to Attendance and Subjects is now enabled. You have been assigned to section EDU-1A.', 0, NULL, '2025-12-09 00:36:05', '2025-12-09 00:36:05');

-- --------------------------------------------------------

--
-- Table structure for table `student_preferences`
--

CREATE TABLE `student_preferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `theme` varchar(255) NOT NULL DEFAULT 'light',
  `language` varchar(255) NOT NULL DEFAULT 'en',
  `notifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notifications`)),
  `sidebar_mode` varchar(255) NOT NULL DEFAULT 'expanded',
  `2fa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_preferences`
--

INSERT INTO `student_preferences` (`id`, `user_id`, `theme`, `language`, `notifications`, `sidebar_mode`, `2fa_enabled`, `security_question`, `security_answer`, `bio`, `profile_image`, `created_at`, `updated_at`) VALUES
(5, 3, 'light', 'en', '{\"preferences\":{\"items_per_page\":\"10\",\"date_format\":\"Y-m-d\",\"time_format\":\"12\",\"show_statistics\":true,\"show_recent_activity\":true,\"auto_refresh\":false}}', 'expanded', 0, NULL, NULL, NULL, NULL, '2025-12-07 23:04:56', '2025-12-07 23:07:45'),
(7, 29, 'light', 'en', '{\"grade_updates\":true,\"enrollment_status\":true,\"announcements\":true,\"attendance_alerts\":true}', 'expanded', 0, NULL, NULL, NULL, NULL, '2025-12-09 00:50:54', '2025-12-09 00:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `student_requests`
--

CREATE TABLE `student_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `request_type` varchar(255) NOT NULL,
  `target_course` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `admin_remarks` text DEFAULT NULL,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_code` varchar(255) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year_level` varchar(255) NOT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `units` int(11) NOT NULL,
  `hours_per_week` int(11) DEFAULT NULL,
  `schedule` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `instructor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `course`, `year_level`, `semester`, `units`, `hours_per_week`, `schedule`, `time`, `description`, `instructor_id`, `created_at`, `updated_at`) VALUES
(7, 'IT 101', 'Introduction to Information Technology', 'Information Technology', '1st Year', '1ST SEMESTER', 3, 5, NULL, NULL, NULL, 35, '2025-12-09 00:45:37', '2025-12-09 00:45:37'),
(8, 'CS 101', 'Introduction to Computer Science', 'Computer Science', '1st Year', '1ST SEMESTER', 3, 4, NULL, NULL, NULL, 34, '2025-12-09 00:46:22', '2025-12-09 00:46:22'),
(9, 'ENG101', 'Engineering Mathematics', 'Engineering', '1st Year', '1ST SEMESTER', 3, 6, NULL, NULL, NULL, 36, '2025-12-09 00:47:02', '2025-12-09 00:47:02'),
(10, 'EDU 101', 'Foundation of Education', 'Education', '1st Year', '1ST SEMESTER', 3, 6, NULL, NULL, NULL, 38, '2025-12-09 00:47:48', '2025-12-09 00:47:48'),
(11, 'BA 101', 'Business Communication', 'Business Administration', '1st Year', '1ST SEMESTER', 3, 3, NULL, NULL, NULL, 37, '2025-12-09 00:48:21', '2025-12-09 00:48:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'student',
  `address` text DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_contact` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `monthly_tuition` decimal(10,2) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `student_id`, `name`, `email`, `role`, `address`, `birthday`, `contact_number`, `profile_image`, `gender`, `guardian_name`, `guardian_contact`, `course`, `position`, `year_level`, `section_id`, `monthly_tuition`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, NULL, 'Administrator', 'admin@sis.com', 'admin', NULL, NULL, '09123456789', 'profiles/3/oPSqflsDlMSvDUmPpZqRrGmJIWD7LS8aBY7v9mjl.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$.pNwdNlaKwwacCweQ3pU.ukwwmjo11rDPR0rKLGA/.DWXxDQ5HuA.', NULL, '2025-12-05 08:36:01', '2025-12-07 23:31:38', NULL),
(29, '0000-001', 'Juan Dela Cruz', 'juandelacruz@gmail.com', 'student', 'SPC', '2001-10-01', NULL, NULL, 'Male', 'Carlos Dela Cruz', '098187123', 'Computer Science', NULL, '1st Year', 6, NULL, NULL, '$2y$12$wfgRr6RCULs0A5tOL7QI9.ENn.XlG1oM2/rEr0668430nl2aGOD1i', NULL, '2025-12-09 00:29:19', '2025-12-09 00:35:51', NULL),
(30, '0001-001', 'John Doe', 'johndoe@gmail.com', 'student', 'SPC', '2001-01-02', NULL, NULL, 'Male', 'Mary Doe', '12391841', 'Information Technology', NULL, '1st Year', 7, NULL, NULL, '$2y$12$RL/4cuideSdc3OCU1BL0wuImLYNLpq0m58rtfAIiv9YBGJJKkL4ia', NULL, '2025-12-09 00:30:52', '2025-12-09 00:35:54', NULL),
(31, '0002-001', 'Anne D. Toako', 'annedtoako@gmail.com', 'student', 'SPC', '2002-02-02', NULL, NULL, 'Female', 'Sharon Toako', '121523634', 'Business Administration', NULL, '1st Year', 8, NULL, NULL, '$2y$12$9Gn.ZGUVSHT.BGMzsxZtru3TpaoVr/EWl/Xk87g1vtRKq03qjZfXu', NULL, '2025-12-09 00:32:11', '2025-12-09 00:35:58', NULL),
(32, '0004-001', 'Boy Reyes', 'boyreyes@gmail.com', 'student', 'SPC', '2003-04-02', NULL, NULL, 'Male', 'Lito Reyes', '1341834', 'Engineering', NULL, '1st Year', 9, NULL, NULL, '$2y$12$0BeN0gvb/iu/AP/4/SYc8eJ.OZkA/5qcP.IrNEmBxNKeGKrfMFSN.', NULL, '2025-12-09 00:33:45', '2025-12-09 00:36:02', NULL),
(33, '0005-001', 'Mae Kaya', 'maekaya@gmail.com', 'student', 'SPC', '2002-03-14', NULL, NULL, 'Female', 'Mary Kaya', '2131412', 'Education', NULL, '1st Year', 10, NULL, NULL, '$2y$12$6GaauYLmTtTCrnKgsL2Cse/H9kvxoyaDMyuid1Zhf4Tpt1ezyAbLq', NULL, '2025-12-09 00:35:03', '2025-12-09 00:36:05', NULL),
(34, NULL, 'Adrian Miguel Santos', 'adrian.miguelsantos@edu.ph', 'teacher', 'Brgy. San Isidro, Cainta, Rial', NULL, '09174528893', NULL, 'Male', NULL, NULL, 'Computer Science', 'Associate Professor', '1st Year', NULL, NULL, NULL, '$2y$12$aNS.bRSe342aSh0o6LQWCeMrMkOx.byXsai13HTkCZ8Z/Q/72tVKK', NULL, '2025-12-09 00:37:32', '2025-12-09 00:37:32', NULL),
(35, NULL, 'Leanne Grace Villanueva', 'leanne.gracevillanueva@edu.ph', 'teacher', 'Poblacion, San Pedto, Laguna', NULL, '09952184470', NULL, 'Female', NULL, NULL, 'Information Technology', 'Instructor I', '1st Year', NULL, NULL, NULL, '$2y$12$mjPN6zURt73brpog9dZq2.GjR03stVcd596XqCPgKHXGVyrVim6Ge', NULL, '2025-12-09 00:38:27', '2025-12-09 00:38:27', NULL),
(36, NULL, 'Carlo Bennett Flores', 'carlo.bennettflores@edu.ph', 'teacher', 'Banilad, Cebu City', NULL, '09387716402', NULL, 'Male', NULL, NULL, 'Engineering', 'Instructor I', '1st Year', NULL, NULL, NULL, '$2y$12$qTBMC8Uj44URAiTrNSvceu4EGPytM4GumKEgSw4Sf.obgLrSNZVtK', NULL, '2025-12-09 00:40:27', '2025-12-09 00:40:27', NULL),
(37, NULL, 'Marian Joyce Herrera', 'marian.joyceherrera@edu.ph', 'teacher', 'Manila', NULL, '09205632218', NULL, 'Female', NULL, NULL, 'Business Administration', 'Associate Professor', '1st Year', NULL, NULL, NULL, '$2y$12$JdDBeZEWGWz0W9GGs4Lw7u8M5xP32S/SrfFY1cazKdV6BQFtdTolq', NULL, '2025-12-09 00:41:15', '2025-12-09 00:41:15', NULL),
(38, NULL, 'Kimberly Shane Dacumos', 'kimberly.shanedacumos@edu.ph', 'teacher', 'Munglanilla, Cebu', NULL, '09068819004', NULL, 'Female', NULL, NULL, 'Education', 'Associate Professor I', '1st Year', NULL, NULL, NULL, '$2y$12$4.ahyYj5w.HS/Z2kzCuDfu3u58w8Ml3o.Bm8VzP1T7dSDO0SUmPqO', NULL, '2025-12-09 00:42:09', '2025-12-09 00:42:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_class_session`
--

CREATE TABLE `user_class_session` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `class_session_id` bigint(20) UNSIGNED NOT NULL,
  `enrolled_date` date NOT NULL DEFAULT '2025-12-06',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academics`
--
ALTER TABLE `academics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academics_user_id_foreign` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_user_id_foreign` (`user_id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_user_id_foreign` (`user_id`),
  ADD KEY `attendances_subject_id_foreign` (`subject_id`),
  ADD KEY `attendances_attendance_code_id_foreign` (`attendance_code_id`);

--
-- Indexes for table `attendance_codes`
--
ALTER TABLE `attendance_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_codes_code_unique` (`code`),
  ADD KEY `attendance_codes_created_by_foreign` (`created_by`),
  ADD KEY `attendance_codes_code_is_active_index` (`code`,`is_active`),
  ADD KEY `attendance_codes_class_session_id_date_index` (`class_session_id`,`date`);

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
-- Indexes for table `class_sessions`
--
ALTER TABLE `class_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_sessions_start_time_end_time_index` (`start_time`,`end_time`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollments_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grades_user_id_foreign` (`user_id`),
  ADD KEY `grades_subject_id_foreign` (`subject_id`),
  ADD KEY `grades_approved_by_foreign` (`approved_by`);

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
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_receiver_id_index` (`sender_id`,`receiver_id`),
  ADD KEY `messages_receiver_id_is_read_index` (`receiver_id`,`is_read`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `school_events`
--
ALTER TABLE `school_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sections_adviser_id_foreign` (`adviser_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `session_enrollments`
--
ALTER TABLE `session_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_enrollments_user_id_foreign` (`user_id`),
  ADD KEY `session_enrollments_class_session_id_foreign` (`class_session_id`),
  ADD KEY `session_enrollments_attendance_code_id_foreign` (`attendance_code_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_id_unique` (`student_id`),
  ADD UNIQUE KEY `students_email_unique` (`email`);

--
-- Indexes for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_documents_user_id_foreign` (`user_id`);

--
-- Indexes for table `student_ids`
--
ALTER TABLE `student_ids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_ids_student_id_unique` (`student_id`),
  ADD KEY `student_ids_assigned_to_foreign` (`assigned_to`),
  ADD KEY `student_ids_created_by_foreign` (`created_by`),
  ADD KEY `student_ids_student_id_index` (`student_id`),
  ADD KEY `student_ids_status_index` (`status`);

--
-- Indexes for table `student_notifications`
--
ALTER TABLE `student_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_notifications_attendance_code_id_foreign` (`attendance_code_id`),
  ADD KEY `student_notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `student_notifications_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `student_preferences`
--
ALTER TABLE `student_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_preferences_user_id_unique` (`user_id`);

--
-- Indexes for table `student_requests`
--
ALTER TABLE `student_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_requests_user_id_foreign` (`user_id`),
  ADD KEY `student_requests_processed_by_foreign` (`processed_by`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_subject_code_unique` (`subject_code`),
  ADD KEY `subjects_instructor_id_foreign` (`instructor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_student_id_unique` (`student_id`),
  ADD KEY `users_section_id_foreign` (`section_id`);

--
-- Indexes for table `user_class_session`
--
ALTER TABLE `user_class_session`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_class_session_user_id_class_session_id_unique` (`user_id`,`class_session_id`),
  ADD KEY `user_class_session_user_id_index` (`user_id`),
  ADD KEY `user_class_session_class_session_id_index` (`class_session_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academics`
--
ALTER TABLE `academics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance_codes`
--
ALTER TABLE `attendance_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `class_sessions`
--
ALTER TABLE `class_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `school_events`
--
ALTER TABLE `school_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `session_enrollments`
--
ALTER TABLE `session_enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_documents`
--
ALTER TABLE `student_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_ids`
--
ALTER TABLE `student_ids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `student_notifications`
--
ALTER TABLE `student_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `student_preferences`
--
ALTER TABLE `student_preferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_requests`
--
ALTER TABLE `student_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `user_class_session`
--
ALTER TABLE `user_class_session`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academics`
--
ALTER TABLE `academics`
  ADD CONSTRAINT `academics_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_attendance_code_id_foreign` FOREIGN KEY (`attendance_code_id`) REFERENCES `attendance_codes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_codes`
--
ALTER TABLE `attendance_codes`
  ADD CONSTRAINT `attendance_codes_class_session_id_foreign` FOREIGN KEY (`class_session_id`) REFERENCES `class_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_codes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_adviser_id_foreign` FOREIGN KEY (`adviser_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `session_enrollments`
--
ALTER TABLE `session_enrollments`
  ADD CONSTRAINT `session_enrollments_attendance_code_id_foreign` FOREIGN KEY (`attendance_code_id`) REFERENCES `attendance_codes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `session_enrollments_class_session_id_foreign` FOREIGN KEY (`class_session_id`) REFERENCES `class_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_enrollments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD CONSTRAINT `student_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_ids`
--
ALTER TABLE `student_ids`
  ADD CONSTRAINT `student_ids_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_ids_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_notifications`
--
ALTER TABLE `student_notifications`
  ADD CONSTRAINT `student_notifications_attendance_code_id_foreign` FOREIGN KEY (`attendance_code_id`) REFERENCES `attendance_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_preferences`
--
ALTER TABLE `student_preferences`
  ADD CONSTRAINT `student_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_requests`
--
ALTER TABLE `student_requests`
  ADD CONSTRAINT `student_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_class_session`
--
ALTER TABLE `user_class_session`
  ADD CONSTRAINT `user_class_session_class_session_id_foreign` FOREIGN KEY (`class_session_id`) REFERENCES `class_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_class_session_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
