<?php

class Auth_Model_DbTable_UserRightGroups extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'user_right_groups';
	protected $_primary = 'user_right_group_id';
	
	protected static $_oMeta;
	
	public function init()
	{
		if(!self::$_oMeta)
		{
			self::$_oMeta = $this->info();
		}
	}

	public function getUserRightGroups()
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
	
	public function getUserRightGroup($a_options)
	{
		$row = '';
		
		if(is_array($a_options) &&
		   key_exists('user_right_group_name', $a_options))
		{
			$row = $this->fetchRow("user_right_group_name = '" . $a_options['user_right_group_name'] . "'");
		}
		else if(is_array($a_options) &&
				key_exists('user_right_group_id', $a_options))
		{
			$row = $this->fetchRow("user_right_group_id = '" . $a_options['user_right_group_id'] . "'");
		}
		else if(is_numeric($a_options))
		{
			try
			{
				$row = $this->fetchRow("user_right_group_id = '" . $a_options . "'");
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
	
	public function updateUserRightGroup( $daten, $user_right_group_id)
	{
		try
		{
			$this->update( $daten, " `user_right_group_id` LIKE ( '" . $user_right_group_id . "')");
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function deleteUserRightGroup( $user_right_group_id)
	{
		try
		{
			$this->delete(  '`user_right_group_id` = ' . $user_right_group_id);
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
}