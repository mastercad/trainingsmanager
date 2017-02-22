<?php

/**
 * Class Application_Model_DbTable_ExerciseMuscles
 */
class Application_Model_DbTable_ExerciseMuscles extends Application_Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'uebung_muskeln';
    /**
     * @var string
     */
    protected $_primary = 'uebung_muskel_id';

    /**
     * @param $iExerciseId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMusclesForExercise($iExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITHOUT_FROM_PART)
            ->setIntegrityCheck(false);
        try {
            $oSelect->from('muskeln')
                ->join('muskelgruppe_muskeln', 'muskelgruppe_muskel_muskel_fk = muskel_id')
                ->join('muskelgruppen', 'muskelgruppe_id = muskelgruppe_muskel_muskelgruppe_fk')
                ->join('uebung_muskelgruppen', 'uebung_muskelgruppe_muskelgruppe_fk = muskelgruppe_id')
                ->joinLeft('uebung_muskeln', 'uebung_muskel_muskel_fk = muskel_id AND uebung_muskel_uebung_fk = uebung_muskelgruppe_uebung_fk')
                ->where('uebung_muskelgruppe_uebung_fk = ?', $iExerciseId);

            return $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $iMuscleId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesForMuscle($iMuscleId) {
        try {
            return $this->fetchAll("uebung_muskeln_muskel_fk = '" . $iMuscleId . "'");
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
    public function saveExerciseMuscle($aData) {
        try {
            return $this->insert($aData);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $aData
     * @param $iExerciseMuscleId
     * @return bool|int
     */
    public function updateExerciseMuscle($aData, $iExerciseMuscleId) {
        try {
            return $this->update($aData, "uebung_muskel_id = '" . $iExerciseMuscleId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $iExerciseMuscleId
     * @return bool|int
     */
    public function deleteExerciseMuscle($iExerciseMuscleId) {
        try {
            return $this->delete( "uebung_muskel_id = '" . $iExerciseMuscleId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $iExerciseId
     * @return bool|int
     */
    public function deleteExerciseMuscleByExerciseId($iExerciseId) {
        try {
            return $this->delete("uebung_muskel_uebung_fk = '" . $iExerciseId . "'");
        } catch (Exception $oExercise) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oExercise->getMessage() . "<br />";
            return false;
        }
    }
}
