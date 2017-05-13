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
DROP TABLE IF EXISTS `training_plans`;
CREATE TABLE IF NOT EXISTS `training_plans` (
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
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_plans: ~12 rows (ungefähr)
DELETE FROM `training_plans`;
/*!40000 ALTER TABLE `training_plans` DISABLE KEYS */;
INSERT INTO `training_plans` (`training_plan_id`, `training_plan_name`, `training_plan_training_plan_layout_fk`, `training_plan_user_fk`, `training_plan_parent_fk`, `training_plan_active`, `training_plan_order`, `training_plan_create_date`, `training_plan_create_user_fk`, `training_plan_update_date`, `training_plan_update_user_fk`) VALUES
	(39, '', '2', 22, 0, 1, 1, '2017-03-15 21:24:15', 22, NULL, NULL),
	(50, 'Montag', '1', 22, 0, 1, 1, '2017-03-15 20:50:00', 22, NULL, NULL),
	(54, 'Mittwoch', '1', 22, 39, 1, 3, '2017-03-15 21:24:14', 22, NULL, NULL),
	(55, 'Freitag', '1', 22, 39, 1, 4, '2017-03-15 21:24:15', 22, NULL, NULL);
/*!40000 ALTER TABLE `training_plans` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
