<?php
	/**
	 * 
	 * @author Andreas Kempe / andreas.kempe@byte-artist.de
	 *
	 */

	define( 'PHONE', 'phone', false);

	class Vavg_Validatoren_Form
	{
		protected $database;
		protected $table;
		protected $label_name;
		protected $form_field_name;
		protected $table_field_name;
		protected $value;
		protected $contentions;
		protected $a_database_metadata;
		protected $a_field_metadata;
		protected $obj_exceptions;
		protected $message_default;
		protected $b_required;
		protected $b_empty_allowed;
		protected $b_null_allowed;
		protected $b_none_zero;
		protected $b_unique;
		
		protected $a_field_errors = Array();
		protected $a_field_messages = Array();
		protected $a_data_foreign_key = array();
		protected $a_special_validators = Array();
		protected $p_fields;
		protected $p_errors;

		/* array keys */
		const KEY_REQUIRED 				= 'required';
		const KEY_SPECIAL_MESSAGE 		= 'spezial_message';
		const KEY_TABLE_FIELD_NAME 		= 'table_field_name';
		const KEY_LABEL_NAME	 		= 'form_label_name';
		/** gibt die tabelle der datenbank an, in der gesucht werden soll. Nötig für u.a. FAG_UNIQUE und SV_FK */
		const KEY_TABLE 				= 'table';
		/** @deprecated statt dessen KEY_TABLE benutzen! */
		const KEY_DATABASE 				= 'database';
		const KEY_EMPTY_ALLOWED 		= 'empty_allowed';
		const KEY_NULL_ALLOWED 			= 'null_allowed';
		const KEY_NONE_ZERO 			= 'none_zero';
		const KEY_FIELD_ERRORS 			= 'field_errors';
		const KEY_FIELD_MESSAGES 		= 'field_messages';
		const KEY_SPECIAL_VALIDATORS 	= 'special_validators';
		const KEY_VALUE 				= 'value';
		const KEY_FLAGS					= 'flags';

		/* boolsche konstanten */
		/**
		 * const FLAG EMPTY ALLOWED
		 *
		 * wird verwendet um ein form field auch zu validieren, wenn es leer ist
		 */
		const FLAG_EMPTY_ALLOWED 		= 'flag_empty_allowed';
		const FLAG_NULL_ALLOWED 		= 'flag_null_allowed';
		const FLAG_NONE_ZERO 			= 'flag_none_zero';
		const FLAG_REQUIRED 			= 'flag_required';
		/** übergebener wert darf nur einmal in der DB vorkommen */
		const FLAG_UNIQUE				= 'flag_unique';
		
		/* spezial validatoren */
		const SV_HEX 					= 'hex';
		const SV_INT 					= 'integer';
		const SV_STRING 				= 'string';
		const SV_PHONE 					= 'phone';
		const SV_EMAIL 					= 'email';
		const SV_URL 					= 'url';
		const SV_FK						= 'fk';
		const SV_DATETIME 				= 'datetime';
		/**
		 * variable zum speichern der aktuellen position im array
		 * @var integer
		 */
		protected $i_count_messages = 0;

		/**
		 * Funktion zum validieren eines datensatzes
		 * übergeben wird ein array
		 *
		 * darin enthalten sind:
		 *
		 * @property form_field_name => der name des formfields
		 * @property table_field_name => der name des tablefields
		 * @property table => die tabelle als object
		 * @property conventions => eventuelle einschränkungen oder lockerungen
		 *   der erlaubten zeichen
		 * @property empty_allowed 0/1 => flag um auch leere einträge zuzulassen
		 * @property null_allowed 0/1 => flag um NULL einträge zuzulassen
		 * @property spezial_validators array() => array mit spezielle validatoren,
		 *   wie z.b. email auf varchar oder integer auf varchar für plz
		 *
		 * @param array $a_daten
		 */

		/*
    	 * $a_daten['fields']['testzugang_nachname']['special_validators'] = Array('phone');
    	 * $a_daten['fields']['testzugang_nachname']['special_validators'] = Array('email');
    	 * $a_daten['fields']['testzugang_nachname']['message_default'] = "den AGB´s muss zugestimmt werden!";
    	 * $a_daten['fields']['testzugang_nachname']['special_validators'] = Array('integer');
    	 * $a_daten['fields']['testzugang_nachname']['empty_allowed'] = 1;
    	 * $a_daten['fields']['testzugang_nachname']['null_allowed'] = 1;
    	 * $a_daten['fields']['testzugang_nachname']['field_name'] = 'Name';
    	 * $a_daten['fields']['testzugang_nachname']['table_field_name'] = 'user_nachname';
    	 * $a_daten['fields']['testzugang_nachname']['value'] = $name;
    	 * $a_daten['fields']['testzugang_nachname']['required'] = 1;
    	 */

		public function isValid(&$a_fields, $database = null, &$errors = null)
		{
			// default message reseten, da sonst bei einem fehlenden
			// datenbank feld die message für das eigentliche formfeld
			// gesetzt werden würde
			$this->p_errors 		= $errors;
			$this->database			= @$a_fields[self::KEY_DATABASE];
			$this->table			= @$a_fields[self::KEY_TABLE];
			$this->a_field_errors 	= @$a_fields[self::KEY_FIELD_ERRORS];
			$this->a_field_messages = @$a_fields[self::KEY_FIELD_MESSAGES];
			
			if($this->database)
			{
				$this->a_database_metadata 	= $this->database->getInfo();
			}
			
			if($this->table)
			{
				$this->a_database_metadata 	= $this->table->getInfo();
			}

			$this->p_fields = &$a_fields;

			if(key_exists('fields', $this->p_fields))
			{
				return $this->validateFieldsArray();
			}
			else
			{
				return $this->validateField();
			}
			return false;
		}

		/**
		 * validiert ein array in der Form
		 * Array(
		 * 			['fields']['feld_1'] = Array()
		 * 					['label_name']
		 * 					['table_field_name']
		 * 					...
		 *
		 * 					  ['feld_2'] = Array()
		 * 					...
		 *
		 * 		)
		 * Enter description here ...
		 */
		protected function validateFieldsArray()
		{
			foreach( $this->p_fields['fields'] as $key => $a_value)
			{
				$this->message_default 	= '';
				$this->b_none_zero 		= false;
				$this->b_required 		= false;
				$this->b_empty_allowed  = false;
				$this->b_unique 		= false;

				$this->label_name				= @$a_value[self::KEY_LABEL_NAME];
				$this->form_field_name	 		= $key;
				$this->table_field_name	 		= @$a_value[self::KEY_TABLE_FIELD_NAME];
				$this->value 			 		= @$a_value[self::KEY_VALUE];
				$this->conventions		 		= @$a_value['conventions'];
				$this->message_default	 		= @$a_value[self::KEY_SPECIAL_MESSAGE];

				$this->a_special_validators 	= @$a_value[self::KEY_SPECIAL_VALIDATORS];

				if(key_exists(self::KEY_FLAGS, $a_value))
				{
					if(is_array($a_value[self::KEY_FLAGS]))
					{
						foreach( @$a_value[self::KEY_FLAGS] as $flag)
						{
							$this->ordneFlagZu($flag);
						}
					}
					else
					{
						$this->ordneFlagZu($a_value[self::KEY_FLAGS]);
					}
				}

				if( is_array($this->a_database_metadata) &&
					!key_exists( $this->table_field_name, $this->a_database_metadata['metadata']))
				{
					$this->setFieldMessage( $this->form_field_name, 'Datenbank Feld \'' . $this->table_field_name . '\' existiert nicht!', 1);

					if( $this->b_required)
					{
//						return false;
					}
				}
				else if( is_array($this->a_database_metadata))
				{
					$this->a_field_metadata = $this->a_database_metadata['metadata'][$this->table_field_name];
				}

				if($this->b_unique &&
				   strlen(trim($this->value)))
				{
					$this->checkUnique();
				}

				if( $this->b_required ||
					!$this->isEmpty())
				{
					if( $this->a_special_validators)
					{
						$this->useSpecialValidators();
					}
					else
					{
						$this->validateField();
					}
				}
				$this->i_count_messages++;
			}
			
			if( !count($this->a_field_errors))
			{
				return true;
			}
			else
			{
				$this->p_fields['field_errors'] = $this->a_field_errors;
				$this->p_fields['field_messages'] = $this->a_field_messages;
			}
		}

		/**
		 * funktion um aus den konstanten wieder klassen variablen zu machen
		 * es wird ein flag übergeben und die klassen variable dazu gesetzt
		 *
		 * @param string flag
		 */
		protected function ordneFlagZu($flag)
		{
			switch($flag)
			{
				case self::FLAG_REQUIRED:
				{
					$this->b_required = true;
					break;
				}
				case self::FLAG_EMPTY_ALLOWED:
				{
					$this->b_empty_allowed = true;
					break;
				}
				case self::FLAG_NULL_ALLOWED:
				{
					$this->b_null_allowed = true;
					break;
				}
				case self::FLAG_NONE_ZERO:
				{
					$this->b_none_zero = true;
					break;
				}
				case self::FLAG_UNIQUE:
				{
					$this->b_unique = true;
					break;
				}
				default:
				{
					"Flag " . $flag . " unbekannt<br />";
				}
			}
		}

		/**
		 * eigentlich zum validieren eines array in dem nur tabellenspalten/wertepaare
		 * stehen sollen, aber das ist mir warscheinlich dann doch zum statisch...
		 *
		 * wird sich zeigen ob ich diese funktion noch brauchen werde. vorerst bleibt sie
		 * unbenutzt
		 *
		 * validiert ein einzelnes array, z.b. für den datenbank import aus
		 * einer bestehenden datenbank oder aus einer CSV datei
		 * Enter description here ...
		 */
		protected function validateArray()
		{
			foreach( $this->p_fields as $table_field_name => $value)
			{
				$this->table_field_name	 		= $table_field_name;
				$this->value 			 		= $value;

				if( is_array($this->a_database_metadata) &&
					!key_exists( $this->table_field_name, $this->a_database_metadata['metadata']))
				{
					$this->setFieldMessage( $this->table_field_name, 'Datenbank Feld \'' . $this->table_field_name . '\' existiert nicht!', 1);

					if( $this->b_required)
					{
//						return false;
					}
				}
				else if( is_array($this->a_database_metadata))
				{
					$this->a_field_metadata = $this->a_database_metadata['metadata'][$this->table_field_name];
				}
				
				$this->validateField();
				$this->i_count_messages++;
			}

			if( !count($this->a_field_messages))
			{
				return true;
			}
			else
			{
				$this->p_errors['field_errors'] = $this->a_field_errors;
				$this->p_errors['field_messages'] = $this->a_field_messages;
			}
		}

		protected function checkUnique()
		{
			if(!strlen(trim($this->value)))
			{
			   	return true;
			}
			$a_treffer = $this->table->fetchRow( "`" . $this->table_field_name . "` = '" . $this->value . "'");

			if(count($a_treffer))
			{
				/* von hand auf required setzen, damit ein fehler gesetzt wird */
				$this->b_required = true;
				$this->setFieldMessage( $this->form_field_name, "Wert bereits in der Datenbank vergeben!", 1);
				return false;
			}
		}
		
		protected function validateField()
		{
			$result = false;

			if( $this->isEmpty())
			{
				return false;
			}

			switch(strtoupper($this->a_field_metadata['DATA_TYPE']))
			{
				case 'VARCHAR':
				{
					$result = $this->validateVarchar();
					break;
				}
				case 'TINYINT':
				{
					$result = $this->validateTinyint();
					break;
				}
				case 'DATETIME':
				{
					$result = $this->validateDatetime();
					break;
				}
				case 'INT':
				case 'INTEGER':
				{
					$result = $this->validateInteger();
					break;
				}
			}
			return $result;
		}

		/**
		 *
		 * funktion zum verarbeiten von userdefinierten
		 * validatoren
		 */
		protected function useSpecialValidators()
		{
			if( $this->isEmpty())
			{
				return false;
			}

			foreach( $this->a_special_validators as $key => $validator)
			{
				// wenn ein erweiterter validator, wie der FK - Check
				if(is_array($validator))
				{
					switch($key)
					{
						case self::SV_FK:
						{
							$this->a_data_foreign_key = $validator;
							return $this->validateFK();
							break;
						}
						default:
						{
							$this->setFieldMessage(null, 'Konnte den Special Validator nicht initialisieren!', 1);
							return false;
						}
					}
				}
				else
				{
					switch( $validator)
					{
						case self::SV_EMAIL:
						{
							return $this->validateEmail();
							break;
						}
						case self::SV_STRING:
						{
							return $this->validateString();
							break;
						}
						case self::SV_INT:
						{
							return $this->validateInteger();
							break;
						}
						case self::SV_PHONE:
						{
							return $this->validatePhone();
							break;
						}
						case self::SV_DATETIME:
						{
							return $this->validateDatetime();
							break;
						}
						case self::SV_URL:
						{
							return $this->validateUrl();
							break;
						}
						case self::SV_HEX:
						{
							return $this->validateHex();
							break;
						}
					}
				}
			}
		}

		protected function isEmpty()
		{
			// wenn eintrag leer aber required, nicht weiter checken
			// sondern fehler setzen und negativ returnen
			if(!strlen(trim($this->value)))
			{
				if($this->b_required)
				{
					$this->setFieldMessage($this->form_field_name, "ist ein Pflichtfeld und darf nicht leer sein!");
				}
				return true;
			}
		}

		protected function validateVarchar()
		{
			// wenn der eintrag länger als das datenbankfeld
			if(is_array($this->a_field_metadata) &&
				key_exists('LENGTH', $this->a_field_metadata) &&
			   $this->a_field_metadata['LENGTH'] > 0 &&
			   strlen($this->value) > $this->a_field_metadata['LENGTH'])
			{
				$this->setFieldMessage($this->form_field_name, "Länge des Eintrags länger als die Zugelassenen Zeichen (" . $this->a_field_metadata['LENGTH'] . ") !", 1);
			}
			else
			{
				return true;
			}
		}

		protected function validateInteger()
		{
			// wenn der eintrag leer, aber benötigt
			// wenn nicht nur aus zahlen besteht
			if(!is_numeric($this->value))
			{
				$this->setFieldMessage($this->form_field_name, "ist keine Zahl!");
			}
			if($this->b_none_zero &&
			   !$this->value)
			{
			   	$this->setFieldMessage($this->form_field_name, "darf nicht kleiner als 1 sein!");
			}
			// wenn der eintrag länger als das datenbankfeld
			if(is_array($this->a_field_metadata) &&
				key_exists('LENGTH', $this->a_field_metadata) &&
			   $this->a_field_metadata['LENGTH'] > 0 &&
			   strlen($this->value) > $this->a_field_metadata['LENGTH'])
			{
				$this->setFieldMessage($this->form_field_name, "ist länger als die zugelassenen Zeichen (" . $this->a_field_metadata['LENGTH'] . ") !", 1);
			}
			else
			{
				return true;
			}
		}

		protected function validateTinyint()
		{

		}

		protected function validateHex()
		{
			if(!preg_match('|^#[0-9a-f]{3,6}$|i', $this->value))
			{
				$this->setFieldMessage( $this->form_field_name, "Hexadezimalwert ungültig! Beispiel: #FF00FF", 1);
			}
		}

		protected function validatePhone()
		{
			$number = preg_replace( '/\||\\|\/|:|\s|\-|\+/Ui', '', $this->value);

			if( !preg_match('/^[0-9]{5,}+$/Ui', $number))
			{
				$this->setFieldMessage( $this->form_field_name, "Nummer ungültig!", 1);
			}
		}

		protected function validateDatetime()
		{

		}

		protected function validateEmail()
		{
			$validator = new Zend_Validate_EmailAddress();
			if(!$validator->isValid($this->value))
			{
				$this->setFieldMessage( $this->form_field_name, "Adresse ungültig!", 1);
			}
		}

		protected function validateUrl()
		{
			if(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->value))
			{
				$this->setFieldMessage( $this->form_field_name, "Adresse ungültig! Beispiel: (http:///ihre-url.de)", 1);
			}
		}

		protected function validateFk()
		{
			$obj_db = $this->a_data_foreign_key[self::KEY_DATABASE];
			$table_field_name = $this->a_data_foreign_key[self::KEY_TABLE_FIELD_NAME];

			if(!$this->value ||
			   !is_numeric($this->value) ||
			   $this->value < 1)
			{
				if(!$this->message_default)
				{
					$this->setFieldMessage( $this->form_field_name, "Muss eine Zahl sein und darf nicht 0 oder kleiner sein!", 1);
				}
				else
				{
					$this->setFieldMessage( $this->form_field_name);
				}
			   	return false;
			}
			$a_treffer = $obj_db->fetchRow( "`" . $table_field_name . "` = '" . $this->value . "'");

			if(!count($a_treffer))
			{
				$this->setFieldMessage( $this->form_field_name, "keine Übereinstimmung des Keys in der Datenbank gefunden!", 2);
				return false;
			}
		}

		protected function setFieldMessage($form_field_name = '', $field_message = '', $prioritaet = 0)
		{
			if( $form_field_name &&
				$this->message_default &&
				!$prioritaet)
			{
				$field_message = $this->message_default;
			}

			if( !$form_field_name)
			{
				$this->a_field_messages[$this->i_count_messages] = $field_message;
			}
			else
			{
				$count_field_messages = count( @$this->a_field_messages[$this->i_count_messages]);

				if( $count_field_messages > 0 &&
					@$this->a_field_messages[($this->i_count_messages - 1)] != $this->label_name . ":")
				{
					$first_message = $this->a_field_messages[$this->i_count_messages];
					// entferne Feldnamen aus erster Message
					$first_message = substr( $first_message, strlen( $this->label_name) + 1, strlen( $first_message) - strlen( $this->field_name) - 1);
					$this->a_field_messages[$this->i_count_messages] = $this->label_name . ":";

					$this->i_count_messages++;
					$this->a_field_messages[$this->i_count_messages] = Array();
					$this->a_field_messages[$this->i_count_messages][0] = $first_message;
					$this->a_field_messages[$this->i_count_messages][1] = $field_message;
				}
				else if( $count_field_messages > 0)
				{
					$this->a_field_messages[$this->i_count_messages][$count_field_messages] = $field_message;
				}
				else
				{
					$first_message = $this->a_field_messages[$this->i_count_messages] = $this->label_name . " " . $field_message;
				}
			}
			if($form_field_name &&
			   $this->b_required)
			{
				$this->setFieldError($form_field_name);
			}
		}

		protected function setFieldError($form_field_name)
		{
			$this->a_field_errors[$form_field_name] = 1;
		}
	}

?>