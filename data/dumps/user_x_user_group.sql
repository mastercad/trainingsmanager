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

-- Exportiere Struktur von Tabelle rundumfit.user_x_user_group
DROP TABLE IF EXISTS `test_user_x_user_group`;
CREATE TABLE IF NOT EXISTS `test_user_x_user_group` (
  `user_x_user_group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_x_user_group_user_group_fk` int(10) unsigned NOT NULL DEFAULT '0',
  `user_x_user_group_user_fk` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_x_user_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.user_x_user_group: ~0 rows (ungefähr)
DELETE FROM `test_user_x_user_group`;
/*!40000 ALTER TABLE `test_user_x_user_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_user_x_user_group` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
