-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2014 at 03:38 AM
-- Server version: 5.6.11
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gb_production`
--
CREATE DATABASE IF NOT EXISTS `gb_production` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `gb_production`;

-- --------------------------------------------------------

--
-- Table structure for table `gb_assets`
--

CREATE TABLE IF NOT EXISTS `gb_assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_object` varchar(250) NOT NULL,
  `asset_revision` int(11) NOT NULL,
  `asset_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `globe_id` int(11) NOT NULL,
  PRIMARY KEY (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `gb_assets`
--

INSERT INTO `gb_assets` (`asset_id`, `asset_object`, `asset_revision`, `asset_create`, `globe_id`) VALUES
(1, '0108584528', 0, '2014-05-18 13:00:36', 99);

-- --------------------------------------------------------

--
-- Table structure for table `gb_documents`
--

CREATE TABLE IF NOT EXISTS `gb_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_owner` int(11) NOT NULL DEFAULT '0',
  `doc_name` varchar(120) NOT NULL,
  `doc_desc` varchar(250) NOT NULL,
  `doc_filename` varchar(250) NOT NULL,
  `doc_type` varchar(10) NOT NULL,
  `doc_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `gb_documents`
--

INSERT INTO `gb_documents` (`document_id`, `doc_owner`, `doc_name`, `doc_desc`, `doc_filename`, `doc_type`, `doc_create`) VALUES
(0, 0, 'Shopping List', 'Shopping List for Supervalue', 'list.txt', 'txt', '2014-05-18 13:03:01'),
(1, 0, 'Sample Excel File', 'Sample Excel File', 'sample.xlsx', 'xlsx', '2014-05-18 22:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `gb_globes`
--

CREATE TABLE IF NOT EXISTS `gb_globes` (
  `globe_id` int(11) NOT NULL AUTO_INCREMENT,
  `globe_name` varchar(250) NOT NULL,
  `globe_desc` varchar(250) NOT NULL,
  `globe_code` varchar(250) NOT NULL,
  `globe_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `globe_owner` int(11) NOT NULL,
  `globe_asset` int(11) DEFAULT NULL,
  PRIMARY KEY (`globe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=100 ;

--
-- Dumping data for table `gb_globes`
--

INSERT INTO `gb_globes` (`globe_id`, `globe_name`, `globe_desc`, `globe_code`, `globe_create`, `globe_owner`, `globe_asset`) VALUES
(1, 'Project Presentation 1', 'Sample Globe for Project Presentation', 'PRG1', '2014-05-18 12:52:59', 0, NULL),
(2, 'Project Presentation 2', 'Sample Globe for Project Presentation', 'PRG2', '2014-05-18 12:52:59', 0, NULL),
(3, 'Project Presentation 3', 'Sample Globe for Project Presentation', 'PRG3', '2014-05-18 12:54:14', 0, NULL),
(4, 'Project Presentation 4', 'Sample Globe for Project Presentation', 'PRG4', '2014-05-18 12:54:14', 0, NULL),
(99, 'Keyring', 'RFID from Supervalue Keyring', 'SV_Key', '2014-05-18 13:01:47', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gb_groups`
--

CREATE TABLE IF NOT EXISTS `gb_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_owner` int(11) NOT NULL DEFAULT '0',
  `group_name` varchar(120) NOT NULL,
  `group_desc` varchar(250) NOT NULL,
  `group_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `gb_groups`
--

INSERT INTO `gb_groups` (`group_id`, `group_owner`, `group_name`, `group_desc`, `group_create`) VALUES
(0, 0, 'Undefined', 'Undefined', '2014-05-18 12:48:47'),
(1, 0, 'PREZ1', 'Presentation Sample Group', '2014-05-18 12:49:09'),
(5, 0, 'R&D1', 'Research & Developers Group', '2014-05-18 13:08:46'),
(6, 0, 'Purchasing', 'Purchasing and Stock control', '2014-05-18 13:09:08'),
(7, 0, 'HR', 'Human Resources Department', '2014-05-19 13:57:33'),
(8, 0, 'Services', 'Support Services Group', '2014-05-19 13:59:39');

-- --------------------------------------------------------

--
-- Table structure for table `gb_sessions`
--

CREATE TABLE IF NOT EXISTS `gb_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_token` varchar(250) NOT NULL,
  `session_activity` int(11) NOT NULL,
  `session_create` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53 ;

--
-- Dumping data for table `gb_sessions`
--

INSERT INTO `gb_sessions` (`session_id`, `session_token`, `session_activity`, `session_create`) VALUES
(1, '86d5cf3247ec40a717465cadc28c61bf6ecfa685', 1, '2014-05-18 13:31:46'),
(2, 'cc6a4615e6126a6a778376189ee2f73707f19bd5', 2, '2014-05-18 13:32:01'),
(3, 'c7339fb36c4d37a5db7ab27cf1317bc7ffc3153c', 1, '2014-05-18 13:33:58'),
(4, 'cf1fd4ef131ffb515cb24ecdecf4d3ba28a2bc42', 1, '2014-05-18 13:34:47'),
(5, '64c0b104c0019af6c9c0a18e083f8ed61ae711f6', 2, '2014-05-18 13:35:35'),
(6, '536907d386ecb942b03bb2aeb049fece0f8f3e01', 1, '2014-05-18 13:38:44'),
(7, 'f6aee05fe77b0c8e6a8ca602695325c94cd69344', 1, '2014-05-18 13:50:21'),
(8, 'e7a7090a9bd51b41760a1258e845a2c6c5510bee', 1, '2014-05-18 13:51:24'),
(9, 'b4446212012fd5416174788f429d32f40a1bc2bb', 1, '2014-05-18 13:52:00'),
(10, '2d83c251c2cd1ef839dafa71be32c65844f31e8a', 2, '2014-05-18 13:52:40'),
(11, '3bdefd44d8eb98ced004dcf477c444c32e42d146', 1, '2014-05-18 13:55:53'),
(12, '4c848fcb5885b0d3103171799bda0e03a8028af7', 2, '2014-05-18 13:56:14'),
(13, 'cac0bee6fa9c64ba3a9479952b491438b5d373e1', 1, '2014-05-18 13:56:27'),
(14, '57605e9fd02d6df66d898dd0255c7ca9a227d75a', 1, '2014-05-18 13:59:41'),
(15, '379633ba77921b4899cc0008fb88897b6a002965', 2, '2014-05-18 13:59:57'),
(16, 'd4a94cd60f885cd30f75cc8578dac27805e8617b', 1, '2014-05-18 14:00:51'),
(17, 'c21f853885e032262375d0eeb908b5219c0a9642', 2, '2014-05-18 14:01:03'),
(18, '4616994a0600d981c3e3b35b890f5b9432b99009', 1, '2014-05-18 14:05:14'),
(19, '4e8ce39d955b0a91f15e756ecde7a66e21debc89', 2, '2014-05-18 14:05:31'),
(20, 'ab2bb5c19c8852f03cba78886a78538ab2e4d6d4', 1, '2014-05-18 14:06:46'),
(21, '2cea7b13d1c82c8be9373ac9a3031a9d3800c274', 2, '2014-05-18 14:06:57'),
(22, 'de9bbfe14c7c74a9762db1134620eac1fb4e9386', 1, '2014-05-18 14:16:02'),
(23, '09e0047be7e7105fea09b0edefe949f3ae14aeb9', 2, '2014-05-18 14:16:16'),
(24, 'e78097966229e43d6041c0aa92a177b5a8a90ea8', 1, '2014-05-18 14:31:25'),
(25, 'dcedabb96a22229a90c3aa9c2a65971c90018cfd', 2, '2014-05-18 14:31:36'),
(26, 'bb9237dafe2bffb02ec1209810e91b7ee606c5d9', 1, '2014-05-18 14:40:10'),
(27, 'a923d65e5c8c0091f4995d1570145e21a4f00387', 2, '2014-05-18 14:40:19'),
(28, 'fb2540a03b882ef7e6c86ab80e11f99c330840bb', 1, '2014-05-18 14:42:36'),
(29, '50eea652fcc5dcdd6aa0aff2f1ce0a21d8545d6f', 2, '2014-05-18 14:42:47'),
(30, '5a997b60377cdadf05b90f0786f34f17a94d6c09', 1, '2014-05-18 14:45:02'),
(31, '8464df925077ad5401efc7fbdca793dac6b7cb46', 2, '2014-05-18 14:45:11'),
(32, '210aaa6d49e7d8e23b48d0329f6d9ccd2ad7e2f8', 1, '2014-05-18 14:47:58'),
(33, 'ebe090487e152c61c15c8ead4e7b2526ff529a21', 2, '2014-05-18 14:48:21'),
(34, '1a07ac5919246b1ce9dedad382cc11a6461a1fed', 1, '2014-05-18 14:57:33'),
(35, 'e5edb8b0d69dd2c666300dcec04bb6bcfc5fb1bc', 2, '2014-05-18 14:57:49'),
(36, '7794dd3ccfedfcbbdb03688ff938f5238984329c', 1, '2014-05-18 22:08:27'),
(37, 'fbc2c7020a2150804ca9aa9809523f39ba1c50c0', 1, '2014-05-18 22:09:04'),
(38, '872d45235a638de27b73e953af8cd11aef701e65', 1, '2014-05-18 22:25:43'),
(39, '64934bb3947cc9738f4b2343d0afb7970f7549e6', -1, '2014-05-18 22:26:44'),
(40, 'af135b3b3d0712d3b99edc1626935f0bb3ee4c1a', 2, '2014-05-18 22:32:24'),
(41, '24915e4437dfd1aea47193cb7ca467503c76015e', 1, '2014-05-18 23:00:53'),
(42, '059615fda9ee1b4a06f9764ace476c448f40176e', 2, '2014-05-18 23:01:09'),
(43, '7322517310e9064f4e2867a7a55fb44efb607f79', 2, '2014-05-18 23:02:12'),
(44, 'cfbc97c6a8c004412c2f7153044db2652a4949a5', 1, '2014-05-19 19:31:45'),
(45, '79ef8a748a08964d79278650f5d1c7dad2f3a3fc', 2, '2014-05-19 19:31:57'),
(46, '92eae75c3ab2cac028e2e554ebb2873a4f1e5c93', 2, '2014-05-19 19:32:13'),
(47, 'faa978564d088689351a9e34855a0945ce3dbc6c', 1, '2014-05-19 20:05:14'),
(48, '01c71908f5b8fee70322aa31a30bf80aa888aa37', 2, '2014-05-19 20:05:23'),
(49, 'c2a1713d7404a2deba64bceec1553ffc787f5f8f', 2, '2014-05-19 20:12:47'),
(50, 'fbd722b44e742fdab10909accf57a98f3c454463', 2, '2014-05-19 20:12:59'),
(51, 'cbe2d938c09586ab01f46a2a755befe6a521ae00', 1, '2014-05-20 01:15:43'),
(52, 'be52c232b61714917d268d9e8497f8e781b496e0', 2, '2014-05-20 01:25:14');

-- --------------------------------------------------------

--
-- Table structure for table `gb_users`
--

CREATE TABLE IF NOT EXISTS `gb_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL DEFAULT '1',
  `user_password` varchar(64) NOT NULL DEFAULT '0',
  `user_first` varchar(250) DEFAULT 'undefined',
  `user_last` varchar(250) DEFAULT 'undefined',
  `user_email` varchar(250) DEFAULT 'undefined',
  `user_dept` varchar(250) DEFAULT 'undefined',
  `group_id` int(11) DEFAULT '0',
  `user_super` int(1) NOT NULL DEFAULT '0',
  `user_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `gb_users`
--

INSERT INTO `gb_users` (`user_id`, `user_name`, `user_password`, `user_first`, `user_last`, `user_email`, `user_dept`, `group_id`, `user_super`, `user_create`) VALUES
(0, 'admin', '12468a3765a017735dfc37d01478af19291e2d0f', 'Administrator', 'System', 'admin@globlock.net', 'sys', 0, 1, '2014-05-18 12:50:38'),
(1, 'alex', '12468a3765a017735dfc37d01478af19291e2d0f', 'Alex', 'Quigley', 'quigley.alex@gmail.com', 'bshce4-nm', 1, 1, '2014-05-18 12:46:54'),
(2, 'undefined', '12468a3765a017735dfc37d01478af19291e2d0f', 'Joe', 'Bloggs', 'joe@bloggs.ie', 'PRV', 8, 0, '2014-05-19 14:08:58');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
