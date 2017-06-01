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

-- Exportiere Struktur von Tabelle rundumfit.training_plan_x_exercise
DROP TABLE IF EXISTS `test_training_plan_x_exercise`;
CREATE TABLE IF NOT EXISTS `test_training_plan_x_exercise` (
  `training_plan_x_exercise_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `training_plan_x_exercise_exercise_fk` int(11) unsigned NOT NULL,
  `training_plan_x_exercise_training_plan_fk` int(11) unsigned NOT NULL,
  `training_plan_x_exercise_exercise_order` int(11) DEFAULT NULL COMMENT 'ist gedacht um die reihenfolge der übungen nachträglich noch ändern zu können',
  `training_plan_x_exercise_comment` text NOT NULL,
  `training_plan_x_exercise_create_date` datetime NOT NULL,
  `training_plan_x_exercise_create_user_fk` int(11) unsigned zerofill NOT NULL,
  `training_plan_x_exercise_update_date` datetime NOT NULL,
  `training_plan_x_exercise_update_user_fk` int(11) unsigned zerofill NOT NULL,
  PRIMARY KEY (`training_plan_x_exercise_id`),
  UNIQUE KEY `training_plan_exercise` (`training_plan_x_exercise_exercise_fk`,`training_plan_x_exercise_training_plan_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_plan_x_exercise: ~11 rows (ungefähr)
DELETE FROM `test_training_plan_x_exercise`;
/*!40000 ALTER TABLE `test_training_plan_x_exercise` DISABLE KEYS */;
INSERT INTO `test_training_plan_x_exercise` (`training_plan_x_exercise_id`, `training_plan_x_exercise_exercise_fk`, `training_plan_x_exercise_training_plan_fk`, `training_plan_x_exercise_exercise_order`, `training_plan_x_exercise_remark`, `training_plan_x_exercise_create_date`, `training_plan_x_exercise_create_user_fk`, `training_plan_x_exercise_update_date`, `training_plan_x_exercise_update_user_fk`) VALUES
	(34, 29, 86, 0, 'sasdasdas', '2017-03-15 20:46:16', 00000000001, '2017-05-09 20:37:49', 00000000001),
	(35, 28, 50, 0, '', '2017-03-15 20:50:10', 00000000001, '2017-05-13 10:53:32', 00000000001),
	(36, 45, 50, 0, '', '2017-03-15 21:24:40', 00000000001, '0000-00-00 00:00:00', 00000000000),
	(37, 44, 54, 0, '', '2017-03-15 21:24:40', 00000000001, '0000-00-00 00:00:00', 00000000000),
	(38, 21, 55, 0, '', '2017-03-15 21:24:40', 00000000001, '0000-00-00 00:00:00', 00000000000),
	(39, 35, 55, 0, 'sdfsdfdsfs', '2017-03-19 19:24:46', 00000000001, '2017-05-09 20:37:48', 00000000001),
	(40, 40, 55, 0, '', '2017-03-22 20:42:13', 00000000001, '2017-05-09 20:37:49', 00000000001),
	(41, 30, 55, 0, '', '2017-03-22 20:42:13', 00000000001, '2017-05-09 20:37:49', 00000000001),
	(94, 35, 126, 0, '', '2017-05-13 14:55:17', 00000000001, '0000-00-00 00:00:00', 00000000000),
	(95, 21, 125, 0, '', '2017-05-13 14:55:17', 00000000001, '0000-00-00 00:00:00', 00000000000),
	(96, 29, 127, 0, '', '2017-05-13 14:55:17', 00000000001, '0000-00-00 00:00:00', 00000000000);
/*!40000 ALTER TABLE `test_training_plan_x_exercise` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
