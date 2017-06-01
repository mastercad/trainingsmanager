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

-- Exportiere Struktur von Tabelle rundumfit.training_plans
DROP TABLE IF EXISTS `test_training_plans`;
CREATE TABLE IF NOT EXISTS `test_training_plans` (
  `training_plan_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `training_plan_name` varchar(250) NOT NULL,
  `training_plan_training_plan_layout_fk` varchar(250) NOT NULL,
  `training_plan_user_fk` int(11) NOT NULL COMMENT 'user für den dieser trainingsplan gilt',
  `training_plan_parent_fk` int(11) unsigned DEFAULT NULL COMMENT 'haupttrainingsplan (für splits)',
  `training_plan_active` tinyint(1) NOT NULL COMMENT 'trainingsplan aktiv? damit nur der aktuellste angezeigt wird im tagebuch zum training',
  `training_plan_order` tinyint(1) NOT NULL COMMENT 'ist für splitpläne gedacht um die reihenfolge zu beeinflussen',
  `training_plan_create_date` datetime NOT NULL,
  `training_plan_create_user_fk` int(11) unsigned DEFAULT NULL,
  `training_plan_update_date` datetime DEFAULT NULL,
  `training_plan_update_user_fk` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`training_plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_plans: ~20 rows (ungefähr)
DELETE FROM `test_training_plans`;
/*!40000 ALTER TABLE `test_training_plans` DISABLE KEYS */;
INSERT INTO `test_training_plans` (`training_plan_id`, `training_plan_name`, `training_plan_training_plan_layout_fk`, `training_plan_user_fk`, `training_plan_parent_fk`, `training_plan_active`, `training_plan_order`, `training_plan_create_date`, `training_plan_create_user_fk`, `training_plan_update_date`, `training_plan_update_user_fk`) VALUES
	(39, '', '2', 22, 0, 0, 0, '2017-03-15 21:24:15', 22, NULL, NULL),
	(50, 'Montag', '1', 22, 39, 0, 2, '2017-03-15 20:50:00', 22, '2017-05-13 10:53:32', 1),
	(54, 'Mittwoch', '1', 22, 39, 0, 3, '2017-03-15 21:24:14', 22, '2017-05-13 10:53:32', 1),
	(55, 'Freitag', '1', 22, 39, 0, 4, '2017-03-15 21:24:15', 22, '2017-05-13 10:53:32', 1),
	(124, '', '2', 24, NULL, 1, 1, '2017-05-13 14:54:28', 22, NULL, NULL),
	(125, 'Mittwoch', '1', 24, 124, 1, 3, '2017-05-13 14:54:28', 22, '2017-05-13 14:55:17', 22),
	(126, 'Montag', '1', 24, 124, 1, 2, '2017-05-13 14:55:17', 22, NULL, NULL),
	(127, 'Freitag', '1', 24, 124, 1, 4, '2017-05-13 14:55:17', 22, NULL, NULL),
	(128, '', '2', 22, NULL, 0, 1, '2017-05-13 18:57:30', 22, NULL, NULL),
	(129, 'Montag', '1', 22, 128, 0, 2, '2017-05-13 18:57:30', 22, NULL, NULL),
	(130, 'Dienstag', '1', 22, 128, 0, 3, '2017-05-13 18:58:19', 22, NULL, NULL),
	(131, 'Mittwoch', '1', 22, 128, 0, 4, '2017-05-13 18:58:19', 22, NULL, NULL),
	(132, 'Donnerstag', '1', 22, 128, 0, 5, '2017-05-13 18:58:19', 22, NULL, NULL),
	(133, 'Freitag', '1', 22, 128, 0, 6, '2017-05-13 18:58:19', 22, NULL, NULL),
	(134, 'Sonnabend', '1', 22, 128, 0, 7, '2017-05-13 18:58:20', 22, NULL, NULL),
	(135, 'Sonntag', '1', 22, 128, 0, 8, '2017-05-13 18:58:20', 22, NULL, NULL),
	(136, '', '2', 22, NULL, 1, 1, '2017-05-13 19:29:47', 22, NULL, NULL),
	(137, 'Montag', '1', 22, 136, 1, 2, '2017-05-13 19:30:13', 22, '2017-05-13 20:13:54', 22),
	(138, 'Dienstag', '1', 22, 136, 1, 3, '2017-05-13 20:09:42', 22, NULL, NULL),
	(139, 'Dienstag', '1', 22, 136, 1, 3, '2017-05-13 20:14:19', 22, NULL, NULL),
	(140, 'Mittwoch', '1', 22, 136, 1, 4, '2017-05-13 20:14:22', 22, NULL, NULL),
	(141, 'Donnerstag', '1', 22, 136, 1, 5, '2017-05-13 20:14:22', 22, NULL, NULL);
/*!40000 ALTER TABLE `test_training_plans` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
