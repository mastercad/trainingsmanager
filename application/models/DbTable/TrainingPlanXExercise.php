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

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

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
            ->joinInner('training_plans', 'training_plan_id = ' . $iTrainingPlanId)
//            ->joinLeft('training_diaries', '')
//            ->joinLeft('training_diary_x_training_plan', '')
            ->joinLeft('exercise_x_device', 'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft('devices', 'device_id = exercise_x_device_device_fk')
            ->where('training_plan_x_exercise_training_plan_fk = ?', $iTrainingPlanId)
            ->order('training_plan_x_exercise_exercise_order');

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
            ->joinInner('training_diary_x_training_plan', 'training_diary_x_training_plan_training_plan_fk = training_plan_x_exercise_training_plan_fk')
//            ->joinInner('training_plan_x_exercise', 'training_plan_x_exercise_ = trainingsplan_uebung_id')
            ->joinInner('training_diary_x_training_plan_exercise', 'training_diary_x_training_plan_exercise_t_p_x_e_fk = training_plan_x_exercise_id ' .
                'AND training_diary_x_training_plan_exercise_training_diary_fk = training_diary_x_training_plan_training_diary_fk')
            ->joinInner('training_diaries', 'training_diary_id = training_diary_x_training_plan_training_diary_fk')
            ->joinInner('training_plans', 'training_plan_id = training_plan_x_exercise_training_plan_fk')
            ->joinInner('exercises', 'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinLeft('exercise_x_device', 'exercise_x_device_exercise_fk = exercise_id')
            ->joinLeft('devices', 'device_id = exercise_x_device_device_fk')
            ->where('training_plan_x_exercise_exercise_fk = ' . $iTrainingPlanExerciseId)
            ->order('training_diary_create_date DESC')
//            ->order('trainingsplan_uebung_order')
        ;

        return $this->fetchRow($oSelect);
    }
}
