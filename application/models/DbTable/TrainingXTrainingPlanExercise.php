<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.14
 * Time: 08:54
 */

class Model_DbTable_TrainingXTrainingPlanExercise extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_x_training_plan_exercise';
    /**
     * @var string
     */
    protected $_primary = 'training_x_training_plan_exercise_id';

    /**
     *
     */
    public function findActualTraining() {

    }

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingByTrainingPlanExerciseId($iTrainingPlanExerciseId) {
//        $this->getAdapter()->getProfiler()->setEnabled(TRUE);
        return $this->fetchRow('training_x_training_plan_exercise_training_plan_x_exercise_fk = ' . $iTrainingPlanExerciseId,
            'training_x_training_plan_exercise_create_date DESC');

//        Zend_Debug::dump($this->getAdapter()->getProfiler()->getLastQueryProfile()->getQuery());
    }
}
