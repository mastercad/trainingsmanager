<?php

/**
 * Class Application_Model_DbTable_ExerciseMuscles
 */
class Model_DbTable_ExerciseXExerciseOption extends Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'exercise_x_exercise_option';
    /**
     * @var string
     */
    protected $_primary = 'exercise_x_exercise_option_id';

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

    /**
     * @param $exerciseId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findExerciseOptionsForExercise($exerciseId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinLeft($this->considerTestUserForTableName('exercise_options'), 'exercise_option_id = exercise_x_exercise_option_exercise_option_fk')
                ->where('exercise_x_exercise_option_exercise_fk = ?', $exerciseId);

            return $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    public function findExerciseOptionForExercise($exerciseId, $exerciseOptionId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->from($this->considerTestUserForTableName('exercise_options'))
                ->joinLeft($this->considerTestUserForTableName('exercise_x_exercise_option'),
                    'exercise_x_exercise_option_exercise_option_fk = exercise_option_id AND exercise_x_exercise_option_exercise_fk = "' . $exerciseId . '"')
                ->where('exercise_option_id = ?', $exerciseOptionId);

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
    public function saveExerciseXExerciseOption($aData) {
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
     * @param $exerciseXExerciseOptionId
     * @return bool|int
     */
    public function updateExerciseXExerciseOption($aData, $exerciseXExerciseOptionId) {
        try {
            return $this->update($aData, "exercise_x_exercise_option_id = '" . $exerciseXExerciseOptionId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $exerciseXExerciseOptionId
     * @return bool|int
     */
    public function deleteExerciseXExerciseOption($exerciseXExerciseOptionId) {
        try {
            return $this->delete( "exercise_x_exercise_option_id = '" . $exerciseXExerciseOptionId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }
}
