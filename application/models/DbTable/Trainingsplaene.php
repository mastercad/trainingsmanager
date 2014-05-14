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
        return $this->fetchRow('trainingsplan_id = ' . $iTrainingsplanId);
    }

    public function getChildTrainingsplaene($iParentTrainingsplanId) {
        return $this->fetchAll('trainingsplan_parent_fk = ' . $iParentTrainingsplanId, 'trainingsplan_order');
    }

    public function getChildTrainingsplaeneInclUebungen($iParentTrainingsplanId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

//        $oSelect->join
    }

    public function getTrainingsplaeneAllActive()
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('users', 'user_id = trainingsplan_user_fk')
            ->where('trainingsplan_active = 1')
            ->where('trainingsplan_parent_fk = 0')
            ->order('user_vorname');

        return $this->fetchAll($oSelect);
    }

    public function getTrainingsplaeneAllInActive()
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('users', 'user_id = trainingsplan_user_fk')
            ->where('trainingsplan_active = 0')
            ->where('trainingsplan_parent_fk = 0')
            ->order('user_vorname');

        return $this->fetchAll($oSelect);
    }
}
