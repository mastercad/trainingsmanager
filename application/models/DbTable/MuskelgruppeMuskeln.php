<?php

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_MuskelgruppeMuskeln extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'muskelgruppe_muskeln';
    protected $_primary = 'muskelgruppe_muskel_id';
	
	protected static $obj_meta;
	
	public function init()
	{
		if(!self::$obj_meta)
		{
			self::$obj_meta = $this->info();
		}
	}

	public function getMuskelnFuerMuskelgruppe($i_muskelgruppe_id)
	{
		$select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
					   ->setIntegrityCheck(false);
		try
		{
                    $select->join('muskeln', 'muskel_id = muskelgruppe_muskel_muskel_fk');
                    $select->where('muskelgruppe_muskel_muskelgruppe_fk = ?', $i_muskelgruppe_id);
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
        
        public function getMuskelgruppenFuerMuskel($i_muskel_id)
        {
            try
            {
                $rows = $this->fetchAll("muskelgruppe_muskel_muskel_fk = '" . $i_muskel_id . "'");
                
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
	
	public function setMuskelgruppeMuskel( $a_data)
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
	
	public function updateMuskelgruppeMuskel( $a_data, $i_muskelgruppen_muskel_id)
	{
		try
		{
                    $result = $this->update( $a_data, "muskelgruppe_muskel_id = '" . $i_muskelgruppen_muskel_id . "'");
                    
                    return $result;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function loescheMuskelAusMuskelgruppe( $i_muskelgruppe_muskel_id)
	{
		try
		{
                    $result = $this->delete("muskelgruppe_muskel_id = '" . $i_muskelgruppe_muskel_id . "'");
                    
                    return $result;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
        
        public function loescheAlleMuskelgruppeMuskelnFuerMuskelgruppe($i_muskelgruppe_id)
        {
            try
            {
                $result = $this->delete("muskelgruppe_muskel_muskelgruppe_fk = '" . $i_muskelgruppe_id . "'");

                return $result;
            }
            catch(Exception $e)
            {
                
            }
        }
        
        public function loescheAlleMuskelgruppeMuskelnFuerMuskel($i_muskel_id)
        {
            try
            {
                $result = $this->delete("muskelgruppe_muskel_muskel_fk = '" . $i_muskel_id . "'");

                return $result;
            }
            catch(Exception $e)
            {
                
            }
        }
}