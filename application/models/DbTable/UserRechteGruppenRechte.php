<?php
/*
 * Created on 17.06.2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

	class Application_Model_DbTable_UserRechteGruppenRechte extends Zend_Db_Table_Abstract
	{
	    protected $_name 	= 'user_rechte_gruppen_rechte';
		protected $_primary = 'user_rechte_gruppen_recht_id';
		
		protected static $obj_meta;
		
		public function init()
		{
			if(!self::$obj_meta)
			{
				self::$obj_meta = $this->info();
			}
		}
		
		public function getUserRechteGruppenRechte()
		{		   
			try
			{
				$rows = $this->fetchAll();
			}
			catch (Exception $e)
			{
				echo "In " . __FUNCTION__ . " Klasse " . __CLASS__ . " Trat folgender Fehler auf:<br />";
				echo $e . "<br />";
			}
			
			return $rows->toArray();
		}
	}
