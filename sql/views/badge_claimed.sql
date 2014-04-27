DROP VIEW IF EXISTS badge_claimed;
CREATE VIEW badge_claimed AS
SELECT 
player.id AS player_id, 
badge.id AS badge_id,
badge_log.id IS NOT NULL AS claimed
FROM player
CROSS JOIN badge
LEFT JOIN badge_log ON badge_log.player_id = player.id AND badge_log.badge_id = badge.id;