-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 16, 2026 at 04:40 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `health_center_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `created_at`) VALUES
(1, 'admin', 'admin@healthcenter.com', '$2y$10$GvAgDXNRDIExB2NcWbYL3ux02tK/z9iwc3b46DrWBgvlE/brYgVX2', 'مدير النظام', '0966321569', '2025-11-18 16:38:29');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_name`, `appointment_date`, `appointment_time`, `status`, `notes`, `created_at`) VALUES
(1, 14, 'أحمد الخطيب', '2026-04-13', '16:36:00', 'cancelled', '', '2026-04-12 11:36:55'),
(2, 14, 'أحمد الخطيب', '2026-03-30', '16:36:00', 'pending', '', '2026-04-12 11:54:13'),
(4, 14, 'د. خالد سعيد', '2026-04-16', '10:55:00', 'pending', 'موعد مستعجل', '2026-04-12 11:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` int NOT NULL,
  `author_type` enum('doctor','specialist') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `published_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `author_id`, `author_type`, `published_at`) VALUES
(1, 'أهمية النشاط البدني للقلب', 'النشاط البدني المنتظم يقلل من مخاطر الإصابة بأمراض القلب بنسبة تصل إلى 30%. نوصي بممارسة الرياضة لمدة 30 دقيقة يوميًا.', 1, 'doctor', '2025-11-18 18:08:14'),
(2, 'استراتيجيات التعامل مع التوحد في المنزل', 'الآباء هم الشريك الأساسي في علاج طفل التوحد. إليكم أهم الاستراتيجيات التي يمكن تطبيقها في المنزل لدعم تطور الطفل.', 2, 'specialist', '2025-11-18 18:08:14'),
(3, 'فهم طيف التوحد: دليل شامل للآباء', 'طيف التوحد (Autism Spectrum Disorder - ASD) هو اضطراب في النمو العصبي يؤثر على التواصل الاجتماعي والسلوك. يظهر عادة في السنوات الأولى من الحياة ويستمر مدى الحياة. التشخيص المبكر والتدخل العلاجي يمكن أن يحسن بشكل كبير من جودة حياة الطفل المصاب.', 1, 'doctor', '2025-11-18 18:12:16');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'قسم الجلدية', 'تشخيص وعلاج الأمراض الجلدية وزرع الشعر', '🧴', 'active', '2025-11-19 10:25:26', '2026-04-15 17:50:33'),
(2, 'قسم الليزر', 'علاجات الليزر للبشرة ', '⚡', 'active', '2025-11-19 10:25:26', '2025-11-19 10:26:39'),
(3, 'قسم التحاليل الطبية', 'إجراء كافة أنواع التحاليل المخبرية', '🧪', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(4, 'قسم طب الأسرة', 'رعاية صحية شاملة لجميع أفراد العائلة', '👨‍👩‍👧‍👦', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(5, 'قسم التغذية', 'استشارات غذائية وتخطيط أنظمة غذائية', '🥗', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(6, 'قسم الصحة النفسية', 'دعم نفسي وعلاج اضطرابات الصحة العقلية', '🧠', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(7, 'قسم العلاج الطبيعي', 'جلسات علاج طبيعي لإعادة التأهيل', '💪', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(8, 'قسم طب الأسنان', 'خدمات طب الأسنان والعلاجات التجميلية', '🦷', 'inactive', '2025-11-19 10:25:26', '2026-04-15 17:50:48'),
(11, 'قسم العلاج السلوكي', 'يهتم بأطفال التوحد', '🧒', 'active', '2026-04-15 17:53:03', '2026-04-15 18:29:14');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `gender` enum('male','female') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `full_name`, `email`, `phone`, `specialization`, `license_number`, `status`, `gender`, `created_at`, `updated_at`) VALUES
(1, 'د. أحمد محمد علي', 'ahmed.doctor@healthcenter.com', '0501234567', 'أمراض القلب', 'LIC-2025-001', 'suspended', 'male', '2025-11-18 17:12:13', '2025-11-18 17:20:52'),
(6, 'د. فاطمة حسن', 'fatima.doctor@healthcenter.com', '0507654321', 'طب الأسرة', 'LIC-2025-002', 'approved', 'female', '2025-11-18 17:20:05', '2025-11-18 17:26:09'),
(7, 'د. خالد سعيد', 'khalid.doctor@healthcenter.com', '0501122334', 'الجراحة العامة', 'LIC-2025-003', 'approved', 'male', '2025-11-18 17:20:05', '2025-11-18 17:26:11'),
(8, 'د. نورة عبد الله', 'nora.doctor@healthcenter.com', '0504455667', 'النساء والتوليد', 'LIC-2025-004', 'approved', 'female', '2025-11-18 17:20:05', '2025-11-18 17:26:13'),
(9, 'أحمد الخطيب', 'ahmad@gmail.com', '0966521457', 'أمراض الكلى', '', 'approved', 'male', '2025-11-18 17:27:50', '2026-04-15 15:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int NOT NULL,
  `staff_id` int NOT NULL,
  `patient_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'USD',
  `description` text COLLATE utf8mb4_general_ci,
  `status` enum('paid','unpaid','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `staff_id`, `patient_name`, `amount`, `currency`, `description`, `status`, `created_at`) VALUES
(3, 7, 'عادل بيهم', 200.00, 'USD', 'فاتورة قسم الجلدية', 'paid', '2026-04-15 17:59:48'),
(4, 7, 'عامر سالم', 1.15, 'USD', 'استشارة تغذية', 'paid', '2026-04-15 18:29:59');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `record_date` date NOT NULL,
  `diagnosis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `treatment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `doctor_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `record_date`, `diagnosis`, `treatment`, `doctor_notes`, `created_at`) VALUES
(1, 1, '2025-10-15', 'ارتفاع ضغط الدم الأساسي', 'وصف دواء لوسارتان 50mg يومياً، وتعديل نمط الحياة', 'يجب مراجعة الضغط بعد شهر، تجنب الملح', '2026-04-15 19:20:10'),
(2, 1, '2025-12-20', 'التهاب الجيوب الأنفية الحاد', 'مضاد حيوي أموكسيسيلين 500mg 3 مرات يومياً لمدة 7 أيام', 'تحسن ملحوظ بعد 3 أيام، استكمال العلاج', '2026-04-15 19:20:10'),
(3, 2, '2025-09-10', 'نقص الحديد وفقر الدم', 'مكملات حديد 100mg يومياً مع فيتامين C', 'فحص CBC بعد شهرين للتأكد من التحسن', '2026-04-15 19:20:10'),
(4, 2, '2025-11-05', 'التهاب المسالك البولية', 'سيبروفلوكساسين 250mg مرتين يومياً لمدة 5 أيام', 'شرب كميات كبيرة من الماء، مراجعة في حال استمرار الأعراض', '2026-04-15 19:20:10'),
(5, 3, '2025-08-22', 'داء السكري من النوع الثاني', 'ميتفورمين 500mg مرتين يومياً، نظام غذائي صحي', 'مراقبة السكر التراكمي كل 3 أشهر', '2026-04-15 19:20:10'),
(6, 3, '2025-12-01', 'اعتلال الأعصاب المحيطية', 'جابابنتين 300mg ليلاً، وفيتامين ب المركب', 'تحسن الألم بعد أسبوعين، متابعة', '2026-04-15 19:20:10'),
(7, 4, '2025-07-18', 'التهاب المعدة الحاد', 'مضادات حموضة ومثبط مضخة البروتون (أوميبرازول) لمدة 14 يوم', 'تجنب الأطعمة الحارة والمنبهات', '2026-04-15 19:20:10'),
(8, 4, '2025-10-30', 'الصداع النصفي', 'تريبتان عند الحاجة، بروبرانولول 40mg يومياً للوقاية', 'تسجيل نوبات الصداع، مراجعة بعد شهر', '2026-04-15 19:20:10'),
(9, 5, '2025-09-25', 'التهاب المفاصل الروماتويدي المبكر', 'ميثوتريكسيت 7.5mg أسبوعياً، ومضادات الالتهاب', 'فحص وظائف الكبد والكلى كل شهرين', '2026-04-15 19:20:10'),
(10, 5, '2025-12-12', 'نقص فيتامين د', 'فيتامين د 50000 وحدة أسبوعياً لمدة 8 أسابيع', 'توصية بالتعرض للشمس', '2026-04-15 19:20:10'),
(11, 13, '2026-01-05', 'اضطراب القلق العام', 'علاج سلوكي معرفي، وسيرترالين 50mg يومياً', 'جلسات نفسية أسبوعياً لمدة شهرين', '2026-04-15 19:20:10'),
(12, 13, '2026-03-20', 'ألم عضلي مزمن', 'علاج طبيعي ومسكنات عند اللزوم', 'تجنب الإجهاد البدني، ممارسة تمارين التمدد', '2026-04-15 19:20:10'),
(13, 14, '2026-02-14', 'ارتفاع الكوليسترول', 'ستاتين (أتورفاستاتين) 10mg ليلاً، حمية غذائية', 'فحص الدهون بعد 3 أشهر', '2026-04-15 19:20:10'),
(14, 14, '2026-04-01', 'التهاب الشعب الهوائية الحاد', 'موسع قصبي وشراب مقشع، راحة تامة', 'شرب سوائل دافئة، مراجعة في حال الحمى', '2026-04-15 19:20:10');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other','ذكر','أنثى') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `full_name`, `email`, `phone`, `dob`, `gender`, `address`, `created_at`, `password`) VALUES
(1, 'محمد أحمد علي', 'mohammed.patient@example.com', '0501122334', '1985-03-15', 'male', 'دمشق، حي المزة', '2025-11-18 17:56:56', ''),
(2, 'فاطمة سليمان', 'fatima.patient@example.com', '0502233445', '1990-07-22', 'female', 'دمشق العباسيين', '2025-11-18 17:56:56', ''),
(3, 'خالد ناصر', 'khalid.patient@example.com', '0503344556', '1978-11-05', 'male', 'دمشق العمارة', '2025-11-18 17:56:56', ''),
(4, 'نورة فهد', 'nora.patient@example.com', '0504455667', '2000-01-30', 'female', 'دمشق، حي القيمرية، شارع التخصصي', '2025-11-18 17:56:56', ''),
(5, 'عبدالله سعد', 'abdullah.patient@example.com', '0505566778', '1983-09-12', 'male', 'دمشق، البرامكة', '2025-11-18 17:56:56', ''),
(13, 'رؤى عمر كشور', 'roo@gmail.com', '0955987412', '1992-04-12', 'female', 'عباسين خلف الملعب', '2026-04-12 11:13:13', '$2y$10$aXiu86RmcGZdC2RmfpSSme5s0l2WVPyk0otw5kmxu0Uv5tuNcpvIy'),
(14, 'تسنيم القدور', 'tasnim-k@gmail.com', '0966325741', '1980-06-12', 'female', 'مزة الشيخ سعد', '2026-04-12 11:28:50', '$2y$10$rNFd4D9dH2jkx.tVelnUC.wWkS/VgMrSaBeQYXYncrhxF1FcWxuSW');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `entity_id` int NOT NULL,
  `entity_type` enum('doctor','specialist','center') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `patient_id`, `entity_id`, `entity_type`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 6, 'doctor', 5, 'تجربة ممتازة.', '2025-11-29 17:01:12'),
(2, 2, 3, 'specialist', 4, 'أخصائي نفسي جيد.', '2025-11-29 17:01:12'),
(4, 14, 9, 'doctor', 4, 'الطبيب لا يعطي وقت كافي', '2026-04-15 19:28:21'),
(5, 14, 3, 'specialist', 4, 'رائع', '2026-04-15 19:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key_name`, `value`, `description`, `updated_at`) VALUES
('center_name_ar', 'المركز الصحي المتقدم', 'الاسم الرسمي للمركز المستخدم في الرأسية والفواتير', '2025-12-01 10:22:49'),
('default_theme', 'light', 'الوضع الافتراضي للواجهة للمستخدمين الجدد', '2025-12-01 11:02:26'),
('invoice_format', 'A4_Standard', 'تنسيق طباعة الفواتير (A4_Standard, Thermal_Small)', '2025-12-01 10:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `specialists`
--

CREATE TABLE `specialists` (
  `id` int NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `gender` enum('male','female') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `specialists`
--

INSERT INTO `specialists` (`id`, `full_name`, `email`, `phone`, `field`, `license_number`, `status`, `gender`, `created_at`, `updated_at`) VALUES
(1, 'أ. ليلى أحمد', 'layla.specialist@healthcenter.com', '0501122334', 'أخصائية ليزر', 'LIC-S-2025-001', 'pending', 'female', '2025-11-18 17:33:10', '2025-11-18 17:33:10'),
(2, 'د. سارة محمد', 'sara.specialist@healthcenter.com', '0502233445', 'أخصائية بشرة', 'LIC-S-2025-002', 'approved', 'female', '2025-11-18 17:33:10', '2025-11-18 17:33:10'),
(3, 'أ. خالد علي', 'khalid.specialist@healthcenter.com', '0503344556', 'أخصائي نفسي', 'LIC-S-2025-003', 'approved', 'male', '2025-11-18 17:33:10', '2025-11-18 17:33:58'),
(4, 'د. نورة فهد', 'nora.specialist@healthcenter.com', '0504455667', 'أخصائية تغذية', 'LIC-S-2025-004', 'approved', 'female', '2025-11-18 17:33:10', '2025-11-18 17:33:10'),
(5, 'أ. فهد سليمان', 'fahd.specialist@healthcenter.com', '0505566778', 'أخصائي علاج طبيعي', 'LIC-S-2025-005', 'suspended', 'male', '2025-11-18 17:33:10', '2025-11-18 17:33:10');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `full_name`, `email`, `phone`, `position`, `status`, `created_at`, `updated_at`, `password`) VALUES
(1, 'خالد محمد', 'khalid.staff@healthcenter.com', '0501122334', 'موظف أمن', 'active', '2025-11-18 17:48:16', '2026-04-15 15:55:32', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'سارة أحمد', 'sara.staff@healthcenter.com', '0502233445', 'موظفة استقبال', 'active', '2025-11-18 17:48:16', '2026-04-15 15:55:32', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'فهد علي', 'fahd.staff@healthcenter.com', '0503344556', 'موظف IT', 'active', '2025-11-18 17:48:16', '2026-04-15 15:55:32', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(4, 'منى سليمان', 'mona.staff@healthcenter.com', '0504455667', 'مساعد طبي', 'active', '2025-11-18 17:48:16', '2026-04-15 15:55:32', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(5, 'ناصر عبد الله', 'nasser.staff@healthcenter.com', '0505566778', 'عامل نظافة', 'active', '2025-11-18 17:48:16', '2026-04-15 15:55:32', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(6, 'سماهر سعيد', 'smaher@gmail.com', '0966325879', 'موظفة إدخال بيانات', 'suspended', '2025-11-18 17:49:15', '2026-04-15 15:55:32', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(7, 'مريم الذياب', 'mariam@gmail.com', '0933265987', 'موظف سكرتير', 'active', '2026-04-15 16:01:56', '2026-04-16 09:30:33', '$2y$10$CLSldhxAdMeBFWC4ycXUz.zkR298pj2oyVYIa.HZrjzAKHwT.rXte');

-- --------------------------------------------------------

--
-- Table structure for table `surveys`
--

CREATE TABLE `surveys` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','hidden') COLLATE utf8mb4_unicode_ci DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surveys`
--

INSERT INTO `surveys` (`id`, `title`, `description`, `created_by`, `created_at`, `status`) VALUES
(2, 'نظافة المركز', 'استبيان حول رضى المرضى عن النظافة والتعقيم', 7, '2026-04-15 19:05:24', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `survey_answers`
--

CREATE TABLE `survey_answers` (
  `id` int NOT NULL,
  `survey_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `question_id` int NOT NULL,
  `answer` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_answers`
--

INSERT INTO `survey_answers` (`id`, `survey_id`, `patient_id`, `question_id`, `answer`, `created_at`) VALUES
(3, 2, 14, 5, 'لا', '2026-04-15 19:07:49');

-- --------------------------------------------------------

--
-- Table structure for table `survey_questions`
--

CREATE TABLE `survey_questions` (
  `id` int NOT NULL,
  `survey_id` int NOT NULL,
  `question_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `question_type` enum('text','rating','multiple_choice') COLLATE utf8mb4_general_ci DEFAULT 'text',
  `options` text COLLATE utf8mb4_general_ci COMMENT 'خيارات مفصولة بفواصل لنوع multiple_choice',
  `order_index` int DEFAULT '0',
  `order_num` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_questions`
--

INSERT INTO `survey_questions` (`id`, `survey_id`, `question_text`, `question_type`, `options`, `order_index`, `order_num`) VALUES
(5, 2, 'هل تزعجك رائحة المعقم؟', 'multiple_choice', 'لا,نعم,قليلاً', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int NOT NULL,
  `survey_id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `score` int DEFAULT NULL,
  `response_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_responses`
--

INSERT INTO `survey_responses` (`id`, `survey_id`, `patient_id`, `score`, `response_text`, `created_at`) VALUES
(1, 1, 4, 9, 'الخدمة كانت سريعة جداً.', '2025-11-29 17:01:36'),
(2, 1, 5, 8, 'الموظفون ودودون.', '2025-11-29 17:01:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_articles_author` (`author_type`,`author_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key_name`);

--
-- Indexes for table `specialists`
--
ALTER TABLE `specialists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `surveys`
--
ALTER TABLE `surveys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `specialists`
--
ALTER TABLE `specialists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `surveys`
--
ALTER TABLE `surveys`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `survey_answers`
--
ALTER TABLE `survey_answers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `survey_questions`
--
ALTER TABLE `survey_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `surveys`
--
ALTER TABLE `surveys`
  ADD CONSTRAINT `surveys_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD CONSTRAINT `survey_answers_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `survey_answers_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `survey_answers_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `survey_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD CONSTRAINT `survey_questions_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
