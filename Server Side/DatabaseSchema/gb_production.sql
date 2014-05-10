-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2014 at 08:15 PM
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
-- Table structure for table `gb_sessions`
--

CREATE TABLE IF NOT EXISTS `gb_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_token` varchar(128) NOT NULL DEFAULT '1',
  `session_activity` int(11) NOT NULL DEFAULT '0',
  `session_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `gb_sessions`
--

INSERT INTO `gb_sessions` (`session_id`, `session_token`, `session_activity`, `session_create`) VALUES
(1, '1BCA1B02E41E909A945FE8B92E294678AC9E65F2', 1, '2014-05-09 17:19:04'),
(2, 'abcdefghijklmnopqrstuvwxyz', 1, '2014-05-09 17:20:25'),
(3, '03f9774e1ddf815db125abcbbfc34d8805ea2a40', 1, '2014-05-09 17:21:36'),
(4, '83d27bb250cace77612c43a35c7d885214c5ef85', 1, '2014-05-09 17:21:52'),
(5, '9f914edc0d117dd4e0645a5d1f155b6768550cd8', 1, '2014-05-09 17:23:10'),
(6, '1be334862b677d9ee7ed0fdf151f6861c49f7385', 1, '2014-05-09 17:30:05'),
(7, '6347941255101c96ccf5b74fc59f5903b9f0940c', 1, '2014-05-09 17:32:24'),
(8, 'e72a3bf925d5a96f81a5d559bcdc5aaa32a2483e', 1, '2014-05-09 17:39:12'),
(9, '0a6462ee2599c921ca93a90b044e91999c5c9179', 1, '2014-05-09 17:42:54'),
(10, '0', 0, '2014-05-09 17:45:04'),
(11, '0', 0, '2014-05-09 17:46:34'),
(12, '0', 0, '2014-05-09 17:47:09'),
(13, '0', 0, '2014-05-09 17:50:53'),
(14, '0', 0, '2014-05-09 17:53:15'),
(15, '0', 0, '2014-05-09 17:53:36'),
(16, '0', 0, '2014-05-09 17:54:32');

-- --------------------------------------------------------

--
-- Table structure for table `gb_users`
--

CREATE TABLE IF NOT EXISTS `gb_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL DEFAULT '1',
  `user_password` varchar(64) NOT NULL DEFAULT '0',
  `user_super` int(1) NOT NULL DEFAULT '0',
  `user_create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `gb_users`
--

INSERT INTO `gb_users` (`user_id`, `user_name`, `user_password`, `user_super`, `user_create`) VALUES
(1, 'AJQShake', 'fb976ba971364bda70569a3d3fd8b02197603507', 1, '2014-05-09 16:52:42');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


CREATE TABLE IF NOT EXISTS 
gb_sessions (
  session_id int(11) NOT NULL AUTO_INCREMENT, 
  session_token CHAR(64) NOT NULL DEFAULT '1',
  session_activity int(11) NOT NULL DEFAULT '0',
  session_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (session_id)
)

CREATE TABLE IF NOT EXISTS 
gb_users (
  user_id int(11) NOT NULL AUTO_INCREMENT, 
  user_name VARCHAR(64) NOT NULL DEFAULT '1',
  user_password VARCHAR(64) NOT NULL DEFAULT '0',
  user_super int(1)NOT NULL DEFAULT '0',
  user_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id)
)

CREATE TABLE IF NOT EXISTS 
gb_globes (
  globe_id int(11) NOT NULL AUTO_INCREMENT,
  globe_name varchar(120) NOT NULL,
  globe_desc varchar(250) DEFAULT NULL,
  globe_code varchar(10) DEFAULT NULL,
  globe_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  globe_owner int(11) NOT NULL,
  PRIMARY KEY (globe_id)
)


CREATE TABLE IF NOT EXISTS 
gb_assets (
  asset_id int(11) NOT NULL AUTO_INCREMENT,
  asset_object varchar(120) NOT NULL,
  asset_revision varchar(120) NOT NULL DEFAULT '0',
  PRIMARY KEY (asset_id)
)