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

-- Exportiere Struktur von Tabelle rundumfit.device_x_device_group
DROP TABLE IF EXISTS `test_device_x_device_group`;
CREATE TABLE IF NOT EXISTS `test_device_x_device_group` (
  `device_x_device_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_x_device_group_device_fk` int(11) NOT NULL,
  `device_x_device_group_device_group_fk` int(11) NOT NULL,
  `device_x_device_group_create_date` datetime NOT NULL,
  `device_x_device_group_create_user_fk` int(11) NOT NULL,
  `device_x_device_group_update_date` datetime NOT NULL,
  `device_x_device_group_update_user_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`device_x_device_group_id`),
  KEY `geraetegruppe_geraet_geraet_fk` (`device_x_device_group_device_fk`),
  KEY `geraetegruppe_geraet_geraetegruppe_fk` (`device_x_device_group_device_group_fk`),
  KEY `geraetegruppe_geraet_eintrag_user_fk` (`device_x_device_group_create_user_fk`),
  KEY `geraetegruppe_geraet_aenderung_user_fk` (`device_x_device_group_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.device_x_device_group: ~29 rows (ungefähr)
DELETE FROM `test_device_x_device_group`;
/*!40000 ALTER TABLE `test_device_x_device_group` DISABLE KEYS */;
INSERT INTO `test_device_x_device_group` (`device_x_device_group_id`, `device_x_device_group_device_fk`, `device_x_device_group_device_group_fk`, `device_x_device_group_create_date`, `device_x_device_group_create_user_fk`, `device_x_device_group_update_date`, `device_x_device_group_update_user_fk`) VALUES
	(4, 25, 2, '2013-05-29 11:30:52', 24, '0000-00-00 00:00:00', NULL),
	(5, 24, 2, '2013-05-29 11:30:52', 24, '0000-00-00 00:00:00', NULL),
	(6, 26, 2, '2013-05-29 11:30:52', 24, '0000-00-00 00:00:00', NULL),
	(7, 30, 2, '2013-05-29 11:30:52', 24, '0000-00-00 00:00:00', NULL),
	(8, 29, 2, '2013-05-29 11:30:52', 24, '0000-00-00 00:00:00', NULL),
	(9, 28, 2, '2013-05-29 11:30:52', 24, '0000-00-00 00:00:00', NULL),
	(10, 27, 2, '2013-05-29 11:30:52', 24, '0000-00-00 00:00:00', NULL),
	(11, 2, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(12, 3, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(13, 4, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(14, 5, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(15, 6, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(16, 7, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(17, 8, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(18, 9, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(19, 31, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(20, 32, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(21, 33, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(22, 34, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(23, 35, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(24, 40, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(25, 39, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(26, 41, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(27, 37, 3, '2013-05-29 12:05:07', 24, '0000-00-00 00:00:00', NULL),
	(29, 22, 2, '2017-04-14 09:53:41', 1, '0000-00-00 00:00:00', NULL),
	(30, 23, 2, '2017-04-14 09:53:44', 1, '0000-00-00 00:00:00', NULL),
	(31, 1, 4, '2017-04-14 10:11:15', 1, '0000-00-00 00:00:00', NULL),
	(32, 38, 4, '2017-04-14 10:11:15', 1, '0000-00-00 00:00:00', NULL);
/*!40000 ALTER TABLE `test_device_x_device_group` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
