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

-- Exportiere Struktur von Tabelle rundumfit.dashboards
CREATE TABLE IF NOT EXISTS `test_dashboards` (
  `dashboard_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dashboard_name` varchar(50) NOT NULL DEFAULT '0',
  `dashboard_user_fk` int(10) unsigned NOT NULL DEFAULT '0',
  `dashboard_flag_active` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `dashboard_create_date` datetime DEFAULT NULL,
  `dashboard_create_user_fk` int(10) unsigned NOT NULL DEFAULT '0',
  `dashboard_update_date` datetime DEFAULT NULL,
  `dashboard_update_user_fk` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`dashboard_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.dashboards: ~0 rows (ungefähr)
DELETE FROM `test_dashboards`;

/*!40000 ALTER TABLE `test_dashboards` DISABLE KEYS */;
-- INSERT INTO `test_dashboards` (`dashboard_id`, `dashboard_name`, `dashboard_user_fk`, `dashboard_flag_active`, `dashboard_create_date`, `dashboard_create_user_fk`, `dashboard_update_date`, `dashboard_update_user_fk`) VALUES
--	(1, 'New Dashboard', 22, 1, '2017-06-04 15:31:37', 22, NULL, 0);
/*!40000 ALTER TABLE `test_dashboards` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
