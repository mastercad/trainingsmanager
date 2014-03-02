<?php
    class Application_Plugin_CheckeRecht extends Zend_Controller_Plugin_Abstract
    {
    	public function hatRecht($module, $controller = 'index', $action = 'index')
    	{
			$acl = Zend_Registry::get('acl');
			$a_user_identify = Zend_Auth::getInstance()->getIdentity();
			$role = $a_user_identify->user_rechte_gruppe_name;

			$resource = null;

		    if ($acl->has($module . ':'))
		    {
				$resource = $module . ':';
			}
			// Ist in der ACL als Ressource das Modul+Controller konfiguriert?
			else if ($acl->has($module . ':' . $controller))
			{
				$resource = $module . ':' . $controller;
			}

			if (!$acl->isAllowed($role, $resource, $action) &&
				!$acl->isAllowed($role, $resource, '*'))
			{
				return false;
			}
			return true;
    	}
    }
?>