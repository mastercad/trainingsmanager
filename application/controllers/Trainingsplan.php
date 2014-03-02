<?php
	class TrainingsplanController extends Zend_Controller_Action
	{
		public function __init()
		{
		}

	    public function postDispatch()
	    {
	    	$a_params = $this->getRequest()->getParams();

	    	if(isset($a_params['ajax']))
	    	{
	    		$this->view->layout()->disableLayout();
	    	}
	    }

        public function showAction()
        {

        }

        public function editAction()
        {
            
        }
    }
