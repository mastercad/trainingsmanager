<?php

class Auth_Plugin_AccessControl extends Zend_Controller_Plugin_Abstract
{
    /** @var Auth_Service_Auth|null Auth_Service_Auth */
    protected $_auth = null;

    /** @var null|Zend_Acl  */
    protected $_acl = null;
    
//    protected $b_logged_in = false;
    protected $b_logged_out = false;
    protected $b_session_timed_out = false;
    protected $a_messages = null;
    protected $error_code = 1;

    protected $_module;
    protected $_action;
    protected $_controller;
    protected $_currentRole = 'guest';

    public function __construct(Auth_Service_Auth $auth, Zend_Acl $acl)
    {
        $this->_auth = $auth;
        $this->_acl  = $acl;
    }

    public function	routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $obj_user = null;
        $username = '';
        $password = '';

//        $logger = Zend_Registry::get(ZEND_LOGGER);

        $zend_auth_namespace = new Zend_Session_Namespace('Zend_Auth');

        if ($request->isPost()
            && null !== $request->getPost('user_logout')
        ) {
            unset($_SESSION['user_login']);
            unset($_SESSION['user_password']);

            $this->_auth->getStorage()->write(null);

            $this->_auth->clearIdentity();
            $_SESSION['__ZF']['Zend_Auth'] = null;

            $this->_auth->b_logged_in = false;
            $this->b_logged_out = true;

            Zend_Session::namespaceUnset('Zend_Auth_Ghost');
        } else if ($request->isPost()
            && null !== $request->getPost('enc_user_login_name')
            && null !== $request->getPost('enc_user_login_password')
        ) {
            if ($this->_auth->hasIdentity()) {
                $this->_auth->clearIdentity();
            }

            // POST-Daten bereinigen
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter(base64_decode($request->getPost('enc_user_login_name')));
            $password = $filter->filter(base64_decode($request->getPost('enc_user_login_password')));

            $this->_auth->b_logged_in = true;
        } else if ($request->isPost()
            && null !== $request->getPost('user_login_name')
            && null !== $request->getPost('user_login_password')
        ) {
            if ($this->_auth->hasIdentity()) {
                $this->_auth->clearIdentity();
            }

            // POST-Daten bereinigen
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter($request->getPost('user_login_name'));
            $password = $filter->filter($request->getPost('user_login_password'));

            $this->_auth->b_logged_in = true;
        } else if (is_array($_SESSION)
            && array_key_exists('user_login', $_SESSION)
            && array_key_exists('user_password', $_SESSION)
        ) {
            $username = $_SESSION['user_login'];
            $password = $_SESSION['user_password'];
        }

        // wenn username gesetzt und unterschiedlich zur session,
        // session löschen
        if ($this->_auth->hasIdentity()
            && $username
            && array_key_exists('user_login', $_SESSION)
            && $_SESSION['user_login'] != $username
        ) {
            unset($_SESSION['user_login']);
            unset($_SESSION['user_password']);

            $this->_auth->clearIdentity();
        }

        if ($this->_auth->b_logged_in
            && empty($username)
        ) {
            $this->a_messages[] = 'Bitte Benutzernamen angeben.';
            $this->error_code = -3;
        }
        if ($this->_auth->b_logged_in
            && empty($password)
        ) {
            $this->a_messages[] = 'Bitte password angeben.';
            $this->error_code = -3;
        }

