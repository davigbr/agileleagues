-- MySQL dump 10.13  Distrib 5.6.12, for Win64 (x86_64)
--
-- Host: localhost    Database: agileleagues
-- ------------------------------------------------------
-- Server version	5.6.12-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `access_log`
--

DROP TABLE IF EXISTS `access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=ARCHIVE DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `fk_activity_player_id_owner` (`player_id_owner`),
  CONSTRAINT `activity_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  CONSTRAINT `fk_activity_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=73 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `activity_leaderboards`
--

DROP TABLE IF EXISTS `activity_leaderboards`;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `activity_leaderboards` (
  `count` tinyint NOT NULL,
  `player_id_owner` tinyint NOT NULL,
  `player_id` tinyint NOT NULL,
  `player_name` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `activity_leaderboards_last_month`
--

DROP TABLE IF EXISTS `activity_leaderboards_last_month`;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards_last_month`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `activity_leaderboards_last_month` (
  `count` tinyint NOT NULL,
  `player_id_owner` tinyint NOT NULL,
  `player_id` tinyint NOT NULL,
  `player_name` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `activity_leaderboards_last_week`
--

DROP TABLE IF EXISTS `activity_leaderboards_last_week`;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards_last_week`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `activity_leaderboards_last_week` (
  `count` tinyint NOT NULL,
  `player_id_owner` tinyint NOT NULL,
  `player_id` tinyint NOT NULL,
  `player_name` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `activity_leaderboards_this_month`
--

DROP TABLE IF EXISTS `activity_leaderboards_this_month`;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards_this_month`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `activity_leaderboards_this_month` (
  `count` tinyint NOT NULL,
  `player_id_owner` tinyint NOT NULL,
  `player_id` tinyint NOT NULL,
  `player_name` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `activity_leaderboards_this_week`
--

DROP TABLE IF EXISTS `activity_leaderboards_this_week`;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards_this_week`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `activity_leaderboards_this_week` (
  `count` tinyint NOT NULL,
  `player_id_owner` tinyint NOT NULL,
  `player_id` tinyint NOT NULL,
  `player_name` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `activity_ranking`
--

DROP TABLE IF EXISTS `activity_ranking`;
/*!50001 DROP VIEW IF EXISTS `activity_ranking`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `activity_ranking` (
  `count` tinyint NOT NULL,
  `player_id` tinyint NOT NULL,
  `player_name` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `activity_requisite`
--

DROP TABLE IF EXISTS `activity_requisite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_requisite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `prerequisite_badge_id` (`badge_id`),
  KEY `prerequisite_activity_id` (`activity_id`),
  CONSTRAINT `prerequisite_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  CONSTRAINT `prerequisite_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=148 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_requisite_tag`
--

DROP TABLE IF EXISTS `activity_requisite_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_requisite_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_requisite_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_requisite_tag_tag_id` (`tag_id`),
  KEY `fk_activity_requisite_tag_activity_requisite_id` (`activity_requisite_id`),
  CONSTRAINT `fk_activity_requisite_tag_activity_requisite_id` FOREIGN KEY (`activity_requisite_id`) REFERENCES `activity_requisite` (`id`),
  CONSTRAINT `fk_activity_requisite_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge`
--

DROP TABLE IF EXISTS `badge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` smallint(5) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `abbr` varchar(3) DEFAULT NULL,
  `new` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `icon` varchar(50) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `player_id_owner` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `badge_domain_id` (`domain_id`),
  KEY `badge_player_id_owner` (`player_id_owner`),
  CONSTRAINT `badge_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  CONSTRAINT `badge_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `badge_activity_progress`
--

DROP TABLE IF EXISTS `badge_activity_progress`;
/*!50001 DROP VIEW IF EXISTS `badge_activity_progress`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `badge_activity_progress` (
  `player_id` tinyint NOT NULL,
  `badge_id` tinyint NOT NULL,
  `activity_id` tinyint NOT NULL,
  `activities_completed` tinyint NOT NULL,
  `activities_required` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `badge_claimed`
--

DROP TABLE IF EXISTS `badge_claimed`;
/*!50001 DROP VIEW IF EXISTS `badge_claimed`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `badge_claimed` (
  `player_id` tinyint NOT NULL,
  `badge_id` tinyint NOT NULL,
  `claimed` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `badge_log`
--

DROP TABLE IF EXISTS `badge_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`badge_id`,`player_id`) USING HASH,
  KEY `fk_badge_log_player_id` (`player_id`),
  CONSTRAINT `fk_badge_log_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`),
  CONSTRAINT `fk_badge_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_requisite`
--

