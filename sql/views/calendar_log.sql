DROP VIEW IF EXISTS calendar_log;
CREATE VIEW calendar_log AS 
SELECT COUNT(*) AS coins, player_id, acquired, domain_id, activity_id FROM log
GROUP BY activity_id, player_id, acquired
ORDER BY acquired, player_id;