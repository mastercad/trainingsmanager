<?php
	/*
	 * liste aller links in, nur nötigste parameter pro url
	 * http://atlchris.com/1665/how-to-create-custom-share-buttons-for-all-the-popular-social-services/
	 * 
	 * 
	 * 
	 */

	class CAD_SocialMedia
	{
		protected $view;
		protected $str_url;
		protected $str_title;
		protected $str_description;
		protected $str_keywords;
		protected $str_images;
		protected $str_image;
		protected $str_formated_link;
		protected $a_images;
		
		protected $str_facebook_url;
		protected $str_facebook_images;
		protected $str_twitter_url;
		protected $str_stumbleupon_url;
		protected $str_google_plus_url;
		protected $str_pinterest_url;
		protected $str_reddit_url;
		protected $str_linked_in_url;
		protected $str_delicious_url;
		protected $str_body_attributes;
		protected $str_html_attributes;
		protected $str_head_attributes;
		
		public function __construct(Zend_View &$view = null)
		{
			if($view)
			{
				$this->setView($view);
			}
			
			return $this;
		}
		
		public function setView(Zend_View $view)
		{
			$this->view = $view;
			
			return $this;
		}
		
		public function getView()
		{
			return $this->view;
		}
		
		public function setUrl($str_url)
		{
			$this->str_url = $str_url;
			
			return $this;
		}
		
		public function getUrl()
		{
			return $this->str_url;
		}
		
		public function setFormatedLink($str_formated_link)
		{
			$this->str_formated_link = trim($str_formated_link);
			
			return $this;
		}
		
		public function addToFormatedLink($str_formated_link_piece)
		{
			if(!strlen(trim($this->str_formated_link)))
			{
				$this->setFormatedLink($str_formated_link_piece);
			}
			else
			{
				$this->str_formated_link .= trim($str_formated_link_piece);
			}
			
			return $this;
		}
		
		public function getFormatedLink()
		{
			return $this->str_formated_link;
		}
		
		public function setTitle($str_title)
		{
			$this->str_title = trim($str_title);
			
			return $this;
		}
		
		public function getTitle()
		{
			return $this->str_title;
		}
		
		public function setDescription($str_description)
		{
			$this->str_description = trim($str_description);
			
			return $this;
		}
		
		public function getDescription()
		{
			return $this->str_description;
		}
		
		public function setKeywords($str_keywords)
		{
			$this->str_keywords = trim($str_keywords);
		}
		
		public function getKeywords()
		{
			return $this->str_keywords;
		}
		
		public function setImage($str_image)
		{
			$this->str_image = trim($str_image);
			
			return $this;
		}
		
		public function getImage()
		{
			return $this->str_image;
		}
		
		public function setImages($a_images)
		{
			$this->a_images = $a_images;
			
			return $this;
		}
		
		public function getImages()
		{
			return $this->a_images;
		}
		
		public function setFacebookImagesString($str_facebook_images)
		{
			$this->str_facebook_images = trim($str_facebook_images);
			
			return $this;
		}
		
		public function getFacebookImagesString()
		{
			if(!strlen(trim($this->str_facebook_images)))
			{
				$this->generateFacebookImagesString();
			}
			return $this->str_facebook_images;
		}
		
		public function getFacebookUrl()
		{
			if(!strlen(trim($this->str_facebook_url)))
			{
				$this->generateFacebookUrl();
			}
			return $this->encodeUrl($this->str_facebook_url);
		}
		
		public function setFacebookUrl($str_facebook_url)
		{
			$this->str_facebook_url = trim($str_facebook_url);
			
			return $this;
		}
		
		public function setGooglePlusUrl($str_google_plus_url)
		{
			$this->str_google_plus_url = trim($str_google_plus_url);
			
			return $this;
		}
		
		public function getGooglePlusUrl()
		{
			if(!strlen(trim($this->str_google_plus_url)))
			{
				$this->generateGooglePlusUrl();
			}
			return $this->encodeUrl($this->str_google_plus_url);
		}
		
		public function setTwitterUrl($str_twitter_url)
		{
			$this->str_twitter_url = trim($str_twitter_url);
			
			return $this;
		}
		
		public function getTwitterUrl()
		{
			if(!strlen(trim($this->str_twitter_url)))
			{
				$this->generateTwitterUrl();
			}
			return $this->encodeUrl($this->str_twitter_url);
		}
		
		public function setStumbleuponUrl($str_stumbleupon_url)
		{
			$this->str_stumbleupon_url = trim($str_stumbleupon_url);
			
			return $this;
		}
		
		public function getStumbleuponUrl()
		{
			if(!strlen(trim($this->str_stumbleupon_url)))
			{
				$this->generateStumbleuponUrl();
			}
			return $this->encodeUrl($this->str_stumbleupon_url);
		}
		
		public function setLinkedInUrl($str_linked_in_url)
		{
			$this->str_linked_in_url = trim($str_linked_in_url);
			
			return $this;
		}
		
		public function getLinkedInUrl()
		{
			if(!strlen(trim($this->str_linked_in_url)))
			{
				$this->generateLinkedInUrl();
			}
			return $this->encodeUrl($this->str_linked_in_url);
		}
		
		public function setDeliciousUrl($str_delicious_url)
		{
			$this->str_delicious_url = trim($str_delicious_url);
			
			return $this;
		}
		
		public function getDeliciousUrl()
		{
			if(!strlen(trim($this->str_delicious_url)))
			{
				$this->generateDeliciousUrl();
			}
			return $this->encodeUrl($this->str_delicious_url);
		}
		
		public function setPinterestUrl($str_pinterest_url)
		{
			$this->str_pinterest_url = trim($str_pinterest_url);
			
			return $this;
		}
		
		public function getPinterestUrl()
		{
			if(!strlen(trim($this->str_pinterest_url)))
			{
				$this->generatePinterestUrl();
			}
			return $this->encodeUrl($this->str_pinterest_url);
		}
		
		public function setRedditUrl($str_reddit_url)
		{
			$this->str_reddit_url = trim($str_reddit_url);
			
			return $this;
		}
		
		public function getRedditUrl()
		{
			if(!strlen(trim($this->str_reddit_url)))
			{
				$this->generateRedditUrl();
			}
			return $this->encodeUrl($this->str_reddit_url);
		}
		
		public function setBodyAttributes($str_body_attributes)
		{
			$this->str_body_attributes = trim($str_body_attributes);
			
			return $this;
		}
		
		public function getBodyAttributes()
		{
			return $this->str_body_attributes;
		}
		
		public function setHtmlAttributes($str_html_attributes)
		{
			$this->str_html_attributes = trim($str_html_attributes);
			
			return $this;
		}
		
		public function getHtmlAttributes()
		{
			return $this->str_html_attributes;
		}
		
		public function setHeadAttributes($str_head_attributes)
		{
			$this->str_head_attributes = trim($str_head_attributes);
			
			return $this;
		}
		
		public function getHeadAttributes()
		{
			return $this->str_head_attributes;
		}
		
		
		public function prepare()
		{
			if(isset($a_projekt['projekt_name']))
			{
				$a_social_media['title'] = urlencode($a_projekt['projekt_name']);
			}
			
			$this->generateFacebookImagesString();
			$this->generateFacebookUrl();
			
			if(preg_match('/facebook/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$this->prepareViewForFacebook();
			}
			
			if(preg_match('/google/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$this->prepareViewForGooglePlus();
			}
			
			if(preg_match('/linkedin/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$this->prepareViewForLinkedIn();
			}
			
			if(preg_match('/twitter/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$this->prepareViewForTwitter();
			}
			
			if(preg_match('/stumbleupon/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$this->prepareViewForStumbleupon();
			}
			
			if(preg_match('/delicious/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$this->prepareViewForDelicious();
			}
			
			if(preg_match('/reddit/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$this->prepareViewForReddit();
			}
		}
		
		public function generateFacebookUrl()
		{
       		/* facebook */
       		/*
       		$facebook_url = 'http://www.facebook.com/sharer.php?' . urlencode('s=100' .
       		'&p[url]=' . $view->obj_social_media->getUrl() .
       		$view->obj_social_media->getImages() .
       		'&p[title]=' . $obj_social_media['title'] .
       		'&p[summary]=' . utf8_encode($obj_social_media['description']));
       		*/
       		
			$facebook_url = 'http://www.facebook.com/sharer.php?' . 's=100' .
					'&p[url]=' . $this->getUrl() .
					$this->getFacebookImagesString() .
					'&p[title]=' .$this->getTitle() .
					'&p[summary]=' . $this->getDescription();
			
			$this->setFacebookUrl($facebook_url);
			
			return $this;
		}
		
		public function generateFacebookImagesString()
		{
			$a_images = $this->getImages();
				
			if(is_array($a_images) &&
					count($a_images) > 0)
			{
				$count = 0;
				$str_facebook_images = '';
			
// 	  	   		$str_facebook_images .= '&p[images][' . $count . ']=http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . base64_encode(getcwd() . '/images/content/dynamisch/projekte/' . $a_projekt['projekt_id'] . '/' . $a_projekt['projekt_vorschaubild']) . '/width/100';
				$str_facebook_images .= '&p[images][' . $count . ']=' . $this->getImage();
			
				$count++;
			
				foreach($a_images as $a_image)
				{
    				$str_facebook_images .= '&p[images][' . $count . ']=http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . (base64_encode($a_image[CAD_File::SYS_PFAD])) . '/width/250/height/250';
// 					$str_facebook_images .= '&p[images][' . $count . ']=http://' . $_SERVER['SERVER_NAME'] . $a_image[CAD_File::HTML_PFAD];
					$count++;
				}
				$this->setFacebookImagesString($str_facebook_images);
			}
		}
		
		public function prepareViewForFacebook()
		{
			if(!$this->getView())
			{
				echo "View nicht gesetzt!<br />";
				return false;
			}
			$view = $this->getView();
			$view->doctype('XHTML1_RDFA');
			
			$view->headMeta()->appendProperty('og:url', $this->getUrl());
			$view->headMeta()->appendProperty('og:title', $this->getTitle());
			$view->headMeta()->appendProperty('og:description', $this->getDescription());
			$view->headMeta()->appendProperty('og:keywords', $this->getKeywords());
// 			$view->headMeta()->appendProperty('og:country-name', 'Germany');
			$view->headMeta()->appendProperty('og:site_name', $this->getTitle());
			
			$view->headMeta()->appendProperty('fb:admins', '100001360008435');
// 			$view->headMeta()->appendProperty('fb:page_id', '198506923607446');
			
// 			$this->setBodyAttributes(' xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/"');
			$this->setHtmlAttributes(' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"');
			$this->setHeadAttributes('  profile="http://gmpg.org/xfn/11" ');
			
			$a_images = $this->getImages();
			
			if(is_array($a_images))
			{
				foreach($a_images as $a_image)
				{
					$a_fileinformationen = getimagesize($a_image[CAD_File::SYS_PFAD]);
					
					$view->headMeta()->appendProperty('og:image', 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . (base64_encode($a_image[CAD_File::SYS_PFAD])) . '/width/250/height/250');
// 					$view->headMeta()->appendName('og:image:width', $a_fileinformationen[0]);
// 					$view->headMeta()->appendName('og:image:height', $a_fileinformationen[1]);
					$view->headMeta()->appendProperty('og:image:width', "250");
					$view->headMeta()->appendProperty('og:image:height', "250");
				}
			}
			
			return $this;
		}
		
		public function generateGooglePlusUrl()
		{
			$str_google_plus_url = 'https://plus.google.com/share?url=' . $this->getUrl() .
							   '&title=' . $this->getTitle();
			
			$this->setGooglePlusUrl($str_google_plus_url);
			
			return $this;
		}
		
		public function prepareViewForGooglePlus()
		{
			if(!$this->getView())
			{
				echo "View nicht gesetzt!<br />";
				return false;
			}
			$view = $this->getView();
			$view->doctype('XHTML1_RDFA');

			$view->headMeta()->appendProperty('og:url', $this->getUrl());
			$view->headMeta()->appendProperty('og:title', $this->getTitle());
			$view->headMeta()->appendProperty('og:description', $this->getDescription());
			$view->headMeta()->appendProperty('og:keywords', $this->getKeywords());
			$view->headMeta()->appendProperty('og:country-name', 'Germany');
			$view->headMeta()->appendProperty('og:site_name', $this->getTitle());

    		$view->headMeta()->appendProperty('author-link', 'https://plus.google.com/u/0/100952657106943880213');

//     		$this->setBodyAttributes(' xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/"');
			$this->setHtmlAttributes(' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"');
    		$this->setHeadAttributes('  profile="http://gmpg.org/xfn/11" ');
			
			$a_images = $this->getImages();
			
			if(is_array($a_images))
			{
				foreach($a_images as $a_image)
				{
					$a_fileinformationen = getimagesize($a_image[CAD_File::SYS_PFAD]);
					
					$view->headMeta()->appendProperty('og:image', 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . (base64_encode($a_image[CAD_File::SYS_PFAD])) . '/width/250/height/250');
// 					$view->headMeta()->appendName('og:image:width', $a_fileinformationen[0]);
// 					$view->headMeta()->appendName('og:image:height', $a_fileinformationen[1]);
					$view->headMeta()->appendProperty('og:image:width', "250");
					$view->headMeta()->appendProperty('og:image:height', "250");
				}
			}
			
			return $this;
		}
		
		public function prepareViewForLinkedIn()
		{
			if(!$this->getView())
			{
				echo "View nicht gesetzt!<br />";
				return false;
			}
			$view = $this->getView();
			$view->doctype('XHTML1_RDFA');

			$view->headMeta()->appendProperty('og:url', $this->getUrl());
			$view->headMeta()->appendProperty('og:title', $this->getTitle());
			$view->headMeta()->appendProperty('og:description', $this->getDescription());
			$view->headMeta()->appendProperty('og:keywords', $this->getKeywords());
			$view->headMeta()->appendProperty('og:country-name', 'Germany');
			$view->headMeta()->appendProperty('og:site_name', $this->getTitle());

    		$view->headMeta()->appendProperty('author-link', 'https://plus.google.com/u/0/100952657106943880213');

//     		$this->setBodyAttributes(' xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/"');
			$this->setHtmlAttributes(' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"');
    		$this->setHeadAttributes('  profile="http://gmpg.org/xfn/11" ');
			
			$a_images = $this->getImages();
			
			if(is_array($a_images))
			{
				foreach($a_images as $a_image)
				{
					$a_fileinformationen = getimagesize($a_image[CAD_File::SYS_PFAD]);
					
					$view->headMeta()->appendProperty('og:image', 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . (base64_encode($a_image[CAD_File::SYS_PFAD])) . '/width/250/height/250');
// 					$view->headMeta()->appendName('og:image:width', $a_fileinformationen[0]);
// 					$view->headMeta()->appendName('og:image:height', $a_fileinformationen[1]);
					$view->headMeta()->appendProperty('og:image:width', "250");
					$view->headMeta()->appendProperty('og:image:height', "250");
				}
			}
			
			return $this;
		}
		
		public function prepareViewForTwitter()
		{
			if(!$this->getView())
			{
				echo "View nicht gesetzt!<br />";
				return false;
			}
			$view = $this->getView();
			$view->doctype('XHTML1_RDFA');

			$view->headMeta()->appendName('twitter:card', "summary");
			$view->headMeta()->appendName('twitter:site', "@byte_artist");
			$view->headMeta()->appendName('twitter:image', $this->getImage());
			$view->headMeta()->appendName('twitter:description', $this->getDescription());
			$view->headMeta()->appendName('twitter:keywords', $this->getKeywords());
			$view->headMeta()->appendName('twitter:title', $this->getTitle());
			$view->headMeta()->appendName('twitter:url', $this->getUrl());

    		$view->headMeta()->appendName('author-link', 'https://plus.google.com/u/0/100952657106943880213');

//     		$this->setBodyAttributes(' xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/"');
// 			$this->setHtmlAttributes(' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"');
//     		$this->setHeadAttributes('  profile="http://gmpg.org/xfn/11" ');
			/*
			$a_images = $this->getImages();
			
			if(is_array($a_images))
			{
				foreach($a_images as $a_image)
				{
					$a_fileinformationen = getimagesize($a_image[CAD_File::SYS_PFAD]);
					
					$view->headMeta()->appendProperty('og:image', 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . (base64_encode($a_image[CAD_File::SYS_PFAD])) . '/width/250/height/250');
// 					$view->headMeta()->appendName('og:image:width', $a_fileinformationen[0]);
// 					$view->headMeta()->appendName('og:image:height', $a_fileinformationen[1]);
					$view->headMeta()->appendProperty('og:image:width', "250");
					$view->headMeta()->appendProperty('og:image:height', "250");
				}
			}
			*/
			return $this;
		}
		
		public function prepareViewForStumbleupon()
		{
			if(!$this->getView())
			{
				echo "View nicht gesetzt!<br />";
				return false;
			}
			$view = $this->getView();
			$view->doctype('XHTML1_RDFA');

			$view->headMeta()->appendProperty('og:url', $this->getUrl());
			$view->headMeta()->appendProperty('og:title', $this->getTitle());
			$view->headMeta()->appendProperty('og:description', $this->getDescription());
			$view->headMeta()->appendProperty('og:keywords', $this->getKeywords());
			$view->headMeta()->appendProperty('og:country-name', 'Germany');
			$view->headMeta()->appendProperty('og:site_name', $this->getTitle());

    		$view->headMeta()->appendProperty('author-link', 'https://plus.google.com/u/0/100952657106943880213');

//     		$this->setBodyAttributes(' xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/"');
			$this->setHtmlAttributes(' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"');
    		$this->setHeadAttributes('  profile="http://gmpg.org/xfn/11" ');
			
			$a_images = $this->getImages();
			
			if(is_array($a_images))
			{
				foreach($a_images as $a_image)
				{
					$a_fileinformationen = getimagesize($a_image[CAD_File::SYS_PFAD]);
					
					$view->headMeta()->appendProperty('og:image', 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . (base64_encode($a_image[CAD_File::SYS_PFAD])) . '/width/250/height/250');
// 					$view->headMeta()->appendName('og:image:width', $a_fileinformationen[0]);
// 					$view->headMeta()->appendName('og:image:height', $a_fileinformationen[1]);
					$view->headMeta()->appendProperty('og:image:width', "250");
					$view->headMeta()->appendProperty('og:image:height', "250");
				}
			}
			
			return $this;
		}
		
		public function prepareViewForDelicious()
		{
			if(!$this->getView())
			{
				echo "View nicht gesetzt!<br />";
				return false;
			}
			$view = $this->getView();
			$view->doctype('XHTML1_RDFA');

			$view->headMeta()->appendProperty('og:url', $this->getUrl());
			$view->headMeta()->appendProperty('og:title', $this->getTitle());
			$view->headMeta()->appendProperty('og:description', $this->getDescription());
			$view->headMeta()->appendProperty('og:keywords', $this->getKeywords());
			$view->headMeta()->appendProperty('og:country-name', 'Germany');
			$view->headMeta()->appendProperty('og:site_name', $this->getTitle());

    		$view->headMeta()->appendProperty('author-link', 'https://plus.google.com/u/0/100952657106943880213');

//     		$this->setBodyAttributes(' xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/"');
			$this->setHtmlAttributes(' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"');
    		$this->setHeadAttributes('  profile="http://gmpg.org/xfn/11" ');
			
			$a_images = $this->getImages();
			
			if(is_array($a_images))
			{
				foreach($a_images as $a_image)
				{
					$a_fileinformationen = getimagesize($a_image[CAD_File::SYS_PFAD]);
					
					$view->headMeta()->appendProperty('og:image', 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . (base64_encode($a_image[CAD_File::SYS_PFAD])) . '/width/250/height/250');
// 					$view->headMeta()->appendName('og:image:width', $a_fileinformationen[0]);
// 					$view->headMeta()->appendName('og:image:height', $a_fileinformationen[1]);
					$view->headMeta()->appendProperty('og:image:width', "250");
					$view->headMeta()->appendProperty('og:image:height', "250");
				}
			}
			
			return $this;
		}
		
		public function prepareViewForReddit()
		{
			if(!$this->getView())
			{
				echo "View nicht gesetzt!<br />";
				return false;
			}
			$view = $this->getView();
			$view->doctype('XHTML1_RDFA');

			$view->headMeta()->appendProperty('og:url', $this->getUrl());
			$view->headMeta()->appendProperty('og:title', $this->getTitle());
			$view->headMeta()->appendProperty('og:description', $this->getDescription());
			$view->headMeta()->appendProperty('og:keywords', $this->getKeywords());
			$view->headMeta()->appendProperty('og:country-name', 'Germany');
			$view->headMeta()->appendProperty('og:site_name', $this->getTitle());

    		$view->headMeta()->appendProperty('author-link', 'https://plus.google.com/u/0/100952657106943880213');

//     		$this->setBodyAttributes(' xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/"');
			$this->setHtmlAttributes(' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"');
    		$this->setHeadAttributes('  profile="http://gmpg.org/xfn/11" ');
			
			$a_images = $this->getImages();
			
			if(is_array($a_images))
			{
				foreach($a_images as $a_image)
				{
					$a_fileinformationen = getimagesize($a_image[CAD_File::SYS_PFAD]);
					
					$view->headMeta()->appendProperty('og:image', 'http://' . $_SERVER['SERVER_NAME'] . '/butler/create-thumb/file/' . (base64_encode($a_image[CAD_File::SYS_PFAD])) . '/width/250/height/250');
// 					$view->headMeta()->appendName('og:image:width', $a_fileinformationen[0]);
// 					$view->headMeta()->appendName('og:image:height', $a_fileinformationen[1]);
					$view->headMeta()->appendProperty('og:image:width', "250");
					$view->headMeta()->appendProperty('og:image:height', "250");
				}
			}
			
			return $this;
		}
		
		public function generateLinkedInUrl()
		{
			/*
			 * http://www.linkedin.com/shareArticle?mini=true&
			 * 		url=http%3A%2F%2Fthenextweb.com%2Ffacebook%2F2013%2F03%2F08%2Fa-deep-dive-into-
			 * 			facebooks-news-feed-redesign-shows-nothing-but-net%2F%3F
			 * 			utm_source%3DLinkedin%26
			 * 			utm_medium%3Dshare%252Bbutton%26
			 * 			utm_content%3DA%2520deep%2520dive%2520into%2520Facebook
			 * 				%25E2%2580%2599s%2520News%2520Feed%2520redesign%253A%2520
			 * 				Success%252C%2520and%2520only%2520a%2520few%2520party%2520
			 * 				fouls%26
			 *			utm_campaign%3Dsocial%252Bmedia&
			 *		title=A+deep+dive+into+Facebook
			 *			%E2%80%99s+News+Feed+redesign%3A+Success%2C+and+only+a+few+party+fouls
			 * 		&summary=
			 * 
			 */
			$str_linked_in = 'http://www.linkedin.com/shareArticle?mini=true' .
       						 '&url=' . $this->getUrl() .
       						 '&title=' . $this->getTitle() .
       						 '&source=http://' . $_SERVER['SERVER_NAME'];
			
			$this->setLinkedInUrl($str_linked_in);
			
			return $this;
		}
		
		public function generateTwitterUrl()
		{
       		/* twitter */
			/*
       		url – URL of the page to share
       		via – Screen name of the user to attribute the Tweet to
       		text – Default Tweet text
       		related – Related accounts
       		count – Count box position
       		lang – The language for the Tweet Button
       		counturl – The URL to which your shared URL resolves to
       		
       		https://dev.twitter.com/docs/intents
       		https://dev.twitter.com/docs/tweet-button
       		http://support.sharethis.com/customer/portal/articles/475079-share-properties-and-sharing-custom-information#sthash.t9i5GLzD.dpbs
       		http://twitter.com/share?url=http://www.djavupixel.com/design/wallpapers/stunning-apple-ipad/&text=tutorial for own Twitter button by @djavupixel&count=horiztonal
       		
       		*/
       		
			$str_twitter_url = 'http://twitter.com/share?url=' . $this->getUrl() .
       					   	   '&image=' . $this->getImage() .
   					   		   '&text=' . $this->getDescription() .
       					   	   '&via=byte_artist';
			
			$this->setTwitterUrl($str_twitter_url);
			
			return $this;
		}
		
		public function generatePinterestUrl()
		{
			$str_pinterest_url = 'http://pinterest.com/pin/create/bookmarklet/?media=' . $this->getImage() .
       						 	 '&url=' . $this->getUrl() .
       						 	 '&is_video=false' .
       						 	 '&description=' . $this->getTitle();
			
			$this->setPinterestUrl($str_pinterest_url);
			
			return $this;
		}
		
		public function generateStumbleuponUrl()
		{
			$str_stumbleupon_url = 'http://www.stumbleupon.com/submit?url=' . $this->getUrl() .
       						     '&title=' . $this->getTitle();
			
			$this->setStumbleuponUrl($str_stumbleupon_url);
			
			return $this;
		}
		
		public function generateRedditUrl()
		{
			$str_reddit_url = 'http://www.reddit.com/submit?url=' . $this->getUrl() . 
       					  	  '&title=' . $this->getTitle();
			
			$this->setRedditUrl($str_reddit_url);
			
			return $this;
		}
		
		public function generateDeliciousUrl()
		{
			$str_delicious_url = 'http://del.icio.us/post?url=' . $this->getUrl() .
       						 	 '&title=' . $this->getTitle() . 
       						 	 '&notes=' . $this->getDescription();
			
			$this->setDeliciousUrl($str_delicious_url);
			
			return $this;
		}
		
		public function encodeUrl($string)
		{
// 			$entities = 	array(	'!', 	'*', 	"'", 	"(", 	")", 	";", 	":", 	"@", 	"&", 	"=", 	"+", 	"$", 	",", 	"/", 	"?", 	"%", 	"#", 	"[", 	"]");
// 			$replacements = array(	'%21', '%2A', 	'%27', 	'%28', 	'%29', 	'%3B', 	'%3A', 	'%40', 	'%26', 	'%3D', 	'%2B', 	'%24', 	'%2C', 	'%2F', 	'%3F', 	'%25', 	'%23', 	'%5B', 	'%5D');

			$entities = 	array(	"&", 		"[", 	"]", 	" ");
			$replacements = array(	'&amp;', 	'%5B', 	'%5D', 	"+");
			
    		return str_replace($entities, $replacements, $string);
		}
		
		public function validateUrl($str_url)
		{
			$new_url = htmlspecialchars($str_url);
			$new_url = preg_replace('/ /i', '+', $new_url);
			
			return $new_url;
		}
	}