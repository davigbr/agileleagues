DROP TABLE event_join_log;
DROP VIEW event_activity_progress;
DROP TABLE event_task_log;
DROP TABLE event_complete_log;
DROP TABLE event_activity;

ALTER TABLE log DROP FOREIGN KEY fk_log_event_id;
ALTER TABLE log DROP COLUMN event_id;

DELETE FROM xp_log WHERE event_task_id IS NOT NULL;
DELETE FROM xp_log WHERE event_task_id_reviewed IS NOT NULL;
DELETE FROM xp_log WHERE event_id_completed IS NOT NULL;
	
ALTER TABLE xp_log DROP FOREIGN KEY fk_xp_log_event_task_id;
ALTER TABLE xp_log DROP COLUMN event_task_id;

ALTER TABLE xp_log DROP FOREIGN KEY fk_xp_log_event_task_id_reviewed;
ALTER TABLE xp_log DROP COLUMN event_task_id_reviewed;

ALTER TABLE xp_log DROP FOREIGN KEY fk_xp_log_event_id_completed;
ALTER TABLE xp_log DROP COLUMN event_id_completed;

DROP TABLE event_task;
DROP TABLE event;
DROP TABLE event_type;

ALTER TABLE player ADD COLUMN last_login DATETIME NULL DEFAULT NULL;

ALTER TABLE xp_log DROP FOREIGN KEY fk_xp_log_event_id_joined;
ALTER TABLE xp_log DROP COLUMN event_id_joined;

ALTER TABLE xp_log DROP FOREIGN KEY fk_xp_log_activity_id;
ALTER TABLE xp_log DROP COLUMN activity_id;

ALTER TABLE xp_log DROP FOREIGN KEY fk_xp_log_activity_id_reviewed;
ALTER TABLE xp_log DROP COLUMN activity_id_reviewed;

ALTER TABLE activity 
ADD COLUMN daily_limit SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0;
