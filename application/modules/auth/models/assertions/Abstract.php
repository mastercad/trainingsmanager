<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 01.09.15
 * Time: 23:00
 */

class Auth_Model_Assertion_Abstract implements Zend_Acl_Assert_Interface {

    private $_aGlobalRights = array(
        'admin',
        'superadmin'
    );

    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Zend_Acl $oAcl
     * @param  Auth_Model_Role_Member $oRole
     * @param  Auth_Model_Resource_Comment $oResource
     * @param  string $sPrivilege
     *
     * @return boolean
     */
    public function assert(Zend_Acl $oAcl, Zend_Acl_Role_Interface $oRole = null,
                           Zend_Acl_Resource_Interface $oResource = null, $sPrivilege = null) {

        return $this->_considerAclRole($oAcl, $oRole, $oResource, $sPrivilege);
    }

    /**
     * @param Zend_Acl $oAcl
     * @param $oRole
     * @param $oResource
     * @param $sPrivilege
     * @return bool
     */
    private function _considerAclRole($oAcl, $oRole, $oResource, $sPrivilege) {
        $bReturn = false;
        if ($oRole instanceof Zend_Acl_Role) {
            $bReturn = $this->_considerZendAclRole($oAcl, $oRole, $oResource, $sPrivilege);
        } else {
            $bReturn = $this->_considerAuthAclRole($oAcl, $oRole, $oResource, $sPrivilege);
        }
        return $bReturn;
    }

    /**
     * @todo ich gehe davon aus, das Zend hier nur rein springt, wenn es die rechte sucht,
     * die hätte es nicht als vergabe, wenn sie nicht im vorfeld definiert wären, wenn
     * das hier noch nicht 100% ist, müsste man hier ansetzen, derzeit springt die navi hier
     * rein um zu prüfen ob ein eintrag angezeigt werden darf oder nicht.
     *
     * @param Zend_Acl $oAcl
     * @param Zend_Acl_Role $oRole
     * @param Zend_Acl_Resource $oResource
     * @param string $sPrivilege
     * @return bool
     */
    private function _considerZendAclRole($oAcl, $oRole, $oResource, $sPrivilege) {
        return true;
    }


    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Zend_Acl $oAcl
     * @param  Auth_Model_Role_Member $oRole
     * @param  Auth_Model_Resource_Comment $oResource
     * @param  string $sPrivilege
     *
     * @return boolean
     */
    protected function _considerAuthAclRole($oAcl, $oRole, $oResource, $sPrivilege) {
        $bReturn = false;

        if ((null !== $oResource->getMemberId()
                && $oRole->getMemberId() === $oResource->getMemberId())
            || true === in_array($oRole->getRoleId(), $this->_aGlobalRights)
        ) {
            $bReturn = true;
        }
        return $bReturn;
    }
}