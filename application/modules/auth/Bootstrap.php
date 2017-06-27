<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.06.15
 * Time: 23:46
 */
//namespace Auth;

//use Zend_Application_Module_Bootstrap;
//use Zend_Loader_Autoloader_Resource;
//use Zend_Registry;
use Auth\Service\Auth;
//use Zend_Db;
use Auth\Model\Adapter\DbTable;
use Auth\Plugin\Acl;
use Auth\Plugin\AccessControl;

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

        $oModuleAutoLoader->addResourceType('role', 'models/roles', 'Model\Role');
        $oModuleAutoLoader->addResourceType('assertion', 'models/assertions', 'Model\Assertion');
        $oModuleAutoLoader->addResourceType('resource', 'models/resources', 'Model\Resource');
    }

    protected function _initGlobalScriptPath()
    {
        Zend_Registry::get('view')->addScriptPath(APPLICATION_PATH . "/views/scripts/");
    }

    protected function _initAuth() {
        $this->bootstrap('frontController');
        $oAuth = Auth::getInstance();

        $dbAdapter = Zend_Db::factory(Zend_Registry::get('config')->resources->db);
        $oAuthAdapter = new DbTable($dbAdapter, 'users', 'user_login', 'user_password', 'MD5(?)');

        $oAcl = new Acl($oAuthAdapter);
        Zend_Registry::set('acl', $oAcl);

        $this->getResource('frontController')->registerPlugin(
            new AccessControl($oAuth, $oAcl))->setParam('auth', $oAuth);
    }
}