-- Pocket - Rewards Application SQL Dump
-- version 3.7
--
-- Admin Panel : https://www.codyhub.com/item/pocket-webpanel/
--
-- Web Version : https://www.codyhub.com/item/web-rewards-app-pocket/
-- Android Version : https://www.codyhub.com/item/android-rewards-app-pocket/
-- IOS Version : https://www.codyhub.com/item/ios-rewards-app-pocket/
--
-- ADDONS : https://www.aym.com/products/
--
-- Run this SQL file to create the neccessary tables and default
-- database entries needed to get the script working.
--
-- Created by AYM - https://www.aym.com
--
-- Host: localhost
-- Generation Time: Apr 29, 2020
-- PHP Version: 7.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pocket`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_data`
--

CREATE TABLE `access_data` (
  `id` int(11) UNSIGNED NOT NULL,
  `accountId` int(11) UNSIGNED NOT NULL,
  `accessToken` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
  `clientId` int(11) UNSIGNED DEFAULT '0',
  `createAt` int(10) UNSIGNED DEFAULT '0',
  `removeAt` int(10) UNSIGNED DEFAULT '0',
  `u_agent` varchar(300) COLLATE utf8_unicode_ci DEFAULT '',
  `ip_addr` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `salt` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fullname` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `createAt` int(11) UNSIGNED DEFAULT '0',
  `u_agent` varchar(300) COLLATE utf8_unicode_ci DEFAULT '',
  `ip_addr` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL,
  `date` varchar(12) NOT NULL,
  `sessions` varchar(12) NOT NULL DEFAULT '0',
  `requests` varchar(12) NOT NULL DEFAULT '0',
  `completed` varchar(12) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Completed`
--

CREATE TABLE `Completed` (
  `rid` int(11) NOT NULL,
  `request_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dev_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dev_man` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gift_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `req_amount` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `points_used` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

CREATE TABLE `configuration` (
  `id` int(2) NOT NULL,
  `config_name` varchar(225) NOT NULL,
  `config_value` text NOT NULL,
  `api_status` varchar(2) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `configuration`
--

INSERT INTO `configuration` (`id`, `config_name`, `config_value`, `api_status`) VALUES
(1, 'APP_NAME', 'Pocket', '0'),
(2, 'APP_DESC', 'Money Making Script by AYM', '0'),
(3, 'SITE_NAME', 'Dashboard', '0'),
(4, 'SITE_DESC', 'Money Making Script by AYM', '0'),
(5, 'WEB_ROOT', 'https://example.com/', '1'),
(6, 'SUPPORT_EMAIL', 'support@example.com', '1'),
(7, 'BASE', 'PCKT', '0'),
(8, 'VERSION', '3.7', '1'),
(9, 'INSTALL', '0', '0'),
(10, 'COMPANY_NAME', 'Example Company', '1'),
(11, 'COMPANY_SITE', 'www.example.com', '1'),
(12, 'SUPPORT_PHONE', '+1 123 456 7890', '1'),
(13, 'COMPANY_COUNTRY', 'USA', '1'),
(14, 'COMPANY_EMAIL', 'hello@example.com', '1'),
(15, 'FIREBASE_API_KEY', 'AIzaSyAv23NYMxosF535eF7kkWxs2Dv_FwdOGqo', '0'),
(16, 'INCOME_OVERVIEW', '1', '0'),
(17, 'INCOME_OVERVIEW_TITLE', '0', '0'),
(18, 'ADMIN_RATIO', '250', '0'),
(19, 'REFER_ACTIVE', '1', '1'),
(20, 'REFER_REWARD', '100', '1'),
(21, 'USER_RATIO', '1000', '0'),
(22, 'DAILY_ACTIVE', '1', '1'),
(23, 'DAILY_REWARD', '25', '1'),
(24, 'LAST_SAVE', '', '0'),
(25, 'LAST_ADMIN_ACCESS', '', '0'),
(26, 'PACKAGE_NAME', 'com.example.appname', '1'),
(27, 'ADMIN', '1', '0'),
(28, 'ADMIN_IMAGE', 'person-placeholder.jpeg', '0'),
(29, 'ACCESS_TOKEN', '', '0'),
(30, 'ADMIN_NAME', 'John Doe', '0'),
(31, 'TRANSACTION_PREFIX', 'PCKT', '1'),
(32, 'TRANSACTION_CREDIT_PREFIX', 'CR010', '0'),
(33, 'TRANSACTION_DEBIT_PREFIX', 'DB010', '0'),
(34, 'APP_NAVBAR_ENABLE', '1', '1'),
(35, 'APP_TABS_ENABLE', '0', '1'),
(36, 'APP_DEBUG_MODE', '0', '1'),
(37, 'POLICY_ACTIVE', '1', '1'),
(38, 'APP_POLICY_URL', 'http://example.com/privacy', '1'),
(39, 'CONTACT_US_ACTIVE', '1', '1'),
(40, 'APP_CONTACT_US_URL', 'http://example.com/contact', '1'),
(41, 'RATE_APP_ACTIVE', '1', '1'),
(42, 'INSTRUCTIONS_ACTIVE', '1', '1'),
(43, 'SHARE_APP_ACTIVE', '1', '1'),
(44, 'APP_SHARE_TEXT', 'Hey Look, What a Wonderful App i have found.', '1'),
(45, 'CHECKIN_BONUS_TITLE', 'Daily Checkin Credit', '0'),
(46, 'REFERAL_BONUS_TITLE', 'Invitation Bonus', '0'),
(47, 'REFERER_BONUS_TITLE', 'Referral Bonus', '0'),
(48, 'APP_VENDOR', 'AYM', '0'),
(49, 'VENDOR_URL', 'www.aym.com', '0'),
(50, 'AdGateMediaActive', '1', '1'),
(51, 'AdGate_Media_WallId', 'naucrg', '1'),
(52, 'AdScendMediaActive', '1', '1'),
(53, 'AdScendMedia_PubId', '107661', '1'),
(54, 'AdScendMedia_AdwallId', '7451', '1'),
(55, 'API_OFFERS_ACTIVE', '1', '1'),
(56, 'VENDOR_SUPPORT_URL', 'http://www.aym.com/support', '1'),
(57, 'WEB_LICENSE_URL', 'http://www.codyhub.com/item/pocket-webpanel', '1'),
(58, 'APP_LICENSE_URL', 'http://www.codyhub.com/item/android-rewards-app-pocket', '1'),
(59, 'APP_LICENSE_URL_WEB', 'http://www.codyhub.com/item/web-rewards-app-pocket', '1'),
(60, 'APP_LICENSE_URL_IOS', 'http://www.codyhub.com/item/ios-rewards-app-pocket', '1'),
(61, 'SPIN_TITLE', 'Spin & Win', '1'),
(62, 'SPIN_REWARD_TITLE', 'Spin Wheel Credit', '0'),
(63, 'APP_SHARE_URL_ANDROID', 'https://play.google.com/store/apps/details?id=com.example.appname', '1'),
(64, 'APP_SHARE_URL_IOS', 'https://apps.apple.com/bt/app/app-name/id123456', '1'),
(65, 'APP_TERMS_URL', 'http://example.com/terms', '1'),
(66, 'WEB_SHOW_NEW_FEATURE_NOTICE', '1', '0'),
(67, 'WEB_SHOW_ANNOUNCEMENT', '1', '0'),
(68, 'WEB_ANNOUNCEMENT_TEXT', 'Show Any Announcements Here.You can change this text or completely disable it from your Admin Panel.', '0'),
(69, 'WEB_SHOW_RECENT_PAYOUTS', '1', '0'),
(70, 'NOTICE_TRANSACTIONS', 'All Pending and Processing payout requests will be processed at every Saturday as we provide weekly Payments. If you wish to cancel or modify the payout request please contact us at suppoty@example.com or alternatively you can contact us at http://example.com/support', '1'),
(71, 'NOTICE_REFER_AND_EARN', 'Make Sure to Refer real and genuine members only. Self referring and Fake Referrals are strictly prohibited and may cause your Account to be Blocked !', '1'),
(72, 'NOTICE_REDEEM', 'All Payout requests will be processed at every Saturday as we provide weekely Payments. If you wish to cancel or modify the payout request please contact us at suppoty@example.com or alternatively you can contact us at http://example.com/support', '1'),
(73, 'SITE_LOGO_LIGHT', 'logo-light.png', '0'),
(74, 'SITE_LOGO_DARK', 'logo-dark.png', '0'),
(75, 'SITE_FAVICON', 'favicon.ico', '0'),
(76, 'CpaLeadActive', '1', '1'),
(77, 'CpaLead_DirectLink', 'https://viral782.com/list/381406', '1'),
(78, 'WannadsActive', '1', '1'),
(79, 'WannadsApiKey', '5deb5aeb82512186481670', '1'),
(80, 'AdMantumActive', '1', '1'),
(81, 'AdMantum_PubId', '217543', '1'),
(82, 'AdMantum_AppId', '11969', '1'),
(83, 'AdMantum_SecretKey', 'adm1234567', '1'),
(84, 'OAOD_CHECK', '0', '0'),
(85, 'GOOGLE_LOGIN_WEB', '1', '0'),
(86, 'GOOGLE_CLIENT_ID', '830402320972-v43npmoh5e621suttsb989u3a53mdtcl', '0'),
(87, 'GOOGLE_SECRET_ID', '0rlWWIFOH-QEHgsyVjJeQsqu', '0'),
(88, 'FACEBOOK_LOGIN_WEB', '1', '0'),
(89, 'FACEBOOK_APP_ID', '551019701964537', '0'),
(90, 'FACEBOOK_SECRET_ID', '990f70f9408ccad76d9053aad54a50eb', '0'),
(91, 'NOTICE_ADBLOCK_TITLE', 'AdBlocker Detected !', '1'),
(92, 'ADBLOCK_WEB', '1', '1'),
(93, 'SMTP_AUTH', '1', '0'),
(94, 'NOTICE_ADBLOCK', 'Please disable adblocker as it may cause issues with displaying and crediting offers. Reload this page once disabled.', '1'),
(95, 'SMTP_EMAIL', 'example@gmail.com', '0'),
(96, 'SMTP_USERNAME', 'example@gmail.com', '0'),
(97, 'SMTP_PASSWORD', '123456', '0'),
(98, 'SMTP_HOST', 'smtp.gmail.com', '0'),
(99, 'SMTP_PORT', '587', '0'),
(100, 'SMTP_SECURE', 'TLS', '0'),
(101, 'KiwiWallActive', '1', '1'),
(102, 'KiwiWall_WallId', 'x7WSuXuvjGsHLevNVY4qiORPLFb7RBAS', '1'),
(103, 'KiwiWall_APIKEY', '2udP6T46zDawoO973tLiIg9h6Gj4jF3J', '1'),
(104, 'KiwiWall_SECKEY', 'rHyprpB1uzhJN0hb0ASSZ7VBXqed5nmI', '1');

-- --------------------------------------------------------

--
-- Table structure for table `offerwalls`
--

CREATE TABLE `offerwalls` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `points` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `featured` varchar(2) NOT NULL,
  `position` varchar(10) NOT NULL,
  `status` varchar(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `offerwalls`
--

INSERT INTO `offerwalls` (`id`, `name`, `subtitle`, `url`, `points`, `image`, `type`, `featured`, `position`, `status`) VALUES
(1, 'Daily Checkin', 'open App daily and get 25 Points', '', '25', 'ic_place_holder.png', 'checkin', '1', '1', '1'),
(2, 'Spin & Win', 'Spin the Wheel To Win Points', '', '0', 'ic_place_holder.png', 'spin', '1', '2', '1'),
(3, 'Refer & Earn', 'Refer Friends and Get Points', '', '100', 'ic_place_holder.png', 'refer', '1', '3', '1'),
(4, 'Transactions', 'View All your Transactions', '', '0', 'ic_place_holder.png', 'transactions', '1', '4', '1'),
(5, 'Redeem', 'Turn your Points into cash', '', '0', 'ic_place_holder.png', 'redeem', '1', '5', '1'),
(6, 'Instructions', 'How to Earn Points', '', '0', 'ic_place_holder.png', 'instructions', '1', '6', '0'),
(7, 'Share This App', 'Help your friends find this App', '', '0', 'ic_place_holder.png', 'share', '1', '7', '0'),
(8, 'Rate the App', 'Support us by Rating our App', '', '0', 'ic_place_holder.png', 'rate', '1', '8', '0'),
(9, 'About Us', 'Advertise with Us', '', '0', 'ic_place_holder.png', 'about', '1', '9', '0'),
(10, 'AdMantum', 'Install Apps To Earn Points', '', '0', 'ic_place_holder.png', 'admantum', '1', '10', '1'),
(11, 'AdGateMedia', 'Install Apps To Earn Points', '', '0', 'ic_place_holder.png', 'adgatemedia', '1', '11', '1'),
(12, 'Adscend Media', 'Install Apps To Earn Points', '', '0', 'ic_place_holder.png', 'adscendmedia', '1', '12', '1'),
(13, 'CpaLead', 'Install Apps To Earn Points', '', '0', 'ic_place_holder.png', 'cpalead', '1', '13', '1'),
(14, 'Wannads', 'Install Apps To Earn Points', '', '0', 'ic_place_holder.png', 'wannads', '1', '14', '1'),
(15, 'KiwiWall', 'Install Apps To Earn Points', '', '0', 'ic_place_holder.png', 'kiwiwall', '1', '15', '1');

-- --------------------------------------------------------

--
-- Table structure for table `offer_status`
--

CREATE TABLE `offer_status` (
  `id` int(11) NOT NULL,
  `cid` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `of_id` varchar(255) NOT NULL,
  `of_title` varchar(255) NOT NULL,
  `of_amount` varchar(255) NOT NULL DEFAULT '0',
  `of_url` varchar(255) NOT NULL,
  `partner` varchar(255) NOT NULL,
  `ip_addr` varchar(255) NOT NULL,
  `dev_name` varchar(255) NOT NULL,
  `dev_man` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payouts`
--

CREATE TABLE `payouts` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `points` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` varchar(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payouts`
--

INSERT INTO `payouts` (`id`, `name`, `subtitle`, `message`, `amount`, `points`, `image`, `status`) VALUES
(1, 'Paypal', '1000 Points = $1 USD', 'Enter your Paypal Email :', '$1 USD', '1000', 'ic_place_holder.png', '1'),
(2, 'Paypal', '4500 Points = $5 USD', 'Enter your Paypal Email :', '$5 USD', '4500', 'ic_place_holder.png', '1'),
(3, 'PayTm', '1000 Points = 100 INR', 'Enter your Paytm Mobile Number :', '100 INR', '1000', 'ic_place_holder.png', '1'),
(4, 'Amazon', '3000 Points = $2.5 USD', 'Enter your Amazon Email :', '$2.5 USD', '3000', 'ic_place_holder.png', '1'),
(5, 'Google Play', '9000 Points = $10 USD', 'Enter your Google Playstore Email :', '$10 USD', '9000', 'ic_place_holder.png', '1');

-- --------------------------------------------------------

--
-- Table structure for table `referers`
--

CREATE TABLE `referers` (
  `id` int(255) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `referer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `points` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Requests`
--

CREATE TABLE `Requests` (
  `rid` int(11) NOT NULL,
  `request_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dev_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dev_man` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gift_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `req_amount` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `points_used` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restore_data`
--

CREATE TABLE `restore_data` (
  `id` int(11) UNSIGNED NOT NULL,
  `accountId` int(11) UNSIGNED NOT NULL,
  `hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `clientId` int(11) UNSIGNED DEFAULT '0',
  `createAt` int(10) UNSIGNED DEFAULT '0',
  `removeAt` int(10) UNSIGNED DEFAULT '0',
  `u_agent` varchar(300) COLLATE utf8_unicode_ci DEFAULT '',
  `ip_addr` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tracker`
--

CREATE TABLE `tracker` (
  `id` int(255) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `points` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `last_access` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `last_ip_addr` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `gcm_regid` text COLLATE utf8_unicode_ci,
  `state` int(10) UNSIGNED DEFAULT '0',
  `fullname` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `salt` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `passw` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `login` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `regtime` int(10) UNSIGNED DEFAULT '0',
  `regtype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip_addr` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `points` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `refer` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `refered` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `referer` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whitelists`
--

CREATE TABLE `whitelists` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ip_addr` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `whitelists`
--

INSERT INTO `whitelists` (`id`, `name`, `ip_addr`) VALUES
(1, 'offertoro', '54.175.173.245'),
(2, 'adscendmedia', '204.232.224.18'),
(3, 'adscendmedia', '204.232.224.19'),
(4, 'adscendmedia', '104.130.46.116'),
(5, 'adscendmedia', '104.130.60.109'),
(6, 'adscendmedia', '104.239.224.178'),
(7, 'adscendmedia', '104.130.60.108'),
(8, 'test', '43.225.55.117'),
(9, 'adgatemedia', '104.130.7.162'),
(10, 'adgatemedia', '52.42.57.125'),
(11, 'superrewards', '54.85.0.76'),
(12, 'superrewards', '54.84.205.80'),
(13, 'superrewards', '54.84.27.163'),
(14, 'fyber', '85.195.83.44'),
(15, 'fyber', '146.0.239.0'),
(16, 'fyber', '146.0.239.1'),
(17, 'fyber', '146.0.239.2'),
(18, 'fyber', '146.0.239.3'),
(19, 'fyber', '146.0.239.4'),
(20, 'fyber', '146.0.239.5'),
(21, 'fyber', '146.0.239.6'),
(22, 'fyber', '146.0.239.7'),
(23, 'fyber', '146.0.239.8'),
(24, 'fyber', '146.0.239.9'),
(25, 'fyber', '146.0.239.10'),
(26, 'fyber', '146.0.239.255'),
(27, 'AYM', '103.50.162.86'),
(28, 'cpalead', '52.0.65.65'),
(29, 'admantum', '162.241.27.11'),
(30, 'admantum', '162.241.27.12'),
(31, 'admantum', '162.241.27.13'),
(32, 'admantum', '162.241.27.24'),
(33, 'AYM', '103.21.59.201'),
(34, 'kiwiwall', '34.193.235.172');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_data`
--
ALTER TABLE `access_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Completed`
--
ALTER TABLE `Completed`
  ADD PRIMARY KEY (`rid`);

--
-- Indexes for table `configuration`
--
ALTER TABLE `configuration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offerwalls`
--
ALTER TABLE `offerwalls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offer_status`
--
ALTER TABLE `offer_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payouts`
--
ALTER TABLE `payouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referers`
--
ALTER TABLE `referers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Requests`
--
ALTER TABLE `Requests`
  ADD PRIMARY KEY (`rid`);

--
-- Indexes for table `restore_data`
--
ALTER TABLE `restore_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tracker`
--
ALTER TABLE `tracker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indexes for table `whitelists`
--
ALTER TABLE `whitelists`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_data`
--
ALTER TABLE `access_data`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `configuration`
--
ALTER TABLE `configuration`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `offerwalls`
--
ALTER TABLE `offerwalls`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `offer_status`
--
ALTER TABLE `offer_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payouts`
--
ALTER TABLE `payouts`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `referers`
--
ALTER TABLE `referers`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Requests`
--
ALTER TABLE `Requests`
  MODIFY `rid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restore_data`
--
ALTER TABLE `restore_data`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tracker`
--
ALTER TABLE `tracker`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whitelists`
--
ALTER TABLE `whitelists`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
