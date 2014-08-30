DROP VIEW IF EXISTS badge_activity_progress;
CREATE VIEW badge_activity_progress AS 
SELECT player.id AS player_id, badge.id AS badge_id, ar.id AS activity_requisite_id, ar.activity_id as activity_id,
COALESCE((
	SELECT SUM(ars.times) FROM activity_requisite_summary AS ars
	WHERE ars.activity_requisite_id = ar.id
	AND ars.player_id = player.id
), 0) AS activities_completed,
COALESCE(ar.count, 0) AS activities_required
FROM player
CROSS JOIN badge 
LEFT JOIN activity_requisite AS ar ON ar.badge_id = badge.id
GROUP BY player.id, badge_id, activity_requisite_id
ORDER BY player.id, badge_id, activity_requisite_id;