<?php

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_Muskelgruppen extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'muskelgruppen';
    protected $_primary = 'muskelgruppe_id';
	
	protected static $obj_meta;
	
	public function init()
	{
		if(!self::$obj_meta)
		{
			self::$obj_meta = $this->info();
		}
	}

        public function getMuskelgruppen()
        {
            $rows = $this->fetchAll(null, "muskelgruppe_name");
            
            if($rows)
            {
                return $rows->toArray();
            }
            return false;
        }
        
	public function getMuskelgruppe($iMuskelgruppeId)
	{
            $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
                                       ->setIntegrityCheck(false);
            try
            {
                $oSelect->join('muskelgruppe_muskeln', 'muskelgruppe_muskel_muskelgruppe_fk = muskelgruppe_id');
                $oSelect->join('muskeln', 'muskel_id = muskelgruppe_muskel_muskel_fk');
                $oSelect->where("muskelgruppe_id = '" . $iMuskelgruppeId . "'");
                return $this->fetchAll($oSelect);
            }
            catch( Exception $e)
            {
                echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
                echo "Meldung : " . $e->getMessage() . "<br />";
                return false;
            }
	}

	public function getMuskelgruppenByName($muskelgruppe_name = '')
	{
            try
            {
                $rows = $this->fetchAll("muskelgruppe_name LIKE( '" . $muskelgruppe_name . "')", 'muskelgruppe_name');

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
	
	public function setMuskelgruppe( $daten)
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
	
	public function updateMuskelgruppe( $daten, $muskelgruppe_id)
	{
		try
		{
                    $result = $this->update( $daten, "muskelgruppe_id = '" . $muskelgruppe_id . "'");
                    
                    return $result;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function loescheMuskelgruppe( $muskelgruppe_id)
	{
		try
		{
                    $result = $this->delete("muskelgruppe_id = '" . $muskelgruppe_id . "'");
                    
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
