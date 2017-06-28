<?php


namespace Auth\Plugin;

use Auth\Model\Adapter\DbTable;
use Zend_Registry;
use Zend_Auth_Result;
use Auth\Model\DbTable\Users;
use Zend_Db_Expr;



class AuthAdapter extends DbTable
{
    protected $_identity;
    protected $_a_identity;

    public function __construct()
    {
        $registry = Zend_Registry::getInstance();
        parent::__construct($registry->db);

        $this->setTableName('users');
        $this->setIdentityColumn('user_login');
        $this->setCredentialColumn('user_password');
        $this->setCredentialTreatment('MD5(?)');
    }

    public function authenticate()
    {
        $authResult = parent::authenticate();

        if (false === is_array($this->_resultRow)) {
            $authResult = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                $this->_identity,
                array('Benutzername/ Passwortkombination unbekannt!')
            );
            return $authResult;
        }

        if (strtoupper($this->_resultRow['user_state_name']) != strtoupper("aktiv")) {
            $authResult = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE,
                $this->_identity,
                array('User inaktiv!')
            );
            return $authResult;
        }

        if ($this->alreadyLoggedIn()) {
            $authResult = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                $this->_identity,
                array('User bereits eingeloggt!')
            );
        } else {
            $this->setLoggedIn();
        }
        return $authResult;
    }

    private function alreadyLoggedIn()
    {
        if ($this->_identity
            && is_array($this->_resultRow)
            && array_key_exists('user_id', $this->_resultRow)
            && $this->_resultRow['user_id']
        ) {
            $aktuelle_session_id = @$_COOKIE['PHPSESSID'];

            /* check, ob bereits eingeloggt und ob kein multilogin und ob
             * die aktuelle session ID != der alten session ID */
            if ($this->_resultRow['user_flag_logged_in']
                && !$this->_resultRow['user_flag_multilogin']
                && !$this->_resultRow['user_right_group_flag_multilogin']
                && $this->_resultRow['user_session_id']
                && $aktuelle_session_id != $this->_resultRow['user_session_id']
            ) {
                return true;
            }
        }
        return false;
    }

    private function setLoggedIn()
    {
        $session_id = null;

        /* SESSION ID ziehen */
        if (isset($_COOKIE)
            && is_array($_COOKIE)
            && array_key_exists('PHPSESSID', $_COOKIE)
        ) {
            $session_id = $_COOKIE['PHPSESSID'];
        }
        $obj_users = new Users();
        $last_login = date("Y-m-d H:i:s");

        $data = Array(
                'user_last_login' => $last_login,
                'user_login_count' => new Zend_Db_Expr('user_login_count + 1'),
                'user_session_id' => $session_id
        );
        $obj_users->updateUser($data, $this->_resultRow['user_id']);

        $this->_resultRow['user_session_id'] = $session_id;
        $this->_resultRow['user_flag_logged_in'] = true;
        $this->_resultRow['user_login_count'] += 1;
    }
}
