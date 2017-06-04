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
  `user_profle_picture` VARCHAR(255) COLLATE utf8_unicode_ci NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Exportiere Daten aus Tabelle rundumfit.test_users: ~22 rows (ungefähr)
DELETE FROM `test_users`;
/*!40000 ALTER TABLE `test_users` DISABLE KEYS */;
INSERT INTO `test_users` (`user_id`, `user_first_name`, `user_email`, `user_login`, `user_password`, `user_facebook_id`, `user_twitter_id`, `user_google_plus_id`, `user_last_name`, `user_session_timeout`, `user_last_login`, `user_login_count`, `user_session_id`, `user_flag_logged_in`, `user_flag_multilogin`, `user_validate_hash`, `user_state_fk`, `user_right_group_fk`, `user_create_date`, `user_create_user_fk`, `user_update_date`, `user_update_user_fk`, `user_profle_picture`) VALUES
	(1, 'SYSTEM', '', '', '', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(2, 'Andreas', 'andreas.kempe@byte-artist.de', 'andreas.kempe@byte-artist.de', '3f498559f6c6b1b31a7213a3c1626d62', '', '', '', 'Kempe', 0, '2017-06-04 19:30:44', 109, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 40, '2013-04-27 08:15:36', 1, '2017-06-04 19:30:44', 22, ''),
	(3, 'Andreas', 'mastercad@gmx.de', 'mastercad@gmx.de', '3f498559f6c6b1b31a7213a3c1626d62', '', '', '', 'Kempe', 0, '2017-05-02 17:04:26', 3, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '2017-05-02 17:04:26', 23, ''),
	(10, 'Test', 'test_user@trainingsmanager.org', 'test_user', '9da1f8e0aecc9d868bad115129706a77', '', '', '', 'User', 0, '2017-06-02 22:57:22', 12, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 24, '0000-00-00 00:00:00', 0, '2017-06-02 22:57:22', 32, ''),
	(11, 'Test', 'test_admin@trainingsmanager.org', 'test_admin', '01b114342d7fc811669eb24dbe609cc4', '', '', '', 'Admin', 0, '2017-06-02 19:40:02', 18, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 34, '0000-00-00 00:00:00', 0, '2017-06-02 19:40:02', 33, ''),
	(12, 'Test', 'test_group_user@trainingsmanager.org', 'test_group_user', '53d634c0d1efa6784baaa99f91d5c553', '', '', '', 'Group User', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 24, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(13, 'Test', 'test_group_admin@trainingsmanager.org', 'test_group_admin', '8cb2e67f76ac4265b0275c22f4e50a14', '', '', '', 'Group Admin', 0, '2017-06-02 20:48:57', 6, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 39, '0000-00-00 00:00:00', 0, '2017-06-02 20:48:57', 35, ''),
	(14, 'Test', 'test_member', 'test_member', 'd9dbe8d47029e8b34ea1511cb1e50e1b', '', '', '', 'Member', 0, '2017-06-04 19:48:03', 7, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 29, '0000-00-00 00:00:00', 0, '2017-06-04 19:48:03', 36, ''),
	(15, 'Test', 'test_user@trainingsmanager.org', 'test_user', '9da1f8e0aecc9d868bad115129706a77', '', '', '', 'User', 0, '2017-05-14 22:49:21', 1, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(16, 'Test', 'test_admin@trainingsmanager.org', 'test_admin', '01b114342d7fc811669eb24dbe609cc4', '', '', '', 'Admin', 0, '2017-05-14 22:50:09', 2, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 31, '0000-00-00 00:00:00', 0, '2017-05-14 22:49:51', 33, NULL),
	(17, 'Test', 'test_group_user1@trainingsmanager.org', 'test_group_user1', '', '', '', '', 'Group User 1', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(18, 'Test', 'test_group_user2@trainingsmanager.org', 'test_group_user2', '', '', '', '', 'Group User 2', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(19, 'Test', 'test_group_user3@trainingsmanager.org', 'test_group_user3', '', '', '', '', 'Group User 3', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(20, 'Test', 'test_group_user4@trainingsmanager.org', 'test_group_user4', '', '', '', '', 'Group User 4', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(21, 'Test', 'test_group_user5@trainingsmanager.org', 'test_group_user5', '', '', '', '', 'Group User 5', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(22, 'Test', 'test_group_user6@trainingsmanager.org', 'test_group_user6', '', '', '', '', 'Group User 6', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(23, 'Test', 'test_group_user7@trainingsmanager.org', 'test_group_user7', '', '', '', '', 'Group User 7', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(24, 'Test', 'test_group_user8@trainingsmanager.org', 'test_group_user8', '', '', '', '', 'Group User 8', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(25, 'Test', 'test_group_user9@trainingsmanager.org', 'test_group_user9', '', '', '', '', 'Group User 9', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(26, 'Test', 'test_group_user10@trainingsmanager.org', 'test_group_user10', '', '', '', '', 'Group User 10', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 21, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(27, 'Test', 'test_group_admin1@trainingsmanager.org', 'test_group_admin1', '', '', '', '', 'Group Admin 1', 0, '0000-00-00 00:00:00', 0, '', 0, 0, '', 2, 39, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL),
	(50, 'Danny', 'danym36@googlemail.com', 'danym36@googlemail.com', '3f498559f6c6b1b31a7213a3c1626d62', '', '', '', 'Müller', 0, '2017-05-14 22:38:52', 24, 'dd687483edb80359dc28a51d97caf661', 0, 0, '', 2, 20, '0000-00-00 00:00:00', 0, '2017-05-14 22:38:52', 24, '');
/*!40000 ALTER TABLE `test_users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
