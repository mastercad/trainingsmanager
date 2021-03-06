<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

namespace Model\DbTable;

use Zend_Db_Table_Rowset_Abstract;
use Zend_Db_Table_Abstract;

/**
 * Class TrainingDiaryXDeviceOption
 *
 * @package Model\DbTable
 */
class TrainingDiaryXDeviceOption extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name     = 'training_diary_x_device_option';
    /**
     * @var string
     */
    protected $_primary = 'training_diary_x_device_option_id';

    /**
     * find device options by training diary training plan exercise
     *
     * @param int $trainingDiaryTrainingPlanExerciseId
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceOptionsByTrainingDiaryTrainingPlanExerciseId($trainingDiaryTrainingPlanExerciseId)
    {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->joinInner(
            $this->considerTestUserForTableName('device_options'),
            'device_option_id = training_diary_x_device_option_device_option_fk'
        )
            ->joinInner(
                $this->considerTestUserForTableName('training_diary_x_training_plan_exercise'),
                'training_diary_x_training_plan_exercise_id = training_diary_x_device_option_t_d_x_t_p_e_fk'
            )
            ->joinInner(
                $this->considerTestUserForTableName('training_diaries'),
                'training_diary_id = training_diary_x_training_plan_exercise_training_diary_fk'
            )
            ->joinInner(
                $this->considerTestUserForTableName('training_plan_x_exercise'),
                'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk'
            )
            ->joinInner(
                $this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk'
            )
            ->where("training_diary_x_device_option_t_d_x_t_p_e_fk = '" . $trainingDiaryTrainingPlanExerciseId . "'");

        return $this->fetchAll($oSelect);
    }

    /**
     * find all device options
     *
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
     * @param null|int $userId     optional
     * @param null|int $exerciseId optional
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDeviceOptions($userId = null, $exerciseId = null) 
    {
        $select = $this->select(self::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->joinInner(
            $this->considerTestUserForTableName('training_diary_x_training_plan_exercise'),
            'training_diary_x_training_plan_exercise_id = training_diary_x_device_option_t_d_x_t_p_e_fk'
        )
            ->joinInner(
                $this->considerTestUserForTableName('training_diaries'),
                'training_diary_id = training_diary_x_training_plan_exercise_training_diary_fk'
            )
            ->joinInner(
                $this->considerTestUserForTableName('training_plan_x_exercise'),
                'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk'
            )
            ->joinInner(
                $this->considerTestUserForTableName('training_plans'),
                'training_plan_id = training_plan_x_exercise_training_plan_fk'
            )
            ->joinInner(
                $this->considerTestUserForTableName('device_options'),
                'device_option_id = training_diary_x_device_option_device_option_fk'
            )
            ->joinInner(
                $this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk'
            )
            ->joinLeft(
                $this->considerTestUserForTableName('training_plan_x_device_option'),
                'training_plan_x_device_option_training_plan_exercise_fk = training_plan_x_exercise_id AND ' .
                'training_plan_x_device_option_device_option_fk = training_diary_x_device_option_device_option_fk'
            )
            ->order(['training_diary_id', 'training_plan_x_exercise_exercise_order'])
            ->columns(
                [
                'training_diary_x_device_option_create_date',
                'training_diary_x_device_option_device_option_value',
                $this->considerTestUserForTableName('training_plan_x_device_option') .
                '.training_plan_x_device_option_device_option_value',
                $this->considerTestUserForTableName('training_diaries') . '.training_diary_id',
                $this->considerTestUserForTableName('exercises') . '.exercise_name',
                $this->considerTestUserForTableName('device_options') . '.device_option_name'
                ]
            );

        if (!empty($userId)) {
            $select->where('training_plan_user_fk = ?', $userId);
        }

        if (!empty($exerciseId)) {
            $select->where('exercise_id = ?', $exerciseId);
        }

        return $this->fetchAll($select);
    }
}
