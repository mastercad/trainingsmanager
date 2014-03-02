<?php
	/**
	 * 
	 * @author Andreas Kempe / andreas.kempe@byte-artist.de
	 *
	 */

	class Vavg_Controller_Action extends Zend_Controller_Action
	{
		protected $firma_id;
		protected $aktueller_user_id;
		protected $user_rechte_gruppe_name;
		protected $firma_status;
		protected $user_status;
		protected $session_expiration_time;
		protected $session_time_left;
		protected $debug;
		protected $obj_user;
		
		public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
		{
			parent::__construct($request, $response, $invokeArgs);
		}
		
	    public function initialize()
	    {
	    	$req = $this->getRequest();
	    	
			$this->view->headLink()->appendStylesheet( '/css/global.css');
			
			$this->view->headScript()->appendFile('/js/jquery.min.js', 'text/javascript');
// 			$this->view->headScript()->appendFile('/js/jquery-ui.min.js', 'text/javascript');
			$this->view->headScript()->appendFile('/js/funktionen_jquery.js', 'text/javascript');
			$this->view->headScript()->appendFile('/js/blur.js', 'text/javascript');
			$this->view->headScript()->appendFile('/js/funktionen.js', 'text/javascript');
			
			$this->view->headTitle()->setSeparator(' | ');
	
			if (Vavg_Auth::getInstance()->hasIdentity())
			{
				$obj_user = Vavg_Auth::getInstance()->getIdentity();
				
				$module = $this->getRequest()->getModuleName();
				$session_expiration_time = SESSION_TIMEOUT;
				
				if(key_exists('__ZF', $_SESSION) &&
				   is_array($_SESSION['__ZF']) &&
				   key_exists('Zend_Auth', $_SESSION['__ZF']) &&
				   is_array($_SESSION['__ZF']['Zend_Auth']) &&
				   key_exists('ENVT', $_SESSION['__ZF']['Zend_Auth']) &&
				   is_array($_SESSION['__ZF']['Zend_Auth']['ENVT']) &&
				   key_exists('accept_answer', $_SESSION['__ZF']['Zend_Auth']['ENVT']))
				{
					$session_expiration_time = $_SESSION['__ZF']['Zend_Auth']['ENVT']['accept_answer'];
				}
				
				$this->session_expiration_time = $session_expiration_time;
				$this->session_time_left = $session_expiration_time - time();
				
	        	$this->firma_id = $obj_user->firma_id;
	        	$this->firma_status = $obj_user->firma_status_name;
				$this->aktueller_user_id = $obj_user->user_id;
				$this->user_rechte_gruppe_name = $obj_user->user_rechte_gruppe_name;
				$this->user_status = $obj_user->user_status_name;
				$this->obj_user = $obj_user;
				
				$this->view->assign('user_login', $obj_user->user_login);
				$this->view->assign('user_vorname', $obj_user->user_vorname);
				$this->view->assign('user_nachname', $obj_user->user_nachname);
				$this->view->assign('user_id', $this->aktueller_user_id);
				$this->view->assign('user_status', $this->user_status);
				$this->view->assign('user_rechte_gruppe_name', $this->user_rechte_gruppe_name);
				$this->view->assign('user_last_login', $obj_user->user_last_login);
				$this->view->assign('user_flag_logged_in', $obj_user->user_flag_logged_in);
				$this->view->assign('user_identifyed', true);
				$this->view->assign('firma_id', $this->firma_id);
				$this->view->assign('firma_name', $obj_user->firma_name);
				$this->view->assign('firma_status', $this->firma_status);
				$this->view->assign('firma_status_name', $this->firma_status);
				$this->view->assign('firma_uebid', $obj_user->firma_cc_uebid);
				$this->view->assign('firma_aussendienst_user_fk', $obj_user->firma_aussendienst_user_fk);
				
				$this->view->assign('session_expiration_time', $this->session_expiration_time);
				$this->view->assign('session_time_left', $this->session_time_left);
				
				$this->view->assign('module', $req->getModuleName());
				$this->view->assign('controller', $req->getControllerName());
				$this->view->assign('action', $req->getActionName());
				
		        $this->firma_header_bild_pfad = $obj_user->firma_header_bild_pfad;
    			
		        if(strtoupper($this->user_rechte_gruppe_name) == 'KONFIGURATOR')
		        {
		        	$this->b_extern = true;
					$this->view->assign('b_extern', $this->b_extern);
					$this->view->assign('extern', $this->b_extern);
		        }
		        
				if(
					$module == "default" &&
					(
						strtoupper($this->user_rechte_gruppe_name) == 'ADMIN' ||
				   		strtoupper($this->user_rechte_gruppe_name) == 'SUPERADMIN'
					)
				)
				{
					$this->view->headScript()->appendFile('/js/jquery-ui.min.js', 'text/javascript');
// 					$this->view->headScript()->appendFile('/js/selection.js', 'text/javascript');
					$this->view->headScript()->appendFile('/js/cms_query.js', 'text/javascript');
// 					$this->view->headScript()->appendFile('/js/jquery.contextmenu.r2.packed.js', 'text/javascript');
// 					$this->view->headScript()->appendFile('/js/jquery.contextmenu.r2.js', 'text/javascript');
		        }
		
				if($module == "user")
				{
					$this->view->headLink()->appendStylesheet('/css/user.css');
					if(file_exists(APPLICATION_PATH . '/../public/css/' . $this->firma_id . '/userdefined.css'))
					{
						$this->view->headLink()->appendStylesheet('/css/' . $this->firma_id . '/userdefined.css');
					}
					if(file_exists(getcwd() . $this->firma_header_bild_pfad) &&
					   is_file(getcwd() . $this->firma_header_bild_pfad) &&
					   is_readable(getcwd() . $this->firma_header_bild_pfad))
					{
						$this->view->assign('user_header', $this->firma_header_bild_pfad);	
					}
					else
					{
						$this->view->assign('user_header', '/images/content/statisch/user/header.jpg');
					}
				}
			}
	    }
	}	