# ************************************************************
# Sequel Pro SQL dump
# Versão 4740
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 192.168.99.100 (MySQL 5.7.9)
# Base de Dados: vanhackathon-2016-05-joao-marques
# Tempo de Geração: 2016-05-22 22:21:49 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump da tabela game
# ------------------------------------------------------------

DROP TABLE IF EXISTS `game`;

CREATE TABLE `game` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `available_colors` char(15) DEFAULT '',
  `code` char(15) DEFAULT '',
  `id_player_owner` int(11) unsigned DEFAULT NULL,
  `id_player_winner` int(11) unsigned DEFAULT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  `started_at` int(11) unsigned DEFAULT NULL,
  `ended_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user_owner` (`id_player_owner`),
  KEY `id_user_winner` (`id_player_winner`),
  CONSTRAINT `game_ibfk_1` FOREIGN KEY (`id_player_owner`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `game_ibfk_2` FOREIGN KEY (`id_player_winner`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;



# Dump da tabela match
# ------------------------------------------------------------

DROP TABLE IF EXISTS `match`;

CREATE TABLE `match` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_game` int(11) unsigned NOT NULL,
  `id_player` int(11) unsigned NOT NULL,
  `joined_at` int(11) DEFAULT NULL,
  `num_guesses` tinyint(4) DEFAULT '0',
  `player_status` varchar(20) DEFAULT 'idle',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_game` (`id_game`,`id_player`),
  KEY `id_player` (`id_player`),
  CONSTRAINT `match_ibfk_1` FOREIGN KEY (`id_game`) REFERENCES `game` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `match_ibfk_2` FOREIGN KEY (`id_player`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;



# Dump da tabela migration
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migration`;

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;

INSERT INTO `migration` (`version`, `apply_time`)
VALUES
	('m000000_000000_base',1463714322),
	('m130524_201442_init',1463714330);

/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;


# Dump da tabela player
# ------------------------------------------------------------

DROP TABLE IF EXISTS `player`;

CREATE TABLE `player` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
  `password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;

INSERT INTO `player` (`id`, `username`, `name`, `auth_key`, `password_hash`, `password_reset_token`, `access_token`, `email`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'joao@jjmf.com','joao','','',NULL,'NKVFgubWowC7n8DYNxZ8jTXOybzp2rPS','joao@jjmf.com',10,1463802745,1463802745),
	(3,'joaomarq@gmail.com','joao','','',NULL,'XMpuGcXfwbmDe6HCw01dk5eAZMiuqDav','joaomarq@gmail.com',10,1463838426,1463838426),
	(4,'bot@bot.io','bot','','',NULL,'xxKX0idKkyNplHZvw429PEmY9pJP0Ndp','bot@bot.io',10,1463878372,1463878372),
	(5,'bot2@bot.io','bot2','','',NULL,'-MbuuvsD0QVr_gw8KOeHNtUa33qtQXr7','bot2@bot.io',10,1463893648,1463893648);

/*!40000 ALTER TABLE `player` ENABLE KEYS */;
UNLOCK TABLES;


# Dump da tabela player_guess_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `player_guess_history`;

CREATE TABLE `player_guess_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_game` int(11) unsigned NOT NULL,
  `id_player` int(11) unsigned NOT NULL,
  `guess` char(15) NOT NULL DEFAULT '',
  `exact_matches` tinyint(4) NOT NULL DEFAULT '0',
  `near_matches` tinyint(4) NOT NULL DEFAULT '0',
  `guessed_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_game` (`id_game`),
  KEY `id_player` (`id_player`),
  CONSTRAINT `player_guess_history_ibfk_1` FOREIGN KEY (`id_game`) REFERENCES `game` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `player_guess_history_ibfk_2` FOREIGN KEY (`id_player`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