DROP TABLE IF EXISTS `badge_requisite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge_requisite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `badge_id_requisite` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `badge_requisite_badge_id` (`badge_id`),
  KEY `badge_requisite_badge_id_requisite` (`badge_id_requisite`),
  CONSTRAINT `badge_requisite_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`),
  CONSTRAINT `badge_requisite_badge_id_requisite` FOREIGN KEY (`badge_id_requisite`) REFERENCES `badge` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_summary`
--

DROP TABLE IF EXISTS `badge_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badge_summary` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `activity_requisite_id` int(10) unsigned NOT NULL,
  `times` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_badge_summary_player_id` (`player_id`),
  KEY `fk_badge_summary_activity_requisite_id` (`activity_requisite_id`),
  KEY `fk_badge_summary_badge_id` (`badge_id`),
  CONSTRAINT `fk_badge_summary_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
  CONSTRAINT `fk_badge_summary_activity_requisite_id` FOREIGN KEY (`activity_requisite_id`) REFERENCES `activity_requisite` (`id`),
  CONSTRAINT `fk_badge_summary_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `calendar_log`
--

DROP TABLE IF EXISTS `calendar_log`;
/*!50001 DROP VIEW IF EXISTS `calendar_log`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `calendar_log` (
  `coins` tinyint NOT NULL,
  `player_id` tinyint NOT NULL,
  `acquired` tinyint NOT NULL,
  `domain_id` tinyint NOT NULL,
  `activity_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuration` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(30) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_idx` (`key`) USING HASH
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `different_activities_completed`
--

