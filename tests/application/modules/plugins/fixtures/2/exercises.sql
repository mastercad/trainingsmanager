-- Valentina Studio --
-- MySQL dump --
-- ---------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- ---------------------------------------------------------


-- CREATE TABLE "exercises" --------------------------------
-- DROP TABLE "exercises" --------------------------------------
DROP TABLE IF EXISTS `exercises` CASCADE;
-- -------------------------------------------------------------


-- CREATE TABLE "exercises" ------------------------------------
CREATE TABLE `exercises` (
	`exercise_id` Int( 11 ) AUTO_INCREMENT NOT NULL,
	`exercise_name` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`exercise_seo_link` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`exercise_description` Text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`exercise_special_features` Text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Besonderheiten',
	`exercise_preview_picture` VarChar( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`exercise_device_fk` Int( 11 ) NULL,
	`exercise_create_date` DateTime NOT NULL,
	`exercise_create_user_fk` Int( 11 ) NOT NULL,
	`exercise_update_date` DateTime NOT NULL,
	`exercise_update_user_fk` Int( 11 ) NULL,
	PRIMARY KEY ( `exercise_id` ),
	CONSTRAINT `unique_exercise_name` UNIQUE( `exercise_name` ),
	CONSTRAINT `unique_exercise_seo_link` UNIQUE( `exercise_seo_link` ) )
CHARACTER SET = utf8
COLLATE = utf8_general_ci
ENGINE = InnoDB
AUTO_INCREMENT = 47;
-- -------------------------------------------------------------
-- ---------------------------------------------------------


INSERT INTO `exercises`(`exercise_id`,`exercise_name`,`exercise_seo_link`,`exercise_description`,`exercise_special_features`,`exercise_preview_picture`,`exercise_device_fk`,`exercise_create_date`,`exercise_create_user_fk`,`exercise_update_date`,`exercise_update_user_fk`) VALUES ( '1', 'TRX Rudern einarmig', 'trx-rudern-einarmig', 'Rücken, Schultern, Arme und Rumpf werden gekräftigt, Rotationskräften wieder widerstanden', 'Halten Sie die Schultern quer zum Verankerungspunkt', 'IMG_3389.jpg', '1', '2013-05-29 13:41:22', '3', '0000-00-00 00:00:00', NULL );


-- CREATE INDEX "uebung_aenderung_user_fk" -----------------
-- CREATE INDEX "uebung_aenderung_user_fk" ---------------------
CREATE INDEX `uebung_aenderung_user_fk` USING BTREE ON `exercises`( `exercise_update_user_fk` );
-- -------------------------------------------------------------
-- ---------------------------------------------------------


-- CREATE INDEX "uebung_eintrag_user_fk" -------------------
-- CREATE INDEX "uebung_eintrag_user_fk" -----------------------
CREATE INDEX `uebung_eintrag_user_fk` USING BTREE ON `exercises`( `exercise_create_user_fk` );
-- -------------------------------------------------------------
-- ---------------------------------------------------------


-- CREATE INDEX "uebung_geraet_fk" -------------------------
-- CREATE INDEX "uebung_geraet_fk" -----------------------------
CREATE INDEX `uebung_geraet_fk` USING BTREE ON `exercises`( `exercise_device_fk` );
-- -------------------------------------------------------------
-- ---------------------------------------------------------


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ---------------------------------------------------------


