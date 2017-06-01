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

-- Exportiere Struktur von Tabelle rundumfit.user_right_groups
DROP TABLE IF EXISTS `test_user_right_groups`;
CREATE TABLE IF NOT EXISTS `test_user_right_groups` (
  `user_right_group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_right_group_name` varchar(250) NOT NULL,
  `user_right_group_parent_fk` int(11) unsigned NOT NULL,
  `user_right_group_session_timeout` int(11) NOT NULL,
  `user_right_group_flag_multilogin` tinyint(1) NOT NULL,
  `user_right_group_create_date` datetime NOT NULL,
  `user_right_group_create_user_fk` int(11) unsigned NOT NULL,
  `user_right_group_update_date` datetime NOT NULL,
  `user_right_group_update_user_fk` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_right_group_id`),
  UNIQUE KEY `unique_user_right_group_name` (`user_right_group_name`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.user_right_groups: ~4 rows (ungefähr)
DELETE FROM `test_user_right_groups`;
/*!40000 ALTER TABLE `test_user_right_groups` DISABLE KEYS */;
INSERT INTO `test_user_right_groups` (`user_right_group_id`, `user_right_group_name`, `user_right_group_parent_fk`, `user_right_group_session_timeout`, `user_right_group_flag_multilogin`, `user_right_group_create_date`, `user_right_group_create_user_fk`, `user_right_group_update_date`, `user_right_group_update_user_fk`) VALUES
	(1, 'guest', 0, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(20, 'user', 1, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(21, 'test_user', 20, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(30, 'admin', 20, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(31, 'test_admin', 30, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(40, 'superadmin', 30, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0);
/*!40000 ALTER TABLE `test_user_right_groups` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
