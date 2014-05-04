<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_Trainingsplaene extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'trainingsplaene';
    protected $_primary = 'trainingsplan_id';

    protected static $obj_meta;

    public function init()
    {
        if(!self::$obj_meta)
        {
            self::$obj_meta = $this->info();
        }
    }

    public function getTrainingsplan($iTrainingsplanId) {
        $oSelect = $this->select(FALSE);
        $oSelect->from(array('parent' => $this->_name));
        $oSelect->joinLeft(
            $this->_name . ' as child',
//            array('child', $this->_name),
            'child.trainingsplan_parent_fk = parent.trainingsplan_id'
//            , array('child' => $this->_name)
        );
//        $oSelect->joinLeft('trainingsplan_layouts', 'trainingsplan_layout_id = parent.trainingsplan_layout_fk');
        $oSelect->where('parent.trainingsplan_id = ?', $iTrainingsplanId);
        $oSelect->order(array('parent.trainingsplan_id', 'child.trainingsplan_id'));

        echo $oSelect->__toString();

        return $this->fetchAll($oSelect);
    }

    public function getChildTrainingsplaene($iParentTrainingsplanId)
    {

    }
}
