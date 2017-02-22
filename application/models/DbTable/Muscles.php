<?php

/**
 * Class Application_Model_DbTable_Muscles
 */
class Application_Model_DbTable_Muscles extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'muskeln';
    /**
     * @var string
     */
    protected $_primary = 'muskel_id';

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllMuscles() {
        return $this->fetchAll(null, 'muskel_name');
    }

    /**
     * @param $sMuscleName
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleByName($sMuscleName) {
        return $this->fetchAll("muskel_name LIKE('" . $sMuscleName . "')", 'muskel_name');
    }

    /**
     * @param $iMuscleId
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findMuscle($iMuscleId) {
		try {
            return $this->fetchRow("muskel_id = '" . $iMuscleId . "'");
		} catch( Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * @param $aData
     * @return bool|mixed
     */
    public function saveMuscle($aData) {
		try {
            return $this->insert($aData);
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $aData
     * @param $iMuscleId
     * @return bool|int
     */
    public function updateMuscle($aData, $iMuscleId) {
		try {
            return $this->update($aData, "muskel_id = '" . $iMuscleId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $iMuscleId
     * @return bool|int
     */
    public function deleteMuscle($iMuscleId) {
		try {
            return $this->delete("muskel_id = '" . $iMuscleId . "'");

        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}
}