        // $logger->info("Habe keine Auth, aber Login und password!");
        if (!$this->_auth->hasIdentity()
            && $username
            && $password
        ) {
            $authAdapter = new Auth_Plugin_AuthAdapter();

            $authAdapter->setIdentity($username);
            $authAdapter->setCredential($password);

            $result_auth = $authAdapter->authenticate();

            $this->error_code = $result_auth->getCode();

            $result = $authAdapter->getResultRowObject();

            if ($this->error_code == -3) {
                $this->a_messages[] = "Benutzer/Passwort-Kombination leider nicht bekannt!";

                $registry = Zend_Registry::getInstance();
                $view = $registry->view;
                $view->user_login = $username;
                $view->user_password = $password;
                $view->a_messages = $this->a_messages;
            } else if ($this->error_code == -4) {
                $this->a_messages[] = 'Sie sind bereits angemeldet!';

                $registry = Zend_Registry::getInstance();
                $view = $registry->view;
                $view->user_login = $username;
                $view->user_password = $password;
                $view->user_email = $result->user_email;
                $view->user_id = $result->user_id;
                $view->user_validierungshash = $result->user_validierungshash;

                $view->a_messages = $this->a_messages;

            } else if ($this->error_code == -1) {
                unset($_SESSION['user_login']);
                unset($_SESSION['user_password']);
            } else if (!$this->error_code) {
                $this->a_messages[] = "Ihr Login ist nicht aktiv!";

                $registry = Zend_Registry::getInstance();
                $view = $registry->view;
                $view->user_login = $username;
                $view->user_password = $password;
                $view->a_messages = $this->a_messages;
            } else if (!$result) {
                $this->a_messages[] = $result;

                unset($_SESSION['user_login']);
                unset($_SESSION['user_password']);

                $this->_auth->clearIdentity();
            } else {
                $this->_auth->getStorage()->write($result);
            }
        }

        if ($this->_auth->hasIdentity()
            && $this->_auth->b_logged_in
        ) {
            $timeout = false;
            $obj_user = $this->_auth->getIdentity();

            if ($obj_user instanceof stdClass) {
                $timeout = $obj_user->user_session_timeout ?
                    $obj_user->user_session_timeout :
                    $obj_user->user_rechte_gruppe_session_timeout;
            }

            // "Habe kein eigenes Timeout, setze Globales Timeout! ";
            if (!$timeout) {
                $timeout = SESSION_TIMEOUT;
            }
            if (!Zend_Session::namespaceIsset('Zend_Auth_Ghost')) {
                $zend_auth_ghost_namespace = new Zend_Session_Namespace('Zend_Auth_Ghost');
                $zend_auth_ghost_namespace->create_date = date("Y-m-d H:i:s");
                $zend_auth_ghost_namespace->accept_answer = true;
                $zend_auth_ghost_namespace->hash = $_COOKIE['PHPSESSID'];
            }

            $zend_auth_namespace->setExpirationSeconds($timeout, 'accept_answer');
            $zend_auth_namespace->accept_answer = true;

            if (!$obj_user->user_last_login
                || "0000-00-00" == substr($obj_user->user_last_login, 0, 10)
            ) {
                $obj_user->user_last_login = date("Y-m-d H:i:s");
            }

            $this->_auth->getStorage()->write($obj_user);
        }

        // wenn authentifiziert und session noch existiert
        if ($this->_auth->hasIdentity()
            && $zend_auth_namespace->accept_answer === true
        ) {
            $session_timeout = CAD_Tool_Extractor::extractOverPath(
                $_SESSION, '__ZF->Zend_Auth->ENVT->accept_answer'
            );

            if (false === is_null($session_timeout)) {
                $session_timeout -= time();
            }

            $obj_user = $this->_auth->getIdentity();

            if ($obj_user instanceof stdClass
                && $session_timeout
                && $session_timeout < SESSION_TIMEOUT
            ) {
                $zend_auth_namespace->setExpirationSeconds(SESSION_TIMEOUT, 'accept_answer');

                $a_data = array();
                $time = date("Y-m-d H:i:s");

                $a_data['user_last_login'] = $time;
                $a_data['user_update_date'] = $time;
                $a_data['user_update_user_fk'] = $obj_user->user_id;

                $obj_user->user_last_login = $time;

                $obj_db_users = new Auth_Model_DbTable_Users();
                $obj_db_users->updateUser($a_data, $obj_user->user_id);

                $this->_auth->getStorage()->write($obj_user);
            }
            // wenn authentifiziert und session abgelaufen, dann abmelden erzwingen
        } else if ($this->_auth->hasIdentity()
            && $zend_auth_namespace->accept_answer !== true
        ) {
            $this->_auth->clearIdentity();
        }

