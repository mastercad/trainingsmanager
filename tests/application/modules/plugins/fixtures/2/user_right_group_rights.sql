-- Valentina Studio --
-- MySQL dump --
-- ---------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- ---------------------------------------------------------

DROP TABLE IF EXISTS `user_right_group_rights` CASCADE;

-- CREATE TABLE "user_right_group_rights" ------------------
-- CREATE TABLE "user_right_group_rights" ----------------------
CREATE TABLE `user_right_group_rights` (
	`user_right_group_right_id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`user_right_group_fk` Int( 11 ) NOT NULL,
	`user_right_group_right` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`user_right_group_right_create_user_fk` Int( 11 ) NOT NULL,
	`user_right_group_right_create_date` DateTime NOT NULL,
	`user_right_group_right_update_user_fk` Int( 11 ) NOT NULL,
	`user_right_group_right_update_date` DateTime NOT NULL,
	`user_right_group_right_validator_class` VarChar( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY ( `user_right_group_right_id` ),
	CONSTRAINT `unique_user_right_group_right` UNIQUE( `user_right_group_right` ),
	CONSTRAINT `user_rechte_gruppe_fk` UNIQUE( `user_right_group_fk`, `user_right_group_right` ),
	CONSTRAINT `user_right_group_right_id` UNIQUE( `user_right_group_right_id` ),
	CONSTRAINT `user_right_group_right__id` UNIQUE( `user_right_group_right_id` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 191;
-- -------------------------------------------------------------
-- ---------------------------------------------------------

-- Dump data of "user_right_group_rights" ------------------
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '1', '20', 'default:training-plans:index', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_TrainingPlans' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '2', '20', 'default:training-plans:show', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_TrainingPlans' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '3', '40', 'default:training-plans:edit', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_TrainingPlans' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '4', '40', 'default:training-plans:new', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_TrainingPlans' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '5', '40', 'default:training-plans:delete', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_TrainingPlans' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '6', '20', 'default:exercises:index', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_Exercises' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '7', '20', 'default:exercises:show', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_Exercises' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '8', '40', 'default:exercises:edit', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_Exercises' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '9', '40', 'default:exercises:new', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_Exercises' );
INSERT INTO `user_right_group_rights`(`user_right_group_right_id`,`user_right_group_fk`,`user_right_group_right`,`user_right_group_right_create_user_fk`,`user_right_group_right_create_date`,`user_right_group_right_update_user_fk`,`user_right_group_right_update_date`,`user_right_group_right_validator_class`) VALUES ( '10', '40', 'default:exercises:delete', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', 'Auth_Model_Assertion_Exercises' );
-- ---------------------------------------------------------


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ---------------------------------------------------------


