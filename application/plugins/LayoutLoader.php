<?php

class Application_Plugin_LayoutLoader extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    { 	
        $config     = Zend_Registry::get('config');
        $moduleName = $request->getModuleName();
        
        if (isset($config->resources->layout->layoutPath)) {
            $layoutPath = $config->resources->layout->layoutPath;
            $moduleDir = Zend_Controller_Front::getInstance()->getModuleDirectory();
            Zend_Layout::getMvcInstance()->setLayoutPath(
                $moduleDir. DIRECTORY_SEPARATOR .$layoutPath
            );
        }
    }	
}