DROP TABLE IF EXISTS `different_activities_completed`;
/*!50001 DROP VIEW IF EXISTS `different_activities_completed`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `different_activities_completed` (
  `different_activities_completed` tinyint NOT NULL,
  `domain_id` tinyint NOT NULL,
  `domain_name` tinyint NOT NULL,
  `player_id` tinyint NOT NULL,
  `player_name` tinyint NOT NULL,
  `player_id_owner` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `domain`
--

DROP TABLE IF EXISTS `domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  PRIMARY KEY (`id`),
  KEY `fk_domain_player_type_id` (`player_type_id`),
  KEY `fk_domain_player_id_owner` (`player_id_owner`),
  CONSTRAINT `fk_domain_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`),
  CONSTRAINT `fk_domain_player_type_id` FOREIGN KEY (`player_type_id`) REFERENCES `player_type` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `domain_activities_count`
--

DROP TABLE IF EXISTS `domain_activities_count`;
/*!50001 DROP VIEW IF EXISTS `domain_activities_count`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `domain_activities_count` (
  `domain_id` tinyint NOT NULL,
  `player_id_owner` tinyint NOT NULL,
  `count` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `event_player_id_owner` (`player_id_owner`),
  CONSTRAINT `event_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`),
  CONSTRAINT `fk_event_event_type_id` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_activity`
--

DROP TABLE IF EXISTS `event_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_event_activity_event_id` (`event_id`),
  KEY `fk_event_activity_activity_id` (`activity_id`),
  CONSTRAINT `fk_event_activity_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  CONSTRAINT `fk_event_activity_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `event_activity_progress`
--

DROP TABLE IF EXISTS `event_activity_progress`;
/*!50001 DROP VIEW IF EXISTS `event_activity_progress`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `event_activity_progress` (
  `player_id` tinyint NOT NULL,
  `event_id` tinyint NOT NULL,
  `activity_id` tinyint NOT NULL,
  `times_obtained` tinyint NOT NULL,
  `times_required` tinyint NOT NULL,
  `progress` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `event_complete_log`
--

DROP TABLE IF EXISTS `event_complete_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_complete_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`event_id`,`player_id`),
  KEY `fk_event_completed_log_player_id` (`player_id`),
  CONSTRAINT `fk_event_completed_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  CONSTRAINT `fk_event_completed_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_join_log`
--

DROP TABLE IF EXISTS `event_join_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_join_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`event_id`,`player_id`),
  KEY `fk_event_join_log_player_id` (`player_id`),
  CONSTRAINT `fk_event_join_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  CONSTRAINT `fk_event_join_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_task`
--

DROP TABLE IF EXISTS `event_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  `xp` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_event_task_event_id` (`event_id`),
  CONSTRAINT `fk_event_task_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_task_log`
--

DROP TABLE IF EXISTS `event_task_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `fk_event_task_log_event_id` (`event_id`),
  CONSTRAINT `fk_event_task_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  CONSTRAINT `fk_event_task_log_event_task_id` FOREIGN KEY (`event_task_id`) REFERENCES `event_task` (`id`),
  CONSTRAINT `fk_event_task_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_type`
--

DROP TABLE IF EXISTS `event_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `level_required` smallint(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `last_week_logs`
--

DROP TABLE IF EXISTS `last_week_logs`;
/*!50001 DROP VIEW IF EXISTS `last_week_logs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `last_week_logs` (
  `activity_id` tinyint NOT NULL,
  `logs` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  PRIMARY KEY (`id`),
  KEY `fk_log_event_id` (`event_id`),
  KEY `fk_activity_activity_id` (`activity_id`) USING BTREE,
  KEY `fk_log_player_idx` (`player_id`) USING BTREE,
  KEY `fk_log_domain_idx` (`domain_id`) USING BTREE,
  KEY `fk_log_player_id_owner` (`player_id_owner`),
  KEY `fk_log_player_id_pair` (`player_id_pair`),
  CONSTRAINT `fk_log_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  CONSTRAINT `fk_log_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  CONSTRAINT `fk_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  CONSTRAINT `fk_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`),
  CONSTRAINT `fk_log_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`),
  CONSTRAINT `fk_log_player_id_pair` FOREIGN KEY (`player_id_pair`) REFERENCES `player` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=1360 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_tag`
--

DROP TABLE IF EXISTS `log_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_tag_tag_id` (`tag_id`),
  KEY `unique` (`log_id`,`tag_id`),
  CONSTRAINT `fk_log_tag_log_id` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`),
  CONSTRAINT `fk_log_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_votes`
--

DROP TABLE IF EXISTS `log_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(10) unsigned NOT NULL,
  `vote` smallint(5) NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `comment` varchar(250) NOT NULL DEFAULT '',
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`log_id`,`player_id`),
  KEY `fk_log_vote_player_id` (`player_id`),
  CONSTRAINT `fk_log_vote_log_id` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`),
  CONSTRAINT `fk_log_vote_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MEMORY AUTO_INCREMENT=1188 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_verification_hash` (`hash`) USING HASH,
  KEY `fk_player_type_id` (`player_type_id`),
  KEY `fk_player_team_id` (`team_id`),
  CONSTRAINT `fk_player_team_id` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`),
  CONSTRAINT `fk_player_type_id` FOREIGN KEY (`player_type_id`) REFERENCES `player_type` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `player_activity_summary`
--

DROP TABLE IF EXISTS `player_activity_summary`;
/*!50001 DROP VIEW IF EXISTS `player_activity_summary`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `player_activity_summary` (
  `player_id` tinyint NOT NULL,
  `player_name` tinyint NOT NULL,
  `count` tinyint NOT NULL,
  `activity_id` tinyint NOT NULL,
  `log_reviewed` tinyint NOT NULL,
  `activity_name` tinyint NOT NULL,
  `activity_description` tinyint NOT NULL,
  `domain_id` tinyint NOT NULL,
  `domain_name` tinyint NOT NULL,
  `domain_abbr` tinyint NOT NULL,
  `domain_color` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `player_type`
--

DROP TABLE IF EXISTS `player_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` varchar(250) NOT NULL,
  `color` char(7) NOT NULL,
  `inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `new` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `player_id_owner` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tag_player_id_owner` (`player_id_owner`),
  CONSTRAINT `fk_tag_player_id_owner` FOREIGN KEY (`player_id_owner`) REFERENCES `player` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `player_id_scrummaster` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_team_player_id_scrummaster` (`player_id_scrummaster`),
  CONSTRAINT `fk_team_player_id_scrummaster` FOREIGN KEY (`player_id_scrummaster`) REFERENCES `player` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timeline`
--

DROP TABLE IF EXISTS `timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MEMORY AUTO_INCREMENT=771 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `title`
--

DROP TABLE IF EXISTS `title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL,
  `min_level` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `min_level` (`min_level`) USING BTREE
) ENGINE=MEMORY AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xp_log`
--

DROP TABLE IF EXISTS `xp_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `fk_xp_log_log_id_reviewed` (`log_id_reviewed`),
  CONSTRAINT `fk_xp_log_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  CONSTRAINT `fk_xp_log_activity_id_reviewed` FOREIGN KEY (`activity_id_reviewed`) REFERENCES `activity` (`id`),
  CONSTRAINT `fk_xp_log_event_id_completed` FOREIGN KEY (`event_id_completed`) REFERENCES `event` (`id`),
  CONSTRAINT `fk_xp_log_event_task_id` FOREIGN KEY (`event_task_id`) REFERENCES `event_task` (`id`),
  CONSTRAINT `fk_xp_log_event_task_id_reviewed` FOREIGN KEY (`event_task_id_reviewed`) REFERENCES `event_task` (`id`),
  CONSTRAINT `fk_xp_log_log_id` FOREIGN KEY (`log_id`) REFERENCES `log` (`id`),
  CONSTRAINT `fk_xp_log_log_id_reviewed` FOREIGN KEY (`log_id_reviewed`) REFERENCES `log` (`id`),
  CONSTRAINT `fk_xp_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=1059 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'agileleagues'
--
/*!50003 DROP FUNCTION IF EXISTS `player_level` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `player_level`(

	_xp INT(10) UNSIGNED

) RETURNS int(10) unsigned
    NO SQL
BEGIN



	RETURN FLOOR(1 + 0.0464159 * POW(_xp, 2/3));



END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `activity_leaderboards`
--

/*!50001 DROP TABLE IF EXISTS `activity_leaderboards`*/;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `activity_leaderboards` AS select count(0) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name` from (`log` join `player` on((`player`.`id` = `log`.`player_id`))) where (`log`.`reviewed` is not null) group by `log`.`player_id`,`log`.`player_id_owner` order by count(0) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `activity_leaderboards_last_month`
--

/*!50001 DROP TABLE IF EXISTS `activity_leaderboards_last_month`*/;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards_last_month`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `activity_leaderboards_last_month` AS select count(`log`.`id`) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval ((dayofmonth(curdate()) + dayofmonth(last_day(curdate()))) - 1) day)) and (`log`.`acquired` < (curdate() - interval (dayofmonth(curdate()) - 1) day)) and (`log`.`reviewed` is not null)))) group by `player`.`id`,`log`.`player_id_owner` order by count(`log`.`id`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `activity_leaderboards_last_week`
--

/*!50001 DROP TABLE IF EXISTS `activity_leaderboards_last_week`*/;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards_last_week`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `activity_leaderboards_last_week` AS select count(`log`.`id`) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofweek(curdate()) + 6) day)) and (`log`.`acquired` < (curdate() - interval (dayofweek(curdate()) - 1) day)) and (`log`.`reviewed` is not null)))) group by `player`.`id`,`log`.`player_id_owner` order by count(`log`.`id`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `activity_leaderboards_this_month`
--

/*!50001 DROP TABLE IF EXISTS `activity_leaderboards_this_month`*/;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards_this_month`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `activity_leaderboards_this_month` AS select count(`log`.`id`) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofmonth(curdate()) - 1) day)) and (`log`.`acquired` <= curdate()) and (`log`.`reviewed` is not null)))) group by `player`.`id`,`log`.`player_id_owner` order by count(`log`.`id`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `activity_leaderboards_this_week`
--

/*!50001 DROP TABLE IF EXISTS `activity_leaderboards_this_week`*/;
/*!50001 DROP VIEW IF EXISTS `activity_leaderboards_this_week`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `activity_leaderboards_this_week` AS select count(`log`.`id`) AS `count`,`log`.`player_id_owner` AS `player_id_owner`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofweek(curdate()) - 1) day)) and (`log`.`acquired` <= curdate()) and (`log`.`reviewed` is not null)))) group by `player`.`id`,`log`.`player_id_owner` order by count(`log`.`id`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `activity_ranking`
--

/*!50001 DROP TABLE IF EXISTS `activity_ranking`*/;
/*!50001 DROP VIEW IF EXISTS `activity_ranking`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `activity_ranking` AS select count(0) AS `count`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name` from (`log` join `player` on((`player`.`id` = `log`.`player_id`))) group by `log`.`player_id` order by count(0) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `badge_activity_progress`
--

/*!50001 DROP TABLE IF EXISTS `badge_activity_progress`*/;
/*!50001 DROP VIEW IF EXISTS `badge_activity_progress`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `badge_activity_progress` AS select `player`.`id` AS `player_id`,`badge`.`id` AS `badge_id`,`ar`.`activity_id` AS `activity_id`,count(`log`.`id`) AS `activities_completed`,`ar`.`count` AS `activities_required` from (((`player` join `badge`) join `activity_requisite` `ar` on((`ar`.`badge_id` = `badge`.`id`))) left join `log` on(((`log`.`activity_id` = `ar`.`activity_id`) and (`log`.`player_id` = `player`.`id`) and (`log`.`accepted` is not null)))) group by `player`.`id`,`ar`.`badge_id`,`ar`.`activity_id` order by `player`.`id`,`badge_id`,`ar`.`activity_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `badge_claimed`
--

/*!50001 DROP TABLE IF EXISTS `badge_claimed`*/;
/*!50001 DROP VIEW IF EXISTS `badge_claimed`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `badge_claimed` AS select `player`.`id` AS `player_id`,`badge`.`id` AS `badge_id`,(`badge_log`.`id` is not null) AS `claimed` from ((`player` join `badge`) left join `badge_log` on(((`badge_log`.`player_id` = `player`.`id`) and (`badge_log`.`badge_id` = `badge`.`id`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `calendar_log`
--

/*!50001 DROP TABLE IF EXISTS `calendar_log`*/;
/*!50001 DROP VIEW IF EXISTS `calendar_log`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `calendar_log` AS select count(0) AS `coins`,`log`.`player_id` AS `player_id`,`log`.`acquired` AS `acquired`,`log`.`domain_id` AS `domain_id`,`log`.`activity_id` AS `activity_id` from `log` group by `log`.`activity_id`,`log`.`player_id`,`log`.`acquired` order by `log`.`acquired`,`log`.`player_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `different_activities_completed`
--

/*!50001 DROP TABLE IF EXISTS `different_activities_completed`*/;
/*!50001 DROP VIEW IF EXISTS `different_activities_completed`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `different_activities_completed` AS select count(distinct `log`.`activity_id`) AS `different_activities_completed`,`log`.`domain_id` AS `domain_id`,`domain`.`name` AS `domain_name`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name`,`log`.`player_id_owner` AS `player_id_owner` from (((`log` join `player` on((`player`.`id` = `log`.`player_id`))) join `activity` on((`activity`.`id` = `log`.`activity_id`))) join `domain` on((`domain`.`id` = `activity`.`domain_id`))) where ((`log`.`reviewed` is not null) and (`activity`.`inactive` = 0)) group by `log`.`player_id`,`log`.`domain_id` order by `log`.`player_id`,`log`.`domain_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `domain_activities_count`
--

/*!50001 DROP TABLE IF EXISTS `domain_activities_count`*/;
/*!50001 DROP VIEW IF EXISTS `domain_activities_count`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `domain_activities_count` AS select `activity`.`domain_id` AS `domain_id`,`activity`.`player_id_owner` AS `player_id_owner`,count(0) AS `count` from `activity` where (`activity`.`inactive` = 0) group by `activity`.`domain_id`,`activity`.`player_id_owner` order by `activity`.`domain_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `event_activity_progress`
--

/*!50001 DROP TABLE IF EXISTS `event_activity_progress`*/;
/*!50001 DROP VIEW IF EXISTS `event_activity_progress`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_activity_progress` AS select `log`.`player_id` AS `player_id`,`log`.`event_id` AS `event_id`,`log`.`activity_id` AS `activity_id`,count(0) AS `times_obtained`,`event_activity`.`count` AS `times_required`,floor(((count(0) / `event_activity`.`count`) * 100)) AS `progress` from (`log` left join `event_activity` on(((`event_activity`.`event_id` = `log`.`event_id`) and (`event_activity`.`activity_id` = `log`.`activity_id`)))) where ((`log`.`reviewed` is not null) and (`log`.`event_id` is not null)) group by `log`.`player_id`,`log`.`event_id`,`log`.`activity_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `last_week_logs`
--

/*!50001 DROP TABLE IF EXISTS `last_week_logs`*/;
/*!50001 DROP VIEW IF EXISTS `last_week_logs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `last_week_logs` AS select `activity`.`id` AS `activity_id`,concat((select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 1 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 2 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 3 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 4 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 5 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 6 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 7 day))))) AS `logs` from `activity` where (`activity`.`inactive` = 0) order by `activity`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `player_activity_summary`
--

/*!50001 DROP TABLE IF EXISTS `player_activity_summary`*/;
/*!50001 DROP VIEW IF EXISTS `player_activity_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `player_activity_summary` AS select `log`.`player_id` AS `player_id`,`player`.`name` AS `player_name`,count(0) AS `count`,`log`.`activity_id` AS `activity_id`,`log`.`reviewed` AS `log_reviewed`,`activity`.`name` AS `activity_name`,`activity`.`description` AS `activity_description`,`domain`.`id` AS `domain_id`,`domain`.`name` AS `domain_name`,`domain`.`abbr` AS `domain_abbr`,`domain`.`color` AS `domain_color` from (((`log` join `activity` on((`activity`.`id` = `log`.`activity_id`))) join `player` on((`player`.`id` = `log`.`player_id`))) join `domain` on((`domain`.`id` = `activity`.`domain_id`))) where ((`activity`.`inactive` = 0) and (`log`.`accepted` is not null)) group by `log`.`activity_id`,`log`.`player_id` order by `log`.`player_id`,`activity`.`domain_id`,`activity`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-30 19:51:33
