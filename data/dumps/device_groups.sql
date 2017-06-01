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

-- Exportiere Struktur von Tabelle rundumfit.device_groups
DROP TABLE IF EXISTS `test_device_groups`;
CREATE TABLE IF NOT EXISTS `test_device_groups` (
  `device_group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `device_group_name` varchar(250) NOT NULL,
  `device_group_seo_link` varchar(250) NOT NULL,
  `device_group_create_date` datetime NOT NULL,
  `device_group_create_user_fk` int(11) NOT NULL,
  `device_group_update_date` datetime NOT NULL,
  `device_group_update_user_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`device_group_id`),
  UNIQUE KEY `device_group_name` (`device_group_name`),
  KEY `geraetegruppe_eintrag_user_fk` (`device_group_create_user_fk`),
  KEY `geraetegruppe_aenderung_user_fk` (`device_group_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.device_groups: ~3 rows (ungefähr)
DELETE FROM `test_device_groups`;
/*!40000 ALTER TABLE `test_device_groups` DISABLE KEYS */;
INSERT INTO `test_device_groups` (`device_group_id`, `device_group_name`, `device_group_seo_link`, `device_group_create_date`, `device_group_create_user_fk`, `device_group_update_date`, `device_group_update_user_fk`) VALUES
	(2, 'Freihantelbereich', 'freihantelbereich', '2013-05-29 11:26:26', 24, '0000-00-00 00:00:00', NULL),
	(3, 'Dr. Wolff Rückenzentrum', 'dr-wolff-rueckenzentrum', '2013-05-29 11:34:14', 24, '0000-00-00 00:00:00', NULL);
/*!40000 ALTER TABLE `test_device_groups` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
