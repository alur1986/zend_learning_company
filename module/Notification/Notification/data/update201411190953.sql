SET FOREIGN_KEY_CHECKS = 0;
UPDATE `notification` SET `site_id`= NULL;
ALTER TABLE `notification` DROP FOREIGN KEY `FK_NOTIFICATION_SITE_ID`;
ALTER TABLE `notification` DROP INDEX `FK_NOTIFICATION_SITE_ID`;
ALTER TABLE `notification` DROP `site_id`;
ALTER TABLE  `notification` CHANGE  `status`  `status` ENUM(  'new',  'active',  'inactive',  'expired',  'deleted' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT  'new'
SET FOREIGN_KEY_CHECKS = 1;