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

-- Exportiere Struktur von Tabelle rundumfit.exercise_x_device
DROP TABLE IF EXISTS `test_exercise_x_device`;
CREATE TABLE IF NOT EXISTS `test_exercise_x_device` (
  `exercise_x_device_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exercise_x_device_device_fk` int(10) unsigned NOT NULL,
  `exercise_x_device_exercise_fk` int(10) unsigned NOT NULL,
  `exercise_x_device_create_date` datetime NOT NULL,
  `exercise_x_device_create_user_fk` int(10) unsigned DEFAULT NULL,
  `exercise_x_device_update_date` datetime NOT NULL,
  `exercise_x_device_update_user_fk` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `unique_exercise_x_device_id` (`exercise_x_device_id`),
  KEY `index_exercise_x_device_exercise_fk` (`exercise_x_device_exercise_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.exercise_x_device: ~47 rows (ungefähr)
DELETE FROM `test_exercise_x_device`;
/*!40000 ALTER TABLE `test_exercise_x_device` DISABLE KEYS */;
INSERT INTO `test_exercise_x_device` (`exercise_x_device_id`, `exercise_x_device_device_fk`, `exercise_x_device_exercise_fk`, `exercise_x_device_create_date`, `exercise_x_device_create_user_fk`, `exercise_x_device_update_date`, `exercise_x_device_update_user_fk`) VALUES
	(2, 26, 35, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(4, 1, 1, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(5, 1, 2, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(6, 1, 3, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(7, 1, 4, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(8, 1, 5, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(9, 1, 6, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(10, 1, 7, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(11, 1, 8, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(12, 1, 9, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(13, 1, 10, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(14, 1, 11, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(15, 1, 12, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(16, 1, 13, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(17, 1, 14, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(18, 1, 15, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(19, 1, 16, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(20, 1, 17, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(21, 1, 18, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(22, 1, 19, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(23, 1, 20, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(24, 2, 21, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(25, 3, 22, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(26, 4, 23, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(27, 5, 24, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(28, 6, 25, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(29, 7, 26, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(30, 8, 27, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(31, 9, 28, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(32, 10, 32, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(33, 11, 33, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(34, 12, 34, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(35, 14, 42, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(36, 15, 41, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(37, 16, 40, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(38, 17, 39, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(39, 18, 38, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(40, 19, 37, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(41, 20, 36, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(42, 23, 45, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(43, 24, 46, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(44, 27, 44, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(45, 28, 43, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(46, 31, 29, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(47, 35, 30, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL),
	(48, 39, 31, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL);
/*!40000 ALTER TABLE `test_exercise_x_device` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
