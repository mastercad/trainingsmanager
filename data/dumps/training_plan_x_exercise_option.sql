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

-- Exportiere Struktur von Tabelle rundumfit.training_plan_x_exercise_option
DROP TABLE IF EXISTS `test_training_plan_x_exercise_option`;
CREATE TABLE IF NOT EXISTS `test_training_plan_x_exercise_option` (
  `training_plan_x_exercise_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_plan_x_exercise_option_exercise_option_fk` int(10) unsigned NOT NULL,
  `training_plan_x_exercise_option_exercise_option_value` varchar(255) NOT NULL,
  `training_plan_x_exercise_option_create_date` datetime NOT NULL,
  `training_plan_x_exercise_option_create_user_fk` int(10) unsigned NOT NULL,
  `training_plan_x_exercise_option_update_date` datetime NOT NULL,
  `training_plan_x_exercise_option_update_user_fk` int(10) unsigned NOT NULL,
  `training_plan_x_exercise_option_training_plan_exercise_fk` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_training_plan_x_exercise_option_id` (`training_plan_x_exercise_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_plan_x_exercise_option: ~11 rows (ungefähr)
DELETE FROM `test_training_plan_x_exercise_option`;
/*!40000 ALTER TABLE `test_training_plan_x_exercise_option` DISABLE KEYS */;
INSERT INTO `test_training_plan_x_exercise_option` (`training_plan_x_exercise_option_id`, `training_plan_x_exercise_option_exercise_option_fk`, `training_plan_x_exercise_option_exercise_option_value`, `training_plan_x_exercise_option_create_date`, `training_plan_x_exercise_option_create_user_fk`, `training_plan_x_exercise_option_update_date`, `training_plan_x_exercise_option_update_user_fk`, `training_plan_x_exercise_option_training_plan_exercise_fk`) VALUES
	(44, 3, '12', '2017-03-15 20:39:41', 1, '0000-00-00 00:00:00', 0, 33),
	(46, 2, '4', '2017-03-23 21:28:06', 1, '2017-03-23 22:08:23', 1, 39),
	(47, 1, '12', '2017-03-23 21:28:06', 1, '2017-03-24 16:50:43', 1, 39),
	(48, 3, '2/12/2', '2017-03-23 21:49:02', 1, '0000-00-00 00:00:00', 0, 39),
	(49, 2, '4', '2017-04-12 20:36:27', 1, '0000-00-00 00:00:00', 0, 46),
	(50, 1, '12', '2017-04-12 20:36:27', 1, '0000-00-00 00:00:00', 0, 46),
	(51, 2, '4', '2017-05-09 20:37:48', 1, '0000-00-00 00:00:00', 0, 39),
	(52, 1, '12', '2017-05-09 20:37:48', 1, '0000-00-00 00:00:00', 0, 39),
	(53, 3, '2/12/2', '2017-05-09 20:37:49', 1, '0000-00-00 00:00:00', 0, 39),
	(54, 2, '4', '2017-05-09 20:37:49', 1, '0000-00-00 00:00:00', 0, 46),
	(55, 1, '12', '2017-05-09 20:37:49', 1, '0000-00-00 00:00:00', 0, 46);
/*!40000 ALTER TABLE `test_training_plan_x_exercise_option` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
