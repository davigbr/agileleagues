-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 06-Abr-2014 às 08:19
-- Versão do servidor: 5.6.12-log
-- versão do PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `agileleagues`
--
CREATE DATABASE IF NOT EXISTS `agileleagues` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `agileleagues`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `claim_badge`(
	_player_id INT(10) UNSIGNED,
	_badge_id INT(10) UNSIGNED
)
BEGIN

	DECLARE _done TINYINT(1) DEFAULT 0;
	DECLARE _can_claim TINYINT(1) DEFAULT 1;
	DECLARE _activity_id INT(3) UNSIGNED DEFAULT NULL;
	DECLARE _coins_required TINYINT(3) UNSIGNED DEFAULT 0;
	DECLARE _coins_obtained TINYINT(3) UNSIGNED DEFAULT 0;
	DECLARE _badges_required TINYINT(3) UNSIGNED DEFAULT 0;
	DECLARE _badges_claimed TINYINT(3) UNSIGNED DEFAULT 0;
	DECLARE _counter SMALLINT(5) UNSIGNED DEFAULT 0;

	DECLARE _badges_activity_progress CURSOR FOR 
		SELECT activity_id, coins_obtained, coins_required FROM badge_activity_progress 
		WHERE badge_id = _badge_id AND player_id = _player_id;

 	DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = 1;

	IF _player_id IS NOT NULL AND _badge_id IS NOT NULL THEN

		-- Verifica se o jogador possui todas as badges de pré-requisito

		-- Número de badges necessárias
		SELECT COUNT(*) INTO _badges_required 
		FROM badge_requisite WHERE badge_id = _badge_id;

		-- Número de badges obtidas		
		SELECT COUNT(*) INTO _badges_claimed 
		FROM badge_log WHERE badge_id IN (
			SELECT badge_id_requisite FROM badge_requisite WHERE badge_id = _badge_id
		);

		-- Se o número bater, procede para a verificação das atividades
		IF _badges_claimed = _badges_required THEN

			OPEN _badges_activity_progress;

			read_loop: LOOP
				FETCH _badges_activity_progress INTO _activity_id, _coins_obtained, _coins_required;

				IF _done THEN
					LEAVE read_loop;
				END IF;

				IF _coins_required > _coins_obtained THEN
					SET _can_claim = 0;
					LEAVE read_loop;
				END IF;

			END LOOP;

			CLOSE _badges_activity_progress;

			IF _can_claim THEN 
				
				-- Consome as activity coins
				OPEN _badges_activity_progress;

				SET _done = 0;

				read_loop: LOOP
					FETCH _badges_activity_progress INTO _activity_id, _coins_obtained, _coins_required;

					IF _done THEN
						LEAVE read_loop;
					END IF;

					update_loop: LOOP

						UPDATE log SET spent = 1 
						WHERE activity_id = _activity_id AND player_id = _player_id AND spent = 0
						ORDER BY acquired ASC 
						LIMIT 1;

						SET _counter = _counter + 1;

						IF (_counter = _coins_required) THEN
							LEAVE update_loop;
						END IF;

					END LOOP;

				END LOOP;

				CLOSE _badges_activity_progress;
				
				INSERT INTO badge_log SET
					badge_id = _badge_id,
					player_id = _player_id
				;

			END IF;

		END IF;

	END IF;	

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `global_notification`(
	_title VARCHAR(30),
	_text TEXT,
	_type VARCHAR(10)
)
BEGIN
	
	DECLARE _done TINYINT(1) DEFAULT 0;
	DECLARE _player_id INT(10) UNSIGNED DEFAULT 0;

	DECLARE _players CURSOR FOR 
		SELECT id FROM player; 

 	DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = 1;

	OPEN _players;

	read_loop: LOOP
		FETCH _players INTO _player_id;

		IF _done THEN
			LEAVE read_loop;
		END IF;

		INSERT INTO notification SET
			`text` = _text,
			title = _title,
			player_id = _player_id,
			type = _type
		;

	END LOOP;

END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `player_level`(
	_xp INT(10) UNSIGNED
) RETURNS int(10) unsigned
    NO SQL
BEGIN

	RETURN FLOOR(1 + 0.0464159 * POW(_xp, 2/3));

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `access_log`
--

