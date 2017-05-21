<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.14
 * Time: 16:15
 */

class Model_DbTable_TrainingDiaryTrainingPlans extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'trainingstagebuch_trainingsplaene';
    /**
     * @var string
     */
    protected $_primary = 'trainingstagebuch_trainingsplan_id';

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

    /**
     *
     */
    public function findActualTrainingDiary() {

    }

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingDiaryByTrainingPlanExerciseId($iTrainingPlanExerciseId) {
//        $this->getAdapter()->getProfiler()->setEnabled(TRUE);
        return $this->fetchRow('trainingstagebuch_trainingsplan_uebung_fk = ' . $iTrainingPlanExerciseId,
            'trainingstagebuch_eintrag_datum DESC');

//        Zend_Debug::dump($this->getAdapter()->getProfiler()->getLastQueryProfile()->getQuery());
    }

    /**
     * @param $iTrainingDiaryTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findLastOpenTrainingPlan($iTrainingDiaryTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect
            ->join('trainingsplaene', 'trainingsplan_id = trainingstagebuch_trainingsplan_trainingsplan_fk')
            ->joinLeft('trainingsplan_uebungen', 'trainingsplan_uebung_trainingsplan_fk = trainingstagebuch_trainingsplan_trainingsplan_fk')
            ->joinLeft('trainingstagebuch_uebungen', 'trainingstagebuch_uebung_trainingsplan_uebung_fk = trainingsplan_uebung_fk')
            ->joinLeft('exercises', 'uebung_id = trainingsplan_uebung_fk')
            ->where('trainingstagebuch_trainingsplan_flag_abgeschlossen != 1')
            ->where('trainingstagebuch_trainingsplan_trainingsplan_fk = ' . $iTrainingDiaryTrainingPlanId)
            ->order('trainingsplan_uebung_order');

        return $this->fetchAll($oSelect);
    }
}
