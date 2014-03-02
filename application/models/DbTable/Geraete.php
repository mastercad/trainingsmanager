<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_Geraete extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'geraete';
    protected $_primary = 'geraet_id';

    protected static $obj_meta;

    public function init()
    {
        if(!self::$obj_meta)
        {
            self::$obj_meta = $this->info();
        }
    }

    public function getGeraete()
    {
        $rows = $this->fetchAll(null, 'geraet_name');

        if($rows)
        {
            return $rows->toArray();
        }
        return false;
    }

    public function getGeraeteByName($str_suche)
    {
        $rows = $this->fetchAll("geraet_name LIKE('" . $str_suche . "')", "geraet_name");

        if($rows)
        {
            return $rows->toArray();
        }
        return false;
    }

    public function getGeraeteUndGeraetegruppeByName($str_suche)
    {
        $select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);
        
        $select->join('geraetegruppe_geraete', 'geraetegruppe_geraet_geraet_fk = geraet_id');
        $select->join('geraetegruppen', 'geraetegruppe_id = geraetegruppe_geraet_geraetegruppe_fk');
        $select->order(array('geraetegruppe_name', 'geraet_name'));
        $select->where("geraet_name LIKE('" . $str_suche . "')");
        
        $rows = $this->fetchAll($select);

        if($rows)
        {
            return $rows->toArray();
        }
        return false;
    }

    public function getGeraet($geraet_id)
    {
        $select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);
        try
        {
            $row = $this->fetchRow("geraet_id = '" . $geraet_id . "'");

            if($row)
            {
                return $row->toArray();
            }
            return false;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function setGeraet( $daten)
    {
        try
        {
            return $this->insert( $daten);
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function updateGeraet( $daten, $geraet_id)
    {
        try
        {
            $result = $this->update( $daten, "geraet_id = '" . $geraet_id . "'");

            return $result;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function loescheGeraet( $geraet_id)
    {
        try
        {
            $result = $this->delete("geraet_id = '" . $geraet_id . "'");

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