CREATE TABLE IF NOT EXISTS `access_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plugin` varchar(10) DEFAULT NULL,
  `controller` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `params` text,
  `post` text,
  `get` text,
  `player_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ARCHIVE  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

--
-- Extraindo dados da tabela `access_log`
--

INSERT INTO `access_log` (`id`, `plugin`, `controller`, `action`, `params`, `post`, `get`, `player_id`) VALUES
(1, NULL, 'dashboards', 'players', '[]', '[]', '[]', 6),
(2, NULL, 'activities', 'report', '[]', '[]', '[]', 6),
(3, NULL, 'badges', 'index', '[]', '[]', '[]', 6),
(4, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(5, NULL, 'events', 'details', '["1"]', '[]', '[]', 6),
(6, NULL, 'events', 'edit', '["1"]', '[]', '[]', 6),
(7, NULL, 'events', 'edit', '["1"]', '{"_method":"PUT","data":{"Event":{"id":"1","event_type_id":"1","name":"Active Mission","description":"Description","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"12","year":"2014"}},"EventTask":[{"name":"Task Name 1","description":"Task Description 1","xp":"0"},{"name":"Task Name 2","description":"Task Description 2","xp":"0"},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"9","count":"1"},{"activity_id":"","count":"0"},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(8, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(9, NULL, 'events', 'details', '["1"]', '[]', '[]', 6),
(10, NULL, 'events', 'edit', '["1"]', '[]', '[]', 6),
(11, NULL, 'events', 'edit', '["1"]', '{"_method":"PUT","data":{"Event":{"id":"1","event_type_id":"1","name":"Active Mission","description":"Description","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"12","year":"2014"}},"EventTask":[{"name":"Task Name 1","description":"Task Description 1","xp":"0"},{"name":"Task Name 2","description":"Task Description 2","xp":"0"},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"9","count":"2"},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(12, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(13, NULL, 'activities', 'report', '[]', '[]', '[]', 6),
(14, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(15, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(16, NULL, 'events', 'details', '["1"]', '[]', '[]', 6),
(17, NULL, 'activities', 'notreviewed', '[]', '[]', '[]', 6),
(18, NULL, 'activities', 'report', '[]', '[]', '[]', 6),
(19, NULL, 'activities', 'report', '[]', '{"_method":"POST","data":{"Log":{"activity_id":"9","description":"asdfasdfasdf","acquired":{"month":"04","day":"05","year":"2014"}},"Event":{"id":"1"}}}', '[]', 6),
(20, NULL, 'activities', 'index', '[]', '[]', '[]', 6),
(21, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(22, NULL, 'events', 'edit', '["1"]', '[]', '[]', 6),
(23, NULL, 'events', 'edit', '["1"]', '{"_method":"PUT","data":{"Event":{"id":"1","event_type_id":"1","name":"Active Mission","description":"Description","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"12","year":"2014"}},"EventTask":[{"name":"Task Name 1","description":"Task Description 1","xp":"0"},{"name":"Task Name 2","description":"Task Description 2","xp":"0"},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"9","count":"2"},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(24, NULL, 'events', 'edit', '["1"]', '[]', '[]', 6),
(25, NULL, 'events', 'edit', '["1"]', '{"_method":"PUT","data":{"Event":{"id":"1","event_type_id":"1","name":"Active Mission","description":"Description","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"12","year":"2014"}},"EventTask":[{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"9","count":"2"},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(26, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(27, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(28, NULL, 'events', 'edit', '["7"]', '{"_method":"PUT","data":{"Event":{"id":"7","event_type_id":"1","name":"Mission Test","description":"","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(29, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(30, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(31, NULL, 'events', 'edit', '["7"]', '{"_method":"PUT","data":{"Event":{"id":"7","event_type_id":"1","name":"Mission Test","description":"","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"asdfa","description":"sdfasdfasd","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(32, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(33, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(34, NULL, 'events', 'edit', '["7"]', '{"_method":"PUT","data":{"Event":{"id":"7","event_type_id":"1","name":"Mission Test","description":"","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"123232","description":"123","xp":""},{"name":"1232323","description":"1232323","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(35, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(36, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(37, NULL, 'events', 'edit', '["7"]', '{"_method":"PUT","data":{"Event":{"id":"7","event_type_id":"1","name":"Mission Test","description":"","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"12312312","description":"12312313","xp":""},{"name":"312312","description":"3123","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(38, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(39, NULL, 'events', 'edit', '["9"]', '[]', '[]', 6),
(40, NULL, 'events', 'edit', '["9"]', '{"_method":"PUT","data":{"Event":{"id":"9","event_type_id":"1","name":"Mission One","description":"","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"123","description":"123","xp":""},{"name":"123","description":"123","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(41, NULL, 'events', 'edit', '["9"]', '[]', '[]', 6),
(42, NULL, 'events', 'edit', '["9"]', '{"_method":"PUT","data":{"Event":{"id":"9","event_type_id":"1","name":"Mission One","description":"","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"123","description":"123","xp":""},{"name":"123","description":"123","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(43, NULL, 'events', 'edit', '["9"]', '[]', '[]', 6),
(44, NULL, 'events', 'edit', '["9"]', '{"_method":"PUT","data":{"Event":{"id":"9","event_type_id":"1","name":"Mission One","description":"","xp":"123","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"adsfasdf","description":"asdfasdf","xp":""},{"name":"asdfas","description":"dfasdf","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(45, NULL, 'events', 'edit', '["9"]', '[]', '[]', 6),
(46, NULL, 'events', 'edit', '["9"]', '{"_method":"PUT","data":{"Event":{"id":"9","event_type_id":"1","name":"Mission One","description":"","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(47, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(48, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(49, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(50, NULL, 'events', 'edit', '["7"]', '{"_method":"PUT","data":{"Event":{"id":"7","event_type_id":"1","name":"Mission Test","description":"123123","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"asdf","description":"asdfasdfa","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(51, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(52, NULL, 'events', 'edit', '["7"]', '{"_method":"PUT","data":{"Event":{"id":"7","event_type_id":"1","name":"Mission Test","description":"123123","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"asdfasdf","description":"asdf","xp":""},{"name":"asdf","description":"asdf","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(53, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(54, NULL, 'events', 'edit', '["9"]', '[]', '[]', 6),
(55, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(56, NULL, 'events', 'edit', '["7"]', '{"_method":"PUT","data":{"Event":{"id":"7","event_type_id":"1","name":"Mission Test","description":"123123","xp":"0","start":{"month":"04","day":"05","year":"2014"},"end":{"month":"04","day":"05","year":"2014"}},"EventTask":[{"name":"asdfasdf","description":"asdf","xp":"0"},{"name":"asdf","description":"asdf","xp":"0"},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""},{"name":"","description":"","xp":""}],"EventActivity":[{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""},{"activity_id":"","count":""}]}}', '[]', 6),
(57, NULL, 'events', 'index', '[]', '[]', '[]', 6),
(58, NULL, 'events', 'edit', '["7"]', '[]', '[]', 6),
(59, NULL, 'activities', 'notreviewed', '[]', '[]', '[]', 6),
(60, NULL, 'logs', 'review', '["689"]', '[]', '[]', 6),
(61, NULL, 'activities', 'notreviewed', '[]', '[]', '[]', 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `new` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `xp` int(10) unsigned NOT NULL,
  `reported` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_name` (`name`),
  KEY `activity_domain_id` (`domain_id`),
  KEY `activity_reported` (`reported`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Extraindo dados da tabela `activity`
--

INSERT INTO `activity` (`id`, `name`, `domain_id`, `description`, `inactive`, `new`, `xp`, `reported`) VALUES
(1, 'Doc', 1, 'Criar documentação para funcionalidade nova (wordpress, por user story).', 1, 0, 0, 21),
(2, 'Code Doc', 1, 'Documentar código-fonte (código-fonte, por classe).', 1, 0, 5, 78),
(3, 'Glossary', 1, 'Adicionar termo no glossário (wordpress, por termo).', 1, 0, 20, 3),
(4, 'Improve Doc', 1, 'Melhorar documentação existente (wordpress, por página).', 1, 0, 0, 2),
(5, 'Question Doc', 1, 'Criar documentação para esclarecer dúvidas (wordpress, por dúvida).', 0, 0, 30, 4),
(6, 'Old Doc', 1, 'Adicionar documentação faltante para funcionalidades antigas (wordpress, por página).', 1, 0, 0, 0),
(7, 'Visual Doc', 1, 'Criar fluxograma, caso de uso ou alguma outra documentação não-textual. Necessita justificativa!', 0, 0, 50, 14),
(8, 'Persona', 1, 'Criar uma persona (completa, com biografia efoto).', 0, 0, 50, 13),
(9, 'API Doc', 1, 'Criar documentação para função da API (controller).', 0, 0, 20, 13),
(10, 'Pair Doc', 1, 'Executar atividade de documentação em par. Deve ser ganho junto com outra atividade de documentação.', 1, 0, 0, 6),
(11, 'Acceptance Criteria', 1, 'Escrever/registrar os critérios de aceitação de uma User Story.', 1, 0, 40, 1),
(12, 'Chima', 2, 'Preparar chimarrão (deve ser aprovado pela equipe).', 0, 0, 2, 7),
(13, 'Library Item', 2, 'Trazer livro/revista para a biblioteca da empresa (deve ser útil para a equipe).', 0, 0, 15, 0),
(14, 'Water Bottle', 2, 'Trocar a garrafa de água.', 0, 0, 2, 9),
(15, 'All-day Pair', 2, 'Parear durante um dia inteiro de trabalho.', 0, 0, 20, 30),
(16, 'Single Coaching', 2, 'Dar um treinamento de pelo menos 30 min para um membro da equipe.', 0, 0, 10, 1),
(17, 'Coffee', 2, 'Preparar café (deve ser aprovado pela equipe).', 0, 0, 2, 19),
(18, 'Team Coaching', 2, 'Dar treinamento à equipe sobre alguma tecnologia.', 0, 0, 50, 0),
(19, 'Help the Team', 2, 'Atividade voluntária de auxílio ao time.', 0, 0, 5, 2),
(20, 'Lead Retrospective', 2, 'Conduzir reunião de retrospectiva.', 0, 0, 50, 0),
(21, 'Track Sprint Metrics', 2, 'Atualizar quadro com métricas do Sprint (velocity, burnup, risks, etc). Somente uma pessoa por Sprint.', 0, 0, 35, 0),
(22, 'Daily Planning', 2, 'Planejar o dia antes da Daily Scrum e apresentar o que foi decidido na mesma.', 0, 0, 10, 1),
(23, 'Refactor', 3, 'Efetuar refatoração. Informar qual o tipo da refatoração, de acordo com "Refactoring: Improving the Design of Existing Code".', 0, 0, 10, 18),
(24, 'Read Article', 3, 'Ler artigo sobre algumas das seguintes tecnologias: XML, XPath, DTD, SVG ou XSLT.', 0, 0, 20, 0),
(25, 'MySQL Book', 3, 'Ler livro, estudar ou criar projeto complexo de MySQL.', 0, 0, 100, 0),
(26, 'Java Book', 3, 'Ler livro ou estudar sobre Java.', 0, 0, 100, 0),
(27, 'JavaScript Book', 3, 'Ler livro ou estudar sobre JavaScript.', 0, 0, 100, 0),
(28, 'PHP Book', 3, 'Ler livro ou estudar sobre PHP.', 0, 0, 100, 0),
(29, 'Android Book', 3, 'Ler livro ou estudar Android (avançado).', 0, 0, 100, 1),
(30, 'HTML Book', 3, 'Ler livro ou estudar sobre HTML5.', 0, 0, 100, 0),
(31, 'CSS Book', 3, 'Ler livro ou estudar sobre CSS3.', 0, 0, 100, 0),
(32, 'RESTful Book', 3, 'Ler livro ou estudar sobre RESTful (avançado).', 0, 0, 100, 0),
(33, 'Refactor Book', 3, 'Ler livro sobre refatoração.', 0, 0, 200, 0),
(34, 'OOP', 3, 'Ler livro ou estudar OOP Design Patterns.', 0, 0, 200, 0),
(35, 'JavaScript Community', 3, 'Criar plugin para jQuery e publicar (github, site do jquery, etc) ou criar módulo para Node.js e publicar.', 0, 0, 50, 1),
(36, 'Pair Refactoring', 3, 'Executar refatoração em par. Deve ser ganho junto com a atividade Refactor.', 0, 0, 10, 5),
(37, 'Unit Test', 4, 'Criar teste unitário (por classe).', 0, 0, 10, 52),
(38, 'Legacy Test', 4, 'Criar teste unitário para uma funcionalidade antiga que não possui teste (por classe).', 0, 0, 15, 1),
(39, 'Unit Bug Trap', 4, 'Criar teste unitário para detectar bug antes de corrígi-lo (por bug).', 0, 0, 25, 5),
(40, 'TDD', 4, 'Criar teste de uma funcionalidade (classe de teste) nova antes de escrever o código (TDD).', 0, 0, 10, 7),
(41, 'System Test', 4, 'Auxiliar no teste de sistema junto com os QA''s.', 0, 0, 100, 0),
(42, 'Improve Unit Test', 4, 'Melhorar teste unitário existente.', 0, 0, 5, 11),
(43, 'Pair Testing', 4, 'Executar atividade de teste em par. Deve ser ganho junto com outra atividade de teste.', 0, 0, 10, 28),
(44, 'JUnit Test', 4, 'Criar teste unitário utilizando JUnit (por classe).', 0, 0, 5, 31),
(45, 'PHPUnit Test', 4, 'Criar teste unitário utilizando PHPUnit (por classe).', 0, 0, 5, 25),
(46, 'Send Article', 2, 'Enviar artigo para a equipe por e-mail (1 por dia no máximo).', 0, 0, 10, 1),
(52, 'Estimation', 2, 'Executar Planning Poker para estimar user stories do backlog (uma vez por dia).', 0, 0, 3, 44),
(54, 'Read Article 2', 3, 'Ler artigo sobre um dos seguintes protocolos: TCP, HTTP, HTTPS/SSL, FTP.', 0, 0, 20, 0),
(55, 'Spike', 3, 'Efetuar pesquisa para redução de risco ou dívida técnica.', 0, 1, 0, 1),
(56, 'API Improvements', 3, 'Melhorar API ou criar utilitários para agilizar a programação.', 0, 1, 5, 5),
(57, 'Test Inspection', 4, 'Ser o responsável pelos testes durante um Sprint.', 0, 1, 20, 1),
(58, 'DoD Check', 2, 'Ao concluir o desenvolvimento de uma User Story, identificar/mostrar que todos os itens da DoD foram verificados.', 0, 1, 10, 13),
(59, 'Brainstorm', 2, 'Conduzir sessão de brainstorming.', 0, 0, 20, 0),
(61, 'Demonstration', 2, 'Demonstrar funcionalidade na reunião de revisão.', 0, 1, 10, 0),
(62, 'Iteration Plan', 2, 'Colaborar na construção do planejamento do Sprint.', 0, 1, 15, 2),
(63, 'Retrospective Action', 2, 'Concluir atividade identificada na reunião de retrospectiva.', 0, 1, 30, 0),
(64, 'Integration Bug Trap', 4, 'Criar teste de integração para detectar bug antes de corrígi-lo.', 0, 1, 40, 0),
(65, 'Build Automation', 4, 'Automatizar build de algum projeto.', 0, 1, 100, 0),
(66, 'Test Automation', 4, 'Automatizar execução de testes de algum projeto.', 0, 1, 100, 0),
(67, 'Deploy Automation', 4, 'Automatizar deploy (em ambiente de desenvolvimento) de alguma aplicação.', 0, 1, 100, 0),
(68, 'testestestsets', 1, 'sadfasdfasdfasdfa lkuhdf alsdkhudf laskdhjf asdlkhjf alsdkjfh\r\n', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `activity_leaderboards`
--
CREATE TABLE IF NOT EXISTS `activity_leaderboards` (
`count` bigint(21)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `activity_leaderboards_last_month`
--
CREATE TABLE IF NOT EXISTS `activity_leaderboards_last_month` (
`count` bigint(21)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `activity_leaderboards_last_week`
--
CREATE TABLE IF NOT EXISTS `activity_leaderboards_last_week` (
`count` bigint(21)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `activity_leaderboards_this_month`
--
CREATE TABLE IF NOT EXISTS `activity_leaderboards_this_month` (
`count` bigint(21)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `activity_leaderboards_this_week`
--
CREATE TABLE IF NOT EXISTS `activity_leaderboards_this_week` (
`count` bigint(21)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `activity_ranking`
--
CREATE TABLE IF NOT EXISTS `activity_ranking` (
`count` bigint(21)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `activity_requisite`
--

CREATE TABLE IF NOT EXISTS `activity_requisite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `prerequisite_badge_id` (`badge_id`),
  KEY `prerequisite_activity_id` (`activity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=113 ;

--
-- Extraindo dados da tabela `activity_requisite`
--

INSERT INTO `activity_requisite` (`id`, `badge_id`, `activity_id`, `count`) VALUES
(26, 1, 3, 10),
(27, 2, 2, 50),
(28, 5, 11, 20),
(29, 6, 8, 5),
(30, 8, 9, 40),
(31, 9, 7, 10),
(33, 10, 5, 10),
(34, 11, 17, 20),
(35, 12, 12, 10),
(36, 13, 46, 10),
(37, 14, 19, 5),
(38, 15, 46, 10),
(39, 15, 19, 5),
(40, 16, 15, 30),
(41, 17, 16, 10),
(42, 18, 18, 3),
(43, 19, 16, 10),
(44, 19, 18, 3),
(45, 20, 13, 5),
(46, 21, 52, 30),
(47, 22, 59, 5),
(48, 23, 20, 3),
(49, 23, 52, 20),
(50, 23, 59, 5),
(51, 24, 21, 5),
(52, 25, 63, 15),
(54, 26, 62, 10),
(55, 28, 61, 15),
(56, 29, 58, 30),
(57, 30, 63, 10),
(58, 30, 62, 10),
(59, 30, 61, 10),
(60, 30, 58, 10),
(61, 31, 20, 5),
(62, 31, 21, 5),
(63, 31, 58, 10),
(64, 32, 23, 30),
(65, 36, 36, 30),
(66, 37, 27, 1),
(67, 37, 35, 3),
(68, 38, 30, 1),
(69, 39, 31, 1),
(70, 40, 28, 1),
(73, 41, 32, 1),
(74, 41, 56, 10),
(75, 42, 25, 1),
(76, 43, 26, 1),
(77, 43, 23, 10),
(78, 44, 23, 50),
(79, 44, 36, 30),
(80, 45, 24, 5),
(81, 45, 35, 2),
(82, 46, 56, 10),
(83, 46, 54, 4),
(84, 47, 29, 1),
(85, 47, 55, 5),
(86, 48, 56, 20),
(87, 48, 23, 30),
(88, 48, 55, 5),
(89, 49, 34, 1),
(90, 49, 23, 50),
(91, 49, 36, 20),
(92, 50, 43, 20),
(93, 51, 41, 3),
(94, 52, 39, 20),
(95, 53, 64, 10),
(96, 54, 39, 20),
(97, 54, 64, 10),
(98, 55, 37, 50),
(99, 56, 40, 40),
(102, 58, 44, 50),
(103, 57, 37, 25),
(104, 57, 40, 20),
(105, 59, 45, 50),
(106, 60, 44, 25),
(107, 60, 45, 25),
(108, 62, 65, 3),
(109, 63, 66, 3),
(110, 64, 67, 2),
(111, 69, 57, 5),
(112, 66, 42, 30);

-- --------------------------------------------------------

--
-- Estrutura da tabela `badge`
--

CREATE TABLE IF NOT EXISTS `badge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` smallint(5) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `abbr` varchar(3) DEFAULT NULL,
  `new` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `icon` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_name` (`name`),
  UNIQUE KEY `badge_abbr` (`abbr`),
  KEY `badge_domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=70 ;

--
-- Extraindo dados da tabela `badge`
--

INSERT INTO `badge` (`id`, `code`, `name`, `domain_id`, `abbr`, `new`, `icon`) VALUES
(1, 101, 'Glossarier', 1, 'GLO', 0, 'fa fa-book'),
(2, 102, 'Code Clarifier', 1, 'CCL', 0, 'fa fa-code'),
(5, 104, 'Acceptance Collector', 1, 'ACC', 0, 'entypo entypo-docs'),
(6, 105, 'Personificator', 1, 'PER', 0, 'fa fa-users'),
(8, 106, 'API Documentator', 1, 'API', 0, 'fa fa-code-fork'),
(9, 107, 'Drawer', 1, 'DRW', 0, 'fa fa-picture-o'),
(10, 108, 'Inquisitor', 1, 'INQ', 0, 'entypo entypo-target'),
(11, 201, 'Coffeeholic', 2, 'COF', 0, 'fa fa-coffee'),
(12, 202, 'Gauderio', 2, 'GAU', 0, 'fa fa-filter'),
(13, 203, 'Spammer', 2, 'SPM', 0, 'fa fa-exclamation-circle'),
(14, 204, 'F1', 2, 'F1', 0, 'fa fa-question'),
(15, 205, 'Information Disseminator', 2, 'INF', 0, 'fa fa-info'),
(16, 206, 'Pairer', 2, 'PAR', 0, 'entypo entypo-users'),
(17, 207, 'Single Coach', 2, 'SIN', 0, 'glyphicon glyphicon-pencil'),
(18, 208, 'Panelist', 2, 'PAN', 0, 'entypo entypo-graduation-cap'),
(19, 209, 'Coach', 2, 'COA', 0, 'entypo entypo-language'),
(20, 210, 'Librarian', 2, 'LIB', 0, 'entypo entypo-book'),
(21, 211, 'Grooman', 2, 'GRO', 0, 'fa fa-th'),
(22, 212, 'Brainstormer', 2, 'BRA', 0, 'entypo-cloud-thunder'),
(23, 213, 'Retrospective Leader', 2, 'REL', 0, 'glyphicon glyphicon-bullhorn'),
(24, 214, 'Radiator Updater', 2, 'RAD', 0, 'glyphicon glyphicon-stats'),
(25, 215, 'Retrospective Adept', 2, 'REA', 0, 'entypo entypo-clipboard'),
(26, 216, 'Iteration Planner', 2, 'IPL', 0, 'glyphicon glyphicon-refresh'),
(28, 218, 'Demonstrator', 2, 'DEM', 0, 'glyphicon glyphicon-phone'),
(29, 219, 'DoD Lunatic', 2, 'DOD', 0, 'glyphicon glyphicon-check'),
(30, 220, 'Scrum Practitioner', 2, 'SCR', 0, 'entypo entypo-play'),
(31, 221, 'Agile Practitioner', 2, 'APR', 0, 'entypo entypo-fast-forward'),
(32, 301, 'Refactor Apprentice', 3, 'RFA', 0, 'entypo entypo-cog'),
(36, 302, 'Pair Refactorer', 3, 'PRF', 0, 'entypo entypo-users'),
(37, 303, 'JavaScript Developer', 3, 'JSS', 0, 'entypo entypo-code'),
(38, 304, 'HTML Builder', 3, 'HTM', 0, 'fa fa-html5'),
(39, 305, 'CSS Expert', 3, 'CSS', 0, 'fa fa-css3'),
(40, 306, 'PHP Developer', 3, 'PHP', 0, 'fa fa-terminal'),
(41, 307, 'RESTfull Developer', 3, 'RST', 0, 'fa fa-code'),
(42, 308, 'Database Developer', 3, 'DBD', 0, 'entypo entypo-database'),
(43, 309, 'Java Developer', 3, 'JAV', 0, 'fa fa-coffee'),
(44, 310, 'Refactor Master', 3, 'RFM', 0, 'fa fa-cogs'),
(45, 311, 'Frontend Developer', 3, 'FED', 0, 'fa fa-picture-o'),
(46, 312, 'Backend Developer', 3, 'BED', 0, 'fa fa-hdd-o'),
(47, 313, 'Mobile Developer', 3, 'MOB', 0, 'entypo entypo-mobile'),
(48, 314, 'Web Developer', 3, 'WEB', 0, 'entypo entypo-globe'),
(49, 315, 'OO Programmer', 3, 'OOP', 0, 'fa fa-plus'),
(50, 401, 'Pair Test Professional', 4, 'PTP', 0, 'entypo entypo-users'),
(51, 402, 'System Tester', 4, 'SYS', 0, 'fa fa-spinner'),
(52, 403, 'Unit Bug Trapper', 4, 'UBT', 0, 'fa fa-bug'),
(53, 404, 'Integration Bug Trapper', 4, 'IBT', 0, 'fa fa-bug'),
(54, 405, 'Insecticide', 4, 'SEC', 0, 'fa fa-bug'),
(55, 406, 'Unit Tester', 4, 'UNT', 0, 'entypo entypo-suitcase'),
(56, 407, 'Test-Driven Developer', 4, 'TDD', 0, 'entypo entypo-cw'),
(57, 408, 'Unit Test-Driven', 4, 'UTD', 0, 'entypo entypo-briefcase'),
(58, 409, 'JUnit Tester', 4, 'JUT', 0, 'fa fa-coffee'),
(59, 410, 'PHPUnit Tester', 4, 'PUT', 0, 'fa fa-terminal'),
(60, 411, 'Framework Tester', 4, 'FRW', 0, 'fa fa-wrench'),
(62, 413, 'Build Automator', 4, 'BAU', 0, 'fa fa-building-o'),
(63, 414, 'Test Automator', 4, 'TAU', 0, 'entypo entypo-lamp'),
(64, 415, 'Automatic Deployer', 4, 'AUD', 0, 'entypo-light-up'),
(65, 416, 'Integrator', 4, 'ITG', 0, 'fa fa-tasks'),
(66, 418, 'Legacy Unit Tester', 4, 'LUT', 0, 'fa fa-shield'),
(69, 421, 'Test Inspector', 4, 'TIR', 0, 'fa fa-crosshairs');

-- --------------------------------------------------------

--
-- Stand-in structure for view `badge_activity_progress`
--
CREATE TABLE IF NOT EXISTS `badge_activity_progress` (
`player_id` int(10) unsigned
,`badge_id` int(10) unsigned
,`activity_id` int(10) unsigned
,`coins_obtained` bigint(21)
,`coins_required` smallint(5) unsigned
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `badge_claimed`
--
CREATE TABLE IF NOT EXISTS `badge_claimed` (
`player_id` int(10) unsigned
,`badge_id` int(10) unsigned
,`claimed` int(1)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `badge_log`
--

CREATE TABLE IF NOT EXISTS `badge_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`badge_id`,`player_id`) USING HASH,
  KEY `fk_badge_log_player_id` (`player_id`),
  KEY `fk_badge_log_badge_id` (`badge_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `badge_requisite`
--

CREATE TABLE IF NOT EXISTS `badge_requisite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `badge_id` int(10) unsigned NOT NULL,
  `badge_id_requisite` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `badge_requisite_badge_id` (`badge_id`),
  KEY `badge_requisite_badge_id_requisite` (`badge_id_requisite`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

--
-- Extraindo dados da tabela `badge_requisite`
--

INSERT INTO `badge_requisite` (`id`, `badge_id`, `badge_id_requisite`) VALUES
(42, 15, 13),
(43, 15, 14),
(44, 19, 19),
(45, 19, 18),
(46, 23, 21),
(47, 23, 22),
(48, 30, 25),
(49, 30, 26),
(50, 30, 28),
(51, 30, 29),
(52, 31, 23),
(53, 31, 24),
(54, 31, 30),
(55, 44, 32),
(56, 44, 36),
(57, 45, 38),
(58, 45, 39),
(59, 45, 37),
(60, 46, 41),
(61, 46, 40),
(62, 47, 43),
(63, 47, 42),
(64, 48, 45),
(65, 48, 46),
(66, 49, 47),
(67, 49, 44),
(68, 54, 52),
(69, 54, 53),
(70, 57, 55),
(71, 57, 56),
(72, 65, 62),
(73, 65, 63),
(74, 65, 64);

-- --------------------------------------------------------

--
-- Stand-in structure for view `calendar_log`
--
CREATE TABLE IF NOT EXISTS `calendar_log` (
`coins` bigint(21)
,`player_id` int(10) unsigned
,`acquired` date
,`domain_id` int(10) unsigned
,`activity_id` int(10) unsigned
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `different_activities_completed`
--
CREATE TABLE IF NOT EXISTS `different_activities_completed` (
`different_activities_completed` bigint(21)
,`domain_id` int(10) unsigned
,`domain_name` varchar(30)
,`player_id` int(10) unsigned
,`player_name` varchar(30)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `domain`
--

CREATE TABLE IF NOT EXISTS `domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `color` char(7) NOT NULL,
  `abbr` char(3) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `domain`
--

INSERT INTO `domain` (`id`, `name`, `color`, `abbr`, `description`, `icon`) VALUES
(1, 'Documentation', '#0083b7', 'DOC', 'Documentation should be barely sufficient. But it is still important.', 'entypo entypo-doc-text'),
(2, 'Collaboration', '#00a45a', 'COL', 'Scrum, XP, Pair Programming, Coffee and more.', 'glyphicon glyphicon-link'),
(3, 'Software Engineering', '#701c1c', 'ENG', 'Programming Languages, Frameworks and Technologies.', 'fa fa-cogs'),
(4, 'Testing', '#f89d00', 'TST', 'Test-Driven Development and Continuous Integration.', 'entypo entypo-tools');

-- --------------------------------------------------------

--
-- Stand-in structure for view `domain_activities_count`
--
CREATE TABLE IF NOT EXISTS `domain_activities_count` (
`domain_id` int(10) unsigned
,`count` bigint(21)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_type_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `description` text NOT NULL,
  `xp` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_event_event_type_id` (`event_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Extraindo dados da tabela `event`
--

INSERT INTO `event` (`id`, `event_type_id`, `name`, `start`, `end`, `description`, `xp`) VALUES
(1, 1, 'Active Mission', '2014-04-05', '2014-04-12', 'Description', 0),
(2, 1, 'Future Mission', '2014-04-12', '2014-05-05', '', 0),
(3, 1, 'Past Mission', '2014-03-05', '2014-03-29', '', 0),
(4, 2, 'Active Challenge', '2014-04-05', '2014-04-12', '', 0),
(5, 2, 'Future Challenge', '2014-04-12', '2014-05-05', '', 0),
(6, 2, 'Past Challenge', '2014-03-05', '2014-03-29', '', 0),
(7, 1, 'Mission Test', '2014-04-05', '2014-04-05', '123123', 0),
(8, 1, 'Another Strange Mission with some large title', '2014-04-02', '2014-04-05', 'asdçkfasdflasdfjhkasdf', 0),
(9, 1, 'Mission One', '2014-04-05', '2014-04-05', '', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_activity`
--

CREATE TABLE IF NOT EXISTS `event_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_event_activity_event_id` (`event_id`),
  KEY `fk_event_activity_activity_id` (`activity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Extraindo dados da tabela `event_activity`
--

INSERT INTO `event_activity` (`id`, `event_id`, `activity_id`, `count`) VALUES
(31, 8, 9, 123),
(32, 8, 12, 1),
(33, 8, 7, 1),
(34, 8, 15, 1),
(35, 8, 15, 1),
(36, 8, 58, 1),
(38, 1, 9, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_activity_log`
--

CREATE TABLE IF NOT EXISTS `event_activity_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_activity_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_event_activity_log_event_activity_id` (`event_activity_id`),
  KEY `fk_event_activity_log_player_id` (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `event_activity_log`
--

INSERT INTO `event_activity_log` (`id`, `event_activity_id`, `player_id`, `creation`) VALUES
(1, 38, 6, '2014-04-05 22:57:58');

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_complete_log`
--

CREATE TABLE IF NOT EXISTS `event_complete_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_event_completed_log_player_id` (`player_id`),
  KEY `fk_event_completed_log_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_join_log`
--

CREATE TABLE IF NOT EXISTS `event_join_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_event_join_log_player_id` (`player_id`),
  KEY `fk_event_join_log_event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Extraindo dados da tabela `event_join_log`
--

INSERT INTO `event_join_log` (`id`, `event_id`, `player_id`, `creation`) VALUES
(9, 8, 5, '2014-04-05 19:38:32'),
(10, 1, 5, '2014-04-05 19:38:39'),
(11, 7, 5, '2014-04-05 19:42:15'),
(12, 9, 5, '2014-04-05 19:43:13'),
(13, 4, 5, '2014-04-05 19:43:38');

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_log`
--

CREATE TABLE IF NOT EXISTS `event_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `event_activity_id` int(10) unsigned DEFAULT NULL,
  `event_task_id` int(10) unsigned DEFAULT NULL,
  `event_completed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `completed_in` date NOT NULL,
  `joined_in` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reviewed` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `player_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`player_id`),
  KEY `fk_event_log_event_id` (`event_id`),
  KEY `fk_event_log_event_task_id` (`event_task_id`),
  KEY `fk_event_log_event_activity_id` (`event_activity_id`),
  KEY `fk_event_log_player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_task`
--

CREATE TABLE IF NOT EXISTS `event_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  `xp` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_event_task_event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

--
-- Extraindo dados da tabela `event_task`
--

INSERT INTO `event_task` (`id`, `name`, `description`, `event_id`, `xp`) VALUES
(37, 'asdfasdf', 'asdfasdf', 8, 0),
(38, 'asdfasdf', 'dfdfd', 8, 0),
(39, 'fdf', 'dfdf', 8, 0),
(40, 'dfasdf', 'dfa', 8, 0),
(47, 'asdfasdf', 'asdf', 7, 0),
(48, 'asdf', 'asdf', 7, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_task_log`
--

CREATE TABLE IF NOT EXISTS `event_task_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_task_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_event_task_log_event_task_id` (`event_task_id`),
  KEY `fk_event_task_log_player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_type`
--

CREATE TABLE IF NOT EXISTS `event_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `level_required` smallint(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `event_type`
--

INSERT INTO `event_type` (`id`, `name`, `level_required`) VALUES
(1, 'Mission', 10),
(2, 'Challenge', 20);

-- --------------------------------------------------------

--
-- Stand-in structure for view `last_week_logs`
--
CREATE TABLE IF NOT EXISTS `last_week_logs` (
`activity_id` int(10) unsigned
,`logs` varbinary(153)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(10) unsigned DEFAULT NULL,
  `activity_code` int(10) NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `player_id` int(10) unsigned NOT NULL,
  `acquired` date NOT NULL,
  `reviewed` timestamp NULL DEFAULT NULL,
  `description` text,
  `domain_id` int(10) unsigned DEFAULT NULL,
  `xp` int(10) unsigned NOT NULL DEFAULT '0',
  `spent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `activity_activity_id` (`activity_id`) USING BTREE,
  KEY `log_player_id` (`player_id`),
  KEY `log_domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=690 ;

--
-- Extraindo dados da tabela `log`
--

INSERT INTO `log` (`id`, `activity_id`, `activity_code`, `creation`, `player_id`, `acquired`, `reviewed`, `description`, `domain_id`, `xp`, `spent`) VALUES
(689, 9, 0, '2014-04-05 22:57:58', 6, '2014-04-05', '2014-04-05 23:06:34', 'asdfasdfasdf', 1, 20, 0);

--
-- Acionadores `log`
--
DROP TRIGGER IF EXISTS `log_au`;
DELIMITER //
CREATE TRIGGER `log_au` AFTER UPDATE ON `log`
 FOR EACH ROW BEGIN

    DECLARE _scrummaster_id INT(10) UNSIGNED DEFAULT NULL;
    DECLARE _developers INT(10) UNSIGNED DEFAULT 0;
    DECLARE _sm_xp INT(10) UNSIGNED DEFAULT 0;

    IF (OLD.reviewed IS NULL AND NEW.reviewed IS NOT NULL) THEN
    	
        SET _scrummaster_id = (SELECT id FROM player WHERE player_type_id = 2 LIMIT 1);
        SET _developers = (SELECT COALESCE(COUNT(*), 0) FROM player WHERE player_type_id = 1);

    	INSERT INTO xp_log SET
    		player_id = NEW.player_id,
    		xp = NEW.xp,
    		activity_id = NEW.activity_id;

        INSERT INTO notification SET
            type = 'success',
            title = 'Activity Reviewed',
            `text`= CONCAT('Your activity was reviewed and you earned ', NEW.xp, ' XP.'),
            player_id = NEW.player_id;

        IF (_developers <> 0 AND _scrummaster_id IS NOT NULL) THEN

            SET _sm_xp = FLOOR(NEW.xp / _developers);
            IF (_sm_xp = 0) THEN
                SET _sm_xp = 1;
            END IF;

            -- O ScrumMaster recebe uma pontuação em XP igual à pontuação do desenvolvedor dividido pelo número de desenvolvedores
            INSERT INTO xp_log SET 
                player_id = _scrummaster_id, -- ScrumMaster
                xp = _sm_xp,
                activity_id_reviewed = NEW.activity_id;

            INSERT INTO notification SET
                type = 'success',
                title = 'Activity Reviewed',
                `text`= CONCAT('You reviewed an activity and earned ', _sm_xp, ' XP.'),
                player_id = _scrummaster_id;

        END IF;

        -- Incrementa o contador "reported" da activity
        UPDATE activity SET reported = reported + 1 WHERE id = NEW.activity_id;

    END IF;

END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `log_bi`;
DELIMITER //
CREATE TRIGGER `log_bi` BEFORE INSERT ON `log`
 FOR EACH ROW BEGIN

	DECLARE _domain_id INT(10) UNSIGNED DEFAULT NULL;
	DECLARE _xp INT(10) UNSIGNED DEFAULT 0;

	SELECT domain_id INTO _domain_id
	FROM activity WHERE id = NEW.activity_id;
	SET NEW.domain_id = _domain_id;

	INSERT INTO timeline SET 
		player_id = NEW.player_id,
		what = 'Activity',
		activity_id = NEW.activity_id,
		domain_id = NEW.domain_id
	;

	SELECT xp INTO _xp FROM activity WHERE id = NEW.activity_id;
	SET NEW.xp = _xp;

END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `player_id` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(30) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'success',
  `action` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notification_player_id` (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=95 ;

--
-- Extraindo dados da tabela `notification`
--

INSERT INTO `notification` (`id`, `text`, `read`, `player_id`, `created`, `title`, `type`, `action`) VALUES
(32, 'It seems our friend has advanced to the next level.', 0, NULL, '2014-03-30 00:48:24', 'texto', 'sound', 'level_up'),
(33, 'Your activity was reviewed and you earned 0 XP.', 1, 1, '2014-04-03 00:39:48', 'Activity Reviewed', 'success', ''),
(34, 'You reviewed an activity and earned 0 XP.', 1, 6, '2014-04-02 22:21:11', 'Activity Reviewed', 'success', ''),
(35, 'Your activity was reviewed and you earned 20 XP.', 1, 1, '2014-04-05 12:03:23', 'Activity Reviewed', 'success', ''),
(36, 'You reviewed an activity and earned 4 XP.', 1, 6, '2014-04-03 00:40:09', 'Activity Reviewed', 'success', ''),
(37, 'Your activity was reviewed and you earned 30 XP.', 1, 6, '2014-04-04 23:39:50', 'Activity Reviewed', 'success', ''),
(38, 'You reviewed an activity and earned 6 XP.', 1, 6, '2014-04-04 23:39:50', 'Activity Reviewed', 'success', ''),
(39, 'Your activity was reviewed and you earned 30 XP.', 1, 6, '2014-04-04 23:39:52', 'Activity Reviewed', 'success', ''),
(40, 'You reviewed an activity and earned 6 XP.', 1, 6, '2014-04-04 23:39:52', 'Activity Reviewed', 'success', ''),
(41, 'Your activity was reviewed and you earned 0 XP.', 1, 6, '2014-04-04 23:39:54', 'Activity Reviewed', 'success', ''),
(42, 'You reviewed an activity and earned 1 XP.', 1, 6, '2014-04-04 23:39:54', 'Activity Reviewed', 'success', ''),
(43, 'Your activity was reviewed and you earned 20 XP.', 1, 6, '2014-04-04 23:49:33', 'Activity Reviewed', 'success', ''),
(44, 'You reviewed an activity and earned 4 XP.', 1, 6, '2014-04-04 23:49:33', 'Activity Reviewed', 'success', ''),
(45, '306 advanced to level 2!', 1, 1, '2014-04-05 12:03:23', 'Level Up', 'warning', ''),
(46, '306 advanced to level 2!', 0, 2, '2014-04-04 23:55:24', 'Level Up', 'warning', ''),
(47, '306 advanced to level 2!', 0, 3, '2014-04-04 23:55:24', 'Level Up', 'warning', ''),
(48, '306 advanced to level 2!', 0, 4, '2014-04-04 23:55:24', 'Level Up', 'warning', ''),
(49, '306 advanced to level 2!', 1, 5, '2014-04-05 19:10:19', 'Level Up', 'warning', ''),
(50, '306 advanced to level 2!', 1, 6, '2014-04-04 23:55:24', 'Level Up', 'warning', ''),
(51, 'Your activity was reviewed and you earned 100 XP.', 1, 6, '2014-04-04 23:55:24', 'Activity Reviewed', 'success', ''),
(52, 'You reviewed an activity and earned 20 XP.', 1, 6, '2014-04-04 23:55:24', 'Activity Reviewed', 'success', ''),
(53, 'Your activity was reviewed and you earned 30 XP.', 1, 6, '2014-04-04 23:55:44', 'Activity Reviewed', 'success', ''),
(54, 'You reviewed an activity and earned 6 XP.', 1, 6, '2014-04-04 23:55:44', 'Activity Reviewed', 'success', ''),
(55, 'Your activity was reviewed and you earned 2 XP.', 1, 6, '2014-04-04 23:55:46', 'Activity Reviewed', 'success', ''),
(56, 'You reviewed an activity and earned 1 XP.', 1, 6, '2014-04-04 23:55:46', 'Activity Reviewed', 'success', ''),
(57, 'Your activity was reviewed and you earned 5 XP.', 1, 6, '2014-04-04 23:55:49', 'Activity Reviewed', 'success', ''),
(58, 'You reviewed an activity and earned 1 XP.', 1, 6, '2014-04-04 23:55:49', 'Activity Reviewed', 'success', ''),
(59, 'Your activity was reviewed and you earned 15 XP.', 1, 6, '2014-04-04 23:56:27', 'Activity Reviewed', 'success', ''),
(60, 'You reviewed an activity and earned 3 XP.', 1, 6, '2014-04-04 23:56:27', 'Activity Reviewed', 'success', ''),
(61, 'Davi advanced to level 4!', 1, 1, '2014-04-05 12:03:23', 'Level Up', 'warning', ''),
(62, 'Davi advanced to level 4!', 0, 2, '2014-04-04 23:56:36', 'Level Up', 'warning', ''),
(63, 'Davi advanced to level 4!', 0, 3, '2014-04-04 23:56:36', 'Level Up', 'warning', ''),
(64, 'Davi advanced to level 4!', 0, 4, '2014-04-04 23:56:36', 'Level Up', 'warning', ''),
(65, 'Davi advanced to level 4!', 1, 5, '2014-04-05 19:10:19', 'Level Up', 'warning', ''),
(66, 'Davi advanced to level 4!', 1, 6, '2014-04-04 23:56:36', 'Level Up', 'warning', ''),
(67, 'Your activity was reviewed and you earned 50 XP.', 1, 6, '2014-04-04 23:56:36', 'Activity Reviewed', 'success', ''),
(68, 'You reviewed an activity and earned 10 XP.', 1, 6, '2014-04-04 23:56:36', 'Activity Reviewed', 'success', ''),
(69, 'Vinícius advanced to level 7!', 0, 1, '2014-04-05 19:34:15', 'Level Up', 'warning', ''),
(70, 'Vinícius advanced to level 7!', 0, 2, '2014-04-05 19:34:15', 'Level Up', 'warning', ''),
(71, 'Vinícius advanced to level 7!', 0, 3, '2014-04-05 19:34:15', 'Level Up', 'warning', ''),
(72, 'Vinícius advanced to level 7!', 0, 4, '2014-04-05 19:34:15', 'Level Up', 'warning', ''),
(73, 'Vinícius advanced to level 7!', 1, 5, '2014-04-05 19:34:19', 'Level Up', 'warning', ''),
(74, 'Vinícius advanced to level 7!', 1, 6, '2014-04-05 19:44:34', 'Level Up', 'warning', ''),
(75, 'Vinícius advanced to level 9!', 0, 1, '2014-04-05 19:34:32', 'Level Up', 'warning', ''),
(76, 'Vinícius advanced to level 9!', 0, 2, '2014-04-05 19:34:32', 'Level Up', 'warning', ''),
(77, 'Vinícius advanced to level 9!', 0, 3, '2014-04-05 19:34:32', 'Level Up', 'warning', ''),
(78, 'Vinícius advanced to level 9!', 0, 4, '2014-04-05 19:34:32', 'Level Up', 'warning', ''),
(79, 'Vinícius advanced to level 9!', 1, 5, '2014-04-05 19:34:34', 'Level Up', 'warning', ''),
(80, 'Vinícius advanced to level 9!', 1, 6, '2014-04-05 19:44:34', 'Level Up', 'warning', ''),
(81, 'Vinícius reached level 10 and can now join Missions!', 0, 1, '2014-04-05 19:34:46', 'Level Up - Missions Unlocked', 'warning', ''),
(82, 'Vinícius reached level 10 and can now join Missions!', 0, 2, '2014-04-05 19:34:46', 'Level Up - Missions Unlocked', 'warning', ''),
(83, 'Vinícius reached level 10 and can now join Missions!', 0, 3, '2014-04-05 19:34:46', 'Level Up - Missions Unlocked', 'warning', ''),
(84, 'Vinícius reached level 10 and can now join Missions!', 0, 4, '2014-04-05 19:34:46', 'Level Up - Missions Unlocked', 'warning', ''),
(85, 'Vinícius reached level 10 and can now join Missions!', 1, 5, '2014-04-05 19:34:48', 'Level Up - Missions Unlocked', 'warning', ''),
(86, 'Vinícius reached level 10 and can now join Missions!', 1, 6, '2014-04-05 19:44:34', 'Level Up - Missions Unlocked', 'warning', ''),
(87, 'Vinícius advanced to level 26!', 0, 1, '2014-04-05 19:43:29', 'Level Up', 'warning', ''),
(88, 'Vinícius advanced to level 26!', 0, 2, '2014-04-05 19:43:29', 'Level Up', 'warning', ''),
(89, 'Vinícius advanced to level 26!', 0, 3, '2014-04-05 19:43:29', 'Level Up', 'warning', ''),
(90, 'Vinícius advanced to level 26!', 0, 4, '2014-04-05 19:43:29', 'Level Up', 'warning', ''),
(91, 'Vinícius advanced to level 26!', 1, 5, '2014-04-05 19:43:31', 'Level Up', 'warning', ''),
(92, 'Vinícius advanced to level 26!', 1, 6, '2014-04-05 19:44:34', 'Level Up', 'warning', ''),
(93, 'Your activity was reviewed and you earned 20 XP.', 1, 6, '2014-04-05 23:06:34', 'Activity Reviewed', 'success', ''),
(94, 'You reviewed an activity and earned 4 XP.', 1, 6, '2014-04-05 23:06:34', 'Activity Reviewed', 'success', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `player`
--

CREATE TABLE IF NOT EXISTS `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `player_type_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(40) NOT NULL,
  `xp` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_player_type_id` (`player_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Extraindo dados da tabela `player`
--

INSERT INTO `player` (`id`, `name`, `player_type_id`, `email`, `password`, `xp`) VALUES
(1, 'André', 1, 'andre@versul.com.br', '24bae80ca7f5a1fd95e9ae0388b7e79bdb9b7c0d', 1955),
(2, 'Cristian', 1, 'cristian.dietrich@versul.com.br', '24bae80ca7f5a1fd95e9ae0388b7e79bdb9b7c0d', 362),
(3, 'Diego', 1, 'diego.vargas@versul.com.br', '24bae80ca7f5a1fd95e9ae0388b7e79bdb9b7c0d', 704),
(4, 'Eduardo', 1, 'eduardok@versul.com.br', '24bae80ca7f5a1fd95e9ae0388b7e79bdb9b7c0d', 725),
(5, 'Vinícius', 1, 'vinicius@versul.com.br', '24bae80ca7f5a1fd95e9ae0388b7e79bdb9b7c0d', 12799),
(6, 'Davi', 2, 'davi@versul.com.br', '24bae80ca7f5a1fd95e9ae0388b7e79bdb9b7c0d', 573);

-- --------------------------------------------------------

--
-- Stand-in structure for view `player_activity_coins`
--
CREATE TABLE IF NOT EXISTS `player_activity_coins` (
`player_id` int(10) unsigned
,`player_name` varchar(30)
,`coins` bigint(21)
,`spent` decimal(25,0)
,`remaining` decimal(26,0)
,`activity_id` int(10) unsigned
,`log_reviewed` timestamp
,`activity_name` varchar(30)
,`activity_description` text
,`domain_id` int(10) unsigned
,`domain_name` varchar(30)
,`domain_abbr` char(3)
,`domain_color` char(7)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `player_total_activity_coins`
--
CREATE TABLE IF NOT EXISTS `player_total_activity_coins` (
`player_id` int(10) unsigned
,`coins` bigint(21)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `player_type`
--

CREATE TABLE IF NOT EXISTS `player_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `player_type`
--

INSERT INTO `player_type` (`id`, `name`) VALUES
(1, 'Developer'),
(2, 'ScrumMaster'),
(3, 'Product Owner');

-- --------------------------------------------------------

--
-- Estrutura da tabela `timeline`
--

CREATE TABLE IF NOT EXISTS `timeline` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1234 ;

--
-- Extraindo dados da tabela `timeline`
--

INSERT INTO `timeline` (`id`, `player_id`, `what`, `when`, `badge_id`, `activity_id`, `domain_id`) VALUES
(1, 1, 'Activity', '2014-01-21 11:32:01', NULL, 2, 1),
(2, 1, 'Activity', '2014-01-21 11:32:01', NULL, 37, 4),
(3, 1, 'Activity', '2014-01-21 11:32:02', NULL, 45, 4),
(4, 1, 'Activity', '2014-01-21 11:32:03', NULL, 40, 4),
(5, 1, 'Activity', '2014-01-21 11:32:03', NULL, 43, 4),
(6, 1, 'Activity', '2014-01-21 11:42:47', NULL, 10, 1),
(7, 1, 'Activity', '2014-01-21 11:42:48', NULL, 17, 2),
(8, 2, 'Activity', '2014-01-21 11:43:03', NULL, 2, 1),
(9, 2, 'Activity', '2014-01-21 11:43:03', NULL, 37, 4),
(10, 2, 'Activity', '2014-01-21 11:43:04', NULL, 45, 4),
(11, 2, 'Activity', '2014-01-21 11:43:04', NULL, 40, 4),
(12, 2, 'Activity', '2014-01-21 11:43:04', NULL, 43, 4),
(13, 2, 'Activity', '2014-01-21 11:43:05', NULL, 10, 1),
(14, 2, 'Activity', '2014-01-21 11:43:13', NULL, 17, 2),
(15, 3, 'Activity', '2014-01-21 11:43:54', NULL, 2, 1),
(16, 3, 'Activity', '2014-01-21 11:43:55', NULL, 2, 1),
(17, 3, 'Activity', '2014-01-21 11:43:56', NULL, 2, 1),
(18, 4, 'Activity', '2014-01-21 11:45:08', NULL, 2, 1),
(19, 4, 'Activity', '2014-01-21 11:45:13', NULL, 2, 1),
(20, 4, 'Activity', '2014-01-21 11:45:44', NULL, 3, 1),
(21, 4, 'Activity', '2014-01-21 11:45:44', NULL, 23, 3),
(22, 4, 'Activity', '2014-01-21 11:45:45', NULL, 37, 4),
(23, 4, 'Activity', '2014-01-21 11:45:45', NULL, 45, 4),
(24, 4, 'Activity', '2014-01-21 11:45:46', NULL, 42, 4),
(25, 5, 'Activity', '2014-01-21 11:46:34', NULL, 19, 2),
(26, 5, 'Activity', '2014-01-21 11:46:35', NULL, 16, 2),
(27, 5, 'Activity', '2014-01-21 11:46:35', NULL, 2, 1),
(28, 5, 'Activity', '2014-01-21 11:46:36', NULL, 2, 1),
(29, 1, 'Activity', '2014-01-21 20:20:42', NULL, 39, 4),
(30, 1, 'Activity', '2014-01-21 20:21:09', NULL, 14, 2),
(31, 4, 'Activity', '2014-01-22 10:09:49', NULL, 52, 2),
(32, 4, 'Activity', '2014-01-22 10:10:42', NULL, 2, 1),
(33, 4, 'Activity', '2014-01-22 10:11:05', NULL, 2, 1),
(34, 4, 'Activity', '2014-01-22 10:11:27', NULL, 37, 4),
(35, 1, 'Activity', '2014-01-22 10:11:40', NULL, 37, 4),
(36, 4, 'Activity', '2014-01-22 10:19:23', NULL, 45, 4),
(37, 1, 'Activity', '2014-01-22 10:27:07', NULL, 52, 2),
(38, 1, 'Activity', '2014-01-22 10:27:23', NULL, 15, 2),
(39, 3, 'Activity', '2014-01-22 10:30:19', NULL, 23, 3),
(40, 3, 'Activity', '2014-01-22 19:54:39', NULL, 17, 2),
(41, 5, 'Activity', '2014-01-22 19:54:51', NULL, 23, 3),
(42, 5, 'Activity', '2014-01-22 19:55:39', NULL, 37, 4),
(43, 5, 'Activity', '2014-01-22 19:56:08', NULL, 43, 4),
(44, 5, 'Activity', '2014-01-22 19:56:33', NULL, 52, 2),
(45, 2, 'Activity', '2014-01-22 19:57:06', NULL, 52, 2),
(46, 5, 'Activity', '2014-01-22 19:57:24', NULL, 2, 1),
(47, 5, 'Activity', '2014-01-22 19:58:41', NULL, 23, 3),
(48, 5, 'Activity', '2014-01-22 20:01:03', NULL, 17, 2),
(49, 1, 'Activity', '2014-01-23 09:55:45', NULL, 15, 2),
(50, 2, 'Activity', '2014-01-23 09:56:04', NULL, 15, 2),
(51, 4, 'Activity', '2014-01-23 09:59:30', NULL, 2, 1),
(52, 2, 'Activity', '2014-01-23 09:59:49', NULL, 15, 2),
(53, 4, 'Activity', '2014-01-23 09:59:55', NULL, 1, 1),
(54, 4, 'Activity', '2014-01-23 10:00:27', NULL, 1, 1),
(55, 4, 'Activity', '2014-01-23 10:00:51', NULL, 37, 4),
(56, 4, 'Activity', '2014-01-23 10:01:24', NULL, 37, 4),
(57, 4, 'Activity', '2014-01-23 10:02:02', NULL, 45, 4),
(58, 1, 'Activity', '2014-01-23 10:02:22', NULL, 45, 4),
(59, 5, 'Activity', '2014-01-23 17:06:41', NULL, 55, 3),
(60, 3, 'Activity', '2014-01-23 19:55:14', NULL, 56, 3),
(61, 2, 'Activity', '2014-01-23 19:59:22', NULL, 15, 2),
(62, 1, 'Activity', '2014-01-23 19:59:32', NULL, 15, 2),
(63, 1, 'Activity', '2014-01-23 20:00:36', NULL, 14, 2),
(64, 5, 'Activity', '2014-01-23 20:00:43', NULL, 17, 2),
(65, 4, 'Activity', '2014-01-24 09:55:43', NULL, 2, 1),
(66, 4, 'Activity', '2014-01-24 09:56:06', NULL, 23, 3),
(67, 4, 'Activity', '2014-01-24 09:56:30', NULL, 37, 4),
(68, 4, 'Activity', '2014-01-24 09:56:53', NULL, 45, 4),
(69, 1, 'Activity', '2014-01-27 10:40:21', NULL, 39, 4),
(70, 1, 'Activity', '2014-01-27 10:40:39', NULL, 14, 2),
(71, 1, 'Activity', '2014-01-27 10:41:35', NULL, 42, 4),
(72, 2, 'Activity', '2014-01-27 10:42:44', NULL, 42, 4),
(73, 2, 'Activity', '2014-01-27 10:43:06', NULL, 39, 4),
(74, 4, 'Activity', '2014-01-27 11:01:34', NULL, 12, 2),
(75, 4, 'Activity', '2014-01-27 11:02:01', NULL, 23, 3),
(76, 4, 'Activity', '2014-01-27 11:02:42', NULL, 37, 4),
(77, 5, 'Activity', '2014-01-27 11:32:46', NULL, 35, 3),
(78, 1, 'Activity', '2014-01-27 15:58:25', NULL, 52, 2),
(79, 5, 'Activity', '2014-01-28 09:57:13', NULL, 15, 2),
(80, 5, 'Activity', '2014-01-28 09:59:04', NULL, 37, 4),
(81, 5, 'Activity', '2014-01-28 09:59:34', NULL, 37, 4),
(82, 5, 'Activity', '2014-01-28 09:59:51', NULL, 43, 4),
(83, 4, 'Activity', '2014-01-28 10:03:34', NULL, 2, 1),
(84, 4, 'Activity', '2014-01-28 10:03:55', NULL, 23, 3),
(85, 4, 'Activity', '2014-01-28 10:04:16', NULL, 37, 4),
(86, 4, 'Activity', '2014-01-28 10:04:28', NULL, 45, 4),
(87, 1, 'Activity', '2014-01-28 10:19:27', NULL, 15, 2),
(88, 2, 'Activity', '2014-01-28 10:19:46', NULL, 15, 2),
(89, 3, 'Activity', '2014-01-28 11:54:57', NULL, 15, 2),
(90, 3, 'Activity', '2014-01-28 11:55:40', NULL, 44, 4),
(91, 3, 'Activity', '2014-01-28 11:56:03', NULL, 43, 4),
(92, 3, 'Activity', '2014-01-28 11:58:47', NULL, 52, 2),
(93, 2, 'Activity', '2014-01-28 11:59:34', NULL, 52, 2),
(94, 5, 'Activity', '2014-01-28 16:31:37', NULL, 12, 2),
(95, 5, 'Activity', '2014-01-28 16:31:49', NULL, 17, 2),
(96, 5, 'Activity', '2014-01-28 16:32:50', NULL, 52, 2),
(97, 5, 'Activity', '2014-01-28 19:56:33', NULL, 23, 3),
(98, 5, 'Activity', '2014-01-28 19:57:03', NULL, 44, 4),
(99, 5, 'Activity', '2014-01-28 19:57:56', NULL, 44, 4),
(100, 1, 'Activity', '2014-01-28 19:58:23', NULL, 43, 4),
(101, 5, 'Activity', '2014-01-28 19:58:58', NULL, 7, 1),
(102, 1, 'Activity', '2014-01-29 09:49:46', NULL, 15, 2),
(103, 1, 'Activity', '2014-01-29 09:50:00', NULL, 14, 2),
(104, 1, 'Activity', '2014-01-29 09:53:57', NULL, 56, 3),
(105, 3, 'Activity', '2014-01-29 09:55:04', NULL, 15, 2),
(106, 1, 'Activity', '2014-01-29 09:55:29', NULL, 23, 3),
(107, 1, 'Activity', '2014-01-29 09:55:57', NULL, 36, 3),
(108, 3, 'Activity', '2014-01-29 09:56:33', NULL, 1, 1),
(109, 1, 'Activity', '2014-01-29 09:57:07', NULL, 56, 3),
(110, 5, 'Activity', '2014-01-29 10:04:22', NULL, 37, 4),
(111, 5, 'Activity', '2014-01-29 10:04:41', NULL, 37, 4),
(112, 4, 'Activity', '2014-01-29 10:10:41', NULL, 12, 2),
(113, 3, 'Activity', '2014-01-29 11:20:06', NULL, 37, 4),
(114, 3, 'Activity', '2014-01-29 11:20:19', NULL, 44, 4),
(115, 3, 'Activity', '2014-01-29 15:40:04', NULL, 7, 1),
(116, 3, 'Activity', '2014-01-29 15:45:20', NULL, 1, 1),
(117, 3, 'Activity', '2014-01-29 16:00:00', NULL, 17, 2),
(118, 3, 'Activity', '2014-01-29 16:39:47', NULL, 7, 1),
(119, 3, 'Activity', '2014-01-29 16:40:37', NULL, 1, 1),
(120, 2, 'Activity', '2014-01-29 16:44:26', NULL, 15, 2),
(121, 2, 'Activity', '2014-01-29 16:44:50', NULL, 43, 4),
(122, 2, 'Activity', '2014-01-29 16:45:35', NULL, 23, 3),
(123, 2, 'Activity', '2014-01-29 16:46:15', NULL, 36, 3),
(124, 1, 'Activity', '2014-01-29 20:27:44', NULL, 45, 4),
(125, 1, 'Activity', '2014-01-29 20:28:16', NULL, 40, 4),
(126, 1, 'Activity', '2014-01-29 20:28:41', NULL, 37, 4),
(127, 1, 'Activity', '2014-01-29 20:29:25', NULL, 2, 1),
(128, 4, 'Activity', '2014-01-30 13:07:15', NULL, 4, 1),
(129, 4, 'Activity', '2014-01-30 13:07:43', NULL, 9, 1),
(130, 4, 'Activity', '2014-01-30 13:07:58', NULL, 56, 3),
(131, 4, 'Activity', '2014-01-30 13:08:17', NULL, 39, 4),
(132, 4, 'Activity', '2014-01-30 13:08:33', NULL, 39, 4),
(133, 5, 'Activity', '2014-01-30 14:45:19', NULL, 17, 2),
(134, 3, 'Activity', '2014-01-30 17:41:54', NULL, 17, 2),
(135, 2, 'Activity', '2014-01-30 21:12:31', NULL, 14, 2),
(136, 4, 'Activity', '2014-01-31 12:07:32', NULL, 1, 1),
(137, 4, 'Activity', '2014-01-31 12:07:52', NULL, 1, 1),
(138, 4, 'Activity', '2014-01-31 12:09:13', NULL, 1, 1),
(139, 4, 'Activity', '2014-01-31 12:09:27', NULL, 1, 1),
(140, 4, 'Activity', '2014-01-31 12:09:47', NULL, 1, 1),
(141, 4, 'Activity', '2014-01-31 12:10:27', NULL, 1, 1),
(142, 4, 'Activity', '2014-01-31 12:10:41', NULL, 1, 1),
(143, 4, 'Activity', '2014-01-31 12:10:53', NULL, 1, 1),
(144, 4, 'Activity', '2014-01-31 12:11:03', NULL, 1, 1),
(145, 4, 'Activity', '2014-01-31 12:11:34', NULL, 9, 1),
(146, 4, 'Activity', '2014-01-31 12:11:50', NULL, 9, 1),
(147, 4, 'Activity', '2014-01-31 12:12:07', NULL, 9, 1),
(148, 4, 'Activity', '2014-01-31 12:12:22', NULL, 9, 1),
(149, 4, 'Activity', '2014-01-31 12:12:53', NULL, 9, 1),
(150, 4, 'Activity', '2014-01-31 12:13:06', NULL, 9, 1),
(151, 4, 'Activity', '2014-01-31 12:13:20', NULL, 9, 1),
(152, 4, 'Activity', '2014-01-31 12:13:30', NULL, 9, 1),
(153, 4, 'Activity', '2014-01-31 12:13:40', NULL, 9, 1),
(154, 4, 'Activity', '2014-01-31 12:14:33', NULL, 12, 2),
(155, 4, 'Activity', '2014-01-31 12:14:59', NULL, 42, 4),
(156, 4, 'Activity', '2014-01-31 12:15:17', NULL, 45, 4),
(157, 5, 'Activity', '2014-01-31 16:55:10', NULL, 52, 2),
(158, 1, 'Activity', '2014-01-31 16:58:50', NULL, 11, 1),
(159, 1, 'Activity', '2014-02-03 10:21:13', NULL, 52, 2),
(160, 2, 'Activity', '2014-02-03 10:39:01', NULL, 2, 1),
(161, 2, 'Activity', '2014-02-03 10:39:43', NULL, 2, 1),
(162, 1, 'Activity', '2014-02-03 15:44:16', NULL, 42, 4),
(163, 1, 'Activity', '2014-02-03 15:44:38', NULL, 45, 4),
(164, 2, 'Activity', '2014-02-04 10:10:59', NULL, 52, 2),
(165, 2, 'Activity', '2014-02-04 17:01:27', NULL, 1, 1),
(166, 2, 'Activity', '2014-02-04 17:02:33', NULL, 1, 1),
(167, 2, 'Activity', '2014-02-04 17:27:14', NULL, 14, 2),
(168, 1, 'Activity', '2014-02-04 20:01:57', NULL, 23, 3),
(169, 1, 'Activity', '2014-02-04 20:02:30', NULL, 2, 1),
(170, 4, 'Activity', '2014-02-05 16:08:34', NULL, 52, 2),
(171, 4, 'Activity', '2014-02-05 16:09:10', NULL, 2, 1),
(172, 4, 'Activity', '2014-02-05 18:37:52', NULL, 2, 1),
(173, 4, 'Activity', '2014-02-05 18:38:05', NULL, 37, 4),
(174, 4, 'Activity', '2014-02-05 18:38:23', NULL, 45, 4),
(175, 4, 'Activity', '2014-02-05 18:38:46', NULL, 23, 3),
(176, 5, 'Activity', '2014-02-06 17:56:41', NULL, 42, 4),
(177, 5, 'Activity', '2014-02-06 17:57:18', NULL, 2, 1),
(178, 5, 'Activity', '2014-02-06 18:04:19', NULL, 45, 4),
(179, 5, 'Activity', '2014-02-06 18:12:14', NULL, 4, 1),
(180, 5, 'Activity', '2014-02-06 19:36:52', NULL, 38, 4),
(181, 5, 'Activity', '2014-02-06 19:37:11', NULL, 2, 1),
(182, 5, 'Activity', '2014-02-06 19:44:53', NULL, 45, 4),
(183, 5, 'Activity', '2014-02-10 09:54:46', NULL, 52, 2),
(184, 5, 'Activity', '2014-02-10 18:36:55', NULL, 52, 2),
(185, 5, 'Activity', '2014-02-10 18:37:10', NULL, 17, 2),
(186, 2, 'Activity', '2014-02-11 10:16:54', NULL, 52, 2),
(187, 1, 'Activity', '2014-02-11 10:17:05', NULL, 52, 2),
(188, 2, 'Activity', '2014-02-11 10:17:15', NULL, 52, 2),
(189, 1, 'Activity', '2014-02-11 10:17:16', NULL, 52, 2),
(190, 3, 'Activity', '2014-02-11 11:35:47', NULL, 44, 4),
(191, 3, 'Activity', '2014-02-11 11:36:28', NULL, 37, 4),
(192, 3, 'Activity', '2014-02-11 19:51:18', NULL, 52, 2),
(193, 1, 'Activity', '2014-02-12 09:54:07', NULL, 14, 2),
(194, 1, 'Activity', '2014-02-12 09:56:12', NULL, 23, 3),
(195, 1, 'Activity', '2014-02-12 09:56:41', NULL, 23, 3),
(196, 1, 'Activity', '2014-02-12 09:57:14', NULL, 23, 3),
(197, 1, 'Activity', '2014-02-12 09:57:45', NULL, 2, 1),
(198, 1, 'Activity', '2014-02-12 09:58:06', NULL, 2, 1),
(199, 5, 'Activity', '2014-02-13 19:35:05', NULL, 37, 4),
(200, 5, 'Activity', '2014-02-13 19:35:20', NULL, 45, 4),
(201, 5, 'Activity', '2014-02-13 19:35:39', NULL, 43, 4),
(202, 5, 'Activity', '2014-02-13 19:47:39', NULL, 42, 4),
(203, 3, 'Activity', '2014-02-14 19:05:59', NULL, 52, 2),
(204, 3, 'Activity', '2014-02-14 19:07:08', NULL, 43, 4),
(205, 3, 'Activity', '2014-02-14 19:07:20', NULL, 45, 4),
(206, 3, 'Activity', '2014-02-14 19:07:43', NULL, 37, 4),
(207, 3, 'Activity', '2014-02-14 19:08:14', NULL, 45, 4),
(208, 3, 'Activity', '2014-02-14 19:08:41', NULL, 37, 4),
(209, 3, 'Activity', '2014-02-14 19:11:06', NULL, 15, 2),
(210, 5, 'Activity', '2014-02-14 19:43:08', NULL, 52, 2),
(211, 5, 'Activity', '2014-02-17 11:29:47', NULL, 52, 2),
(212, 2, 'Activity', '2014-02-17 11:30:14', NULL, 52, 2),
(213, 5, 'Activity', '2014-02-17 20:14:16', NULL, 37, 4),
(214, 5, 'Activity', '2014-02-17 20:14:57', NULL, 44, 4),
(215, 5, 'Activity', '2014-02-17 20:15:31', NULL, 45, 4),
(216, 5, 'Activity', '2014-02-17 20:17:32', NULL, 40, 4),
(217, 5, 'Activity', '2014-02-18 10:56:14', NULL, 17, 2),
(218, 5, 'Activity', '2014-02-18 20:02:27', NULL, 37, 4),
(219, 5, 'Activity', '2014-02-18 20:02:43', NULL, 43, 4),
(220, 5, 'Activity', '2014-02-18 20:03:12', NULL, 40, 4),
(221, 5, 'Activity', '2014-02-18 20:03:31', NULL, 45, 4),
(222, 5, 'Activity', '2014-02-18 20:47:23', NULL, 23, 3),
(223, 3, 'Activity', '2014-02-18 20:50:21', NULL, 15, 2),
(224, 1, 'Activity', '2014-02-18 20:50:26', NULL, 15, 2),
(225, 3, 'Activity', '2014-02-18 20:50:42', NULL, 2, 1),
(226, 3, 'Activity', '2014-02-18 20:51:06', NULL, 37, 4),
(227, 3, 'Activity', '2014-02-18 20:51:29', NULL, 45, 4),
(228, 1, 'Activity', '2014-02-18 20:52:30', NULL, 52, 2),
(229, 3, 'Activity', '2014-02-18 20:52:59', NULL, 15, 2),
(230, 1, 'Activity', '2014-02-18 20:53:18', NULL, 10, 1),
(231, 3, 'Activity', '2014-02-18 20:53:24', NULL, 52, 2),
(232, 1, 'Activity', '2014-02-18 20:53:26', NULL, 10, 1),
(233, 1, 'Activity', '2014-02-18 20:54:11', NULL, 37, 4),
(234, 1, 'Activity', '2014-02-18 20:54:29', NULL, 43, 4),
(235, 3, 'Activity', '2014-02-18 20:54:39', NULL, 43, 4),
(236, 1, 'Activity', '2014-02-18 20:54:41', NULL, 45, 4),
(237, 1, 'Activity', '2014-02-18 20:55:08', NULL, 46, 2),
(238, 1, 'Activity', '2014-02-18 20:55:54', NULL, 15, 2),
(239, 1, 'Activity', '2014-02-18 20:56:37', NULL, 2, 1),
(240, 1, 'Activity', '2014-02-18 20:57:19', NULL, 2, 1),
(241, 2, 'Activity', '2014-02-18 20:57:55', NULL, 52, 2),
(242, 5, 'Activity', '2014-02-19 10:48:47', NULL, 52, 2),
(243, 4, 'Activity', '2014-02-19 11:13:18', NULL, 2, 1),
(244, 4, 'Activity', '2014-02-19 11:13:35', NULL, 2, 1),
(245, 4, 'Activity', '2014-02-19 11:13:52', NULL, 22, 2),
(246, 4, 'Activity', '2014-02-19 11:14:11', NULL, 44, 4),
(247, 4, 'Activity', '2014-02-19 11:14:23', NULL, 37, 4),
(248, 5, 'Activity', '2014-02-19 12:36:09', NULL, 23, 3),
(249, 5, 'Activity', '2014-02-19 12:36:53', NULL, 37, 4),
(250, 5, 'Activity', '2014-02-19 12:37:20', NULL, 44, 4),
(251, 5, 'Activity', '2014-02-19 12:38:00', NULL, 36, 3),
(252, 5, 'Activity', '2014-02-19 12:38:21', NULL, 43, 4),
(253, 5, 'Activity', '2014-02-19 20:17:16', NULL, 37, 4),
(254, 5, 'Activity', '2014-02-19 20:17:34', NULL, 45, 4),
(255, 5, 'Activity', '2014-02-19 20:17:49', NULL, 43, 4),
(256, 1, 'Activity', '2014-02-20 17:33:15', NULL, 15, 2),
(257, 1, 'Activity', '2014-02-20 17:33:49', NULL, 43, 4),
(258, 1, 'Activity', '2014-02-20 17:34:12', NULL, 44, 4),
(259, 1, 'Activity', '2014-02-20 17:35:03', NULL, 37, 4),
(260, 1, 'Activity', '2014-02-20 17:35:24', NULL, 14, 2),
(261, 3, 'Activity', '2014-02-20 17:39:34', NULL, 15, 2),
(262, 3, 'Activity', '2014-02-20 17:40:18', NULL, 43, 4),
(263, 3, 'Activity', '2014-02-20 17:41:15', NULL, 44, 4),
(264, 3, 'Activity', '2014-02-20 17:41:44', NULL, 37, 4),
(265, 5, 'Activity', '2014-02-20 17:59:09', NULL, 42, 4),
(266, 5, 'Activity', '2014-02-20 19:51:57', NULL, 37, 4),
(267, 5, 'Activity', '2014-02-20 19:52:11', NULL, 45, 4),
(268, 5, 'Activity', '2014-02-20 19:52:32', NULL, 40, 4),
(269, 5, 'Activity', '2014-02-20 19:52:44', NULL, 43, 4),
(270, 5, 'Activity', '2014-02-20 19:55:14', NULL, 56, 3),
(271, 3, 'Activity', '2014-02-20 20:22:28', NULL, 17, 2),
(272, 3, 'Activity', '2014-02-20 20:22:40', NULL, 15, 2),
(273, 3, 'Activity', '2014-02-20 20:23:17', NULL, 44, 4),
(274, 3, 'Activity', '2014-02-20 20:23:29', NULL, 37, 4),
(275, 3, 'Activity', '2014-02-20 20:24:35', NULL, 43, 4),
(276, 3, 'Activity', '2014-02-20 20:25:13', NULL, 10, 1),
(277, 3, 'Activity', '2014-02-20 20:26:13', NULL, 1, 1),
(278, 1, 'Activity', '2014-02-21 10:50:29', NULL, 15, 2),
(279, 1, 'Activity', '2014-02-21 10:51:00', NULL, 1, 1),
(280, 1, 'Activity', '2014-02-21 10:51:21', NULL, 10, 1),
(281, 1, 'Activity', '2014-02-21 10:51:51', NULL, 58, 2),
(282, 1, 'Activity', '2014-02-21 10:53:03', NULL, 58, 2),
(283, 1, 'Activity', '2014-02-21 10:53:22', NULL, 58, 2),
(284, 1, 'Activity', '2014-02-21 10:53:41', NULL, 58, 2),
(285, 3, 'Activity', '2014-02-21 10:59:26', NULL, 58, 2),
(286, 1, 'Activity', '2014-02-21 13:33:50', NULL, 44, 4),
(287, 1, 'Activity', '2014-02-21 13:34:02', NULL, 37, 4),
(288, 1, 'Activity', '2014-02-21 13:34:25', NULL, 43, 4),
(289, 3, 'Activity', '2014-02-24 19:46:55', NULL, 58, 2),
(290, 3, 'Activity', '2014-02-24 20:47:09', NULL, 43, 4),
(291, 3, 'Activity', '2014-02-24 20:49:01', NULL, 58, 2),
(292, 3, 'Activity', '2014-02-24 20:51:06', NULL, 58, 2),
(293, 3, 'Activity', '2014-02-24 20:57:07', NULL, 23, 3),
(294, 3, 'Activity', '2014-02-25 19:37:15', NULL, 17, 2),
(295, 4, 'Activity', '2014-02-26 11:06:49', NULL, 12, 2),
(296, 4, 'Activity', '2014-02-26 11:06:58', NULL, 12, 2),
(297, 5, 'Activity', '2014-02-26 20:09:33', NULL, 12, 2),
(298, 5, 'Activity', '2014-02-26 20:12:21', NULL, 37, 4),
(299, 5, 'Activity', '2014-02-26 20:12:40', NULL, 44, 4),
(300, 5, 'Activity', '2014-02-26 20:13:01', NULL, 43, 4),
(301, 5, 'Activity', '2014-02-26 20:50:06', NULL, 15, 2),
(302, 1, 'Activity', '2014-02-27 17:24:07', NULL, 15, 2),
(303, 1, 'Activity', '2014-02-27 17:24:24', NULL, 43, 4),
(304, 1, 'Activity', '2014-02-27 17:25:32', NULL, 1, 1),
(305, 1, 'Activity', '2014-02-27 17:26:25', NULL, 37, 4),
(306, 1, 'Activity', '2014-02-27 17:26:42', NULL, 45, 4),
(307, 1, 'Activity', '2014-02-27 17:27:07', NULL, 40, 4),
(308, 1, 'Activity', '2014-02-27 17:27:58', NULL, 44, 4),
(309, 1, 'Activity', '2014-02-27 17:28:08', NULL, 44, 4),
(310, 1, 'Activity', '2014-02-27 17:29:09', NULL, 58, 2),
(311, 1, 'Activity', '2014-02-27 17:30:06', NULL, 2, 1),
(312, 3, 'Activity', '2014-02-27 17:53:53', NULL, 17, 2),
(313, 3, 'Activity', '2014-02-27 20:30:34', NULL, 17, 2),
(314, 3, 'Activity', '2014-02-27 20:31:31', NULL, 58, 2),
(315, 1, 'Activity', '2014-02-28 16:12:54', NULL, 57, 4),
(316, 1, 'Activity', '2014-03-03 10:45:45', NULL, 52, 2),
(317, 1, 'Activity', '2014-03-03 10:45:58', NULL, 14, 2),
(318, 2, 'Activity', '2014-03-03 14:22:00', NULL, 52, 2),
(319, 1, 'Activity', '2014-03-04 11:16:27', NULL, 17, 2),
(320, 1, 'Activity', '2014-03-04 11:16:56', NULL, 37, 4),
(321, 1, 'Activity', '2014-03-04 11:17:07', NULL, 45, 4),
(322, 2, 'Activity', '2014-03-04 12:11:46', NULL, 2, 1),
(323, 2, 'Activity', '2014-03-04 12:12:39', NULL, 37, 4),
(324, 2, 'Activity', '2014-03-04 12:12:54', NULL, 44, 4),
(325, 2, 'Activity', '2014-03-04 16:50:45', NULL, 17, 2),
(326, 3, 'Activity', '2014-03-04 19:52:37', NULL, 58, 2),
(327, 3, 'Activity', '2014-03-04 19:53:18', NULL, 58, 2),
(328, 3, 'Activity', '2014-03-04 19:54:43', NULL, 42, 4),
(329, 3, 'Activity', '2014-03-04 19:56:05', NULL, 1, 1),
(330, 2, 'Activity', '2014-03-05 16:47:17', NULL, 52, 2),
(331, 5, 'Activity', '2014-03-07 10:44:57', NULL, 52, 2),
(332, 5, 'Activity', '2014-03-07 10:45:39', NULL, 37, 4),
(333, 5, 'Activity', '2014-03-07 10:46:03', NULL, 44, 4),
(334, 5, 'Activity', '2014-03-07 10:46:47', NULL, 44, 4),
(335, 5, 'Activity', '2014-03-07 10:47:09', NULL, 43, 4),
(336, 1, 'Activity', '2014-03-07 11:26:44', NULL, 58, 2),
(337, 1, 'Activity', '2014-03-07 11:27:30', NULL, 36, 3),
(338, 1, 'Activity', '2014-03-07 11:27:50', NULL, 36, 3),
(339, 1, 'Activity', '2014-03-07 11:28:35', NULL, 43, 4),
(340, 1, 'Activity', '2014-03-07 11:29:59', NULL, 52, 2),
(341, 2, 'Activity', '2014-03-07 11:33:37', NULL, 2, 1),
(342, 2, 'Activity', '2014-03-07 11:34:14', NULL, 37, 4),
(343, 2, 'Activity', '2014-03-07 11:34:34', NULL, 44, 4),
(344, 1, 'Activity', '2014-03-07 11:35:10', NULL, 44, 4),
(345, 2, 'Activity', '2014-03-07 11:35:23', NULL, 43, 4),
(346, 1, 'Activity', '2014-03-07 11:35:24', NULL, 37, 4),
(347, 2, 'Activity', '2014-03-07 11:35:55', NULL, 15, 2),
(348, 2, 'Activity', '2014-03-07 11:36:17', NULL, 15, 2),
(349, 1, 'Activity', '2014-03-07 11:36:36', NULL, 44, 4),
(350, 1, 'Activity', '2014-03-07 11:37:08', NULL, 37, 4),
(351, 1, 'Activity', '2014-03-07 11:37:24', NULL, 43, 4),
(352, 3, 'Activity', '2014-03-07 12:49:10', NULL, 15, 2),
(353, 3, 'Activity', '2014-03-07 12:49:37', NULL, 44, 4),
(354, 3, 'Activity', '2014-03-07 12:51:18', NULL, 15, 2),
(355, 3, 'Activity', '2014-03-07 12:51:34', NULL, 44, 4),
(356, 3, 'Activity', '2014-03-07 12:53:27', NULL, 37, 4),
(357, 3, 'Activity', '2014-03-07 12:54:27', NULL, 37, 4),
(358, 3, 'Activity', '2014-03-07 12:55:35', NULL, 43, 4),
(359, 3, 'Activity', '2014-03-07 12:55:58', NULL, 43, 4),
(360, 5, 'Activity', '2014-03-10 11:00:21', NULL, 52, 2),
(361, 2, 'Activity', '2014-03-10 11:07:10', NULL, 52, 2),
(362, 2, 'Activity', '2014-03-10 11:10:19', NULL, 62, 2),
(363, 3, 'Activity', '2014-03-10 20:05:59', NULL, 52, 2),
(364, 3, 'Activity', '2014-03-10 20:06:30', NULL, 37, 4),
(365, 3, 'Activity', '2014-03-10 20:06:43', NULL, 44, 4),
(366, 2, 'Activity', '2014-03-11 11:20:04', NULL, 52, 2),
(367, 4, 'Activity', '2014-03-11 14:25:46', NULL, 52, 2),
(368, 4, 'Activity', '2014-03-11 14:26:05', NULL, 52, 2),
(369, 4, 'Activity', '2014-03-11 14:26:14', NULL, 52, 2),
(370, 4, 'Activity', '2014-03-11 14:27:12', NULL, 52, 2),
(371, 4, 'Activity', '2014-03-11 16:06:52', NULL, 37, 4),
(372, 4, 'Activity', '2014-03-11 16:07:08', NULL, 37, 4),
(373, 4, 'Activity', '2014-03-11 16:07:19', NULL, 37, 4),
(374, 4, 'Activity', '2014-03-11 16:07:33', NULL, 37, 4),
(375, 4, 'Activity', '2014-03-11 16:07:52', NULL, 44, 4),
(376, 4, 'Activity', '2014-03-11 16:08:02', NULL, 44, 4),
(377, 4, 'Activity', '2014-03-11 16:08:10', NULL, 44, 4),
(378, 4, 'Activity', '2014-03-11 16:08:20', NULL, 44, 4),
(379, 4, 'Activity', '2014-03-11 16:08:39', NULL, 2, 1),
(380, 4, 'Activity', '2014-03-11 16:08:49', NULL, 2, 1),
(381, 4, 'Activity', '2014-03-11 16:08:59', NULL, 2, 1),
(382, 4, 'Activity', '2014-03-11 16:09:08', NULL, 2, 1),
(383, 4, 'Activity', '2014-03-11 16:09:16', NULL, 2, 1),
(384, 4, 'Activity', '2014-03-11 16:09:28', NULL, 2, 1),
(385, 4, 'Activity', '2014-03-11 16:09:41', NULL, 2, 1),
(386, 4, 'Activity', '2014-03-11 16:09:52', NULL, 2, 1),
(387, 4, 'Activity', '2014-03-11 16:10:04', NULL, 2, 1),
(388, 4, 'Activity', '2014-03-11 16:10:13', NULL, 2, 1),
(389, 4, 'Activity', '2014-03-11 16:10:26', NULL, 2, 1),
(390, 4, 'Activity', '2014-03-11 16:10:35', NULL, 2, 1),
(391, 4, 'Activity', '2014-03-11 16:10:43', NULL, 2, 1),
(392, 4, 'Activity', '2014-03-11 16:10:53', NULL, 2, 1),
(393, 4, 'Activity', '2014-03-11 16:37:56', NULL, 2, 1),
(394, 4, 'Activity', '2014-03-11 16:38:06', NULL, 2, 1),
(395, 4, 'Activity', '2014-03-11 16:38:14', NULL, 2, 1),
(396, 4, 'Activity', '2014-03-11 16:38:24', NULL, 2, 1),
(397, 4, 'Activity', '2014-03-11 16:38:38', NULL, 2, 1),
(398, 4, 'Activity', '2014-03-11 16:38:46', NULL, 2, 1),
(399, 4, 'Activity', '2014-03-11 16:38:53', NULL, 2, 1),
(400, 4, 'Activity', '2014-03-11 16:39:03', NULL, 2, 1),
(401, 1, 'Activity', '2014-03-11 18:07:52', NULL, 2, 1),
(402, 1, 'Activity', '2014-03-11 18:08:05', NULL, 2, 1),
(403, 1, 'Activity', '2014-03-11 18:08:36', NULL, 44, 4),
(404, 1, 'Activity', '2014-03-11 18:08:50', NULL, 44, 4),
(405, 1, 'Activity', '2014-03-11 18:09:04', NULL, 37, 4),
(406, 1, 'Activity', '2014-03-11 18:09:19', NULL, 37, 4),
(407, 1, 'Activity', '2014-03-11 18:10:29', NULL, 52, 2),
(408, 2, 'Activity', '2014-03-11 20:25:43', NULL, 52, 2),
(409, 5, 'Activity', '2014-03-11 20:26:25', NULL, 52, 2),
(410, 5, 'Activity', '2014-03-11 20:30:21', NULL, 2, 1),
(411, 5, 'Activity', '2014-03-11 20:30:22', NULL, 2, 1),
(412, 5, 'Activity', '2014-03-11 20:30:30', NULL, 2, 1),
(413, 5, 'Activity', '2014-03-11 20:30:31', NULL, 2, 1),
(414, 5, 'Activity', '2014-03-11 20:30:31', NULL, 2, 1),
(415, 5, 'Activity', '2014-03-11 20:30:31', NULL, 2, 1),
(416, 5, 'Activity', '2014-03-11 20:30:32', NULL, 2, 1),
(417, 5, 'Activity', '2014-03-11 20:30:32', NULL, 2, 1),
(418, 5, 'Activity', '2014-03-11 20:30:33', NULL, 2, 1),
(419, 5, 'Activity', '2014-03-11 20:30:33', NULL, 2, 1),
(420, 5, 'Activity', '2014-03-11 20:30:33', NULL, 2, 1),
(421, 4, 'Activity', '2014-03-12 10:54:45', NULL, 17, 2),
(422, 5, 'Activity', '2014-03-12 12:10:13', NULL, 9, 1),
(423, 5, 'Activity', '2014-03-12 12:10:13', NULL, 9, 1),
(424, 5, 'Activity', '2014-03-12 12:10:14', NULL, 9, 1),
(425, 5, 'Activity', '2014-03-12 12:10:14', NULL, 9, 1),
(426, 5, 'Activity', '2014-03-12 12:10:15', NULL, 9, 1),
(427, 5, 'Activity', '2014-03-12 12:10:15', NULL, 9, 1),
(428, 5, 'Activity', '2014-03-12 12:10:24', NULL, 9, 1),
(429, 5, 'Activity', '2014-03-12 12:10:24', NULL, 9, 1),
(430, 5, 'Activity', '2014-03-12 12:10:25', NULL, 9, 1),
(431, 5, 'Activity', '2014-03-12 12:10:25', NULL, 9, 1),
(432, 5, 'Activity', '2014-03-12 12:10:26', NULL, 9, 1),
(433, 5, 'Activity', '2014-03-12 12:10:26', NULL, 9, 1),
(434, 5, 'Activity', '2014-03-12 12:10:27', NULL, 9, 1),
(435, 5, 'Activity', '2014-03-12 12:10:27', NULL, 9, 1),
(436, 5, 'Activity', '2014-03-12 12:10:28', NULL, 9, 1),
(437, 5, 'Activity', '2014-03-12 12:10:31', NULL, 9, 1),
(438, 3, 'Activity', '2014-03-12 20:39:29', NULL, 17, 2),
(439, 3, 'Activity', '2014-03-12 20:40:44', NULL, 42, 4),
(440, 3, 'Activity', '2014-03-12 20:42:14', NULL, 37, 4),
(441, 3, 'Activity', '2014-03-12 20:43:06', NULL, 44, 4),
(442, 3, 'Activity', '2014-03-12 20:45:08', NULL, 2, 1),
(443, 2, 'Activity', '2014-03-13 18:26:47', NULL, 52, 2),
(444, 3, 'Activity', '2014-03-13 18:39:29', NULL, 52, 2),
(445, 3, 'Activity', '2014-03-13 18:41:57', NULL, 2, 1),
(446, 3, 'Activity', '2014-03-13 19:45:47', NULL, 42, 4),
(447, 1, 'Activity', '2014-03-15 14:09:28', NULL, 2, 1),
(448, 1, 'Activity', '2014-03-15 17:54:35', NULL, 8, 1),
(449, 1, 'Activity', '2014-03-15 22:39:54', NULL, 8, 1),
(450, 1, 'Activity', '2014-03-15 22:40:31', NULL, 8, 1),
(451, 1, 'Activity', '2014-03-15 23:05:41', NULL, 8, 1),
(452, 6, 'Activity', '2014-03-15 23:09:34', NULL, 5, 1),
(453, 1, 'Activity', '2014-03-16 11:20:14', NULL, 8, 1),
(454, 1, 'Activity', '2014-03-16 11:20:19', NULL, 8, 1),
(455, 1, 'Activity', '2014-03-16 11:20:23', NULL, 8, 1),
(456, 1, 'Activity', '2014-03-16 11:20:27', NULL, 8, 1),
(457, 1, 'Activity', '2014-03-16 11:20:31', NULL, 8, 1),
(458, 1, 'Activity', '2014-03-16 11:20:35', NULL, 8, 1),
(459, 1, 'Activity', '2014-03-16 11:49:24', NULL, 3, 1),
(460, 1, 'Activity', '2014-03-16 11:49:56', NULL, 3, 1),
(461, 1, 'Activity', '2014-03-16 17:40:22', NULL, 2, 1),
(462, 1, 'Activity', '2014-03-16 17:40:27', NULL, 2, 1),
(463, 1, 'Activity', '2014-03-16 17:40:31', NULL, 2, 1),
(464, 1, 'Activity', '2014-03-16 17:40:35', NULL, 2, 1),
(465, 1, 'Activity', '2014-03-16 17:40:37', NULL, 2, 1),
(466, 1, 'Activity', '2014-03-16 17:40:39', NULL, 2, 1),
(467, 1, 'Activity', '2014-03-16 17:45:34', NULL, 7, 1),
(468, 1, 'Activity', '2014-03-16 17:45:45', NULL, 7, 1),
(469, 1, 'Activity', '2014-03-16 17:45:52', NULL, 7, 1),
(470, 1, 'Activity', '2014-03-16 17:45:58', NULL, 7, 1),
(471, 1, 'Activity', '2014-03-16 17:46:06', NULL, 7, 1),
(472, 1, 'Activity', '2014-03-16 17:46:11', NULL, 7, 1),
(473, 1, 'Activity', '2014-03-16 17:46:15', NULL, 7, 1),
(474, 1, 'Activity', '2014-03-16 17:46:20', NULL, 7, 1),
(475, 1, 'Activity', '2014-03-16 17:46:25', NULL, 7, 1),
(476, 1, 'Activity', '2014-03-16 17:46:32', NULL, 7, 1),
(477, 1, 'Activity', '2014-03-16 17:46:37', NULL, 7, 1),
(478, 1, 'Activity', '2014-03-17 00:07:14', NULL, 2, 1),
(479, 6, 'Activity', '2014-03-21 01:04:39', NULL, 3, 1),
(480, 6, 'Activity', '2014-03-21 01:05:46', NULL, 8, 1),
(481, 6, 'Activity', '2014-03-21 01:06:20', NULL, 8, 1),
(482, 6, 'Activity', '2014-03-27 00:10:48', NULL, 2, 1),
(483, 6, 'Activity', '2014-03-27 00:12:28', NULL, 8, 1),
(484, 1, 'Activity', '2014-03-29 16:52:44', NULL, 9, 1),
(485, 1, 'Activity', '2014-03-29 16:55:18', NULL, 9, 1),
(486, 1, 'Activity', '2014-04-02 22:18:10', NULL, 1, 1),
(487, 1, 'Activity', '2014-04-03 00:39:54', NULL, 15, 2),
(488, 6, 'Activity', '2014-04-04 23:07:49', NULL, 5, 1),
(489, 6, 'Activity', '2014-04-04 23:09:26', NULL, 5, 1),
(490, 6, 'Activity', '2014-04-04 23:09:42', NULL, 68, 1),
(491, 6, 'Activity', '2014-04-04 23:49:28', NULL, 15, 2),
(492, 6, 'Activity', '2014-04-04 23:55:10', NULL, 29, 3),
(493, 6, 'Activity', '2014-04-04 23:55:14', NULL, 5, 1),
(494, 6, 'Activity', '2014-04-04 23:55:18', NULL, 17, 2),
(495, 6, 'Activity', '2014-04-04 23:55:21', NULL, 19, 2),
(496, 6, 'Activity', '2014-04-04 23:56:20', NULL, 7, 1),
(497, 6, 'Activity', '2014-04-04 23:56:23', NULL, 62, 2),
(498, 1, 'Activity', '2014-04-05 12:00:31', NULL, 2, 1),
(499, 1, 'Activity', '2014-04-05 12:00:31', NULL, 37, 4),
(500, 1, 'Activity', '2014-04-05 12:00:31', NULL, 45, 4),
(501, 1, 'Activity', '2014-04-05 12:00:31', NULL, 40, 4),
(502, 1, 'Activity', '2014-04-05 12:00:31', NULL, 43, 4),
(503, 1, 'Activity', '2014-04-05 12:00:31', NULL, 10, 1),
(504, 1, 'Activity', '2014-04-05 12:00:31', NULL, 17, 2),
(505, 2, 'Activity', '2014-04-05 12:00:31', NULL, 2, 1),
(506, 2, 'Activity', '2014-04-05 12:00:31', NULL, 37, 4),
(507, 2, 'Activity', '2014-04-05 12:00:31', NULL, 45, 4),
(508, 2, 'Activity', '2014-04-05 12:00:31', NULL, 40, 4),
(509, 2, 'Activity', '2014-04-05 12:00:31', NULL, 43, 4),
(510, 2, 'Activity', '2014-04-05 12:00:31', NULL, 10, 1),
(511, 2, 'Activity', '2014-04-05 12:00:31', NULL, 17, 2),
(512, 3, 'Activity', '2014-04-05 12:00:31', NULL, 2, 1),
(513, 3, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(514, 3, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(515, 4, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(516, 4, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(517, 4, 'Activity', '2014-04-05 12:00:32', NULL, 3, 1),
(518, 4, 'Activity', '2014-04-05 12:00:32', NULL, 23, 3),
(519, 4, 'Activity', '2014-04-05 12:00:32', NULL, 37, 4),
(520, 4, 'Activity', '2014-04-05 12:00:32', NULL, 45, 4),
(521, 4, 'Activity', '2014-04-05 12:00:32', NULL, 42, 4),
(522, 5, 'Activity', '2014-04-05 12:00:32', NULL, 19, 2),
(523, 5, 'Activity', '2014-04-05 12:00:32', NULL, 16, 2),
(524, 5, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(525, 5, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(526, 1, 'Activity', '2014-04-05 12:00:32', NULL, 39, 4),
(527, 1, 'Activity', '2014-04-05 12:00:32', NULL, 14, 2),
(528, 4, 'Activity', '2014-04-05 12:00:32', NULL, 52, 2),
(529, 4, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(530, 4, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(531, 4, 'Activity', '2014-04-05 12:00:32', NULL, 37, 4),
(532, 1, 'Activity', '2014-04-05 12:00:32', NULL, 37, 4),
(533, 4, 'Activity', '2014-04-05 12:00:32', NULL, 45, 4),
(534, 1, 'Activity', '2014-04-05 12:00:32', NULL, 52, 2),
(535, 1, 'Activity', '2014-04-05 12:00:32', NULL, 15, 2),
(536, 3, 'Activity', '2014-04-05 12:00:32', NULL, 23, 3),
(537, 3, 'Activity', '2014-04-05 12:00:32', NULL, 17, 2),
(538, 5, 'Activity', '2014-04-05 12:00:32', NULL, 23, 3),
(539, 5, 'Activity', '2014-04-05 12:00:32', NULL, 37, 4),
(540, 5, 'Activity', '2014-04-05 12:00:32', NULL, 43, 4),
(541, 5, 'Activity', '2014-04-05 12:00:32', NULL, 52, 2),
(542, 2, 'Activity', '2014-04-05 12:00:32', NULL, 52, 2),
(543, 5, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(544, 5, 'Activity', '2014-04-05 12:00:32', NULL, 23, 3),
(545, 5, 'Activity', '2014-04-05 12:00:32', NULL, 17, 2),
(546, 1, 'Activity', '2014-04-05 12:00:32', NULL, 15, 2),
(547, 2, 'Activity', '2014-04-05 12:00:32', NULL, 15, 2),
(548, 4, 'Activity', '2014-04-05 12:00:32', NULL, 2, 1),
(549, 2, 'Activity', '2014-04-05 12:00:33', NULL, 15, 2),
(550, 4, 'Activity', '2014-04-05 12:00:33', NULL, 1, 1),
(551, 4, 'Activity', '2014-04-05 12:00:33', NULL, 1, 1),
(552, 4, 'Activity', '2014-04-05 12:00:33', NULL, 37, 4),
(553, 4, 'Activity', '2014-04-05 12:00:33', NULL, 37, 4),
(554, 4, 'Activity', '2014-04-05 12:00:33', NULL, 45, 4),
(555, 1, 'Activity', '2014-04-05 12:00:33', NULL, 45, 4),
(556, 5, 'Activity', '2014-04-05 12:00:33', NULL, 55, 3),
(557, 3, 'Activity', '2014-04-05 12:00:33', NULL, 56, 3),
(558, 2, 'Activity', '2014-04-05 12:00:33', NULL, 15, 2),
(559, 1, 'Activity', '2014-04-05 12:00:33', NULL, 15, 2),
(560, 1, 'Activity', '2014-04-05 12:00:33', NULL, 14, 2),
(561, 5, 'Activity', '2014-04-05 12:00:33', NULL, 17, 2),
(562, 4, 'Activity', '2014-04-05 12:00:33', NULL, 2, 1),
(563, 4, 'Activity', '2014-04-05 12:00:33', NULL, 23, 3),
(564, 4, 'Activity', '2014-04-05 12:00:33', NULL, 37, 4),
(565, 4, 'Activity', '2014-04-05 12:00:33', NULL, 45, 4),
(566, 1, 'Activity', '2014-04-05 12:00:33', NULL, 39, 4),
(567, 1, 'Activity', '2014-04-05 12:00:33', NULL, 14, 2),
(568, 1, 'Activity', '2014-04-05 12:00:33', NULL, 42, 4),
(569, 2, 'Activity', '2014-04-05 12:00:33', NULL, 42, 4),
(570, 2, 'Activity', '2014-04-05 12:00:33', NULL, 39, 4),
(571, 4, 'Activity', '2014-04-05 12:00:33', NULL, 12, 2),
(572, 4, 'Activity', '2014-04-05 12:00:33', NULL, 23, 3),
(573, 4, 'Activity', '2014-04-05 12:00:33', NULL, 37, 4),
(574, 5, 'Activity', '2014-04-05 12:00:33', NULL, 35, 3),
(575, 0, 'Activity', '2014-04-05 12:00:33', NULL, NULL, NULL),
(576, 1, 'Activity', '2014-04-05 12:01:55', NULL, 2, 1),
(577, 1, 'Activity', '2014-04-05 12:01:55', NULL, 37, 4),
(578, 1, 'Activity', '2014-04-05 12:01:55', NULL, 45, 4),
(579, 1, 'Activity', '2014-04-05 12:01:56', NULL, 40, 4),
(580, 1, 'Activity', '2014-04-05 12:01:56', NULL, 43, 4),
(581, 1, 'Activity', '2014-04-05 12:01:56', NULL, 10, 1),
(582, 1, 'Activity', '2014-04-05 12:01:56', NULL, 17, 2),
(583, 2, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(584, 2, 'Activity', '2014-04-05 12:01:56', NULL, 37, 4),
(585, 2, 'Activity', '2014-04-05 12:01:56', NULL, 45, 4),
(586, 2, 'Activity', '2014-04-05 12:01:56', NULL, 40, 4),
(587, 2, 'Activity', '2014-04-05 12:01:56', NULL, 43, 4),
(588, 2, 'Activity', '2014-04-05 12:01:56', NULL, 10, 1),
(589, 2, 'Activity', '2014-04-05 12:01:56', NULL, 17, 2),
(590, 3, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(591, 3, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(592, 3, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(593, 4, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(594, 4, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(595, 4, 'Activity', '2014-04-05 12:01:56', NULL, 3, 1),
(596, 4, 'Activity', '2014-04-05 12:01:56', NULL, 23, 3),
(597, 4, 'Activity', '2014-04-05 12:01:56', NULL, 37, 4),
(598, 4, 'Activity', '2014-04-05 12:01:56', NULL, 45, 4),
(599, 4, 'Activity', '2014-04-05 12:01:56', NULL, 42, 4),
(600, 5, 'Activity', '2014-04-05 12:01:56', NULL, 19, 2),
(601, 5, 'Activity', '2014-04-05 12:01:56', NULL, 16, 2),
(602, 5, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(603, 5, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(604, 1, 'Activity', '2014-04-05 12:01:56', NULL, 39, 4),
(605, 1, 'Activity', '2014-04-05 12:01:56', NULL, 14, 2),
(606, 4, 'Activity', '2014-04-05 12:01:56', NULL, 52, 2),
(607, 4, 'Activity', '2014-04-05 12:01:56', NULL, 2, 1),
(608, 4, 'Activity', '2014-04-05 12:01:57', NULL, 2, 1),
(609, 4, 'Activity', '2014-04-05 12:01:57', NULL, 37, 4),
(610, 1, 'Activity', '2014-04-05 12:01:57', NULL, 37, 4),
(611, 4, 'Activity', '2014-04-05 12:01:57', NULL, 45, 4),
(612, 1, 'Activity', '2014-04-05 12:01:57', NULL, 52, 2),
(613, 1, 'Activity', '2014-04-05 12:01:57', NULL, 15, 2),
(614, 3, 'Activity', '2014-04-05 12:01:57', NULL, 23, 3),
(615, 3, 'Activity', '2014-04-05 12:01:57', NULL, 17, 2),
(616, 5, 'Activity', '2014-04-05 12:01:57', NULL, 23, 3),
(617, 5, 'Activity', '2014-04-05 12:01:57', NULL, 37, 4),
(618, 5, 'Activity', '2014-04-05 12:01:57', NULL, 43, 4),
(619, 5, 'Activity', '2014-04-05 12:01:57', NULL, 52, 2),
(620, 2, 'Activity', '2014-04-05 12:01:57', NULL, 52, 2),
(621, 5, 'Activity', '2014-04-05 12:01:57', NULL, 2, 1),
(622, 5, 'Activity', '2014-04-05 12:01:57', NULL, 23, 3),
(623, 5, 'Activity', '2014-04-05 12:01:57', NULL, 17, 2),
(624, 1, 'Activity', '2014-04-05 12:01:57', NULL, 15, 2),
(625, 2, 'Activity', '2014-04-05 12:01:57', NULL, 15, 2),
(626, 4, 'Activity', '2014-04-05 12:01:57', NULL, 2, 1),
(627, 2, 'Activity', '2014-04-05 12:01:57', NULL, 15, 2),
(628, 4, 'Activity', '2014-04-05 12:01:57', NULL, 1, 1),
(629, 4, 'Activity', '2014-04-05 12:01:57', NULL, 1, 1),
(630, 4, 'Activity', '2014-04-05 12:01:57', NULL, 37, 4),
(631, 4, 'Activity', '2014-04-05 12:01:57', NULL, 37, 4),
(632, 4, 'Activity', '2014-04-05 12:01:57', NULL, 45, 4),
(633, 1, 'Activity', '2014-04-05 12:01:57', NULL, 45, 4),
(634, 5, 'Activity', '2014-04-05 12:01:57', NULL, 55, 3),
(635, 3, 'Activity', '2014-04-05 12:01:57', NULL, 56, 3),
(636, 2, 'Activity', '2014-04-05 12:01:57', NULL, 15, 2),
(637, 1, 'Activity', '2014-04-05 12:01:57', NULL, 15, 2),
(638, 1, 'Activity', '2014-04-05 12:01:57', NULL, 14, 2),
(639, 5, 'Activity', '2014-04-05 12:01:57', NULL, 17, 2),
(640, 4, 'Activity', '2014-04-05 12:01:57', NULL, 2, 1),
(641, 4, 'Activity', '2014-04-05 12:01:57', NULL, 23, 3),
(642, 4, 'Activity', '2014-04-05 12:01:57', NULL, 37, 4),
(643, 4, 'Activity', '2014-04-05 12:01:57', NULL, 45, 4),
(644, 1, 'Activity', '2014-04-05 12:01:58', NULL, 39, 4),
(645, 1, 'Activity', '2014-04-05 12:01:58', NULL, 14, 2),
(646, 1, 'Activity', '2014-04-05 12:01:58', NULL, 42, 4),
(647, 2, 'Activity', '2014-04-05 12:01:58', NULL, 42, 4),
(648, 2, 'Activity', '2014-04-05 12:01:58', NULL, 39, 4),
(649, 4, 'Activity', '2014-04-05 12:01:58', NULL, 12, 2),
(650, 4, 'Activity', '2014-04-05 12:01:58', NULL, 23, 3),
(651, 4, 'Activity', '2014-04-05 12:01:58', NULL, 37, 4),
(652, 5, 'Activity', '2014-04-05 12:01:58', NULL, 35, 3),
(653, 0, 'Activity', '2014-04-05 12:01:58', NULL, NULL, NULL),
(654, 1, 'Activity', '2014-04-05 12:02:14', NULL, 52, 2),
(655, 5, 'Activity', '2014-04-05 12:02:15', NULL, 15, 2),
(656, 5, 'Activity', '2014-04-05 12:02:15', NULL, 37, 4),
(657, 5, 'Activity', '2014-04-05 12:02:15', NULL, 37, 4),
(658, 5, 'Activity', '2014-04-05 12:02:15', NULL, 43, 4),
(659, 4, 'Activity', '2014-04-05 12:02:15', NULL, 2, 1),
(660, 4, 'Activity', '2014-04-05 12:02:15', NULL, 23, 3),
(661, 4, 'Activity', '2014-04-05 12:02:15', NULL, 37, 4),
(662, 4, 'Activity', '2014-04-05 12:02:15', NULL, 45, 4),
(663, 1, 'Activity', '2014-04-05 12:02:15', NULL, 15, 2),
(664, 2, 'Activity', '2014-04-05 12:02:15', NULL, 15, 2),
(665, 3, 'Activity', '2014-04-05 12:02:15', NULL, 15, 2),
(666, 3, 'Activity', '2014-04-05 12:02:15', NULL, 44, 4),
(667, 3, 'Activity', '2014-04-05 12:02:15', NULL, 43, 4),
(668, 3, 'Activity', '2014-04-05 12:02:15', NULL, 52, 2),
(669, 2, 'Activity', '2014-04-05 12:02:15', NULL, 52, 2),
(670, 5, 'Activity', '2014-04-05 12:02:15', NULL, 12, 2),
(671, 5, 'Activity', '2014-04-05 12:02:15', NULL, 17, 2),
(672, 5, 'Activity', '2014-04-05 12:02:15', NULL, 52, 2),
(673, 5, 'Activity', '2014-04-05 12:02:15', NULL, 23, 3),
(674, 5, 'Activity', '2014-04-05 12:02:15', NULL, 44, 4),
(675, 5, 'Activity', '2014-04-05 12:02:15', NULL, 44, 4),
(676, 1, 'Activity', '2014-04-05 12:02:15', NULL, 43, 4),
(677, 5, 'Activity', '2014-04-05 12:02:15', NULL, 7, 1),
(678, 1, 'Activity', '2014-04-05 12:02:15', NULL, 15, 2),
(679, 1, 'Activity', '2014-04-05 12:02:15', NULL, 14, 2),
(680, 1, 'Activity', '2014-04-05 12:02:15', NULL, 56, 3),
(681, 3, 'Activity', '2014-04-05 12:02:16', NULL, 15, 2),
(682, 1, 'Activity', '2014-04-05 12:02:16', NULL, 23, 3),
(683, 1, 'Activity', '2014-04-05 12:02:16', NULL, 36, 3),
(684, 3, 'Activity', '2014-04-05 12:02:16', NULL, 1, 1),
(685, 1, 'Activity', '2014-04-05 12:02:17', NULL, 56, 3),
(686, 5, 'Activity', '2014-04-05 12:02:17', NULL, 37, 4),
(687, 5, 'Activity', '2014-04-05 12:02:17', NULL, 37, 4),
(688, 4, 'Activity', '2014-04-05 12:02:17', NULL, 12, 2),
(689, 3, 'Activity', '2014-04-05 12:02:17', NULL, 37, 4),
(690, 3, 'Activity', '2014-04-05 12:02:17', NULL, 44, 4),
(691, 3, 'Activity', '2014-04-05 12:02:17', NULL, 7, 1),
(692, 0, 'Activity', '2014-04-05 12:02:17', NULL, NULL, NULL),
(693, 3, 'Activity', '2014-04-05 12:02:19', NULL, 1, 1),
(694, 3, 'Activity', '2014-04-05 12:02:19', NULL, 17, 2),
(695, 3, 'Activity', '2014-04-05 12:02:19', NULL, 7, 1),
(696, 3, 'Activity', '2014-04-05 12:02:19', NULL, 1, 1),
(697, 2, 'Activity', '2014-04-05 12:02:19', NULL, 15, 2),
(698, 2, 'Activity', '2014-04-05 12:02:19', NULL, 43, 4),
(699, 2, 'Activity', '2014-04-05 12:02:19', NULL, 23, 3),
(700, 2, 'Activity', '2014-04-05 12:02:19', NULL, 36, 3),
(701, 1, 'Activity', '2014-04-05 12:02:19', NULL, 45, 4),
(702, 1, 'Activity', '2014-04-05 12:02:19', NULL, 40, 4),
(703, 1, 'Activity', '2014-04-05 12:02:19', NULL, 37, 4),
(704, 1, 'Activity', '2014-04-05 12:02:20', NULL, 2, 1),
(705, 4, 'Activity', '2014-04-05 12:02:20', NULL, 4, 1),
(706, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(707, 4, 'Activity', '2014-04-05 12:02:20', NULL, 56, 3),
(708, 4, 'Activity', '2014-04-05 12:02:20', NULL, 39, 4),
(709, 4, 'Activity', '2014-04-05 12:02:20', NULL, 39, 4),
(710, 5, 'Activity', '2014-04-05 12:02:20', NULL, 17, 2),
(711, 3, 'Activity', '2014-04-05 12:02:20', NULL, 17, 2),
(712, 2, 'Activity', '2014-04-05 12:02:20', NULL, 14, 2),
(713, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(714, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(715, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(716, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(717, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(718, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(719, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(720, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(721, 4, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(722, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(723, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(724, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(725, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(726, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(727, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(728, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(729, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(730, 4, 'Activity', '2014-04-05 12:02:20', NULL, 9, 1),
(731, 4, 'Activity', '2014-04-05 12:02:20', NULL, 12, 2),
(732, 4, 'Activity', '2014-04-05 12:02:20', NULL, 42, 4),
(733, 4, 'Activity', '2014-04-05 12:02:20', NULL, 45, 4),
(734, 5, 'Activity', '2014-04-05 12:02:20', NULL, 52, 2),
(735, 1, 'Activity', '2014-04-05 12:02:20', NULL, 11, 1),
(736, 1, 'Activity', '2014-04-05 12:02:20', NULL, 52, 2),
(737, 2, 'Activity', '2014-04-05 12:02:20', NULL, 2, 1),
(738, 2, 'Activity', '2014-04-05 12:02:20', NULL, 2, 1),
(739, 1, 'Activity', '2014-04-05 12:02:20', NULL, 42, 4),
(740, 1, 'Activity', '2014-04-05 12:02:20', NULL, 45, 4),
(741, 2, 'Activity', '2014-04-05 12:02:20', NULL, 52, 2),
(742, 2, 'Activity', '2014-04-05 12:02:20', NULL, 1, 1),
(743, 2, 'Activity', '2014-04-05 12:02:21', NULL, 1, 1),
(744, 2, 'Activity', '2014-04-05 12:02:21', NULL, 14, 2),
(745, 1, 'Activity', '2014-04-05 12:02:21', NULL, 23, 3),
(746, 1, 'Activity', '2014-04-05 12:02:21', NULL, 2, 1),
(747, 4, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(748, 4, 'Activity', '2014-04-05 12:02:21', NULL, 2, 1),
(749, 4, 'Activity', '2014-04-05 12:02:21', NULL, 2, 1),
(750, 4, 'Activity', '2014-04-05 12:02:21', NULL, 37, 4),
(751, 0, 'Activity', '2014-04-05 12:02:21', NULL, 4, 1),
(752, 4, 'Activity', '2014-04-05 12:02:21', NULL, 45, 4),
(753, 4, 'Activity', '2014-04-05 12:02:21', NULL, 23, 3),
(754, 5, 'Activity', '2014-04-05 12:02:21', NULL, 42, 4),
(755, 5, 'Activity', '2014-04-05 12:02:21', NULL, 2, 1),
(756, 5, 'Activity', '2014-04-05 12:02:21', NULL, 45, 4),
(757, 5, 'Activity', '2014-04-05 12:02:21', NULL, 4, 1),
(758, 5, 'Activity', '2014-04-05 12:02:21', NULL, 38, 4),
(759, 5, 'Activity', '2014-04-05 12:02:21', NULL, 2, 1),
(760, 5, 'Activity', '2014-04-05 12:02:21', NULL, 45, 4),
(761, 5, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(762, 5, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(763, 5, 'Activity', '2014-04-05 12:02:21', NULL, 17, 2),
(764, 2, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(765, 1, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(766, 2, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(767, 1, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(768, 3, 'Activity', '2014-04-05 12:02:21', NULL, 44, 4),
(769, 3, 'Activity', '2014-04-05 12:02:21', NULL, 37, 4),
(770, 3, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(771, 1, 'Activity', '2014-04-05 12:02:21', NULL, 14, 2),
(772, 1, 'Activity', '2014-04-05 12:02:21', NULL, 23, 3),
(773, 1, 'Activity', '2014-04-05 12:02:21', NULL, 23, 3),
(774, 1, 'Activity', '2014-04-05 12:02:21', NULL, 23, 3),
(775, 1, 'Activity', '2014-04-05 12:02:21', NULL, 2, 1),
(776, 1, 'Activity', '2014-04-05 12:02:21', NULL, 2, 1),
(777, 5, 'Activity', '2014-04-05 12:02:21', NULL, 37, 4),
(778, 5, 'Activity', '2014-04-05 12:02:21', NULL, 45, 4),
(779, 5, 'Activity', '2014-04-05 12:02:21', NULL, 43, 4),
(780, 5, 'Activity', '2014-04-05 12:02:21', NULL, 42, 4),
(781, 3, 'Activity', '2014-04-05 12:02:21', NULL, 52, 2),
(782, 3, 'Activity', '2014-04-05 12:02:22', NULL, 43, 4),
(783, 3, 'Activity', '2014-04-05 12:02:22', NULL, 45, 4),
(784, 3, 'Activity', '2014-04-05 12:02:22', NULL, 37, 4),
(785, 3, 'Activity', '2014-04-05 12:02:22', NULL, 45, 4),
(786, 3, 'Activity', '2014-04-05 12:02:22', NULL, 37, 4),
(787, 3, 'Activity', '2014-04-05 12:02:22', NULL, 15, 2),
(788, 5, 'Activity', '2014-04-05 12:02:22', NULL, 52, 2),
(789, 5, 'Activity', '2014-04-05 12:02:22', NULL, 52, 2),
(790, 2, 'Activity', '2014-04-05 12:02:22', NULL, 52, 2),
(791, 5, 'Activity', '2014-04-05 12:02:22', NULL, 37, 4),
(792, 5, 'Activity', '2014-04-05 12:02:22', NULL, 44, 4),
(793, 5, 'Activity', '2014-04-05 12:02:22', NULL, 45, 4),
(794, 5, 'Activity', '2014-04-05 12:02:22', NULL, 40, 4),
(795, 5, 'Activity', '2014-04-05 12:02:22', NULL, 17, 2),
(796, 5, 'Activity', '2014-04-05 12:02:22', NULL, 37, 4),
(797, 5, 'Activity', '2014-04-05 12:02:22', NULL, 43, 4),
(798, 5, 'Activity', '2014-04-05 12:02:22', NULL, 40, 4),
(799, 5, 'Activity', '2014-04-05 12:02:22', NULL, 45, 4),
(800, 5, 'Activity', '2014-04-05 12:02:22', NULL, 23, 3),
(801, 3, 'Activity', '2014-04-05 12:02:22', NULL, 15, 2),
(802, 1, 'Activity', '2014-04-05 12:02:22', NULL, 15, 2),
(803, 3, 'Activity', '2014-04-05 12:02:22', NULL, 2, 1),
(804, 3, 'Activity', '2014-04-05 12:02:22', NULL, 37, 4),
(805, 3, 'Activity', '2014-04-05 12:02:22', NULL, 45, 4),
(806, 1, 'Activity', '2014-04-05 12:02:23', NULL, 52, 2),
(807, 3, 'Activity', '2014-04-05 12:02:23', NULL, 15, 2),
(808, 1, 'Activity', '2014-04-05 12:02:23', NULL, 10, 1),
(809, 3, 'Activity', '2014-04-05 12:02:23', NULL, 52, 2),
(810, 1, 'Activity', '2014-04-05 12:02:23', NULL, 10, 1),
(811, 1, 'Activity', '2014-04-05 12:02:23', NULL, 37, 4),
(812, 1, 'Activity', '2014-04-05 12:02:23', NULL, 43, 4),
(813, 3, 'Activity', '2014-04-05 12:02:23', NULL, 43, 4),
(814, 1, 'Activity', '2014-04-05 12:02:23', NULL, 45, 4),
(815, 1, 'Activity', '2014-04-05 12:02:23', NULL, 46, 2),
(816, 1, 'Activity', '2014-04-05 12:02:23', NULL, 15, 2),
(817, 1, 'Activity', '2014-04-05 12:02:23', NULL, 2, 1),
(818, 1, 'Activity', '2014-04-05 12:02:23', NULL, 2, 1),
(819, 2, 'Activity', '2014-04-05 12:02:23', NULL, 52, 2),
(820, 5, 'Activity', '2014-04-05 12:02:23', NULL, 52, 2),
(821, 4, 'Activity', '2014-04-05 12:02:23', NULL, 2, 1),
(822, 4, 'Activity', '2014-04-05 12:02:23', NULL, 2, 1),
(823, 4, 'Activity', '2014-04-05 12:02:23', NULL, 22, 2),
(824, 0, 'Activity', '2014-04-05 12:02:23', NULL, 2, 1),
(825, 4, 'Activity', '2014-04-05 12:02:23', NULL, 44, 4),
(826, 4, 'Activity', '2014-04-05 12:02:23', NULL, 37, 4),
(827, 5, 'Activity', '2014-04-05 12:02:23', NULL, 23, 3),
(828, 0, 'Activity', '2014-04-05 12:02:23', NULL, NULL, NULL),
(829, 5, 'Activity', '2014-04-05 12:02:24', NULL, 37, 4),
(830, 5, 'Activity', '2014-04-05 12:02:24', NULL, 44, 4),
(831, 5, 'Activity', '2014-04-05 12:02:24', NULL, 36, 3),
(832, 5, 'Activity', '2014-04-05 12:02:24', NULL, 43, 4),
(833, 5, 'Activity', '2014-04-05 12:02:24', NULL, 37, 4),
(834, 5, 'Activity', '2014-04-05 12:02:24', NULL, 45, 4),
(835, 5, 'Activity', '2014-04-05 12:02:24', NULL, 43, 4),
(836, 1, 'Activity', '2014-04-05 12:02:24', NULL, 15, 2),
(837, 1, 'Activity', '2014-04-05 12:02:24', NULL, 43, 4),
(838, 1, 'Activity', '2014-04-05 12:02:24', NULL, 44, 4),
(839, 1, 'Activity', '2014-04-05 12:02:24', NULL, 37, 4),
(840, 1, 'Activity', '2014-04-05 12:02:24', NULL, 14, 2),
(841, 3, 'Activity', '2014-04-05 12:02:24', NULL, 15, 2),
(842, 3, 'Activity', '2014-04-05 12:02:24', NULL, 43, 4),
(843, 3, 'Activity', '2014-04-05 12:02:24', NULL, 44, 4),
(844, 3, 'Activity', '2014-04-05 12:02:24', NULL, 37, 4),
(845, 5, 'Activity', '2014-04-05 12:02:24', NULL, 42, 4),
(846, 5, 'Activity', '2014-04-05 12:02:24', NULL, 37, 4),
(847, 5, 'Activity', '2014-04-05 12:02:25', NULL, 45, 4),
(848, 5, 'Activity', '2014-04-05 12:02:25', NULL, 40, 4),
(849, 5, 'Activity', '2014-04-05 12:02:25', NULL, 43, 4),
(850, 5, 'Activity', '2014-04-05 12:02:25', NULL, 56, 3),
(851, 0, 'Activity', '2014-04-05 12:02:25', NULL, NULL, NULL),
(852, 3, 'Activity', '2014-04-05 12:02:25', NULL, 17, 2),
(853, 3, 'Activity', '2014-04-05 12:02:25', NULL, 15, 2),
(854, 3, 'Activity', '2014-04-05 12:02:25', NULL, 44, 4),
(855, 3, 'Activity', '2014-04-05 12:02:25', NULL, 37, 4),
(856, 3, 'Activity', '2014-04-05 12:02:25', NULL, 43, 4),
(857, 3, 'Activity', '2014-04-05 12:02:25', NULL, 10, 1),
(858, 3, 'Activity', '2014-04-05 12:02:25', NULL, 1, 1),
(859, 1, 'Activity', '2014-04-05 12:02:25', NULL, 15, 2),
(860, 1, 'Activity', '2014-04-05 12:02:25', NULL, 1, 1),
(861, 1, 'Activity', '2014-04-05 12:02:25', NULL, 10, 1),
(862, 1, 'Activity', '2014-04-05 12:02:25', NULL, 58, 2),
(863, 1, 'Activity', '2014-04-05 12:02:25', NULL, 58, 2),
(864, 1, 'Activity', '2014-04-05 12:02:25', NULL, 58, 2),
(865, 1, 'Activity', '2014-04-05 12:02:25', NULL, 58, 2),
(866, 3, 'Activity', '2014-04-05 12:02:25', NULL, 58, 2),
(867, 1, 'Activity', '2014-04-05 12:02:26', NULL, 44, 4),
(868, 1, 'Activity', '2014-04-05 12:02:26', NULL, 37, 4),
(869, 1, 'Activity', '2014-04-05 12:02:26', NULL, 43, 4),
(870, 3, 'Activity', '2014-04-05 12:02:26', NULL, 58, 2),
(871, 3, 'Activity', '2014-04-05 12:02:26', NULL, 43, 4),
(872, 3, 'Activity', '2014-04-05 12:02:26', NULL, 58, 2),
(873, 3, 'Activity', '2014-04-05 12:02:26', NULL, 58, 2),
(874, 3, 'Activity', '2014-04-05 12:02:26', NULL, 23, 3),
(875, 0, 'Activity', '2014-04-05 12:02:26', NULL, 3, 1),
(876, 3, 'Activity', '2014-04-05 12:02:26', NULL, 17, 2),
(877, 4, 'Activity', '2014-04-05 12:02:26', NULL, 12, 2),
(878, 4, 'Activity', '2014-04-05 12:02:26', NULL, 12, 2),
(879, 5, 'Activity', '2014-04-05 12:02:26', NULL, 12, 2),
(880, 5, 'Activity', '2014-04-05 12:02:26', NULL, 37, 4),
(881, 5, 'Activity', '2014-04-05 12:02:26', NULL, 44, 4),
(882, 5, 'Activity', '2014-04-05 12:02:26', NULL, 43, 4),
(883, 5, 'Activity', '2014-04-05 12:02:27', NULL, 15, 2),
(884, 1, 'Activity', '2014-04-05 12:02:27', NULL, 15, 2),
(885, 1, 'Activity', '2014-04-05 12:02:27', NULL, 43, 4),
(886, 1, 'Activity', '2014-04-05 12:02:27', NULL, 1, 1),
(887, 1, 'Activity', '2014-04-05 12:02:27', NULL, 37, 4),
(888, 1, 'Activity', '2014-04-05 12:02:27', NULL, 45, 4),
(889, 1, 'Activity', '2014-04-05 12:02:27', NULL, 40, 4),
(890, 1, 'Activity', '2014-04-05 12:02:27', NULL, 44, 4),
(891, 1, 'Activity', '2014-04-05 12:02:27', NULL, 44, 4),
(892, 1, 'Activity', '2014-04-05 12:02:27', NULL, 58, 2),
(893, 1, 'Activity', '2014-04-05 12:02:27', NULL, 2, 1),
(894, 3, 'Activity', '2014-04-05 12:02:27', NULL, 17, 2),
(895, 3, 'Activity', '2014-04-05 12:02:27', NULL, 17, 2),
(896, 3, 'Activity', '2014-04-05 12:02:27', NULL, 58, 2);
INSERT INTO `timeline` (`id`, `player_id`, `what`, `when`, `badge_id`, `activity_id`, `domain_id`) VALUES
(897, 0, 'Activity', '2014-04-05 12:02:27', NULL, 2, 1),
(898, 1, 'Activity', '2014-04-05 12:02:27', NULL, 57, 4),
(899, 1, 'Activity', '2014-04-05 12:02:27', NULL, 52, 2),
(900, 1, 'Activity', '2014-04-05 12:02:27', NULL, 14, 2),
(901, 2, 'Activity', '2014-04-05 12:02:27', NULL, 52, 2),
(902, 1, 'Activity', '2014-04-05 12:02:27', NULL, 17, 2),
(903, 1, 'Activity', '2014-04-05 12:02:27', NULL, 37, 4),
(904, 1, 'Activity', '2014-04-05 12:02:27', NULL, 45, 4),
(905, 2, 'Activity', '2014-04-05 12:02:27', NULL, 2, 1),
(906, 2, 'Activity', '2014-04-05 12:02:28', NULL, 37, 4),
(907, 2, 'Activity', '2014-04-05 12:02:28', NULL, 44, 4),
(908, 2, 'Activity', '2014-04-05 12:02:28', NULL, 17, 2),
(909, 3, 'Activity', '2014-04-05 12:02:28', NULL, 58, 2),
(910, 3, 'Activity', '2014-04-05 12:02:28', NULL, 58, 2),
(911, 3, 'Activity', '2014-04-05 12:02:28', NULL, 42, 4),
(912, 3, 'Activity', '2014-04-05 12:02:28', NULL, 1, 1),
(913, 2, 'Activity', '2014-04-05 12:02:28', NULL, 52, 2),
(914, 5, 'Activity', '2014-04-05 12:02:28', NULL, 52, 2),
(915, 5, 'Activity', '2014-04-05 12:02:28', NULL, 37, 4),
(916, 5, 'Activity', '2014-04-05 12:02:28', NULL, 44, 4),
(917, 5, 'Activity', '2014-04-05 12:02:28', NULL, 44, 4),
(918, 5, 'Activity', '2014-04-05 12:02:28', NULL, 43, 4),
(919, 1, 'Activity', '2014-04-05 12:02:28', NULL, 58, 2),
(920, 1, 'Activity', '2014-04-05 12:02:28', NULL, 36, 3),
(921, 1, 'Activity', '2014-04-05 12:02:28', NULL, 36, 3),
(922, 1, 'Activity', '2014-04-05 12:02:28', NULL, 43, 4),
(923, 1, 'Activity', '2014-04-05 12:02:28', NULL, 52, 2),
(924, 2, 'Activity', '2014-04-05 12:02:28', NULL, 2, 1),
(925, 2, 'Activity', '2014-04-05 12:02:28', NULL, 37, 4),
(926, 2, 'Activity', '2014-04-05 12:02:29', NULL, 44, 4),
(927, 1, 'Activity', '2014-04-05 12:02:29', NULL, 44, 4),
(928, 2, 'Activity', '2014-04-05 12:02:29', NULL, 43, 4),
(929, 1, 'Activity', '2014-04-05 12:02:29', NULL, 37, 4),
(930, 2, 'Activity', '2014-04-05 12:02:29', NULL, 15, 2),
(931, 2, 'Activity', '2014-04-05 12:02:29', NULL, 15, 2),
(932, 1, 'Activity', '2014-04-05 12:02:29', NULL, 44, 4),
(933, 1, 'Activity', '2014-04-05 12:02:29', NULL, 37, 4),
(934, 1, 'Activity', '2014-04-05 12:02:29', NULL, 43, 4),
(935, 3, 'Activity', '2014-04-05 12:02:29', NULL, 15, 2),
(936, 3, 'Activity', '2014-04-05 12:02:29', NULL, 44, 4),
(937, 3, 'Activity', '2014-04-05 12:02:29', NULL, 15, 2),
(938, 3, 'Activity', '2014-04-05 12:02:29', NULL, 44, 4),
(939, 3, 'Activity', '2014-04-05 12:02:29', NULL, 37, 4),
(940, 3, 'Activity', '2014-04-05 12:02:29', NULL, 37, 4),
(941, 3, 'Activity', '2014-04-05 12:02:29', NULL, 43, 4),
(942, 3, 'Activity', '2014-04-05 12:02:29', NULL, 43, 4),
(943, 5, 'Activity', '2014-04-05 12:02:29', NULL, 52, 2),
(944, 2, 'Activity', '2014-04-05 12:02:29', NULL, 52, 2),
(945, 2, 'Activity', '2014-04-05 12:02:29', NULL, 62, 2),
(946, 3, 'Activity', '2014-04-05 12:02:29', NULL, 52, 2),
(947, 3, 'Activity', '2014-04-05 12:02:29', NULL, 37, 4),
(948, 3, 'Activity', '2014-04-05 12:02:29', NULL, 44, 4),
(949, 2, 'Activity', '2014-04-05 12:02:29', NULL, 52, 2),
(950, 4, 'Activity', '2014-04-05 12:02:29', NULL, 52, 2),
(951, 4, 'Activity', '2014-04-05 12:02:30', NULL, 52, 2),
(952, 4, 'Activity', '2014-04-05 12:02:30', NULL, 52, 2),
(953, 4, 'Activity', '2014-04-05 12:02:30', NULL, 37, 4),
(954, 4, 'Activity', '2014-04-05 12:02:30', NULL, 37, 4),
(955, 4, 'Activity', '2014-04-05 12:02:30', NULL, 37, 4),
(956, 4, 'Activity', '2014-04-05 12:02:30', NULL, 37, 4),
(957, 4, 'Activity', '2014-04-05 12:02:30', NULL, 44, 4),
(958, 4, 'Activity', '2014-04-05 12:02:30', NULL, 44, 4),
(959, 4, 'Activity', '2014-04-05 12:02:30', NULL, 44, 4),
(960, 4, 'Activity', '2014-04-05 12:02:30', NULL, 44, 4),
(961, 4, 'Activity', '2014-04-05 12:02:30', NULL, 2, 1),
(962, 4, 'Activity', '2014-04-05 12:02:30', NULL, 2, 1),
(963, 0, 'Activity', '2014-04-05 12:02:30', NULL, 1, 1),
(964, 4, 'Activity', '2014-04-05 12:02:30', NULL, 2, 1),
(965, 0, 'Activity', '2014-04-05 12:02:30', NULL, 1, 1),
(966, 4, 'Activity', '2014-04-05 12:02:30', NULL, 2, 1),
(967, 4, 'Activity', '2014-04-05 12:02:30', NULL, 2, 1),
(968, 4, 'Activity', '2014-04-05 12:02:30', NULL, 2, 1),
(969, 4, 'Activity', '2014-04-05 12:02:30', NULL, 2, 1),
(970, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(971, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(972, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(973, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(974, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(975, 0, 'Activity', '2014-04-05 12:02:31', NULL, 1, 1),
(976, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(977, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(978, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(979, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(980, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(981, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(982, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(983, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(984, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(985, 4, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(986, 1, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(987, 1, 'Activity', '2014-04-05 12:02:31', NULL, 2, 1),
(988, 1, 'Activity', '2014-04-05 12:02:31', NULL, 44, 4),
(989, 1, 'Activity', '2014-04-05 12:02:31', NULL, 44, 4),
(990, 1, 'Activity', '2014-04-05 12:02:31', NULL, 37, 4),
(991, 1, 'Activity', '2014-04-05 12:02:31', NULL, 37, 4),
(992, 1, 'Activity', '2014-04-05 12:02:31', NULL, 52, 2),
(993, 2, 'Activity', '2014-04-05 12:02:31', NULL, 52, 2),
(994, 5, 'Activity', '2014-04-05 12:02:32', NULL, 52, 2),
(995, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(996, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(997, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(998, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(999, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(1000, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(1001, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(1002, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(1003, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(1004, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(1005, 5, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(1006, 4, 'Activity', '2014-04-05 12:02:32', NULL, 17, 2),
(1007, 5, 'Activity', '2014-04-05 12:02:32', NULL, 9, 1),
(1008, 3, 'Activity', '2014-04-05 12:02:32', NULL, 17, 2),
(1009, 3, 'Activity', '2014-04-05 12:02:32', NULL, 42, 4),
(1010, 3, 'Activity', '2014-04-05 12:02:32', NULL, 37, 4),
(1011, 3, 'Activity', '2014-04-05 12:02:32', NULL, 44, 4),
(1012, 3, 'Activity', '2014-04-05 12:02:32', NULL, 2, 1),
(1013, 2, 'Activity', '2014-04-05 12:02:33', NULL, 52, 2),
(1014, 3, 'Activity', '2014-04-05 12:02:33', NULL, 52, 2),
(1015, 3, 'Activity', '2014-04-05 12:02:33', NULL, 2, 1),
(1016, 3, 'Activity', '2014-04-05 12:02:33', NULL, 42, 4),
(1017, 1, 'Activity', '2014-04-05 12:02:33', NULL, 37, 4),
(1018, 1, 'Activity', '2014-04-05 12:02:33', NULL, 44, 4),
(1019, 1, 'Activity', '2014-04-05 12:02:33', NULL, 14, 2),
(1020, 5, 'Activity', '2014-04-05 12:02:33', NULL, 61, 2),
(1021, 2, 'Activity', '2014-04-05 12:02:33', NULL, 62, 2),
(1022, 2, 'Activity', '2014-04-05 12:02:33', NULL, 52, 2),
(1023, 1, 'Activity', '2014-04-05 12:02:33', NULL, 62, 2),
(1024, 3, 'Activity', '2014-04-05 12:02:33', NULL, 15, 2),
(1025, 3, 'Activity', '2014-04-05 12:02:33', NULL, 43, 4),
(1026, 3, 'Activity', '2014-04-05 12:02:33', NULL, 37, 4),
(1027, 3, 'Activity', '2014-04-05 12:02:33', NULL, 44, 4),
(1028, 3, 'Activity', '2014-04-05 12:02:33', NULL, 17, 2),
(1029, 1, 'Activity', '2014-04-05 12:02:33', NULL, 15, 2),
(1030, 1, 'Activity', '2014-04-05 12:02:33', NULL, 46, 2),
(1031, 1, 'Activity', '2014-04-05 12:02:33', NULL, 40, 4),
(1032, 1, 'Activity', '2014-04-05 12:02:33', NULL, 37, 4),
(1033, 1, 'Activity', '2014-04-05 12:02:33', NULL, 44, 4),
(1034, 1, 'Activity', '2014-04-05 12:02:33', NULL, 14, 2),
(1035, 1, 'Activity', '2014-04-05 12:02:34', NULL, 43, 4),
(1036, 5, 'Activity', '2014-04-05 12:02:34', NULL, 37, 4),
(1037, 5, 'Activity', '2014-04-05 12:02:34', NULL, 44, 4),
(1038, 5, 'Activity', '2014-04-05 12:02:34', NULL, 43, 4),
(1039, 5, 'Activity', '2014-04-05 12:02:34', NULL, 52, 2),
(1040, 5, 'Activity', '2014-04-05 12:02:34', NULL, 62, 2),
(1041, 1, 'Activity', '2014-04-05 12:02:34', NULL, 8, 1),
(1042, 1, 'Activity', '2014-04-05 12:02:34', NULL, 8, 1),
(1043, 1, 'Activity', '2014-04-05 12:02:34', NULL, 8, 1),
(1044, 1, 'Activity', '2014-04-05 12:02:34', NULL, 8, 1),
(1045, 1, 'Activity', '2014-04-05 12:02:34', NULL, 8, 1),
(1046, 4, 'Activity', '2014-04-05 12:02:34', NULL, 52, 2),
(1047, 3, 'Activity', '2014-04-05 12:02:34', NULL, 42, 4),
(1048, 5, 'Activity', '2014-04-05 12:02:34', NULL, 37, 4),
(1049, 5, 'Activity', '2014-04-05 12:02:34', NULL, 45, 4),
(1050, 5, 'Activity', '2014-04-05 12:02:34', NULL, 42, 4),
(1051, 5, 'Activity', '2014-04-05 12:02:34', NULL, 23, 3),
(1052, 5, 'Activity', '2014-04-05 12:02:34', NULL, 23, 3),
(1053, 5, 'Activity', '2014-04-05 12:02:34', NULL, 23, 3),
(1054, 5, 'Activity', '2014-04-05 12:02:34', NULL, 37, 4),
(1055, 5, 'Activity', '2014-04-05 12:02:34', NULL, 40, 4),
(1056, 5, 'Activity', '2014-04-05 12:02:34', NULL, 45, 4),
(1057, 5, 'Activity', '2014-04-05 12:02:34', NULL, 23, 3),
(1058, 2, 'Activity', '2014-04-05 12:02:35', NULL, 2, 1),
(1059, 2, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1060, 5, 'Activity', '2014-04-05 12:02:35', NULL, 23, 3),
(1061, 4, 'Activity', '2014-04-05 12:02:35', NULL, 2, 1),
(1062, 4, 'Activity', '2014-04-05 12:02:35', NULL, 2, 1),
(1063, 4, 'Activity', '2014-04-05 12:02:35', NULL, 44, 4),
(1064, 4, 'Activity', '2014-04-05 12:02:35', NULL, 40, 4),
(1065, 1, 'Activity', '2014-04-05 12:02:35', NULL, 14, 2),
(1066, 5, 'Activity', '2014-04-05 12:02:35', NULL, 45, 4),
(1067, 5, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1068, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1069, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1070, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1071, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1072, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1073, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1074, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1075, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1076, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1077, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1078, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1079, 3, 'Activity', '2014-04-05 12:02:35', NULL, 42, 4),
(1080, 5, 'Activity', '2014-04-05 12:02:35', NULL, 23, 3),
(1081, 5, 'Activity', '2014-04-05 12:02:35', NULL, 37, 4),
(1082, 5, 'Activity', '2014-04-05 12:02:36', NULL, 42, 4),
(1083, 5, 'Activity', '2014-04-05 12:02:36', NULL, 40, 4),
(1084, 5, 'Activity', '2014-04-05 12:02:36', NULL, 43, 4),
(1085, 5, 'Activity', '2014-04-05 12:02:36', NULL, 23, 3),
(1086, 5, 'Activity', '2014-04-05 12:02:36', NULL, 23, 3),
(1087, 5, 'Activity', '2014-04-05 12:02:36', NULL, 23, 3),
(1088, 1, 'Activity', '2014-04-05 12:02:36', NULL, 61, 2),
(1089, 3, 'Activity', '2014-04-05 12:02:36', NULL, 58, 2),
(1090, 3, 'Activity', '2014-04-05 12:02:36', NULL, 42, 4),
(1091, 3, 'Activity', '2014-04-05 12:02:36', NULL, 23, 3),
(1092, 5, 'Activity', '2014-04-05 12:02:36', NULL, 37, 4),
(1093, 5, 'Activity', '2014-04-05 12:02:36', NULL, 44, 4),
(1094, 5, 'Activity', '2014-04-05 12:02:36', NULL, 43, 4),
(1095, 5, 'Activity', '2014-04-05 12:02:36', NULL, 37, 4),
(1096, 5, 'Activity', '2014-04-05 12:02:36', NULL, 42, 4),
(1097, 5, 'Activity', '2014-04-05 12:02:36', NULL, 40, 4),
(1098, 5, 'Activity', '2014-04-05 12:02:36', NULL, 40, 4),
(1099, 1, 'Activity', '2014-04-05 12:02:37', NULL, 40, 4),
(1100, 1, 'Activity', '2014-04-05 12:02:37', NULL, 45, 4),
(1101, 1, 'Activity', '2014-04-05 12:02:37', NULL, 37, 4),
(1102, 5, 'Activity', '2014-04-05 12:02:37', NULL, 7, 1),
(1103, 5, 'Activity', '2014-04-05 12:02:37', NULL, 42, 4),
(1104, 5, 'Activity', '2014-04-05 12:02:37', NULL, 42, 4),
(1105, 5, 'Activity', '2014-04-05 12:02:37', NULL, 37, 4),
(1106, 5, 'Activity', '2014-04-05 12:02:37', NULL, 45, 4),
(1107, 5, 'Activity', '2014-04-05 12:02:37', NULL, 40, 4),
(1108, 5, 'Activity', '2014-04-05 12:02:37', NULL, 7, 1),
(1109, 5, 'Activity', '2014-04-05 12:02:37', NULL, 23, 3),
(1110, 5, 'Activity', '2014-04-05 12:02:37', NULL, 36, 3),
(1111, 4, 'Activity', '2014-04-05 12:02:37', NULL, 2, 1),
(1112, 4, 'Activity', '2014-04-05 12:02:37', NULL, 36, 3),
(1113, 1, 'Activity', '2014-04-05 12:02:37', NULL, 56, 3),
(1114, 1, 'Activity', '2014-04-05 12:02:37', NULL, 14, 2),
(1115, 3, 'Activity', '2014-04-05 12:02:37', NULL, 42, 4),
(1116, 3, 'Activity', '2014-04-05 12:02:37', NULL, 42, 4),
(1117, 3, 'Activity', '2014-04-05 12:02:38', NULL, 52, 2),
(1118, 1, 'Activity', '2014-04-05 12:02:38', NULL, 40, 4),
(1119, 1, 'Activity', '2014-04-05 12:02:38', NULL, 45, 4),
(1120, 1, 'Activity', '2014-04-05 12:02:38', NULL, 37, 4),
(1121, 1, 'Activity', '2014-04-05 12:02:38', NULL, 43, 4),
(1122, 1, 'Activity', '2014-04-05 12:02:38', NULL, 42, 4),
(1123, 1, 'Activity', '2014-04-05 12:02:38', NULL, 15, 2),
(1124, 4, 'Activity', '2014-04-05 12:02:38', NULL, 52, 2),
(1125, 5, 'Activity', '2014-04-05 12:02:38', NULL, 52, 2),
(1126, 1, 'Activity', '2014-04-05 12:02:38', NULL, 52, 2),
(1127, 3, 'Activity', '2014-04-05 12:02:38', NULL, 52, 2),
(1128, 3, 'Activity', '2014-04-05 12:02:38', NULL, 52, 2),
(1129, 1, 'Activity', '2014-04-05 12:02:38', NULL, 52, 2),
(1130, 3, 'Activity', '2014-04-05 12:02:39', NULL, 52, 2),
(1131, 1, 'Activity', '2014-04-05 12:02:39', NULL, 52, 2),
(1132, 5, 'Activity', '2014-04-05 12:02:39', NULL, 52, 2),
(1133, 5, 'Activity', '2014-04-05 12:02:39', NULL, 37, 4),
(1134, 5, 'Activity', '2014-04-05 12:02:39', NULL, 42, 4),
(1135, 5, 'Activity', '2014-04-05 12:02:39', NULL, 23, 3),
(1136, 5, 'Activity', '2014-04-05 12:02:39', NULL, 40, 4),
(1137, 5, 'Activity', '2014-04-05 12:02:39', NULL, 23, 3),
(1138, 5, 'Activity', '2014-04-05 12:02:39', NULL, 23, 3),
(1139, 2, 'Activity', '2014-04-05 12:02:39', NULL, 52, 2),
(1140, 2, 'Activity', '2014-04-05 12:02:39', NULL, 15, 2),
(1141, 4, 'Activity', '2014-04-05 12:02:39', NULL, 4, 1),
(1142, 4, 'Activity', '2014-04-05 12:02:39', NULL, 2, 1),
(1143, 4, 'Activity', '2014-04-05 12:02:39', NULL, 44, 4),
(1144, 4, 'Activity', '2014-04-05 12:02:39', NULL, 37, 4),
(1145, 4, 'Activity', '2014-04-05 12:02:39', NULL, 52, 2),
(1146, 4, 'Activity', '2014-04-05 12:02:39', NULL, 52, 2),
(1147, 4, 'Activity', '2014-04-05 12:02:39', NULL, 52, 2),
(1148, 1, 'Activity', '2014-04-05 12:02:39', NULL, 2, 1),
(1149, 1, 'Activity', '2014-04-05 12:02:39', NULL, 15, 2),
(1150, 1, 'Activity', '2014-04-05 12:02:39', NULL, 16, 2),
(1151, 1, 'Activity', '2014-04-05 12:02:39', NULL, 16, 2),
(1152, 1, 'Activity', '2014-04-05 12:02:40', NULL, 37, 4),
(1153, 1, 'Activity', '2014-04-05 12:02:40', NULL, 44, 4),
(1154, 1, 'Activity', '2014-04-05 12:02:40', NULL, 43, 4),
(1155, 1, 'Activity', '2014-04-05 12:02:40', NULL, 37, 4),
(1156, 1, 'Activity', '2014-04-05 12:02:40', NULL, 43, 4),
(1157, 1, 'Activity', '2014-04-05 12:02:40', NULL, 44, 4),
(1158, 1, 'Activity', '2014-04-05 12:02:40', NULL, 2, 1),
(1159, 4, 'Activity', '2014-04-05 12:02:40', NULL, 2, 1),
(1160, 4, 'Activity', '2014-04-05 12:02:40', NULL, 37, 4),
(1161, 4, 'Activity', '2014-04-05 12:02:40', NULL, 45, 4),
(1162, 3, 'Activity', '2014-04-05 12:02:40', NULL, 58, 2),
(1163, 3, 'Activity', '2014-04-05 12:02:40', NULL, 58, 2),
(1164, 3, 'Activity', '2014-04-05 12:02:40', NULL, 39, 4),
(1165, 3, 'Activity', '2014-04-05 12:02:40', NULL, 45, 4),
(1166, 3, 'Activity', '2014-04-05 12:02:40', NULL, 58, 2),
(1167, 3, 'Activity', '2014-04-05 12:02:40', NULL, 37, 4),
(1168, 3, 'Activity', '2014-04-05 12:02:40', NULL, 45, 4),
(1169, 3, 'Activity', '2014-04-05 12:02:40', NULL, 37, 4),
(1170, 3, 'Activity', '2014-04-05 12:02:40', NULL, 45, 4),
(1171, 3, 'Activity', '2014-04-05 12:02:40', NULL, 37, 4),
(1172, 3, 'Activity', '2014-04-05 12:02:40', NULL, 45, 4),
(1173, 3, 'Activity', '2014-04-05 12:02:40', NULL, 2, 1),
(1174, 4, 'Activity', '2014-04-05 12:02:40', NULL, 2, 1),
(1175, 4, 'Activity', '2014-04-05 12:02:41', NULL, 2, 1),
(1176, 4, 'Activity', '2014-04-05 12:02:41', NULL, 45, 4),
(1177, 4, 'Activity', '2014-04-05 12:02:41', NULL, 2, 1),
(1178, 4, 'Activity', '2014-04-05 12:02:41', NULL, 44, 4),
(1179, 4, 'Activity', '2014-04-05 12:02:41', NULL, 37, 4),
(1180, 2, 'Activity', '2014-04-05 12:02:41', NULL, 52, 2),
(1181, 2, 'Activity', '2014-04-05 12:02:41', NULL, 52, 2),
(1182, 2, 'Activity', '2014-04-05 12:02:41', NULL, 15, 2),
(1183, 1, 'Activity', '2014-04-05 12:02:41', NULL, 2, 1),
(1184, 1, 'Activity', '2014-04-05 12:02:41', NULL, 1, 1),
(1185, 1, 'Activity', '2014-04-05 12:02:41', NULL, 40, 4),
(1186, 1, 'Activity', '2014-04-05 12:02:41', NULL, 37, 4),
(1187, 1, 'Activity', '2014-04-05 12:02:41', NULL, 45, 4),
(1188, 1, 'Activity', '2014-04-05 12:02:41', NULL, 43, 4),
(1189, 1, 'Activity', '2014-04-05 12:02:41', NULL, 42, 4),
(1190, 1, 'Activity', '2014-04-05 12:02:41', NULL, 23, 3),
(1191, 1, 'Activity', '2014-04-05 12:02:41', NULL, 15, 2),
(1192, 1, 'Activity', '2014-04-05 12:02:41', NULL, 62, 2),
(1193, 2, 'Activity', '2014-04-05 12:02:41', NULL, 2, 1),
(1194, 1, 'Activity', '2014-04-05 12:02:41', NULL, 2, 1),
(1195, 1, 'Activity', '2014-04-05 12:02:41', NULL, 10, 1),
(1196, 2, 'Activity', '2014-04-05 12:02:41', NULL, 62, 2),
(1197, 2, 'Activity', '2014-04-05 12:02:41', NULL, 1, 1),
(1198, 2, 'Activity', '2014-04-05 12:02:42', NULL, 2, 1),
(1199, 2, 'Activity', '2014-04-05 12:02:42', NULL, 40, 4),
(1200, 2, 'Activity', '2014-04-05 12:02:42', NULL, 37, 4),
(1201, 2, 'Activity', '2014-04-05 12:02:42', NULL, 45, 4),
(1202, 2, 'Activity', '2014-04-05 12:02:42', NULL, 43, 4),
(1203, 2, 'Activity', '2014-04-05 12:02:42', NULL, 37, 4),
(1204, 2, 'Activity', '2014-04-05 12:02:42', NULL, 10, 1),
(1205, 2, 'Activity', '2014-04-05 12:02:42', NULL, 44, 4),
(1206, 2, 'Activity', '2014-04-05 12:02:42', NULL, 43, 4),
(1207, 2, 'Activity', '2014-04-05 12:02:42', NULL, 14, 2),
(1208, 1, 'Activity', '2014-04-05 12:02:42', NULL, 52, 2),
(1209, 3, 'Activity', '2014-04-05 12:02:42', NULL, 52, 2),
(1210, 3, 'Activity', '2014-04-05 12:02:42', NULL, 58, 2),
(1211, 3, 'Activity', '2014-04-05 12:02:42', NULL, 42, 4),
(1212, 3, 'Activity', '2014-04-05 12:02:42', NULL, 2, 1),
(1213, 3, 'Activity', '2014-04-05 12:02:42', NULL, 45, 4),
(1214, 3, 'Activity', '2014-04-05 12:02:42', NULL, 62, 2),
(1215, 3, 'Activity', '2014-04-05 12:02:42', NULL, 37, 4),
(1216, 3, 'Activity', '2014-04-05 12:02:42', NULL, 52, 2),
(1217, 4, 'Activity', '2014-04-05 12:02:42', NULL, 42, 4),
(1218, 5, 'Activity', '2014-04-05 12:02:43', NULL, 23, 3),
(1219, 5, 'Activity', '2014-04-05 12:02:43', NULL, 36, 3),
(1220, 5, 'Activity', '2014-04-05 12:02:43', NULL, 42, 4),
(1221, 3, 'Activity', '2014-04-05 12:02:43', NULL, 58, 2),
(1222, 3, 'Activity', '2014-04-05 12:02:43', NULL, 42, 4),
(1223, 2, 'Activity', '2014-04-05 12:02:43', NULL, 23, 3),
(1224, 2, 'Activity', '2014-04-05 12:02:43', NULL, 36, 3),
(1225, 2, 'Activity', '2014-04-05 12:02:43', NULL, 42, 4),
(1226, 2, 'Activity', '2014-04-05 12:02:43', NULL, 52, 2),
(1227, 5, 'Activity', '2014-04-05 12:02:43', NULL, 52, 2),
(1228, 5, 'Activity', '2014-04-05 12:02:43', NULL, 15, 2),
(1229, 3, 'Activity', '2014-04-05 12:02:43', NULL, 2, 1),
(1230, 3, 'Activity', '2014-04-05 12:02:43', NULL, 37, 4),
(1231, 3, 'Activity', '2014-04-05 12:02:43', NULL, 45, 4),
(1232, 3, 'Activity', '2014-04-05 12:02:43', NULL, 58, 2),
(1233, 6, 'Activity', '2014-04-05 22:57:58', NULL, 9, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `title`
--

CREATE TABLE IF NOT EXISTS `title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL,
  `min_level` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `min_level` (`min_level`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Extraindo dados da tabela `title`
--

INSERT INTO `title` (`id`, `title`, `min_level`) VALUES
(1, 'Unfleshed', 0),
(2, 'Novice', 5),
(3, 'Apprentice', 10),
(4, 'Sorcerer', 20),
(5, 'Wizard', 30),
(6, 'Magician', 40),
(7, 'Archmage', 50),
(8, 'Maester', 60),
(9, 'Archmaester', 70);

-- --------------------------------------------------------

--
-- Estrutura da tabela `xp_log`
--

CREATE TABLE IF NOT EXISTS `xp_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `xp` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activity_id` int(10) unsigned DEFAULT NULL,
  `activity_id_reviewed` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_xp_log_player_id` (`player_id`),
  KEY `fk_xp_log_activity_id` (`activity_id`),
  KEY `fk_xp_log_activity_id_reviewed` (`activity_id_reviewed`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Extraindo dados da tabela `xp_log`
--

INSERT INTO `xp_log` (`id`, `player_id`, `xp`, `created`, `activity_id`, `activity_id_reviewed`) VALUES
(1, 6, 5, '2014-03-27 00:11:28', 2, NULL),
(2, 6, 50, '2014-03-27 00:12:31', 8, NULL),
(4, 1, 1935, '2014-03-29 16:41:16', NULL, NULL),
(5, 2, 362, '2014-03-29 16:41:16', NULL, NULL),
(6, 3, 704, '2014-03-29 16:41:16', NULL, NULL),
(7, 4, 725, '2014-03-29 16:41:16', NULL, NULL),
(8, 5, 699, '2014-03-29 16:41:16', NULL, NULL),
(9, 6, 205, '2014-03-29 16:41:16', NULL, NULL),
(11, 1, 20, '2014-03-29 16:53:28', 9, NULL),
(12, 6, 4, '2014-03-29 16:53:28', NULL, 9),
(13, 1, 20, '2014-03-29 16:55:30', 9, NULL),
(14, 6, 4, '2014-03-29 16:55:30', NULL, 9),
(15, 1, 0, '2014-04-02 22:21:11', 1, NULL),
(16, 6, 0, '2014-04-02 22:21:11', NULL, 1),
(17, 1, 20, '2014-04-03 00:40:09', 15, NULL),
(18, 6, 4, '2014-04-03 00:40:09', NULL, 15),
(19, 6, 30, '2014-04-04 23:39:49', 5, NULL),
(20, 6, 6, '2014-04-04 23:39:49', NULL, 5),
(21, 6, 30, '2014-04-04 23:39:52', 5, NULL),
(22, 6, 6, '2014-04-04 23:39:52', NULL, 5),
(23, 6, 0, '2014-04-04 23:39:54', 68, NULL),
(24, 6, 1, '2014-04-04 23:39:54', NULL, 68),
(25, 6, 20, '2014-04-04 23:49:32', 15, NULL),
(26, 6, 4, '2014-04-04 23:49:32', NULL, 15),
(27, 6, 100, '2014-04-04 23:55:24', 29, NULL),
(28, 6, 20, '2014-04-04 23:55:24', NULL, 29),
(29, 6, 30, '2014-04-04 23:55:44', 5, NULL),
(30, 6, 6, '2014-04-04 23:55:44', NULL, 5),
(31, 6, 2, '2014-04-04 23:55:46', 17, NULL),
(32, 6, 1, '2014-04-04 23:55:46', NULL, 17),
(33, 6, 5, '2014-04-04 23:55:49', 19, NULL),
(34, 6, 1, '2014-04-04 23:55:49', NULL, 19),
(35, 6, 15, '2014-04-04 23:56:26', 62, NULL),
(36, 6, 3, '2014-04-04 23:56:26', NULL, 62),
(37, 6, 50, '2014-04-04 23:56:36', 7, NULL),
(38, 6, 10, '2014-04-04 23:56:36', NULL, 7),
(39, 5, 1000, '2014-04-05 19:34:15', NULL, NULL),
(40, 5, 1000, '2014-04-05 19:34:32', NULL, NULL),
(41, 5, 100, '2014-04-05 19:34:46', NULL, NULL),
(42, 5, 10000, '2014-04-05 19:43:29', NULL, NULL),
(43, 6, 20, '2014-04-05 23:06:34', 9, NULL),
(44, 6, 4, '2014-04-05 23:06:34', NULL, 9);

--
-- Acionadores `xp_log`
--
DROP TRIGGER IF EXISTS `xp_log_ai`;
DELIMITER //
CREATE TRIGGER `xp_log_ai` AFTER INSERT ON `xp_log`
 FOR EACH ROW BEGIN
	
	DECLARE _player_xp INT(10) UNSIGNED DEFAULT NULL;
	DECLARE _level INT(10) UNSIGNED DEFAULT NULL;
	DECLARE _level_after_xp INT(10) UNSIGNED DEFAULT NULL;
	DECLARE _player_name VARCHAR(100) DEFAULT '';

	SELECT name, xp INTO _player_name, _player_xp FROM player WHERE id = NEW.player_id;

	SET _level = player_level(_player_xp);
	SET _level_after_xp = player_level(_player_xp + NEW.xp);

	UPDATE player SET xp = xp + NEW.xp WHERE id = NEW.player_id;

	-- Jogador avançou de nível
	IF (_level <> _level_after_xp) THEN
        
		-- Jogador atingiu nível 10 e desbloqueou as missões
		IF (_level_after_xp = 10) THEN
			CALL global_notification(
				'Level Up - Missions Unlocked',
				CONCAT(_player_name, ' reached level 10 and can now join Missions!'),
				'warning'
			);
		ELSEIF (_level_after_xp = 20) THEN
			CALL global_notification(
				'Level Up - Challenges Unlocked',
				CONCAT(_player_name, ' reached level 10 and can now join Challenges!'),
				'warning'
			);
		ELSE 
	        CALL global_notification(
	    		'Level Up',
	        	CONCAT(_player_name, ' reached level ', _level_after_xp, '!'),
	        	'warning'
	    	);

		END IF;

	END IF;

	
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure for view `activity_leaderboards`
--
DROP TABLE IF EXISTS `activity_leaderboards`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `activity_leaderboards` AS select count(0) AS `count`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name` from (`log` join `player` on((`player`.`id` = `log`.`player_id`))) where (`log`.`reviewed` is not null) group by `log`.`player_id` order by `count` desc;

-- --------------------------------------------------------

--
-- Structure for view `activity_leaderboards_last_month`
--
DROP TABLE IF EXISTS `activity_leaderboards_last_month`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `activity_leaderboards_last_month` AS select count(`log`.`id`) AS `count`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval ((dayofmonth(curdate()) + dayofmonth(last_day(curdate()))) - 1) day)) and (`log`.`acquired` < (curdate() - interval (dayofmonth(curdate()) - 1) day)) and (`log`.`reviewed` is not null)))) group by `player`.`id` order by `count` desc;

-- --------------------------------------------------------

--
-- Structure for view `activity_leaderboards_last_week`
--
DROP TABLE IF EXISTS `activity_leaderboards_last_week`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `activity_leaderboards_last_week` AS select count(`log`.`id`) AS `count`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofweek(curdate()) + 6) day)) and (`log`.`acquired` < (curdate() - interval (dayofweek(curdate()) - 1) day)) and (`log`.`reviewed` is not null)))) group by `player`.`id` order by `count` desc;

-- --------------------------------------------------------

--
-- Structure for view `activity_leaderboards_this_month`
--
DROP TABLE IF EXISTS `activity_leaderboards_this_month`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `activity_leaderboards_this_month` AS select count(`log`.`id`) AS `count`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofmonth(curdate()) - 1) day)) and (`log`.`acquired` <= curdate()) and (`log`.`reviewed` is not null)))) group by `player`.`id` order by `count` desc;

