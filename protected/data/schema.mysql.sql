-- --------------------------------------------------------
-- Server version:               5.5.20 - MySQL Community Server (GPL)
-- Date/time:                    2012-06-04 13:53:26
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table stories.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `email` varchar(128) NOT NULL,
  `status_id` tinyint(3) unsigned NOT NULL,
  `salt` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `activkey` varchar(32) DEFAULT NULL,
  `login_failures` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `locked_until` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `lastlogin_time` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status_id` (`status_id`),
  KEY `is_admin` (`is_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `email`, `status_id`, `salt`, `password`, `activkey`, `login_failures`, `locked_until`, `create_time`, `lastlogin_time`, `is_admin`) VALUES
	(1, 'admin', 'pdal@assetrecoverycorp.com', 9, '', '21232f297a57a5a743894a0e4a801fc3', NULL, 0, NULL, '2012-05-21 10:36:16', '2012-06-04 10:05:02', 1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Dumping structure for table stories.story
CREATE TABLE IF NOT EXISTS `story` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `content_html` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `read_count` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_count_1` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_count_2` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_count_3` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_count_4` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_count_5` int(10) unsigned NOT NULL DEFAULT '0',
  `reported_user` int(10) unsigned DEFAULT NULL,
  `reported_time` timestamp NULL DEFAULT NULL,
  `reported_note` text COLLATE utf8_unicode_ci,
  `reviewed_user` int(10) unsigned DEFAULT NULL,
  `reviewed_note` text COLLATE utf8_unicode_ci,
  `reviewed_time` timestamp NULL DEFAULT NULL,
  `create_user` int(10) unsigned DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_user` int(10) unsigned DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `date_created` (`create_time`),
  KEY `author_id` (`author_id`),
  KEY `status_id` (`is_published`),
  KEY `read_count` (`read_count`),
  KEY `reported_by_id` (`reported_user`),
  KEY `reviewed_by_id` (`reviewed_user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `story` DISABLE KEYS */;
INSERT INTO `story` (`id`, `author_id`, `title`, `description`, `content`, `content_html`, `tags`, `is_published`, `read_count`, `rating_count_1`, `rating_count_2`, `rating_count_3`, `rating_count_4`, `rating_count_5`, `reported_user`, `reported_time`, `reported_note`, `reviewed_user`, `reviewed_note`, `reviewed_time`, `create_user`, `create_time`, `update_user`, `update_time`) VALUES
	(1, 1, 'test', 'Testing', 'This a test story', '<p>This a test story</p>', 'MF', 1, 85, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2012-05-18 09:41:55', 1, '2012-05-18 09:41:55');
/*!40000 ALTER TABLE `story` ENABLE KEYS */;

-- Dumping structure for table stories.comment
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `story_id` int(10) unsigned NOT NULL DEFAULT '0',
  `author_id` int(10) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `is_published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reported_user` int(10) unsigned DEFAULT NULL,
  `reported_time` timestamp NULL DEFAULT NULL,
  `reported_note` text,
  `reviewed_user` int(10) unsigned DEFAULT NULL,
  `reviewed_note` text,
  `reviewed_time` timestamp NULL DEFAULT NULL,
  `create_user` int(10) unsigned DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_user` int(10) unsigned DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `story_id` (`story_id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` (`id`, `story_id`, `author_id`, `content`, `is_published`, `reported_user`, `reported_time`, `reported_note`, `reviewed_user`, `reviewed_note`, `reviewed_time`, `create_user`, `create_time`, `update_user`, `update_time`) VALUES
	(1, 1, 1, 'This is a test comment!', 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2012-06-04 13:52:46', 1, '2012-06-04 13:52:46');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
