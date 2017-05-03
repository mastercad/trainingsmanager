<?php

	/*
	 * aufbau:
	 *
	 * Modul: modul:
	 * Modul + Controller : Modul:Controller
	 *
	 */

class Auth_Plugin_Acl extends Zend_Acl
{
    const DEFAULT_ROLE = 'guest';
    private $_aDynamicPermissions = array();

    public function __construct()
    {
        $obj_db_user_right_groups_rights = new Auth_Model_DbTable_UserRightGroupRights();
        $obj_db_user_right_groups = new Auth_Model_DbTable_UserRightGroups();

        $a_user_right_groups_rights = $obj_db_user_right_groups_rights->getUserRightGroupRights();
        $a_user_right_groups = $obj_db_user_right_groups->getUserRightGroups();
        $a_user_right_groups_namen = array();
        $a_user_right_groups_erbt_von = array();

        foreach($a_user_right_groups as $a_user_right_group) {
            $a_user_right_groups_namen[$a_user_right_group['user_right_group_id']] = $a_user_right_group['user_right_group_name'];
            $a_user_right_groups_erbt_von[$a_user_right_group['user_right_group_id']] = $a_user_right_group['user_right_group_parent_fk'];
        }

        foreach($a_user_right_groups as $a_user_right_group) {
            if(!$this->hasRole($a_user_right_groups_namen[$a_user_right_group['user_right_group_id']])) {
                if($a_user_right_groups_erbt_von[$a_user_right_group['user_right_group_id']]) {
                    $this->addRole(
                        new Zend_Acl_Role($a_user_right_groups_namen[$a_user_right_group['user_right_group_id']]),
                        $a_user_right_groups_namen[$a_user_right_groups_erbt_von[$a_user_right_group['user_right_group_id']]]);
                } else {
                    $this->addRole(new Zend_Acl_Role($a_user_right_groups_namen[$a_user_right_group['user_right_group_id']]));
                }
            }
        }

        foreach($a_user_right_groups_rights as $a_user_right_groups_right) {
            $aPath = explode(':', $a_user_right_groups_right['user_right_group_right']);
            $sModule = CAD_Tool_Extractor::extractOverPath($aPath, 0);
            $sController = CAD_Tool_Extractor::extractOverPath($aPath, 1);
            $sAction = CAD_Tool_Extractor::extractOverPath($aPath, 2);

            $sModuleControllerResource = $sModule . ':' . $sController;
            $sModuleControllerActionResource = $sModule . ':' . $sController . ':' . $sAction;

            $sValidatorClass = $a_user_right_groups_right['user_right_group_right_validator_class'];
            $sRole = $a_user_right_groups_namen[$a_user_right_groups_right['user_right_group_fk']];

            if (false === empty($sValidatorClass)) {
                $this->_aDynamicPermissions[$sModuleControllerActionResource] = array(
                    'action' => $sAction,
                    'validatorClass' => $sValidatorClass,
                    'role' => $sRole
                );
            }

            if(!$this->has($sModule)) {
                $this->addResource($sModule);
            }
            if (!$this->has($sModuleControllerResource)) {
                $this->addResource(new Zend_Acl_Resource($sModuleControllerResource), $sModule);
            }

            if (!$this->has($sModuleControllerActionResource)) {
                $this->addResource(new Zend_Acl_Resource($sModuleControllerActionResource), $sModuleControllerResource);
            }

            $this->allow($sRole, $sModule, $sModuleControllerResource);

            // nur wenn es hier keinen validator gibt, alles klar machen
//            if (true === empty($sValidatorClass)) {
                $this->allow($sRole, $sModuleControllerResource, $sAction);
//            } else {
//                $this->deny($sRole, $sModuleControllerResource, $sAction);
//            }
        }
    }

    private function _registerValidatorClassForRoleAndChildren($sRole, $sModuleControllerActionResource,
        $sAction, $sValidatorClass) {

        $this->_aDynamicPermissions[$sModuleControllerActionResource] = array(
            'action' => $sAction,
            'validatorClass' => $sValidatorClass,
            'role' => $sRole
        );

        $aChildren = CAD_Tool_Extractor::extractOverPath(
            $this->_roleRegistry, 'getRoles->' . $sRole . '->children');

        if (true === is_array($aChildren)
            && 0 < count($aChildren)
        ) {
            foreach ($aChildren as $sNewRole => $oAclRole) {
                $this->_registerValidatorClassForRoleAndChildren($sNewRole, $sModuleControllerActionResource,
                    $sAction, $sValidatorClass);
            }
        }
        return $this;
    }

    public function prepareDynamicPermissionsForCurrentResource($sCurrentRole, $sResource, $sAction) {
        if (true === array_key_exists($sResource . ':' . $sAction, $this->_aDynamicPermissions)) {
            $sRole = CAD_Tool_Extractor::extractOverPath($this->_aDynamicPermissions, $sResource . ':' . $sAction . '->role');
            $sValidatorClass = CAD_Tool_Extractor::extractOverPath($this->_aDynamicPermissions, $sResource . ':' . $sAction . '->validatorClass');
            $this->allow($sCurrentRole, $sResource, $sAction, new $sValidatorClass());
        }
    }
}