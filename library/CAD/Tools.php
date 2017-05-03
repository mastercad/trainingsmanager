<?php
class CAD_Tools
{
    protected $a_params = null;
    protected $a_fehler = array();

    protected $a_csv = null;
    protected $a_ergebnisse = array();

    protected $aktueller_user_id = null;
    protected $user_rechte_gruppe_name = null;

    protected $obj_progress = null;

    protected $b_nicht_im_import_loeschen = 0;
    protected $b_progressbar = false;

    /**
     *
     * @param mixed $roh_wert
     *
     * @return float
     */
    public function konvertiereWert($roh_wert)
    {
        if (preg_match('/^([\d\.\,]+) *([^\d ]+)$/', $roh_wert, $a_treffer)) {
            switch ($a_treffer[2]) {
            case "μg":
                return $this->mikroZuMilligramm($a_treffer[1]);
                break;
            case "g":
                return $this->grammZuMilligramm($a_treffer[1]);
                break;
            default:
                return $a_treffer[1];
            }
        } else {
            return $roh_wert;
        }
    }

    public function mikroZuMilligramm($wert)
    {
        return $wert / 1000;
    }

    public function grammZuMilligramm($wert)
    {
        return $wert * 1000;
    }

    public function __construct($user_id = 0)
    {
    }

    public function getErgebnisse()
    {
        return $this->a_ergebnisse;
    }

    public function getObjProgress()
    {
        return $this->obj_progress;
    }

    public function setObjProgress($obj_progress)
    {
        $this->obj_progress = $obj_progress;

        if($this->obj_progress)
        {
            $this->setProgressbar(true);
        }
    }

    public function setProgressbar($b_progressbar)
    {
        $this->b_progressbar = $b_progressbar;
    }

    public function getProgressbar()
    {
        return $this->b_progressbar;
    }


    public function generiereIdentNummer($haendler_id = 0)
    {
        $uid = md5(uniqid(mt_rand(), true));

        return $uid;
    }

    public function generateTinyUrl(Zend_Db_Table_Abstract &$obj_db_table = null, $col_name = '')
    {
        $num_chars = 6;
        $i = 0;
        $my_keys = "123456789abcdefghijklmnopqrstuvwxyz";
        $keys_length = strlen($my_keys);
        $url  = "";

        while($i < $num_chars)
        {
            $rand_num = mt_rand(1, $keys_length - 1);
            $url .= $my_keys[$rand_num];
            $i++;
        }

        if($obj_db_table &&
            $col_name)
        {
            $row = $obj_db_table->fetchRow($col_name . " = '" . $url . "'");

            if($row)
            {
                return $this->generateTinyUrl($obj_db_table, $col_name);
            }
        }
        return $url;
    }

    public function generateRandomId($length)
    {
        $hex = md5("Don´t feed the Troll!" . uniqid("", true));

        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);

        $uid = preg_replace("#(*UTF8)[^A-Za-z0-9]#", "", $tmp);

        $length = max(4, min(128, $length));

        while (strlen($uid) < $length)
        {
            $uid .= gen_uuid(22);
        }

