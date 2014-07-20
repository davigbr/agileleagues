ALTER TABLE `player`
ADD COLUMN `credly_access_token`  varchar(200) NULL DEFAULT NULL AFTER `timezone`,
ADD COLUMN `credly_refresh_token`  varchar(200) NULL DEFAULT NULL AFTER `credly_access_token`;

ALTER TABLE `player`
ADD COLUMN `credly_id`  varchar(20) NULL DEFAULT NULL AFTER `timezone`,
ADD COLUMN `credly_email`  varchar(255) NULL DEFAULT NULL AFTER `credly_profile_id`;

ALTER TABLE `badge`
ADD COLUMN `credly_badge_id`  int(10) NULL DEFAULT NULL AFTER `inactive`,
ADD COLUMN `credly_badge_name`  varchar(255) NULL DEFAULT NULL AFTER `credly_badge_id`,
ADD COLUMN `credly_badge_image_url`  varchar(255) NULL DEFAULT NULL AFTER `credly_badge_name`;

ALTER TABLE `badge_log`
ADD COLUMN `credly_given`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `domain_id`;

