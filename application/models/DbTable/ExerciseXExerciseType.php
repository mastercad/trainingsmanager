<?php

/**
 * Class Application_Model_DbTable_ExerciseMuscles
 */
class Model_DbTable_ExerciseXExerciseType extends Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'exercise_x_exercise_type';
    /**
     * @var string
     */
    protected $_primary = 'exercise_x_exercise_type_id';

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

    /**
     * @param $exerciseId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findExerciseTypeForExercise($exerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinInner('exercises', 'exercise_id = exercise_x_exercise_type_exercise_fk')
                ->where('exercise_x_exercise_type_exercise_fk = ?', $exerciseId);

            return $this->fetchRow($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    public function findExercisesWithoutExerciseTypes()
    {
        $select = $this->select(ZEND_DB_TABLE::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);
        try {
            $select->from('exercises', '')
                ->joinLeft('exercise_x_exercise_type', 'exercise_x_exercise_type_exercise_fk = exercise_id', '')
                ->where('exercise_x_exercise_type_id IS NULL')
                ->columns(['COUNT(exercise_id) AS exerciseCount']);

            return $this->fetchRow($select);
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
    public function saveExerciseXExerciseType($aData) {
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
     * @param $exerciseXExerciseTypeId
     * @return bool|int
     */
    public function updateExerciseXExerciseType($aData, $exerciseXExerciseTypeId) {
        try {
            return $this->update($aData, "exercise_x_exercise_type_id = '" . $exerciseXExerciseTypeId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $exerciseXExerciseTypeId
     * @return bool|int
     */
    public function deleteExerciseXExerciseType($exerciseXExerciseTypeId) {
        try {
            return $this->delete( "exercise_x_exercise_type_id = '" . $exerciseXExerciseTypeId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }
}
