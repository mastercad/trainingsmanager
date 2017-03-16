<?php

/**
 * Class Application_Model_DbTable_ExerciseMuscles
 */
class Model_DbTable_ExerciseXMuscle extends Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'exercise_x_muscle';
    /**
     * @var string
     */
    protected $_primary = 'exercise_x_muscle_id';

    /**
     * @param $iExerciseId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMusclesForExercise($iExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->from('exercise_x_muscle')
                ->joinInner('muscle_x_muscle_group', 'muscle_x_muscle_group_muscle_fk = exercise_x_muscle_muscle_fk')
                ->joinInner('muscle_groups', 'muscle_group_id = muscle_x_muscle_group_muscle_group_fk')
                ->joinLeft('muscles', 'muscle_id = exercise_x_muscle_muscle_fk')
                ->where('exercise_x_muscle_exercise_fk = ?', $iExerciseId)
                ->order('muscle_group_name');

            return $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $iExerciseId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroupsForExercise($iExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->from('exercise_x_muscle')
                ->joinInner('muscle_x_muscle_group', 'muscle_x_muscle_group_muscle_fk = exercise_x_muscle_muscle_fk')
                ->joinInner('muscle_groups', 'muscle_group_id = muscle_x_muscle_group_muscle_group_fk')
                ->joinLeft('muscles', 'muscle_id = exercise_x_muscle_muscle_fk')
                ->where('exercise_x_muscle_exercise_fk = ?', $iExerciseId)
                ->order('muscle_group_name')
                ->group('muscle_group_id');

            return $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    public function findMusclesForExerciseInMuscleGroup($exerciseId, $muscleGroupId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->from('exercise_x_muscle')
                ->joinInner('muscle_x_muscle_group', 'muscle_x_muscle_group_muscle_fk = exercise_x_muscle_muscle_fk')
                ->joinInner('muscle_groups', 'muscle_group_id = muscle_x_muscle_group_muscle_group_fk')
                ->joinLeft('muscles', 'muscle_id = exercise_x_muscle_muscle_fk')
                ->where('exercise_x_muscle_exercise_fk = ?', $exerciseId)
                ->where('muscle_group_id = ?', $muscleGroupId)
                ->order('muscle_group_name');

            return $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    public function findAllMusclesForMuscleGroupWithExerciseMuscles($exerciseId, $muscleGroupId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->from('muscle_groups')
                ->joinInner('muscle_x_muscle_group', 'muscle_x_muscle_group_muscle_group_fk = muscle_group_id')
                ->joinInner('muscles', 'muscle_id = muscle_x_muscle_group_muscle_fk')
                ->joinLeft('exercise_x_muscle', 'exercise_x_muscle_muscle_fk = muscle_id AND exercise_x_muscle_exercise_fk = "' . $exerciseId . '"')
                ->where('muscle_group_id = ?', $muscleGroupId)
                ->order('muscle_group_name');

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
            return $this->fetchAll("exercise_x_muscles_muscle_fk = '" . $iMuscleId . "'");
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
            return $this->update($aData, "exercise_x_muscles_id = '" . $iExerciseMuscleId . "'");
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
            return $this->delete( "exercise_x_muscle_id = '" . $iExerciseMuscleId . "'");
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
    public function deleteExerciseXMuscleByExerciseId($iExerciseId) {
        try {
            return $this->delete("exercise_x_muscle_exercise_fk = '" . $iExerciseId . "'");
        } catch (Exception $oExercise) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oExercise->getMessage() . "<br />";
            return false;
        }
    }

    public function deleteMusclesByMuscleGroupId($muscleGroupId) {
        return $this->_db->query('DELETE
                `exercise_x_muscle`.*
            FROM
                exercise_x_muscle
            INNER JOIN
                muscle_x_muscle_group
            ON
                muscle_x_muscle_group.muscle_x_muscle_group_muscle_fk = exercise_x_muscle.exercise_x_muscle_muscle_fk
            WHERE
                muscle_x_muscle_group.muscle_x_muscle_group_muscle_group_fk = ' . $muscleGroupId);
    }
}