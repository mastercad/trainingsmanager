<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table_Rowset_Abstract;
use Zend_Db_Table;

/**
 * Class TrainingPlans
 *
 * @package Model\DbTable
 */
class TrainingPlans extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name     = 'training_plans';
    /**
     * @var string
     */
    protected $_primary = 'training_plan_id';

    public function findByPrimary($trainingPlanId) 
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->joinInner($this->considerTestUserForTableName('users'), 'user_id = training_plan_user_fk')
            ->joinLeft($this->considerTestUserForTableName('user_x_user_group'), 'user_x_user_group_user_fk = user_id')
            ->joinLeft($this->considerTestUserForTableName('user_groups'), 'user_group_id = user_x_user_group_user_group_fk')
            ->where('training_plan_id = ?', $trainingPlanId);

        return $this->fetchRow($select);
    }

    /**
     * find training plan
     *
     * @param int $iTrainingPlanId
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlan($iTrainingPlanId) 
    {
        return $this->fetchRow('training_plan_id = ' . $iTrainingPlanId);
    }

    /**
     * find first exercise in training plan
     *
     * @param int $iTrainingPlanId
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findFirstExerciseInTrainingPlan($iTrainingPlanId) 
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->joinInner(
            $this->considerTestUserForTableName('training_plan_x_exercise'),
            'training_plan_x_exercise_training_plan_fk = ' . $iTrainingPlanId
        )
            ->order('training_plan_x_exercise_exercise_order DESC')
            ->limit(1);

        return $this->fetchRow($select);
    }

    /**
     * find all training plans for user
     *
     * @param int $userId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findAllTrainingPlansForUser($userId) 
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->order('training_plan_create_date DESC')
            ->where('training_plan_user_fk = ?', $userId);

        return $this->fetchAll($select);
    }

    /**
     * find all single or parent training plans for user
     *
     * @param int $userId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findAllSingleOrParentTrainingPlansForUser($userId) 
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->order('training_plan_create_date ASC')
            ->where('training_plan_user_fk = ?', $userId)
            ->where('(training_plan_parent_fk IS NULL OR training_plan_parent_fk = 0)');

        return $this->fetchAll($select);
    }

    /**
     * find active training plan for user
     *
     * @param int $userId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findActiveTrainingPlan($userId) 
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->where('training_plan_active = 1')
            ->where('training_plan_user_fk = ?', $userId)
            ->order(['training_plan_parent_fk', 'training_plan_order', 'training_plan_create_date']);

        return $this->fetchAll($select);
    }

    /**
     * find training plan and children by parent training plan id
     *
     * @param int $trainingPlanId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanAndChildrenByParentTrainingPlanId($trainingPlanId) 
    {
        return $this->fetchAll(
            'training_plan_id = ' . $trainingPlanId . ' OR training_plan_parent_fk = ' . $trainingPlanId,
            ['training_plan_parent_fk', 'training_plan_order']
        );
    }

    /**
     * find all training plans in archive for user
     *
     * @param int $userId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findAllTrainingPlansInArchive($userId) 
    {
        return $this->fetchAll(
            'training_plan_user_fk = ' . $userId . ' AND (training_plan_active = 0 OR training_plan_active IS NULL) AND (' .
                'training_plan_parent_fk IS NULL OR training_plan_parent_fk = 0)',
            ['training_plan_create_date DESC']
        );
    }

    /**
     * find child training plans by training plan
     *
     * @param int $iParentTrainingPlanId
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findChildTrainingPlans($iParentTrainingPlanId) 
    {
        return $this->fetchAll('training_plan_parent_fk = ' . $iParentTrainingPlanId, 'training_plan_order');
    }

    /**
     * find all active training plans
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllActiveTrainingPlans() 
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $oSelect->join($this->considerTestUserForTableName('users'), 'user_id = training_plan_user_fk')
            ->where('training_plan_active = 1')
            ->where('training_plan_parent_fk = 0')
            ->order('user_first_name');

        return $this->fetchAll($oSelect);
    }

    /**
     * find all inactive training plans
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllInactiveTrainingPlans() 
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $oSelect->join('users', 'user_id = training_plan_user_fk')
            ->where('training_plan_active = 0')
            ->where('training_plan_parent_fk = 0')
            ->order('user_first_name');

        return $this->fetchAll($oSelect);
    }

    /**
     * find active training plan by user
     *
     * @param int $userId
     *
     * @return null|\Zend_Db_Table_Row_Abstract
     */
    public function findActiveTrainingPlanByUserId($userId) 
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->where('training_plan_user_fk = ?', $userId)
            ->where('training_plan_active = 1')
            ->where('training_plan_training_plan_layout_fk = 1')
            ->order(['training_plan_order', 'training_plan_create_date'])
            ->limit(1);

        return $this->fetchRow($select);
    }

    /**
     * find next active training plan by user and current order
     *
     * @param int $userId
     * @param int $currentTrainingPlanOrder
     *
     * @return null|\Zend_Db_Table_Row_Abstract
     */
    public function findNextActiveTrainingPlan($userId, $currentTrainingPlanOrder) 
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->where('training_plan_user_fk = ?', $userId)
            ->where('training_plan_active = 1')
            ->where('training_plan_training_plan_layout_fk = 1')
            ->where('training_plan_order > "' . $currentTrainingPlanOrder .'"')
            ->order(['training_plan_order', 'training_plan_create_date'])
            ->limit(1);

        return $this->fetchRow($select);
    }
}
