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

-- Exportiere Struktur von Tabelle rundumfit.muscles
DROP TABLE IF EXISTS `test_muscles`;
CREATE TABLE IF NOT EXISTS `test_muscles` (
  `muscle_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `muscle_name` varchar(250) NOT NULL,
  `muscle_seo_link` varchar(250) NOT NULL,
  `muscle_create_date` datetime NOT NULL,
  `muscle_create_user_fk` int(11) unsigned NOT NULL,
  `muscle_update_date` datetime NOT NULL,
  `muscle_update_user_fk` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`muscle_id`),
  UNIQUE KEY `muscle_name` (`muscle_name`),
  UNIQUE KEY `muscle_seo_link` (`muscle_seo_link`),
  KEY `muskel_eintrag_user_fk` (`muscle_create_user_fk`),
  KEY `muskel_aenderung_user_fk` (`muscle_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.muscles: ~13 rows (ungefähr)
DELETE FROM `test_muscles`;
/*!40000 ALTER TABLE `test_muscles` DISABLE KEYS */;
INSERT INTO `test_muscles` (`muscle_id`, `muscle_name`, `muscle_seo_link`, `muscle_create_date`, `muscle_create_user_fk`, `muscle_update_date`, `muscle_update_user_fk`) VALUES
	(32, 'Oberer Bauch', 'oberer-bauch', '2014-05-15 23:03:26', 1, '0000-00-00 00:00:00', NULL),
	(33, 'Unterer Bauch', 'unterer-bauch', '2014-05-15 23:03:42', 1, '2017-02-25 11:17:36', 1),
	(34, 'Mittlerer Bauch', 'mittlerer-bauch', '2014-05-15 23:03:57', 1, '2017-05-03 20:30:09', 22),
	(35, 'Seitlicher Bauch', 'seitlicher-bauch', '2014-05-15 23:04:13', 23, '0000-00-00 00:00:00', NULL),
	(37, 'Oberer Rücken', 'oberer-ruecken', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', NULL),
	(38, 'Großer Rückenmuskel', 'grosser-rueckenmuskel', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', NULL),
	(39, 'Unterer Rücken', 'unterer-ruecken', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', NULL),
	(40, 'Bizeps', 'bizeps', '2017-03-24 16:48:23', 1, '0000-00-00 00:00:00', NULL),
	(41, 'Trizeps', 'trizeps', '2017-03-24 16:48:40', 1, '0000-00-00 00:00:00', NULL),
	(42, 'Oberer Brustmuskel', 'oberer-brustmuskel', '2017-04-14 10:49:36', 1, '0000-00-00 00:00:00', NULL);
/*!40000 ALTER TABLE `test_muscles` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
