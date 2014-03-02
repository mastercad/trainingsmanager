<?php
/**
  * Remap layouts file names based on UserAgent device.
  */
class CAD_Controller_Plugin_DeviceLayout extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $req)
    {
        /** @var $userAgent Zend_Http_UserAgent */
//         $userAgent = Zend_Registry::get('Zend_Http_UserAgent');
    	$frontController = Zend_Controller_Front::getInstance();
    	$bootstrap       = $frontController->getParam('bootstrap');
    	
//     	var_dump($frontController->getParam('bootstrap'));
    	
//         if (!$bootstrap->hasResource('useragent')) {
//             throw new Zend_Controller_Exception('The mobile plugin can only be loaded when the UserAgent resource is bootstrapped');
//         }
    	
//     	$userAgent		 = $bootstrap->getResource('useragent');
    	$userAgent = new Zend_Http_UserAgent();
    	
        /** call to initialize browser type */
        $userAgent->getDevice();

        /* if desktop do nothing, so use standard layout file names */
        if ($userAgent->getBrowserType() === 'desktop') {
            return $req;
        }

        $layout = Zend_Layout::getMvcInstance();
        $inflector = $layout->getInflector();
        $inflector->setTarget(':device.:script.:suffix');

        switch ($userAgent->getBrowserType()) {
            default:
            case 'mobile':
                $inflector->setStaticRule('device', 'mobile');
                break;
        }
        return $req;
    }
}
