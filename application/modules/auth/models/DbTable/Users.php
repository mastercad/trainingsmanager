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

class Auth_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
	protected $_name 	= 'users';
	protected $_primary = 'user_id';
	
	protected static $_oMeta;
	
	public function init() {
		if(!self::$_oMeta)
		{
			self::$_oMeta = $this->info();
		}
	}
	
	public function getInfo() {
		return self::$_oMeta;
	}

	public function getUser($user_id) {
		try
		{
			$row = $this->fetchRow("`user_id` LIKE( '" . $user_id . "')");
	
			if( !$row)
			{
				throw new Exception("Konnte User " . $user_id . " nicht finden !");
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

	public function getUserByEmail($str_user_email) {
		try {
			return $this->fetchRow("user_email LIKE( '" . $str_user_email . "')");
		} catch( Exception $exception) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $exception->getMessage() . "<br />";
			return false;
		}
	}
	
	public function updateUser($a_data, $user_id) {
		try {
			return $this->update( $a_data, "user_id = '" . $user_id . "'");
		} catch( Exception $exception) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $exception->getMessage() . "<br />";
			return false;
		}
	}
	
	public function saveUser($a_data) {
		try {
			return $this->insert( $a_data);
		} catch( Exception $exception) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $exception->getMessage() . "<br />";
			return false;
		}
	}
	
	public function loescheUser( $user_id) {
		return $this->delete("`user_id` = '" . $user_id . "'");
	}
	
	public function checkEmailExists($str_email) {
		$result = $this->fetchRow("user_email = '" . $str_email . "' OR user_login = '" . $str_email . "'");
	
		if($result) {
			return true;
		}
		return false;
	}
}

