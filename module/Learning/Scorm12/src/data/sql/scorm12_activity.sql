CREATE TABLE IF NOT EXISTS `scorm12_activity` (
  `activity_id` int(11) unsigned NOT NULL COMMENT 'References learning_activity.activity_id',
  `allowed_attempts` int(11) DEFAULT '10',
  `allow_browse` enum('1','0') NOT NULL DEFAULT '0',
  `allow_review_on_completion` enum('1','0') NOT NULL DEFAULT '1',
  `allow_review_on_fail` enum('1','0') NOT NULL DEFAULT '1',
  `allow_tracking_override_after_completion` enum('1','0') NOT NULL DEFAULT '0',
  `window_scrollable` enum('1','0') NOT NULL DEFAULT '0',
  `window_width` int(11) unsigned NOT NULL DEFAULT '1012',
  `window_height` int(11) unsigned NOT NULL DEFAULT '688',
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE  `scorm12_activity` ADD FOREIGN KEY (  `activity_id` ) REFERENCES  `learning_activity` ( `activity_id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE  `scorm12_activity` ADD  `allow_tracking_override_after_completion` ENUM(  '1',  '0' ) NOT NULL DEFAULT  '0' AFTER  `allow_review_on_fail`;