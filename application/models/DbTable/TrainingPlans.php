<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

class Model_DbTable_TrainingPlans extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_plans';
    /**
     * @var string
     */
    protected $_primary = 'training_plan_id';

    /**
     * @param $iTrainingPlanId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlan($iTrainingPlanId) {
        return $this->fetchRow('training_plan_id = ' . $iTrainingPlanId);
    }

    public function findTrainingPlanAndChildrenByParentTrainingPlanId($trainingPlanId) {
        return $this->fetchAll(
            'training_plan_id = ' . $trainingPlanId . ' OR training_plan_parent_fk = ' . $trainingPlanId,
            ['training_plan_parent_fk', 'training_plan_order']);
    }

    /**
     * @param $iParentTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findChildTrainingPlans($iParentTrainingPlanId) {
        return $this->fetchAll('training_plan_parent_fk = ' . $iParentTrainingPlanId, 'training_plan_order');
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

        $oSelect->join('users', 'user_id = training_plan_user_fk')
            ->where('training_plan_active = 1')
            ->where('training_plan_parent_fk = 0')
            ->order('user_first_name');

        return $this->fetchAll($oSelect);
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllInactiveTrainingPlans() {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('users', 'user_id = training_plan_user_fk')
            ->where('training_plan_active = 0')
            ->where('training_plan_parent_fk = 0')
            ->order('user_first_name');

        return $this->fetchAll($oSelect);
    }
}
