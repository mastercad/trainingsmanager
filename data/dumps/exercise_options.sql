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

-- Exportiere Struktur von Tabelle rundumfit.exercise_options
DROP TABLE IF EXISTS `test_exercise_options`;
CREATE TABLE IF NOT EXISTS `test_exercise_options` (
  `exercise_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exercise_option_name` varchar(255) NOT NULL,
  `exercise_option_create_date` datetime NOT NULL,
  `exercise_option_create_user_fk` int(10) unsigned NOT NULL,
  `exercise_option_update_date` datetime NOT NULL,
  `exercise_option_update_user_fk` int(10) unsigned NOT NULL,
  `exercise_option_default_value` varchar(255) NOT NULL,
  UNIQUE KEY `unique_uebung_option_id` (`exercise_option_id`),
  UNIQUE KEY `exercise_option_name` (`exercise_option_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.exercise_options: ~4 rows (ungefähr)
DELETE FROM `test_exercise_options`;
/*!40000 ALTER TABLE `test_exercise_options` DISABLE KEYS */;
INSERT INTO `test_exercise_options` (`exercise_option_id`, `exercise_option_name`, `exercise_option_create_date`, `exercise_option_create_user_fk`, `exercise_option_update_date`, `exercise_option_update_user_fk`, `exercise_option_default_value`) VALUES
	(1, 'Wiederholungen', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(2, 'Sätze', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(3, 'Phasen', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '5'),
	(4, 'Dauer', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '');
/*!40000 ALTER TABLE `test_exercise_options` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
