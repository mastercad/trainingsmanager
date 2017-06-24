<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

class Model_DbTable_TrainingPlanXDeviceOption extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_plan_x_device_option';
    /**
     * @var string
     */
    protected $_primary = 'training_plan_x_device_option_id';

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanDeviceOptionsByTrainingPlanExerciseIdProblem($trainingPlanExerciseId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->from($this->considerTestUserForTableName('device_options'))
            ->joinLeft($this->considerTestUserForTableName('training_plan_x_device_option'),
                'training_plan_x_device_option_training_plan_exercise_fk = training_plan_x_exercise_training_plan_exercise_fk')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device_option'), 'exercise_x_device_option_id = training_plan_x_device_option_device_option_fk')
            ->where('training_plan_x_exercise_id = ?', $trainingPlanExerciseId);

        return $this->fetchAll($oSelect);
    }

    /**
     * @param $trainingPlanExerciseId
     * @param $deviceOptionId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanDeviceOptionsByTrainingPlanExerciseId($trainingPlanExerciseId, $deviceOptionId = null) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $oSelect->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'), 'training_plan_x_exercise_id = training_plan_x_device_option_training_plan_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('exercises'), 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('device_options'), 'device_option_id = training_plan_x_device_option_device_option_fk')
            ->joinLeft($this->considerTestUserForTableName('device_x_device_option'), 'device_x_device_option_device_option_fk = device_option_id AND device_x_device_option_device_fk = exercise_x_device_device_fk')
            ->where('training_plan_x_device_option_training_plan_exercise_fk = ?', $trainingPlanExerciseId);

        if (! empty($deviceOptionId)) {
            $oSelect->where('training_plan_x_device_option_device_option_fk = ?', $deviceOptionId);
        }
        return $this->fetchAll($oSelect);
    }

    public function findTrainingPlanDeviceOptionByTrainingPlanExerciseIdAndDeviceOptionId($trainingPlanExerciseId, $deviceOptionId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $oSelect->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'), 'training_plan_x_exercise_id = ' . $trainingPlanExerciseId)
            ->joinInner($this->considerTestUserForTableName('exercises'), 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('device_options'), 'device_option_id = ' . $deviceOptionId)
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device_option'), 'exercise_x_device_option_device_option_fk = ' . $deviceOptionId . ' AND exercise_x_device_option_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('device_x_device_option'), 'device_x_device_option_id = ' . $deviceOptionId . ' AND device_x_device_option_device_fk = exercise_x_device_device_fk')
            ->where('training_plan_x_device_option_training_plan_exercise_fk = ?', $trainingPlanExerciseId)
            ->where('training_plan_x_device_option_device_option_fk = ?', $deviceOptionId);

        $sql = $oSelect->assemble();

        return $this->fetchRow($oSelect);
    }

    public function findExerciseDeviceOptionByTrainingPlanExerciseIdAndDeviceOptionId($trainingPlanExerciseId, $deviceOptionId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);

        $oSelect->from($this->considerTestUserForTableName('training_plan_x_exercise'))
            ->joinInner($this->considerTestUserForTableName('exercises'), 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('exercise_x_device_option'), 'exercise_x_device_option_exercise_fk = exercise_id')
            ->joinLeft($this->considerTestUserForTableName('device_options'), 'device_option_id = exercise_x_device_option_device_option_fk')
            ->joinLeft($this->considerTestUserForTableName('device_x_device_option'), 'device_x_device_option_id = exercise_x_device_option_device_option_fk')
            ->where('training_plan_x_exercise_id = ?', $trainingPlanExerciseId)
            ->where('exercise_x_device_option_device_option_fk = ?', $deviceOptionId);

        $sql = $oSelect->assemble();

        return $this->fetchRow($oSelect);
    }

    /**
     * @param $aData
     * @return mixed
     */
    public function saveTrainingPlanDeviceOption($aData) {
        return $this->insert($aData);
    }

    /**
     * @param $aData
     * @param $iTrainingPlanExerciseOptionId
     * @return int
     */
    public function updateTrainingPlanDeviceOption($aData, $iTrainingPlanExerciseOptionId) {
        return $this->update($aData, 'training_plan_x_device_option_id = ' . $iTrainingPlanExerciseOptionId);
    }

    /**
     * @param $iTrainingPlanExerciseOptionId
     * @return int
     */
    public function deleteTrainingPlanDeviceOption($iTrainingPlanExerciseOptionId) {
        return $this->delete('training_plan_x_device_option_id = ' . $iTrainingPlanExerciseOptionId);
    }

    public function deleteTrainingPlanDeviceOptionsByTrainingPlanXExerciseId($trainingPlanXExerciseId) {
        $this->delete('training_plan_x_device_option_training_plan_exercise_fk = ' . $trainingPlanXExerciseId);
    }
}