        if (Zend_Session::namespaceIsset('Zend_Auth_Ghost')) {
            $zend_auth_ghost_namespace = new Zend_Session_Namespace('Zend_Auth_Ghost');

            if ($zend_auth_ghost_namespace->accept_answer === true
                && $zend_auth_namespace->accept_answer !== true
            ) {
                $this->b_session_timed_out = true;
            }
        }

        if (!$this->_auth->hasIdentity()) {
            $oUser = new stdClass();
            $oUser->user_id = 0;
            $oUser->user_right_group_name = 'guest';
            $oUser->user_right_group_id = 1;
            $oUser->user_first_name = 'guest';
            $oUser->user_last_name = '';
            $this->_auth->getStorage()->write($oUser);
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $role = "guest";
        $obj_user = null;
        $view = Zend_Registry::get('view');

        $module     = $request->getModuleName() ? $request->getModuleName() : 'default';
        $controller	= $request->getControllerName();
        $action 	= $request->getActionName();

        $a_params = $request->getParams();

        /* check ob blog */
        if ($module == "default"
            && $controller == "blog"
            && preg_match('/\d{4}/', $action)
        ) {
            $action = "index";
        }

        if ($this->error_code < 1) {
            if (!isset($a_params['ajax'])) {
                $request->setModuleName('default');
                $request->setControllerName('error');
                $request->setActionName('login-fail');
            }

            $view->a_messages = $this->a_messages;

            return false;
        }

        if ($this->_auth->hasIdentity()) {
            $obj_user = $this->_auth->getStorage()->read();

            if (!$obj_user instanceof stdClass) {
                $obj_user = null;
                $this->_auth->clearIdentity();
                return false;
            } else {
                $role = $obj_user->user_right_group_name;
            }
        }
        $resource = $module . ':' . $controller;
        $action_erlaubt = false;

        if ($this->_acl->has($resource)) {
            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $currentControllerName = $this->convertControllerName($controller);
                $dbClassName = 'Model_DbTable_'.$currentControllerName;

                if (class_exists($dbClassName)) {
                    /** @var Model_DbTable_Abstract $db */
                    $db = new $dbClassName();
                    $row = $db->findByPrimary($id);

                    $role = new Auth_Model_Role_Member();
                    $resourceClassName = 'Auth_Model_Resource_' . $currentControllerName;
                    $resource = new $resourceClassName($row);
                    $resourceName = $module . ':' . $controller;

                    Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource($role, $resourceName, $action);
                }
            }

            $action_erlaubt = $this->_acl->isAllowed($role, $resource, $action);
        }

        if ($this->_auth->hasIdentity()
            && $this->b_session_timed_out
            && !$this->_auth->b_logged_in
        ) {
            $this->a_messages[] = "Ihre Session is abgelaufen!";
        }

        if (false === $action_erlaubt) {
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('no-access');

            $this->persistMessage('text_needed_permissions');
        }

        $view->a_messages = $this->a_messages;

        if (true === $this->b_logged_out) {
            if (!isset($a_params['ajax'])) {
                unset($a_params['user_logout']);
                $this->b_logged_out = false;
            }
        }
        return $this;
    }

    protected function _getCurrentUserRole() {

        if ($this->_auth->hasIdentity()) {
            $authData = $this->_auth->getIdentity();
            $role = isset($authData->property->privilage)?strtolower($authData->property->privilage): 'guest';
        } else {
            $role = 'guest';
        }

        return $role;
    }

    private function convertControllerName($controllerName) {
        return ucFirst(preg_replace_callback('/(\-[a-z]{1})/', function(array $piece) {
            return ucfirst(str_replace('-', '', $piece[1]));
        }, $controllerName));
    }

    private function translate($key) {
        Service_GlobalMessageHandler::getMessageEntity()->setState(300);
        $translator = new Service_Translator();
        return $translator->getTranslation()->translate($key);
    }

    private function persistMessage($key) {
        Service_GlobalMessageHandler::getMessageEntity()->setMessage($this->translate($key));
    }
}
