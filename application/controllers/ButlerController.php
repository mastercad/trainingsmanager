<?php

	class ButlerController extends Zend_Controller_Action
	{
	    public function init()
	    {
	    }
	
	    public function createThumbAction()
	    {
	    	$req = $this->getRequest();
	    	$a_params = $req->getParams();
	    	
	    	$file = '';
	    	
	    	if(isset($a_params['file']))
    		{
	    		$file = $a_params['file'];
	    		
	    		/**
	    		 * wenn keine slashes und punkte im namen enthalten sind, davon
	    		 * ausgehen, dass es sich um einen base64 encodierten string
	    		 * handelt
	    		 */
	    		if(!preg_match('/[\/|\.]/', $file))
	    		{
	    			$file = base64_decode($file);
	    		}
	    		
	    		if(!file_exists($file) ||
	    	   	   !is_file($file) ||
	    	   	   !is_readable($file))
	    		{
	    			echo "Datei " . $file . " nicht vorhanden, oder nicht lesbar!<br />";
    				return false; 
	    		}
	    	}
	    	else 
	    	{
	    		echo "Kein Dateiname übergeben!<br />";
	    		return false;
	    	}
	    	
                $this->getHelper('viewRenderer')->setNoRender();
			
	    	$a_fileinformationen = getimagesize($file);
	    	
	    	$orig_width = $a_fileinformationen[0];
	    	$orig_height = $a_fileinformationen[1];
	    	
	    	$thumb_width = null;
	    	$thumb_height = null;
	    	
	    	$new_width = null;
	    	$new_height = null;
	    	
	    	$ratio = $orig_width / $orig_height;
	    	
	    	$faktor_width = 1;
	    	$faktor_height = 1;
	    	
	    	$dest_x = 0;
	    	$dest_y = 0;
	    	$src_x = 0;
	    	$src_y = 0;
	    	
	    	/**
			 * die prioritäten können gesetzt werden, um bei angabe von
			 * breite UND höhe des thumbs sicher zu stellen, das nach einer der
			 * beiden masse der faktor berechnet wird
	    	 */
	    	$b_prio_width = false;
	    	$b_prio_height = false;
	    	
	    	$type = $a_fileinformationen[2];
	    	
	    	if(isset($a_params['width']))
	    	{
	    		$thumb_width = $a_params['width'];
	    	}
	    	
	    	if(isset($a_params['height']))
	    	{
	    		$thumb_height = $a_params['height'];
	    	}
	    	
	    	if(!$thumb_width &&
	    	   $thumb_height)
	    	{
	    		$thumb_width = $thumb_height / $ratio;
	    	}
	    	if(!$thumb_height &&
	    	   $thumb_width)
	    	{
	    		$thumb_height = $thumb_width / $ratio;
	    	}
	    	
	    	if(!$thumb_width &&
	    	   !$thumb_height)
	    	{
	    		$thumb_width = $orig_width - 1;
	    		$thumb_height = $orig_height - 1;
	    	}
	    	
	    	if(isset($a_params['prio_width']) || 
	    	   !isset($a_params['height']))
	    	{
	    		$b_prio_width = true;
	    	}
	    	else if(isset($a_params['prio_height']) ||
    				!isset($a_params['width']))
	    	{
	    		$b_prio_height = true;
	    	}
	    	
	    	if($orig_width > $thumb_width)
	    	{
	    		$faktor_width = $thumb_width / $orig_width;
	    	}
	    	
	    	if($orig_height > $thumb_height)
	    	{
	    		$faktor_height = $thumb_height / $orig_height;
	    	}
	    	
	    	if($b_prio_width)
	    	{
	    		$new_height = $orig_height * $faktor_width;
	    		$new_width = $orig_width * $faktor_width;
	    	}
	    	else
	    	{
	    		$new_height = $orig_height * $faktor_height;
	    		$new_width = $orig_width * $faktor_height;
	    	}
	    	
	    	if($new_height < $thumb_height)
	    	{
	    		$dest_y = ($thumb_height - $new_height) / 2;
	    	}
	    	else
	    	{
	    		$thumb_height = $new_height;
	    	}
	    	
	    	if($new_width < $thumb_width)
	    	{
	    		$dest_x = ($thumb_width - $new_width) / 2;
	    	}
	    	else
	    	{
	    		$thumb_width = $new_width;
	    	}
	    	/*
	    	echo "Orig Breite : " . $orig_width . "<br />";
	    	echo "Orig Höhe : " . $orig_height . "<br />";
	    	echo "Thumb Breite : " . $thumb_width . "<br />";
	    	echo "Thumb Höhe : " . $thumb_height . "<br />";
	    	echo "Faktor Breite : " . $faktor_width . "<br />";
	    	echo "Faktor Höhe : " . $faktor_height . "<br />";

	    	echo "Ratio : " . $ratio . "<br />";
	    	echo "Prio Breite : " . $b_prio_width . "<br />";
	    	echo "Prio Höhe : " . $b_prio_height . "<br />";
	    	
	    	echo "Neue Breite : " . $new_width . "<br />";
	    	echo "Neue Höhe : " . $new_height . "<br />";
	    	
	    	echo "Type : " . $type . "<br />";
                */
	    	$img_dest = imagecreatetruecolor($thumb_width, $thumb_height);
	    	
	    	switch ($type)
	    	{
	    		/* gif */
	    		case 1:
	    			{
                                    header('Content-Type: image/gif');

                                    $black = imagecolorallocate($img_dest, 0, 0, 0);
                                    imagecolortransparent($img_dest, $black);

                                    $img_src = imagecreatefromgif($file);

                                    /* eventuelle transparenz des originals beibehalten */
                                    $transparent_index = imagecolortransparent($img_src);
                                    if($transparent_index != (-1))
                                    {
                                        @$transparent_color = imagecolorsforindex($img_src, $transparent_index);
                                        @$transparent_new = imagecolorallocate($img_src, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                                        $transparent_new_index = imagecolortransparent($img_src, $transparent_new);
                                        imagefill($img_src, 0, 0, $transparent_new_index);
                                    }

                                    imagecopyresampled($img_dest, $img_src, $dest_x, $dest_y, $src_x, $src_y, $new_width, $new_height, $orig_width, $orig_height);
                                    imagegif($img_dest);
                                    ImageDestroy($img_src);
                                    ImageDestroy($img_dest);
                                    break;
	    			}
    			/* jpeg */
	    		case 2:
                        {
                            header('Content-Type: image/jpeg');
                            $weiss = imagecolorallocate($img_dest, 255, 255, 255);
// 			    imagefill($img_dest, 0, 0, $transparent_new_index);
	    				
                            $img_src = imagecreatefromjpeg($file);
                            imagecopyresampled($img_dest, $img_src, $dest_x, $dest_y, $src_x, $src_y, $new_width, $new_height, $orig_width, $orig_height);
                            imagejpeg($img_dest);
                            ImageDestroy($img_src);
                            ImageDestroy($img_dest);
                            break;
                        }
	    		/* png */
	    		case 3:
	    			{
                                    header('Content-Type: image/png');

                                    $black = imagecolorallocate($img_dest, 0, 0, 0);
                                    imagecolortransparent($img_dest, $black);

                                    $img_src = imagecreatefrompng($file);
                                    imagealphablending($img_src, false);
                                    imagesavealpha($img_src, true); 

                                    imagecopyresampled($img_dest, $img_src, $dest_x, $dest_y, $src_x, $src_y, $new_width, $new_height, $orig_width, $orig_height);
                                    imagepng($img_dest);
                                    ImageDestroy($img_src);
                                    ImageDestroy($img_dest);
                                    break;
	    			}
    			/* swf */
// 	    		case 4:
// 	    			{
// 	    				break;
// 	    			}
	    		default:
	    			{
                                    imagestring($img_dest,1,1,$thumb_height / 2,"Fehler beim Verarbeiten", $black);
                                    imagepng($img_dest);
                                    break;
	    			}
	    	}
	    }
	    
	    public function createDummyBlogEintraegeAction()
	    {
	    	$obj_db_tags = new Application_Model_DbTable_Tags();
	    	$obj_db_blog = new Application_Model_DbTable_Blog();
	    	$obj_db_blog_tags = new Application_Model_DbTable_BlogTags();
	    	
	    	$obj_seo = new CAD_Seo();
	    	
	    	$a_tags_roh = $obj_db_tags->getTags();
	    	$a_tags = array();
	    	
	    	foreach($a_tags_roh as $a_tag)
	    	{
	    		array_push($a_tags, $a_tag['tag_id']);
	    	}
	    	
	    	$a_blog_tag_eintraege = array();
	    	
	    	for($i = 0; $i < 1000; ++$i)
	    	{
// 	    		$i_rnd_anzahl_tags = rand(1, count($a_tags) - 1);
	    		$i_rnd_anzahl_tags = rand(1, 10);
	    		$a_blog_tag_eintraege[$i] = array();
	    		
	    		for($j = 0; $j < $i_rnd_anzahl_tags; ++$j)
	    		{
	    			$i_rnd_pos_tag = rand(1, count($a_tags) - 1);
	    			if(!in_array($a_tags[$i_rnd_pos_tag], $a_blog_tag_eintraege[$i]))
	    			{
	    				array_push($a_blog_tag_eintraege[$i], $a_tags[$i_rnd_pos_tag]);
	    			}
	    		}
	    		
	    		$a_data = array();
	    		$a_data['blog_name'] = "Blog Dummy Eintrag " . $i;
	    		$obj_seo->setLinkName($a_data['blog_name']);
	    		$obj_seo->createSeoLink();
	    		$a_data['blog_seo_link'] = $obj_seo->getSeoName();
	    		$a_data['blog_text'] = "Das ist nur ein Dummytext für Dummy Eintrag " . $i;
	    		$a_data['blog_eintrag_datum'] = date("Y-m-d H:i:s");
	    		$a_data['blog_eintrag_user_fk'] = -1;
	    		
	    		$insert_id = $obj_db_blog->setBlogEintrag($a_data);
	    		
	    		for($j = 0; $j < count($a_blog_tag_eintraege[$i]); ++$j)
	    		{
		    		$a_data = array();
		    		$a_data['blog_tag_tag_fk'] = $a_blog_tag_eintraege[$i][$j];
		    		$a_data['blog_tag_blog_fk'] = $insert_id;
		    		$a_data['blog_tag_eintrag_datum'] = date("Y-m-d H:i:s");
		    		$a_data['blog_tag_eintrag_user_fk'] = -1;

		    		$obj_db_blog_tags->setBlogTag($a_data);
	    		}	
	    	}
	    }
	    
	    public function insertTagsAction()
	    {
	    	$obj_db_tags = new Application_Model_DbTable_Tags();
	    	
	    	$a_tags = array('PHP', 'PHP 5', 'PHP 4', 'Javascript', 'MySQL', 
	    					'Datenbank', 'Tag', 'Tags', 'Cloud', 'Clouds',
	    					'Wissenswert', 'Tip', 'C', 'C++', 'C#', 'jQuery',
	    					'Programmieren', 'Linux', 'Debian', 'Ubuntu', 'XBMC',
	    					'Multimedia', 'Video', 'Videobearbeitung', 'Perl',
	    					'Python', 'Shell', 'Bash', 'Zend Framework', 'Ajax',
	    					'CSS', 'HTML', 'HTML5', 'Git', 'SVN', 'Regex', 'Web',
	    					'FTP', 'Game');
	    	
	    	foreach($a_tags as $str_tag)
	    	{
	    		if(!$obj_db_tags->checkTagExists($str_tag))
	    		{
	    			$a_data = array();
	    			$a_data['tag_name'] = $str_tag;
	    			$a_data['tag_eintrag_datum'] = date("Y-m-d H:i:s");
	    			$a_data['tag_eintrag_user_fk'] = -1;
	    			
	    			$obj_db_tags->speichereTag($a_data);
	    		}
	    	}
	    }
	    
	    public function createWebThumbNailAction()
	    {	
	    	$a_params = $this->getRequest()->getParams();
	    	
	    	if(isset($a_params['url']))
	    	{
				$str_url = $a_params['url'];
				 
		    	error_reporting(E_ALL);
		    	ini_set('display_startup_errors', 1);
		    	ini_set('display_errors', 1);
		    	$root = getcwd() . '/../library/webthumbnail/';
		    	require $root.'/webthumbnail.php';
		    	
		    	$thumb = new Webthumbnail($str_url);
		    	$thumb
			    	->setWidth(320)
			    	->setHeight(240)
			    	->setScreen(1280)
			    	->captureToOutput(false);
	    	}
	    }
	    
	    public function postDispatch()
	    {
	    	$req = $this->getRequest();
	    	$a_params = $req->getParams();
	    	/*
	    	if(isset($a_params['ajax']))
	    	{
	    		$this->view->layout()->disableLayout();
	    	}
	    	*/
    		$this->view->layout()->disableLayout();
	    }
	}

