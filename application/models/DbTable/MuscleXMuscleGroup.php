<?php

/**
 * Class Application_Model_DbTable_MuscleGroupMuscles
 */
class Model_DbTable_MuscleXMuscleGroup extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'muscle_x_muscle_group';
    /**
     * @var string
     */
    protected $_primary = 'muscle_x_muscle_group_id';

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

    /**
     * @param $iMuscleGroupId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMusclesByMuscleGroupId($iMuscleGroupId) {
		$oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
		try {
            $oSelect->join($this->considerTestUserForTableName('muscles'), 'muscle_id = muscle_x_muscle_group_muscle_fk')
                ->where('muscle_x_muscle_group_muscle_group_fk = ?', $iMuscleGroupId);

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
    public function findMuscleGroupByMuscleId($iMuscleId) {
        try {
            return $this->fetchRow("muscle_x_muscle_group_muscle_fk = '" . $iMuscleId . "'");
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
    public function saveMuscleXMuscleGroup($aData) {
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
            return $this->update($aData, "muscle_x_muscle_group_id = '" . $iMuscleGroupMuscleId . "'");
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $iMuscleGroupMuscleId
     * @param $muscleGroupId
     *
     * @return bool|int
     */
    public function deleteMuscleGroupMuscle($iMuscleGroupMuscleId, $muscleGroupId){
		try {
            return $this->delete("muscle_x_muscle_group_muscle_fk = '" . $iMuscleGroupMuscleId . "' AND muscle_x_muscle_group_muscle_group_fk = '" . $muscleGroupId . "'");
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
            return $this->delete("muscle_x_muscle_group_muscle_group_fk = '" . $iMuscleGroupId . "'");
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
            return $this->delete("muscle_x_muscle_group_muscle_fk = '" . $iMuscleId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    public function deleteMuscleGroupById($muscleGroupId) {
        try {
            return $this->delete("muscle_x_muscle_group_id = '" . $muscleGroupId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}