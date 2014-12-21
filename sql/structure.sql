SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS `agileleagues` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `agileleagues`;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `add_tag_to_logs_with_activity`(
	_activity_id INT,
	_tag_id INT
)
    MODIFIES SQL DATA
BEGIN 
		
	DECLARE _id INT UNSIGNED DEFAULT NULL;
	DECLARE _done INT DEFAULT FALSE;
	DECLARE _cursor CURSOR FOR 
		SELECT id FROM log WHERE log.activity_id = _activity_id;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = TRUE;

	OPEN _cursor;

	  	read_loop: LOOP
	    	FETCH _cursor INTO _id;
	    
	    IF _done THEN
	    	LEAVE read_loop;
	    END IF;
	   
	    INSERT INTO log_tag SET log_id = _id, tag_id = _tag_id;

		END LOOP;

	CLOSE _cursor;


END$$

CREATE DEFINER=CURRENT_USER FUNCTION `player_level`(
	_xp INT(10) UNSIGNED
) RETURNS int(10) unsigned
    NO SQL
BEGIN

	RETURN FLOOR(1 + 0.0464159 * POW(_xp, 2/3));

END$$

DELIMITER ;

CREATE TABLE `access_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plugin` varchar(10) DEFAULT NULL,
  `controller` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `params` text,
  `post` text,
  `get` text,
  `player_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ARCHIVE  DEFAULT CHARSET=latin1;

CREATE TABLE `activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `description` varchar(200) NOT NULL,
  `inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `new` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `xp` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reported` int(10) unsigned NOT NULL DEFAULT '0',
  `player_id_owner` int(10) unsigned NOT NULL,
  `acceptance_votes` int(10) unsigned NOT NULL DEFAULT '1',
  `rejection_votes` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `activity_domain_id` (`domain_id`),
  KEY `activity_reported` (`reported`) USING BTREE,
  KEY `fk_activity_player_id_owner` (`player_id_owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
CREATE TABLE `activity_leaderboards` (
`count` bigint(21)
,`player_id_owner` int(10) unsigned
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);CREATE TABLE `activity_leaderboards_last_month` (
`count` bigint(21)
,`player_id_owner` int(10) unsigned
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);CREATE TABLE `activity_leaderboards_last_week` (
`count` bigint(21)
,`player_id_owner` int(10) unsigned
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);CREATE TABLE `activity_leaderboards_this_month` (
`count` bigint(21)
,`player_id_owner` int(10) unsigned
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);CREATE TABLE `activity_leaderboards_this_week` (
`count` bigint(21)
,`player_id_owner` int(10) unsigned
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);CREATE TABLE `activity_ranking` (
`count` bigint(21)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);
CREATE TABLE `activity_requisite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `prerequisite_badge_id` (`badge_id`),
  KEY `prerequisite_activity_id` (`activity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `activity_requisite_summary` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `activity_requisite_id` int(10) unsigned NOT NULL,
  `times` int(10) unsigned NOT NULL DEFAULT '0',
  `player_id_owner` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`badge_id`,`player_id`,`activity_requisite_id`),
  KEY `fk_activity_requisite_summary_activity_requisite_id` (`activity_requisite_id`),
  KEY `fk_activity_requisite_summary_player_id` (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `activity_requisite_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_requisite_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_requisite_tag_tag_id` (`tag_id`),
  KEY `fk_activity_requisite_tag_activity_requisite_id` (`activity_requisite_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `badge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `new` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `icon` varchar(50) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `player_id_owner` int(10) unsigned NOT NULL,
  `inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `credly_badge_id` int(10) DEFAULT NULL,
  `credly_badge_name` varchar(255) DEFAULT NULL,
  `credly_badge_image_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `badge_domain_id` (`domain_id`),
  KEY `badge_player_id_owner` (`player_id_owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
CREATE TABLE `badge_activity_progress` (
`player_id` int(10) unsigned
,`badge_id` int(10) unsigned
,`activity_id` int(10) unsigned
,`activities_completed` decimal(32,0)
,`activities_required` decimal(5,0)
);CREATE TABLE `badge_claimed` (
`player_id` int(10) unsigned
,`badge_id` int(10) unsigned
,`claimed` int(1)
);
CREATE TABLE `badge_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `domain_id` int(10) unsigned NOT NULL,
  `credly_given` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`badge_id`,`player_id`) USING HASH,
  KEY `fk_badge_log_player_id` (`player_id`),
  KEY `fk_badge_log_domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `badge_requisite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `badge_id_requisite` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `badge_requisite_badge_id` (`badge_id`),
  KEY `badge_requisite_badge_id_requisite` (`badge_id_requisite`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
CREATE TABLE `calendar_log` (
`coins` bigint(21)
,`player_id` int(10) unsigned
,`acquired` date
,`domain_id` int(10) unsigned
,`activity_id` int(10) unsigned
);
CREATE TABLE `configuration` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(30) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_idx` (`key`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `different_activities_completed` (
`different_activities_completed` bigint(21)
,`domain_id` int(10) unsigned
,`domain_name` varchar(30)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
,`player_id_owner` int(10) unsigned
);
CREATE TABLE `domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `color` char(7) NOT NULL,
  `abbr` char(3) NOT NULL,
  `description` varchar(200) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `player_type_id` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `player_id_owner` int(10) unsigned NOT NULL,
  `inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_domain_player_type_id` (`player_type_id`),
  KEY `fk_domain_player_id_owner` (`player_id_owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
CREATE TABLE `domain_activities_count` (
`domain_id` int(10) unsigned
,`player_id_owner` int(10) unsigned
,`count` bigint(21)
);
CREATE TABLE `event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_type_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `description` varchar(200) NOT NULL,
  `xp` smallint(5) unsigned NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `player_id_owner` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_event_event_type_id` (`event_type_id`),
  KEY `event_player_id_owner` (`player_id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_event_activity_event_id` (`event_id`),
  KEY `fk_event_activity_activity_id` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `event_activity_progress` (
`player_id` int(10) unsigned
,`event_id` int(1) unsigned
,`activity_id` int(10) unsigned
,`times_obtained` bigint(21)
,`times_required` smallint(5) unsigned
,`progress` decimal(16,0)
);
CREATE TABLE `event_complete_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`event_id`,`player_id`),
  KEY `fk_event_completed_log_player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_join_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`event_id`,`player_id`),
  KEY `fk_event_join_log_player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  `xp` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_event_task_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_task_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `event_task_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`event_task_id`,`player_id`),
  KEY `fk_event_task_log_player_id` (`player_id`),
  KEY `fk_event_task_log_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `level_required` smallint(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
CREATE TABLE `last_week_logs` (
`activity_id` int(10) unsigned
,`logs` varbinary(153)
);
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `player_id` int(10) unsigned NOT NULL,
  `acquired` date NOT NULL,
  `reviewed` timestamp NULL DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `domain_id` int(10) unsigned DEFAULT NULL,
  `xp` int(10) unsigned NOT NULL DEFAULT '0',
  `event_id` int(1) unsigned DEFAULT NULL,
  `player_id_owner` int(10) unsigned NOT NULL,
  `player_id_pair` int(10) unsigned DEFAULT NULL,
  `accepted` timestamp NULL DEFAULT NULL,
  `rejected` timestamp NULL DEFAULT NULL,
  `acceptance_votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rejection_votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hash` char(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_hash` (`hash`) USING HASH,
  KEY `fk_log_event_id` (`event_id`),
  KEY `fk_activity_activity_id` (`activity_id`) USING BTREE,
  KEY `fk_log_player_idx` (`player_id`) USING BTREE,
  KEY `fk_log_domain_idx` (`domain_id`) USING BTREE,
  KEY `fk_log_player_id_owner` (`player_id_owner`),
  KEY `fk_log_player_id_pair` (`player_id_pair`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `log_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_tag_tag_id` (`tag_id`),
  KEY `unique` (`log_id`,`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `log_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(10) unsigned NOT NULL,
  `vote` smallint(5) NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `comment` varchar(250) NOT NULL DEFAULT '',
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`log_id`,`player_id`),
  KEY `fk_log_vote_player_id` (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(200) NOT NULL,
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(30) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'success',
  `action` varchar(10) DEFAULT NULL,
  `player_id_sender` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notification_player_id` (`player_id`) USING HASH,
  KEY `fk_notification_player_id_sender` (`player_id_sender`) USING HASH
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `player_type_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(40) NOT NULL,
  `xp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `team_id` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hash` char(64) DEFAULT NULL,
  `verified_in` timestamp NULL DEFAULT NULL,
  `timezone` varchar(200) NOT NULL DEFAULT 'UTC',
  `credly_id` varchar(20) DEFAULT NULL,
  `credly_email` varchar(255) DEFAULT NULL,
  `credly_access_token` varchar(200) DEFAULT NULL,
  `credly_refresh_token` varchar(200) DEFAULT NULL,
  `last_login DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_verification_hash` (`hash`) USING HASH,
  KEY `fk_player_type_id` (`player_type_id`),
  KEY `fk_player_team_id` (`team_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
CREATE TABLE `player_activity_summary` (
`player_id` int(10) unsigned
,`player_name` varchar(30)
,`count` bigint(21)
,`activity_id` int(10) unsigned
,`log_reviewed` timestamp
,`activity_name` varchar(30)
,`activity_description` varchar(200)
,`domain_id` int(10) unsigned
,`domain_name` varchar(30)
,`domain_abbr` char(3)
,`domain_color` char(7)
);
CREATE TABLE `player_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` varchar(250) NOT NULL,
  `color` char(7) NOT NULL,
  `inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `new` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `player_id_owner` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tag_player_id_owner` (`player_id_owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `player_id_owner` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_team_player_id_scrummaster` (`player_id_owner`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `timeline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `what` varchar(30) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `badge_id` int(10) unsigned DEFAULT NULL,
  `activity_id` int(10) unsigned DEFAULT NULL,
  `domain_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_timeline_player_id` (`player_id`),
  KEY `fk_timeline_activity_id` (`activity_id`),
  KEY `fk_timeline_badge_id` (`badge_id`),
  KEY `fk_timeline_domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL,
  `min_level` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `min_level` (`min_level`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `xp_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `xp` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activity_id` int(10) unsigned DEFAULT NULL,
  `activity_id_reviewed` int(10) unsigned DEFAULT NULL,
  `event_id_joined` int(10) unsigned DEFAULT NULL,
  `event_id_completed` int(10) unsigned DEFAULT NULL,
  `event_task_id` int(10) unsigned DEFAULT NULL,
  `event_task_id_reviewed` int(10) unsigned DEFAULT NULL,
  `log_id` int(10) unsigned DEFAULT NULL,
  `log_id_reviewed` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_xp_log_activity_id` (`activity_id`),
  KEY `fk_xp_log_activity_id_reviewed` (`activity_id_reviewed`),
  KEY `fk_xp_log_event_id_completed` (`event_id_completed`),
  KEY `fk_xp_log_event_task_id` (`event_task_id`),
  KEY `fk_xp_log_event_task_id_reviewed` (`event_task_id_reviewed`),
  KEY `fk_xp_log_player_id` (`player_id`),
  KEY `fk_xp_log_log_id` (`log_id`),
  KEY `fk_xp_log_log_id_reviewed` (`log_id_reviewed`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS `activity_leaderboards`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `activity_leaderboards` AS select count(0) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name` from (`log` join `player` on((`player`.`id` = `log`.`player_id`))) where (`log`.`accepted` is not null) group by `log`.`player_id`,`log`.`player_id_owner` order by `count` desc;
DROP TABLE IF EXISTS `activity_leaderboards_last_month`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `activity_leaderboards_last_month` AS select count(`log`.`id`) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval ((dayofmonth(curdate()) + dayofmonth(last_day(curdate()))) - 1) day)) and (`log`.`acquired` < (curdate() - interval (dayofmonth(curdate()) - 1) day)) and (`log`.`accepted` is not null)))) group by `player`.`id`,`log`.`player_id_owner` order by `count` desc;
DROP TABLE IF EXISTS `activity_leaderboards_last_week`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `activity_leaderboards_last_week` AS select count(`log`.`id`) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofweek(curdate()) + 6) day)) and (`log`.`acquired` < (curdate() - interval (dayofweek(curdate()) - 1) day)) and (`log`.`accepted` is not null)))) group by `player`.`id`,`log`.`player_id_owner` order by `count` desc;
DROP TABLE IF EXISTS `activity_leaderboards_this_month`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `activity_leaderboards_this_month` AS select count(`log`.`id`) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofmonth(curdate()) - 1) day)) and (`log`.`acquired` <= curdate()) and (`log`.`accepted` is not null)))) group by `player`.`id`,`log`.`player_id_owner` order by `count` desc;
DROP TABLE IF EXISTS `activity_leaderboards_this_week`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `activity_leaderboards_this_week` AS select count(`log`.`id`) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofweek(curdate()) - 1) day)) and (`log`.`acquired` <= curdate()) and (`log`.`accepted` is not null)))) group by `player`.`id`,`log`.`player_id_owner` order by `count` desc;
DROP TABLE IF EXISTS `activity_ranking`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `activity_ranking` AS select count(0) AS `count`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name` from (`log` join `player` on((`player`.`id` = `log`.`player_id`))) group by `log`.`player_id` order by count(0) desc;
DROP TABLE IF EXISTS `badge_activity_progress`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `badge_activity_progress` AS select `player`.`id` AS `player_id`,`badge`.`id` AS `badge_id`,`ar`.`activity_id` AS `activity_id`,coalesce(sum(`ars`.`times`),0) AS `activities_completed`,coalesce(`ar`.`count`,0) AS `activities_required` from (((`player` join `badge`) left join `activity_requisite` `ar` on((`ar`.`badge_id` = `badge`.`id`))) left join `activity_requisite_summary` `ars` on(((`ar`.`id` = `ars`.`activity_requisite_id`) and (`ars`.`player_id` = `player`.`id`)))) group by `player`.`id`,`badge_id`,`ar`.`activity_id` order by `player`.`id`,`badge_id`,`ar`.`activity_id`;
DROP TABLE IF EXISTS `badge_claimed`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `badge_claimed` AS select `player`.`id` AS `player_id`,`badge`.`id` AS `badge_id`,(`badge_log`.`id` is not null) AS `claimed` from ((`player` join `badge`) left join `badge_log` on(((`badge_log`.`player_id` = `player`.`id`) and (`badge_log`.`badge_id` = `badge`.`id`))));
DROP TABLE IF EXISTS `calendar_log`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `calendar_log` AS select count(0) AS `coins`,`log`.`player_id` AS `player_id`,`log`.`acquired` AS `acquired`,`log`.`domain_id` AS `domain_id`,`log`.`activity_id` AS `activity_id` from `log` group by `log`.`activity_id`,`log`.`player_id`,`log`.`acquired` order by `log`.`acquired`,`log`.`player_id`;
DROP TABLE IF EXISTS `different_activities_completed`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `different_activities_completed` AS select count(distinct `log`.`activity_id`) AS `different_activities_completed`,`log`.`domain_id` AS `domain_id`,`domain`.`name` AS `domain_name`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name`,`log`.`player_id_owner` AS `player_id_owner` from (((`log` join `player` on((`player`.`id` = `log`.`player_id`))) join `activity` on((`activity`.`id` = `log`.`activity_id`))) join `domain` on((`domain`.`id` = `activity`.`domain_id`))) where ((`log`.`reviewed` is not null) and (`activity`.`inactive` = 0)) group by `log`.`player_id`,`log`.`domain_id` order by `log`.`player_id`,`log`.`domain_id`;
DROP TABLE IF EXISTS `domain_activities_count`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `domain_activities_count` AS select `activity`.`domain_id` AS `domain_id`,`activity`.`player_id_owner` AS `player_id_owner`,count(0) AS `count` from `activity` where (`activity`.`inactive` = 0) group by `activity`.`domain_id`,`activity`.`player_id_owner` order by `activity`.`domain_id`;
DROP TABLE IF EXISTS `event_activity_progress`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `event_activity_progress` AS select `log`.`player_id` AS `player_id`,`log`.`event_id` AS `event_id`,`log`.`activity_id` AS `activity_id`,count(0) AS `times_obtained`,`event_activity`.`count` AS `times_required`,floor(((count(0) / `event_activity`.`count`) * 100)) AS `progress` from (`log` left join `event_activity` on(((`event_activity`.`event_id` = `log`.`event_id`) and (`event_activity`.`activity_id` = `log`.`activity_id`)))) where ((`log`.`reviewed` is not null) and (`log`.`event_id` is not null)) group by `log`.`player_id`,`log`.`event_id`,`log`.`activity_id`;
DROP TABLE IF EXISTS `last_week_logs`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `last_week_logs` AS select `activity`.`id` AS `activity_id`,concat((select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 1 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 2 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 3 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 4 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 5 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 6 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 7 day))))) AS `logs` from `activity` where (`activity`.`inactive` = 0) order by `activity`.`id`;
DROP TABLE IF EXISTS `player_activity_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=CURRENT_USER SQL SECURITY DEFINER VIEW `player_activity_summary` AS select `log`.`player_id` AS `player_id`,`player`.`name` AS `player_name`,count(0) AS `count`,`log`.`activity_id` AS `activity_id`,`log`.`reviewed` AS `log_reviewed`,`activity`.`name` AS `activity_name`,`activity`.`description` AS `activity_description`,`domain`.`id` AS `domain_id`,`domain`.`name` AS `domain_name`,`domain`.`abbr` AS `domain_abbr`,`domain`.`color` AS `domain_color` from (((`log` join `activity` on((`activity`.`id` = `log`.`activity_id`))) join `player` on((`player`.`id` = `log`.`player_id`))) join `domain` on((`domain`.`id` = `activity`.`domain_id`))) where ((`activity`.`inactive` = 0) and (`log`.`accepted` is not null)) group by `log`.`activity_id`,`log`.`player_id` order by `log`.`player_id`,`activity`.`domain_id`,`activity`.`name`;


ALTER TABLE `activity`
  ADD CONSTRAINT `activity_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  ADD CONSTRAINT `fk_activity_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);

ALTER TABLE `activity_requisite`
  ADD CONSTRAINT `prerequisite_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `prerequisite_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`);

ALTER TABLE `activity_requisite_summary`
  ADD CONSTRAINT `fk_activity_requisite_summary_activity_requisite_id` FOREIGN KEY (`activity_requisite_id`) REFERENCES `activity_requisite` (`id`),
  ADD CONSTRAINT `fk_activity_requisite_summary_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`),
  ADD CONSTRAINT `fk_activity_requisite_summary_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

ALTER TABLE `activity_requisite_tag`
  ADD CONSTRAINT `fk_activity_requisite_tag_activity_requisite_id` FOREIGN KEY (`activity_requisite_id`) REFERENCES `activity_requisite` (`id`),
  ADD CONSTRAINT `fk_activity_requisite_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`);

ALTER TABLE `badge`
  ADD CONSTRAINT `badge_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  ADD CONSTRAINT `badge_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);

ALTER TABLE `badge_log`
  ADD CONSTRAINT `fk_badge_log_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`),
  ADD CONSTRAINT `fk_badge_log_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  ADD CONSTRAINT `fk_badge_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

ALTER TABLE `badge_requisite`
  ADD CONSTRAINT `badge_requisite_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`),
  ADD CONSTRAINT `badge_requisite_badge_id_requisite` FOREIGN KEY (`badge_id_requisite`) REFERENCES `badge` (`id`);

ALTER TABLE `domain`
  ADD CONSTRAINT `fk_domain_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fk_domain_player_type_id` FOREIGN KEY (`player_type_id`) REFERENCES `player_type` (`id`);

ALTER TABLE `event`
  ADD CONSTRAINT `event_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fk_event_event_type_id` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`);

ALTER TABLE `event_activity`
  ADD CONSTRAINT `fk_event_activity_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `fk_event_activity_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

ALTER TABLE `event_complete_log`
  ADD CONSTRAINT `fk_event_completed_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_event_completed_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

ALTER TABLE `event_join_log`
  ADD CONSTRAINT `fk_event_join_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_event_join_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

ALTER TABLE `event_task`
  ADD CONSTRAINT `fk_event_task_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

ALTER TABLE `event_task_log`
  ADD CONSTRAINT `fk_event_task_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_event_task_log_event_task_id` FOREIGN KEY (`event_task_id`) REFERENCES `event_task` (`id`),
  ADD CONSTRAINT `fk_event_task_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

ALTER TABLE `log`
  ADD CONSTRAINT `fk_log_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `fk_log_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  ADD CONSTRAINT `fk_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fk_log_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`),
  ADD CONSTRAINT `fk_log_player_id_pair` FOREIGN KEY (`player_id_pair`) REFERENCES `player` (`id`);

ALTER TABLE `log_tag`
  ADD CONSTRAINT `fk_log_tag_log_id` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`),
  ADD CONSTRAINT `fk_log_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`);

