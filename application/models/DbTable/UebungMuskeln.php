<?php

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_UebungMuskeln extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'uebung_muskeln';
    protected $_primary = 'uebung_muskel_id';

    protected static $obj_meta;

    public function init()
    {
        if(!self::$obj_meta)
        {
            self::$obj_meta = $this->info();
        }
    }

    public function getMuskelnFuerUebung($i_uebung_id)
    {
        $select = $this->select(ZEND_DB_TABLE::SELECT_WITHOUT_FROM_PART)
            ->setIntegrityCheck(false);
        try
        {
            $select->from('muskeln');
            $select->join('muskelgruppe_muskeln', 'muskelgruppe_muskel_muskel_fk = muskel_id');
            $select->join('muskelgruppen', 'muskelgruppe_id = muskelgruppe_muskel_muskelgruppe_fk');
            $select->join('uebung_muskelgruppen', 'uebung_muskelgruppe_muskelgruppe_fk = muskelgruppe_id');
            $select->joinLeft('uebung_muskeln', 'uebung_muskel_muskel_fk = muskel_id');
            $select->where('uebung_muskelgruppe_uebung_fk = ?', $i_uebung_id);
            return $this->fetchAll($select);
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function getUebungenFuerMuskel($i_muskel_id)
    {
        try
        {
            return $this->fetchAll("uebung_muskeln_muskel_fk = '" . $i_muskel_id . "'");
        }
        catch(Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function setUebungMuskel( $a_data)
    {
        try
        {
            return $this->insert( $a_data);
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function updateUebungMuskel( $a_data, $i_uebung_muskel_id)
    {
        try
        {
            $result = $this->update( $a_data, "uebung_muskel_id = '" . $i_uebung_muskel_id . "'");

            return $result;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function loescheUebungMuskel( $uebung_muskel_id)
    {
        try
        {
            $result = $this->delete( "uebung_muskel_id = '" . $uebung_muskel_id . "'");

            return $result;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

//    public function loescheUebungMuskelgruppeVonMuskelgruppe($i_muskelgruppe_id)
//    {
//        try
//        {
//            $result = $this->delete("uebung_muskelgruppe_muskelgruppe_fk = '" . $i_muskelgruppe_id . "'");
//            return $result;
//        }
//        catch( Exception $e)
//        {
//            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
//            echo "Meldung : " . $e->getMessage() . "<br />";
//            return false;
//        }
//    }

    public function loescheUebungMuskelnFuerUebung($i_uebung_id)
    {
        try
        {
            $result = $this->delete("uebung_muskel_uebung_fk = '" . $i_uebung_id . "'");
            return $result;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }
}
