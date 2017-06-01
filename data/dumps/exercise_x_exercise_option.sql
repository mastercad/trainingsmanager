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

-- Exportiere Struktur von Tabelle rundumfit.exercise_x_exercise_option
DROP TABLE IF EXISTS `test_exercise_x_exercise_option`;
CREATE TABLE IF NOT EXISTS `test_exercise_x_exercise_option` (
  `exercise_x_exercise_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exercise_x_exercise_option_exercise_fk` int(10) unsigned NOT NULL,
  `exercise_x_exercise_option_exercise_option_fk` int(10) unsigned NOT NULL,
  `exercise_x_exercise_option_exercise_option_value` varchar(255) NOT NULL,
  `exercise_x_exercise_option_create_date` datetime NOT NULL,
  `exercise_x_exercise_option_create_user_fk` int(10) unsigned NOT NULL,
  `exercise_x_exercise_option_update_date` datetime NOT NULL,
  `exercise_x_exercise_option_update_user_fk` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_uebung_x_uebung_option_id` (`exercise_x_exercise_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.exercise_x_exercise_option: ~5 rows (ungefähr)
DELETE FROM `test_exercise_x_exercise_option`;
/*!40000 ALTER TABLE `test_exercise_x_exercise_option` DISABLE KEYS */;
INSERT INTO `test_exercise_x_exercise_option` (`exercise_x_exercise_option_id`, `exercise_x_exercise_option_exercise_fk`, `exercise_x_exercise_option_exercise_option_fk`, `exercise_x_exercise_option_exercise_option_value`, `exercise_x_exercise_option_create_date`, `exercise_x_exercise_option_create_user_fk`, `exercise_x_exercise_option_update_date`, `exercise_x_exercise_option_update_user_fk`) VALUES
	(1, 34, 1, '1|2|3', '0000-00-00 00:00:00', 0, '2017-03-04 12:03:49', 1),
	(2, 34, 2, '1|2|3|4', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(3, 34, 3, '4321', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(7, 48, 1, '12', '2017-03-18 20:50:56', 22, '0000-00-00 00:00:00', 0),
	(19, 48, 2, '3', '2017-03-18 22:26:42', 22, '0000-00-00 00:00:00', 0);
/*!40000 ALTER TABLE `test_exercise_x_exercise_option` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
