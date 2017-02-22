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

class Application_Model_DbTable_Users extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'users';
    /**
     * @var string
     */
    protected $_primary = 'user_id';

    /**
     * @param $iUserId
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findUser($iUserId) {
		try {
			return $this->fetchRow( "`user_id` LIKE( '" . $iUserId . "')");
		} catch (Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
		}
        return false;
	}

    /**
     * @param $sUserEmail
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findUserByEmail($sUserEmail) {
		try {
			return $this->fetchRow( "user_email LIKE( '" . $sUserEmail . "')");
		} catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
	}

    /**
     * @param $aData
     * @param $iUserId
     * @return bool|int
     */
    public function updateUser($aData, $iUserId) {
		try {
			return $this->update($aData, "user_id = '" . $iUserId . "'");
		} catch (Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
		}
        return false;
	}

    /**
     * @param $aData
     * @return bool|mixed
     */
    public function saveUser($aData) {
		try {
			return $this->insert($aData);
		} catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
	}

    /**
     * @param $iUseId
     * @return int
     */
    public function deleteUser($iUseId) {
		return $this->delete("`user_id` = '" . $iUseId . "'");
	}

    /**
     * @param $str_email
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function checkEmailExists($str_email) {
		return $this->fetchRow("user_email = '" . $str_email . "' OR user_login = '" . $str_email . "'");
	}

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findActiveUsers() {
        return $this->fetchAll('user_status_fk = 2', 'user_vorname');
    }
}

