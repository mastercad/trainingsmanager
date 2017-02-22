<?php

/**
 * Class Application_Model_DbTable_UserRights
 */
class Application_Model_DbTable_UserRights extends Application_Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'user_rechte';
    /**
     * @var string
     */
    protected $_primary = 'user_recht_id';

    /**
     * @param $iUserId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findAllUserRightsByUserId($iUserId) {
		$oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
					   ->setIntegrityCheck(false);
		try {
            $oSelect->join('controllers', 'user_recht_controller_fk = controller_id', 'controller_name')
                ->join('modules', 'module_id = controller_module_fk', 'module_name')
                ->where('user_recht_user_fk = ' . $iUserId);
            return $this->fetchAll($oSelect);
		} catch (Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
		}
        return false;
	}

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
			echo "<pre>";
			print_r($oException);
			echo "</pre>";
			echo "<br />" . $oSelect->__toString();
		}
        return false;
	}

    /**
     * @param $aData
     * @return bool|mixed
     */
    public function saveUserRight($aData) {
		try {
			return $this->insert( $aData);
		} catch(Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
		}
        return false;
	}
}