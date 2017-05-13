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

-- Exportiere Struktur von Tabelle rundumfit.training_diary_x_training_plan_exercise
DROP TABLE IF EXISTS `training_diary_x_training_plan_exercise`;
CREATE TABLE IF NOT EXISTS `training_diary_x_training_plan_exercise` (
  `training_diary_x_training_plan_exercise_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `training_diary_x_training_plan_exercise_t_p_x_e_fk` varchar(255) NOT NULL,
  `training_diary_x_training_plan_exercise_comment` varchar(255) NOT NULL,
  `training_diary_x_training_plan_exercise_flag_finished` varchar(255) NOT NULL,
  `training_diary_x_training_plan_exercise_create_date` varchar(255) NOT NULL,
  `training_diary_x_training_plan_exercise_create_user_fk` varchar(255) NOT NULL,
  `training_diary_x_training_plan_exercise_update_date` varchar(255) NOT NULL,
  `training_diary_x_training_plan_exercise_update_user_fk` varchar(255) NOT NULL,
  `training_diary_x_training_plan_exercise_training_diary_fk` int(10) unsigned NOT NULL,
  PRIMARY KEY (`training_diary_x_training_plan_exercise_id`),
  KEY `index_training_diary_x_training_plan_exercise_training_diary_fk` (`training_diary_x_training_plan_exercise_training_diary_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='Übungen für das Trainingstagebuch';

-- Exportiere Daten aus Tabelle rundumfit.training_diary_x_training_plan_exercise: ~71 rows (ungefähr)
DELETE FROM `training_diary_x_training_plan_exercise`;
/*!40000 ALTER TABLE `training_diary_x_training_plan_exercise` DISABLE KEYS */;
INSERT INTO `training_diary_x_training_plan_exercise` (`training_diary_x_training_plan_exercise_id`, `training_diary_x_training_plan_exercise_t_p_x_e_fk`, `training_diary_x_training_plan_exercise_comment`, `training_diary_x_training_plan_exercise_flag_finished`, `training_diary_x_training_plan_exercise_create_date`, `training_diary_x_training_plan_exercise_create_user_fk`, `training_diary_x_training_plan_exercise_update_date`, `training_diary_x_training_plan_exercise_update_user_fk`, `training_diary_x_training_plan_exercise_training_diary_fk`) VALUES
	(1, '39', '', '', '2017-04-19 18:29:42', '22', '', '', 1),
	(2, '41', '', '1', '2017-04-19 18:30:07', '22', '2017-04-22 19:08:57', '1', 2),
	(3, '46', '', '1', '2017-04-19 18:44:34', '22', '2017-04-19 18:44:34', '1', 2),
	(4, '34', '', '', '2017-04-22 18:01:01', '22', '', '', 3),
	(5, '45', '', '1', '2017-04-22 19:08:52', '22', '2017-04-22 19:08:53', '1', 2),
	(6, '42', '', '1', '2017-04-22 19:08:59', '22', '2017-04-22 19:08:59', '1', 2),
	(7, '43', '', '1', '2017-04-22 19:09:01', '22', '2017-04-22 19:09:01', '1', 2),
	(8, '44', '', '1', '2017-04-22 19:09:03', '22', '2017-04-22 19:09:03', '1', 2);
/*!40000 ALTER TABLE `training_diary_x_training_plan_exercise` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
