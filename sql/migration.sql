ALTER TABLE `activity_requisite_summary`
ADD COLUMN `player_id_owner`  int(10) UNSIGNED NULL AFTER `times`;

ALTER TABLE `activity_requisite_summary` ADD CONSTRAINT `fk_activity_requisite_summary_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);

UPDATE activity_requisite_summary SET player_id_owner = (SELECT player_id_owner FROM badge WHERE badge.id = badge_id);