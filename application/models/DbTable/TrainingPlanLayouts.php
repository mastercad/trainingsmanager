<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.05.14
 * Time: 10:47
 */

class Application_Model_DbTable_TrainingPlanLayouts extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'trainingsplan_layouts';
    /**
     * @var string
     */
    protected $_primary = 'trainingsplan_layout_id';

    /**
     * @param $iTrainingPlanLayoutId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanLayout($iTrainingPlanLayoutId) {
        return $this->fetchRow('trainingsplan_layout_id = ' . $iTrainingPlanLayoutId);
    }

    /**
     * @param $sTrainingPlanLayoutName
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanLayoutByName($sTrainingPlanLayoutName)
    {
        return $this->fetchRow('trainingsplan_layout_name LIKE("' . $sTrainingPlanLayoutName . '")');
    }
}
