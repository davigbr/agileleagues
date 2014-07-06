ALTER TABLE `log`
ADD COLUMN `hash`  char(64) NULL DEFAULT NULL AFTER `rejection_votes`,
ADD UNIQUE INDEX `idx_hash` (`hash`) USING HASH ;

ALTER TABLE `badge_log`
ADD COLUMN `domain_id`  int(10) UNSIGNED NOT NULL AFTER `creation`;

UPDATE badge_log SET domain_id = (SELECT domain_id FROM badge WHERE badge_log.badge_id = badge.id);

ALTER TABLE `badge_log` ADD CONSTRAINT `fk_badge_log_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`);

DROP VIEW IF EXISTS activity_leaderboards_last_month;
CREATE VIEW activity_leaderboards_last_month AS SELECT 
COUNT(log.id) AS count, player_id_owner, player.id AS player_id, player.name AS player_name
FROM player
LEFT JOIN log ON player.id = log.player_id AND (
	acquired >= curdate() - INTERVAL (DAYOFMONTH(curdate()) + DAYOFMONTH(LAST_DAY(curdate())) - 1) DAY
	AND acquired < curdate() - INTERVAL (DAYOFMONTH(curdate())-1) DAY
	AND log.accepted IS NOT NULL
)
GROUP BY player.id, player_id_owner
ORDER BY count DESC;
DROP VIEW IF EXISTS activity_leaderboards;
CREATE VIEW activity_leaderboards AS SELECT 
COUNT(*) AS count, player_id_owner, player_id, player.name AS player_name
FROM log 
INNER JOIN player ON player.id = log.player_id
WHERE log.accepted IS NOT NULL
GROUP BY player_id, player_id_owner
ORDER BY count DESC;
DROP VIEW IF EXISTS activity_leaderboards_this_week;
CREATE VIEW activity_leaderboards_this_week AS SELECT 
COUNT(log.id) AS count, player_id_owner, player.id AS player_id, player.name AS player_name
FROM player 
LEFT JOIN log ON player.id = log.player_id AND (
	acquired >= curdate() - INTERVAL DAYOFWEEK(curdate()) - 1 DAY
	AND acquired <= curdate()
	AND log.accepted IS NOT NULL
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
	AND log.accepted IS NOT NULL
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
	AND log.accepted IS NOT NULL
)
GROUP BY player.id, player_id_owner
ORDER BY count DESC;
