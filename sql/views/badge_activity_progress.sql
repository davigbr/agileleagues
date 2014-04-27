DROP VIEW IF EXISTS badge_activity_progress;
CREATE VIEW badge_activity_progress AS 
SELECT player.id AS player_id, badge.id AS badge_id, ar.activity_id AS activity_id,
COUNT(log.id) AS coins_obtained,
ar.count AS coins_required
FROM player
CROSS JOIN badge 
INNER JOIN activity_requisite AS ar ON ar.badge_id = badge.id
LEFT JOIN log ON log.activity_id = ar.activity_id AND player_id = player.id AND spent = 0 AND reviewed IS NOT NULL
GROUP BY player.id, badge_id, activity_id
ORDER BY player.id, badge_id, activity_id;
