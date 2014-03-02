<?php

class Application_Model_DbTable_UserRechte extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'user_rechte';
	protected $_primary = 'user_recht_id';
	
	protected static $obj_meta;
	
	public function init()
	{
		if(!self::$obj_meta)
		{
			self::$obj_meta = $this->info();
		}
	}

	public function getUserRechte($user_id)
	{
		$select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
					   ->setIntegrityCheck(false);
		try
		{
			$select->join('controllers', 'user_recht_controller_fk = controller_id', 'controller_name');
			$select->join('modules', 'module_id = controller_module_fk', 'module_name');
			$select->where('user_recht_user_fk = ' . $user_id);
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
		
		$rows = $this->fetchAll($select)->toArray();
		return $rows;
	}
	
	public function getUserGruppe($a_options)
	{
		$db_select = $this->select();
		
		foreach( $a_options['where_fields'] as $key => $option)
		{
			$db_select->where($key . " = ?", $option);
		}
		try
		{
			$row = $this->fetchRow($db_select);
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			echo "<pre>";
			print_r($a_options);
			echo "</pre>";
			echo "<br />" . $db_select->__toString();
			return false;
		}
		
		if(!$row)
		{
			return false;
		}
		return $row->toArray();
	}
	
	public function setUserGruppe( $daten)
	{
		try
		{
			return $this->insert( $daten);
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function updateUserGruppe( $daten, $user_gruppe_id)
	{
		try
		{
			$this->update( $daten, " `user_gruppe_id` LIKE ( '" . $user_gruppe_id . "')");
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function loescheUserGruppe( $user_gruppe_id)
	{
		try
		{
			$this->delete( $user_gruppe_id);
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
}