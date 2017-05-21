<?php 
	/**
	 * 
	 * @author Andreas Kempe / andreas.kempe@byte-artist.de
	 *
	 */

	class Auth_Model_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
	{
	    public function authenticate()
	    {
	    	$this->_authenticateSetup();

	        $authResult = parent::authenticate();
	        
	        return $authResult;
	    }
	    
	    protected function _authenticateCreateSelect()
	    {
	        // build credential expression
	        if (empty($this->_credentialTreatment)
                || false === strpos($this->_credentialTreatment, '?')
            ) {
	            $this->_credentialTreatment = '?';
	        }
	
	        $credentialExpression = new Zend_Db_Expr(
	            '(CASE WHEN ' .
	            $this->_zendDb->quoteInto(
	                $this->_zendDb->quoteIdentifier($this->_credentialColumn, true)
	                . ' = ' . $this->_credentialTreatment, $this->_credential
	                )
	            . ' THEN 1 ELSE 0 END) AS '
	            . $this->_zendDb->quoteIdentifier(
	                $this->_zendDb->foldCase('zend_auth_credential_match')
	                )
	            );
	
	        // get select
	        $dbSelect = clone $this->getDbSelect();
	        $dbSelect->from($this->_tableName, array('*', $credentialExpression))
                ->joinInner('user_state', 'user_state_id = user_state_fk')
                ->joinInner('user_right_groups', 'user_right_group_id = user_right_group_fk')
                ->joinLeft('user_x_user_group', 'user_x_user_group_user_fk = user_id')
                ->joinLeft('user_groups', 'user_group_id = user_x_user_group_user_group_fk')
                ->where($this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?', $this->_identity);
	        
	        return $dbSelect;
	    }
	}
