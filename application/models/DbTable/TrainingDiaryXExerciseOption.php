<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.14
 * Time: 16:15
 */

class Model_DbTable_TrainingDiaryXExerciseOption extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_diary_x_exercise_option';
    /**
     * @var string
     */
    protected $_primary = 'training_diary_x_exercise_option_id';

    /**
     * @param int $trainingDiaryTrainingPlanExerciseId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExerciseOptionsByTrainingDiaryTrainingPlanExerciseId($trainingDiaryTrainingPlanExerciseId)
    {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->joinInner('exercise_options', 'exercise_option_id = training_diary_x_exercise_option_exercise_option_fk')
            ->joinInner('training_diary_x_training_plan_exercise', 'training_diary_x_training_plan_exercise_id = ' . $trainingDiaryTrainingPlanExerciseId)
            ->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk')
            ->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->where("training_diary_x_exercise_option_t_d_x_t_p_e_fk = '" . $trainingDiaryTrainingPlanExerciseId . "'")
        ;

        return $this->fetchAll($oSelect);
    }

    /**
     * SELECT
    training_diary_x_device_option_create_date,
    training_diary_x_device_option_device_option_value,
    training_plan_x_device_option_device_option_value,
    training_diary_id,
    exercise_name,
    device_option_name
    FROM
    training_diary_x_device_option

    INNER JOIN training_diary_x_training_plan_exercise
    ON training_diary_x_training_plan_exercise_id = training_diary_x_device_option_t_d_x_t_p_e_fk

    INNER JOIN training_diaries
    ON training_diary_id = training_diary_x_training_plan_exercise_training_diary_fk

    INNER JOIN training_plan_x_exercise
    ON training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk

    INNER JOIN training_plan_x_device_option
    ON training_plan_x_device_option_training_plan_exercise_fk = training_plan_x_exercise_id

    INNER JOIN device_options
    ON device_option_id = training_diary_x_device_option_device_option_fk

    INNER JOIN exercises
    ON exercise_id = training_plan_x_exercise_exercise_fk

    ORDER BY training_diary_id, training_plan_x_exercise_exercise_order
     *
     * @param null $userId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findAllExerciseOptions($userId = null) {
        $select = $this->select(self::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->joinInner('training_diary_x_training_plan_exercise', 'training_diary_x_training_plan_exercise_id = training_diary_x_exercise_option_t_d_x_t_p_e_fk')
            ->joinInner('training_diaries', 'training_diary_id = training_diary_x_training_plan_exercise_training_diary_fk')
            ->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk')
            ->joinInner('training_plan_x_exercise_option', 'training_plan_x_exercise_option_training_plan_exercise_fk = training_plan_x_exercise_id')
            ->joinInner('exercise_options', 'exercise_option_id = training_diary_x_exercise_option_exercise_option_fk')
            ->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->order(['training_diary_id', 'training_plan_x_exercise_exercise_order'])
            ->columns(['training_diary_x_exercise_option_create_date',
                'training_diary_x_exercise_option_exercise_option_value',
                'training_plan_x_exercise_option.training_plan_x_exercise_option_exercise_option_value',
                'training_diaries.training_diary_id',
                'exercises.exercise_name',
                'exercise_options.exercise_option_name'
            ]);

        return $this->fetchAll($select);
    }
}