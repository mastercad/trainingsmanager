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
DROP TABLE IF EXISTS `training_diary_x_training_plan`;
CREATE TABLE IF NOT EXISTS `training_diary_x_training_plan` (
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.training_diary_x_training_plan: ~13 rows (ungefähr)
DELETE FROM `training_diary_x_training_plan`;
/*!40000 ALTER TABLE `training_diary_x_training_plan` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
