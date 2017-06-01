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

-- Exportiere Struktur von Tabelle rundumfit.training_plan_x_device_option
DROP TABLE IF EXISTS `test_training_plan_x_device_option`;
CREATE TABLE IF NOT EXISTS `test_training_plan_x_device_option` (
  `training_plan_x_device_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_plan_x_device_option_device_option_fk` int(10) unsigned NOT NULL,
  `training_plan_x_device_option_device_option_value` varchar(255) NOT NULL,
  `training_plan_x_device_option_create_date` datetime NOT NULL,
  `training_plan_x_device_option_create_user_fk` int(10) unsigned NOT NULL,
  `training_plan_x_device_option_update_date` datetime NOT NULL,
  `training_plan_x_device_option_update_user_fk` int(10) unsigned NOT NULL,
  `training_plan_x_device_option_training_plan_exercise_fk` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_training_plan_x_device_option_id` (`training_plan_x_device_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_plan_x_device_option: ~10 rows (ungefähr)
DELETE FROM `test_training_plan_x_device_option`;
/*!40000 ALTER TABLE `test_training_plan_x_device_option` DISABLE KEYS */;
INSERT INTO `test_training_plan_x_device_option` (`training_plan_x_device_option_id`, `training_plan_x_device_option_device_option_fk`, `training_plan_x_device_option_device_option_value`, `training_plan_x_device_option_create_date`, `training_plan_x_device_option_create_user_fk`, `training_plan_x_device_option_update_date`, `training_plan_x_device_option_update_user_fk`, `training_plan_x_device_option_training_plan_exercise_fk`) VALUES
	(52, 1, '2', '2017-03-15 20:39:41', 1, '0000-00-00 00:00:00', 0, 33),
	(60, 2, '4', '2017-03-24 16:52:10', 1, '0000-00-00 00:00:00', 0, 39),
	(61, 2, '3', '2017-03-24 17:32:39', 1, '2017-04-19 18:07:48', 1, 34),
	(62, 2, '10', '2017-04-13 19:22:22', 1, '2017-04-13 19:31:23', 1, 46),
	(64, 2, '15', '2017-04-13 19:40:33', 1, '0000-00-00 00:00:00', 0, 45),
	(65, 2, '4', '2017-05-09 20:37:49', 1, '0000-00-00 00:00:00', 0, 39),
	(66, 2, '3', '2017-05-09 20:37:49', 1, '0000-00-00 00:00:00', 0, 34),
	(67, 2, '15', '2017-05-09 20:37:49', 1, '0000-00-00 00:00:00', 0, 45),
	(68, 2, '10', '2017-05-09 20:37:49', 1, '0000-00-00 00:00:00', 0, 46),
	(69, 2, '1|1.25|1.5|2|2.5|3|3.5|4|4.5|5|6|7|8|9|10|15|20|25|30|35|40|45|50|55|60|65', '2017-05-09 20:37:49', 1, '0000-00-00 00:00:00', 0, 59),
	(70, 1, '4', '2017-05-13 18:46:52', 1, '0000-00-00 00:00:00', 0, 35);
/*!40000 ALTER TABLE `test_training_plan_x_device_option` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
