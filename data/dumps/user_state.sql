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

-- Exportiere Struktur von Tabelle rundumfit.user_state
DROP TABLE IF EXISTS `test_user_state`;
CREATE TABLE IF NOT EXISTS `test_user_state` (
  `user_state_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_state_name` varchar(250) NOT NULL,
  `user_state_create_date` datetime NOT NULL,
  `user_state_create_user_fk` int(11) NOT NULL,
  `user_state_update_date` datetime NOT NULL,
  `user_state_update_user_fk` int(11) NOT NULL,
  PRIMARY KEY (`user_state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.user_state: ~0 rows (ungefähr)
DELETE FROM `test_user_state`;
/*!40000 ALTER TABLE `test_user_state` DISABLE KEYS */;
INSERT INTO `test_user_state` (`user_state_id`, `user_state_name`, `user_state_create_date`, `user_state_create_user_fk`, `user_state_update_date`, `user_state_update_user_fk`) VALUES
	(1, 'offen', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(2, 'aktiv', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(3, 'geschlossen', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0);
/*!40000 ALTER TABLE `test_user_state` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
