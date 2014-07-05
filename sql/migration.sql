ALTER TABLE `log`
DROP COLUMN `spent`,
MODIFY COLUMN `event_id`  int(1) UNSIGNED NULL DEFAULT NULL AFTER `xp`;

DROP VIEW IF EXISTS player_activity_coins;

DROP VIEW IF EXISTS player_activity_summary;
CREATE VIEW player_activity_summary AS 
select `log`.`player_id` AS `player_id`,
`player`.`name` AS `player_name`,
count(*) AS `count`,
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
order by `log`.`player_id`,`activity`.`domain_id`, `activity`.`name`;

DROP VIEW IF EXISTS player_total_activity_coins;

DROP VIEW IF EXISTS badge_activity_progress;
CREATE VIEW badge_activity_progress AS 
SELECT player.id AS player_id, badge.id AS badge_id, ar.activity_id AS activity_id,
COUNT(log.id) AS coins_obtained,
ar.count AS coins_required
FROM player
CROSS JOIN badge 
INNER JOIN activity_requisite AS ar ON ar.badge_id = badge.id
LEFT JOIN log ON log.activity_id = ar.activity_id AND player_id = player.id AND accepted IS NOT NULL
GROUP BY player.id, badge_id, activity_id
ORDER BY player.id, badge_id, activity_id;

DROP VIEW IF EXISTS badge_activity_progress;
CREATE VIEW badge_activity_progress AS 
SELECT player.id AS player_id, badge.id AS badge_id, ar.activity_id AS activity_id,
COUNT(log.id) AS activities_completed,
ar.count AS activities_required
FROM player
CROSS JOIN badge 
INNER JOIN activity_requisite AS ar ON ar.badge_id = badge.id
LEFT JOIN log ON log.activity_id = ar.activity_id AND player_id = player.id AND accepted IS NOT NULL
GROUP BY player.id, badge_id, activity_id
ORDER BY player.id, badge_id, activity_id;

CREATE TABLE `tag` (
	`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`name`  varchar(250) NOT NULL ,
	`color`  char(7) NOT NULL ,
	PRIMARY KEY (`id`)
);

CREATE TABLE `log_tag` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`log_id`  int(10) UNSIGNED NOT NULL ,
`tag_id`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
CONSTRAINT `fk_log_tag_log_id` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`),
CONSTRAINT `fk_log_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
)
;

ALTER TABLE `tag`
ADD COLUMN `inactive`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `color`;

ALTER TABLE `tag`
ADD COLUMN `new`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `inactive`;

ALTER TABLE `tag`
MODIFY COLUMN `name`  varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `id`,
ADD COLUMN `description`  varchar(250) NOT NULL AFTER `name`;

ALTER TABLE `log_tag`
ADD INDEX `unique` (`log_id`, `tag_id`) ;

ALTER TABLE `tag`
DROP COLUMN `player_id_owner`,
ADD COLUMN `player_id_owner`  int(10) UNSIGNED NOT NULL AFTER `new`;

ALTER TABLE `tag` ADD CONSTRAINT `fk_tag_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);

ALTER TABLE `tag`
DROP COLUMN `player_id_owner`,
ADD COLUMN `player_id_owner`  int(10) UNSIGNED NOT NULL AFTER `new`;

ALTER TABLE `tag` ADD CONSTRAINT `fk_tag_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);

CREATE TABLE `activity_requisite_tag` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`activity_requisite_id`  int(10) UNSIGNED NOT NULL ,
`tag_id`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
CONSTRAINT `fk_activity_requisite_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`),
CONSTRAINT `fk_activity_requisite_tag_activity_requisite_id` FOREIGN KEY (`activity_requisite_id`) REFERENCES `activity_requisite` (`id`)
)
;

CREATE TABLE `activity_requisite_summary` (
	`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`badge_id`  int(10) UNSIGNED NOT NULL ,
	`player_id`  int(10) UNSIGNED NOT NULL ,
	`activity_requisite_id`  int(10) UNSIGNED NOT NULL ,
	`times`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
	PRIMARY KEY (`id`),
	CONSTRAINT `fk_activity_requisite_summary_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
	CONSTRAINT `fk_activity_requisite_summary_activity_requisite_id` FOREIGN KEY (`activity_requisite_id`) REFERENCES `activity_requisite` (`id`),
	CONSTRAINT `fk_activity_requisite_summary_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`)
);

ALTER TABLE `activity_requisite_summary`
ADD UNIQUE INDEX `unique` (`badge_id`, `player_id`, `activity_requisite_id`) ;

DROP VIEW IF EXISTS badge_activity_progress;
CREATE VIEW badge_activity_progress AS 
SELECT player.id AS player_id, badge.id AS badge_id, ar.activity_id AS activity_id,
COALESCE(SUM(ars.times), 0) AS activities_completed,
COALESCE(ar.count, 0) AS activities_required
FROM player
CROSS JOIN badge 
LEFT JOIN activity_requisite AS ar ON ar.badge_id = badge.id
LEFT JOIN activity_requisite_summary AS ars ON ar.id = ars.activity_requisite_id AND ars.player_id = player.id
GROUP BY player.id, badge_id, activity_id
ORDER BY player.id, badge_id, activity_id;

-- Activities to inactivate

--- Pair testing
--- PHPUnit test
--- JUnit test
--- TDD
--- Unit Bug Trap
--- Integration Bug Trap 
--- Pair Refactoring

-- Change meaning
--- Pair Problem Solving -> Problem Solving

-- CREATE
--- Integration Test

-- 43: Pair testing -> X
-- 40: TDD -> X

-- 45: PHPUnit test -> Unit Test(37) + PHPUnit(14)
-- 44: JUnit test -> Unit Test(37) + JUnit(15)
-- 39: Unit Bug Trap -> Unit Test(37) + Bug Trap(20)
-- 36: Pair Refactoring -> Refactor(23) + Pair(18)

CALL add_tag_to_logs_with_activity(45, 14);
CALL add_tag_to_logs_with_activity(44, 15);
CALL add_tag_to_logs_with_activity(39, 20);
CALL add_tag_to_logs_with_activity(36, 18);

UPDATE log SET activity_id = 37 WHERE activity_id = 45;
UPDATE log SET activity_id = 37 WHERE activity_id = 44;
UPDATE log SET activity_id = 37 WHERE activity_id = 39;
UPDATE log SET activity_id = 23 WHERE activity_id = 36;

-- DO NOT WORRY
-- 64: Integration Bug Trap -> Integration Test + Bug Trap
