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

-- Exportiere Struktur von Tabelle rundumfit.training_diary_x_exercise_option
DROP TABLE IF EXISTS `test_training_diary_x_exercise_option`;
CREATE TABLE IF NOT EXISTS `test_training_diary_x_exercise_option` (
  `training_diary_x_exercise_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_diary_x_exercise_option_exercise_option_fk` int(10) unsigned NOT NULL,
  `training_diary_x_exercise_option_exercise_option_value` varchar(255) NOT NULL,
  `training_diary_x_exercise_option_create_date` datetime NOT NULL,
  `training_diary_x_exercise_option_create_user_fk` int(10) unsigned NOT NULL,
  `training_diary_x_exercise_option_update_date` datetime NOT NULL,
  `training_diary_x_exercise_option_update_user_fk` int(10) unsigned DEFAULT NULL,
  `training_diary_x_exercise_option_t_d_x_t_p_e_fk` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_training_diary_x_exercise_option_id` (`training_diary_x_exercise_option_id`),
  KEY `index_training_diary_x_exercise_option_create_user_fk` (`training_diary_x_exercise_option_create_user_fk`),
  KEY `index_training_diary_x_exercise_option_update_user_fk` (`training_diary_x_exercise_option_update_user_fk`),
  KEY `index_training_diary_x_exercise_option_t_d_x_e_fk` (`training_diary_x_exercise_option_t_d_x_t_p_e_fk`),
  KEY `index_training_diary_x_exercise_option_exercise_option_fk` (`training_diary_x_exercise_option_exercise_option_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_diary_x_exercise_option: ~45 rows (ungefähr)
DELETE FROM `test_training_diary_x_exercise_option`;
/*!40000 ALTER TABLE `test_training_diary_x_exercise_option` DISABLE KEYS */;
INSERT INTO `test_training_diary_x_exercise_option` (`training_diary_x_exercise_option_id`, `training_diary_x_exercise_option_exercise_option_fk`, `training_diary_x_exercise_option_exercise_option_value`, `training_diary_x_exercise_option_create_date`, `training_diary_x_exercise_option_create_user_fk`, `training_diary_x_exercise_option_update_date`, `training_diary_x_exercise_option_update_user_fk`, `training_diary_x_exercise_option_t_d_x_t_p_e_fk`) VALUES
	(1, 2, '5', '2017-04-19 18:44:34', 1, '0000-00-00 00:00:00', NULL, 3),
	(2, 1, '13', '2017-04-20 18:44:34', 1, '0000-00-00 00:00:00', NULL, 3),
	(3, 2, '3', '2017-04-22 19:10:12', 1, '0000-00-00 00:00:00', NULL, 11),
	(4, 1, '15', '2017-04-23 19:10:12', 1, '0000-00-00 00:00:00', NULL, 11),
	(5, 2, '4', '2017-04-24 19:11:44', 1, '0000-00-00 00:00:00', NULL, 20),
	(6, 1, '12', '2017-04-25 19:11:44', 1, '0000-00-00 00:00:00', NULL, 20),
	(7, 2, '4', '2017-04-26 19:12:14', 1, '0000-00-00 00:00:00', NULL, 26),
	(8, 1, '10', '2017-04-27 19:12:14', 1, '0000-00-00 00:00:00', NULL, 26),
	(9, 2, '4', '2017-05-03 23:51:37', 1, '0000-00-00 00:00:00', NULL, 28),
	(10, 1, '12', '2017-05-03 23:51:38', 1, '0000-00-00 00:00:00', NULL, 28),
	(11, 2, '4', '2017-05-03 23:53:13', 1, '0000-00-00 00:00:00', NULL, 34),
	(12, 1, '12', '2017-05-03 23:53:13', 1, '0000-00-00 00:00:00', NULL, 34),
	(13, 2, '4', '2017-05-03 23:54:27', 1, '0000-00-00 00:00:00', NULL, 40),
	(14, 1, '12', '2017-05-03 23:54:27', 1, '0000-00-00 00:00:00', NULL, 40),
	(15, 2, '4', '2017-05-04 00:02:11', 1, '0000-00-00 00:00:00', NULL, 47),
	(16, 1, '12', '2017-05-04 00:02:11', 1, '0000-00-00 00:00:00', NULL, 47),
	(17, 2, '4', '2017-05-04 00:04:00', 1, '0000-00-00 00:00:00', NULL, 56),
	(18, 1, '12', '2017-05-04 00:04:00', 1, '0000-00-00 00:00:00', NULL, 56),
	(19, 2, '4', '2017-05-04 00:04:43', 1, '0000-00-00 00:00:00', NULL, 58),
	(20, 1, '12', '2017-05-04 00:04:43', 1, '0000-00-00 00:00:00', NULL, 58),
	(21, 2, '4', '2017-05-08 20:42:05', 1, '0000-00-00 00:00:00', NULL, 68),
	(22, 1, '12', '2017-05-08 20:42:05', 1, '0000-00-00 00:00:00', NULL, 68),
	(23, 3, '2/12/2', '2017-05-08 20:42:05', 1, '0000-00-00 00:00:00', NULL, 68),
	(24, 2, '4', '2017-05-13 09:22:18', 1, '0000-00-00 00:00:00', NULL, 71),
	(25, 1, '12', '2017-05-13 09:22:18', 1, '0000-00-00 00:00:00', NULL, 71),
	(26, 3, '2/12/2', '2017-05-13 09:22:18', 1, '0000-00-00 00:00:00', NULL, 71),
	(27, 2, '4', '2017-05-13 10:21:16', 1, '0000-00-00 00:00:00', NULL, 77),
	(28, 1, '12', '2017-05-13 10:21:16', 1, '0000-00-00 00:00:00', NULL, 77),
	(29, 3, '2/12/2', '2017-05-13 10:21:16', 1, '0000-00-00 00:00:00', NULL, 77),
	(30, 2, '4', '2017-05-13 10:22:55', 1, '0000-00-00 00:00:00', NULL, 80),
	(31, 1, '12', '2017-05-13 10:22:57', 1, '0000-00-00 00:00:00', NULL, 80),
	(32, 3, '2/12/2', '2017-05-13 10:22:59', 1, '0000-00-00 00:00:00', NULL, 80),
	(33, 2, '4', '2017-05-13 10:24:39', 1, '0000-00-00 00:00:00', NULL, 81),
	(34, 1, '12', '2017-05-13 10:24:39', 1, '0000-00-00 00:00:00', NULL, 81),
	(35, 3, '2/12/2', '2017-05-13 10:24:39', 1, '0000-00-00 00:00:00', NULL, 81),
	(36, 2, '4', '2017-05-13 10:35:38', 1, '0000-00-00 00:00:00', NULL, 83),
	(37, 1, '12', '2017-05-13 10:35:38', 1, '0000-00-00 00:00:00', NULL, 83),
	(38, 3, '2/12/2', '2017-05-13 10:35:38', 1, '0000-00-00 00:00:00', NULL, 83),
	(39, 2, '4', '2017-05-13 10:46:56', 1, '0000-00-00 00:00:00', NULL, 91),
	(40, 1, '12', '2017-05-13 10:46:56', 1, '0000-00-00 00:00:00', NULL, 91),
	(41, 3, '2/12/2', '2017-05-13 10:46:56', 1, '0000-00-00 00:00:00', NULL, 91),
	(42, 2, '4', '2017-05-13 10:54:59', 1, '0000-00-00 00:00:00', NULL, 99),
	(43, 1, '12', '2017-05-13 10:54:59', 1, '0000-00-00 00:00:00', NULL, 99),
	(44, 3, '2/12/2', '2017-05-13 10:54:59', 1, '0000-00-00 00:00:00', NULL, 99),
	(45, 2, '4', '2017-05-13 11:09:49', 1, '0000-00-00 00:00:00', NULL, 114),
	(46, 1, '12', '2017-05-13 11:09:50', 1, '0000-00-00 00:00:00', NULL, 114),
	(47, 3, '2/12/2', '2017-05-13 11:09:50', 1, '0000-00-00 00:00:00', NULL, 114),
	(48, 2, '4', '2017-05-13 11:24:49', 1, '0000-00-00 00:00:00', NULL, 128),
	(49, 1, '12', '2017-05-13 11:24:49', 1, '0000-00-00 00:00:00', NULL, 128),
	(50, 3, '2/12/2', '2017-05-13 11:24:49', 1, '0000-00-00 00:00:00', NULL, 128);
/*!40000 ALTER TABLE `test_training_diary_x_exercise_option` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
