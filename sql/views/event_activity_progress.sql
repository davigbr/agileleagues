DROP VIEW IF EXISTS event_activity_progress;

CREATE VIEW event_activity_progress AS 
SELECT 
player_id, 
log.event_id AS event_id, 
log.activity_id AS activity_id, 
count(*) AS times_obtained,
event_activity.count AS times_required,
FLOOR(count(*) / event_activity.count * 100) AS progress
FROM log
LEFT JOIN event_activity 
ON event_activity.event_id = log.event_id 
AND event_activity.activity_id = log.activity_id
WHERE log.reviewed IS NOT NULL AND log.event_id IS NOT NULL
GROUP BY player_id, log.event_id, log.activity_id

