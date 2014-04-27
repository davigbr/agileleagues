DROP VIEW IF EXISTS domain_activities_count;
CREATE VIEW domain_activities_count AS
SELECT domain_id, COUNT(*) count 
FROM activity 
WHERE inactive = 0
GROUP BY domain_id
ORDER BY domain_id;
