<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

class Application_Model_DbTable_TrainingPlanExercises extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'trainingsplan_uebungen';
    /**
     * @var string
     */
    protected $_primary = 'trainingsplan_uebung_id';

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanExercise($iTrainingPlanExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->join('uebungen', 'uebung_id = trainingsplan_uebung_fk')
            ->where('trainingsplan_uebung_id = ?', $iTrainingPlanExerciseId);

        return $this->fetchRow($oSelect);
    }

    /**
     * @param $iTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByTrainingPlanId($iTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('uebungen', 'uebung_id = trainingsplan_uebung_fk')
            ->join('geraete', 'geraet_id = uebung_geraet_fk')
            ->join('trainingsplaene', 'trainingsplan_id = trainingsplan_uebung_trainingsplan_fk')
            ->where('trainingsplan_uebung_trainingsplan_fk = ' . $iTrainingPlanId)
            ->order('trainingsplan_uebung_order');

        return $this->fetchAll($oSelect);
    }

    /**
     * @param $iParentTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByParentTrainingPlanId($iParentTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('uebungen', 'uebung_id = trainingsplan_uebung_fk')
            ->join('trainingsplaene', 'trainingsplan_id = trainingsplan_uebung_trainingsplan_fk')
            ->join('geraete', 'geraet_id = uebung_geraet_fk')
            ->where('trainingsplan_id = ' . $iParentTrainingPlanId)
            ->orWhere('trainingsplan_parent_fk = ' . $iParentTrainingPlanId)
//            ->where('trainingsplan_id = ' . $iParentTrainingsplanId . ' AND trainingsplan_layout_fk = 1')
//            ->orWhere('trainingsplan_parent_fk = ' . $iParentTrainingsplanId . ' AND trainingsplan_layout_fk = 2')
            ->order(array('trainingsplan_order', 'trainingsplan_eintrag_datum', 'trainingsplan_uebung_order'));

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
        return $this->update($aData, 'trainingsplan_uebung_id = ' . $iTrainingPlanExerciseId);
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
//            ->join('uebungen', 'uebung_id = trainingsplan_uebung_fk')
//            ->where('trainingsplan_uebung_id = ' . $iTrainingsplanUebungId)
//            ->order('trainingsplan_uebung_order');

        $oSelect
            ->joinLeft('trainingstagebuch_trainingsplaene', 'trainingstagebuch_trainingsplan_trainingsplan_fk = trainingsplan_uebung_trainingsplan_fk')
            ->joinLeft('trainingstagebuch_uebungen', 'trainingstagebuch_uebung_trainingsplan_uebung_fk = trainingsplan_uebung_id')
            ->join('trainingsplaene', 'trainingsplan_id = trainingsplan_uebung_trainingsplan_fk')
            ->join('uebungen', 'uebung_id = trainingsplan_uebung_fk')
            ->join('geraete', 'geraet_id = uebung_geraet_fk')
            ->where('trainingsplan_uebung_id = ' . $iTrainingPlanExerciseId)
//            ->order('trainingsplan_uebung_order')
        ;

        return $this->fetchRow($oSelect);
    }
}
