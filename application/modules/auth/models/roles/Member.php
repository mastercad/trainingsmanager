<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:41
 */

namespace Auth\Model\Role;

use Zend_Acl_Role_Interface;
use Zend_Auth;




class Member implements Zend_Acl_Role_Interface {

    private $role = null;
    private $memberId = null;
    private $group = null;
    private $groupId = null;

    public function __construct(array $config = null) {
        if (!empty($config)) {
            $this->parseConfig($config);
        } else {
            $this->memberId = Zend_Auth::getInstance()->getIdentity()->user_id;
            $this->role = Zend_Auth::getInstance()->getIdentity()->user_right_group_name;
            $this->group = Zend_Auth::getInstance()->getIdentity()->user_group_name;
            $this->groupId = Zend_Auth::getInstance()->getIdentity()->user_group_id;
        }
    }

    private function parseConfig(array $config) {
        if (array_key_exists('userId', $config)) {
            $this->setMemberId($config['userId']);
        }
        if (array_key_exists('role', $config)) {
            $this->setRole($config['role']);
        }
        if (array_key_exists('groupName', $config)) {
            $this->setGroup($config['groupName']);
        }
        if (array_key_exists('groupId', $config)) {
            $this->setGroupId($config['groupId']);
        }
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId() {
        return $this->role;
    }

    /**
     * @return null
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * @return null
     */
    public function getMemberId() {
        return $this->memberId;
    }

    /**
     * @param null $memberId
     */
    public function setMemberId($memberId) {
        $this->memberId = $memberId;
    }

    /**
     * @param string $sRole
     */
    public function setRole($sRole) {
        $this->role = $sRole;
    }

    /**
     * @return null
     */
    public function getGroup() {
        return $this->group;
    }

    /**
     * @param null $group
     */
    public function setGroup($group) {
        $this->group = $group;
    }

    /**
     * @return null
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * @param null $groupId
     */
    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }
}