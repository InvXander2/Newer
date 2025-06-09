-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 09, 2025 at 06:48 PM
-- Server version: 10.6.21-MariaDB-cll-lve-log
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nexuvmvy_nexusinsights`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `act_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `category` text NOT NULL,
  `date_sent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`act_id`, `user_id`, `message`, `category`, `date_sent`) VALUES
(1, 4, 'You updated your profile', 'Info', '2025-06-04 11:49 AM'),
(2, 4, 'You updated your profile', 'Info', '2025-06-04 11:50 AM'),
(3, 2, 'You updated your profile', 'Info', '2025-06-04 12:14 PM'),
(4, 2, 'You made a deposit request of $50000', 'Deposit Request', '2025-06-04 12:33 PM'),
(5, 2, 'Amount of $50000 was deposited successfully', 'Deposit', '2025-06-04 12:34 PM'),
(6, 2, 'Current Ongoing investment', 'Advanced Plan', '2025-06-04 12:35 PM'),
(7, 2, 'Current Ongoing investment', 'Starter Plan', '2025-06-04 12:39 PM'),
(8, 2, 'Investment Completed', 'Starter Plan', '2025-06-04 12:40 PM'),
(9, 2, 'Current Ongoing investment', 'Standard Plan', '2025-06-04 12:40 PM'),
(10, 2, 'Investment Completed', 'Standard Plan', '2025-06-04 01:01 PM'),
(11, 2, 'Current Ongoing investment', 'Starter Plan', '2025-06-04 01:17 PM'),
(12, 2, 'You updated your profile', 'Info', '2025-06-04 03:10 PM'),
(13, 2, 'You made a withdrawal request of $10000', 'Withdrawal Request', '2025-06-04 03:11 PM'),
(14, 2, 'You made a deposit request of $60000', 'Deposit Request', '2025-06-04 03:18 PM'),
(15, 2, 'Amount of $10000 was withdrawn successfully', 'Withdrawal', '2025-06-04 03:31 PM'),
(16, 2, 'Current Ongoing investment', 'Standard Plan', '2025-06-04 06:34 PM'),
(17, 2, 'You updated your profile', 'Info', '2025-06-04 08:35 PM'),
(18, 2, 'You updated your profile', 'Info', '2025-06-04 08:39 PM'),
(19, 2, 'Amount of $60000 was deposited successfully', 'Deposit', '2025-06-04 10:42 PM'),
(20, 2, 'Investment Cancelled', 'Advanced Plan', '2025-06-05 12:17 PM'),
(21, 2, 'You updated your profile', 'Info', '2025-06-05 12:19 PM'),
(22, 2, 'You made a deposit request of $5000', 'Deposit Request', '2025-06-05 12:20 PM'),
(23, 2, 'You updated your profile', 'Info', '2025-06-05 05:13 PM'),
(24, 2, 'You updated your profile', 'Info', '2025-06-05 05:13 PM'),
(25, 2, 'Investment Completed', 'Starter Plan', '2025-06-05 05:13 PM'),
(26, 2, 'You made a withdrawal request of $17000', 'Withdrawal Request', '2025-06-05 05:15 PM'),
(27, 2, 'You made a withdrawal request of $5000', 'Withdrawal Request', '2025-06-05 05:24 PM'),
(28, 2, 'You made a withdrawal request of $1318', 'Withdrawal Request', '2025-06-05 05:25 PM'),
(29, 2, 'You made a withdrawal request of $30000', 'Withdrawal Request', '2025-06-05 05:35 PM'),
(30, 2, 'Amount of $30000 was withdrawn successfully', 'Withdrawal', '2025-06-05 05:41 PM'),
(31, 2, 'Amount of $1318 was withdrawn successfully', 'Withdrawal', '2025-06-05 05:41 PM'),
(32, 2, 'Amount of $5000 was withdrawn successfully', 'Withdrawal', '2025-06-05 05:41 PM'),
(33, 2, 'Amount of $17000 was withdrawn successfully', 'Withdrawal', '2025-06-05 05:42 PM'),
(34, 3, 'You updated your profile', 'Info', '2025-06-06 11:49 AM'),
(35, 2, 'Amount of $5000 was deposited successfully', 'Deposit', '2025-06-06 11:50 AM'),
(36, 3, 'You made a deposit request of $5000', 'Deposit Request', '2025-06-06 12:00 PM'),
(37, 3, 'Amount of $5000 was deposited successfully', 'Deposit', '2025-06-06 12:01 PM'),
(38, 8, 'You updated your profile', 'Info', '2025-06-06 12:40 PM'),
(39, 8, 'You updated your profile', 'Info', '2025-06-06 12:42 PM'),
(40, 8, 'You have 1 new message', 'Inbox', '2025-06-06 12:44 PM'),
(41, 8, 'You made a deposit request of $10000', 'Deposit Request', '2025-06-06 12:49 PM'),
(42, 8, 'Amount of $10000 was deposited successfully', 'Deposit', '2025-06-06 12:50 PM'),
(43, 8, 'Current Ongoing investment', 'Standard Plan', '2025-06-06 12:50 PM'),
(44, 8, 'You changed your password', 'Security', '2025-06-06 01:02 PM'),
(45, 8, 'Current Ongoing investment', 'Starter Plan', '2025-06-06 01:07 PM'),
(46, 6, 'You made a deposit request of $50000', 'Deposit Request', '2025-06-06 01:47 PM'),
(47, 6, 'Amount of $50000 was deposited successfully', 'Deposit', '2025-06-06 01:48 PM'),
(48, 6, 'You made a withdrawal request of $3000', 'Withdrawal Request', '2025-06-06 01:55 PM'),
(49, 6, 'You made a deposit request of $60000', 'Deposit Request', '2025-06-06 02:01 PM'),
(50, 6, 'Amount of $3000 was withdrawn successfully', 'Withdrawal', '2025-06-06 02:06 PM'),
(51, 6, 'Amount of $60000 was deposited successfully', 'Deposit', '2025-06-06 06:51 PM'),
(52, 2, 'Amount of $30000 was withdrawn successfully', 'Withdrawal', '2025-06-06 06:52 PM'),
(53, 8, 'You updated your profile', 'Info', '2025-06-06 07:03 PM'),
(54, 8, 'Current Ongoing investment', 'Advanced Plan', '2025-06-06 07:06 PM'),
(55, 2, 'Current Ongoing investment', 'Starter Plan', '2025-06-07 05:37 AM'),
(56, 2, 'Current Ongoing investment', 'Starter Plan', '2025-06-07 05:41 AM'),
(57, 2, 'Current Ongoing investment', 'Starter Plan', '2025-06-07 01:03 PM'),
(58, 8, 'Investment Completed', 'Starter Plan', '2025-06-07 01:09 PM'),
(59, 6, 'Current Ongoing investment', 'Premium Plan', '2025-06-07 02:33 PM'),
(60, 6, 'You made a deposit request of $5000', 'Deposit Request', '2025-06-07 03:31 PM'),
(61, 9, 'You made a deposit request of $50000', 'Deposit Request', '2025-06-07 05:12 PM'),
(62, 9, 'Amount of $50000 was deposited successfully', 'Deposit', '2025-06-07 05:14 PM'),
(63, 9, 'You made a withdrawal request of $5000', 'Withdrawal Request', '2025-06-07 05:19 PM'),
(64, 9, 'Amount of $5000 was withdrawn successfully', 'Withdrawal', '2025-06-07 05:27 PM'),
(65, 9, 'Current Ongoing investment', 'Starter Plan', '2025-06-07 05:33 PM'),
(66, 9, 'Investment Completed', 'Starter Plan', '2025-06-07 05:35 PM'),
(67, 9, 'Current Ongoing investment', 'Standard Plan', '2025-06-07 05:37 PM'),
(68, 9, 'Completed investment of $3500 for Standard Plan', 'Investment Completion', '2025-06-07 05:41 PM'),
(69, 10, 'You made a deposit request of $5000', 'Deposit Request', '2025-06-07 06:18 PM'),
(70, 10, 'Amount of $5000 was deposited successfully', 'Deposit', '2025-06-07 06:19 PM'),
(71, 10, 'Current Ongoing investment', 'Advanced Plan', '2025-06-07 06:27 PM'),
(72, 10, 'Completed investment of $5000 for Advanced Plan', 'Investment Completion', '2025-06-07 06:31 PM'),
(73, 10, 'You made a withdrawal request of $5000', 'Withdrawal Request', '2025-06-07 06:35 PM'),
(74, 10, 'Amount of $5000 was withdrawn successfully', 'Withdrawal', '2025-06-07 06:38 PM'),
(75, 11, 'You made a deposit request of $500', 'Deposit Request', '2025-06-07 10:25 PM'),
(76, 11, 'Amount of $500 was deposited successfully', 'Deposit', '2025-06-07 10:32 PM'),
(77, 11, 'Current Ongoing investment', 'Starter Plan', '2025-06-07 10:34 PM'),
(78, 11, 'Completed investment of $550 for Starter Plan', 'Investment Completion', '2025-06-07 10:41 PM'),
(79, 11, 'You made a withdrawal request of $660', 'Withdrawal Request', '2025-06-07 11:15 PM'),
(80, 11, 'Amount of $660 was withdrawn successfully', 'Withdrawal', '2025-06-07 11:18 PM'),
(81, 11, 'Amount of $660 was withdrawn successfully', 'Withdrawal', '2025-06-07 11:28 PM'),
(82, 11, 'Amount of 660 was deposited successfully', 'Deposit', '2025-06-07 11:29 PM'),
(83, 2, 'Completed investment of $2500 for Standard Plan', 'Investment Completion', '2025-06-08 02:54 AM'),
(84, 2, 'Current Ongoing investment', 'Starter Plan', '2025-06-08 02:55 AM'),
(85, 8, 'Current Ongoing investment', 'Starter Plan', '2025-06-08 01:22 PM'),
(86, 2, 'Current Ongoing investment', 'Starter Plan', '2025-06-09 10:24 AM'),
(87, 2, 'You made a deposit request of $40000', 'Deposit Request', '2025-06-09 10:58 AM'),
(88, 6, 'Amount of $5000 was deposited successfully', 'Deposit', '2025-06-09 10:59 AM'),
(89, 2, 'Amount of $40000 was deposited successfully', 'Deposit', '2025-06-09 10:59 AM'),
(90, 2, 'You made a withdrawal request of $9000', 'Withdrawal Request', '2025-06-09 12:20 PM'),
(91, 2, 'You made a withdrawal request of $10000', 'Withdrawal Request', '2025-06-09 12:23 PM'),
(92, 2, 'Amount of $10000 was withdrawn successfully', 'Withdrawal', '2025-06-09 12:26 PM'),
(93, 2, 'You made a withdrawal request of $680', 'Withdrawal Request', '2025-06-09 12:38 PM'),
(94, 8, 'Completed investment of $3000 for Standard Plan', 'Investment Completion', '2025-06-09 09:43 PM'),
(95, 8, 'Completed investment of $500 for Starter Plan', 'Investment Completion', '2025-06-09 09:43 PM');

-- --------------------------------------------------------

--
-- Table structure for table `blockchaintx`
--

CREATE TABLE `blockchaintx` (
  `btx_id` int(11) NOT NULL,
  `btx_address` text NOT NULL,
  `btx_txid` text NOT NULL,
  `btx_amount` decimal(65,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `direct_message`
--

CREATE TABLE `direct_message` (
  `msg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `date_sent` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `direct_message`
