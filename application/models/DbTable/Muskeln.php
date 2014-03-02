<?php

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_Muskeln extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'muskeln';
    protected $_primary = 'muskel_id';
	
	protected static $obj_meta;
	
	public function init()
	{
		if(!self::$obj_meta)
		{
			self::$obj_meta = $this->info();
		}
	}

        public function getMuskeln()
        {
            $rows = $this->fetchAll(null, 'muskel_name');
            
            if($rows)
            {
                return $rows->toArray();
            }
            return false;
        }
        
        public function getMuskelByName($str_suche)
        {
            $rows = $this->fetchAll("muskel_name LIKE('" . $str_suche . "')", 'muskel_name');
            
            if($rows)
            {
                return $rows->toArray();
            }
            return false;
        }
        
        public function getMuskelnFuerMuskelgruppe($i_muskelgruppe_id)
        {
            $rows = $this->fetchAll("muskel_muskelgruppe_fk = '" . $i_muskelgruppe_id . "'", "muskel_name");
            
            if($rows)
            {
                return $rows->toArray();
            }
            return false;
        }
        
	public function getMuskel($muskel_id)
	{
		$select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
					   ->setIntegrityCheck(false);
		try
		{
                    $row = $this->fetchRow("muskel_id = '" . $muskel_id . "'");
                    
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
	
	public function setMuskel( $daten)
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
	
	public function updateMuskel( $daten, $muskel_id)
	{
		try
		{
                    $result = $this->update( $daten, "muskel_id = '" . $muskel_id . "'");
                    
                    return $result;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function loescheMuskel( $muskel_id)
	{
		try
		{
                    $result = $this->delete("muskel_id = '" . $muskel_id . "'");
                    
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