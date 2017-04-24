<?php
	class CAD_Seo
	{
		protected $ref_db_table = null;
		protected $str_table_field_name = null;
		protected $str_link_name = null;
		protected $str_clean_link_name = null;
		protected $str_seo_name = null;
		protected $str_table_field_id_name = null;
		protected $i_max_count = null;
		protected $i_table_field_id = null;
		
		public function __construct($a_params = null)
		{
			$this->i_max_count = 0;
			
			if(isset($a_params) &&
			   is_array($a_params))
			{
				if(isset($a_params['ref_db_table']))
				{
					$this->ref_db_table = $a_params['ref_db_table'];
				}
				if(isset($a_params['str_table_field_name']))
				{
					$this->str_table_field_name = $a_params['str_table_field_name'];
				}
				if(isset($a_params['str_link_name']))
				{
					$this->str_link_name = $a_params['str_link_name'];
				}
				if(isset($a_params['str_table_field_id_name']))
				{
					$this->str_table_field_id_name = $a_params['str_table_field_id_name'];
				}
				if(isset($a_params['i_table_field_id']))
				{
					$this->i_table_field_id = $a_params['i_table_field_id'];
				}
			}
		}
		
		public function setDbTable(&$ref_db_table)
		{
			$this->ref_db_table = $ref_db_table;
			
			return $this;
		}
		
		public function getDbTable()
		{
			return $this->ref_db_table;
		}
		
		public function setTableFieldName($str_table_field_name)
		{
			$this->str_table_field_name = trim($str_table_field_name);
			
			return $this;
		}
		
		public function getTableFieldName()
		{
			return $this->str_table_field_name;
		}

		public function setTableFieldId($i_table_field_id)
		{
			$this->i_table_field_id = $i_table_field_id;
			
			return $this;
		}
		
		public function getTableFieldId()
		{
			return $this->i_table_field_id;
		}
		
		public function setTableFieldIdName($str_table_field_id_name)
		{
			$this->str_table_field_id_name = trim($str_table_field_id_name);
			
			return $this;
		}
		
		public function getTableFieldIdName()
		{
			return $this->str_table_field_id_name;
		}
		
		public function setLinkName($str_link_name)
		{
			$this->str_link_name = strtolower(trim($str_link_name));
			
			return $this;
		}
		
		public function getLinkName()
		{
			return $this->str_link_name;
		}
		
		public function setCleanLinkName($str_clean_link_name)
		{
			$this->str_clean_link_name = trim($str_clean_link_name);
			
			return $this;
		}
		
		public function getCleanLinkName()
		{
			return $this->str_clean_link_name;
		}
		
		public function setSeoName($str_seo_name)
		{
			$this->str_seo_name = trim($str_seo_name);
			
			return $this;
		}
		
		public function getSeoName()
		{
			return $this->str_seo_name;
		}
		
		public function setMaxCount($i_max_count)
		{
			$this->i_max_count = $i_max_count;
			
			return $this;
		}
		
		public function getMaxCount()
		{
			return $this->i_max_count;
		}
		
		public function replaceBadSigns($str_text = null)
		{
			if(!$str_text)
			{
				$str_text = $this->getLinkName();
			}
			if(!$str_text)
			{
				echo "Habe keinen String übergeben!<br />";
				return false;
			}
			
			$str_text = strtolower($str_text);
			
			$a_search = array
			(
				'/ä/',
				'/ü/',
				'/ö/',
				'/ß/',
				'/Ü/',
				'/Ä/',
				'/Ö/'
			);
			
			$a_replaces = array
			(
				'ae',
				'ue',
				'oe',
				'ss',
				'ue',
				'ae',
				'oe'	
			);
			
			$str_text = preg_replace($a_search, $a_replaces, $str_text);
			$str_text = preg_replace('/[^A-Za-z0-9]/i', '-', $str_text);
			$str_text = preg_replace('/\-{2,}+/' , '-' , $str_text);
			$str_text = preg_replace('/^\-/', '', $str_text);
			$str_text = preg_replace('/\-$/', '', $str_text);
			
			return $str_text;
		}
		
		public function createValidLinkName($str_link)
		{
			$this->setCleanLinkName($this->replaceBadSigns());
			
			return $this;
		}
		
		public function createSeoLink($str_link_name = null, &$ref_obj_db = null, $str_table_field_name = null)
		{
			if($str_link_name) {
				$this->setLinkName($str_link_name);
			}
			if($ref_obj_db) {
				$this->setDbTable($ref_obj_db);
			}
			if($str_table_field_name) {
				$this->setTableFieldName($str_table_field_name);
			}
			$this->setCleanLinkName($this->replaceBadSigns($str_link_name));
			
			$this->createUniqueDbEntry();
			
			return $this;
		}
		
		/**
		 * function zum erstellen eines eindeutigen seo link namens an hand eines
		 * übergebenen string und der datenbank
		 *
		 *
		 */
		public function createUniqueDbEntry($str_link_name = null, Zend_DbTable &$ref_obj_db = null, $str_table_field_name = null)
		{
			if(!$str_link_name)
			{
				$str_link_name = $this->getCleanLinkName();
			}
			if(!$str_link_name)
			{
				$str_link_name = $this->createValidLinkName($this->getLinkName());
			}
			if(!$ref_obj_db)
			{
				$ref_obj_db = $this->getDbTable();
			}
			if(!$str_table_field_name)
			{
				$str_table_field_name = $this->getTableFieldName();
			}
			
			if(!strlen(trim($str_link_name)))
			{
				echo "Habe nicht alle nötigen Parameter! Breche ab!<br />";
				return false;
			}
			
			$str_base_seo_name = $this->getCleanLinkName();
			$str_seo_name = $str_base_seo_name;
			
			if($ref_obj_db &&
			   $str_table_field_name)
			{
			
				$select = $ref_obj_db->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART);
				$i_max_count = 100;
				$i_count = 0;
				
				while($i_count < $i_max_count)
				{
					$str_seo_name = $str_base_seo_name;
					
					if($i_count > 0)
					{
						$str_seo_name .= "-" . $i_count;
					}
					
					$row = $ref_obj_db->fetchRow($str_table_field_name . " = '" . $str_seo_name . "'");
					
					if(!$row ||
					   (
					   		$row->{$this->getTableFieldIdName()} == $this->getTableFieldId() 
					    )
					)
					{
						break;
					}
					else if($row &&
							$row->{$this->getTableFieldIdName()} == $this->getTableFieldId())
					{
						break;
					}
					++$i_count;
				}
				if($i_count >= $i_max_count)
				{
					echo "Bin durch, konnte aber keinen neuen link erstellen!<br />";
					$str_seo_name = false;
				}
			}
			$this->setSeoName($str_seo_name);
			
			return $this;
		}
	}