ALTER TABLE `log_votes`
  ADD CONSTRAINT `fk_log_vote_log_id` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`),
  ADD CONSTRAINT `fk_log_vote_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

ALTER TABLE `player`
  ADD CONSTRAINT `fk_player_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`),
  ADD CONSTRAINT `fk_player_type_id` FOREIGN KEY (`player_type_id`) REFERENCES `player_type` (`id`);

ALTER TABLE `tag`
  ADD CONSTRAINT `fk_tag_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);

ALTER TABLE `team`
  ADD CONSTRAINT `fk_team_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`);

ALTER TABLE `xp_log`
  ADD CONSTRAINT `fk_xp_log_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `fk_xp_log_activity_id_reviewed` FOREIGN KEY (`activity_id_reviewed`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `fk_xp_log_event_id_completed` FOREIGN KEY (`event_id_completed`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_xp_log_event_task_id` FOREIGN KEY (`event_task_id`) REFERENCES `event_task` (`id`),
  ADD CONSTRAINT `fk_xp_log_event_task_id_reviewed` FOREIGN KEY (`event_task_id_reviewed`) REFERENCES `event_task` (`id`),
  ADD CONSTRAINT `fk_xp_log_log_id` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`),
  ADD CONSTRAINT `fk_xp_log_log_id_reviewed` FOREIGN KEY (`log_id_reviewed`) REFERENCES `log` (`id`),
  ADD CONSTRAINT `fk_xp_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
