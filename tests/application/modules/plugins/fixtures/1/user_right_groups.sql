-- Valentina Studio --
-- MySQL dump --
-- ---------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- ---------------------------------------------------------


-- CREATE TABLE "user_right_groups" ------------------------
-- CREATE TABLE "user_right_groups" ----------------------------
CREATE TABLE `user_right_groups` (
	`user_right_group_id` Int( 11 ) UNSIGNED AUTO_INCREMENT NOT NULL,
	`user_right_group_name` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`user_right_group_parent_fk` Int( 11 ) UNSIGNED NOT NULL,
	`user_right_group_session_timeout` Int( 11 ) NOT NULL,
	`user_right_group_flag_multilogin` TinyInt( 1 ) NOT NULL,
	`user_right_group_create_date` DateTime NOT NULL,
	`user_right_group_create_user_fk` Int( 11 ) UNSIGNED NOT NULL,
	`user_right_group_update_date` DateTime NOT NULL,
	`user_right_group_update_user_fk` Int( 11 ) UNSIGNED NOT NULL,
	PRIMARY KEY ( `user_right_group_id` ),
	CONSTRAINT `unique_user_right_group_name` UNIQUE( `user_right_group_name` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 41;
-- -------------------------------------------------------------
-- ---------------------------------------------------------


-- Dump data of "user_right_groups" ------------------------
INSERT INTO `user_right_groups`(`user_right_group_id`,`user_right_group_name`,`user_right_group_parent_fk`,`user_right_group_session_timeout`,`user_right_group_flag_multilogin`,`user_right_group_create_date`,`user_right_group_create_user_fk`,`user_right_group_update_date`,`user_right_group_update_user_fk`) VALUES ( '1', 'guest', '0', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
INSERT INTO `user_right_groups`(`user_right_group_id`,`user_right_group_name`,`user_right_group_parent_fk`,`user_right_group_session_timeout`,`user_right_group_flag_multilogin`,`user_right_group_create_date`,`user_right_group_create_user_fk`,`user_right_group_update_date`,`user_right_group_update_user_fk`) VALUES ( '20', 'user', '1', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
INSERT INTO `user_right_groups`(`user_right_group_id`,`user_right_group_name`,`user_right_group_parent_fk`,`user_right_group_session_timeout`,`user_right_group_flag_multilogin`,`user_right_group_create_date`,`user_right_group_create_user_fk`,`user_right_group_update_date`,`user_right_group_update_user_fk`) VALUES ( '21', 'test_user', '20', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
INSERT INTO `user_right_groups`(`user_right_group_id`,`user_right_group_name`,`user_right_group_parent_fk`,`user_right_group_session_timeout`,`user_right_group_flag_multilogin`,`user_right_group_create_date`,`user_right_group_create_user_fk`,`user_right_group_update_date`,`user_right_group_update_user_fk`) VALUES ( '25', 'member', '20', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
INSERT INTO `user_right_groups`(`user_right_group_id`,`user_right_group_name`,`user_right_group_parent_fk`,`user_right_group_session_timeout`,`user_right_group_flag_multilogin`,`user_right_group_create_date`,`user_right_group_create_user_fk`,`user_right_group_update_date`,`user_right_group_update_user_fk`) VALUES ( '30', 'admin', '20', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
INSERT INTO `user_right_groups`(`user_right_group_id`,`user_right_group_name`,`user_right_group_parent_fk`,`user_right_group_session_timeout`,`user_right_group_flag_multilogin`,`user_right_group_create_date`,`user_right_group_create_user_fk`,`user_right_group_update_date`,`user_right_group_update_user_fk`) VALUES ( '31', 'test_admin', '30', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
INSERT INTO `user_right_groups`(`user_right_group_id`,`user_right_group_name`,`user_right_group_parent_fk`,`user_right_group_session_timeout`,`user_right_group_flag_multilogin`,`user_right_group_create_date`,`user_right_group_create_user_fk`,`user_right_group_update_date`,`user_right_group_update_user_fk`) VALUES ( '35', 'group_admin', '30', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
INSERT INTO `user_right_groups`(`user_right_group_id`,`user_right_group_name`,`user_right_group_parent_fk`,`user_right_group_session_timeout`,`user_right_group_flag_multilogin`,`user_right_group_create_date`,`user_right_group_create_user_fk`,`user_right_group_update_date`,`user_right_group_update_user_fk`) VALUES ( '40', 'superadmin', '30', '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0' );
-- ---------------------------------------------------------


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ---------------------------------------------------------


