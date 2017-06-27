<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table;

/**
 * Class TrainingPlanXDeviceOption
 *
 * @package Model\DbTable
 */
class TrainingPlanXDeviceOption extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name 	= 'training_plan_x_device_option';
    /**
     * @var string
     */
    protected $_primary = 'training_plan_x_device_option_id';

    /**
     * find training plan device option by training plan exercise and optional device option
     *
     * @param int $trainingPlanExerciseId optional
     * @param int $deviceOptionId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanDeviceOptionsByTrainingPlanExerciseId($trainingPlanExerciseId, $deviceOptionId = null) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $oSelect->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'),
            'training_plan_x_exercise_id = training_plan_x_device_option_training_plan_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device'),
                'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('device_options'),
                'device_option_id = training_plan_x_device_option_device_option_fk')
            ->joinLeft($this->considerTestUserForTableName('device_x_device_option'),
                'device_x_device_option_device_option_fk = device_option_id AND device_x_device_option_device_fk = exercise_x_device_device_fk')
            ->where('training_plan_x_device_option_training_plan_exercise_fk = ?', $trainingPlanExerciseId);

        if (! empty($deviceOptionId)) {
            $oSelect->where('training_plan_x_device_option_device_option_fk = ?', $deviceOptionId);
        }
        return $this->fetchAll($oSelect);
    }

    /**
     * find training plan device option by training plan exercise and device option
     *
     * @param int $trainingPlanExerciseId
     * @param int $deviceOptionId
     *
     * @return null|\Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanDeviceOptionByTrainingPlanExerciseIdAndDeviceOptionId($trainingPlanExerciseId, $deviceOptionId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $oSelect->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'),
            'training_plan_x_exercise_id = ' . $trainingPlanExerciseId)
            ->joinInner($this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('device_options'), 'device_option_id = ' . $deviceOptionId)
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device'),
                'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device_option'),
                'exercise_x_device_option_device_option_fk = ' . $deviceOptionId .
                ' AND exercise_x_device_option_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('device_x_device_option'), 'device_x_device_option_id = ' .
                $deviceOptionId . ' AND device_x_device_option_device_fk = exercise_x_device_device_fk')
            ->where('training_plan_x_device_option_training_plan_exercise_fk = ?', $trainingPlanExerciseId)
            ->where('training_plan_x_device_option_device_option_fk = ?', $deviceOptionId);

        return $this->fetchRow($oSelect);
    }

    /**
     * find exercise device option by training plan exercise and device option
     *
     * @param int $trainingPlanExerciseId
     * @param int $deviceOptionId
     *
     * @return null|\Zend_Db_Table_Row_Abstract
     */
    public function findExerciseDeviceOptionByTrainingPlanExerciseIdAndDeviceOptionId($trainingPlanExerciseId, $deviceOptionId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);

        $oSelect->from($this->considerTestUserForTableName('training_plan_x_exercise'))
            ->joinInner($this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('exercise_x_device_option'),
                'exercise_x_device_option_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('device_options'),
                'device_option_id = exercise_x_device_option_device_option_fk')
            ->joinLeft($this->considerTestUserForTableName('device_x_device_option'),
                'device_x_device_option_id = exercise_x_device_option_device_option_fk')
            ->where('training_plan_x_exercise_id = ?', $trainingPlanExerciseId)
            ->where('exercise_x_device_option_device_option_fk = ?', $deviceOptionId);

        return $this->fetchRow($oSelect);
    }

    /**
     * save training plan device option data
     *
     * @param array $aData
     *
     * @return mixed
     */
    public function saveTrainingPlanDeviceOption($aData) {
        return $this->insert($aData);
    }

    /**
     * update training plan device option data by training plan exercise option
     *
     * @param array $aData
     * @param int $iTrainingPlanExerciseOptionId
     *
     * @return int
     */
    public function updateTrainingPlanDeviceOption($aData, $iTrainingPlanExerciseOptionId) {
        return $this->update($aData, 'training_plan_x_device_option_id = ' . $iTrainingPlanExerciseOptionId);
    }

    /**
     * delete training plan exercise option by training plan exercise option
     *
     * @param int $iTrainingPlanExerciseOptionId
     *
     * @return int
     */
    public function deleteTrainingPlanDeviceOption($iTrainingPlanExerciseOptionId) {
        return $this->delete('training_plan_x_device_option_id = ' . $iTrainingPlanExerciseOptionId);
    }

    /**
     * delete training plan device options by training plan exercise
     *
     * @param int $trainingPlanXExerciseId
     *
     * @return int
     */
    public function deleteTrainingPlanDeviceOptionsByTrainingPlanXExerciseId($trainingPlanXExerciseId) {
        return $this->delete('training_plan_x_device_option_training_plan_exercise_fk = ' . $trainingPlanXExerciseId);
    }
}
