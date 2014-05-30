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

ALTER TABLE `activity`
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

ALTER TABLE `domain`
ADD COLUMN `player_id_owner`  int(10) UNSIGNED NOT NULL AFTER `created`;

UPDATE domain SET player_id_owner = (SELECT id FROM player WHERE name = 'Davi');

ALTER TABLE `domain` ADD CONSTRAINT `fk_domain_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);


ALTER TABLE `activity`
ADD COLUMN `player_id_owner`  int(10) UNSIGNED NOT NULL;

UPDATE activity SET player_id_owner = (SELECT id FROM player WHERE name = 'Davi');

ALTER TABLE `activity` ADD CONSTRAINT `fk_activity_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);

ALTER TABLE `event`
ADD COLUMN `player_id_owner`  int(10) UNSIGNED NOT NULL;

UPDATE event SET player_id_owner = (SELECT id FROM player WHERE name = 'Davi');

ALTER TABLE `event` ADD CONSTRAINT `event_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);


ALTER TABLE `badge`
ADD COLUMN `player_id_owner`  int(10) UNSIGNED NOT NULL;

UPDATE badge SET player_id_owner = (SELECT id FROM player WHERE name = 'Davi');

ALTER TABLE `badge` ADD CONSTRAINT `badge_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);


ALTER TABLE `log`
ADD COLUMN `player_id_owner`  int(10) UNSIGNED NOT NULL AFTER `event_id`;

UPDATE log SET player_id_owner = (SELECT id FROM player WHERE name = 'Davi');

ALTER TABLE `log` ADD CONSTRAINT `fk_log_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);


DROP VIEW IF EXISTS different_activities_completed;
CREATE VIEW different_activities_completed AS SELECT 
COUNT(DISTINCT(activity_id)) AS different_activities_completed,
log.domain_id AS domain_id,
domain.name AS domain_name,
player_id,
player.name AS player_name,
log.player_id_owner AS player_id_owner
FROM log
INNER JOIN player ON player.id = log.player_id
INNER JOIN activity ON activity.id = log.activity_id
INNER JOIN domain ON domain.id = activity.domain_id
WHERE reviewed IS NOT NULL AND activity.inactive = 0
GROUP BY player_id, domain_id
ORDER BY player_id, domain_id;


DROP VIEW IF EXISTS domain_activities_count;
CREATE VIEW domain_activities_count AS
SELECT domain_id, player_id_owner, COUNT(*) count 
FROM activity 
WHERE inactive = 0
GROUP BY domain_id, player_id_owner
ORDER BY domain_id;

DROP VIEW IF EXISTS activity_leaderboards;
CREATE VIEW activity_leaderboards AS SELECT 
COUNT(*) AS count, player_id_owner, player_id, player.name AS player_name
FROM log 
INNER JOIN player ON player.id = log.player_id
WHERE log.reviewed IS NOT NULL
GROUP BY player_id, player_id_owner
ORDER BY count DESC;

DROP VIEW IF EXISTS activity_leaderboards_last_month;
CREATE VIEW activity_leaderboards_last_month AS SELECT 
COUNT(log.id) AS count, player_id_owner, player.id AS player_id, player.name AS player_name
FROM player
LEFT JOIN log ON player.id = log.player_id AND (
	acquired >= curdate() - INTERVAL (DAYOFMONTH(curdate()) + DAYOFMONTH(LAST_DAY(curdate())) - 1) DAY
	AND acquired < curdate() - INTERVAL (DAYOFMONTH(curdate())-1) DAY
	AND log.reviewed IS NOT NULL
)
GROUP BY player.id, player_id_owner
ORDER BY count DESC;

DROP VIEW IF EXISTS activity_leaderboards_last_week;
CREATE VIEW activity_leaderboards_last_week AS SELECT 
COUNT(log.id) AS count, player_id_owner, player.id AS player_id, player.name AS player_name
FROM player 
LEFT JOIN log ON player.id = log.player_id AND (
	acquired >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY
	AND acquired < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY
	AND log.reviewed IS NOT NULL
)
GROUP BY player.id, player_id_owner
ORDER BY count DESC;

DROP VIEW IF EXISTS activity_leaderboards_this_month;
CREATE VIEW activity_leaderboards_this_month AS SELECT 
COUNT(log.id) AS count, player_id_owner, player.id AS player_id, player.name AS player_name
FROM player 
LEFT JOIN log ON player.id = log.player_id AND (
	acquired >= (curdate() - INTERVAL (DAYOFMONTH(curdate()) - 1) DAY)
	AND acquired <= curdate()
	AND log.reviewed IS NOT NULL
)
GROUP BY player.id, player_id_owner
ORDER BY count DESC;

DROP VIEW IF EXISTS activity_leaderboards_this_week;
CREATE VIEW activity_leaderboards_this_week AS SELECT 
COUNT(log.id) AS count, player_id_owner, player.id AS player_id, player.name AS player_name
FROM player 
LEFT JOIN log ON player.id = log.player_id AND (
	acquired >= curdate() - INTERVAL DAYOFWEEK(curdate()) - 1 DAY
	AND acquired <= curdate()
	AND log.reviewed IS NOT NULL
)
GROUP BY player.id, player_id_owner
ORDER BY count DESC;


