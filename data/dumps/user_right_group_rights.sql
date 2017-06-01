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

-- Exportiere Struktur von Tabelle rundumfit.user_right_group_rights
DROP TABLE IF EXISTS `test_user_right_group_rights`;
CREATE TABLE IF NOT EXISTS `test_user_right_group_rights` (
  `user_right_group_right_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_right_group_fk` int(11) NOT NULL,
  `user_right_group_right` varchar(250) NOT NULL,
  `user_right_group_right_create_user_fk` int(11) NOT NULL,
  `user_right_group_right_create_date` datetime NOT NULL,
  `user_right_group_right_update_user_fk` int(11) NOT NULL,
  `user_right_group_right_update_date` datetime NOT NULL,
  `user_right_group_right_validator_class` varchar(255) NOT NULL,
  PRIMARY KEY (`user_right_group_right_id`),
  UNIQUE KEY `user_rechte_gruppe_fk` (`user_right_group_fk`,`user_right_group_right`),
  UNIQUE KEY `unique_user_right_group_right` (`user_right_group_right`),
  UNIQUE KEY `user_right_group_right__id` (`user_right_group_right_id`),
  UNIQUE KEY `user_right_group_right_id` (`user_right_group_right_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.user_right_group_rights: ~97 rows (ungefähr)
DELETE FROM `test_user_right_group_rights`;
/*!40000 ALTER TABLE `test_user_right_group_rights` DISABLE KEYS */;
INSERT INTO `test_user_right_group_rights` (`user_right_group_right_id`, `user_right_group_fk`, `user_right_group_right`, `user_right_group_right_create_user_fk`, `user_right_group_right_create_date`, `user_right_group_right_update_user_fk`, `user_right_group_right_update_date`, `user_right_group_right_validator_class`) VALUES
	(1, 1, 'default:index:index', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(2, 1, 'default:exercises:index', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(3, 1, 'default:exercises:show', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(4, 1, 'auth:login:index', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(5, 1, 'default:error:error', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(6, 1, 'default:error:login-fail', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(7, 40, 'auth:admin:index', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(8, 1, 'auth:password-lost:index', 0, '2017-05-01 17:00:21', 0, '0000-00-00 00:00:00', ''),
	(9, 1, 'auth:register:index', 0, '2017-05-01 17:00:21', 0, '0000-00-00 00:00:00', ''),
	(10, 1, 'default:device-groups:index', 0, '2017-05-01 17:00:21', 0, '0000-00-00 00:00:00', ''),
	(11, 1, 'default:device-groups:show', 0, '2017-05-01 17:00:21', 0, '0000-00-00 00:00:00', ''),
	(12, 1, 'default:devices:index', 0, '2017-05-01 17:00:21', 0, '0000-00-00 00:00:00', ''),
	(13, 1, 'default:devices:show', 0, '2017-05-01 17:00:21', 0, '0000-00-00 00:00:00', ''),
	(14, 1, 'default:error:no-access', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(15, 1, 'default:kontakt:index', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(16, 1, 'default:muscle-groups:index', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(17, 1, 'default:muscle-groups:show', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(18, 1, 'default:muscles:index', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(19, 1, 'default:muscles:show', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(20, 1, 'default:options:index', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(21, 1, 'default:options:show', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(22, 1, 'default:training-diaries:index', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(23, 30, 'default:training-diaries:show', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', 'Auth_Model_Assertion_TrainingPlans'),
	(24, 1, 'default:training-plans:index', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(25, 30, 'default:training-plans:show', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', 'Auth_Model_Assertion_TrainingPlans'),
	(26, 1, 'default:xml:index', 0, '2017-05-01 17:00:22', 0, '0000-00-00 00:00:00', ''),
	(27, 1, 'default:device-options:index', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(28, 1, 'default:device-options:show', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(29, 1, 'default:exercise-options:index', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(30, 1, 'default:exercise-options:show', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(31, 30, 'default:device-groups:delete', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(32, 30, 'default:device-groups:edit', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(33, 30, 'default:device-groups:save', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(34, 30, 'default:devices:delete', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(35, 30, 'default:devices:delete-picture', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(36, 30, 'default:devices:edit', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(37, 30, 'default:devices:get-device-option-edit', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(38, 30, 'default:devices:get-device-proposals', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(39, 30, 'default:devices:get-devices-for-edit', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(40, 30, 'default:devices:get-pictures-for-edit', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(41, 30, 'default:devices:save', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(42, 30, 'default:devices:upload-picture', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(43, 30, 'default:exercises:delete', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(44, 30, 'default:exercises:delete-picture', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(45, 30, 'default:exercises:edit', 0, '2017-05-01 19:38:05', 0, '0000-00-00 00:00:00', ''),
	(46, 30, 'default:exercises:get-exercise-option-edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(47, 30, 'default:exercises:get-muscle-group-for-exercise-edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(48, 30, 'default:exercises:get-pictures-for-edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(49, 30, 'default:exercises:save', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(50, 30, 'default:exercises:upload-picture', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(51, 30, 'default:muscle-groups:delete', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(52, 30, 'default:muscle-groups:delete-muscle', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(53, 30, 'default:muscle-groups:edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(54, 30, 'default:muscle-groups:get-muscle-group-for-edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(55, 30, 'default:muscle-groups:get-muscle-group-proposals', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(56, 30, 'default:muscle-groups:get-muskelgruppe-fuer-edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(57, 30, 'default:muscle-groups:save', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(58, 30, 'default:muscles:delete', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', 'Auth_Model_Assertion_Muscles'),
	(59, 30, 'default:muscles:delete-muscle', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', 'Auth_Model_Assertion_Muscles'),
	(60, 30, 'default:muscles:edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', 'Auth_Model_Assertion_Muscles'),
	(61, 30, 'default:muscles:get-muscle-for-edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', 'Auth_Model_Assertion_Muscles'),
	(62, 30, 'default:muscles:get-muscle-proposals', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', 'Auth_Model_Assertion_Muscles'),
	(63, 30, 'default:muscles:save', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(64, 30, 'default:options:delete', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(65, 30, 'default:options:edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(66, 30, 'default:options:save', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(67, 30, 'default:training-diaries:edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(68, 20, 'default:training-diaries:save', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(69, 30, 'default:training-plans:archive', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(70, 30, 'default:training-plans:create-layout', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(71, 30, 'default:training-plans:edit', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(72, 30, 'default:training-plans:get-device-option', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(73, 30, 'default:training-plans:get-exercise', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(74, 30, 'default:training-plans:get-exercise-option', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(75, 30, 'default:training-plans:get-exercise-proposals', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(76, 30, 'default:training-plans:get-training-plan-for-split', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(77, 30, 'default:training-plans:save', 0, '2017-05-01 19:38:06', 0, '0000-00-00 00:00:00', ''),
	(78, 30, 'default:training-plans:select-layout', 0, '2017-05-01 19:38:07', 0, '0000-00-00 00:00:00', ''),
	(79, 1, 'default:qr:get-image-for-url', 0, '2017-05-01 19:38:37', 0, '0000-00-00 00:00:00', ''),
	(80, 1, 'default:xml:create-sitemap', 0, '2017-05-01 19:38:37', 0, '0000-00-00 00:00:00', ''),
	(81, 20, 'default:training-diaries:get-exercise', 0, '2017-05-01 19:39:16', 0, '0000-00-00 00:00:00', ''),
	(82, 20, 'default:training-diaries:show-exercise', 0, '2017-05-01 19:39:16', 0, '0000-00-00 00:00:00', ''),
	(83, 20, 'default:training-diaries:start', 0, '2017-05-01 19:39:16', 0, '0000-00-00 00:00:00', ''),
	(86, 1, 'auth:index:index', 0, '2017-05-01 19:40:33', 0, '0000-00-00 00:00:00', ''),
	(87, 1, 'auth:index:login', 0, '2017-05-01 19:40:33', 0, '0000-00-00 00:00:00', ''),
	(88, 1, 'auth:index:login-form', 0, '2017-05-01 19:40:33', 0, '0000-00-00 00:00:00', ''),
	(89, 1, 'auth:index:passwort-vergessen', 0, '2017-05-01 19:40:33', 0, '0000-00-00 00:00:00', ''),
	(90, 1, 'auth:index:passwort-vergessen-form', 0, '2017-05-01 19:40:33', 0, '0000-00-00 00:00:00', ''),
	(91, 1, 'auth:index:register', 0, '2017-05-01 19:40:33', 0, '0000-00-00 00:00:00', ''),
	(92, 1, 'auth:index:register-form', 0, '2017-05-01 19:40:33', 0, '0000-00-00 00:00:00', ''),
	(93, 1, 'auth:index:validate-registration', 0, '2017-05-01 19:40:33', 0, '0000-00-00 00:00:00', ''),
	(94, 1, 'default:butler:create-thumb', 0, '2017-05-01 19:40:34', 0, '0000-00-00 00:00:00', ''),
	(95, 1, 'default:butler:create-image-string', 0, '2017-05-03 09:21:55', 0, '0000-00-00 00:00:00', ''),
	(96, 1, 'auth:password-lost:reset-password', 0, '2017-05-03 11:06:12', 0, '0000-00-00 00:00:00', ''),
	(97, 1, 'auth:register:save', 0, '2017-05-03 15:19:09', 0, '0000-00-00 00:00:00', ''),
	(99, 20, 'default:training-plans:get-training-plan', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', ''),
	(100, 30, 'default:training-plans:get-training-plan-select', 0, '2017-05-07 19:03:21', 0, '0000-00-00 00:00:00', '');
/*!40000 ALTER TABLE `test_user_right_group_rights` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
