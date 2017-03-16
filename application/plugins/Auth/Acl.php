<?php

	/*
	 * aufbau:
	 * 
	 * Modul: modul:
	 * Modul + Controller : Modul:Controller
	 * 
	 */

	class Plugin_Auth_Acl extends Zend_Acl
	{
		protected $userRightGroupRightsDb;
		static $rightsCollection;
		
		public function __construct()
		{
			$userRightGroupRightsDb = new Model_DbTable_UserRightGroupRights();
            $userRightGroupsDb = new Model_DbTable_UserRightGroups();

			$userRightGroupRightsCollection = $userRightGroupRightsDb->findAllUserRightGroupRights();
			$userRightGroupsCollection = $userRightGroupsDb->findAllUserRightGroups();
			$userRightGroupNamesCollection = array();
			$userRightGroupParentsCollection = array();
			
			foreach($userRightGroupsCollection as $userRightGroup) {
                $userRightGroupNamesCollection[$userRightGroup['user_right_group_id']] = $userRightGroup['user_right_group_name'];
                $userRightGroupParentsCollection[$userRightGroup['user_right_group_id']] = $userRightGroup['user_right_group_parent_fk'];
			}
			
			foreach($userRightGroupsCollection as $userRightGroup) {
				if(!$this->hasRole($userRightGroupNamesCollection[$userRightGroup['user_right_group_id']])) {
					if($userRightGroupParentsCollection[$userRightGroup['user_right_group_id']]) {
						$this->addRole(
                            new Zend_Acl_Role($userRightGroupNamesCollection[$userRightGroup['user_right_group_id']]),
                            $userRightGroupNamesCollection[$userRightGroupParentsCollection[$userRightGroup['user_right_group_id']]]);
					} else {
						$this->addRole(new Zend_Acl_Role($userRightGroupNamesCollection[$userRightGroup['user_right_group_id']]));
					}
				}
			}
			foreach($userRightGroupRightsCollection as $userRightGroupRight) {
				list($module, $controller, $action) = explode(':', $userRightGroupRight['user_right_group_right']);
				$resource = $module . ':' . $controller;
				
				if(!$this->has($resource)) {
					$this->addResource(new Zend_Acl_Resource($resource));
				}
				
				$this->allow($userRightGroupNamesCollection[$userRightGroupRight['user_right_group_fk']], $resource, $action);
			}
		}
	} 