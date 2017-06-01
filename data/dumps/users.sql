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

-- Exportiere Struktur von Tabelle rundumfit.users
DROP TABLE IF EXISTS `test_users`;
CREATE TABLE IF NOT EXISTS `test_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_first_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_login` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_facebook_id` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_twitter_id` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_google_plus_id` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_last_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_session_timeout` int(11) NOT NULL,
  `user_last_login` datetime NOT NULL,
  `user_login_count` int(11) NOT NULL,
  `user_session_id` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_flag_logged_in` tinyint(1) NOT NULL,
  `user_flag_multilogin` int(11) NOT NULL,
  `user_validate_hash` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_state_fk` int(11) NOT NULL,
  `user_right_group_fk` int(11) NOT NULL,
  `user_create_date` datetime NOT NULL,
  `user_create_user_fk` int(11) NOT NULL,
  `user_update_date` datetime NOT NULL,
  `user_update_user_fk` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Exportiere Daten aus Tabelle rundumfit.users: ~11 rows (ungefähr)
DELETE FROM `test_users`;
/*!40000 ALTER TABLE `test_users` DISABLE KEYS */;
INSERT INTO `test_users` (`user_id`, `user_first_name`, `user_email`, `user_login`, `user_password`, `user_facebook_id`, `user_twitter_id`, `user_google_plus_id`, `user_last_name`, `user_session_timeout`, `user_last_login`, `user_login_count`, `user_session_id`, `user_flag_logged_in`, `user_flag_multilogin`, `user_validate_hash`, `user_state_fk`, `user_right_group_fk`, `user_create_date`, `user_create_user_fk`, `user_update_date`, `user_update_user_fk`) VALUES
	(1, 'SYSTEM', '', '', '', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(22, 'Andreas', 'andreas.kempe@byte-artist.de', 'andreas.kempe@byte-artist.de', '3f498559f6c6b1b31a7213a3c1626d62', '', '', '', 'Kempe', 0, '2017-05-14 21:39:35', 64, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 40, '2013-04-27 08:15:36', 1, '2017-05-14 21:39:35', 22),
	(23, 'Andreas', 'mastercad@gmx.de', 'mastercad@gmx.de', '3f498559f6c6b1b31a7213a3c1626d62', '', '', '', 'Kempe', 0, '2017-05-02 17:04:26', 3, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '2017-05-02 17:04:26', 23),
	(24, 'Danny', 'danym36@googlemail.com', 'danym36@googlemail.com', '3f498559f6c6b1b31a7213a3c1626d62', '', '', '', 'Müller', 0, '2017-05-14 22:38:52', 24, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '2017-05-14 22:38:52', 24),
	(25, 'Andreas', '1andreas.kempe@byte-artist.de', '1andreas.kempe@byte-artist.de', '3f498559f6c6b1b31a7213a3c1626d62', '', '', '', 'Kempe', 0, '2017-05-13 13:23:54', 1, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '2017-05-13 13:23:54', 25),
	(26, 'Master', 'mastercad1@gmx.de', 'mastercad1@gmx.de', 'xV27Rzvy', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(27, 'Master', 'mastercad2@gmx.de', 'mastercad2@gmx.de', 'pdZfXVrK', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 30, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(28, 'Master', 'mastercad3@gmx.de', 'mastercad3@gmx.de', 'C3BW4Xnq', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(29, 'Master', 'mastercad5@gmx.de', 'mastercad5@gmx.de', 'pbRzcxMt', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(30, 'Master', 'mastercad123@web.de', 'mastercad123@web.de', 'nxCNZ3h7', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(31, 'Master', 'mastercad12412@web.de', 'mastercad12412@web.de', 'LX2NgfnH', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(32, 'Test', 'test_user@trainingsmanager.org', 'test_user@trainingsmanager.org', '9da1f8e0aecc9d868bad115129706a77', '', '', '', 'User', 0, '2017-05-14 22:49:21', 1, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
	(33, 'Test', 'test_admin@trainingsmanager.org', 'test_admin@trainingsmanager.org', '01b114342d7fc811669eb24dbe609cc4', '', '', '', 'Admin', 0, '2017-05-14 22:50:09', 2, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 31, '0000-00-00 00:00:00', 0, '2017-05-14 22:49:51', 33);
/*!40000 ALTER TABLE `test_users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
