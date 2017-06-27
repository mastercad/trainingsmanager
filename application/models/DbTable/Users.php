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

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table_Rowset_Abstract;
use Exception;
use Zend_Db_Table;

/**
 * Class Users
 *
 * @package Model\DbTable
 */
class Users extends AbstractDbTable
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
     * @inheritdoc
     */
    function findByPrimary($id) {
        return $this->fetchRow("user_id = '" . $id . "'");
    }

    /**
     * find user
     *
     * @param int $iUserId
     *
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
     * find user email
     *
     * @param string $sUserEmail
     *
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
     * update user data
     *
     * @param array $aData
     * @param int $iUserId
     *
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
     * save user data
     *
     * @param array $aData
     *
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
     * find test users
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findTestUsers() {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->joinInner('user_right_groups', 'user_right_group_id = user_right_group_fk')
            ->where('user_right_group_name = "Test"');

        return $this->fetchAll($select);
    }

    /**
     * delete user
     *
     * @param int $iUseId
     *
     * @return int
     */
    public function deleteUser($iUseId) {
		return $this->delete("`user_id` = '" . $iUseId . "'");
	}

    /**
     * find user by email
     *
     * @param string $email
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function checkEmailExists($email) {
		return $this->fetchRow("user_email = '" . $email . "' OR user_login = '" . $email . "'");
	}

    /**
     * find active users
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findActiveUsers() {
        return $this->fetchAll('user_state_fk = 2', 'user_first_name');
    }

    /**
     * find all active users in same user group
     *
     * @param int $userGroupId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findAllActiveUsersInSameUserGroup($userGroupId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->joinInner($this->considerTestUserForTableName('user_x_user_group'), 'user_x_user_group_user_fk = user_id AND ' .
            'user_x_user_group_user_group_fk = ' . $userGroupId);

        return $this->fetchAll($select);
    }
}

