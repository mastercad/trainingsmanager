<?php 
	/**
	 * 
	 * @author Andreas Kempe / andreas.kempe@byte-artist.de
	 *
	 */

	class CAD_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
	{
	    public function authenticate()
	    {
	    	$this->_authenticateSetup();
	    	
	    	$dbSelect = $this->_authenticateCreateSelect();
	        $authResult = parent::authenticate();
	        
	        return $authResult;
	    }
	    
	    protected function _authenticateCreateSelect()
	    {
	        // build credential expression
	        if (empty($this->_credentialTreatment) || (strpos($this->_credentialTreatment, '?') === false)) {
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
	        $dbSelect->from($this->_tableName, array('*', $credentialExpression));
	        $dbSelect->join('user_status', 'user_status_id = user_status_fk');
	        $dbSelect->join('user_rechte_gruppen', 'user_rechte_gruppe_id = user_rechte_gruppe_fk');
	        $dbSelect->where($this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?', $this->_identity);

	        return $dbSelect;
	    }
	}
