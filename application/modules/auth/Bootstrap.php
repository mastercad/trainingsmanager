<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.06.15
 * Time: 23:46
 */

define("PROJECT_NAME", "trainingsmanager.byte-artist.de");
define("PROJECT_URL", "http://trainingsmanager.byte-artist.de");

class Auth_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initResourceLoader()
    {
        $oModuleAutoLoader = new Zend_Loader_Autoloader_Resource(
            array(
                'namespace' => 'Auth',
                'basePath' => dirname(__FILE__)
            )
        );

        $oModuleAutoLoader->addResourceType('role', 'models/roles', 'Model_Role');
        $oModuleAutoLoader->addResourceType('assertion', 'models/assertions', 'Model_Assertion');
        $oModuleAutoLoader->addResourceType('resource', 'models/resources', 'Model_Resource');
    }

    protected function _initGlobalScriptPath()
    {
        Zend_Registry::get('view')->addScriptPath(APPLICATION_PATH . "/views/scripts/");
    }

    protected function _initAuth() {
        $this->bootstrap('frontController');
        $oAuth = Auth_Service_Auth::getInstance();

        $dbAdapter = Zend_Db::factory(Zend_Registry::get('config')->resources->db);
        $oAuthAdapter = new Auth_Model_Adapter_DbTable($dbAdapter, 'users', 'user_login', 'user_password', 'MD5(?)');

        $oAcl = new Auth_Plugin_Acl($oAuthAdapter);
        Zend_Registry::set('acl', $oAcl);

        $this->getResource('frontController')->registerPlugin(
            new Auth_Plugin_AccessControl($oAuth, $oAcl))->setParam('auth', $oAuth);
    }
}