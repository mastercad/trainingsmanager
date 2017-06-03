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

class Model_DbTable_Users extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'users';
    /**
     * @var string
     */
    protected $_primary = 'user_id';

    function findByPrimary($id) {
        return $this->fetchRow("user_id = '" . $id . "'");
    }

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

    public function findTestUsers() {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->joinInner('user_right_groups', 'user_right_group_id = user_right_group_fk')
            ->where('user_right_group_name = "Test"');

        return $this->fetchAll($select);
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
        return $this->fetchAll('user_state_fk = 2', 'user_first_name');
    }

    public function findAllActiveUsersInSameUserGroup($userGroupId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->joinInner($this->considerTestUserForTableName('user_x_user_group'), 'user_x_user_group_user_fk = user_id AND ' .
            'user_x_user_group_user_group_fk = ' . $userGroupId);

        return $this->fetchAll($select);
    }
}

