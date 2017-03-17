<?php
	/**
	 * 
	 * @author Andreas Kempe / andreas.kempe@byte-artist.de
	 *
	 */

	class CAD_Auth extends Zend_Auth
	{
		static protected $_instance = null;
		protected $_resultRow = array();
		
		public static function getInstance()
		{
			if (null === self::$_instance)
			{
				self::$_instance = new Zend_Auth();
				self::$_instance = new self();
			}
		
			return self::$_instance;
		}
		
	    public function clearIdentity()
	    {
	    	if(!$this->_resultRow)
	    	{
	    		$this->_resultRow = $this->getStorage()->read('Zend_Auth');
	    	}
	    	
			if( $this->_resultRow instanceof stdClass)
			{
	 			$this->getStorage()->write('');
	 			
				$obj_db_users = new Model_DbTable_Users();
			
				$a_data = Array(
								'user_session_id' => '',
								'user_flag_logged_in' => false
				);
				
				if(isset($this->_resultRow->user_id) &&
				   $obj_db_users->updateUser( $a_data, $this->_resultRow->user_id))
				{
        			parent::clearIdentity();
				}
			}
			else
			{
        		parent::clearIdentity();
			}
			
			Zend_Session::namespaceUnset('Zend_Auth');
	    }
	}