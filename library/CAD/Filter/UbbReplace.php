<?php

$sGeshiPath = APPLICATION_PATH . '/../library/geshi/geshi.php';

if (is_readable($sGeshiPath)) {
    include($sGeshiPath);
}

class CAD_Filter_UbbReplace implements Zend_Filter_Interface
{
    const REGEX = 'regex';
    const REPLACE_FUNCTION = 'replace_function';

    // enhält alle erlaubten tags, die replaced werden sollen
    protected $_aAllowdTags = array(
        '[QUOTE=]',
        '[QUOTE]',
        '[IMG]',
        '[IMG=]',
        '[EMAIL]',
        '[EMAIL=]',
        '[URL]',
        '[URL=]',
        '[URLIMG=]',
        '[PHP]',
        '[CODE=]',
        '[MITGLIED]',
        '[LINIE=]',
        '[ULISTE]',
        '[DANKE]',
        '[BLOCK]',
        '[CENTER]',
        '[LEFT]',
        '[RIGHT]',
        '[FLOAT]',
        '[MARQUEE]',
        '[NL]',
        '[VIDEO]',
        '[COLOR=]',
        '[BGCOLOR=]',
        '[SIZE=]',
        '[GLOW]',
        '[WAVE]',
        '[SHADOW=]',
        '[I]',
        '[B]',
        '[U]',
        '[S]',
        '[FONT=]',
        '[NEWS]'
    );