        return substr($uid, 0, $length);
    }

    public function generateHash()
    {
        $new_hash = MD5(time());

        return $new_hash;
    }

    public function getRemoteFileSize($url, $handle = null)
    {
        $remoteFile = $url;

        $contentLength = 'unknown';
        $status = 'unknown';

        if(!$handle)
        {
            $handle = curl_init($remoteFile);
        }

        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true); //not necessary unless the file redirects (like the PHP example we're using here)

        $data = curl_exec($handle);
        curl_close($handle);

        if ($data === false) {
            exit;
        }

        if (preg_match('/^HTTP\/1\.[01] (\d\d\d)/', $data, $matches)) {
            $status = (int)$matches[1];
        }
        if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
            $contentLength = (int)$matches[1];
        }
    }

    public function replaceUmlaute($string)
    {
        $a_search = array('Ä', 'Ü', 'Ö', 'ß', 'ä', 'ü', 'ö');
        $a_replace = array('Ae', 'Ue', 'Oe', 'ss', 'ae', 'ue', 'oe');

        $string = str_replace( $a_search, $a_replace, $string);

        return $string;
    }

    public function formatPreis($preis)
    {
        if(preg_match('/\..*\,/i', $preis))
        {
            $preis = str_replace('.', '', $preis);
            $preis = str_replace(',', '.', $preis);
        }
        else
        {
            $preis = str_replace(',', '.', $preis);
        }
        if(is_numeric($preis))
        {
            $format_preis = number_format($preis, 2, ',', '.');
        }
        else
        {
            return false;
        }
        return $format_preis;
    }

    public function formatiereWertZuPreis($wert)
    {
        /*
        echo "<br />" . $wert . "<br />";
        $wert = number_format($wert,2,'.','');
        echo $wert . "<br />";
        $wert = number_format($wert,2,',','.');
        return $wert;
        */
        // nur komma an 2. letzter stelle
        if(preg_match('/^(\d*)\,(\d{2})$/', $wert, $a_treffer))
        {
            return number_format($a_treffer[1], 0, ',', '.') . ',' . $a_treffer[2];
        }
        // 123.321,10
        else if(preg_match('/\.*\d{1,3}+\,\d{2}$/', $wert))
        {
            return $wert;
        }
        // 123.10
        else if(is_numeric($wert) &&
            preg_match('/\d*\.\d+$/', $wert))
        {
            return number_format($wert, 2, ',', '.');
        }
        else if(preg_match('/^([\,|\d]*)\.(\d{1,2})$/', $wert, $a_treffer))
// 		else if(preg_match('/^(\d{0,3}(\.\d{3})*(,\d*)?|\d{0,3}(,\d{3})*(\.\d*) ?)$/', $wert, $a_treffer))
        {
            echo "!";
            return number_format(str_replace('.', '', $a_treffer[1]), 0, ',', '.') . ',' . $a_treffer[2];
        }

        // falls jetzt noch ein string mit tausendertrennzeichen übrig bleibt
        $wert = str_replace('.', '', $wert);
        if(is_numeric($wert))
        {
            return number_format($wert, 2, ',', '.');
        }
        else
        {
            return $wert;
        }
        /*
        if(preg_match('/([0-9\.,-]+)/', $wert, $array_treffer))
        {
            // Zahl gefunden also können wir weitermachen
            $wert = $array_treffer[0];

            if(preg_match('/^[0-9.-\s]*[\,]{1}[0-9-]{0,2}$/', $wert))
            {
                // Komma als Dezimal Separator
                // Alle Punkte entfernen und anschließend das Komma in einen
                // Punkt umwandeln
                $wert = str_replace(' ', '', $wert);
                $wert = str_replace('.', '', $wert);
                $wert = str_replace(',', '.', $wert);
                return floatval($wert);
            }
            elseif(preg_match('/^[0-9,-\s]*[\.]{1}[0-9-]{0,2}$/', $wert))
            {
                // Punkt als Dezimal Separator
                // Alle Kommata entfernen
                $wert = str_replace(' ', '', $wert);
                $wert = str_replace(',', '', $wert);
                return floatval($wert);
            }
            elseif (preg_match('/^[0-9.-\s]*[\.]{1}[0-9-]{0,3}$/', $wert))
            {
                // Es gibt nur Tausender Separatoren
                // Alle Punkte enfernen
                $wert = str_replace(' ', '', $wert);
                $wert = str_replace('.', '', $wert);
                return floatval($wert);
            }
            elseif (preg_match('/^[0-9,-\s]*[\,]{1}[0-9-]{0,3}$/', $wert))
            {
                // Es gibt nur Tausender Separatoren
                // Alle Kommata enfernen
                $wert = str_replace(' ', '', $wert);
                $wert = str_replace(',', '', $wert);
                return floatval($wert);
            }
            else
            {
                return floatval($wert);
            }
        }
        else
        {
            return 0;
        }
        */
    }

    /**
     * formatiert den übergebenen preis in eine für das system
     * verarbeitbare variable, komma werden entfernt und gegen . ersetzt,
     * etc
     *
     * @param float/int $preis
     */
    public function formatierePreisZuWert($preis)
    {
        $preis = preg_replace('/\€/', '', $preis);
        $preis = trim($preis);

        // punkt an 2. letzter stelle
        if(preg_match('/([\-|\+|\d|\,]*)\.(\d{1,2})(\%*)$/', $preis, $a_treffer) ||
            preg_match('/([\-|\+|\d|\.]*)\,(\d{1,2})(\%*)$/', $preis, $a_treffer))
        {
            $preis = preg_replace('/[^\+|^\-|^\d]/', '', $a_treffer[1]) . "." . $a_treffer[2];

            return number_format($preis, 2, '.', '') . $a_treffer[3];
        }
        // wenn eine zahl und kein punkt
        else if(is_numeric($preis) &&
            !preg_match('/\./', $preis))
        {
            return number_format($preis, 2, '.', '');
        }
        // wenn eine zahl und ein punkt enthalten
        else if(is_numeric($preis) &&
            preg_match('/\./', $preis) &&
            preg_match('/\,/', $preis))
        {
            return number_format(preg_replace('/\./', '', $preis), 2, '.', '');
        }
        else
        {
            return $preis;
        }
    }

    public function formatiereDatumToMysql($datum, $b_incl_time = 0)
    {
        if(preg_match('/^(\d{2,4})\-(\d{1,2})\-(\d{1,2}) *(\d{0,2})\:*(\d{0,2})\:*(\d{0,2})$/', $datum, $a_treffer))
        {
            if(!(int)$a_treffer[1] &&
                !(int)$a_treffer[2] &&
                !(int)$a_treffer[3])
            {
                return '';
            }
            if($b_incl_time)
            {
                return date("Y-m-d H:i:s", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[3], (int)$a_treffer[1]));
            }
            else
            {
                return date("Y-m-d", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[3], (int)$a_treffer[1]));
            }
        }
        else if(preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{2,4}) *(\d{0,2})\:*(\d{0,2})\:*(\d{0,2})$/', $datum, $a_treffer))
        {
            if(!(int)$a_treffer[1] &&
                !(int)$a_treffer[2] &&
                !(int)$a_treffer[3])
            {
                return '';
            }
            if($b_incl_time)
            {
                return date("Y-m-d H:i:s", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[1], (int)$a_treffer[3]));
            }
            else
            {
                return date("Y-m-d", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[1], (int)$a_treffer[3]));
            }
        }
        else if(preg_match('/^(\d{1,2})\.(\d{2,4}) *(\d{0,2})\:*(\d{0,2})\:*(\d{0,2})$/', $datum, $a_treffer))
        {
            if(!(int)$a_treffer[1] &&
                !(int)$a_treffer[2] &&
                !(int)$a_treffer[3])
            {
                return '';
            }
            if($b_incl_time)
            {
                return date("Y-m-d H:i:s", mktime((int)$a_treffer[3], (int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[1], 1, (int)$a_treffer[2]));
            }
            else
            {
                return date("Y-m-d", mktime((int)$a_treffer[3], (int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[1], 1, (int)$a_treffer[2]));
            }
        }
        else if(preg_match('/^(\d{1,2})\/(\d{2,4}) *(\d{0,2})\:*(\d{0,2})\:*(\d{0,2})$/', $datum, $a_treffer))
        {
            if(!(int)$a_treffer[1] &&
                !(int)$a_treffer[2] &&
                !(int)$a_treffer[3])
            {
                return '';
            }
            if($b_incl_time)
            {
                return date("Y-m-d H:i:s", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[1], (int)$a_treffer[3]));
            }
            else
            {
                return date("Y-m-d", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[1], (int)$a_treffer[3]));
            }
        }
    }

    public function formatiereMysqlToDatum($datum, $b_incl_time = 0)
    {
        if(preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{2,4}) *(\d{0,2})\:*(\d{0,2})\:*(\d{0,2})$/', $datum, $a_treffer))
        {
            if(!(int)$a_treffer[1] &&
                !(int)$a_treffer[2] &&
                !(int)$a_treffer[3])
            {
                return 'keine';
            }
            if($b_incl_time)
            {
                return date("d.m.Y H:i:s", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[1], (int)$a_treffer[3]));
            }
            else
            {
                return date("d.m.Y", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[1], (int)$a_treffer[3]));
            }
        }
        else if(preg_match('/^(\d{2,4})\-(\d{1,2})\-(\d{1,2}) *(\d{0,2})\:*(\d{0,2})\:*(\d{0,2})$/', $datum, $a_treffer))
        {
            if(!(int)$a_treffer[1] &&
                !(int)$a_treffer[2] &&
                !(int)$a_treffer[3])
            {
                return 'keine';
            }
            if($b_incl_time)
            {
                return date("d.m.Y H:i:s", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[3], (int)$a_treffer[1]));
            }
            else
            {
                return date("d.m.Y", mktime((int)$a_treffer[4], (int)$a_treffer[5], (int)$a_treffer[6], (int)$a_treffer[2], (int)$a_treffer[3], (int)$a_treffer[1]));
            }
        }
    }

    public function createPassword($pw_length = 8, $use_caps = true, $use_numeric = true, $use_specials = false)
    {
        $caps = array();
        $numbers = array();
        $num_specials = 0;
        $reg_length = $pw_length;
        $pws = array();
        $chars = range(97, 122); // create a-z

        if ($use_caps)
        {
            $caps = range(65, 90); // create A-Z
        }
        if ($use_numeric)
        {
            $numbers = range(48, 57); // create 0-9
        }
        $all = array_merge($chars, $caps, $numbers);
        if ($use_specials)
        {
            $reg_length =  ceil($pw_length*0.75);
            $num_specials = $pw_length - $reg_length;
            if ($num_specials > 5) $num_specials = 5;
            $signs = range(33, 47);
            $rs_keys = array_rand($signs, $num_specials);
            foreach ($rs_keys as $rs) {
                $pws[] = chr($signs[$rs]);
            }
        }
        $rand_keys = array_rand($all, $reg_length);
        foreach ($rand_keys as $rand)
        {
            $pw[] = chr($all[$rand]);
        }
        $compl = array_merge($pw, $pws);
        shuffle($compl);
        return implode('', $compl);
    }

    /** @deprecated
     */
    public function dateGermanToMysql($date, $b_incl_uhrzeit = true)
    {
        // ist bereits mysql format?
        if(preg_match("/^[\d]{4}\-[\d]{1,2}\-[\d]{1,2}.*/", $date))
        {
            return $date;
        }

        $a_date = $this->dateToArray($date, '.', ':');
        $datum = "";

        if($b_incl_uhrzeit)
        {
            $datum = date("Y-m-d H:i:s", strtotime($a_date['jahr'] . "-" . $a_date['tag'] . "-" . $a_date['monat'] . " " . $a_date['stunden'] . ":" . $a_date['minuten'] . ":" . $a_date['sekunden']));
        }
        else
        {
            $datum = date("Y-m-d", strtotime($a_date['jahr'] . "-" . $a_date['tag'] . "-" . $a_date['monat'] . " " . $a_date['stunden'] . ":" . $a_date['minuten'] . ":" . $a_date['sekunden']));
        }
        return $datum;
    }

    /** @deprecated
     */
    public function dateMysqlToGerman($date, $b_incl_uhrzeit = true)
    {
        // ist bereits mysql format?
        if(preg_match("/^[\d]{1,2}\.[\d]{1,2}\.[\d]{2,4}.*/", $date))
        {
            return $date;
        }

        $a_date = $this->dateToArray($date);
        $datum = "";

        if($b_incl_uhrzeit)
        {
            $datum = date("d.m.Y H:i:s", strtotime($a_date['jahr'] . "-" . $a_date['tag'] . "-" . $a_date['monat'] . " " . $a_date['stunden'] . ":" . $a_date['minuten'] . ":" . $a_date['sekunden']));
        }
        else
        {
            $datum = date("d.m.Y", strtotime($a_date['jahr'] . "-" . $a_date['tag'] . "-" . $a_date['monat'] . " " . $a_date['stunden'] . ":" . $a_date['minuten'] . ":" . $a_date['sekunden']));
        }

        return $datum;
    }

    /** @TODO hier muss ich noch änderungen in einem bug bei dateToArray und einer
     * damit verbundenen endlosschleife beheben
     *
     * @deprecated
     */
    public function dateToMysql($date)
    {
        // ist bereits mysql format?
        if(preg_match("/^[\d]{4}\-[\d]{1,2}\-[\d]{1,2}.*/", $date))
        {
            return $date;
        }
        $a_date = $this->dateToArray($date, '.', ':');

        $datum = date("Y-m-d H:i:s", strtotime($a_date['jahr'] . "-" . $a_date['tag'] . "-" . $a_date['monat'] . " " . $a_date['stunden'] . ":" . $a_date['minuten'] . ":" . $a_date['sekunden']));

        return $datum;
    }

    public function dateToArray($date, $limiter_date = "-", $limiter_time = ":")
    {
        $date_format = $this->dateToMysql($date);

        $a_date = explode($limiter_date, $date_format);
        $a_time = explode($limiter_time, $a_date[2]);

        if(count($a_time))
        {
            $a_time_temp = explode(" ", $a_time[0]);
            $a_date[2] = $a_time_temp[0];
            $a_time[0] = $a_time_temp[1];
        }

        $a_date['tag'] = sprintf("%02d", $a_date[2]);
        $a_date['monat'] = sprintf("%02d", $a_date[1]);
        $a_date['jahr'] = sprintf("%04d", $a_date[0]);
        $a_date['stunden'] = sprintf("%02d", $a_time[0]);
        $a_date['minuten'] = sprintf("%02d", $a_time[1]);
        $a_date['sekunden'] = sprintf("%02d", $a_time[2]);

        return $a_date;
    }

    public function dateToTimestamp($date)
    {
        $a_date = $this->dateToArray($date);

        $timestamp = mktime($a_date['stunden'], $a_date['minuten'], $a_date['sekunden'], $a_date['monat'], $a_date['tag'], $a_date['jahr']);

        return $timestamp;
    }

    public function dateCompare($date1, $date2)
    {
        $date1_format = $date1 ? $this->dateToTimestamp($date1) : 0;
        $date2_format = $date2 ? $this->dateToTimestamp($date2) : 0;

        if($date1_format < $date2_format)
        {
            return -1;
        }
        else if($date1_format == $date2_format)
        {
            return 0;
        }
        else
        {
            return 1;
        }

    }

    /**
     * funktion, die checkt ob ein übergebenes datum zwischen start und ende
     * liegt
     *
     * @param date $suche
     * @param date $start
     * @param date $ende
     * @return boolean
     */
    public function isBetween($suche, $start = null, $ende = null)
    {
        if(!$start)
        {
            $start = "000000";
        }
        if(!$ende)
        {
            $ende = date("Ym");
        }

// 		echo "Checke ob " . $suche . " zwischen " . $start . " und " . $ende . "liegt!<br />";

        if($suche >= $start &&
            $suche <= $ende)
        {
            return true;
        }
        return false;
    }

    public function generatePasswort($i_length = 8)
    {
        $str_password = "";
        $str_possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

        $i_max_length = strlen($str_possible);

        if ($i_length > $i_max_length) {
            $i_length = $i_max_length;
        }

        $i = 0;

        while ($i < $i_length)
        {
            $char = substr($str_possible, mt_rand(0, $i_max_length-1), 1);

            if (!strstr($str_password, $char))
            {
                $str_password .= $char;
                $i++;
            }
        }
        return $str_password;
    }
}
