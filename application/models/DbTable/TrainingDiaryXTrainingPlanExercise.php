<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.14
 * Time: 08:54
 */

class Model_DbTable_TrainingDiaryXTrainingPlanExercise extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_diary_x_training_plan_exercise';

    /**
     * @var string
     */
    protected $_primary = 'training_diary_x_training_plan_exercise_id';

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingDiaryByTrainingPlanExerciseId($iTrainingPlanExerciseId) {
//        $this->getAdapter()->getProfiler()->setEnabled(TRUE);
        return $this->fetchRow('training_diary_x_training_plan_exercise_training_plan_x_exercise_fk = ' . $iTrainingPlanExerciseId,
            'training_diary_x_training_plan_exercise_create_date DESC');

//        Zend_Debug::dump($this->getAdapter()->getProfiler()->getLastQueryProfile()->getQuery());
    }

    public function checkTrainingDiaryFinished($trainingDiaryXTrainingPlanExerciseId)
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $oSelect->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk')
            ->joinInner('training_diary_x_training_plan', 'training_diary_x_training_plan_training_plan_fk = training_plan_x_exercise_training_plan_fk AND training_diary_x_training_plan_training_diary_fk = training_diary_x_training_plan_exercise_training_diary_fk')
            ->joinInner('training_plans', 'training_plan_id = training_plan_x_exercise_training_plan_fk')
            ->joinInner('training_plan_x_exercise AS trainingPlanExercises', 'trainingPlanExercises.training_plan_x_exercise_training_plan_fk = training_plan_id')
            ->joinLeft('training_diary_x_training_plan_exercise AS trainingDiaryXTrainingPlanExercises', 'trainingDiaryXTrainingPlanExercises.training_diary_x_training_plan_exercise_t_p_x_e_fk = trainingPlanExercises.training_plan_x_exercise_id ' .
                'AND trainingDiaryXTrainingPlanExercises.training_diary_x_training_plan_exercise_training_diary_fk = training_diary_x_training_plan_exercise.training_diary_x_training_plan_exercise_training_diary_fk')
            ->where('training_diary_x_training_plan_exercise.training_diary_x_training_plan_exercise_id = ?', $trainingDiaryXTrainingPlanExerciseId)
            ->columns([
                'COUNT(trainingPlanExercises.training_plan_x_exercise_id) = SUM(trainingDiaryXTrainingPlanExercises.training_diary_x_training_plan_exercise_flag_finished) AS trainingPlanIsFinished',
                'training_diary_x_training_plan.training_diary_x_training_plan_id'
            ]);

        return $this->fetchRow($oSelect);
    }
}
