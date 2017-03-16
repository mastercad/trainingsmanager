<?php

/**
 * Class Application_Model_DbTable_UserRightGroups
 */
class Model_DbTable_UserRightGroups extends Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'user_right_groups';
    /**
     * @var string
     */
    protected $_primary = 'user_right_group_id';

    /**
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
     * @param $aOptions
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
     * @param $aData
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
     * @param $aData
     * @param $iUserRightGroupId
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
     * @param $iUserRightGroupId
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