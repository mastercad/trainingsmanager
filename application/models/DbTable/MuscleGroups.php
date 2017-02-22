<?php

/**
 * Class Application_Model_DbTable_MuscleGroups
 */
class Application_Model_DbTable_MuscleGroups extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'muskelgruppen';
    /**
     * @var string
     */
    protected $_primary = 'muskelgruppe_id';

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllMuscleGroups() {
        return $this->fetchAll(null, "muskelgruppe_name");
    }

    /**
     * @param $iMuscleGroupId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroup($iMuscleGroupId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
                                   ->setIntegrityCheck(false);
        try {
            $oSelect->join('muskelgruppe_muskeln', 'muskelgruppe_muskel_muskelgruppe_fk = muskelgruppe_id')
                ->join('muskeln', 'muskel_id = muskelgruppe_muskel_muskel_fk')
                ->where("muskelgruppe_id = '" . $iMuscleGroupId . "'");

            return $this->fetchAll($oSelect);
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $sMuscleGroupName
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroupsByName($sMuscleGroupName) {
        try {
            return $this->fetchAll("muskelgruppe_name LIKE( '" . $sMuscleGroupName . "')", 'muskelgruppe_name');
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
    public function saveMuscleGroup($aData) {
		try {
            return $this->insert($aData);
		} catch( Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * @param $aData
     * @param $iMuscleGroupId
     * @return bool|int
     */
    public function updateMuscleGroup($aData, $iMuscleGroupId) {
		try {
            return $this->update($aData, "muskelgruppe_id = '" . $iMuscleGroupId . "'");
		} catch( Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * @param $iMuscleGroupId
     * @return bool|int
     */
    public function deleteMuscleGroup($iMuscleGroupId) {
		try {
            return $this->delete("muskelgruppe_id = '" . $iMuscleGroupId . "'");
		} catch( Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}
}
