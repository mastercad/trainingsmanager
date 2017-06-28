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
