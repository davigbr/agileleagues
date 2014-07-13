ALTER TABLE `badge`
ADD COLUMN `inactive`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `player_id_owner`;

ALTER TABLE `domain`
ADD COLUMN `inactive`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `player_id_owner`;

ALTER TABLE `player`
ADD COLUMN `timezone`  varchar(200) NOT NULL DEFAULT 'UTC' AFTER `verified_in`;

