<?php
	/**
	 * 
	 * @author Andreas Kempe / andreas.kempe@byte-artist.de
	 *
	 * Created on 02.05.2011
	 *
	 * To change the template for this generated file go to
	 * Window - Preferences - PHPeclipse - PHP - Code Templates
	 * 
	 */

class Vavg_Validatoren_MD5
{
	var $a_source;
	var $a_dest;
	
	public function isValid($string)
	{
		if( preg_match('/^[a-z0-9]+$/Ui', $string))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}