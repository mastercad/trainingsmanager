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

-- Exportiere Struktur von Tabelle rundumfit.session
DROP TABLE IF EXISTS `test_session`;
CREATE TABLE IF NOT EXISTS `test_session` (
  `session_id` char(32) NOT NULL DEFAULT '',
  `session_update` int(11) DEFAULT NULL,
  `session_lifetime` int(11) DEFAULT NULL,
  `session_data` text,
  `session_update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.session: 3 rows
DELETE FROM `test_session`;
/*!40000 ALTER TABLE `test_session` DISABLE KEYS */;
INSERT INTO `test_session` (`session_id`, `session_update`, `session_lifetime`, `session_data`, `session_update_time`) VALUES
	('6199ee041f8f213c56272c7ce85155fd', 1494767212, 864000, 'Zend_Auth|a:2:{s:13:"accept_answer";N;s:7:"storage";O:8:"stdClass":5:{s:7:"user_id";i:0;s:21:"user_right_group_name";s:5:"guest";s:19:"user_right_group_id";i:1;s:15:"user_first_name";s:5:"guest";s:14:"user_last_name";s:0:"";}}', '2017-05-14 13:06:52'),
	('dd687483edb80359dc28a51d97caf661', 1494795009, 864000, 'Zend_Auth|a:2:{s:13:"accept_answer";b:1;s:7:"storage";O:8:"stdClass":37:{s:7:"user_id";s:2:"33";s:15:"user_first_name";s:4:"Test";s:10:"user_email";s:31:"test_admin@trainingsmanager.org";s:10:"user_login";s:31:"test_admin@trainingsmanager.org";s:13:"user_password";s:32:"01b114342d7fc811669eb24dbe609cc4";s:16:"user_facebook_id";s:0:"";s:15:"user_twitter_id";s:0:"";s:19:"user_google_plus_id";s:0:"";s:14:"user_last_name";s:5:"Admin";s:20:"user_session_timeout";s:1:"0";s:15:"user_last_login";s:19:"2017-05-14 22:49:51";s:16:"user_login_count";i:2;s:15:"user_session_id";s:32:"dd687483edb80359dc28a51d97caf661";s:19:"user_flag_logged_in";b:1;s:20:"user_flag_multilogin";s:1:"0";s:18:"user_validate_hash";s:0:"";s:13:"user_state_fk";s:1:"2";s:19:"user_right_group_fk";s:2:"31";s:16:"user_create_date";s:19:"0000-00-00 00:00:00";s:19:"user_create_user_fk";s:1:"0";s:16:"user_update_date";s:19:"2017-05-14 22:49:51";s:19:"user_update_user_fk";s:2:"33";s:13:"user_state_id";s:1:"2";s:15:"user_state_name";s:5:"aktiv";s:22:"user_state_create_date";s:19:"0000-00-00 00:00:00";s:25:"user_state_create_user_fk";s:1:"0";s:22:"user_state_update_date";s:19:"0000-00-00 00:00:00";s:25:"user_state_update_user_fk";s:1:"0";s:19:"user_right_group_id";s:2:"31";s:21:"user_right_group_name";s:10:"test_admin";s:26:"user_right_group_parent_fk";s:2:"30";s:32:"user_right_group_session_timeout";s:1:"0";s:32:"user_right_group_flag_multilogin";s:1:"0";s:28:"user_right_group_create_date";s:19:"0000-00-00 00:00:00";s:31:"user_right_group_create_user_fk";s:1:"0";s:28:"user_right_group_update_date";s:19:"0000-00-00 00:00:00";s:31:"user_right_group_update_user_fk";s:1:"0";}}Zend_Auth_Ghost|a:3:{s:11:"create_date";s:19:"2017-05-03 08:02:21";s:13:"accept_answer";b:1;s:4:"hash";s:32:"dd687483edb80359dc28a51d97caf661";}__ZF|a:1:{s:9:"Zend_Auth";a:1:{s:4:"ENVT";a:1:{s:13:"accept_answer";i:1494802409;}}}', '2017-05-14 20:50:09'),
	('0e17043d6826e82645d9a2f8e269bfde', 1494790560, 864000, 'Zend_Auth|a:2:{s:13:"accept_answer";b:1;s:7:"storage";O:8:"stdClass":37:{s:7:"user_id";s:2:"22";s:15:"user_first_name";s:7:"Andreas";s:10:"user_email";s:28:"andreas.kempe@byte-artist.de";s:10:"user_login";s:28:"andreas.kempe@byte-artist.de";s:13:"user_password";s:32:"3f498559f6c6b1b31a7213a3c1626d62";s:16:"user_facebook_id";s:0:"";s:15:"user_twitter_id";s:0:"";s:19:"user_google_plus_id";s:0:"";s:14:"user_last_name";s:5:"Kempe";s:20:"user_session_timeout";s:1:"0";s:15:"user_last_login";s:19:"2017-05-14 21:36:00";s:16:"user_login_count";i:63;s:15:"user_session_id";s:32:"0e17043d6826e82645d9a2f8e269bfde";s:19:"user_flag_logged_in";b:1;s:20:"user_flag_multilogin";s:1:"0";s:18:"user_validate_hash";s:0:"";s:13:"user_state_fk";s:1:"2";s:19:"user_right_group_fk";s:2:"40";s:16:"user_create_date";s:19:"2013-04-27 08:15:36";s:19:"user_create_user_fk";s:1:"1";s:16:"user_update_date";s:19:"2017-05-14 19:06:37";s:19:"user_update_user_fk";s:2:"22";s:13:"user_state_id";s:1:"2";s:15:"user_state_name";s:5:"aktiv";s:22:"user_state_create_date";s:19:"0000-00-00 00:00:00";s:25:"user_state_create_user_fk";s:1:"0";s:22:"user_state_update_date";s:19:"0000-00-00 00:00:00";s:25:"user_state_update_user_fk";s:1:"0";s:19:"user_right_group_id";s:2:"40";s:21:"user_right_group_name";s:10:"superadmin";s:26:"user_right_group_parent_fk";s:2:"30";s:32:"user_right_group_session_timeout";s:1:"0";s:32:"user_right_group_flag_multilogin";s:1:"0";s:28:"user_right_group_create_date";s:19:"0000-00-00 00:00:00";s:31:"user_right_group_create_user_fk";s:1:"0";s:28:"user_right_group_update_date";s:19:"0000-00-00 00:00:00";s:31:"user_right_group_update_user_fk";s:1:"0";}}Zend_Auth_Ghost|a:3:{s:11:"create_date";s:19:"2017-05-14 19:03:25";s:13:"accept_answer";b:1;s:4:"hash";s:32:"0e17043d6826e82645d9a2f8e269bfde";}__ZF|a:1:{s:9:"Zend_Auth";a:1:{s:4:"ENVT";a:1:{s:13:"accept_answer";i:1494797960;}}}', '2017-05-14 19:36:00');
/*!40000 ALTER TABLE `test_session` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
