<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

class Application_Model_DbTable_TrainingPlans extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'trainingsplaene';
    /**
     * @var string
     */
    protected $_primary = 'trainingsplan_id';

    /**
     * @param $iTrainingPlanId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlan($iTrainingPlanId) {
        return $this->fetchRow('trainingsplan_id = ' . $iTrainingPlanId);
    }

    /**
     * @param $iParentTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findChildTrainingPlans($iParentTrainingPlanId) {
        return $this->fetchAll('trainingsplan_parent_fk = ' . $iParentTrainingPlanId, 'trainingsplan_order');
    }

    /**
     * @FIXME diese funktion sieht mir doch noch ein klein wenig unfertig aus ...
     *
     * @param $iParentTrainingPlanId
     */
    public function getChildTrainingPlansWithExercises($iParentTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

//        $oSelect->join
    }

    /**
     * @FIXME diese funktion sieht mir doch noch ein klein wenig unfertig aus ...
     *
     * @param $iTrainingPlanId
     */
    public function findChildTrainingPlanIdsForTrainingPlan($iTrainingPlanId)
    {

    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllActiveTrainingPlans() {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('users', 'user_id = trainingsplan_user_fk')
            ->where('trainingsplan_active = 1')
            ->where('trainingsplan_parent_fk = 0')
            ->order('user_vorname');

        return $this->fetchAll($oSelect);
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllInactiveTrainingPlans() {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('users', 'user_id = trainingsplan_user_fk')
            ->where('trainingsplan_active = 0')
            ->where('trainingsplan_parent_fk = 0')
            ->order('user_vorname');

        return $this->fetchAll($oSelect);
    }

    /**
     * @param $iTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findLastOpenTrainingPlan($iTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect
            ->joinLeft('trainingsplan_uebungen', 'trainingsplan_uebung_trainingsplan_fk = trainingsplan_id')
            ->joinLeft('uebungen', 'uebung_id = trainingsplan_uebung_fk')
            ->joinLeft('trainingstagebuch_uebungen', 'trainingstagebuch_uebung_trainingsplan_uebung_fk = trainingsplan_uebung_fk')
            ->joinLeft('trainingstagebuch_trainingsplaene', 'trainingsplan_id = trainingstagebuch_trainingsplan_trainingsplan_fk')
            ->where('trainingstagebuch_trainingsplan_flag_abgeschlossen != 1 OR trainingstagebuch_trainingsplan_flag_abgeschlossen IS NULL')
            ->where('trainingsplan_id = ' . $iTrainingPlanId)
            ->order('trainingsplan_uebung_order');

        return $this->fetchAll($oSelect);
    }
}
