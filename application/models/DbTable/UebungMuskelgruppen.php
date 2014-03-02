<?php

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_UebungMuskelgruppen extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'uebung_muskelgruppen';
    protected $_primary = 'uebung_muskelgruppe_id';
	
	protected static $obj_meta;
	
	public function init()
	{
		if(!self::$obj_meta)
		{
			self::$obj_meta = $this->info();
		}
	}

	public function getMuskelgruppenFuerUebung($i_uebung_id)
	{
            $select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
                                       ->setIntegrityCheck(false);
            try
            {
                $select->join('muskelgruppen', 'muskelgruppe_id = uebung_muskelgruppe_muskelgruppe_fk');
                $select->where('uebung_muskelgruppe_uebung_fk = ?', $i_uebung_id);
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
        
        public function getUebungenFuerMuskelgruppe($i_muskelgruppe_id)
        {
            try
            {
                $rows = $this->fetchAll("uebung_muskelgruppe_muskelgruppe_fk = '" . $i_muskelgruppe_id . "'");
                
                if($rows)
                {
                    return $rows->toArray();
                }
                return false;
            }
            catch(Exception $e)
            {
                echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
                echo "Meldung : " . $e->getMessage() . "<br />";
                return false;
            }
        }
	
	public function setUebungMuskelgruppen( $a_data)
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
	
	public function updateUebungMuskelgruppen( $a_data, $i_uebung_muskelgruppen_id)
	{
		try
		{
                    $result = $this->update( $a_data, "uebung_muskelgruppe_id = '" . $i_uebung_muskelgruppen_id . "'");
                    
                    return $result;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function loescheUebungMuskelgruppe( $uebung_muskelgruppe_id)
	{
		try
		{
                    $result = $this->delete( "uebung_muskelgruppe_id = '" . $uebung_muskelgruppe_id . "'");
                    
                    return $result;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}

    public function loescheUebungMuskelgruppeVonMuskelgruppe($i_muskelgruppe_id)
    {
        try
        {
            $result = $this->delete("uebung_muskelgruppe_muskelgruppe_fk = '" . $i_muskelgruppe_id . "'");
            return $result;
        }
        catch( Exception $e)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $e->getMessage() . "<br />";
            return false;
        }
    }

    public function loescheUebungMuskelgruppeFuerUebung($i_uebung_id)
    {
        try
        {
            $result = $this->delete("uebung_muskelgruppe_uebung_fk = '" . $i_uebung_id . "'");
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
