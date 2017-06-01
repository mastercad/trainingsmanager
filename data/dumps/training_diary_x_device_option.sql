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

-- Exportiere Struktur von Tabelle rundumfit.training_diary_x_device_option
DROP TABLE IF EXISTS `test_training_diary_x_device_option`;
CREATE TABLE IF NOT EXISTS `test_training_diary_x_device_option` (
  `training_diary_x_device_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_diary_x_device_option_device_option_fk` int(10) unsigned NOT NULL,
  `training_diary_x_device_option_device_option_value` varchar(255) NOT NULL,
  `training_diary_x_device_option_t_d_x_t_p_e_fk` int(10) unsigned NOT NULL,
  `training_diary_x_device_option_create_date` varchar(255) NOT NULL,
  `training_diary_x_device_option_create_user_fk` int(10) unsigned NOT NULL,
  `training_diary_x_device_option_update_date` varchar(255) NOT NULL,
  `training_diary_x_device_option_update_user_fk` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_training_diary_x_device_option_id` (`training_diary_x_device_option_id`),
  KEY `index_training_diary_x_device_option_device_option_fk` (`training_diary_x_device_option_device_option_fk`),
  KEY `index_training_diary_x_device_option_t_p_e_fk` (`training_diary_x_device_option_t_d_x_t_p_e_fk`),
  KEY `index_training_diary_x_device_option_create_user_fk` (`training_diary_x_device_option_create_user_fk`),
  KEY `index_training_diary_x_device_option_update_user_fk` (`training_diary_x_device_option_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_diary_x_device_option: ~32 rows (ungefähr)
DELETE FROM `test_training_diary_x_device_option`;
/*!40000 ALTER TABLE `test_training_diary_x_device_option` DISABLE KEYS */;
INSERT INTO `test_training_diary_x_device_option` (`training_diary_x_device_option_id`, `training_diary_x_device_option_device_option_fk`, `training_diary_x_device_option_device_option_value`, `training_diary_x_device_option_t_d_x_t_p_e_fk`, `training_diary_x_device_option_create_date`, `training_diary_x_device_option_create_user_fk`, `training_diary_x_device_option_update_date`, `training_diary_x_device_option_update_user_fk`) VALUES
	(1, 2, '12', 3, '2017-04-19 18:44:34', 1, '', 0),
	(2, 2, '3', 5, '2017-04-23 19:08:53', 1, '', 0),
	(3, 2, '4', 10, '2017-04-24 19:09:59', 1, '', 0),
	(4, 2, '10', 11, '2017-04-25 19:10:12', 1, '', 0),
	(5, 2, '8', 19, '2017-04-26 19:11:37', 1, '', 0),
	(6, 2, '8', 20, '2017-04-27 19:11:44', 1, '', 0),
	(7, 2, '15', 25, '2017-04-28 19:12:07', 1, '', 0),
	(8, 2, '12', 26, '2017-04-29 19:12:14', 1, '', 0),
	(9, 2, '15', 28, '2017-05-03 23:51:38', 1, '', 0),
	(10, 2, '15', 32, '2017-05-03 23:52:46', 1, '', 0),
	(11, 2, '17', 34, '2017-05-03 23:53:13', 1, '', 0),
	(12, 2, '15', 35, '2017-05-03 23:53:52', 1, '', 0),
	(13, 2, '17', 40, '2017-05-03 23:54:27', 1, '', 0),
	(14, 2, '15', 44, '2017-05-03 23:56:06', 1, '', 0),
	(15, 2, '15', 46, '2017-05-04 00:00:32', 1, '', 0),
	(16, 2, '10', 47, '2017-05-04 00:02:11', 1, '', 0),
	(17, 2, '15', 55, '2017-05-04 00:03:54', 1, '', 0),
	(18, 2, '10', 56, '2017-05-04 00:04:00', 1, '', 0),
	(19, 2, '10', 58, '2017-05-04 00:04:43', 1, '', 0),
	(20, 2, '15', 62, '2017-05-04 08:48:45', 1, '', 0),
	(21, 2, '4', 68, '2017-05-08 20:42:05', 1, '', 0),
	(22, 2, '3', 69, '2017-05-08 20:42:25', 1, '', 0),
	(23, 2, '4', 71, '2017-05-13 09:22:18', 1, '', 0),
	(24, 2, '4', 77, '2017-05-13 10:21:16', 1, '', 0),
	(25, 2, '4', 80, '2017-05-13 10:23:00', 1, '', 0),
	(26, 2, '4', 81, '2017-05-13 10:24:39', 1, '', 0),
	(27, 2, '4', 83, '2017-05-13 10:35:38', 1, '', 0),
	(28, 2, '7', 91, '2017-05-13 10:46:56', 1, '', 0),
	(29, 2, '4', 99, '2017-05-13 10:54:59', 1, '', 0),
	(30, 2, '1|1.25|1.5|2|2.5|3|3.5|4|4.5|5|6|7|8|9|10|15|20|25|30|35|40|45|50|55|60|65', 104, '2017-05-13 10:58:19', 1, '', 0),
	(31, 2, '4', 114, '2017-05-13 11:09:50', 1, '', 0),
	(32, 2, '1|1.25|1.5|2|2.5|3|3.5|4|4.5|5|6|7|8|9|10|15|20|25|30|35|40|45|50|55|60|65', 119, '2017-05-13 11:13:09', 1, '', 0),
	(33, 2, '7', 128, '2017-05-13 11:24:49', 1, '', 0),
	(34, 2, '1|1.25|1.5|2|2.5|3|3.5|4|4.5|5|6|7|8|9|10|15|20|25|30|35|40|45|50|55|60|65', 133, '2017-05-13 15:02:05', 1, '', 0),
	(35, 1, '5', 136, '2017-05-13 18:47:30', 1, '', 0),
	(36, 2, '22.5', 137, '2017-05-13 18:47:36', 1, '', 0);
/*!40000 ALTER TABLE `test_training_diary_x_device_option` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
