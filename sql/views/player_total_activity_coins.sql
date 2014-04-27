DROP VIEW IF EXISTS player_total_activity_coins;
CREATE VIEW player_total_activity_coins AS
SELECT player_id, COUNT(*) AS coins FROM log GROUP BY player_id;