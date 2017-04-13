<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.14
 * Time: 16:15
 */

class Model_DbTable_TrainingDiaryXDeviceOption extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_diary_x_device_option';
    /**
     * @var string
     */
    protected $_primary = 'training_diary_x_device_option_id';

    /**
     * @param int $trainingDiaryTrainingPlanExerciseId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceOptionsByTrainingDiaryTrainingPlanExerciseId($trainingDiaryTrainingPlanExerciseId)
    {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->joinInner('device_options', 'device_option_id = training_diary_x_device_option_device_option_fk')
            ->joinInner('training_diary_x_training_plan_exercise', 'training_diary_x_training_plan_exercise_id = training_diary_x_device_option_t_d_x_t_p_e_fk')
            ->joinInner('training_diaries', 'training_diary_id = training_diary_x_training_plan_exercise_training_diary_fk')
            ->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk')
            ->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->where("training_diary_x_device_option_t_d_x_t_p_e_fk = '" . $trainingDiaryTrainingPlanExerciseId . "'")
        ;

        return $this->fetchAll($oSelect);
    }
}
