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

    public function findByPrimary($trainingPlanId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->joinInner($this->considerTestUserForTableName('users'), 'user_id = training_plan_user_fk')
            ->joinLeft($this->considerTestUserForTableName('user_x_user_group'), 'user_x_user_group_user_fk = user_id')
            ->joinLeft($this->considerTestUserForTableName('user_groups'), 'user_group_id = user_x_user_group_user_group_fk')
            ->where('training_plan_id = ?', $trainingPlanId);

        return $this->fetchRow($select);
    }

    /**
     * @param $iTrainingPlanId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlan($iTrainingPlanId) {
        return $this->fetchRow('training_plan_id = ' . $iTrainingPlanId);
    }
    /**
     * @param $iTrainingPlanId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findFirstExerciseInTrainingPlan($iTrainingPlanId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'), 'training_plan_x_exercise_training_plan_fk = ' . $iTrainingPlanId)
            ->order('training_plan_x_exercise_exercise_order DESC')
            ->limit(1);

        return $this->fetchRow($select);
    }

    public function findAllTrainingPlansForUser($userId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->order('training_plan_create_date DESC')
            ->where('training_plan_user_fk = ?', $userId)
        ;

        return $this->fetchAll($select);
    }

    public function findAllSingleOrParentTrainingPlansForUser($userId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->order('training_plan_create_date ASC')
            ->where('training_plan_user_fk = ?', $userId)
            ->where('(training_plan_parent_fk IS NULL OR training_plan_parent_fk = 0)')
        ;

        return $this->fetchAll($select);
    }

    public function findActiveTrainingPlan($userId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->where('training_plan_active = 1')
            ->where('training_plan_user_fk = ?', $userId)
            ->order(['training_plan_parent_fk', 'training_plan_order', 'training_plan_create_date']);

        return $this->fetchAll($select);
    }

    public function findTrainingPlanAndChildrenByParentTrainingPlanId($trainingPlanId) {
        return $this->fetchAll(
            'training_plan_id = ' . $trainingPlanId . ' OR training_plan_parent_fk = ' . $trainingPlanId,
            ['training_plan_parent_fk', 'training_plan_order']);
    }

    public function findAllTrainingPlansInArchive($userId) {
        return $this->fetchAll(
            'training_plan_user_fk = ' . $userId . ' AND (training_plan_active = 0 OR training_plan_active IS NULL) AND (' .
                'training_plan_parent_fk IS NULL OR training_plan_parent_fk = 0)',
            ['training_plan_create_date']
        );
    }

    /**
     * @param $iParentTrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findChildTrainingPlans($iParentTrainingPlanId) {
        return $this->fetchAll('training_plan_parent_fk = ' . $iParentTrainingPlanId, 'training_plan_order');
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllActiveTrainingPlans() {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $oSelect->join($this->considerTestUserForTableName('users'), 'user_id = training_plan_user_fk')
            ->where('training_plan_active = 1')
            ->where('training_plan_parent_fk = 0')
            ->order('user_first_name');

        return $this->fetchAll($oSelect);
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllInactiveTrainingPlans() {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $oSelect->join('users', 'user_id = training_plan_user_fk')
            ->where('training_plan_active = 0')
            ->where('training_plan_parent_fk = 0')
            ->order('user_first_name');

        return $this->fetchAll($oSelect);
    }

    public function findActiveTrainingPlanByUserId($userId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

//        $select->where('((training_plan_parent_fk IS NOT NULL OR training_plan_parent_fk = 0) AND training_plan_active = 1 AND training_plan_user_fk = ' . $userId);
        $select->where('training_plan_user_fk = ?', $userId)
            ->where('training_plan_active = 1')
            ->where('training_plan_training_plan_layout_fk = 1')
            ->order(['training_plan_order', 'training_plan_create_date'])
            ->limit(1);

        return $this->fetchRow($select);
    }

    public function findNextActiveTrainingPlan($userId, $currentTrainingPlanOrder) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(FALSE);

        $select->where('training_plan_user_fk = ?', $userId)
            ->where('training_plan_active = 1')
            ->where('training_plan_training_plan_layout_fk = 1')
            ->where('training_plan_order > "' . $currentTrainingPlanOrder .'"')
            ->order(['training_plan_order', 'training_plan_create_date'])
            ->limit(1);

        return $this->fetchRow($select);
    }
}
