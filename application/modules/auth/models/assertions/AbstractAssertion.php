<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

namespace Auth\Model\Assertion;

use Auth\Model\Resource\AbstractResource;
use Zend_Acl_Assert_Interface;
use Zend_Acl;
use Zend_Acl_Role;
use Zend_Acl_Role_Interface;
use Zend_Acl_Resource;
use Zend_Acl_Resource_Interface;
use Auth\Model\Role\Member as MemberRole;


class AbstractAssertion implements Zend_Acl_Assert_Interface {

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
     * @param  Zend_Acl_Role_Interface $oRole
     * @param  Zend_Acl_Resource_Interface $oResource
     * @param  string $sPrivilege
     *
     * @return boolean
     */
    public function assert(Zend_Acl $oAcl, Zend_Acl_Role_Interface $oRole = null,
        Zend_Acl_Resource_Interface $oResource = null, $sPrivilege = null) {
        return $this->considerAclRole($oAcl, $oRole, $oResource, $sPrivilege);
    }

    /**
     * @param Zend_Acl $oAcl
     * @param $oRole
     * @param $oResource
     * @param $sPrivilege
     * @return bool
     */
    private function considerAclRole($oAcl, $oRole, $oResource, $sPrivilege) {
        if ($oRole instanceof Zend_Acl_Role) {
            return $this->considerZendAclRole($oAcl, $oRole, $oResource, $sPrivilege);
        }
        return $this->considerAuthAclRole($oAcl, $oRole, $oResource, $sPrivilege);
    }

    /**
     * @todo ich gehe davon aus, das Zend hier nur rein springt, wenn es die rechte sucht,
     * die hätte es nicht als vergabe, wenn sie nicht im vorfeld definiert wären, wenn
     * das hier noch nicht 100% ist, müsste man hier ansetzen, derzeit springt die navi hier
     * rein um zu prüfen ob ein eintrag angezeigt werden darf oder nicht.
     *
     * theoretisch kann diese Role nur von einem Gast Konto aufgerufen werden
     *
     * @param Zend_Acl $oAcl
     * @param Zend_Acl_Role $oRole
     * @param Zend_Acl_Resource $oResource
     * @param string $sPrivilege
     * @return bool
     */
    private function considerZendAclRole($oAcl, $oRole, $oResource, $sPrivilege) {
        return false;
    }


    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  MemberRole $oRole
     * @param AbstractResource $oResource
     *
     * @return boolean
     */
    protected function considerAuthAclRole($oAcl, $oRole, $oResource, $sPrivilege) {
        // if the current user the owner of the resource?
        // or group admin and in the same group like to owner
        // or if the current user member of one of the global right groups?
        if ((!empty($oResource->getMemberId())
                && $oRole->getMemberId() == $oResource->getMemberId())
            || ($oRole->getGroupId() == $oResource->getGroupId()
                && $oRole->getGroup() == $oResource->getGroupName()
                && "GROUP_ADMIN" == strtoupper($oRole->getRoleId()))
            || (true === in_array($oRole->getRoleId(), $this->_aGlobalRights))
        ) {
            return true;
        }
        return false;
    }
}