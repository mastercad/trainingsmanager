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

-- Exportiere Struktur von Tabelle rundumfit.exercises
DROP TABLE IF EXISTS `test_exercises`;
CREATE TABLE IF NOT EXISTS `test_exercises` (
  `exercise_id` int(11) NOT NULL AUTO_INCREMENT,
  `exercise_name` varchar(250) NOT NULL,
  `exercise_seo_link` varchar(250) NOT NULL,
  `exercise_description` text NOT NULL,
  `exercise_special_features` text NOT NULL COMMENT 'Besonderheiten',
  `exercise_preview_picture` varchar(250) NOT NULL,
  `exercise_device_fk` int(11) DEFAULT NULL,
  `exercise_create_date` datetime NOT NULL,
  `exercise_create_user_fk` int(11) NOT NULL,
  `exercise_update_date` datetime NOT NULL,
  `exercise_update_user_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`exercise_id`),
  UNIQUE KEY `unique_exercise_name` (`exercise_name`),
  UNIQUE KEY `unique_exercise_seo_link` (`exercise_seo_link`),
  KEY `uebung_geraet_fk` (`exercise_device_fk`),
  KEY `uebung_eintrag_user_fk` (`exercise_create_user_fk`),
  KEY `uebung_aenderung_user_fk` (`exercise_update_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle rundumfit.exercises: ~48 rows (ungefähr)
DELETE FROM `test_exercises`;
/*!40000 ALTER TABLE `test_exercises` DISABLE KEYS */;
INSERT INTO `test_exercises` (`exercise_id`, `exercise_name`, `exercise_seo_link`, `exercise_description`, `exercise_special_features`, `exercise_preview_picture`, `exercise_device_fk`, `exercise_create_date`, `exercise_create_user_fk`, `exercise_update_date`, `exercise_update_user_fk`) VALUES
	(1, 'TRX Bizeps Curl', 'trx-bizeps-curl', 'Ausgangsposition: Schrittstellung, gestreckte Arme in Schulterhöhe, Armbeuge bis 90 Grad,bei der Bewegung Ellebogen immer in Schulterhöhe halten. Komplette Körperspannung', 'Einstellung M', 'IMG_3393.jpg', 1, '2013-05-16 13:26:02', 24, '2013-05-30 12:58:25', 24),
	(2, 'TRX Bankdrücken', 'trx-bankdruecken', 'asdsadsada', 'asdr43dfdsfg45tzurhgfb', 'IMG_3381.jpg', 1, '2013-05-29 13:21:58', 24, '2017-05-04 13:31:41', 22),
	(3, 'TRX Bankdrücken einarmig', 'trx-bankdruecken-einarmig', 'Rumpf,Brust und Schultermuskulatur werden gekräftigt, zudem können sie mit der Übung für einarmige Liegestütze trainieren', 'Nehmen sie einen weiten stand oder eine versetzte Fußpostion ein, um mehr Stabilität zu erreichen', 'IMG_3391.jpg', 1, '2013-05-29 13:33:30', 24, '2017-05-03 18:42:04', 22),
	(4, 'TRX Rudern einarmig', 'trx-rudern-einarmig', 'Rücken, Schultern, Arme und Rumpf werden gekräftigt, Rotationskräften wieder widerstanden', 'Halten Sie die Schultern quer zum Verankerungspunkt', 'IMG_3389.jpg', 1, '2013-05-29 13:41:22', 24, '0000-00-00 00:00:00', NULL),
	(5, 'TRX Bizep Curl einarmig', 'trx-bizep-curl-einarmig', 'Die Kraft von Bizep und Unterarmen wird gefördert, und die Rotationsstabilität der Rumpfmuskulatur wird beansprucht.', 'Halten sie die Ellebogen auf Schulterhöhe und zur Seite zeigend', 'IMG_3395.jpg', 1, '2013-05-29 13:56:18', 24, '2014-05-21 22:07:30', 22),
	(6, 'TRX Trizeppresse', 'trx-trizeppresse', 'der Trizeps wird gekräftigt, vollständige Körperstabilität ist erforderlich', 'die Ellebogen müssen schulterbreit sein und direkt nach vorne zeigen', '', 1, '2013-05-29 14:02:30', 24, '0000-00-00 00:00:00', NULL),
	(7, 'TRX Front Delta-Fly', 'trx-front-delta-fly', 'Achten Sie auf eine gleichbleibende Spannung des TRX, besonders im oberen Teil der Bewegung', 'Kraft und Stabilität des hinteren und mittleren Schulterbereichs wird aufgebaut', 'IMG_3437.jpg', 1, '2013-05-30 13:13:24', 24, '0000-00-00 00:00:00', NULL),
	(8, 'TRX geteilter Deltamuskelmuskel-Fly', 'trx-geteilter-deltamuskelmuskel-fly', 'Lassen sie die Rumpfmuskulatur angespannt, damit die Bewegung die Bewegung nicht von der Hüfte geführt wird', 'Schultern und Latissimus werden gekräftigt, und die Stabilisierung der Schultern wird gefördert', 'IMG_3404.jpg', 1, '2013-05-30 13:23:35', 24, '2013-05-30 13:24:09', 24),
	(9, 'TRX Bizep Curl beidarmig', 'trx-bizep-curl-beidarmig', 'Halten sie die Ellbogen auf Schulterhöhe', 'Bizeps, Unterarme und für die Haltung zuständige Muskeln werden gekräftigt.', 'IMG_3393.jpg', 1, '2013-05-31 13:51:58', 24, '2013-05-31 13:52:33', 24),
	(10, 'TRX Ausfallschritt seitlich', 'trx-ausfallschritt-seitlich', 'Nutzen sie das TRX, um die Bewegung zu entlasten und den Bewegungsumfang beim Dehnen der Innenseite der Oberschenkel zu vergrößern', 'Abduktoren und adduktoren der Hüfte werden trainiert, während Kraft für laterale Bewegungen aufgebaut wird', 'IMG_3409.jpg', 1, '2013-05-31 14:01:56', 24, '2013-05-31 14:02:03', 24),
	(11, 'TRX Ausfallschritt nach hinten', 'trx-ausfallschritt-nach-hinten', 'lassen Sie die Ferse auf dem Boden und schieben sie das Bein im TRX nach hinten', 'bei diesem Ausfallschritt werden  die Kraft der Beine einseitig sowie die Stabilisierung des Rumpfes gestärkt, das Vordere Knie sollte nie über die Fußspitze zeigen', 'IMG_3433.jpg', 1, '2013-05-31 14:15:44', 24, '0000-00-00 00:00:00', NULL),
	(12, 'TRX Einbeinig Kniebeuge', 'trx-einbeinig-kniebeuge', 'Lassen Sie die Fersen auf dem Boden, und spannen Sie Bein-und Gesäßmuskeln an.', ' diesem freihändigen Ausfallschritt werden die Kraft der Beine (Einseitig) sowie die Stabilisierung des Rumpfes gestärkt', 'IMG_3413.jpg', 1, '2013-06-04 13:39:15', 24, '0000-00-00 00:00:00', NULL),
	(13, 'TRX Hüfte/Extension', 'trx-huefte-extension', 'Führen Sie die Bewegung langsam und ohne Schwung aus, damit die Muskeln allmählich gedehnt werden', 'Die hintere Oberschenkelmuskulatur und die Hüftbeuger werden gedehnt und gekräftigt', '', 1, '2013-06-04 13:54:31', 24, '0000-00-00 00:00:00', NULL),
	(14, 'TRX Bauch Front', 'trx-bauch-front', 'Drücken sie die Fersen gerade nach unten in die Fußschlaufen, damit die Muskeln von Anfang bis Ende der Übung aktiviert bleiben, beim Einsteigen immer gesteckt unterm TRX halten und dann mit Schlaufen in die Rückenlage begeben.', 'Heben sie nun langsam in einer rollenden Bewegung den Oberkörper nach oben auf, kein Schwung und Beine immer gestreckt halten !', 'IMG_3449.jpg', 1, '2013-06-04 14:09:33', 24, '0000-00-00 00:00:00', NULL),
	(15, 'TRX Überkreuzkniebeuge', 'trx-ueberkreuzkniebeuge', 'Bitte ein Bein hinter dem Anderen kreuzen und versuchen die Hüfte und das Knie stabil lassen, Oberkörper bitte aufrecht halten', 'Po Außenseite wird trainiert, sowie der Außenoberschenkel', '', 1, '2013-06-05 13:40:05', 24, '0000-00-00 00:00:00', NULL),
	(16, 'TRX Ganzkörperrotation', 'trx-ganzkoerperrotation', 'Grundstellung, Beide Arme in Schulterhöhe ausstrecken und in der Aufwärtsbewegung nach oben eindrehend arbeiten. Körpermitte fest lassen. Nicht mit den Ellebogen nachziehen.', 'Seitenbauchmuskulatur wird trainiert, Seitliche Schulter wird gleichzeitig beansprucht.', 'IMG_3439.jpg', 1, '2013-06-05 13:47:50', 24, '0000-00-00 00:00:00', NULL),
	(17, 'TRX Trizepstrecken', 'trx-trizepstrecken', 'Ellebogen zusammendrücken und in der gestreckten Armposition beginnen, lassen sie nun ihr gesamtes Körpergewicht nach vorn fallen und beugen sie dabei die Arme ein, achten sie bei der Rückwärtsbewegung darauf das nur aus dem hinteren Arm gedrückt wird (Trizep)', 'Schulter und vor allem Trizepmuskulatur wird trainiert.', 'IMG_3397.jpg', 1, '2013-06-05 13:57:19', 24, '2013-06-05 13:57:39', 24),
	(18, 'TRX Beinheben mit Händedruck', 'trx-beinheben-mit-haendedruck', 'Senken Siedie Beine, bis sie sich ca. 30cm über dem Boden befinden', 'Fördert die Stabilität der Rumpfmuskulatur und die Hüftbewegung', 'IMG_3450.jpg', 1, '2013-06-05 14:18:57', 24, '0000-00-00 00:00:00', NULL),
	(19, 'TRX Beckenheben', 'trx-beckenheben', 'Der Kniewinkel bleibt während der gesamten Übung gleich.', 'Bei dieser Rumpfübung wird die hintere Oberschenkelmuskultur trainiert, und Gesäßmuskeln und Extensoren im Rücken werden aktiviert.', 'IMG_3447.jpg', 1, '2013-06-05 14:23:33', 24, '0000-00-00 00:00:00', NULL),
	(20, 'TRX Laufbewegung in Rückenlage mit Widerstand', 'trx-laufbewegung-in-rueckenlage-mit-widerstand', 'Drücken sie die Fersen gleichmäßig in die Fußschlaufen und simulieren sie ein Gehen in der Luft, bei dem die Zielmuskeln dieser Übung voll kontrahiert werden', 'Die hintere Oberschenkelmuskulatur, Hüfte, Gesäßmuskeln, Extensoren im Rücken und der Rumpfbereich werden gestärkt', 'IMG_3446.jpg', 1, '2013-06-05 14:37:12', 24, '0000-00-00 00:00:00', NULL),
	(21, 'Dr. Wolff Beinheben ', 'dr-wolff-beinheben', 'legen sie sich mit dem Gesäß in das untere Polster und rollen sie nun ganz langsam ihr Becken auf, achten sie darauf das sich der Oberkörper dabei nicht bewegt', 'die untere Bauchmuskulatur wird gestärkt, und durch das anathomische Polster wird der Rücken entlastet.', 'IMG_3521.jpg', 2, '2013-06-05 14:45:52', 24, '2014-05-15 22:44:31', 1),
	(22, 'Dr.Wolff Crunch front', 'dr-wolff-crunch-front', 'legen sie die Füße auf der Stütze ab und ziehen sie langsam den Oberkörper nach oben. Halten sie das Kinn zum Brustbein gezogen, oder nehmen sie ein Handtuch als Kopfstütze', 'Die Obere Bauchmuskulatur wird trainiert', 'IMG_3526.jpg', 3, '2013-06-05 14:50:09', 24, '0000-00-00 00:00:00', NULL),
	(23, 'Dr. Wolff seitlicher Crunch', 'dr-wolff-seitlicher-crunch', 'beugen sie langsam den Körper in Seitenlage nach oben ein, achten sie auf komplette Körperspannung und das die Hüften beim Einbeugen fest bleibt', 'Laterale Bauchmuskulatur wird trainiert', 'IMG_3527.jpg', 4, '2013-06-05 14:56:03', 24, '0000-00-00 00:00:00', NULL),
	(24, 'Dr. Wolff Hyperextension', 'dr-wolff-hyperextension', 'senken sie den Oberkörper 90 Grad zum Boden ab, Stirn sollte parallel zum Boden gehalten werden. Rumpf bitte angespannt halten', 'LWS wird trainiert, aber gleichzeitig wir die hintere Beinmuskulatur aktiviert', 'IMG_3522.jpg', 5, '2013-06-05 15:02:58', 24, '2013-06-05 15:03:56', 24),
	(25, 'Dr. Wolff LWS Rotieren', 'dr-wolff-lws-rotieren', 'Stellen sie die Beine in die Vorrichtung, leicht gebeugter Beinstand, Oberkörper aufrecht. Fixieren sie die Hüfte und den Bauch. Bewegen sie in Anspannung den gesamten Körper in einer leichten drehenden Bewegung so das in der Wirbelsäulengegend eine Spannung zu spüren ist.', 'Seitliche Bauch und Rückenmuskulatur wird gestärkt. Gleichzeitig wird die Beweglichkeit der Wirbelsäule geschult ', 'IMG_3533.jpg', 6, '2013-06-05 15:15:56', 24, '0000-00-00 00:00:00', NULL),
	(26, 'Dr. Wolff Po-Beinpressen', 'dr-wolff-po-beinpressen', 'Schieben sie ein Bein oder beide Beine aus der korrekten Polsterstellung nach hinten. halten sie in der Gesäßmuskulatur ständig Spannung.', 'Gesäß und Beinmuskulatur werden trainiert', 'IMG_3523.jpg', 7, '2013-06-05 15:22:19', 24, '2013-06-05 15:23:47', 24),
	(27, 'Dr. Wolff Extension HWS', 'dr-wolff-extension-hws', 'Stellen sie sich das Sitzpolster so ein, das die Kopfmitte am Kopfpolster abschließt, bewegen sie in einer geraden Bewegung den Kopf vor und zurück.', 'Die Halswirbelsäule wird gekräftigt ', 'IMG_3525.jpg', 8, '2013-06-05 15:41:34', 24, '0000-00-00 00:00:00', NULL),
	(28, 'Dr. Wolff Rudern sitzend', 'dr-wolff-rudern-sitzend', 'Nimm Sitzposition ein und drücke deinen kompletten Oberkörper gegen die Lehne, nimm bewege jetzt die Arme in einer Druck-Ruderbewegung nach hinten. Spanne dabei deine Schulterblätter kräftig an.', 'oberer Rückenmuskel und Schulterbereich wird trainiert', 'IMG_3520.jpg', 9, '2013-06-05 15:46:19', 24, '0000-00-00 00:00:00', NULL),
	(29, 'Bauchpressen ', 'bauchpressen', 'Stellen sie sich das Knieraster so ein das sie eine 90 Grad Winkel erreichen. Schulter bei der Bewegung stets ablegen. Ziehen sie nun den Oberkörper langsam Richtung Knie und spannen sie dabei den Bauch an.', 'oberer Bauch und mittlere Bauch werden trainiert.', '', 31, '2013-06-05 16:07:18', 24, '2014-05-16 16:22:25', 1),
	(30, 'Dr. Wolff Kreiselstand', 'dr-wolff-kreiselstand', 'Es gibt 2 verschiedene Kreisel, einen leichten und einen schweren Kreisel. Je nach Trainingsstand bitte verwenden. Immer zuerst mit einem Fuß auftreten, zur sicherheit am Gerät 33 festhalten. ', 'Tiefenmuskulatur des Rumpfes wird trainiert, Gleichgewichtssinn geschult und Dissballancen ausgeglichen', 'IMG_3536.jpg', 35, '2013-06-06 14:33:00', 24, '2013-06-06 14:33:08', 24),
	(31, 'Dr. Wolff Aussenrotation', 'dr-wolff-aussenrotation', 'Das Schulterhorn mit dem Bogen nach hinten auf den Nacken legen. Drücken sie die Ellebogen fest an den oberen Balken und bewegen sie die Unterarme in einer gleichbleibenden Bewegung nach oben. Ausgangsposition sollte eine Parallele der Unterarme zum Boden ergeben. Arbeiten sie bitte ohne Schwung', 'hintere Schultere (Aussenrotatoren werden trainiert) der gesamte Schulterbereich wird aktiviert.', '', 39, '2013-06-06 14:40:09', 24, '0000-00-00 00:00:00', NULL),
	(32, 'Reha Beinstrecken 59', 'reha-beinstrecken-59', 'Füße unter die Rolle und eine gleichmäßige Auf und Abwärtsbewegung der Beine. Je schneller die Bewegung ausgeführt umso höher wird das Widerstandsgefühl', 'Beinstrecker und Beinbeuger werden trainiert', 'IMG_3537.jpg', 10, '2013-06-10 11:11:42', 24, '0000-00-00 00:00:00', NULL),
	(33, 'Reha Bankdrücken / Rückenziehen 58', 'reha-bankdruecken-rueckenziehen-58', 'In der Sitzposition wird eine Druck und Zugbewegung ausgeführt, achte darauf das dein Rücken stets am Polster bleiben. Ellebogen sollten immer in Schulterhöhe ausgerichtet werden.', 'Obere Brust und der obere Rücken wird trainiert, dabei wird die Schultermuskulatur sehr stark aktiviert.', 'IMG_3538.jpg', 11, '2013-06-10 11:24:16', 24, '0000-00-00 00:00:00', NULL),
	(34, 'Reha Beinpressen 57', 'reha-beinpressen-57', 'Stemme dich mit deiner Oberschenkelkraft nach oben, achte darauf das deine Fersen stets auf dem Fußraster bleiben. Auch der Rücken sollte ständig an der Lehne bleiben, Knie und Füße bleiben bei der Bewegung parallel.', 'Oberschenkelvorderseite wird trainiert', 'ddb0dba76a5e10af7214dd206a97220f.jpg', 12, '2013-06-10 11:29:39', 24, '2017-05-14 21:34:44', 22),
	(35, 'Reha Armbeugen/Armstrecken 56', 'reha-armbeugen-armstrecken-56', 'Lege deine Arme so auf, das die Achselhöhlen genau am Polster sind. Beuge jetzt die Arme bis 90 Grad nach oben ein, und gehe in einem gleichmäßigen Tempo in die Streckung, dabei beachte bitte das der gesamte Arm an der Lehne bleibt. Je schneller umso intensiver !', 'Bizep und Trizep wird trainiert', 'IMG_3540.jpg', 13, '2013-06-10 11:43:15', 24, '0000-00-00 00:00:00', NULL),
	(36, 'Reha Bauchziehen 54', 'reha-bauchziehen-54', 'Knieposition einnehmen und Unterarme aufs Polster legen. Ziehe beide Knie in einer gleichmäßigen Bewegung nach oben, ohne das sich der Oberkörper bewegt. Ohne Schwung arbeiten', 'die Untere Bauchmuskulatur wird trainiert', 'IMG_3542.jpg', 20, '2013-06-10 12:09:39', 24, '0000-00-00 00:00:00', NULL),
	(37, 'Reha Kniebeugen 53', 'reha-kniebeugen-53', 'Stelle dich genau unter das Schulterpolster, Fußspitzen sollen genau unter den Schulter stehen und beuge die Beine bis 90 Grad ein. Schau immer gerade nach vorn bei der Beuge.', 'Oberschenkelmuskulatur und Gesäßmuskulatur werden trainiert. ', 'IMG_3544.jpg', 19, '2013-06-10 12:29:39', 24, '2013-06-10 12:39:38', 24),
	(38, 'Reha fliegende Bewegung/ hintere Schulter', 'reha-fliegende-bewegung-hintere-schulter', 'leichte Beuge von den Ellebogen und in einer Butterfly Bewegung nach hinten gehen. Gleichmäßige Bewegung von hinten nach vorn und von vorn nach hinten. Schultern immer am Polster lassen', 'Mittlere Brust wird trainiert und die hintere Schulter', 'IMG_3549.jpg', 18, '2013-06-10 15:15:41', 24, '0000-00-00 00:00:00', NULL),
	(39, 'Reha Nackenziehen  51', 'reha-nackenziehen-51', 'Greife ganz eng im Dreieck und ziehe die Ellebogen in die höchste Position, Griff bis zum Kinn ziehen. Fester Fußstand bei der Bewegung, keinen Schwung holen.', 'Der gesamte Nackenbereich wird trainiert und die gesamte Schulter kommt zum Einsatz', 'IMG_3551.jpg', 17, '2013-06-10 15:30:53', 24, '2013-06-10 15:36:31', 24),
	(40, 'Reha Rückenstrecken/Bauchcrunch 50', 'reha-rueckenstrecken-bauchcrunch-50', 'Schulterblätter fest am Polster lassen, bewegen deinen Oberkörper langsam nach vorn unten hinten, achte dabei darauf das, das Gerät ständig in Bewegung bleibt, ohne Absetzen halt. Keine Beweung aus dem Beinbereich.', 'Bauch und Rückenstreckermuskulatur werden trainiert.', 'IMG_3547.jpg', 16, '2013-06-10 15:43:54', 24, '0000-00-00 00:00:00', NULL),
	(41, 'Reha Schulterdrücken/Nackenzug 60', 'reha-schulterdruecken-nackenzug-60', 'Je nach Trainingsplan, eng oder breit greifen und für die Schulter die Aufwärtsbewegung suchen und für den Rücken die Abwärtsbewegung für den Rücken suchen. Achte auf fließenden Bewegungsblauf.', 'Schulter und Rückenmuskulatur wird trainiert', 'IMG_3546.jpg', 15, '2013-06-10 15:51:59', 24, '0000-00-00 00:00:00', NULL),
	(42, 'Reha Innen und Außenschenkel 55', 'reha-innen-und-aussenschenkel-55', 'Ziehe deine Fußspitzen zum Schienbein und gehe in die Adduktion für Innenschenkel und für die Abduktion der Außenschenkel. Achte auf gleichmäßigen Bewegungsablauf.', 'Innen und Außenschenkel werden trainiert. Die äusserliche Gesäßmuskulatur wird trainiert.', 'IMG_3545.jpg', 14, '2013-06-10 15:57:49', 24, '2013-06-10 15:57:58', 24),
	(43, 'LH Bankdrücken Flachbank', 'lh-bankdruecken-flachbank', 'Lege dich auf den Rücken stelle die Beine an, deine Augen sollten genau unter der Langhantel sein. Achte darauf das beim Rausdrücken der Hantel kein Hohlkreuz entsteht. Bei der Abwärtsbewegung einatmen und Aufwärtsbewegung ausatmen', 'Gesamte Brustmuskulatur wird trainiert, Schultermuskulatur wird aktiviert und gleichzeitig die Trizepmuskulatur', 'IMG_3554.jpg', 28, '2013-06-20 12:08:32', 24, '0000-00-00 00:00:00', NULL),
	(44, 'LH Schrägbankdrücken Langhantel', 'lh-schraegbankdruecken-langhantel', 'Lege dich mit dem Rücken auf die Schrägbank und die Stange soll immer in oberer Brusthöhe abgelegt werden. ', 'die obere Brust, vordere Schulter wird trainert', 'IMG_3555.jpg', 27, '2013-06-20 13:18:53', 24, '0000-00-00 00:00:00', NULL),
	(45, 'KH fliegende Bewegung auf der Schrägbank', 'kh-fliegende-bewegung-auf-der-schraegbank', 'stelle die Kurzhantelbank auf die schräge Position und gehe mit den Armen im 90 Grad Winkel nach unten und drücke die Arme wieder gerade nach oben', 'obere Brust und vordere Schulter wird trainiert', 'IMG_3555.jpg', 23, '2013-06-20 13:50:47', 24, '0000-00-00 00:00:00', NULL),
	(46, 'Hackenschmidtkniebeuge', 'hackenschmidtkniebeuge', 'festes Schuhwerk bitte, Füße Hüftbreit auseinander und beuge die Beine bis 90 Grad ein. Den Kopf immer geradeaus halten und hinten an die Lehne drücken ', 'Oberschenkelmuskulatur wird trainiert, bei Fußstellung außen wird der innere Beinmuskel aktiviert .', 'IMG_3557.jpg', 24, '2013-06-20 14:24:34', 24, '2013-10-17 23:37:35', NULL),
	(47, 'Rumpfbeugen', 'rumpfbeugen', 'lege dich auf den rücken und die beine auf den boden, dann hebst du deinen oberkörper an, bis er einen winkel von 15° zum boden bildet, nun hebst du deinen oberkörper weiter an, bis er 45° vom boden abgehoben ist. danach gehst du in die ausgangsposition (15°) zurück.', 'der bauchmuskel bleibt die gesamte übung über angespannt', '16806744_10154887005950180_7612834202515741990_n.jpg', NULL, '2017-03-04 18:57:38', 1, '2017-03-29 21:22:32', 1),
	(49, 'Liegestütz', 'liegestuetz', 'stellen sie sich mit armen und beinen auf den boden, die arme sind gestreckt, die beine ebenfalls. die beine können je nach intensität weiter auseinander stehen. je gespreizter sie sind, desto anspruchsvoller ist die übung.\n\nfür einen durchgang winkelt man die arme nun bis 90° vom körper an, beugt sie praktisch, dabei hält man den rücken gerade, den po in einer linie und die bauchmuskeln angespannt. man senkst bis 90° ab und streckt die arme dann wieder bis zur ausgangsstellung.', '', '', NULL, '2017-04-14 11:10:07', 1, '2017-04-14 11:10:39', 1);
/*!40000 ALTER TABLE `test_exercises` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
