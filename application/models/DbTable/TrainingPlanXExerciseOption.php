<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

class Model_DbTable_TrainingPlanXExerciseOption extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_plan_x_exercise_option';
    /**
     * @var string
     */
    protected $_primary = 'training_plan_x_exercise_option_id';

    /**
     * @param $iTrainingPlanExerciseId
     * @param int $exerciseOptionId
     *
     * @return null|Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanExerciseOptionsByTrainingPlanExerciseId($iTrainingPlanExerciseId, $exerciseOptionId = null) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_id = training_plan_x_exercise_option_training_plan_exercise_fk')
            ->joinLeft('exercise_options', 'exercise_option_id = training_plan_x_exercise_option_exercise_option_fk')
            ->joinLeft('exercise_x_exercise_option', 'exercise_x_exercise_option_exercise_option_fk = training_plan_x_exercise_option_exercise_option_fk AND ' .
                'exercise_x_exercise_option_exercise_fk = training_plan_x_exercise_exercise_fk')
            ->where('training_plan_x_exercise_option_training_plan_exercise_fk = ?', $iTrainingPlanExerciseId);

        if (!empty($exerciseOptionId)) {
            $oSelect->where('exercise_option_id = ?', $exerciseOptionId);
        }

        return $this->fetchAll($oSelect);
    }

    /**
     * @param $trainingPlanXExerciseId
     *
     * @return null|Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanExerciseOptionsByTrainingDiaryExerciseId($trainingPlanXExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect
            ->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_id = ' . $trainingPlanXExerciseId)
            ->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner('exercise_options', 'exercise_option_id = training_plan_x_exercise_option_exercise_option_fk')
            ->where('training_plan_x_exercise_option_training_plan_exercise_fk = ?', $trainingPlanXExerciseId);

        return $this->fetchAll($oSelect);
    }

    /**
     * @param $iTrainingPlanExerciseId
     * @param $exerciseOptionId
     *
     * @return null|Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanExerciseOptionsByTrainingPlanExerciseIdAndExerciseOptionId($iTrainingPlanExerciseId, $exerciseOptionId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->joinLeft('exercise_options', 'exercise_option_id = training_plan_x_exercise_option_exercise_option_fk')
            ->where('training_plan_x_exercise_option_training_plan_exercise_fk = ?', $iTrainingPlanExerciseId)
            ->where('training_plan_x_exercise_option_exercise_option_fk = ?', $exerciseOptionId);

        return $this->fetchRow($oSelect);
    }

    /**
     * @param $aData
     * @return mixed
     */
    public function saveTrainingPlanExerciseOption($aData) {
        return $this->insert($aData);
    }

    /**
     * @param $aData
     * @param $iTrainingPlanExerciseOptionId
     * @return int
     */
    public function updateTrainingPlanExerciseOption($aData, $iTrainingPlanExerciseOptionId) {
        return $this->update($aData, 'training_plan_x_exercise_option_id = ' . $iTrainingPlanExerciseOptionId);
    }

    /**
     * @param $iTrainingPlanExerciseOptionId
     * @return int
     */
    public function deleteTrainingPlanExerciseOption($iTrainingPlanExerciseOptionId) {
        return $this->delete('training_plan_x_exercise_option_id = ' . $iTrainingPlanExerciseOptionId);
    }
}
