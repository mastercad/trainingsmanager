<?php

class Vavg_Filter_Url implements Zend_Filter_Interface
{
    public function filter($string)
    {
    	$registry = Zend_Registry::getInstance();
    	$view = $registry->view;
    	
    	if( substr( $string, 0, 1) == '/')
    	{
    		if(substr($string, -1) != "/")
    		{
    			$string .= '/';
    		}
    		return $string;
    	}
    	else if(trim($string) == '#')
    	{
    		return '#';
    	}
    	else
    	{
    		return $view->url(array( 'module' => 'default', 'controller'=>'index', 'action'=>'show', 'name'=> urlencode($string)), null, true) . "/";
    	}
    }
}