<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:41
 */

class Auth_Model_Role_Member implements Zend_Acl_Role_Interface {

    private $_sRole = null;
    private $_iMemberId = null;

    public function __construct() {
        $this->_iMemberId = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->_sRole = Zend_Auth::getInstance()->getIdentity()->user_right_group_name;
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId() {
        return $this->_sRole;
    }

    /**
     * @return null
     */
    public function getMemberId() {
        return $this->_iMemberId;
    }

    /**
     * @param null $iMemberId
     */
    public function setMemberId($iMemberId) {
        $this->_iMemberId = $iMemberId;
    }

    /**
     * @return null
     */
    public function getRole() {
        return $this->_sRole;
    }

    /**
     * @param string $sRole
     */
    public function setRole($sRole) {
        $this->_sRole = $sRole;
    }
}