--

INSERT INTO `direct_message` (`msg_id`, `user_id`, `subject`, `message`, `date_sent`, `status`) VALUES
(1, 8, 'Alert', '<p>You be thief</p>\r\n', '2025-06-06 12:44 PM', 1);

-- --------------------------------------------------------

--
-- Table structure for table `error_log`
--

CREATE TABLE `error_log` (
  `id` int(11) NOT NULL,
  `error_message` text DEFAULT NULL,
  `error_time` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hp_deposits`
--

CREATE TABLE `hp_deposits` (
  `id` int(11) NOT NULL,
  `payment_type` text NOT NULL,
  `username` text NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hp_transactions`
--

CREATE TABLE `hp_transactions` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `type` int(1) NOT NULL,
  `amount` int(11) NOT NULL,
  `trans_date` date NOT NULL,
  `payment_mode` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hp_transactions`
--

INSERT INTO `hp_transactions` (`id`, `username`, `type`, `amount`, `trans_date`, `payment_mode`) VALUES
(1, 'Antonio588', 1, 5000, '2025-06-04', 'bitcoin'),
(2, 'Mr.Marco', 2, 9620, '2025-06-04', 'etherum'),
(3, 'Alessandro', 1, 4811, '2025-06-06', 'bitcoin');

-- --------------------------------------------------------

--
-- Table structure for table `hp_withdrawals`
--

CREATE TABLE `hp_withdrawals` (
  `id` int(11) NOT NULL,
  `payment_type` text NOT NULL,
  `username` text NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investment`
--

CREATE TABLE `investment` (
  `invest_id` int(11) NOT NULL,
  `invest_plan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `capital` int(11) NOT NULL,
  `returns` int(11) NOT NULL,
  `current` int(11) NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investment`
--

INSERT INTO `investment` (`invest_id`, `invest_plan_id`, `user_id`, `capital`, `returns`, `current`, `start_date`, `end_date`, `status`) VALUES
(1, 3, 2, 5000, 6250, 5719, '2025-06-04 12:35:05', '2025-06-11 12:35:05', 'in progress'),
(2, 1, 2, 300, 360, 300, '2025-06-04 12:39:30', '2025-06-05 12:39:30', 'completed'),
(3, 2, 2, 2000, 2440, 2001, '2025-06-04 12:40:48', '2025-06-04 00:00:00', 'completed'),
(4, 1, 2, 200, 240, 238, '2025-06-04 13:17:16', '2025-06-05 00:00:00', 'completed'),
(5, 2, 2, 2500, 3050, 3043, '2025-06-04 18:34:13', '2025-06-08 00:00:00', 'completed'),
(6, 2, 8, 3000, 3660, 3444, '2025-06-06 12:50:51', '2025-06-09 00:00:00', 'completed'),
(7, 1, 8, 500, 600, 501, '2025-06-06 13:07:54', '2025-06-07 00:00:00', 'completed'),
(8, 3, 8, 5000, 6250, 5313, '2025-06-06 19:06:51', '2025-06-13 19:06:51', 'in progress'),
(9, 1, 2, 500, 600, 501, '2025-06-07 05:37:07', '2025-06-07 05:40:07', 'completed'),
(10, 1, 2, 500, 600, 501, '2025-06-07 05:41:12', '2025-06-07 05:42:12', 'completed'),
(11, 1, 2, 300, 360, 301, '2025-06-07 13:03:40', '2025-06-07 13:08:40', 'completed'),
(12, 5, 6, 70000, 95200, 70794, '2025-06-07 14:33:17', '2025-07-07 14:33:17', 'in progress'),
(13, 1, 9, 600, 720, 600, '2025-06-07 17:33:57', '2025-06-08 17:33:57', 'completed'),
(14, 2, 9, 3500, 4270, 3501, '2025-06-07 17:37:14', '2025-06-07 00:00:00', 'completed'),
(15, 3, 10, 5000, 6250, 5001, '2025-06-07 18:27:42', '2025-06-07 00:00:00', 'completed'),
(16, 1, 11, 550, 660, 551, '2025-06-07 22:34:49', '2025-06-07 22:40:00', 'completed'),
(17, 1, 2, 999, 1199, 1085, '2025-06-08 02:55:12', '2025-06-09 02:55:12', 'completed'),
(18, 1, 8, 500, 600, 501, '2025-06-08 13:22:16', '2025-06-09 00:00:00', 'completed'),
(19, 1, 2, 800, 960, 801, '2025-06-09 10:24:12', '2025-06-10 10:24:12', 'in progress');

-- --------------------------------------------------------

--
-- Table structure for table `investment_plans`
--

CREATE TABLE `investment_plans` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `duration` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `min_invest` int(11) NOT NULL,
  `max_invest` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investment_plans`
--

INSERT INTO `investment_plans` (`id`, `name`, `duration`, `rate`, `min_invest`, `max_invest`) VALUES
(1, 'Starter Plan', 1, 20, 200, 999),
(2, 'Standard Plan', 3, 22, 1000, 4999),
(3, 'Advanced Plan', 7, 25, 5000, 9999),
(4, 'Elite Plan', 14, 30, 10000, 49999),
(5, 'Premium Plan', 30, 36, 50000, 100000);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` text DEFAULT NULL,
  `posted` varchar(20) NOT NULL,
  `details` text NOT NULL,
  `short_title` text NOT NULL,
  `short_details` text NOT NULL,
  `photo` text NOT NULL,
  `slug` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `posted`, `details`, `short_title`, `short_details`, `photo`, `slug`) VALUES
(2, 'STATEMENT', '4 June, 2025', '<p>&quot;I love President Xi of China, I always have and I always will, but he is very tough and it is extremely difficult to negotiate a deal with him!&quot;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>- said Donald Trump.</p>\r\n', 'STATEMENT', '<p>&quot;I love President Xi of China, I always have and I always will, but he is very tough and it is extremely difficult to negotiate a deal with him!&quot;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>- said Donald Trump.</p>\r\n', 'statement_1749227265.jpg', 'statement'),
(3, 'Breaking News', '6 June, 2025', '<p>President Trump calls the Federal Reserve a &ldquo;disaster,&rdquo; calls for a 100 basis point interest rate cut.</p>\r\n', 'Breaking News', '<p>President Trump calls the Federal Reserve a &ldquo;disaster,&rdquo; calls for a 100 basis point interest rate cut.</p>\r\n', 'breaking-news.jpg', 'breaking-news'),
(4, 'News', '8 June, 2025', '<p>Michael Saylor hints he&#39;s considering buying more Bitcoin.</p>\r\n\r\n<p>&quot;Send more orange.&quot;</p>\r\n', 'News', '<p>Michael Saylor hints he&#39;s considering buying more Bitcoin.</p>\r\n\r\n<p>&quot;Send more orange.&quot;</p>\r\n', 'news.jpg', 'news');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `wallet_address` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `photo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `wallet_address`, `details`, `photo`) VALUES
(12, 'Bitcoin(BTC)', 'bc1q4exyqqy6e2tprsef994c8qyw9cc2xa9uz5npqc', 'btc', 'images/6842bb32d06ca.jpg'),
(13, 'Ethereum(ETH)', '0x83e4922408C3ebD163F75158b23b2869AcBB5B8c', 'eth', 'images/6842bb7292145.jpg'),
(15, 'USDT(BEP20)', '0x83e4922408C3ebD163F75158b23b2869AcBB5B8c', 'bep20', 'images/6842bbfa8ad62.jpg'),
(16, 'Tron(TRX)', 'TN2eh8odb4gHzoQfpP8akktFYEu7gz7y3s', 'trx', 'images/6842bc1d37795.jpg'),
(17, 'Solana(SOL)', 'GeGwdXV5DZrWsT79ww3x13zxz9Yb8cf3yq6UsGLcBPNH', 'sol', 'images/6842bc407f210.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trans_date` text NOT NULL,
  `type` text NOT NULL,
  `amount` int(11) NOT NULL,
  `status` text NOT NULL,
  `payment_mode` varchar(100) DEFAULT NULL,
  `payment_info` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`request_id`, `user_id`, `trans_date`, `type`, `amount`, `status`, `payment_mode`, `payment_info`) VALUES
