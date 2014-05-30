DROP VIEW IF EXISTS domain_activities_count;
CREATE VIEW domain_activities_count AS
SELECT domain_id, player_id_owner, COUNT(*) count 
FROM activity 
WHERE inactive = 0
GROUP BY domain_id, player_id_owner
ORDER BY domain_id;
