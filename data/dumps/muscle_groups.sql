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

-- Exportiere Struktur von Tabelle rundumfit.muscle_groups
DROP TABLE IF EXISTS `test_muscle_groups`;
CREATE TABLE IF NOT EXISTS `test_muscle_groups` (
  `muscle_group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `muscle_group_name` varchar(250) NOT NULL,
  `muscle_group_seo_link` varchar(250) NOT NULL,
  `muscle_group_color` varchar(250) NOT NULL,
  `muscle_group_create_date` datetime NOT NULL,
  `muscle_group_create_user_fk` int(11) unsigned NOT NULL,
  `muscle_group_update_date` datetime NOT NULL,
  `muscle_group_update_user_fk` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`muscle_group_id`),
  UNIQUE KEY `unique_muscle_group_seo_link` (`muscle_group_seo_link`),
  UNIQUE KEY `unique_muscle_group_name` (`muscle_group_name`),
  KEY `muskelgruppe_aenderung_user_fk` (`muscle_group_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.muscle_groups: ~7 rows (ungefähr)
DELETE FROM `test_muscle_groups`;
/*!40000 ALTER TABLE `test_muscle_groups` DISABLE KEYS */;
INSERT INTO `test_muscle_groups` (`muscle_group_id`, `muscle_group_name`, `muscle_group_seo_link`, `muscle_group_color`, `muscle_group_create_date`, `muscle_group_create_user_fk`, `muscle_group_update_date`, `muscle_group_update_user_fk`) VALUES
	(10, 'Bauch', 'bauch', '', '2014-05-15 23:05:47', 22, '0000-00-00 00:00:00', NULL),
	(11, 'Rücken', 'ruecken', '', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', NULL),
	(12, 'Test', 'test', '', '2017-03-18 15:53:59', 1, '0000-00-00 00:00:00', NULL),
	(13, 'Arme', 'arme', '', '2017-03-24 16:49:06', 1, '0000-00-00 00:00:00', NULL),
	(14, 'Brust', 'brust', '', '2017-04-14 10:49:52', 1, '0000-00-00 00:00:00', NULL);
/*!40000 ALTER TABLE `test_muscle_groups` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
