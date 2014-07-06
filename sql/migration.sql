ALTER TABLE `log`
ADD COLUMN `hash`  char(64) NULL DEFAULT NULL AFTER `rejection_votes`,
ADD UNIQUE INDEX `idx_hash` (`hash`) USING HASH ;

ALTER TABLE `badge_log`
ADD COLUMN `domain_id`  int(10) UNSIGNED NOT NULL AFTER `creation`;

UPDATE badge_log SET domain_id = (SELECT domain_id FROM badge WHERE badge_log.badge_id = badge.id);

ALTER TABLE `badge_log` ADD CONSTRAINT `fk_badge_log_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`);

