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