<?php

class CAD_Filter_ConvertTextToImage implements Zend_Filter_Interface
{
    public function filter( $text, $angle = "90", $size = "12")
    {
    	header("Content-type: image/jpeg");
    	
    	$a_mase_image_from_text = imagettfbbox( $size, $angle, "Arial", $text);
    	
    	echo "<pre>";
    	print_r($a_mase_image_from_text);
    	echo "</pre>";
    	/*
    	$img = imagecreatetruecolor($width, $height)
    	$a_image_from_text = imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $text)
    	$img = imagecreate($width, $height);
    	*/
    }
}