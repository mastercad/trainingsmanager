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

-- Exportiere Struktur von Tabelle rundumfit.device_options
DROP TABLE IF EXISTS `test_device_options`;
CREATE TABLE IF NOT EXISTS `test_device_options` (
  `device_option_id` int(255) NOT NULL AUTO_INCREMENT,
  `device_option_name` varchar(255) NOT NULL,
  `device_option_default_value` varchar(255) NOT NULL,
  `device_option_create_date` datetime NOT NULL,
  `device_option_create_user_fk` int(10) unsigned NOT NULL,
  `device_option_update_date` datetime NOT NULL,
  `device_option_update_user_fk` int(10) unsigned NOT NULL,
  UNIQUE KEY `device_option_name` (`device_option_name`),
  UNIQUE KEY `unique_geraet_option_id` (`device_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.device_options: ~5 rows (ungefähr)
DELETE FROM `test_device_options`;
/*!40000 ALTER TABLE `test_device_options` DISABLE KEYS */;
INSERT INTO `test_device_options` (`device_option_id`, `device_option_name`, `device_option_default_value`, `device_option_create_date`, `device_option_create_user_fk`, `device_option_update_date`, `device_option_update_user_fk`) VALUES
	(1, 'Beinpolsterposition', '', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(2, 'Gewicht', '', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(3, 'Rückenpolsterposition', '', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(4, 'Sitzpolsterposition', '', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0);
/*!40000 ALTER TABLE `test_device_options` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
