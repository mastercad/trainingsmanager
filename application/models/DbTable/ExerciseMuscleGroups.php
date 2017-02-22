<?php

/**
 * Class Application_Model_DbTable_ExerciseMuscleGroups
 */
class Application_Model_DbTable_ExerciseMuscleGroups extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'uebung_muskelgruppen';
    /**
     * @var string
     */
    protected $_primary = 'uebung_muskelgruppe_id';

    /**
     * @param $iExerciseId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findExerciseMuscleGroupByExerciseId($iExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
                                   ->setIntegrityCheck(false);
        try {
            $oSelect->join('muskelgruppen', 'muskelgruppe_id = uebung_muskelgruppe_muskelgruppe_fk')
                ->join('muskelgruppe_muskeln', 'muskelgruppe_muskel_muskelgruppe_fk = muskelgruppe_id')
                ->join('muskeln', 'muskel_id = muskelgruppe_muskel_muskel_fk')
                ->where('uebung_muskelgruppe_uebung_fk = ?', $iExerciseId);
            return  $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
        }
        return false;
	}

    /**
     * @param $iMuscleGroupId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByMuscleGroup($iMuscleGroupId) {
        try {
            return $this->fetchAll("uebung_muskelgruppe_muskelgruppe_fk = '" . $iMuscleGroupId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $aData
     * @return bool|mixed
     */
    public function saveExerciseMuscleGroup($aData) {
		try {
            return $this->insert($aData);
		} catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
        }
        return false;
	}

    /**
     * @param $aData
     * @param $iExerciseMuscleGroupId
     * @return bool|int
     */
    public function updateExerciseMuscleGroup($aData, $iExerciseMuscleGroupId) {
		try {
            return $this->update($aData, "uebung_muskelgruppe_id = '" . $iExerciseMuscleGroupId . "'");
		} catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
        }
        return false;
	}

    /**
     * @param $iExerciseMuscleGroupId
     * @return bool|int
     */
    public function deleteExerciseMuscleGroup($iExerciseMuscleGroupId)
	{
		try {
            return $this->delete( "uebung_muskelgruppe_id = '" . $iExerciseMuscleGroupId . "'");
		} catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
        }
        return false;
	}

    /**
     * @param $iMuscleGroupId
     * @return bool|int
     */
    public function deleteExerciseMuscleGroupByMuscleGroup($iMuscleGroupId) {
        try {
            return $this->delete("uebung_muskelgruppe_muskelgruppe_fk = '" . $iMuscleGroupId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $iExerciseId
     * @return bool|int
     */
    public function deleteExerciseMuscleGroupByExercise($iExerciseId){
        try {
            return $this->delete("uebung_muskelgruppe_uebung_fk = '" . $iExerciseId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
        }
        return false;
    }
}
