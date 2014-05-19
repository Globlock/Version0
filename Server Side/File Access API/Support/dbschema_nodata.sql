--
-- Database: `gb_production`
--
CREATE DATABASE IF NOT EXISTS `gb_production` 
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
);
-- --------------------------------------------------------
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
);
-- --------------------------------------------------------
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
);
-- --------------------------------------------------------
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
);
-- --------------------------------------------------------
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
);
-- --------------------------------------------------------
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
);
-- --------------------------------------------------------
