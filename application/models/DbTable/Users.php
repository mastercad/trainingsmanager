<?php
/**
 * @Filename: User.php
 * @Usage: php
 * @Description:
 * @Date: 24.03.2012
 * @Author: Andreas Kempe
 * @Company: byte-artist
 * @Email: andreas.kempe@byte-artist.de
 * @Copyright: Andreas Kempe, 2011
 * @License: GNU General Public License
 * @Version:
 * @Last changed: 24.03.2013 10:05:13
 * 
 */

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
	protected $_name 	= 'users';
	protected $_primary = 'user_id';
	
	protected static $obj_meta;
	
	public function init()
	{
		if(!self::$obj_meta)
		{
			self::$obj_meta = $this->info();
		}
	}
	
	public function getInfo()
	{
		return self::$obj_meta;
	}

	public function getUser($user_id)
	{
		try
		{
			$row = $this->fetchRow( "`user_id` LIKE( '" . $user_id . "')");
	
			if( !$row)
			{
				throw new Exception( "Konnte User " . $user_id . " nicht finden !");
			}
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
		return $row->toArray();
	}

	public function getUserByEmail($str_user_email)
	{
		try
		{
			$row = $this->fetchRow( "user_email LIKE( '" . $str_user_email . "')");
	
			if( $row)
			{
				return $row->toArray();
			}
			return false;
// 				throw new Exception( "Konnte User mit der E-Mail " . $str_user_email . " nicht finden !");
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function updateUser( $a_data, $user_id)
	{
		try
		{
			$result = $this->update( $a_data, "user_id = '" . $user_id . "'");
			
			return $result;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function setUser( $a_data)
	{
		try
		{
			$ergebnis = $this->insert( $a_data);
	
			if(!$ergebnis)
			{
				throw new Exception("Fehler beim Anlegen des Users?");
			}
			return $ergebnis;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			echo "<pre>";
			print_r($a_data);
			echo "</pre>";
			echo "<br />" . $db_select->__toString();
			return false;
		}
	}
	
	public function loescheUser( $user_id)
	{
		$result = $this->delete("`user_id` = '" . $user_id . "'");
	
		if($result)
		{
			return $result;
		}
		return false;
	}
	
	public function checkEmailExists($str_email)
	{
		$result = $this->fetchRow("user_email = '" . $str_email . "' OR user_login = '" . $str_email . "'");
	
		if($result)
		{
			return true;
		}
		return false;
	}
	
	public function getDummy($script_id)
	{
		$row = null;
		$select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
					   ->setIntegrityCheck(false);

		$select->joinLeft('firmen_status', 'firma_status_id = firma_status_fk');

		if(is_array($a_options) &&
		   key_exists('where_fields', $a_options))
		{
			foreach( $a_options['where_fields'] as $key => $option)
			{
				$select->where($key . " = ?", $option);
			}
		}
		else if(is_array($a_options) &&
				key_exists('firma_ftp_name', $a_options))
		{
			$select->where("firma_ftp_name = '" . $a_options['firma_ftp_name'] . "' AND firma_flag_aktiv LIKE('1')");
		}
		else if(is_numeric($a_options))
		{
			$select->where("firma_id = ?", $a_options);
		}
		else
		{
			return false;
		}

		$select->joinLeft('users', 'user_id = firma_aussendienst_user_fk');
		
		try
		{
			$row = $this->fetchRow($select);
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
	
}