(1, 2, '2025-06-04', '1', 50000, 'approved', 'Bitcoin(BTC)', NULL),
(2, 2, '2025-06-04', '2', 10000, 'approved', 'Tron(TRX)', 'Please please'),
(3, 2, '2025-06-04', '1', 60000, 'approved', 'USDT(BEP20)', NULL),
(4, 2, '2025-06-05', '1', 5000, 'approved', 'Solana(SOL)', NULL),
(5, 2, '2025-06-05', '2', 17000, 'approved', 'USDT(BEP20)', '0x2diuKd34CbxcjdjfkcJxiddUjxZkzjpphhphff'),
(6, 2, '2025-06-05', '2', 5000, 'approved', 'Solana(SOL)', 'Yeye'),
(7, 2, '2025-06-05', '2', 1318, 'approved', 'Tron(TRX)', 'Vshh'),
(8, 2, '2025-06-05 05:35 PM', '2', 30000, 'approved', 'Ethereum(ETH)', 'Yestt'),
(9, 3, '2025-06-06', '1', 5000, 'approved', 'USDT(BEP20)', NULL),
(10, 8, '2025-06-06', '1', 10000, 'approved', 'USDT(BEP20)', NULL),
(11, 6, '2025-06-06', '1', 50000, 'approved', 'Bitcoin(BTC)', NULL),
(12, 6, '2025-06-06 01:55 PM', '2', 3000, 'approved', 'Ethereum(ETH)', 'Csgwhwubsvdhdjd'),
(13, 6, '2025-06-06', '1', 60000, 'approved', 'Tron(TRX)', NULL),
(14, 6, '2025-06-07', '1', 5000, 'approved', 'Tron(TRX)', NULL),
(15, 9, '2025-06-07 05:12 PM', '1', 50000, 'approved', 'USDT(BEP20)', NULL),
(16, 9, '2025-06-07 05:19 PM', '2', 5000, 'approved', 'Tron(TRX)', 'Yesss'),
(17, 10, '2025-06-07 06:18 PM', '1', 5000, 'approved', 'Solana(SOL)', NULL),
(18, 10, '2025-06-07 06:35 PM', '2', 5000, 'approved', 'Ethereum(ETH)', 'Yuhhh'),
(19, 11, '2025-06-07 10:25 PM', '1', 500, 'approved', 'Ethereum(ETH)', NULL),
(20, 11, '2025-06-07 11:15 PM', '2', 660, 'approved', 'Tron(TRX)', 'Yyyyw'),
(21, 2, '2025-06-09 10:58 AM', '1', 40000, 'approved', 'USDT(BEP20)', NULL),
(22, 2, '2025-06-09 12:20 PM', '2', 9000, 'cancelled', 'Ethereum(ETH)', 'Rrrr'),
(23, 2, '2025-06-09 12:23 PM', '2', 10000, 'approved', 'Bitcoin(BTC)', 'Jhhh'),
(24, 2, '2025-06-09 12:38 PM', '2', 680, 'pending', 'Tron(TRX)', 'Ggg');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `trans_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trans_date` text NOT NULL,
  `type` text NOT NULL,
  `amount` int(11) NOT NULL,
  `remark` text NOT NULL,
  `balance` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`trans_id`, `user_id`, `trans_date`, `type`, `amount`, `remark`, `balance`) VALUES