-- --------------------------------------------------------

--
-- Structure for view `activity_leaderboards_this_week`
--
DROP TABLE IF EXISTS `activity_leaderboards_this_week`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `activity_leaderboards_this_week` AS select count(`log`.`id`) AS `count`,`player`.`id` AS `player_id`,`player`.`name` AS `player_name` from (`player` left join `log` on(((`player`.`id` = `log`.`player_id`) and (`log`.`acquired` >= (curdate() - interval (dayofweek(curdate()) - 1) day)) and (`log`.`acquired` <= curdate()) and (`log`.`reviewed` is not null)))) group by `player`.`id` order by `count` desc;

-- --------------------------------------------------------

--
-- Structure for view `activity_ranking`
--
DROP TABLE IF EXISTS `activity_ranking`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `activity_ranking` AS select count(0) AS `count`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name` from (`log` join `player` on((`player`.`id` = `log`.`player_id`))) group by `log`.`player_id` order by `count` desc;

-- --------------------------------------------------------

--
-- Structure for view `badge_activity_progress`
--
DROP TABLE IF EXISTS `badge_activity_progress`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `badge_activity_progress` AS select `player`.`id` AS `player_id`,`badge`.`id` AS `badge_id`,`ar`.`activity_id` AS `activity_id`,count(`log`.`id`) AS `coins_obtained`,`ar`.`count` AS `coins_required` from (((`player` join `badge`) join `activity_requisite` `ar` on((`ar`.`badge_id` = `badge`.`id`))) left join `log` on(((`log`.`activity_id` = `ar`.`activity_id`) and (`log`.`player_id` = `player`.`id`) and (`log`.`spent` = 0) and (`log`.`reviewed` is not null)))) group by `player`.`id`,`ar`.`badge_id`,`ar`.`activity_id` order by `player`.`id`,`badge_id`,`ar`.`activity_id`;

-- --------------------------------------------------------

--
-- Structure for view `badge_claimed`
--
DROP TABLE IF EXISTS `badge_claimed`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `badge_claimed` AS select `player`.`id` AS `player_id`,`badge`.`id` AS `badge_id`,(`badge_log`.`id` is not null) AS `claimed` from ((`player` join `badge`) left join `badge_log` on(((`badge_log`.`player_id` = `player`.`id`) and (`badge_log`.`badge_id` = `badge`.`id`))));

-- --------------------------------------------------------

--
-- Structure for view `calendar_log`
--
DROP TABLE IF EXISTS `calendar_log`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `calendar_log` AS select count(0) AS `coins`,`log`.`player_id` AS `player_id`,`log`.`acquired` AS `acquired`,`log`.`domain_id` AS `domain_id`,`log`.`activity_id` AS `activity_id` from `log` group by `log`.`activity_id`,`log`.`player_id`,`log`.`acquired` order by `log`.`acquired`,`log`.`player_id`;

-- --------------------------------------------------------

--
-- Structure for view `different_activities_completed`
--
DROP TABLE IF EXISTS `different_activities_completed`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `different_activities_completed` AS select count(distinct `log`.`activity_id`) AS `different_activities_completed`,`log`.`domain_id` AS `domain_id`,`domain`.`name` AS `domain_name`,`log`.`player_id` AS `player_id`,`player`.`name` AS `player_name` from (((`log` join `player` on((`player`.`id` = `log`.`player_id`))) join `activity` on((`activity`.`id` = `log`.`activity_id`))) join `domain` on((`domain`.`id` = `activity`.`domain_id`))) where ((`log`.`reviewed` is not null) and (`activity`.`inactive` = 0)) group by `log`.`player_id`,`log`.`domain_id` order by `log`.`player_id`,`log`.`domain_id`;

-- --------------------------------------------------------

--
-- Structure for view `domain_activities_count`
--
DROP TABLE IF EXISTS `domain_activities_count`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `domain_activities_count` AS select `activity`.`domain_id` AS `domain_id`,count(0) AS `count` from `activity` where (`activity`.`inactive` = 0) group by `activity`.`domain_id` order by `activity`.`domain_id`;

-- --------------------------------------------------------

--
-- Structure for view `last_week_logs`
--
DROP TABLE IF EXISTS `last_week_logs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `last_week_logs` AS select `activity`.`id` AS `activity_id`,concat((select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 1 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 2 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 3 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 4 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 5 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 6 day)))),',',(select count(0) from `log` where ((`log`.`activity_id` = `activity`.`id`) and (`log`.`acquired` = (curdate() - interval 7 day))))) AS `logs` from `activity` where (`activity`.`inactive` = 0) order by `activity`.`id`;

