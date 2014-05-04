<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.05.14
 * Time: 10:47
 */

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_TrainingsplanLayouts extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'trainingsplan_layouts';
    protected $_primary = 'trainingsplan_layout_id';

    protected static $obj_meta;

    public function init()
    {
        if(!self::$obj_meta)
        {
            self::$obj_meta = $this->info();
        }
    }

    public function getTrainingsplanLayout($iTrainingsplanLayoutId) {
        return $this->fetchRow('trainingsplan_layout_id = ' . $iTrainingsplanLayoutId);
    }

    public function getTrainingsplanLayoutByName($sTrainingsplanLayoutName)
    {
        return $this->fetchRow('trainingsplan_layout_name LIKE("' . $sTrainingsplanLayoutName . '")');
    }
}
