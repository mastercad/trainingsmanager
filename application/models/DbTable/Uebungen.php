<?php

require_once(getcwd() . '/../library/Zend/Db/Table.php');

class Application_Model_DbTable_Uebungen extends Zend_Db_Table_Abstract
{
    protected $_name 	= 'uebungen';
    protected $_primary = 'uebung_id';
	
	protected static $obj_meta;
	
	public function init()
	{
		if(!self::$obj_meta)
		{
			self::$obj_meta = $this->info();
		}
	}

        public function getUebungen()
        {
            $rows = $this->fetchAll(null, 'uebung_name');
            
            if($rows)
            {
                return $rows->toArray();
            }
            return false;
        }

        public function getUebungenByName($str_suche)
        {
            return $this->fetchAll("uebung_name LIKE('" . $str_suche . "')", 'uebung_name');
        }
        
	public function getUebung($uebung_id)
	{
		$select = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
					   ->setIntegrityCheck(false);
		try
		{
            $select->join('geraete', 'geraet_id = uebung_geraet_fk');
            $select->where('uebung_id = ?', $uebung_id);

            return $this->fetchRow($select);
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}

    public function getUebungenFuerGeraet($i_geraet_id)
    {
        $rows = $this->fetchAll("uebung_geraet_fk = '" . $i_geraet_id . "'");

        if($rows)
        {
            return $rows->toArray();
        }
        return false;
    }

	public function getUserGruppe($a_options)
	{
		$db_select = $this->select();
		
		foreach( $a_options['where_fields'] as $key => $option)
		{
			$db_select->where($key . " = ?", $option);
		}
		try
		{
			$row = $this->fetchRow($db_select);
		}
		catch( Exception $e)
		{
                    echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
                    echo "Meldung : " . $e->getMessage() . "<br />";
                    echo "<pre>";
                    print_r($a_options);
                    echo "</pre>";
                    echo "<br />" . $db_select->__toString();
                    
                    return false;
		}
		
		if(!$row)
		{
                    return false;
		}
		return $row->toArray();
	}
	
	public function setUebung( $a_data)
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
	
	public function updateUebung( $a_data, $i_uebung_id)
	{
		try
		{
                    $result = $this->update( $a_data, "uebung_id = '" . $i_uebung_id . "'");
                    
                    return $result;
		}
		catch( Exception $e)
		{
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $e->getMessage() . "<br />";
			return false;
		}
	}
	
	public function loescheUebung( $uebung_id)
	{
            try
            {
                $result = $this->delete("uebung_id = '" . $uebung_id . "'");
                return $result;
            }
            catch( Exception $e)
            {
                echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
                echo "Meldung : " . $e->getMessage() . "<br />";
                return false;
            }
	}

    public function loescheUebungenMitGeraet($i_geraet_id)
    {
        try
        {
            $result = $this->delete("uebung_geraet_fk = '" . $i_geraet_id . "'");
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