    // enthält das Tag und dazu den regex sowie den funktionsaufruf
    protected $_aMapTagToFunction = array(
        '[QUOTE=]' => array(
            self::REGEX => '/(\[QUOTE=)(.*?)(\])(.*?)(\[\/QUOTE\])/is',
            self::REPLACE_FUNCTION => "generateZitat"
        ),
        '[QUOTE]' => array(
            self::REGEX => '/(\[QUOTE\])(.*?)(\[\/QUOTE\])/is',
            self::REPLACE_FUNCTION => "generateZitat"
        ),
        '[IMG]' => array(
            self::REGEX => '/(\[IMG\])([^\[]+)?(\[\/IMG\])/is',
            self::REPLACE_FUNCTION => "generateImage"
        ),
        '[IMG=]' => array(
            self::REGEX => '/(\[IMG[=|:])([^\]]+)?(\])(.*?)(\[\/IMG\])/is',
            self::REPLACE_FUNCTION => "generateImage"
        ),
        '[EMAIL]' => array(
            self::REGEX => '/(\[EMAIL\])(.*?)(\[\/EMAIL\])/Uis',
            self::REPLACE_FUNCTION => "generateMail"
        ),
        '[EMAIL=]' => array(
            self::REGEX => '/(\[EMAIL=)([^\[]+)?(\])(.*?)(\[\/EMAIL\])/is',
            self::REPLACE_FUNCTION => "generateMail"
        ),
        '[URL]' => array(
            self::REGEX => '/(\[URL\])([^\[]+)(\[\/URL\])/Uis',
            self::REPLACE_FUNCTION => "generateUrl"
        ),
        '[URL=]' => array(
            self::REGEX => '/(\[URL=)([^\[]+)?(\])([^\[]+)?(\[\/URL\])/is',
            self::REPLACE_FUNCTION => "generateUrl"
        ),
        '[URLIMG=]' => array(
            self::REGEX => '/(\[URLIMG=)(.*)?(\])([^\[]+?)(\[\/URLIMG\])/is',
            self::REPLACE_FUNCTION => "generateUrlImage"
        ),
        '[PHP]' => array(
            self::REGEX => '/(\[PHP\])(.*?)(\[\/PHP\])/is',
            self::REPLACE_FUNCTION => "replacePhpCode"
        ),
        '[CODE=]' => array(
            self::REGEX => '|(\[CODE=)(.*?)(\])(.*?)(\[\/CODE\])|is',
            self::REPLACE_FUNCTION => "replaceCode"
        ),
        '[MITGLIED]' => array(
            self::REGEX => '/(\[MITGLIED\])(.*?)(\[\/MITGLIED\])/is',
            self::REPLACE_FUNCTION => "mitgliedText"
        ),
        '[LINIE=]' => array(
            self::REGEX => '/(\[LINIE=)(.*?)(\])/Uis',
            self::REPLACE_FUNCTION => 'generateLine'
        ),
        '[ULISTE]' => array(
            self::REGEX => '/(\[ULISTE\])(.*?)(\[\/ULISTE\])/is',
            self::REPLACE_FUNCTION => "replaceList"
        ),
        '[DANKE]' => array(
            self::REGEX => '/(\[DANKE\])(.*?)(\[\/DANKE\])/is',
            self::REPLACE_FUNCTION => "replaceDankeText"
        ),
        '[BLOCK]' => array(
            self::REGEX => '/(\[BLOCK\])(.*?)(\[\/BLOCK\])/is',
            self::REPLACE_FUNCTION => "replaceBlock"
        ),
        '[CENTER]' => array(
            self::REGEX => '/(\[CENTER\])(.*?)(\[\/CENTER\])/is',
            self::REPLACE_FUNCTION => "replaceCenter"
        ),
        '[LEFT]' => array(
            self::REGEX => '/(\[LEFT\])(.*?)(\[\/LEFT\])/is',
            self::REPLACE_FUNCTION => "replaceLeft"
        ),
        '[RIGHT]' => array(
            self::REGEX => '/(\[RIGHT\])(.*?)(\[\/RIGHT\])/is',
            self::REPLACE_FUNCTION => "replaceRight"
        ),
        '[FLOAT]' => array(
            self::REGEX => '/(\[FLOAT\])(.*?)(\[\/FLOAT\])/is',
            self::REPLACE_FUNCTION => "replaceFloat"
        ),
        '[MARQUEE]' => array(
            self::REGEX => '/(\[MARQUEE\])(.*?)(\[\/MARQUEE\])/is',
            self::REPLACE_FUNCTION => "replaceMarquee"
        ),
        '[NL]' => array(
            self::REGEX => '/(\[NL\])/Ui',
            self::REPLACE_FUNCTION => "replaceNewLine"
        ),
        '[VIDEO]' => array(
            self::REGEX => '/(\[VIDEO\])(.*?)(\[\/VIDEO\])/i',
            self::REPLACE_FUNCTION => 'addVideo'
        ),
        '[COLOR=]' => array(
            self::REGEX => '/(\[COLOR=)(#+[0-9a-f]{3,}|[A-Z]{3,})(\])(.*?)(\[\/COLOR\])/is',
            self::REPLACE_FUNCTION => "replaceColor"
        ),
        '[BGCOLOR=]' => array(
            self::REGEX => '/(\[BGCOLOR=)(#+[0-9a-f]{3,}|[A-Z]{3,})(\])(.*?)(\[\/BGCOLOR\])/is',
            self::REPLACE_FUNCTION => "replaceBackgroundColor"
        ),
        '[SIZE=]' => array(
            self::REGEX => '/(\[SIZE=)([0-9]{1,})(\])(.*?)(\[\/SIZE\])/is',
            self::REPLACE_FUNCTION => 'replaceSize'
        ),
        '[GLOW]' => array(
            self::REGEX => '/(\[GLOW\])(.*?)(\[\/GLOW\])/is',
            self::REPLACE_FUNCTION => 'replaceGlow'
        ),
        '[WAVE]' => array(
            self::REGEX => '/(\[WAVE\])(.*?)(\[\/WAVE])/is',
            self::REPLACE_FUNCTION => 'replaceWave'
        ),
        '[SHADOW=]' => array(
            self::REGEX => '/(\[SHADOW=)(#+[0-9a-f]{3,}|[A-Z]{3,})(\])(.*?)(\[\/SHADOW\])/is',
            self::REPLACE_FUNCTION => 'replaceShadow'
        ),
        '[I]' => array(
            self::REGEX => '/(\[I\])(.*?)(\[\/I\])/is',
            self::REPLACE_FUNCTION => "replaceItalic"
        ),
        '[B]' => array(
            self::REGEX => '/(\[B\])(.*?)(\[\/B\])/is',
            self::REPLACE_FUNCTION => "replaceBold"
        ),
        '[U]' => array(
            self::REGEX => '/(\[U\])(.*?)(\[\/U\])/is',
            self::REPLACE_FUNCTION => "replaceUnderlined"
        ),
        '[S]' => array(
            self::REGEX => '/(\[S\])(.*?)(\[\/S\])/is',
            self::REPLACE_FUNCTION => "replaceSmall"
        ),
        '[FONT=]' => array(
            self::REGEX => '/(\[FONT=)(.*?)(\])(.*?)(\[\/FONT\])/is',
            self::REPLACE_FUNCTION => 'replaceFont'
        ),
        '[NEWS]' => array(
            self::REGEX => '/(\[NEWS])/i',
            self::REPLACE_FUNCTION => "getNews"
        )
    );

    private $_bRemoveDeniedTags = false;

    private $b_br_cleart;
    private $a_bilder;
    private $str_bilder_pfad;
    private $str_temp_bilder_pfad;
    private $bWithoutLinks = FALSE;
    private static $_aReplaceSmilies;

    public function __construct($b_br_cleart = true)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors',1);
        error_reporting(E_ALL | E_STRICT);

