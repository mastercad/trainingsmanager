<?php

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table_Rowset_Abstract;
use Exception;

/**
 * Class Application_Model_DbTable_UserRightGroups
 */
class UserRightGroups extends AbstractDbTable {

    /**
     * @var string
     */
    protected $_name 	= 'user_right_groups';

    /**
     * @var string
     */
    protected $_primary = 'user_right_group_id';

    /**
     * find all user right groups
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
	public function findAllUserRightGroups() {
		try {
			return $this->fetchAll();
		} catch(Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
		}
        return false;
	}

    /**
     * find user right group by options
     *
     * @param array $aOptions
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findUserRightGroup($aOptions) {
        try {
            if (true === is_array($aOptions)
                && true === array_key_exists('user_right_group_name', $aOptions)
            ) {
                return $this->fetchRow("user_right_group_name = '" . $aOptions['user_right_group_name'] . "'");
            } else if (true === is_array($aOptions)
                && array_key_exists('user_right_group_id', $aOptions)
            ) {
                return $this->fetchRow("user_right_group_id = '" . $aOptions['user_right_group_id'] . "'");
            } else if(true === is_numeric($aOptions)) {
                return $this->fetchRow("user_right_group_id = '" . $aOptions . "'");
            }
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
		return false;
	}

    /**
     * save user right group data
     *
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveUserRightGroup($aData) {
		try {
			return $this->insert($aData);
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
	}

    /**
     * update user right group data
     *
     * @param array $aData
     * @param int $iUserRightGroupId
     *
     * @return bool
     */
    public function updateUserRightGroup($aData, $iUserRightGroupId) {
		try {
			$this->update($aData, "`user_right_group_id` = '" . $iUserRightGroupId . "'");
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
	}

    /**
     * delete user right group
     *
     * @param int $iUserRightGroupId
     *
     * @return bool|int
     */
    public function deleteUserRightGroup($iUserRightGroupId) {
		try {
			return $this->delete("user_right_group_id = " . $iUserRightGroupId);
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
	}
}