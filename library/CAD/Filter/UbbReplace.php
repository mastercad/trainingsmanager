<?php

require_once APPLICATION_PATH . '/../library/geshi/geshi.php';

class CAD_Filter_UbbReplace implements Zend_Filter_Interface
{
	protected $b_br_cleart;
	protected $a_bilder;
	protected $str_bilder_pfad;
	protected $str_temp_bilder_pfad;
	
	public function __construct($b_br_cleart = true)
	{
		$this->$b_br_cleart = $b_br_cleart;
	}

    public function filter( $text)
    {
//     	$text = htmlentities($text);
//    	$text = htmlspecialchars($text);
//    	$text = addslashes($text);
        
        $text = preg_replace( '/\[QUOTE=(.*)?\](.*)?\[\/QUOTE\]/Uis', "<div class='quote' ><div class='quote_kopf'> Zitat von : $1 </div><div class='quote_inhalt'>$2</div></div>", $text);
        $text = preg_replace( '/\[QUOTE\](.*)?\[\/QUOTE\]/Uis', "<div class='quote' ><div class='quote_kopf'> Zitat :</div><div class='quote_inhalt'>$1</div></div>", $text);
        $text = preg_replace( '/\[IMG\]([^\[]+)?\[\/IMG\]/eUis', '$this->imageEinfuegen( "$1")', $text);
        $text = preg_replace( '/\[IMG=([^\]]+)?\](.*)?\[\/IMG\]/eUis', '$this->imageEinfuegen( "$2", "$1")', $text);
        $text = preg_replace( '/\[IMG:([^]]+)?\](.*)?\[\/IMG\]/eUis', '$this->imageEinfuegenNeu( "$2", "$1")', $text);
        $text = preg_replace( '/\[EMAIL\]?([^\[]+)?\[\/EMAIL\]/Uis', "<a href='mailto:$1'>$1</a>", $text);
        $text = preg_replace( '/\[EMAIL=([^\[]+)?\]([^\[]+)?\[\/EMAIL\]/Uis', "<a href='mailto:$1'>$2</a>", $text);
        $text = preg_replace( '/\[URL=([^\[]+)?\]([^\[]+)?\[\/URL\]/eUis', '$this->ersetzeURL( "$2", "$1")', $text);
        $text = preg_replace( '/\[URLIMG=(.*)?]([^\[]+?)\[\/URLIMG\]/eUis', '$this->ersetzeURLImage( "$2", "$1")', $text);
        $text = preg_replace( '/\[URL\]([^\[]+)\[\/URL\]/eUis', '$this->ersetzeURL("$1")', $text);
        $text = preg_replace( '/\[PHP\](.*)?\[\/PHP\]/eUis', '$this->phpString( "$1")', $text);
        $text = preg_replace_callback( '/\[CODE=?(.*)??\](.*)?\[\/CODE\]/Uis', array(&$this, "codeString"), $text);
        $text = preg_replace( '/\[MITGLIED\](.*)?\[\/MITGLIED\]/eUis', '$this->mitgliedText( "$1")', $text);
        $text = preg_replace( '/\[LINIE=(.*)?\]/Uis', '<hr style="width: 100%; height: 2px; color: $1; background: $1; margin: 10px 0px; border: 0;" />', $text);
        $text = preg_replace( '/\[ULISTE\](.*)?\[\/ULISTE\]/Ueis', '$this->erstelleListe( "$1")', $text);
        $text = preg_replace( '/\[DANKE\](.*)?\[\/DANKE\]/eUis', '$this->dankeText( "$1")', $text);
        $text = preg_replace( '/\[BLOCK\](.*)?\[\/BLOCK\]/Uis', '<div style="text-align: justify;">$1<br style="clear: both;" /></div>', $text);
        $text = preg_replace( '/\[CENTER\](.*)?\[\/CENTER\]/Uis', "<div style='text-align: center;'>$1<br style='clear: both;' /></div>", $text);
        $text = preg_replace( '/\[LEFT\](.*)?\[\/LEFT\]/Uis', "<div style='text-align: left;'>$1<br style='clear: both;' /></div>", $text);
        $text = preg_replace( '/\[RIGHT\](.*)?\[\/RIGHT\]/Uis', "<div style='text-align: right;'>$1<br style='clear: both;' /></div>", $text);
        $text = preg_replace( '/\[FLOAT\](.*)?\[\/FLOAT\]/Uis', '<div style="float: left; display: inline;">$1</div>', $text);
        $text = preg_replace( '/\[MARQUEE\](.*)?\[\/MARQUEE\]/Uis', "<marquee>$1</marquee>", $text);
        $text = preg_replace( '/\[NL\]/Ui', '<br class="clearfix" />', $text);
        $text = preg_replace( '/\[VIDEO](.*)?\[\/VIDEO\]/i', '<script type="text/javascript" src="/js/swfobject.js"></script><script type="text/javascript" src="/js/jwplayer.js"></script><embed flashvars="file=$1&autostart=false" allowfullscreen="true" allowscripaccess="always" id="player1" name="player1" src="/film/player.swf" width="480" height="270"/>', $text);

        while( preg_match( '/\[COLOR=\#[0-9a-f]{3,6}\].*\[\/COLOR\]/eUis', $text))
        {
            $text = preg_replace( '/(.*)\[COLOR=(\#[0-9a-f]{3,6})\](.*)\[\/COLOR\](.*?)/eUis', '$this->stylesZusammenfuehren( \'$1\', \'color: $2;\', \'$3\', \'$4\')', $text);
    	}

        while( preg_match( '/\[BGCOLOR=\#[0-9a-f]{3,6}\].*\[\/BGCOLOR\]/eUis', $text))
        {
            $text = preg_replace( '/(.*)\[BGCOLOR=(\#[0-9a-f]{3,6})\](.*)\[\/BGCOLOR\](.*?)/eUis', '$this->stylesZusammenfuehren( \'$1\', \'background-color: $2;\', \'$3\', \'$4\')', $text);
    	}
        $text = preg_replace( '/(.*)\[SIZE=([0-9]{1,2})\](.*)?\[\/SIZE\](.*)?/eUis', '$this->stylesZusammenfuehren( \'$1\', \'font-size: $2px; \', \'$3\', \'$4\')', $text);
        $text = preg_replace( '/(.*)\[GLOW=(.*)\](.*)?\[\/GLOW\]?(.*)?/eUis', '$this->stylesZusammenfuehren( \'$1\', \'filter: glow( color=$2, strength=2); \', \'$3\', \'$4\')', $text);
        $text = preg_replace( '/(.*)\[WAVE\](.*)?\[\/WAVE]?/eUis', '$this->stylesZusammenfuehren( \'$1\', \'filter: Wave( freq=2, light=20, phase=50, strength=2); \', \'$2\', \'$3\')', $text);
        $text = preg_replace( '/(.*)\[SHADOW\](.*)?\[\/SHADOW]?/eUis', '$this->stylesZusammenfuehren( \'$1\', \'filter: Shadow( color=#707070, direction=135;) \', \'$2\', \'$3\')', $text);

        while( preg_match( '/\[B\](.*)?\[\/B\]/eUis', $text))
        {
            $text = preg_replace( '/(.*)\[B\](.*)?\[\/B\](.*)?/eUis', '$this->stylesZusammenfuehren( \'$1\', \'font-weight: bold; \', \'$2\', \'$3\')', $text);
        }

        while( preg_match( '/\[I\](.*)\[\/I\]/eUis', $text))
        {
            $text = preg_replace( '/(.*)\[I\](.*)?\[\/I\](.*)?/eUis', '$this->stylesZusammenfuehren( \'$1\', \'font-style: italic; \', \'$2\', \'$3\')', $text);
    	}

        while( preg_match( '/\[U\](.*)\[\/U\]/eUis', $text))
        {
            $text = preg_replace( '/(.*)\[U\](.*)?\[\/U\](.*)?/eUis', '$this->stylesZusammenfuehren( \'$1\', \'text-decoration: underline; \', \'$2\', \'$3\')', $text);
    	}

        $text = preg_replace( '/(.*)\[S\](.*)?\[\/S\]/eUis', "<s>$1</s>", $text);

        while( preg_match( '/\[FONT=(.*)\](.*)?\[\/FONT\]/', $text))
        {
            $text = preg_replace( '/(.*)\[FONT=(.*)\](.*)\[\/FONT\](.*?)/eUis', '$this->stylesZusammenfuehren( \'$1\', \'font-family: $2; \', \'$3\', \'$4\')', $text);
    	}

        if($this->b_br_cleart)
        {
            $text = preg_replace( '(\n|\r\n|\n\r)', '<br class="clearfix" />', $text);
        }
        else
        {
            $text = preg_replace( '(\n|\r\n|\n\r)', '<br />', $text);
        }

        $text = preg_replace( "[\t]", "&nbsp;&nbsp;&nbsp;&nbsp;", $text);

        $text = preg_replace( '/\[NEWS]/Uei', '$this->getNews(\'$1\')', $text);

//    	$text = htmlspecialchars($text);
        
        return $text;
    }

