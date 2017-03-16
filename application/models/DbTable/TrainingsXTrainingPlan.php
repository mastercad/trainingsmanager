<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.14
 * Time: 08:54
 */

class Model_DbTable_TrainingsXTrainingPlan extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'trainings_x_training_plan';
    /**
     * @var string
     */
    protected $_primary = 'trainings_x_training_plan_id';

    /**
     *
     */
    public function findActualTraining() {

    }

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingByTrainingPlanExerciseId($iTrainingPlanExerciseId)
    {
//        $this->getAdapter()->getProfiler()->setEnabled(TRUE);
        return $this->fetchRow('trainings_x_training_plan_training_plan_exercise_fk = ' . $iTrainingPlanExerciseId,
            'training_create_date DESC');

//        Zend_Debug::dump($this->getAdapter()->getProfiler()->getLastQueryProfile()->getQuery());
    }

    /**
     * @param $iTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findLastOpenTrainingPlan($iTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect
            ->from('training_plans')
            ->joinLeft('training_plan_x_exercise', 'training_plan_x_exercise_training_plan_fk = training_plan_id')
            ->joinLeft('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinLeft('trainings_x_training_plan', 'trainings_x_training_plan_training_plan_fk = ' . $iTrainingPlanId)
            ->joinLeft('trainings', 'trainings_x_training_plan_training_plan_fk = ' . $iTrainingPlanId)
            ->where('trainings_x_training_plan_flag_finished != 1 OR trainings_x_training_plan_flag_finished IS NULL')
            ->where('training_plan_id = ' . $iTrainingPlanId)
            ->order('training_plan_x_exercise_exercise_order');

        return $this->fetchAll($oSelect);
    }
}
