<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.14
 * Time: 14:05
 */

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_TrainingsplanUebungen extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'trainingsplan_uebungen';
    protected $_primary = 'trainingsplan_uebung_id';

    protected static $obj_meta;

    public function init()
    {
        if(!self::$obj_meta)
        {
            self::$obj_meta = $this->info();
        }
    }

    public function getTrainingsplanUebung($iTrainingsplanUebungId)
    {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->join('uebungen', 'uebung_id = trainingsplan_uebung_fk')
            ->where('trainingsplan_id = ?', $iTrainingsplanUebungId);

        $oRow = $this->fetchRow($oSelect);
        return $oRow;
    }

    public function setTrainingsplanUebung($aData)
    {
        return $this->insert($aData);
    }

    public function updateTrainingsplanUebung($aData, $iTrainingsplanUebungId)
    {
        return $this->update($aData, 'trainingsplan_uebung_id = ' . $iTrainingsplanUebungId);
    }
}