        $this->b_br_cleart = $b_br_cleart;
    }

    public function filter($sText)
    {
        $sText = htmlentities($sText, null, 'UTF-8');

        foreach ($this->_aAllowdTags as $sAllowedTag) {
            if (array_key_exists($sAllowedTag, $this->_aMapTagToFunction)) {

                $aCurrentMap = $this->_aMapTagToFunction[$sAllowedTag];

                $sText = preg_replace_callback(
                    $aCurrentMap[self::REGEX],
                    array(&$this, $aCurrentMap[self::REPLACE_FUNCTION]),
                    $sText);
            }
        }

        if ($this->b_br_cleart) {
            $sText = preg_replace_callback( '([\n\r|\n])', array(&$this, "replaceNewLine"), $sText);
            //            $sText = nl2br($sText);
        } else {
            $sText = preg_replace_callback( '([\n\r|\n])', array(&$this, "replaceLineBreak"), $sText);
            //            $sText = nl2br($sText);
        }
        //
        $sText = preg_replace_callback("/([\t])/", array(&$this, "replaceTab"), $sText);

        //        $sText = $this->_smileyReplace($sText);

        $oCadMerge = new CAD_DOM_TagMerge();

        return $oCadMerge->merge($sText);
    }

    public function replaceTab($aMatches)
    {
        return "&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    public function replaceLineBreak($aMatches)
    {
        return '<br />';
    }

    public function replaceSmall($aMatches)
    {
        return "<s>" . $aMatches[1] . "</s>";
    }

    public function replaceNewLine($aMatches)
    {
        return '<br class="clearfix" />';
    }

    public function replaceMarquee($aMatches)
    {
        return '<marquee>' . $aMatches[2] . '</marquee>';
    }

    public function replaceFloat($aMatches)
    {
        return '<div style="float: left; display: inline;">' . $aMatches[2] . '</div>';
    }

    public function replaceRight($aMatches)
    {
        return '<div style="text-align: right;">' . $aMatches[2] . '<br style="clear: both;" /></div>';
    }

    public function replaceLeft($aMatches)
    {
        return '<div style="text-align: left;">' . $aMatches[2] . '<br style="clear: both;" /></div>';
    }

    public function replaceCenter($aMatches)
    {
        return '<div style="text-align: center;">' . $aMatches[2] . '<br style="clear: both;" /></div>';
    }

    public function replaceBlock($aMatches)
    {
        return '<div style="text-align: justify;">' . $aMatches[2] . '<br style="clear: both;" /></div>';
    }

    public function replaceDankeText($aMatches)
    {
        return $this->dankeText($aMatches[2]);
    }

    public function replaceList($aMatches)
    {
        return $this->erstelleListe($aMatches[2]);
    }

    public function replaceColor($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[COLOR=]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<span style="color: ' . $mInput[2] . ';">' . $mInput[4] . '</span>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceColor"), $mInput);
    }

    public function replaceBackgroundColor($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[BGCOLOR=]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<span style="background-color: ' . $mInput[2] . ';">' . $mInput[4] . '</span>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceBackgroundColor"), $mInput);
    }

    public function replaceBold($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[B]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<span style="font-weight: bold;">' . $mInput[2] . '</span>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceBold"), $mInput);
    }

    public function replaceItalic($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[I]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<span style="font-style: italic;">' . $mInput[2] . '</span>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceItalic"), $mInput);
    }

    public function replaceUnderlined($mInput) {
        $sRegEx = $this->_aMapTagToFunction["[U]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<span style="text-decoration: underline;">' . $mInput[2] . '</span>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceUnderlined"), $mInput);
    }

    public function replaceCode($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[CODE=]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = $this->codeString($mInput[2], $mInput[4]);
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceCode"), $mInput);
    }

    public function replacePhpCode($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[PHP]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = $this->codeString('php', $mInput[2]);
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceCode"), $mInput);
    }

    public function replaceFont($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[FONT=]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<span style="font-family: ' . $mInput[2] . ';">' . $mInput[4] . '</span>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceFont"), $mInput);
    }

    public function replaceSize($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[SIZE=]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<span style="font-size: ' . $mInput[2] . 'px;">' . $mInput[4] . '</span>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceSize"), $mInput);
    }

    public function replaceGlow($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[GLOW]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<GLOW>' . $mInput[2] . '</GLOW>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceWave"), $mInput);
    }

    public function replaceWave($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[WAVE]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<WAVE>' . $mInput[2] . '</WAVE>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceWave"), $mInput);
    }

    public function replaceShadow($mInput)
    {
        $sRegEx = $this->_aMapTagToFunction["[SHADOW=]"][self::REGEX];

        if (is_array($mInput)) {
            $mInput = '<span style="box-shadow: 5px 5px 15px ' . $mInput[2] . ';">' . $mInput[4] . '</span>';
        }
        return preg_replace_callback($sRegEx, array(&$this, "replaceShadow"), $mInput);
    }

    public function generateZitat($aMatches)
    {
        if (6 == count($aMatches)) {
            return '<div class="quote" ><div class="quote_kopf"> Zitat von : ' . $aMatches[2] .
            '</div><div class="quote_inhalt">' . $aMatches[4] . '</div></div>';
        } else {
            return '<div class="quote" ><div class="quote_kopf"> Zitat :</div><div class="quote_inhalt">' .
            $aMatches[2] . '</div></div>';
        }
    }

    public function generateMail($aMatches)
    {
        if (6 == count($aMatches)) {
            return '<a href="mailto:' . $aMatches[4] . '">' . $aMatches[2] . '</a>';
        } else {
            return '<a href="mailto:' . $aMatches[2] . '">' . $aMatches[2] . '</a>';
        }
    }

    public function generateImage($aMatches)
    {
        if (6 == count($aMatches)) {
            return $this->imageEinfuegenNeu($aMatches[4], $aMatches[2]);
        } else {
            return $this->imageEinfuegen($aMatches[2]);
        }
    }

    public function generateLine($aMatches)
    {
        return '<hr style="width: 100%; height: 2px; color: ' . $aMatches[2] . '; background: ' . $aMatches[2] .
        '; margin: 10px 0px; border: 0;" />';
    }

    public function generateUrl($aMatches)
    {
        if (6 == count($aMatches)) {
            return $this->ersetzeUrl($aMatches[4], $aMatches[2]);
        } else {
            return $this->ersetzeUrl($aMatches[2]);
        }
    }

    public function generateUrlImage($aMatches)
    {
        return $this->ersetzeUrlImage($aMatches[4], $aMatches[2]);
    }

    /**
     * @todo weitere weichen und HTML5 Code für embedded videos implementieren
     * ich brauchte jetzt erstmal nur youtube :D
     *
     * @param $aMatches
     *
     * @return string
     */
    public function addVideo($aMatches)
    {
        if (true === is_array($aMatches)
            && array_key_exists(2, $aMatches)
        ) {
            $sVideoUrl = $aMatches[2];
            if (preg_match('/^http[s]{0,1}:\/\/www\.youtube\..*/i', $sVideoUrl)) {
                return $this->addYoutubeVideo($sVideoUrl);
            } else if (preg_match('/^http[s]{0,1}:\/\/youtu\..*/i', $sVideoUrl)) {
                return $this->addYoutubeVideo($sVideoUrl);
            } else {
                return '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>' .
                '<script type="text/javascript" src="/js/jwplayer.js"></script>' .
                '<embed flashvars="file=$1&autostart=false" allowfullscreen="true" ' .
                'allowscripaccess="always" id="player1" name="player1" src="' . $sVideoUrl . '" ' .
                'width="480" height="270"/>';
            }
        };
    }

    public function addYoutubeVideo($sVideoUrl) {
        $sVideoId = $this->_parseYoutubeVideoUrl($sVideoUrl);
        return
            '<div style="text-align:center; width: 100%;">' .
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $sVideoId . '" ' .
            ' frameborder="0" allowfullscreen ></iframe></div>';
    }

    private function _parseYoutubeVideoUrl($sVideoUrl) {
        $sVideoId = $sVideoUrl;

        if (preg_match('/youtu\.be\/(.*)/i', $sVideoUrl, $aMatches)) {
            $sVideoId = $aMatches[1];
        } else if (preg_match('/youtube.*[\?|\&]v=([\-\_A-Za-z0-9]{1,})/i', $sVideoUrl, $aMatches)) {
            $sVideoId = $aMatches[1];
        } else if (preg_match('/youtube.*?v=(.*?)/i', $sVideoUrl, $aMatches)) {
            $sVideoId = $aMatches[1];
        }
        return trim($sVideoId);
    }

    public function addKnownVideoFormat($aMatches) {

    }

    public function addUnknownVideoFormat($aMatches) {

    }

    /* 
     * Funktion zum ersetzen der URLs, ist ein URL außerhalb des lokalen
     * Servers, wird das Link Tag automatisch mit einem Target _blank versehen
     */
    private function ersetzeUrl( $url, $name = '')
    {
        $link = '';
        $hostname_url = parse_url($url, PHP_URL_HOST);
        $hostname_server = "byte-artist.de";

        if(is_array($_SERVER) &&
            array_key_exists('SERVER_NAME', $_SERVER))
        {
            $hostname_server = $_SERVER['SERVER_NAME'];
        }

        if(!$name)
        {
            $name = $url;
        }

        if (FALSE === $this->getWithoutLinks()) {
            // wenn eine dateiendung
            if(preg_match('/(\.[a-z0-9]{2,5})\/?$/i', $url))
            {
                $link = '<a href="' . $url . '" target="_blank">' . $name . '</a>';
            }
            else if($hostname_url &&
                $hostname_url != $hostname_server)
            {
                $link = '<a href="' . $url . '" target="_blank">' . $name . '</a>';
            }
            else
            {
                $link = '<a href="' . $url . '">' . $name . '</a>';
            }
        } else {
            $link = $name;
        }
        return $link;
    }

    public function ersetzeUrlImage( $url, $image)
    {
        $link = '';
        $hostname_url = parse_url($url, PHP_URL_HOST);
        $hostname_server = "byte-artist.de";

        if(is_array($_SERVER) &&
            array_key_exists('SERVER_NAME', $_SERVER))
        {
            $hostname_server = $_SERVER['SERVER_NAME'];
        }

        if (FALSE === $this->getWithoutLinks()) {
            // wenn eine dateiendung
            if(preg_match('/(\.[a-z0-9]{2,5})\/?$/i', $url))
            {
                $link = '<a href="' . $url . '" target="_blank">' . $this->imageEinfuegen($image) . '</a>';
            }
            else if($hostname_url &&
                $hostname_url != $hostname_server)
            {
                $link = '<a href="' . $url . '" target="_blank">' . $this->imageEinfuegen($image) . '</a>';
            }
            else
            {
                $link = '<a href="' . $url . '" target="_blank">' . $this->imageEinfuegen($image) . '</a>';
            }
        } else {
            $link = $this->imageEinfuegen($image);
        }
        return $link;
    }

    // function, die checkt, ob das user ein mitglied des forums ist, wenn ja wird text angezeigt, wenn nein, der register link
    private function mitgliedText($aMatches)
    {
        $text = $aMatches[2];
        $iForumId = isset($_GET['forumid']) ? $_GET['forumid'] : '';
        $iSubForumId = isset($_GET['subforumid']) ? $_GET['subforumid'] : '';
        $iThreadId = isset($_GET['threadid']) ? $_GET['threadid'] : '';
        $iAktuelleSeite = isset($_GET['aktuelle_seite']) ? $_GET['aktuelle_seite'] : '';
        $iAnzahlPosts = isset($_GET['anzahl_posts']) ? $_GET['anzahl_posts'] :'';

        if( isset($_SESSION['mitglieder_id']))
        {
            return $text;
        }
        else
        {
            return '<span style="color: red;">Bitte einloggen, oder <a style="color: red;" href="?seite=registrieren&amp;forumid=' . $iForumId . '&amp;subforumid=' . $iSubForumId . '&amp;threadid=' . $iThreadId . '&amp;aktuelle_seite=' . $iAktuelleSeite . '&amp;anzahl_posts=' . $iAnzahlPosts . '" title="Nicht die benötigten Rechte um diesen Text zu sehen" >registrieren</a>, damit der Text angezeigt wird !</span>';
        }
    }

    public function codeString($sLanguage, $sSource)
    {
        /*
    	$code_container = '<code class="blog-code lang-' . $sLanguage . '" ><pre>' . $sSource . '</pre></code>';

    	return $code_container;
         *
         */

        $sLanguage = strtoupper($sLanguage);

        $sSource = stripslashes($sSource);
        $sSource = preg_replace('/^\n/', '', $sSource);
        $sSource = preg_replace('/\n$/', '', $sSource);

        $header_content = '<div class="code_header" style="position: relative; padding: 2px 5px; font-weight: bold; background-color: #CCCCCC; color: #333333;">';
        $header_content .= '<span class="highlight_minimize">+</span>';

        if(strlen(trim($sLanguage)) > 0)
        {
            $header_content .= '<h3 style="position: absolute; top: -10px; left: 15px; padding: 2px 5px; background-color: #FFFFFF; border: 1px solid #CCCCCC;">'. $sLanguage . ' code</h3>';
        }

        //        $header_content .= '<img src="#" alt="copy to clipboard" style="position: absolute; top: 2px; right: 5px;" />';
        $header_content .= '</div>';
        $footer_content = '<div class="code_footer" style="height: 10px; background-color: #CCCCCC; color: #333333;"></div>';

        if (true === class_exists("GeSHi")) {
            $oGeshi = new GeSHi($sSource, $sLanguage);

            $oGeshi->enable_classes(true);
            $oGeshi->set_overall_class('highlight_code');
            //        $oGeshi->set_header_type(GESHI_HEADER_DIV);
            $oGeshi->set_header_type(GESHI_HEADER_PRE);
            /*
             *
                GESHI_NORMAL_LINE_NUMBERS - Use normal line numbering
                GESHI_FANCY_LINE_NUMBERS - Use fancy line numbering
                GESHI_NO_LINE_NUMBERS - Disable line numbers (default)
             */
            $oGeshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);

            //        $oGeshi->start_line_numbers_at($number);
            //        $oGeshi->highlight_lines_extra(array(8));

            $oGeshi->set_header_content($header_content);
            $oGeshi->set_footer_content($footer_content);
            $replaced_source = $sSource;

            if(strlen(trim($sLanguage)) > 0)
            {
                $oGeshi->set_language($sLanguage);

                if(false !== $oGeshi->get_language_name()) {
                    if ("PHP" == $sLanguage
                    ) {
                        $oGeshi->set_url_for_keyword_group(3, 'http://www.php.net/{FNAME}');
                    }
                }
            }

            if (TRUE === $this->getWithoutLinks()) {
                $oGeshi->enable_keyword_links(FALSE);
            }

            $sSource = $oGeshi->parse_code();
        }

        // eventuell im text enthaltene [ oder ] escapen
        $sSource = preg_replace('/\[/', '&#91;', $sSource);
        $sSource = preg_replace('/\]/', '&#93;', $sSource);

        return $sSource;
    }

    private function charEinfuegen( $text)
    {
        if( preg_match( '/^\#[0-9]{3,4}$/', $text))
        {
            return "&" . $text . ";";
        }
        else if( $text > 32)
        {
            return chr( $text);
        }
    }

    private function dankeThread( $text)
    {
        if( ereg( '\[DANKE](.*)\[\/DANKE\]', $text))
        {
            return true;
        }
        return false;
    }

    function erstelleListe( $text)
    {
        $text = stripslashes($text);

        if( preg_match( '/\{ULISTE\}(.*)\{\/ULISTE\}/Usi', $text))
        {
            preg_replace( '/\{ULISTE\}(.*)\{\/ULISTE\}/Usei', '$self->erstelleListe( $1)', $text);
        }

        $a_listen_punkte = preg_split( '/\n|\r|\<br \/>/Ui', $text);
        $liste = '<ul style="margin-left: 10px; float: left; display: inline;">';

        foreach( $a_listen_punkte as $listen_punkt)
        {
            if( strlen( trim($listen_punkt)) > 0 &&
                !preg_match( '/\<ul/i', $listen_punkt))
            {
                $liste .= '<li style="list-style: disc inside none; margin-left: 10px;">' . $listen_punkt . '</li>';
            }
            else if( strlen(trim($listen_punkt)) > 0
                && (!preg_match( '/\<ul/i', $listen_punkt) ||
                    !preg_match( '/\<\/ul\>/i', $listen_punkt))
            ) {
                $liste .= $listen_punkt;
            }
        }

        $liste .= '</ul>';

        return $liste;
    }

    private function imageAnhaengen( $text, $name = "")
    {
        $pfad = '';
        $bild_array = array();

        // suchen, ob von extern geöffnet werden soll
        if( ereg( 'http://|http:\\|www.', $text))
        {
            $bild_array = @getimagesize( $text);
        }
        //ansonsten aus lokalem ordner öffnen
        else
        {
            if( file_exists( getcwd() . $this->getBilderPfad() . $text ))
            {
                $pfad = $this->getBilderPfad();
            }
            if( file_exists( getcwd() . $this->getTempBilderPfad() . $text ))
            {
                $pfad = $this->getTempBilderPfad();
            }
            $bild_array = getimagesize( $pfad . $text);
        }

        if( $bild_array)
        {
            $anhang = '<div style="width: 140px; background: #FFF; border: 1px solid #666666; margin-top: 15px;">';

            if( !$name)
            {
                $name = basename( $text);
            }

            $name_array = chunk_split( $name, 20, "<br />");
            $anhang .= '<div style="display: block; padding: 5px;">' . $name_array . '</div>';
            if (FALSE === $this->getWithoutLinks()) {
                $anhang .= '<a href="/' . $pfad . $text . '" title="' . $text . '" target="_blank" >';
            }
            $anhang .= '<img src="/butler/create-thumb/file/' . $pfad . $text . '" alt="Bild ' . $text . ' nicht gefunden !" title="' . $text . '" />';
            if (FALSE === $this->getWithoutLinks()) {
                $anhang .= '</a>';
            }
            $anhang .= '<div style="width: 140px; height: 20px; background: #FFF; text-align: center;">' . $bild_array[0] . ' x ' . $bild_array[1] . '</div>';
            $anhang .= '</div>';

            $_SESSION['post_bilder'][] = $text;

            return $anhang;
        }
        return $text;
    }

    private function imageEinfuegen( $bild, $name = null)
    {
        if( preg_match( '/http\:\/\/|http\:\\\\|www\./Ui', $bild))
        {
            $a_bildinformationen = @getimagesize( $bild);
        }
        else if(file_exists(getcwd() . $this->getTempBilderPfad() . $bild) &&
            is_file(getcwd() . $this->getTempBilderPfad() . $bild) &&
            is_readable(getcwd() . $this->getTempBilderPfad() . $bild))
        {
            //         	$bild = '/butler/create-thumb/file/' . base64_encode(getcwd() . $this->getTempBilderPfad() . $bild);
            $bild = 'http://' . $_SERVER['SERVER_NAME'] . $this->getTempBilderPfad() . $bild;
        }
        else if(file_exists(getcwd() . $this->getBilderPfad() . $bild) &&
            is_file(getcwd() . $this->getBilderPfad() . $bild) &&
            is_readable(getcwd() . $this->getBilderPfad() . $bild))
        {
            //         	$bild = '/butler/create-thumb/file/' . base64_encode(getcwd(). $this->getBilderPfad() . $bild);
            $bild = 'http://' . $_SERVER['SERVER_NAME'] . $this->getBilderPfad() . $bild;
        }

        $bild_link = '';

        if($name)
        {
            $name = addslashes($name);
            $bild_link .= '<p style="clear: both; float: left; display: inline; padding: 5px 0 2px 0; margin: 5px 0 0 0;">' . $name . '</p>';
        }
        $bild_link .= '<img style="float: left; display: inline;" src="' . $bild . '" alt="Bild ' . $bild . ' nicht gefunden !" title="' . $bild . '" />';

        return $bild_link;
    }

    private function imageEinfuegenNeu( $bild, $params)
    {
        $name = null;

        $bild = $this->ersetzeUmlaute($bild);

        $a_bildinformationen = array();
        $bild_formatiert = $bild;
        $b_extern = false;
        $bWithoutButler = false;

        if( preg_match( '/http\:\/\/|http\:\\\\|www\./Ui', $bild))
        {
            $b_extern = true;
            $this->setTempBilderPfad(getcwd() . '/tmp/butler/');
            $obj_cad_file = new CAD_Service_File();
            if($obj_cad_file->checkAndCreateDir($this->getTempBilderPfad())
                && TRUE === is_readable($bild)
            ) {
                $bild_formatiert = $this->getTempBilderPfad() . 'dummy.jpg';
                file_put_contents($bild_formatiert, file_get_contents($bild));
                //        	    $a_bildinformationen = getimagesize( $this->getTempBilderPfad() . $bild_formatiert);
                $bild_formatiert = '/butler/create-thumb/file/' . base64_encode($bild_formatiert);
            }

        } else if(file_exists(getcwd() . $this->getTempBilderPfad() . $bild) &&
            is_file(getcwd() . $this->getTempBilderPfad() . $bild) &&
            is_readable(getcwd() . $this->getTempBilderPfad() . $bild)
        ) {
            $bild_formatiert = 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . base64_encode(getcwd() . $this->getTempBilderPfad() . $bild_formatiert);
        } else if(file_exists(getcwd() . $this->getBilderPfad() . $bild)
            && is_file(getcwd() . $this->getBilderPfad() . $bild)
            && is_readable(getcwd() . $this->getBilderPfad() . $bild)
        ) {
            $bild_formatiert = 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . base64_encode(getcwd(). $this->getBilderPfad() . $bild_formatiert);
        } else {
            $bWithoutButler = true;
            $bild_formatiert = 'http://' . $_SERVER['SERVER_NAME'] . $this->getBilderPfad() . $bild;
        }

        if (false === $bWithoutButler) {
            $a_params = explode(":", $params);

            foreach ($a_params as $a_param) {
                $a_style = explode("=", $a_param);
                if (1 === count($a_style)
                    && 0 == strlen($name)
                ) {
                    $name = addslashes($a_style[0]);
                } else if (strtolower($a_style[0]) == "name") {
                    $name = addslashes($a_style[1]);
                } else if (isset($a_style[0])
                    && isset($a_style[1])
                ) {
                    $bild_formatiert .= '/' . $a_style[0] . '/' . $a_style[1];
                }
            }
        }

        $bild_link = '<div class="blog_pic_container" >';

        if($name)
        {
            $bild_link .= '<p>' . $name . '</p>';
        }
        else
        {
            $name = $bild;
        }
        $bild_link .= '<img class="blog_pic" src="' . $bild_formatiert . '" alt="Bild ' . $name . ' nicht gefunden !" title="' . $name . '" />';
        $bild_link .= '</div>';

        return $bild_link;
    }

    public function setBilderPfad($str_pfad)
    {
        $this->str_bilder_pfad = $str_pfad;
    }

    public function getBilderPfad()
    {
        return $this->str_bilder_pfad;
    }

    public function setTempBilderPfad($str_pfad)
    {
        $this->str_temp_bilder_pfad = $str_pfad;
    }

    public function getTempBilderPfad()
    {
        return $this->str_temp_bilder_pfad;
    }

    private function sonderzeichenErsetzen( $text)
    {
        $replace = array(
            '/ä/' => '&auml;',
            '/Ä/' => '&Auml;',
            '/ü/' => '&uuml;',
            '/Ü/' => '&Uuml;',
            '/ö/' => '&ouml;',
            '/Ö/' => '&Ouml;',
            '/ß/' => '&szlig;'
        );

        $text = preg_replace( array_keys( $replace), array_values( $replace), $text);

        return $text;
    }

    private function erstelleListenpunkt( $text)
    {
        $text = '<li style="margin-left: 20px;">' . $text . '</li>';

        return $text;
    }

    private function erstelleListenueberschrift( $text)
    {
        $text = '<span style="margin-left: 16px;">' . $text . '</span>';

        return $text;
    }

    private function ersetzeUmlaute($text)
    {
        //        var_dump(utf8_decode($text));
        //        var_dump(utf8_encode($text));
        //        var_dump($this->_detectUTF8($text));
        //        var_dump($this->_detectUTF8(utf8_encode($text)));
        //        var_dump($this->_detectUTF8(utf8_decode($text)));
        //        echo '<br />' . $text . '<br />';
        /*
        $replace = array(
            '/ä/' => 'ae',
            '/Ä/' => 'Ae',
            '/ü/' => 'ue',
            '/Ü/' => 'Ue',
            '/ö/' => 'oe',
            '/Ö/' => 'Oe',
            '/ß/' => 'ss',
        );

        return preg_replace( array_keys($replace), array_values($replace), $text);
        */
        $text = str_replace(
            array('ä','ö','ü','ß','Ä','Ö','Ü'),
            array('ae','oe','ue','ss','Ae','Oe','Ue'),
            $text
        );

        $text = str_replace(
            array("\xC4","\xD6","\xDC","\xDF","\xE4","\xF6","\xFC"),
            array("Ae","Oe","Ue","ss","ae","oe","ue"),
            $text
        );

        //        echo '<br />' . $text . '<br />';

        return $text;
    }

    private function _detectUTF8($string)
    {
        return (bool) preg_match('%(?:
            [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
            )+%xs', $string);
    }

    private function _smileyReplace($sText){

        $this->_prepareSmileys();

        $sText = preg_replace(array_keys(self::$_aReplaceSmilies), array_values(self::$_aReplaceSmilies), $sText);

        return $sText;
    }

    private function _prepareSmileys() {
        $sTheme = 'standart';

        if (true === isset($_SESSION['smiley_theme'])) {
            $sTheme = $_SESSION['smiley_theme'];
        }

        if (null === self::$_aReplaceSmilies) {
            self::$_aReplaceSmilies = array();

            $oSmileysDbTable = new Cms_Model_DbTable_Smileys();
            $oSmileysRowSet = $oSmileysDbTable->findSmileys($sTheme, $sCategory = null);

            foreach ($oSmileysRowSet as $oSmileyRow) {
                $sRegEx = '/' . $this->_escapeRegEx($oSmileyRow->smile_short) . '/i';

                $sPicturePath = '<img src="/images/content/statisch/grafiken/smileys/' . $sTheme . '/' .
                    $oSmileyRow->smile_picture . '" alt="' . $oSmileyRow->smile_short . '" title="' .
                    $oSmileyRow->smile_short . '" />';

                self::$_aReplaceSmilies[$sRegEx] = $sPicturePath;
            }
        }
    }

    private function _escapeRegEx($sText) {
        return preg_replace('/([\-|\.|\(|\)|\/|\?|\\\])/', "\\\\$1", $sText);
    }

    public function dankeText($sText)
    {
        return 'Danke, ' . $sText . '!';
    }

    public function getNews($aMatches)
    {
        return __METHOD__ . 'NEWS';
    }

    /**
     * @param boolean $bWithoutLinks
     */
    public function setWithoutLinks($bWithoutLinks) {
        $this->bWithoutLinks = $bWithoutLinks;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getWithoutLinks() {
        return $this->bWithoutLinks;
    }

    /**
     * @return array
     */
    public function getAllowdTags() {
        return $this->_aAllowdTags;
    }

    /**
     * @param array $aAllowdTags
     */
    public function setAllowdTags($aAllowdTags) {
        $this->_aAllowdTags = $aAllowdTags;
    }

    /**
     * @return array
     */
    public function getMapTagToFunction() {
        return $this->_aMapTagToFunction;
    }

    /**
     * @param array $aMapTagToFunction
     */
    public function setMapTagToFunction($aMapTagToFunction) {
        $this->_aMapTagToFunction = $aMapTagToFunction;
    }

    /**
     * @return boolean
     */
    public function isRemoveDeniedTags() {
        return $this->_bRemoveDeniedTags;
    }

    /**
     * @param boolean $bRemoveDeniedTags
     */
    public function setRemoveDeniedTags($bRemoveDeniedTags) {
        $this->_bRemoveDeniedTags = $bRemoveDeniedTags;
    }
}
