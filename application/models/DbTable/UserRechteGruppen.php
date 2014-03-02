<?php

class Application_Model_DbTable_UserRechteGruppen extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'user_rechte_gruppen';
	protected $_primary = 'user_rechte_gruppe_id';
	
	protected static $obj_meta;
	
	public function init()
	{
		if(!self::$obj_meta)
		{
			self::$obj_meta = $this->info();
		}
	}

	public function getUserRechteGruppen()
	{
		try
		{
			$rows = $this->fetchAll();

			if( !$rows)
			{
				throw new Exception("Konnte User Rechte Gruppen Liste nicht laden!");
			}
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
		return $rows->toArray();
	}
	
	public function getUserRechteGruppe($a_options)
	{
		$row = '';
		
		if(is_array($a_options) &&
		   key_exists('user_rechte_gruppe_name', $a_options))
		{
			$row = $this->fetchRow("user_rechte_gruppe_name = '" . $a_options['user_rechte_gruppe_name'] . "'");
		}
		else if(is_array($a_options) &&
				key_exists('user_rechte_gruppe_id', $a_options))
		{
			$row = $this->fetchRow("user_rechte_gruppe_id = '" . $a_options['user_rechte_gruppe_id'] . "'");
		}
		else if(is_numeric($a_options))
		{
			try
			{
				$row = $this->fetchRow("user_rechte_gruppe_id = '" . $a_options . "'");
			}
			catch( Exception $e)
			{
				echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
				echo "Meldung : " . $e->getMessage() . "<br />";
				return false;
			}
		}
		if($row)
		{
			return $row->toArray();
		}
		return false;
	}
	
	public function schreibeUserRechteGruppe( $daten)
	{
		try
		{
			$this->insert( $daten);
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function updateUserRechteGruppe( $daten, $user_rechte_gruppe_id)
	{
		try
		{
			$this->update( $daten, " `user_rechte_gruppe_id` LIKE ( '" . $user_rechte_gruppe_id . "')");
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function loescheUserGruppe( $user_rechte_gruppe_id)
	{
		try
		{
			$this->delete( $user_rechte_gruppe_id);
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
}