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

-- Exportiere Struktur von Tabelle rundumfit.exercise_x_exercise_type
DROP TABLE IF EXISTS `test_exercise_x_exercise_type`;
CREATE TABLE IF NOT EXISTS `test_exercise_x_exercise_type` (
  `exercise_x_exercise_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exercise_x_exercise_type_exercise_fk` int(10) unsigned NOT NULL,
  `exercise_x_exercise_type_create_date` datetime NOT NULL,
  `exercise_x_exercise_type_create_user_fk` int(10) unsigned NOT NULL,
  `exercise_x_exercise_type_update_date` datetime NOT NULL,
  `exercise_x_exercise_type_update_user_fk` int(10) unsigned NOT NULL,
  `exercise_x_exercise_type_exercise_type_fk` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_exercise_x_exercise_type_id` (`exercise_x_exercise_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.exercise_x_exercise_type: ~10 rows (ungefähr)
DELETE FROM `test_exercise_x_exercise_type`;
/*!40000 ALTER TABLE `test_exercise_x_exercise_type` DISABLE KEYS */;
INSERT INTO `test_exercise_x_exercise_type` (`exercise_x_exercise_type_id`, `exercise_x_exercise_type_exercise_fk`, `exercise_x_exercise_type_create_date`, `exercise_x_exercise_type_create_user_fk`, `exercise_x_exercise_type_update_date`, `exercise_x_exercise_type_update_user_fk`, `exercise_x_exercise_type_exercise_type_fk`) VALUES
	(2, 34, '2017-03-03 22:47:39', 1, '2017-05-14 21:34:44', 22, 3),
	(3, 47, '2017-03-04 18:57:39', 1, '2017-03-29 21:22:32', 1, 3),
	(4, 48, '2017-03-18 22:28:06', 22, '2017-03-18 22:35:50', 22, 1),
	(5, 28, '2017-03-21 20:19:08', 1, '0000-00-00 00:00:00', 0, 3),
	(6, 21, '2017-03-21 20:19:44', 1, '0000-00-00 00:00:00', 0, 3),
	(7, 29, '2017-03-22 19:32:26', 1, '0000-00-00 00:00:00', 0, 3),
	(8, 35, '2017-03-28 18:38:49', 1, '0000-00-00 00:00:00', 0, 5),
	(9, 49, '2017-04-14 11:10:07', 1, '2017-04-14 11:10:39', 1, 3),
	(11, 2, '2017-04-24 21:20:18', 1, '2017-05-04 13:31:41', 22, 3),
	(12, 13, '2017-04-24 21:35:23', 1, '0000-00-00 00:00:00', 0, 1),
	(13, 1, '2017-04-24 21:37:22', 1, '0000-00-00 00:00:00', 0, 3);
/*!40000 ALTER TABLE `test_exercise_x_exercise_type` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
