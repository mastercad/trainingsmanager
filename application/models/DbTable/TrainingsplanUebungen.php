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
            ->where('trainingsplan_uebung_id = ?', $iTrainingsplanUebungId);

        return $this->fetchRow($oSelect);
    }

    public function getUebungenFuerTrainingsplan($iTrainingsplanId)
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('uebungen', 'uebung_id = trainingsplan_uebung_fk')
            ->join('geraete', 'geraet_id = uebung_geraet_fk')
            ->join('trainingsplaene', 'trainingsplan_id = trainingsplan_uebung_trainingsplan_fk')
            ->where('trainingsplan_uebung_trainingsplan_fk = ' . $iTrainingsplanId)
            ->order('trainingsplan_uebung_order');

        return $this->fetchAll($oSelect);
    }

    public function getUebungenFuerParentTrainingsplan($iParentTrainingsplanId)
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect->join('uebungen', 'uebung_id = trainingsplan_uebung_fk')
            ->join('trainingsplaene', 'trainingsplan_id = trainingsplan_uebung_trainingsplan_fk')
            ->join('geraete', 'geraet_id = uebung_geraet_fk')
            ->where('trainingsplan_id = ' . $iParentTrainingsplanId)
            ->orWhere('trainingsplan_parent_fk = ' . $iParentTrainingsplanId)
//            ->where('trainingsplan_id = ' . $iParentTrainingsplanId . ' AND trainingsplan_layout_fk = 1')
//            ->orWhere('trainingsplan_parent_fk = ' . $iParentTrainingsplanId . ' AND trainingsplan_layout_fk = 2')
            ->order(array('trainingsplan_order', 'trainingsplan_eintrag_datum', 'trainingsplan_uebung_order'));

        return $this->fetchAll($oSelect);
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