-- --------------------------------------------------------

--
-- Structure for view `player_activity_coins`
--
DROP TABLE IF EXISTS `player_activity_coins`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `player_activity_coins` AS select `log`.`player_id` AS `player_id`,`player`.`name` AS `player_name`,count(0) AS `coins`,sum(`log`.`spent`) AS `spent`,(count(0) - sum(`log`.`spent`)) AS `remaining`,`log`.`activity_id` AS `activity_id`,`log`.`reviewed` AS `log_reviewed`,`activity`.`name` AS `activity_name`,`activity`.`description` AS `activity_description`,`domain`.`id` AS `domain_id`,`domain`.`name` AS `domain_name`,`domain`.`abbr` AS `domain_abbr`,`domain`.`color` AS `domain_color` from (((`log` join `activity` on((`activity`.`id` = `log`.`activity_id`))) join `player` on((`player`.`id` = `log`.`player_id`))) join `domain` on((`domain`.`id` = `activity`.`domain_id`))) group by `log`.`activity_id`,`log`.`player_id` order by `log`.`player_id`,`activity`.`domain_id`,`activity`.`name`;

-- --------------------------------------------------------

--
-- Structure for view `player_total_activity_coins`
--
DROP TABLE IF EXISTS `player_total_activity_coins`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `player_total_activity_coins` AS select `log`.`player_id` AS `player_id`,count(0) AS `coins` from `log` group by `log`.`player_id`;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `activity_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`);

