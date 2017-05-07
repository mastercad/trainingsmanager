<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.14
 * Time: 08:54
 */

class Model_DbTable_TrainingDiaryXTrainingPlan extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_diary_x_training_plan';
    /**
     * @var string
     */
    protected $_primary = 'training_diary_x_training_plan_id';

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingByTrainingPlanExerciseId($iTrainingPlanExerciseId)
    {
//        $this->getAdapter()->getProfiler()->setEnabled(TRUE);
        return $this->fetchRow('training_diary_x_training_plan_training_plan_exercise_fk = ' . $iTrainingPlanExerciseId,
            'training_create_date DESC');

//        Zend_Debug::dump($this->getAdapter()->getProfiler()->getLastQueryProfile()->getQuery());
    }

    /**
     * @param $iTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findLastOpenTrainingPlanByTrainingPlanIdAndUserId($iTrainingPlanId, $userId) {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect
            ->from('training_plans')
            ->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_training_plan_fk = training_plan_id')
            ->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner('training_diary_x_training_plan', 'training_diary_x_training_plan_training_plan_fk = ' . $iTrainingPlanId)
            ->joinLeft('training_diary_x_training_plan_exercise', 'training_diary_x_training_plan_exercise_t_p_x_e_fk = training_plan_x_exercise_id AND training_diary_x_training_plan_exercise_training_diary_fk = training_diary_x_training_plan_training_diary_fk')
//            ->joinInner('training_diaries', 'training_diary_x_training_plan_training_plan_fk = ' . $iTrainingPlanId)
            ->where('training_diary_x_training_plan_flag_finished != 1 OR training_diary_x_training_plan_flag_finished IS NULL')
            ->where('training_diary_x_training_plan_exercise_flag_finished != 1 OR training_diary_x_training_plan_exercise_flag_finished IS NULL')
            ->where('training_plan_id = ?', $iTrainingPlanId)
            ->where('training_plan_user_fk = ?', $userId)
            ->order('training_plan_x_exercise_exercise_order');

        $sql = $oSelect->assemble();

        return $this->fetchAll($oSelect);
    }

    public function findExercisesByTrainingDiaryId($trainingDiaryId) {

        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $oSelect
            ->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_training_plan_fk = training_diary_x_training_plan_training_plan_fk')
            ->joinLeft('training_diary_x_training_plan_exercise', 'training_diary_x_training_plan_exercise_t_p_x_e_fk = training_plan_x_exercise_id AND training_diary_x_training_plan_exercise_training_diary_fk = ' . $trainingDiaryId)
            ->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner('training_diaries', 'training_diary_id = ' . $trainingDiaryId)
            ->where('training_diary_x_training_plan_training_diary_fk = ' . $trainingDiaryId)
            ->order('training_plan_x_exercise_exercise_order');

        return $this->fetchAll($oSelect);
    }
}
