<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

class Model_DbTable_TrainingPlanXExercise extends Model_DbTable_Abstract
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
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanExercise($iTrainingPlanExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->join('exercises', 'exercise_id = training_plan_exercise_fk')
            ->where('training_plan_x_exercise_id = ?', $iTrainingPlanExerciseId);

        return $this->fetchRow($oSelect);
    }

    /**
     * @param $iTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByTrainingPlanId($iTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner('training_plans', 'training_plan_id = training_plan_x_exercise_training_plan_fk')
            ->joinLeft('exercise_x_device', 'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft('exercise_x_exercise_option', 'exercise_x_exercise_option_exercise_fk = exercise_id')
            ->joinLeft('exercise_options', 'exercise_option_id = exercise_x_exercise_option_exercise_option_fk')
            ->joinLeft('device_x_device_option', 'device_x_device_option_device_fk = exercise_x_device_device_fk')
            ->joinLeft('device_options', 'device_option_id = device_x_device_option_device_option_fk')
            ->joinLeft('devices', 'device_id = exercise_x_device_device_fk')
            ->where('training_plan_id = ?', $iTrainingPlanId)
            ->order('training_plan_x_exercise_exercise_order');

        $sql = $oSelect->assemble();

        return $this->fetchAll($oSelect);
    }

    /**
     * @param $iParentTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByParentTrainingPlanId($iParentTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner('training_plans', 'training_plan_id = training_plan_x_exercise_training_plan_fk')
            ->joinLeft('exercise_x_device', 'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft('devices', 'device_id = exercise_x_device_device_fk')
            ->where('training_plan_id = ' . $iParentTrainingPlanId)
            ->orWhere('training_plan_parent_fk = ' . $iParentTrainingPlanId)
//            ->where('trainingsplan_id = ' . $iParentTrainingsplanId . ' AND trainingsplan_layout_fk = 1')
//            ->orWhere('trainingsplan_parent_fk = ' . $iParentTrainingsplanId . ' AND trainingsplan_layout_fk = 2')
            ->order(array('training_plan_order', 'training_plan_create_date', 'training_plan_x_exercise_exercise_order'));

        return $this->fetchAll($oSelect);
    }

    /**
     * @param $aData
     * @return mixed
     */
    public function saveTrainingPlanExercise($aData) {
        return $this->insert($aData);
    }

    /**
     * @param $aData
     * @param $iTrainingPlanExerciseId
     * @return int
     */
    public function updateTrainingPlanExercise($aData, $iTrainingPlanExerciseId) {
        return $this->update($aData, 'training_plan_x_exercise_id = ' . $iTrainingPlanExerciseId);
    }

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingDiaryByTrainingPlanExerciseId($iTrainingPlanExerciseId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

//        $oSelect->joinLeft('trainingstagebuch_uebungen', 'trainingstagebuch_uebung_trainingsplan_uebung_fk = trainingsplan_uebung_id')
//            ->join('trainingsplaene', 'trainingsplan_id = trainingsplan_uebung_trainingsplan_fk')
//            ->join('exercises', 'uebung_id = trainingsplan_uebung_fk')
//            ->where('trainingsplan_uebung_id = ' . $iTrainingsplanUebungId)
//            ->order('trainingsplan_uebung_order');

        $oSelect
            ->joinLeft('trainings_x_training_plan', 'trainings_x_training_plan_training_plan_fk = training_plan_x_exercise_training_plan_fk')
//            ->joinLeft('training_plan_x_exercise', 'trainingstagebuch_uebung_trainingsplan_uebung_fk = trainingsplan_uebung_id')
            ->join('training_plans', 'training_plan_id = training_plan_x_exercise_training_plan_fk')
            ->join('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinLeft('exercise_x_device', 'exercise_x_device_exercise_fk = exercise_id')
            ->join('devices', 'device_id = exercise_x_device_device_fk')
            ->where('training_plan_x_exercise_exercise_fk = ' . $iTrainingPlanExerciseId)
//            ->order('trainingsplan_uebung_order')
        ;

        return $this->fetchRow($oSelect);
    }
}
