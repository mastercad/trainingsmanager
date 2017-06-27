<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.05.14
 * Time: 10:47
 */

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Nette\NotImplementedException;

/**
 * Class TrainingPlanLayouts
 *
 * @package Model\DbTable
 */
class TrainingPlanLayouts extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name 	= 'training_plan_layouts';

    /**
     * @var string
     */
    protected $_primary = 'training_plan_layout_id';

    /**
     * @inheritdoc
     */
    function findByPrimary($id) {
        throw new NotImplementedException('Function findByPrimary not implemented yet!');
    }

    /**
     * find training plan layout
     *
     * @param int $iTrainingPlanLayoutId
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanLayout($iTrainingPlanLayoutId) {
        return $this->fetchRow('training_plan_layout_id = ' . $iTrainingPlanLayoutId);
    }

    /**
     * find training plan layout by name
     *
     * @param string $sTrainingPlanLayoutName
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findTrainingPlanLayoutByName($sTrainingPlanLayoutName)
    {
        return $this->fetchRow('training_plan_layout_name LIKE("' . $sTrainingPlanLayoutName . '")');
    }
}
