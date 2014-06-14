ALTER TABLE `player`
CHANGE COLUMN `verification_hash` `hash`  char(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `created`;

ALTER TABLE `log`
ADD COLUMN `player_id_pair`  int(10) UNSIGNED NULL DEFAULT NULL AFTER `player_id_owner`;

ALTER TABLE `log` ADD CONSTRAINT `fk_log_player_id_pair` FOREIGN KEY (`player_id_pair`) REFERENCES `player` (`id`);

UPDATE xp_log SET created = NOW() WHERE created = '0000-00-00 00:00:00';

ALTER TABLE `activity`
ADD COLUMN `acceptance_votes`  int(10) UNSIGNED NOT NULL DEFAULT 1 AFTER `player_id_owner`,
ADD COLUMN `rejection_votes`  int(10) UNSIGNED NOT NULL DEFAULT 1 AFTER `approval_votes`;

CREATE TABLE `log_votes` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`log_id` INT(10) UNSIGNED NOT NULL ,
	`vote` SMALLINT(5) UNSIGNED NOT NULL ,
	`player_id` INT(10) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id`),
	CONSTRAINT `fk_log_vote_player_id` FOREIGN KEY (`player_id`) REFERENCES `agileleagues`.`player` (`id`),
	CONSTRAINT `fk_log_vote_log_id` FOREIGN KEY (`log_id`) REFERENCES `agileleagues`.`log` (`id`)
);

ALTER TABLE `log`
ADD COLUMN `accepted`  timestamp NULL DEFAULT NULL,
ADD COLUMN `rejected`  timestamp NULL DEFAULT NULL;

ALTER TABLE `log_votes`
ADD COLUMN `comment`  varchar(250) NOT NULL DEFAULT '' AFTER `player_id`;

ALTER TABLE `log`
ADD COLUMN `acceptance_votes`  smallint(5) UNSIGNED NOT NULL DEFAULT 0 AFTER `rejected`,
ADD COLUMN `rejection_votes`  smallint(5) UNSIGNED NOT NULL DEFAULT 0 AFTER `acceptance_votes`;

ALTER TABLE `log_votes`
MODIFY COLUMN `vote`  smallint(5) NOT NULL AFTER `log_id`;

ALTER TABLE `log_votes`
ADD UNIQUE INDEX `unique` (`log_id`, `player_id`) ;


ALTER TABLE `xp_log`
ADD COLUMN `log_id`  int(10) UNSIGNED NULL DEFAULT NULL AFTER `event_task_id_reviewed`,
ADD COLUMN `log_id_reviewed`  int(10) UNSIGNED NULL DEFAULT NULL AFTER `log_id`;

ALTER TABLE `xp_log` ADD CONSTRAINT `fk_xp_log_log_id` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`);

ALTER TABLE `xp_log` ADD CONSTRAINT `fk_xp_log_log_id_reviewed` FOREIGN KEY (`log_id_reviewed`) REFERENCES `log` (`id`);


DROP VIEW IF EXISTS player_activity_coins;
CREATE VIEW player_activity_coins AS 
select `log`.`player_id` AS `player_id`,
`player`.`name` AS `player_name`,
count(*) AS `coins`,
sum(log.spent) AS `spent`,
count(*) - sum(log.spent) AS `remaining`,
`log`.`activity_id` AS `activity_id`,
`log`.`reviewed` AS `log_reviewed`,
`activity`.`name` AS `activity_name`,
`activity`.`description` AS `activity_description`,
`domain`.`id` AS `domain_id`,
`domain`.`name` AS `domain_name`,
`domain`.`abbr` AS `domain_abbr`,
`domain`.`color` AS `domain_color` 
from (((`log` 
	join `activity` on((`activity`.`id` = `log`.`activity_id`))) 
	join `player` on((`player`.`id` = `log`.`player_id`))) 
	join `domain` on((`domain`.`id` = `activity`.`domain_id`))) 
where `activity`.inactive = 0 AND log.accepted IS NOT NULL
group by `log`.`activity_id`,`log`.`player_id` 
order by `log`.`player_id`,`activity`.`domain_id`, `activity`.`name`

