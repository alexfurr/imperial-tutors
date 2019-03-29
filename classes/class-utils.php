<?php


class imperialTutorUtils
{




	static function getUKdate($inputDate, $format="Y-m-d H:i:s")
	{
		$tz = new DateTimeZone('Europe/London');
		$date = new DateTime($inputDate);
		$date->setTimezone($tz);
		$UKdate = $date->format($format);
		
		
		return $UKdate;
	}	

	

	
	
	
}


?>