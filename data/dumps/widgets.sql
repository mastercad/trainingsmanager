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

-- Exportiere Struktur von Tabelle rundumfit.test_widgets
CREATE TABLE IF NOT EXISTS `test_widgets` (
  `widget_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `widget_name` varchar(50) NOT NULL DEFAULT '0',
  `widget_editable` tinyint(4) NOT NULL DEFAULT '0',
  `widget_create_date` datetime NOT NULL,
  `widget_create_user_fk` int(11) NOT NULL,
  `widget_update_date` datetime DEFAULT NULL,
  `widget_update_user_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`widget_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.test_widgets: ~2 rows (ungefähr)
DELETE FROM `test_widgets`;
/*!40000 ALTER TABLE `test_widgets` DISABLE KEYS */;
INSERT INTO `test_widgets` (`widget_id`, `widget_name`, `widget_editable`, `widget_create_date`, `widget_create_user_fk`, `widget_update_date`, `widget_update_user_fk`) VALUES
	(1, 'Aktueller Trainingsplan', 0, '0000-00-00 00:00:00', 0, NULL, NULL),
	(2, 'Übungsfortschritt', 1, '0000-00-00 00:00:00', 0, NULL, NULL);
/*!40000 ALTER TABLE `test_widgets` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