(1, 2, '2025-06-04 11:57 AM', '1', 10, 'Registration Bonus', 10),
(2, 2, '2025-06-04 12:34 PM', '1', 50000, 'Amount of $50000 was deposited successfully', 50010),
(3, 2, '2025-06-04 12:35:05', '2', 5000, 'Invested in Advanced Plan', 45010),
(4, 2, '2025-06-04 12:39:30', '2', 300, 'Invested in Starter Plan', 44710),
(5, 2, '2025-06-04 06:40:16', '1', 360, 'Starter Plan Investment Completed', 45070),
(6, 2, '2025-06-04 12:40:48', '2', 2000, 'Invested in Standard Plan', 43070),
(7, 2, '2025-06-04 07:01:03', '1', 2440, 'Received return for Standard Plan', 45510),
(8, 2, '2025-06-04 13:17:16', '2', 200, 'Invested in Starter Plan', 45310),
(9, 2, '2025-06-04 3:31 PM', '2', 10000, 'Amount of $10000 was withdrawn successfully', 35310),
(10, 5, '2025-06-04 6:07 PM', '1', 10, 'Registration Bonus', 10),
(11, 2, '2025-06-04 18:34:13', '2', 2500, 'Invested in Standard Plan', 32810),
(12, 2, '2025-06-04 10:42 PM', '1', 60000, 'Amount of $60000 was deposited successfully', 92810),
(13, 3, '2025-06-05 12:39 AM', '1', 10, 'Registration Bonus', 10),
(14, 6, '2025-06-05 2:43 AM', '1', 10, 'Registration Bonus', 10),
(17, 7, '2025-06-05 7:55 AM', '1', 50, 'Registration Bonus', 50),
(18, 2, '2025-06-05 06:17:22', '1', 5000, 'Advanced Plan Investment Cancelled', 97810),
(19, 2, '2025-06-05 11:13:49', '1', 240, 'Received return for Starter Plan', 98050),
(20, 2, '2025-06-05 5:41 PM', '2', 30000, 'Amount of $30000 was withdrawn successfully', 68050),
(21, 2, '2025-06-05 5:41 PM', '2', 1318, 'Amount of $1318 was withdrawn successfully', 66732),
(22, 2, '2025-06-05 5:41 PM', '2', 5000, 'Amount of $5000 was withdrawn successfully', 61732),
(23, 2, '2025-06-05 5:42 PM', '2', 17000, 'Amount of $17000 was withdrawn successfully', 44732),
(24, 2, '2025-06-06 11:50 AM', '1', 5000, 'Amount of $5000 was deposited successfully', 49732),
(25, 3, '2025-06-06 12:01 PM', '1', 5000, 'Amount of $5000 was deposited successfully', 5010),
(26, 8, '2025-06-06 12:44 PM', '1', 50, 'Registration Bonus', 50),
(27, 8, '2025-06-06 12:50 PM', '1', 10000, 'Amount of $10000 was deposited successfully', 10050),
(28, 8, '2025-06-06 12:50:51', '2', 3000, 'Invested in Standard Plan', 7050),
(29, 8, '2025-06-06 13:07:54', '2', 500, 'Invested in Starter Plan', 6550),
(30, 6, '2025-06-06 1:48 PM', '1', 50000, 'Amount of $50000 was deposited successfully', 50010),
(31, 6, '2025-06-06 2:06 PM', '2', 3000, 'Amount of $3000 was withdrawn successfully', 47010),
(32, 6, '2025-06-06 6:51 PM', '1', 60000, 'Amount of $60000 was deposited successfully', 107010),
(33, 2, '2025-06-06 6:52 PM', '2', 30000, 'Amount of $30000 was withdrawn successfully', 19732),
(34, 8, '2025-06-06 19:06:51', '2', 5000, 'Invested in Advanced Plan', 1550),
(35, 2, '2025-06-07 05:37:07', '2', 500, 'Invested in Starter Plan', 19232),
(36, 2, '2025-06-07 05:41:12', '2', 500, 'Invested in Starter Plan', 18732),
(37, 2, '2025-06-07 13:03:40', '2', 300, 'Invested in Starter Plan', 18432),
(38, 8, '2025-06-07 07:09:21', '1', 600, 'Received return for Starter Plan', 2150),
(39, 6, '2025-06-07 14:33:17', '2', 70000, 'Invested in Premium Plan', 37010),
(40, 9, '2025-06-07 5:09 PM', '1', 5, 'Welcome Bonus', 5),
(41, 9, '2025-06-07 5:14 PM', '1', 50000, 'Amount of $50000 was deposited successfully', 50005),
(42, 9, '2025-06-07 5:27 PM', '2', 5000, 'Amount of $5000 was withdrawn successfully', 45005),
(43, 9, '2025-06-07 17:33:57', '2', 600, 'Invested in Starter Plan', 44405),
(44, 9, '2025-06-07 11:35:37', '1', 720, 'Starter Plan Investment Completed', 45125),
(45, 9, '2025-06-07 17:37:14', '2', 3500, 'Invested in Standard Plan', 41625),
(46, 9, '2025-06-07 05:41 PM', '1', 4270, 'Received return of $4270 for Standard Plan', 45895),
(47, 10, '2025-06-07 6:17 PM', '1', 50, 'Welcome Bonus', 50),
(48, 10, '2025-06-07 6:19 PM', '1', 5000, 'Amount of $5000 was deposited successfully', 5050),
(49, 10, '2025-06-07 18:27:42', '2', 5000, 'Invested in Advanced Plan', 50),
(50, 10, '2025-06-07 06:31 PM', '1', 6250, 'Received return of $6250 for Advanced Plan', 6300),
(51, 10, '2025-06-07 6:38 PM', '2', 5000, 'Amount of $5000 was withdrawn successfully', 1300),
(52, 11, '2025-06-07 10:22 PM', '1', 50, 'Welcome Bonus', 50),
(53, 11, '2025-06-07 10:32 PM', '1', 500, 'Amount of $500 was deposited successfully', 550),
(54, 11, '2025-06-07 22:34:49', '2', 550, 'Invested in Starter Plan', 0),
(55, 11, '2025-06-07 10:41 PM', '1', 660, 'Received return of $660 for Starter Plan', 660),
(56, 11, '2025-06-07 11:18 PM', '2', 660, 'Amount of $660 was withdrawn successfully', 0),
(57, 11, '2025-06-07 11:28 PM', '2', 660, 'Amount of $660 was withdrawn successfully', -660),
(58, 11, '2025-06-07 11:29 PM', '1', 660, 'Amount of 660 was deposited successfully', 0),
(59, 2, '2025-06-08 02:54 AM', '1', 3050, 'Received return of $3050 for Standard Plan', 21482),
(60, 2, '2025-06-08 02:55:12', '2', 999, 'Invested in Starter Plan', 20483),
(61, 8, '2025-06-08 13:22:16', '2', 500, 'Invested in Starter Plan', 1650),
(62, 2, '2025-06-09 10:24:12', '2', 800, 'Invested in Starter Plan', 19683),
(63, 6, '2025-06-09 10:59 AM', '1', 5000, 'Amount of $5000 was deposited successfully', 42010),
(64, 2, '2025-06-09 10:59 AM', '1', 40000, 'Amount of $40000 was deposited successfully', 59683),
(65, 2, '2025-06-09 12:26 PM', '2', 10000, 'Amount of $10000 was withdrawn successfully', 49683),
(66, 8, '2025-06-09 09:43 PM', '1', 3660, 'Received return of $3660 for Standard Plan', 5310),
(67, 8, '2025-06-09 09:43 PM', '1', 600, 'Received return of $600 for Starter Plan', 5910);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` text DEFAULT NULL,
  `type` int(1) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `status` int(1) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `referral_code` varchar(30) DEFAULT NULL,
  `activate_code` text NOT NULL,
  `reset_code` text DEFAULT NULL,
  `created_on` date NOT NULL,
  `uname` varchar(30) DEFAULT NULL,
  `password` varchar(60) NOT NULL,
  `photo` text DEFAULT NULL,
  `date_view` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `type`, `gender`, `dob`, `status`, `email`, `phone_no`, `nationality`, `address`, `referral_code`, `activate_code`, `reset_code`, `created_on`, `uname`, `password`, `photo`, `date_view`) VALUES
(1, 'Agente Gabriella ', 1, NULL, NULL, 1, 'support@nexusinsights.it.com', NULL, NULL, NULL, 'nexusinsights', 'wUQ5iFace34I', NULL, '2025-06-03', 'Admin', '$2y$10$9dwepHfaUUCwCbU3QrqMXOc4caHwd4VfoLZX.nnfx4HoakBELOlCW', 'IMG_20250527_003710_912.jpg', '2025-06-03 15:53:59.066258'),
(2, 'Francesca Novelli', 0, 'Female', '1988-09-14', 1, 'georgerichie22222@gmail.com', '+393505285611', 'Italy', 'Paris, France', 'nexusinsights', 'cWYzPe4BRD7A', NULL, '2025-06-04', 'Francesca', '$2y$10$ZKdmjgAvGw4DSnxk05wvXOMOURijcns8Ybloitz77Ji3GF1iolUOW', '1749136369894.jpg', '2025-06-08 11:56:22.000000'),
(3, 'Bertocchi Marco', 0, 'Male', '1980-02-11', 1, 'azeez.azeez0852@gmail.com', '+395369263482', 'Italy', 'Venice', 'Francesca', 'YLqRdp9bCHoO', 't87lsZWfSKdPD9L', '2025-06-04', 'Marc11', '$2y$10$Gk00rNsrvlCMDbd9590fjuJlMdr5TFsnkw.ItAeGcGXVXqZ25TtqS', NULL, '2025-06-06 17:44:54.000000'),
(6, 'PATRIZIA SERRI', 0, NULL, NULL, 1, 'okx74770@gmail.com', NULL, NULL, NULL, 'Francesca', 'RG7buzsSiVyI', NULL, '2025-05-13', 'patrizia', '$2y$10$dEto2S5LDSw7SLWaNBzECeLGWCI9hy6xi.CwHZZmeCb02w3qF2442', NULL, '2025-06-05 00:42:20.150020'),
(7, 'MariaLoredana Rodella', 0, NULL, NULL, 1, 'T5545888@gmail.com', NULL, NULL, NULL, 'Francesca', 'yqtHXwpGCrnA', NULL, '2025-05-09', 'icyy6', '$2y$10$gxmnznHlR/UhRYrUKjtorOjlxbpLFO37pbeXgmBtAHqqPRz0gBcD.', NULL, '2025-06-05 05:54:10.000000'),
(8, 'Raphael Olivier Marchand', 0, 'Male', '1983-08-11', 1, 'marchandraphael811@gmail.com', '+330652814629', 'France', '24 Rue de la Buffa 06000 Nice, France', 'Francesca', 'tip2rhlq8IML', NULL, '2025-05-26', 'Raphael', '$2y$10$jCSSAHQAUykTrQ/46jQ1Ieb9JkvUYaMx89Wv2oiFnw45IfLtyxlU2', '1749206523578.jpg', '2025-06-06 16:50:56.000000'),
(9, 'Meta Mask', 0, NULL, NULL, 1, 'metamask388@gmail.com', NULL, NULL, NULL, 'nexusinsights', 'i1uIrP9Tz5lg', 'Sg9FxUDsXqu2y1E', '2025-06-07', 'Meta', '$2y$10$ivYkQtK60IS1sfmWWMo/PesZtIxVtoZewpqjkKbvqI9haGiWvo1yK', NULL, '2025-06-07 15:48:05.000000'),
(10, 'Marco Antonio', 0, NULL, NULL, 1, 'bybit4220@gmail.com', NULL, NULL, NULL, 'nexusinsights', 'Xto3Cfvw84S6', NULL, '2025-06-07', 'Anto55', '$2y$10$dELkhQ.qJOk34Ipu7RzPhuw4DE1spGbbZMRUXCF0RiMZpTl3kz2gu', NULL, '2025-06-07 16:16:15.697644'),
(11, 'Luca Romano', 0, NULL, NULL, 1, 'blockchain24427@gmail.com', NULL, NULL, NULL, 'nexusinsights', 'txG4DwrsJS5B', 'uQ7cSUbVm6xvHLY', '2025-06-07', 'Romano', '$2y$10$e2goyUKQlAvmA/UmjEloG.bvINGKkNqKFKlaj3yeNRfR28hvygP/u', NULL, '2025-06-07 21:31:31.000000');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_logs`
--

