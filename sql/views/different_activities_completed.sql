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
ORDER BY player_id, domain_id