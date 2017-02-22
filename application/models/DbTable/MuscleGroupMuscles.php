<?php

/**
 * Class Application_Model_DbTable_MuscleGroupMuscles
 */
class Application_Model_DbTable_MuscleGroupMuscles extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'muskelgruppe_muskeln';
    /**
     * @var string
     */
    protected $_primary = 'muskelgruppe_muskel_id';

    /**
     * @param $iMuscleGroupId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMusclesByMuscleGroupId($iMuscleGroupId) {
		$oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
					   ->setIntegrityCheck(false);
		try {
            $oSelect->join('muskeln', 'muskel_id = muskelgruppe_muskel_muskel_fk')
                ->where('muskelgruppe_muskel_muskelgruppe_fk = ?', $iMuscleGroupId);

            return $this->fetchAll($oSelect);
		} catch(Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * @param $iMuscleId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroupsByMuscleId($iMuscleId) {
        try {
            return $this->fetchAll("muskelgruppe_muskel_muskel_fk = '" . $iMuscleId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $aData
     * @return bool|mixed
     */
    public function saveMuscleGroupMuscle($aData) {
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
     * @param $iMuscleGroupMuscleId
     * @return bool|int
     */
    public function updateMuscleGroupMuscle($aData, $iMuscleGroupMuscleId) {
		try {
            return $this->update( $a_data, "muskelgruppe_muskel_id = '" . $iMuscleGroupMuscleId . "'");
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $iMuscleGroupMuscleId
     * @return bool|int
     */
    public function deleteMuscleGroupMuscle($iMuscleGroupMuscleId){
		try {
            return $this->delete("muskelgruppe_muskel_id = '" . $iMuscleGroupMuscleId . "'");
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $iMuscleGroupId
     * @return bool|int
     */
    public function deleteAllMuscleGroupsMusclesByMuscleGroupId($iMuscleGroupId){
        try {
            return $this->delete("muskelgruppe_muskel_muskelgruppe_fk = '" . $iMuscleGroupId . "'");
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
    public function deleteAllMuscleGroupsMusclesByMuscleId($iMuscleId){
        try {
            return $this->delete("muskelgruppe_muskel_muskel_fk = '" . $iMuscleId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}