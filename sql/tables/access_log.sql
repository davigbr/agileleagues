DROP TABLE IF EXISTS access_log;
CREATE TABLE `access_log` (
	`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`plugin`  varchar(10) NULL ,
	`controller`  varchar(50) NULL ,
	`action`  varchar(50) NULL ,
	`params`  text NULL ,
	`post`  text NULL ,
	`get`  text NULL ,
	`player_id`  int(10) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE=ARCHIVE;
