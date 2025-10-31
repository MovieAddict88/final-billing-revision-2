-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql100.byetcluster.com
-- Generation Time: Oct 30, 2025 at 09:37 PM
-- Server version: 10.6.22-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40117390_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `billings`
--

CREATE TABLE `billings` (
  `id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `bill_id` varchar(255) NOT NULL,
  `bill_month` varchar(255) NOT NULL,
  `Discount` int(11) NOT NULL DEFAULT 0,
  `bill_amount` int(11) NOT NULL,
  `paid_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_collection`
--

CREATE TABLE `cash_collection` (
  `id` int(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` bigint(20) NOT NULL,
  `payee` varchar(255) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_expanse`
--

CREATE TABLE `cash_expanse` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` bigint(20) NOT NULL,
  `purpose` text NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `nid` varchar(16) NOT NULL,
  `address` text DEFAULT NULL,
  `conn_location` text NOT NULL,
  `email` varchar(30) NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `conn_type` varchar(10) NOT NULL,
  `package_id` int(10) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `login_code` varchar(255) DEFAULT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `dropped` tinyint(1) NOT NULL DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disconnected_billings`
--

CREATE TABLE `disconnected_billings` (
  `id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `bill_id` varchar(255) NOT NULL,
  `bill_month` varchar(255) NOT NULL,
  `Discount` int(11) NOT NULL DEFAULT 0,
  `bill_amount` int(11) NOT NULL,
  `paid_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disconnected_customers`
--

CREATE TABLE `disconnected_customers` (
  `id` int(11) NOT NULL,
  `original_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `nid` varchar(16) NOT NULL,
  `address` text DEFAULT NULL,
  `conn_location` text NOT NULL,
  `email` varchar(30) NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `conn_type` varchar(10) NOT NULL,
  `package_id` int(10) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `login_code` varchar(255) DEFAULT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `disconnected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `disconnected_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disconnected_payments`
--

CREATE TABLE `disconnected_payments` (
  `id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `r_month` varchar(255) NOT NULL,
  `amount` int(10) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `g_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `p_date` timestamp NULL DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `gcash_name` varchar(255) DEFAULT NULL,
  `gcash_number` varchar(255) DEFAULT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `payment_timestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `disconnected_payments`
--

INSERT INTO `disconnected_payments` (`id`, `customer_id`, `employer_id`, `package_id`, `r_month`, `amount`, `balance`, `g_date`, `p_date`, `status`, `payment_method`, `reference_number`, `gcash_name`, `gcash_number`, `screenshot`, `payment_timestamp`) VALUES
(27, 16, 37, NULL, 'October', 800, '300.00', '2025-10-31 01:17:04', '2025-10-31 01:17:51', 'Unpaid', 'Manual', 'Manual', NULL, NULL, NULL, '2025-10-31 01:17:00'),
(28, 17, 37, NULL, 'October', 2000, '1200.00', '2025-10-31 01:29:05', '2025-10-31 01:30:11', 'Unpaid', 'Manual', 'nxnx', NULL, NULL, NULL, '2025-10-30 22:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `disconnected_payment_history`
--

CREATE TABLE `disconnected_payment_history` (
  `id` int(11) NOT NULL,
  `payment_id` int(10) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `r_month` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `disconnected_payment_history`
--

INSERT INTO `disconnected_payment_history` (`id`, `payment_id`, `customer_id`, `employer_id`, `package_id`, `r_month`, `amount`, `paid_amount`, `balance_after`, `payment_method`, `reference_number`, `paid_at`) VALUES
(18, 27, 16, 37, 1, 'October', '800.00', '500.00', '300.00', 'Manual', 'Manual', '2025-10-31 01:17:00'),
(20, 28, 17, 37, 2, 'October', '2000.00', '800.00', '1200.00', 'Manual', 'nxnx', '2025-10-30 22:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `kp_category`
--

CREATE TABLE `kp_category` (
  `cat_id` int(3) NOT NULL,
  `cat_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Category name for products table, parents to products';

--
-- Dumping data for table `kp_category`
--

INSERT INTO `kp_category` (`cat_id`, `cat_name`) VALUES
(2, 'MC'),
(1, 'Onu');

-- --------------------------------------------------------

--
-- Table structure for table `kp_products`
--

CREATE TABLE `kp_products` (
  `pro_id` int(3) NOT NULL COMMENT 'Product identity no',
  `pro_name` varchar(40) NOT NULL COMMENT 'Product name',
  `pro_unit` varchar(10) NOT NULL COMMENT 'Product unit',
  `pro_category` varchar(20) CHARACTER SET utf16 COLLATE utf16_unicode_ci NOT NULL COMMENT 'Product category: Child of kp_category table',
  `pro_details` text NOT NULL COMMENT 'Product details',
  `pro_dropped` int(1) NOT NULL DEFAULT 0 COMMENT 'If a product is dropped or not'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Table for individuals products';

-- --------------------------------------------------------

--
-- Table structure for table `kp_user`
--

CREATE TABLE `kp_user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `user_pwd` varchar(255) NOT NULL,
  `full_name` text NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `contact` varchar(15) NOT NULL,
  `address` text DEFAULT NULL,
  `c_date` datetime NOT NULL DEFAULT current_timestamp(),
  `authentication` int(1) NOT NULL DEFAULT 0,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `location` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `kp_user`
--

INSERT INTO `kp_user` (`user_id`, `user_name`, `user_pwd`, `full_name`, `email`, `contact`, `address`, `c_date`, `authentication`, `role`, `location`, `profile_pic`) VALUES
(32, 'moshiur', '$2y$10$Xk8TYun1Crr0gIS960sSCeh7rfXfQfK2VJ3rJ7SThN/6cehlop35O', 'Moshiur Rahman', 'unimrgm@gmail.com', '01719454658', 'Bogra', '2017-10-21 20:39:51', 0, 'admin', NULL, NULL),
(33, 'misu', '$2y$10$xuIQ8LSURAJbGj8dD8Wr9.0PzbPq2qUvaLjmSwMh/5xEY.SHeKYnS', 'Mushfiqur Rahman', 'misu@gmail.com', '01719454658', 'Bogra', '2017-10-22 07:29:55', 0, 'admin', NULL, NULL),
(34, 'admin', '$2y$10$51/xhfZqmMt6pr9HhXfZB.Punql5srC5vXtOEradf0Cs5Dg/FzHYy', 'Mr Admin', 'admin@netwaybd.com', '051-56565', 'Netway', '2017-10-23 15:28:31', 0, 'admin', NULL, NULL),
(37, 'Ronald', '$2y$10$YOjitQc0vM39hWGuGbYyceqDOCzzDMnJUL9kX/wUuAu.6fd1w.tty', 'Fagmmmu', 'ronatorrejos@gmail.com', '09663016917', 'Zone 9, Bonbon', '2025-10-30 22:15:11', 0, 'employer', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fee` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `fee`, `created_at`) VALUES
(1, '2 Mbps', 800, '2017-10-30 19:22:42'),
(2, '5 Mbps', 2000, '2017-11-01 07:21:18'),
(3, '3 Mbps ', 1200, '2017-11-01 07:21:53');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `r_month` varchar(255) NOT NULL,
  `amount` int(10) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `g_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `p_date` timestamp NULL DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `gcash_name` varchar(255) DEFAULT NULL,
  `gcash_number` varchar(255) DEFAULT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `payment_timestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` int(11) NOT NULL,
  `payment_id` int(10) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `r_month` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cdate` datetime DEFAULT current_timestamp(),
  `provider` varchar(50) NOT NULL,
  `remarks` text NOT NULL,
  `recipient` varchar(50) NOT NULL,
  `type` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reconnection_requests`
--

CREATE TABLE `reconnection_requests` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billings`
--
ALTER TABLE `billings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cash_collection`
--
ALTER TABLE `cash_collection`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cash_expanse`
--
ALTER TABLE `cash_expanse`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nid` (`nid`);

--
-- Indexes for table `disconnected_customers`
--
ALTER TABLE `disconnected_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `original_id` (`original_id`),
  ADD KEY `disconnected_at` (`disconnected_at`);

--
-- Indexes for table `kp_category`
--
ALTER TABLE `kp_category`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `cat_name` (`cat_name`);

--
-- Indexes for table `kp_products`
--
ALTER TABLE `kp_products`
  ADD PRIMARY KEY (`pro_id`),
  ADD UNIQUE KEY `pro_name` (`pro_name`),
  ADD KEY `pro_category` (`pro_category`);

--
-- Indexes for table `kp_user`
--
ALTER TABLE `kp_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_id` (`payment_id`),
  ADD KEY `idx_customer_id_paidat` (`customer_id`,`paid_at`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reconnection_requests`
--
ALTER TABLE `reconnection_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billings`
--
ALTER TABLE `billings`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_collection`
--
ALTER TABLE `cash_collection`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_expanse`
--
ALTER TABLE `cash_expanse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `disconnected_customers`
--
ALTER TABLE `disconnected_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `kp_category`
--
ALTER TABLE `kp_category`
  MODIFY `cat_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kp_products`
--
ALTER TABLE `kp_products`
  MODIFY `pro_id` int(3) NOT NULL AUTO_INCREMENT COMMENT 'Product identity no';

--
-- AUTO_INCREMENT for table `kp_user`
--
ALTER TABLE `kp_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reconnection_requests`
--
ALTER TABLE `reconnection_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
