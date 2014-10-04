ALTER TABLE activity ADD COLUMN first_report DATETIME NULL DEFAULT NULL;
ALTER TABLE activity ADD COLUMN last_report DATETIME NULL DEFAULT NULL;
ALTER TABLE activity ADD COLUMN times_reported INT(10) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE activity ADD COLUMN reports_per_day DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0;

UPDATE activity SET first_report = (SELECT created FROM log WHERE activity_id = activity.id ORDER BY created ASC LIMIT 1);
UPDATE activity SET last_report = (SELECT created FROM log WHERE activity_id = activity.id ORDER BY created DESC LIMIT 1);

UPDATE activity SET times_reported = (SELECT COUNT(*) FROM log WHERE activity_id = activity.id);
UPDATE activity SET reports_per_day = 
	(SELECT COUNT(*) FROM log WHERE activity_id = activity.id) / 
	(1 + DATEDIFF(NOW(), created));


SELECT created, 
	(1 + DATEDIFF(NOW(), created)), 
	(SELECT COUNT(*) FROM log WHERE activity_id = activity.id) 
FROM activity;



