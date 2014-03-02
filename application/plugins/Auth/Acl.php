<?php

	/*
	 * aufbau:
	 * 
	 * Modul: modul:
	 * Modul + Controller : Modul:Controller
	 * 
	 */

	class Application_Plugin_Auth_Acl extends Zend_Acl
	{
		protected $obj_db_user_rechte_gruppen;
		static $a_rechte;
		
		public function __construct()
		{
			$db = Zend_Registry::get('db');
			$obj_db_user_rechte_gruppen_rechte = new Application_Model_DbTable_UserRechteGruppenRechte();
			$obj_db_user_rechte_gruppen = new Application_Model_DbTable_UserRechteGruppen();
			$obj_db_user_rechte = new Application_Model_DbTable_UserRechte();
			
			$obj_user_auth = CAD_Auth::getInstance();
			$a_rechte = Array();
			
			$a_resourcen = Array();
			$a_user_rechte_gruppen_rechte = $obj_db_user_rechte_gruppen_rechte->getUserRechteGruppenRechte();
			$a_user_rechte_gruppen = $obj_db_user_rechte_gruppen->getUserRechteGruppen();
			$a_user_rechte_gruppen_namen = array();
			$a_user_rechte_gruppen_erbt_von = array();
			
			foreach($a_user_rechte_gruppen as $a_user_rechte_gruppe)
			{
				$a_user_rechte_gruppen_namen[$a_user_rechte_gruppe['user_rechte_gruppe_id']] = $a_user_rechte_gruppe['user_rechte_gruppe_name'];
				$a_user_rechte_gruppen_erbt_von[$a_user_rechte_gruppe['user_rechte_gruppe_id']] = $a_user_rechte_gruppe['user_rechte_gruppe_erbt_von'];
			}
			
			foreach($a_user_rechte_gruppen as $a_user_rechte_gruppe)
			{
				if(!$this->hasRole($a_user_rechte_gruppen_namen[$a_user_rechte_gruppe['user_rechte_gruppe_id']]))
				{
					if($a_user_rechte_gruppen_erbt_von[$a_user_rechte_gruppe['user_rechte_gruppe_id']])
					{
						$this->addRole(new Zend_Acl_Role($a_user_rechte_gruppen_namen[$a_user_rechte_gruppe['user_rechte_gruppe_id']]), $a_user_rechte_gruppen_namen[$a_user_rechte_gruppen_erbt_von[$a_user_rechte_gruppe['user_rechte_gruppe_id']]]);
					}
					else
					{
						$this->addRole(new Zend_Acl_Role($a_user_rechte_gruppen_namen[$a_user_rechte_gruppe['user_rechte_gruppe_id']]));
					}
				}
			}
			foreach($a_user_rechte_gruppen_rechte as $a_user_rechte_gruppen_recht)
			{
				list($module, $controller, $action) = explode(':', $a_user_rechte_gruppen_recht['user_rechte_gruppen_recht']);
				$resource = $module . ':' . $controller;
				
				if(!$this->has($resource))
				{
					$this->add(new Zend_Acl_Resource($resource));
				}
				
				$this->allow($a_user_rechte_gruppen_namen[$a_user_rechte_gruppen_recht['user_rechte_gruppe_fk']], $resource, $action);
			}
		}
	} 