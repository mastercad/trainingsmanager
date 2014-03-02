<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 21:06
 * To change this template use File | Settings | File Templates.
 */

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_GeraetegruppeGeraete extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'geraetegruppe_geraete';
    protected $_primary = 'geraetegruppe_geraet_id';

    protected static $obj_meta;

    public function init()
    {
        if(!self::$obj_meta)
        {
            self::$obj_meta = $this->info();
        }
    }

    public function getGeraeteFuerGeraetegruppe($i_geraetegruppe_id)
    {
        $select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);
        try
        {
            $select->join('geraete', 'geraet_id = geraetegruppe_geraet_geraet_fk');
            $select->where('geraetegruppe_geraet_geraetegruppe_fk = ?', $i_geraetegruppe_id);
            $obj_rows = $this->fetchAll($select);

            if($obj_rows)
            {
                return $obj_rows->toArray();
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

    public function getGeraetegruppenFuerGeraet($i_geraet_id)
    {
        try
        {
            $rows = $this->fetchAll("geraetegruppe_geraet_geraet_fk = '" . $i_geraet_id . "'");

            if($rows)
            {
                return $rows->toArray();
            }
            return false;
        }
        catch(Exception $e)
        {

        }
    }

    public function setGeraetegruppeGeraet( $a_data)
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

    public function updateGeraetegruppeGeraet( $a_data, $i_geraetegruppen_geraet_id)
    {
        try
        {
            $result = $this->update( $a_data, "geraetegruppe_geraet_id = '" . $i_geraetegruppen_geraet_id . "'");

            return $result;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function loescheGeraetAusGeraetegruppe( $i_geraetegruppe_geraet_id)
    {
        try
        {
            $result = $this->delete("geraetegruppe_geraet_id = '" . $i_geraetegruppe_geraet_id . "'");

            return $result;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function loescheAlleGeraetegruppeGeraeteFuerGeraetegruppe($i_geraetegruppe_id)
    {
        try
        {
            $result = $this->delete("geraetegruppe_geraet_geraetegruppe_fk = '" . $i_geraetegruppe_id . "'");

            return $result;
        }
        catch(Exception $e)
        {

        }
    }

    public function loescheAlleGeraetegruppeGeraeteFuerGeraet($i_geraet_id)
    {
        try
        {
            $result = $this->delete("geraetegruppe_geraet_geraet_fk = '" . $i_geraet_id . "'");

            return $result;
        }
        catch(Exception $e)
        {

        }
    }
}
