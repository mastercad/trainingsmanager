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

-- Exportiere Struktur von Tabelle rundumfit.training_diary_x_training_plan
DROP TABLE IF EXISTS `test_training_diary_x_training_plan`;
CREATE TABLE IF NOT EXISTS `test_training_diary_x_training_plan` (
  `training_diary_x_training_plan_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `training_diary_x_training_plan_training_plan_fk` int(255) unsigned DEFAULT NULL,
  `training_diary_x_training_plan_flag_finished` int(4) unsigned NOT NULL,
  `training_diary_x_training_plan_create_date` datetime NOT NULL,
  `training_diary_x_training_plan_create_user_fk` int(255) unsigned DEFAULT NULL,
  `training_diary_x_training_plan_update_date` datetime NOT NULL,
  `training_diary_x_training_plan_update_user_fk` int(255) unsigned DEFAULT NULL,
  `training_diary_x_training_plan_training_diary_fk` int(10) unsigned NOT NULL,
  PRIMARY KEY (`training_diary_x_training_plan_id`),
  KEY `index_training_diary_x_training_plan_training_diary_fk` (`training_diary_x_training_plan_training_diary_fk`),
  KEY `index_training_diary_x_training_plan_create_user_fk` (`training_diary_x_training_plan_create_user_fk`),
  KEY `index_training_diary_x_training_plan_update_user_fk` (`training_diary_x_training_plan_update_user_fk`),
  KEY `index_training_diary_x_training_plan_training_plan_fk` (`training_diary_x_training_plan_training_plan_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_diary_x_training_plan: ~39 rows (ungefähr)
DELETE FROM `test_training_diary_x_training_plan`;
/*!40000 ALTER TABLE `test_training_diary_x_training_plan` DISABLE KEYS */;
INSERT INTO `test_training_diary_x_training_plan` (`training_diary_x_training_plan_id`, `training_diary_x_training_plan_training_plan_fk`, `training_diary_x_training_plan_flag_finished`, `training_diary_x_training_plan_create_date`, `training_diary_x_training_plan_create_user_fk`, `training_diary_x_training_plan_update_date`, `training_diary_x_training_plan_update_user_fk`, `training_diary_x_training_plan_training_diary_fk`) VALUES
	(1, 50, 1, '2017-04-19 18:29:42', 22, '0000-00-00 00:00:00', NULL, 1),
	(2, 54, 1, '2017-04-19 18:30:07', 22, '2017-04-22 19:09:03', 22, 2),
	(3, 55, 1, '2017-04-22 18:01:01', 22, '0000-00-00 00:00:00', NULL, 3),
	(4, 50, 1, '2017-04-22 19:09:47', 22, '2017-04-22 19:10:22', 22, 4),
	(5, 54, 1, '2017-04-22 19:11:24', 22, '2017-04-22 19:11:44', 22, 5),
	(6, 55, 1, '2017-04-22 19:11:55', 22, '2017-04-22 19:12:14', 22, 6),
	(7, 50, 1, '2017-04-22 19:27:03', 22, '2017-05-03 23:52:46', 22, 7),
	(8, 54, 1, '2017-05-03 23:53:03', 22, '2017-05-03 23:54:05', 22, 8),
	(9, 55, 1, '2017-05-03 23:54:20', 22, '2017-05-03 23:59:45', 22, 9),
	(10, 50, 1, '2017-05-04 00:00:24', 22, '2017-05-04 00:02:51', 22, 10),
	(11, 50, 1, '2017-05-04 00:03:02', 22, '2017-05-04 00:04:00', 22, 11),
	(12, 50, 1, '2017-05-04 00:04:37', 22, '2017-05-04 08:48:46', 22, 12),
	(13, 54, 1, '2017-05-04 14:41:34', 22, '0000-00-00 00:00:00', NULL, 13),
	(14, 54, 1, '2017-05-08 20:39:22', 22, '0000-00-00 00:00:00', NULL, 14),
	(15, 54, 1, '2017-05-08 20:41:57', 22, '2017-05-08 20:42:09', 22, 15),
	(16, 54, 1, '2017-05-08 20:42:19', 22, '2017-05-08 20:42:27', 22, 16),
	(17, 55, 1, '2017-05-08 21:25:17', 22, '0000-00-00 00:00:00', NULL, 17),
	(18, 50, 1, '2017-05-13 09:42:22', 22, '2017-05-13 09:52:45', 22, 18),
	(19, 54, 1, '2017-05-13 09:57:06', 22, '2017-05-13 09:57:10', 22, 19),
	(20, 55, 1, '2017-05-13 09:57:30', 22, '2017-05-13 10:36:16', 22, 20),
	(21, 50, 1, '2017-05-13 10:36:23', 22, '2017-05-13 10:36:28', 22, 21),
	(22, 54, 1, '2017-05-13 10:36:33', 22, '2017-05-13 10:36:35', 22, 22),
	(23, 55, 1, '2017-05-13 10:36:45', 22, '2017-05-13 10:47:02', 22, 23),
	(24, 50, 1, '2017-05-13 10:47:09', 22, '2017-05-13 10:54:23', 22, 24),
	(25, 54, 1, '2017-05-13 10:54:28', 22, '2017-05-13 10:54:34', 22, 25),
	(26, 55, 1, '2017-05-13 10:54:52', 22, '2017-05-13 10:55:05', 22, 26),
	(27, 92, 1, '2017-05-13 10:58:15', 22, '2017-05-13 10:58:22', 22, 27),
	(28, 91, 1, '2017-05-13 10:58:27', 22, '2017-05-13 10:58:33', 22, 28),
	(29, 50, 1, '2017-05-13 10:59:39', 22, '2017-05-13 11:01:24', 22, 29),
	(30, 54, 1, '2017-05-13 11:01:31', 22, '2017-05-13 11:01:33', 22, 30),
	(31, 55, 1, '2017-05-13 11:09:32', 22, '2017-05-13 11:10:31', 22, 31),
	(32, 92, 1, '2017-05-13 11:12:22', 22, '2017-05-13 11:14:11', 22, 32),
	(33, 91, 1, '2017-05-13 11:14:45', 22, '2017-05-13 11:15:18', 22, 33),
	(34, 50, 1, '2017-05-13 11:20:12', 22, '2017-05-13 11:20:19', 22, 34),
	(35, 54, 1, '2017-05-13 11:24:02', 22, '2017-05-13 11:24:06', 22, 35),
	(36, 55, 1, '2017-05-13 11:24:17', 22, '2017-05-13 11:24:56', 22, 36),
	(38, 126, 1, '2017-05-13 15:01:17', 24, '2017-05-13 15:02:05', 22, 38),
	(39, 125, 1, '2017-05-13 15:02:10', 24, '2017-05-13 15:02:12', 22, 39),
	(40, 127, 1, '2017-05-13 15:02:16', 24, '2017-05-13 15:02:20', 22, 40),
	(41, 50, 1, '2017-05-13 18:30:51', 22, '2017-05-13 18:47:36', 22, 41),
	(42, 54, 1, '2017-05-13 18:56:57', 22, '2017-05-13 18:57:02', 22, 42);
/*!40000 ALTER TABLE `test_training_diary_x_training_plan` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
