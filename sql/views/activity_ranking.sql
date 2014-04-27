DROP VIEW IF EXISTS activity_ranking;
CREATE VIEW activity_ranking AS 
SELECT COUNT(*) AS count, player_id, player.name AS player_name 
FROM log
INNER JOIN player ON player.id = player_id
GROUP BY player_id
ORDER BY count DESC
