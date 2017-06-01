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

-- Exportiere Struktur von Tabelle rundumfit.user_rights
DROP TABLE IF EXISTS `test_user_rights`;
CREATE TABLE IF NOT EXISTS `test_user_rights` (
  `user_right_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_right_user_fk` int(11) NOT NULL,
  `user_right_module_fk` int(11) NOT NULL,
  `user_right_controller_fk` int(11) NOT NULL,
  `user_right_action` varchar(50) NOT NULL,
  `user_right_create_user_fk` int(11) NOT NULL,
  `user_right_create_date` datetime NOT NULL,
  `user_right_update_user_fk` int(11) NOT NULL,
  `user_right_update_date` datetime NOT NULL,
  PRIMARY KEY (`user_right_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.user_rights: ~0 rows (ungefähr)
DELETE FROM `test_user_rights`;
/*!40000 ALTER TABLE `test_user_rights` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_user_rights` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
