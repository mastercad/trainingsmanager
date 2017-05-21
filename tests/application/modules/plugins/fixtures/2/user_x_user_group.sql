-- Valentina Studio --
-- MySQL dump --
-- ---------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- ---------------------------------------------------------


-- CREATE TABLE "user_x_user_group" ------------------------
-- DROP TABLE "user_x_user_group" ------------------------------
DROP TABLE IF EXISTS `user_x_user_group` CASCADE;
-- -------------------------------------------------------------


-- CREATE TABLE "user_x_user_group" ----------------------------
CREATE TABLE `user_x_user_group` (
	`user_x_user_group_id` Int( 10 ) UNSIGNED AUTO_INCREMENT NOT NULL,
	`user_x_user_group_user_group_fk` Int( 10 ) UNSIGNED NOT NULL DEFAULT '0',
	`user_x_user_group_user_fk` Int( 10 ) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY ( `user_x_user_group_id` ),
	CONSTRAINT `user_x_user_group_user_group_fk_user_x_user_group_user_fk` UNIQUE( `user_x_user_group_user_group_fk`, `user_x_user_group_user_fk` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 1;
-- -------------------------------------------------------------
-- ---------------------------------------------------------

INSERT INTO `user_x_user_group`(`user_x_user_group_id`,`user_x_user_group_user_group_fk`,`user_x_user_group_user_fk`) VALUES ( '1', '1', '3' );
INSERT INTO `user_x_user_group`(`user_x_user_group_id`,`user_x_user_group_user_group_fk`,`user_x_user_group_user_fk`) VALUES ( '2', '1', '4' );


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ---------------------------------------------------------


