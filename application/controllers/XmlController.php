<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

/**
 * Class XmlController
 */
class XmlController extends AbstractController
{
    /**
     * initial function for controller
     */
    public function init()
    {
    	$this->_helper->getHelper('contextSwitch')
    		->addActionContext($this->_getParam('action'), 'xml')
    		->initContext();
    	
    	$req = $this->getRequest();
    	$a_params = $req->getParams();
    	
    	if(isset($a_params['ajax']))
    	{
    		$this->view->layout()->disableLayout();
    	}
    }

    /**
     * index action
     */
    public function indexAction()
    {
    }

    /**
     * create site map action
     *
     * @throws \Zend_Controller_Exception
     * @throws \Zend_Filter_Exception
     */
    public function createSitemapAction()
    {
		$this->view->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
     	$this->_response->setHeader('Content-Type','text/xml;charset=utf-8');

    	$front = $this->getFrontController();
    	$acl = array();

    	$inflector = new Zend_Filter_Inflector(':page');
    	$inflector->setRules(array(
    			':page'  => array('Word_CamelCaseToDash', 'StringToLower')
    	));
    	
    	foreach ($front->getControllerDirectory() as $module => $path)
    	{
    		foreach (scandir($path) as $file)
    		{
    			if (strstr($file, "Controller.php") !== false)
    			{
    				include_once $path . DIRECTORY_SEPARATOR . $file;
    				
    				/*
    				 * @TODO hier noch entweder einen KEYWORD Check einbauen
    				 * oder einen Access Check auf Gast zugriff um die restlichen
    				 * internen actions nicht öffentlich sichtbar zu machen
    				 * 
    				 */
    				
//     				foreach (get_declared_classes() as $class)
//     				{
//     					if (is_subclass_of($class, 'Zend_Controller_Action'))
//     					{
    						$controller = $inflector->filter(array('page' => substr($file, 0, strpos($file, "Controller.php"))));
    						$actions = array();
    						
    						foreach (get_class_methods(substr($file, 0, strpos($file, ".php"))) as $action)
    						{
    							if (strstr($action, "Action") !== false)
    							{
    								$count_actions = count($actions);
    								$action = preg_replace('/Action/i', '', $action);
    								$a_stats = stat($path . DIRECTORY_SEPARATOR . $file);
    								
									$actions[$count_actions]['name'] = $inflector->filter(array('page' => $action));
									$actions[$count_actions]['mtime'] = $a_stats['mtime'];
    							}
    						}
    						
//     					}
//     				}
    				
    				$acl[$module][$controller] = $actions;
    			}
    		}
    	}
    	
    	$xml_content = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    	foreach($acl as $modul => $a_controllers)
    	{
    		$pfad = '/';
    		
    		if(strtolower($modul) != "default")
    		{
    			$pfad .= $modul . "/";
    		}
    		
    		foreach($a_controllers as $controller => $a_actions)
    		{
    			foreach($a_actions as $a_action)
    			{
	    			$xml_content .= '<url>';
	    			$xml_content .= '<loc>' . $_SERVER["SERVER_NAME"] . $pfad . $controller . "/" . $a_action['name'] . '</loc>';
	    			$xml_content .= '<lastmod>' . (date("c", $a_action['mtime']) . '</lastmod>');
	    			$xml_content .= '<changefreq>weekly</changefreq>';
	    			$xml_content .= '</url>';
    			}
    		}
    	}
    	$xml_content .= '</urlset>';
    	
    	$doc = new DOMDocument('1.0');
    	$doc->preserveWhiteSpace = false;
    	$doc->loadXML($xml_content);
    	$doc->formatOutput = true;
    	
    	echo $doc->saveXML();
    }
}

