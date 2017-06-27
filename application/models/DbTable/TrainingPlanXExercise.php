<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table_Rowset_Abstract;
use Zend_Db_Table;

/**
 * Class TrainingPlanXExercise
 *
 * @package Model\DbTable
 */
class TrainingPlanXExercise extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name 	= 'training_plan_x_exercise';

    /**
     * @var string
     */
    protected $_primary = 'training_plan_x_exercise_id';

    /**
     * find training plan exercise
     *
     * @param int $iTrainingPlanExerciseId
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanExercise($iTrainingPlanExerciseId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->join($this->considerTestUserForTableName('exercises'), 'exercise_id = training_plan_exercise_fk')
            ->where('training_plan_x_exercise_id = ?', $iTrainingPlanExerciseId);

        return $this->fetchRow($oSelect);
    }

    /**
     * find exercises by training plan
     *
     * @param int $iTrainingPlanId
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByTrainingPlanId($iTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $oSelect->joinInner($this->considerTestUserForTableName('exercises'),
            'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('training_plans'),
                'training_plan_id = ' . $iTrainingPlanId)
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device'),
                'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
            ->where('training_plan_x_exercise_training_plan_fk = ?', $iTrainingPlanId)
            ->order('training_plan_x_exercise_exercise_order');

        return $this->fetchAll($oSelect);
    }

    /**
     * find exercises by parent training plan
     *
     * @param int $iParentTrainingPlanId
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByParentTrainingPlanId($iParentTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $oSelect->joinInner($this->considerTestUserForTableName('exercises'),
            'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('training_plans'),
                'training_plan_id = training_plan_x_exercise_training_plan_fk')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device'),
                'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
            ->where('training_plan_id = ' . $iParentTrainingPlanId)
            ->orWhere('training_plan_parent_fk = ' . $iParentTrainingPlanId)
            ->order([
                    'training_plan_order',
                    'training_plan_create_date',
                    'training_plan_x_exercise_exercise_order'
                ]);

        return $this->fetchAll($oSelect);
    }

    /**
     * save training plan exercise
     *
     * @param array $aData
     *
     * @return mixed
     */
    public function saveTrainingPlanExercise($aData) {
        return $this->insert($aData);
    }

    /**
     * update training plan exercise data
     *
     * @param array $aData
     * @param int $iTrainingPlanExerciseId
     *
     * @return int
     */
    public function updateTrainingPlanExercise($aData, $iTrainingPlanExerciseId) {
        return $this->update($aData, 'training_plan_x_exercise_id = ' . $iTrainingPlanExerciseId);
    }

    /**
     * find training diary by training plan exercise
     *
     * @param int $iTrainingPlanExerciseId
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingDiaryByTrainingPlanExerciseId($iTrainingPlanExerciseId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect
            ->joinInner($this->considerTestUserForTableName('training_diary_x_training_plan'),
                'training_diary_x_training_plan_training_plan_fk = training_plan_x_exercise_training_plan_fk')
            ->joinInner($this->considerTestUserForTableName('training_diary_x_training_plan_exercise'),
                'training_diary_x_training_plan_exercise_t_p_x_e_fk = training_plan_x_exercise_id ' .
                'AND training_diary_x_training_plan_exercise_training_diary_fk = training_diary_x_training_plan_training_diary_fk')
            ->joinInner($this->considerTestUserForTableName('training_diaries'),
                'training_diary_id = training_diary_x_training_plan_training_diary_fk')
            ->joinInner($this->considerTestUserForTableName('training_plans'),
                'training_plan_id = training_plan_x_exercise_training_plan_fk')
            ->joinInner($this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device'),
                'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
            ->where('training_plan_x_exercise_exercise_fk = ' . $iTrainingPlanExerciseId)
            ->order('training_diary_create_date DESC')
        ;

        return $this->fetchRow($oSelect);
    }

    /**
     * find exercise by parent training plan and exercise
     *
     * @param int $trainingPlanId
     * @param int $exerciseId
     *
     * @return null|\Zend_Db_Table_Row_Abstract
     */
    public function findExerciseByParentTrainingPlanIdAndExerciseId($trainingPlanId, $exerciseId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->where('training_plan_x_exercise_training_plan_fk = ?', $trainingPlanId)
            ->where('training_plan_x_exercise_exercise_fk = ?', $exerciseId);

        return $this->fetchRow($select);
    }
}
