<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    
    class Helper_Sidebar extends Zend_Controller_Action_Helper_Abstract
    {
        protected $_run = false;
        
        public function preDispatch()
        {
//            parent::postDispatch();
            if(true === $this->_run)
            {
                return;
            }
            
            $sidebarData = array('foo' => 'bar');
            
            $view = $this->getActionController()->view;
//            echo $this->getRequest()->getParam('controller');
            $view->sidebarData = $sidebarData;
            
            $this->_run = true;
        }
        
        /**
         * @var Zend_Loader_PluginLoader
         */
        public $pluginLoader;

        /**
         * Constructor: initialize plugin loader
         * 
         * @return void
         */
        public function __construct()
        {
            $this->pluginLoader = new Zend_Loader_PluginLoader();
        }

        /**
         * Load a form with the provided options
         * 
         * @param  string $name 
         * @param  array|Zend_Config $options 
         * @return Zend_Form
         */
        public function loadForm($name, $options = null)
        {
            $module  = $this->getRequest()->getModuleName();
            $front   = $this->getFrontController();
            $default = $front->getDispatcher()
                             ->getDefaultModule();
            if (empty($module)) {
                $module = $default;
            }
            $moduleDirectory = $front->getControllerDirectory($module);
            $formsDirectory  = dirname($moduleDirectory) . '/forms';

            $prefix = (('default' == $module) ? '' : ucfirst($module) . '_')
                    . 'Form_';
            $this->pluginLoader->addPrefixPath($prefix, $formsDirectory);

            $name      = ucfirst((string) $name);
            $formClass = $this->pluginLoader->load($name);
            return new $formClass($options);
        }

        /**
         * Strategy pattern: call helper as broker method
         * 
         * @param  string $name 
         * @param  array|Zend_Config $options 
         * @return Zend_Form
         */
        public function direct($name, $options = null)
        {
            echo "Direct!";
//            return $this->loadForm($name, $options);
        }
    }
?>
