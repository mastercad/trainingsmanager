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

-- Exportiere Struktur von Tabelle rundumfit.exercise_x_device_option
DROP TABLE IF EXISTS `test_exercise_x_device_option`;
CREATE TABLE IF NOT EXISTS `test_exercise_x_device_option` (
  `exercise_x_device_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exercise_x_device_option_exercise_fk` int(10) unsigned NOT NULL,
  `exercise_x_device_option_device_option_fk` int(10) unsigned NOT NULL,
  `exercise_x_device_option_create_date` datetime NOT NULL,
  `exercise_x_device_option_create_user_fk` int(10) unsigned NOT NULL,
  `exercise_x_device_option_update_date` datetime NOT NULL,
  `exercise_x_device_option_update_user_fk` int(10) unsigned NOT NULL,
  `exercise_x_device_option_device_option_value` varchar(255) NOT NULL,
  UNIQUE KEY `unique_exercise_x_device_option_id` (`exercise_x_device_option_id`),
  UNIQUE KEY `exercise_id_device_option_id` (`exercise_x_device_option_exercise_fk`,`exercise_x_device_option_device_option_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.exercise_x_device_option: ~9 rows (ungefähr)
DELETE FROM `test_exercise_x_device_option`;
/*!40000 ALTER TABLE `test_exercise_x_device_option` DISABLE KEYS */;
INSERT INTO `test_exercise_x_device_option` (`exercise_x_device_option_id`, `exercise_x_device_option_exercise_fk`, `exercise_x_device_option_device_option_fk`, `exercise_x_device_option_create_date`, `exercise_x_device_option_create_user_fk`, `exercise_x_device_option_update_date`, `exercise_x_device_option_update_user_fk`, `exercise_x_device_option_device_option_value`) VALUES
	(2, 34, 2, '2017-03-05 17:37:50', 1, '0000-00-00 00:00:00', 0, '1|1.5|2|2.5|3|3.5|4|4.5|5|6|7|8|9|10|15|20|25|30|35|40|45|50'),
	(3, 34, 3, '2017-03-05 17:37:50', 1, '0000-00-00 00:00:00', 0, '1|2|3|4'),
	(4, 22, 3, '2017-03-05 17:37:50', 1, '0000-00-00 00:00:00', 0, '1|2|3|4'),
	(5, 48, 2, '2017-03-18 20:27:45', 22, '0000-00-00 00:00:00', 0, '1|2|4|5|10'),
	(6, 48, 4, '2017-03-18 20:27:45', 22, '0000-00-00 00:00:00', 0, '3'),
	(7, 35, 2, '2017-03-24 16:49:24', 1, '0000-00-00 00:00:00', 0, '1|1.25|1.5|2|2.5|3|3.5|4|4.5|5|6|7|8|9|10|15|20|25|30|35|40|45|50|55|60|65'),
	(9, 2, 2, '2017-04-22 12:38:26', 1, '0000-00-00 00:00:00', 0, '6'),
	(10, 3, 2, '2017-04-24 21:14:55', 1, '0000-00-00 00:00:00', 0, '6'),
	(11, 1, 2, '2017-04-24 21:17:09', 1, '0000-00-00 00:00:00', 0, '6'),
	(12, 13, 2, '2017-04-24 21:34:35', 1, '0000-00-00 00:00:00', 0, '6'),
	(13, 34, 1, '2017-05-14 21:34:28', 22, '0000-00-00 00:00:00', 0, '1');
/*!40000 ALTER TABLE `test_exercise_x_device_option` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
