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

-- Exportiere Struktur von Tabelle rundumfit.test_dashboard_x_widget
CREATE TABLE IF NOT EXISTS `test_dashboard_x_widget` (
  `dashboard_x_widget_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dashboard_x_widget_dashboard_fk` int(10) unsigned NOT NULL,
  `dashboard_x_widget_widget_fk` int(10) unsigned NOT NULL,
  `dashboard_x_widget_widget_type` varchar(250) NOT NULL,
  `dashboard_x_widget_order` int(10) unsigned NOT NULL,
  `dashboard_x_widget_create_date` datetime NOT NULL,
  `dashboard_x_widget_create_user_fk` int(11) NOT NULL,
  `dashboard_x_widget_update_date` datetime DEFAULT NULL,
  `dashboard_x_widget_update_user_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`dashboard_x_widget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.test_dashboard_x_widget: ~0 rows (ungefähr)
DELETE FROM `test_dashboard_x_widget`;
/*!40000 ALTER TABLE `test_dashboard_x_widget` DISABLE KEYS */;
-- INSERT INTO `test_dashboard_x_widget` (`dashboard_x_widget_id`, `dashboard_x_widget_dashboard_fk`, `dashboard_x_widget_widget_fk`, `dashboard_x_widget_widget_type`, `dashboard_x_widget_order`, `dashboard_x_widget_create_date`, `dashboard_x_widget_create_user_fk`, `dashboard_x_widget_update_date`, `dashboard_x_widget_update_user_fk`) VALUES
-- ^	(1, 1, 1, '', 0, '2017-06-04 15:31:37', 22, NULL, NULL);
/*!40000 ALTER TABLE `test_dashboard_x_widget` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