    /* 
     * Funktion zum ersetzen der URLs, ist ein URL außerhalb des lokalen
     * Servers, wird das Link Tag automatisch mit einem Target _blank versehen
     */
    private function ersetzeURL( $url, $name = '')
    {
    	$link = '';
		$hostname_url = parse_url($url, PHP_URL_HOST);
		$hostname_server = "byte-artist.de";
		
		if(is_array($_SERVER) &&
		   key_exists('SERVER_NAME', $_SERVER))
		{
			$hostname_server = $_SERVER['SERVER_NAME'];
		}
		
    	if(!$name)
    	{
    		$name = $url;
    	}
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
    	return $link;
    }

    public function ersetzeURLImage( $url, $image)
    {
    	$link = '';

    	// wenn eine dateiendung
    	if(preg_match('/(\.[a-z0-9]{2,5})\/?$/i', $url))
    	{
    		$link = '<a href="' . $url . '" target="_blank">' . $this->imageEinfuegen($image) . '</a>';
    	}
    	else
    	{
    		$link = '<a href="' . $url . '">' . $this->imageEinfuegen($image) . '</a>';
    	}
    	return $link;
    }

    // function, die checkt, ob das user ein mitglied des forums ist, wenn ja wird text angezeigt, wenn nein, der register link
    private function mitgliedText( $text)
    {
        if( $_SESSION['mitglieder_id'])
        {
            return $text;
        }
        else
        {
            return '<span style="color: red;">Bitte einloggen, oder <a style="color: red;" href="?seite=registrieren&amp;forumid=' . $_GET['forumid'] . '&amp;subforumid=' . $_GET['subforumid'] . '&amp;threadid=' . $_GET['threadid'] . '&amp;aktuelle_seite=' . $_GET['aktuelle_seite'] . '&amp;anzahl_posts=' . $_GET['anzahl_posts'] . '" title="Nicht die benötigten Rechte um diesen Text zu sehen" >registrieren</a>, damit der Text angezeigt wird !</span>';
        }
    }

