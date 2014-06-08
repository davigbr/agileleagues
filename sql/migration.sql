ALTER TABLE `player`
CHANGE COLUMN `verification_hash` `hash`  char(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `created`;

