CREATE TABLE IF NOT EXISTS `gb_assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_object` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `asset_revision` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`asset_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `gb_documents`
--

CREATE TABLE IF NOT EXISTS `gb_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_owner` int(11) NOT NULL DEFAULT '0',
  `doc_name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `doc_desc` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `doc_filename` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `doc_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `doc_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `gb_globes`
--

CREATE TABLE IF NOT EXISTS `gb_globes` (
  `globe_id` int(11) NOT NULL AUTO_INCREMENT,
  `globe_name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `globe_desc` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `globe_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `globe_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `globe_owner` int(11) NOT NULL,
  PRIMARY KEY (`globe_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `gb_groups`
--

CREATE TABLE IF NOT EXISTS `gb_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_owner` int(11) NOT NULL DEFAULT '0',
  `group_name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `group_desc` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `group_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `gb_sessions`
--

CREATE TABLE IF NOT EXISTS `gb_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_token` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `session_activity` int(11) NOT NULL DEFAULT '0',
  `session_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `gb_users`
--

CREATE TABLE IF NOT EXISTS `gb_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `user_password` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_super` int(1) NOT NULL DEFAULT '0',
  `user_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
);