    public function codeString($a_params)
    {
        $language = trim($a_params[1]);
        $source = $a_params[2];

        /*
    	$code_container = '<code class="blog-code lang-' . $language . '" ><pre>' . $source . '</pre></code>';

    	return $code_container;
         *
         */

        $language = strtoupper($language);

        $source = stripslashes($source);
        $source = preg_replace('/^\n/', '', $source);
        $source = preg_replace('/\n$/', '', $source);

        $header_content = '<div class="code_header" style="position: relative; padding: 2px 5px; font-weight: bold; background-color: #CCCCCC; color: #333333;">';
        $header_content .= '<span class="highlight_minimize">+</span>';
        $header_content .= '<h3 style="position: absolute; top: -10px; left: 15px; padding: 2px 5px; background-color: #FFFFFF; border: 1px solid #CCCCCC;">'. $language . ' code</h3>';
//        $header_content .= '<img src="#" alt="copy to clipboard" style="position: absolute; top: 2px; right: 5px;" />';
        $header_content .= '</div>';
        $footer_content = '<div class="code_footer" style="height: 10px; background-color: #CCCCCC; color: #333333;"></div>';

        $obj_geshi = new GeSHi($source);

        $obj_geshi->enable_classes(true);
        $obj_geshi->set_overall_class('highlight_code');
//        $obj_geshi->set_header_type(GESHI_HEADER_DIV);
        $obj_geshi->set_header_type(GESHI_HEADER_PRE);
        /*
         *
            GESHI_NORMAL_LINE_NUMBERS - Use normal line numbering
            GESHI_FANCY_LINE_NUMBERS - Use fancy line numbering
            GESHI_NO_LINE_NUMBERS - Disable line numbers (default)
         */
        $obj_geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
//        $obj_geshi->start_line_numbers_at($number);
//        $obj_geshi->enable_classes();
        $obj_geshi->set_header_content($header_content);
        $obj_geshi->set_footer_content($footer_content);
//        $obj_geshi->highlight_lines_extra(array(8));
        $replaced_source = $source;

        if(strlen(trim($language)) > 0)
        {
            $obj_geshi->set_language($language);
            if("PHP" == $language)
            {
                $obj_geshi->set_url_for_keyword_group(3, 'http://www.php.net/{FNAME}');
            }
            $replaced_source = $obj_geshi->parse_code();
            // eventuell im text enthaltene [ oder ] escapen
            $replaced_source = preg_replace('/\[/', '&#91;', $replaced_source);
            $replaced_source = preg_replace('/\]/', '&#93;', $replaced_source);
        }

        return ($replaced_source);
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

    private function dankethread( $text)
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
			if( strlen( $listen_punkt) > 0 &&
				!preg_match( '/\<ul/i', $listen_punkt))
			{
				$liste .= '<li style="list-style: disc inside none; margin-left: 10px;">' . $listen_punkt . '</li>';
			}
			else if( !preg_match( '/\<ul/i', $listen_punkt) ||
					!preg_match( '/\<\/ul\>/i', $listen_punkt))
			{
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

            $anhang .= '<a href="/' . $pfad . $text . '" title="' . $text . '" target="_blank" >';
            $anhang .= '<img src="/butler/create-thumb/file/' . $pfad . $text . '" alt="Bild ' . $text . ' nicht gefunden !" title="' . $text . '" />';
         	
            $anhang .= '</a>';
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
		$a_bildinformationen = array();
		$bild_formatiert = $bild;
		$b_extern = false;
		
        if( preg_match( '/http\:\/\/|http\:\\\\|www\./Ui', $bild))
        {
        	$b_extern = true;
        	$this->setTempBilderPfad(getcwd() . '/tmp/butler/');
        	$obj_cad_file = new CAD_File();
        	if($obj_cad_file->checkAndCreateDir($this->getTempBilderPfad()))
        	{
	        	$bild_formatiert = $this->getTempBilderPfad() . 'dummy.jpg';
	        	file_put_contents($bild_formatiert, file_get_contents($bild));
//        	    $a_bildinformationen = getimagesize( $this->getTempBilderPfad() . $bild_formatiert);
            	$bild_formatiert = '/butler/create-thumb/file/' . base64_encode($bild_formatiert);
        	}
            
        }
		else if(file_exists(getcwd() . $this->getTempBilderPfad() . $bild) &&
	           	is_file(getcwd() . $this->getTempBilderPfad() . $bild) &&
	           	is_readable(getcwd() . $this->getTempBilderPfad() . $bild))
        {
        	$bild_formatiert = 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . base64_encode(getcwd() . $this->getTempBilderPfad() . $bild_formatiert);
        }
        else if(file_exists(getcwd() . $this->getBilderPfad() . $bild) &&
        		is_file(getcwd() . $this->getBilderPfad() . $bild) &&
        		is_readable(getcwd() . $this->getBilderPfad() . $bild))
        {
        	$bild_formatiert = 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . base64_encode(getcwd(). $this->getBilderPfad() . $bild_formatiert);	
        }

        $a_params = explode(":", $params);
        
        foreach($a_params as $a_param)
        {
        	$a_style = explode("=", $a_param);
        	if(strtolower($a_style[0]) == "name")
        	{
        		$name = addslashes($a_style[1]);
        	}
        	else if(isset($a_style[0]) &&
        			isset($a_style[1]))
        	{
        		$bild_formatiert .= '/' . $a_style[0] . '/' . $a_style[1];
        	}
        }
        
        $bild_link = '<div style="margin-right: 15px; margin-bottom: 15px; float: left; display: inline; padding: 5px 0 2px 0; ">';
        
        if($name)
        {
        	$bild_link .= '<p>' . $name . '</p>';
        }
        else
        {
        	$name = $bild;
        }
        $bild_link .= '<img src="' . $bild_formatiert . '" alt="Bild ' . $name . ' nicht gefunden !" title="' . $name . '" />';
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

    private function ersetzeUmlaute( $text)
    {
        $replace = array(
                            '/ä/' => 'ae',
                            '/Ä/' => 'ae',
                            '/ü/' => 'ue',
                            '/Ü/' => 'ue',
                            '/ö/' => 'oe',
                            '/Ö/' => 'oe',
                            '/ß/' => 'ss'
                        );

        $text = preg_replace( array_keys($replace), array_values($replace), $text);

        return $text;
    }

    private function smilieReplace( $text)
    {
        $theme = $_SESSION['smilie_theme'];

        if( !$theme)
        {
            $theme = 'standart';
        }

        $requestStringSmilies = "SELECT * FROM smilies WHERE smilie_theme LIKE( '" . $theme . "')";
        $newResultSmilies = mysql_query( $requestStringSmilies);

        while( $dataResultSmilies = mysql_fetch_array( $newResultSmilies))
        {
            // escaped alle sonderzeichen, wie hier eben : oder ( oder )
            // hier müssen die suchstrings mit sonderzeichen ersetzt werden, da sonst umlaute nicht mehr gefunden werden !
            $kuerzel = sonderzeichen_ersetzen( $dataResultSmilies['smilie_kuerzel']);
            $suchstring = ' ' . addcslashes( $kuerzel, ':()?');
            $ersatz = " <img src='/images/grafiken/smilies/" . $theme . "/" . $dataResultSmilies['smilie_bild'] ."' alt='" . $kuerzel . "' title='" . $kuerzel . "' /> ";

            $text = ereg_replace( $suchstring, $ersatz, $text);
        }

        return $text;
    }

    private function sucheErsten( $text, $tag)
    {
    	$pos = -1;
    	
        return $pos;
    }

    private function sucheLetzten( $text, $tag)
    {
        $pos = strripos( $text, $tag);
		return $pos;
    }

	private function stylesZusammenfuehren( $vortext, $style, $string, $text_nach_tag = "NÜX")
	{
	    #*********************************************************************
	    # vars reseten
	    #*********************************************************************
		$vortext_tag_start_pos = -1;
	    $uebergabe_tag_start_pos = -1;
		$rueckgabe = '';

		$vortext_string_vor_tag = -1;
		$vortext_tag_end_pos = -1;
		$vortext_string_nach_tag = -1;
		$vortext_tag = -1;
		$vortext_style_start_pos = -1;
		$vortext_string_vor_style = '';
		$vortext_style_temp_string = '';
		$vortext_style_end_pos = -1;
		$vortext_style_string = '';

		$uebergabe_style_start_pos;
		$uebergabe_string_vor_style = '';
		$uebergabe_string_vor_tag = -1;
		$uebergabe_tag_end_pos = -1;
		$uebergabe_string_nach_tag = -1;
		$uebergabe_tag = -1;
		$uebergabe_style_temp_string;
		$uebergabe_style_end_pos;
		$uebergabe_style_string = '';
		$span_start = -1;

		$folgetext_tag_start_pos = -1;

	    $string = stripslashes( $string);
		$vortext = stripslashes( $vortext);
		$style = stripslashes( $style);
		$text_nach_tag = stripslashes( $text_nach_tag);

	    $vortext_tag_start_pos = $this->sucheLetzten( $vortext, '<span ');
	    $uebergabe_tag_start_pos = $this->sucheErsten( $string, "<span ");
		$folgetext_tag_start_pos = $this->sucheErsten( trim( $text_nach_tag), "</span>");

		#***********************************************************************
	    # checken ob die tags auch das letzte oder erste im string sind !
	    #***********************************************************************
	    if( $vortext_tag_start_pos > -1)
	    {
	    	$vortext_string_vor_tag = substr( $vortext, 0, $vortext_tag_start_pos);
			$vortext_tag_end_pos = strpos( $vortext, ' ">');
			$vortext_string_nach_tag = substr( $vortext, $vortext_tag_end_pos + 3, strlen( $vortext) - $vortext_tag_end_pos);
			$vortext_tag = substr( $vortext, $vortext_tag_start_pos, $vortext_tag_end_pos - $vortext_tag_start_pos + 1);
	    }
		$uebergabe_string_vor_tag = substr( $string, 0, $uebergabe_tag_start_pos);
		$uebergabe_tag_end_pos = strpos( $string, ' ">');
	    $uebergabe_string_nach_tag = substr( $string, $uebergabe_tag_end_pos + 3 , strlen( $string));
	    $uebergabe_tag = substr( $string, $uebergabe_tag_start_pos, $uebergabe_tag_end_pos - $uebergabe_tag_start_pos + 1);

		# nun noch checken, ob im string nach dem tag an erster stelle ein span steht,
		# wenn ja, dieses löschen
		# wenn nein, stehen lassen und beim zusammenbau darauf reagieren
	    #**********************************************************************
	    # wenn der tag das letzte in vortext und ein tag das erste im string
	    #**********************************************************************
	    if( ( $vortext_tag_start_pos > -1) &&
	    	( strlen( trim( $vortext_string_nach_tag)) == 0) &&
	    	( $uebergabe_tag_start_pos > -1) &&
	    	( strlen( trim( $uebergabe_string_vor_tag)) == 0))
		{
	        # in dem tag nach dem style element suchen ******** slashes müssen nach htmlentities entfernt werden ******************
	        $vortext_style_start_pos = strpos( $vortext_tag, 'style="');
	        $vortext_string_vor_style = substr( $vortext_tag, 0, $vortext_style_start_pos);
			$vortext_style_temp_string = substr( $vortext_tag, $vortext_style_start_pos, strlen( $vortext_tag));
			$vortext_style_end_pos = strpos( $vortext_tag, '"');
	        $vortext_style_string = substr( $vortext_tag, $vortext_style_start_pos + 7, $vortext_style_end_pos - $vortext_style_start_pos - 7);

			$uebergabe_style_start_pos = strrpos( $uebergabe_tag, 'style="');
			$uebergabe_string_vor_style = substr( $uebergabe_tag, 0, $uebergabe_style_start_pos);
			$uebergabe_style_temp_string = substr( $uebergabe_tag, $uebergabe_style_start_pos, strlen( $uebergabe_tag));
	        $uebergabe_style_end_pos = strpos( $uebergabe_tag, '"');

	        $uebergabe_style_string = substr( $uebergabe_tag, $uebergabe_style_start_pos + 7, $uebergabe_style_end_pos - $uebergabe_style_start_pos - 7);

	        # style an vortextstyle anfügen
	        $style_neu = $style . $vortext_style_string . " " . $uebergabe_style_string;
	        $string = $uebergabe_string_nach_tag;

	        if( strlen( $folgetext_tag_start_pos) &&
	        	$folgetext_tag_start_pos == 0)
	       	{
		        # wegreduziertes schließendes tag löschen
				$span_start = strpos( $string, '</span>');
		        $string_rest = substr( $string, 0, $span_start) . substr( $text_nach_tag, $span_start + 7, strlen( $text_nach_tag));

				$rueckgabe = $vortext_string_vor_tag . '<span style="' . $style_neu . ' ">' . $string . $string_rest;
	       	}
	       	else
	       	{
	       		$rueckgabe = $vortext . '<span style="' . $style . $uebergabe_style_string . ' ">' . $string . $text_nach_tag;
	       	}
	    }
	    #**********************************************************************
	    # wenn der tag das letzte in vortext
	    #**********************************************************************
	    else if( 	( $vortext_tag_start_pos > -1) &&
	    		( strlen( trim( $vortext_string_nach_tag)) == 0))
		{
	        # in dem tag nach dem style element suchen ******** slashes müssen nach htmlentities entfernt werden ******************
	        $vortext_style_start_pos = strpos( $vortext_tag, 'style="');
	        $vortext_string_vor_style = substr( $vortext_tag, 0, $vortext_style_start_pos);
			$vortext_style_temp_string = substr( $vortext_tag, $vortext_style_start_pos, strlen( $vortext_tag));
	        $vortext_style_end_pos = strrpos( $vortext_tag, '"');
			$vortext_style_string = substr( $vortext_tag, $vortext_style_start_pos + 7, $vortext_style_end_pos - $vortext_style_start_pos - 7);

			$rueckgabe = $vortext_string_vor_tag . '<span style="' . $vortext_style_string . ' ' . $style . '">' . $string . $text_nach_tag;
	    }
	    #**********************************************************************
	    # wenn der tag das erste im string
	    #**********************************************************************
	    if( ( $uebergabe_tag_start_pos > -1) &&
	    		( strlen( trim( $uebergabe_string_vor_tag)) == 0))
		{
	        $uebergabe_style_start_pos = strrpos( $uebergabe_tag, 'style="');
	        $uebergabe_string_vor_style = substr( $uebergabe_tag, 0, $uebergabe_style_start_pos);
			$uebergabe_style_temp_string = substr( $uebergabe_tag, $uebergabe_style_start_pos, strlen( $uebergabe_tag));
	        $uebergabe_style_end_pos = strrpos( $uebergabe_tag, '"');
	        $uebergabe_style_string = substr( $uebergabe_tag, $uebergabe_style_start_pos + 7, $uebergabe_style_end_pos - $uebergabe_style_start_pos - 7);
	        $string = substr( $string, $uebergabe_tag_end_pos + 3, strlen( $string));

			$rueckgabe = $vortext . '<span style="' . $uebergabe_style_string . ' ' . $style . ' ">' . $string . $text_nach_tag;
	    }
	    if( !strlen( $rueckgabe))
		{
			$rueckgabe = $vortext . '<span style="' . $style . '">' . $string . '</span>' . $text_nach_tag;
			$ungueltig = 1;
	    }

	    #**********************************************************************
	    # wenn keine vorhandenen tags gefunden neuen erzeugen und übergeben
	    #**********************************************************************
		return $rueckgabe;
	}

    private function styleErsetzen( $vortext, $style, $string)
    {
        //*********************************************************************
        // vars reseten
        //*********************************************************************
        $vortext_tag_start_pos = -1;
        $ubergabe_tag_start_pos = -1;

        if( isset( $_GET['debug']))
        {
        	$debug = $_GET['debug'];
        }
        else
        {
        	$debug = 0;
        }

        $string = stripslashes( $string);
        $vortext = stripslashes( $vortext);
        $style = stripslashes( $style);

        $vortext_tag_start_pos = $this->sucheLetzten( $vortext, '<span ');
        $uebergabe_tag_start_pos = $this->sucheErsten( $string, '<span ');

        //***********************************************************************
        // checken ob die tags auch das letzte oder erste im string sind !
        //***********************************************************************
        $vortext_string_vor_tag = substr( $vortext, 0, $vortext_tag_start_pos);
        $vortext_tag_end_pos = strripos( $vortext, '">');
        $vortext_string_nach_tag = substr( $vortext, $vortext_tag_end_pos + 2, strlen( $vortext));
        $vortext_tag = substr( $vortext, $vortext_tag_start_pos, $vortext_tag_end_pos - $vortext_tag_start_pos + 2);

        $uebergabe_string_vor_tag = substr( $string, 0, $uebergabe_tag_start_pos);
        $uebergabe_tag_end_pos = strpos( $string, '">');
        $uebergabe_string_nach_tag = substr( $string, $uebergabe_tag_end_pos + 2 , strlen( $string));
        $uebergabe_tag = substr( $string, $uebergabe_tag_start_pos, $uebergabe_tag_end_pos - $uebergabe_tag_start_pos + 2);

        //**********************************************************************
        // wenn der tag das letzte in vortext und ein tag das erste im string
        //**********************************************************************
        if( ( $vortext_tag_start_pos > -1) && ( strlen( trim( $vortext_string_nach_tag)) == 0) && ( $uebergabe_tag_start_pos > -1) &&  ( strlen( trim( $uebergabe_string_vor_tag)) == 0))
        {
            $vortext_style_start_pos = strpos( $vortext_tag, 'style="');
            $vortext_string_vor_style = substr( $vortext_tag, 0, $vortext_style_start_pos);
            $vortext_style_temp_string = substr( $vortext_tag, $vortext_style_start_pos, strlen( $vortext_tag));
            $vortext_style_end_pos = strripos( $vortext_tag, '"');
            $vortext_style_string = substr( $vortext_tag, $vortext_style_start_pos + 7, $vortext_style_end_pos - $vortext_style_start_pos - 7);

            $uebergabe_style_start_pos = strpos( $uebergabe_tag, 'style="');
            $uebergabe_string_vor_style = substr( $uebergabe_tag, 0, $uebergabe_style_start_pos);
            $uebergabe_style_temp_string = substr( $uebergabe_tag, $uebergabe_style_start_pos, strlen( $uebergabe_tag));
            $uebergabe_style_end_pos = strripos( $uebergabe_tag, '"');

            $uebergabe_style_string = substr( $uebergabe_tag, $uebergabe_style_start_pos + 7, $uebergabe_style_end_pos - $uebergabe_style_start_pos - 7);

            // style an vortextstyle anfügen
            $style .= $vortext_style_string . $uebergabe_style_string;
            $string = $uebergabe_string_nach_tag;

            // wegreduziertes schließendes tag löschen
            $span_start = strpos( $string, '</span>');
            $string = substr( $string, 0, $span_start) . substr( $string, $span_start + 7, strlen( $string));

            $rueckgabe = $vortext_string_vor_tag . ' <span style="' . $style . ' ">' . $string;

            return $rueckgabe;
        }
        //**********************************************************************
        // wenn der tag das erste im string
        //**********************************************************************
        else if( ( $uebergabe_tag_start_pos > -1) && ( strlen( trim( $uebergabe_string_vor_tag)) == 0))
        {
            $uebergabe_style_start_pos = strpos( $uebergabe_tag, 'style="');
            $uebergabe_string_vor_style = substr( $uebergabe_tag, 0, $uebergabe_style_start_pos);
            $uebergabe_style_temp_string = substr( $uebergabe_tag, $uebergabe_style_start_pos, strlen( $uebergabe_tag));
            $uebergabe_style_end_pos = strripos( $uebergabe_tag, '"');
            $uebergabe_style_string = substr( $uebergabe_tag, $uebergabe_style_start_pos + 7, $uebergabe_style_end_pos - $uebergabe_style_start_pos - 7);

            $string = substr( $string, $uebergabe_tag_end_pos + 2, strlen( $string));
            $rueckgabe = $vortext . ' <span style="' . $uebergabe_style_string . ' ' . $style . ' ">' . $string;

            return $rueckgabe;
        }
        //**********************************************************************
        // wenn der tag das letzte in vortext
        //**********************************************************************
        else if( ( $vortext_tag_start_pos > -1) && ( strlen( trim( $vortext_string_nach_tag)) == 0))
        {
            // in dem tag nach dem style element suchen ******** slashes müssen nach htmlentities entfernt werden ******************
            $vortext_style_start_pos = strpos( $vortext_tag, 'style="');
            $vortext_string_vor_style = substr( $vortext_tag, 0, $vortext_style_start_pos);
            $vortext_style_temp_string = substr( $vortext_tag, $vortext_style_start_pos, strlen( $vortext_tag));
            $vortext_style_end_pos = strripos( $vortext_tag, '"');
            $vortext_style_string = substr( $vortext_tag, $vortext_style_start_pos + 7, $vortext_style_end_pos - $vortext_style_start_pos - 7);

            $rueckgabe = $vortext_string_vor_tag . ' <span style="' . $vortext_style_string . ' ' . $style . ' ">' . $string;

            return $rueckgabe;
        }
        else
        {
            $ungueltig = 1;
        }

        //**********************************************************************
        // wenn keine vorhandenen tags gefunden neuen erzeugen und �bergeben
        //**********************************************************************
        $rueckgabe = $vortext . '<span style="' . $style . '">' . $string . '</span>';
        return $rueckgabe;
    }
}