CREATE TABLE `visitor_logs` (
  `id` int(11) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `visit_time` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `visitor_logs`
--

INSERT INTO `visitor_logs` (`id`, `page_name`, `visit_time`, `location`, `ip_address`, `user_id`) VALUES
(6, 'index.php', '2025-06-08 21:31:54', 'Lagos, LA, Nigeria', '102.88.112.178', NULL),
(7, 'investments.php', '2025-06-08 21:32:21', 'Lagos, LA, Nigeria', '102.88.112.178', NULL),
(8, 'login.php', '2025-06-08 21:33:19', 'Lagos, LA, Nigeria', '102.88.112.178', NULL),
(9, 'index.php', '2025-06-08 21:39:59', 'Ogbomoso, OY, Nigeria', '197.211.63.145', NULL),
(10, 'login.php', '2025-06-08 21:40:03', 'Ogbomoso, OY, Nigeria', '197.211.63.145', NULL),
(11, 'verify.php', '2025-06-08 21:41:19', 'Ogbomoso, OY, Nigeria', '197.211.63.145', NULL),
(12, 'login.php', '2025-06-08 21:41:19', 'Ogbomoso, OY, Nigeria', '197.211.63.145', NULL),
(13, 'verify.php', '2025-06-08 21:42:03', 'Ogbomoso, OY, Nigeria', '197.211.63.145', NULL),
(14, 'login.php', '2025-06-08 21:42:04', 'Ogbomoso, OY, Nigeria', '197.211.63.145', 8),
(15, 'dashboard.php', '2025-06-08 21:42:05', 'Ogbomoso, OY, Nigeria', '197.211.63.145', 8),
(16, 'dashboard.php', '2025-06-08 22:25:37', 'Ogbomoso, OY, Nigeria', '197.211.63.145', 8),
(17, 'profile.php', '2025-06-08 22:39:02', 'Ogbomoso, OY, Nigeria', '197.211.63.145', 8),
(18, 'dashboard.php', '2025-06-08 22:39:19', 'Ogbomoso, OY, Nigeria', '197.211.63.145', 8),
(19, 'index.php', '2025-06-08 22:56:41', 'Paris, IDF, France', '146.70.68.219', NULL),
(20, 'index.php', '2025-06-08 23:29:51', 'Zürich, ZH, Switzerland', '91.84.87.137', NULL),
(21, 'index.php', '2025-06-09 02:01:30', 'San Jose, CA, United States', '23.27.145.204', NULL),
(22, 'index.php', '2025-06-09 02:19:44', 'San Jose, CA, United States', '23.27.145.82', NULL),
(23, 'index.php', '2025-06-09 02:22:22', 'New York, NY, United States', '149.57.180.173', NULL),
(24, 'index.php', '2025-06-09 02:29:54', 'New York, NY, United States', '149.57.180.113', NULL),
(25, 'index.php', '2025-06-09 07:18:18', 'Santa Clara, CA, United States', '198.235.24.172', NULL),
(26, 'index.php', '2025-06-09 08:22:30', 'Paris, IDF, France', '194.59.249.247', NULL),
(27, 'login.php', '2025-06-09 08:22:34', 'Paris, IDF, France', '194.59.249.247', NULL),
(28, 'verify.php', '2025-06-09 08:22:38', 'Paris, IDF, France', '194.59.249.247', NULL),
(29, 'login.php', '2025-06-09 08:22:39', 'Paris, IDF, France', '194.59.249.247', NULL),
(30, 'index.php', '2025-06-09 10:21:12', 'Paris, IDF, France', '194.59.249.250', NULL),
(31, 'login.php', '2025-06-09 10:21:16', 'Paris, IDF, France', '194.59.249.250', NULL),
(32, 'verify.php', '2025-06-09 10:21:24', 'Paris, IDF, France', '194.59.249.250', NULL),
(33, 'login.php', '2025-06-09 10:21:24', 'Paris, IDF, France', '194.59.249.250', NULL),
(34, 'index.php', '2025-06-09 10:22:53', 'Paris, IDF, France', '194.59.249.250', NULL),
(35, 'login.php', '2025-06-09 10:23:02', 'Paris, IDF, France', '194.59.249.250', NULL),
(36, 'verify.php', '2025-06-09 10:23:10', 'Paris, IDF, France', '194.59.249.250', NULL),
(37, 'login.php', '2025-06-09 10:23:11', 'Paris, IDF, France', '194.59.249.250', 2),
(38, 'dashboard.php', '2025-06-09 10:23:11', 'Paris, IDF, France', '194.59.249.250', 2),
(39, 'dashboard.php', '2025-06-09 10:23:34', 'Paris, IDF, France', '194.59.249.250', 2),
(40, 'investments_details.php', '2025-06-09 10:23:36', 'Paris, IDF, France', '194.59.249.250', 2),
(41, 'dashboard.php', '2025-06-09 10:23:38', 'Paris, IDF, France', '194.59.249.250', 2),
(42, 'investments.php', '2025-06-09 10:23:59', 'Paris, IDF, France', '194.59.249.250', 2),
(43, 'invest_summary.php', '2025-06-09 10:24:07', 'Paris, IDF, France', '194.59.249.250', 2),
(44, 'dashboard.php', '2025-06-09 10:24:12', 'Paris, IDF, France', '194.59.249.250', 2),
(45, 'dashboard.php', '2025-06-09 10:49:13', 'Paris, IDF, France', '194.59.249.250', 2),
(46, 'news-detail.php', '2025-06-09 10:49:20', 'Paris, IDF, France', '194.59.249.250', 2),
(47, 'news.php', '2025-06-09 10:49:43', 'Paris, IDF, France', '194.59.249.250', 2),
(48, 'news-detail.php', '2025-06-09 10:57:28', 'Paris, IDF, France', '194.59.249.250', 2),
(49, 'dashboard.php', '2025-06-09 10:57:31', 'Paris, IDF, France', '194.59.249.250', 2),
(50, 'deposits.php', '2025-06-09 10:57:37', 'Paris, IDF, France', '194.59.249.250', 2),
(51, 'deposits-add-fund.php', '2025-06-09 10:57:39', 'Paris, IDF, France', '194.59.249.250', 2),
(52, 'deposits-payment-option.php', '2025-06-09 10:57:44', 'Paris, IDF, France', '194.59.249.250', 2),
(53, 'deposits-complete-request.php', '2025-06-09 10:57:49', 'Paris, IDF, France', '194.59.249.250', 2),
(54, 'deposits.php', '2025-06-09 10:58:12', 'Paris, IDF, France', '194.59.249.250', 2),
(55, 'dashboard.php', '2025-06-09 10:58:16', 'Paris, IDF, France', '194.59.249.250', 2),
(56, 'dashboard.php', '2025-06-09 11:15:30', 'Paris, IDF, France', '194.59.249.250', NULL),
(57, 'login.php', '2025-06-09 11:15:31', 'Paris, IDF, France', '194.59.249.250', NULL),
(58, 'about.php', '2025-06-09 11:15:35', 'Paris, IDF, France', '194.59.249.250', NULL),
(59, 'index.php', '2025-06-09 11:17:59', 'Abuja, FC, Nigeria', '197.210.78.88', NULL),
(60, 'index.php', '2025-06-09 11:18:06', 'Mountain View, CA, United States', '66.249.93.129', NULL),
(61, 'index.php', '2025-06-09 11:18:06', 'Mountain View, CA, United States', '74.125.210.110', NULL),
(62, 'index.php', '2025-06-09 11:18:06', 'Mountain View, CA, United States', '74.125.210.110', NULL),
(63, 'about.php', '2025-06-09 11:19:12', 'Abuja, FC, Nigeria', '197.210.78.88', NULL),
(64, 'index.php', '2025-06-09 11:32:04', 'Paris, IDF, France', '194.59.249.250', NULL),
(65, 'about.php', '2025-06-09 12:19:44', 'Paris, IDF, France', '194.59.249.250', NULL),
(66, 'login.php', '2025-06-09 12:19:47', 'Paris, IDF, France', '194.59.249.250', NULL),
(67, 'verify.php', '2025-06-09 12:19:55', 'Paris, IDF, France', '194.59.249.250', NULL),
(68, 'login.php', '2025-06-09 12:19:55', 'Paris, IDF, France', '194.59.249.250', 2),
(69, 'dashboard.php', '2025-06-09 12:19:56', 'Paris, IDF, France', '194.59.249.250', 2),
(70, 'withdrawals.php', '2025-06-09 12:20:05', 'Paris, IDF, France', '194.59.249.250', 2),
(71, 'withdrawals-remove-fund.php', '2025-06-09 12:20:09', 'Paris, IDF, France', '194.59.249.250', 2),
(72, 'withdrawals-payment-complete.php', '2025-06-09 12:20:20', 'Paris, IDF, France', '194.59.249.250', 2),
(73, 'withdrawals.php', '2025-06-09 12:20:30', 'Paris, IDF, France', '194.59.249.250', 2),
(74, 'index.php', '2025-06-09 12:20:57', 'Paris, IDF, France', '194.59.249.250', NULL),
(75, 'index.php', '2025-06-09 12:20:58', 'Paris, IDF, France', '194.59.249.250', NULL),
(76, 'login.php', '2025-06-09 12:20:59', 'Paris, IDF, France', '194.59.249.250', NULL),
(77, 'login.php', '2025-06-09 12:21:00', 'Paris, IDF, France', '194.59.249.250', NULL),
(78, 'verify.php', '2025-06-09 12:21:08', 'Paris, IDF, France', '194.59.249.250', NULL),
(79, 'login.php', '2025-06-09 12:21:09', 'Paris, IDF, France', '194.59.249.250', NULL),
(80, 'profile.php', '2025-06-09 12:22:10', 'Paris, IDF, France', '194.59.249.250', 2),
(81, 'dashboard.php', '2025-06-09 12:23:11', 'Paris, IDF, France', '194.59.249.250', 2),
(82, 'withdrawals.php', '2025-06-09 12:23:23', 'Paris, IDF, France', '194.59.249.250', 2),
(83, 'withdrawals-remove-fund.php', '2025-06-09 12:23:25', 'Paris, IDF, France', '194.59.249.250', 2),
(84, 'withdrawals-payment-complete.php', '2025-06-09 12:23:35', 'Paris, IDF, France', '194.59.249.250', 2),
(85, 'withdrawals.php', '2025-06-09 12:23:42', 'Paris, IDF, France', '194.59.249.250', 2),
(86, 'withdrawals.php', '2025-06-09 12:37:44', 'Paris, IDF, France', '194.59.249.250', 2),
(87, 'withdrawals-remove-fund.php', '2025-06-09 12:37:58', 'Paris, IDF, France', '194.59.249.250', 2),
(88, 'withdrawals-remove-fund.php', '2025-06-09 12:38:00', 'Paris, IDF, France', '194.59.249.250', 2),
(89, 'withdrawals-payment-complete.php', '2025-06-09 12:38:07', 'Paris, IDF, France', '194.59.249.250', 2),
(90, 'withdrawals.php', '2025-06-09 12:38:26', 'Paris, IDF, France', '194.59.249.250', 2),
(91, 'withdrawals.php', '2025-06-09 12:52:14', 'Paris, IDF, France', '194.59.249.250', NULL),
(92, 'login.php', '2025-06-09 12:52:15', 'Paris, IDF, France', '194.59.249.250', NULL),
(93, 'login.php', '2025-06-09 13:01:34', 'Paris, IDF, France', '194.59.249.250', NULL),
(94, 'index.php', '2025-06-09 13:01:51', 'Paris, IDF, France', '194.59.249.250', NULL),
(95, 'index.php', '2025-06-09 13:48:24', 'Paris, IDF, France', '194.59.249.250', NULL),
(96, 'index.php', '2025-06-09 15:04:22', 'Paris, IDF, France', '194.59.249.248', NULL),
(97, 'index.php', '2025-06-09 16:00:36', 'Paris, IDF, France', '194.59.249.248', NULL),
(98, 'login.php', '2025-06-09 16:00:39', 'Paris, IDF, France', '194.59.249.248', NULL),
(99, 'verify.php', '2025-06-09 16:00:48', 'Paris, IDF, France', '194.59.249.248', NULL),
(100, 'login.php', '2025-06-09 16:00:48', 'Paris, IDF, France', '194.59.249.248', NULL),
(101, 'index.php', '2025-06-09 17:07:41', 'Paris, IDF, France', '194.59.249.248', NULL),
(102, 'login.php', '2025-06-09 17:07:59', 'Paris, IDF, France', '194.59.249.248', NULL),
(103, 'login.php', '2025-06-09 19:11:53', 'Paris, IDF, France', '194.59.249.248', NULL),
(104, 'verify.php', '2025-06-09 19:12:02', 'Paris, IDF, France', '194.59.249.248', NULL),
(105, 'login.php', '2025-06-09 19:12:02', 'Paris, IDF, France', '194.59.249.248', NULL),
(106, 'index.php', '2025-06-09 21:41:56', 'Karu, NA, Nigeria', '197.211.63.33', NULL),
(107, 'register.php', '2025-06-09 21:42:01', 'Karu, NA, Nigeria', '197.211.63.33', NULL),
(108, 'terms.php', '2025-06-09 21:42:05', 'Karu, NA, Nigeria', '197.211.63.33', NULL),
(109, 'terms.php', '2025-06-09 21:42:20', 'Karu, NA, Nigeria', '197.211.63.33', NULL),
(110, 'login.php', '2025-06-09 21:42:27', 'Karu, NA, Nigeria', '197.211.63.33', NULL),
(111, 'verify.php', '2025-06-09 21:43:01', 'Karu, NA, Nigeria', '197.211.63.33', NULL),
(112, 'login.php', '2025-06-09 21:43:02', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(113, 'dashboard.php', '2025-06-09 21:43:03', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(114, 'dashboard.php', '2025-06-09 21:43:20', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(115, 'dashboard.php', '2025-06-09 21:43:22', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(116, 'dashboard.php', '2025-06-09 21:43:27', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(117, 'dashboard.php', '2025-06-09 21:43:29', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(118, 'withdrawals.php', '2025-06-09 21:43:55', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(119, 'history.php', '2025-06-09 19:44:09', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(120, 'dashboard.php', '2025-06-09 21:45:17', 'Karu, NA, Nigeria', '197.211.63.33', 8),
(121, 'dashboard.php', '2025-06-09 22:25:01', 'Karu, NA, Nigeria', '197.211.63.33', NULL),
(122, 'index.php', '2025-06-09 22:29:19', 'Phoenix, AZ, United States', '20.171.207.44', NULL),
(123, 'news-detail.php', '2025-06-09 22:29:26', 'Phoenix, AZ, United States', '20.171.207.44', NULL),
(124, 'index.php', '2025-06-09 23:17:41', 'Paris, IDF, France', '146.70.68.202', NULL),
(125, 'login.php', '2025-06-09 23:27:42', 'Paris, IDF, France', '146.70.68.202', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`act_id`);

--
-- Indexes for table `blockchaintx`
--
ALTER TABLE `blockchaintx`
  ADD PRIMARY KEY (`btx_id`);

--
-- Indexes for table `direct_message`
--
ALTER TABLE `direct_message`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `error_log`
--
ALTER TABLE `error_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hp_deposits`
--
ALTER TABLE `hp_deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hp_transactions`
--
ALTER TABLE `hp_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hp_withdrawals`
--
ALTER TABLE `hp_withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `investment`
--
ALTER TABLE `investment`
  ADD PRIMARY KEY (`invest_id`);

--
-- Indexes for table `investment_plans`
--
ALTER TABLE `investment_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`trans_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uname` (`uname`);

--
-- Indexes for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `act_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `blockchaintx`
--
ALTER TABLE `blockchaintx`
  MODIFY `btx_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `direct_message`
--
ALTER TABLE `direct_message`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `error_log`
--
ALTER TABLE `error_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hp_deposits`
--
ALTER TABLE `hp_deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hp_transactions`
--
ALTER TABLE `hp_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hp_withdrawals`
--
ALTER TABLE `hp_withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `investment`
--
ALTER TABLE `investment`
  MODIFY `invest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `investment_plans`
--
ALTER TABLE `investment_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `trans_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