--
-- Limitadores para a tabela `activity_requisite`
--
ALTER TABLE `activity_requisite`
  ADD CONSTRAINT `prerequisite_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `prerequisite_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`);

--
-- Limitadores para a tabela `badge`
--
ALTER TABLE `badge`
  ADD CONSTRAINT `badge_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`);

--
-- Limitadores para a tabela `badge_log`
--
ALTER TABLE `badge_log`
  ADD CONSTRAINT `fk_badge_log_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`),
  ADD CONSTRAINT `fk_badge_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Limitadores para a tabela `badge_requisite`
--
ALTER TABLE `badge_requisite`
  ADD CONSTRAINT `badge_requisite_badge_id` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`),
  ADD CONSTRAINT `badge_requisite_badge_id_requisite` FOREIGN KEY (`badge_id_requisite`) REFERENCES `badge` (`id`);

--
-- Limitadores para a tabela `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_event_type_id` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`);

--
-- Limitadores para a tabela `event_activity`
--
ALTER TABLE `event_activity`
  ADD CONSTRAINT `fk_event_activity_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `fk_event_activity_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

--
-- Limitadores para a tabela `event_activity_log`
--
ALTER TABLE `event_activity_log`
  ADD CONSTRAINT `fk_event_activity_log_event_activity_id` FOREIGN KEY (`event_activity_id`) REFERENCES `event_activity` (`id`),
  ADD CONSTRAINT `fk_event_activity_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Limitadores para a tabela `event_complete_log`
--
ALTER TABLE `event_complete_log`
  ADD CONSTRAINT `fk_event_completed_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_event_completed_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Limitadores para a tabela `event_join_log`
