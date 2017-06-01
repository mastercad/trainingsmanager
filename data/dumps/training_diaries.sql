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

-- Exportiere Struktur von Tabelle rundumfit.training_diaries
DROP TABLE IF EXISTS `test_training_diaries`;
CREATE TABLE IF NOT EXISTS `test_training_diaries` (
  `training_diary_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `training_diary_comment` text,
  `training_diary_create_date` datetime NOT NULL,
  `training_diary_create_user_fk` int(11) unsigned NOT NULL,
  `training_diary_update_date` datetime NOT NULL,
  `training_diary_update_user_fk` int(11) unsigned NOT NULL,
  PRIMARY KEY (`training_diary_id`),
  KEY `index_training_diary_create_user_fk` (`training_diary_create_user_fk`),
  KEY `index_training_diary_update_user_fk` (`training_diary_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='trainingstagebücher';

-- Exportiere Daten aus Tabelle rundumfit.training_diaries: ~39 rows (ungefähr)
DELETE FROM `test_training_diaries`;
/*!40000 ALTER TABLE `test_training_diaries` DISABLE KEYS */;
INSERT INTO `test_training_diaries` (`training_diary_id`, `training_diary_comment`, `training_diary_create_date`, `training_diary_create_user_fk`, `training_diary_update_date`, `training_diary_update_user_fk`) VALUES
	(1, NULL, '2017-04-19 18:29:42', 22, '0000-00-00 00:00:00', 0),
	(2, NULL, '2017-04-19 18:30:07', 22, '0000-00-00 00:00:00', 0),
	(3, NULL, '2017-04-22 18:01:00', 22, '0000-00-00 00:00:00', 0),
	(4, NULL, '2017-04-22 19:09:46', 22, '0000-00-00 00:00:00', 0),
	(5, NULL, '2017-04-22 19:11:24', 22, '0000-00-00 00:00:00', 0),
	(6, NULL, '2017-04-22 19:11:55', 22, '0000-00-00 00:00:00', 0),
	(7, NULL, '2017-04-22 19:27:02', 22, '0000-00-00 00:00:00', 0),
	(8, NULL, '2017-05-03 23:53:03', 22, '0000-00-00 00:00:00', 0),
	(9, NULL, '2017-05-03 23:54:20', 22, '0000-00-00 00:00:00', 0),
	(10, NULL, '2017-05-04 00:00:24', 22, '0000-00-00 00:00:00', 0),
	(11, NULL, '2017-05-04 00:03:02', 22, '0000-00-00 00:00:00', 0),
	(12, NULL, '2017-05-04 00:04:37', 22, '0000-00-00 00:00:00', 0),
	(13, NULL, '2017-05-04 14:41:34', 22, '0000-00-00 00:00:00', 0),
	(14, NULL, '2017-05-08 20:39:22', 22, '0000-00-00 00:00:00', 0),
	(15, NULL, '2017-05-08 20:41:57', 22, '0000-00-00 00:00:00', 0),
	(16, NULL, '2017-05-08 20:42:19', 22, '0000-00-00 00:00:00', 0),
	(17, NULL, '2017-05-08 21:25:17', 22, '0000-00-00 00:00:00', 0),
	(18, NULL, '2017-05-13 09:42:22', 22, '0000-00-00 00:00:00', 0),
	(19, NULL, '2017-05-13 09:57:06', 22, '0000-00-00 00:00:00', 0),
	(20, NULL, '2017-05-13 09:57:30', 22, '0000-00-00 00:00:00', 0),
	(21, NULL, '2017-05-13 10:36:23', 22, '0000-00-00 00:00:00', 0),
	(22, NULL, '2017-05-13 10:36:32', 22, '0000-00-00 00:00:00', 0),
	(23, NULL, '2017-05-13 10:36:45', 22, '0000-00-00 00:00:00', 0),
	(24, NULL, '2017-05-13 10:47:09', 22, '0000-00-00 00:00:00', 0),
	(25, NULL, '2017-05-13 10:54:28', 22, '0000-00-00 00:00:00', 0),
	(26, NULL, '2017-05-13 10:54:52', 22, '0000-00-00 00:00:00', 0),
	(27, NULL, '2017-05-13 10:58:15', 22, '0000-00-00 00:00:00', 0),
	(28, NULL, '2017-05-13 10:58:26', 22, '0000-00-00 00:00:00', 0),
	(29, NULL, '2017-05-13 10:59:39', 22, '0000-00-00 00:00:00', 0),
	(30, NULL, '2017-05-13 11:01:31', 22, '0000-00-00 00:00:00', 0),
	(31, NULL, '2017-05-13 11:09:32', 22, '0000-00-00 00:00:00', 0),
	(32, NULL, '2017-05-13 11:12:22', 22, '0000-00-00 00:00:00', 0),
	(33, NULL, '2017-05-13 11:14:45', 22, '0000-00-00 00:00:00', 0),
	(34, NULL, '2017-05-13 11:20:12', 22, '0000-00-00 00:00:00', 0),
	(35, NULL, '2017-05-13 11:24:02', 22, '0000-00-00 00:00:00', 0),
	(36, NULL, '2017-05-13 11:24:17', 22, '0000-00-00 00:00:00', 0),
	(38, NULL, '2017-05-13 15:01:17', 24, '0000-00-00 00:00:00', 0),
	(39, NULL, '2017-05-13 15:02:10', 24, '0000-00-00 00:00:00', 0),
	(40, NULL, '2017-05-13 15:02:16', 24, '0000-00-00 00:00:00', 0),
	(41, NULL, '2017-05-13 18:30:51', 22, '0000-00-00 00:00:00', 0),
	(42, NULL, '2017-05-13 18:56:57', 22, '0000-00-00 00:00:00', 0);
/*!40000 ALTER TABLE `test_training_diaries` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
