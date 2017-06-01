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

-- Exportiere Struktur von Tabelle rundumfit.training_plan_layouts
DROP TABLE IF EXISTS `test_training_plan_layouts`;
CREATE TABLE IF NOT EXISTS `test_training_plan_layouts` (
  `training_plan_layout_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `training_plan_layout_name` varchar(250) NOT NULL,
  `training_plan_layout_create_date` datetime NOT NULL,
  `training_plan_layout_create_user_fk` int(11) unsigned NOT NULL,
  `training_plan_layout_update_date` datetime NOT NULL,
  `training_plan_layout_update_user_fk` int(11) unsigned NOT NULL,
  PRIMARY KEY (`training_plan_layout_id`),
  UNIQUE KEY `trainingsplan_layout_name` (`training_plan_layout_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_plan_layouts: ~2 rows (ungefähr)
DELETE FROM `test_training_plan_layouts`;
/*!40000 ALTER TABLE `test_training_plan_layouts` DISABLE KEYS */;
INSERT INTO `test_training_plan_layouts` (`training_plan_layout_id`, `training_plan_layout_name`, `training_plan_layout_create_date`, `training_plan_layout_create_user_fk`, `training_plan_layout_update_date`, `training_plan_layout_update_user_fk`) VALUES
	(1, 'Normal', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(2, 'Split', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0);
/*!40000 ALTER TABLE `test_training_plan_layouts` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
