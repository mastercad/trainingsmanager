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

-- Exportiere Struktur von Tabelle rundumfit.devices
DROP TABLE IF EXISTS `test_devices`;
CREATE TABLE IF NOT EXISTS `test_devices` (
  `device_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `device_name` varchar(250) NOT NULL,
  `device_seo_link` varchar(250) NOT NULL,
  `device_preview_picture` varchar(250) NOT NULL,
  `device_create_date` datetime NOT NULL,
  `device_create_user_fk` int(11) NOT NULL,
  `device_update_date` datetime NOT NULL,
  `device_update_user_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`device_id`),
  UNIQUE KEY `device_name` (`device_name`),
  UNIQUE KEY `device_seo_link` (`device_seo_link`),
  KEY `geraet_eintrag_user_fk` (`device_create_user_fk`),
  KEY `geraet_aenderung_user_fk` (`device_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.devices: ~42 rows (ungefähr)
DELETE FROM `test_devices`;
/*!40000 ALTER TABLE `test_devices` DISABLE KEYS */;
INSERT INTO `test_devices` (`device_id`, `device_name`, `device_seo_link`, `device_preview_picture`, `device_create_date`, `device_create_user_fk`, `device_update_date`, `device_update_user_fk`) VALUES
	(1, 'TRX Band', 'trx-band', '', '2013-05-16 13:09:45', 24, '2017-04-22 12:31:50', 1),
	(2, 'Lower-Abdominal-Trainer 346', 'lower-abdominal-trainer-346', 'IMG_3521.jpg', '2013-05-28 13:37:45', 24, '2013-05-28 13:47:13', 24),
	(3, 'Abdominaltrainer 336', 'abdominaltrainer-336', 'IMG_3526.jpg', '2013-05-28 13:43:25', 24, '2013-05-28 13:46:30', 24),
	(4, 'Lateraltrainer 316', 'lateraltrainer-316', 'IMG_3527.jpg', '2013-05-28 13:49:56', 24, '0000-00-00 00:00:00', NULL),
	(5, 'Lumbaltrainer 306', 'lumbaltrainer-306', 'IMG_3522.jpg', '2013-05-28 13:56:27', 24, '0000-00-00 00:00:00', NULL),
	(6, 'Rotationstrainer 300', 'rotationstrainer-300', 'IMG_3533.jpg', '2013-05-28 13:59:32', 24, '0000-00-00 00:00:00', NULL),
	(7, 'Elypso 406', 'elypso-406', 'IMG_3523.jpg', '2013-05-28 14:05:57', 24, '0000-00-00 00:00:00', NULL),
	(8, 'Cervex 506', 'cervex-506', 'IMG_3525.jpg', '2013-05-28 14:08:34', 24, '0000-00-00 00:00:00', NULL),
	(9, 'Rhomboflex 506', 'rhomboflex-506', '', '2013-05-28 14:09:46', 24, '0000-00-00 00:00:00', NULL),
	(10, 'Reha/Beinstrecker-Beinbeuger 59', 'reha-beinstrecker-beinbeuger-59', 'IMG_3537.jpg', '2013-05-28 14:52:08', 24, '2013-05-28 14:53:30', 24),
	(11, 'Reha/Brust-Rücken 58', 'reha-brust-ruecken-58', 'IMG_3538.jpg', '2013-05-28 14:55:04', 24, '0000-00-00 00:00:00', NULL),
	(12, 'Reha/Beinpresse 57', 'reha-beinpresse-57', 'IMG_3539.jpg', '2013-05-28 15:01:46', 24, '0000-00-00 00:00:00', NULL),
	(13, 'Reha/Bizep-Trizep 56', 'reha-bizep-trizep-56', 'IMG_3540.jpg', '2013-05-28 15:03:20', 24, '0000-00-00 00:00:00', NULL),
	(14, 'Reha/Abduktoren-Adduktoren 55', 'reha-abduktoren-adduktoren-55', 'IMG_3545.jpg', '2013-05-28 15:06:48', 24, '0000-00-00 00:00:00', NULL),
	(15, 'Reha/Schulterpresse-Latzug 60', 'reha-schulterpresse-latzug-60', 'IMG_3546.jpg', '2013-05-28 15:13:49', 24, '2017-04-22 10:37:15', 1),
	(16, 'Reha/Bauch-Extension 50', 'reha-bauch-extension-50', 'IMG_3547.jpg', '2013-05-28 15:15:30', 24, '0000-00-00 00:00:00', NULL),
	(17, 'Reha/Nackenzug 51', 'reha-nackenzug-51', 'IMG_3552.jpg', '2013-05-28 15:17:21', 24, '0000-00-00 00:00:00', NULL),
	(18, 'Reha/Butterfly-hintere Schulter 52', 'reha-butterfly-hintere-schulter-52', 'IMG_3549.jpg', '2013-05-28 15:19:40', 24, '0000-00-00 00:00:00', NULL),
	(19, 'Reha/Kniebeuge 53', 'reha-kniebeuge-53', 'IMG_3543.jpg', '2013-05-28 15:21:31', 24, '2013-05-28 15:22:25', 24),
	(20, 'Reha/Bauchschaukel 54', 'reha-bauchschaukel-54', 'IMG_3542.jpg', '2013-05-28 15:24:06', 24, '0000-00-00 00:00:00', NULL),
	(21, 'Freihantel/KH Bizep', 'freihantel-kh-bizep', 'IMG_3566.jpg', '2013-05-28 15:32:31', 24, '0000-00-00 00:00:00', NULL),
	(22, 'Langhantel', 'langhantel', '', '2013-05-29 11:15:01', 24, '0000-00-00 00:00:00', NULL),
	(23, 'Kurzhantel', 'kurzhantel', '', '2013-05-29 11:15:26', 24, '0000-00-00 00:00:00', NULL),
	(24, 'Hackenschmitt', 'hackenschmitt', 'Screenshot_20170212_153606.png', '2013-05-29 11:17:54', 24, '2017-02-26 12:13:01', 1),
	(25, 'Wadenmaschine sitzend', 'wadenmaschine-sitzend', 'IMG_3559.jpg', '2013-05-29 11:19:15', 24, '0000-00-00 00:00:00', NULL),
	(26, 'Beinpresse 90 Grad', 'beinpresse-90-grad', '', '2013-05-29 11:23:42', 24, '0000-00-00 00:00:00', NULL),
	(27, 'Schrägbank', 'schraegbank', '', '2013-05-29 11:26:50', 24, '0000-00-00 00:00:00', NULL),
	(28, 'Flachbank', 'flachbank', '', '2013-05-29 11:27:12', 24, '0000-00-00 00:00:00', NULL),
	(29, 'Kurzhantelbank', 'kurzhantelbank', '', '2013-05-29 11:27:36', 24, '0000-00-00 00:00:00', NULL),
	(30, 'SZ Bank', 'sz-bank', '', '2013-05-29 11:28:02', 24, '2013-05-29 11:28:57', 24),
	(31, 'Bauch 32a', 'bauch-32a', '', '2013-05-29 11:34:31', 24, '0000-00-00 00:00:00', NULL),
	(32, 'Bauch 33', 'bauch-33', 'kein_bild.png', '2013-05-29 11:34:49', 24, '2017-05-03 20:33:06', 22),
	(33, 'Strechboy', 'strechboy', '', '2013-05-29 11:38:24', 24, '0000-00-00 00:00:00', NULL),
	(34, 'Relax LWS', 'relax-lws', '', '2013-05-29 11:44:52', 24, '2013-05-29 11:46:39', 24),
	(35, 'Kreisel', 'kreisel', '', '2013-05-29 11:52:29', 24, '2013-05-29 11:52:54', 24),
	(36, 'Bauch 34', 'bauch-34', '', '2013-05-29 11:53:17', 24, '0000-00-00 00:00:00', NULL),
	(37, 'Rudermaschine vorgebeugt 28', 'rudermaschine-vorgebeugt-28', '', '2013-05-29 11:53:51', 24, '2013-05-29 11:55:14', 24),
	(38, 'Theraband', 'theraband', '', '2013-05-29 11:55:36', 24, '0000-00-00 00:00:00', NULL),
	(39, 'Schulterhorn', 'schulterhorn', '', '2013-05-29 11:59:01', 24, '0000-00-00 00:00:00', NULL),
	(40, 'Flexibar', 'flexibar', '', '2013-05-29 11:59:25', 24, '0000-00-00 00:00:00', NULL),
	(41, 'Wackelkissen', 'wackelkissen', '', '2013-05-29 12:00:07', 24, '0000-00-00 00:00:00', NULL),
	(42, 'Ladyturm von 1-10kg', 'ladyturm-von-1-10kg', '', '2013-05-29 12:00:44', 24, '0000-00-00 00:00:00', NULL);
/*!40000 ALTER TABLE `test_devices` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
