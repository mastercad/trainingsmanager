<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.14
 * Time: 08:54
 */

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_Trainingstagebuecher extends Zend_Db_Table
{
    protected $_name 	= 'trainingstagebuecher';
    protected $_primary = 'trainingstagebuch_id';

    protected static $obj_meta;

    public function init()
    {
        if(!self::$obj_meta)
        {
            self::$obj_meta = $this->info();
        }
    }

    public function getActualTrainingstagebuch()
    {

    }

    public function getActualTrainingstagebuchFuerUebung($iTrainingsplanUebungId)
    {
//        $this->getAdapter()->getProfiler()->setEnabled(TRUE);
        return $this->fetchRow('trainingstagebuch_trainingsplan_uebung_fk = ' . $iTrainingsplanUebungId,
            'trainingstagebuch_eintrag_datum DESC');

//        Zend_Debug::dump($this->getAdapter()->getProfiler()->getLastQueryProfile()->getQuery());
    }
}
