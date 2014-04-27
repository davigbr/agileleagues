DROP VIEW IF EXISTS activity_leaderboards_last_week;
CREATE VIEW activity_leaderboards_last_week AS SELECT 
COUNT(log.id) AS count, player.id AS player_id, player.name AS player_name
FROM player 
LEFT JOIN log ON player.id = log.player_id AND (
	acquired >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY
	AND acquired < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY
	AND log.reviewed IS NOT NULL
)
GROUP BY player.id
ORDER BY count DESC;
