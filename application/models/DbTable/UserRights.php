<?php

/**
 * Class Application_Model_DbTable_UserRights
 */
class Model_DbTable_UserRights extends Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'user_rights';
    /**
     * @var string
     */
    protected $_primary = 'user_right_id';

    /**
     * @param $aOptions
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findUserRights($aOptions) {
		$oSelect = $this->select();
		
		foreach ($aOptions['where_fields'] as $sKey => $sOption) {
            $oSelect->where($sKey . " = ?", $sOption);
		}

        try {
			return $this->fetchRow($oSelect);
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
    public function saveUserRight($aData) {
		try {
			return $this->insert($aData);
		} catch(Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
		}
        return false;
	}
}