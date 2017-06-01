-- --------------------------------------------------------
-- Host:                         localhost
-- Server Version:               10.1.21-MariaDB-1~jessie - mariadb.org binary distribution
-- Server Betriebssystem:        debian-linux-gnu
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Struktur von Tabelle rundumfit.muscle_x_muscle_group
DROP TABLE IF EXISTS `test_muscle_x_muscle_group`;
CREATE TABLE IF NOT EXISTS `test_muscle_x_muscle_group` (
  `muscle_x_muscle_group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `muscle_x_muscle_group_muscle_fk` int(11) unsigned NOT NULL,
  `muscle_x_muscle_group_muscle_group_fk` int(11) unsigned NOT NULL,
  `muscle_x_muscle_group_use` int(11) NOT NULL COMMENT 'Beanspruchung des Muskels',
  `muscle_x_muscle_group_create_date` datetime NOT NULL,
  `muscle_x_muscle_group_create_user_fk` int(11) unsigned NOT NULL,
  `muscle_x_muscle_group_update_date` datetime NOT NULL,
  `muscle_x_muscle_group_update_user_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`muscle_x_muscle_group_id`),
  UNIQUE KEY `muscle_x_muscle_group_muscle_fk` (`muscle_x_muscle_group_muscle_fk`),
  KEY `muskelgruppe_muskel_muskel_fk` (`muscle_x_muscle_group_muscle_fk`),
  KEY `muskelgruppe_muskel_muskelgruppe_fk` (`muscle_x_muscle_group_muscle_group_fk`),
  KEY `muskelgruppe_muskel_eintrag_user_fk` (`muscle_x_muscle_group_create_user_fk`),
  KEY `muskelgruppe_muskel_aenderung_user_fk` (`muscle_x_muscle_group_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.muscle_x_muscle_group: ~9 rows (ungefähr)
DELETE FROM `test_muscle_x_muscle_group`;
/*!40000 ALTER TABLE `test_muscle_x_muscle_group` DISABLE KEYS */;
INSERT INTO `test_muscle_x_muscle_group` (`muscle_x_muscle_group_id`, `muscle_x_muscle_group_muscle_fk`, `muscle_x_muscle_group_muscle_group_fk`, `muscle_x_muscle_group_use`, `muscle_x_muscle_group_create_date`, `muscle_x_muscle_group_create_user_fk`, `muscle_x_muscle_group_update_date`, `muscle_x_muscle_group_update_user_fk`) VALUES
	(1, 32, 10, 0, '2014-05-15 23:05:47', 22, '0000-00-00 00:00:00', NULL),
	(2, 34, 10, 0, '2014-05-15 23:05:47', 22, '0000-00-00 00:00:00', NULL),
	(4, 35, 10, 0, '2014-05-15 23:05:47', 22, '0000-00-00 00:00:00', NULL),
	(5, 37, 11, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', NULL),
	(6, 38, 11, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', NULL),
	(7, 39, 11, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', NULL),
	(12, 33, 12, 0, '2017-03-18 15:53:59', 1, '0000-00-00 00:00:00', NULL),
	(13, 40, 13, 0, '2017-03-24 16:49:06', 1, '0000-00-00 00:00:00', NULL),
	(14, 41, 13, 0, '2017-03-24 16:49:06', 1, '0000-00-00 00:00:00', NULL),
	(15, 42, 14, 0, '2017-04-14 10:49:52', 1, '0000-00-00 00:00:00', NULL),
	(17, 45, 18, 0, '2017-05-03 19:26:08', 22, '0000-00-00 00:00:00', NULL),
	(18, 44, 20, 0, '2017-05-03 19:26:52', 22, '0000-00-00 00:00:00', NULL);
/*!40000 ALTER TABLE `test_muscle_x_muscle_group` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