--
ALTER TABLE `event_join_log`
  ADD CONSTRAINT `fk_event_join_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_event_join_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Limitadores para a tabela `event_log`
--
ALTER TABLE `event_log`
  ADD CONSTRAINT `fk_event_log_event_activity_id` FOREIGN KEY (`event_activity_id`) REFERENCES `event_activity` (`id`),
  ADD CONSTRAINT `fk_event_log_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_event_log_event_task_id` FOREIGN KEY (`event_task_id`) REFERENCES `event_task` (`id`),
  ADD CONSTRAINT `fk_event_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Limitadores para a tabela `event_task`
--
ALTER TABLE `event_task`
  ADD CONSTRAINT `fk_event_task_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

--
-- Limitadores para a tabela `event_task_log`
--
ALTER TABLE `event_task_log`
  ADD CONSTRAINT `fk_event_task_log_event_task_id` FOREIGN KEY (`event_task_id`) REFERENCES `event_task` (`id`),
  ADD CONSTRAINT `fk_event_task_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Limitadores para a tabela `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `log_domain_id` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  ADD CONSTRAINT `log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Limitadores para a tabela `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notification_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

--
-- Limitadores para a tabela `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `fk_player_type_id` FOREIGN KEY (`player_type_id`) REFERENCES `player_type` (`id`);

--
-- Limitadores para a tabela `xp_log`
--
ALTER TABLE `xp_log`
  ADD CONSTRAINT `fk_xp_log_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `fk_xp_log_activity_id_reviewed` FOREIGN KEY (`activity_id_reviewed`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `fk_xp_log_player_id` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
