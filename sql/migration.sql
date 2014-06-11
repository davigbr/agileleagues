ALTER TABLE `player`
CHANGE COLUMN `verification_hash` `hash`  char(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `created`;

ALTER TABLE `log`
ADD COLUMN `player_id_pair`  int(10) UNSIGNED NULL DEFAULT NULL AFTER `player_id_owner`;

ALTER TABLE `log` ADD CONSTRAINT `fk_log_player_id_pair` FOREIGN KEY (`player_id_pair`) REFERENCES `player` (`id`);

UPDATE xp_log SET created = NOW() WHERE created = '0000-00-00 00:00:00';
