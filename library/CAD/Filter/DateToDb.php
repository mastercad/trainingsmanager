<?php 

class CAD_Filter_DateToDb implements Zend_Filter_Interface
{
	public function filter($datum)
	{
		if(preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{2,4})$/', $datum, $matches))
		{
			return date("Y-m-d", strtotime( $matches[3] . "-" . $matches[2] . "-" . $matches[1])); 
		}
		else if(preg_match('/^(\d{1,2})\.(\d{2,4})$/', $datum, $matches))
		{
			return date("Y-m-d", strtotime( $matches[2] . "-" . $matches[1] . "-01"));
		}
		else if(preg_match('/^(\d{2,4})\-(\d{1,2})\-(\d{1,2})$/', $datum, $matches))
		{
			return date("Y-m-d", strtotime( $matches[1] . "-" . $matches[2] . "-" . $matches[3])); 
		}
		
		return false;
	}
}
?>