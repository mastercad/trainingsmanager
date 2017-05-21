-- Valentina Studio --
-- MySQL dump --
-- ---------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- ---------------------------------------------------------


-- CREATE TABLE "users" ------------------------------------
-- DROP TABLE "users" ------------------------------------------
DROP TABLE IF EXISTS `users` CASCADE;
-- -------------------------------------------------------------


-- CREATE TABLE "users" ----------------------------------------
CREATE TABLE `users` (
	`user_id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`user_first_name` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_email` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_login` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_password` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_facebook_id` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_twitter_id` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_google_plus_id` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_last_name` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_session_timeout` Int( 11 ) NOT NULL,
	`user_last_login` DateTime NOT NULL,
	`user_login_count` Int( 11 ) NOT NULL,
	`user_session_id` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_flag_logged_in` TinyInt( 1 ) NOT NULL,
	`user_flag_multilogin` Int( 11 ) NOT NULL,
	`user_validate_hash` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_state_fk` Int( 11 ) NOT NULL,
	`user_right_group_fk` Int( 11 ) NOT NULL,
	`user_create_date` DateTime NOT NULL,
	`user_create_user_fk` Int( 11 ) NOT NULL,
	`user_update_date` DateTime NOT NULL,
	`user_update_user_fk` Int( 11 ) NOT NULL,
	PRIMARY KEY ( `user_id` ) )
CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
ENGINE = InnoDB
AUTO_INCREMENT = 34;
-- -------------------------------------------------------------
-- ---------------------------------------------------------

INSERT INTO `users`(`user_id`,`user_first_name`,`user_email`,`user_login`,`user_password`,`user_facebook_id`,`user_twitter_id`,`user_google_plus_id`,`user_last_name`,`user_session_timeout`,`user_last_login`,`user_login_count`,`user_session_id`,`user_flag_logged_in`,`user_flag_multilogin`,`user_validate_hash`,`user_state_fk`,`user_right_group_fk`,`user_create_date`,`user_create_user_fk`,`user_update_date`,`user_update_user_fk`) VALUES ( '1', 'SYSTEM', '', '', '', '', '', '', '', '0', '0000-00-00 00:00:00', '0', '', '0', '0', '', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
INSERT INTO `users`(`user_id`,`user_first_name`,`user_email`,`user_login`,`user_password`,`user_facebook_id`,`user_twitter_id`,`user_google_plus_id`,`user_last_name`,`user_session_timeout`,`user_last_login`,`user_login_count`,`user_session_id`,`user_flag_logged_in`,`user_flag_multilogin`,`user_validate_hash`,`user_state_fk`,`user_right_group_fk`,`user_create_date`,`user_create_user_fk`,`user_update_date`,`user_update_user_fk`) VALUES ( '2', 'guest', '', '', '', '', '', '', '', '0', '2017-05-18 18:27:12', '69', '', '0', '0', '', '2', '1', '2013-04-27 08:15:36', '1', '2017-05-18 18:27:12', '22' );
INSERT INTO `users`(`user_id`,`user_first_name`,`user_email`,`user_login`,`user_password`,`user_facebook_id`,`user_twitter_id`,`user_google_plus_id`,`user_last_name`,`user_session_timeout`,`user_last_login`,`user_login_count`,`user_session_id`,`user_flag_logged_in`,`user_flag_multilogin`,`user_validate_hash`,`user_state_fk`,`user_right_group_fk`,`user_create_date`,`user_create_user_fk`,`user_update_date`,`user_update_user_fk`) VALUES ( '3', 'user', '', '', '', '', '', '', '', '0', '2017-05-02 17:04:26', '3', '', '0', '0', '', '2', '10', '0000-00-00 00:00:00', '0', '2017-05-02 17:04:26', '23' );
INSERT INTO `users`(`user_id`,`user_first_name`,`user_email`,`user_login`,`user_password`,`user_facebook_id`,`user_twitter_id`,`user_google_plus_id`,`user_last_name`,`user_session_timeout`,`user_last_login`,`user_login_count`,`user_session_id`,`user_flag_logged_in`,`user_flag_multilogin`,`user_validate_hash`,`user_state_fk`,`user_right_group_fk`,`user_create_date`,`user_create_user_fk`,`user_update_date`,`user_update_user_fk`) VALUES ( '4', 'member', '', '', '', '', '', '', '', '0', '2017-05-14 22:38:52', '24', '', '0', '0', '', '2', '20', '0000-00-00 00:00:00', '0', '2017-05-14 22:38:52', '24' );
INSERT INTO `users`(`user_id`,`user_first_name`,`user_email`,`user_login`,`user_password`,`user_facebook_id`,`user_twitter_id`,`user_google_plus_id`,`user_last_name`,`user_session_timeout`,`user_last_login`,`user_login_count`,`user_session_id`,`user_flag_logged_in`,`user_flag_multilogin`,`user_validate_hash`,`user_state_fk`,`user_right_group_fk`,`user_create_date`,`user_create_user_fk`,`user_update_date`,`user_update_user_fk`) VALUES ( '5', 'admin', '', '', '', '', '', '', '', '0', '2017-05-13 13:23:54', '1', '', '0', '0', '', '2', '30', '0000-00-00 00:00:00', '0', '2017-05-13 13:23:54', '25' );
INSERT INTO `users`(`user_id`,`user_first_name`,`user_email`,`user_login`,`user_password`,`user_facebook_id`,`user_twitter_id`,`user_google_plus_id`,`user_last_name`,`user_session_timeout`,`user_last_login`,`user_login_count`,`user_session_id`,`user_flag_logged_in`,`user_flag_multilogin`,`user_validate_hash`,`user_state_fk`,`user_right_group_fk`,`user_create_date`,`user_create_user_fk`,`user_update_date`,`user_update_user_fk`) VALUES ( '6', 'superadmin', '', '', '', '', '', '', '', '0', '0000-00-00 00:00:00', '0', '', '0', '0', '', '2', '40', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ---------------------------------------------------------


