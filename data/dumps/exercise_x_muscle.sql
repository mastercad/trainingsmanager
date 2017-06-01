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

-- Exportiere Struktur von Tabelle rundumfit.exercise_x_muscle
DROP TABLE IF EXISTS `test_exercise_x_muscle`;
CREATE TABLE IF NOT EXISTS `test_exercise_x_muscle` (
  `exercise_x_muscle_id` int(11) NOT NULL AUTO_INCREMENT,
  `exercise_x_muscle_muscle_fk` int(11) unsigned NOT NULL,
  `exercise_x_muscle_exercise_fk` int(11) unsigned NOT NULL,
  `exercise_x_muscle_muscle_use` int(11) NOT NULL COMMENT 'Beanspruchung des Muskels',
  `exercise_x_muscle_create_date` datetime NOT NULL,
  `exercise_x_muscle_create_user_fk` int(11) unsigned NOT NULL,
  `exercise_x_muscle_update_date` datetime DEFAULT NULL,
  `exercise_x_muscle_update_user_fk` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`exercise_x_muscle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.exercise_x_muscle: ~13 rows (ungefähr)
DELETE FROM `test_exercise_x_muscle`;
/*!40000 ALTER TABLE `test_exercise_x_muscle` DISABLE KEYS */;
INSERT INTO `test_exercise_x_muscle` (`exercise_x_muscle_id`, `exercise_x_muscle_muscle_fk`, `exercise_x_muscle_exercise_fk`, `exercise_x_muscle_muscle_use`, `exercise_x_muscle_create_date`, `exercise_x_muscle_create_user_fk`, `exercise_x_muscle_update_date`, `exercise_x_muscle_update_user_fk`) VALUES
	(15, 33, 22, 5, '2017-03-04 18:15:57', 1, NULL, NULL),
	(20, 33, 47, 2, '2017-03-04 18:57:39', 1, NULL, NULL),
	(23, 37, 34, 5, '2017-03-05 17:37:50', 1, NULL, NULL),
	(24, 39, 34, 2, '2017-03-05 17:37:50', 1, NULL, NULL),
	(27, 37, 48, 2, '2017-03-18 22:35:11', 22, '2017-03-18 22:35:50', 22),
	(28, 38, 28, 4, '2017-03-21 20:19:08', 1, NULL, NULL),
	(35, 40, 35, 5, '2017-03-24 16:49:24', 1, NULL, NULL),
	(36, 41, 35, 2, '2017-03-24 16:49:24', 1, NULL, NULL),
	(38, 42, 49, 5, '2017-04-14 11:10:39', 1, NULL, NULL),
	(41, 42, 2, 4, '2017-04-22 19:43:10', 1, NULL, NULL),
	(42, 40, 3, 4, '2017-04-24 21:14:55', 1, NULL, NULL),
	(43, 41, 3, 2, '2017-04-24 21:14:55', 1, NULL, NULL),
	(44, 40, 1, 4, '2017-04-24 21:17:08', 1, NULL, NULL),
	(45, 39, 13, 3, '2017-04-24 21:34:35', 1, NULL, NULL);
/*!40000 ALTER TABLE `test_exercise_x_muscle` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
