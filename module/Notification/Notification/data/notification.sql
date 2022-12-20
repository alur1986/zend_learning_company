-- --------------------------------------------------------
--
-- Table structure for table `notification`
--
CREATE TABLE IF NOT EXISTS `notification` (
  `notification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) unsigned NULL DEFAULT NULL COMMENT 'References site.site_id',
  `subject` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `sender_name` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `sender_email` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `active_from` datetime NULL DEFAULT NULL,
  `active_to` datetime NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','active','inactive','expired','deleted') COLLATE utf8_unicode_ci DEFAULT 'new',
  PRIMARY KEY (`notification_id`),
  KEY `FK_NOTIFICATION_SITE_ID` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 COMMENT='Stores the notification data';

-- --------------------------------------------------------
--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_NOTIFICATION_SITE_ID` FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE ON UPDATE CASCADE;