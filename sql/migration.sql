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