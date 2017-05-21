-- Valentina Studio --
-- MySQL dump --
-- ---------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- ---------------------------------------------------------


-- CREATE TABLE "training_plans" ---------------------------
-- DROP TABLE "training_plans" ---------------------------------
DROP TABLE IF EXISTS `training_plans` CASCADE;
-- -------------------------------------------------------------


-- CREATE TABLE "training_plans" -------------------------------
CREATE TABLE `training_plans` (
	`training_plan_id` Int( 11 ) UNSIGNED AUTO_INCREMENT NOT NULL,
	`training_plan_name` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`training_plan_training_plan_layout_fk` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`training_plan_user_fk` Int( 11 ) NOT NULL COMMENT 'user für den dieser trainingsplan gilt',
	`training_plan_parent_fk` Int( 11 ) UNSIGNED NULL COMMENT 'haupttrainingsplan (für splits)',
	`training_plan_active` TinyInt( 1 ) NOT NULL COMMENT 'trainingsplan aktiv? damit nur der aktuellste angezeigt wird im tagebuch zum training',
	`training_plan_order` TinyInt( 1 ) NOT NULL COMMENT 'ist für splitpläne gedacht um die reihenfolge zu beeinflussen',
	`training_plan_create_date` DateTime NOT NULL,
	`training_plan_create_user_fk` Int( 11 ) UNSIGNED NULL,
	`training_plan_update_date` DateTime NULL,
	`training_plan_update_user_fk` Int( 11 ) UNSIGNED NULL,
	PRIMARY KEY ( `training_plan_id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 152;
-- -------------------------------------------------------------
-- ---------------------------------------------------------

INSERT INTO `training_plans`(`training_plan_id`,`training_plan_name`,`training_plan_training_plan_layout_fk`,`training_plan_user_fk`,`training_plan_parent_fk`,`training_plan_active`,`training_plan_order`,`training_plan_create_date`,`training_plan_create_user_fk`,`training_plan_update_date`,`training_plan_update_user_fk`) VALUES ( '1', '', '2', '3', '0', '1', '1', '2017-03-15 21:24:15', '5', NULL, NULL );
INSERT INTO `training_plans`(`training_plan_id`,`training_plan_name`,`training_plan_training_plan_layout_fk`,`training_plan_user_fk`,`training_plan_parent_fk`,`training_plan_active`,`training_plan_order`,`training_plan_create_date`,`training_plan_create_user_fk`,`training_plan_update_date`,`training_plan_update_user_fk`) VALUES ( '2', 'Montag', '1', '3', '1', '1', '2', '2017-03-15 20:50:00', '5', '2017-05-13 10:53:32', '1' );
INSERT INTO `training_plans`(`training_plan_id`,`training_plan_name`,`training_plan_training_plan_layout_fk`,`training_plan_user_fk`,`training_plan_parent_fk`,`training_plan_active`,`training_plan_order`,`training_plan_create_date`,`training_plan_create_user_fk`,`training_plan_update_date`,`training_plan_update_user_fk`) VALUES ( '3', 'Mittwoch', '1', '3', '1', '1', '3', '2017-03-15 21:24:14', '5', '2017-05-13 10:53:32', '1' );
INSERT INTO `training_plans`(`training_plan_id`,`training_plan_name`,`training_plan_training_plan_layout_fk`,`training_plan_user_fk`,`training_plan_parent_fk`,`training_plan_active`,`training_plan_order`,`training_plan_create_date`,`training_plan_create_user_fk`,`training_plan_update_date`,`training_plan_update_user_fk`) VALUES ( '4', 'Freitag', '1', '3', '1', '1', '4', '2017-03-15 21:24:15', '5', '2017-05-13 10:53:32', '1' );


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ---------------------------------------------------------


