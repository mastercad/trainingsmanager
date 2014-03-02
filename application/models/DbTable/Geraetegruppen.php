<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_Geraetegruppen extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'geraetegruppen';
    protected $_primary = 'geraetegruppe_id';

    protected static $obj_meta;

    public function init()
    {
        if(!self::$obj_meta)
        {
            self::$obj_meta = $this->info();
        }
    }

    public function getGeraetegruppen()
    {
        $rows = $this->fetchAll(null, "geraetegruppe_name");

        if($rows)
        {
            return $rows->toArray();
        }
        return false;
    }

    public function getGeraetegruppe($geraetegruppe_id)
    {
        $select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);
        try
        {
            $row = $this->fetchRow("geraetegruppe_id = '" . $geraetegruppe_id . "'");

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

    public function getGeraetegruppenByName($geraetegruppe_name = '')
    {
        try
        {
            $rows = $this->fetchAll("geraetegruppe_name LIKE( '" . $geraetegruppe_name . "')", 'geraetegruppe_name');

            if($rows)
            {
                return $rows->toArray();
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

    public function setGeraetegruppe( $daten)
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

    public function updateGeraetegruppe( $daten, $geraetegruppe_id)
    {
        try
        {
            $result = $this->update( $daten, "geraetegruppe_id = '" . $geraetegruppe_id . "'");

            return $result;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function loescheGeraetegruppe( $geraetegruppe_id)
    {
        try
        {
            $result = $this->delete("geraetegruppe_id = '" . $geraetegruppe_id . "'");

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