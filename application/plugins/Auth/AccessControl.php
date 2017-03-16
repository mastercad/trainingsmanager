<?php

/**
 * Class Application_Plugin_Auth_AccessControl
 */
class Plugin_Auth_AccessControl extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var CAD_Auth|null
     */
    protected $_auth = null;
    /**
     * @var null|Zend_Acl
     */
    protected $_acl = null;
    /**
     * @var bool
     */
    protected $b_logged_in = false;
    /**
     * @var bool
     */
    protected $b_logged_out = false;
    /**
     * @var bool
     */
    protected $b_session_timed_out = false;
    /**
     * @var null
     */
    protected $a_messages = null;
    /**
     * @var int
     */
    protected $error_code = 1;

    /**
     * @param CAD_Auth $auth
     * @param Zend_Acl $acl
     */
    public function __construct(CAD_Auth $auth, Zend_Acl $acl)
    {
        $this->_auth = $auth;
        $this->_acl  = $acl;
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @throws Zend_Exception
     * @throws Zend_Session_Exception
     */
    public function	routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $obj_user = null;
        $username = '';
        $password = '';
        $result = '';

        $logger = Zend_Registry::get(ZEND_LOGGER);

        $zend_auth_namespace = new Zend_Session_Namespace('Zend_Auth');

        if($request->isPost() &&
           null !== $request->getPost('user_logout'))
        {
// 				$logger->info("Lösche Session!");

            unset($_SESSION['user_login']);
            unset($_SESSION['user_password']);

            $this->_auth->getStorage()->write(null);

            $this->_auth->clearIdentity();
            $_SESSION['__ZF']['Zend_Auth'] = null;

            $this->b_logged_in = false;
            $this->b_logged_out = true;

            Zend_Session::namespaceUnset('Zend_Auth_Ghost');
        }
        else if ($request->isPost() &&
                null !== $request->getPost('enc_user_login_name') &&
                null !== $request->getPost('enc_user_login_password'))
        {
// 				$logger->info("Habe Post ENC Login Daten!");

            if($this->_auth->hasIdentity())
            {
                $this->_auth->clearIdentity();
            }

            // POST-Daten bereinigen
            $filter = new Zend_Filter_StripTags();

            $username = $filter->filter(base64_decode($request->getPost('enc_user_login_name')));
            $password = $filter->filter(base64_decode($request->getPost('enc_user_login_password')));

            $this->b_logged_in = true;
        }
        else if ($request->isPost() &&
                 null !== $request->getPost('user_login_name') &&
                 null !== $request->getPost('user_login_password'))
        {
// 				$logger->info("Habe Post Login Daten!");

            if($this->_auth->hasIdentity())
            {
                $this->_auth->clearIdentity();
            }

            // POST-Daten bereinigen
            $filter = new Zend_Filter_StripTags();

            $username = $filter->filter($request->getPost('user_login_name'));
            $password = $filter->filter($request->getPost('user_login_password'));

            $this->b_logged_in = true;
        }
        else if(is_array($_SESSION) &&
            isset($_SESSION['user_login']) &&
            isset($_SESSION['user_password'])
        )
        {
// 				$logger->info("Habe login Daten in der Session! Lade sie!");

            $username = $_SESSION['user_login'];
            $password = $_SESSION['user_password'];
        }

        // wenn username gesetzt und unterschiedlich zur session,
        // session löschen
        if($this->_auth->hasIdentity() &&
           $username &&
            array_key_exists('user_login', $_SESSION) &&
           $_SESSION['user_login'] != $username)
        {
            unset($_SESSION['user_login']);
            unset($_SESSION['user_password']);

            $this->_auth->clearIdentity();
        }

        if ($this->b_logged_in &&
            empty($username))
        {
            array_push($this->a_messages, 'Bitte Benutzernamen angeben.');
            $this->error_code = -3;
        }
        if ($this->b_logged_in &&
            empty($password))
        {
            array_push($this->a_messages, 'Bitte Passwort angeben.');
            $this->error_code = -3;
        }

        if(!$this->_auth->hasIdentity() &&
            $username &&
            $password)
        {
// 				$logger->info("Habe keine Auth, aber Login und Passwort!");

            $authAdapter = new Plugin_Auth_AuthAdapter();

            $authAdapter->setIdentity($username);
            $authAdapter->setCredential($password);

            $result_auth = $authAdapter->authenticate();
            $this->error_code = $result_auth->getCode();

            $result = $authAdapter->getResultRowObject();

            if ($this->error_code == -3)
            {
                $this->a_messages[] = "Benutzer/Passwortkombination leider nicht bekannt!";

                $registry = Zend_Registry::getInstance();
                $view = $registry->view;
                $view->user_login = $username;
                $view->user_password = $password;
                $view->a_messages = $this->a_messages;
            }
            else if($this->error_code == -4)
            {
                $this->a_messages[] = 'Sie sind bereits angemeldet!';

                $registry = Zend_Registry::getInstance();
                $view = $registry->view;
                $view->user_login = $username;
                $view->user_password = $password;
                $view->user_email = $result->user_email;
                $view->user_id = $result->user_id;
                $view->user_validierungshash = $result->user_validierungshash;

                $view->a_messages = $this->a_messages;

            }
            else if($this->error_code == -1)
            {
                unset($_SESSION['user_login']);
                unset($_SESSION['user_password']);
            }
            else if(!$this->error_code)
            {
                $this->a_messages[] = "Ihr Login ist nicht aktiv!";

                $registry = Zend_Registry::getInstance();
                $view = $registry->view;
                $view->user_login = $username;
                $view->user_password = $password;
                $view->a_messages = $this->a_messages;
            }
            else if (!$result)
            {
                $this->a_messages[] = $result;

                unset($_SESSION['user_login']);
                unset($_SESSION['user_password']);

                $this->_auth->clearIdentity();
            }
            else
            {
                $this->_auth->getStorage()->write($result);
            }
        }

        if($this->_auth->hasIdentity() &&
           $this->b_logged_in)
        {
            $timeout = false;
            $obj_user = $this->_auth->getIdentity();

            if($obj_user instanceof stdClass)
            {
// 					echo "Eben eingeloggt ! ";
                $timeout = $obj_user->user_session_timeout ? $obj_user->user_session_timeout : $obj_user->user_rechte_gruppe_session_timeout;
            }

            if(!$timeout)
            {
// 					echo "Habe kein eigenes Timeout, setze Globales Timeout! ";
                $timeout = SESSION_TIMEOUT;
            }
// 				echo "Timeout : " . $timeout . "<br />";

            if(!Zend_Session::namespaceIsset('Zend_Auth_Ghost'))
            {
                $zend_auth_ghost_namespace = new Zend_Session_Namespace('Zend_Auth_Ghost');
                $zend_auth_ghost_namespace->create_date = date("Y-m-d H:i:s");
                $zend_auth_ghost_namespace->accept_answer = true;
                $zend_auth_ghost_namespace->hash = $_COOKIE['PHPSESSID'];
            }

            $zend_auth_namespace->setExpirationSeconds($timeout, 'accept_answer');
            $zend_auth_namespace->accept_answer = true;

            if( !$obj_user->user_last_login ||
                substr( $obj_user->user_last_login, 0, 10) == "0000-00-00")
            {
                $obj_user->user_last_login = date("Y-m-d H:i:s");
            }

            $this->_auth->getStorage()->write($obj_user);
        }

        // wenn authentifiziert und session noch existiert
        if($this->_auth->hasIdentity() &&
           $zend_auth_namespace->accept_answer === true)
        {
            $session_timeout = $_SESSION['__ZF']['Zend_Auth']['ENVT']['accept_answer'] - time();

            $obj_user = $this->_auth->getIdentity();

            if($obj_user instanceof stdClass &&
               $session_timeout &&
               $session_timeout < SESSION_TIMEOUT)
            {
                $zend_auth_namespace->setExpirationSeconds(SESSION_TIMEOUT, 'accept_answer');

                $a_data = array();
                $time = date("Y-m-d H:i:s");

                $a_data['user_last_login'] = $time;
                $a_data['user_update_date'] = $time;
                $a_data['user_update_user_fk'] = $obj_user->user_id;

                $obj_user->user_last_login = $time;

                $obj_db_users = new Model_DbTable_Users();
                $obj_db_users->updateUser($a_data, $obj_user->user_id);

                $this->_auth->getStorage()->write($obj_user);
            }
        }
        // wenn authentifiziert und session abgelaufen, dann abmelden erzwingen
        else if($this->_auth->hasIdentity() &&
                $zend_auth_namespace->accept_answer !== true)
        {
            $this->_auth->clearIdentity();
        }

        if(Zend_Session::namespaceIsset('Zend_Auth_Ghost'))
        {
            $zend_auth_ghost_namespace = new Zend_Session_Namespace('Zend_Auth_Ghost');

            if( $zend_auth_ghost_namespace->accept_answer === true &&
                $zend_auth_namespace->accept_answer !== true)
            {
                $this->b_session_timed_out = true;
            }
        }
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return bool
     * @throws Zend_Exception
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
		{
			$b_call_forward = false;
			$role = "gast";
			$logger = Zend_Registry::get(ZEND_LOGGER);
			$obj_user = null;
			$view = Zend_Registry::get('view');

// 			$registry = Zend_Registry::getInstance();
// 			$view = $registry->view;
// 			$view->a_messages = $this->a_messages;

// 			$logger->info("Logged in : " . $this->b_logged_in);
// 			$logger->info("Logged out : " . $this->b_logged_out);
			
			$module         = $request->getModuleName() ? $request->getModuleName() : 'default';
			$controller	= $request->getControllerName();
			$action 	= $request->getActionName();
			
            $view->assign('controller', $controller);
            $view->assign('action', $action);
            $view->assign('module', $module);

            return true;
			$a_params = $request->getParams();
			
			$url = '/';
				
			if($module != "default")
			{
				$url .= $module . '/';
			}
			if($controller != 'index')
			{
				$url .= $controller . '/';
			}
			if($action != 'index')
			{
				$url .= $action . '/';
			}
			
			/* check ob blog */
			if($module == "default" &&
			   $controller == "blog" &&
			   preg_match('/\d{4}/', $action))
			{
				$action = "index";
			}
			
// 			$logger->info("Modul : " . $module);
// 			$logger->info("Controller : " . $controller);
// 			$logger->info("Action : " . $action);
			
			if($this->error_code < 1)
			{
				if(!isset($a_params['ajax']))
				{
					$request->setModuleName('default');
					$request->setControllerName('error');
					$request->setActionName('login-fail');
				}
// 				$logger->info("Login Fail!");
				
				$view->a_messages = $this->a_messages;
				
				return false;
			}
			
			if ($this->_auth->hasIdentity())
			{
				$obj_user = $this->_auth->getStorage()->read();
				
				if(!$obj_user instanceof stdClass)
				{
// 					$logger->info("Habe defektes user Object! lösche es!");
					$obj_user = null;
					$this->_auth->clearIdentity();
					return false;
				}
				else
				{
					$role = $obj_user->user_rechte_gruppe_name;
				}
			}
			
// 			$logger->info($this->_auth);
// 			$logger->info($_SESSION);
// 			$logger->info("Noch Identifiziert? : " . $this->_auth->hasIdentity());
			
			if($this->_auth->hasIdentity())
			{
// 				$logger->info("Habe noch Identität!");
				if ($this->_acl->has($module . ':'))
				{
					$resource = $module . ':';
				}
				// Ist in der ACL als Ressource das Modul+Controller konfiguriert?
				else if ($this->_acl->has($module . ':' . $controller))
				{
					$resource = $module . ':' . $controller;
				}
				// Gar nichts von beidem!
				else
				{
					$resource = null;
				}
				
				$module_erlaubt = $this->_acl->isAllowed($role, $module . ':index', '*');
				$controller_erlaubt = $this->_acl->isAllowed($role, $resource, '*');
				$action_erlaubt = $this->_acl->isAllowed($role, $resource, $action);
				
				/* _call catches */
				if($module == "default" &&
					(
						$controller == "scripte" ||
						$controller == "schnipsel" ||
						$controller == "projekte"
					)
				)
				{
					$test_request = new Zend_Controller_Request_Http();
					$test_request->setParams(array(
							'action'     => $action,
							'controller' => $controller,
							'module'     => $module
						)
					);
					
					$dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
					
					$class  = $dispatcher->loadClass($dispatcher->getControllerClass($test_request));
					$method = $dispatcher->formatActionName($test_request->getActionName());
					
					if($dispatcher->isDispatchable($test_request) &&
					   false === is_callable(array($class, $method)))
					{
// 						$logger->info("Kann weiterleiten, habe aber keine Action, nehme __call an!");
						$b_call_forward = true;
						$action_erlaubt = $this->_acl->isAllowed($role, $resource, '__call');
					}
				}
				
// 				$logger->info("Modul erlaubt? : " . $module_erlaubt);
// 				$logger->info("Controller erlaubt? : " . $controller_erlaubt);
// 				$logger->info("Action erlaubt? : " . $action_erlaubt);
// 				$logger->info("Role : " . $role);
				
				if($this->b_session_timed_out &&
				   !$this->b_logged_in)
				{
					$this->a_messages[] = "Ihre Session is abgelaufen!";
				}
				
				if (!$action_erlaubt &&
				    !$controller_erlaubt &&
					!isset($a_params['ajax']))
				{
					$request->setModuleName('default');
					$request->setControllerName('error');
					$request->setActionName('no-access');
					
					$this->a_messages[] = "Bitte loggen Sie sich ein, um diese Seite angezeigt zu bekommen!";
// 					$logger->info("Keine Auth und keine Berechtigung für controller!");
				}
				else if(true === $this->b_logged_in &&
						!isset($a_params['ajax']) &&
					    $this->_auth->hasIdentity())
				{
					$redirect = new Zend_Controller_Action_Helper_Redirector;
					$redirect->gotoUrl($url, $a_params)->redirectAndExit();
					
// 					$query = http_build_query($data);
// 					$redirect->gotoUrlAndExit($url . '?' . $query);
				}
				$view->a_messages = $this->a_messages;
			}
				
			if(true === $this->b_logged_out)
			{
				if(!isset($a_params['ajax']))
				{
// 					echo "<pre>";
// 					print_r($a_params);
// 					echo "</pre>";
					
// 					echo "Leiter weiter auf orig!";
					
					unset($a_params['user_logout']);
					$this->b_logged_out = false;
					
					$redirect = new Zend_Controller_Action_Helper_Redirector;
					$redirect->gotoUrl($url, $a_params)->redirectAndExit();

// 					$view->_helper->redirector->gotoUrl($url, $a_params);

// 					$this->_redirect($url);

// 					$query = http_build_query($data);
// 					$redirect->gotoUrlAndExit($url . '?' . $query);
				}
			}
		}
	}
