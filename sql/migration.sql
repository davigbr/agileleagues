CREATE TABLE `team` (
	`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`name`  varchar(30) NOT NULL ,
	`player_id_scrummaster`  int(10) UNSIGNED NULL DEFAULT NULL ,
	`player_id_product_owner`  int(10) UNSIGNED NULL DEFAULT NULL ,
	PRIMARY KEY (`id`),
	CONSTRAINT `fk_team_player_id_scrummaster` FOREIGN KEY (`player_id_scrummaster`) REFERENCES `player` (`id`),
	CONSTRAINT `fk_team_player_id_product_owner` FOREIGN KEY (`player_id_product_owner`) REFERENCES `player` (`id`)
);

ALTER TABLE `player`
ADD COLUMN `team_id`  int(10) UNSIGNED NULL DEFAULT NULL AFTER `xp`;

ALTER TABLE `player` ADD CONSTRAINT `fk_player_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`);

ALTER TABLE `domain`
ADD COLUMN `player_type_id`  int(10) UNSIGNED NULL DEFAULT NULL AFTER `icon`;

ALTER TABLE `domain` ADD CONSTRAINT `fk_domain_player_type_id` FOREIGN KEY (`player_type_id`) REFERENCES `player_type` (`id`);

UPDATE domain SET player_type_id = 1;


ALTER TABLE `player`
ADD COLUMN `created`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `team_id`,
ADD COLUMN `verification_hash`  char(64) NULL DEFAULT NULL AFTER `created`;

ALTER TABLE `domain`
ADD COLUMN `created`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `player_type_id`;

ALTER TABLE `badge`
ADD COLUMN `created`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `icon`;

ALTER TABLE `event`
ADD COLUMN `created`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `xp`;

ALTER TABLE `player`
ADD COLUMN `verified_in`  timestamp NULL AFTER `verification_hash`;

UPDATE player SET verified_in = NOW();

ALTER TABLE `player`
ADD UNIQUE INDEX `idx_verification_hash` (`verification_hash`) USING HASH ;

ALTER TABLE `team` DROP FOREIGN KEY `fk_team_player_id_product_owner`;

ALTER TABLE `team`
DROP COLUMN `player_id_product_owner`;

ALTER TABLE `notification`
ADD COLUMN `player_id_sender`  int(10) UNSIGNED NULL DEFAULT NULL AFTER `action`;

ALTER TABLE `notification`
DROP INDEX `fk_notification_player_id` ,
ADD INDEX `fk_notification_player_id` (`player_id`) USING HASH ,
ADD INDEX `fk_notification_player_id_sender` (`player_id_sender`) USING HASH ;

