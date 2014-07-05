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