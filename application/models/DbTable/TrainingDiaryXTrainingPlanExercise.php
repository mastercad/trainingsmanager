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

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table;

/**
 * Class TrainingDiaryXTrainingPlanExercise
 *
 * @package Model\DbTable
 */
class TrainingDiaryXTrainingPlanExercise extends AbstractDbTable
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
     * find actual training diary by training plan exercise
     *
     * @param int $iTrainingPlanExerciseId
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingDiaryByTrainingPlanExerciseId($iTrainingPlanExerciseId) {
        return $this->fetchRow('training_diary_x_training_plan_exercise_training_plan_x_exercise_fk = ' . $iTrainingPlanExerciseId,
            'training_diary_x_training_plan_exercise_create_date DESC');
    }

    /**
     * check if training diary is finished
     *
     * @param int $trainingDiaryXTrainingPlanExerciseId
     *
     * @return null|\Zend_Db_Table_Row_Abstract
     * @throws \Zend_Db_Select_Exception
     */
    public function checkTrainingDiaryFinished($trainingDiaryXTrainingPlanExerciseId)
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $oSelect->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'),
            'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk')
            ->joinInner($this->considerTestUserForTableName('training_diary_x_training_plan'),
                'training_diary_x_training_plan_training_plan_fk = training_plan_x_exercise_training_plan_fk '.
                'AND training_diary_x_training_plan_training_diary_fk = training_diary_x_training_plan_exercise_training_diary_fk')
            ->joinInner($this->considerTestUserForTableName('training_plans'),
                'training_plan_id = training_plan_x_exercise_training_plan_fk')
            ->joinInner($this->considerTestUserForTableName('training_plan_x_exercise') .
                ' AS trainingPlanExercises', 'trainingPlanExercises.training_plan_x_exercise_training_plan_fk = training_plan_id')
            ->joinLeft($this->considerTestUserForTableName('training_diary_x_training_plan_exercise') .
                ' AS trainingDiaryXTrainingPlanExercises', 'trainingDiaryXTrainingPlanExercises.training_diary_x_training_plan_exercise_t_p_x_e_fk = trainingPlanExercises.training_plan_x_exercise_id ' .
                'AND trainingDiaryXTrainingPlanExercises.training_diary_x_training_plan_exercise_training_diary_fk =
                ' . $this->considerTestUserForTableName('training_diary_x_training_plan_exercise') .
                '.training_diary_x_training_plan_exercise_training_diary_fk')
            ->where($this->considerTestUserForTableName('training_diary_x_training_plan_exercise') .
                '.training_diary_x_training_plan_exercise_id = ?', $trainingDiaryXTrainingPlanExerciseId)
            ->columns([
                'COUNT(trainingPlanExercises.training_plan_x_exercise_id) = SUM(trainingDiaryXTrainingPlanExercises.training_diary_x_training_plan_exercise_flag_finished) AS trainingPlanIsFinished',
                $this->considerTestUserForTableName('training_diary_x_training_plan') . '.training_diary_x_training_plan_id'
            ]);

        return $this->fetchRow($oSelect);
    }

    /**
     * find actual training diary training plan exercise for user
     *
     * @param int $userId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findActualTrainingDiaryTrainingPlanExercises($userId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select
            ->joinInner($this->considerTestUserForTableName('training_diary_x_training_plan'),
                'training_diary_x_training_plan_id = training_diary_x_training_plan_exercise_t_d_x_t_p_fk')
            ->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'),
                'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk')
            ->joinInner($this->considerTestUserForTableName('training_plans'),
                'training_plan_id = training_diary_x_training_plan_training_plan_fk')
            ->joinInner($this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinLeft($this->considerTestUserForTableName('training_diary_x_device_option'),
                'training_diary_x_device_option_t_d_x_t_p_e_fk = training_diary_x_training_plan_exercise_id')
            ->joinLeft($this->considerTestUserForTableName('training_diary_x_exercise_option'),
                'training_diary_x_exercise_option_t_d_x_t_p_e_fk = training_diary_x_training_plan_exercise_id')
            ->where('training_plan_active = 1')
            ->where('training_plan_user_fk = ?', $userId)
            ->where('training_diary_x_exercise_option_id IS NOT NULL OR training_diary_x_device_option_id IS NOT NULL')
            ->group('exercise_id')
            ->order('exercise_name');

        return $this->fetchAll($select);
    }

    /**
     * find training diary training plan exercise
     *
     * @param int $userId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingDiaryTrainingPlanExercises($userId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select
            ->joinInner($this->considerTestUserForTableName('training_diary_x_training_plan'),
                'training_diary_x_training_plan_id = training_diary_x_training_plan_exercise_t_d_x_t_p_fk')
            ->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'),
                'training_plan_x_exercise_id = training_diary_x_training_plan_exercise_t_p_x_e_fk')
            ->joinInner($this->considerTestUserForTableName('training_plans'),
                'training_plan_id = training_diary_x_training_plan_training_plan_fk')
            ->joinInner($this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinLeft($this->considerTestUserForTableName('training_diary_x_device_option'),
                'training_diary_x_device_option_t_d_x_t_p_e_fk = training_diary_x_training_plan_exercise_id')
            ->joinLeft($this->considerTestUserForTableName('training_diary_x_exercise_option'),
                'training_diary_x_exercise_option_t_d_x_t_p_e_fk = training_diary_x_training_plan_exercise_id')
            ->where('training_plan_user_fk = ?', $userId)
            ->where('training_diary_x_exercise_option_id IS NOT NULL OR training_diary_x_device_option_id IS NOT NULL')
            ->group('exercise_id')
            ->order('exercise_name');

        return $this->fetchAll($select);
    }
}
