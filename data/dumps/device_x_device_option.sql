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

-- Exportiere Struktur von Tabelle rundumfit.device_x_device_option
DROP TABLE IF EXISTS `test_device_x_device_option`;
CREATE TABLE IF NOT EXISTS `test_device_x_device_option` (
  `device_x_device_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_x_device_option_device_fk` int(255) NOT NULL,
  `device_x_device_option_device_option_fk` int(255) NOT NULL,
  `device_x_device_option_device_option_value` varchar(255) NOT NULL,
  `device_x_device_option_create_date` datetime NOT NULL,
  `device_x_device_option_create_user_fk` int(10) unsigned NOT NULL,
  `device_x_device_option_update_date` datetime NOT NULL,
  `device_x_device_option_update_user_fk` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_geraet_geraet_option_id` (`device_x_device_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.device_x_device_option: ~19 rows (ungefähr)
DELETE FROM `test_device_x_device_option`;
/*!40000 ALTER TABLE `test_device_x_device_option` DISABLE KEYS */;
INSERT INTO `test_device_x_device_option` (`device_x_device_option_id`, `device_x_device_option_device_fk`, `device_x_device_option_device_option_fk`, `device_x_device_option_device_option_value`, `device_x_device_option_create_date`, `device_x_device_option_create_user_fk`, `device_x_device_option_update_date`, `device_x_device_option_update_user_fk`) VALUES
	(1, 26, 1, '1|2|3|4|5', '2017-02-25 23:37:06', 1, '2017-02-26 10:08:09', 1),
	(2, 26, 2, '1|1.5|2|2.5|3|3.5|4|4.5|5|6|7|8|9|10|15|20|25|30|40|50', '2017-02-25 23:37:12', 1, '2017-02-26 10:08:09', 1),
	(3, 26, 3, '1|2|3', '2017-02-26 10:08:09', 1, '0000-00-00 00:00:00', 0),
	(4, 43, 1, '1', '2017-02-26 12:16:24', 1, '0000-00-00 00:00:00', 0),
	(5, 43, 3, '1|2|3', '2017-02-26 12:16:25', 1, '0000-00-00 00:00:00', 0),
	(6, 12, 1, '1', '2017-02-26 12:16:24', 1, '0000-00-00 00:00:00', 0),
	(7, 3, 1, '1|2|3|4', '2017-03-12 09:26:39', 1, '0000-00-00 00:00:00', 0),
	(8, 9, 1, '1|2|3|4|5|6', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(9, 23, 2, '1.25|2.5|5|7.5|10|12.5|15|20|22.5|25|30|35|40|45|50|60|70|80', '2017-03-19 19:39:53', 22, '0000-00-00 00:00:00', 0),
	(10, 31, 2, '1|2|3|4|5|6', '2017-03-20 21:02:08', 1, '0000-00-00 00:00:00', 0),
	(11, 28, 2, '0.25|0.5|1|1.25|1.5|2|2.5|3|3.5|4|4.5|5|6|7|8|9|10|11|12|13|14|15|20|25|30|35|40|45|50', '2017-04-13 19:40:02', 1, '0000-00-00 00:00:00', 0),
	(33, 15, 1, '1|2|3', '2017-04-22 10:37:15', 1, '0000-00-00 00:00:00', 0),
	(34, 1, 2, '6', '2017-04-22 12:31:50', 1, '0000-00-00 00:00:00', 0),
	(35, 45, 1, '12', '2017-04-22 18:26:19', 1, '0000-00-00 00:00:00', 0),
	(36, 45, 3, '2', '2017-04-22 18:26:19', 1, '0000-00-00 00:00:00', 0),
	(37, 45, 2, '134', '2017-04-22 18:26:19', 1, '0000-00-00 00:00:00', 0),
	(38, 49, 2, '12', '2017-04-22 18:35:05', 1, '0000-00-00 00:00:00', 0),
	(39, 49, 3, '21', '2017-04-22 18:35:05', 1, '0000-00-00 00:00:00', 0),
	(40, 50, 2, '10', '2017-05-03 20:07:17', 22, '0000-00-00 00:00:00', 0);
/*!40000 ALTER TABLE `test_device_x_device_option` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
