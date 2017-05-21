<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.05.14
 * Time: 10:47
 */

class Model_DbTable_TrainingPlanLayouts extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_plan_layouts';
    /**
     * @var string
     */
    protected $_primary = 'training_plan_layout_id';

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

    /**
     * @param $iTrainingPlanLayoutId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanLayout($iTrainingPlanLayoutId) {
        return $this->fetchRow('training_plan_layout_id = ' . $iTrainingPlanLayoutId);
    }

    /**
     * @param $sTrainingPlanLayoutName
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanLayoutByName($sTrainingPlanLayoutName)
    {
        return $this->fetchRow('training_plan_layout_name LIKE("' . $sTrainingPlanLayoutName . '")');
    }
}
