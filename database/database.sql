SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+02:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `activity` (
  `act_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `category` text NOT NULL,
  `date_sent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `blockchaintx` (
  `btx_id` int(11) NOT NULL,
  `btx_address` text NOT NULL,
  `btx_txid` text NOT NULL,
  `btx_amount` decimal(65,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `direct_message` (
  `msg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `date_sent` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `error_log` (
  `id` int(11) NOT NULL,
  `error_message` text DEFAULT NULL,
  `error_time` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `hp_deposits` (
  `id` int(11) NOT NULL,
  `payment_type` text NOT NULL,
  `username` text NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `hp_transactions` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `type` int(1) NOT NULL,
  `amount` int(11) NOT NULL,
  `trans_date` date NOT NULL,
  `payment_mode` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `hp_withdrawals` (
  `id` int(11) NOT NULL,
  `payment_type` text NOT NULL,
  `username` text NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `investment_plans` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `duration` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `min_invest` int(11) NOT NULL,
  `max_invest` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `wallet_address` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `photo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `transaction` (
  `trans_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trans_date` text NOT NULL,
  `type` text NOT NULL,
  `amount` int(11) NOT NULL,
  `remark` text NOT NULL,
  `balance` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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


ALTER TABLE `activity`
  ADD PRIMARY KEY (`act_id`);

ALTER TABLE `blockchaintx`
  ADD PRIMARY KEY (`btx_id`);

ALTER TABLE `direct_message`
  ADD PRIMARY KEY (`msg_id`);

ALTER TABLE `error_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `hp_deposits`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `hp_transactions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `hp_withdrawals`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `investment`
  ADD PRIMARY KEY (`invest_id`);

ALTER TABLE `investment_plans`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `request`
  ADD PRIMARY KEY (`request_id`);

ALTER TABLE `transaction`
  ADD PRIMARY KEY (`trans_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uname` (`uname`);


ALTER TABLE `activity`
  MODIFY `act_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `blockchaintx`
  MODIFY `btx_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `direct_message`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `error_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `hp_deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `hp_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `hp_withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `investment`
  MODIFY `invest_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `investment_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `transaction`
  MODIFY `trans_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
