<?php

class Vavg_Filter_Validate implements Zend_Filter_Interface
{
    public function filter($string)
    {
    	$string = preg_replace('/&[^amp;]/Ui', '&amp;', $string);
    	
    	return $string;
    }
}