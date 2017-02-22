<?php

class Application_Plugin_LayoutLoader extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $oConfig     = Zend_Registry::get('config');
        
        if (true === isset($oConfig->resources->layout->layoutPath)) {
            $asLayoutPath = $oConfig->resources->layout->layoutPath;
            $sModuleDir = Zend_Controller_Front::getInstance()->getModuleDirectory();

            Zend_Layout::getMvcInstance()->setLayoutPath($sModuleDir. DIRECTORY_SEPARATOR . $asLayoutPath);
        }
    }	
}